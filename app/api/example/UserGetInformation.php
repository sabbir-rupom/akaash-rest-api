<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Exception\AppException;
use System\Message\ResultCode;
use System\Config;
use View\Output;
use Helper\CommonUtil;

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
            $this->_userId = $this->_userId;
        } else {
            $this->_userId = $this->getInputPost('user_id', 'int', true);
        }
    }

    /**
     * Processing API script execution.
     */
    public function action()
    {
        $user = UserModel::findBy(array('email' => $this->_email), $this->pdo);
        if (empty($user)) {
            throw new AppException(ResultCode::USER_NOT_FOUND);
        }

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
