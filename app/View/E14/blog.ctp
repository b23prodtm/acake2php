<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["blog"] . "/" . $p);
                } else if ($p) {
                        include($GLOBALS["blog__" . $p]);
                } else {
                        include($GLOBALS["blog__index"]);
                }
