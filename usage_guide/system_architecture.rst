###################
System Architecture
###################

I have organized my project's system structure little similar like an MVC architecturesp that the web developers with minimum MVC knowledge can understand the structure before 
starting their development on this project. The system architecture is as follows:

-   API classes will act as controller classes in ``app\api`` directory
-   All database and logical code execution will be written as model class in ``app/model`` directory
-   Any response or viewable data will be written in ``app/view`` directory

As we are talking about **REST API**, the *View* part of an MVC is not a concern. So, my view is restricted to only the 
*Output Class* - whose member function receives a JSON data as parameter and show them as ``application/json`` content-type.

Application Flow
----------------

|Application Flow|

Project Skeleton Structure
--------------------------

The Rest-API project's skeleton is driven by the **Flight microframework**

All necessary routing is done in ``app/route/route.php`` and pointed to ``app/api/Controller.php``

The **Controller** class receive the request method and search corresponding api Class inside the ``app/api/`` directory.

The called api path is camelized [ *The 1st letter of the string and letter/s next to underscore `_` / Hypen `-` is camelized and underscore / hypen is removed* ] 
before searching the *API Class*

The database query related functions been defined inside the ``app/model`` directory. 
All the DB Table model has to be extended with *Base Model Class* to inherit some basic query builder functions. 

The **Config** Class in ``app/config`` directory handles most of the major server configurations.

For MySQL Database connectivity ``PDO Driver`` is used. If you are not familiar with PDO, please visit and learn the basics `PDO Tutorial <https://phpdelusions.net/pdo>`_ 
 
The **helper** classes are defined in ``app/common`` directory. Available helper classes are:

-   ArrayUtil : Custom class for various array manipulations 
-   DateUtil : Custom class for various date/time manipulations
-   Utils : Custom utility class for frequently used helper functions

Application system classes are initialized in `app/system` directory. These classes are:

-   ApiException : API Exception class extends the default PHP *Exception Class* for common & user-defined exception messages for REST-APi
-   FileCacheClient : File cache class extending PhpFileCache library package `source link <https://github.com/Wruczek/PHP-File-Cache>`_
-   JwtToken : Implementing JWT token features from extending JWT library package `source link <https://jwt.io/>`_
-   MemcachedServer : Memcache implementation class
-   Security : Security class consists of some common sanitization function for input handling from xss-attack

Server constant class definitions in ``app/const`` directory 

The **ResultCode** class is defined to modify messages from exception class with appropriate result-code and http-status-code for clients

.. |Application Flow| image:: https://sabbirrupom.com/resources/git/rest-template-architecture.jpg


