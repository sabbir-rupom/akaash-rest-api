<?php
/*
 * RESTful API Template
 * 
 * A RESTful API template based on flight-PHP framework
 * This software project is based on my recent REST-API development experiences. 
 * 
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT 
 * 
 * @author	Sabbir Hossain Rupom
 * @since	Version 1.0.0
 * @filesource
 */

header_remove('X-Powered-By'); 

define('APP_NAME', 'RESTful API Template (Flight PHP)');

define('ROOT_DIR'   , realpath(__DIR__));
define('APP_DIR'    , ROOT_DIR . '/../app');
define('API_DIR'    , APP_DIR . '/api');
define('CONFIG_DIR' , APP_DIR . '/config');
define('SYSTEM_DIR' , APP_DIR . '/system');

/*
 * Server Host URL
 */
define('SERVER_HOST', (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST']);

/*
 * Load Flight microframework and other dependency libraries
 */
require ROOT_DIR . '/../vendor/autoload.php';

/*
 *  load server environment
 */
require CONFIG_DIR . '/initialize.php';

/*
 *  Run Rest-API Application Template 
 */
Flight::start();
