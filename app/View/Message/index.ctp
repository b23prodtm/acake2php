<?php
/* vue message index.ctp */
$r = new Index($this);
include $GLOBALS['include__php_tbl.class.inc'];
$t = new Tableau(count($messages) + 1, 3, $r->lang("contactus"));
$t->setContenu_Ligne(0, $r->lang(array("date", "titre", "identifiant"), "contactus"));

/* On fait un tour des $messages array */

for ($i = 1; $i < count($messages) + 1; $i++) {
		$message = $messages[$i - 1];
		$t->setContenu_Cellule($i, 0, $message['Message']['fk_identifiant']);
		$t->setContenu_Cellule($i, 1, $this->Html->link($message['Message']['id'], array('controller' => 'message',
					'action' => 'view',
					array($message['Message']['id'])))
		);
		$t->setContenu_Cellule($i, 2, $message['Message']['published']);
}
echo $t->fin();
?>