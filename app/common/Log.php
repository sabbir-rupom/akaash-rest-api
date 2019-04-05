<?php

/**
 * A RESTful API template in PHP based on flight micro-framework.
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license	http://www.opensource.org/licenses/mit-license.php ( MIT License )
 *
 * @since       Version 1.0.0
 */
(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Log helper class.
 *
 * Developer may use this custom function to write different API logs
 * Or they can use any existing PHP log library [ e.g Monolog Logger ] for better development friendly situation
 */
class Common_Log {
    /**
     * Write log file.
     *
     * @param unknown_type $arrMsg message or array of message
     */
    public static function log($arrMsg) {
        if (false == Config_Config::getInstance()->isLogEnable() || empty($arrMsg)) {
            return;
        }

        //define entry message
        $logEntry = '['.date('D Y-m-d h:i:s A').'] [client: '.(isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost').'] ';

        // if message is array type
        if (is_array($arrMsg)) {
            //concatenate msg with time-frame
            foreach ($arrMsg as $key => $msg) {
                if (is_array($msg) || is_object($msg)) {
                    $msg = json_encode($msg);
                }
                $logEntry .= $key.' : '.$msg."\r\n";
            }
        } else {   //concatenate msg with time-frame
            $logEntry .= $arrMsg."\r\n";
        }
//        $logEntry .= PHP_EOL;
        //create log file with current date as name-extension
        $logFileName = 'api_'.date('Ymd').'.log';

        //open the file append mode,dats the log file will create day wise

        if (false !== ($fp = fopen(Config_Config::getInstance()->getAppLogPath().DIRECTORY_SEPARATOR.$logFileName, 'a+'))) {
            if (true === flock($fp, LOCK_EX)) {
                //write the info into the file
                fwrite($fp, $logEntry);
                flock($fp, LOCK_UN);
            }

            //close handler
            fclose($fp);
        }
    }
}
