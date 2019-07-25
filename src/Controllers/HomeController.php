<?php

namespace Controllers;

use Core\Controller;

class HomeController extends Controller
{
    /**
     */
    public function index(): void
    {
        $message = $this->session->get('message');

        $this->setBaseData();
        $this->render('home', ['message' => $message]);
    }
}
