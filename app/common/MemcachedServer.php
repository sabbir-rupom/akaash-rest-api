<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

class Common_MemcachedServer {

    public $host;
    public $port;

    private $connection;
    
    function __construct() {
        $this->connection = new Memcache();
    }
    
    public function _getRawConnection() {
        return $this->connection;
    }

    public function addServer() {
        $this->connection->addServer($this->host, $this->port);
    }

    public function get($key) {
        $this->_checkKey($key);

        return $this->connection->get($key);
    }

    public function put($key, $value, $limit = 0) {
        $this->_checkKey($key);

        $this->connection->set($key, $value, false, $limit);
    }

    public function set($key, $value, $flag, $limit = 0) {
        // For the third argument, it is best to specify what to specify
        $this->connection->set($key, $value, $flag, $limit);
    }

    public function delete($key, $timeout = 0) {
        $this->connection->delete($key, $timeout);
    }

    public function remove($key) {
        $this->_checkKey($key);

        $this->connection->delete($key, 0);
    }


    public function add($value, $limit = 0) {
        $key = uniqid(rand(), true);

        $this->connection->set($key, $value, false, $limit);

        return $key;
    }

    public function flush($delay = 0) {
        return $this->connection->flush();
    }


    public function getPrefixKeyArray($prefix, $limit) {
        
    }


    private function _checkKey($key) {


    }
}
