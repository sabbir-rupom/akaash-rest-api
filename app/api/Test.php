<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Description of Test.
 *
 * @author sabbir-hossain
 *
 * @internal
 * @coversNothing
 */
class Test extends BaseClass {

    
    const LOGIN_REQUIRED = false; // Disable session requirement flag
    const TEST_ENV = true; // Enable test environment flag

    /**
     * Processing API script execution.
     */
    public function action() {
        /**
         *  Check database connection with PDO driver
         */
        if ($this->pdo instanceof PDO) {
            $responseArray['DB'] = 'Database is properly connected';
//            $dbUserCount = Model_User::countBy();
//            $responseArray['DB'] = !empty($dbUserCount) ? 'Database to user table connection is functional' : 'Database to user table connection is not functional';
        } else {
            $responseArray['DB'] = 'Database is not connected properly. Please check config_app.ini';
        }

        // JWT token verification test case
        if (false === Config_Config::getInstance()->checkRequestTokenFlag()) {
            $responseArray['JWT'] = 'JWT token verification system is disabled. To enable, please check config_app.ini';
        } else {
            if (empty($this->config['REQUEST_TOKEN_SECRET'])) {
                $responseArray['JWT'] = 'JWT token secret key is not set. Please check config_app.ini';
            } else {
                $result = System_JwtToken::createToken(array('test' => 1), $this->config['REQUEST_TOKEN_SECRET']);
                if (0 == $result['error']) {
                    $result = System_JwtToken::verifyToken($result['token'], $this->config['REQUEST_TOKEN_SECRET']);
                }
                $responseArray['JWT'] = ($result['error'] > 0) ? 'JWT verification system is not functional' : 'JWT verification system is functional';
            }
        }

        // Check application logging is functional or not
        $checkLogStatus = true;
        if (Config_Config::getInstance()->isLogEnable()) {
            $logPath = Config_Config::getInstance()->getLogFile();
            if (!file_exists($logPath) && !is_dir($logPath)) {
                if (!mkdir($logPath, 0755, true)) {
                    $responseArray['Log'] = 'Log folder cannnot be created. Please change folder permission for apache access : ' . $logPath;
                    $checkLogStatus = false;
                }
            } else {
                if (!is_writable($logPath)) {
                    $responseArray['Log'] = 'Log folder is not writable. Please change file permission for apache access : ' . $logPath;
                    $checkLogStatus = false;
                }
            }

            if ($checkLogStatus) {
                $responseArray['Log'] = 'System application log is functional';
            }
        } else {
            $responseArray['Log'] = 'System application log is disabled from config';
        }

        // Check cache system is functional or not

        if (Config_Config::getInstance()->isServerCacheEnable()) {
            $message1 = 'Local filecache system is functional';
            if (Config_Config::getInstance()->isLocalFileCacheEnable()) {
                // Check local cache path access permission
                $cachePath = Config_Config::getInstance()->getLocalCachePath();
                if (!file_exists($cachePath) && !is_dir($cachePath)) {
                    if (!mkdir($cachePath, 0755, true)) {
                        $message1 = 'Cache folder cannnot be created. Please change folder permission for apache access : ' . $cachePath;
                    }
                } else {
                    if (!is_writable($cachePath)) {
                        $message1 = 'Cache folder is not writable. Please change file permission for apache access : ' . $cachePath;
                    }
                }
            } else {
                $message1 = 'Local filecache system is disabled from config';
            }

            // Memcache system test case
            $key = 'app_test';

            if (!extension_loaded('memcache')) {
                $message2 = 'Memcache module is not installed';
            } else {
                $cache = Config_Config::getMemcachedClient();
                // clear all existing cache data
                $cache->flush();
                $cache->set($key, 'Checking Data from Memcache', MEMCACHE_COMPRESSED, 120);  // set sample cache data

                $message2 = !empty($cache->get($key)) ? 'Memcache system is functional' : 'Memcache system is not functional. Please check memcache settings.';
            }
            $responseArray['Cache'] = array(
                'filecache' => $message1,
                'memcache' => $message2,
            );
            
        } else {
            $responseArray['Cache'] = 'Server cache feature is disabled from config';
        }

        // Check file upload path access permission

        $profileImagePath = Const_Application::UPLOAD_PROFILE_IMAGE_PATH;
        if (!file_exists($profileImagePath) && !is_dir($profileImagePath)) {
            if (!mkdir($profileImagePath, 0777, true)) {
                $responseArray['Upload'] = 'Upload folder cannnot be created. Please change folder permission for apache access : ' . $profileImagePath;
            }
        } else {
            if (!is_writable($profileImagePath)) {
                $responseArray['Upload'] = 'Upload folder cannnot be created. Please change folder permission for apache access : ' . $profileImagePath;
            } else {
                $imgBase64Data = '/9j/4AAQSkZJRgABAQEAYABgAAD/4RD4RXhpZgAATU0AKgAAAAgABAE7AAIAAAAPAAAISodpAAQAAAABAAAIWpydAAEAAAAeAAAQ0uocAAcAAAgMAAAAPgAAAAAc6gAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFNhYmJpciBIb3NzYWluAAAABZADAAIAAAAUAAAQqJAEAAIAAAAUAAAQvJKRAAIAAAADODcAAJKSAAIAAAADODcAAOocAAcAAAgMAAAInAAAAAAc6gAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADIwMTk6MDI6MTQgMTA6MDM6MTMAMjAxOTowMjoxNCAxMDowMzoxMwAAAFMAYQBiAGIAaQByACAASABvAHMAcwBhAGkAbgAAAP/hCyFodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvADw/eHBhY2tldCBiZWdpbj0n77u/JyBpZD0nVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkJz8+DQo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIj48cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSJ1dWlkOmZhZjViZGQ1LWJhM2QtMTFkYS1hZDMxLWQzM2Q3NTE4MmYxYiIgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIi8+PHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9InV1aWQ6ZmFmNWJkZDUtYmEzZC0xMWRhLWFkMzEtZDMzZDc1MTgyZjFiIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iPjx4bXA6Q3JlYXRlRGF0ZT4yMDE5LTAyLTE0VDEwOjAzOjEzLjg3MzwveG1wOkNyZWF0ZURhdGU+PC9yZGY6RGVzY3JpcHRpb24+PHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9InV1aWQ6ZmFmNWJkZDUtYmEzZC0xMWRhLWFkMzEtZDMzZDc1MTgyZjFiIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iPjxkYzpjcmVhdG9yPjxyZGY6U2VxIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+PHJkZjpsaT5TYWJiaXIgSG9zc2FpbjwvcmRmOmxpPjwvcmRmOlNlcT4NCgkJCTwvZGM6Y3JlYXRvcj48L3JkZjpEZXNjcmlwdGlvbj48L3JkZjpSREY+PC94OnhtcG1ldGE+DQogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgIDw/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';
                $responseArray['Upload'] = 'File upload system is functional';
                $responseArray['TestImagUrl'] = SERVER_HOST . 'uploads/profile_images/' . $this->process_binary_image('test', $imgBase64Data, 'profile', true);
            }
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => $responseArray,
            'error' => array(),
        );
    }

}

