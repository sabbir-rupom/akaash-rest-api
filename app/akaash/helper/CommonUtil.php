<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Akaash\Helper;

/**
 * Common helper class
 */
class CommonUtil
{

    /**
     * Prepare API class name
     * @param string $name Api name
     * @param string $group Api Group name
     */
    public static function prepareApiClass($name, $group = '')
    {
        return empty($group) ? self::camelize($name)
            : (ucfirst($group) . (empty($name) ? '' : "_") . self::camelize($name));
    }

    /*
     * utf8 error correction from result array
     * @param array $mixed array of result from API
     * Correct all utf-8 related errors for proper JSON output
     */

    public static function utf8ize($mixed)
    {
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $mixed[$key] = self::utf8ize($value);
            }
        } elseif (is_string($mixed)) {
            return utf8_encode($mixed);
        }
        return $mixed;
    }

    /**
     * Convert a string to CamelCase
     *
     * @param string $str Text Column
     * @param boolean $ucfirst
     * @return CamelCase of string, underscore is removed
     */
    public static function camelize($str, $ucfirst = true)
    {
        if (stristr($str, '-')) {
            $elements = explode('-', $str);
        } else {
            $elements = explode('_', $str);
        }
        $capitalized = array();
        if (!$ucfirst) {
            $capitalized[] = array_shift($elements);
        }
        foreach ($elements as $element) {
            $capitalized[] = ucfirst(array_shift($elements));
        }
        return implode('', $capitalized);
    }

    /**
     * Check whether the value is an integer greater than or equal to 0
     *
     * @param mixed $var Value to be checked
     * @return boolean Check result
     */
    public static function isInt($var)
    {
        if (is_int($var)) {
            return true;
        }
        return preg_match("/^[0-9]+$/", $var) > 0;
    }

    /**
     * Returns the var_dump content of the specified object as a string.
     * Line breaks included in the character string are escaped (\ n -> \ \ n)
     * and returned in a state where it can be displayed on one line.
     *
     * @param mixed $object
     * @return string Var_dump content text column
     */
    public static function dump($object)
    {
        ob_start();
        var_dump($object);
        $dumpStdOut = ob_get_contents();
        ob_end_clean();
        $dumpStdOut = str_replace("\n", "\\n", $dumpStdOut);
        echo json_encode($dumpStdOut);
        exit;
    }

    /**
     * Convert any object into a JSON hash representation
     *
     * @param object Any object
     * @return array Conversion hash representation
     */
    public static function objToJsonHash($obj)
    {
        $str = json_encode($obj);
        $hash = json_decode($str, true);
        return $hash;
    }

    /**
     * Convert any of hash representation JSON to object
     *
     * @param array JSON hash representation
     * @return object Converted object representation
     */
    public static function objFromJsonHash($hash)
    {
        $str = json_encode($hash);
        $obj = json_decode($str, false);
        return $obj;
    }

    /**
     * Check the type of the value
     *
     * @param mixed $value value to validate
     * @param mixed $type expected type of value
     *
     * @return bool, TRUE If it is correct type , otherwise FALSE.
     * Value returns TRUE unconditionally if it is NULL.
     */
    public static function isValidType($value, $type)
    {
        $result = false;
        if (is_null($value)) {
            return true;
        }
        switch ($type) {
            case 'int':
                $result = self::isInt($value);

                break;
            case 'bool':
                $result = is_bool($value);

                break;
            case 'string':
                $result = is_string($value) || is_numeric($value);

                break;
            case 'float':
                $result = is_float($value) || self::isInt($value);

                break;
            case 'json':
                $result = self::isJSON($value);

                break;
            case 'binary':
                $result = is_binary($value);

                break;
            case 'array':
                $result = is_array($value);

                break;
            default:
                $result = true;
                break;
        }

        return $result;
    }

    /**
     * Check if a string is valid json
     *
     * @param string $str
     * @return bool
     */
    public static function isJSON($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }

    /**
     * Check if a value is empty or not
     *
     * @param mixed $var
     * @return boolean
     */
    public static function notEmpty(&$var)
    {
        if (isset($var) && ($var === '0' || $var === 0 || !empty($var))) {
            return true;
        }
        return false;
    }

    /**
     * Return the requested OS type
     *
     * @return unknown NULL
     */
    public static function getHttpRequestPlatformType()
    {
        preg_match("/iPhone|Android|iPad|iPod|webOS/", $_SERVER['HTTP_USER_AGENT'], $matches);
        $os = current($matches);

        $platformType = 0;

        switch ($os) {
            case 'iPhone':
            case 'iPad':
            case 'iPod':
                $platformType = PLATFORM_TYPE_IOS;
                break;
            case 'Android':
                $platformType = PLATFORM_TYPE_ANDROID;
                break;
            case 'webOS':
            default:
                $platformType = PLATFORM_TYPE_WEB;
                break;
        }

        return $platformType;
    }
}
