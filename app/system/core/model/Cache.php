<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Abstract Cache Model Class
 *
 * @author sabbir-hossain
 */

namespace System\Core\Model;

use System\Cache\Service;

abstract class Cache {

    /**
     * Get the cache data.
     * @param unknown_type $key
     */
    public static function getCache(Service $cache, $key ) {
        return $cache->get($key);
    }

    /**
     * Save the cache data.
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public static function setCache(Service $cache, $key, $value, $flag = 0, $expire = 0) {

        return $cache->put($key, $value, $flag, $expire);
    }

    /**
     * Save the cache data.
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public static function addCache(Service $cache, $key, $value, $expire = 0, $flag = 0) {

        return $cache->set($key, $value, $flag, $expire);
    }

    /**
     * Delete the cache.
     * @param unknown_type $key
     */
    public static function deleteCache(Service $cache, $key) {
        return $cache->delete($key);
    }


    /**
     * Delete the cache.
     * @param unknown_type $key
     */
    public static function clearCache(Service $cache) {
        return $cache->flush();
    }

}
