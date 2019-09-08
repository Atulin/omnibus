<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 20:15
 */

namespace Omnibus\Controllers\Admin;

use Omnibus\Models\User;
use Omnibus\Models\Article;
use Omnibus\Core\Controller;


class DashboardController extends Controller
{

    public function index(): void
    {
        $this->auth('isStaff');

        $this->setBaseData();
        $this->render('admin/dashboard', [
            'counters' => [
                'users'    => $this->em->getRepository(User::class)   ->count([]),
                'articles' => $this->em->getRepository(Article::class)->count([])
            ]
        ]);
    }

}
