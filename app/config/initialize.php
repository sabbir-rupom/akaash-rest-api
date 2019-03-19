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

(defined('APP_NAME')) OR exit('Forbidden 403');

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
        'result_code' => 404
            ), 404);
    exit;
}

Flight::set('app_config', parse_ini_file(CONFIG_DIR . "/config_app.ini"));


/*
 * Register the flight server with app configuration
 */
$configArray = Flight::get('app_config');

if (empty($configArray['ENV'])) {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'Server environment is not set in config',
        ),
        'result_code' => 404
            ), 404);
    exit;
}

Flight::set('env', $configArray['ENV']); // Set system environment 

/*
 * Set server timezone acording to Configuration
 */
if (!empty($configArray['DB_TIMEZONE']) && !empty($configArray['DB_SET_TIMEZONE']) && $configArray['DB_SET_TIMEZONE'] == 1) {
    if (!date_default_timezone_set($configArray['DB_TIMEZONE'])) {
        /*
         * Set error condition if server timezone is set wrongly
         */
    }
}


if (file_exists(CONFIG_DIR . '/constants.php')) {
    //load server constants
    require_once(CONFIG_DIR . '/constants.php');
} else {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'Server constants file not found',
        ),
        'result_code' => 404
            ), 404);
    exit;
}

if (file_exists(CONFIG_DIR . '/db.php')) {
    //load database connection
    require_once(CONFIG_DIR . '/db.php');
} else {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'DB configuration file not found',
        ),
        'result_code' => 404
            ), 404);
    exit;
}

if (file_exists(CONFIG_DIR . '/route.php')) {
    //load basic routes
    require_once(CONFIG_DIR . '/route.php');
} else {
    Flight::json(array(
        'error' => array(
            'title' => 'Server Configuration Error',
            'message' => 'Routing file not found',
        ),
        'result_code' => 404
            ), 404);
    exit;
}

/*
 * Initialize required class directories for autoload register
 */
Flight::path(array(APP_DIR, API_DIR, SYSTEM_DIR));

spl_autoload_register('directoryClassLoader');
/*
 * directoryClassLoader finds a class and register with the system 
 * if the class name has string like '\\' [Double Slash] or '_' [underscore]
 * the function with treat the class inside a directory 
 * which will be found at 0'th index value after explode() 
 */
function directoryClassLoader($class) {
    if (strpos($class, '_')) {
        $class_file = explode('/', str_replace('_', '/', $class) . '.php');
        for ($i = 0; $i < count($class_file) - 1; $i++) {
            $class_file[$i] = strtolower($class_file[$i]);
        }
        $class_file = implode('/', $class_file);
        foreach (array(APP_DIR, API_DIR) as $dir) {
            $file = $dir . '/' . $class_file;
            if (file_exists($file)) {
                require $file;
                return;
            }
        }
    }
}
