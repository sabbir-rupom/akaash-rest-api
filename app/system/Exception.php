<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * System Exception Class
 * 
 * @author sabbir-hossain
 */

class System_Exception extends Exception {

    public function __construct($code = 0, $message = '', Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
