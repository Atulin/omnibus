<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\API;

use Omnibus\Models\Report;
use Omnibus\Models\Comment;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Models\CommentThread;
use Omnibus\Core\Utility\Gravatar;
use Omnibus\Core\Utility\APIMessage;
use Omnibus\Core\Utility\HttpStatus;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;


/**
 * Class CommentsApiController
 * @package Omnibus\Controllers\API
 */
class CommentsApiController extends Controller
{

    /**
     * @api {get} ?thread=:thread
     * @apiName Get Comments
     * @apiDescription Gets all comments that belong to the given thread
     * @apiParam {int} thread Thread from which to get comments
     */
    public function get(): void
    {
        if (!isset($_GET['thread'])) {
            http_response_code(400);
            echo json_encode(new APIMessage(
                HttpStatus::S400(),
                'No thread supplied',
                []
            ));
            die();
        }

        $comments = $this->em->getRepository(Comment::class)->findBy(['thread' => $_GET['thread']]);

        $data = array_map(static function(Comment $c) {
            $c->parse();
            $a = $c->getAuthor();
            return [
                'user' => $a->getName(),
                'user_id' => $a->getId(),
                'avatar' => $a->getAvatar() ? '//'.CONFIG['cdn domain'].'/file/Omnibus/' . $a->getAvatar() : (new Gravatar($a->getEmail(), 50))->getGravatar(),
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
        die();
    }


    /**
     * @api {post}
     * @apiName Add Comment
     * @apiDescription Adds a comment to the given thread
     * @apiParam {string} token X-CSRF token
     * @apiParam {int} thread Thread to which add the comment
     * @apiParam {string} body Body of the comment
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
        die();

    }



    /**
     * @api {post}
     * @apiName Report Comment
     * @apiDescription Reports the given comment
     * @apiParam {string} token X-CSRF token
     * @apiParam {int} comment Comment to report
     * @apiParam {string} reason Reason for the report
     */
    public function report(): void
    {
        $errors = [];
        $status = null;
        $msg = null;

        $POST = filter_input_array(INPUT_POST, [
            'token'   => FILTER_SANITIZE_STRING,
            'comment' => FILTER_VALIDATE_INT,
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
            $report->setReason($_POST['reason'] ?: '');

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
            $errors[] = 'X-CSRF protection triggered';
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
        die();

    }

    public function getReports(): void
    {
        $comments = $this->em->getRepository(Comment::class)
                             ->createQueryBuilder('comments')
                             ->select('c')
                             ->from(Comment::class, 'c')
                             ->innerJoin('c.reports', 'r')//, 'WITH', 'c.id = ?1', 'r.comment')
                             ->getQuery()
                             ->getResult();

        http_response_code(200);
        echo json_encode(new APIMessage(
            HttpStatus::S200(),
            'Retrieved succesfully',
            [],
            $comments
        ));
        die();
    }


    /**
     * @api {post}
     * @apiName Accept Comment
     * @apiDescription Removes all reports from the given comment
     * @apiParam {string} token X-CSRF token
     * @apiParam {int} comment Comment to accept
     */
    public function accept(): void
    {
        $errors = [];

        $POST = filter_input_array(INPUT_POST, [
            'token'   => FILTER_SANITIZE_STRING,
            'id' => FILTER_VALIDATE_INT,
        ]);


        if ($POST['token'] === $this->session->get('token')) {

            $role = $this->getRole();
            if ($role && $role->canModerateComments()) {

                $comment = null;
                try {
                    $comment = $this->em->find(Comment::class, $POST['id']);
                } catch (OptimisticLockException $e) {
                    $errors[] = '1::'.$e->getMessage();
                } catch (TransactionRequiredException $e) {
                    $errors[] = '2::'.$e->getMessage();
                } catch (ORMException $e) {
                    $errors[] = '3::'.$e->getMessage();
                }

                if (!$errors && $comment) {
                    try {
                        foreach ($comment->getReports() as $r) {
                            $this->em->remove($r);
                        }
                    } catch (ORMException $e) {
                        $errors[] = '4::'.$e->getMessage();
                    }

                    try {
                        $this->em->flush();
                    } catch (OptimisticLockException $e) {
                        $errors[] = '5::'.$e->getMessage();
                    } catch (ORMException $e) {
                        $errors[] = '6::'.$e->getMessage();
                    }
                } else {
                    $errors[] = 'Could not approve comment';
                }

            } else {
                $errors[] = 'Insufficient permissions';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered';
        }

        // Set status
        if ($errors) {
            $status = HttpStatus::S500();
        } else {
            $status = HttpStatus::S200();
        }

        http_response_code($status->code);
        echo json_encode(new APIMessage(
            $status,
            $errors ? 'An error has occurred:' : 'Comment approved',
            $errors
        ));
        die();
    }


    /**
     * @api {post}
     * @apiName Delete Comment
     * @apiDescription Removes the given comment
     * @apiParam {string} token X-CSRF token
     * @apiParam {int} comment Comment to delete
     */
    public function delete(): void
    {
        $errors = [];

        $POST = filter_input_array(INPUT_POST, [
            'token'   => FILTER_SANITIZE_STRING,
            'id' => FILTER_VALIDATE_INT,
        ]);


        if ($POST['token'] === $this->session->get('token')) {

            $role = $this->getRole();
            if ($role && $role->canModerateComments()) {

                /** @var Comment $comment */
                $comment = null;
                try {
                    $comment = $this->em->find(Comment::class, ['id' => $POST['id']]);
                } catch (OptimisticLockException $e) {
                    $errors[] = $e->getMessage();
                } catch (TransactionRequiredException $e) {
                    $errors[] = $e->getMessage();
                } catch (ORMException $e) {
                    $errors[] = $e->getMessage();
                }

                try {
                    $this->em->remove($comment);
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
                $errors[] = 'Insufficient permissions';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered';
        }

        // Set status
        if ($errors) {
            $status = HttpStatus::S500();
        } else {
            $status = HttpStatus::S200();
        }

        http_response_code($status->code);
        echo json_encode(new APIMessage(
            $status,
            $errors ? 'An error has occurred:' : 'Deleted succesfully',
            $errors
        ));
        die();
    }

}
