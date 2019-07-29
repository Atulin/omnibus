<?php
namespace Controllers\User;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\TwoFactorAuthException;

class MFAController extends Controller
{
    /** @var array $messages */
    private $messages;
    /** @var */
    private $mfa;

    /**
     * @throws TwoFactorAuthException
     */
    public function index(): void
    {
        // Set MFA
        $this->mfa = new TwoFactorAuth('Omnibus');

        // Set secret
        $secret = $this->mfa->createSecret();
        $this->session->set('mfa-secret', $secret);

        // Get QR code
        $qr = $this->mfa->getQRCodeImageAsDataUri('Omnibus', $secret);

        $this->setBaseData();
        $this->render('user/mfa', [
            'messages' => $this->messages,
            'qr' => $qr
        ]);
    }

    /**
     * @throws TwoFactorAuthException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function setup(): void
    {
        // Set MFA
        $this->mfa = new TwoFactorAuth('Omnibus');

        $code = $_POST['2fa'];

        $user = $this->getUser();

        if ($this->session->get('token') === $_POST['token']) {

            // Verify 2FA
            $result = $this->mfa->verifyCode($this->session->get('mfa-secret'), $code);

            if ($result) {
                $user->setMfa($this->session->get('mfa-secret'));
                $this->em->flush();

                $this->session->remove('mfa-secret');

                $this->session->set('message', 'Multi-factor authentication set up succesfully!');
                header('Location: /');
                die();

            } else {
                $this->messages[] = 'Error setting up MFA. Try again.';
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        $this->index();
    }
}
