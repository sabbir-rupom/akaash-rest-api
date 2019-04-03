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
 * Description of BaseClass.
 *
 * @author sabbir-hossain
 */
class BaseClass {
    /**
     * User Authentication variable defined.
     */
    const LOGIN_REQUIRED = false;

    // Token Verification bypass config
    const TEST_ENV = false;

    protected $headers;
    protected $getParams;
    protected $json;
    protected $sessionId;
    protected $requestToken;
    protected $requestTime;
    protected $userId;
    protected $cache_user;

    /**
     * Client Type, Version.
     */
    protected $client_type;
    protected $client_version;
    protected $actionName;

    /**
     * Database PDO.
     */
    protected $pdo;

    /**
     * Server Configuration Parameters.
     */
    protected $config;

    /**
     * Base Class Constructor.
     *
     * @param array  $headers    All Requested Headers
     * @param array  $getParams  All Requested Query/GET Parameters
     * @param obj    $json       All Requested JSON Parameters
     * @param string $apiName    API Controller Class Name
     * @param mixed  $actionName
     *
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

        // Get DB Connection object
        $this->pdo = Flight::pdo();

        /*
         * Retrive the session ID
         * Retrive request token
         * From HTTP Header
         */

        foreach ($this->headers as $key => $value) {
            $upperKey = strtoupper($key);
            if ($this->config['USER_SESSION_HEADER_KEY'] == $upperKey) {
                $this->sessionId = $value;
            }
            if ($this->config['REQUEST_TOKEN_HEADER_KEY'] == $upperKey) {
                $this->requestToken = $value;
                if (Config_Config::getInstance()->checkRequestTokenFlag()) {
                    break;
                }
            }
        }
    }

    /**
     * Request processing execution.
     */
    public function process() {
        // Check and verify client request call / User session
        $this->_filter();

        if (static::LOGIN_REQUIRED) {
            $this->cache_user = Model_User::cache_or_find($this->userId, $this->pdo);

            if (empty($this->cache_user)) {
                throw new System_ApiException(ResultCode::USER_NOT_FOUND, 'Session user not found');
            }

            $this->cache_user->last_api_time = Common_DateUtil::getToday();
            // Update user's active time without updating cache
            $this->cache_user->update($this->pdo, false);
        }
        // Fetch and check GET query strings
        $this->validate();

        // Execute API Action Controller
        $response = $this->action();

        return $response;
    }

    /**
     * Execute the individual processing of the action.
     *
     * @throws Exception
     *
     * @return array Array to convert to response JSON.
     */
    public function action() {
        throw new Exception('action is not implemented.');
    }

    /**
     * It verifies the value.
     */
    public function validate() {
        // Client Type [1 = Android, 2 = iOS, 0 = Other]
        $this->client_type = $this->getValueFromQuery('client_type', 'int');

        // Applicaion version
        $this->client_version = $this->getValueFromQuery('client_version', 'int');
    }

    /**
     * filter processing.
     *
     * @throws Exception
     */
    protected function _filter() {
        // Check Server Maintenance Status
        $this->_checkMaintenance();

        // Check & Verify Request Token
        $this->_checkRequestToken();
        // Check & Verify User Login
        if (static::LOGIN_REQUIRED) {
            $this->isLoggedIn();

            if (false == $this->userId) {
                throw new System_ApiException(ResultCode::SESSION_ERROR, 'Session error.');
            }
        }
    }

    /**
     * Maintenance check.
     *
     * @throws System_ApiException
     */
    protected function _checkMaintenance() {
        // Maintenance check parameter
        $maintainance_check = false;

        if ($maintainance_check) {
            if ($this->isMaintenance()) {
                throw new System_ApiException(ResultCode::MAINTENANCE, 'maintenance.');
            }
        }
    }

    /**
     * Verify request token
     * [ Check if API request-call has been issued from authenticated source ].
     */
    protected function _checkRequestToken() {
        if (Config_Config::getInstance()->checkRequestTokenFlag() && !static::TEST_ENV) {
            $result = System_JwtToken::verifyToken($this->requestToken, Config_Config::getInstance()->getRequestTokenSecret());

            if ($result['error'] > 0) {
                switch ($result['error']) {
                    case Const_Application::HASH_SIGNATURE_VERIFICATION_FAILED:
                        throw new System_ApiException(ResultCode::INVALID_REQUEST_TOKEN, 'Signature Verification Error');

                        break;
                    case Const_Application::EMPTY_TOKEN:
                        throw new System_ApiException(ResultCode::INVALID_REQUEST_TOKEN, 'Token is empty');

                        break;
                    default:
                        throw new System_ApiException(ResultCode::UNKNOWN_ERROR, 'Unexpected token error has been found');

                        break;
                }
            } else {
                // Retrieve session ID from payload if exist
                if (!empty($result['data']->sessionToken)) {
                    $this->sessionId = $result['data']->sessionToken;
                }
                // Get client API request time
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
     * Return extract value from the JSON.
     *
     * @param $path JSON Path array to the value contained in. Is acceptable string if the highest layer.
     * @param $type Type of the variable. "int", "bool", "string".
     * @param $required Required.
     *
     * @return Value extracted from JSON.
     */
    protected function getValueFromJSON($path, $type, $required = false) {
        if (empty($this->json) && $required) {
            throw new System_ApiException(ResultCode::INVALID_JSON, 'JSON is empty.');
        }
        if (is_string($path)) {
            $path = [
                $path,
            ];
        }
        $pathStr = implode('->', $path);
        $var = $this->json;

        try {
            while (!empty($path)) {
                $pathElement = array_shift($path);
                $var = $var->{$pathElement};
            }
        } catch (Exception $e) {
            $var = null;
        }
        if (true == $required && (is_null($var) || '' === $var)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "${pathStr} is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of ${pathStr} is not valid.");
        }

        return $var;
    }

    /**
     * Return from GET parameters to extract the value.
     *
     * @param string  $name     GET The name of the parameter.
     * @param unknown $type     Type of the variable. "int", "bool", "string".
     * @param bool    $required Required. Extracted value from
     *
     * @return GET parameters.
     */
    protected function getValueFromQuery($name, $type, $required = false) {
        if (isset($this->getParams[$name])) {
            $var = $this->getParams[$name];
            if ('string' != $type && '' === $var) {
                $var = null;
            }
        } else {
            $var = null;
        }
        if (true == $required && is_null($var)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "${name} is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of ${name} is not valid.");
        }

        return $var;
    }

    /**
     * Return parameter value from Post call.
     *
     * @param string  $name      Name of the parameter.
     * @param unknown $type      Type of the variable. "int", "bool", "string".
     * @param bool    $required  Value required
     * @param bool    $xss_clean XSS clean
     *
     * @return parameter value from POST Request.
     */
    protected function getInputPost($name, $type, $required = false, $xss_clean = false) {
        if (isset($_POST[$name])) {
            $var = $_POST[$name];
            if ('string' != $type && '' === $var) {
                $var = null;
            }
        } else {
            $var = null;
        }

        if (true == $required && empty($var)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "${name} is not set.");
        }
        if (!$this->isValidType($var, $type)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of ${name} is not valid.");
        }

        return (true === $xss_clean) ? Common_Security::xss_clean($var) : $var;
    }

    /**
     * Check the type of the value.
     *
     * @param $ Value value to validate.
     * @param $ Type expected to type.
     * The time being "int," string "
     * If it is correct type of @return value TRUE, otherwise FALSE. Value returns TRUE unconditionally if it is NULL.
     * @param mixed $value
     * @param mixed $type
     */
    protected function isValidType($value, $type) {
        $result = false;
        if (is_null($value)) {
            return true;
        }
        switch ($type) {
                case 'int':
                    $result = Common_Utils::isInt($value);

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
                    $result = true;

                    break;
            }

        return $result;
    }

    /**
     * Application of state to check whether maintenance mode.
     *
     * @return bool In the case of maintenance mode, true. Otherwise, false.
     */
    protected function isMaintenance() {
        // In the case of maintenance state
        if (Config_Config::getInstance()->checkMaintenance()) {
            // If it is not in the test user
            if (false == $this->isTestUser()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether or not the test user.
     *
     * @return bool If it has been tested user registration, true. Others are false.
     */
    protected function isTestUser() {
        // If you are already logged in
        if (true == $this->isLoggedIn()) {
            // Check if session user ID matches the tester ID
            return $this->userId == Config_Config::getInstance()->getTestUserID();
        }

        return false;
    }

    /**
     * @param int $str
     *
     * @return bool if string is in json formate
     */
    protected function is_json($str) {
        $json = json_decode($str);

        return $json && $str != $json;
    }

    /**
     * Get Distance between two latlong co-ordinates.
     *
     * @param int    $length String length
     * @param string $type   Type of Random String
     *
     * @return string $randomString
     */
    protected function generate_random_string($length = 4, $type = 'string') {
        $stringRegex = '1234567890ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $intRegex = '1234567890';
        $capNdigitRegex = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';

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
        for ($i = 0; $i < $length; ++$i) {
            $randomString .= $regex[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Process base64 formatted image upload.
     *
     * @param int    $ID               DB insert ID
     * @param string $binary_image     Base64 Encoded String
     * @param bool   $old_image_delete if any existing image needed to be deleted or kept
     * @param mixed  $type
     *
     * @return string $image_name Return uploaded image name
     */
    protected function process_binary_image($ID, $binary_image, $type = '', $old_image_delete = true) {
        $base64_string = 'data:image/png;base64,'.$binary_image;

        $curr_time = time();
        $image_prefix = $type.$ID.'_';
        $image_name = $image_prefix.$curr_time.'.png';
        $mask = $image_prefix.'*.*';

        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH)) {
            // If upload directory not exist, create
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH, 0777, true);
        } elseif ($old_image_delete) {
            // Delete all previous profile image
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH.$mask));
        }

        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE)) {
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE, 0777, true);
        } else {
            // Delete all previous profile (mobile size) image
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE.$mask));
        }

        $output_file = Const_Application::UPLOAD_PROFILE_IMAGE_PATH.$image_name;

        $ifp = fopen($output_file, 'wb');
        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        // Resize uploaded image for mobile view
        $this->resize_image($output_file, $image_name, Const_Application::MOBILE_IMAGE_WIDTH, Const_Application::MOBILE_IMAGE_HEIGHT, $type);

        return $image_name;
    }

    /**
     * Process base64 formatted image upload.
     *
     * @param int    $ID               DB insert ID
     * @param string $binary_image     Base64 Encoded String
     * @param bool   $old_image_delete if any existing image needed to be deleted or kept
     * @param mixed  $image_file
     * @param mixed  $type
     *
     * @return string $image_name Return uploaded image name
     */
    protected function process_image_upload($ID, $image_file, $type = '', $old_image_delete = true) {
        $ext = pathinfo($image_file['name'], PATHINFO_EXTENSION);
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'])) {
            throw new System_ApiException(ResultCode::DATA_NOT_ALLOWED, 'Improper image is provided! Only jpg and png allowed!');
        }

        $curr_time = time();
        $image_prefix = $type.$ID.'_';
        $image_name = $image_prefix.$curr_time.'.png';
        $mask = $image_prefix.'*.*';

        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH)) {
            // If upload directory not exist, create
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH, 0777, true);
        } elseif ($old_image_delete) {
            // Delete all previous profile image
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH.$mask));
        }

        if (!file_exists(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE)) {
            mkdir(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE, 0777, true);
        } else {
            // Delete all previous profile (mobile size) image
            array_map('unlink', glob(Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE.$mask));
        }

        $output_file = Const_Application::UPLOAD_PROFILE_IMAGE_PATH.$image_name;

        if (move_uploaded_file($image_file['tmp_name'], $output_file)) {
            $this->resize_image($output_file, $image_name, Const_Application::MOBILE_IMAGE_WIDTH, Const_Application::MOBILE_IMAGE_HEIGHT, $type);
        } else {
            throw new System_ApiException(ResultCode::FILE_UPLOAD_ERROR, 'An error occured in system! Upload failed!');
        }

        return $image_name;
    }

    /**
     * Resizing an uploaded image.
     *
     * @param string $image_src  Source Image
     * @param string $image_name Image name to be saved
     * @param int    $maxDimW    Image new width
     * @param int    $maxDimH    Image new height
     * @param mixed  $type
     *
     * @return string $randomString
     */
    protected function resize_image($image_src, $image_name, $maxDimW, $maxDimH, $type) {
        $destinationImage = Const_Application::UPLOAD_PROFILE_IMAGE_PATH_MOBILE.$image_name;
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

        if ('' == $target_filename) {
            return false;
        }

        return true;
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
}
