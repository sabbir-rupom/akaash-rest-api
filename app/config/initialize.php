<?php

if (!defined('APP_NAME'))
    die('Forbidden');

session_start();

/*
 * Initialize Server Configuration 
 */
if (!file_exists(CONFIG_DIR . "/config_app.ini")) {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'Server configuration file is missing',
        ),
        'result_code' => ResultCode::NOT_FOUND
    ));
}

Flight::set('app_config', parse_ini_file(CONFIG_DIR . "/config_app.ini"));


/*
 * Register database connection with the flight server
 */
$configArray = Flight::get('app_config');

Flight::set('env', $configArray['ENV']);

//Configure Database Connection
require_once CONFIG_DIR . '/db.php';

//load basic routes
require_once APP_DIR . '/route/route.php';
