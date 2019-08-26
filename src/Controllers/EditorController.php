<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 26.08.2019, 05:02
 */

namespace Controllers;

use DateTime;
use Exception;
use Models\Tag;
use Models\User;
use Models\Article;
use Core\Controller;
use Models\Category;
use Core\Utility\Utils;
use Models\CommentThread;
use Core\Utility\APIMessage;
use Core\Utility\HttpStatus;
use Core\Utility\FileHandler;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\OptimisticLockException;


/**
 * Class EditorController
 * @package Controllers
 */
class EditorController extends Controller
{
    /**
     * @var
     */
    private $errors;

    /**
     *
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('/editor', [
            'tags' => $this->em->getRepository(Tag::class)->findAll(),
            'categories' => $this->em->getRepository(Category::class)->findAll(),
            'id' => null,
        ]);
    }

    /**
     *
     */
    public function admin(): void
    {
        $this->setBaseData();
        $this->render('/editor', [
            'tags' => $this->em->getRepository(Tag::class)->findAll(),
            'categories' => $this->em->getRepository(Category::class)->findAll(),
            'users' => $this->em->getRepository(User::class)->findAll(),
            'id' => null,
            'admin' => true
        ]);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws Exception
     */
    public function create(): void
    {
        $msg = null;
        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'title' => FILTER_SANITIZE_STRING,
            'body' => FILTER_SANITIZE_STRING,
            'excerpt' => FILTER_SANITIZE_STRING,
        ]);
        if ($POST['token'] === $this->session->get('token')) {

            if ($this->getUser()->getRole()->canAddArticles()) {

                if (trim($POST['title'])) {

                    /** @var Article $art */
                    $art = new Article();
                    $art->setTitle($POST['title'])
                        ->setBody($POST['body'])
                        ->setExcerpt($POST['excerpt'])
                        ->setCategory($this->em->find(Category::class, $_POST['category']))
                        ->setAuthor($this->em->find(User::class, $_POST['author']) ?: $this->getUser())
                        ->setComments(new CommentThread());

                    foreach ($_POST['tags'] as $tag) {
                        $art->addTag($this->em->find(Tag::class, $tag));
                    }

                    if (isset($_FILES['image'])) {

                        if ($_FILES['image']['size'] < CONFIG['file sizes']['article cover']) {
                            $fh = new FileHandler();
                            // Upload image
                            $name = $fh->upload($_FILES['image'], 'covers/article/' . Utils::friendlify($POST['title']));

                            $art->setImage($name);

                        } else {
                            $this->errors[]  = 'File too big. Maximum size is ' . CONFIG['file sizes']['article cover'] / 1024 . ' KB';
                        }

                    }

                    if (!$this->errors) {
                        $this->em->persist($art);
                        $this->em->flush($art);
                    }

                } else {
                    $this->errors[] = 'Name cannot be empty.';
                }

            } else {
                $this->errors[] = 'Insufficient rights.';
            }

        } else {
            $this->errors[] = 'X-CSRF protection triggered.';
        }
        http_response_code($this->errors ? 500 : 201);
        echo json_encode(new APIMessage(
            $this->errors ? HttpStatus::S500() : HttpStatus::S201(),
            $this->errors ? 'An error has occurred.' : 'Successfully added the category.',
            $this->errors ?: []
        ));
    }

    /**
     *
     */
    public function update(): void
    {

    }

}
