<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Controllers\User;

use Core\Controller;
use Core\Security\Token;
use Core\Utility\Email;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Models\RecoveryCode;
use Models\User;

class ForgottenPasswordController extends Controller
{
    private $messages = [];

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function forgot(): void
    {
        if ($this->session->get('token') === $_POST['token']) {
            $email = $_POST['email'];

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                /** @var User $user */
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user) {
                    // Generate a unique activation code
                    $code = null;
                    do {
                        $code = Token::Get(32);
                    } while ($this->em->getRepository(RecoveryCode::class)->count(['code' => $code]) !== 0);

                    $rc = new RecoveryCode();
                    $rc->setUserId($user->getId());
                    $rc->setCode($code);

                    $this->em->persist($rc);
                    $this->em->flush($rc);

                    $em = new Email();
                    $em->setSubject('Omnibus – forgotten password')
                        ->setToEmail($email)
                        ->setToName($user->getName())
                        ->setFromEmail('admin@omnibus.org')
                        ->setFromName('Admin')
                        ->setBody('pass-forgot', ['name' => $user->getName(), 'code' => $code])
                        ->Send();

                    $this->session->set('message', 'An email with password recovery instructions should arrive shortly.');
                    header('Location: /');
                    die();

                } else {
                    $this->messages[] = 'User does not exist';
                }

            } else {
                $this->messages[] = 'Invalid email.';
            }
        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        $this->index();
    }

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/forgot', ['messages' => $this->messages]);
    }
}
