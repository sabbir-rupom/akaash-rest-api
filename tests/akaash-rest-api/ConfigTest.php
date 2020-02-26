<?php

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        $this->configParams = [];
        defined('APP_NAME') or define('APP_NAME', 'TEST: Akaash - RESTful API Template');
    }

    public function test_app_constants_validity()
    {
        $this->assertEquals(ROOT_DIR, realpath(__DIR__ . '/../../public'), 'root directory path test');

        $this->assertDirectoryExists(APP_DIR, 'app directory path test');

        $this->assertDirectoryExists(API_DIR, 'api class directory path test');

        $this->assertDirectoryExists(CONFIG_DIR, 'application config directory path test');

        $this->assertDirectoryExists(SYSTEM_DIR, 'application system directory path test');
    }

    public function test_configuration_parameters()
    {
        $this->assertFileExists(CONFIG_DIR . '/app_config.ini');

        $defaultConfigParams = [
          'ENV', 'BASE_URL', 'PRODUCTION_ENV', 'CLIENT_VERSION',
          'CLIENT_STORE_LOCATION_ANDROID', 'CLIENT_STORE_LOCATION_iOS',
          'ERROR_DUMP', 'MAINTENANCE', 'TEST_USER_ID', 'SUPPORT_MAIL',
          'CHECK_REQUEST_TOKEN', 'REQUEST_TOKEN_HEADER_KEY', 'REQUEST_TOKEN_SECRET',
          'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_PORT', 'DB_SET_TIMEZONE',
          'SERVER_TIMEZONE', 'SERVER_CACHE_ENABLE_FLAG', 'FILE_CACHE_FLAG', 'LOCAL_CACHE_PATH',
          'MEMCACHE_PREFIX', 'MEMCACHED_HOST', 'MEMCACHED_PORT', 'APPLICATION_LOG', 'LOG_FILE_PATH'
        ];

        $this->configParams = parse_ini_file(CONFIG_DIR . "/app_config.ini");

        $this->assertTrue(is_array($this->configParams) && count($this->configParams) >= count($defaultConfigParams));

        foreach ($defaultConfigParams as $value) {
            $this->assertTrue(array_key_exists($value, $this->configParams));
        }
    }

    public function test_database_connection()
    {
        $this->assertFileExists(CONFIG_DIR . '/database.php');

        $this->configParams = parse_ini_file(CONFIG_DIR . "/app_config.ini");

        $this->assertArrayHasKey('DB_HOST', $this->configParams);
        $this->assertArrayHasKey('DB_NAME', $this->configParams);
        $this->assertArrayHasKey('DB_USER', $this->configParams);
        $this->assertArrayHasKey('DB_PASSWORD', $this->configParams);
        $this->assertArrayHasKey('DB_PORT', $this->configParams);


        $this->assertTrue(!empty($this->configParams['DB_HOST']));
        $this->assertTrue(!empty($this->configParams['DB_NAME']));
        $this->assertTrue(!empty($this->configParams['DB_USER']));
        $this->assertTrue(!empty($this->configParams['DB_PORT']));
        $this->assertTrue(!empty($this->configParams['DB_SET_TIMEZONE']));

//        $configArray = $this->configParams;
//        include_once CONFIG_DIR . '/database.php';
//
//        if (intval($this->configParams['DB_SET_TIMEZONE']) > 0) {
//            $this->assertTrue(!empty($this->configParams['SERVER_TIMEZONE']));
//            $this->assertTrue(in_array($this->configParams['SERVER_TIMEZONE'], timezone_identifiers_list()));
//        }
//
//        $this->assertInstanceOf(PDO::class, \Flight::pdo());
    }

    public function test_other_config_files()
    {
        $this->assertFileExists(CONFIG_DIR . '/constants.php');
        $this->assertFileExists(CONFIG_DIR . '/initialize.php');
        $this->assertFileExists(CONFIG_DIR . '/hooks.php');
        $this->assertFileExists(CONFIG_DIR . '/route.php');
    }
}
