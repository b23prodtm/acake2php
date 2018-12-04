<?php
if ($p) {
        require $r->r['etc'] . DS . $p;
} else {
        require $r->r['etc__index'];
}
?>
