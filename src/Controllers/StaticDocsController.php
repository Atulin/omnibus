<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 07:41
 */

namespace Controllers;

use Core\Controller;


/**
 * Class StaticDocsController
 * @package Controllers
 */
class StaticDocsController extends Controller
{

    /**
     *
     */
    public function tos(): void
    {
        $this->index('Terms-of-Service');
    }

    public function md(): void
    {
        $this->index('Markdown-Reference');
    }

    /**
     * @param string $doc
     */
    private function index(string $doc): void
    {
        $text = file_get_contents(dirname(__DIR__, 2) . '/public/assets/static/docs/' . $doc . '.md');

        $this->setBaseData();
        $this->render('static-doc', [
            'text' => $text,
            'name' => str_replace('-', ' ', $doc)
        ]);
    }

}
