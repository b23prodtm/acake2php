<div class="clients form">
<?php echo $this->Form->create('Client'); ?>
	<fieldset>
		<legend><?php echo __('Add a new subscription'); ?></legend>
	<?php
		echo $this->Form->input('id', array('label' => __('My username')));
		echo $this->Form->input('email', array('label' => __('My email address')));
		echo $this->Form->hidden('id_motdepasse');
		echo $this->Form->input('prenom', array('label' => __('My first name')));
		echo $this->Form->input('nom', array('label' => __('My name')));
		echo $this->Form->input('annee_de_naissance', array('label' => __('My birthday')));
		echo $this->Form->input('adresse', array('label' => __('My street address')));
		echo $this->Form->input('codepostal', array('label' => __('My city code')));
		echo $this->Form->input('ville', array('label' => __('My city')));
		echo $this->Form->input('pays', array('label' => __('My country')));
		echo $this->Form->input('telephone', array('label' => 'My phone number'));
		echo $this->Form->input('role', array(
				'label' => 'Choose a role',
        'options' => array('admin' => 'Member', 'visiteur' => 'Free')
        )
		);
		echo $this->Form->input('cree', array('label' => 'Profile creation date'));
		echo $this->Form->input('modifie', array('label' => 'Profile modification date'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Continue')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('My subscription'), array('action' => 'index')); ?></li>
	</ul>
</div>
