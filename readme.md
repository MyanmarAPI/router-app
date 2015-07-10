##Router App for MyanmarAPI

####Installation
1. Clone this project

		git clone https://github.com/MyanmarAPI/router-app.git
        
2. Install Composer packages

		composer install
        
If you don't have composer installed in your machine, please check [here](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

####Configuration

1. Please change your specific host urls in **config/app.php**

3. Duplicate **.env.example** and create your own **.env** file for application configuration.

2. Add Database Information in **.env** file.

3. Add **API_APP_KEY** and **API_APP_SECRET** in **.env** file for router app to interact with endpoints and other app parts.

4. You can turn on and off google analytics and internal report logging by changing **ANALYTIC_REPORT** and **GA_ANALYTIC** to false.

5. Add **AUTH_APP_KEY** and **AUTH_APP_SECRET** to interact with main application Auth.

4. If you are going to use **redis** for Queue Driver, please configure redis database information in **config/database.php**

####Add End Point

Add your endpoints information in following array format in **config/endpoints.php**

        'endpoint-uri' => [
            'name' => 'Name of the endpoint',
            'desc' => 'Endpoint description',
            'base' => 'http://base-url-of-endpoint.com/endpoint',
            'docs' => 'http://url-to-endpoint-docmentation.com/docs',
            'API_KEYS' => 'SomeRandomKey', //Leave it blank if your endpoint isn't filter with these keys.
            'API_SECRET' => 'SomeRandomSecret',
		]

#####API URL Format

		http://your-domain.org/{endpoint-uri}/{resource-uri}?api_key=your-api-key

####Packages 
* [Lumen PHP Framework](http://lumen.laravel.com)
* [theiconic/php-ga-measurement-protocol](https://github.com/theiconic/php-ga-measurement-protocol)
* [hexcores/mongo-lite](https://github.com/hexcores/mongo-lite)
* [illuminate/redis](https://github.com/illuminate/redis)