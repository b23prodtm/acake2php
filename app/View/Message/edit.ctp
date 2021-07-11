<?php
echo __('Modifier un message');
echo $this->Form->create('Message');
echo $this->Form->input('titre', array('label' => __('Sujet de votre message'),
    'required' => true));
echo $this->Form->input('texte', array(
    'label' => __('Contenu du message'),
      'required' => true));
echo $this->Form->input('id', array('label' => __('Pseudonyme'),
      'required' => true));
echo $this->Form->input('date', array('label' => __('Date'),
            'required' => true));
echo $this->Form->input('id', array('hidden' => true));
echo $this->Form->end(__('Sauvegarder'));
?>
