<div class="clients index">
	<h2><?php echo __('Tableau de bord'); ?></h2>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Se connecter'), array('action' => 'login')); ?></li>
		<li><?php echo $this->Html->link(__('Nouveau client'), array('action' => 'add')); ?></li>
	</ul>
</div>
