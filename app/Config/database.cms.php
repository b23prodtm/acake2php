<?php
/**
* CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
* Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
*
* Licensed under The MIT License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
* @link          http://cakephp.org CakePHP(tm) Project
* @package       app.Config
* @since         CakePHP(tm) v 0.2.9
* @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/

/**
* Database configuration class.
*
* You can specify multiple configurations for production, development and testing.
*
* datasource => The name of a supported datasource; valid options are as follows:
*  Database/Mysql - MySQL 4 & 5,
*  Database/Sqlite - SQLite (PHP5 only),
*  Database/Postgres - PostgreSQL 7 and higher,
*  Database/Sqlserver - Microsoft SQL Server 2005 and higher
*
* You can add custom database datasources (or override existing datasources) by adding the
* appropriate file to app/Model/Datasource/Database. Datasources should be named 'MyDatasource.php',
*
*
* persistent => true / false
* Determines whether or not the database should use a persistent connection
*
* host =>
* the host you connect to the database. To add a socket or port number, use 'port' => #
*
* prefix =>
* Uses the given prefix for all the tables in this database. This setting can be overridden
* on a per-table basis with the Model::$tablePrefix property.
*
* schema =>
* For Postgres/Sqlserver specifies which schema you would like to use the tables in.
* Postgres defaults to 'public'. For Sqlserver, it defaults to empty and use
* the connected user's default schema (typically 'dbo').
*
* encoding =>
* For MySQL, Postgres specifies the character encoding to use when connecting to the
* database. Uses database default not specified.
*
* sslmode =>
* For Postgres specifies whether to 'disable', 'allow', 'prefer', or 'require' SSL for the
* connection. The default value is 'allow'.
*
* unix_socket =>
* For MySQL to connect via socket specify the `unix_socket` parameter instead of `host` and `port`
*
* settings =>
* Array of key/value pairs, on connection it executes SET statements for each pair
* For MySQL : http://dev.mysql.com/doc/refman/5.7/en/set-statement.html
* For Postgres : http://www.postgresql.org/docs/9.2/static/sql-set.html
* For Sql Server : http://msdn.microsoft.com/en-us/library/ms190356.aspx
*
* flags =>
* A key/value array of driver specific connection options.
*
* Edit ./Scripts/fooargs.sh and common.env !
* For Test find settings in test-cake.sh
* --------------------------------------
* The resulting file is a generated copy made by ./Scripts/config_app_database.sh of database.cms.php. Edit app/Config/database.cms.php to make changes.
* --------------------------------------
*/
App::uses('MysqlCms', 'Datasources.Model/Datasource/Database');
define('unix_socket', '/var/run/mysqld/mysqld.sock');
/**
* Defines a constant expression from environment variables
* @param define default value if environment isn't found
*/
function defEnv($varEnv, $default=FALSE, $override=FALSE) {
  if ($override || !getenv($varEnv))
  define($varEnv, $default);
  else
  define($varEnv, getenv($varEnv));
}
defEnv('DB', 'Mysql');
defEnv(strtoupper(DB).'_HOST');
defEnv(strtoupper(DB).'_TCP_PORT');
defEnv('DATABASE_USER');
defEnv('MYSQL_ROOT_PASSWORD');
defEnv('MYSQL_DATABASE');
defEnv('MYSQL_USER');
defEnv('MYSQL_PASSWORD');
defEnv('TEST_DATABASE_NAME');
class DATABASE_CONFIG {
  private $identities = array(
    'Mysql' => array(
      /* this is an extended Mysql database (providing blob-binary storage)*/
      'datasource' => 'Datasources.Database/MysqlCms',
      'persistent' => true,
      'prefix' => '',
      'encoding' => 'utf8',
      'unix_socket' => '',
      'host' => MYSQL_HOST,
      'port' => MYSQL_TCP_PORT,
      'flags' => array (
        PDO::ATTR_TIMEOUT => 120,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      )
    ),
    'unix_socket' => array(
      'datasource' => 'Datasources.Database/MysqlCms',
      'persistent' => true,
      'prefix' => '',
      'encoding' => 'utf8',
      'unix_socket' => unix_socket,
      'host' => '',
      'port' => '',
    ),
  );
  public $default = array(
    'login' => DATABASE_USER,
    'password' => MYSQL_ROOT_PASSWORD,
    'database' => MYSQL_DATABASE,
  );
  public $test = array(
    'login' => MYSQL_USER,
    'password' => MYSQL_PASSWORD,
    'database' => TEST_DATABASE_NAME,
  );
  public function __construct() {
    $db = ucfirst(DB);
    foreach (array('default', 'test') as $source) {
      $config = array_merge($this->{$source}, $this->identities[$db]);
      $localhost = array('localhost');
      if (in_array($config['host'], $localhost)) {
        $config = array_merge($config, $this->identities['unix_socket']);
      }
      $this->{$source} = $config;
    }
  }
}
