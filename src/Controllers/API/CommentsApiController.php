<?php
namespace Controllers\API;

use Core\Controller;
use Core\Utility\Gravatar;
use Core\Utility\HttpStatus;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Models\Comment;
use Models\CommentThread;

class CommentsApiController extends Controller
{

    public function get(): void
    {
        $comments = $this->em->getRepository(Comment::class)->findBy(['thread' => $_GET['thread']]);

        $data = array_map(static function(Comment $c) {
            $c->parse();
            $a = $c->getAuthor();
            return [
                'user' => $a->getName(),
                'avatar' => (new Gravatar($a->getEmail(), 50))->getGravatar(),
                'date' => $c->getDate()->format('d.m.Y H:i'),
                'body' => $c->getBody(),
                'id'   => $c->getId(),
            ];
        }, $comments);

        $msg = [
            'status' => HttpStatus::S200(),
            'comments' => $data,
        ];
        echo json_encode($msg);
    }


    public function add(): void
    {
        $errors = [];

        if($_POST['token'] === $this->session->get('token')) {

            $thread = null;
            try {
                $thread = $this->em->find(CommentThread::class, $_POST['thread']);
            } catch (OptimisticLockException $e) {
                $errors[] = $e->getMessage();
            } catch (TransactionRequiredException $e) {
                $errors[] = $e->getMessage();
            } catch (ORMException $e) {
                $errors[] = $e->getMessage();
            }

            $comment = new Comment();
            $comment->setAuthor($this->getUser());
            $comment->setBody($_POST['body']);
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
            $errors[] = 'X-CSRD protection triggered';
        }

        $msg = [
            'status' => $errors ? HttpStatus::S500() : HttpStatus::S201(),
            'message' => $errors ? 'Successfully added the comment.' : 'An error has occurred:',
            'errors' => $errors ?: null,
        ];

        http_response_code($errors ? 500 : 201);
        echo json_encode($msg);

    }

}
