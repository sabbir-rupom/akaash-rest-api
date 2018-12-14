<?php

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

    /**
     * String parameter sent with HTTP POST
     * @return string
     */
    public static function getPostStringParameter() {
        $handle = fopen('php://input', 'r');
        $string = fgets($handle);

        fclose($handle);

        return $string;
    }

}
