<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Core;

use \flight\net\Request;

/**
 * Controller for application
 *
 * @author sabbir-hossain
 */
class Controller
{

    /**
     * Initialize API application
     * @param type $name Api name
     */
    public static function initAPI($name)
    {
        Initiate::makeCall(
            new Request(),
            [
          'name' => $name,
          'group' => null,
          'value' => null
        ]
        );
    }

    /**
     * Initialize API Application from Group
     * @param type $group Group name
     * @param type $name Api name
     */
    public static function initGroupAPI($group, $name)
    {
        Initiate::makeCall(
            new Request(),
            [
          'name' => $name,
          'group' => $group,
          'value' => null
        ]
        );
    }

    /**
     * Initialize API Application from Group with requested value as query-param
     * @param type $group Group name
     * @param type $name Api name
     * @param type $value Query parameter
     */
    public static function initGroupAPIwithParam($group, $name, $value)
    {
        Initiate::makeCall(
            new Request(),
            [
          'name' => $name,
          'group' => $group,
          'value' => empty($value) ? null : $value
        ]
        );
    }
}
