<?php

if (isset($pIndex)) {
		$r = new Index(null);
		require_once $GLOBALS['include__php_page.class.inc'];
		$page = new Page($r, $pIndex);
		$script = "<!-- Global Site Tag (gtag.js) - Google Analytics -->\n".
		$this->Html->script('https://www.googletagmanager.com/gtag/js?id=UA-107378583-1')."\n".
		$this->Html->scriptBlock("window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments)};
		gtag('js', new Date());

		gtag('config', 'UA-107378583-1');")."\n".
		$this->Html->script('//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js')."\n".
		$this->Html->scriptBlock("(adsbygoogle = window.adsbygoogle || []).push({
			google_ad_client: 'ca-pub-3110663509052970',
			enable_page_level_ads: true
		});")."\n";
		$page->entete($script);
		$page->ajouterContenu($this->fetch('content'));
		echo $page->fin(0);
}
?>