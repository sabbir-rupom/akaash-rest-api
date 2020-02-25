<?php

/**
 * Akaash - RESTful API Template
 * Developed in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @category    Rest API Template
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php ( MIT License )
 * @version     2.0.0
 */

define('APP_NAME', 'Akaash - RESTful API Template');

define('ROOT_DIR', realpath(__DIR__));
define('APP_DIR', ROOT_DIR . '/../app');
define('API_DIR', APP_DIR . '/api');
define('CONFIG_DIR', APP_DIR . '/config');
define('SYSTEM_DIR', APP_DIR . '/akaash/system');

/*
 * Server Host URL
 */
define('SERVER_HOST', (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST']);

/*
 * Load Flight microframework and other dependency libraries
 */
require_once ROOT_DIR . '/../vendor/autoload.php';

/*
 *  load server environment
 */
require CONFIG_DIR . '/initialize.php';

/*
 *  Run Rest-API Application Template
 */
Flight::start();
