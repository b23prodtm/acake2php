<?php

if (!$i_sitemap) { require '../include/php_index.inc.php'; }
$r = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
require $GLOBALS['include__php_constantes.inc'];
require $GLOBALS['include__php_page.class.inc'];
require $GLOBALS['include__php_menu.class.inc'];
require $GLOBALS['include__php_SQL.class.inc'];


$pAdmin = new ADMIN_Page($r, "admin__index", session_id());
$pAdmin->ajouterContenu("<br><br><b><center>" . $pAdmin->r->lang("bienvenue", "admin") . "</center></b><br>\n");
global $MENU;
echo isset($MENU) ? "<b>YES</b>" : " NO";
$pAdmin->ajouterContenu("<br>" . $pAdmin->r->lang("choosemodule", "admin") . ":<br>" . $MENU["admin__index"]->getRubriquesListe());
$pAdmin->fin();
?>