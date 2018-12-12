<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

class Common_Util_KeyValueStoreUtil {

    /**
     * MEMCACHE CLIENT
     *
     * @var unknown
     */
    private static $_memcachedClient = null;

    /**
     * Mem Cache Client for User Session
     *
     * @var unknown
     */
    private static $_userSessionMemcachedClient = null;

    /**
     * Memory cache client for user session resolve
     *
     * @var unknown
     */
    private static $_userSessionResolveMemcachedClient = null;

    /**
     * Get memcached client
     *
     * @param string $host
     * @param string $port
     * @return KvsLocalFileClient MemcachedClient
     */
    public static function getMemcachedClient($host = null, $port = null) {

        // Check is cache server already initiated
        if (null !== self::$_memcachedClient) {
            return self::$_memcachedClient;
        }

        // Get instance of config class
        $config = Common_Util_ConfigUtil::getInstance();

        if (true == $config->isLocalKvs()) {

            $localFileClient = new Common_Kvs_KvsLocalFileClient(Common_Util_ConfigUtil::getInstance()->getLocalKvsPath());

            self::$_memcachedClient = $localFileClient;
            return $localFileClient;
        }

        $memCachedClient = new Common_Kvs_MemcachedClient();

        $memcachedServerDto = $config->getMemcachedServerDto();
        $memCachedClient->addServer($memcachedServerDto->host, $memcachedServerDto->port);

        self::$_memcachedClient = $memCachedClient;

        return $memCachedClient;
    }

    /**
     * Get memcached client.
     *
     * @param string $host
     * @param string $port
     * @return KvsLocalFileClient MemcachedClient
     */
    public static function getUserSessionMemcachedClient($host = null, $port = null) {

        // Check is cache server already initiated
        if (null !== self::$_userSessionMemcachedClient) {
            return self::$_userSessionMemcachedClient;
        }

        // Get instance of config class
        $config = Common_Util_ConfigUtil::getInstance();
        
        if (true == $config->isLocalKvs()) {

            $localFileClient = new Common_Kvs_KvsLocalFileClient(Common_Util_ConfigUtil::getInstance()->getLocalKvsPath());

            self::$_userSessionMemcachedClient = $localFileClient;
            return $localFileClient;
        }

        $memCachedClient = new Common_Kvs_MemcachedClient();

        $memcachedServerDto = $config->getMemcachedServerDto();
        $memCachedClient->addServer($memcachedServerDto->host, $memcachedServerDto->port);

        self::$_userSessionMemcachedClient = $memCachedClient;

        return $memCachedClient;
    }

    /**
     * Get memcached client.
     *
     * @param string $host
     * @param string $port
     * @return KvsLocalFileClient MemcachedClient
     */
    public static function getUserSessionResolveMemcachedClient($host = null, $port = null) {

        // Check is cache server already initiated
        if (null !== self::$_userSessionResolveMemcachedClient) {
            return self::$_userSessionResolveMemcachedClient;
        }

        // Get instance of config class
        $config = Common_Util_ConfigUtil::getInstance();

        if (true == $config->isLocalKvs()) {

            $localFileClient = new Common_Kvs_KvsLocalFileClient(Common_Util_ConfigUtil::getInstance()->getLocalKvsPath());

            self::$_userSessionResolveMemcachedClient = $localFileClient;
            return $localFileClient;
        }

        $memCachedClient = new Common_Kvs_MemcachedClient();

        $memcachedServerDtoList = $config->getUserSessionResolveMemcachedServerDtoList();
        foreach ($memcachedServerDtoList as $memcachedServerDto) {
            $memCachedClient->addServer($memcachedServerDto->host, $memcachedServerDto->port);
        }

        self::$_userSessionResolveMemcachedClient = $memCachedClient;

        return $memCachedClient;
    }

}
