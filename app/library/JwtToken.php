<?php declare(strict_types=1);

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Library;

use Firebase\JWT\JWT;

/**
 * PHP library on JWT Token
 */
class JwtToken
{
    const JWT_ENCODE_ALGORITHM = 'HS256';

    private static $result = [
      'success' => false,
      'msg' => '',
      'data' => []
    ];

    /**
     * Verify JWT Token with secret key.
     *
     * @param string $token JWT Token
     * @param string $key   secret key to sign the JWT token
     *
     * @return array Token verification result
     */
    public static function verifyToken(string $token, string $key):array
    {
        if (empty($token)) {
            static::$result['msg'] = 'Token is empty';
        } elseif (empty($key)) {
            static::$result['msg'] = 'Token verification secret key is empty';
        } else {
            try {
                JWT::$leeway = 60; // $leeway in seconds
                static::$result['data'] = JWT::decode($token, $key, self::JWT_ENCODE_ALGORITHM);
                static::$result['success'] = true;
            } catch (\Exception $e) { // Also tried JwtException
                static::$result['msg'] = $e->getMessage();
            }
        }
        return static::$result;
    }

    /**
     * Create JWT Token from given key and payload.
     *
     * @param array  $payload sensitive data as payload
     * @param string $key     secret key to sign the JWT token
     *
     * @return array Token creation result
     */
    public static function createToken(array $payload = array(), string $key):array
    {
        if (empty($key)) {
            static::$result['msg'] = 'Token verification secret key is empty';
        } else {
            try {
                static::$result['data'] = JWT::encode($payload, $key);
                static::$result['success'] = true;
            } catch (\Exception $e) {
                // Also tried JwtException
                static::$result['msg'] = $e->getMessage();
            }
        }

        return static::$result;
    }
}
