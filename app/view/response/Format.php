<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace View\Response;

use System\Config as Config;
use View\Response\Validate as Validate;

/**
 * Response Format class
 *
 * @author sabbir-hossain
 */

class Format
{
    public static function formatJson(array $data): string
    {
        if ('PRODUCTION' != strtoupper(Config::getInstance()->getEnv())) {
            /*
             * Calculate server execution time for running API script [ For developers only ]
             * And add to output result
             */
            $data['execution_time'] = (microtime(true) - \Flight::app()->get('start_time')) . ' seconds ';
        }
        $jsonResult = Validate::safeJsonEncode($data);
        /*
         * Flight JSON encode feature is not used
         * to avoid JSON_ERROR_UTF8
         * ------------- $arr = array_map('utf8_encode', $json_array);
         * ------------- Flight::json($arr);
         */

        return $jsonResult;
    }
}
