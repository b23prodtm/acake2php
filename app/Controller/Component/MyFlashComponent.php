<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

/**
 * CakePHP MyFlashComponent
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */

App::uses('FlashComponent', 'Controller/Component');

class MyFlashComponent extends FlashComponent {

        public $components = array();

        public function initialize($controller) {
                parent::initialize($controller);
        }

        public function startup($controller) {
                parent::startup($controller);
        }

        public function beforeRender($controller) {
                parent::beforeRender($controller);
        }

        public function shutDown($controller) {
                parent::shutdown($controller);
                
        }

        public function beforeRedirect($controller, $url, $status = null, $exit = true) {
                parent::beforeRedirect($controller, $url, $status, $exit);
        }
        
        public function success($message) {                
                $this->set($message, array('params' => array('class' => 'success')));
        }
        
        public function error($message) {
                $this->set($message, array('params' => array('class' => 'error')));
        }
}
