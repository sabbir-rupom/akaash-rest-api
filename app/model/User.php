<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Model;

use System\Core\Model\Base as BaseModel;
use System\Core\Model\Cache;
use System\Config;

class User extends BaseModel
{
    /* Table Name */
    const TABLE_NAME = "users";

    public static $cache = null;
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
            Cache::deleteCache(Config::getInstance()->getMemcachePrefix() . 'user_' . $this->user_id);
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
    public static function cacheUserSession($userObj)
    {
        session_regenerate_id();
        $sessionId = session_id();
        if (empty(self::$cache)) {
            self::$cache = $this->config->cacheService();
        }
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
        Cache::addCache(self::$cache, $sessionKey, $sessionId);

        $userKey = Config::getInstance()->getMemcachePrefix() . 'user_' . $userId;
        Cache::setCache(self::$cache, $userKey, $userObj);

        return $sessionId;
    }

    /**
     * Delete old session.
     */
    public static function removeSessionFromUserId($userId)
    {
        if (empty(self::$cache)) {
            self::$cache = $this->config->cacheService();
        }
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
        Cache::deleteCache(self::$cache, $sessionKey);
    }

    /**
     * Return from the session to get a user ID.
     */
    public static function retrieveSessionFromUserId($userId)
    {
        if (empty(self::$cache)) {
            self::$cache = $this->config->cacheService();
        }
        $sessionKey = Config::getInstance()->getMemcachePrefix() . 'user_ses_' . $userId;
        return Cache::getCache(self::$cache, $sessionKey);
    }
    
    /**
     * Find User form cache
     * @param int $userId
     * @param PDO $pdo
     * @return Model_User
     */
    public static function cache_or_find($userId, $pdo = null)
    {
        $user = Cache::getCache($cache, Config::getInstance()->getMemcachePrefix() . 'user_' . $userId);

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
    public static function refreshCache($userId, $pdo)
    {
        $user = self::find($userId, $pdo);
        Cache::setCache(Config::getInstance()->getMemcachePrefix() . 'user_' . $userId, $user);
        return $user;
    }
}
