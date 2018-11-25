<p class="error">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php echo $message; ?>
</p>
<p><strong><?php echo __d('cms','Please review your configuration file '); ?>: database.cms.php</strong>
<?php echo __d('cms','In a server shell prompt'); ?>:<pre>./configure.sh -d -Y</pre>
</p>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
