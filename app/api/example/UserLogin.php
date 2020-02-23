<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Config;
use System\Message\ResultCode;
use System\Core\Model\Cache;
use Model\User as UserModel;
use Helper\DateUtil;

/**
 * Sample API Example: User Login
 */
class Example_UserLogin extends BaseClass
{
    private $_email;
    private $_password;

    /**
     * Validating Login Request.
     */
    public function validate()
    {
        parent::validate();

        $this->_email = $this->getInputPost('email', 'string', true);
        $this->_password = $this->getInputPost('password', 'string', true, true);

        if (empty($this->_password)) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, 'Password is empty');
        }

        if (false === filter_var($this->_email, FILTER_VALIDATE_EMAIL)) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, 'Email is invalid');
        }
    }

    /**
     * Processing API script execution.
     */
    public function action()
    {
        UserModel::startTransaction($this->pdo);

        try {
            $user = UserModel::findBy(array('email' => $this->_email), $this->pdo, true);
            if (empty($user)) {
                throw new AppException(ResultCode::USER_NOT_FOUND);
            }
            if (false === password_verify($this->_password, $user->password)) {
                throw new AppException(ResultCode::DATA_NOT_ALLOWED, 'Password did not matched');
            }

            // Delete previous user session from cache
            UserModel::removeSessionFromUserId($user->user_id);

            // Update device token and model update if provided
            $deviceToken = $this->getInputPost('device_token', 'string');
            if (!empty($deviceToken) && $deviceToken !== $user->device_token) {
                $user->device_token = $deviceToken;
            }
            $deviceModel = $this->getInputPost('device_model', 'string');
            if (!empty($deviceModel) && $deviceModel != $user->device_model) {
                $user->device_model = $deviceModel;
            }

            $user->update($this->pdo);

            $sessionId = UserModel::cacheUserSession($user);



            $this->pdo->commit();

            // Encode session data for client
            $encodeUserSession = base64_encode(serialize(array(
              'session_id' => $sessionId,
              'user_id' => $user->user_id,
            )));
        } catch (PDOException $e) {
            $this->pdo->rollback();

            throw $e;
        }

        

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => DateUtil::getToday(),
            'data' => array(
                'user_info' => $user->toJsonHash(),
            ),
            'error' => []
        );
    }
}
