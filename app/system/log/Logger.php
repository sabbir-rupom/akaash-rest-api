<?php

namespace System\Log;

use System\Log\LoggerInterface as LoggerInterface;
use System\Config as Config;
use \flight\net\Request as Request;

final class Logger implements LoggerInterface {

    private $logTime;
    private $logFile;
    private $logPath;
    private $logData;
    private $clientIp;
    private $logContent;

    public function __contruct() {
        $this->content = [];
    }

    public static function create(Request $request, Config $config, $data, string $type = ''): bool {
        if ($config->isLogEnable() == FALSE || empty($data)) {
            return false;
        }

        $logger = new self;

        $logger->clientIp = !empty($request->ip) ? $request->ip : 'localhost';
        $logger->logData = empty($data) ? '' : $data;
        $logger->logPath = $config->getAppLogPath() . DIRECTORY_SEPARATOR;
        $logger->logTime = date('D Y-m-d h:i:s A');
        $logger->logFile = (!empty($type) ? $type : 'api') . '_' . date('Ymd') . '.log';

        $logger->prepare();
        $logger->write();

        return true;
    }

    public function prepare() {
        $this->logContent = "[{$this->logTime}] [client: {$this->clientIp}] ";

        if (is_array($this->logData)) {
            //concatenate msg with time-frame
            foreach ($this->logData as $key => $msg) {
                if (is_array($msg) || is_object($msg)) {
                    $msg = json_encode($msg);
                }
                $this->logContent .= $key . ' : ' . $msg . "\r\n";
            }
        } else {   //concatenate msg with time-frame
            $this->logContent .= strval($this->logData) . "\r\n";
        }
    }

    public function write(): bool {
        if (!is_dir($this->logPath)) {
            return false;
        }
        if (($fp = fopen($this->logPath . $this->logFile, 'a+')) !== false) {
            if (flock($fp, LOCK_EX) === TRUE) {
                //write the info into the file
                fwrite($fp, $this->logContent);
                flock($fp, LOCK_UN);
            }

            //close handler
            fclose($fp);
        }

        return true;
    }

    public static function get(array $options): string {
        
    }

}
