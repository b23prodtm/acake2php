<?php

if (stristr($p, ".php")) {
        include($GLOBALS["e13"] . DS . $p);
} else {
        include($GLOBALS["e13__index"]);
}
