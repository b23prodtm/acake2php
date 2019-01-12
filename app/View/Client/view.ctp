<div class="clients view">
<h2><?php echo __('Client'); ?></h2>
	<dl>
		<dt><?php echo __('Identifiant'); ?></dt>
		<dd>
			<?php echo h($client['Client']['identifiant']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($client['Client']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Mot de passe'); ?></dt>
		<dd>
			<?php echo $this->Html->link($client['Motdepasse']['id'], array('controller' => 'motdepasse', 'action' => 'view', $client['Motdepasse']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nom'); ?></dt>
		<dd>
			<?php echo h($client['Client']['nom']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Prenom'); ?></dt>
		<dd>
			<?php echo h($client['Client']['prenom']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Annee De Naissance'); ?></dt>
		<dd>
			<?php echo h($client['Client']['annee_de_naissance']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Adresse'); ?></dt>
		<dd>
			<?php echo h($client['Client']['adresse']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Code Postal'); ?></dt>
		<dd>
			<?php echo h($client['Client']['codepostal']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Ville'); ?></dt>
		<dd>
			<?php echo h($client['Client']['ville']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Pays'); ?></dt>
		<dd>
			<?php echo h($client['Client']['pays']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Telephone'); ?></dt>
		<dd>
			<?php echo h($client['Client']['telephone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Role'); ?></dt>
		<dd>
			<?php echo h($client['Client']['role']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Crée le'); ?></dt>
		<dd>
			<?php echo h($client['Client']['cree']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modifié le'); ?></dt>
		<dd>
			<?php echo h($client['Client']['modifie']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Modifier le profil'), array('action' => 'edit', $client['Client']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Supprimer le profil'), array(
			'action' => 'delete', $client['Client']['id']),
			array(
				'confirm' => __('Êtes-vous certain de supprimer le profil # %s?', $client['Client']['id'])
			)
		); ?> </li>
		<li><?php echo $this->Html->link(__('Modifier mon mot de passe'), array('controller' => 'motdepasse', 'action' => 'index')); ?> </li>
	</ul>
</div>