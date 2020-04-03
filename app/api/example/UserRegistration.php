<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use Akaash\System\Message\ResultCode;
use Model\User as UserModel;
use Akaash\Helper\DateUtil;
use Akaash\System\Exception\AppException;

/**
 * Sample API Example: User Registration
 */
class Example_UserRegistration extends BaseClass
{
    public $user;
    public $email;

    /**
     * Validate Registration Request.
     */
    public function validate()
    {
        parent::validate();

        $this->email = $this->getInputPost('email', 'string', true);
        if (false === filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, 'Email is invalid');
        }

        $this->user = UserModel::findBy(['email' => $this->email], $this->pdo);

        if (!empty($this->user)) {
            throw new AppException(ResultCode::DATA_ALREADY_EXISTS, 'User already exist');
        } else {
            $this->user = new UserModel();
        }

        $this->user->email = $this->email;
        $this->user->password = password_hash(
            trim($this->getInputPost('password', 'string', true, true)),
            PASSWORD_BCRYPT,
            array('cost' => 10)
        );

        $this->user->gender = $this->getInputPost('gender', 'string', true);
        $this->user->gender = ($this->user->gender === 'male' ? 'male' : 'female');

        $this->user->first_name = $this->getInputPost('first_name', 'string', true, true);
        $this->user->last_name = $this->getInputPost('last_name', 'string', true, true);
        $this->user->device_token = $this->getInputPost('device_token', 'string', false, true);
        $this->user->device_model = $this->getInputPost('device_model', 'string', false, true);
        $this->user->created_at = DateUtil::getToday();
    }

    /**
     * Processing API script execution.
     */
    public function action()
    {
        UserModel::startTransaction($this->pdo);

        try {
            $this->user->user_id = $this->user->create($this->pdo);
            UserModel::commit($this->pdo);

            if (empty($this->user->user_id)) {
                throw new AppException(ResultCode::DATABASE_ERROR, 'User data insertion failed');
            }
        } catch (PDOException $e) {
            $this->pdo->rollback();

            throw $e;
        }

        return [
          'result_code' => ResultCode::SUCCESS,
          'time' => DateUtil::getToday(),
          'data' => array(
            'user_info' => $this->user->toJsonHash(),
          ),
          'error' => []
        ];
    }
}
