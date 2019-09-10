<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Controllers;

use Omnibus\Models\User;
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

        $reps = 1;

        $d = microtime(true);
        for ($i = 1; $i <= $reps; $i++) {
            $ud = $this->em->find(User::class, 1);
            echo $ud->getName();
        }
        $d = microtime(true) - $d;
        $dt = sprintf("%.9f", $d);

        $r = microtime(true);
        for ($i = 1; $i <= $reps; $i++) {
            $ur = (new UserRepository())->find(1);
            echo $ur->getName();
        }
        $r = microtime(true) - $r;
        $rt = sprintf("%.9f", $r);

        echo "
        <div style='position:fixed;top:200px;left:0;color:black;background:red;font-family:Consolas monospace;padding:1rem;font-size:1.1rem'>
        <div>$rt</div><div>$dt</div>
        </div>
        ";

    }
}
