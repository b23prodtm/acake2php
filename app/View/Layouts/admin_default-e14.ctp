<?php

if (isset($pIndex)) {
        $r = new Index(null);
        require_once $GLOBALS['include__php_page.class.inc'];
        /** no header sent to allow header() redirections*/
        $page = new ADMIN_Page($r, $pIndex, session_id(), array(), false);
        $page->ajouterContenu($this->fetch('content'));
        $page->ajouterContenu($page->menu->ouvrirBonneRubrique($page->getURL())->getRubriquesListe(true));
        echo "<div id='console_js'></div>";
        echo $this->Html->script("media-query.js");
        $page->fin();
}
?>