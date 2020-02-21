# Akaash - RESTful API template

[**This version is in testing phase**] 

**Akaash**[ REST-API-PHP-flight: version 2 ] is a restful API template built with PHP driven by flight microframework. It is light weighted with some helpful services like logger, caching etc. 
The system architecture is modified with some solid principles and optimized the source code in PSR-2 coding standard. 

NOTE: This template version is based on my recent web development experiences. I will not boast that its a very good project. 
This may have some architectural flaws- which I may need to overcome in recent days. But I have tried my best to develop this with appropriate PHP coding standard 
and good architecture. I am still learning to overcome the flaws and trying to make this rest-template project simple, robust and developer friendly.

### What is Flight?

Flight is a fast, simple, extensible framework for PHP. Flight enables you to quickly and easily build RESTful web applications. [Weblink](http://flightphp.com/)

## Getting Started

This project includes both file cache system and memcache system. To make the project more simple to handle, I have made the project more scalable 
than the previous version of this template with appropriate configuration settings.  

For testing purposes and examples, I have added a database sample in `/public/resource` folder; 
and added some custom API along with a `/public/console` web form to communicate those API and view responses

### System Requirements

`PHP 5.6` or greater. Some prerequisite modules are:
```
pdo memcache/memcached pdo_mysql
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
	DocumentRoot "C:/Xampp/htdocs/akaash/public"
	ServerName akaash.test
	<Directory "C:/Xampp/htdocs/akaash/public">
		Order allow,deny
		Allow from all
	</Directory>
</VirtualHost>
```
* Add your virtual-host information and point to the localhost IP. Go to `Windows\System32\drivers\etc` and open file `hosts` with admin permission. Then append a similar line as follows:
```
127.0.0.1	akaash.test
```
* Restart your apache server [Note: *update your php.ini file if any module is missing. Check the apache logs if you get any unknown error*]
* [**NOTE**] If you are having trouble installing memcache in your system, simply follow [ this guide ](https://commaster.net/content/installing-memcached-windows) 
  * If your windows is 64bit and PHP version 7.2, use the ts version of `php-7.2.x_memcache.dll` as module extension
  * Use the binary version of `memcached-win64-1.4.*.zip` to install memcache server. Follow the instructions accordingly.

**For linux (Ubuntu)**

* Install Apache, MySQL server, PHP [ version 5.6 or higher ]
* Install Composer if not installed already
* Clone the repository to your desired root-directory

**Web Server Configuration**

* For *Apache*, add or edit `.htaccess` file in your host root directory with the following:
```
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```
* Go to the `app/config` directory inside your project folder. Rename the existing `example.*` file to `app_config.ini`. 
* Open your `app_config.ini` file and **change the configuration** parameters suitable to your host environment.

That's it. You are ready to develop and test your API project. 

### Why use this?

RESTful APIs used in distributed client/server interaction, like the communication system between mobile application and a server. If the REST Application is light
and robust, server can process and generate response fast and authentic client can retrieve the desired data and process faster. And an user always loves to use those server 
dependent application which can process data fast. I have tried my best to implement as much essential feature as possible without making the overall structure *complex*. 

And this template is executes faster than traditional MVC framework like laravel, codeigniter etc.

You can follow my presentation slide on '[RESTApi Design & Develop](https://www.slideshare.net/rpm_ruoma/restapi-design-develop)'. 
I have tried to implement many features in this project mentioned in my slide tutorial, and I will continue working on this more ... 

## Development Guide

* Write your api class controllers in `app/api/` directory
* Yo may either extend your API Controller Class with the *BaseClass* provided with the template, or just write your own codes
* Sample API Controller Class: 
  * If endpoint: `http://{base-url}/get_user_information` , write your API class `GetUserInformation` in `app/api/` directory
  * If endpoint: `http://{base-url}/user/get_information` , write your API class `GetInformation` in `app/api/user` directory
  * If endpoint: `http://{base-url}/user/get_information/1`, if the API class exists as above, the 3rd segment will be counted a query string
  * For more information, check the provided API examples

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
            * Put successful response in array here
            */
        );
    }
}
```

* For exception handle inside your backend code, use the following code sample [**NOTE**: Check the `ResultCode` class for status-code definitions]
``` 
 use System\Exception\AppException;

 throw new AppException(ResultCode::NOT_FOUND, "Data not found");
```

* To access system / model / helper / view classes, simply call their resources using namespaces
``` 
 use Helper\DateUtil;
 $data = DateUtil::getToday();
```

* Write your model class in `app/model` directory as follows
```php

 namespace Model;

 use System\Core\Model\Base as BaseModel;

 class User extends BaseModel {      
    // Define your class variables and constants here

    /**
     * Table definitions
     */
    const TABLE_NAME = "users";
    
    const PRIMARY_KEY = "user_id";

    /* Table Column Definitions */
    protected static $columnDefs = array(
        'user_id' => array(
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
        Add your model class methods here
    */
}
```
[**NOTE**]

Try to add separate model classes for each tables in your database to retain integrity

Table Column Definitions will help you which data to be showed in response and which data to be processed in database through methods of the base model class

* Check the API model examples for further study
* If you wish to add new third party library, you may either add in `composer.json` and run the `composer update` command from root directory for autoload library classes, 
or you can put the PHP library in `app/lib` directory and extend your custom class inside the directory by defining class name with correct namespace

<!-- If you get any error like `"Class '' not found"` during development, you may need to run the `composer update` command from root directory (*if any new class files not loaded*) -->

## Features

This project does not include much features, but I have tried to add some to make the application more user-friendly. Let's see these features:

* Integration of **flight-microframework** - which is extremely light weight. To learn it's features, simply go to this [ ::learn flight-PHP:: ](http://flightphp.com/learn/)

* A **Model-View-Controller** like system architecture

* **Single Configuration** file

* MySQL database connection support with **PDO driver** . If you are not familiar with PDO, please visit and learn the basics [ ::pdo tutorial:: ](https://phpdelusions.net/pdo) 

* **Helper** Utility classes: Array, Date, Common

* **System Library** : core, exception, log, message, security

* **Console** testing [ ::view doc:: ](usage_guide/test_console.rst)

## Changelog and Updates

You can find a list of all changes for each release/version in the [change log](https://github.com/sabbir-rupom/akaash-rest-api/blob/master/changelog.rst) 

## Running the tests

* Check the console webpage to test your API. URL: `http://akaash.test/console`

**Note**: If you need to use this project in sub-directory under the *Root Directory*, 
you may need change the *Base URL* path in the form / console.js - whatever is suited for you

* To check your system is fully cope with the project or not, simply goto the `Test API` from Sidebar, click the underlining anchor-link, then click **Submit** button 
from the right form section. The results and header parameters will be available in Response/ Header tabs under the form page. If your server is fully coped with 
Akaash REST-API template, it will show the following results as success:
```javascript
// For endpoint like: http://akaash.test/test/2020-02-20/1
{
  "result_code": 0,
  "time": "2020-02-20 00:10:20",
  "data": {
    "DB": "Database is properly connected",
    "Log": "System application log is functional",
    "Cache": {
      "filecache": "Local filecache system is functional",
      "memcache": "Memcache system is functional"
    },
    "Upload": "File upload directory permission is set properly",
    "Value": [
      "2020-02-20",
      "1"
    ]
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

This project is licensed under the [MIT License](LICENSE).








