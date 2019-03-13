# REST-API (flight-PHP)
A RESTful API template based on flight-PHP framework 

### What is Flight?

Flight is a fast, simple, extensible framework for PHP. Flight enables you to quickly and easily build RESTful web applications. [Weblink](http://flightphp.com/)

**NOTE:** This template is based on my recent API development experiences. I will not boast that its a very good template, 
this project may have some serious design flaws- which I may need to overcome in recent days. But I have tried my best to include some basic functionalities 
along with some help with third party PHP libraries so that young developers may find this project helpful to their Rest-API development and learning curve. 

## Getting Started

This project includes both file cache system and memcache system, along with JWT authentication process.

For testing purposes, I have added some database sample in `/public/resource` folder; 
and added some custom API along with a `/public/console` webform to communicate those API.

### System Requirements

`PHP 5.6` or greater. Some prerequisite modules are:
```
pdo memcache/memcached pdo_mysql gd gmp
```

`MySQL 5.6` or greater.

### Installing

**For windows (version 10)**

if you have xampp installed, steps are as follows

* Clone the repository or download zip then extract in a folder [e.g rest-api] under htdocs 
* Use [Composer](https://getcomposer.org/) to install or update dependencies and autoload required class directories. Make sure `composer.json` file is always present in root directory
```bash
$ composer update
```
* Create a virtual host. Go to `xampp\apache\conf\extra\` and open file `httpd-vhosts.conf`. Then write a similar configuration as follows:
```
<VirtualHost *:80>
	DocumentRoot "C:/Xampp/htdocs/rest-api/public"
	ServerName rest-api.test
	<Directory "C:/Xampp/htdocs/rest-api/public">
		Order allow,deny
		Allow from all
	</Directory>
</VirtualHost>
```
* Add your virtual-host information and point to the localhost IP. Go to `Windows\System32\drivers\etc` and open file `hosts` with admin permission. Then append a similar line as follows:
```
127.0.0.1	rest-api.test
```
* Restart your apache server [Note: *change your php.ini file if any module is missing. Check the apache logs if you get any unknown error*]

**For linux (Ubuntu)**

* Install Apache, MySQL server, PHP [ version 5.6 or higher ]
* Install Composer if not installed already
* Clone the repository to your desired root-directory

If you are using fresh ubuntu server, you may follow *My Project installation Guide* [ [https://pastebin.com/k79QVFRi](https://pastebin.com/k79QVFRi) ]

**Web Server Configuration**

* For *Apache*, edit your `.htaccess` file in root directory with the following:
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```
**Note**: If you need to use flight in a subdirectory add the line `RewriteBase /subdir/` just after `RewriteEngine On`.
* Go to the `app/config` directory inside your project folder. Rename the existing `example.*` file to `config_app.ini`. 
* Open your `config_app.ini` file and **change the configuration** parameters suitable to your machine environment.

That's it. You are ready to develop and test your API server. 

## Skeleton Architecture

The Rest-API project's skeleton is driven by the *Flight microframework*

All necessary routing is done in `app/route/route.php` and pointed to `app/api/Controller.php`

The *Controller* class receive the request method and fetch called api Class written inside the `app/api/` directory.

The called api path is camelized [*The 1st letter of the string and letter/s next to underscore `_` / Hypen `-` is camelized and underscore / hypen is removed*] 
before searching the *API Class*

To group more than one API class, keep the files under *Group-Name Folder* inside `app/api/` director [ e.g app/api/User/Login.php ]. 
The called api path which is related to a group is concatenated with underscore and camelized before searching the *API Class* [ e.g class User_Login ] 

The database query related functions are defined inside the `app/model` directory. 
All the DB Table model must be extended with *Base Model Class*

The *Config* Class in `app/config` directory handles most of the major server configurations

For MySQL Database connectivity `PDO Driver` is used
 
The *helper* classes are defined in `app/helper` directory. Available helper classes are
* ArrayUtil : Custom class for various array manipulations 
* CommonUtil : Custom utility class for frequently used for some common helper functions
* DateUtil : Custom class for various date/time manipulations

Application system classes are initialized in `app/system` directory. These classes are:
* Constant : System constants are defined here
* Exception : System exception class inheriting the `Exception Class` PHP 
* FileCacheClient : System file cache functions are defined here
* JwtToken : Implementing JWT token features from extending JWT library package [source link](https://jwt.io/)
* FileCacheClient : File cache class extending PhpFileCache library package [source link](https://github.com/Wruczek/PHP-File-Cache)
* MemcachedServer : Memcache implementation class
* Security : Security class

Server constant class definitions in `app/const` directory 

### Why use this?

REST or RESTful APIs used in distributed client/server interaction, like mobile application to server communication. If the REST structure in server is light
and robust, it can generate response fast and authentic client can retrieve the desired data quickly and process accordingly. And user always love to use server 
dependant application which can show data without much waiting. I have tried my best to implement as much essential feature as possible without making
the overall structure *complex* to understand. 

And this template is for those developers, who loves PHP. And a microframework is always faster than normal MVC framework like laravel, codeigniter etc.

You can follow my presentation slide on '[RESTApi Design & Develop](https://www.slideshare.net/rpm_ruoma/restapi-design-develop)'. 
I have tried to implement many features in this project mentioned in my slide tutorial, and I will continue working on this more ... 

## Development Guide

* Write your api class controller in `app/api/` directory
* Extend your API Controller Class  with *Base Class* 
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
 $data2 = $this->getValueFromInputPost('id', 'int', TRUE)   // Post Params [*POST*]
 $data3 = $this->getValueFromQuery('id', 'int');            // Query Params [*GET*]
```

* For exception handle inside your backend code, use the following code sample [**NOTE**: Check the `ResultCode` class for status-code definitions]
``` 
 throw new System_Exception(ResultCode::NOT_FOUND, "Data not found");
```

* To access system / config / model / helper / view classes, simply call their resources as follows [**NOTE**: Check the class name prefix; must be the similar with the directory name where resides]
``` 
 $data = Helper_DateUtil::getToday();
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

Table Column Definitions will help you which data to be showed in response and which data to be processed in database through the base model functions

* Check the existing API classes for further study
* If you wish to add new third party library, you may either add in `composer.json` and run the `composer update` command from root directory for autoload library classes, 
or you can put the PHP library in `app/lib` directory and extend your custom class inside the directory by defining class name with directory-name 
as classname-prefix `class Lib_Library {}`

## Features

Features of the system will be defined here shortly

## Running the tests

* Check the console webpage to test your API. URL: `http://rest-api.test/console`
* To check your system is fully cope with the project or not, simply goto the `Test API` Sidebar, click the underlining anchor-link, then click **Submit** button. 
The results and header parameters will be available in Response/ Header tabs under the form page. Check for following results as success:
```json
{
  "result_code": 0,
  "time": "2019-03-01 12:00:00",
  "data": {
    "DB": "Database to user table connection is functional",
    "JWT": "JWT token verification system is functional",
    "Log": "System application log is functional",
    "Cache": "Cache system is functional",
    "testImagUrl": "http://rest-api.url/uploads/profile_images/profiletest_1552473619.png",
    "Upload": "File upload system is functional"
  },
  "error": [],
  "execution_time": 0.06548380851745605
}
```
If any system functionality is not enabled, or incorporated with your PHP environment, above response messages will let you know the specifics.
* To populate your console page with you own API, edit `api-list.json` page with appropriate API information

## Used Libraries

Following third party libraries are used in Application system
* [PHP File Cache](https://github.com/Wruczek/PHP-File-Cache) - For local file caching
* [JWT](https://github.com/firebase/php-jwt) - Client request verification

## Author

* **Sabbir Hossain (Rupom)** - *Web Developer* - [https://sabbirrupom.com/](https://sabbirrupom.com/)

