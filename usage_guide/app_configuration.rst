#####################
Project Configuration
#####################

Project application settings is configured through the `config_app.ini` location in the `app/config` directory. Rename the file from `example.*.ini` 
then **change the configuration parameters** suitable to your machine environment.   

Configuration keys are explained below:

- **ENV**
    - Server environment of deployed project application 
    - e.g `development`, `beta`, `alpha`, `production` etc.
    - Sample code for retrieving the environment value is given below:
    ::

	$config = new Config_Config();
        echo $config->getEnv();
- **PRODUCTION_ENV** 
    - Set project environment status. 
    - Purpose of this parameter is for testing features which are dependent on specific credential key's [ e.g. switch Stripe payment feature as sandbox test or live production etc. ]
    ::

	$config = new Config_Config();
        if($config->checkProductionEnvironment()) {
            // Do your staff
        }
- **CLIENT_VERSION** 
    - Current application version in Server. 
    - Purpose of this parameter is to crosscheck the client request with the server application. 
    - If there exists multiple version of client software application, based on their client version parameter in API request, server application may decide whether the API request will be redirected to the proper url / directory path or disallow the request call  
    ::

	$config = new Config_Config();
        echo $config->getClientVersion();
- **CLIENT_UPDATE_LOCATION_ANDROID** 
    - Client application download link for android
    - Add the google play-store path
    ::

        $config = new Config_Config();
        echo $config->getClientUpdateLocation(Const_Application::PLATFORM_TYPE_ANDROID);
- **CLIENT_UPDATE_LOCATION_iOS** 
    - Client application download link for iPhone
    - Add the ios app-store path
    ::

        $config = new Config_Config();
        echo $config->getClientUpdateLocation(Const_Application::PLATFORM_TYPE_IOS);
- **CLIENT_UPDATE_LOCATION_WindowsApp** 
    - Client application download link for Windows phone 
    - Add the microsoft-store path
    ::

        $config = new Config_Config();
        echo $config->getClientUpdateLocation(Const_Application::PLATFORM_TYPE_WINDOWS);
- **ERROR_DUMP** 
    - Set error reporting status ON / OFF [ 0=OFF , 1=ON ] 
    - If ON, specific error message will be added alongside the API JSON response 
::

	"error_dump": {
            "code": 5,
            "file": "C:\\xampp\\htdocs\\template-api\\flight-v1\\app\\api\\BaseClass.php",
            "line": 279
        }
- **MAINTENANCE**
    - Set server maintenance mode ON / OFF [ 0=OFF , 1=ON ] 
    ::

	$config = new Config_Config();
        if($config->checkMaintenance()) {
            // Do your staff
        }
- **SUPPORT_MAIL_TO** 
    - Support mail address, where any project application related issues might be mailed at. 
    ::

	$config = new Config_Config();
        echo $config->getSupportMailAddress();
- **TEST_USER_ID** 
    - Purpose of *Test User ID* is to bypass all security, login validation, maintenance mode of server
    - Test user feature is useful for testing API server while project environment is on production
    ::

	$config = new Config_Config();
        if($logged_in_user_id == $config->getTestUserID()) {
            // ignore security cross checking
        }
- **DB_HOST** 
    - Database host name 
- **DB_NAME** 
    - Name of the database 
- **DB_USER** 
    - Database access username
- **DB_PASSWORD** 
    - Database access password
- **DB_PORT** 
    - Database connection port number
    ::

        $config = new Config_Config();
        $host = $config->getDatabaseHostName();
        $db = $config->getDatabaseName();
        $user = $config->getDatabaseUsername();
        $pass = $config->getDatabasePassword();
        $port = $config->getDatabasePort();

        $conn = new PDO("mysql:host=$host;dbname=$db;port=$port;", $user, $pass);
        if ($conn) {
            echo "Connected to the '$db' database successfully!";
        }
- **DB_SET_TIMEZONE** 
    - Set server timezone set mode ON / OFF [ 0=OFF , 1=ON ] 
    - This flag refers to whether mysql **database timezone** will be set as same as the Server or not
- **DB_TIMEZONE**
    - Server timezone 
    ::

        $conn = { PDO Connection }
        if ($conn) {
            if (Config_Config::getInstance()->isDbSetTimezone()) {
                $db_timezone = (new DateTime('now', new DateTimeZone(Config_Config::getInstance()->getServerTimezone())))->format('P');
                $conn->exec("SET time_zone='{$db_timezone}'");
            }
        }
- **LOCAL_CACHE_FLAG**
    - Local file cache system ON / OFF flag  [ 0=OFF , 1=ON ] 
    - if enabled, caching will be done in local directory 
- **LOCAL_CACHE_PATH**
    - Local file cache directory path
- **MEMCACHE_PREFIX**
    - Cache key prefix for specific data caching
- **MEMCACHED_HOST**
    - Memcache server host name
- **MEMCACHED_PORT**
    - Memcache server connection port number
- **APPLICATION_LOG**
    - Application log system ON / OFF flag [ 0=OFF , 1=ON ]
    - If enabled, any data / response will be written as log in local directory
- **LOG_FILE_PATH**
    - Application log file path in local directory









