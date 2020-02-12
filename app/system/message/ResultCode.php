<?php

declare(strict_types = 1);

/**
 * ResultCode Class
 * This class represents the state of API during / after the execution 
 * Points out the type of exception along with user-defined messages to handle all error exceptions
 * 
 * @author sabbir-hossain
 */
(defined('APP_NAME')) OR exit('Forbidden 403');

namespace System\Message;

use System\Message\Constant as Constant;
use System\Message\MethodTemplate as Template;

class ResultCode implements Constant, Template {

    /**
     * Get result code message
     * @param int $code
     * @return string Return message against result code.
     */
    public static function getTitle(int $code): string {
        return self::CODE_MESSAGE[$code]['title'];
    }

    /**
     * Get result code message
     * @param int $code
     * @return string Return message against result code.
     */
    public static function getMessage(int $code): string {
        return self::CODE_MESSAGE[$code]['msg'];
    }

    /**
     * Get result code corresponding HTTP status code
     * @param int $code
     * @return int Return HTTP Status code against result code.
     */
    public static function getHTTPstatusCode(int $code): int {
        return self::CODE_MESSAGE[$code]['http_status'];
    }

}










