<?php

/**
 * A RESTful API template in PHP based on flight micro-framework.
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php ( MIT License )
 *
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * System library class MemcachedServer.
 */
class System_MemcachedServer {
    public $host;
    public $port;

    public static $memcachedServerInstance = null;

    private $connection;

    public function __construct() {
        $this->connection = new Memcache();
    }

    public function getConnection() {
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
