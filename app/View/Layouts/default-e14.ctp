<?php

if (isset($pIndex)) {
		$r = new Index(null);
		require $GLOBALS['include__php_page.class.inc'];
		$page = new Page($r, $pIndex);
		$page->entete();
		$page->ajouterContenu($this->fetch('content'));
		$page->fin();
}
?>