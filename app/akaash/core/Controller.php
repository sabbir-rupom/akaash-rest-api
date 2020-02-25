<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Akaash - RESTful API Template
 * Developed in PHP based on flight micro-framework
 *
 * ANYONE IN THE DEVELOPER COMMUNITY MAY USE THIS PROJECT FREELY
 * FOR THEIR OWN DEVELOPMENT SELF-LEARNING OR DEVELOPMENT or LIVE PROJECT
 *
 * @category    Rest API Template
 * @author      Sabbir Hossain Rupom <sabbir.hossain.rupom@hotmail.com>
 * @license     http://www.opensource.org/licenses/mit-license.php ( MIT License )
 * @version     2.0.0
 */

namespace Akaash\Core;

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
     * @param type $name API name
     */
    public static function initAPI($name)
    {
        Initiate::makeCall(new Request(), [
            'name' => $name,
            'group' => null,
            'value' => null
        ]);
    }

    /**
     * Initialize API Application from Group
     * @param type $group Group name
     * @param type $name API name
     */
    public static function initGroupAPI($group, $name)
    {
        Initiate::makeCall(new Request(), [
            'name' => $name,
            'group' => $group,
            'value' => null
        ]);
    }

    /**
     * Initialize API Application from Group with requested value as query-param
     * @param type $group Group name
     * @param type $name API name
     * @param type $value Query parameter
     */
    public static function initGroupAPIwithParam($group, $name, $value)
    {
        Initiate::makeCall(new Request(), [
            'name' => $name,
            'group' => $group,
            'value' => empty($value) ? null : $value
        ]);
    }
}
