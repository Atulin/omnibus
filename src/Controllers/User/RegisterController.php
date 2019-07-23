<?php

namespace Controllers\User;

use Core\Controller;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Models\User;

/**
 * Class RegisterController
 * @package Controllers\User
 */
class RegisterController extends Controller
{
    /**
     * @var array
     */
    private $messages = [];

    /**
     * Render the page
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/register', ['messages' => $this->messages]);
    }

    /**
     * Register new user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function register(): void
    {
        if ($this->session->get('token') === $_POST['token']) {

            // Friendlify variables
            $name  = $_POST['name'];
            $email = $_POST['email'];
            $pass  = $_POST['password'];
            $pass2 = $_POST['password2'];
            $tos   = $_POST['tos'];

            // Check passwords
            if ($pass !== $pass2) {
                $this->messages[] = 'Passwords have to be identical.';
            }
            // Check email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->messages[] = 'The email format is invalid.';
            }
            // Check TOS
            if (!$tos) {
                $this->messages[] = 'You have to agree to Terms of Service.';
            }

            // If all's fine, create user
            if (count($this->messages) <= 0) {
                $u = new User();
                $u->setName($name);
                $u->setEmail($email);
                $u->setPassword(password_hash($pass, PASSWORD_ARGON2I));

                $this->em->persist($u);
            }

            // Check if user exists
            try {
                $this->em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->messages[] = 'User with that name or email already exists.';
            }
        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        if (count($this->messages) > 0) {
            $this->index();
        } else {
            $this->session->set('message', 'Registration successful! Confirmation email should arrive shortly.');
            header('Location: /');
        }
    }


    /**
     * Validate whether the username or email is taken and whether or not the email is valid
     * Returns true if OK
     */
    public function validate(): void
    {
        if ($this->session->get('token') === $_GET['token']) {
            $name = $this->em->getRepository(User::class)->count(['name' => $_GET['name']]) === 0;
            $email = $this->em->getRepository(User::class)->count(['email' => $_GET['email']]) === 0;

            $is_email = filter_var($email, FILTER_VALIDATE_EMAIL);

            header('Content-Type: application/json');
            echo json_encode([
                'name' => $name,
                'email' => $email,
                'is_email' => $is_email
            ]);
        } else {
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Access Denied');
            die('X-CSRF protection triggered');
        }
    }
}
