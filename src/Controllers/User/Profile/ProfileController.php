<?php
namespace Controllers\User\Profile;

use Core\Controller;
use Core\Utility\Gravatar;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Models\Comment;
use Models\CommentThread;
use Models\User;

class ProfileController extends Controller
{

    /**
     * @param array $params
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function index(array $params): void
    {
        /** @var User $owner */
        $owner = ($params && $params['id']) ? $this->em->find(User::class, $params['id']) : null;
        /** @var CommentThread $thread */
        $thread = $owner ? $owner->getCommentThread() : $this->getUser()->getCommentThread();

        $comments = $this->em->getRepository(Comment::class)->findBy(['thread' => $thread]);

        $this->setBaseData();
        $this->render('user/profile/profile', [
            'data' => var_export($params, true),
            'profile_owner' => $owner,
            'thread' => $thread,
            'comments' => array_map(static function(Comment $c) {
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
            }, $comments)
        ]);
    }

}
