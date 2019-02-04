# waze-checker

## Prerequisites

You will need the following things properly installed on your computer.

* [Git](https://git-scm.com/)
* [Node.js](https://nodejs.org/) ([nvm](https://github.com/creationix/nvm))
* [Ember CLI](https://ember-cli.com/)
* [PhantomJS](http://phantomjs.org/)
* php ^7.2
* nginx
* mysql
* [Composer](https://getcomposer.org/)

## Installation

* `git clone git@gitlab.com:ixxvivxxi/waze-checker.git`
### backend
* `cd waze-checker/backend-php`
* `composer install`
* change database config `backend-php/application/config/database.php`:
```
	'hostname' => 'localhost',
	'username' => 'username',
	'password' => 'password',
	'database' => 'database',
```
* `git update-index --assume-unchanged backend-php/application/config/database.php`
* nginx config: 
`sudo nano /etc/nginx/sites-enabled/waze-checker.local`
```
server {
     listen 80;

     root /path-to-project/waze-checker/backend-php/public;
     index index.php index.html index.htm;

     server_name waze-checker.local;

     location / {
        try_files $uri $uri/ /index.php$is_args$args;
     }

     location ~ ^/assets/.*\.php$ {
        deny all;
     }

     location ~ \.php$ {
         include snippets/fastcgi-php.conf;
         fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
         fastcgi_read_timeout 300;
     }

     location ~ /\. {
         deny all;
     }
 }
 ```
* `sudo ln -s /etc/nginx/sites-available/waze-checker.local /etc/nginx/sites-enabled/`
* add to /etc/hosts: `127.0.0.1 waze-checker.local`
* `sudo systemctl reload nginx`
* run migrations [http://waze-checker.local/api/update](http://waze-checker.local/api/update)

### frontend
* install node.js with [nvm](https://github.com/creationix/nvm)
* install ember.js (2.18) `npm install -g ember-cli@2.18.2`
* `cd waze-checker/frontend`
* `npm install`
* [get google map API Key](https://developers.google.com/maps/documentation/javascript/get-api-key) 
* change environment config `frontend/config/environment.js`:
```javascript
var ENV = {
    googleMapKey: 'APIKey',
}
```
* `git update-index --assume-unchanged frontend/config/environment.js`
* `ember s`
* Visit app at [http://localhost:4200](http://localhost:4200).

## Building
* change `build.conf` with you settings
* `git update-index --assume-unchanged build.conf`
* `./build.sh`
