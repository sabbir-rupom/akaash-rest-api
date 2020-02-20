<?php

namespace System\Message;

interface MethodTemplate
{
    public static function getTitle(int $code);

    public static function getMessage(int $code);

    public static function getHTTPstatusCode(int $code);
}
