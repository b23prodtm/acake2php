<?php

if (isset($p)) {
        if (stristr($p, ".php")) {
                include($GLOBALS["e13"] . DS . $p);
        } else {
                include($GLOBALS["e13__" . $p]);
        }
} else {
        $r = new Index();
        require_once $GLOBALS['include__php_info.class.inc'];
        require_once $GLOBALS['include__php_SQL.class.inc'];
        require_once $GLOBALS['include__php_constantes.inc'];

        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

        /** test de la connexion */
        if ($sql->connect_succes()) {
                /** une info non sauvegardee en base de donnee */
                $info = new Info($sql, $result, $r->lang("message", "infos"), "webmaster", "1", date("Y-m-d"));
                $info->ajouterImage($r->sitemap['images__wip'], "");
                $info->ajouterContenu($r->lang("visitus", "infos") . " " . HTML_lien($r->sitemap["blog__index"], $r->lang("blog")));

                echo "<BR>" . $info->getTableauMultiLang($sql);
                $sql->close();
        } else {
                echo "Err code : " . ERROR_DB_CONNECT;
        }
}
