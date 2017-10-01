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
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 *
*/
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home-redirect'));

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
* ...and connect the rest of 'Pages' controller's URLs.
*/
	Router::connect('/e14/', array('controller' => 'e14', 'action' => 'index'));

/**
* ...UNSUPPORTED as of current cake php version (2.x)
*
        Router::connect('/e14/index/:page/:n/:count/at/:YYYY/:MM/:DD/*', array('controller' => 'e14', 'action' => 'index'),
        // order matters since this will simply map ":np" to
                                // $np in your action
                array(
                        'pass' => array('page','n','count','YYYY','MM','DD'),
                        'page'=>'.*','n' => '[0-9]+','count'=> '[0-9]+','YYYY'=> '[0-9]{4}', 'MM' => '[0-9]{2}','DD'=>'[0-9]{2}'));
/**
        * ...and connect the rest of 'Pages' controller's URLs.
        *
        Router::connect('/e14/index/:page/:n/:count/*', array('controller' => 'e14', 'action' => 'index'), array(
                                'pass' => array('page','n','count'),
                                'page'=>'.*','n' => '[0-9]+','count'=> '[0-9]+'));                       
/**
* ...and connect the rest of 'Pages' controller's URLs.
*/
        Router::connect('/e14/:action/*', array('controller' => 'e14'));

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

