<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Application constant class
 * This class provides all constant values required throughout this application
 * 
 * @author sabbir-hossain
 */

class System_Constant {

    /**
     */
    const PLATFORM_TYPE_NONE = 0;

    /**
     */
    const PLATFORM_TYPE_IOS = 1;

    /**
     */
    const PLATFORM_TYPE_ANDROID = 2;

    /**
     * Maintenance type (not the maintenance.)
     * @var int
     */
    const MAINTENANCE_TYPE_NOT_MAINTENANCE = 0;

    /**
     * Maintenance type (normal maintenance)
     * @var int
     */
    const MAINTENANCE_TYPE_NORMAL = 1;

    /**
     * Maintenance type (no RDB connection)
     * @var int
     */
    const MAINTENANCE_TYPE_NONE_RDB_CONNECTION = 2;
    
    /*
     * JWT Token verification error codes
     */
    const HASH_SIGNATURE_VERIFICATION_FAILED = 1;
    const EMPTY_TOKEN = 5;
    
    /*
     * User Profile image upload path
     */
    
    const UPLOAD_PROFILE_IMAGE_PATH = ROOT_DIR . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile_images' . DIRECTORY_SEPARATOR;
    const UPLOAD_PROFILE_IMAGE_PATH_MOBILE = self::UPLOAD_PROFILE_IMAGE_PATH . 'mobile' . DIRECTORY_SEPARATOR;
    
    /*
     * Set custom maximum width & height for image mobile view
     */
    const CUSTOM_IMAGE_RESIZE_ARRAY = [ // array(width,height)
        [256,256] // For mobile view
    ];

}
