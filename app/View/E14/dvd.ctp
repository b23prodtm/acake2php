<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["dvd"] . DS  . $p);
                } else if ($p) {
                        include($GLOBALS["dvd__" . $p]);
                } else {
                        include($GLOBALS["dvd__index"]);
                }
