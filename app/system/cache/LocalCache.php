<?php

namespace System\Cache;

use System\Cache\Service;
use Wruczek\PhpFileCache\PhpFileCache;

class LocalCache implements Service
{
    private $connection;

    public function __construct($cachePath)
    {
        $this->connection = new PhpFileCache($cachePath);
    }

    /**
     * Add value to cache with an unique key
     *
     * @param type $value
     * @param bool $flag
     * @param int $expire
     * @return string
     */
    public function add($value, int $flag = 0, $expire = null): string
    {
        $key = uniqid(rand(1, 99999), true);
        if (empty($expire)) {
            $expire = self::DEFAULT_EXPIRATION;
        }
        $this->connection->store($key, $value, $expire, boolval($flag));
        return $key;
    }

    /**
     * Fetch cached value if exist
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key, int $flag = 0)
    {
        if ($this->connection->isCached($key)) {
            return $this->connection->retrieve($key, boolval($flag));
        }
        return null;
    }

    /**
     * Put value at expired key/value in cache
     *
     * @param string $key
     * @param type $value
     * @param bool $flag
     * @param int $expire
     * @return bool
     */
    public function put(string $key, $value, int $flag = 0, $expire = null): bool
    {
        if ($this->connection->isExpired($key)) {
            if (empty($expire)) {
                $expire = self::DEFAULT_EXPIRATION;
            }
            $this->set($key, $value, $expire, boolval($flag));
        }


        return true;
    }

    /**
     * Store value with provided key in cache
     *
     * @param string $key
     * @param type $value
     * @param bool $flag
     * @param int $expire
     * @return bool
     */
    public function set(string $key, $value, int $flag = 0, $expire = null): bool
    {
        if (empty($expire)) {
            $expire = self::DEFAULT_EXPIRATION;
        }
        $this->connection->store($key, $value, $expire, boolval($flag));
        return true;
    }

    /**
     * Delete a key/value pair in cache
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->connection->eraseKey($key);
    }

    /**
     * Remove / Delete a key/value pair in cache
     *
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        return $this->delete($key);
    }

    /**
     * Clear / Flush all data in cache
     *
     * @return bool
     */
    public function flush(): bool
    {
        $this->connection->clearCache();
        return true;
    }
}
