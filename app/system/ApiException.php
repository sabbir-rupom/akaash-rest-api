<?php

/*
 * RESTful API Template
 * 
 * A RESTful API template based on flight-PHP framework
 * This software project is based on my recent REST-API development experiences. 
 * 
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT 
 * 
 * @author	Sabbir Hossain Rupom
 * @since	Version 1.0.0
 * @filesource
 */


(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * API Exception Class
 * 
 * @author sabbir-hossain
 */
class System_ApiException extends Exception {

    public function __construct($code = 0, $message = '', $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
