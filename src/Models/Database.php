<?php
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
     * @var string
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
        $config = Setup::createAnnotationMetadataConfiguration([__DIR__], $isDevMode, null, null, false);

        $conn = [
            'driver'   => 'pdo_pgsql',
            'host'     => $host,
            'dbname'   => $db,
            'usser'    => $username,
            'password' => $password
        ];

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
