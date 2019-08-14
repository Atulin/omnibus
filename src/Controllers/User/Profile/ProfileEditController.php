<?php
namespace Controllers\User\Profile;

use BackblazeB2\File;
use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use BackblazeB2\Client;
use Exception;

class ProfileEditController extends Controller
{
    private $messages = [];

    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/profile/edit-profile', ['messages' => $this->messages]);
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
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
                $this->messages[] = "Title can't be longer than 20 characters";
            }

            if (strlen($POST['bio']) < 2000) {
                $u->setBio($POST['bio']);
            } else {
                $this->messages[] = "Bio can't be longer than 2000 characters";
            }

            if ($_FILES['avatar']['size'] < 200 * 1024) {
                $client = new Client($_SERVER['BACKBLAZE_ID'], $_SERVER['BACKBLAZE_MASTER']);
                /** @var File $file */
                $file = $client->upload([
                    'BucketName' => 'Omnibus',
                    'FileName' => 'avatars/' . $u->getName() . '.' . explode('/', $_FILES['avatar']['type'])[1],
                    'Body' => fopen($_FILES['avatar']['tmp_name'], 'rb')
                ]);
                $u->setAvatar('https://f002.backblazeb2.com/file/Omnibus/' . $file->getName());
            } else {
                $this->messages[] = 'File too big. Maximum size is 200 KB';
            }

            if (!$this->messages) {
                $this->em->flush();
            }

        } else {
            $this->messages[] = 'X-CSRF protection triggered';
        }

        if ($this->messages) {
            $this->index();
        } else {
            $this->session->set('message', 'Profile information edited successfully!');
            header('Location: /profile');
        }

    }

}
