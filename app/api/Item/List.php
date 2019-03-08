<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Get all other registered user list
 */
class Item_List extends BaseClass {

    /**
     * Login required or not
     */
    const LOGIN_REQUIRED = TRUE;

    protected $itemName;
    protected $targetUserId;
    
    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();
        
        $this->itemName = $this->getValueFromJSON('item_name', 'string');
        
        /*
         *  If a user ID is specified through GET / Query string
         */
        $this->targetUserId = $this->getValueFromQuery('user_id', 'int');
    }

    /**
     * Process API request
     */
    public function action() {
        /*
         * Find items from database
         */
        $itemsObj = Model_UserItem::getAllItems($this->itemName, $this->targetUserId, $this->pdo);


        if (empty($itemsObj)) {
            throw new System_Exception(ResultCode::NOT_FOUND, 'No items available as user item');
        }

        // Initialize empty array
        $items = [];

        foreach ($itemsObj as $key => $val) {
            $items[$key]['name'] = $val->item_name;
            $items[$key]['count'] = (int) $val->count;
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => array(
                'items' => $items,
                'user_info' => $this->cacheUser->toJsonHash()
            ),
            'error' => []
        );
    }

}
