<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class User_Info extends BaseClass {

    /**
     * Login required or not
     */
    const LOGIN_REQUIRED = TRUE;

    private $targetUserId;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();

        /*
         *  If a user ID is specified through GET / Query string
         */
        $this->targetUserId = $this->getValueFromQuery('user_id', 'int');
    }

    /**
     * Process API request
     */
    public function action() {
        if (empty($this->targetUserId) || $this->targetUserId == $this->userId) {
            /*
             *  If user ID not specified, get the session user information
             */
            $user = $this->cacheUser;
        } else {
            $user = Model_User::cache_or_find($this->targetUserId, $this->pdo);
        }

        if (empty($user)) {
            throw new System_Exception(ResultCode::USER_NOT_FOUND, 'User not found');
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => array(
                'user_info' => $user->toJsonHash()
            ),
            'error' => []
        );
    }

}
