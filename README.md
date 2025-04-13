<!-- toc -->

- [A Cake2PHP 3.x application ](#a-cake2php-3.x-application)
    + [Quickstart](#quickstart)
    - [Plugins](#plugins)
      + [NodeJs packages](#nodejs-packages)
      + [Composer Plugins](#composer-plugins)
    + [Local Built-in Server](#local-built-in-server)
    + [PHPUnit Test](#phpunit-test)
    + [Device pod environment](#device-pod-environment)
    - [Database terminal](#database-terminal)
      + [More Database Configuration](#more-database-configuration)
      + [Generate new administrator password](#generate-new-administrator-password)
    + [Common Issues](FAQ.md#common-issues)
    + [Cross Platform](#cross-platform)
    + [Docker Hub](#docker-hub)
    + [License](#license)

<!-- tocstop -->

A Cake2PHP 3.x application 
=========================
[![TravisCI Status](https://app.travis-ci.com/b23prodtm/acake2php.svg?token=VkN3AkpvB5yVGfXx1qj5&branch=development)](https://travis-ci.com/b23prodtm/acake2php)
[![CircleCI Status](https://circleci.com/gh/b23prodtm/acake2php.svg?style=svg)](https://app.circleci.com/pipelines/github/b23prodtm/acake2php)

> [The PHP-CMS eShop project was at the origin of this application](https://sourceforge.net/projects/pohse/)

Quickstart
----------
Using the basic container orchestrator or engine to deploy and test, is straitforward.
Currently the deployment script 
```. deploy.sh```
Based on [Balena engine](http://www.balena.io). See more about [NodeJs dependencies](#nodejs-dependencies)

[![balena deploy button](https://www.balena.io/deploy.svg)](https://dashboard.balena-cloud.com/deploy?repoUrl=https://github.com/b23prodtm/acake2php)

Requirements
------------
To deploy a server or onto a container manager like docker, you need at least a developer environment with the following software:
+ PHP 7.4 or later in PATH
+ NodeJS 19 or later in PATH and NPM or Yarn (recommended in Windows)
+ Package managers NPM or Yarn, also HomeBrew, MacPorts or Chocolatey, etc.
+ a Docker setup (Mac or PC) or BalenaEngine (Linux)
+ (recommended for Windows) git unix-style shell, like Git Bash
+ OpenSSH Agent with key-pair (.ssh/*.pub) must be added to the webserver host

Configuration
-------------
Once you have got the server up and running (usually in a docker container), the website may not be reacheable until the database is configured.
You need to have network access to the host running the webservice as a container, just connect to it, with BalenaOS it's very easy:

    ./balena-connect-it.sh 22222 <user@host-ip> acake2php

Otherwise use:

    ssh -ttp <port> <user@host-ip> docker exec -it <container-name> "/bin/sh"

Once you're logged in, run as a normal user in `/var/www/localhost/htdocs #` :

    ./configure.sh -d -u -i

It will configure PHP plugins and migrate the table in databases.
You can also test the configuration, lauch Cake Tests from `/var/www/localhost/htdocs #` :

    ./test-cake.sh

Plugins
-------
You do not need to change anything in your existing PHP project's repository.
However, if these files exist they will affect the behavior of the build process:

* Git **submodules**

  The acake2php folder includes modules that need to be pulled in order to install locally.
  After the first checkout browse to acake2php folder and do
  
    git submodule sync && git submodule update --init --recursive
  
  You'll see modules populating the subfolder app/webroot/... If something goes wrong, erase the acake2php folder and start over.
> After
>     git checkout
>  each time, run once
>
>     git submodule update --init --recursive
>  to ensure submodules are downloaded from git. Otherwise your build may fail.

* Packagist **composer.json**

  Update all required plugins

      composer update

  [Packagist](https://packagist.org).


#### NodeJs packages

* Modules **package.json**

  List of dependencies to be installed with `npmjs` [here](https://www.npmjs.com).

      npm update
  
  or:

      yarn

  You can install NPM/YARN helper package [balena-cloud-apps](https://www.npmjs.com/package/balena-cloud-apps).

   To use it:
   
      npm install -g balena-cloud-apps
   
   or:
    
      yarn global add balena-cloud-apps

  [Classic Yarn](https://classic.yarnpkg.com/en/docs/usage)
  whenever the system complains about `balena_deploy` not found.

* **Templates files**

  Setup environment variables, build files, ready for deployment with any of the available targets:

      Scripts/update-templates.sh

#### Composer Plugins 

   Plugins are registered in both _git submodule_ and _composer.json_. To make them ready for build, edit _composer.json_ as needed and launch the command ```composer update```. 
   Plugins home folder: 
       
       app/Vendor/<package-name>
       app/Plugin/<plugin-name>/

* **.htaccess**

  To allow Apache server to browse directly to the app/webroot folder on server-side, use mod_rewrite rules, as provided by .htaccess files.

  >/.htaccess

      <IfModule mod_rewrite.c>
        RewriteEngine on
        # Uncomment if you have a .well-known directory in the root folder, e.g. for the Let's Encrypt challenge
        # https://tools.ietf.org/html/rfc5785
        #RewriteRule ^(\.well-known/.*)$ $1 [L]
        RewriteRule ^$ app/webroot/ [L]
        RewriteRule (.*) app/webroot/$1 [L]
      </IfModule>

  >/app/.htaccess

      <IfModule mod_rewrite.c>
         RewriteEngine on
         RewriteBase /app/
         RewriteRule    ^$    webroot/    [L]
         RewriteRule    (.*) webroot/$1    [L]
      </IfModule>

Local Built-in Server
---------------------
* CakePHP application also supports Docker
* MariaDB 10.1 and later

Start a local server machine for testing on port 9000.
Open a Terminal window:

    DB=Mysql ./configure.sh --mig-database -u
    ./start-cake.sh --docker -c server -p 9000

> Ctrl-click the URL that appear on the terminal. It will open them in the browser. To get more help about the command line interface :

    ./start-cake.sh --help

### PHPUnit Test
JUNIT tests are available with the following call to CAKE server:
Open a Terminal window:

    ./test-cake.sh

There are options (--runner, --travis) dedicated to continuous integration build environments. Use --help to see more about options.

See [below](#common-issues) to allow access on the built-in local server.

Device pod environment
----------------------
When deployment happens on device or is triggered by a git push event, 'source-to-image (s2i)', the httpd-server  or pod needs proper environment variables to be set ready. Otherwise the scripts will fail with an error state, unable to connect to the database

The following variables must be set up as server environment, provided by your **database administrator**:

    # Sqlite, Postgres
    DB:Mysql

> Note: DB selects CakePhp Model/Datasource/Database DBOSource class to configure SQL connections.    

    MYSQL_DATABASE:default
    # a hostname or IP address
    MYSQL_HOST:mysql

> Note: Prefixed with *TEST_* they are used by the index.php?test=1 URLs and ./test-cake.sh (--travis)

The following additional variables must be set up as server secrets environment, provided by your database administrator:

    #(optional)
    WEBHOOK_URL:<discordapp-url>
    # Persistent connection credentials
    DATABASE_USER:<provided-user>
    MYSQL_ROOT_PASSWORD:<provided-password>
    # Just add MYSQL_USER and MYSQL_PASSWORD
    MYSQL_USER:<test-user>
    MYSQL_PASSWORD:<test-password>
    # CakePHP generated
    CAKEPHP_SECRET_TOKEN:<secret-token>
    CAKEPHP_SECRET_SALT:<secret-salt>
    CAKEPHP_SECURITY_CIPHER_SEED:<cipher-seed>
    # Generated by ./configure.sh -h
    GET_HASH_PASSWORD:<hashed-password>

    MYSQL_DATABASE
    aria_db

    MYSQL_HOST
    db

    MYSQL_PASSWORD
    maria-abc

    MYSQL_ROOT_PASSWORD
    mariadb

    MYSQL_TCP_PORT
    3306

    MYSQL_USER
    maria

    SERVER_NAME
    <Domain-Name>


Database terminal
-----------------
Container engines provides provide a confined environment, with persistent storage. Check that last database deployment was successful, open a pod shell :

Inside **db** pod:

```mysql -uroot --password=${MYSQL_ROOT_PASSWORD}```

Issue some SQL statements, for instance :

```ùse aria_db; show tables;``` should list tables

Inside **acake2php** pod:

```cake schema update --connection=default``` should build the databases

```cake schema update --connection=test``` should build the test databases

#### More Database Configuration

An SQL server (must match remote server version) must be reachable by hostname or via its socket. If it's the 1st time you use this connection,

Configure it as a service and configure the login ACL with the user shell.
* __Optional__ database automatic configuration
*
*     ./configure.sh -d -u -i


* __Optional__ To Setup MYSQL_ROOT_PASSWORD at prompt:
*
*     mysql_secure_installation


* __Optional__ Edit
*
*     ./app/Config/database.php
*  if you wish to modify the DATABASE_CONFIG class.

* __Optional__ Edit
*
*     ./app/Model/Datasources/Database
*  if you wish to modify the DBOSource driver.

* Edit `./Scripts/fooargs.sh` to change default *test* environment settings (host, port, login, database name)

* Run the configuration script:
*
*     ./configure.sh -d -p <root-password> -i --sql-password=<new-password>


* More about configuration:
*
*     ./configure.sh --help && ./migrate-database.sh --help

* More [common issues](#common-issues)

* The following command resets SQL users `${DATABASE_USER}` and `${MYSQL_USER}` password
*
*     ./migrate-database.sh -p -i -p --test-sql-password


#### Generate new administrator password
To sign in with staff rights, at http://localhost/admin/index.php, somebody needs a unique password stored in `GET_HASH_PASSWORD`. One way to generate this hashed password with "salted“ encryption and setup:

    ./configure.sh -h -p <password> -w <salt>

To regenerate or read the current password hash again, simply browse to http://localhost/php-cms/e13/etc/getHashPassword.php

    GET_HASH_PASSWORD=<HaSheD/PasSwoRd!>

must be stored in the local server environment as a system readable variable.

Cross Platform
--------------

   If selecting an ARM device target from an ordinary X86 machine, first enable the RUN [ cross-build-start ] and RUN [ cross-build-end ] balenaOS cross-platform build modes, run `./deploy.sh`:
   
    1:local-balena
  
  Choose the target architecture, and then choose the option:
    
    6:build dependencies

  Only balenaOS baselib images can use cross-build based on balenaEngine. You should otherwise run `docker buildx build --platform=linux/arm64` from an ARM computer.
  BalenaOS and BalenaCloud as an open source platform allow us to maintain a small devices fleet (aka swarm, cluster).
  Use Balena one button deployment, update the source code as your needs, and deploy to BalenaCloud, this will disable cross-platform build:

    2:balena
    5:push

You are able to deploy to a balena fleet, using their original deployment process.

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
