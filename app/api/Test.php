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

    /**
     * Processing API script execution
     */
    public function action() {

        $key = 'halaku_kha';
        $memcache = Config_Config::getMemcachedClient();
        $memcache->set($key, 'Checking Data ' . time(), MEMCACHE_COMPRESSED, 120);

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => ['cache' => $memcache->get($key)],
            'error' => []
        );
    }

}
