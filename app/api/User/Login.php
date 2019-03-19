<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Description of UserLogin
 *
 * @author sabbir-hossain
 */
class User_Login extends BaseClass {

    // Login Required.
    const LOGIN_REQUIRED = FALSE;

    private $_user_email = null;
    private $_user_password = null;

    /**
     * Validating Login Request
     */
    public function validate() {
        parent::validate();

        $this->_user_email = $this->getValueFromJSON('email', 'string', TRUE);
        $this->_user_password = $this->getValueFromJSON('password', 'string', TRUE);


        if (empty($this->_user_password)) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, 'Password is empty');
        }

        if (filter_var($this->_user_email, FILTER_VALIDATE_EMAIL) === false) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, 'Email is invalid.');
        }
    }

    /**
     * Processing API script execution
     */
    public function action() {
        $this->pdo->beginTransaction();

        try {
            /*
             * Search user from DB table
             */
            $user = Model_User::findBy(array('email' => $this->_user_email), $this->pdo, TRUE);
            if (null === $user || empty($user)) {
                throw new AppException(ResultCode::USER_NOT_FOUND);
            }
            if (password_verify($this->_user_password, $user->password) === FALSE) {
                throw new AppException(ResultCode::PASSWORD_MISMATCHED);
            }

            /*
             *  Delete old session data from cache
             */
            $user->removeSessionFromUserId($user->id);
            /*
             *  Save DB session data in cache
             */

            $sessionId = $user->setSession();

            // Device token and model update : update only if the value set in the parameters and user information is different

            if (property_exists($this->json, 'device_token')) {
                $deviceToken = $this->getValueFromJSON('device_token', 'string');
                if (!empty($deviceToken) && $deviceToken != $user->device_token) {
                    $user->device_token = $deviceToken;
                }
            }
            if (property_exists($this->json, 'device_model')) {
                $deviceModel = $this->getValueFromJSON('device_model', 'string');
                if (!empty($deviceModel) && $deviceModel != $user->device_model) {
                    $user->device_model = $deviceModel;
                }
            }

            if (property_exists($this->json, 'longitude')) {
                $user->longitude = $this->getValueFromJSON('longitude', 'string', TRUE);
            }
            if (property_exists($this->json, 'latitude')) {
                $user->latitude = $this->getValueFromJSON('latitude', 'string', TRUE);
            }

            $user->update($this->pdo);

            /*
             * Update last login in same day
             */
            Model_UserLoginSession::updateSession($user->id, $sessionId, 1, $this->pdo);

            /*
             * Update User data in cache
             */

            Model_User::setCache(Model_CacheKey::getUserKey($user->id), $user);

            $this->pdo->commit();
        } catch (PDOAppException $e) {
            $this->pdo->rollback();
            throw $e;
        }

        /*
         * Encode session data for client
         */
        $encodeUserSession = base64_encode(serialize(array(
            'session_id' => $sessionId,
            'user_id' => $user->id
        )));

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => array(
                'session_id' => $encodeUserSession,
                'user_info' => $user->toJsonHash(),
            ),
            'error' => []
        );
    }

}
