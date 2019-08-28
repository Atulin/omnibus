<?php
// cli-config.php
use Omnibus\Models\Database;
use Symfony\Component\Dotenv\Dotenv;
use Doctrine\ORM\Tools\Console\ConsoleRunner;


// Load .env file
if (!isset($_ENV['APP_ENV'])) {
    (new Dotenv())->load(__DIR__.'/.env');
}

$entityManager = (new Database())->Get();

return ConsoleRunner::createHelperSet($entityManager);
