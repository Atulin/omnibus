<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 06:53
 */

namespace Omnibus\Controllers\User;

use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\Email;
use RobThree\Auth\TwoFactorAuth;
use Doctrine\ORM\OptimisticLockException;
use RobThree\Auth\TwoFactorAuthException;


/**
 * Class MFAController
 * @package Omnibus\Controllers\User
 */
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
        $data = ['messages' => $this->messages];
        $template = 'user/setup-mfa';

        if (!$this->getUser()->getMfa()) {

            // Set MFA
            $this->mfa = new TwoFactorAuth('Omnibus');

            // Set secret
            $secret = $this->mfa->createSecret();
            $this->session->set('mfa-secret', $secret);

            // Get QR code
            $qr = $this->mfa->getQRCodeImageAsDataUri('Omnibus', $secret);
            $data['qr'] = $qr;

        } else {

            $template = 'user/remove-mfa';

        }

        $this->setBaseData();
        $this->render($template, $data);
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

            if (!$user->getMfa()) {

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
                $this->messages[] = '2FA has already been set up.';
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }

        $this->index();
    }

    /**
     * @throws TwoFactorAuthException
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(): void
    {
        // Set MFA
        $user = $this->getUser();
        if ($this->session->get('token') === $_POST['token']) {

            if ($user->getMfa()) {

                // Remove MFA token
                $user->setMfa(null);

                $this->em->flush();
                $this->session->set('message', 'Multi-factor authentication removed succesfully!');

                // Send information email
                $em = new Email();
                $em ->setSubject('Omnibus – 2FA removal')
                    ->setToEmail($user->getEmail())
                    ->setToName($user->getEmail())
                    ->setFromEmail('admin@omnibus.org')
                    ->setFromName('Admin')
                    ->setBody('mfa-removal', ['name' => $user->getName()])
                    ->Send();

                header('Location: /');
                die();

            } else {
                $this->messages[] = 'No 2FA has been set up.';
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered.';
        }
        $this->index();
    }
}
