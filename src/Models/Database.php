<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 07:38
 */

namespace Models;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

/**
 * Class Database
 * @package Models
 */
class Database
{

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * Database constructor.
     * @throws ORMException
     */
    public function __construct()
    {
        $host     = $_ENV['HOST'];
        $db       = $_ENV['DB'];
        $username = $_ENV['USER'];
        $password = $_ENV['PASS'];

        $isDevMode = true;
        $config = Setup::createAnnotationMetadataConfiguration(
            [__DIR__],
            $isDevMode,
            null,
            null,
            false
        );

        if (isset($_ENV['DATABASE_URL'])) {
            $conn = [
                'url' => $_ENV['DATABASE_URL']
            ];
        } else {
            $conn = [
                'driver' => 'pdo_pgsql',
                'host' => $host,
                'dbname' => $db,
                'user' => $username,
                'password' => $password
            ];
        }

        $this->entityManager = EntityManager::create($conn, $config);
    }

    /**
     * @return EntityManager|string
     */
    public function Get()
    {
        return $this->entityManager;
    }


}
