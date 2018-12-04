<?php

if (stristr($p, ".php")) {
        require $r->r["shop"] . DS . $p;
} else {
        require $r->r["shop__index"];
}
