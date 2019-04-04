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

if (!isset($argv)) {
    // Set root / index page response view

    Flight::route('GET|POST /', function () {
        Flight::json(array(
            'error' => array(
                'title' => 'Direct Access Forbidden',
                'message' => 'Unauthorized access is forbidden',
            ),
            'result_code' => ResultCode::ACCESS_FORBIDDEN,
        ));
    });

    // Set route for all GET query request from client
    Flight::route('GET /api/@name', array('Controller', 'initGet'));

    // Set route for all POST query request from client
    Flight::route('POST /api/@name', array('Controller', 'initPost'));

    // Set route for all PUT query request from client
    Flight::route('PUT /api/@name', array('Controller', 'initPut'));

    // Set route for all PATCH query request from client
    Flight::route('PATCH /api/@name', array('Controller', 'initPatch'));

    // Set route for all DELETE query request from client
    Flight::route('DELETE /api/@name', array('Controller', 'initDelete'));

    /*
     * Image path is masked in API response
     * Show image from file get content by table rowID and type
     */
    Flight::route('GET /image/@type/@id', array('ShowImage', 'index'));

    // Set error page page response
    Flight::map('notFound', function () {
        Flight::json(array(
            'error' => array(
                'title' => 'Data Not Found',
                'message' => 'Requested data not found',
            ),
            'result_code' => ResultCode::NOT_FOUND,
        ));
    });
}
