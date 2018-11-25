

<!-- toc -->

- [CakePHP Sample App on OpenShift](#cakephp-sample-app-on-openshift)
    + [Source repository layout](#source-repository-layout)
    + [Compatibility](#compatibility)
    + [Local Built-in Server](#local-built-in-server)
    + [PHP Unit Test](#php-unit-test)
    + [Common Issues](#common-issues)
    + [License](#license)

<!-- tocstop -->

CakePHP for [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) on OpenShift [![Build Status](https://travis-ci.org/b23prodtm/myphpcms.svg?branch=development)](https://travis-ci.org/b23prodtm/myphpcms)
===============================

This is a quickstart CakePHP application for OpenShift v3 that you ''can'' use as a starting point to develop your own application and deploy it on an [OpenShift](https://github.com/openshift/origin) cluster.

If you'd like to install it, follow [these directions](https://github.com/openshift/cakephp-ex/blob/master/README.md#installation).  

It includes a link to [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) and its [GIT cake-php release](https://bitbucket.org/b23prodtm/php-cms/branch/cake-php). The latter PHP CMS is featuring well-known functionalities as cool as posting some web contents with pictures stored in a database. More features do come thank to the powerful [Cake PHP framework](http://www.cakephp.org).

### Source repository layout

You do not need to change anything in your existing PHP project's repository.
However, if these files exist they will affect the behavior of the build process:

* **submodules**

  The myphpcms folder includes modules that need to be pulled in order to install locally.
  After the first checkout browse to myphpcms folder and do
  ```git submodule update --init --recursive```
  You'll see modules populating the subfolder app/webroot/... If something goes wrong, erase the myphpcms folder and start over.

* **composer.json**

  List of dependencies to be installed with `composer`. The format is documented
  [here](https://getcomposer.org/doc/04-schema.md).
  Plugins are registered in both _git submodule_ and _composer.json_. To allow a plugin to accept ```composer update```, edit _composer.json_ according to the available released tags. In the plugin's home repository (app/Plugin/<plugin-name>/), call```git tag``` or  ``git log``` for more information.
  >_DEVELOPER TIP:_ To push tags : ```git tag`<version> && git push --tags```.   

### Compatibility

This repository is compatible with PHP 5.6 and higher, excluding any alpha or beta versions.

### Local built-in server
>for local test only

Open a Terminal window:

    ./start_cake.sh

>Ctrl-click the URLs to open them in the browser.
### PHP Unit Test

Open a Terminal window:

    ./test_cake.sh -p <sql-password>

See [below](#common-issues) to allow access on the built-in local server.

### Common Issues

1. How to fix the following error?

Index page displays:

    errno : 1146
    sqlstate : 42S02
    error : Table 'phpcms.info' doesn't exist

Try the following to migrate (update) all database tables, answer 'y' when prompted:

    ./migrate-database.sh -u

2. ACCESS DENIED for user root appears with other information complaining about database connection, what does that mean ?

You probably have modified user privileges on your server:

    mysql -u root
    use mysql;
    grant all on $TEST_DATABASE_NAME.* to '$TEST_DATABASE_USER'@'$TEST_MYSQL_SERVICE_HOST';
    exit
    ./configure.sh -c

This will reset the connection profile in ..etc/ properties file.

3. ACCESS DENIED for root@'127.0.0.1' appears with other information complaining about database connection, what does that mean ?

This looks like a first installation of mysql. You have to secure or reset your mysql root access:

    sudo rm -rf /usr/local/var/mysql
    mysqld --initialize

[Note] A temporary password is generated for root@localhost. Now import identities.

    brew services restart mysql@5.7
    ./migrate-database.sh -Y -i
    <temporary password>

4. My mysql server's upgraded to another version, what should I do ?

Upgrade your phpcms database within a (secure)shell:

    mysql_upgrade -u root --password=<password>

4. I've made changes to mysql database tables, I've made changes to Config/Schema/myschema.php, as Config/database.php defines it, what should I do ?

Migrate all your tables:

    ./migrate-database.sh -u

Answer 'y' when prompted.

5. How to fix up 'Database connection "Mysql" is missing, or could not be created' ?

Check your environment variable (./Scripts/bootargs.sh or Docker/Pod settings)

    TEST_DATABASE_NAME=cakephp_test

Log in with authorized privileges from a shell prompt:

    mysql -u root --password=<password> cakephp_test

6. How to fix up ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/mysql/mysql.sock' (2) ?

Run the socket fixup script with arguments:

    ./migrate-database.sh -y
    brew services restart mysql@5.7

### License
   Copyright 2016 b23production GNU

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
