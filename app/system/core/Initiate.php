<?php

namespace Core;

use flight\net\Request as Request;
use Helper\CommonUtil as Common;
use System\Exception\AppException as AppException;
use System\Message\ResultCode as ResultCode;
use System\Config as Config;

class Initiate
{
    protected static $apiName;
    protected static $queryValue;

    /**
     * Initialize application
     *
     * @param array $arrayParams Array parameters for initializing API class
     */
    public static function makeCall(Request $request, array $properties)
    {
        try {
            self::prepare($properties);

            // Call Base Controller to Retrieve Instance of API Controller
            $action = new self::$apiName($request, self::$queryValue, self::$apiName);
            $action->process();
        } catch (AppException $e) {
            /*
             * Handle all error / exception messages
             */
            $e->generate($request, Config::getInstance(), self::$apiName);
        }
    }

    /**
     * Prepare all request parameters with appropriate class / values
     *
     * @param array $properties API request parameters
     * @param string $temp
     * @throws AppException
     */
    public static function prepare(array $properties)
    {

        // prepare api controller from request url call
        self::$apiName = Common::prepareApiClass($properties['name'], $properties['group']);

        // Check if requested API controller exist in app
        if (!class_exists(self::$apiName)) {
            if (!empty($properties['group'])) {
                self::$apiName = Common::prepareApiClass($properties['group']);

                if (!class_exists(self::$apiName)) {
                    throw new AppException(ResultCode::NOT_FOUND, "No such api: " . self::$apiName);
                } else {
                    self::$queryValue = empty($properties['value']) ? $properties['name'] : [
                      $properties['name'], $properties['value']
                    ];
                }
            } else {
                throw new AppException(ResultCode::NOT_FOUND, "No such api: " . self::$apiName);
            }
        } else {
            self::$queryValue = $properties['value'];
        }
    }
}
