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
 */
class DATABASE_CONFIG {

        public $default = array(
        # this is an extended Mysql database (providing blob-binary storage)
            'datasource' => 'Database/MysqlCms',
            'persistent' => false,
            //'host' => 'localhost',
            'host' => '127.0.0.1',
            'port' => '3306',
            'login' => 'root',
            'password' => '',
            'database' => 'phpcms',
            'prefix' => '',
            'encoding' => 'utf8',
        );
        public $test = array(
            'datasource' => 'Database/MysqlCms',
            'persistent' => false,
            //'host' => 'localhost',
            'host' => '127.0.0.1',
            'port' => '3306',
            'login' => 'test',
            'password' => '',
            'database' => 'phpcms',
            'prefix' => '',
            'encoding' => 'utf8',
        );

        public function __construct() {
                $datasource = getenv('DATABASE_ENGINE') ? 'Database/' . ucfirst(getenv('DATABASE_ENGINE')) . '_cms' : FALSE;
                /** a different test/local configuration (shall not be the same as production)*/
                $test['host'] = getenv('TEST_' . strtoupper(getenv("DATABASE_SERVICE_NAME")) . "_SERVICE_HOST");
                $test['port'] = getenv('TEST_' . strtoupper(getenv("DATABASE_SERVICE_NAME")) . "_SERVICE_PORT");
                $test['login'] = getenv('TEST_DATABASE_USER');
                $test['password'] = getenv('TEST_DATABASE_PASSWORD');
                $test['database'] = getenv('TEST_DATABASE_NAME');
                $test['datasource'] = $datasource;
                $this->test = $test;

                $default['host'] = getenv(strtoupper(getenv("DATABASE_SERVICE_NAME")) . "_SERVICE_HOST");
                $default['port'] = getenv(strtoupper(getenv("DATABASE_SERVICE_NAME")) . "_SERVICE_PORT");
                $default['login'] = getenv("DATABASE_USER");
                $default['password'] = getenv("DATABASE_PASSWORD");
                $default['database'] = getenv("DATABASE_NAME");
                $default['datasource'] = $datasource;
                $this->default = $default;

                /* copy default to test if necessary */
                $this->redirectIfNull($test, $this->default);
                /* copy member variables if null detected */
                $this->redirectIfNull($test, $this->test);
                $this->redirectIfNull($default, $this->default);
        }
        function redirectIfNull(&$default, $redirect) {
                foreach ($default as $key => $val) {
                        if ((!$val || $val === "") && ($redirect[$key] || $redirect[$key] !== "")) {
                                $default[$key] = $redirect[$key];
                        }
                }
        }
}
