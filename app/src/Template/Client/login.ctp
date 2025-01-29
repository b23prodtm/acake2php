<div class="users form">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('Client'); ?>
    <fieldset>
        <legend>
            <?php echo __("Please enter an username"); ?>
        </legend>
        <?php echo $this->Form->input('id');
        echo $this->Form->input('motdepasse', array('type' => 'password'));
    ?>
    </fieldset>
    <?php echo $this->Form->end(__('Sign in')); ?>
</div>
<h3><?php echo __('Actions'); ?></h3>
<ul><li><?php echo $this->Html->link(__('New subscription'), array('action' => 'add')); ?></li>
<li><?php echo $this->Form->postLink(__('Forgot my password ?'), array(
    'action' => 'recovery'
  )); ?></li>
</ul>
</div>
