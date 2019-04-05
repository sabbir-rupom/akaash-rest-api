<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Description of UserSignUp API class.
 *
 * @author sabbir-hossain
 */
class UserSignUp extends BaseClass {
    // Login Required.
    const LOGIN_REQUIRED = false;

    private $_user_email;
    private $_user_password;
    private $_user_name;

    /**
     * Validating Login Request.
     */
    public function validate() {
        parent::validate();

        $this->_user_email = $this->getValueFromJSON('email', 'string', true);
        $this->_user_password = $this->getValueFromJSON('password', 'string', true);
        $this->_user_name = $this->getValueFromJSON('user_name', 'string', true);

        if (false === filter_var($this->_user_email, FILTER_VALIDATE_EMAIL)) {
            throw new System_ApiException(ResultCode::INVALID_REQUEST_PARAMETER, 'Email is invalid.');
        }
        if (Model_User::countBy(array('email' => $this->_user_email), $this->pdo) > 0) {
            throw new System_ApiException(ResultCode::DATA_ALREADY_EXISTS, 'Another user is registered with this email!');
        }
    }

    /**
     * Processing API script execution.
     */
    public function action() {
        $this->pdo->beginTransaction();

        try {
            $user = new Model_User();

            // Add only if the value set in requested parameters

            if (property_exists($this->json, 'first_name')) {
                $user->first_name = $this->getValueFromJSON('first_name', 'string');
            }
            if (property_exists($this->json, 'last_name')) {
                $user->last_name = $this->getValueFromJSON('last_name', 'string');
            }
            if (property_exists($this->json, 'gender')) {
                $user->gender = $this->getValueFromJSON('gender', 'string');
            }
//            if (property_exists($this->json, 'device_token')) {
//                $user->device_token = $this->getValueFromJSON('device_token', 'string');
//            }
//            if (property_exists($this->json, 'device_model')) {
//                    $user->device_model = $this->getValueFromJSON('device_model', 'string');
//            }

            if (property_exists($this->json, 'longitude')) {
                $user->longitude = $this->getValueFromJSON('longitude', 'string', true);
            }
            if (property_exists($this->json, 'latitude')) {
                $user->latitude = $this->getValueFromJSON('latitude', 'string', true);
            }

            if (property_exists($this->json, 'profile_image')) {
                // If image data is provided in base64 string
                $profile_image = $this->getValueFromJSON('profile_image', 'string'); // nullable
                if (!empty($profile_image)) {
                    if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $profile_image)) { // checking if data is in base64 formatted or not
                        $user->profile_image = $this->process_binary_image('new', $profile_image, 'profile');
                    }
                }
            }

            $user->password = password_hash(trim($this->_user_password), PASSWORD_BCRYPT, array('cost' => 10));
            $user->user_name = $this->_user_name;
            $user->email = $this->_user_email;

            $user->create($this->pdo);

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollback();

            throw $e;
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => array(
                'user_info' => $user->toJsonHash(),
            ),
            'error' => array(),
        );
    }
}
