<?php

/**
 * A RESTful API template in PHP based on flight micro-framework.
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php ( MIT License )
 *
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * 　Cache key generation Model class.
 */
class Model_CacheKey {
    /**
     * Session storage cache key acquisition.
     *
     * @param int $userId
     *
     * @return Cache key
     */
    public static function getUserSessionKey($userId) {
        return Config_Config::getInstance()->getMemcachePrefix().'user_ses_'.$userId;
    }

    /**
     * Session storage cache key acquisition.
     *
     * @param string $sessionId
     * @param mixed  $userId
     *
     * @return Cache key
     */
//    public static function getSessionResolveKey($sessionId) {
//        return Config_Config::getInstance()->getMemcachePrefix() . 'user_ses_resolv_' . $sessionId;
//    }

    /**
     * User ID storage cache key acquisition.
     *
     * @param int $userId
     *
     * @return Cache key
     */
    public static function getUserKey($userId) {
        return Config_Config::getInstance()->getMemcachePrefix().'users_'.$userId;
    }
}
