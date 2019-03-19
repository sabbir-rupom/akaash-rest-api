<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class Item_Edit extends BaseClass {

    /**
     * User Login required or not
     */
    const LOGIN_REQUIRED = TRUE; 

    protected $itemName;
    protected $itemId;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();

        // Acquiring item information from json request
        $this->itemName = $this->getValueFromJSON('item_name', 'string', TRUE);
        $this->itemId = $this->getValueFromJSON('item_id', 'int', TRUE);
    }

    /**
     * Process API request
     */
    public function action() {
        
        $userItemObj = Model_UserItem::findBy(array('user_id' => $this->userId, 'id' => $this->itemId), $this->pdo, TRUE);
        
        if(empty($userItemObj)) {
            throw new AppException(ResultCode::NOT_FOUND, 'User item not found in database!');
        }
        
        /*
         * Update item information
         */
        $userItemObj->item_name = $this->itemName;
        $userItemObj->update($this->pdo);

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
