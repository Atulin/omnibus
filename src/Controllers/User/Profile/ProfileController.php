<?php
namespace Controllers\User\Profile;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
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
        $owner = ($params && $params['id']) ? $this->em->find(User::class, $params['id']) : null;

        $this->setBaseData();
        $this->render('user/profile/profile', [
            'data' => var_export($params, true),
            'profile_owner' => $owner
        ]);
    }

}
