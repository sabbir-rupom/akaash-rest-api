<?php

(defined('APP_NAME')) OR exit('Forbidden 403');

/**
 * Base Abstract Model 
 */
abstract class Model_BaseModel {

    // Table name. Be overridden by the implementation class.
    const TABLE_NAME = "";
    // Updated_at whether the column exists. Be overridden by the implementation class, if necessary.
    const HAS_UPDATED_AT = TRUE;
    // Created_at whether the column exists. Be overridden by the implementation class, if necessary.
    const HAS_CREATED_AT = TRUE;
    // Created_on whether the column exists. Be overridden by the implementation class, if necessary.
    const HAS_CREATED_ON = FALSE;

    // Cache the column name list on the db
    private static $columnsOnDB = null;

    /**
     * ID Specify to retrieve records from the database.
     * @param mixed $id ID
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query.
     * @return Model_User Object
     */
    public static function find($id, $pdo = null, $forUpdate = FALSE) {
        if ($pdo == null) {
            $pdo = Flight::pdo();
        }
        $sql = "SELECT * FROM " . static::TABLE_NAME . " WHERE id = ?";
        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $obj = $stmt->fetch(PDO::FETCH_CLASS);
        return $obj;
    }

    /**
     * Based on the specified conditions, return to get only one record from the database.
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query.
     * @return Search result object.
     */
    public static function findBy($params, $pdo = null, $forUpdate = FALSE) {
        if ($pdo == null) {
            $pdo = Flight::pdo();
        }
        list($conditionSql, $values) = self::constructQuery($params, null, null, $forUpdate);
        $sql = "SELECT * FROM " . static::TABLE_NAME . $conditionSql;
        //Common_Util_LogUtil::getApplicationLogger()->debug($sql, Zend_Log::DEBUG);
        
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        
        $obj = $stmt->fetch(PDO::FETCH_CLASS);

        return $obj;
    }

    /**
     * Based on specified criteria, returns all items of the records from the database.
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param string $order SQL ORDER BY column, associative array whose value is Direction and key is Column
     * @param array $limitArgs SQL LIMIT value
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query.
     * @return PDO PDO fetch class object
     */
    public static function findAllBy($params, $order = null, $limitArgs = null, $pdo = null, $forUpdate = FALSE) {
        if ($pdo == null) {
            $pdo = Flight::pdo();
        }
        list($conditionSql, $values) = self::constructQuery($params, $order, $limitArgs, $forUpdate);
        $sql = "SELECT * FROM " . static::TABLE_NAME . $conditionSql;
        
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        $objs = $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
        return $objs;
    }

    /**
     * 指定した条件にマッチするレコード数を返す.
     * @param array $params カラム名をキー、検索に使う値を値とする連想配列.
     * @param PDO $pdo トランザクション内で実行するときは、PDOオブジェクトを指定すること.
     * @return レコード数.
     */
    public static function countBy($params = array(), $pdo = null, $highPerformanceFlag = false) {
        if ($pdo == null) {
            $pdo = Flight::pdo();
        }
        list($conditionSql, $values) = self::constructQuery($params);

        $countSql = ' * ';
        if (true === $highPerformanceFlag) {
            $countSql = 'id';
        }

        $sql = "SELECT count(" . $countSql . ") as count FROM " . static::TABLE_NAME . $conditionSql;
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        $records = $stmt->fetchAll(PDO::FETCH_CLASS, get_called_class());
        $count = 0;
        if (!empty($records[0]->count)) {
            $count = $records[0]->count;
        }
        return $count;
    }

    /**
     * To build a conditional clause and bind the value array of SQL.
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param string $order SQL ORDER BY column, associative array whose value is Direction and key is Column
     * @param array $limitArgs SQL LIMIT value
     * @param boolean $forUpdate Whether to update the query.
     * @return array Constructed Query
     */
    protected static function constructQuery($params, $order = array(), $limitArgs = null, $forUpdate = FALSE) {
        $conditions = array();
        $values = array();
        foreach ($params as $k => $v) {
            if (is_array($v)) {
                $conditions[] = $k . ' IN (' . implode(',', array_fill(0, count($v), '?')) . ')';
                $values = array_merge($values, $v);
            } else {
                $conditions[] = $k . '=?';
                $values[] = $v;
            }
        }
        $sql = "";
        if (!empty($conditions)) {
            $sql .= " WHERE " . join(' AND ', $conditions);
        }
        if (isset($order) && is_array($order)) {
            $sql .= " ORDER BY ";
            foreach ($order as $key => $val) {
                $sql .= "{$key} {$val}";
                break;
            }
        }
        if (isset($limitArgs) && array_key_exists('limit', $limitArgs)) {
            if (array_key_exists('offset', $limitArgs)) {
                $sql .= " LIMIT " . $limitArgs['offset'] . ", " . $limitArgs['limit'];
            } else {
                $sql .= " LIMIT " . $limitArgs['limit'];
            }
        }
        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }
        return array($sql, $values);
    }

    /**
     * Insert new record to database
     * @param PDO $pdo
     * @return PDO object.
     */
    public function create($pdo = null) {
        if (is_null($pdo)) {
            $pdo = Flight::pdo();
        }
        // SQL Building.
        list($columns, $values) = $this->getValues();

        $now = Common_Util_Utils::timeToStr(time());
        // Common_Util_LogUtil::getApplicationLogger()->log("columns = " . count($columns) , Zend_Log::DEBUG);
        $sql = 'INSERT INTO ' . static::TABLE_NAME . ' (' . join(',', $columns);
        $sql .= (static::HAS_CREATED_ON === TRUE ? ',created_on' : '');
        $sql .= (static::HAS_CREATED_AT === TRUE ? ',created_at' : '');
        $sql .= (static::HAS_UPDATED_AT === TRUE ? ',updated_at' : '');
        $sql .= ') VALUES (' . str_repeat('?,', count($columns) - 1) . '?';
        $sql .= (static::HAS_CREATED_ON === TRUE ? ',CURRENT_DATE()' : '');
        $sql .= (static::HAS_CREATED_AT === TRUE ? ",'" . $now . "'" : '');
        $sql .= (static::HAS_UPDATED_AT === TRUE ? ",'" . $now . "'" : '');
        $sql .= ')';
        // INSERT data
        //$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute($values);
        $this->id = $pdo->lastInsertId();
        return $result;
    }

    /**
     * To update the object.
     * @param PDO $pdo
     */
    public function update($pdo = null) {
        if (!isset($this->id)) {
            throw new Exception('The ' . get_called_class() . ' is not saved yet.');
        }
        if (is_null($pdo)) {
            $pdo = Flight::pdo();
        }
        // SQLを構築.
        list($columns, $values) = $this->getValuesForUpdate();
        $sql = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $setStmts = array();
        foreach ($columns as $column) {
            $setStmts[] = $column . '=?';
        }
        $sql .= join(',', $setStmts);
        if (static::HAS_UPDATED_AT === TRUE) {
            $sql .= ",updated_at='" . Common_Util_Utils::timeToStr(time()) . "'";
        }
        $sql .= ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        //echo $sql;
        $result = $stmt->execute(array_merge($values, array($this->id)));
        return $result;
    }

    /**
     * オブジェクトを削除する.
     * @param PDO $pdo
     */
    public function delete($pdo = null) {
        if (!isset($this->id)) {
            throw new Exception('The ' . get_called_class() . ' is not saved yet.');
        }
        if (is_null($pdo)) {
            $pdo = Common_Util_DatabaseUtil::getConnectionForWrite();
        }
        $stmt = $pdo->prepare('DELETE FROM ' . static::TABLE_NAME . ' WHERE id = ?');
        $stmt->bindParam(1, $this->id);
        $result = $stmt->execute();
        return $result;
    }

    /**
     * $columns に対応する値の配列を返す.
     * インスタンスにセットされていない属性は含めない(デフォルト値を使用する場合のため).
     * @return array カラムの配列、値の配列からなる配列.
     */
    protected function getValues() {
        $values = array();
        $columns = array();
        foreach (static::getColumns() as $column) {
            if (isset($this->$column)) {
                $columns[] = $column;
                $values[] = $this->$column;
            }
        }
        return array($columns, $values);
    }

    /**
     * $columns に対応する値の配列を返す.
     * @return array カラムの配列、値の配列からなる配列.
     */
    protected function getValuesForUpdate() {
        $values = array();
        $columns = array();
        foreach (static::getColumns() as $column) {
            $columns[] = $column;
            $values[] = $this->$column;
        }
        return array($columns, $values);
    }

    /**
     * Return the column name list.
     */
    protected static function getColumns() {
        if (isset(static::$columnDefs)) {
            return array_keys(static::$columnDefs);
        } else {
            return static::$columns;
        }
    }

    /**
     * To get the column list of the table of the db
     */
    protected static function getColumnsOnDB($pdo = null) {
        if (self::$columnsOnDB == null) {
            if ($pdo == null) {
                $pdo = Flight::pdo();
            }

            $stmt = $pdo->prepare("SELECT * from " . static::TABLE_NAME . " order by id limit 1 ");
            $stmt->execute();
            self::$columnsOnDB = array_keys($stmt->fetch(PDO::FETCH_ASSOC));
        }
        return self::$columnsOnDB;
    }

    /**
     * To check whether there is a column name
     * */
    public static function hasColumn($name) {

        return (in_array($name, static::getColumnsOnDB()) &&
                in_array($name, static::getColumns()));
    }

    public static function hasColumn2($name) {

        return (in_array($name, static::getColumns()));
    }

    /**
     * Returns the type of column.
     * @param String $ column target column name.
     */
    protected static function getColumnType($column) {
        return static::$columnDefs[$column]['type'];
    }

    /**
     * Return to the specified column whether or not to include in JSON.
     * @param String $ column target column name.
     */
    public static function isColumnIncludedInJson($column) {
        $columnDef = static::$columnDefs[$column];
        if (isset($columnDef['json'])) {
            return $columnDef['json'];
        }
        // The default is TRUE.
        return TRUE;
    }

    /**
     * Return an associative array for JSON.
     */
    public function toJsonHash() {
        foreach (static::getColumns() as $column) {
            if (static::isColumnIncludedInJson($column)) {
                if ('int' === static::getColumnType($column) && !is_null($this->$column)) {
                    $hash[$column] = (int) $this->$column;
                } else if ('float' === static::getColumnType($column) && !is_null($this->$column)) {
                    $hash[$column] = floatval($this->$column);
                } else if ('bool' === static::getColumnType($column) && !is_null($this->$column)) {
                    $hash[$column] = ("1" === $this->$column);
                } else {
                    $hash[$column] = $this->$column == null ? "" : $this->$column;
                }
            }
        }
        return $hash;
    }

    /**
     * Get the cache data.
     * @param unknown_type $key
     */
    public static function getCache($cacheKey) {
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        return $memcache->get($cacheKey);
    }

    /**
     * Save the cache data.
     * @param unknown_type $key
     * @param unknown_type $value
     */
    public static function setCache($cacheKey, $value) {
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $call_class = get_called_class();
        $ret_value = $memcache->set($cacheKey, $value, MEMCACHE_COMPRESSED, $call_class::MEMCACHED_EXPIRE);
        return $ret_value;
    }

    /**
     * Delete the cache.
     * @param unknown_type $key
     */
    public static function deleteCache($cacheKey) {
        $memcache = Common_Util_KeyValueStoreUtil::getMemcachedClient();
        $value = $memcache->delete($cacheKey);
        //Common_Util_LogUtil::getApplicationLogger()->debug("delete cache " . $cacheKey, Zend_Log::DEBUG);
        return $value;
    }

    /**
     * Returns the key for setting all records in memcache.
     */
    protected static function getAllKey() {
        return Common_Util_ConfigUtil::getInstance()->getMemcachePrefix() . get_called_class() . '_all';
    }

    public static function generateRandomString($length = 5) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function getColumnSpecificData($table, $column, $pdo = null) {
        
    }

}
