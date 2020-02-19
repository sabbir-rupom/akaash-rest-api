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

            public static function printColumns() {
                print_r(static::$columnsOnDB);
            } 
        }

        echo Model_User::printColumns();

For the ease of query execution I have included multiple query builder functions in *BaseModel* class:

Data Selection
==============
Following functions will help you executing **SELECT** SQL query

- **find()**

  - Find and returns one row [if found] after SQL execution 

  - The function upto three parameters

    1. Table row ID, which is the primary key

       [*Note*] This function can only be used if the primary key of that table denoted as `id`

       function will throw database error if ID is not passed as argument

    2. Database connection object [ Instance of PDO (optional) ]

    3. Set locking read status `::learn more:: <https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html>`_

  - Returns table row as called class object::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            public static function getUser($userId = 1) {
                $pdo = Flight::pdo();
                $userObj = self::find($userId, $pdo, FALSE);
            }
        }

- **findBy()** 

  - Find and returns one row as result [if found] after SQL execution 

  - The function accepts upto three parameters
    
    1. Array of conditions [key-value pair] for *WHERE* clause in SQL statement

       [*Note*] if conditions are not passed in array function will return the 1st row of the table after SQL execution

    2. Database connection object [ Instance of PDO (optional) ]

    3. Set locking read status `::learn more:: <https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html>`_

  - Returns table row as called class object::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            public static function getUser($userEmail = 'x@gmail.com') {
                $pdo = Flight::pdo();
                $userObj = self::findby([ 'email' => $userEmail ], $pdo, FALSE);
            }
        }

- **findAllBy()** 

  - Find and returns multiple rows as result [if found] after SQL execution 

  - The function accepts about five parameters
    
    1. Array of conditions [key-value pair] for *WHERE* clause in SQL statement

       [*Note*] if conditions are not passed in array function will return the all rows inside the table after SQL execution

    2. SQL ``ORDER BY`` column, expects an associative array whose value is Direction and key is Column
    
    3. Expects an array of two elements-
       
       * Query limit ``[ limit => 5 ]``
       * Query offset ``[ offset => 10 ]`` ( after which table rows will be returned )
    
    4. Database connection object [ Instance of PDO (optional) ]

    5. Set locking read status `::learn more:: <https://dev.mysql.com/doc/refman/8.0/en/innodb-locking-reads.html>`_

  - Returns table row as called class object::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            public static function getUsers($userGender = 'male') {
                $pdo = Flight::pdo();
                $userObj = self::findAllBy([ 'gender' => $userGender ], [ 'id' => 'DESC' ], [ 'limit' => 5, 'offset' => 5 ], $pdo, FALSE);
            }
        }

- **findColumnSpecificData()** 

  - Find and returns multiple rows with specific column values as result [if found] after SQL execution 

  - The function accepts about five parameters

    1. Array of columns whose value will be returned from the table
    
    2. Array of conditions [key-value pair] for *WHERE* clause in SQL statement

       [*Note*] if conditions are not passed in array function will return the all rows inside the table after SQL execution

    3. SQL ``ORDER BY`` column, expects an associative array whose value is Direction and key is Column
    
    4. Expects an array of two elements-
       
       * Query limit ``[ limit => 5 ]``
       * Query offset ``[ offset => 10 ]`` ( after which table rows will be returned )
    
    5. Database connection object [ Instance of PDO (optional) ]

  - Returns table row as called class object::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            public static function getUsers($userGender) {
                $pdo = Flight::pdo();
                $userObj = self::findColumnSpecificData([ 'id', 'email', 'first_name', 'last_name' ], [ 'gender' => $userGender ], [ 'id' => 'DESC' ], [ 'limit' => 5, 'offset' => 5 ], $pdo);
                return $userObj;
            }
        }

        print_r(Model_User::getUsers('male'));


- **countBy()** 

  - Counts the row of results after query execution

  - the function accepts upto three parameters
    
    1. Array of conditions [key-value pair] for *WHERE* clause in SQL statement

       [*Note*] if conditions are not passed in array function will return the 1st row of the table after SQL execution

    2. Database connection object [ Instance of PDO (optional) ]

    3. For faster query execution, counts all the rows in the table if set to boolean **TRUE** 

  - Returns table row as called class object::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            public static function countUsers($userGender = 'male') {
                $pdo = Flight::pdo();
                $userObj = self::countBy([ 'gender' => $userGender ], $pdo);
            }
        }

[ **Note** ] : 

To match the query condition, please set the condition array as follows:
   
    - ``array('id >=' => 5)``

    - Following condition operations are accepted - ``>``, ``>=``, ``<``, ``<=``, ``like``, ``!=``, ``<>``
    
    - To find result from array range simply add the array as condition value [ e.g  ``array('id' => [1,2,3,4,5] )`` ]

    - Query condition for ``OR`` clause not implemented yet


- **create()** 

  - Inserts a row in the associated table

  - Can be invoked by creating an instance of *Table Model Class*

  - Table columns must be defined under **columnsOnDB** in *Table Model Class*

  - Can pass database connection object [ Instance of PDO ] as argument (optional) 

  - Returns inserted row as object [column,value pair] ::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            const HAS_CREATED_AT = TRUE;
            const HAS_UPDATED_AT = TRUE;

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

        $userObj = new Model_User();
        $userObj->email = 'x@gmail.com';
        $userObj->create();

        print_r($userObj); //  result object of inserted row

- **update()** 

  - Updates a row in the associated table

  - Table columns must be defined under **columnsOnDB** in *Table Model Class*

  - Can pass database connection object [ Instance of PDO ] as argument (optional) 

  - Returns number of updated record ::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

            const HAS_UPDATED_AT = TRUE;

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
        
        $userId = 1;
        $userObj = self::find($userId);
        $userObj->email = 'x@gmail.com';

        if($userObj->update() > 0) {
            echo 'User updated successfully!';
        }

- **delete()** 

  - Deletes a row by table ``id`` in the associated table

  - Can pass database connection object [ Instance of PDO ] as argument (optional) 

  - Sample code ::

        class Model_User extends Model_BaseModel {
            const TABLE_NAME='users';

        }
        
        $userId = 1;
        $userObj = self::find($userId);

        $userObj->delete();  // Deletes record of ID 1

- **hasColumn()** 

  - Check if a column is defined in *Model Table Class* as well as exist in database table or not
  
  - Column name must be passed [ string or array ] as argument ::

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

        echo Model_User::hasColumn('email'); // prints 1 if exists
        echo Model_User::hasColumn('name'); //  prints nothing if not exists

- **hasColumnDefined()** 

  - Check if a column is defined in *Model Table Class* or not
  
  - Column name must be passed [ string or array ] as argument ::

        echo Model_User::hasColumnDefined('email'); // prints 1 if exists, or prints nothing

- **toJsonHash()**

  - This function prepares selected results from query to proper data-array for json response

  - The result array is filtered with the column definitions [``columnsOnDB``] in the *Model Table Class*

  - Sample code example::

        $userId = 1;
        $userObj = Model_user::find($userId);

        $result = $userObj->toJsonHash();  
        print_r($result);  // results are filtered with proper data type values


Other than this, some other functions are included:

- **getCache()**

  - Retrieves data from cache by cache-key passed as argument::

        echo Model_user::getCache('user_id_1'); // get result if exist

- **setCache()**

  - Store data in cache by 

  - The function accepts two parameters

    1. Cache key, by which the data will be stored in cache

    2. Value ::

        $userId = 1;
        $userObj = self::find($userId);
        Model_user::setCache('user_id_1', $userObj);

- **deleteCache()**

  - Deletes data from cache by cache-key passed as argument::

        Model_user::deleteCache('user_id_1');