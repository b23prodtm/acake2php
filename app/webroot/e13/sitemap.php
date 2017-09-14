<?php
if (!$i_sitemap) { require 'include/php_index.inc.php'; }
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF'));
require $GLOBALS['include__php_page.class.inc'];

$page = new Page($r, "e13__sitemap");

$page->ajouterContenu($page->menu->getRubriquesListe(true));

$page->fin();
?>
