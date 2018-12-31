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
        return Config_Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
    }

    /**
     * Session storage cache key acquisition
     *
     * @param string $sessionId
     * @return Cache key
     */
//    public static function getSessionResolveKey($sessionId) {
//        return Config_Config::getInstance()->getMemcachePrefix() . 'user_ses_resolv_' . $sessionId;
//    }

    /**
     * User ID storage cache key acquisition
     *
     * @param int $userId
     * @return Cache key
     */
    public static function getUserKey($userId) {
        return Config_Config::getInstance()->getMemcachePrefix() . 'users_' . $userId;
    }

}
