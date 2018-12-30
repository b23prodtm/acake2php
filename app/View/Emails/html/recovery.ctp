<p><?php echo __('Cher utilisateur %s', $client['Client']['identifiant']); ?>,<br>
  <?php echo __('Vous avez demandé la récupération de votre mot de passe. Par sécurité, nous vous recommandons de réinitialiser celui-ci.');?></p>
<p><?php echo __("Si vous n'êtes pas l'auteur de cette demande, vous pouvez ignorer ce message."); ?></p>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Réinitialiser le mot de passe'), array(
    'controller' => 'MotDePasse', 'action' => 'delete', $client['MotDePasse']['id'])
  );?></li>
  </ul>
</div>
<p><?php echo __("Ce message est généré à la demande de l'utilisateur."); ?></p>
