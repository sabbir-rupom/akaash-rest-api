<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Message\ResultCode;
use Library\JwtToken;
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

            // Prepare session data for client
            $sessionId = UserModel::cacheUserSession($user);
            $encodeUserSession = base64_encode(serialize(array(
              'session_id' => $sessionId,
              'user_id' => $user->user_id,
            )));

            $this->pdo->commit();
        } catch (PDOException $e) {
            $this->pdo->rollback();

            throw $e;
        }

        $authAccessToken = JwtToken::createToken(
                ['session' => $encodeUserSession],
                $this->config->getRequestTokenSecret()
            )['data'];
        header($this->config->getRequestTokenHeaderKey() . ": " . $authAccessToken);

        return [
          'result_code' => ResultCode::SUCCESS,
          'time' => DateUtil::getToday(),
          'data' => array(
            'user_info' => $user->toJsonHash(),
          ),
          'error' => []
        ];
    }
}
