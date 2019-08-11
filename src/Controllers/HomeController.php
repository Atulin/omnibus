<?php

namespace Controllers;

use Core\Controller;

class HomeController extends Controller
{
    /**
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('home');
    }
}
