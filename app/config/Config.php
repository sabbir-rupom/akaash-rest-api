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

/**
 * Config class
 * Application configuration class
 * 
 * @author sabbir-hossain
 */

class Config_Config {

    // store config parameters
    protected $_config = null;
    private static $_memcachedClient = null;

    /**
     * constructor
     */
    public function __construct() {
        // Load application config file
        $this->_config = Flight::get('app_config');
    }

    /**
     * Read Database Host Name
     */
    public function getDatabaseHostName() {
        return $this->_config['DB_HOST'];
    }

    /**
     * Read Database Access Name
     */
    public function getDatabaseName() {
        return $this->_config['DB_NAME'];
    }

    /**
     * Read Database User Name
     */
    public function getDatabaseUser() {
        return $this->_config['DB_USER'];
    }

    /**
     * Read Database Access Password
     */
    public function getDatabasePassword() {
        return $this->_config['DB_PASSWORD'];
    }

    /**
     * Read Database Access Port
     */
    public function getDatabasePort() {
        return $this->_config['DB_PORT'];
    }

    /**
     * Read Server Log File path
     */
    public function getLogFile() {
        return $this->_config['LOG_FILE_PATH'];
    }

    /**
     * Request Token secret Key
     */
    public function getRequestTokenSecret() {
//     $key = 'REQUEST_TOKEN_SECRET_' . $clientVersion;
        $key = 'REQUEST_TOKEN_SECRET';

        if (array_key_exists($key, $this->_config)) {
            return $this->_config[$key];
        }

        return null;
    }

    /**
     * Whether the Local Cache mode ON or OFF
     */
    public function isLocalCache() {
        if (array_key_exists("LOCAL_CACHE_FLAG", $this->_config) && $this->_config["LOCAL_CACHE_FLAG"] === '1') {
            return true;
        }
        return false;
    }
    
    /**
     * Whether the Local Cache mode ON or OFF
     */
    public function isLogEnable() {
        if (array_key_exists("APPLICATION_LOG", $this->_config) && $this->_config["APPLICATION_LOG"] === '1') {
            return true;
        }
        return false;
    }

    /**
     * Local Cache file path
     */
    public function getLocalCachePath() {
        return $this->_config["LOCAL_CACHE_PATH"];
    }
    /**
     * Local log file path
     */
    public function getAppLogPath() {
        return $this->_config["LOG_FILE_PATH"];
    }

    /*
     * Whether production environment is live or not
     */
    public function checkProductionEnvironment() {
        return $this->_config["PRODUCTION_ENV"];
    }

    protected $_memcachedServerInstance = null;

    /**
     * Load Memcache configuration
     */
    public function getMemcachedServer() {
        // If it exists, return it as is.
        if (!is_null($this->_memcachedServerInstance)) {
            return $this->_memcachedServerInstance;
        }

        $memcachedServerDto = new System_MemcachedServer();
        if (isset($this->_config['MEMCACHED_HOST'])) {
            $memcachedServerDto->host = $this->_config['MEMCACHED_HOST'];
        }
        if (isset($this->_config['MEMCACHED_PORT'])) {
            $memcachedServerDto->port = $this->_config['MEMCACHED_PORT'];
        }
        $this->_memcachedServerInstance = $memcachedServerDto;
        return $this->_memcachedServerInstance;
    }

    /**
     * Get cache server connection 
     * @return obj Cache server connection 
     */
    public static function getMemcachedClient() {

        if (null !== self::$_memcachedClient) {
            return self::$_memcachedClient;
        }

        $config = New Config_Config();

        if (true == $config->isLocalCache()) {

            $localFileClient = new System_FileCacheClient($config->getLocalCachePath());

            self::$_memcachedClient = $localFileClient;
            return $localFileClient;
        }

        $memCachedClient = $config->getMemcachedServer();
        $memCachedClient->addServer();

        self::$_memcachedClient = $memCachedClient;

        return $memCachedClient;
    }

    /**
     * Read Memcache Key Prefix
     */
    public function getMemcachePrefix() {
        return $this->_config["MEMCACHE_PREFIX"];
    }

    /**
     * Whether to set the time zone or not
     *
     * @return boolean
     */
    public function isDbSetTimezone() {
        if (array_key_exists("DB_SET_TIMEZONE", $this->_config) && $this->_config["DB_SET_TIMEZONE"] === '1') {
            return true;
        }
        return false;
    }

    /**
     * Set time zone
     */
    public function getDbTimezone() {
        return $this->_config["DB_TIMEZONE"];
    }

    /**
     * Read Server Environment 
     */
    public function getEnv() {
        return $this->_config["ENV"];
    }

    /**
     * Read Client Token Verification Secret Key
     */
    public function getClientTokenSecret() {
        return $this->_setting["REQUEST_TOKEN_SECRET"];
    }

    public function isErrorDump() {
        if (array_key_exists("ERROR_DUMP", $this->_config) && $this->_config["ERROR_DUMP"] === '1') {
            return true;
        }
        return false;
    }

    /**
     * Read Server Maintenance Mode
     */
    public function getMaintenance() {
        return $this->_config["MAINTENANCE"];
    }

    /**
     * Read Server Application Version
     */
    public function getClientVersion() {
        return (int) $this->_config["CLIENT_VERSION"];
    }

    public function getClientUpdateLocation($clientEdition) {
        $location = $this->_config["CLIENT_UPDATE_LOCATION"];

        if (empty($location)) {
            throw new System_ApiException(ResultCode::UNKNOWN_ERROR, 'Client location not found!');
        }
        return $location;
    }

    /**
     * Check the request token verification flag
     *
     * @return boolean
     */
    public function checkRequestTokenFlag() {
        if (array_key_exists("CHECK_REQUEST_TOKEN", $this->_config) && $this->_config["CHECK_REQUEST_TOKEN"] === '1') {
            return true;
        }
        return false;
    }
    
    /*
     * Get class instance
     */
    public static function getInstance() {
        return new Config_Config;
    }

}
