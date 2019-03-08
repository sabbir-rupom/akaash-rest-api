<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class Item_Add extends BaseClass {

    /**
     * User Login required or not
     */
    const LOGIN_REQUIRED = TRUE;

    protected $itemName;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();

        // Acquiring item name from json request
        $this->itemName = $this->getValueFromJSON('item_name', 'string', TRUE);
    }

    /**
     * Process API request
     */
    public function action() {
        
        $userItemObj = Model_UserItem::addUserItem($this->userId, $this->itemName, $this->pdo);
        
        if(empty($userItemObj->id)) {
            throw new System_Exception(ResultCode::DATABASE_ERROR, 'Failed to insert item in database!');
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => array(
                'user_item' => $userItemObj->toJsonHash(),
            ),
            'error' => []
        );
    }

}
