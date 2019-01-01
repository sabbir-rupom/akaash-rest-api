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
         * Cache system test case
         */
        $key = 'app_test';
        $memcache = Config_Config::getMemcachedClient();
        /*
         * clear all existing cache data
         */
        $memcache->flush();  
        $memcache->set($key, 'Checking Data from Memcache', MEMCACHE_COMPRESSED, 120);  // set sample cache data
        
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

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => [
                'Cache' => !empty($memcache->get($key)) ? 'Cache system is functional' : 'Cache system is not functional',
                'DB' => !empty($dbUserCount) ? 'Database to user table connection is functional' : 'Database to user table connection is not functional',
                'JWT' => $result['error'] > 0 ? 'JWT token verification system is not functional' : 'JWT token verification system is functional'
            ],
            'error' => []
        );
    }

}
