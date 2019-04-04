<?php

/**
 * RESTful API Template
 *
 * A RESTful API template in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	https://github.com/sabbir-rupom/rest-api-PHP-flight/blob/master/LICENSE ( MIT License )
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * API Exception Class.
 */
class System_ApiException extends Exception
{
    public function __construct($code = 0, $message = '', $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
