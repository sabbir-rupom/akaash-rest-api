Result Code
===========

The sole purpose of *ResultCode Class* is handling response of different types of error and exceptions with appropriate **Result Code** and **HTTP Status Code** during an API execution.

In most simple API platform the exceptions are handled by throwing only the HTTP Status Code with the response, 
where client side needs to check the HTTP status for handling errors on their side. 

A specific result code with appropriate HTTP Status code brings one more step towards effective exception handling inside client-end. 

Client application may choose which action need to be done after checking the HTTP Status code, and based on the result code in json response client can switch between multiple 
action for different case of similar exceptions. 

The API developer will decide which code will be defined for which type of exception, and add appropriate HTTP Status code and messages for error specification.

::

    const SESSION_ERROR = 3;

    const CODE_MESSAGE = array(
        self::SESSION_ERROR => array(
            'title' => 'SESSION ERROR',
            'msg' => 'Session expired',
            'http_status' => 401
        )
    );

ResultCode class mainly used for handling exceptions: Sample code is given below::

    class ResultCode {
        const USER_NOT_FOUND = 6;

        const CODE_MESSAGE = array(
            self::USER_NOT_FOUND => array(
                'title' => 'USER NOT FOUND',
                'msg' => 'User not found',
                'http_status' => 404
            )
        );
    }
 
    $userId = 1;
    $user = Model_User::find($userId);
    if (empty($user)) {
        throw new System_ApiException(ResultCode::USER_NOT_FOUND);
    }

ResultCode class has following functions: 

- **getTitle()**

  - Accepts error code as argument and returns corresponding *Error Title* defined in the class::

    class ResultCode {
        const USER_NOT_FOUND = 6;
        const CODE_MESSAGE = array(
            self::USER_NOT_FOUND => array(
                'title' => 'USER NOT FOUND',
                'msg' => 'User not found from database',
                'http_status' => 404
            )
        );
        ....
        ....
    }
    echo ResultCode::getTitle(ResultCode::USER_NOT_FOUND); // prints 'USER NOT FOUND'

- **getMessage()**

  - Accepts error code as argument and returns corresponding *Error Message* defined in the class::

    class ResultCode {
        const USER_NOT_FOUND = 6;
        const CODE_MESSAGE = array(
            self::USER_NOT_FOUND => array(
                'title' => 'USER NOT FOUND',
                'msg' => 'User not found in database',
                'http_status' => 404
            )
        );
        ....
        ....
    }
    echo ResultCode::getTitle(ResultCode::USER_NOT_FOUND); // prints 'User not found in database'

- **getHTTPstatusCode()**

  - Accepts error code as argument and returns corresponding *HTTP Status Code* defined in the class::

    class ResultCode {
        const USER_NOT_FOUND = 6;
        const CODE_MESSAGE = array(
            self::USER_NOT_FOUND => array(
                'title' => 'USER NOT FOUND',
                'msg' => 'User not found in database',
                'http_status' => 404
            )
        );
        ....
        ....
    }
    echo ResultCode::getHTTPstatusCode(ResultCode::USER_NOT_FOUND); // prints '404'