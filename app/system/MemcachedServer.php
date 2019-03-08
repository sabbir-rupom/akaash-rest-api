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

(defined('APP_NAME')) OR exit('Forbidden 403');

class System_MemcachedServer {

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
        return $this->connection->get($key);
    }

    public function put($key, $value, $limit = 0) {
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

}
