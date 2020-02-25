<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * API Exception Class
 *
 * @author sabbir-hossain
 */

namespace Akaash\System\Exception;

use flight\net\Request;
use Akaash\Config;
use Akaash\System\Message\ResultCode;
use Akaash\System\Exception\Handler;

class AppException extends \Exception
{
    public $resultCode;

    public function __construct($code = 0, $message = '', Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->resultCode = $code;
    }

    public function generate(Request $request, Config $config, string $apiName = '')
    {
        if ($this instanceof \PDOException) {
            $this->resultCode = ResultCode::DATABASE_ERROR;
        } elseif (!$this instanceof AppException) {
            $this->resultCode = ResultCode::UNKNOWN_ERROR;
        }

        Handler::handle(
            $request,
            $config,
            $this->resultCode,
            ResultCode::getHTTPstatusCode($this->resultCode),
            ResultCode::getTitle($this->resultCode),
            empty($this->getMessage()) ? ResultCode::getMessage($this->resultCode) : $this->getMessage(),
            $apiName,
            $config->isErrorDump() ? [
              'code' => $this->getCode(),
              'file' => $this->getFile(),
              'line' => $this->getLine(),
            ] : []
        );
    }
}
