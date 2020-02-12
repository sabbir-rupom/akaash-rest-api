<?php

namespace Hooks;

use API\Filter\Maintenance as Maintenance;
use System\Config as Config;
use flight\net\Request as Request;
use System\Exception\AppException as AppException;

class AppMaintenace
{

    /**
     * check server application under maintenance or not
     */
    public static function isRunning()
    {
        try {
            //Check Server Maintenance Status
            $maintenance = new Maintenance(Config::getInstance()->checkMaintenance());
            $maintenance->check();
        } catch (AppException $e) {
            /*
             * Handle all error / exception messages
             */
            $e->generate(new Request(), Config::getInstance(), 'hooks');
        }
    }
}
