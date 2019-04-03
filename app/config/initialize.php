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

(defined('APP_NAME')) or exit('Forbidden 403');

session_start();

// Initialize Server Configuration
if (!file_exists(CONFIG_DIR.'/config_app.ini')) {
    Flight::json([
        'error' => [
            'title' => 'Server Configuration Error',
            'message' => 'Server configuration file [ config_app.ini ] is missing',
        ],
        'result_code' => 500,
    ], 500);
    exit;
}

Flight::set('app_config', parse_ini_file(CONFIG_DIR.'/config_app.ini'));

// Register the flight server with app configuration
$configArray = Flight::get('app_config');

if (empty($configArray['ENV'])) {
    Flight::json([
        'error' => [
            'title' => 'Server Configuration Error',
            'message' => 'Server environment is not set in config',
        ],
        'result_code' => 503,
    ], 503);
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

//Configure Database Connection
require_once CONFIG_DIR.'/db.php';

//load basic routes
require_once APP_DIR.'/route/route.php';
