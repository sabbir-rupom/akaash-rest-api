<?php

(defined('APP_NAME')) or exit('Forbidden 403');

/**
 * Abstract Base Model Class
 *
 * @author sabbir-hossain
 */

namespace Akaash\Core\Model;

use Akaash\Helper\DateUtil;

abstract class Base
{

    // Table name. Be overridden by the implementation class.
    const TABLE_NAME = "";
    // Table name. Be overridden by the implementation class.
    const PRIMARY_KEY = "";
    // Updated_at whether the column exists. Be overridden by the implementation class, if necessary.
    const HAS_UPDATED_AT = false;
    // Created_at whether the column exists. Be overridden by the implementation class, if necessary.
    const HAS_CREATED_AT = false;
    // Memcached Validity period
    const CACHE_EXPIRE = 3600; // 1 hour

    // Database table column list
    private static $columnsOnDB = null;

    /**
     * Retrieve records by table Primary Key/ID from the database
     *
     * @param mixed $id Table Primary Key/ID
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query result
     * @return object Search result as an object of called class
     */
    public static function find($id, \PDO $pdo = null, $forUpdate = false)
    {
        if ($pdo == null) {
            $pdo = \Flight::pdo();
        }

        $sql = "SELECT * FROM " . static::TABLE_NAME
            . " WHERE " . (empty(static::PRIMARY_KEY) ? 'id' : static::PRIMARY_KEY) . " = ?";
        
        if ($forUpdate) {
            $sql .= " FOR UPDATE";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $obj = $stmt->fetch(\PDO::FETCH_CLASS);
        return $obj;
    }

    /**
     * Based on the specified conditions, return to get only one record from the database
     *
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query result
     * @return object Search result as an object of called class
     */
    public static function findBy($params, \PDO $pdo = null, $forUpdate = false)
    {
        if ($pdo == null) {
            $pdo = \Flight::pdo();
        }
        list($conditionSql, $values) = self::constructQuery($params, null, null, $forUpdate);
        $sql = "SELECT * FROM " . static::TABLE_NAME . $conditionSql;

        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);

        $obj = $stmt->fetch(\PDO::FETCH_CLASS);

        return $obj;
    }

    /**
     * Based on specified criteria, returns all items of the records from the database
     *
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param string $order SQL ORDER BY column, associative array whose value is Direction and key is Column
     * @param array $limitArgs SQL LIMIT value
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query result
     * @return PDO PDO fetch class object
     */
    public static function findAllBy($params = [], $order = [], $limitArgs = null, \PDO $pdo = null, $forUpdate = false)
    {
        if ($pdo == null) {
            $pdo = \Flight::pdo();
        }
        list($conditionSql, $values) = self::constructQuery($params, $order, $limitArgs, $forUpdate);
        $sql = "SELECT * FROM " . static::TABLE_NAME . $conditionSql;

        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        $objs = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        return $objs;
    }

    /**
     * Returns the number of records matching the specified condition
     *
     * @param array $params Associative array with column name as key, value as search value.
     * @param PDO $pdo When executing within a transaction, specify the PDO object.
     * @param bool $flag to count the number of records in table
     * @return int Number of records
     */
    public static function countBy($params = array(), \PDO $pdo = null, $flag = false)
    {
        if ($pdo == null) {
            $pdo = \Flight::pdo();
        }
        if (empty($params)) {
            list($conditionSql, $values) = array('', array());
        } else {
            list($conditionSql, $values) = self::constructQuery($params);
        }
        $id = (empty(static::PRIMARY_KEY) ? 'id' : static::PRIMARY_KEY);
        $countSql = ' * ';
        if (true === $flag) {
            $countSql = " {$id} ";
        }

        $sql = "SELECT count( " . (true === $flag ? $id : "*") . " ) as count FROM "
            . static::TABLE_NAME . (true === $flag ? '' : $conditionSql);

        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        $records = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        $count = 0;
        if (!empty($records[0]->count)) {
            $count = $records[0]->count;
        }
        return $count;
    }

    /**
     * Execute prepared sql
     *
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param string $order SQL ORDER BY column, associative array whose value is Direction and key is Column
     * @param array $limitArgs SQL LIMIT value
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query result
     * @return PDO PDO fetch class object
     */
    public static function query(string $sql = '', \PDO $pdo = null, bool $single = false)
    {
        if (!empty($sql)) {
            return null;
        } elseif ($pdo == null) {
            $pdo = \Flight::pdo();
        }

        $obj = $pdo->query($sql, $single ? \PDO::FETCH_ORI_FIRST : \PDO::FETCH_OBJ);

        return $obj;
    }

    /**
     * To build a conditional clause and bind the value array of SQL
     *
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param string $order SQL ORDER BY column, associative array whose value is Direction and key is Column
     * @param array $limitArgs SQL LIMIT value
     * @param boolean $forUpdate Whether to update the query.
     * @return array Constructed Query
     */
    protected static function constructQuery(array $params, $order = [], $limitArgs = null, $forUpdate = false)
    {
        list($conditions, $values) = self::constructQueryCondition($params);

        $sql = "";
        if (!empty($conditions)) {
            $sql .= " WHERE " . join(' AND ', $conditions);
        }
        if (isset($order) && is_array($order) && !empty($order)) {
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
     * To construct query conditions
     *
     * @param array $params $params[][0] for column-name, $params[][1] for value, $params[][1] for condition
     * @return array Constructed Query condition
     */
    protected static function constructQueryCondition(array $params)
    {
        $condition = $values = [];
        $operator = '=';
        if (empty($params)) {
            return [$condition, $values];
        } else {
            foreach ($params as $k => $v) {
                if (empty($v)) {
                    continue;
                } elseif (is_array($v)) {
                    $conditions[] = $k . ' IN (' . implode(',', array_fill(0, count($v), '?')) . ')';
                    $values = array_merge($values, $v);
                } else {
                    $k = explode(' ', trim($k));
                    $operator = isset($k[1]) ? $k[1] : $operator;

                    switch ($operator) {
                        case '=':
                        case '<>':
                        case '!=':
                        case '>=':
                        case '<=':
                        case '>':
                        case '<':
                            $conditions[] = $k[0] . " $operator ?";
                            $values[] = $v;
                            break;
                        case 'like':
                            $conditions[] = $k[0] . " $operator ?";
                            $values[] = ("%" . $v . "%");
                            break;
                        default:
                            $conditions[] = $k[0] . " = ?";
                            $values[] = $v;
                            break;
                    }
                }
            }
        }
        
        return [$conditions, $values];
    }

    /**
     * Insert new record in database
     *
     * @param PDO $pdo
     * @return PDO object.
     */
    public function create(\PDO $pdo = null)
    {
        if (is_null($pdo)) {
            $pdo = \Flight::pdo();
        }
        // Prepare SQL
        list($columns, $values) = $this->getValues();

        $now = DateUtil::getToday();
        $sql = 'INSERT INTO ' . static::TABLE_NAME . ' (' . join(',', $columns);
        $sql .= (static::HAS_CREATED_AT === true && !in_array('created_at', $columns) ? ',created_at' : '');
        $sql .= (static::HAS_UPDATED_AT === true ? ',updated_at' : '');
        $sql .= ') VALUES (' . str_repeat('?,', count($columns) - 1) . '?';
        $sql .= (static::HAS_CREATED_AT === true && !in_array('created_at', $columns) ? ",'" . $now . "'" : '');
        $sql .= (static::HAS_UPDATED_AT === true ? ",'" . $now . "'" : '');
        $sql .= ')';
        // INSERT data
        $stmt = $pdo->prepare($sql);
        $stmt->execute($values);
        return $pdo->lastInsertId();
    }

    /**
     * To update table record
     *
     * @param \PDO $pdo
     * @return type
     * @throws Exception
     */
    public function update(\PDO $pdo = null)
    {
        $id = empty(static::PRIMARY_KEY) ? 'id' : static::PRIMARY_KEY;

        if (!isset($this->{$id})) {
            throw new Exception('The ' . get_called_class() . ' model is not set');
        }
        if (is_null($pdo)) {
            $pdo = \Flight::pdo();
        }
        // Preparing SQL
        list($columns, $values) = $this->getValues();
        $sql = 'UPDATE ' . static::TABLE_NAME . ' SET ';
        $setStmts = array();
        foreach ($columns as $column) {
            $setStmts[] = $column . '=?';
        }
        $sql .= join(',', $setStmts);
        if (static::HAS_UPDATED_AT === true) {
            $sql .= ",updated_at='" . DateUtil::getToday() . "'";
        }
        $sql .= " WHERE {$id} = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($values, array($this->{$id})));
        /*
         * Return updated record count
         */
        return $stmt->rowCount();
    }

    /**
     * Delete the object of specific row ID from table
     *
     * @param PDO $pdo
     */
    public function delete($pdo = null)
    {
        $id = empty(static::PRIMARY_KEY) ? 'id' : static::PRIMARY_KEY;
        if (!isset($this->{$id})) {
            throw new Exception('The ' . get_called_class() . ' is not initiated properly.');
        }
        if (is_null($pdo)) {
            $pdo = \Flight::pdo();
        }

        $stmt = $pdo->prepare('DELETE FROM ' . static::TABLE_NAME . ' WHERE ' . $id . ' = ?');
        $stmt->bindParam(1, $this->{$id});
        return $stmt->execute();
    }

    /**
     * Return an array of values corresponding to
     * Do not include attributes that are not set in the instance
     * [
     *   id model class does not include attributes in the DB column definition,
     *   they will not be executed, only default values will be
     * ]
     *
     * @return array An array consisting of an array of columns, an array of values
     */
    protected function getValues()
    {
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
     * Start database query transaction
     *
     * @param \PDO $pdo
     * @return bool
     */
    public static function startTransaction(\PDO $pdo): bool
    {
        return $pdo->beginTransaction();
    }

    /**
     * End database query transaction
     *
     * @param \PDO $pdo
     * @return bool
     */
    public static function commit(\PDO $pdo): bool
    {
        return $pdo->commit();
    }

    /**
     * Rollback all query transaction
     *
     * @param \PDO $pdo
     * @return bool
     */
    public static function rollback(\PDO $pdo): bool
    {
        return $pdo->rollback();
    }

    /**
     * Return the column name list.
     */
    protected static function getColumns()
    {
        if (isset(static::$columnDefs)) {
            return array_keys(static::$columnDefs);
        } else {
            return static::$columns;
        }
    }

    /**
     * To get the column list of the database table
     */
    protected static function getColumnsOnDB(\PDO $pdo)
    {
        if (self::$columnsOnDB == null) {
            if ($pdo == null) {
                $pdo = \Flight::pdo();
            }

            $stmt = $pdo->prepare("SELECT * from " . static::TABLE_NAME . " limit 1 ");
            $stmt->execute();
            self::$columnsOnDB = array_keys($stmt->fetch(\PDO::FETCH_ASSOC));
        }
        return self::$columnsOnDB;
    }

    /**
     * Based on specified criteria, returns specific items of the records from the database
     *
     * @param array $columns Column names are values which will be returned in result only
     * @param array $params Column name the key, associative array whose value is the value to use for the search.
     * @param string $order SQL ORDER BY column, associative array whose value is Direction and key is Column
     * @param array $limitArgs SQL LIMIT value
     * @param PDO $pdo Database connection object
     * @param boolean $forUpdate Whether to update the query result
     * @return PDO PDO fetch class object
     */
    public static function getColumnSpecificData($columns, $params, $order = null, $limitArgs = null, \PDO $pdo = null)
    {
        if ($pdo == null) {
            $pdo = \Flight::pdo();
        }

        list($conditionSql, $values) = self::constructQuery($params, $order, $limitArgs, $forUpdate);

        $sql = "SELECT " . implode(',', $columns) . " FROM " . static::TABLE_NAME . $conditionSql;

        $stmt = $pdo->prepare($sql);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        $stmt->execute($values);
        $objs = $stmt->fetchAll(\PDO::FETCH_CLASS, get_called_class());
        return $objs;
    }

    /**
     * To check whether there is a column name both in database and model class definition
     * */
    public static function hasColumn($name)
    {
        return (in_array($name, static::getColumnsOnDB()) &&
            in_array($name, static::getColumns()));
    }

    /**
     * To check whether there is a column in model class column definition
     * */
    public static function hasColumnDefined($name)
    {
        return (in_array($name, static::getColumns()));
    }

    /**
     * Returns the type of column
     *
     * @param string $column
     * @return string Type of database table column
     */
    protected static function getColumnType($column)
    {
        return static::$columnDefs[$column]['type'];
    }

    /**
     * Return to the specified column whether or not to include in JSON
     *
     * @param String $ column target column name.
     */
    public static function isColumnIncludedInJson($column)
    {
        $columnDef = static::$columnDefs[$column];
        if (isset($columnDef['json'])) {
            return $columnDef['json'];
        }
        // The default is TRUE.
        return true;
    }

    /**
     * Return an associative array for JSON.
     */
    public function toJsonHash()
    {
        foreach (static::getColumns() as $column) {
            if (static::isColumnIncludedInJson($column)) {
                if ('int' === static::getColumnType($column) && !is_null($this->$column)) {
                    $hash[$column] = (int) $this->$column;
                } elseif ('float' === static::getColumnType($column) && !is_null($this->$column)) {
                    $hash[$column] = floatval($this->$column);
                } elseif ('bool' === static::getColumnType($column) && !is_null($this->$column)) {
                    $hash[$column] = ("1" === $this->$column);
                } else {
                    $hash[$column] = (!isset($this->$column) || $this->$column == null) ? "" : $this->$column;
                }
            }
        }
        return $hash;
    }
}
