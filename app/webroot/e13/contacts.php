<?php
require("include/php_index.inc.php");
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF'));
require($GLOBALS["include__php_module_guestbook.inc"]);
require($GLOBALS["include__php_page.class.inc"]);

$page = new Page($r, "e13__contacts",false);
$page->ajouterContenu(file_get_contents("contacts.html"));
$page->fin();
?>
