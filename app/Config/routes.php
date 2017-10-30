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
 * ...and connect the rest of 'Pages' controller's URLs.
 */
Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 **************************************************        E14Controller routing
 * the one star(*) wildcard is for one-to-one passed arguments separated by the slash '/'
 */
Router::connect('/e14/:action/*', array('controller' => 'e14'));
/**
 * the two stars(**) wildcard is for many-to-one passed argument as a whole string
 */
Router::connect('/e14/**', array('controller' => 'e14', 'action' => 'index'));
/**
 */
Router::connect('/admin/e14/:action/*', array('controller' => 'e14', 'admin' => true));
/**
 ***************************************************   ArticleController routing
 * the one star(*) wildcard is for one-to-one passed arguments separated by the slash '/'
 */
Router::connect('/article/:action/*', array('controller' => 'article'));
/**
 * the two stars(**) wildcard is for many-to-one passed argument as a whole string
 */
Router::connect('/article/**', array('controller' => 'article', 'action' => 'index'));
/**
 */
Router::connect('/admin/article/:action/*', array('controller' => 'article', 'admin' => true));
/**
 ***************************************************             default routing 
 */
Router::connect('/admin/*', array('controller' => 'e14', 'action' => 'index', 'admin' => true));
/**
 *
 */
Router::connect('/:action/*', array('controller' => 'e14'));
/**
 *
 */
Router::connect('/', array('controller' => 'e14', 'action' => 'index'));



Router::parseExtensions('php');
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 * */
require CAKE . 'Config' . DS . 'routes.php';

