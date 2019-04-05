<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class UserItemAdd extends BaseClass {
    /**
     * User Login required or not.
     */
    const LOGIN_REQUIRED = true;

    protected $item_name;

    /**
     * Validation of request.
     */
    public function validate() {
        parent::validate();

        // Acquiring item name from json request
        $this->item_name = $this->getValueFromJSON('item_name', 'string', true);
    }

    /**
     * Process API request.
     */
    public function action() {
        $userItemObj = Model_UserItem::addUserItem($this->userId, $this->item_name, $this->pdo);

        if (empty($userItemObj->id)) {
            throw new System_ApiException(ResultCode::DATABASE_ERROR, 'Failed to insert item in database!');
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => array(
                'user_item' => $userItemObj->toJsonHash(),
            ),
            'error' => array(),
        );
    }
}
