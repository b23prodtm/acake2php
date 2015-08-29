<?php
require ("include/php_registre.inc.php");
$r = new Registre(filter_input(INPUT_SERVER,'PHP_SELF'));
require($GLOBALS['include__php_page.class.inc']);


$page = new Page($r, "e13__activites");

$page->ajouterContenu("<br><center>".$r->lang("licenceutil","activites")."</center><br><br>");

/* inclusion d'un fichier html editable depuis word, ou autre traitement de texte. */
$activites = file_get_contents("activites_inc.html");
$page->ajouterContenu("<br>" . $activites . "<br>");
$page->fin();
?>