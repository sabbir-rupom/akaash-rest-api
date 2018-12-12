<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * KVS cache connect to Base class
 */
class Common_Kvs_MemcachedClient implements Common_Kvs_ClientInterface {

    /**
     * Connection of memcached
     */
    private $connection;

    /**
     * Initiate Memcache instance
     */
    function __construct() {
        $this->connection = new Memcache();
    }

    /**
     * For obtaining the raw Memcache instance to which common config is applied.
     *
     * Normally it should not be used.
     */
    public function _getRawConnection() {
        return $this->connection;
    }

    /**
     * Set the connection information in the instance
     *
     * @param String $host
     * @param String $port
     */
    public function addServer($host, $port) {
        $this->connection->addServer($host, $port);
    }

    /**
     * Retrieve the value with the specified key
     *
     * @param String key
     */
    public function get($key) {

        return $this->connection->get($key);
    }

    /**
     * Save the value specified by the specified key.
     *
     * @param String key
     * @param String value
     */
    public function put($key, $value, $limit = 0) {

        // For the third argument, it is best to specify what to specify
        $this->connection->set($key, $value, false, $limit);
    }

    /**
     * Save the value specified by the specifie。
     *
     * @param String key
     * @param String value
     */
    public function set($key, $value, $flag, $limit = 0) {
        // For the third argument, it is best to specify what to specify
        $this->connection->set($key, $value, $flag, $limit);
    }

    /**
     * Delete the value with the specified key
     *
     * @param String key
     */
    public function delete($key, $timeout = 0) {
        $this->connection->delete($key, $timeout);
    }

    /**
     * Delete the value with the specified key
     *
     * @param String key
     */
    public function remove($key) {

        $this->connection->delete($key, 0);
    }

    /**
     * Automatically generate a unique key and save the value.
     *
     * @param mixed value
     * @return String key
     */
    public function add($value, $limit = 0) {
        $key = uniqid(rand(), true);

        $this->connection->set($key, $value, false, $limit);

        return $key;
    }

    /**
     * Invalidate all items in the cache.
     *
     * @param int $delay
     */
    public function flush($delay = 0) {
        return $this->connection->flush();
    }

    /**
     * Returns a key array matching with a forward match.
     *
     * @param string prefix
     * @param string limit
     */
    public function getPrefixKeyArray($prefix, $limit) {
        
    }

}
