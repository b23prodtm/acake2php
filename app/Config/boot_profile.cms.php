<?php
/*var_dump(App::path('Cms'));*/
/* -- PHP AUTOLOAD
 Load Composer autoload.*/
include APP . 'Vendor' . DS . 'autoload.php';
/* Remove and re-prepend CakePHP's autoloader as Composer thinks it is the
 most important.
 See: http://goo.gl/kKVJO7 */
spl_autoload_unregister(array('App', 'load'));
spl_autoload_register(array('App', 'load'), true, true);
/* PHP AUTOLOAD (app/Vendor/autoload.php installed from composer)
It is recommended to use only one extension for all classes.
PHP (more exactly spl_autoload) does the rest for you
and is even quicker than a semantically equal self-defined autoload function like this one
*/
App::build(array(
  'Cms' => array(WWW_ROOT . 'php_cms' . DS . 'e13' . DS . 'include' . DS)
), App::REGISTER);
/**
 * Load DebugKit plugin
 */
CakePlugin::load('DebugKit');

/**
 * Load Markdown Plugin
 */

CakePlugin::load(array('Markdown' => array('bootstrap' => true)));
/**
 * Load UpdateShell Plugin
 */
CakePlugin::load('UpdateShell');
/**
 * Load DataSources Plugin
 */
CakePlugin::load('Datasources');
