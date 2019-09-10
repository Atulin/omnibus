<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 26.08.2019, 05:02
 */

namespace Omnibus\Controllers;

use DateTime;
use Exception;
use Omnibus\Models\Tag;
use Omnibus\Models\User;
use Omnibus\Models\Article;
use Omnibus\Models\Category;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\Utils;
use Omnibus\Models\CommentThread;
use Omnibus\Core\Utility\FileHandler;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;
use Omnibus\Models\Repositories\TagRepository;
use Omnibus\Models\Repositories\ArticleRepository;
use Omnibus\Models\Repositories\CategoryRepository;


/**
 * Class EditorController
 * @package Omnibus\Controllers
 */
class EditorController extends Controller
{
    /**
     * @var array $errors
     */
    private $errors;

    /** @var Article $article */
    private $article;

    /**
     * @param array $params
     */
    public function index(array $params = null): void
    {
        $role = $this->getRole();

        if (isset($params['id']) && (!$role || !$role->canManageArticles())) {
            header('Location: /editor');
        }

        if (!$role || !$role->canAddArticles()) {
            header('Location: /');
        }

        $this->article = isset($params['id']) ? (new ArticleRepository())->find($params['id']) : $this->article;

        $this->setBaseData();
        $this->render('/editor', [
            'tags' => $this->em->getRepository(Tag::class)->findAll(),
            'categories' => $this->em->getRepository(Category::class)->findAll(),
            'users' => $this->em->getRepository(User::class)->findAll(),
            'article' => $this->article,
            'errors' => $this->errors
        ]);
    }

    /**
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

            /** @var ArticleRepository $ar */
            $ar = new ArticleRepository();
            /** @var CategoryRepository $cr */
            $cr = new CategoryRepository();
            /** @var TagRepository $tr */
            $tr = new TagRepository();

            if (isset($_POST['id'])) {
                if ($role && $role->canManageArticles()) {
                    $art = $ar->find($_POST['id']);
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

                    try {
                        $art->setTitle($POST['title'])
                            ->setBody($POST['body'])
                            ->setExcerpt($POST['excerpt'])
                            ->setCategory($this->em->find(Category::class, $_POST['category']))
                            ->setAuthor(isset($_POST['author']) ? $this->em->find(User::class, $_POST['author']) : $this->getUser())
                            ->setComments(new CommentThread());
                    } catch (OptimisticLockException | TransactionRequiredException | ORMException $e) {
                        $this->errors[] = 'Could not get the selected category or author.';
                    }
                    if (isset($_POST['date'])) {
                        try {
                            $art->setDate(new DateTime($_POST['date']));
                        } catch (Exception $e) {
                            $this->errors[] = 'Could not parse the date format.';
                        }
                    }

                    foreach ($_POST['tags'] as $tag) {
                        try {
                            $art->addTag($this->em->find(Tag::class, $tag));
                        } catch (OptimisticLockException | TransactionRequiredException | ORMException $e) {
                            $this->errors[] = 'Could not add one of the tags.';
                        }
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
                        $ar->save($art);
                    }

                } else {
                    $this->errors[] = 'Name cannot be empty.';
                }

            }

        } else {
            $this->errors[] = 'Something went wrong. Refresh the page.';
        }

        $this->article = $art;
        $this->index();

    }

}
