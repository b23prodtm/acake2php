<?php

if (isset($pIndex)) {
        $r = new Index($this);
        require_once $GLOBALS['include__php_page.class.inc'];
        $page = new Page($r, $pIndex);
        $script = $this->element("Adsense/analytics");
        $page->entete($script);
        $page->ajouterContenu($this->fetch('content'));
        $page->fin();
} else {
        trigger_error("no pIndex set in default-e14.ctp", E_USER_WARNING);
}
?>