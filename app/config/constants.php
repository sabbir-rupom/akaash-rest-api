<?php

/**
 * Write all your application constants in here
 */

// Set numeric value for client end platforms
(!defined('PLATFORM_TYPE_WEB')) or define('PLATFORM_TYPE_WEB', 0);
(!defined('PLATFORM_TYPE_IOS')) or define('PLATFORM_TYPE_IOS', 1);
(!defined('PLATFORM_TYPE_ANDROID')) or define('PLATFORM_TYPE_ANDROID', 2);

/**
 * Maintenance Enable
 * @var int
 */
(!defined('MAINTENANCE_ON')) or define('MAINTENANCE_ON', 1);

/*
 * JWT Token verification error codes
 */
(!defined('INVALID_TOKEN')) or define('INVALID_TOKEN', 1);
(!defined('EMPTY_TOKEN')) or define('EMPTY_TOKEN', 5);

/**
 * File upload path(s)
 */
(!defined('UPLOAD_PATH')) or define('UPLOAD_PATH', ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR);
