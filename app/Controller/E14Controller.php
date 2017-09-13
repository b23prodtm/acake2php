<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');
App::import('file', 'Index', false, array(WWW_ROOT . 'e13' . DS . 'include' . DS), 'php_index.inc.php');

/**
 * CakePHP E13
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class E14Controller extends AppController {

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                /** initalize $GLOBALS */
                $this->set("r", new Index(WWW_ROOT, false, APP));
        }

        public function index($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("p", $p);
                $this->render();
        }

        public function etc($p = NULL) {
                //debug($this->request->params);                
                if (stristr($p, ".css")) {
                        $this->response->file($GLOBALS["etc"] . "/" . $p);
                        $this->response->send();
                } else if ($p) {
                        $this->response->file($GLOBALS["etc__" . $p]);
                        $this->response->send();
                } else {
                        return $this->redirect(array('Controller' => 'E14', 'action' => 'index'));
                }
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [admin]
         */
        public function admin($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("p", $p);
                $this->render();
        }

        /**
         * @param String $page SITEMAP.PROPERTIES key in [admin]
         */
        public function images($p = NULL) {
                //debug($this->request->params);
                if (stristr($p, ".gif") || stristr($p, ".png") || stristr($p, ".jpg")) {
                        $this->response->file($GLOBALS["images"] . "/" . $p);
                        $this->response->send();
                } else if ($p) {
                        $this->response->file($GLOBALS["images__" . $p]);
                        $this->response->send();
                } else {
                        return $this->redirect(array('Controller' => 'E14', 'action' => 'index'));
                }
        }

}
