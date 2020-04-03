<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use Akaash\System\Exception\AppException;
use Akaash\System\Message\ResultCode;
use Model\User as UserModel;
use Akaash\Helper\CommonUtil;
use Akaash\Helper\DateUtil;

/**
 * Sample API Example: Get User Information
 */
class Example_UserGetInformation extends BaseClass
{

    /**
     * Login required or not.
     */
    const LOGIN_REQUIRED = true;

    private $_userId;

    /**
     * Validation of request.
     */
    public function validate()
    {
        parent::validate();

        // If a user ID is specified through GET / Query string
        if (!empty($this->value) && CommonUtil::isInt($this->value)) {
            $this->_userId = $this->value;
        } else {
            $this->_userId = $this->getInputPost('user_id', 'int');
        }

        $this->_userId = empty($this->_userId) ? $this->userId : $this->_userId; // Default session user ID
    }

    /**
     * Processing API script execution.
     */
    public function action()
    {
        $userInfo = UserModel::cacheOrFind($this->_userId, $this->pdo);
        if (empty($userInfo)) {
            throw new AppException(ResultCode::USER_NOT_FOUND);
        }

        return [
          'result_code' => ResultCode::SUCCESS,
          'time' => DateUtil::getToday(),
          'data' => [
            'user_info' => $userInfo->toJsonHash(),
          ],
          'error' => []
        ];
    }
}
