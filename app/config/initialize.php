<?php

/**
 * A RESTful API template in PHP based on flight micro-framework.
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php ( MIT License )
 *
 * @since       Version 1.0.0
 */
if (!defined('APP_NAME')) {
    die('Forbidden 403');
}

session_start();

// Initialize Server Configuration
if (!file_exists(CONFIG_DIR.'/config_app.ini')) {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'Server configuration file [ config_app.ini ] is missing',
        ),
        'result_code' => 500,
    ), 500);
    exit;
}

Flight::set('app_config', parse_ini_file(CONFIG_DIR.'/config_app.ini'));

// Register the flight server with app configuration
$configArray = Flight::get('app_config');

if (empty($configArray['ENV'])) {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'Server environment is not set in config',
        ),
        'result_code' => 503,
    ), 503);
    exit;
}

// Set server timezone acording to Configuration
if (!empty($configArray['SERVER_TIMEZONE'])
        && !empty($configArray['DB_SET_TIMEZONE'])
        && 1 == $configArray['DB_SET_TIMEZONE']) {
    if (!date_default_timezone_set($configArray['SERVER_TIMEZONE'])) {
        // Set error condition if server timezone is set wrongly
    }
}

Flight::set('env', $configArray['ENV']);

/*
 * Set Server Host URL
 */
define('SERVER_HOST', !empty($configArray['BASE_URL']) ? $configArray['BASE_URL'] : (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST']) . '/';

/**
 * Following definition is to bypass the memcache-compression flag error
 */
defined('MEMCACHE_COMPRESSED') or define('MEMCACHE_COMPRESSED', 1);

//Configure Database Connection
require_once CONFIG_DIR.'/db.php';

//load basic routes
require_once APP_DIR.'/route/route.php';
