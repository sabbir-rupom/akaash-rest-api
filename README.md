# REST-API (flight-PHP)
A RESTful API template based on flight-PHP framework 

**NOTE:** This template is based on my recent API development experiences. I will not boast that its a very good template, 
this project may have some serious design flaws- which I may need to overcome in recent days. But I have tried my best to include some basic functionalities 
along with some help with third party PHP libraries so that young developers may find this project helpful to their Rest-API development and learning curve. 

## Getting Started

This project includes both file cache system and memcache system, along with JWT authentication process.

For testing purposes, I have added some database sample in `/resource` folder; 
and added some custom API along with a `/console` webform to communicate those API.

### System Requirements

`PHP 5.6` or greater. Some prerequisite modules are:
```
pdo memcache pdo_mysql gd gmp
```

`MySQL 5.6` or greater.

### Installing

**For windows (version 10)**

if you have xampp installed, steps are as follows

* Clone the repository or download zip then extract in a folder [e.g rest-api] under htdocs 
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
* Add your vhost information and point to the localhost IP. Go to `Windows\System32\drivers\etc` and open file `hosts` with admin permission. Then append a similar line as follows:
```
127.0.0.1	rest-api.test
```
* Restart your apache server [Note: change your php.ini file if any module is missing. Check the apache logs if you get any unknown error]
* Go to the `app\config` directory inside your project folder. Rename the existing `example.*` file to `config_app.ini`. 
* Open your `config_app.ini` file and change the configuration parameters suitable to your machine environment.
* Create an appropriate `.htaccess` file for your Flight-PHP server inside the project folder

That's it. You are ready to develop and test your API server. 

## Running the tests

Explain how to run the automated tests for this system

### Break down into end to end tests

Explain what these tests test and why

```
Give an example
```

### And coding style tests

Explain what these tests test and why

```
Give an example
```

## Deployment

Add additional notes about how to deploy this on a live system

## Built With

* [Dropwizard](http://www.dropwizard.io/1.0.2/docs/) - The web framework used
* [Maven](https://maven.apache.org/) - Dependency Management
* [ROME](https://rometools.github.io/rome/) - Used to generate RSS Feeds

## Contributing

Please read [CONTRIBUTING.md](https://gist.github.com/PurpleBooth/b24679402957c63ec426) for details on our code of conduct, and the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/your/project/tags). 

## Authors

* **Billie Thompson** - *Initial work* - [PurpleBooth](https://github.com/PurpleBooth)

See also the list of [contributors](https://github.com/your/project/contributors) who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* Hat tip to anyone whose code was used
* Inspiration
* etc

