#!/usr/bin/env bash
source configure.sh "-c" "-d" "-y" "-h" "-p" "pass" "-s" "word"
echo -e "
Set of default environment
==========================
  Find exports for local development phase only in './Scripts/bootargs.sh')
  ";
echo -e "
Documented VARIABLES
  TRAVIS_OS_NAME: os: ['osx','linux'] in .travis.yml
  TRAVIS_PHP_VERSION : php: <version> in .travis.yml
  DB=['mysql', 'pgsql', 'sqlite']

optional environment VARIABLES
  ADDITIONAL_PHP_INI='path to a php.ini settings file'
==========================
";
echo $(bin/composer.phar install --dev --no-interaction)
if [ ! -z "${ADDITIONAL_PHP_INI}" ]; then /usr/bin/env bash .travis/TravisCI-OSX-PHP/build/custom_php_ini.sh; fi
mkdir -p build/logs
echo "Database Unit Tests... DB=${DB}"
if [[ ("${TRAVIS_OS_NAME}" == "linux") && (${TRAVIS_PHP_VERSION:0:3} == "7.2") ]] ; then pear config-set preferred_state snapshot && yes "" | pecl install mcrypt ; fi
sudo locale-gen de_DE
sudo locale-gen es_ES
if [[ ${DB} == 'mysql' ]]; then mysql -e 'CREATE DATABASE cakephp_test;'; fi
if [[ ${DB} == 'mysql' ]]; then mysql -e 'CREATE DATABASE cakephp_test2;'; fi
if [[ ${DB} == 'mysql' ]]; then mysql -e 'CREATE DATABASE cakephp_test3;'; fi
if [[ ${DB} == 'pgsql' ]]; then psql -c 'CREATE DATABASE cakephp_test;' -U postgres; fi
if [[ ${DB} == 'pgsql' ]]; then psql -c 'CREATE SCHEMA test2;' -U postgres -d cakephp_test; fi
if [[ ${DB} == 'pgsql' ]]; then psql -c 'CREATE SCHEMA test3;' -U postgres -d cakephp_test; fi
chmod -R 777 ./app/tmp
if [[ ("${TRAVIS_OS_NAME}" == "linux") && (${TRAVIS_PHP_VERSION:0:3} == "5.3") ]] ; then pecl install timezonedb ; fi
if [[ "${TRAVIS_OS_NAME}" == "linux" ]]; then
  echo "extension = memcached.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
  if [[ ${TRAVIS_PHP_VERSION:0:1} == "7" ]] ; then echo "yes" | pecl install apcu-5.1.3 || true; fi
  if [[ ${TRAVIS_PHP_VERSION:0:1} == "5" ]] ; then echo "yes" | pecl install apcu-4.0.11 || true; fi
  echo -e "extension = apcu.so\napc.enable_cli=1" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
  phpenv rehash
fi
set +H
echo "<?php
  class DATABASE_CONFIG {
  private \$identities = array(
    'mysql' => array(
      'datasource' => 'Database/Mysql',
      'host' => '127.0.0.1',
      'login' => 'root'
    ),
    'pgsql' => array(
      'datasource' => 'Database/Postgres',
      'host' => '127.0.0.1',
      'login' => 'postgres',
      'database' => 'cakephp_test',
      'schema' => array(
        'default' => 'public',
        'test' => 'public',
        'test2' => 'test2',
        'test_database_three' => 'test3'
      )
    ),
    'sqlite' => array(
      'datasource' => 'Database/Sqlite',
      'database' => array(
        'default' => ':memory:',
        'test' => ':memory:',
        'test2' => '/tmp/cakephp_test2.db',
        'test_database_three' => '/tmp/cakephp_test3.db'
      ),
    )
  );
  public \$default = array(
    'persistent' => false,
    'host' => '',
    'login' => '',
    'password' => '',
    'database' => 'cakephp_test',
    'prefix' => ''
  );
  public \$test = array(
    'persistent' => false,
    'host' => '',
    'login' => '',
    'password' => '',
    'database' => 'cakephp_test',
    'prefix' => ''
  );
  public \$test2 = array(
    'persistent' => false,
    'host' => '',
    'login' => '',
    'password' => '',
    'database' => 'cakephp_test2',
    'prefix' => ''
  );
  public \$test_database_three = array(
    'persistent' => false,
    'host' => '',
    'login' => '',
    'password' => '',
    'database' => 'cakephp_test3',
    'prefix' => ''
  );
  public function __construct() {
    \$db = 'mysql';
    if (!empty(\$_SERVER['DB'])) {
      \$db = \$_SERVER['DB'];
    }
    foreach (array('default', 'test', 'test2', 'test_database_three') as \$source) {
      \$config = array_merge(\$this->{\$source}, \$this->identities[\$db]);
      if (is_array(\$config['database'])) {
        \$config['database'] = \$config['database'][\$source];
      }
      if (!empty(\$config['schema']) && is_array(\$config['schema'])) {
        \$config['schema'] = \$config['schema'][\$source];
      }
      \$this->{\$source} = \$config;
    }
  }
  }" > app/Config/database.php
  echo "Unit Test was set up in app/Config/database.php"
