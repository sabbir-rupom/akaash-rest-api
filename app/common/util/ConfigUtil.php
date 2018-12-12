<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

class Common_Util_ConfigUtil {

    /**
     * Get object instance of Config class
     * @param string $platform
     * @return Config
     */
    public static function getInstance($platform = null) {
        if ($platform !== null) {
            return Config_Config::getInstance($platform);
        }

        $configObject = Config_Config::getInstance();

        return $configObject;
    }

}
