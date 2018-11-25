<?php
	$r = new Index($this);
 /* Vue Message add.ctp */
	echo $this->Form->create('Message'); 
	echo $this->Form->input('titre', array('label' => $r->lang('titre', 'contactus'),
			'required' => true));
	echo $this->Form->input('texte', array('label' => $r->lang('texte', 'contactus'),
				'required' => true));
	echo $this->Form->input('fk_identifiant', array('label' => $r->lang('identifiant', 'contactus'),
				'required' => true));
	echo $this->Form->input('date', array('label' => $r->lang('date', 'contactus'),
				'required' => true));
	echo $this->Form->end($r->lang('write', 'contactus'));
?>