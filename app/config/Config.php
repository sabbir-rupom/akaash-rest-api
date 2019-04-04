<?php

/**
 * RESTful API Template
 *
 * A RESTful API template in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	https://github.com/sabbir-rupom/rest-api-PHP-flight/blob/master/LICENSE ( MIT License )
 * @since       Version 1.0.0
 */

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Config class
 * Application configuration class.
 *
 * @author sabbir-hossain
 */
class Config_Config
{
    // store config parameters
    protected $_config;
    private static $_memcachedClient = null;

    /**
     * constructor.
     */
    public function __construct()
    {
        // Load application config file
        $this->_config = Flight::get('app_config');
    }

    /** C:\xampp\htdocs\flight-v1\
     * Read Server Environment.
     */
    public function getEnv()
    {
        return $this->_config['ENV'];
    }

    // Whether production environment is live or not
    public function checkProductionEnvironment()
    {
        return $this->_config['PRODUCTION_ENV'];
    }

    /**
     * Read Server Application Version.
     */
    public function getClientVersion()
    {
        return (int) $this->_config['CLIENT_VERSION'];
    }

    /*
     * Retrieve client application download link
     * @return string $location
     */
    public function getClientUpdateLocation($clientType)
    {
        switch ($clientType) {
            case Const_Application::PLATFORM_TYPE_ANDROID:
                $location = $this->_config['CLIENT_UPDATE_LOCATION_ANDROID'];

                break;
            case Const_Application::PLATFORM_TYPE_IOS:
                $location = $this->_config['CLIENT_UPDATE_LOCATION_iOS'];

                break;
            case Const_Application::PLATFORM_TYPE_WINDOWS:
                $location = $this->_config['CLIENT_UPDATE_LOCATION_WINDOWS_APP'];

                break;
        }

        if (empty($location)) {
            throw new System_ApiException(ResultCode::UNKNOWN_ERROR, 'Client location not found! Check configuration file.');
        }

        return $location;
    }

    /*
     * Check error debugging mode
     *
     * @return boolean
     */
    public function isErrorDump()
    {
        if (array_key_exists('ERROR_DUMP', $this->_config) && '1' === $this->_config['ERROR_DUMP']) {
            return true;
        }

        return false;
    }

    /**
     * Read Server Maintenance Mode.
     */
    public function checkMaintenance()
    {
        if (array_key_exists('MAINTENANCE', $this->_config) && '1' === $this->_config['MAINTENANCE']) {
            return true;
        }

        return false;
    }

    /**
     * Read Global Test User ID
     * This user will to test server API responses while in maintenance mode
     * if user ID exists in database.
     *
     * @return int User ID
     */
    public function getTestUserID()
    {
        if (array_key_exists('TEST_USER_ID', $this->_config)) {
            return (int) $this->_config['TEST_USER_ID'];
        }

        return -1;
    }

    /**
     * Read Support Mail Address.
     */
    public function getSupportMailAddress()
    {
        return $this->_config['SUPPORT_MAIL_TO'];
    }

    /**
     * Check the request token verification flag.
     *
     * @return bool
     */
    public function checkRequestTokenFlag()
    {
        if (array_key_exists('CHECK_REQUEST_TOKEN', $this->_config) && '1' === $this->_config['CHECK_REQUEST_TOKEN']) {
            return true;
        }

        return false;
    }

    /**
     * Read Client Token HTTP Header Key.
     */
    public function getRequestTokenHeaderKey()
    {
        return $this->_setting['REQUEST_TOKEN_HEADER_KEY'];
    }

    /**
     * Read Client Session HTTP Header Key.
     */
    public function getClientSessionHeaderKey()
    {
        return $this->_setting['USER_SESSION_HEADER_KEY'];
    }

    /**
     * Request Token secret Key.
     */
    public function getRequestTokenSecret()
    {
        $key = 'REQUEST_TOKEN_SECRET';

        if (array_key_exists($key, $this->_config)) {
            return $this->_config[$key];
        }

        return null;
    }

    /**
     * Read Database Host Name.
     */
    public function getDatabaseHostName()
    {
        return $this->_config['DB_HOST'];
    }

    /**
     * Read Database Name.
     */
    public function getDatabaseName()
    {
        return $this->_config['DB_NAME'];
    }

    /**
     * Read Database Access Username.
     */
    public function getDatabaseUsername()
    {
        return $this->_config['DB_USER'];
    }

    /**
     * Read Database Access Password.
     */
    public function getDatabasePassword()
    {
        return $this->_config['DB_PASSWORD'];
    }

    /**
     * Read Database Access Port.
     */
    public function getDatabasePort()
    {
        return $this->_config['DB_PORT'];
    }

    /**
     * Whether to set the time zone or not.
     *
     * @return bool
     */
    public function isDbSetTimezone()
    {
        if (array_key_exists('DB_SET_TIMEZONE', $this->_config) && '1' === $this->_config['DB_SET_TIMEZONE']) {
            return true;
        }

        return false;
    }

    /**
     * Get Server Time zone.
     */
    public function getServerTimezone()
    {
        return $this->_config['SERVER_TIMEZONE'];
    }

    /**
     * Whether Server Local File-caching is enabled / disabled.
     */
    public function isLocalCacheEnable()
    {
        if (array_key_exists('LOCAL_CACHE_FLAG', $this->_config) && '1' === $this->_config['LOCAL_CACHE_FLAG']) {
            return true;
        }

        return false;
    }

    /**
     * Local Cache file path.
     */
    public function getLocalCachePath()
    {
        return $this->_config['LOCAL_CACHE_PATH'];
    }

    /**
     * Read Server Log File path.
     */
    public function getLogFile()
    {
        return $this->_config['LOG_FILE_PATH'];
    }

    /**
     * Read Memcache Key Prefix.
     */
    public function getMemcachePrefix()
    {
        return $this->_config['MEMCACHE_PREFIX'];
    }

    /**
     * Whether the Local Cache mode ON or OFF.
     */
    public function isLogEnable()
    {
        if (array_key_exists('APPLICATION_LOG', $this->_config) && '1' === $this->_config['APPLICATION_LOG']) {
            return true;
        }

        return false;
    }

    /**
     * Local log file path.
     */
    public function getAppLogPath()
    {
        return $this->_config['LOG_FILE_PATH'];
    }

    /**
     * Load Memcache configuration.
     */
    public function getMemcachedServer()
    {
        // If it exists, return as it is
        if (!is_null(System_MemcachedServer::$memcachedServerInstance)) {
            return System_MemcachedServer::$memcachedServerInstance;
        }

        $memcachedServerDto = new System_MemcachedServer();
        if (isset($this->_config['MEMCACHED_HOST'])) {
            $memcachedServerDto->host = $this->_config['MEMCACHED_HOST'];
        }
        if (isset($this->_config['MEMCACHED_PORT'])) {
            $memcachedServerDto->port = $this->_config['MEMCACHED_PORT'];
        }
        System_MemcachedServer::$memcachedServerInstance = $memcachedServerDto;

        return System_MemcachedServer::$memcachedServerInstance;
    }

    /**
     * Get cache server connection.
     *
     * @return obj Cache server connection
     */
    public static function getMemcachedClient()
    {
        if (null !== self::$_memcachedClient) {
            return self::$_memcachedClient;
        }

        $config = new Config_Config();

        if (true == $config->isLocalCacheEnable()) {
            $localFileClient = new System_FileCacheClient($config->getLocalCachePath());

            self::$_memcachedClient = $localFileClient;

            return $localFileClient;
        }

        $memCachedClient = $config->getMemcachedServer();
        $memCachedClient->addServer();

        self::$_memcachedClient = $memCachedClient;

        return $memCachedClient;
    }

    // Get class instance
    public static function getInstance()
    {
        return new Config_Config();
    }
}
