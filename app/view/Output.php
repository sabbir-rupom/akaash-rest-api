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

/**
 * Output class
 * This class views all the responses after API execution.
 *
 * @author sabbir-hossain
 */
class View_Output
{
    /**
     * class constructor.
     */
    public function __construct()
    {
    }

    /**
     * Server Response in JSON.
     *
     * @param mixed $json_array
     */
    public static function responseJson($json_array)
    {
        $jsonResult = self::safe_json_encode($json_array);
        /*
         * Flight JSON encode feature is not used
         * to avoid JSON_ERROR_UTF8
         * ------------- $arr = array_map('utf8_encode', $json_array);
         * ------------- Flight::json($arr);
         */

        header('Content-type:application/json;charset=utf-8');
        echo $jsonResult;
        exit;
    }

    /*
     * Convert result to JSON Object
     * @param array $data array of result from API
     * Convert result to JSON Object for output result
     */

    public static function safe_json_encode($data)
    {
        $encoded = json_encode($data, JSON_PRETTY_PRINT);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw new System_ApiException(ResultCode::JSON_OUTPUT_ERROR, 'Maximum stack depth exceeded');
            case JSON_ERROR_STATE_MISMATCH:
                throw new System_ApiException(ResultCode::JSON_OUTPUT_ERROR, 'Underflow or the modes mismatch');
            case JSON_ERROR_CTRL_CHAR:
                throw new System_ApiException(ResultCode::JSON_OUTPUT_ERROR, 'Unexpected control character found');
            case JSON_ERROR_SYNTAX:
                throw new System_ApiException(ResultCode::JSON_OUTPUT_ERROR, 'Syntax error, malformed JSON');
            case JSON_ERROR_UTF8:
                $clean = self::utf8ize($data);

                return self::safe_json_encode($clean);
            default:
                throw new System_ApiException(ResultCode::JSON_OUTPUT_ERROR, 'Unknown error');
        }
    }

    /*
     * utf8 error correction from result array
     * @param array $mixed array of result from API
     * Correct all utf-8 related errors for proper JSON output
     */

    public static function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return utf8_encode($mixed);
        }

        return $mixed;
    }
}
