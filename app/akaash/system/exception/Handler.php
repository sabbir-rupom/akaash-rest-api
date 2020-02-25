<?php

namespace Akaash\System\Exception;

use flight\net\Request;
use Akaash\Helper\DateUtil;
use Akaash\System\Log\Logger;
use Akaash\View\Output;
use Akaash\Config as Config;

class Handler
{
    public static function handle(
        Request $request,
        Config $config,
        int $resultCode,
        int $httpStatusCode,
        string $title,
        string $message,
        string $apiName,
        array $other
    ) {
        header("HTTP/1.1 " . $httpStatusCode . " " . $title);

        Logger::create(
            $request,
            $config,
            "{$apiName} ({$resultCode}): {$message}",
            'error'
        );

        /**
         * Prepare error message data
         */
        $errorData = ['title' => $title, 'msg' => $message] + $other;

        /**
         * Call output class to generate JSON data
         */
        Output::response([
          'result_code' => $resultCode,
          'time' => DateUtil::getToday(),
          'error' => $errorData
        ]);


//                Logger::write(array(
//                  'message' => $apiName . ' (' . $this->resultCode . '): ' . $errMsg,
//                  'file_name' => $e->getFile(),
//                  'line_number' => $e->getLine()
//                ));
    }
}
