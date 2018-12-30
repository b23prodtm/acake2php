<div class="users form">
<?php echo $this->Flash->render('auth'); ?>
<?php echo $this->Form->create('Client'); ?>
    <fieldset>
        <legend>
            <?php echo __("Veuillez entrer un identifiant et un mot de passe s'il-vous-plaît."); ?>
        </legend>
        <?php echo $this->Form->input('identifiant');
        echo $this->Form->input('motdepasse');
    ?>
    </fieldset>
<?php echo $this->Form->end(__('Connexion')); ?>
</div>
