<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * KVS interface
 */
interface Common_Kvs_ClientInterface {

    const HEX_CHAR_SPACE = 0x20;
    const HEX_CHAR_DEL = 0x7F;

    /**
     * Acquire data with the specified key
     */
    function get($key);

    /**
     * Save the data with the specified key
     */
    function put($key, $value, $limit);

    /**
     * Delete the data with the specified key
     */
    function remove($key);

    /**
     * Automatically generate unique keys and save data
     *
     * @param mixed $value
     */
    function add($value, $limit);

    /**
     * Returns a key array matching with a forward match.
     *
     * @param string prefix
     * @param string limit
     */
    function getPrefixKeyArray($prefix, $limit);
}
