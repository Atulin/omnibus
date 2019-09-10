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
use Omnibus\Models\Repositories\UserRepository;
use Omnibus\Core\Security\ReCaptcha\ReCaptchaHandler;
use Omnibus\Models\Repositories\RecoveryCodeRepository;


class RecoverController extends Controller
{
    private $errors = [];

    public function index($params): void
    {
        $this->setBaseData();
        $this->render('user/recover', ['messages' => $this->errors]);
    }

    /**
     * @param $params
     */
    public function recover($params)
    {
        // Friendlify variables 6f88d606fa9b5dfa1a152ec9f5438702
        $name    = (string)$_POST['name'];
        $email   = (string)$_POST['email'];
        $pass    = (string)$_POST['password'];
        $pass2   = (string)$_POST['password2'];
        $captcha = $_POST['g-recaptcha-response'] ?? '';
        $code    = $params['code'];

        if ($this->session->get('token') === $_POST['token']) {

            // Check captcha
            $ch = new ReCaptchaHandler(
                $_ENV['CAPTCHA_SECRET'],
                $captcha
            );
            $res = $ch->Check();

            if (!$res->isSuccess()) {
                $this->errors[] = 'Incorrect ReCaptcha';
            }

            // Try to get the user
            /** @var User $user */
            $user = (new UserRepository())->findOneBy(['name' => $name, 'email' => $email]);

            if (!$user) {
                $this->errors[] = 'User does not exist';
            }

            // Try to get the token
            /** @var RecoveryCode $token */
            $token = (new RecoveryCodeRepository())->findOneBy(['code' => $code]);

            if (!$token) {
                $this->errors[] = 'Incorrect recovery code';
            }

            // Check if user matches the token
            if ($user->getId() !== $token->getUserId()) {
                $this->errors[] = 'The user and code don\'t match';
            }

            // Check identical passwords
            if ($pass !== $pass2) {
                $this->errors[] = 'Passwords have to be identical.';
            }

            // Validate password
            $messages = PasswordUtils::Check($pass);
            if ($messages) {
                $this->errors = array_merge($this->errors, $messages);
            }

            if (!$this->errors) {

                // Set new password
                $hash = password_hash($pass, PASSWORD_ARGON2I);
                $user->setPassword($hash);

                // Remove token
                try {
                    $this->em->remove($token);
                } catch (ORMException $e) {
                    $this->errors[] = 'Could not fully restore account. Contact the administrator.';
                }

                // Flush
                try {
                    $this->em->flush();
                } catch (OptimisticLockException | ORMException $e) {
                    $this->errors[] = 'Could not fully restore account. Contact the administrator.';
                }

                // Send an email
                $em = new Email();
                $em->setSubject('Omnibus – restore password')
                    ->setToEmail($email)
                    ->setToName($name)
                    ->setFromEmail('admin@omnibus.org')
                    ->setFromName('Admin')
                    ->setBody('pass-recovered', ['name' => $name])
                    ->Send();

                $this->session->set('message', 'Recovery successful! You can log in with your new password now.');
                header('Location: /');

            } else {

                $this->session->set('token', $this->getToken());
                $this->index([]);

            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page.';
        }
    }
}
