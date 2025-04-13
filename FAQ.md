FAQ

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
  
  To be able to publish into [DockerHub](https://hub.docker.com/u/betothreeprod) repository, first login as *DOCKER_USER* from a web browser. Then use the following to deploy images to Docker Hub:
    
    update_templates
    DOCKER_USER=yourDockerUserName DOCKER_PASS=yourDockerPassword ./deploy.sh 
