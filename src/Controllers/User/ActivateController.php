<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User;

use Omnibus\Core\Controller;
use Doctrine\ORM\ORMException;
use Omnibus\Models\ActivationCode;


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

            if ($ac) {
                try {
                    $this->em->remove($ac);
                    $this->em->flush($ac);
                } catch (ORMException $e) {
                    $this->errors[] = 'Could not activate user.';
                }
            } else {
                $this->errors[] = 'Incorrect code.';
            }

            if (!$this->errors) {
                $this->session->set('message', 'Your account has been activated successfully!');
                $this->session->set('token', $this->getToken());
                header('Location: /login');
                die();
            }

        } else {
            $this->errors[] = 'Code cannot be empty.';
        }

        $this->index();
    }
}
