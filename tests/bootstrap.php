<?php

define('ROOT_DIR', realpath(__DIR__ . '/../public'));
define('APP_DIR', ROOT_DIR . '/../app');
define('API_DIR', APP_DIR . '/api');
define('CONFIG_DIR', APP_DIR . '/config');
define('SYSTEM_DIR', APP_DIR . '/akaash/system');


/*
 * Load Flight microframework and other dependency libraries
 */
require ROOT_DIR . '/../vendor/autoload.php';

/*
 * Initialize required class directories with flight autoload register
 */
Flight::path(array(APP_DIR, API_DIR, SYSTEM_DIR));

// Set error page page response
Flight::map('notFound', function () {
    // do nothing
});

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

/*
 *  Run Flight Engine
 */
Flight::start();
