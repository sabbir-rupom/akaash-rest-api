<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Description of ResultCode
 *
 * @author sabbir-hossain
 */
class ResultCode {

    /*
     * USER DEFINED
     */

    const SUCCESS = 0;    // No error found

    const UNKNOWN_ERROR = 1;    // unexpected error occured

    const INVALID_JSON = 2;     // Illegal / Incorrect JSON.

    const SESSION_ERROR = 3;     // Session Broke / user is not logged in

    const INVALID_REQUEST_TOKEN = 4;     // Request token from header is invalid.

    const INVALID_REQUEST_PARAMETER = 5;     // Request parameter is incorrect.

    const USER_NOT_FOUND = 6;     // User is not registerd to the system.

    const USER_ALREADY_EXISTS = 7;    // Already registered (at the time of user registration).
    
    const DATA_NOT_ALLOWED = 8;    // Provided data not allowed in server
    
    const FILE_UPLOAD_ERROR = 10;    // File upload error
 
    const JSON_OUTPUT_ERROR = 20;     //Another user connected to google play

    const ACCESS_FORBIDDEN = 100;     //Forbidden access error

    /* 
     * SERVER RELATED 
     */
    

    const NOT_FOUND_404 = 404;     // Data not found

    const OWNER_NOT_FOUND = 500;     //Owner Not found
    
    // Login blacklist. Not be able to game play.
    const LOGIN_BLACKLIST = 1000;
    const CODE_MESSAGE = array(
        self::SUCCESS => "Success",
        self::UNKNOWN_ERROR => "Unknown error",
        self::INVALID_JSON => "Invalid JSON",
        self::SESSION_ERROR => "Session expired",
        self::INVALID_REQUEST_TOKEN => "Invalid token requested",
        self::INVALID_REQUEST_PARAMETER => "Invalid request parameter",
        self::USER_NOT_FOUND => "User not found",
        self::USER_ALREADY_EXISTS => "User already exists",
        self::NOT_FOUND_404 => "404 not fount",
        self::OWNER_NOT_FOUND => "Owner not found",
        self::LOGIN_BLACKLIST => "Blacklisted user",
        self::ACCESS_FORBIDDEN => "Direct access forbidden",
        self::DATA_NOT_ALLOWED => "Data not allowed",
        self::FILE_UPLOAD_ERROR => "File upload failed",
        self::JSON_OUTPUT_ERROR => "Failed to convert result to JSON"
    );

    /**
     * Get result code message
     * @param int $code
     * @return string Return message agains result code.
     */
    public static function getTitle($code) {
        $message = self::CODE_MESSAGE;
        return $message[$code];
    }

    /**
     * Get result code message
     * @param int $code
     * @return string Return message agains result code.
     */
    public static function getMessage($code) {
        $message = self::CODE_MESSAGE;
        return $message[$code];
    }

}
