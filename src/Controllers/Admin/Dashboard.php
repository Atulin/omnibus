<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 20.08.2019, 20:15
 */

namespace Controllers\Admin;

use Core\Controller;

class Dashboard extends Controller
{

    public function index(): void
    {
        echo 'Here be admins';
    }

}
