<?php

if (isset($pIndex)) {
        $r = new Index(null);
        require $GLOBALS['include__php_page.class.inc'];
        $page = new Page($r, $pIndex);
        if (isset($pUrl)) {
                $page->ajouterContenu(file_get_contents($pUrl));
        }
        $page->fin();
}
?>