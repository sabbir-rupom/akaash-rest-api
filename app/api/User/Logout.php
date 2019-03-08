<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Description of UserLogin
 *
 * @author sabbir-hossain
 */
class User_Logout extends BaseClass {

    // Login Required.
    const LOGIN_REQUIRED = TRUE;

    /**
     * Validating Login Request
     */
    public function validate() {
        parent::validate();
    }

    /**
     * Processing API script execution
     */
    public function action() {
        /*
         * Delete user data from cache
         */
        Model_User::deleteCache(Model_CacheKey::getUserKey($this->cacheUser->id));

        /*
         * Remove session data from cache
         */
        $this->cacheUser->removeSessionFromUserId($this->cacheUser->id);

        /*
         * Encode session data for client
         */
        
        unset($this->cacheUser);

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => [
                'msg' => 'User is logged out'
            ],
            'error' => []
        );
    }

}
