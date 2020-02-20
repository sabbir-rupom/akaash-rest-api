<?php

use PHPUnit\Framework\TestCase;
use System\Log\Logger as Logger;
use flight\net\Request as Request;
use System\Config as Config;

class LoggerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->configParams = [];
    }

    public function test_logger_configuration_params()
    {
        $this->configParams = parse_ini_file(CONFIG_DIR . "/app_config.ini");

        $this->assertArrayHasKey('APPLICATION_LOG', $this->configParams);
        $this->assertArrayHasKey('LOG_FILE_PATH', $this->configParams);
        $this->assertNotEmpty($this->configParams['LOG_FILE_PATH']);
    }

    public function test_logger_directory()
    {
        $this->configParams = parse_ini_file(CONFIG_DIR . "/app_config.ini");
        if (intval($this->configParams['APPLICATION_LOG']) > 0) {
            $this->assertDirectoryExists($this->configParams['LOG_FILE_PATH']);
            $this->assertDirectoryIsReadable($this->configParams['LOG_FILE_PATH']);
            $this->assertDirectoryIsWritable($this->configParams['LOG_FILE_PATH']);
        }
    }

    public function test_application_logger_class()
    {
        $defaultAttr = ['logContent', 'logTime', 'logFile', 'logPath', 'logData', 'clientIp'];
        foreach ($defaultAttr as $attr) {
            $this->assertClassHasAttribute($attr, Logger::class);
        }

        $testMessage = "Test Message";

        $logger = Logger::create(
            new Request,
            new Config,
            $testMessage,
            'test'
        );

        $this->assertTrue(boolval(strpos($logger->lastLine, $testMessage)));
        $this->assertTrue(boolval(strstr($logger->get(['type' => 'test', 'line-num' => 1]), $testMessage)));
    }
}
