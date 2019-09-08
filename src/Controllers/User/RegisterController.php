<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User;

use Omnibus\Models\User;
use Omnibus\Core\Controller;
use Omnibus\Core\Utility\Email;
use Omnibus\Core\Security\Token;
use Omnibus\Models\ActivationCode;
use Omnibus\Core\Utility\APIMessage;
use Omnibus\Core\Utility\HttpStatus;
use Omnibus\Core\Security\PasswordUtils;
use Omnibus\Models\Repositories\UserRepository;
use Omnibus\Core\Security\ReCaptcha\ReCaptchaHandler;
use Omnibus\Models\Repositories\ActivationCodeRepository;


/**
 * Class RegisterController
 * @package Omnibus\Controllers\User
 */
class RegisterController extends Controller
{
    /** @var array $errors */
    private $errors = [];

    /**
     * Render the page
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/register', ['messages' => $this->errors]);
    }

    /**
     * Register new user
     */
    public function register(): void
    {
        // Friendlify variables
        $name    = (string)$_POST['name'];
        $email   = (string)$_POST['email'];
        $pass    = (string)$_POST['password'];
        $pass2   = (string)$_POST['password2'];
        $tos     = $_POST['tos'] ?? '';
        $captcha = $_POST['g-recaptcha-response'] ?? '';
        $code    = null;

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

            // Check identical passwords
            if ($pass !== $pass2) {
                $this->errors[] = 'Passwords have to be identical.';
            }

            // Validate password
            $messages = PasswordUtils::Check($pass);
            if ($messages) {
                $this->errors = array_merge($this->errors, $messages);
            }

            // Check email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->errors[] = 'The email format is invalid.';
            }

            // Check TOS
            if (!$tos) {
                $this->errors[] = 'You have to agree to Terms of Service.';
            }

            // If all's fine, create user
            if (!$this->errors) {

                // Insert user
                $u = new User();
                $ur = new UserRepository();
                $u->setName($name)
                  ->setEmail($email)
                  ->setPassword(password_hash($pass, PASSWORD_ARGON2I));
                $this->errors = array_merge($this->errors, $ur->save($u));

                if (!$this->errors) {
                    // Generate a unique activation code
                    do {
                        $code = Token::Get();
                    } while ($this->em->getRepository(ActivationCode::class)->count(['code' => $code]) !== 0);

                    // Insert activation code
                    $ac = new ActivationCode();
                    $acr = new ActivationCodeRepository();
                    $ac->setUserId($u->getId())
                        ->setCode($code);
                    $this->errors = array_merge($this->errors, $acr->save($ac));
                }
            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page.';
        }

        if ($this->errors) {

            $this->session->set('token', $this->getToken());
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

            $this->session->set('token', $this->getToken());
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
