<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User\Profile;

use Omnibus\Models\User;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;


class AccountEditController extends Controller
{

    private $errors;

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/profile/account', ['messages' => $this->errors]);
    }

    /**
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
                    $this->errors[] = 'The email format is invalid.';
                }

                if ($_POST['new_password']) {
                    $pass = password_hash($_POST['new_password'], PASSWORD_ARGON2I);
                    $u->setPassword($pass);
                }

                if (!$this->errors) {
                    try {
                        $this->em->flush();
                    } catch (OptimisticLockException | ORMException $e) {
                        $this->errors[] = 'Could not update information.';
                    }
                }

            } else {
                $this->errors[] = 'Incorrect password';
            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page';
        }

        if ($this->errors) {
            $this->index();
        } else {
            $this->session->set('message', 'Account information edited successfully!');
            header('Location: /profile');
        }

    }

    public function validate(): void
    {
        if ($this->session->get('token') === $_GET['token']) {

            $user = $this->getUser();

            $email = $this->em->getRepository(User::class)->count(['email' => $_GET['email']]) === 0
                     && $_GET['email'] !== $user->getEmail();

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
