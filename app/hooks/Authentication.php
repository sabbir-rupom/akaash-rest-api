<?php
namespace Hooks;

use flight\net\Request;
use Hooks\Filter\Authorization;
use Akaash\Config;
use Akaash\System\Exception\AppException;

class Authentication
{
    public static $excludedApiList;
    private static $result = true;
    public static $request;
    public static $config;

    /**
     * Check client authorization
     */
    public static function isAuthorized()
    {
        self::$request = new Request();
        self::$config = new Config();

        try {
            if (self::notInExcludedList()) {
                //Check Server Maintenance Status
                $auth = new Authorization(self::$config->checkRequestTokenFlag());
                $auth->check();
            }
        } catch (AppException $e) {
            /*
             * Handle all error / exception messages
             */
            $e->generate(self::$request, self::$config, 'hooks');
        }
        return true;
    }

    /**
     * Check for excluded API call
     *
     * @return bool
     */
    private static function notInExcludedList()
    {
        self::$excludedApiList = defined('EXCLUDE_API_VERIFICATION') ? EXCLUDE_API_VERIFICATION : [];

        if (!empty(self::$excludedApiList)) {
            foreach (self::$excludedApiList as $excluded) {
                if (stripos(strtolower(self::$request->url), $excluded) !== false ||
                    in_array(self::$request->url, ['', '/'])) {
                    // 'see details' is in the $line
                    self::$result = false;
                    break;
                }
            }
        }
        return self::$result;
    }
}
