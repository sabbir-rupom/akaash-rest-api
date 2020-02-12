<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

namespace Model;

/**
 * User login log model.
 */
class UserLoginSession extends BaseModel {

    const TABLE_NAME = "user_login_sessions";

    protected static $columnDefs = array(
        'id' => array(
            'type' => 'int',
            'json' => FALSE
        ),
        'user_id' => array(
            'type' => 'int',
            'json' => FALSE
        ),
        'session_id' => array(
            'type' => 'string',
            'json' => FALSE
        ),
        'login_type' => array(
            'type' => 'int',
            'json' => FALSE
        ),
        'login_count' => array(
            'type' => 'int',
            'json' => FALSE
        ),
        'time' => array(
            'type' => 'int',
            'json' => FALSE
        ),
        'created_at' => array(
            'type' => 'datetime',
            'json' => FALSE
        ),
        'updated_at' => array(
            'type' => 'datetime',
            'json' => FALSE
        )
    );

    /**
     * Insert / Update user session for each day
     *
     * @param int $userId
     * @param string $sessionId
     * @param int $loginType
     * @param obj $pdo
     * @throws System_ApiException
     * @return bool
     */
    public static function updateSession($userId, $sessionId, $loginType, $pdo = null) {
        if (null === $pdo) {
            $pdo = Flight::pdo();
        }

        $userSession = self::findBy(array('user_id' => $userId, 'DATE(created_at)' => Common_DateUtil::getToday('Y-m-d')), $pdo, TRUE);

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

        return TRUE;
    }

}
