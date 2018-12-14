<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Get all other registered user list
 */
class GetOtherUsers extends BaseClass {

    /**
     * Login required or not
     */
    const LOGIN_REQUIRED = TRUE;

    protected $offset;
    protected $limit;
    protected $sortOrder;

    /**
     * Validation of request
     */
    public function validate() {
        parent::validate();
        $this->offset = $this->getValueFromJSON('offset', 'int');
        if (empty($this->offset) || $this->offset < 0) {
            $this->offset = 0;
        }
        $this->limit = $this->getValueFromJSON('limit', 'int');
        if (empty($this->limit) || $this->limit <= 0) {
            $this->limit = 5;
        }
        $this->sortOrder = strtoupper($this->getValueFromJSON('sort_order', 'string'));
        if ($this->sortOrder != 'DESC') {
            $this->sortOrder = 'ASC';
        }
    }

    /**
     * Process API request
     */
    public function action() {
        /*
         * Find all users from database
         */
        $allUsersObj = Model_User::findAllBy(array('id !' => $this->userId), $this->sortOrder, array('limit' => $this->limit, 'offset' => $this->offset), $this->pdo);


        if (empty($allUsersObj)) {
            throw new Exception_ApiException(ResultCode::NOT_FOUND, 'Session user not found');
        }

        // Initialize empty array
        $userArray = $userItems = [];

        foreach ($allUsersObj as $key => $user) {
            $userID = (int) $user->id;
            $userItemsObj = Model_UserItem::findAllBy(array('user_id' => $userID), null, null, $this->pdo);
            if (!empty($userItemsObj)) {
                foreach ($userItemsObj as $item) {
                    $userItems[] = $item->toJsonHash();
                }
            }
            
            $userArray[$key] = $user->toJsonHash();
            $userArray[$key]['items'] = $userItems;
            $userItems = [];
        }

        return array(
            'result_code' => ResultCode::SUCCESS,
            'time' => Common_Util_DateUtil::getToday(),
            'data' => array(
                'other_users' => $userArray,
                'user_info' => $this->cache_user->toJsonHash()
            ),
            'error' => []
        );
    }

}
