<?php

if (stristr($p, ".php")) {
        include($GLOBALS["admin"] . DS . $p);
}else {
        include($GLOBALS["admin__index"]);
}
