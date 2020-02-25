##########
Change Log
##########

Version 2.0.0
=============

Release Date: Not Released

-  General Changes

   - API group system is introduced
   - API query parameter can be passed with endpoint by adding additional URI segment
   - Architecture is modified
   - Documentation updated

-  System Changes

   - All system classes are defined under ``namespace Akaash``
   - ``Controller`` class is modified to core controller class under ``app/akaash/system/core`` directory
   - Core Controller initiates API with ``Initiate`` class under ``app/akaash/system/core`` directory
   - ``BaseModel`` class moved to ``app/akaash/system/core/model`` directory and modified as ``Base`` Model class
   - ``CacheModel`` class moved to ``app/akaash/system/core/model`` directory as ``Cache`` Model class
   - Cache service handler classes are created / modified under ``app/akaash/system/cache`` directory
   - Logger service handler class is moved from ``Helper`` section and moved under ``app/akaash/system/log`` directory
   - ``ApiException`` class is changed and modified to ``AppException`` class under ``app/akaash/system/exception`` directory
   - Following system library classes are removed: ``FileCacheClient.php``, ``JwtToken.php``, ``MemcachedServer.php``
   - ``Security`` class is modified 
   - ``Config`` class is modified and moved from ``app/config`` to ``app/akaash`` directory
   - ``app\route`` directory is removed
   - ``route.php`` is moved to ``app/config`` directory
   - Added new API config module ``hooks``
   - Added `constants.php` under ``app/config`` directory
   - ``ResultCode`` class has been split into ``ResultCode`` class and ``Constant`` class under ``app/akaash/system/message`` directory

-  Structural changes

   - Source code architecture is modified and tried to follow SOLID principles
   - Interfaces are written implemented with system classes as much as can be
   - Namespaces are defined and used as much as can be 

-  Configuration Changes

   - Followings are added

        - ``BASE_URL``, ``CLIENT_STORE_LOCATION_ANDROID``, ``CLIENT_STORE_LOCATION_iOS``, ``SUPPORT_MAIL``

   - Followings are discarded

        - ``CLIENT_UPDATE_LOCATION_ANDROID``, ``CLIENT_UPDATE_LOCATION_iOS``, ``CLgjH2m5c8emE66pjdExmgep47BAdKTrCJ7`, ``SUPPORT_MAIL_TO``, ``USER_SESSION_HEADER_KEY``
   
   - Added ``app/hooks`` directory to write new classes for **Server Hooks** configuration
   - Added ``app/api/filter`` directory to write new classes/interfaces for **Server Request Validation** codes

Version 1.2.0
=============

Release Date: Apr 12, 2019

-  General Changes

   - Test api class controller is updated for more efficient testing
   - Code optimized with PHP CS fixer
   - Documentation updated

-  Core 

    - Server configuration file modified

        - Added new config parameter ``SERVER_CACHE_ENABLE_FLAG`` for enable / disable caching feature
        - Config parameter ``LOCAL_CACHE_FLAG`` is changed to ``FILE_CACHE_FLAG``

   - Config class is updated according to the configuration file changes

   - Base model class updated for cache related issues

   - Server cache feature is updated 

Bug fixes for 1.2.0
-------------------

-  Memcache-compression constant related issue fix
-  Base model class function bug fix

   
Version 1.1.0
=============

Release Date: Mar 31, 2019

-  General Changes

   - Server configuration file modified slightly

        - Client update location configuration param updated for different platform
        - ``DB_TIMEZONE`` is changed to ``SERVER_TIMEZONE``
        - Added ``TEST_USER_ID`` param for bypassing secutiry check for test user

   - Project application initialize file ``app/config/initialize.php`` is changed according to changed configuration

   - Base model class bug fix for member functions

   - System library classes modified

   - Test api class controller is updated for more accurate server testing

- 


Version 1.0.1
==============

Release Date: Mar 14, 2019

-  General Changes

   -  API testing console form is updated for dynamic Base URL change
   -  Documentation Updated
   -  System library [JwtToken] function name is changed
   -  HTTP status code is changed for erroneous API request


Bug fixes for 1.0.1
====================

-  Model

   - Base model function toJsonHash modified for unset indexed array values 


Version 1.0.0
================

Release Date: March 08, 2019

First publicly released version.


