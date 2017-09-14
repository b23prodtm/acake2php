<?php
                if (stristr($p, ".php")) {
                        include($GLOBALS["dvd"] . "/" . $p);
                } else if ($p) {
                        include($GLOBALS["dvd__" . $p]);
                } else {
                        include($GLOBALS["dvd__index"]);
                }
