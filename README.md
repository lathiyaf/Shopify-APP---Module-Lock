# Shopify Module Lock App

### Installation
Install the dependencies and devDependencies.

For both environment
```sh
$ composer install
$ cp .env.example .env 
$ nano .env // set all credentials(ex: database, shopify api key and secret)
$ php artisan migrate
```

For development environments...

```sh
$ npm install
$ npm run dev
```
For production environments...

```sh
$ npm install --production
$ npm run prod
```
add schedular in crontab

ex. * * * * * cd [path-to-project][project-name] && php artisan schedule:run >> /dev/null 2>&1
 

### Used Shopify Tools

* Admin rest-api
* Shopify graphQl
* App-bridge


### Document

* Module lock used to lock page, collection or product.
* Filter resources using graphql api.
* Create snippet while creating/updating lock.
* Add product and collection update webhook to detect delete page/collection.
* Add scheduler to delect deleted page. which run every five minutes.
