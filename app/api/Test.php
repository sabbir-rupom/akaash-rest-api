<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Description of Test
 *
 * @author sabbir-hossain
 */
class Test extends BaseClass {

    // Login Required.
    const LOGIN_REQUIRED = FALSE;
    const TEST_ENV = TRUE;

    /**
     * Processing API script execution
     */
    public function action() {

        /*
         * DB connectivity with user table test case
         */
        $dbUserCount = Model_User::countBy();

        /*
         * JWT token verification test case
         */
        $result = Lib_JwtToken::createToken(array('test' => 1), $this->config['REQUEST_TOKEN_SECRET']);
        if ($result['error'] == 0) {
            $result = Lib_JwtToken::verify_token($result['token'], $this->config['REQUEST_TOKEN_SECRET']);
        }

        $responseArray = [
            'DB' => !empty($dbUserCount) ? 'Database to user table connection is functional' : 'Database to user table connection is not functional',
            'JWT' => $result['error'] > 0 ? 'JWT token verification system is not functional' : 'JWT token verification system is functional'
        ];

        /*
         * Check application log path access permission
         */
        $logPath = Config_Config::getInstance()->getLogFile();
        if (Config_Config::getInstance()->isLogEnable()) {

            if (!file_exists($logPath) && !is_dir($logPath)) {
                if (!mkdir($logPath, 0755, true)) {
                    $responseArray['Log'] = 'Log folder cannnot be created. Please change folder permission for apache access';
                }
            } else {
                if (!is_writable($logPath)) {
                    $responseArray['Log'] = 'Log folder is not writable. Please change file permission for apache access';
                }
            }
        }

        $cacheSystemStatus = TRUE;
        /*
         * Check cache system is functional or not
         */
        $message = '';
        if (Config_Config::getInstance()->isLocalCache()) {
            /*
             * Check local cache path access permission
             */
            $cachePath = Config_Config::getInstance()->getLocalCachePath();
            if (!file_exists($cachePath) && !is_dir($cachePath)) {
                if (!mkdir($cachePath, 0755, true)) {
                    $message = 'Cache folder cannnot be created. Please change folder permission for apache access';
                    $cacheSystemStatus = FALSE;
                }
            } else {
                if (!is_writable($logPath)) {
                    $message = 'Cache folder is not writable. Please change file permission for apache access';
                    $cacheSystemStatus = FALSE;
                }
            }
        }

        if ($cacheSystemStatus) {
            /*
             * Cache system test case
             */
            $key = 'app_test';
            $memcache = Config_Config::getMemcachedClient();
            /*
             * clear all existing cache data
             */
            $memcache->flush();
            $memcache->set($key, 'Checking Data from Memcache', MEMCACHE_COMPRESSED, 120);  // set sample cache data

            $message = !empty($memcache->get($key)) ? 'Cache system is functional' : 'Cache system is not functional';
        }
        $responseArray['Cache'] = $message;

        /*
         * Check file upload path access permission
         */

        $profileImagePath = Const_Application::UPLOAD_PROFILE_IMAGE_PATH;
        if (!file_exists($profileImagePath) && !is_dir($profileImagePath)) {
            if (!mkdir($profileImagePath, 0777, true)) {
                $responseArray['Upload'] = 'Upload folder cannnot be created. Please change folder permission for apache access';
            }
        } else {
            if (!is_writable($profileImagePath)) {
                $responseArray['Upload'] = 'Upload folder cannnot be created. Please change folder permission for apache access';
            }
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => $responseArray,
            'error' => []
        );
    }

}
