<?php
namespace Akaash\System\Log;

use \flight\net\Request;
use Akaash\System\Log\LoggerInterface;
use Akaash\Config;

final class Logger implements LoggerInterface
{
    private $logTime;
    private $logFile;
    private $logPath;
    private $logData;
    private $clientIp;
    private $logContent;
    public $lastLine;

    public function __contruct()
    {
        $this->content = [];
    }

    public static function create(Request $request, Config $config, $data, string $type = ''): Logger
    {
        if ($config->isLogEnable() == false || empty($data)) {
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

        return $logger;
    }

    public function prepare()
    {
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

    public function write(): bool
    {
        if (!is_dir($this->logPath)) {
            return false;
        }
        if (($fp = fopen($this->logPath . $this->logFile, 'a+')) !== false) {
            if (flock($fp, LOCK_EX) === true) {
                //write the info into the file
                fwrite($fp, $this->logContent);
                flock($fp, LOCK_UN);
            }

            //close handler
            fclose($fp);
        }

        $this->lastLine = $this->logContent;

        return true;
    }

    /**
     * Fetch log data
     *
     * @param array $options | key parameters are
     *              [ line-break => text line break delimiter ]
     *              [ line-num => First n number of lines to return  ]
     *              [ date => date string in php date('Ymd') format  ]
     * @return string
     */
    public function get(array $options): string
    {
        $this->logContent = '';
        $this->logFile = (isset($options['type']) && !empty($options['type']) ? $options['type'] : 'api')
            . '_'
            . (isset($options['date']) ? $options['date'] : date('Ymd')) . '.log';

        if (file_exists($this->logPath . $this->logFile)) {
            $fh = fopen($this->logPath . $this->logFile, 'r');
            $c = 1;

            while ($line = fgets($fh)) {
                $this->logContent .= $line . (isset($options['line-break']) ? $options['line-break'] : PHP_EOL);
                if (isset($options['line-num']) && $c >= $options['line-num']) {
                    break;
                }
                $c++;
            }
            fclose($fh);
        }

        return str_replace(array("\n", "\t", "\r", "\r\n"), '', $this->logContent);
    }
}
