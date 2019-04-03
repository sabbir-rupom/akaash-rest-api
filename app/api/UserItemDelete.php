<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * User data acquisition actions.
 */
class UserItemDelete extends BaseClass
{
    /**
     * User Login required or not.
     */
    const LOGIN_REQUIRED = true;

    protected $item_id;

    /**
     * Validation of request.
     */
    public function validate()
    {
        parent::validate();

        // Acquiring item id from json request
        $this->item_id = $this->getValueFromJSON('item_id', 'int', true);
    }

    /**
     * Process API request.
     */
    public function action()
    {
        $userItemObj = Model_UserItem::findBy(['user_id' => $this->userId, 'id' => $this->item_id], $this->pdo, true);

        if (empty($userItemObj)) {
            throw new System_ApiException(ResultCode::NOT_FOUND, 'User does not have this item!');
        }

        // Delete user item
        $userItemObj->delete($this->pdo);

        return [
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_DateUtil::getToday(),
            'data' => ['msg' => 'Item ID '.$this->item_id.' has been deleted'],
            'error' => [],
        ];
    }
}
