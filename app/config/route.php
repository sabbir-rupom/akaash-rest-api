<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Message\ResultCode;
use Helper\DateUtil;

if (!isset($argv)) {
    // Set root / index page response view

    $apiUriPrefix = ''; // e.g /api

    Flight::route('/', function () {
        Flight::json(array(
          'data' => array(
            'title' => 'Welcome',
            'time' => DateUtil::getToday(),
            'message' => 'Akaash is a REST-API template is built in PHP, with flight micro-framework as engine',
          ),
          'result_code' => ResultCode::SUCCESS,
          'error' => []
        ));
    });

    // Set route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE ' . $apiUriPrefix . '/@name', array('Core\Controller', 'initAPI'));

    // Set group route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE ' . $apiUriPrefix . '/@group/@name', array('Core\Controller', 'initGroupAPI'));

    // Set group route for GET|POST|PUT|PATCH|DELETE query request from client
    Flight::route('GET|POST|PUT|PATCH|DELETE ' . $apiUriPrefix . '/@group/@name/@value', array('Core\Controller', 'initGroupAPIwithParam'));

    // Set error page page response
    Flight::map('notFound', function () {
        Flight::json([
          'result_code' => ResultCode::NOT_FOUND,
          'time' => DateUtil::getToday(),
          'data' => [],
          'error' => [
            'title' => 'Data Not Found: ' . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : ''),
            'message' => 'Requested data not found',
          ]
        ], ResultCode::getHTTPstatusCode(ResultCode::NOT_FOUND));
    });
}
