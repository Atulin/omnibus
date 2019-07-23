<?php
namespace Controllers\User;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Models\User;

class LoginController extends Controller
{
    private $messages = [];

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/login', ['messages' => $this->messages]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function login(): void
    {
        $login    = $_POST['login'];
        $pass     = $_POST['password'];
        $remember = $_POST['remember'];

        $user = $this->em->getRepository(User::class)->findOneBy(['name' => $login]);

        if ($user) {
            if (password_verify($pass, $user->getPassword())) {

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
            } else {
                $this->messages[] = 'Wrong password.';
                $this->index();
            }
        } else {
            $this->messages[] = 'User does not exist.';
            $this->index();
        }
    }
}
