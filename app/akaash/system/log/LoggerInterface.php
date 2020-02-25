<?php

namespace Akaash\System\Log;

use \flight\net\Request;
use Akaash\Config;

interface LoggerInterface
{
    public static function create(Request $request, Config $config, $data, string $type = ''): Logger;

    public function prepare();

    public function write();

    public function get(array $options): string;
}
