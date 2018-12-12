<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class UserItemUpdate extends BaseClass {

    /**
     * User Login required or not
     */
    const LOGIN_REQUIRED = TRUE;

    protected $item_name;
    protected $item_id;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();

        // Acquiring item information from json request
        $this->item_name = $this->getValueFromJSON('item_name', 'string', TRUE);
        $this->item_id = $this->getValueFromJSON('item_id', 'string', TRUE);
    }

    /**
     * Process API request
     */
    public function action() {
        
        $userItemObj = self::findBy(array('user_id' => $this->userId, 'id' => $this->item_id), $pdo, TRUE);
        
        if(empty($userItemObj)) {
            throw new Exception_ApiException(ResultCode::NOT_FOUND_404, 'Item not found in database!');
        }
        
        $userItemObj->item_name = $this->item_name;
        $userItemObj->update($this->pdo);

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_Util_DateUtil::getToday(),
            'data' => array(
                'user_item' => $userItemObj->toJsonHash(),
            ),
            'error' => []
        );
    }

}
