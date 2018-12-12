<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Application Related Exception.
 *
 */
class Exception_ApplicationException extends Exception_SystemException {

    protected $_errorNo = 20002;
    protected $_cause = 1;

    // Redefine the exception, and not the message options
    public function __construct($message = "", $code = 0, Exception $previous = null, $cause = null) {
        if (null !== $cause) {
            $this->_cause = $cause;
        }

        parent::__construct($message, $code, $previous);
    }

    public function getCause() {
        return $this->_cause;
    }

}
