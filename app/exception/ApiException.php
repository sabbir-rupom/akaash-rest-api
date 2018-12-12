<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Bit Coin Exception
 */
class Exception_ApiException extends Exception {

    public function __construct($code = 0, $message = '', Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
