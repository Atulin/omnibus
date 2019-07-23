<?php
namespace Controllers\User;

use Core\Controller;

class LogoutController extends Controller
{
    public function index(): void
    {
        if (isset($_COOKIE['__Secure-rememberme'])) {
            unset($_COOKIE['__Secure-rememberme']);
            setcookie('__Secure-rememberme', '', time() - 3600, '/', '', true, true);
        }

        // Invalidate session
        $this->session->invalidate();

        // Redirect
        $this->session->set('message', 'See you soon!');
        header('Location: /');
        die();
    }
}
