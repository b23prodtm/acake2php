<?php
 /* Vue Message add.ctp */
	echo $this->Form->create('Message');
	echo $this->Form->input('titre', array('label' => __('Entête'),
			'required' => true));
	echo $this->Form->input('texte', array(
			'label' => __('Description'),
				'required' => true));
	echo $this->Form->input('fk_identifiant', array('label' => __('Auteur'),
				'required' => true));
	echo $this->Form->input('date', array('label' => __('Date'),
				'required' => true));
	echo $this->Form->end(__('Poster un ticket'));
?>
