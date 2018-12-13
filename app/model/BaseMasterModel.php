<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Base class of the master model
 */
abstract class Model_BaseMasterModel extends Model_BaseModel {

    /**
     * Memcached Validity period
     */
    const MEMCACHED_EXPIRE = 1800; // 30 minutes

    /**
     * To get all the data from Memcache.
     * If it's not registered to Memcache, it is set to Memcache to retrieve from the database.
     *
     * @return Array of model objects.
     */
    public static function getAll() {
        $key = static::getAllKey();
        // To connect to Memcached, to get the value.
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $value = $memcache->get($key);
        if (FALSE === $value) {
            // If the value has been set to Memcached, it is set to Memcached to retrieve from the database.
            $value = self::findAllBy(array());
            if ($value) {
                $memcache->set($key, $value, 0, static::MEMCACHED_EXPIRE);
            }
        }
        return $value;
    }

}
