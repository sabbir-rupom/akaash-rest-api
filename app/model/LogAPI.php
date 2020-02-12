<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

namespace Model;

use System\Core\Model\Base as BaseModel;

/**
 * API log model class.
 */
class LogAPI extends BaseModel {

    /**
     * Table definitions
     */
    const TABLE_NAME = "log_api";
    
    const PRIMARY_KEY = "log_id";

    /**
     * Column Definitions
     */
    protected static $columnDefs = array(
        'log_id' => array(
            'type' => 'int',
            'json' => true
        ),
        'api_name' => array(
            'type' => 'int',
            'json' => true
        ),
        'request_data' => array(
            'type' => 'string',
            'json' => true
        ),
        'response' => array(
            'type' => 'string',
            'json' => true
        ),
        'method' => array(
            'type' => 'string',
            'json' => true
        )
    );

}




