<?php

/*
 * RESTful API Template
 * 
 * A RESTful API template based on flight-PHP framework
 * This software project is based on my recent REST-API development experiences. 
 * 
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT 
 * 
 * @author	Sabbir Hossain Rupom
 * @since	Version 1.0.0
 * @filesource
 */

(defined('APP_NAME')) OR exit('Forbidden 403');

use \Firebase\JWT\JWT;

/**
 * JwtToken Class
 *
 */

class JwtToken {

    /**
     * Verify JWT Token with secret key
     * @param string $token JWT Token
     * @param string $key secret key to sign the JWT token 
     * @return array Token verification result
     */
    public static function verifyToken($token = '', $key = '') {
        $result = array('error' => 0, 'data' => array());
        if ($token == '') {
            $result['error'] = 5;
            return $result;
        }

        try {
            $decoded = JWT::decode($token, $key, array('HS256'));
            JWT::$leeway = 600; // $leeway in seconds
        } catch (\Exception $e) { // Also tried JwtException
            $result['error'] = 1;
            return $result;
        }

        $result['data'] = $decoded;

        return $result;
    }

    /**
     * Create JWT Token from given key and payload
     * @param array $payload sensitive data as payload
     * @param string $key secret key to sign the JWT token 
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
