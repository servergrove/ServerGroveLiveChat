What is ServerGroveLiveChat?
----------------------------

ServerGroveLiveChat is a PHP 5.3 Symfony 2 application that allows website visitors to engage in a web-based chat
with operators of a website. It allows a company to offer live chat support.

Requirements
------------

* PHP 5.3.2 or newer
* PHP mongo extension
* MongDB database server
* jQuery in your website to integrate the livechat button/tracker

Installation
------------

1. Download application package or [clone the repository](https://github.com/servergrove/ServerGroveLiveChat)

2. Setup a virtual host (optional) and restart web server

    <VirtualHost *:80>
        DocumentRoot /usr/local/ServerGroveLiveChat/web
        ServerName livechat.example.com
        ErrorLog "logs/livechat-error_log"
        CustomLog "logs/livechat-access_log" combined
        <Directory /usr/local/ServerGroveLiveChat/web>
            AllowOverride All
        </Directory>
    </VirtualHost>

3. Make sure web server can write to app/cache and app/logs, ie.:

        chmod -R 777 app/cache app/logs

4. Configure MongoDB connection, by default it will connect with localhost, you can change it by editing app/config/config.yml

        doctrine_odm.mongodb:
          server: mongodb://localhost:27017


5. Configure the livechat caching engine. The application uses a caching engine to store chat status information. By default it will use APC, but you can also use mongo. You can configure it like this in app/config/config.yml:

        sglivechat.config:
          cache_engine: mongo


6. Add a livechat administrator

        ./app/console sglivechat:admin:add-administrator "Your Name" email@example.com password1234

7. Launch launch administration interface and login with the administration information entered in step 6

        http://livechat.example.com/admin/sglivechat

8. Add the livechat button and status indicator to your website:

    In `<head>`:
        `<script src="http://livechat.example.com/js/jquery.js"></script>`

    In the location where you want the livechat button to appear:
        `<script src="http://livechat.example.com/js/sglivechat-tracker/status.js"></script>`

9. You can also test the livechat functionality by loading:

    http://livechat.example.com/test.html

Contributing
------------

We encourage people to participate and contribute to the project. Feel free to clone the git repository and send us pull requests.
Please contact us before starting a new feature to make sure there is no effort duplication.

Todo
----

* Integration with Jabber for new chats alerts
* Additional documentation
* Bug fixes

