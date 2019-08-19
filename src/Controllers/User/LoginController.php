<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Controllers\User;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Models\User;
use RobThree\Auth\TwoFactorAuth;

class LoginController extends Controller
{
    private $messages = [];

    public function index(array $params = [], bool $has_mfa = false): void
    {
        $this->setBaseData();
        $this->render('user/login', [
            'messages' => $this->messages,
            'message'  => $this->session->get('message'),
            'has_mfa'  => $has_mfa,
        ]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function login(): void
    {
        $mfa = new TwoFactorAuth();

        $login    = $_POST['login'];
        $pass     = $_POST['password'];
        $remember = $_POST['remember'] ?? '';
        $mfa_code = $_POST['mfa'] ?? null;

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['name' => $login]);

        // Check X-CSRF
        if ($this->session->get('token') === $_POST['token']) {

            // Check if user exists
            if ($user) {

                // Check if user has MFA, redirect to MFA form if so
                if ($mfa_code === null && $user->getMfa()) {
                    $this->index([], true);
                    return;
                }

                // Check password
                if (password_verify($pass, $user->getPassword())) {

                    // Check if user has an active account
                    if (!$this->isActive()) {

                        // Check MFA code
                        if (
                            (!$user->getMfa() && $mfa_code === null) ||                         // User has no MFA set up, no MFA sent from form
                            ($user->getMfa() && $mfa->verifyCode($user->getMfa(), $mfa_code))   // User has MFA set up, MFA sent from form is correct
                        ) {

                            // Log the user in via session
                            $this->session->set('userid', $user->getId());
                            $this->session->set('message', "Welcome back, {$user->getName()}");

                            // Set RememberMe
                            if ($remember !== null) {
                                $r_token = bin2hex(random_bytes(256));

                                $cookie = $user->getId() . ':' . $r_token;
                                $mac = password_hash($cookie, PASSWORD_BCRYPT);
                                $cookie .= ':' . $mac;
                                setcookie('__Secure-rememberme', $cookie, time() + 2592000, '/', '', true, true);

                                $user->setRememberme($r_token);
                                $this->em->flush();
                            }

                            header('Location: /');
                            die();

                        } else {
                            $this->messages[] = 'Incorrect 2FA token';
                            $needs_mfa = true;
                        }

                    } else {
                        $this->messages[] = 'Account not activated.';
                    }

                } else {
                    $this->messages[] = 'Wrong password.';
                }

            } else {
                $this->messages[] = 'User does not exist.';
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        $this->index([], $needs_mfa ?? false);
    }
}
