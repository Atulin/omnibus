<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User;

use Omnibus\Core\Controller;
use Omnibus\Models\ActivationCode;
use Omnibus\Models\Repositories\ActivationCodeRepository;


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

            /** @var ActivationCode $ac */
            $ac = $this->em->getRepository(ActivationCode::class)->findOneBy(['code' => $code]);

            if ($ac) {
                $acr = new ActivationCodeRepository();
                $this->errors = array_merge($this->errors, $acr->remove($ac));
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
