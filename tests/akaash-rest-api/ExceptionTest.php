<?php

use PHPUnit\Framework\TestCase;
use Akaash\System\Exception\AppException;

class ExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->configParams = [];
        defined('APP_NAME') or define('APP_NAME', 'TEST: Akaash - RESTful API Template');
    }

    public function test_app_exception_class()
    {
        $this->assertClassHasAttribute('resultCode', AppException::class);
    }
}
