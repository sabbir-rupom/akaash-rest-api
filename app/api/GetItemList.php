<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Get all other registered user list
 */
class GetItemList extends BaseClass {

    /**
     * Login required or not
     */
    const LOGIN_REQUIRED = TRUE;

    protected $itemName;
    
    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();
        
        $this->itemName = $this->getValueFromJSON('item_name', 'string');
    }

    /**
     * Process API request
     */
    public function action() {
        /*
         * Find items from database
         */
        $itemsObj = Model_UserItem::getAllItems($this->itemName, $this->pdo);


        if (empty($itemsObj)) {
            throw new Exception_ApiException(ResultCode::NOT_FOUND, 'No items available as user item');
        }

        // Initialize empty array
        $items = [];

        foreach ($itemsObj as $key => $val) {
            $items[$key]['name'] = $val->item_name;
            $items[$key]['count'] = (int) $val->count;
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_Util_DateUtil::getToday(),
            'data' => array(
                'items' => $items,
                'user_info' => $this->cache_user->toJsonHash()
            ),
            'error' => []
        );
    }

}
