<p class="error">
	<strong><?php echo __d('cake', 'Error'); ?>: </strong>
	<?php echo $message; ?>
</p>
<p><strong><?php echo __d('cms','Please review your configuration file Please review your application ('. APP . ') :'); ?>: Config/database.cms.php, Scripts/bootargs.sh</strong>
<?php echo __d('cms','In a server shell prompt'); ?>:<pre>./migrate-database.sh -Y -i</pre>
</p>
<?php
if (Configure::read('debug') > 0):
	echo $this->element('exception_stack_trace');
endif;
