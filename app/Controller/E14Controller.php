<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP E13
 * les indexes de pages 'pIndex' se trouvent dans webroot/.../etc/menu.properties (menu deroulant) et sitemap.properties (plan de site general)
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class E14Controller extends AppController {

        public function __construct($request = null, $response = null) {
                parent::__construct($request, $response);
                /* initialise les $GLOBALS et le sitemap */
                $this->r = new Index($this->View, ROOT . DS . 'index.php', false, WWW_ROOT . 'php-cms/');
                $this->set("i_sitemap", $this->r->sitemap);
        }

        /** @param string $p page filename.php
         */
        public function index($p = NULL, $images = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if ($p === "images") {
                        return $this->images($images);
                } else if ($p) {
                        /* parametre de nom de fichier c.f. index.ctp */
                        $this->set("p", $p);
                } else {
                        /* parametre sitemap c.f. index.ctp , page.Class */
                        $this->set("pIndex", "e13__index");
                }
                $this->render("index", "default-e14");
        }

        /**
         * @param String $p SITEMAP.PROPERTIES key in [admin]
         */
        public function admin_index($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("pIndex", "admin__" . $p);
                $this->render("admin_index", "admin_default-e14");
        }

        /**
         * @param int $np paginate number
         * @param int $count count per page
         * @param int $YYYY 4-digit year
         * @param int $MM 2-digit month
         * @param int $DD 2-digit day
         */
        public function infos($np = 1, $count = 10, $YYYY = NULL, $MM = NULL, $DD = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("pIndex", "infos__index");
                $d = "";
                /** date selection */
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
                $this->set("count", $count);
                $this->set("np", $np);
                $this->render("infos", "default-e14");
        }

        /**
         * @param String $p  method name (defined in view/admin_infos.ctp)
         */
        public function admin_infos($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("pIndex", "admin__infos");
                $this->set("pMethod", $p);
                $this->render(null, "admin_default-e14");
        }

        /**
         * @param int categorie
         * @param int $np paginate number
         * @param int $count count per page
         * @param int $YYYY 4-digit year
         * @param int $MM 2-digit month
         * @param int $DD 2-digit day
         */
        public function cat($cat = NULL, $np = 1, $count = 10, $YYYY = NULL, $MM = NULL, $DD = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                if (isset($cat)) {
                        $this->set("cat", $cat);
                        $this->infos($np, $count, $YYYY, $MM, $DD);
                } else {
                        $this->set("pIndex", "cat__index");
                        $this->render(null, "default-e14");
                }
        }

        /**
         * @param String $p method name (defined in view/admin_cat.ctp)
         */
        public function admin_cat($p = NULL) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("pIndex", "admin__cat");
                $this->set("pMethod", $p);
                $this->render(null, "admin_default-e14");
        }

        /** @param string $p page name in etc/*.php, folder or NULL
          &param string $subp file name if $p was a folder */
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
        public function dvd($webdir = 'data', $file = '') {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set("pIndex", "library__index");
                $this->set('nom', $file);
                $this->set('base', $webdir);
                //echo $webdir . ' '. $file;
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p method name (defined in view/admin_dvd.ctp)
         */
        public function admin_dvd($p = NULL, $webdir = 'data', $file = '') {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set('pIndex', 'admin__library');
                $this->set('pMethod', $p);
                $this->set('nom', $file);
                $this->set('base', $webdir);
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

        /**/

        public function about() {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->index("index.php");
        }

}
