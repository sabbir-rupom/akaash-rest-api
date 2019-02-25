<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Utility helper class
 */
class Common_Utils {

    /**
     * Convert a string to CamelCase
     *
     * @param string $str Text Column
     * @param boolean $ucfirst
     * @return CamelCase of string, underscore is removed
     */
    public static function camelize($str, $ucfirst = TRUE) {
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
    public static function isInt($var) {
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
    public static function dump($object) {
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
    public static function objToJsonHash($obj) {
        $str = json_encode($obj);
        $hash = json_decode($str, TRUE);
        return $hash;
    }

    /**
     * Convert any of hash representation JSON to object
     *
     * @param array JSON hash representation
     * @return object Converted object representation
     */
    public static function objFromJsonHash($hash) {
        $str = json_encode($hash);
        $obj = json_decode($str, FALSE);
        return $obj;
    }

    /**
     * String parameter sent with HTTP POST
     * @return string
     */
    public static function getPostStringParameter() {
        $handle = fopen('php://input', 'r');
        $string = fgets($handle);

        fclose($handle);

        return $string;
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
