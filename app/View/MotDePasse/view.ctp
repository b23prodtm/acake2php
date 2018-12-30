<div class="motdepasses view">
<h2><?php echo __('Mot de passe'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($motdepasse['Motdepasse']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mot de passe'); ?></dt>
		<dd>
			<?php echo h($motdepasse['Motdepasse']['password']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Confirmation du mot de passe'); ?></dt>
		<dd>
			<?php echo h($motdepasse['Motdepasse']['password_confirm']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Créé le'); ?></dt>
		<dd>
			<?php echo h($motdepasse['Motdepasse']['cree']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modifié le'); ?></dt>
		<dd>
			<?php echo h($motdepasse['Motdepasse']['modifie']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
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
	</ul>
</div>
