<?php
namespace Model;

(defined('APP_NAME')) or exit('Forbidden 403');

use Akaash\Core\Model\Base as BaseModel;

/**
 * User login log model.
 */
class LogUserLogin extends BaseModel
{
    /* Table Definition */
    const TABLE_NAME = "log_user_logins";
    const PRIMARY_KEY = 'login_log_id';
    const HAS_CREATED_AT = true;

    protected static $columnDefs = array(
      'login_log_id' => array(
        'type' => 'int',
        'json' => false
      ),
      'user_id' => array(
        'type' => 'int',
        'json' => false
      ),
      'session_id' => array(
        'type' => 'string',
        'json' => false
      ),
      'login_time' => array(
        'type' => 'int',
        'json' => false
      ),
      'created_at' => array(
        'type' => 'datetime',
        'json' => false
      ),
      'updated_at' => array(
        'type' => 'datetime',
        'json' => false
      )
    );

    /**
     * Log user session during login
     *
     * @param int $userId
     * @param string $sessionId
     * @param int $loginType
     * @param obj $pdo
     * @return bool
     */
    public static function logSession($userId, $sessionId, $pdo = null)
    {
        if (null === $pdo) {
            $pdo = Flight::pdo();
        }


        $userSession = new UserLoginSession();
        $userSession->user_id = $userId;
        $userSession->session_id = $sessionId;
        $userSession->login_time = time();

        $userSession->create($pdo);

        return true;
    }
}
