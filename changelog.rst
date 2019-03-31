##########
Change Log
##########

Version 2.0.0
=============

Release Date: Not Released

-  General Changes

   -  


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
