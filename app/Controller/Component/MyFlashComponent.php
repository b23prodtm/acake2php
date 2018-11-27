<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

/**
 * CakePHP MyFlashComponent
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 * Flash is a message displayed with emphasized text
 */

App::uses('FlashComponent', 'Controller/Component');

class MyFlashComponent extends FlashComponent {

        public $components = array();

        public function initialize(Controller $controller) {
                parent::initialize($controller);
        }

        public function startup(Controller $controller) {
                parent::startup($controller);
        }

        public function beforeRender(Controller $controller) {
                parent::beforeRender($controller);
        }

        public function shutDown(Controller $controller) {
                parent::shutdown($controller);
                
        }

        public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
                parent::beforeRedirect($controller, $url, $status, $exit);
        }
        
        public function success($message) {                
                $this->set($message, array('params' => array('class' => 'success')));
        }
        
        public function error($message) {
                $this->set($message, array('params' => array('class' => 'error')));
        }
}
