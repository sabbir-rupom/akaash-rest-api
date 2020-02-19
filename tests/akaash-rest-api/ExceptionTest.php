<?php

use PHPUnit\Framework\TestCase;
use System\Exception\AppException;

class ExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        $this->configParams = [];
        defined('APP_NAME') or define('APP_NAME', 'TEST-rpm-REST-flight-PHP');
    }

    public function test_app_exception_class()
    {
        $this->assertClassHasAttribute('resultCode', AppException::class);
    }
}
