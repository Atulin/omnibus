<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User;

use Omnibus\Models\User;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\Email;
use Omnibus\Models\RecoveryCode;
use Omnibus\Core\Security\Token;
use Doctrine\ORM\OptimisticLockException;


class ForgottenPasswordController extends Controller
{
    private $errors = [];

    /**
     */
    public function forgot(): void
    {
        if ($this->session->get('token') === $_POST['token']) {
            $email = $_POST['email'];

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                /** @var User $user */
                $user = $this->em->getRepository(User::class)->findOneBy(['email' => $email]);

                if ($user) {

                    // Generate a unique restore code
                    $code = null;
                    do {
                        $code = Token::Get();
                    } while ($this->em->getRepository(RecoveryCode::class)->count(['code' => $code]) !== 0);

                    $rc = new RecoveryCode();
                    $rc->setUserId($user->getId());
                    $rc->setCode($code);

                    try {
                        $this->em->persist($rc);
                    } catch (ORMException $e) {
                        $this->errors[] = 'Could not create restoration token. Contact the administrator.';
                    }

                    try {
                        $this->em->flush($rc);
                    } catch (OptimisticLockException| ORMException $e) {
                        $this->errors[] = 'Could not create restoration token. Contact the administrator.';
                    }

                    $em = new Email();
                    $em->setSubject('Omnibus – forgotten password')
                        ->setToEmail($email)
                        ->setToName($user->getName())
                        ->setFromEmail('admin@omnibus.org')
                        ->setFromName('Admin')
                        ->setBody('pass-forgot', ['name' => $user->getName(), 'code' => $code])
                        ->Send();

                    $this->session->set('token', $this->getToken());
                    $this->session->set('message', 'An email with password recovery instructions should arrive shortly.');
                    header('Location: /');
                    die();

                } else {
                    $this->errors[] = 'User does not exist';
                }

            } else {
                $this->errors[] = 'Invalid email.';
            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page.';
        }

        $this->index();
    }

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/forgot', ['messages' => $this->errors]);
    }
}
