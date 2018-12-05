<?php
if ($p && isset($r)) {
        require $r->r['etc'] . DS . $p;
} else if (isset($r)){
        require $r->r['etc__index'];
} else {
  // code...
}
?>
