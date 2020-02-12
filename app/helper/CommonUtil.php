<?php

(defined('APP_NAME')) or exit('Forbidden 403');

namespace Helper;

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
        return empty($group) ? self::camelize($name) : ucfirst($group) . (empty($name) ? '' : "_") . self::camelize($name);
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
     * Line breaks included in the character string are escaped (\ n -> \ \ n) and returned in a state where it can be displayed on one line.
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
     * String parameter sent with HTTP POST
     * @return string
     */
    public static function getPostStringParameter()
    {
        $handle = fopen('php://input', 'r');
        $string = fgets($handle);

        fclose($handle);

        return $string;
    }

    /**
     * Return the requested OS type
     *
     * @return unknown NULL
     */
    public static function getHttpRequestPlatformType()
    {

        // Unable to acquire OS type None
        if (false == array_key_exists('platform_type', $_GET)) {
            if (true == array_key_exists('client_type', $_GET)) {
                $client = intval($_GET['client_type']);
                if ($client === 2) {
                    return Const_Application::PLATFORM_TYPE_ANDROID;
                }
            }
            return Const_Application::PLATFORM_TYPE_NONE;
        }

        $platformType = $_GET['platform_type'];
        // Not applicable OS type
        if (Const_Application::PLATFORM_TYPE_IOS != $platformType &&
            Const_Application::PLATFORM_TYPE_ANDROID != $platformType) {
            return Const_Application::PLATFORM_TYPE_NONE;
        }

        return $platformType;
    }
}

//
///**
// * The default class of JSON of premise object。
// *
// * Operational policies and methods of DTO put in here as an interim implementation until the firm.
// */
//class Jsonizable {
//
//    public function toJsonHash() {
//        return Utils::objToJsonHash($this);
//    }
//
//}
