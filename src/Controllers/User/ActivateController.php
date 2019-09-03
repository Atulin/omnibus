<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User;

use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Models\ActivationCode;
use Doctrine\ORM\OptimisticLockException;


class ActivateController extends Controller
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * Render the page
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('user/activate', ['messages' => $this->errors]);
    }

    /**
     * @param $params
     */
    public function activate($params): void
    {
        $code = ($_POST['code'] ?? $params['code']) ?? null;
        if (!empty($code)) {

            $ac = $this->em->getRepository(ActivationCode::class)->findOneBy(['code' => $code]);

            if ($ac !== null) {

                try {
                    $this->em->remove($ac);
                } catch (ORMException $e) {
                    $this->errors[] = 'Could not activate account. Contact the administrator.';
                }

                try {
                    $this->em->flush();
                } catch (OptimisticLockException | ORMException $e) {
                    $this->errors[] = 'Could not activate account. Contact the administrator.';
                }

                $this->session->set('message', 'Your account has been activated successfully!');
                $this->session->set('token', $this->getToken());
                header('Location: /login');
                die();
            }

            $this->errors[] = 'Incorrect code.';

        } else {
            $this->errors[] = 'Code cannot be empty.';
        }

        $this->index();
    }
}
