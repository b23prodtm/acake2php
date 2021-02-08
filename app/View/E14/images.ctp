<?php
if ($p && isset($r)) {
        include APP . $r->r['images'] . DS . $p;
} else if (isset($r)){
        include APP . $r->r['images__index'];
} else {
  // code...
}
?>
