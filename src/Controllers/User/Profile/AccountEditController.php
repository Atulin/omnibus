<?php
namespace Controllers\User\Profile;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Models\User;

class AccountEditController extends Controller
{

    private $messages;

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/profile/account', ['messages' => $this->messages]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function edit(): void
    {
        $POST = filter_input_array(INPUT_POST, [
            'token'    => FILTER_SANITIZE_STRING,
            'email'    => FILTER_SANITIZE_EMAIL
        ]);


        if ($POST['token'] === $this->session->get('token')) {

            $u = $this->getUser();

            if (password_verify($_POST['password'], $u->getPassword())) {

                if ($POST['email'] && filter_var($POST['email'], FILTER_VALIDATE_EMAIL)) {
                    $u->setEmail($POST['email']);
                } else {
                    $this->messages[] = 'The email format is invalid.';
                }

                if ($_POST['new_password']) {
                    $pass = password_hash($_POST['new_password'], PASSWORD_ARGON2I);
                    $u->setPassword($pass);
                }

                if (!$this->messages) {
                    $this->em->flush();
                }

            } else {
                $this->messages[] = 'Incorrect password';
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered';
        }

        if ($this->messages) {
            $this->index();
        } else {
            $this->session->set('message', 'Account information edited successfully!');
            header('Location: /profile');
        }

    }

    public function validate(): void
    {
        if ($this->session->get('token') === $_GET['token']) {
            $email = $this->em->getRepository(User::class)->count(['email' => $_GET['email']]) === 0
                     && $_GET['email'] !== $this->getUser()->getEmail();

            $is_email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);

            header('Content-Type: application/json');
            echo json_encode([
                'email' => $email,
                'is_email' => $is_email ? true : false
            ]);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Access Denied');
            die('X-CSRF protection triggered');
        }
    }

}
