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
class Example_UserLogOut extends BaseClass
{

    /**
     * Login required or not.
     */
    const LOGIN_REQUIRED = true;

    /**
     * Processing API script execution.
     */
    public function action()
    {
        $userInfo = UserModel::cacheOrFind($this->userId, $this->pdo);
        if (empty($userInfo)) {
            throw new AppException(ResultCode::INVALID_REQUEST_TOKEN, 'Token has invalid user ID');
        }

        // Delete user data from cache
        UserModel::removeUserSession($this->userId);

        return [
          'result_code' => ResultCode::SUCCESS,
          'time' => DateUtil::getToday(),
          'data' => [
            'msg' => 'User is logged out',
          ],
          'error' => []
        ];
    }
}
