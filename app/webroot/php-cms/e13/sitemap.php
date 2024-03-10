<?php
${__FILE__} = new Index($this, __FILE__, true, dirname(__DIR__)));
include ${__FILE__}->r['include__php_page.class.inc'];
$page = new Page(${__FILE__}, "e13__sitemap");
$page->ajouterContenu($page->menu->getRubriquesListe(true));
$page->fin();
?>
