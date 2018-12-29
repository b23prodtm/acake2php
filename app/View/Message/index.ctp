<?php
/* vue message index.ctp */
require APP . $r->r['include__php_tbl.class.inc'];
echo $this->Html->link(
    __('Poster un message'),
    array('controller' => 'message', 'action' => 'add')
);
$t = new Tableau(count($messages) + 1, 5, __('Tableau de bord'));
$t->setContenu_Ligne(0, array(__('date'), __('titre'), __('identifiant')));

/* On fait un tour des $messages array */

for ($i = 1; $i < count($messages) + 1; $i++) {
		$message = $messages[$i - 1];
		$t->setContenu_Cellule($i, 0, $this->Text->autoLinkEmails($message['Message']['fk_identifiant']));
		$t->setContenu_Cellule($i, 1, $this->Html->link($message['Message']['titre'], array(
			'action' => 'view', $message['Message']['id']))
		);
		$t->setContenu_Cellule($i, 2, $message['Message']['date']);
		$t->setContenu_Cellule($i, 3, $this->Html->link(__('Modifier un message'), array(
			'action' => 'edit', $message['Message']['id']))
		);
		$t->setContenu_Cellule($i, 4, $this->Form->postLink(
                __('Supprimer'),
                array('action' => 'delete', $message['Message']['id']),
                array('confirm' => 'Êtes-vous sûr ?'))
		);
}
echo $t->fin(4);
?>
