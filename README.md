<!-- toc -->

- [A Cake2PHP website](#a-cake2php-3.x-application)
    + [Quickstart](#quickstart)
    + [Softwares](#softwares)
    + [Configuration](#configuration)
    + [Generate new administrator password](#generate-new-administrator-password)
    + [Plugins](#plugins)
    + [NodeJs packages](#nodejs-packages)
    + [Composer Plugins](#composer-plugins)
    + [Built-in Server](#built-in-server)
    + [PHPUnit Test](#phpunit-test)
    + [Common Issues](FAQ.md#common-issues)
    + [Build Platform](#build-platform)
    + [Docker Hub](#docker-hub)
    + [License](#license)

<!-- tocstop -->

A Cake2PHP website
==================
[![Build Status](https://github.com/b23prodtm/acake2php/actions/workflows/build.yml/badge.svg)](https://github.com/b23prodtm/acake2php/actions/workflows/build.yml)
[![CircleCI Status](https://circleci.com/gh/b23prodtm/acake2php.svg?style=svg)](https://app.circleci.com/pipelines/github/b23prodtm/acake2php)

> [The PHP-CMS eShop project was at the origin of this application](https://sourceforge.net/projects/pohse/)

Quickstart
----------
Using the basic container orchestrator or engine to deploy and test, is straitforward.
Currently the deployment script 
```. deploy.sh```
Based on [Balena engine](http://www.balena.io). See more about [NodeJs dependencies](#nodejs-dependencies)

[![balena deploy button](https://www.balena.io/deploy.svg)](https://dashboard.balena-cloud.com/deploy?repoUrl=https://github.com/b23prodtm/acake2php)

Define Environment Secrets: 

    - MYSQL_ROOT_PASSWORD=A-roOt!-Password
    - MYSQL_USER=someUser
    - MYSQL_PASSWORD=SomePassword
    - MASTER_PASSWORD=AdminPassword

MASTER_PASSWORD_HASH as described further in this file.

Softwares
---------
To deploy a server or onto a container manager like docker, you need at least a developer environment with the following software:
+ PHP 7.4 FPM (Alpine Linux)
+ Apache 2.4 HTTPD (Alpine Linux)
+ MariaDB 10.1 MySQL database
+ Balena Cloud Apps (NodeJS Package)

Configuration
-------------
Once you have got the server up and running (usually in a docker container), the website may not be reacheable until the database is configured.
You need to have network access to the host running the webservice as a container, just connect to it, with BalenaOS it's very easy:

    ./balena-connect-it.sh 22222 <user@host-ip> acake2php

Otherwise use:

    ssh -ttp <port> <user@host-ip> docker exec -it <container-name> "/bin/sh"

Once you're logged in, run as a normal user in `/usr/local/apache2/htdocs #` :

    ./configure.sh -d -i -u

It will configure PHP plugins and migrate the table in databases.
You can also test the configuration, lauch Cake Tests from `/usr/local/apache2/htdocs #` :

    ./test-cake.sh

#### **db** configuration

Container engines provides a sanbox virtual system with some persistent storage. To check that the last database migration was successful, open a pod shell :

    mysql -uroot --password=${MYSQL_ROOT_PASSWORD}

Issue some SQL statements, for instance :

    use aria_db; show tables;

#### **php-fpm** configuration

By editing the files `Config/app_local.template` and `Config/Schema/AppSchema.template` if you wish to modify the database connection and email transport.
You can then re-configure and migrate databases (configuration and migration)

     ./configure.sh -d -i -u
    cake schema update --connection=default

This migrates the databases.

    cake schema update --connection=test

This should mogrates the *test* databases.

More about configuration:

     ./configure.sh --help && ./migrate-database.sh --help

More [common issues](#common-issues)

#### Generate new administrator password

To sign in with staff rights, at http://localhost/admin/index.php, somebody needs a unique password stored in `MASTER_PASSWORD_HASH`. One way to generate this hashed password with "hashed“ encryption and setup:

    ./configure.sh -p <password> -s <hash>

To regenerate or read the current password hash again, simply browse to `http://localhost/php-cms/e13/etc/getHashPassword.php` or use php:

    php app/webroot/php-cms/e13/etc/getHashPassword.php -p password -s SomeSalt -f output.txt

Then copy it to environment variable `MASTER_PASSWORD_HASH` in runtime context:
 
    MASTER_PASSWORD_HASH=<EncryptedPassword>


One of them must be stored in the local server environment as a system readable variable.

#### PHPUnit Test
JUNIT tests are available with the following call to CAKE server:
Open a Terminal window:

    ./test-cake.sh

There are options (--runner, --travis) dedicated to continuous integration build environments. Use --help to see more about options.

See [below](#common-issues) to allow access on the built-in local service.

Plugins
-------
You do not need to change anything in your existing PHP project's repository.
There are various dependencies.
Plugins are registered in both _packages.json_ and  _app/composer.json_

#### Node modules 

* **package.json**

  List of dependencies to be installed with `npmjs` [here](https://www.npmjs.com).

      yarn install

  Re-Install the helper package [balena-cloud-apps](https://www.npmjs.com/package/balena-cloud-apps).
    
      ysrn add balena-cloud-apps

  whenever the system complains about `balena_deploy` not found.

#### Composer Plugins 

* Packagist **composer.json**

  Update all required plugins

      Scripts/composer.sh update

  [Packagist](https://packagist.org).

   Plugins home folder: 
       
       app/Vendor/<package-name>
       app/Plugin/<plugin-name>/

* **Templates files**

  Setup environment variables, build files, ready for deployment with any of the available targets:

      Scripts/update-templates.sh
  
* mod_rewrite.so
  The Apache rewrite module for URL becomes [obsolete on Apache with FPM Proxy FCGI]
  [FilesMatch in etc/apache2/site.conf]
  

Built-in Test Server
---------------
* CakePHP application also supports Docker
* MariaDB 10.1 and later

Start a *local* tedt server machine for testing on port 9000.
Open a Terminal window:

    ./configure.sh -d -i -u
    ./start-cake.sh --docker -c server -p 9000

> Ctrl-click the URL that appear on the terminal. It will open them in the browser. To get more help about the command line interface :

    ./start-cake.sh --help

Build Platform
--------------
   Make changes to **.template** files and update the various arch files. 
    
    update_templates
    ./deploy.sh

   Choose the target architecture 1, 2 or 3 , and then push to balena:
    
    2:balena

   Once connected to your Github account you'll have to push the source files.
   Deploy from a machine that has acces to the internet.
   
   Try local build options if you want to make a Build Test but balena is reliable and secure way.

   You are ready able to deploy to a balena fleet, using their original deployment process.

Docker Hub
----------

  You should configure a DOCKER_USER and DOCKER_PASS as environment variables. You may use an [access_token](https://docs.docker.com/security/for-developers/access-tokens/#use-an-access-token) for DOCKER_PASS for better security.

License
-------
	Copyright 2016-2025 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
