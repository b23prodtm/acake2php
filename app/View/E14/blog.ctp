<?php

if (stristr($p, ".php")) {
        require $r->r["blog"] . DS . $p;
} else {
        require $r->r["blog__index"];
}
