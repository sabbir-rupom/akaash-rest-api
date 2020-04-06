<?php
namespace Akaash\View\Response;

(defined('APP_NAME')) or exit('Forbidden 403');

use Akaash\View\Response\Validate as Validate;

/**
 * Response Format class
 *
 * @author sabbir-hossain
 */

class Format
{
    public static function formatJson(array $data): string
    {
        $jsonResult = Validate::safeJsonEncode($data);
        /*
         * Flight JSON encode feature is not used
         * to avoid JSON_ERROR_UTF8
         * ------------- $arr = array_map('utf8_encode', $json_array);
         * ------------- Flight::json($arr);
         */

        return $jsonResult;
    }
}
