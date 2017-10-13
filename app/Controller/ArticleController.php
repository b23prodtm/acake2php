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

        public function index($id) {
                if ($id !== null) {
                        $this->set('articles', $this->Article->find('all'));
                } else {
                        $this->set('articles', $this->Article->find($id));
                }
        }

}
