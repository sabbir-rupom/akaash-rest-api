################
System Libraries
################

System library classes reside under ``app/system`` directory. 

Library classes are as follows:

Exception Class
===============

ApiException class extends the **PHP Exception** class thus handles the exceptions inside API source code. Can be invoked as ``System_ApiException()``

Use the codes of **ResultCode Class** for handling exceptions with *ApiException* library class:: 

    throw new System_ApiException(ResultCode::USER_NOT_FOUND, 'User not found');


ApiException constructor accepts 3 parameters

  1. Result Code - a valid result code (integer type) defined in **ResultCode Class**

  2. Message - An user defined message for adding custom message (string type) to overwrite the message defined in *ResultCode Class*

  3. previous - The previous exception used for the exception chaining [ not in use ]


Exception handling usage example::

    $userId = 1;
    $user = Model_User::find($userId);
    if (empty($user)) {
        throw new System_ApiException(ResultCode::USER_NOT_FOUND, 'User not found');
    }


JwtToken Class
===============

This class is used for JWT token verfication [ if enabled from configuration ] by extending the `JWT library <https://github.com/firebase/php-jwt>`_

To change JWT verification algorithm, simply modify the constant array value ``JWT_ENCODE_ALGORITHMS`` 

The library class has following functions:

- **verifyToken()**

  - Verifies the JWT token by passing the following arguments:

    - Token value [ received from HTTP request header ] 

    - Secret key [ stored in server ]

    - Function returns array with error code and payload-data [ if found inside the token ]
  
  :: 

        System_JwtToken::verifyToken($requestToken, $secretKey);

- **createToken()**

  - Creates JWT token with payload data [ if passed as argument ] and secret key [ stored in server ]

  ::

        System_JwtToken::createToken($payload = [], $secretKey);

JWT token verification is an essential feature for client authentication, so that unknown / improper client won't able to receive data through API request 

In my test console, I have used ``HS256`` encode algorithm to create JWT token for API request. 

FileCacheClient Class
=====================

System class FileCacheClient inherits the library **PhpFileCache**, a library class for caching data in local file in server.

The local cache path is provided to the class constructor allowing the access of file-cache properties of the library.

By default, cache-expiration time is set to one hour.

The member functions are as follows:

- **get()**

  - Retrieves value from cache by key name, passed as argument ::

    $cacheObj = new System_FileCacheClient(Config_Config::getInstance()->getLocalCachePath());
    echo $cacheObj->get('key'); // prints 'value' stored with 'key' name if exist in filecache

- **put()**

  - Replace stored key-value only when it's expiry time is over::

    $cacheObj = new System_FileCacheClient(Config_Config::getInstance()->getLocalCachePath());
    $cacheObj->put('key', 'value'); // stores 'value' against 'key' name if not exist or expires in filecache

- **set()**

  - Store or replace value with provided key name::

    $cacheObj = new System_FileCacheClient(Config_Config::getInstance()->getLocalCachePath());
    $cacheObj->set('key', 'value', 1800); // sets 'value' under 'key' name with expiry time 1800 seconds


- **delete()**

  - Deletes value from cache by key name::

    $cacheObj = new System_FileCacheClient(Config_Config::getInstance()->getLocalCachePath());
    $cacheObj->delete('key'); // deletes 'key' along with 'value'
 
- **flush()**

  - Deletes all cache data from file::

    $cacheObj = new System_FileCacheClient(Config_Config::getInstance()->getLocalCachePath());
    $cacheObj->flush(); // deletes every cache data


MemcachedServer Class
=====================

MemcachedServer is the library class for caching data in memcache.

The REST application's cache system will be connected to memcache server if ``SERVER_CACHE_ENABLE_FLAG`` flag is set to 1 and ``FILE_CACHE_FLAG`` is set to 0 from ``config_app.ini``.

Function definitions are same as above [ *FileCacheClient* ]


Security Class
=====================

[[ Will be discussed soon ]]




