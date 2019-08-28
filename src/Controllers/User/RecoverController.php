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
use Omnibus\Core\Security\PasswordUtils;
use Doctrine\ORM\OptimisticLockException;
use Omnibus\Core\Security\ReCaptcha\ReCaptchaHandler;


class RecoverController extends Controller
{
    private $messages = [];

    /**
     * @param $params
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function recover($params)
    {
        // Friendlify variables 6f88d606fa9b5dfa1a152ec9f5438702
        $name    = (string)$_POST['name'];
        $email   = (string)$_POST['email'];
        $pass    = (string)$_POST['password'];
        $pass2   = (string)$_POST['password2'];
        $captcha = $_POST['g-recaptcha-response'];
        $code    = $params['code'];

        if ($this->session->get('token') === $_POST['token']) {

            // Check captcha
            $ch = new ReCaptchaHandler(
                $_ENV['CAPTCHA_SECRET'],
                $captcha
            );
            $res = $ch->Check();

            if (!$res->isSuccess()) {
                $this->messages[] = 'Incorrect ReCaptcha';
            }

            // Try to get the user
            /** @var User $user */
            $user = $this->em->getRepository(User::class)->findOneBy(['name' => $name, 'email' => $email]);

            if (!$user) {
                $this->messages[] = 'User does not exist';
            }

            // Try to get the token
            /** @var RecoveryCode $token */
            $token = $this->em->getRepository(RecoveryCode::class)->findOneBy(['code' => $code]);

            if (!$token) {
                $this->messages[] = 'Incorrect recovery code';
            }

            // Check if user matches the token
            if (!$this->messages && $user->getId() === $token->getUserId()) {

                // Check identical passwords
                if ($pass !== $pass2) {
                    $this->messages[] = 'Passwords have to be identical.';
                }

                // Validate password
                $messages = PasswordUtils::Check($pass);

                if ($messages) {
                    $this->messages = array_merge($this->messages, $messages);
                } else {

                    // Set new password
                    $hash = password_hash($pass, PASSWORD_ARGON2I);
                    $user->setPassword($hash);

                    // Remove token
                    $this->em->remove($token);

                    // Flush
                    $this->em->flush();

                }

            } else {
                $this->messages[] = 'The user and code don\'t match';
            }


        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        if (count($this->messages) > 0) {

            $this->index([]);

        } else {

            $em = new Email();
            $em->setSubject('Omnibus – restore password')
                ->setToEmail($email)
                ->setToName($name)
                ->setFromEmail('admin@omnibus.org')
                ->setFromName('Admin')
                ->setBody('pass-recovered', ['name' => $name])
                ->Send();

            $this->session->set('message', 'Registration successful! Confirmation email should arrive shortly.');
            header('Location: /');

        }
    }

    public function index($params): void
    {
        $this->setBaseData();
        $this->render('user/recover', ['messages' => $this->messages]);
    }
}
