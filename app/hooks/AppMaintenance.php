<?php
namespace Hooks;

use flight\net\Request;
use Hooks\Filter\Maintenance;
use Akaash\Config;
use Akaash\System\Exception\AppException;

class AppMaintenance
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
