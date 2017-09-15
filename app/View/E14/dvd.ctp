<?php

if (stristr($p, ".php")) {
        include $GLOBALS["library"] . DS . $p;
} else {
        include $GLOBALS["library__index"];
}
?>