<?php

if (stristr($p, ".php")) {
        include $GLOBALS["blog"] . DS . $p;
} else {
        include $GLOBALS["blog__index"];
}
