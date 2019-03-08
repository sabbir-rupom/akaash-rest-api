<?php

/*
 * RESTful API Template
 * 
 * A RESTful API template based on flight-PHP framework
 * This software project is based on my recent REST-API development experiences. 
 * 
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT 
 * 
 * @author	Sabbir Hossain Rupom
 * @since	Version 1.0.0
 * @filesource
 */

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * A utility class that summarizes the processing on the network
 *
 */
class Common_NetUtil {

    /**
     * Return the requested OS type
     *
     * @return unknown NULL
     */
    public static function getHttpRequestPlatformType() {

        // Unable to acquire OS type None
        if (false == array_key_exists('platform_type', $_GET)) {
            if (TRUE == array_key_exists('client_type', $_GET)) {
                $client = intval($_GET['client_type']);
                if ($client === 2) {
                    return Const_Application::PLATFORM_TYPE_ANDROID;
                }
            }
            return Const_Application::PLATFORM_TYPE_NONE;
        }

        $platformType = $_GET['platform_type'];
        // Not applicable OS type
        if (Const_Application::PLATFORM_TYPE_IOS != $platformType &&
                Const_Application::PLATFORM_TYPE_ANDROID != $platformType) {
            return Const_Application::PLATFORM_TYPE_NONE;
        }

        return $platformType;
    }

}
