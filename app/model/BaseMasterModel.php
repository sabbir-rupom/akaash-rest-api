<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Base class of the master model
 */
abstract class Model_BaseMasterModel extends Model_BaseModel {

    /**
     * Memcached Validity period
     */
    const MEMCACHED_EXPIRE = 1800; // 30分

    /**
     * Master Data Type
     * 1 : All
     * 2 : iOS only
     * 3 : Android only
     */
    const MASTER_DATA_TYPE_NONE = 0;
    const MASTER_DATA_TYPE_ALL = 1;
    const MASTER_DATA_TYPE_IOS = 2;
    const MASTER_DATA_TYPE_ANDROID = 3;

    /**
     * To get the data for the specified ID from Memcache.
     * If it's not registered to Memcache, it is set to Memcache to retrieve from the database.
     *
     * @param mixed $id ID
     * @return Model object.
     */
    public static function get($id, $pdo = null) {
        $key = static::getKey($id);
        // To connect to Memcached, to get the value.
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $value = $memcache->get($key);
        if (FALSE === $value) {
            // If the value has been set to Memcached, it is set to Memcached to retrieve from the database.
            $value = self::find($id, $pdo);
            if ($value) {
                $memcache->set($key, $value, 0, static::MEMCACHED_EXPIRE);
            }
        }
        return $value;
    }

    /**
     * To get all the data from Memcache.
     * If it's not registered to Memcache, it is set to Memcache to retrieve from the database.
     *
     * @return Array of model objects.
     */
    public static function getAll() {
        $key = static::getAllKey();
        // To connect to Memcached, to get the value.
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $value = $memcache->get($key);
        if (FALSE === $value) {
            // If the value has been set to Memcached, it is set to Memcached to retrieve from the database.
            $value = self::findAllBy(array());
            if ($value) {
                $memcache->set($key, $value, 0, static::MEMCACHED_EXPIRE);
            }
        }
        return $value;
    }

    /**
     * To remove a cache of all the records that were set in memcached.
     * Although cache of get() and getAll() is cleared,
     * If you are making a cache individually, this method to override,
     * Performing the separate cache clear implementation.
     */
    public static function removeAllCache() {
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        // Delete for each id (get() to remove the corresponding cache)
        $all_data = static::findAllBy(array());
        foreach ($all_data as $data) {
            $key = static::getKey($data->id);
            $memcache->delete($key, 0);
        }
        // Delete the entire cache (getAll() to remove the corresponding cache)
        $key = static::getAllKey();
        $memcache->delete($key, 0);
    }

    /**
     * To get all the data that meets the conditions from Memcache.
     * If it's not registered to Memcache, it is set to Memcache to retrieve from the database.
     *
     * @param unknown_type $params conditions
     * @param string $order order
     * @param int $limitArgs Acquisition upper limit
     * @return array Array of model objects.
     */
    public static function getAllBy($params, $order = null, $limitArgs = null) {
        $key = static::getAllByKey($params, $order, $limitArgs);
        // To connect to Memcached, to get the value.
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $value = $memcache->get($key);
        if (FALSE === $value) {
            // If the value has been set to Memcached, it is set to Memcached to retrieve from the database.
            $value = self::findAllBy($params, $order, $limitArgs);
            if ($value) {
                $memcache->set($key, $value, 0, static::MEMCACHED_EXPIRE);
            }
        }
        return $value;
    }

    /**
     * 条件にあうデータを1件Memcacheから取得する.
     * もしMemcacheに登録されていなければ、データベースから取得してMemcacheにセットする.
     *
     * @param unknown_type $params
     *            条件
     * @return モデルオブジェクトの配列.
     */
    public static function getBy($params) {
        $key = static::getByKey($params);
        // Memcahced に接続して、値を取得する.
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $value = $memcache->get($key);
        if (FALSE === $value) {
            // Memcached に値がセットされていなければ、データベースから取得して Memcached にセットする.
            $value = self::findBy($params);
            if ($value) {
                $memcache->set($key, $value, 0, static::MEMCACHED_EXPIRE);
            }
        }
        return $value;
    }

    /**
     * CSVファイルから読み込んだデータを保存する。
     *
     * @param object $pdo
     *            PDO
     * @param string $file
     *            CSVファイルパス
     * @param int $client_type
     *            1:iOS, 2:Android
     */
    public static function replaceAllFromCsvFile($pdo, $file, $client_type) {

        // 全件削除。
        $stmt = $pdo->prepare('DELETE FROM ' . static::TABLE_NAME);
        $result = $stmt->execute();

        // CSVファイルを読み込む。
        $fp = fopen($file, 'r');
        $columns = fgetcsv($fp, 0);
        array_shift($columns); // 1行目のカラム名は除く。

        while ($values = fgetcsv($fp, 0)) {

            $master_data_type = array_shift($values);
            if ($master_data_type != self::MASTER_DATA_TYPE_NONE) {
                $hash = array_combine($columns, $values);
                $class = get_called_class();
                $obj = new $class();
                foreach ($hash as $column => $value) {
                    $obj->$column = $value;
                }
                $obj->create($pdo);
            }
        }
        fclose($fp);
    }

    /**
     * memcacheにセットした全レコードのキャッシュを削除する。(IDのみ取得し対応。findAllByのメモリ問題解決)
     */
    public static function removeAllCacheWithID() {

        // 全データID取得
        $pdo = Common_Util_DatabaseUtil::getConnectionForRead();
        $stmt = $pdo->prepare('SELECT id FROM ' . static::TABLE_NAME);
        $stmt->execute();
        $all_id_list = $stmt->fetchAll(PDO::FETCH_FUNC, 'Utils::toInt');

        // 全データIDのCache削除
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        foreach ($all_id_list as $id) {
            $key = static::getKey($id);
            $memcache->delete($key, 0);
        }
        // 全キャッシュの削除(getAll()に対応するキャッシュの削除)
        $all_key = static::getAllKey();
        $memcache->delete($all_key, 0);
    }

}
