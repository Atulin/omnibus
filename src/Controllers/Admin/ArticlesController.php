<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 26.08.2019, 04:14
 */

namespace Omnibus\Controllers\Admin;

use Omnibus\Core\Controller;


class ArticlesController extends Controller
{

    public function index(): void
    {
        $this->setBaseData();
        $this->render('/admin/articles', []);
    }


    public function editor(): void
    {
        $this->setBaseData();
        $this->render('/admin/editor');
    }


    public function create(): void
    {

    }


    public function update(): void
    {

    }


    public function delete(): void
    {

    }


    public function fetch(): void
    {

    }

}
