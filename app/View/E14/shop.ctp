<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["shop"] . "/" . $p);
                } else if ($p) {
                        include($GLOBALS["shop__" . $p]);
                } else {
                        include($GLOBALS["shop__index"]);
                }
