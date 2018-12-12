<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

(defined('MEMCACHE_COMPRESSED')) OR define('MEMCACHE_COMPRESSED', 1);

/**
 * Local cache file initiate class
 */
class Common_Kvs_KvsLocalFileClient {

    private $filePath;

    /**
     * Generate a connection to the local cache file
     */
    function __construct($filePath) {
        $this->filePath = $filePath;
    }

    /**
     * Retrieve the value with the specified key
     *
     * @param String key
     */
    public function get($key) {
        // File existence check
        if (!file_exists($this->filePath)) {
            return false;
        }

        $content = file_get_contents($this->filePath);
        $array = unserialize($content);

        if (false === $array) {
            return false;
        }

        // Check for existence of key after deserialization
        if (array_key_exists($key, $array)) {
            return unserialize($array[$key]);
        }

        return false;
    }

    /**
     * Save the value specified by the specified key.
     *
     * @param String key
     * @param String value
     */
    public function set($key, $value, $flag = null, $expire = null) {
        // File existence check
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            $array = unserialize($content);
        }

        $array[$key] = serialize($value);

        file_put_contents($this->filePath, serialize($array));
    }

    /**
     * Delete the value with the specified key
     *
     * @param String key
     */
    public function delete($key) {
        $array = array();
        // File existence check
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            $array = unserialize($content);
        }

        // Check for existence of key after deserialization
        if(is_array($array)):
        if (array_key_exists($key, $array)) {
            unset($array[$key]);
        }
        endif;

        file_put_contents($this->filePath, serialize($array));
    }

    public function remove($key) {
        $this->delete($key);
    }

    /**
     * Automatically generate a unique key and save the value.
     *
     * @param mixed value
     * @return String key
     */
    public function add($value, $limit = null) {

        $key = uniqid(rand(), true);

        // File existence check
        if (file_exists($this->filePath)) {
            $content = file_get_contents($this->filePath);
            $array = unserialize($content);
        }

        $array[$key] = serialize($value);

        file_put_contents($this->filePath, serialize($array));

        return $key;
    }

    /**
     * Return key array matching with forward match
     *
     * @param string prefix
     * @param string limit
     */
    public function getPrefixKeyArray($prefix, $limit) {
        
    }

    /**
     * Delete local cache file
     */
    public function vanish() {
        if (!file_exists($this->filePath)) {
            return null;
        }
        unlink($this->filePath);
    }

    /**
     * Initialize (Flush) Memcache.
     */
    public static function flushMemcache() {
        return true;
    }

    /**
     * Invalidate all items in the cache.
     *
     * @param int $delay
     */
    public function flush($delay = 0) {
        return $this->vanish();
    }

}
