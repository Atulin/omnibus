<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 03:54
 */

namespace Omnibus\Controllers\Admin;

use Omnibus\Models\Category;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\Utils;
use Omnibus\Core\Utility\HttpStatus;
use Omnibus\Core\Utility\APIMessage;
use Omnibus\Core\Utility\FileHandler;
use Doctrine\ORM\OptimisticLockException;
use GuzzleHttp\Exception\GuzzleException;
use BackblazeB2\Exceptions\NotFoundException;
use Doctrine\ORM\TransactionRequiredException;


class CategoriesController extends Controller
{

    public function index(): void
    {
        $this->setBaseData();
        $this->render('/admin/categories');
    }


    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function create(): void
    {
        $errors = [];
        $msg = null;
        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'name' => FILTER_SANITIZE_STRING,
            'description' => FILTER_SANITIZE_STRING
        ]);
        if ($POST['token'] === $this->session->get('token')) {

            if ($this->getUser()->getRole()->isAdmin()) {

                if (trim($POST['name'])) {

                    /** @var Category $cat */
                    $cat = new Category();
                    $cat->setName($POST['name'])
                        ->setDescription($POST['description']);

                    if (isset($_FILES['image'])) {

                        if ($_FILES['image']['size'] < CONFIG['file sizes']['category cover']) {
                            $fh = new FileHandler();
                            // Upload image
                            $name = $fh->upload($_FILES['image'], 'covers/category/' . Utils::friendlify($POST['name']));

                            $cat->setImage($name);

                        } else {
                            $errors[]  = 'File too big. Maximum size is ' . CONFIG['file sizes']['avatar'] / 1024 . ' KB';
                        }

                    }

                    if (!$errors) {
                        $this->em->persist($cat);
                        $this->em->flush($cat);
                    }

                } else {
                    $errors[] = 'Name cannot be empty.';
                }

            } else {
                $errors[] = 'Insufficient rights.';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered.';
        }
        http_response_code($errors ? 500 : 201);
        echo json_encode(new APIMessage(
            $errors ? HttpStatus::S500() : HttpStatus::S201(),
            $errors ? 'An error has occurred.' : 'Successfully added the category.',
            $errors
        ));
    }


    /**
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function update(): void
    {
        $errors = [];
        $msg = null;
        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'name' => FILTER_SANITIZE_STRING,
            'description' => FILTER_SANITIZE_STRING
        ]);
        if ($POST['token'] === $this->session->get('token')) {

            if ($this->getUser()->getRole()->isAdmin()) {

                if (trim($POST['name'])) {

                    /** @var Category $cat */
                    $cat = $this->em->find(Category::class, $_POST['id']);
                    $cat->setName($POST['name'])->setDescription($POST['description']);
                    if (isset($_FILES['image'])) {

                        if ($_FILES['image']['size'] < CONFIG['file sizes']['category cover']) {

                            $fh = new FileHandler();
                            // Delete old image
                            $fh->delete($cat->getImage());
                            // Upload new image
                            $name = $fh->upload($_FILES['image'], 'covers/category/' . Utils::friendlify($POST['name']));
                            $cat->setImage($name);

                        } else {
                            $errors[] = 'File too big. Maximum size is ' . CONFIG['file sizes']['avatar'] / 1024 . ' KB';
                        }
                    }
                    if (!$errors) {
                        $this->em->flush($cat);
                    }

                } else {
                    $errors[] = 'Name cannot be empty.';
                }

            } else {
                $errors[] = 'Insufficient rights.';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered.';
        }
        http_response_code($errors ? 500 : 200);
        echo json_encode(new APIMessage(
            $errors ? HttpStatus::S500() : HttpStatus::S200(),
            $errors ? 'An error has occurred.' : 'Successfully updated the category.',
            $errors
        ));
    }


    /**
     * @throws GuzzleException
     * @throws NotFoundException
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     */
    public function delete(): void
    {
        $errors = [];
        $msg = null;
        $POST = filter_input_array(INPUT_POST, [
            'token' => FILTER_SANITIZE_STRING,
            'id' => FILTER_VALIDATE_INT
        ]);
        if ($POST['token'] === $this->session->get('token')) {

            if ($this->getUser()->getRole()->isAdmin()) {

                /** @var Category $cat */
                $cat = $this->em->find(Category::class, $POST['id']);

                if ($cat->getImage()) {
                    // Delete old image
                    $fh = new FileHandler();
                    $fh->delete($cat->getImage());
                }

                $this->em->remove($cat);
                $this->em->flush($cat);

            } else {
                $errors[] = 'Insufficient rights.';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered.';
        }
        http_response_code($errors ? 500 : 200);
        echo json_encode(new APIMessage(
            $errors ? HttpStatus::S500() : HttpStatus::S200(),
            $errors ? 'An error has occurred.' : 'Successfully deleted the category.',
            $errors
        ));
    }

    /**
     *
     */
    public function fetch(): void
    {
        $categories = $this->em->getRepository(Category::class)->findAll();
        array_walk($categories, static function (Category $x) {
            $x->setImage('//' . CONFIG['cdn domain'] . '/file/Omnibus/' . $x->getImage());
        });
        http_response_code(200);
        echo json_encode(new APIMessage(
            HttpStatus::S200(),
            'Success.',
            [],
            $categories
        ));
    }

}
