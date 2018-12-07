<?php

if (stristr($p, ".php")) {
        include $r->r["shop"] . DS . $p;
} else {
        include $r->r["shop__index"];
}
