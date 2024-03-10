<?php

${__FILE__} = new Index($this, __FILE__, true, dirname(__DIR__));
include ${__FILE__}->r['include__php_page.class.inc'];
$p = new Page($i, "e13__google");
$p->ajouterContenu($p->getAdElement("Adsense/search_result"));
$p->fin();
?>
