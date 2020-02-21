<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Core\Model\Cache as CacheModel;
use Helper\DateUtil;
use flight\net\Request;
use System\Message\ResultCode;

/**
 * Description of Test.
 *
 * @author sabbir-hossain
 *
 * @internal
 * @coversNothing
 */
class Test extends BaseClass
{
    public function __construct(Request $request, $value, $apiName)
    {
        parent::__construct($request, $value, $apiName);
    }

    /**
     * Processing API script execution.
     */
    public function action()
    {

        /**
         *  Check database connection with PDO driver
         */
        if ($this->pdo instanceof PDO) {
            $responseArray['DB'] = 'Database is properly connected';
        } else {
            $responseArray['DB'] = 'Database is not connected properly. Please check config_app.ini';
        }

        /**
         * Check application logger is functional or not
         */
        $checkLoggerStatus = true;
        if ($this->config->isLogEnable()) {
            $logPath = $this->config->getAppLogPath();
            if (!file_exists($logPath) && !is_dir($logPath)) {
                if (!mkdir($logPath, 0755, true)) {
                    $responseArray['Log'] = 'Log folder cannnot be created. Please change folder permission for apache access : ' . $logPath;
                    $checkLoggerStatus = false;
                }
            } else {
                if (!is_writable($logPath)) {
                    $responseArray['Log'] = 'Log folder is not writable. Please change file permission for apache access : ' . $logPath;
                    $checkLoggerStatus = false;
                }
            }

            if ($checkLoggerStatus) {
                $responseArray['Log'] = 'System application log is functional';
            }
        } else {
            $responseArray['Log'] = 'System application log is disabled from config_app.ini';
        }

        /**
         * Check cache system is functional or not
         */
        if ($this->config->isServerCacheEnable()) {
            $message1 = 'Local filecache system is functional';
            if ($this->config->isLocalFileCacheEnable()) {
                /**
                 * Check local cache path access permission
                 */
                $cachePath = $this->config->getLocalCachePath();
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
                $message1 = 'Local filecache system is disabled from config_app.ini';
            }

            // Memcache system test case
            $key = 'akaash_test';
            $msgValue = 'Memcache system is functional';

            if (!extension_loaded('memcache')) {
                $message2 = 'Memcache module is not installed';
            } else {
                $cache = new \System\Cache\Memcached($this->config->getMemcacheHost(), $this->config->getMemcachePort());

                // clear all existing cache data
                CacheModel::clearCache($cache);

                // set sample cache data
                CacheModel::addCache($cache, $key, $msgValue, 120);

                $message2 = !empty(CacheModel::getCache($cache, $key)) ? $msgValue : 'Memcache system is not functional. Please check memcache settings.';
            }
            $responseArray['Cache'] = array(
                'filecache' => $message1,
                'memcache' => $message2,
            );
        } else {
            $responseArray['Cache'] = 'Server cache feature is disabled from config';
        }

        // Check file upload path access permission

        if (!file_exists(UPLOAD_PATH) && !is_dir(UPLOAD_PATH)) {
            if (!mkdir(UPLOAD_PATH, 0644, true)) {
                $responseArray['Upload'] = 'Upload folder cannnot be created. Please change folder permission for apache access : ' . UPLOAD_PATH;
            }
        } else {
            if (!is_writable(UPLOAD_PATH)) {
                $responseArray['Upload'] = 'Upload folder is not writable. Please change folder permission for apache access : ' . UPLOAD_PATH;
            } else {
                $responseArray['Upload'] = 'File upload directory permission is set properly';
            }
        }

        if (!empty($this->value)) {
            $responseArray['value'] = $this->value;
        }

        return [
            'result_code' => ResultCode::SUCCESS,
            'time' => DateUtil::getToday(),
            'data' => $responseArray,
            'error' => []
        ];

//        Model\LogAPI::startTransaction($this->pdo);
//
//        $apiLogObj = new Model\LogAPI();
//
//        $apiLogObj->api_name = 'Test';
//        $apiLogObj->request_data = !empty($this->data) ? json_encode($this->data) : json_encode($this->get);
//        $apiLogObj->response = 123;
//        $apiLogObj->method = $this->method;
//        $id = $apiLogObj->create($this->pdo);
//
//        $result = [];
//        if ($id > 0) {
//            $result = Model\LogAPI::find($id, $this->pdo, true);
//
//            $result->delete($this->pdo);
//        }
//
//        Model\LogAPI::commit($this->pdo);

//        if (!empty($result)) {
//            $result = Model\LogAPI::findAllBy([Model\LogAPI::PRIMARY_KEY => $id], $this->pdo);
//        }
    }
}
