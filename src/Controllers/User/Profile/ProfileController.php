<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

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

        $this->setBaseData();
        $this->render('user/profile/profile', [
            'data' => var_export($params, true),
            'profile_owner' => $owner,
            'thread' => $thread,
        ]);
    }

}
