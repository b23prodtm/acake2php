<?php

if (stristr($p, ".php") && isset($r)) {
        require $r->r["blog"] . DS . $p;
} else if(isset($r)){
        require $r->r["blog__index"];
} else {
  // code...
}
