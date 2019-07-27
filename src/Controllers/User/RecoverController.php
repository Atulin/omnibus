<?php
namespace Controllers\User;

use Core\Controller;
use Core\Security\ReCaptcha\ReCaptchaHandler;
use Core\Security\Token;
use Core\Utility\Email;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Models\User;

class RecoverController extends Controller
{
    private $messages;

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/recover', ['messages' => $this->messages]);
    }

    public function recover()
    {
        // Friendlify variables
        $name    = (string)$_POST['name'];
        $email   = (string)$_POST['email'];
        $pass    = (string)$_POST['password'];
        $pass2   = (string)$_POST['password2'];
        $captcha = $_POST['g-recaptcha-response'];

        if ($this->session->get('token') === $_POST['token']) {

            // Check captcha
            $ch = new ReCaptchaHandler(
                $_ENV['CAPTCHA_SECRET'],
                $captcha
            );
            $res = $ch->Check();
            echo '<pre>'.var_export($res, true).'</pre>';
            if (!$res->isSuccess()) {
                $this->messages[] = 'Incorrect ReCaptcha';
            }

            // Check identical passwords
            if ($pass !== $pass2) {
                $this->messages[] = 'Passwords have to be identical.';
            }
            // Check password length
            if (strlen($pass) < 10) {
                $this->messages[] = 'Password has to be at least 10 characters long.';
            }
            // Check password special chars
            if (!preg_match('/[_\W]/', $pass)) {
                $this->messages[] = 'Password needs at least one special character.';
            }
            // Check password numbers
            if (!preg_match('/[_0-9]/', $pass)) {
                $this->messages[] = 'Password needs at least one number.';
            }
            // Check password capital letters
            if (!preg_match('/[_A-Z]/', $pass)) {
                $this->messages[] = 'Password needs at least one capital letter.';
            }

            // If all's fine, create user
            if (count($this->messages) <= 0) {
                $user = $this->em->getRepository(User::class)->findOneBy(['name' => $name, 'email' => $email]);
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

        }
    }
}
