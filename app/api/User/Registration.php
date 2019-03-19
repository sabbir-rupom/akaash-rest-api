<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Description of UserSignUp API class
 *
 * @author sabbir-hossain
 */
class User_Registration extends BaseClass {

    // Login Required.
    const LOGIN_REQUIRED = FALSE;

    private $_user_email = null;
    private $_user_password = null;
    private $_user_name = null;

    /**
     * Validating Requested values for Registration
     */
    public function validate() {
        parent::validate();

        $this->_user_email = $this->getValueFromJSON('email', 'string', TRUE);
        $this->_user_password = $this->getValueFromJSON('password', 'string', TRUE);
        $this->_user_name = $this->getValueFromJSON('user_name', 'string', TRUE);

        if (filter_var($this->_user_email, FILTER_VALIDATE_EMAIL) === false) { // Verify email address
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, 'Email is invalid.');
        } 
        if(Model_User::countBy(array('email' => $this->_user_email), $this->pdo) > 0) { // Check for duplicate email address
            throw new AppException(ResultCode::DATA_ALREADY_EXISTS, 'Another user is registered with this email!');
        }
    }

    /**
     * Processing API script execution
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
            if (property_exists($this->json, 'longitude')) {
                $user->longitude = $this->getValueFromJSON('longitude', 'string', TRUE);
            }
            if (property_exists($this->json, 'latitude')) {
                $user->latitude = $this->getValueFromJSON('latitude', 'string', TRUE);
            }
            
            if (property_exists($this->json, 'profile_image')) {
                /*
                 * If image data is provided in base64 string
                 */
                $profile_image = $this->getValueFromJSON('profile_image', 'string'); // nullable
                if (!empty($profile_image)) {
                    if (preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $profile_image)) { // checking if data is in base64 formatted or not
                        $user->profile_image = FileUpload::processBinaryImage('new', $profile_image, 'profile');
                    }
                }
            }
            
            $user->password = password_hash(trim($this->_user_password), PASSWORD_BCRYPT, array('cost' => 10));
            $user->user_name = $this->_user_name;
            $user->email = $this->_user_email;
            
            $user->create($this->pdo);

            $this->pdo->commit();
        } catch (PDOAppException $e) {
            $this->pdo->rollback();
            throw $e;
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => array(
                'user_info' => $user->toJsonHash(),
            ),
            'error' => []
        );
    }

}
