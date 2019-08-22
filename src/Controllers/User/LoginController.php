<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Controllers\User;

use Exception;
use Models\User;
use Core\Controller;
use Core\Utility\Gravatar;
use Core\Utility\APIMessage;
use Core\Utility\HttpStatus;
use Doctrine\ORM\ORMException;
use RobThree\Auth\TwoFactorAuth;
use Doctrine\ORM\OptimisticLockException;


/**
 * Class LoginController
 * @package Controllers\User
 */
class LoginController extends Controller
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     *
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/login', ['messages' => $this->messages, 'message' => $this->session->get('message')]);
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function login(): void
    {
        $mfa = new TwoFactorAuth();

        $login = $_POST['login'];
        $pass = $_POST['password'];
        $remember = $_POST['remember'] ?? '';
        $mfa_code = $_POST['mfa'] ?? null;

        /** @var User $user */
        $user = $this->em->getRepository(User::class)->findOneBy(['name' => $login]);

        // Check X-CSRF
        if ($this->session->get('token') === $_POST['token']) {

            // Check if user exists and verify the password
            if ($user && password_verify($pass, $user->getPassword())) {

                // Check if user has an active account
                if (!$this->isActive()) {

                    // Check MFA code
                    if ((!$user->getMfa() && $mfa_code === null) ||                         // User has no MFA set up, no MFA sent from form
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
                        $this->messages[] = 'Incorrect 2FA token.';
                    }

                } else {
                    $this->messages[] = 'Account not activated.';
                }

            } else {
                $this->messages[] = 'Incorrect credentials.';
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        $this->index();
    }


    /**
     *
     */
    public function validate(): void
    {
        $GET = filter_var_array($_GET, ['login' => FILTER_SANITIZE_STRING]);

        if ($this->session->get('token') === $_GET['token']) {

            /** @var User $u */
            $u = $this->em->getRepository(User::class)->findOneBy(['name' => $GET['login']]);

            if ($u) {
                $data = [
                    'avatar' => $u->getAvatar()
                        ? '//'.CONFIG['cdn domain'].'/file/Omnibus/' . $u->getAvatar()
                        : (new Gravatar($u->getEmail()))->getGravatar(),
                    'mfa' => $u->getMfa() !== null,
                ];
            } else {
                $data = [
                    'avatar' => (new Gravatar($GET['login'].'@mail.ph'))->getGravatar(),
                    'mfa' => false
                ];
            }

            http_response_code(200);
            echo json_encode(new APIMessage (
                HttpStatus::S200(),
                'Data retrieved',
                [],
                $data
            ));

        } else {

            http_response_code(401);
            echo json_encode(new APIMessage (
                HttpStatus::S401(),
                'X-CSRF protection triggered.',
                []
            ));
        }
    }
}
