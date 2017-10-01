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

        /** @param string $p page filename.php
                @param int $np paginate number
                @param int $count count per page
                @param int $YYYY 4-digit year
                @param int $MM 2-digit month
                @param int $DD 2-digit day
                */
        public function index($p = NULL, $np = 1, $count = 10, $YYYY = NULL, $MM = NULL, $DD = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if ($p === "images") {
                        return $this->images($images);
                } else {
                        $this->set("p", $p);
                        $d = "";
                        /** date selection*/
                        if (isset($YYYY)) {
                                $d = $YYYY;
                                if (isset($MM)) {
                                        $d = $d . "-" . $MM;
                                        if (isset($DD)) {
                                                $d = $d . "-" . $DD;
                                        } else {
                                                $d = $d . "-*";
                                        }
                                } else {
                                        $d = $d . "-*";
                                }
                                $this->set("d", $d);
                        }
                        if (isset($count))
                                $this->set("count", $count);
                        if (isset($np))
                                $this->set("np", $np);
                        $this->render(null, "default-e14");
                }
        }

        /** @param string $p page name in etc/*.php, folder or NULL
                &param string $subp file name if $p was a folder*/
        public function etc($p = NULL, $subp = NULL) {
                //debug($this->request->params);                
                if ($p === "locale" || $p === "js") {
                        $this->response->file($GLOBALS["etc"] . DS . $p . DS . $subp);
                        $this->response->send();
                } else if (stristr($p, ".php")) {
                        $this->set("p", $p);
                        $this->render(null, "default-e14");
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
                $this->render(null, "admin_default-e14");
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
         * @param String $p method name (defined in view/admin_dvd.ctp)
         */
        public function admin_dvd($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set('pIndex', 'admin__dvd');
                $this->set('pMethod', $p);
                $this->render(null, "admin_default-e14");
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

        /**
         * @param String $p method name (defined in view/admin_content.ctp template)
         */
        public function admin_content($p = null) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set('pIndex', 'admin__activites');
                $this->set('pMethod', $p);
                $this->render(null, "admin_default-e14");
        }

}
