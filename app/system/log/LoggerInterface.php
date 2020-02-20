<?php

namespace System\Log;

use System\Config as Config;
use \flight\net\Request as Request;

interface LoggerInterface
{
    public static function create(Request $request, Config $config, $data, string $type = ''): Logger;

    public function prepare();

    public function write();

    public function get(array $options): string;
}
