<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * User login log model.
 */
class Model_UserLoginSession extends Model_BaseModel
{
    const TABLE_NAME = 'user_login_sessions';

    // Table column definitions
    protected static $columnsOnDB = [
        'id' => [
            'type' => 'int',
            'json' => false,
        ],
        'user_id' => [
            'type' => 'int',
            'json' => false,
        ],
        'session_id' => [
            'type' => 'string',
            'json' => false,
        ],
        'login_type' => [
            'type' => 'int',
            'json' => false,
        ],
        'login_count' => [
            'type' => 'int',
            'json' => false,
        ],
        'time' => [
            'type' => 'int',
            'json' => false,
        ],
        'created_at' => [
            'type' => 'datetime',
            'json' => false,
        ],
        'updated_at' => [
            'type' => 'datetime',
            'json' => false,
        ],
    ];

    /**
     * Insert / Update user session for each day.
     *
     * @param int    $userId    User ID
     * @param string $sessionId User's session ID
     * @param int    $loginType Type of login
     * @param obj    $pdo       DB connection Object PDO
     *
     * @throws System_ApiException
     *
     * @return bool Success [TRUE]
     */
    public static function updateSession($userId, $sessionId, $loginType, $pdo = null)
    {
        if (null === $pdo) {
            $pdo = Flight::pdo();
        }

        $userSession = self::findBy(['user_id' => $userId, 'DATE(created_at)' => Common_DateUtil::getToday('Y-m-d')], $pdo, true);

        if (empty($userSession)) {
            $userSession = new Model_UserLoginSession();
            $userSession->user_id = $userId;
            $userSession->session_id = $sessionId;
            $userSession->login_type = $loginType;
            $userSession->time = time();

            $userSession->create($pdo);
        } else {
            $userSession->session_id = $sessionId;
            $userSession->login_type = $loginType;
            $userSession->login_count = intval($userSession->login_count) + 1;
            $userSession->time = time();

            $userSession->update($pdo);
        }

        return true;
    }
}
