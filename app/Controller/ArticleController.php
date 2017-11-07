<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP ArticleController
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class ArticleController extends AppController {

        public function index($fk_reference_categorie = null) {
                if ($fk_reference_categorie === null) {
                        $this->set('articles', $this->Article->find('all'));
                } else {
                        $this->set('articles', $this->Article->find($fk_reference_categorie));
                }
                $this->set("pIndex","activites__index");
                $this->render(null, "default-e14");
        }

        /**
         * @param String $p method name (defined in view/admin_content.ctp template)
         */
        public function admin_index($p = null) {
                //debug($this->request->params);
                //debug($GLOBALS);
                $this->set('pIndex', 'admin__activites');
                $this->set('pMethod', $p);
                $this->render(null, "admin_default-e14");
        }

        public function view($id) {
                if (!$id) {
                        throw new NotFoundException(__('Invalid article'));
                }

                $article = $this->Article->findById($id);
                if (!$article) {
                        throw new NotFoundException(__('Invalid article'));
                }
                $this->set("pIndex","activites__view");
                $this->set('article', $article);
        }

}
