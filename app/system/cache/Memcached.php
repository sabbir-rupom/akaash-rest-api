<?php

declare(strict_types=1);

namespace System\Cache;

use System\Cache\Service;

class Memcached implements Service
{
    protected $host;
    protected $port;
    private $connection;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;

        $this->connection = new \Memcache;

        $this->addServer();
    }

    /**
     * Add memcache server connection
     */
    public function addServer()
    {
        $this->connection->addServer($this->host, $this->port);
    }

    /**
     * Add new key/value in server
     */
    public function new(string $key, $value, int $flag = 0, int $expire = 0)
    {
        return $this->connection->add($key, $value, $flag, $expire);
    }

    /**
     * Increment a cache value
     */
    public function increment(string $key, int $value)
    {
        return $this->connection->increment($key, $value);
    }

    /**
     * Add value to cache with an unique key
     *
     * @param type $value
     * @param bool $flag
     * @param int $expire
     * @return string
     */
    public function add($value, int $flag = 0, int $expire = 0): string
    {
        $key = uniqid(rand(1, 99999), true);
        $this->connection->set($key, $value, $flag, $expire);
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
        return $this->connection->get($key, $flag);
    }

    /**
     * Put / Store value with provided key in cache
     *
     * @param string $key
     * @param type $value
     * @param bool $flag
     * @param int $expire
     * @return bool
     */
    public function put(string $key, $value, int $flag = 0, int $expire = 0): bool
    {
        return $this->connection->replace($key, $value, $flag, $expire);
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
    public function set(string $key, $value, int $flag = 0, int $expire = 0): bool
    {
        return $this->connection->set($key, $value, $flag, $expire);
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
     * Delete a key/value pair in cache
     *
     * @param string $key
     * @return bool
     */
    public function delete(string $key): bool
    {
        return $this->connection->delete($key);
    }

    /**
     * Clear / Flush all data in cache
     *
     * @return bool
     */
    public function flush(): bool
    {
        return $this->connection->flush();
    }
}
