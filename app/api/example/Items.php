<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use System\Message\ResultCode;
use Model\Item as ItemModel;
use Helper\DateUtil;

/**
 * Sample API Example: Get Item List
 */
class Example_Items extends BaseClass
{

    /**
     * Login required or not.
     */
    const LOGIN_REQUIRED = true;

    private $_itemId;
    private $_itemName;
    private $_itemList;

    /**
     * Validation of request.
     */
    public function validate()
    {
        parent::validate();

        $this->_itemId = $this->getInputPost('item_id', 'int');
        $this->_itemName = $this->getInputPost('item_name', 'string');

        $this->_itemList = [];
    }

    /**
     * Processing API script execution.
     */
    public function action()
    {
        $filter = [];
        if (!empty($this->_itemId) && $this->_itemId > 0) {
            $filter['item_id'] = $this->_itemId;
        }
        if (!empty($this->_itemName)) {
            $filter['item_name like'] = $this->_itemName;
        }
        
        $itemResult = ItemModel::findAllBy($filter, ['item_name' => 'ASC'], null, $this->pdo);

        if (!empty($itemResult)) {
            foreach ($itemResult as $item) {
                $this->_itemList[] = $item->toJsonHash();
            }
        }

        return [
          'result_code' => ResultCode::SUCCESS,
          'time' => DateUtil::getToday(),
          'data' => [
            'items' => $this->_itemList
          ],
          'error' => []
        ];
    }
}
