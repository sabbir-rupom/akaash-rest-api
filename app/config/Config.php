<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

class Config_Config {

    protected static $_configClassName = "Config_Config";
    protected static $_singleton = null;
    // config file
    protected $_config = null;

    /**
     * constructor
     */
    public function __construct($platformType = null) {
        // Automatically load when no platform type is specified
        if (null === $platformType) {
            $this->_autoSelectConfig();
            return true;
        }

        $this->_selectConfig($platformType);
    }

    /**
     * Change the setting file to be read automatically
     */
    protected function _autoSelectConfig() {
        $platformType = Common_Util_NetUtil::getHttpRequestPlatformType();
        return $this->_selectConfig($platformType);
    }

    /**
     * Read the configuration file of the platform passed as an argument
     *
     * @param string $platformType
     * @throws Exception_ApiException
     */
    protected function _selectConfig($platformType = null) {
        $this->_config = $this->_loadConfig('config_app.ini');

        switch ($platformType) {
            case Const_Application::PLATFORM_TYPE_ANDROID:
                $this->_getLoadAnroidConfig();
                break;
            case Const_Application::PLATFORM_TYPE_IOS:
                $this->_getLoadIosConfig();
                break;
            default:
                $this->_getLoadAnroidConfig();
                break;
        }
    }

    /**
     * Load Android configuration file
     */
    private function _getLoadAnroidConfig() {
        /*
         * Load any configuration file specifically for Android
         */
    }

    /**
     * Read iOS configuration file
     */
    private function _getLoadIosConfig() {
        /*
         * Load any configuration file specifically for iOS
         */
    }

    /**
     * Application Config
     */
    private function _loadConfig($fileName) {
        if (defined('CONFIG_DIR') && file_exists(CONFIG_DIR . "/" . $fileName)) {
            return parse_ini_file(CONFIG_DIR . "/" . $fileName);
        }

        return array();
    }

    public static function setConfigClassName($configClassName) {
        self::$_configClassName = $configClassName;
    }

    /**
     * Load prescribed config
     *
     * @param unknown $platform
     */
    public static function setDefaultConfig($platform) {
        self::$_singleton = self::getInstance($platform);
    }

    /**
     *
     * @param string $platform
     * @return Config_Config
     */
    public static function getInstance($platform = null) {
        if ($platform !== null) {

            return new self::$_configClassName($platform);
        }

        if (self::$_singleton !== null) {
            return self::$_singleton;
        }

        self::$_singleton = new self::$_configClassName();

        return self::$_singleton;
    }

    /**
     * Read Database Host Name
     */
    public function getDatabaseHostName() {
        return $this->_config['MYSQL_READ_CONNECTION'];
    }

    /**
     * Read Database Access Name
     */
    public function getDatabaseName() {
        return $this->_config['MYSQL_DB_NAME'];
    }

    /**
     * Read Database User Name
     */
    public function getDatabaseUser() {
        return $this->_config['MYSQL_USER'];
    }

    /**
     * Read Database Access Password
     */
    public function getDatabasePassword() {
        return $this->_config['MYSQL_PASSWORD'];
    }

    /**
     * Read Database Access Port
     */
    public function getDatabasePort() {
        return $this->_config['MYSQL_PORT'];
    }

    /**
     * Read Server Log File path
     */
    public function getLogFile() {
        return $this->_config['LOG_FILE'];
    }

    /**
     * Client Support Version
     */
//    public function isSupportClientVersion($clientVersion) {
//        if (null === $clientVersion) {
//            return false;
//        }
//
//        $key = 'REQUEST_TOKEN_SECRET_' . $clientVersion;
//
//        if (array_key_exists($key, $this->_game)) {
//            return true;
//        }
//
//        return false;
//    }

    /**
     * Request hash's secret Key
     */
    public function getRequestTokenSecret($clientVersion = null) {
        if (null === $clientVersion) {
            return null;
        }

//        $key = 'REQUEST_TOKEN_SECRET_' . $clientVersion;
        $key = 'REQUEST_TOKEN_SECRET';

        if (array_key_exists($key, $this->_game)) {
            return $this->_config[$key];
        }

        return null;
    }

    /**
     * Whether the virtual KVS mode or not
     */
    public function isLocalKvs() {
        if (array_key_exists("LOCAL_KVS_FLAG", $this->_config) && $this->_config["LOCAL_KVS_FLAG"] === '1') {
            return true;
        }
        return false;
    }

    /**
     * Virtual KVS file path
     */
    public function getLocalKvsPath() {
        return $this->_config["LOCAL_KVS_PATH"];
    }

    public function checkProductionEnvironment() {
        return $this->_config["PRODUCTION_ENV"];
    }

    protected $_memcachedServerInstance = null;

    /**
     * Load Memcache configuration
     */
    public function getMemcachedServerDto() {
        // If it exists, return it as is.
        if (!is_null($this->_memcachedServerInstance)) {
            return $this->_memcachedServerInstance;
        }

        $memcachedServerDto = new Common_Kvs_MemcachedServerDto();
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
            throw new Exception_ApiException(ResultCode::UNKNOWN_ERROR, 'Client location not found!');
        }
        return $location;
    }

    /**
     * Check the request token verification flag
     *
     * @return boolean
     */
    public function getRequestTokenCheckFlag() {
        if (array_key_exists("CHECK_REQUEST_TOKEN", $this->_config) && $this->_config["CHECK_REQUEST_TOKEN"] === '1') {
            return true;
        }
        return false;
    }

    /**
     * Check the log write permission flag
     *
     * @return boolean
     */
    public function getApplicationLog() {
        if (array_key_exists("APPLICATION_LOG", $this->_config) && $this->_config["APPLICATION_LOG"] === '1') {
            return true;
        }
        return false;
    }

}
