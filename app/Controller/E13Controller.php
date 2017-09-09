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
class E13Controller extends AppController {

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                /** initalize $GLOBALS */
                $this->set("r", new Index(WWW_ROOT.filter_input(INPUT_SERVER, "PHP_SELF"), false, WWW_ROOT));
        }

        public function index($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if (stristr($p, ".php")) {
                        include($GLOBALS["e13"] . "/" . $p);
                } else if ($p) {
                        include($GLOBALS["e13__" . $p]);
                } else {
                        include($GLOBALS["e13__index"]);
                }
        }

        public function etc($p = NULL) {
                //debug($this->request->params);
                if (stristr($p, ".css")) {
                        $this->response->file($GLOBALS["etc"] . "/" . $p);
                } else if ($p) {
                        $this->response->file($GLOBALS["etc__" . $p]);
                } else {
                        $this->redirect(array('action' => 'index'));
                }
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [admin]
         */
        public function admin($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if (stristr($p, ".php")) {
                        include($GLOBALS["admin"] . "/" . $p);
                } else if ($p) {
                        include($GLOBALS["admin__" . $p]);
                } else {
                        include($GLOBALS["admin__index"]);
                }
        }

        /**
         * @param String $page SITEMAP.PROPERTIES key in [admin]
         */
        public function images($p = NULL) {
                //debug($this->request->params);
                if (stristr($p, ".gif") || stristr($p, ".png") || stristr($p, ".jpg")) {
                        $this->response->file($GLOBALS["images"] . "/" . $p);
                } else if ($p) {
                        $this->response->file($GLOBALS["images__" . $p]);
                } else {
                        $this->redirect(array('Controller' => 'E13', 'action' => 'index'));
                }
        }

}
