<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Abstract Cache Model Class
 *
 * @author sabbir-hossain
 */

namespace Akaash\Core\Model;

use Akaash\System\Cache\Service;

abstract class Cache implements Service
{

    /**
     * Get cached data if exist
     *
     * @param Service $cache
     * @param string $key
     * @return mixed
     */
    public static function getCache(Service $cache, $key)
    {
        return $cache->get($key);
    }

    /**
     * Save data in cache
     *
     * @param Service $cache
     * @param string $key
     * @param mixed $value
     * @param int $flag
     * @param int $expire
     * @return mixed
     */
    public static function setCache(Service $cache, $key, $value, $flag = 0, $expire = null)
    {
        return $cache->put($key, $value, $flag, is_null($expire) ? self::DEFAULT_EXPIRATION : $expire);
    }

    /**
     * Save the cache data.
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public static function addCache(Service $cache, $key, $value, $expire = null, $flag = 0)
    {
        return $cache->set($key, $value, $flag, is_null($expire) ? self::DEFAULT_EXPIRATION : $expire);
    }

    /**
     * Delete data from cache
     *
     * @param Service $cache
     * @param string $key
     * @return bool
     */
    public static function deleteCache(Service $cache, $key)
    {
        return $cache->delete($key);
    }

    /**
     * Flush all cache data
     *
     * @param Service $cache
     * @return bool
     */
    public static function clearCache(Service $cache)
    {
        return $cache->flush();
    }
}
