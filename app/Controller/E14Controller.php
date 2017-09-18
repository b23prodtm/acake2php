<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');
App::import('file', 'Index', false, array(WWW_ROOT . 'php-cms' . DS . 'e13' . DS . 'include' . DS), 'php_index.inc.php');

/**
 * CakePHP E13
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class E14Controller extends AppController {

        /**
         * This controller does not use a model
         *
         * @var array
         */
        public $uses = array();
        var $r;

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                /** initalize $GLOBALS */
                $this->r = new Index(ROOT . DS . 'index.php', false, WWW_ROOT . 'php-cms/');
                $this->set("i_sitemap", $this->r->sitemap);
        }

        public function index($p = NULL, $images = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if ($p === "images") {
                        return $this->images($images);
                } else {
                        $this->set("p", $p);
                        $this->render(null, "default-e14");
                }
        }

        public function etc($p = NULL, $locale = NULL) {
                //debug($this->request->params);                
                if ($p === "locale") {
                        $this->response->file($GLOBALS["etc"] . DS . $p . DS . $locale);
                        $this->response->send();
                } else if (stristr($p, ".php")) {
                        /** THE FOLOWING DOESNT WORK ??
                          $this->render(null, "default-e14");
                         */
                        $this->set("p", $p);
                        $this->render();
                } else {
                        $this->response->file($GLOBALS["etc"] . DS . $p);
                        $this->response->send();
                }
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [admin]
         */
        public function admin_index($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("p", $p);
                $this->render(null, "default-e14");
        }

        /**
         * @param String $page SITEMAP.PROPERTIES key in [images]
         */
        public function images($p = NULL) {
                //debug($this->request->params);
                $this->response->file($GLOBALS["images"] . DS . $p);
                $this->response->send();
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [blog]
         */
        public function blog($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("p", $p);
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [library]
         */
        public function dvd($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("p", $p);
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [shop]
         */
        public function shop($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("p", $p);
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [activites]
         */
        public function content($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if (stristr($p, "images")) {
                        $this->response->file($GLOBALS['activites'] . DS . $p);
                        $this->response->send();
                } elseif (stristr($p, ".html")) {
                        $this->set('pIndex', 'activites__index');
                        $this->set('pUrl', $GLOBALS['activites'] . DS . $p);
                } else {
                        $this->set('pIndex', 'activites__' . $p);
                        $this->set('pUrl', $GLOBALS['activites' . $p]);
                }
                $this->render('content', "default-e14");
        }

}
