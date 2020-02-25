<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Model;

use System\Core\Model\Base as BaseModel;
use System\Core\Model\Cache;
use System\Config;

class User extends BaseModel
{
    /* Table Definition */

    const TABLE_NAME = "users";
    const PRIMARY_KEY = 'user_id';
    const HAS_CREATED_AT = true;

    protected static $columnDefs = array(
      'user_id' => array(
        'type' => 'int',
        'json' => true
      ),
      'email' => array(
        'type' => 'string',
        'json' => true
      ),
      'password' => array(
        'type' => 'string',
        'json' => false
      ),
      'profile_image' => array(
        'type' => 'string',
        'json' => true
      ),
      'gender' => array(
        'type' => 'string',
        'json' => true
      ),
      'first_name' => array(
        'type' => 'string',
        'json' => true
      ),
      'last_name' => array(
        'type' => 'string',
        'json' => true
      ),
      'device_token' => array(
        'type' => 'string',
        'json' => false
      ),
      'device_model' => array(
        'type' => 'string',
        'json' => false
      ),
      'created_at' => array(
        'type' => 'string',
        'json' => false
      ),
      'updated_at' => array(
        'type' => 'string',
        'json' => false
      )
    );
    protected static $cache = null;

    public function __construct()
    {
        self::$cache = Config::getInstance()->cacheService();
    }

    /**
     * Initialize cache instance for any valid static method call
     * @param type $method
     */
    public static function __callStatic($method, $args)
    {
        if (method_exists(__CLASS__, $method)) {
            if (empty(static::$cache)) {
                static::$cache = Config::getInstance()->cacheService();
            }
        }

        return call_user_func_array(
            array(__CLASS__, $method),
            $args
        );
    }

    /**
     * Registration process execution.
     *
     * @return User object.
     * @throws System_ApiException
     */
    public function createUser($dataArray, $pdo)
    {

        // Get User data by uuid
        $user = self::findBy(array('email' => $dataArray['email']), $pdo);

        // Checke User exist or not
        if (null === $user || empty($user)) {

            // User Data Collection
            $this->password = password_hash($dataArray['password'], PASSWORD_BCRYPT, array('cost' => 12));
            $this->email = $dataArray['email'];
            $this->first_name = $dataArray['firstname'];
            $this->last_name = $dataArray['lasttname'];
            $this->gender = $dataArray['gender'] === 'male' ? 'male' : 'female';

            $this->device_token = $dataArray['d_token'];
            $this->device_model = $dataArray['d_model'];

            $this->create($pdo);
        } else {
            throw new AppException(ResultCode::DATA_ALREADY_EXISTS, 'User already exist in database');
        }
        return $user;
    }

    /**
     * Before updating user information delete userdata from cache
     * @param type $pdo
     */
    public function update($pdo = null, $cacheDelete = true)
    {
        if ($cacheDelete) {
            Cache::deleteCache(self::$cache, Config::getInstance()->getMemcachePrefix() . 'user_' . $this->user_id);
        }
        parent::update($pdo);
    }

    /**
     * Return an associative array for JSON.
     */
    public function toJsonHash($additionalData = array())
    {
        $hash = parent::toJsonHash();

        if (!empty($additionalData)) {
            foreach ($additionalData as $key => $value) {
                $hash[$key] = $value;
            }
        }
        return $hash;
    }

    /**
     * Update the session ID, save the session to Memcached.
     */
    protected static function cacheUserSession($userObj)
    {
        session_regenerate_id();
        $sessionId = session_id();
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userObj->user_id;

        Cache::addCache(self::$cache, $sessionKey, $sessionId);

        $userKey = Config::getInstance()->getMemcachePrefix() . 'user_' . $userObj->user_id;
        Cache::setCache(self::$cache, $userKey, $userObj);

        return $sessionId;
    }

    /**
     * Delete old session.
     */
    protected static function removeUserSession($userId)
    {
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
        Cache::deleteCache(self::$cache, $sessionKey);

        $userKey = Config::getInstance()->getMemcachePrefix() . 'user_' . $userId;
        Cache::deleteCache(self::$cache, $userKey);
    }

    /**
     * Return from the session to get a user ID.
     */
    protected static function retrieveSessionFromUserId($userId)
    {
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
        return Cache::getCache(self::$cache, $sessionKey);
    }

    /**
     * Save the user ID in Cache with session ID as key
     *
     * @param mixed $sessionId
     * @param mixed $userId
     */
    protected static function cacheSession($sessionId, $userId)
    {
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
        Cache::setCache(self::$cache, $sessionKey, $sessionId);
    }

    /**
     * Find User form cache
     *
     * @param int $userId
     * @param PDO $pdo
     *
     * @return Model_User
     */
    protected static function cacheOrFind($userId, $pdo = null)
    {
        $user = Cache::getCache(self::$cache, Config::getInstance()->getMemcachePrefix() . 'user_' . $userId);

        if ($user == false) {
            //user not in cache, refreshing cache
            $user = self::refreshCache($userId, $pdo);
        }
        return $user;
    }

    /**
     * Refresh Cache
     * @param type $userId
     * @param type $pdo
     * @return type
     */
    protected static function refreshCache($userId, $pdo)
    {
        $user = self::find($userId, $pdo);
        Cache::setCache(self::$cache, Config::getInstance()->getMemcachePrefix() . 'user_' . $userId, $user);
        return $user;
    }
}
