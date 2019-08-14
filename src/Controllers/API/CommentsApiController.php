<?php
namespace Controllers\API;

use Core\Controller;
use Core\Utility\APIMessage;
use Core\Utility\Gravatar;
use Core\Utility\HttpStatus;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Models\Comment;
use Models\CommentThread;
use Models\Report;

/**
 * Class CommentsApiController
 * @package Controllers\API
 */
class CommentsApiController extends Controller
{

    /**
     *
     */
    public function get(): void
    {
        $comments = $this->em->getRepository(Comment::class)->findBy(['thread' => $_GET['thread']]);

        $data = array_map(static function(Comment $c) {
            $c->parse();
            $a = $c->getAuthor();
            return [
                'user' => $a->getName(),
                'user_id' => $a->getId(),
                'avatar' => $a->getAvatar() ?? (new Gravatar($a->getEmail(), 50))->getGravatar(),
                'date' => $c->getDate()->format('d.m.Y H:i'),
                'body' => $c->getBody(),
                'id'   => $c->getId(),
            ];
        }, $comments);

        http_response_code(200);
        echo json_encode(new APIMessage(
            HttpStatus::S200(),
            'Retrieved succesfully',
            [],
            $data
        ));
    }


    /**
     *
     */
    public function add(): void
    {
        $errors = [];
        $msg = null;

        $POST = filter_input_array(INPUT_POST, [
            'token'  => FILTER_SANITIZE_STRING,
            'thread' => FILTER_VALIDATE_INT,
            'body'   => FILTER_SANITIZE_STRING
        ]);

        if($POST['token'] === $this->session->get('token')) {

            if (trim($POST['body'])) {

                /** @var CommentThread $thread */
                $thread = null;
                try {
                    $thread = $this->em->find(CommentThread::class, $POST['thread']);
                } catch (OptimisticLockException $e) {
                    $errors[] = $e->getMessage();
                } catch (TransactionRequiredException $e) {
                    $errors[] = $e->getMessage();
                } catch (ORMException $e) {
                    $errors[] = $e->getMessage();
                }

                $comment = new Comment();
                $comment->setAuthor($this->getUser());
                $comment->setBody($POST['body']);
                $comment->setThread($thread);

                try {
                    $this->em->persist($comment);
                } catch (ORMException $e) {
                    $errors[] = $e->getMessage();
                }

                try {
                    $this->em->flush();
                } catch (OptimisticLockException $e) {
                    $errors[] = $e->getMessage();
                } catch (ORMException $e) {
                    $errors[] = $e->getMessage();
                }

            } else {
                $errors[] = 'Comment cannot be empty';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered';
        }


        http_response_code($errors ? 500 : 201);
        echo json_encode(new APIMessage(
            $errors ? HttpStatus::S500() : HttpStatus::S201(),
            $errors ? 'An error has occurred:' : 'Successfully added the comment.',
            $errors
        ));

    }


    /**
     *
     */
    public function report(): void
    {
        $errors = [];
        $status = null;
        $msg = null;

        $POST = filter_input_array(INPUT_POST, [
            'token'   => FILTER_SANITIZE_STRING,
            'comment' => FILTER_VALIDATE_INT,
            'reason'  => FILTER_SANITIZE_STRING
        ]);


        if($POST['token'] === $this->session->get('token')) {

            /** @var Comment $comment */
            $comment = null;
            try {
                $comment = $this->em->find(Comment::class, ['id' => $POST['comment']]);
            } catch (OptimisticLockException $e) {
                $errors[] = $e->getMessage();
            } catch (TransactionRequiredException $e) {
                $errors[] = $e->getMessage();
            } catch (ORMException $e) {
                $errors[] = $e->getMessage();
            }

            $report = new Report();
            $report->setUser($this->getUser());
            $report->setComment($comment);
            $report->setReason($POST['reason'] ?: '');

            try {
                $this->em->persist($report);
            } catch (ORMException $e) {
                $errors[] = $e->getMessage();
            }

            try {
                $this->em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $status = HttpStatus::S409();
                $errors[] = 'Already reported';
            } catch (OptimisticLockException $e) {
                $errors[] = $e->getMessage();
            } catch (ORMException $e) {
                $errors[] = $e->getMessage();
            }

        } else {
            $errors[] = 'X-CSRD protection triggered';
        }

        // Set status
        if ($errors && !$status) {
            $status = HttpStatus::S500();
        } else if (!$errors) {
            $status = HttpStatus::S200();
        }

        http_response_code($status->code);
        echo json_encode(new APIMessage(
            $status,
            $errors ? 'An error has occurred:' : 'Report successful',
            $errors
        ));

    }

}
