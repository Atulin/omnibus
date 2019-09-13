<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers\User;

use Omnibus\Core\Controller;


class LogoutController extends Controller
{
    public function index(): void
    {
        if (isset($_COOKIE['__Secure-rememberme'])) {
            unset($_COOKIE['__Secure-rememberme']);
            setcookie('__Secure-rememberme', '', time() - 3600, '/', '', true, true);
        }

        // Invalidate session
        $this->session->remove('userid');
        $this->session->invalidate();

        // Redirect
        $this->session->set('message', 'See you soon!');
        $this->session->save();
        header('Location: /');
        die();
    }
}
