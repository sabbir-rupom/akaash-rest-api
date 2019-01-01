<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Controller for application
 *
 * @property BaseClass $action BaseClass
 * @author sabbir-hossain
 */
class Controller {

    protected static $apiName;
    protected static $getParams;
    protected static $headers;
    protected static $json;

    /**
     * Initialize application
     * 
     * @param string $name REST API name
     * @param string $method Application request Method 
     */
    public static function init($name, $method) {
        $data = null;
        try {
            self::$apiName = Common_Utils::camelize($name); // prepare api controller from request url call
            self::$getParams = $_GET;
            self::$headers = getallheaders();

            if (in_array($method, array('POST', 'PUT', 'PATCH', 'DELETE'))) {
                /*
                 * Fetch all requested parameters
                 */
                $data = file_get_contents('php://input');

                self::$json = json_decode($data);

                /*
                 * Check if requested parameters are in json format or not 
                 */
                if (!empty($data) && json_last_error() != JSON_ERROR_NONE && empty($_FILES)) {
                    throw new Exception_ApiException(ResultCode::INVALID_JSON, "Invalid JSON: $data");
                }
            } else {
                self::$json = array();
            }

            /*
             * Check if requested API controller exist in server
             */
            if (!class_exists(self::$apiName)) {
                throw new Exception_ApiException(ResultCode::UNKNOWN_ERROR, "No such api: " . $name);
            }

            /**
             * Call Base Controller to Retrieve Instance of API Controller
             */
            $action = new self::$apiName(self::$headers, self::$getParams, self::$json, self::$apiName);
            $result = $action->process();
        } catch (Exception $e) {
            /*
             * Handle all exception messages
             */

            if ($e instanceof Exception_ApiException) {
                header("HTTP/1.0 " . ResultCode::getHTTPstatusCode($e->getCode()) . " " . ResultCode::getTitle($e->getCode()));
                $result = array(
                    'result_code' => $e->getCode(),
                    'time' => Common_DateUtil::getToday(),
                    'error' => array(
                        'title' => ResultCode::getTitle($e->getCode()),
                        'msg' => empty($e->getMessage()) ? ResultCode::getMessage($e->getCode()) : $e->getMessage()
                    )
                );
            } else if($e instanceof PDOException){
                header("HTTP/1.0 " . ResultCode::getHTTPstatusCode(ResultCode::DATABASE_ERROR) . " " . ResultCode::getTitle(ResultCode::DATABASE_ERROR));
                $result = array(
                    'result_code' => ResultCode::DATABASE_ERROR,
                    'time' => Common_DateUtil::getToday(),
                    'error' => array(
                        'title' => ResultCode::getTitle(ResultCode::DATABASE_ERROR),
                        'msg' => ResultCode::getMessage(ResultCode::DATABASE_ERROR) . ': check connection'
                    )
                );
            } else {
            
                header("HTTP/1.0 " . ResultCode::getHTTPstatusCode(ResultCode::UNKNOWN_ERROR) . " " . ResultCode::getTitle(ResultCode::UNKNOWN_ERROR));
                $result = array(
                    'result_code' => ResultCode::UNKNOWN_ERROR,
                    'time' => Common_DateUtil::getToday(),
                    'error' => array(
                        'title' => ResultCode::getTitle(ResultCode::UNKNOWN_ERROR),
                        'msg' => ResultCode::getMessage(ResultCode::UNKNOWN_ERROR)
                    )
                );
            }
            if (Config_Config::getInstance()->isErrorDump()) {
                /*
                 * Additional error messages 
                 * For developers debug purpose
                 */
                $result['error_dump'] = array(
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                );
            }
        }
        $json_array = $result;


        if (strtoupper(Flight::get('env')) != 'PRODUCTION') {
            /*
             * Calculate server execution time for running API script [ For developers only ]
             * And add to output result
             */
            $json_array['execution_time'] = microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"];
        }
        $jsonResult = self::safe_json_encode($json_array);
        /*
         * Flight JSON encode feature is not used 
         * to avoid JSON_ERROR_UTF8 
         */

        header('Content-type:application/json;charset=utf-8');
        echo $jsonResult;
        exit;
//        $arr = array_map('utf8_encode', $json_array);
//        Flight::json($arr);
    }

    /**
     * Initialize application for GET method
     * @param type $name Api name
     */
    public static function initGet($name) {
        self::init($name, 'GET');
    }

    /**
     * Initialize application for POST method
     * @param type $name Api name
     */
    public static function initPost($name) {
        self::init($name, 'POST');
    }

    /**
     * Initialize application for PUT method
     * @param type $name Api name
     */
    public static function initPut($name) {
        self::init($name, 'PUT');
    }

    /**
     * Initialize application for PATCH method
     * @param type $name Api name
     */
    public static function initPatch($name) {
        self::init($name, 'PATCH');
    }

    /**
     * Initialize application for DELETE method
     * @param type $name Api name
     */
    public static function initDelete($name) {
        self::init($name, 'DELETE');
    }

    /*
     * Convert result to JSON Object
     * @param array $data array of result from API
     * Convert result to JSON Object for output result
     */

    public static function safe_json_encode($data) {
        $encoded = json_encode($data, JSON_PRETTY_PRINT);

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $encoded;
            case JSON_ERROR_DEPTH:
                throw new Exception_ApiException(ResultCode::JSON_OUTPUT_ERROR, "Maximum stack depth exceeded");
            case JSON_ERROR_STATE_MISMATCH:
                throw new Exception_ApiException(ResultCode::JSON_OUTPUT_ERROR, "Underflow or the modes mismatch");
            case JSON_ERROR_CTRL_CHAR:
                throw new Exception_ApiException(ResultCode::JSON_OUTPUT_ERROR, "Unexpected control character found");
            case JSON_ERROR_SYNTAX:
                throw new Exception_ApiException(ResultCode::JSON_OUTPUT_ERROR, "Syntax error, malformed JSON");
            case JSON_ERROR_UTF8:
                $clean = self::utf8ize($data);
                return self::safe_json_encode($clean);
            default:
                throw new Exception_ApiException(ResultCode::JSON_OUTPUT_ERROR, "Unknown error");
        }
    }

    /*
     * utf8 error correction from result array 
     * @param array $mixed array of result from API
     * Correct all utf-8 related errors for proper JSON output
     */

    public static function utf8ize($mixed) {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::utf8ize($value);
            }
        } else if (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

}
