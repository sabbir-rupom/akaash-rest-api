<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

namespace System;

class Config {

    /**
     * initialize class properties
     */
    private $_config = null;
    private static $_instance = null;

    /**
     * Initialize class constructor
     */
    public function __construct() {
        // Load application config file
        $this->_config = \Flight::app()->get('app_config');
    }

    /**
     * get database host name
     * 
     * @return string
     */
    public function getDatabaseHostName() {
        return $this->_config['DB_HOST'];
    }

    /**
     * get database name
     *
     * @return string
     */
    public function getDatabaseName() {
        return $this->_config['DB_NAME'];
    }

    /**
     * get database credential username
     *
     * @return string
     */
    public function getDatabaseUser() {
        return $this->_config['DB_USER'];
    }

    /**
     * get database credential password
     *
     * @return string
     */
    public function getDatabasePassword() {
        return $this->_config['DB_PASSWORD'];
    }

    /**
     * get database connection port number
     *
     * @return int
     */
    public function getDatabasePort() {
        return intval($this->_config['DB_PORT']);
    }

    /**
     * whether to set the time zone or not
     *
     * @return boolean
     */
    public function isDbSetTimezone() {
        if (array_key_exists("DB_SET_TIMEZONE", $this->_config) && intval($this->_config["DB_SET_TIMEZONE"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * get server timezone which is to be set
     *
     * @return string
     */
    public function getDbTimezone() {
        return $this->_config["DB_TIMEZONE"];
    }

    /**
     * read server environment
     *
     * @return string
     */
    public function getEnv() {
        return $this->_config["ENV"];
    }

    /**
     * check production status
     *
     * @return bool
     */
    public function isProduction() {
        if (array_key_exists("PRODUCTION_ENV", $this->_config) && intval($this->_config["PRODUCTION_ENV"])> 0) {
            return true;
        }
        return false;
    }

    /**
     * whether the local cache mode ON or OFF
     *
     * @return boolean
     */
    public function isLogEnable() {
        if (array_key_exists("APPLICATION_LOG", $this->_config) && intval($this->_config["APPLICATION_LOG"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * get local message log path for server application
     *
     * @return string
     */
    public function getAppLogPath() {
        return $this->_config["LOG_FILE_PATH"];
    }

    /**
     * check if error debug mode is on
     *
     * @return boolean
     */
    public function isErrorDump() {
        if (array_key_exists("ERROR_DUMP", $this->_config) && intval($this->_config["ERROR_DUMP"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * check server maintenance mode
     *
     * @return boolean
     */
    public function checkMaintenance() {
        if (array_key_exists("MAINTENANCE", $this->_config) && intval($this->_config["MAINTENANCE"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * check the request token verification flag
     *
     * @return boolean
     */
    public function checkRequestTokenFlag() {
        if (array_key_exists("CHECK_REQUEST_TOKEN", $this->_config) && intval($this->_config["CHECK_REQUEST_TOKEN"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * get token secret value for verifying request token
     *
     * @return string
     */
    public function getRequestTokenSecret() {
        return $this->_config['REQUEST_TOKEN_SECRET'];
    }

    /**
     * get token header key for client authentication check
     *
     * @return string
     */
    public function getRequestTokenHeaderKey() {
        return $this->_config['REQUEST_TOKEN_HEADER_KEY'];
    }

    /**
     * Read Global Test User ID
     * This user will to test server API responses while in maintenance mode
     * if user ID exists in database.
     *
     * @return int User ID
     */
    public function getTestUserID() {
        if (array_key_exists('TEST_USER_ID', $this->_config)) {
            return (int) $this->_config['TEST_USER_ID'];
        }

        return -1;
    }

    /**
     * read support mail address
     *
     * @return string
     */
    public function getSupportMailAddress() {
        return $this->_config['SUPPORT_MAIL'];
    }

    /**
     * check server caching is enabled or disabled
     * 
     * @return boolean
     */
    public function isServerCacheEnable() {
        if (array_key_exists('SERVER_CACHE_ENABLE_FLAG', $this->_config) && intval($this->_config["SERVER_CACHE_ENABLE_FLAG"]) > 0) {
            return true;
        }
        return false;
    }

    /**
     * check server local file-caching is enabled or disabled
     * 
     * @return boolean
     */
    public function isLocalFileCacheEnable() {
        if (array_key_exists('FILE_CACHE_FLAG', $this->_config) && intval($this->_config["FILE_CACHE_FLAG"]) > 0) {
            return true;
        }

        return false;
    }

    /**
     * get local cache path
     * 
     * @return string
     */
    public function getLocalCachePath() {
        return $this->_config['LOCAL_CACHE_PATH'];
    }

    /**
     * get memcache host address
     *
     * @return string
     */
    public function getMemcacheHost() {
        return $this->_config['MEMCACHED_HOST'];
    }

    /**
     * get memcache port number
     *
     * @return string
     */
    public function getMemcachePort() {
        return $this->_config['MEMCACHED_PORT'];
    }

    /**
     * get memcache key prefix
     *
     * @return string
     */
    public function getMemcachePrefix() {
        return $this->_config["MEMCACHE_PREFIX"];
    }

    /**
     * get application version
     *
     * @return int
     */
    public function getClientVersion() {
        if (array_key_exists("CLIENT_VERSION", $this->_config) && !empty($this->_config['CLIENT_VERSION'])) {
            return intval($this->_config['CLIENT_VERSION']);
        }
        return 1;
    }

    /**
     * get client application download location
     *
     * @param string $client
     * @return string
     */
    public function getClientStoreLocation(string $client = '') {
        if ($client === 'android') {
            return $this->_config["CLIENT_STORE_LOCATION_ANDROID"];
        } elseif ($client === 'ios') {
            return $this->_config["CLIENT_STORE_LOCATION_iOS"];
        } else {
            return '';
        }
    }

    /**
     * Retrieve activated cache service instance
     *
     * @return object
     */
    public function cacheService() {
        if ($this->isLocalFileCacheEnable()) {
            return new \System\Cache\LocalCache($this->getLocalCachePath());
        } else {
            return new \System\Cache\Memcached($this->getMemcacheHost(), $this->getMemcachePort());
        }
    }

    /**
     * get class instance (singleton)
     *
     * @return obj
     */
    public static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new Config();
        }

        return self::$_instance;
    }

}











