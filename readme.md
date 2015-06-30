##Router App for MyanmarAPI

####Installation
1. Clone this project

		git clone https://github.com/MyanmarAPI/router-app.git
        
2. Install Composer packages

		composer install
        
If you don't have composer installed in your machine, please check [here](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

####Configuration
1. Please change your specific host urls in **config/app.php**

2. Add Database Information in **.env** file.

3. If you are going to use **redis** for Queue Driver, please configure redis database information in **config/database.php**

####Add End Point

Add your endpoints information in following array format in **config/endpoints.php**

        'endpoint-uri' => [
            'name' => 'Name of the endpoint',
            'desc' => 'Endpoint description',
            'base' => 'http://base-url-of-endpoint.com/endpoint',
            'docs' => 'http://url-to-endpoint-docmentation.com/docs'
		]

####Packages 
* [Lumen PHP Framework](http://lumen.laravel.com)
* [theiconic/php-ga-measurement-protocol](https://github.com/theiconic/php-ga-measurement-protocol)