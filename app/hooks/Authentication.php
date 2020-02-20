<?php

namespace Hooks;

use API\Filter\Authorization;
use System\Config;
use flight\net\Request;
use System\Exception\AppException;

class Authentication
{

    /**
     * Check client authorization
     */
    public static function isAuthorized()
    {
        try {
            //Check Server Maintenance Status
            $auth = new Authorization(Config::getInstance()->checkRequestTokenFlag());
            $auth->check();
        } catch (AppException $e) {
            /*
             * Handle all error / exception messages
             */
            $e->generate(new Request(), Config::getInstance(), 'hooks');
        }
    }
}
