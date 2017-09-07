<?php

/*
 * @copyrights www.b23prodtm.info - 2017 (all rights reserved to author)
 * @author T. Arimanana
 */

App::uses('AppController', 'Controller');

/**
 * CakePHP e13Controller
 * @author wwwb23prodtminfo <b23prodtm at sourceforge.net>
 */
class e13Controller extends AppController {

        public function index($id) {                
                require ROOT . DS . APP_DIR . DS . 'e13' . DS . 'include' . DS . 'php_index.inc.php';
                $r = new Index($_SERVER["PHP_SELF"]);                
                require($GLOBALS["e13__index"]);
        }

}
