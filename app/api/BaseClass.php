<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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
    const USER_DATA_REQUIRED = FALSE;

    protected $headers;
    protected $getParams;
    protected $json;
    protected $sessionId = NULL;
    protected $requestToken = NULL;
    protected $userId = NULL;
    protected $cache_user = NULL;

    /**
     * Client Type, Version
     */
    protected $client_type;
    protected $client_version;
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
                throw new Exception_ApiException(ResultCode::SESSION_ERROR, 'Session error.');
            }
        }
    }

    /**
     * Maintenance check
     *
     * @throws Exception_ApiException
     */
    protected function _checkMaintenance() {
        // Maintenance check parameter
        $maintainance_check = FALSE;

        if ($maintainance_check) {
            if ($this->isMaintenance()) {
                throw new Exception_ApiException(ResultCode::MAINTENANCE, 'maintenance.');
            }
        }
    }

    /**
     * Verify request token
     * [ Check if API request-call has been issued from authenticated source ]
     */
    protected function _checkRequestToken() {

        $result = Lib_JwtToken::verify_token($this->requestToken, $this->config['REQUEST_TOKEN_SECRET']);

        if ($result['error'] > 0) {
            switch ($result['error']) {
                case Const_Application::HASH_SIGNATURE_VERIFICATION_FAILED:
                    throw new Exception_ApiException(ResultCode::INVALID_REQUEST_TOKEN, 'Signature Verification Error');
                    break;
                case Const_Application::EMPTY_TOKEN:
                    throw new Exception_ApiException(ResultCode::INVALID_REQUEST_TOKEN, 'Token is empty');
                    break;
                default :
                    throw new Exception_ApiException(ResultCode::UNKNOWN_ERROR, 'Unexpected token error has been found');
                    break;
            }
        } else {
            /*
             * Retrieve session ID from payload if exist
             */
            if (!empty($result['data']->session_id)) {
                $this->sessionId = $result['data']->session_id;
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

        if ($this->userId) {
            /*
             * Save session user information
             */
            $this->cache_user = Model_User::cache_or_find($this->userId, $this->pdo);
            $this->cache_user->last_api_time = Common_Util_DateUtil::getToday();
            $this->cache_user->update($this->pdo, FALSE);
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
        $this->client_type = $this->getValueFromQuery('client_type', 'int');

        /*
         * Applicaion version
         */
        $this->client_version = $this->getValueFromQuery('client_version', 'int');
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
            throw new Exception_ApiException(ResultCode::INVALID_JSON, 'JSON is empty.');
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
            throw new Exception_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "$pathStr is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new Exception_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of $pathStr is not valid.");
        }
        return $var;
    }

    /**
     * Return from GET parameters to extract the value.
     *
     * @param $name GET The name of the parameter.
     * @param $type Type of the variable. "int", "bool", "string".
     * @param $required Required. Extracted value from
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
            throw new Exception_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "$name is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new Exception_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of $name is not valid.");
        }
        return $var;
    }

    /**
     * Check the type of the value.
     *
     * @param $ Value value to validate.
     * @param $ Type expected to type.
     * The time being "int," string "
     * If it is correct type of @return value TRUE, otherwise FALSE. Value returns TRUE unconditionally if it is NULL.
     */
    protected function isValidType($value, $type) {
        $result = FALSE;
        if (is_null($value)) {
            return TRUE;
        } else {
            switch ($type) {
                case 'int':
                    $result = Common_Util_Utils::isInt($value);
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
                    $result = is_binary($value);
                    break;
                case 'array':
                    $result = is_array($value);
                    break;
                default:
                    $result = TRUE;
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
        if (Const_Application::MAINTENANCE_TYPE_NORMAL == $this->config['MAINTENANCE']) {

            // If it is not in the test user
            if (false == $this->isTestUser()) {
                return true;
            }
            // No RDB connection maintenance
        } else if (Const_Application::MAINTENANCE_TYPE_NONE_RDB_CONNECTION == $this->config['MAINTENANCE']) {
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

    /**
     * 
     * @param int $now
     * @return string Date formated as "Ymd His"
     */
    protected function time($now = null) {
        return date("Y-m-d H:i:s", ($now == null ? time() : $now));
    }

    /**
     * Get Distance between two latlong co-ordinates
     * @param int $latitude1, $longitude1, $latitude2, $longitude2, Unit of return(K = Km, M= Miles, N= Nautical)
     * @return float value as Distance
     */
    protected function distance($lat1, $lon1, $lat2, $lon2, $unit) {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "M") {
            return ($miles * 1.609344 * 1000);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    /**
     * Get Distance between two latlong co-ordinates
     * @param int $length String length
     * @param string $type Type of Random String
     * @return string $randomString 
     */
    protected function generate_random_string($length = 4, $type = 'string') {
        $stringRegex = "1234567890ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
        $intRegex = "1234567890";
        $capNdigitRegex = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        switch ($type) {
            case 'num': // All NUMERIC
                $regex = $intRegex;
                break;
            case 'cap': // All CAPITAL
                $regex = $capNdigitRegex;
                break;

            default: // Any Character Combination
                $regex = $stringRegex;
                break;
        }

        $charactersLength = strlen($regex);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $regex[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Process base64 formatted image upload
     * @param int $ID DB insert ID
     * @param string $binary_image Base64 Encoded String
     * @param bool $old_image_delete if any existing image needed to be deleted or kept
     * @return string $image_name Return uploaded image name
     */
    protected function process_binary_image($ID, $binary_image, $type = '', $old_image_delete = TRUE) {
        $base64_string = "data:image/png;base64," . $binary_image;

        $curr_time = time();
        $image_prefix = $type . $ID . '_';
        $image_name = $image_prefix . $curr_time . '.png';
        $mask = $image_prefix . '*.*';

        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH)) {
            /*
             * If upload directory not exist, create
             */
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH, 0777, true);
        } else if ($old_image_delete) {
            /*
             * Delete all previous profile image
             */
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH . $mask));
        }


        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE)) {
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE, 0777, true);
        } else {
            /*
             * Delete all previous profile (mobile size) image
             */
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE . $mask));
        }

        $output_file = Const_Application::UPLOAD_PROFILE_IMAGE_PATH . $image_name;

        $ifp = fopen($output_file, "wb");
        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        /*
         * Resize uploaded image for mobile view
         */
        $this->resize_image($output_file, $image_name, Const_Application::MOBILE_IMAGE_WIDTH, Const_Application::MOBILE_IMAGE_HEIGHT, $type);
        return $image_name;
    }

    /**
     * Process base64 formatted image upload
     * @param int $ID DB insert ID
     * @param string $binary_image Base64 Encoded String
     * @param bool $old_image_delete if any existing image needed to be deleted or kept
     * @return string $image_name Return uploaded image name
     */
    protected function process_image_upload($ID, $image_file, $type = '', $old_image_delete = TRUE) {
        $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, array('jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'))) {
            throw new Exception_ApiException(ResultCode::DATA_NOT_ALLOWED, "Improper image is provided! Only jpg and png allowed!");
        }

        $curr_time = time();
        $image_prefix = $type . $ID . '_';
        $image_name = $image_prefix . $curr_time . '.png';
        $mask = $image_prefix . '*.*';

        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH)) {
            /*
             * If upload directory not exist, create
             */
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH, 0777, true);
        } else if ($old_image_delete) {
            /*
             * Delete all previous profile image
             */
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH . $mask));
        }


        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE)) {
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE, 0777, true);
        } else {
            /*
             * Delete all previous profile (mobile size) image
             */
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE . $mask));
        }

        $output_file = Const_Application::UPLOAD_PROFILE_IMAGE_PATH . $image_name;

        if (move_uploaded_file($image_file["tmp_name"], $output_file)) {
            $this->resize_image($output_file, $image_name, Const_Application::MOBILE_IMAGE_WIDTH, Const_Application::MOBILE_IMAGE_HEIGHT, $type);
        } else {
            throw new Exception_ApiException(ResultCode::FILE_UPLOAD_ERROR, 'An error occured in system! Upload failed!');
        }
        
        return $image_name;
    }

    /**
     * Resizing an uploaded image
     * @param string $image_src Source Image
     * @param string $image_name Image name to be saved
     * @param int $maxDimW Image new width 
     * @param int $maxDimH Image new height 
     * @return string $randomString 
     */
    protected function resize_image($image_src, $image_name, $maxDimW, $maxDimH, $type) {
        $destinationImage = Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE . $image_name;
        copy($image_src, $destinationImage);

        $target_filename = '';
        list($width, $height, $type, $attr) = getimagesize($destinationImage);
        if ($width > $maxDimW || $height > $maxDimH) {
            $target_filename = $destinationImage;
            $size = getimagesize($destinationImage);
            $ratio = $size[0] / $size[1]; // width/height
            if ($ratio > 1) {
                $width = $maxDimW;
                $height = $maxDimH / $ratio;
            } else {
                $width = $maxDimW * $ratio;
                $height = $maxDimH;
            }
            $src = imagecreatefromstring(file_get_contents($destinationImage));
            $dst = imagecreatetruecolor($width, $height);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);

            imagejpeg($dst, $target_filename); // adjust format as needed
        }

        if ($target_filename == '') {
            return FALSE;
        }

        return TRUE;
    }

}