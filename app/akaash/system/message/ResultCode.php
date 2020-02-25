<?php

declare(strict_types = 1);

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Akaash\System\Message;

use Akaash\System\Message\Constant as Constant;
use Akaash\System\Message\MethodTemplate as Template;

/**
 * ResultCode Class
 * This class represents the state of API during / after the execution
 * Points out the type of exception along with user-defined messages to handle all error exceptions
 *
 * @author sabbir-hossain
 */
class ResultCode implements Constant, Template
{

    /**
     * Get result code message
     * @param int $code
     * @return string Return message against result code.
     */
    public static function getTitle(int $code): string
    {
        return self::CODE_MESSAGE[$code]['title'];
    }

    /**
     * Get result code message
     * @param int $code
     * @return string Return message against result code.
     */
    public static function getMessage(int $code): string
    {
        return self::CODE_MESSAGE[$code]['msg'];
    }

    /**
     * Get result code corresponding HTTP status code
     * @param int $code
     * @return int Return HTTP Status code against result code.
     */
    public static function getHTTPstatusCode(int $code): int
    {
        return self::CODE_MESSAGE[$code]['http_status'];
    }
}
