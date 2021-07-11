<div class="users form">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('Client'); ?>
    <fieldset>
        <legend>
            <?php echo __("Veuillez entrer un id et un mot de passe s'il-vous-plaît."); ?>
        </legend>
        <?php echo $this->Form->input('id');
        echo $this->Form->input('motdepasse', array('type' => 'password'));
    ?>
    </fieldset>
    <?php echo $this->Form->end(__('Connexion')); ?>
</div>
<h3><?php echo __('Actions'); ?></h3>
<ul><li><?php echo $this->Html->link(__('Nouveau client'), array('action' => 'add')); ?></li>
<li><?php echo $this->Form->postLink(__('Mot de passe oublié ?'), array(
    'action' => 'recovery'
  )); ?></li>
</ul>
</div>
