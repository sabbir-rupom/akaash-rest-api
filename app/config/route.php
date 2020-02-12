<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Message\ResultCode as ResultCode;

if (!isset($argv)) {
    // Set root / index page response view

    Flight::route('/', function () {
        Flight::json(array(
            'data' => array(
                'title' => 'Welcome',
                'message' => 'This REST-API template is built in PHP, with flight micro-framework as engine',
            ),
            'result_code' => ResultCode::SUCCESS,
        ));
    });

    // Set route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE /api/@name', array('Core\Controller', 'initAPI'));

    // Set group route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE /api/@group/@name', array('Core\Controller', 'initGroupAPI'));

    // Set group route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE /api/@group/@name/@value', array('Core\Controller', 'initGroupAPIwithParam'));

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














