<?php

require_once "PhpFileCache.php";

class Common_FileCacheClient {

    private $connection;

    const CACHE_TIMEOUT = 3600; // in seconds

    function __construct($cachePath) {
        $this->connection = new PhpFileCache($cachePath);
    }

    public function addConnection() {
    }

    public function get($key) {

        if ($this->connection->isCached($key)) {
            return $this->connection->retrieve($key);
        }
        return NULL;
    }

    public function put($key, $value, $timeout = 0) {
        if ($this->connection->isExpired($key)) {
            if ($timeout == 0) {
                $timeout = self::CACHE_TIMEOUT;
            }
            $this->connection->store($key, $value, $timeout);
        }
    }

    public function set($key, $value, $timeout = 0) {
        if ($timeout == 0) {
            $timeout = self::CACHE_TIMEOUT;
        }
        $this->connection->store($key, $value, $timeout);
    }

    public function delete($key) {
        $this->connection->eraseKey($key);
    }
    
    public function remove($key) {
        $this->connection->eraseKey($key);
    }
    
    public function flush() {
        $this->connection->clearCache();
    }

}
