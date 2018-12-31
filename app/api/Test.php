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

        $key = 'app_test';
        $memcache = Config_Config::getMemcachedClient();
        $memcache->flush();  // clear all cache data
        $memcache->set($key, 'Checking Data from Memcache', MEMCACHE_COMPRESSED, 120);  // set sample cache data

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => ['cache' => $memcache->get($key)],
            'error' => []
        );
    }

}
