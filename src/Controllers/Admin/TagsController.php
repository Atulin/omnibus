<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 13:28
 */

namespace Omnibus\Controllers\Admin;

use Omnibus\Models\Tag;
use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Core\Utility\APIMessage;
use Omnibus\Core\Utility\HttpStatus;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\TransactionRequiredException;


class TagsController extends Controller
{

    public function index(): void
    {
        $this->setBaseData();
        $this->render('/admin/tags');
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

                    /** @var Tag $tag */
                    $tag = new Tag();
                    $tag->setName($POST['name'])
                        ->setDescription($POST['description']);

                        $this->em->persist($tag);
                        $this->em->flush($tag);

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
            $errors ? 'An error has occurred.' : 'Successfully added the tag.',
            $errors
        ));
    }


    /**
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

                    /** @var Tag $tag */
                    $tag = $this->em->find(Tag::class, $_POST['id']);
                    $tag->setName($POST['name'])
                        ->setDescription($POST['description']);
                        $this->em->flush($tag);

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
            $errors ? 'An error has occurred.' : 'Successfully updated the tag.',
            $errors
        ));
    }


    /**
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

                /** @var Tag $tag */
                $tag = $this->em->find(Tag::class, $POST['id']);

                $this->em->remove($tag);
                $this->em->flush($tag);

            } else {
                $errors[] = 'Insufficient rights.';
            }

        } else {
            $errors[] = 'X-CSRF protection triggered.';
        }
        http_response_code($errors ? 500 : 200);
        echo json_encode(new APIMessage(
            $errors ? HttpStatus::S500() : HttpStatus::S200(),
            $errors ? 'An error has occurred.' : 'Successfully deleted the tag.',
            $errors
        ));
    }

    /**
     *
     */
    public function fetch(): void
    {
        $tags = $this->em->getRepository(Tag::class)->findAll();

        http_response_code(200);
        echo json_encode(new APIMessage(
            HttpStatus::S200(),
            'Success.',
            [],
            $tags
        ));
    }

}
