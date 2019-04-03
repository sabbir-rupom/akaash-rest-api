<?php

/*
 * RESTful API Template
 *
 * A RESTful API template based on flight-PHP framework
 * This software project is based on my recent REST-API development experiences.
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author	Sabbir Hossain Rupom
 * @since	Version 1.0.0
 * @filesource
 */

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * 　Cache key generation Model class
 *
 * @author sabbir-hossain
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
