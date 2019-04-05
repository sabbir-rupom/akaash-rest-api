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

use Firebase\JWT\JWT;

// System library class JwtToken
class System_JwtToken {
    /**
     * Verify JWT Token with secret key.
     *
     * @param string $token JWT Token
     * @param string $key   secret key to sign the JWT token
     *
     * @return array Token verification result
     */
    public static function verifyToken($token = '', $key = '') {
        $result = array('error' => 0, 'data' => array());
        if ('' == $token) {
            $result['error'] = 5;

            return $result;
        }

        try {
            JWT::$leeway = 60; // $leeway in seconds
            $decoded = JWT::decode($token, $key, Const_Application::JWT_ENCODE_ALGORITHMS);
        } catch (\Exception $e) { // Also tried JwtException
            $result['error'] = 1;

            return $result;
        }

        $result['data'] = $decoded;

        return $result;
    }

    /**
     * Create JWT Token from given key and payload.
     *
     * @param array  $payload sensitive data as payload
     * @param string $key     secret key to sign the JWT token
     *
     * @return array Token creation result
     */
    public static function createToken($payload = array(), $key = '') {
        $result = array('error' => 0, 'token' => '');

        try {
            $jwt = JWT::encode($payload, $key);
            $result['token'] = $jwt;
        } catch (\Exception $e) {
            // Also tried JwtException
            $result['error'] = 1;

            return $result;
        }

        return $result;
    }
}
