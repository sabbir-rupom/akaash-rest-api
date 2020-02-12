<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

namespace View;

use Helper\CommonUtil as CommonUtil;
use View\Response\Format as ResponseFormat;

/**
 * Output class
 * This class views all the responses after API execution
 * 
 * @author sabbir-hossain
 */
class Output {

    /**
     * Server Response in JSON
     */
    public static function response($data = null) {

        if (is_array($data)) {
            header('Content-type:application/json;charset=utf-8');
            echo ResponseFormat::formatJson($data);
        }

        exit();
    }

}

