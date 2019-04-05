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
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Application constant class
 * This class provides all constant values required throughout this application.
 */
class Const_Application {
    /** Unknown client
     */
    const PLATFORM_TYPE_NONE = 0;

    /** iOS Client
     */
    const PLATFORM_TYPE_IOS = 1;

    /** Android Client
     */
    const PLATFORM_TYPE_ANDROID = 2;

    /** Windows Phone Client
     */
    const PLATFORM_TYPE_WINDOWS = 3;

    // JWT Token verification error codes
    const HASH_SIGNATURE_VERIFICATION_FAILED = 1;
    const EMPTY_TOKEN = 5;

    // User Profile image upload path
    const UPLOAD_PROFILE_IMAGE_PATH = APP_DIR.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'profile_images'.DIRECTORY_SEPARATOR;
    const UPLOAD_PROFILE_IMAGE_PATH_MOBILE = self::UPLOAD_PROFILE_IMAGE_PATH.'mobile'.DIRECTORY_SEPARATOR;

    // Set custom maximum width & height for image mobile view
    const MOBILE_IMAGE_WIDTH = 256;
    const MOBILE_IMAGE_HEIGHT = 256;

    // Allowed encoded algorithms for JWT
    const JWT_ENCODE_ALGORITHMS = array('HS256', 'HS512');
}
