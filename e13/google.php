<?php
require ('./include/php_registre.inc.php');
$r = new Registre(filter_input(INPUT_SERVER,'PHP_SELF'));
require ($GLOBALS['include__php_page.class.inc']);
$p = new Page($r, "e13__google");
$p->ajouterContenu($p->adSense($GLOBALS['include__AdSense_search_result.inc']));
$p->fin();
?>
