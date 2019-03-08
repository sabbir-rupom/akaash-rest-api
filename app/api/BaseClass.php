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
 * Description of BaseClass
 *
 * @author sabbir-hossain
 */
class BaseClass {

    /**
     * User Authentication variable defined
     */
    const LOGIN_REQUIRED = FALSE;

    /*
     * Token Verification bypass config
     */
    const TEST_ENV = FALSE;

    protected $headers;
    protected $getParams;
    protected $json;
    protected $sessionId = NULL;
    protected $requestToken = NULL;
    protected $requestTime = NULL;
    protected $userId = NULL;
    protected $cacheUser = NULL;

    /**
     * Client Type, Version
     */
    protected $clientType;
    protected $clientVersion;
    protected $actionName;

    /**
     * Database PDO 
     */
    protected $pdo;

    /**
     * Server Configuration Parameters
     */
    protected $config;

    /**
     * Base Class Constructor
     * @param array $headers All Requested Headers
     * @param array $getParams All Requested Query/GET Parameters
     * @param obj $json All Requested JSON Parameters
     * @param string $apiName API Controller Class Name
     * @return Instance of Requested API Controller
     */
    public function __construct($headers, $getParams, $json, $actionName) {
        $this->headers = $headers;
        $this->getParams = $getParams;
        $this->json = $json;
        $this->actionName = $actionName;

        /*
         * Get Configuration Parameters
         * template_api_user_ses_2 template_api_users_2
         */
        $this->config = Flight::get('app_config');

        /*
         * Get DB Connection object
         */
        $this->pdo = Flight::pdo();

        /*
         * Retrive the session ID 
         * Retrive request token 
         * From HTTP Header
         */

        foreach ($this->headers as $key => $value) {
            $upperKey = strtoupper($key);
//            if ($this->config['USER_SESSION_HEADER_KEY'] == $upperKey) {
//                $this->sessionId = $value;
//            }
            if ($this->config['REQUEST_TOKEN_HEADER_KEY'] == $upperKey) {
                $this->requestToken = $value;
                break;
            }
        }
    }

    /**
     * filter processing
     *
     * @throws Exception
     */
    protected function _filter() {
        /*
         * Check Server Maintenance Status
         */
        $this->_checkMaintenance();

        /*
         * Check & Verify Request Token
         */
        $this->_checkRequestToken();
        /*
         *  Check & Verify User Login
         */
        if (static::LOGIN_REQUIRED) {
            $this->isLoggedIn();

            if (FALSE == $this->userId) {
                throw new System_Exception(ResultCode::SESSION_ERROR, 'Session error.');
            }
        }
    }

    /**
     * Maintenance check
     *
     * @throws System_Exception
     */
    protected function _checkMaintenance() {
        // Maintenance check parameter
        $maintainance_check = FALSE;

        if ($maintainance_check) {
            if ($this->isMaintenance()) {
                throw new System_Exception(ResultCode::MAINTENANCE, 'maintenance.');
            }
        }
    }

    /**
     * Verify request token
     * [ Check if API request-call has been issued from authenticated source ]
     */
    protected function _checkRequestToken() {

        if (Config_Config::getInstance()->checkRequestTokenFlag() && !self::TEST_ENV) {
            $result = System_JwtToken::verifyToken($this->requestToken, $this->config['REQUEST_TOKEN_SECRET']);

            if ($result['error'] > 0) {
                switch ($result['error']) {
                    case System_Constant::HASH_SIGNATURE_VERIFICATION_FAILED:
                        throw new System_Exception(ResultCode::INVALID_REQUEST_TOKEN, 'Signature Verification Error');
                        break;
                    case System_Constant::EMPTY_TOKEN:
                        throw new System_Exception(ResultCode::INVALID_REQUEST_TOKEN, 'Token is empty');
                        break;
                    default :
                        throw new System_Exception(ResultCode::UNKNOWN_ERROR, 'Unexpected token error has been found');
                        break;
                }
            } else {
                /**
                 * Retrieve session ID from payload if exist
                 */
                if (!empty($result['data']->sessionToken)) {
                    $this->sessionId = $result['data']->sessionToken;
                }
                /**
                 * Get client API request time 
                 */
                if (!empty($result['data']->iat)) {
                    // convert request time to Server time assuming client request time format is in UTC milisecond
                    $this->requestTime = intval($result['data']->reqAt) + (date('Z') * 1000);
                } else {
                    $this->requestTime = time();
                }
            }
        }
    }

    /**
     * Request processing execution.
     */
    public function process() {
        /*
         * Check and verify client request call / User session
         */
        $this->_filter();

        if (static::LOGIN_REQUIRED) {
            $this->cacheUser = Model_User::cache_or_find($this->userId, $this->pdo);

            if (empty($this->cacheUser)) {
                throw new System_Exception(ResultCode::USER_NOT_FOUND, 'Session user not found');
            }

            $this->cacheUser->last_api_time = Helper_DateUtil::getToday();
            /*
             * Update user's active time without updating cache
             */
            $this->cacheUser->update($this->pdo, FALSE);
        }
        /*
         * Fetch and check GET query strings
         */
        $this->validate();

        /*
         * Execute API Action Controller
         */
        $response = $this->action();
        return $response;
    }

    /**
     * Execute the individual processing of the action.
     *
     * @return array Array to convert to response JSON.
     * @throws Exception
     */
    public function action() {
        throw new Exception('action is not implemented.');
    }

    /**
     * It verifies the value.
     */
    public function validate() {
        /*
         * Client Type [1 = Android, 2 = iOS, 0 = Other]
         */
        $this->clientType = $this->getValueFromQuery('client_type', 'int');

        /*
         * Applicaion version
         */
        $this->clientVersion = $this->getValueFromQuery('client_version', 'int');
    }

    /**
     * Return extract value from the JSON.
     *
     * @param $path JSON Path array to the value contained in. Is acceptable string if the highest layer.
     * @param $type Type of the variable. "int", "bool", "string".
     * @param $required Required.
     * @return Value extracted from JSON.
     */
    protected function getValueFromJSON($path, $type, $required = FALSE) {
        if (empty($this->json) && $required) {
            throw new System_Exception(ResultCode::INVALID_JSON, 'JSON is empty.');
        }
        if (is_string($path)) {
            $path = array(
                $path
            );
        }
        $pathStr = implode("->", $path);
        $var = $this->json;

        try {
            while (!empty($path)) {
                $pathElement = array_shift($path);
                $var = $var->$pathElement;
            }
        } catch (Exception $e) {
            $var = NULL;
        }
        if (TRUE == $required && (is_null($var) || $var === '')) {
            throw new System_Exception(ResultCode::INVALID_REQUEST_PARAMETER, "$pathStr is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new System_Exception(ResultCode::INVALID_REQUEST_PARAMETER, "The type of $pathStr is not valid.");
        }
        return $var;
    }

    /**
     * Return from GET parameters to extract the value.
     *
     * @param string $name GET The name of the parameter.
     * @param unknown $type Type of the variable. "int", "bool", "string".
     * @param bool $required Required. Extracted value from
     * @return GET parameters.
     */
    protected function getValueFromQuery($name, $type, $required = FALSE) {
        if (isset($this->getParams[$name])) {
            $var = $this->getParams[$name];
            if ('string' != $type && '' === $var) {
                $var = NULL;
            }
        } else {
            $var = NULL;
        }
        if (TRUE == $required && is_null($var)) {
            throw new System_Exception(ResultCode::INVALID_REQUEST_PARAMETER, "$name is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new System_Exception(ResultCode::INVALID_REQUEST_PARAMETER, "The type of $name is not valid.");
        }
        return $var;
    }

    /**
     * Return parameter value from Post call
     *
     * @param string $name Name of the parameter.
     * @param unknown $type Type of the variable. "int", "bool", "string".
     * @param bool $required Value required
     * @param bool $xss_clean XSS clean 
     * @return parameter value from POST Request.
     */
    protected function getValueFromInputPost($name, $type, $required = FALSE, $xss_clean = FALSE) {
        if (isset($_POST[$name])) {
            $var = $_POST[$name];
            if ('string' != $type && '' === $var) {
                $var = NULL;
            }
        } else {
            $var = NULL;
        }

        if (TRUE == $required && empty($var)) {
            throw new System_Exception(ResultCode::INVALID_REQUEST_PARAMETER, "$name is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new System_Exception(ResultCode::INVALID_REQUEST_PARAMETER, "The type of $name is not valid.");
        }

        return ($xss_clean === TRUE) ? System_Security::xssClean($var) : $var;
    }

    /**
     * Check the type of the value.
     *
     * @param unknown $value Value value to validate.
     * @param unknown $type Type expected to type.
     * If it is correct type of @return value TRUE, otherwise FALSE. 
     * Value returns TRUE unconditionally if it is NULL.
     * 
     * @return bool True / False
     */
    protected function isValidType($value, $type) {
        $result = FALSE;
        if (is_null($value)) {
            return TRUE;
        } else {
            switch ($type) {
                case 'int':
                    $result = Helper_CommonUtil::isInt($value);
                    break;
                case 'bool':
                    $result = is_bool($value);
                    break;
                case 'string':
                    $result = is_string($value);
                    break;
                case 'float':
                    $result = is_float($value) || is_int($value);
                    break;
                case 'json':
                    $result = $this->is_json($value);
                    break;
                case 'binary':
                    $result = preg_match('~[^\x20-\x7E\t\r\n]~', $str) > 0;
                    break;
                case 'array':
                    $result = is_array($value);
                    break;
            }
        }
        return $result;
    }

    /**
     * Or is a login state check.
     * A user ID if the login state, returns FALSE if it is not logged in.
     */
    private function isLoggedIn() {
        if ($this->sessionId) {
            $sessionArray = unserialize(base64_decode($this->sessionId));
            $cacheSessionId = Model_User::retrieveSessionFromUserId($sessionArray['user_id']);

            if ($cacheSessionId == $sessionArray['session_id']) {
                $this->userId = (int) $sessionArray['user_id'];

                // Re-set of the session time limit
                Model_User::cacheSession($cacheSessionId, $this->userId);

                return true;
            }
        }
        return false;
    }

    /**
     * Application of state to check whether maintenance mode.
     *
     * @return boolean In the case of maintenance mode, true. Otherwise, false.
     */
    protected function isMaintenance() {


        // In the case of maintenance state
        if (System_Constant::MAINTENANCE_TYPE_NORMAL == $this->config['MAINTENANCE']) {

            // If it is not in the test user
            if (false == $this->isTestUser()) {
                return true;
            }
            // No RDB connection maintenance
        } else if (System_Constant::MAINTENANCE_TYPE_NONE_RDB_CONNECTION == $this->config['MAINTENANCE']) {
            return true;
        }
        return false;
    }

    /**
     * Returns whether or not the test user.
     *
     * @return boolean If it has been tested user registration, true. Others are false.
     */
    protected function isTestUser() {

        // If you are already logged in
        if (true == $this->isLoggedIn()) {
            // Check if session user ID matches the tester ID
            return ($this->userId == 1); // Tester ID is Set to 1
        }

        return false;
    }

    /**
     * 
     * @param int $str
     * @return boolean if string is in json formate
     */
    protected function is_json($str) {
        $json = json_decode($str);
        return $json && $str != $json;
    }

}
