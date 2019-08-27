<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

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
