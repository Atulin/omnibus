<?php

namespace Controllers\User;

use Core\Controller;
use Core\Security\PasswordUtils;
use Core\Security\ReCaptcha\ReCaptchaHandler;
use Core\Security\Token;
use Core\Utility\APIMessage;
use Core\Utility\Email;
use Core\Utility\HttpStatus;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Exception;
use Models\ActivationCode;
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
        // Friendlify variables
        $name    = (string)$_POST['name'];
        $email   = (string)$_POST['email'];
        $pass    = (string)$_POST['password'];
        $pass2   = (string)$_POST['password2'];
        $tos     = $_POST['tos'] ?? '';
        $captcha = $_POST['g-recaptcha-response'];
        $code    = null;

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

            // Check identical passwords
            if ($pass !== $pass2) {
                $this->messages[] = 'Passwords have to be identical.';
            }

            // Validate password
            $messages = PasswordUtils::Check($pass);

            if ($messages) {
                $this->messages = array_merge($this->messages, $messages);
            }

            // Check email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->messages[] = 'The email format is invalid.';
            }
            // Check TOS
            if (!$tos) {
                $this->messages[] = 'You have to agree to Terms of Service.';
            }

            // Generate a unique activation code
            do {
                $code = Token::Get(32);
            } while ($this->em->getRepository(ActivationCode::class)->count(['code' => $code]) !== 0);

            // If all's fine, create user
            if (count($this->messages) <= 0) {
                $u = new User();
                $u->setName($name);
                $u->setEmail($email);
                $u->setPassword(password_hash($pass, PASSWORD_ARGON2I));

                $this->em->persist($u);

                // Check if user exists
                try {
                    $this->em->flush();

                    // Insert activation code
                    $ac = new ActivationCode();
                    $ac->setUserId($u->getId());
                    $ac->setCode($code);

                    $this->em->persist($ac);

                    try {
                        $this->em->flush();
                    } catch (Exception $e) {
                        $this->messages[] = 'Could not create activation token.';
                        $this->messages[] = $e->getMessage();
                    }

                } catch (UniqueConstraintViolationException $e) {
                    $this->messages[] = 'User with that name or email already exists.';
                }
            }
        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        if (count($this->messages) > 0) {

            $this->index();

        } else {

            $em = new Email();
            $em ->setSubject('Omnibus – registration')
                ->setToEmail($email)
                ->setToName($name)
                ->setFromEmail('admin@omnibus.org')
                ->setFromName('Admin')
                ->setBody('activation', ['name' => $name, 'code' => $code])
                ->Send();

            $this->session->set('message', 'Registration successful! Confirmation email should arrive shortly.');
            header('Location: /');
            die();
        }
    }


    /**
     * Returns true if OK
     * @api {get} ?name=:name&email=:email&token=:token& Validate
     * @apiName Validate
     * @apiDescription Validate whether the username or email is taken and whether or not the email is valid
     * @apiParam {string} name Name to check
     * @apiParam {string} email Email to check
     * @apiParam {string} token CSRF token
     */
    public function validate(): void
    {
        if ($this->session->get('token') === $_GET['token']) {
            $name = $this->em->getRepository(User::class)->count(['name' => $_GET['name']]) === 0;
            $email = $this->em->getRepository(User::class)->count(['email' => $_GET['email']]) === 0;

            $is_email = filter_var($_GET['email'], FILTER_VALIDATE_EMAIL);

            $is_valid = $name&&$email&&$is_email;

            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(new APIMessage(
                $is_valid ? HttpStatus::S200() : HttpStatus::S409(),
                $is_valid ? 'Valid' : 'Invalid. Check data for more details.',
                [],
                [
                    'name' => $name,
                    'email' => $email,
                    'is_email' => $is_email ? true : false
                ]
            ));
        } else {
            http_response_code(401);
            die('X-CSRF protection triggered');
        }
    }
}
