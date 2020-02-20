<?php

if (!defined('APP_NAME')) {
    die('Forbidden');
}

// Register some basic information with flight
Flight::set('start_time', microtime(true));
Flight::set('headers', getallheaders());

session_start();

/*
 * Initialize Server Configuration
 */
if (!file_exists(CONFIG_DIR . "/app_config.ini")) {
    Flight::json(array(
      'error' => array(
        'title' => 'Server Configuration Error',
        'message' => 'Server configuration file is missing',
      ),
      'result_code' => 500
        ), 500);
    exit;
}

/*
 * Register the flight server with app configuration
 */
$configArray = parse_ini_file(CONFIG_DIR . "/app_config.ini");
Flight::set('app_config', $configArray);

if (empty($configArray['ENV'])) {
    Flight::json(array(
      'error' => array(
        'title' => 'Server Configuration Error',
        'message' => 'Server environment is not set in config',
      ),
      'result_code' => 500
        ), 500);
    exit;
}

/**
 * Set server timezone according to Configuration
 */
if (!empty($configArray['SERVER_TIMEZONE']) && !empty($configArray['DB_SET_TIMEZONE']) && intval($configArray['DB_SET_TIMEZONE']) > 0) {
    if (!date_default_timezone_set($configArray['SERVER_TIMEZONE'])) {
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
      'result_code' => 500
        ), 500);
    exit;
}

if (file_exists(CONFIG_DIR . '/database.php')) {
    //load database connection
    require_once(CONFIG_DIR . '/database.php');
} else {
    Flight::json(array(
      'error' => array(
        'title' => 'Server Configuration Error',
        'message' => 'DB configuration file not found',
      ),
      'result_code' => 500
        ), 500);
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
      'result_code' => 500
        ), 500);
    exit;
}

/*
 * Initialize required class directories for autoload register
 */
Flight::path(array(APP_DIR, API_DIR, SYSTEM_DIR));

spl_autoload_register('directoryClassLoader');

/**
 * directoryClassLoader finds a class and register with the system
 * if the class name has string like '\\' [Double Slash] or '_' [underscore]
 * the function with treat the class inside a directory
 * which will be found at 0'th index value after explode()
 */
function directoryClassLoader($class)
{
    if (strpos($class, '_') || strpos($class, '-')) {
        $class_file = explode('/', str_replace(['_', '-'], ['/', '/'], $class) . '.php');
        for ($i = 0; $i < count($class_file) - 1; $i++) {
            $class_file[$i] = strtolower($class_file[$i]);
        }
        $filePath = implode('/', $class_file);
        foreach (array(APP_DIR, API_DIR) as $dir) {
            $file = $dir . '/' . $filePath;
            if (file_exists($file)) {
                require_once $file;
                break;
            }
        }
    }
}

if (file_exists(CONFIG_DIR . "/hooks.php")) {
    /**
     * Initialize Server Hooks
     */
    include_once CONFIG_DIR . "/hooks.php";
}
