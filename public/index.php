<?php
require '../vendor/autoload.php';
require '../config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Core\Application;

define('IS_DEV', true);
define('ASSETS', '/assets');

$application = new Application();
$application->run();
