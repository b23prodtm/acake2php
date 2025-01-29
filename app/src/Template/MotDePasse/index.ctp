<div class="motdepasses index">
	<h2><?php echo __('Mot de passe'); ?></h2>
	<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Modifier mon mot de passe'), array('action' => 'edit', $motdepasse['Motdepasse']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Réinitialiser mon mot de passe'), array(
				'action' => 'delete',
				$this->Form->value('Motdepasse.id')
			), array(
				'confirm' => __('Êtes-vous certain de réinitialiser votre mot de passe ?')
		)); ?></li>
		<li><?php echo $this->Html->link(__('Modifier le profil'),
		array(
			'action' => 'edit', AuthComponent::user('id')
		)); ?> </li>
	</ul>
</div>
