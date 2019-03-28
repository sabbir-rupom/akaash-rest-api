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




