######################
DB Query Builder Class
######################

The **Base Model Class** residing in ``app/model`` directory is an abstract class acting as a query builder class.
By extending this class with your custom model class will allow using its member variables and functions to build minimal query for data
insert, update, retrieve, delete with minimal scripting. There are also scope for escaping unwanted string for safer query execution. 
Some additional helper function to set, retrieve and delete data from cache alongside the database query builder functions.

The members constants and variables are as follows:

- **TABLE_NAME**
    - declared as empty constant, can be override in child class to pass the correct table name for database query execution
    - sample code example::

        abstract class Model_BaseModel {   
            const TABLE_NAME='';
            public static function getAll() {
                return "SELECT * FROM `" . static::TABLE_NAME. "`";
            }   
        }
        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';
        }
        echo Model_User::getAll();

- **HAS_CREATED_AT**
    - Declared as ``boolean TRUE`` constant, expecting the child model class has a db-table column structure with ``created_at`` column [ DB column to store the insert time ] 
    - if override as ``boolean FALSE`` in child class will indicate the associated db-table has no column as ``created_at``
 
- **HAS_UPDATED_AT**
    - Declared as ``boolean TRUE`` constant, expecting the child model class has a db-table column structure with ``updated_at`` column [ DB column to store the edit time of the table row ] 
    - if override as ``boolean FALSE`` in child class will indicate the associated db-table has no column as ``updated_at``
 
- **MEMCACHED_EXPIRE** 
    - Cache data expiration time in seconds. By default, the constant value is set to 1800 seconds. 
    - Can be override in child class to modify the expiration time of that particular cache key

- **columnsOnDB**
    - Declared as private static member variable, can be override any access modifier below **private** from child class
    - Purpose is for defining db-table column structure of associated model class
    - sample code example::

        abstract class Model_BaseModel {   
            private static $columnsOnDB = null;

            public static function printColumns() {
                print_r(static::$columnsOnDB);
            }   
        }

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';
            protected static $columnsOnDB = array(
                'id' => array(
                    'type' => 'int',
                    'json' => true
                ),
                'email' => array(
                    'type' => 'string',
                    'json' => true
                ),
                'created_at' => array(
                    'type' => 'string',
                    'json' => false
                ),
                'updated_at' => array(
                    'type' => 'string',
                    'json' => false
                )
            );
        }

        echo Model_User::printColumns();

For the ease of query execution I have included multiple query builder functions in *BaseModel* class:

Data Selection
==============
Following functions will help you executing **SELECT** SQL query

- **find()**

  find function accepts upto three parameters

  1. Table row ID, which is the primary key

     [*Note*] This function can only be used if the primary key of that table denoted as `id`

     function will throw database error if ID is not passed as argument


    
 
- find()

    - find function accepts 3 parameter
        1. Table row ID, which is the primary key
            - [*Note*] This function can only be used if the primary key of that table denoted as `id`
            - function will throw database error if ID is not passed as argument
        2. Database connection object [ Instance of PDO (optional) ]
        3. Set locking read status `::learn more:: <https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html>`_
 * Returns table row as called class object::
    
        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            public static function getUser($userId = 1) {
                $pdo = Flight::pdo();
                $userObj = self::find($userId, $pdo, FALSE);
            }
        }

        
    









