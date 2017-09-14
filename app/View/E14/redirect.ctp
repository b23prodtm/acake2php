<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["redirect"] . DS  . $p);
                } else if ($p) {
                        include($GLOBALS["redirect__" . $p]);
                } else {
                        include($GLOBALS["redirect__index"]);
                }
