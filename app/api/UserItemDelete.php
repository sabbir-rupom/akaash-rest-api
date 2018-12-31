<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class UserItemDelete extends BaseClass {

    /**
     * User Login required or not
     */
    const LOGIN_REQUIRED = TRUE; 

    protected $item_id;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();

        // Acquiring item id from json request
        $this->item_id = $this->getValueFromJSON('item_id', 'int', TRUE);
    }

    /**
     * Process API request
     */
    public function action() {
        
        $userItemObj = Model_UserItem::findBy(array('user_id' => $this->userId, 'id' => $this->item_id), $this->pdo, TRUE);
        
        
        if(empty($userItemObj)) {
            throw new Exception_ApiException(ResultCode::NOT_FOUND, 'User item not found in database!');
        }
        
        /*
         * Delete user item
         */
        $userItemObj->delete($this->pdo);

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => [],
            'error' => []
        );
    }

}
