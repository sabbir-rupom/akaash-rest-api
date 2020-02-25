<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Model;

use System\Core\Model\Base as BaseModel;

/**
 * Item model class.
 */
class Item extends BaseModel
{

    /* Table Definition */
    const TABLE_NAME = "items";
    const PRIMARY_KEY = 'item_id';

    /**
     * Column Defination
     */
    protected static $columnDefs = array(
        'item_id' => array(
            'type' => 'int',
            'json' => true
        ),
        'item_name' => array(
            'type' => 'string',
            'json' => true
        ),
        'created_at' => array(
            'type' => 'string',
            'json' => false
        ),
        'updated_at' => array(
            'type' => 'string',
            'json' => false
        )
    );
}
