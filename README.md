<!-- toc -->

- [A CakePHP 2.x application ](#a-cakephp-2.x-application)
    + [Quickstart](#quickstart)
    - [Plugins](#plugins)
      + [CakePHP Plugins](#cakephp-plugins)
      + [NodeJs dependencies](#nodejs-dependencies)
    + [Compatibility](#compatibility)
    + [PHPUnit Test](#phpunit-test)
    + [Device pod environment](#device-pod-environment)
    - [Database terminal](#database-terminal)
      + [More Database Configuration](#more-database-configuration)
      + [Generate new administrator password](#generate-new-administrator-password)
    + [Common Issues](#common-issues)
    + [License](#license)

<!-- tocstop -->
> We are moving to Kubernetes to host our website... See more about that project in [Kubespray](http://www.github.com/b23prodtm/kubespray).

A CakePHP 2.x application 
=========================
[![TravisCI Status](https://travis-ci.com/b23prodtm/acake2php.svg?branch=development)](https://travis-ci.com/b23prodtm/acake2php)
[![CircleCI Status](https://circleci.com/gh/b23prodtm/acake2php.svg?style=svg)](https://app.circleci.com/pipelines/github/b23prodtm/acake2php)

> [Including PHP-CMS ex-Pohse](https://sourceforge.net/projects/pohse/)

Quickstart
----------
Using the basic container orchestrator or engine to deploy and test, is straitforward.
Currently the deployment script 
```. deploy.sh```
Based on [Balena engine](http://www.balena.io). See more about [NodeJs dependencies](#nodejs-dependencies)

[![balena deploy button](https://www.balena.io/deploy.svg)](https://dashboard.balena-cloud.com/deploy?repoUrl=https://github.com/b23prodtm/acake2php)

Plugins
-------
You do not need to change anything in your existing PHP project's repository.
However, if these files exist they will affect the behavior of the build process:

* Git **submodules**

  The acake2php folder includes modules that need to be pulled in order to install locally.
  After the first checkout browse to acake2php folder and do
  ```git submodule sync && git submodule update --init --recursive```
  You'll see modules populating the subfolder app/webroot/... If something goes wrong, erase the acake2php folder and start over.
  > After a sucessful ```git checkout```each time, run once ```git submodule update --init --recursive``` to ensure submodules are downloaded from git. Otherwise your build may fail.
  > _DEVELOPER TIP:_ To push tags : ```git tag`<version> && git push --tags```.   

* Packagist **composer.json**

  List of dependencies to be installed with `composer`[here](https://packagist.org).

#### CakePHP Plugins 

   Plugins are registered in both _git submodule_ and _composer.json_. To allow a plugin to accept ```composer update```, edit _composer.json_ according to the available released tags. 
   In the plugin's home repository (`app/Vendor/<package-name>` or `app/Plugin/<plugin-name>/`)

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

#### NodeJs dependencies

  This project depends on npmjs [balena-cloud](https://www.npmjs.com/package/balena-cloud). Please call
  `npm update`
  whenever the system complains about `balena_deploy` not found.

* **.env files**

  Set environment variables as the following arguments, for instance on MacOS X:

      ./deploy.sh amd64 --nobuild

  Use a .env file in shell to configure up with RaspberryPI3 hosts :

      ./deploy.sh arm32 --nobuild

  .env -> arm32v7.env

      ./deploy.sh arm32 --balena

Compatibility
-------------
* CakePHP 2.X application also supports Docker CE 18.03 and later
* MariaDB 10.1 and later

CAKE includes a server application that´s only made for local tests on port 9000.
Open a Terminal window:

    DB=Mysql ./configure.sh --mig-database -u
    ./start-cake.sh --docker -c server -p 9000

> Ctrl-click the URLs to open them in the browser. To get more help about the command line interface :

    ./start-cake.sh --help

### PHPUnit Test
JUNIT tests are available with the following call to CAKE server:
Open a Terminal window:

    ./test-cake.sh

There are options (--travis, --openshift, --circle) dedicated to continuous integration build environments. Use --help to see more about options.

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
* __Optional__ database automatic configuration:

```acake2php
./configure.sh -d -u -i
```

* __Optional__ To Setup MYSQL_ROOT_PASSWORD at prompt:

```db
mysql_secure_installation
```

* __Optional__ Edit `./app/Config/database.php` if you wish to modify the DATABASE_CONFIG class.

* __Optional__ Edit `./app/Model/Datasources/Database` if you wish to modify the DBOSource driver.

* Edit `./Scripts/fooargs.sh` to change default *test* environment settings (host, port, login, database name)

* Run the configuration script:

```acake2php
./configure.sh -d -p <root-password> -i --sql-password=<new-password>
```

* More about configuration:

```acake2php
./configure.sh --help && ./migrate-database.sh --help
```

* More [common issues](#common-issues)

* The following command resets SQL users `${DATABASE_USER}` and `${MYSQL_USER}` password :

    ./migrate-database.sh -p -i -p --test-sql-password

#### Generate new administrator password
To sign in with staff rights, at http://localhost/admin/index.php, somebody needs a unique password stored in `GET_HASH_PASSWORD`. One way to generate this hashed password with "salted“ encryption and setup:

		./configure.sh -h -p <password> -w <salt>

To regenerate or read the current password hash again, simply browse to http://localhost/php-cms/e13/etc/getHashPassword.php

`GET_HASH_PASSWORD=<HaSheD/PasSwoRd!>` must be stored in the local server environment as a system readable variable.

Common Issues
-------------
1. How to fix the following error?

  Index page displays:
```
    errno : 1146
    sqlstate : 42S02
    error : Table 'phpcms.info' doesn't exist
```
  Try the following to migrate (update) all database tables, answer 'y' when prompted:
```acake2php
    ./migrate-database.sh -u
```
2. ACCESS DENIED appears with other information complaining about database connection, what does that mean ?

  You probably have modified user privileges on your server:
```db
    mysql -u root
    use mysql;
    grant all PRIVILEGES on $TEST_DATABASE_NAME.* to '$MYSQL_USER'@'$MYSQL_HOST';
    exit
```acake2php
    ./configure.sh -c
```
  This will reset the connection profile in ..etc/ properties file with the template.
  More about environment variables are located in the remote pod (OpenShift) settings and locally in ./Scripts/fooargs.sh.  

  > Note:
```acake2php
    ./configure.sh --mig-database -p -i --sql-password
```
  to do a reset with environment root and user password.

3. ACCESS DENIED for root@'127.0.0.1' or root@'localhost' appears with other information complaining about database connection, what does that mean ?

  (automatic) This looks like a first installation of mysql. You have to secure or reset your mysql root access:
```acake2php
    MYSQL_ROOT_PASSWORD=<password> sudo bash deployment/images/mysqldb/mysql_secure_shell
```
  (manual) The Linux shell way to reinitialize sql root password:
```db
    sudo rm -rf /usr/local/var/mysql
    mysqld --initialize | grep "temporary password" | cut -f4  -d ":" | cut -c 2-  > app/tmp/nupwd
```
  > Note: A temporary password is generated for root@localhost. Now import identities.
```acake2php
    brew services restart mysql@5.7
    ./configure.sh --mig-database -p $(cat app/tmp/nupwd) -i --sql-password
```
  > You have now configured a new SQL root password and a test password. Local SQL access and server is ready to run tests:
```acake2php
    ./test-cake.sh -p -t <test-password>
```
  Go on to development phase with the [Local Built-in server](#local-built-in-server).

4. I've made changes to mysql database tables, I've made changes to Config/Schema/schema.php, as Config/database.php defines it, what should I do ?

  Migrate all your tables:
```acake2php
    ./migrate-database.sh -u
```
  Answer 'y' when prompted.

5. How to fix up 'Database connection "Mysql" or could not be created ?
  PHP mysql extensions must be installed.
```acake2php
    php -i | grep Extensions
```
  Log in with root privileges should work:
```db
    mysql -u root --password=${MYSQL_ROOT_PASSWORD}
```
  If not, do a reset of your passwords:
```db
    mysqladmin -uroot password
```
  If it isn't possible to login:
    + Check your environment variables (common.env and docker-compose.yml) settings). Use one or the other, and see which works for you:
```
    MYSQL_HOST=$(hostname)
```(Unix/OSX platforms)
            or if docker-compose services are the following name:
```
    MYSQL_HOST=db
    MYSQL_TCP_PORT=3306
```
  + Debug the local configuration, look for unbound VARIABLES, add verbosity level information (add `-o` if you are in a remote shell):
```acake2php
    set -u
    ./configure.sh --verbose -d -u
```
  + Try resetting privileges
```acake2php
    ./configure.sh --mig-database -p ${MYSQL_ROOT_PASSWORD} -t ${MYSQL_PASSWORD} -i
```
  Don't miss the parameter to startup a local container database :
```acake2php
    ./migrate-database.sh -u --docker -i or ./configure.sh --mig-database -u --docker -i
```
  + Note that localhost is a special value. Using 127.0.0.1 is not the same thing. The latter will connect to the mysqld server through tcpip.
  + Try the [secure_installation](#database-configuration).

6. How to fix up ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/run/mysqld/mysql.sock' (2) ?

  Run the socket fixup script with arguments:
```acake2php
    ./migrate-database.sh /tmp/mysqld.sock
    brew services restart mysql@5.7
```
7. I'm testing with ./start_cake.sh and I cannot add any new post on Updates section, what should I do ?

  With the CLI, you may ctrl-X ctrl-C to exit server and migrate your database:
```acake2php
    ./migrate-database.sh -u
    ./start_cake.sh
```
  Answer 'y' when prompted.

8. I cannot upload any picture, why ?

  The Mysql.php Datasource must define binary and mediumbinary storage types. Please look at the file  __app/Model/Datasource/Mysql_cms.php__ if it exists and if you experienced the following error:
```
    errno : 1054
    sqlstate : 42S22
    error : Unknown column 'image' in 'field list'
```
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
  Ensure it is set as $identities[DB]['datasource'] in `app/Config/database.php`,`./Scripts/fooargs.sh`, `.travis.yml` and update the database schema:
```acake2php
    ./migrate-database.sh -u
```
9. It looks like submodule folders have disappeared, why ?

  A recent `git checkout ` made the submodule disappear from disk, that can happen on master/development branch.  Recall or add the shell configure script to your workflow:
```acake2php
    ./configure.sh -m
```
10. Error: Please install PHPUnit framework v3.7 (http://www.phpunit.de)

  You need to configure development environment from Composer dependencies.
```acake2php
    ./configure.sh --development
```
11. Undefined functins balena_deploy or init_functions: No such file or directory

  You need to export the `node_modules/.bin` for this shell to find npmjs installed binaries.

```
    export PATH="`pwd`/node_modules/.bin:\$PATH"
```

12. Any message "saved[@]: unbound variable" on Darwin (OSX)

  Your BASH doesn't handle array in scripts and uses version 3. Please upgrade to v.4 or later.
  Check your bash version and upgrade OpenSSL Cacert as well:
```
    .travis/TravisCI-OSX-PHP/build/prepare_osx_env.sh
```
License
-------
	Copyright 2016 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
