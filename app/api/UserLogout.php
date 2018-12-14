<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Description of UserLogin
 *
 * @author sabbir-hossain
 */
class UserLogout extends BaseClass {

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
        Model_User::deleteCache(Model_CacheKey::getUserKey($this->cache_user->id));

        /*
         * Remove session data from cache
         */
        $this->cache_user->removeSessionFromUserId($this->cache_user->id);

        /*
         * Encode session data for client
         */
        
        unset($this->cache_user);

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_Util_DateUtil::getToday(),
            'data' => [
                'msg' => 'User is logged out'
            ],
            'error' => []
        );
    }

}
