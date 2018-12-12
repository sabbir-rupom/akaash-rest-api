<?php
/**
 * 配列に関する処理をまとめたユーティリティクラス.
 *
 */
class Common_Util_ArrayUtil
{
    /**
     * 配列の要素を指定し、データを取得する。
     *
     * 取得できる場合はkeyの中に含まれるデータを取得する。
     *
     * @param string $key
     * @param array $array
     * @param unknown_type $defaultValue
     */
    public static function getArrayValue($key, $array, $defaultValue = null)
    {
        if (!array_key_exists($key, $array)) {

            if (!is_null($defaultValue)) {
                return $defaultValue;
            }

            return null;
        }
        return $array[$key];
    }

    public static function searchPrefixValue($prefix, array $array)
    {
        foreach ($array as $key => $value) {
            if (strpos($value, $prefix) !== false) {
                return $key;
            }
        }
        return false;
    }

    /**
     * 配列のインデックスを0から振りなおす。
     * value値にnullがセットされている場合、配列から削除される。
     *
     * @access public
     * @param array $arrayData 対象配列
     */
    public static function trimArray($arrayData)
    {
        // 対象配列が空かnullだったらそのまま返す
        if (empty($arrayData)) {
            return $arrayData;
        }

        $returnArray = array();

        reset($arrayData);

        while (list($key, $value) = each($arrayData)) {
            if ($value !== null) {
                $returnArray[] = $value;
            }
        }

        return $returnArray;
    }

    /**
     * ベースとなる配列の指定されたインデックスに要素を追加する.
     *
     * @param array $array
     * @param array $element
     * @param int $index
     * @return array
     */
    public static function add(array &$array, array $element, $index = null)
    {

        if ($index === null) {
            $index = count($array);
        }

        array_splice($array, $index, 0, $element);

        return true;
    }

}
