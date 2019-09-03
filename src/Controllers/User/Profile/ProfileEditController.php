<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 07:06
 */

namespace Omnibus\Controllers\User\Profile;

use Exception;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\FileHandler;
use Doctrine\ORM\OptimisticLockException;
use GuzzleHttp\Exception\GuzzleException;
use BackblazeB2\Exceptions\NotFoundException;


class ProfileEditController extends Controller
{
    private $errors = [];

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/profile/edit-profile', ['messages' => $this->errors]);
    }

    /**
     */
    public function edit(): void
    {
        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'title' => FILTER_SANITIZE_STRING,
            'bio'   => FILTER_SANITIZE_STRING
        ]);


        if ($POST['token'] === $this->session->get('token')) {

            $u = $this->getUser();

            if (strlen($POST['title']) < 20) {
                $u->setTitle($POST['title']);
            } else {
                $this->errors[] = "Title can't be longer than 20 characters";
            }

            if (strlen($POST['bio']) < 2000) {
                $u->setBio($POST['bio']);
            } else {
                $this->errors[] = "Bio can't be longer than 2000 characters";
            }

            if (isset($_FILES['avatar']) && !$_FILES['avatar']['error']) {

                if ($_FILES['avatar']['size'] < CONFIG['file sizes']['avatar']) {

                    $fh = new FileHandler();

                    // Delete old avatar if it exists
                    $file_arr = explode('/', $u->getAvatar());
                    if ($u->getAvatar()) {
                        try {
                            $fh->delete('avatars/' . $file_arr[array_key_last($file_arr)]);
                        } catch (NotFoundException | GuzzleException | Exception $e) {
                            $this->errors[] = 'Could not remove old avatar.';
                        }
                    }

                    // Upload new avatar
                    $name = $fh->upload($_FILES['avatar'], 'avatars/' . $u->getName());
                    $u->setAvatar($name);

                } else {
                    $this->errors[] = 'File too big. Maximum size is ' . CONFIG['file sizes']['avatar']/1024 . ' KB';
                }

            }

            if (!$this->errors) {
                try {
                    $this->em->flush();
                } catch (OptimisticLockException | ORMException $e) {
                    $this->errors[] = 'Could not update information.';
                }
            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page';
        }

        if ($this->errors) {
            $this->index();
        } else {
            $this->session->set('message', 'Profile information edited successfully!');
            header('Location: /profile');
        }

    }

}
