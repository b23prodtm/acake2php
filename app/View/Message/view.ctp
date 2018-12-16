<?php
	/* vue message view.ctp */
	require APP . $r->r['include__php_tbl.class.inc'];
	$t = new Tableau(3, 2, __('Ticket view'));
	$t->setContenu_Colonne(0, array(__('date'), __('titre'),
	 		$this->Text->autoLinkEmails($message['fk_identifiant'])
	));
	$t->setContenu_Cellule(0, 1, $message['Message']['date']);
	$t->setContenu_Cellule(1, 1, $message['Message']['titre']);
	$t->setContenu_Cellule(2, 1, $this->Text->autoLink($message['Message']['texte']));
	echo $t->fin(4);
?>
