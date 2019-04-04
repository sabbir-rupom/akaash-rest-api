<?php

/**
 * RESTful API Template
 *
 * A RESTful API template in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	https://github.com/sabbir-rupom/rest-api-PHP-flight/blob/master/LICENSE ( MIT License )
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

use Wruczek\PhpFileCache\PhpFileCache;

/**
 * System library class FileCacheClient
 */
class System_FileCacheClient
{
    const CACHE_TIMEOUT = 3600; // in seconds
    private $connection;

    public function __construct($cachePath)
    {
        $this->connection = new PhpFileCache($cachePath);
    }

    public function addConnection()
    {
    }

    public function get($key)
    {
        if ($this->connection->isCached($key)) {
            return $this->connection->retrieve($key);
        }

        return null;
    }

    public function put($key, $value, $timeout = 0)
    {
        if ($this->connection->isExpired($key)) {
            if (0 == $timeout) {
                $timeout = self::CACHE_TIMEOUT;
            }
            $this->connection->store($key, $value, $timeout);
        }
    }

    public function set($key, $value, $timeout = 0)
    {
        if (0 == $timeout) {
            $timeout = self::CACHE_TIMEOUT;
        }
        $this->connection->store($key, $value, $timeout);
    }

    public function delete($key)
    {
        $this->connection->eraseKey($key);
    }

    public function remove($key)
    {
        $this->connection->eraseKey($key);
    }

    public function flush()
    {
        $this->connection->clearCache();
    }
}
