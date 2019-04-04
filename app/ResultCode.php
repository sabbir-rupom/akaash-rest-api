<?php

/**
 * RESTful API Template
 *
 * A RESTful API template in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	https://github.com/sabbir-rupom/rest-api-PHP-flight/blob/master/LICENSE ( MIT License )
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * ResultCode Class
 * This class represents the state of API during / after the execution
 * Points out the type of exception along with user-defined messages to handle all error exceptions.
 */
class ResultCode
{
    // USER DEFINED

    const SUCCESS = 0;    // No error found
    const UNKNOWN_ERROR = 1;    // unexpected error occured
    const INVALID_JSON = 2;     // Illegal / Incorrect JSON.
    const SESSION_ERROR = 3;     // Session Broke / user is not logged in
    const INVALID_REQUEST_TOKEN = 4;     // Request token from header is invalid.
    const INVALID_REQUEST_PARAMETER = 5;     // Request parameter is incorrect.
    const USER_NOT_FOUND = 6;     // User is not registerd to the system.
    const DATA_ALREADY_EXISTS = 7;    // Data already exist in table
    const DATA_NOT_ALLOWED = 8;    // Provided data not allowed in server
    const DUPLICATE_DATA = 9;    // Provided data not allowed in server
    const FILE_UPLOAD_ERROR = 10;    // File upload error
    const DATABASE_ERROR = 11;    // Database error
    const PASSWORD_MISMATCHED = 15;    // Password does not match with saved one
    const JSON_OUTPUT_ERROR = 20;     // Failed to generate JSON data
    const ACCESS_FORBIDDEN = 100;     //Forbidden access error
    const NOT_FOUND = 404;     // Data not found

    const USER_BLACKLIST = 1000; // User is blacklisted / blocked error

    const CODE_MESSAGE = array(
        self::SUCCESS => array(
            'title' => 'SUCCESS',
            'msg' => 'Success',
            'http_status' => 404,
        ),
        self::UNKNOWN_ERROR => array(
            'title' => 'UNKNOWN ERROR',
            'msg' => 'Unknown error occured',
            'http_status' => 500,
        ),
        self::INVALID_JSON => array(
            'title' => 'INVALID JSON',
            'msg' => 'Invalid json found',
            'http_status' => 400,
        ),
        self::SESSION_ERROR => array(
            'title' => 'SESSION ERROR',
            'msg' => 'Session expired',
            'http_status' => 401,
        ),
        self::INVALID_REQUEST_TOKEN => array(
            'title' => 'INVALID REQUEST TOKEN',
            'msg' => 'Requested roken is invalid',
            'http_status' => 400,
        ),
        self::INVALID_REQUEST_PARAMETER => array(
            'title' => 'INVALID REQUEST PARAMETER',
            'msg' => 'Requested parameter is not valid',
            'http_status' => 406,
        ),
        self::USER_NOT_FOUND => array(
            'title' => 'USER NOT FOUND',
            'msg' => 'User not found',
            'http_status' => 404,
        ),
        self::DATA_ALREADY_EXISTS => array(
            'title' => 'DATA EXIST',
            'msg' => 'Data already exist',
            'http_status' => 409,
        ),
        self::NOT_FOUND => array(
            'title' => 'NOT FOUND',
            'msg' => 'Data not found',
            'http_status' => 404,
        ),
        self::USER_BLACKLIST => array(
            'title' => 'BLACKLIST USER',
            'msg' => 'User is blacklisted',
            'http_status' => 401,
        ),
        self::ACCESS_FORBIDDEN => array(
            'title' => 'ACCESS FORBIDDEN',
            'msg' => 'Access forbidden',
            'http_status' => 403,
        ),
        self::PASSWORD_MISMATCHED => array(
            'title' => 'PASSWORD MISMATCHED',
            'msg' => 'Password did not matched',
            'http_status' => 406,
        ),
        self::DATA_NOT_ALLOWED => array(
            'title' => 'DATA NOT ALLOWED',
            'msg' => 'Provided data is not acceptable',
            'http_status' => 406,
        ),
        self::DUPLICATE_DATA => array(
            'title' => 'DUPLICATE DATA',
            'msg' => 'Duplicate data found',
            'http_status' => 406,
        ),
        self::DATABASE_ERROR => array(
            'title' => 'DATABASE ERROR',
            'msg' => 'Database error occured',
            'http_status' => 500,
        ),
        self::FILE_UPLOAD_ERROR => array(
            'title' => 'FILE UPLOAD ERROR',
            'msg' => 'File upload failed',
            'http_status' => 500,
        ),
        self::JSON_OUTPUT_ERROR => array(
            'title' => 'JSON OUTPUT ERROR',
            'msg' => 'Failed to generate JSON output',
            'http_status' => 500,
        ),
    );

    /**
     * Get result code message.
     *
     * @param int $code
     *
     * @return string return message against result code
     */
    public static function getTitle($code)
    {
        return self::CODE_MESSAGE[$code]['title'];
    }

    /**
     * Get result code message.
     *
     * @param int $code
     *
     * @return string return message against result code
     */
    public static function getMessage($code)
    {
        return self::CODE_MESSAGE[$code]['msg'];
    }

    /**
     * Get result code corresponding HTTP status code.
     *
     * @param int $code
     *
     * @return int return HTTP Status code against result code
     */
    public static function getHTTPstatusCode($code)
    {
        return self::CODE_MESSAGE[$code]['http_status'];
    }
}
