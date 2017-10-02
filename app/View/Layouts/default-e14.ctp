<?php

if (isset($pIndex)) {
		$r = new Index(null);
		require $GLOBALS['include__php_page.class.inc'];
		$page = new Page($r, $pIndex);
		$script = "<!-- Global Site Tag (gtag.js) - Google Analytics -->\n".
		$this->Html->script('https://www.googletagmanager.com/gtag/js?id=UA-107378583-1')."\n".
		$this->Html->script('gtag')."\n".
		$this->Html->script('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js')."\n".
		$this->Html->script('adsense')."\n";
		$page->entete($script);
		$page->ajouterContenu($this->fetch('content'));
		echo $page->fin(0);
}
?>