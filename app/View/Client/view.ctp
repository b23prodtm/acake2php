<div class="clients view">
<h2><?php echo __('Client'); ?></h2>
	<dl>
		<dt><?php echo __('My username'); ?></dt>
		<dd>
			<?php echo h($client['Client']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My email address'); ?></dt>
		<dd>
			<?php echo h($client['Client']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My first name'); ?></dt>
		<dd>
			<?php echo h($client['Client']['prenom']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My name'); ?></dt>
		<dd>
			<?php echo h($client['Client']['nom']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My birthday'); ?></dt>
		<dd>
			<?php echo h($client['Client']['annee_de_naissance']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My street address'); ?></dt>
		<dd>
			<?php echo h($client['Client']['adresse']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My city code'); ?></dt>
		<dd>
			<?php echo h($client['Client']['codepostal']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My city'); ?></dt>
		<dd>
			<?php echo h($client['Client']['ville']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My country'); ?></dt>
		<dd>
			<?php echo h($client['Client']['pays']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My phone number'); ?></dt>
		<dd>
			<?php echo h($client['Client']['telephone']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('My role'); ?></dt>
		<dd>
			<?php echo h($client['Client']['role']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created on'); ?></dt>
		<dd>
			<?php echo h($client['Client']['cree']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified on'); ?></dt>
		<dd>
			<?php echo h($client['Client']['modifie']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Modify subscription'), array('action' => 'edit', $client['Client']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Remove subscription'), array(
			'action' => 'delete', $client['Client']['id']),
			array(
				'confirm' => __('Are you sure to unsubscribe # %s?', $client['Client']['id'])
			)
		); ?> </li>
		<li><?php echo $this->Html->link(__('Change my password'), array('controller' => 'motdepasse', 'action' => 'index')); ?> </li>
	</ul>
</div>
