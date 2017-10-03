<?php

if (isset($pIndex)) {
        $r = new Index(null);
        require $GLOBALS['include__php_page.class.inc'];
        $page = new ADMIN_Page($r, $pIndex, session_id());
        $page->ajouterContenu($this->fetch('content'));
        echo $page->fin(0);
}
?>