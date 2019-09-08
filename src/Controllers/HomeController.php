<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers;

use Omnibus\Models\Article;
use Omnibus\Core\Controller;
use Omnibus\Models\Repositories\UserRepository;


class HomeController extends Controller
{
    /**
     */
    public function index(): void
    {
        $this->setBaseData();
        $this->render('home', [
            'articles' => $this->em->getRepository(Article::class)->findAll()
        ]);

        $ur = new UserRepository();
        echo '<pre>'.var_export($ur->checkNameOrEmailTaken('Angius', 'aaa'), true).'</pre>'; // false
        echo '<pre>'.var_export($ur->checkNameOrEmailTaken('Trem', 'bbb'), true).'</pre>'; // false
        echo '<pre>'.var_export($ur->checkNameOrEmailTaken('aaa', 'koumarin@gmail.com'), true).'</pre>'; // false
        echo '<pre>'.var_export($ur->checkNameOrEmailTaken('bbb', 'a@b.c'), true).'</pre>'; // false
        echo '<pre>'.var_export($ur->checkNameOrEmailTaken('AAA', 'aaa'), true).'</pre>'; // true
        echo '<pre>'.var_export($ur->checkNameOrEmailTaken('BBB', 'bbb'), true).'</pre>'; // true
    }
}
