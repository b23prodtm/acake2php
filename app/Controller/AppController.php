<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 */
App::uses('Controller', 'Controller');
App::import('file', 'Index', false, array(WWW_ROOT . 'php-cms' . DS . 'e13' . DS . 'include' . DS), 'php_index.inc.php');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

        /**
         * Add in the DebugKit toolbar
         */
        public $components = array('DebugKit.Toolbar');
        public $helpers = array('Markdown.Markdown');
        var $r;

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);

                /* initialise les $GLOBALS et le sitemap */
                $this->r = new Index($this->View, ROOT . DS . 'index.php', false, WWW_ROOT . 'php-cms/');
                $this->set("i_sitemap", $this->r->sitemap);
        }

}
