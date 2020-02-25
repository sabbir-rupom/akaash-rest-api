<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace View;

use View\Response\Format as ResponseFormat;
use System\Config as Config;

/**
 * Output class
 * This class views all the responses after API execution
 *
 * @author sabbir-hossain
 */
class Output
{

    /**
     * Server Response in JSON
     */
    public static function response($data = null)
    {
        if ('PRODUCTION' != strtoupper(Config::getInstance()->getEnvironment())) {
            /*
             * Calculate server execution time for running API script [ For developers only ]
             * And add to output result
             */
            $data['execution_time'] = round(microtime(true) - \Flight::app()->get('start_time'), 4) . ' seconds';
        }
        if (is_array($data)) {
            header('Content-type:application/json;charset=utf-8');
            echo ResponseFormat::formatJson($data);
        }

        if (\Flight::app()->get('exit_on_response')) {
            exit();
        }
    }
}
