<?php
// cli-config.php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Models\Database;
use Symfony\Component\Dotenv\Dotenv;

// Load .env file
(new Dotenv())->load(__DIR__.'/.env');

$entityManager = (new Database())->Get();

return ConsoleRunner::createHelperSet($entityManager);
