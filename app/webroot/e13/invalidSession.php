<?php
require("include/php_index.inc.php");
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF'));
require($GLOBALS['include__php_page.class.inc']);

$p = new Page($r, 'e13__invalid', true, true);
$contenu = "<script>
function goBack()
  {
  window.history.back()
  }
</script>

<body>
<button onclick='goBack()'>".$p->r->lang("back","buttons")."</button>";

$p->ajouterContenu("<hr>".$p->r->lang("invalid"). "<hr>".$contenu);
$p->fin();
?>