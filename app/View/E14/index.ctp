<?php

if(isset($p) && array_key_exists("e13__" . $p, $GLOBALS)){
        include($GLOBALS["e13__" . $p]);
} else {
        $r = new Index($this);
        require_once $GLOBALS['include__php_info.class.inc'];
        require_once $GLOBALS['include__php_SQL.class.inc'];
        require_once $GLOBALS['include__php_constantes.inc'];

        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

        /** test de la connexion */
        if ($sql->connect_succes()) {
                $pageUrl = $r->sitemap[$pIndex];
                /** infos flashs pagination ($p : offset) */
                $pages = array();
                $page = isset($p) ? $p : 0;
                echo $this->Info->getInfoFlashN($page, $pages);
                foreach($pages as $n => $offset) {
                        $this->Html->addCrumb($n, $pageUrl . "/" . $offset);
                }
                echo "[ " . $this->Html->getCrumbs(" - ") . " ]";
        } else {
                echo HTML_lien($r->sitemap["e13__index"] . "?debug=1", "Err code : " . ERROR_DB_CONNECT);
        }
}
