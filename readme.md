##Router App for MyanmarAPI

####Installation
1. Clone this project

		git clone https://github.com/MyanmarAPI/router-app.git
        
2. Install Composer packages

		composer install
        
If you don't have composer installed in your machine, please check [here](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx)

####Configuration

1. Please change your specific host urls in **config/app.php**

	* Auth Url
	* Main Documentation Url
	* Google Analytic Information

3. Duplicate **.env.example** and create your own **.env** file for application configuration.

2. Add Database Information in **.env** file.

		DB_CONNECTION=mongodb
		DB_HOST=localhost
		DB_PORT=27017
		DB_DATABASE=myanmarapi
		DB_USERNAME=
		DB_PASSWORD=

3. Add **X-API-KEY** and **X-API-SECRET** in **.env** file for router app to interact with endpoints and other app parts.

		## These are example keys. Please generate your own keys at server deployment.

		X-API-KEY=cPjF5lGG8NVCbSiueyWFrwTkLtS3aJxE
		X-API-SECRET=MpFLIgqhj5EDl8jmufDn5rvX1ItYoeCm

4. You can turn on and off google analytics and internal report logging by changing **ANALYTIC_REPORT** and **GA_ANALYTIC** to false.

		ANALYTIC_REPORT=true
		GA_ANALYTIC=true

5. Add **AUTH_APP_KEY** and **AUTH_APP_SECRET** to interact with main application Auth.
		
		## These are example keys. Please generate your own keys at server deployment.

		AUTH_APP_KEY=3zkx76mmdXAnaqmMnQ7jcbii2fCGlx7o
		AUTH_APP_SECRET=6CkWAf2gun14B6tKKmXGWh3nHKQgbqh7

4. If you are going to use **redis** for Queue Driver, please configure redis database information in **config/database.php**

		'redis' => [

		    'cluster' => false,

		    'default' => [
		        'host'     => '127.0.0.1',
		        'port'     => 6379,
		        'database' => 0,
		    ],

		]

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

#####How to use API router

For unique user tracking, you need to generate a token for each user of your application and use that token to request the data.

1. Generate token for each user
		
   Send POST request to
        http://your-domain.org/token/generate
   With api_key 
   
   	{
        	api_key : 'your-api-key'
     	}
        
   You will get a response like this
   	{
    		"_meta": {
        	"status": "ok",
        	"count": 1,
        	"api_version": 1
    	},
    	"data": {
        	"token": "user-token-key"
    		}
		}
        
2. Request data with user token
        
	Request Data
		http://your-domain.org/{endpoint-uri}/{resource-uri}?token=user-token-key
        
	Get Data in Zawgyi Font
    	http://your-domain.org/{endpoint-uri}/{resource-uri}?font=zawgyi&token=user-token-key

####Packages 
* [Lumen PHP Framework](http://lumen.laravel.com)
* [theiconic/php-ga-measurement-protocol](https://github.com/theiconic/php-ga-measurement-protocol)
* [hexcores/mongo-lite](https://github.com/hexcores/mongo-lite)
* [illuminate/redis](https://github.com/illuminate/redis)
* [hexcores/api-support](https://github.com/hexcores/api-support)
* [rabbit-converter/rabbit-php](https://github.com/Rabbit-Converter/Rabbit-PHP)

####LICENSE

**GNU Lesser General Public License v3 (LGPL-3)**