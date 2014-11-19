# Kakadu - The free learning system

Kakadu is a PHP website that focuses on learning. Users can create learngroups, courses, catalogs and questions and 
share them with other users. The project features a learning algorithm which aims at selecting the best questions for the user such that the learning success is as high as possible.

Kakadu is based on the following open source projects:
- Laravel - A clean and classy framework for PHP web development
- Sentry - A framework agnostic authentication & authorization system for Laravel
- PHPExcel - A pure PHP library for reading and writing spreadsheet files
- jQuery - A multi-browser JavaScript library designed to simplify the client-side scripting of HTML
- Bootstrap -  A free collection of tools for creating websites and web applications
- Backbone - A JavaScript library with a RESTful JSON interface

## Requirements
- Apache web server with PHP >= 5.3.7
- MySQL Database
- Composer
- HP >= 5.3.7
- MCrypt PHP Extension
- Git

## Installation
- Download Kakadu
- Upload content to your web server
- Verify that the following directories and files are writeable:
  - storage/view
  - app/config/database_kakadu.php
  - app/config/app.php
There are two possible ways to install Kakadu

per Installer:

Installer creates a user account for you and connects to your created
MySQL database
  - Create your MySQL database
  - Point your Apache VirtualHost configuration to the public
folder
<pre><code>
    &lt;VirtualHost *:80&gt;
        DocumentRoot /Users/Kakadu/Sites/kakadu/public
        ServerName yourwebsite.com
    &lt;/VirtualHost&gt;
    
</code></pre>
  - Open the installation with your browser at http://yourwebsite.com/install
  - Input your desired display name, e-mail and password for
your user account
  - Input your Host(e.g. localhost), MySQL database name,
username and password
  - Go to app/filters.php and make sure to write the url of your client in Access-Control-Allow-Origin.

per Controller:
  - Go to app/config/database.php
In connections by mysql: write in the assignments "host", "database", "username", "password" your MySQL host, database, username and password.
  - Go to app/database/seeds/UserSeeder.php
Create your user account with desired email and password
  - Go back to your root folder and write following commands
  - php artisan key:generate
  - php artisan migrate --package=cartalyst/sentry
  - php artisan migrate
  - php artisan db:seed
  - Go to app/filters.php and make sure to write the url of your client in Access-Control-Allow-Origin.

## License
Kakadu is open-sourced software licensed under the MIT License.
