<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * 　Cache key generation Util class
 */
class Model_CacheKey {

    /**
     * Session storage cache key acquisition
     *
     * @param int $userId
     * @return Cache key
     */
    public static function getUserSessionKey($userId) {
        return Common_Util_ConfigUtil::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
    }

    /**
     * Session storage cache key acquisition
     *
     * @param string $sessionId
     * @return Cache key
     */
//    public static function getSessionResolveKey($sessionId) {
//        return Common_Util_ConfigUtil::getInstance()->getMemcachePrefix() . 'user_ses_resolv_' . $sessionId;
//    }

    /**
     * User ID storage cache key acquisition
     *
     * @param int $userId
     * @return Cache key
     */
    public static function getUserKey($userId) {
        return Common_Util_ConfigUtil::getInstance()->getMemcachePrefix() . 'users_' . $userId;
    }

}
