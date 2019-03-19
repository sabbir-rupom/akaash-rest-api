<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

class Model_User extends Model_BaseModel {

    const MEMCACHED_EXPIRE = 3600; // Time to expire memcache; 1 hour
    const SESSION_DURATION_SEC = 3600; // 1 hour
    const SESSION_RESOLVE_DURATION_SEC = 0; // No limit

    /* Table Name */
    const TABLE_NAME = "users";

    protected static $columnDefs = array(
        'id' => array(
            'type' => 'int',
            'json' => true
        ),
        'user_name' => array(
            'type' => 'string',
            'json' => true
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
        'email' => array(
            'type' => 'string',
            'json' => true
        ),
        'password' => array(
            'type' => 'string',
            'json' => false
        ),
        'personal_info' => array(
            'type' => 'string',
            'json' => true
        ),
        'latitude' => array(
            'type' => 'float',
            'json' => true
        ),
        'longitude' => array(
            'type' => 'float',
            'json' => true
        ),
        'last_api_time' => array(
            'type' => 'string',
            'json' => false
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
     * @throws AppException
     */

    public function createUser($dataArray, $pdo = null) {

        // Get User data by uuid
        $user = Model_User::findBy(array('email' => $dataArray['email']), $pdo);


        // Checke User exist or not
        if (null === $user || empty($user)) {


            // User Data Collection
            $this->user_name = $dataArray['username'];
            $this->password = password_hash($dataArray['password'], PASSWORD_BCRYPT, array('cost' => 12));
            $this->email = $dataArray['email'];
            $this->personal_info = $dataArray['personal_info'];
            $this->first_name = $dataArray['firstname'];
            $this->last_name = $dataArray['lasttname'];
            $this->latitude = $dataArray['lat'];
            $this->longitude = $dataArray['long'];
            $this->gender = $dataArray['gender'];

            $this->last_api_time = Helper_DateUtil::getToday();

            $this->device_token = $dataArray['d_token'];
            $this->device_model = $dataArray['d_model'];

            $this->create($pdo);

        } else {
            throw new AppException(ResultCode::DATA_ALREADY_EXISTS, 'User already exist in database');
        }
        return $user;
    }

    /**
     * Update user's last active time
     * @param PDO $pdo
     */
    public function updateUserLastActiveTime($pdo = null) {
        if (property_exists($this, 'last_api_time')) {
            if (null === $pdo) {
                $pdo = Flight::pdo();
            }
            $this->last_api_time = Helper_DateUtil::getToday();
            $this->update($pdo);
        }
    }

    /**
     * Return from the session to get a user ID.
     */
//    public static function retrieveUserIdFromSession($sessionId) {
//        $sessionKey = Model_CacheKey::getUserSessionKey($sessionId);
//        $memcache = Config::getMemcachedClient();
//        $userId = $memcache->get($sessionKey);
//        return $userId;
//    }
    
    /**
     * Return from the session to get a user ID.
     */
    public static function retrieveSessionFromUserId($userId) {
        $sessionKey = Model_CacheKey::getUserSessionKey($userId);
        $memcache = Config::getMemcachedClient();
        $sessionId = $memcache->get($sessionKey);
        return $sessionId;
    }

    /**
     * Update the session ID, save the session to Memcached.
     */
    public function setSession() {
        session_regenerate_id();
        $sessionId = session_id();
        self::cacheSession($sessionId, $this->id);
        
        return $sessionId;
    }

    /**
     * Delete old session.
     */
    public function removeSessionFromUserId($userId) {
        $sessionKey = Model_CacheKey::getUserSessionKey($userId);
        $session = Config::getMemcachedClient();
        $session->remove($sessionKey);
    }

    /**
     * Save the user ID to Memcache to the session ID as a key.
     */
    public static function cacheSession($sessionId, $userId) {
        $sessionKey = Model_CacheKey::getUserSessionKey($userId);
        $memcache = Config::getMemcachedClient();
        $memcache->set($sessionKey, $sessionId, 0, self::SESSION_DURATION_SEC);
    }

    /**
     * Return an associative array for JSON.
     */
    public function toJsonHash($additionalData = array()) {
        $userId = intval($this->id);
        $hash = parent::toJsonHash();

        if ($hash['profile_image'] != '') {
            $hash['profile_image'] = SERVER_HOST . '/image/user-profile/' . $userId . '?ref=' . md5($hash['profile_image']);
        }

        if (!empty($additionalData)) {
            foreach ($additionalData as $key => $value) {
                $hash[$key] = $value;
            }
        }
        return $hash;
    }

    /**
     * Find User form cache
     * @param int $userId
     * @param PDO $pdo
     * @return Model_User
     */
    public static function cache_or_find($userId, $pdo = null) {
        $user = parent::getCache(Model_CacheKey::getUserKey($userId));
        
        if ($user == FALSE) {
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
    public static function refreshCache($userId, $pdo = null) {
        $user = self::find($userId, $pdo, TRUE);
        parent::setCache(Model_CacheKey::getUserKey($userId), $user);
        return $user;
    }

    /**
     * Before Update Deleting Cache
     * @param type $pdo
     */
    public function update($pdo = null, $cacheDelete = TRUE) {
        if ($cacheDelete) {
            parent::deleteCache($this->id);
        }
        parent::update($pdo);
    }
}
