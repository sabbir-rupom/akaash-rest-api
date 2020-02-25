<?php

namespace Hooks;

use API\Filter\Authorization;
use System\Config;
use flight\net\Request;
use System\Exception\AppException;

class Authentication
{
    public static $excludedApiList = [
      'example/user-login',
      'example/user_registration',
      'test'
    ];
    private static $result = true;
    public static $request;
    public static $config;

    /**
     * Initialize class properties for any valid static method call
     * @param type $method
     * @param type $args
     */
    public static function __callStatic($method, $args)
    {
        if (!isset(self::$request)) {
            self::$request = new Request();
        }
        if (!isset(self::$config)) {
            self::$config = new Config();
        }

        return call_user_func_array(
            array(__CLASS__, $method),
            $args
        );
    }

    /**
     * Check client authorization
     */
    public static function isAuthorized()
    {
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
            $e->generate($request, self::$config, 'hooks');
        }
        return true;
    }

    /**
     * Check for excluded API call
     *
     * @return bool
     */
    private function notInExcludedList()
    {
        if (!empty(self::$excludedApiList)) {
            foreach (self::$excludedApiList as $excluded) {
                if (preg_match('#^' . $excluded . '$#iu', self::$request->url)) {
                    self::$result = false;
                    break;
                }
            }
        }
        return self::$result;
    }
}
