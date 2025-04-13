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
    + [Common Issues](#common-issues)
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

Quick Configuration
-------------------
Once you havev the server up and running (usually in a docker container), the website may not be reacheable until the database is configured.
if you have access to the host running the webservice as a container, just connect to it, with BalenaOS it's very easy:

  ./balena-connect-it 22222 <user@host-ip> acake2php

Otherwise use:

  ssh -ttp <port> <user@host-ip> docker exec -it <container-name> "/bin/sh"

Then once loggedin, run as normal user:

  ./configure.sh -d -u -i

It will configure PHP plugins and migrate the table in databases.

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

Common Issues
-------------
1. How to fix the following error?

  Index page displays:

    errno : 1146
    sqlstate : 42S02
    error : Table 'phpcms.info' doesn't exist

  Try the following to migrate (update) all database tables, answer 'y' when prompted:

    ./migrate-database.sh -u

2. ACCESS DENIED appears with other information complaining about database connection, what does that mean ?

  You probably have modified user privileges on your server:

    mysql -u root
    use mysql;
    grant all PRIVILEGES on $TEST_DATABASE_NAME.* to '$MYSQL_USER'@'$MYSQL_HOST';
    exit

    ./configure.sh -c

  This will reset the connection profile in ..etc/ properties file with the template.
  More about environment variables are located in the remote pod (OpenShift) settings and locally in ./Scripts/fooargs.sh.  

  > Note:

    ./configure.sh --mig-database -p -i --sql-password

  to do a reset with environment root and user password.

3. ACCESS DENIED for root@'127.0.0.1' or root@'localhost' appears with other information complaining about database connection, what does that mean ?

  (automatic) This looks like a first installation of mysql. You have to secure or reset your mysql root access:

    MYSQL_ROOT_PASSWORD=<password> sudo bash deployment/images/mysqldb/mysql_secure_shell

  (manual) The Linux shell way to reinitialize sql root password:

    sudo rm -rf /usr/local/var/mysql
    mysqld --initialize | grep "temporary password" | cut -f4  -d ":" | cut -c 2-  > app/tmp/nupwd

  > Note: A temporary password is generated for root@localhost. Now import identities.

    ./configure.sh --mig-database -p $(cat app/tmp/nupwd) -i --sql-password

  > You have now configured a new SQL root password and a test password. Local SQL access and server is ready to run tests:

    ./test-cake.sh -p -t <test-password>

  Go on to development phase with the [Local Built-in server](#local-built-in-server).

4. I've made changes to mysql database tables, I've made changes to Config/Schema/schema.php, as Config/database.php defines it, what should I do ?

  Migrate all your tables:

    ./migrate-database.sh -u

  Answer 'y' when prompted.

5. How to fix up 'Database connection "Mysql" or could not be created ?
  PHP mysql extensions must be installed.

    php -i | grep Extensions

  Log in with root privileges should work:

    mysql -u root --password=${MYSQL_ROOT_PASSWORD}

  If not, do a reset of your passwords:

    mysqladmin -uroot password

  If it isn't possible to login:
    + Check your environment variables in
      
      common.env
      docker-compose.template
      
Don't forget to update YML with new values

      Scripts/update-templates.sh

Use one or the other, and see which works for you:

    MYSQL_HOST=$(hostname)

(Unix/OSX platforms) or if using docker-compose services

    MYSQL_HOST=db
    MYSQL_TCP_PORT=3306

  + Debug the local configuration, look for unbound VARIABLES, add verbosity level information (add `-o` if you are in a remote shell):

    set -u
    ./configure.sh --verbose -d -u

  + Try resetting privileges

    ./configure.sh --mig-database -p ${MYSQL_ROOT_PASSWORD} -t ${MYSQL_PASSWORD} -i

  Don't miss the parameter to startup a local container database :

    ./migrate-database.sh -u --docker -i or ./configure.sh --mig-database -u --docker -i

  + Note that localhost is a special value. Using 127.0.0.1 is not the same thing. The latter will connect to the mysqld server through tcpip.

  + Try the [secure_installation](#database-configuration).

6. How to fix up ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/run/mysqld/mysql.sock' (2) ?

  Run the socket fixup script with arguments:

    ./migrate-database.sh /tmp/mysqld.sock
    brew services restart mysql@5.7

7. I'm testing with ./start_cake.sh and I cannot add any new post on Updates section, what should I do ?

  With the CLI, you may ctrl-X ctrl-C to exit server and migrate your database:

    ./migrate-database.sh -u
    ./start_cake.sh

  Answer 'y' when prompted.

8. I cannot upload any picture, why ?

  The Mysql.php Datasource must define binary and mediumbinary storage types. Please look at the file  __app/Model/Datasource/Mysql_cms.php__ if it exists and if you experienced the following error:

    errno : 1054
    sqlstate : 42S22
    error : Unknown column 'image' in 'field list'

  Add the *__mediumbinary__* storage, extending the original Datasource class:

```
<?php
App::uses('Mysql', 'Model/Datasource/Database');

class Mysql_cms extends Mysql
{
	public function __construct()
	{
		parent::__construct();
		$this->columns['mediumbinary'] = array('name' => 'mediumblob');
	}

	/**
	 * Converts database-layer column types to basic types
	 *
	 * @param string $real Real database-layer column type (i.e. "varchar(255)")
	 * @return string Abstract column type (i.e. "string")
	 */
		public function column($real) {
			$s = parent::column($real);
			if($s === "text") {
				$col = str_replace(')', '', $real);
				$limit = $this->length($real);
				if (strpos($col, '(') !== false) {
					list($col, $vals) = explode('(', $col);
				}
				if (strpos($col, 'mediumblob') !== false || $col === 'mediumbinary') {
					return 'mediumbinary';
				}
			}
			return $s;
		}
}
?>
```

Ensure it is set as $identities[DB]['datasource'] in 
    
    app/Config/database.php
    ./Scripts/fooargs.sh

and update the database schema:

    ./migrate-database.sh -u

9. It looks like submodule folders have disappeared, why ?

  A recent change made the submodule disappear from disk, that can happen on master/development branch.  Recall or add the shell configure script to your workflow:

    ./configure.sh -m

10. Error: Please install PHPUnit framework v3.7 (http://www.phpunit.de)

  You need to configure development environment from Composer dependencies.

    ./configure.sh --development

11. Undefined functins balena_deploy or init_functions: No such file or directory

  You need to export the `node_modules/.bin` for this shell to find npmjs installed binaries.


    export PATH="`pwd`/node_modules/.bin:\$PATH"


12. Any message "saved[@]: unbound variable" on Darwin (OSX)

  Your BASH doesn't handle array in scripts. Please upgrade to Bash v.5 or later.

13. I've made changes to deployment/images/ dockerfile, how can I rebuild it?
  
  To be able to publish on to DockerHub [betothreeprod](https://hub.docker.com/u/betothreeprod) repository, first login as *DOCKER_USER* from a web browser. Then use the following to deploy images to Docker Hub:
    
    DOCKER_USER=yourDockerUserName DOCKER_PASS=yourDockerPassword ./deploy.sh 

   If selecting ARM 32 or 64 from a PC/Mac machine, first enable the RUN [ cross-build-start ] and RUN [ cross-build-end ] balenaOS cross-platform build modes, run `./deploy.sh`:
   
    1:local-balena
  
  Then, choose the Docker architecture, and then choose option:
    
    6:build dependencies.

  Only balenaOS baselib images can use cross-build based on balenaEngine. If not, you should run docker_build from the target architecture, e.g. a Raspberry PI for aarch64.

14. The error push access denied, repository does not exist or may require authorization: server message: insufficient_scope: authorization failed appears upon successful build

  You must configure a DOCKER_USER and DOCKER_PASS as environment variables. Use an [access_token](https://docs.docker.com/security/for-developers/access-tokens/#use-an-access-token) for DOCKER_PASS.

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
