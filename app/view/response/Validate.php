<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

namespace View\Response;

use System\Message\ResultCode as ResultCode;
use System\Exception\AppException as AppException;


/**
 * Response Validation class
 *
 * @author sabbir-hossain
 */

class Validate {
        /**
     * Convert result to JSON Object
     * @param array $data array of result from API
     * Convert result to JSON Object for output result
     */
    public static function safeJsonEncode(array $data) {
        $encoded = json_encode($data, JSON_PRETTY_PRINT);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw new AppException(ResultCode::JSON_OUTPUT_ERROR, "Maximum stack depth exceeded");
            case JSON_ERROR_STATE_MISMATCH:
                throw new AppException(ResultCode::JSON_OUTPUT_ERROR, "Underflow or the modes mismatch");
            case JSON_ERROR_CTRL_CHAR:
                throw new AppException(ResultCode::JSON_OUTPUT_ERROR, "Unexpected control character found");
            case JSON_ERROR_SYNTAX:
                throw new AppException(ResultCode::JSON_OUTPUT_ERROR, "Syntax error, malformed JSON");
            case JSON_ERROR_UTF8:
                $clean = self::utf8ize($data);
                return CommonUtil::safeJsonEncode($clean);
            default:
                throw new AppException(ResultCode::JSON_OUTPUT_ERROR, "Unknown error");
        }
    }
}
