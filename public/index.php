<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:29
 */

require '../vendor/autoload.php';
require '../config.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

use Core\Application;

define('IS_DEV', true);
define('ASSETS', '/assets');//'/assets');

$application = new Application();
$application->run();
