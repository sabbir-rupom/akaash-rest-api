<?php

namespace API\Filter;

use System\Exception\AppException;
use System\Config;
use \Library\JwtToken;
use System\Message\ResultCode;

class Authorization implements MethodTemplate
{

    /**
     *  0 = do not check request token
     *  1 = check and validate request token
     *
     * @property int $mode
     */
    private $mode;
    private $token;
    private $result;

    /**
     * Check server maintenance status
     */
    public function check()
    {
        $this->mode = Config::getInstance()->checkRequestTokenFlag();

        $this->validate();
    }

    /**
     * Validate and throw message exception on maintenance
     *
     * @throws AppException
     */
    public function validate()
    {
        if ($this->mode > 0) {
            foreach (getallheaders() as $key => $value) {
                if (Config::getInstance()->getRequestTokenHeaderKey() === strtoupper($key)) {
                    $this->token = $value;
                    break;
                }
            }

            $this->result = JwtToken::verifyToken($this->token, Config::getInstance()->getRequestTokenSecret());

            if ($this->result['success']) {
                \Flight::app()->set('token_payload', $this->result['data']);
            } else {
                throw new AppException(ResultCode::INVALID_REQUEST_TOKEN, empty($this->result['msg']) ? 'Invalid token' : $this->result['msg']);
            }
        }
        return;
    }
}
