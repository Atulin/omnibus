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
use Omnibus\Models\Role;
use Omnibus\Models\Article;
use Omnibus\Models\Category;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\Utils;
use Omnibus\Models\CommentThread;
use Omnibus\Core\Utility\FileHandler;


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

        $this->article = isset($params['id']) ? $this->em->getRepository(Article::class)->find($params['id']) : $this->article;

        $this->setBaseData();
        $this->render('/editor', [
            'tags' => $this->em->getRepository(Tag::class)->findAll(),
            'categories' => $this->em->getRepository(Category::class)->findAll(),
            'users' => $this->em->getRepository(User::class)->findAll(),
            'article' => $this->article,
            'errors' => $this->errors
        ]);
    }


    public function create(): void
    {
        /** @var Article $art */
        $art = null;
        /** @var Role $role */
        $role = $this->getRole();

        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'title' => FILTER_SANITIZE_STRING,
            'body' => FILTER_SANITIZE_STRING,
            'excerpt' => FILTER_SANITIZE_STRING,
        ]);

        if ($POST['token'] === $this->session->get('token')) {

            if (isset($_POST['id'])) {
                if ($role->canManageArticles()) {
                    $art = $this->em->getRepository(Article::class)->find($_POST['id']);
                } else {
                    $this->errors[] = 'Insufficient permissions';
                }
            } else if ($role->canAddArticles()) {
                $art = new Article();
            } else {
                $this->errors[] = 'Insufficient permissions';
            }

            if (!$this->errors && $art) {

                if (trim($POST['title'])) {

                    // Try to create an article
                    try {
                        $art->setTitle($POST['title'])
                            ->setBody($POST['body'])
                            ->setExcerpt($POST['excerpt'])
                            ->setCategory($this->em->find(Category::class, $_POST['category']))
                            ->setAuthor(isset($_POST['author']) ? $this->em->find(User::class, $_POST['author']) : $this->getUser())
                            ->setComments(new CommentThread());
                    } catch (ORMException $e) {
                        $this->errors[] = 'Could not get the selected category or author.';
                    }

                    // Try to parse the given date
                    if (isset($_POST['date'])) {
                        try {
                            $art->setDate(new DateTime($_POST['date']));
                        } catch (Exception $e) {
                            $this->errors[] = 'Could not parse the date format.';
                        }
                    }

                    // Try to add tags
                    foreach ($_POST['tags'] as $tag) {
                        try {
                            $art->addTag($this->em->find(Tag::class, $tag));
                        } catch (ORMException $e) {
                            $this->errors[] = 'Could not add one of the tags.';
                        }
                    }

                    // Upload cover image if specified
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

                    // Try to save the article
                    if (!$this->errors) {
                        try {
                            $this->em->persist($art);
                            $this->em->flush($art);
                        } catch (ORMException $e) {
                            $this->errors[] = 'Could not create the article.';
                        }
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
