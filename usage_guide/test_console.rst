#####################
REST-API Test Console
#####################

Can be accessed through the link `http://rest-api.test/console`_

The left side of the console page shows the **list of API** in group and the right side shows the *Form and Result section* for selected API

The API definitions must be written inside the ``/console/api-list.json`` file in **JSON format**

Sample definition code is given below::

    {
        "groups": [
            {
                "name": "User Related",
                "contents": [
                    "user login",
                    "user logout"
                ]
            }
        ],
        "user login": {
            "title": "User Login",
            "action": "/user_login",
            "query": "client_type=1",
            "method": "POST",
            "json": "{\"email\": \"user@email.com\",\"password\": \"\",\"device_model\": \"Windows 10 Laptop\"}"
        },
        "user logout": {
            "title": "User Logout",
            "action": "/user_logout",
            "query": "",
            "method": "GET",
            "json": ""
        }
    }

For the above code example, the sections are described below:

  - **groups**

    + to group together all related API's as menu item in sidebar

    + **name** denotes the group's name, contents are the list of API which are described below of **groups**

  - API description
    
    + The json fields inside the parent field other than **groups** are the API definitions 

        - **title** : Title of the API, is shown under sidebar menu groups
        
        - **action** : API path, which must be concatenated with the base url to access / request that API

        - **query** : Query parameters which will be sent as GET params with the API request url

        - **method** : Intended API HTTP method 

        - **json** : JSON request body if any parameter is to be sent as json request [ POST / PATCH / PUT / DELETE ]


By selecting a API menu items, the right side form sections changes it's input field per API definitions 
    
If the REST-API application is installed and run successfully, the form will deliver appropriate JSON results based on the state of the server and database results. 
Responses are shown under the **Response** tab. 

The Test API in the menu section serves as the Server testing tool, if the api-server is fully functional, following result will be shown::

    {
        "result_code": 0,
        "time": "2019-03-28 09:27:26",
        "data": {
          "DB": "Database to user table connection is functional",
          "JWT": "JWT verification system is functional",
          "Log": "System application log is functional",
          "Cache": "Cache system is functional",
          "testImagUrl": "http://flight-api-v1.sol/uploads/profile_images/profiletest_1553761646.png",
          "Upload": "File upload system is functional"
        },
        "error": [],
        "execution_time": 0.2339470386505127
    }

In the above JSON response, the elements inside the ``data`` key describes 

- **DB**: Is the database server is connected with the Rest-API application server properly or not ? [ Assuming the existance of ``users`` table in database ]

- "JWT": Is JWT token creation and verification with the  secret key from configuration is functional or not ?

- "Log": Is text log [ file write permission and access ] feature is functional or not ?

- "Cache": 

  1. *filecache* : Check local file cache system

  2. *memcache* : Check server memcache system

- **Upload** : Check file upload system is functional or not

-  **TestImagUrl** : Check uploaded image is viewable or not
