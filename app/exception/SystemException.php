<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * System releted Exception.
 *
 */
class Exception_SystemException extends Exception {

    protected $_errorNo = 20001;

    // Redefine the exception, and not the message options
    public function __construct($message = "", $code = 0, Exception $previous = null) {
        //If code not specified, utilizing the default
        if ($code === 0) {
            $code = $this->_errorNo;
        }

        parent::__construct($message, $code, $previous);
    }

    public function getErrorNo() {
        return $this->_errorNo;
    }

}
