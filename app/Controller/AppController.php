<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('Index', 'Cms');
App::uses('AuthComponent', 'Controller/Component/');
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
        public $components = array('DebugKit.Toolbar',
            'Flash' => array(
                'className' => 'MyFlash'
              ),
            'Auth' => array(
                  'loginRedirect' => array('controller' => 'clients', 'action' => 'index'),
                  'logoutRedirect' => array('controller' => 'e14', 'action' => 'index'),
                  'authError' => "Veuillez vous authentifier, s'il-vous-plaît.",
                  'authenticate' => array(
                      AuthComponent::ALL => array(
                        'userModel' => 'Client',
                        'fields' => array(
                            'username' => 'id', // 'username' par défaut
                            'password' => 'id_motdepasse'  // 'password' par défaut
                        )
                      ),
                      'Basic',
                      'Form'
                  ),
                  'authorize' => array('Controller')
            )
        );

        /** Gestion simple des acces controlés par role. Un 'controller' dépendant de cette méthode pour
          * définir l'autorisation Client pour une action donnée
          */
        public function isAuthorized($user) {
            /* Admin peut accéder à toute action */
            if (isset($user['role']) && $user['role'] === 'admin') {
                return true;
            }

            /* Refus par défaut */
            return false;
        }
        public $helpers = array('Info' => array(
                'index' => null,
                'countPerPage' => '10',
                'md' => true
              ), 'Markdown.Markdown' => true, 'Text', 'Form', 'Html', 'Js', 'Time', 'Flash');
        protected $_r;

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                /* initialise les $GLOBALS et le sitemap */
                $this->_r = new Index($this->View, APP . 'index.php', true, WWW_ROOT . 'php-cms');
                $this->helpers['Info']['index'] = $this->_r;
                $this->set("r", $this->_r);
        }

        public function beforeFilter() {
                parent::beforeFilter();
                /* internationalisation (i18n) */
                Configure::write('Config.language', $this->_r->getLanguage());
                /* AuthComponent de ne pas exiger un login pour toutes les actions index et view*/
                $this->Auth->allow(
                  'index',
                  'view');
        }

        /**
         * @param String $page SITEMAP.PROPERTIES key in [images]
         */
        public function images($p = NULL) {
                //debug($this->request->params);
                $this->response->file($this->_r->r["images"] . $p);
                $this->response->send();
        }

}
