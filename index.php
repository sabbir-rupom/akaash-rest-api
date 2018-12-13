<?php

header_remove('X-Powered-By'); 

define('APP_NAME', 'RESTful API Template (Flight PHP)');

define('ROOT_DIR', realpath(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('API_DIR', APP_DIR . '/api');
define('CONFIG_DIR', APP_DIR . '/config');

/*
 * Server Host URL
 */
define('SERVER_HOST', (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . '/flight');

require 'flight/Flight.php';

//load server environment
require_once CONFIG_DIR . '/initialize.php';

Flight::start();
