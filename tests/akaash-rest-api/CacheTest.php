<?php

use PHPUnit\Framework\TestCase;
use Akaash\Config;
use Akaash\Core\Model\Cache as CacheModel;

class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        $this->configParams = [];
        defined('APP_NAME') or define('APP_NAME', 'TEST: Akaash - RESTful API Template');
    }

    public function test_cache_configuration_params()
    {
        $this->configParams = parse_ini_file(CONFIG_DIR . "/app_config.ini");

        $this->assertArrayHasKey('SERVER_CACHE_ENABLE_FLAG', $this->configParams);
        $this->assertArrayHasKey('FILE_CACHE_FLAG', $this->configParams);
        $this->assertArrayHasKey('LOCAL_CACHE_PATH', $this->configParams);
        $this->assertArrayHasKey('MEMCACHE_PREFIX', $this->configParams);
        $this->assertArrayHasKey('MEMCACHED_HOST', $this->configParams);
        $this->assertArrayHasKey('MEMCACHED_PORT', $this->configParams);

        if (intval($this->configParams['SERVER_CACHE_ENABLE_FLAG']) > 0) {
            $this->assertNotEmpty($this->configParams['LOCAL_CACHE_PATH']);
        }
    }

    public function test_cache_service()
    {
        $this->configParams = parse_ini_file(CONFIG_DIR . "/app_config.ini");

        if (intval($this->configParams['SERVER_CACHE_ENABLE_FLAG']) > 0) {
            $cacheService = Config::getInstance()->cacheService();
            $cacheData = 11223344;
            $this->assertTrue(CacheModel::addCache($cacheService, 'my_test', $cacheData, 100));
            $this->assertEquals(CacheModel::getCache($cacheService, 'my_test'), $cacheData);
        }
    }
}
