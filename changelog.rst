##########
Change Log
##########

Version 2.0.0
=============

Release Date: Not Released

-  General Changes

   -  

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
