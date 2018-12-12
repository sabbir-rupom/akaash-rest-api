<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User Item model class.
 */
class Model_UserItem extends Model_BaseModel {

    /**
     * Table Name
     */
    const TABLE_NAME = "user_items";

    /**
     * Column Defination
     */
    protected static $columnDefs = array(
        'id' => array(
            'type' => 'int',
            'json' => TRUE
        ),
        'user_id' => array(
            'type' => 'int',
            'json' => FALSE
        ),
        'item_name' => array(
            'type' => 'string',
            'json' => TRUE
        ),
        'created_at' => array(
            'type' => 'string',
            'json' => FALSE
        ),
        'updated_at' => array(
            'type' => 'string',
            'json' => FALSE
        )
    );

    /**
     * Insert new unique item for session user
     *
     * @param int $userId
     * @param string $itemName
     * @param obj $pdo
     * @throws Exception_ApiException
     * @return obj
     */
    public static function addUserItem($userId, $itemName, $pdo = null) {
        if (null === $pdo) {
            $pdo = Flight::pdo();
        }

        $userItemObj = self::findBy(array('user_id' => $userId, 'item_name' => $itemName), $pdo);

        if (empty($userItemObj)) {
            $userItemObj = new Model_UserItem();
            $userItemObj->user_id = $userId;
            $userItemObj->item_name = $itemName;

            $userItemObj->create($pdo);
        } else {
            throw new Exception_ApiException(ResultCode::DATABASE_ERROR, 'Item already exist!');
        }

        return $userItemObj;
    }

}
