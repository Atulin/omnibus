<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 20:15
 */

namespace Controllers\Admin;

use Models\User;
use Models\Article;
use Core\Controller;

class DashboardController extends Controller
{

    public function index(): void
    {
        $this->setBaseData();
        $this->render('admin/dashboard', [
            'counters' => [
                'users'    => $this->em->getRepository(User::class)   ->count([]),
                'articles' => $this->em->getRepository(Article::class)->count([])
            ]
        ]);
    }

}
