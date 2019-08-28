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
use Doctrine\ORM\TransactionRequiredException;


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

    private $article;

    /**
     * @param array $params
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function index(array $params = null): void
    {
        $role = $this->getRole();
        if (isset($params['id']) && (!$role || !$role->canEditArticles())) {
            header('Location: /editor');
        }
        if (!$role || !$role->canAddArticles()) {
            header('Location: /');
        }

        $this->article = isset($params['id']) ? $this->em->find(Article::class, $params['id']) : $this->article;

        $this->setBaseData();
        $this->render('/editor', [
            'tags' => $this->em->getRepository(Tag::class)->findAll(),
            'categories' => $this->em->getRepository(Category::class)->findAll(),
            'users' => $this->em->getRepository(User::class)->findAll(),
            'article' => $this->article,
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
        $art = null;
        $role = $this->getRole();

        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'title' => FILTER_SANITIZE_STRING,
            'body' => FILTER_SANITIZE_STRING,
            'excerpt' => FILTER_SANITIZE_STRING,
        ]);

        if ($POST['token'] === $this->session->get('token')) {

            /** @var Article $art */
            $art = null;
            if (isset($_POST['id'])) {
                if ($role && $role->canEditArticles()) {
                    $art = $this->em->find(Article::class, $_POST['id']);
                } else {
                    $this->errors[] = 'Insufficient permissions';
                }
            } else if ($role && $role->canAddArticles()) {
                $art = new Article();
            } else {
                $this->errors[] = 'Insufficient permissions';
            }

            if (!$this->errors && $art) {

                if (trim($POST['title'])) {

                    $art->setTitle($POST['title'])
                        ->setBody($POST['body'])
                        ->setExcerpt($POST['excerpt'])
                        ->setCategory($this->em->find(Category::class, $_POST['category']))
                        ->setAuthor(isset($_POST['author']) ? $this->em->find(User::class, $_POST['author']) : $this->getUser())
                        ->setComments(new CommentThread());

                    if (isset($_POST['date'])) {
                        $art->setDate(new DateTime($_POST['date']));
                    }

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

            }

        } else {
            $this->errors[] = 'X-CSRF protection triggered.';
        }

        $this->article = $art;
        $this->index();

    }

    /**
     *
     */
    public function update(): void
    {

    }

}
