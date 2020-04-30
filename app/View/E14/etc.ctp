<?php
if ($p && isset($r)) {
        include APP . $r->r['etc'] . DS . $p;
} else if (isset($r)){
        include APP . $r->r['etc__index'];
} else {
  // code...
}
?>
