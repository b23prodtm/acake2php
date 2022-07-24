<div class="motdepasses form">
<?php echo $this->Form->create('MotDePasse'); ?>
	<fieldset>
		<legend><?php echo __('Créer mon mot de passe'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('password', array('label' => 'Entrez un mot de passe'));
		echo $this->Form->input('password_confirm', array(
			'type' => 'password',
			'label' => 'Confirmez le mot de passe')
		);
		echo $this->Form->input('cree');
		echo $this->Form->input('modifie');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Soumettre')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Mon profil'), array('controller' => 'Client', 'action' => 'index')); ?></li>
	</ul>
</div>
