<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use flight\net\Request;
use System\Exception\AppException;
use System\Message\ResultCode;
use System\Config;
use View\Output;
use Helper\CommonUtil;
use System\Security;

/**
 * Description of BaseClass
 *
 * @author sabbir-hossain
 */
class BaseClass
{

    /**
     * User Authentication variable defined
     */
    const LOGIN_REQUIRED = false;

    /*
     * Authentication bypass flag for testing
     */
    const TEST_ENV = false;

    protected $get;
    protected $data;
    protected $method;
    protected $value;
    protected $response;
    protected $apiName;
    protected $pdo;
    protected $requestToken = null;
    protected $config = null;

//    protected $sessionId = NULL;
//    protected $userId = NULL;
//    protected $cacheUser = NULL;

    public function __construct(Request $request, $value, $apiName)
    {
        $this->get = $request->query;
        $this->data = $request->data;
        $this->value = $value;
        $this->method = $request->method;
        $this->apiName = $apiName;
        $this->config = Config::getInstance();

        // Get DB Connection Object
        $this->pdo = \Flight::pdo();
    }

    /**
     * Request processing execution.
     */
    public function process()
    {

        // Check and verify client request call / User session
        $this->filter();

        // Fetch and check GET query strings
        $this->validate();

        // Execute API Action Controller
        $response = $this->action();

        Output::response($response);
    }

    /**
     * Execute the individual processing of the action.
     *
     * @throws AppException
     */
    public function action()
    {
        throw new AppException(ResultCode::NOT_FOUND, 'Action script not found');
    }

    /**
     * Validate request parameters before processing
     */
    protected function validate()
    {
        // Write your validation code here
    }

    /**
     * Filter API request before processing
     *
     * @throws Exception
     */
    protected function filter()
    {
        // Write your filter code here
    }

    /**
     * Return parameter value from Post call.
     *
     * @param string  $name      name of the parameter
     * @param unknown $type      Type of the variable. "int", "bool", "string".
     * @param bool    $required  Value required
     * @param bool    $xss_clean XSS clean
     *
     * @return parameter value from POST Request
     * @throws AppException
     */
    protected function getInputPost($name, $type, $required = false, $xss_clean = false)
    {
        if (isset($this->data[$name])) {
            $var = $this->data[$name];
            if ('string' !== $type && '' === $var) {
                $var = null;
            }
        } else {
            $var = null;
        }

        if (true === $required && CommonUtil::notEmpty($var) === false) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, "{$name} is not set.");
        } elseif (!CommonUtil::isValidType($var, $type)) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of {$name} is not valid.");
        } else {
            return (true === $xss_clean) ? Security::xssClean($var) : $var;
        }
    }

    /**
     * Retrieve specific value from get query string
     *
     * @param string $name
     * @param string $type
     * @param bool $required
     * @param bool $xss_clean
     *
     * @return mixed Value from get query string
     * @throws AppException
     */
    protected function getInputQuery($name, $type = '', $required = false, $xss_clean = false)
    {
        if (isset($this->get[$name])) {
            $var = $this->get[$name];
            if ('string' != $type && '' === $var) {
                $var = null;
            }
        } else {
            $var = null;
        }

        if (true === $required && CommonUtil::notEmpty($var) === false) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, "{$name} is not set.");
        } elseif (!CommonUtil::isValidType($var, $type)) {
            throw new AppException(ResultCode::INVALID_REQUEST_PARAMETER, "The type of {$name} is not valid.");
        } else {
            return (true === $xss_clean) ? Security::xssClean($var) : $var;
        }
    }
}
