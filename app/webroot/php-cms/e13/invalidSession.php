<?php
${__FILE__} = new Index($this, __FILE__, true, dirname(__DIR__)));
include ${__FILE__}->r['include__php_page.class.inc'];

$p = new Page(${__FILE__}, 'e13__invalid', true, true);
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
