# REST-API PHP Template
A RESTful API template in PHP driven by *flight microframework* 

**NOTE:** This template is based on my recent API development experiences. I will not boast that its a very good template. 
This project may have some serious design flaws- which I may need to overcome in recent days. But I have tried my best to include some basic functionalities 
along with some help with third party PHP libraries so that young developers may find this project helpful to their Rest-API development and learning curve. 

### What is flight-PHP?

Flight is a fast, simple, extensible framework for PHP. Flight enables you to quickly and easily build RESTful web applications. 
[Learn more about Flight-PHP](http://flightphp.com/)

## Getting Started

This project includes both file cache system and memcache system, along with simple JWT authentication process.

For testing purposes, I have added a database sample with some tables in `/resource` folder. 

I have also added some custom API along with a `/console` form as webpage to communicate those API.

### System Requirements

`PHP 5.6` or greater. Some prerequisite modules are:
```
pdo memcache/memcached pdo_mysql gd gmp
```

`MySQL 5.6` or greater.

### Installing

**For windows (version 10)**

if you have xampp installed, steps are as follows:

* Clone the repository or download zip then extract in a folder [e.g rest-api] under htdocs 
* Use [Composer](https://getcomposer.org/) to install or update library dependencies. Make sure `composer.json` file is always present in root directory
```bash
$ composer update
```
* Create a virtual host. Go to `xampp\apache\conf\extra\` and open file `httpd-vhosts.conf`. Then write a similar configuration as follows:
```
<VirtualHost *:80>
	DocumentRoot "C:/Xampp/htdocs/rest-api"
	ServerName rest-api.test
	<Directory "C:/Xampp/htdocs/rest-api">
		Order allow,deny
		Allow from all
	</Directory>
</VirtualHost>
```
* Add your virtual-host information and point to the localhost IP. Go to `Windows\System32\drivers\etc` and open file `hosts` with admin permission. Then append a similar line as follows:
```
127.0.0.1	rest-api.test
```
* Restart your apache server [Note: *update your php.ini file if any module is missing. Check the apache logs if you get any unknown error*]
* [**NOTE**] If you are having trouble installing memcache in your system, simply follow [ this guide ](https://commaster.net/content/installing-memcached-windows) 
  * If your windows is 64bit and PHP version 7.2, use the ts version of `php-7.2.x_memcache.dll` as module extension
  * Use the binary version of `memcached-win64-1.4.*.zip` to install memcache server. Follow the instructions accordingly.

**For linux (Ubuntu)**

* Install Apache, MySQL server, PHP [ version 5.6 or higher ]
* Install Composer if not installed already
* Clone the repository to your desired root-directory

If you are using fresh ubuntu server, you may follow *My Project installation Guide* [ [https://pastebin.com/KMpSAhYK](https://pastebin.com/KMpSAhYK) ]

**Web Server Configuration**

* For *Apache*, add or edit `.htaccess` file in your host root directory with the following:
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```
* Go to the `app/config` directory inside your project folder. Rename the existing `example.*` file to `config_app.ini`. 
* Open your `config_app.ini` file and **change the configuration** parameters suitable to your host environment.

That's it. You are ready to develop and test your API server. 

### Why use this?

RESTful APIs used in distributed client/server interaction, like mobile application to server communication. If the REST structure in server is light
and robust, it can generate response fast and authentic client can retrieve the desired data quickly and process accordingly. And user always love to use those application 
which can communicate with server and show respective data without delay. I have tried my best to implement as much essential feature as possible without making
the overall structure *complex* to understand. 

And this template is for those developers, who loves to code in PHP. And a microframework is always faster than normal MVC framework like laravel, codeigniter etc.

You can follow my presentation slide on '[RESTApi Design & Develop](https://www.slideshare.net/rpm_ruoma/restapi-design-develop)'. 
I have tried to implement many features in this project mentioned in my slide tutorial, and I will continue working on this more ... 

## Development Guide

* Write your api class controller in `app/api/` directory
* Extend your API Controller Class with *Base Class* 
* Sample API Controller Class, [**Note**: If your API class name is `GetUserInformation`, your request URL will be `http://rest-api.test/api/get_user_information`]

```php
class GetUserInformation extends BaseClass {        
    /*
    * Define your class variables and constants here
    */

    public function validate() {
        parent::validate();
        /*
         * Add your code here
         * to retrieve your request parameters
         */
    }

    public function action() {

        /*
         * Add API code here
         */
        
        return array(
            /*
            * Put successful response here
            */
        );
    }
}
```

* To retrieve parameters from request method, use the following code sample [**NOTE**: Allowed HTTP methods are `GET | POST | PUT | DELETE`]
``` 
 $data1 = $this->getValueFromJSON('item', 'string', FALSE);  // JSON Request [*POST / PUT / DELETE*]  
 $data2 = $this->getInputPost('id', 'int', TRUE);           // Post Params [*POST*]
 $data3 = $this->getValueFromQuery('id', 'int');            // Query Params [*GET*]
```

* For exception handle inside your backend code, use the following code sample [**NOTE**: Check the `ResultCode` class for status-code definitions]
``` 
 throw new System_ApiException(ResultCode::NOT_FOUND, "Data not found");
```

* To access system / model / common / config classes, simply call their resources as follows 
[**NOTE**: Check the class name prefix; must be the similar with the directory name where resides]
``` 
 $data = Common_DateUtil::getToday();
```

* Write your model class in `app/model` directory as follows
```php
class Model_TableName extends Model_BaseModel {      
    // Define your class variables and constants here

    /* Database Table Name */
    const TABLE_NAME = "users";

    /* Table Column Definitions */
    protected static $columnDefs = array(
        'id' => array(
            'type' => 'int',
            'json' => true
        ),
        'name' => array(
            'type' => 'string',
            'json' => true
        ),
        'created_at' => array(
            'type' => 'string',
            'json' => false
        ));

    /*
        Add your code for functions here
    */
}
```
[**NOTE**]

Try to add separate model classes for each tables in your database.

Table Column Definitions will help you organize your table data with desired data-type in JSON response as well as handles data insertion in table through the base model functions

* Check the existing API classes for further study
<!-- If you get any error like `"Class '' not found"` during development, you may need to run the `composer update` command from root directory (*if any new class files not loaded*) -->

## Features

This project does not include much features, but I have tried to add some to make the application more user-friendly. Let's see these features:

* Integration of **flight-microframework** - which is extremely light weight. To learn it's features, simply go to this [ ::learn flight-PHP:: ](http://flightphp.com/learn/)

* A **Model-View-Controller** like system architecture [ ::view doc:: ](usage_guide/system_architecture.rst)

* **Single Configuration** file with Config class [ ::view doc:: ](usage_guide/app_configuration.rst)

* **Base Class** controller for Client Request validation [ ::view doc:: ](usage_guide/request_validation_class.rst)

* MySQL database connection support with **PDO driver** . If you are not familiar with PDO, please visit and learn the basics [ ::pdo tutorial:: ](https://phpdelusions.net/pdo) 

* **Abstract Base Model** class for database **Query Builder Function** support [ ::view doc:: ](usage_guide/query_builder_class.rst)

* **Common Helper** classes: Array, Date, Log, Utils(Utility)

* **System Library** classes: ApiException, FileCacheClient, JwtToken, MemcachedServer, Security [ ::view doc:: ](usage_guide/system_library.rst)

* **Application Constant** class [ ::view doc:: ](usage_guide/application_constant.rst)

* **Result-Code** class for supporting exception handle with necessary status codes [ ::view doc:: ](usage_guide/result_code.rst)

* **Console** testing [ ::view doc:: ](usage_guide/test_console.rst)

* Others [ ::view doc:: ](usage_guide/other_features.rst)

## Changelog and Updates

You can find a list of all changes for each release/version in the [change log](https://github.com/sabbir-rupom/rest-api-flight-PHP/blob/master/changelog.rst) 

## Running the tests

* Check the console webpage to test your API. URL: `http://rest-api.test/console`

**Note**: If you need to use this project-template in sub-directory under the *Root Directory*, 
you may need change the *Base URL* path in the form / console.js - whatever is suited for you

* To check your system is fully cope with the project or not, simply goto the `Test API` from Sidebar, click the underlining anchor-link, then click **Submit** button 
from the right form section. The results and header parameters will be available in Response/ Header tabs under the form page. If your server is fully coped with 
this REST-API template, it will show the following results as success:
```javascript
{
  "result_code": 0,
  "time": "2019-03-31 00:10:20",
  "data": {
    "DB": "Database to user table connection is functional",
    "JWT": "JWT verification system is functional",
    "Log": "System application log is functional",
    "Cache": {
      "filecache": "Local filecache system is functional",
      "memcache": "Memcache system is functional"
    },
    "Upload": "File upload system is functional",
    "TestImagUrl": "http://flight-api-v1.test/uploads/profile_images/profiletest_1553974673.png"
  },
  "error": [],
  "execution_time": 0.03506016731262207
}
```
* To populate your console page with you own API, edit `api-list.json` page with appropriate API information
* This project is tested in PHP version 5.6 to 7.2.4 . Any version of PHP below the lower number or upper may cause errors during project run. 

## Used Libraries

Following third party libraries are used in Application system
* [PHP File Cache](https://github.com/Wruczek/PHP-File-Cache) - For local file caching
* [JWT](https://github.com/firebase/php-jwt) - Client request verification

## Author

* **Sabbir Hossain (Rupom)** - *Web Developer* - [https://sabbirrupom.com/](https://sabbirrupom.com/)

## License

This project is open source and available under the [MIT License](LICENSE).












