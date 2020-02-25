<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Core\Model\Cache as CacheModel;
use Helper\DateUtil;
use System\Config;
use flight\net\Request;
use Library\JwtToken;
use System\Message\ResultCode;
use View\Output;
use System\Cache\Memcached;

/**
 * This API performs minimal REST application system check
 *
 * @author sabbir-hossain
 *
 * @internal
 * @coversNothing
 */
class Test
{
    public $response;
    public $value;
    public $pdo;
    public $config;

    public function __construct(Request $request, $value, $apiName)
    {
        $this->response = [];
        $this->value = $value;
        $this->config = new Config;
        $this->pdo = \Flight::pdo();
    }

    /**
     * Process API script
     */
    public function process()
    {

        //  Check database connection with PDO driver
        $this->_checkDatabaseConnectivity();

        // Check application logger is functional or not
        $this->_checkApplicationLoggerService();

        // Check cache system is functional or not
        $this->_checkCacheService();

        // Check token verification servide is functional or not
        $this->_checkTokenAuthenticationService();

        // Check file upload path access permission
        $this->_checkFileUploadService();

        // check URI segment values if provided
        if (!empty($this->value)) {
            $this->response['Value'] = $this->value;
        }

        /**
         * if (!empty($this->get))
         *    echo $this->getInputQuery('client');
         *    echo $this->getInputQuery('type', 'string');
         */

        Output::response([
          'result_code' => ResultCode::SUCCESS,
          'time' => DateUtil::getToday(),
          'data' => $this->response,
          'error' => []
        ]);
    }

    /**
     * Authentication token verification functionality check
     *
     * @return
     */
    private function _checkTokenAuthenticationService()
    {
        if (false === $this->config->checkRequestTokenFlag()) {
            $this->response['AUTH'] = 'AUTH token verification service is disabled. To enable, please check app_config.ini';
            return;
        }
        $tokenSecret = $this->config->getRequestTokenSecret();
        if (empty($tokenSecret)) {
            $this->response['AUTH'] = 'AUTH token secret key is not set. Please check app_config.ini';
            return;
        }

        $testData = ['test' => 1, 'iat' => time()];
        $cResult = JwtToken::createToken($testData, $tokenSecret);
        if (!$cResult['success']) {
            $this->response['AUTH'] = 'AUTH token verification service is not functional. ' . $cResult['msg'];
            return;
        }

        $vResult = JwtToken::verifyToken($cResult['data'], $tokenSecret);
        if ($vResult['success'] && (array) $vResult['data'] == $testData) {
            $this->response['AUTH'] = 'JWT verification service is functional';
        } else {
            $this->response['AUTH'] = 'AUTH token verification service is not functional. ' . $vResult['msg'];
        }

        return;
    }

    /**
     * Check file upload functionality
     */
    private function _checkFileUploadService()
    {
        if (!file_exists(UPLOAD_PATH) && !is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0644, true)) {
            $this->response['Upload'] = 'Upload folder cannnot be created. '
                . 'Please change folder permission for apache access : ' . UPLOAD_PATH;
        } elseif (!is_writable(UPLOAD_PATH)) {
            $this->response['Upload'] = 'Upload folder is not writable. '
                . 'Please change folder permission for apache access : ' . UPLOAD_PATH;
        } else {
            $this->response['Upload'] = 'File upload directory permission is set properly';
        }
    }

    /**
     * Check application logger service functionality
     */
    private function _checkApplicationLoggerService()
    {
        $checkLoggerStatus = true;
        if ($this->config->isLogEnable()) {
            $logPath = $this->config->getAppLogPath();
            if (!file_exists($logPath) && !is_dir($logPath)) {
                if (!mkdir($logPath, 0755, true)) {
                    $this->response['Log'] = 'Log folder cannnot be created. '
                        . 'Please change folder permission for apache access : ' . $logPath;
                    $checkLoggerStatus = false;
                }
            } else {
                if (!is_writable($logPath)) {
                    $this->response['Log'] = 'Log folder is not writable. '
                        . 'Please change file permission for apache access : ' . $logPath;
                    $checkLoggerStatus = false;
                }
            }

            if ($checkLoggerStatus) {
                $this->response['Log'] = 'System application log is functional';
            }
        } else {
            $this->response['Log'] = 'System application log is disabled from app_config.ini';
        }
    }

    /**
     * Check database connectivity
     */
    private function _checkDatabaseConnectivity()
    {
        if ($this->pdo instanceof PDO) {
            $this->response['DB'] = 'Database is properly connected';
        } else {
            $this->response['DB'] = 'Database is not connected properly. Please check app_config.ini';
        }
    }

    /**
     * Check cache service functionality
     *
     * @return
     */
    private function _checkCacheService()
    {
        if ($this->config->isServerCacheEnable() == false) {
            $this->response['Cache'] = 'Server cache feature is disabled from config';
            return;
        }

        $message1 = 'Local filecache service is functional';
        if ($this->config->isLocalFileCacheEnable()) {
            /**
             * Check local cache path access permission
             */
            $cachePath = $this->config->getLocalCachePath();
            if (!file_exists($cachePath) && !is_dir($cachePath) && !mkdir($cachePath, 0755, true)) {
                $message1 = 'Cache folder cannnot be created. '
                    . 'Please change folder permission for apache access : ' . $cachePath;
            }
            if (!is_writable($cachePath)) {
                $message1 = 'Cache folder is not writable. '
                    . 'Please change file permission for apache access : ' . $cachePath;
            }
        } else {
            $message1 = 'Local filecache service is disabled from app_config.ini';
        }

        // Memcache system test case
        $key = 'akaash_test';
        $msgValue = 'Memcache service is functional';

        if (!extension_loaded('memcache')) {
            $message2 = 'Memcache module is not installed';
        } else {
            $cache = new Memcached($this->config->getMemcacheHost(), $this->config->getMemcachePort());

            // clear all existing cache data
            CacheModel::clearCache($cache);

            // set sample cache data
            CacheModel::addCache($cache, $key, $msgValue, 120);

            $message2 = !empty(CacheModel::getCache($cache, $key)) ? $msgValue : 'Memcache system is not functional. '
                . 'Please check memcache settings.';
        }
        $this->response['Cache'] = array(
          'filecache' => $message1,
          'memcache' => $message2,
        );
        return;
    }
}

//        Model\Item::startTransaction($this->pdo);
//
//        $itemObj = new Model\Item();
//        $itemObj->item_name = 'Test';
//        $itemObj->created_at = Helper\DateUtil::getToday();
//        $itemId = $itemObj->create($this->pdo);
//
//        $result = [];
//        if ($itemId > 0) {
//            $result = Model\Item::find($itemId, $this->pdo);
//            $result->delete($this->pdo);
//        }
//
//        Model\Item::commit($this->pdo);
