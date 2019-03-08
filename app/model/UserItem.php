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
     * @throws System_Exception
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
            throw new System_ApiException(ResultCode::DATABASE_ERROR, 'Item already exist!');
        }

        return $userItemObj;
    }

    /**
     * Get all item list available in database
     * @param string $itemName Item name to be searched
     * @param string $userId User ID to be searched
     * @param obj $pdo
     * @return array $result Array of item list
     */
    public static function getAllItems($itemName = '', $userId = null, $pdo = null) {
        if (null === $pdo) {
            $pdo = Flight::pdo();
        }
        $sql = "SELECT item_name, count(item_name) AS count FROM " . self::TABLE_NAME . " WHERE ";

        list($condition, $values) = self::constructQueryCondition(
                        array(
                            array('item_name', $itemName, 'like'), 
                            array('user_id', $userId, '=')
                        )
        );

        $sql .= $condition . " GROUP BY item_name ORDER BY item_name ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);


        return $result;
    }

}
