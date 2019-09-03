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
use Symfony\Component\Console\Exception\CommandNotFoundException;


/**
 * Class MFAController
 * @package Omnibus\Controllers\User
 */
class MFAController extends Controller
{
    /** @var array $errors */
    private $errors;
    /** @var */
    private $mfa;

    /**
     * @throws TwoFactorAuthException
     */
    public function index(): void
    {
        $data = ['messages' => $this->errors];
        $template = 'user/setup-mfa';

        $user = $this->getUser();
        if (!$user->getMfa()) {

            // Set MFA
            $this->mfa = new TwoFactorAuth(CONFIG['name']);

            // Set secret
            $secret = $this->mfa->createSecret();
            $this->session->set('mfa-secret', $secret ?? '');

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
                    try {
                        $this->em->flush();
                    } catch (OptimisticLockException | ORMException $e) {
                        $this->errors[] = 'Could not set up 2FA.';
                    }

                    $this->session->remove('mfa-secret');
                    $this->session->set('message', 'Multi-factor authentication set up succesfully!');

                    header('Location: /');
                    die();

                } else {
                    $this->errors[] = 'Error setting up MFA. Try again.';
                }

            } else {
                $this->errors[] = '2FA has already been set up.';
            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page.';
        }

        $this->index();
    }

    /**
     * @throws TwoFactorAuthException
     */
    public function remove(): void
    {
        // Set MFA
        $user = $this->getUser();
        if ($this->session->get('token') === $_POST['token']) {

            if ($user->getMfa()) {

                // Remove MFA token
                $user->setMfa(null);
                try {
                    $this->em->flush();
                } catch (OptimisticLockException | ORMException $e) {
                    $this->errors[] = 'Could not remove 2FA.';
                }

                $this->session->set('message', 'Multi-factor authentication removed successfully!');

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
                $this->errors[] = 'No 2FA has been set up.';
            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page.';
        }
        $this->index();
    }
}
