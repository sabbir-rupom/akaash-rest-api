<?php

/**
 * A RESTful API template in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php ( MIT License )
 * @since       Version 1.0.0
 */
header_remove('X-Powered-By'); 

define('APP_NAME', 'RESTful API Template (Flight PHP)');

define('ROOT_DIR', realpath(__DIR__));
define('APP_DIR', ROOT_DIR . '/app');
define('API_DIR', APP_DIR . '/api');
define('CONFIG_DIR', APP_DIR . '/config');

/*
 * Load Flight microframework
 */
require 'flight/Flight.php';

/*
 * Load other dependency libraries
 */
require 'vendor/autoload.php';

/*
 *  load server environment
 */
require_once CONFIG_DIR . '/initialize.php';


/*
 *  Run Rest-API Application Template 
 */

Flight::start();
/* ----- End of script execution ----- */

