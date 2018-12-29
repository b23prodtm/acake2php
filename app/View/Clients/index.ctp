<div class="clients index">
	<h2><?php echo __('Profils clients'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('identifiant'); ?></th>
			<th><?php echo $this->Paginator->sort('email'); ?></th>
			<th><?php echo $this->Paginator->sort('fk_id_mdp'); ?></th>
			<th><?php echo $this->Paginator->sort('nom'); ?></th>
			<th><?php echo $this->Paginator->sort('prenom'); ?></th>
			<th><?php echo $this->Paginator->sort('annee_de_naissance'); ?></th>
			<th><?php echo $this->Paginator->sort('adresse'); ?></th>
			<th><?php echo $this->Paginator->sort('codepostal'); ?></th>
			<th><?php echo $this->Paginator->sort('ville'); ?></th>
			<th><?php echo $this->Paginator->sort('pays'); ?></th>
			<th><?php echo $this->Paginator->sort('telephone'); ?></th>
			<th><?php echo $this->Paginator->sort('role'); ?></th>
			<th><?php echo $this->Paginator->sort('cree'); ?></th>
			<th><?php echo $this->Paginator->sort('modifie'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($clients as $client): ?>
	<tr>
		<td><?php echo h($client['Client']['identifiant']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['email']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['fk_id_mdp']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['nom']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['prenom']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['annee_de_naissance']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['adresse']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['codepostal']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['ville']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['pays']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['telephone']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['role']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['cree']); ?>&nbsp;</td>
		<td><?php echo h($client['Client']['modifie']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Visualiser'), array('action' => 'view', $client['Client']['id'])); ?>
			<?php echo $this->Html->link(__('Modifier'), array('action' => 'edit', $client['Client']['id'])); ?>
			<?php echo $this->Form->postLink(__('Supprimer'), array('action' => 'delete', $client['Client']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $client['Client']['id']))); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
		'format' => __('Page {:page} de {:pages}, affichage de {:current} enregistrements sur un total de {:count}, starting on record {:start}, se termine à {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('précédent'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('suivant') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Nouveau client'), array('action' => 'add')); ?></li>
	</ul>
</div>
