<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["library"] . DS  . $p);
                } else if ($p) {
                        include($GLOBALS["library__" . $p]);
                } else {
                        include($GLOBALS["library__index"]);
                }
?>