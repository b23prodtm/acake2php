<?php

if (isset($pIndex)) {
        $r = new Index($this);
        require_once $GLOBALS['include__php_page.class.inc'];
        $page = new Page($r, $pIndex);
        $safe = array('safe' => false);
        $script = "<!-- Global Site Tag (gtag.js) - Google Analytics -->\n" .
        "<script async src=\"//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js\"></script>
        <script>
                (adsbygoogle = window.adsbygoogle || []).push({
                        google_ad_client: \"ca-pub-3110663509052970\",
                        enable_page_level_ads: true
                });
        </script>". "\n";
        $page->entete($script);
        $page->ajouterContenu($this->fetch('content'));
        $page->fin();
} else {
        trigger_error("no pIndex set ", E_USER_WARNING);
}
?>