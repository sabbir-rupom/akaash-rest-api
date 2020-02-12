<?php

(defined('APP_NAME')) or exit('Forbidden 403');

use flight\net\Request;
use System\Exception\AppException;
use System\Message\ResultCode;
use System\Config;
use View\Output;

/**
 * Description of BaseClass
 *
 * @property System\Config Config
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
        throw new ApiException(ResultCode::NOT_FOUND, 'Action script not found');
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
}
