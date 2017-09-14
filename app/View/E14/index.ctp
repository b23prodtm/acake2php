<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["e13"] . DS . $p);
                } else if ($p) {
                        include($GLOBALS["e13__" . $p]);
                } else {
                        include($GLOBALS["e13__index"]);
                }
