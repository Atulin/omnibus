<?php
namespace Controllers\User\Profile;

use Core\Controller;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

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

            if (!$this->messages) {
                $this->em->flush();
            }

        } else {
            $this->messages[] = 'X-CSRD protection triggered';
        }

        if ($this->messages) {
            $this->index();
        } else {
            $this->session->set('message', 'Profile information edited successfully!');
            header('Location: /profile');
        }

    }

}
