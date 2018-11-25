<?php

/**
 * Web Access Frontend for TestSuite
 *
 * PHP 5
 *
 * CakePHP(tm) Tests <http://book.cakephp.org/view/1196/Testing>
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/view/1196/Testing
 * @package       app.webroot
 * @since         CakePHP(tm) v 1.2.0.4433
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 * Source :
 * @link          https://gist.github.com/2055307
 */
set_time_limit(0);
ini_set('display_errors', 1);
/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * These defines should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * When using custom settings be sure to use the DS and do not add a trailing DS.
 */
/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 *
 */
if (!defined('ROOT')) {
	define('ROOT', dirname(__FILE__));
}
/**
 * The actual directory name for the "app".
 *
 */
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(ROOT . DS . 'app'));
}

/**
 * The absolute path to the "Cake" directory, WITHOUT a trailing DS.
 *
 * For ease of development CakePHP uses PHP's include_path.  If you
 * need to cannot modify your include_path, you can set this path.
 *
 * Leaving this constant undefined will result in it being defined in Cake/bootstrap.php
 */
define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');

/**
 * Editing below this line should not be necessary.
 * Change at your own risk.
 *
 */
if (!defined('WEBROOT_DIR')) {
	define('WEBROOT_DIR', APP_DIR . DS . 'webroot');
}
if (!defined('WWW_ROOT')) {
	define('WWW_ROOT', WEBROOT_DIR . DS);
}

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
	if (function_exists('ini_set')) {
		ini_set('include_path', ROOT . DS . 'lib' . PATH_SEPARATOR . ini_get('include_path'));
	}
	if (!include('Cake' . DS . 'bootstrap.php')) {
		$failed = true;
	}
} else {
	if (!include(CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'bootstrap.php')) {
		$failed = true;
	}
}
if (!empty($failed)) {
	trigger_error("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/index.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
}

if (Configure::read('debug') < 1) {
	die(__d('cake_dev', 'Debug setting does not allow access to this url.'));
}

/** PHP autoloader shall detect applcation plugins from this profile */
include_once APP . 'Config' . DS . 'boot_profile.cms.php';
ini_set('register_globals', 1);
global $$GLOBALS;
App::uses('Index', 'Cms');
$r = new Index(null, ROOT . DS . 'bootstrap_phpunit.php', false, WWW_ROOT . 'php_cms/');

require_once CAKE . 'TestSuite' . DS . 'CakeTestSuiteDispatcher.php';
require_once CAKE . 'TestSuite' . DS . 'CakeTestSuiteCommand.php';
