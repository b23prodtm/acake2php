<?php
if ($p) {
        include $GLOBALS['etc'] . DS . $p;
} else {
        include $GLOBALS['etc__index'];
}
?>