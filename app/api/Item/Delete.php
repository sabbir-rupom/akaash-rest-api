<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class Item_Delete extends BaseClass {

    /**
     * User Login required or not
     */
    const LOGIN_REQUIRED = TRUE; 

    protected $itemId;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();

        // Acquiring item id from json request
        $this->itemId = $this->getValueFromJSON('item_id', 'int', TRUE);
    }

    /**
     * Process API request
     */
    public function action() {
        
        $userItemObj = Model_UserItem::findBy(array('user_id' => $this->userId, 'id' => $this->itemId), $this->pdo, TRUE);
        
        
        if(empty($userItemObj)) {
            throw new AppException(ResultCode::NOT_FOUND, 'User does not have this item!');
        }
        
        /*
         * Delete user item
         */
        $userItemObj->delete($this->pdo);

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Helper_DateUtil::getToday(),
            'data' => [
                'msg' => 'Item ID ' . $this->itemId  . ' has been deleted'
                ],
            'error' => []
        );
    }

}
