<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:29
 */

declare(strict_types=1);

$start = microtime(true);

require '../vendor/autoload.php';
require '../config.php';

error_reporting(E_ALL);
ini_set('display_errors', '1');


use Omnibus\Core\Application;


define('IS_DEV', true);
define('ASSETS', '/assets');

$application = new Application();
$application->run();

$end = microtime(true);
$diff = $end - $start;

echo "<div style='position:fixed;bottom:0;right:0;z-index:99999;background:#ffd503;color:black;'>Request took <strong>$diff</strong> seconds</div>";
