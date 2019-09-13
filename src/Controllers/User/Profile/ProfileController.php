<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User\Profile;

use Omnibus\Models\User;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Models\CommentThread;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;


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
            'profile_owner' => $owner,
            'thread' => $thread,
        ]);
    }

}
