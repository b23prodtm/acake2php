<div class="clients form">
<?php echo $this->Form->create('Client'); ?>
	<fieldset>
		<legend><?php echo __('Ajoutez votre profil client'); ?></legend>
	<?php
		echo $this->Form->input('identifiant', array('label' => __('Votre identifiant')));
		echo $this->Form->input('email', array('label' => __('Une adresse email')));
		echo $this->Form->input('fk_id_mdp', array('hidden' => true));
		echo $this->Form->input('nom', array('label' => __('Votre nom')));
		echo $this->Form->input('prenom', array('label' => __('Votre prénom')));
		echo $this->Form->input('annee_de_naissance', array('label' => __('Votre date de naissance')));
		echo $this->Form->input('adresse', array('label' => __('N°, Nom de la voie')));
		echo $this->Form->input('codepostal', array('label' => __('Code postal'));
		echo $this->Form->input('ville', array('label' => __('Localité'));
		echo $this->Form->input('pays', array('label' => __('Pays')));
		echo $this->Form->input('telephone', array('label' => 'Un numéro de téléphone');
		echo $this->Form->input('role', array(
				'label' => 'Séléctionner votre rôle',
        'options' => array('admin' => 'Admin', 'visiteur' => 'Visiteur')
        )
		);
		echo $this->Form->input('cree', array('label' => 'Date de création du profil');
		echo $this->Form->input('modifie', array('label' => 'Date de modification du profil');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Soumettre')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Liste des profils clients'), array('action' => 'index')); ?></li>
	</ul>
</div>
