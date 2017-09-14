<?php

if (stristr($p, ".php")) {
        include($GLOBALS["admin"] . DS . $p);
} else if ($p) {
        include($GLOBALS["admin__" . $p]);
} else {
        include($GLOBALS["admin__index"]);
}
