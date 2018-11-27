<?php
	/* vue message view.ctp */
	$r = new Index($this);
	require $GLOBALS['include__php_tbl.class.inc'];
	$t = new Tableau(3, 2, $r->lang("view", "contactus"));
	$t->setContenu_Colonne(0, $r->lang(array("date", "titre", "texte"), "contactus"));
	$t->setContenu_Cellule(0, 1, $message['Message']['date']);
	$t->setContenu_Cellule(1, 1, $message['Message']['titre']);
	$t->setContenu_Cellule(2, 2, $message['Message']['texte']);
	$t->fin(1);

?>