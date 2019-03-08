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

if (!isset($argv)) {

    /*
     * Set root / index page response view
     */

    Flight::route('GET|POST /', function() {
        Flight::json(array(
            'error' => array(
                'title' => 'Direct Access Forbidden',
                'message' => 'Unauthorized access is forbidden',
            ),
            'result_code' => ResultCode::ACCESS_FORBIDDEN
        ));
    });

    // Set route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE /api/@name', array('Controller', 'initAPI'));
    
    // Set group route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE /api/@group/@name', array('Controller', 'initGroupAPI'));

    /*
     * Image path is masked in API response 
     * Show image from file get content by table rowID and type
     */
    Flight::route('GET /image/@type/@id', array('ShowImage', 'index'));

    /*
     * Set error page page response
     */
    Flight::map('notFound', function() {
        Flight::json(array(
            'error' => array(
                'title' => 'Data Not Found',
                'message' => 'Requested data not found',
            ),
            'result_code' => ResultCode::NOT_FOUND
        ));
    });
}
