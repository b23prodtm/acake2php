<?php

if (stristr($p, ".php")) {
        include $GLOBALS["shop"] . DS . $p;
} else {
        include $GLOBALS["shop__index"];
}
