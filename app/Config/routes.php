<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 */

/**
 *
*/
	Router::connect('/', array('controller' => 'e14', 'action' => 'index'));

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
*/
	Router::connect('/e14/', array('controller' => 'e14', 'action' => 'index'));
                  
/**
*/
        Router::connect('/e14/:action/*', array('controller' => 'e14'));
        
/* admin pages are prefixed and .php files may be accessed through admin_index($p)*/
        Router::connect('/admin/e14/index/:p', array('controller' => 'e14', 'action' => 'index', 'admin' => true), array('pass' => array('p'),'p' => '.*\.php'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 **/
	require CAKE . 'Config' . DS . 'routes.php';

