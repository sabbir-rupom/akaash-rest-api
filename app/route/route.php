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

(defined('APP_NAME')) or exit('Forbidden 403');

if (!isset($argv)) {
    // Set root / index page response view

    Flight::route('GET|POST /', function () {
        Flight::json([
            'error' => [
                'title' => 'Direct Access Forbidden',
                'message' => 'Unauthorized access is forbidden',
            ],
            'result_code' => ResultCode::ACCESS_FORBIDDEN,
        ]);
    });

    // Set route for all GET query request from client
    Flight::route('GET /api/@name', ['Controller', 'initGet']);

    // Set route for all POST query request from client
    Flight::route('POST /api/@name', ['Controller', 'initPost']);

    // Set route for all PUT query request from client
    Flight::route('PUT /api/@name', ['Controller', 'initPut']);

    // Set route for all PATCH query request from client
    Flight::route('PATCH /api/@name', ['Controller', 'initPatch']);

    // Set route for all DELETE query request from client
    Flight::route('DELETE /api/@name', ['Controller', 'initDelete']);

    /*
     * Image path is masked in API response
     * Show image from file get content by table rowID and type
     */
    Flight::route('GET /image/@type/@id', ['ShowImage', 'index']);

    // Set error page page response
    Flight::map('notFound', function () {
        Flight::json([
            'error' => [
                'title' => 'Data Not Found',
                'message' => 'Requested data not found',
            ],
            'result_code' => ResultCode::NOT_FOUND,
        ]);
    });
}
