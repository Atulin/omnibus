<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 07.09.2019, 22:14
 */

namespace Omnibus\Core;

use Omnibus\Models\Database;
use Doctrine\ORM\EntityManager;


class Model
{
    /** @var EntityManager $em */
    protected $em;

    public function __construct()
    {
        $this->em = (new Database())->Get();
    }

}
