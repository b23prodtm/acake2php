<?php
                if (stristr($p, ".php")) {
                        require($GLOBALS["blog"] . DS  . $p);
                } else if ($p) {
                        require($GLOBALS["blog__" . $p]);
                } else {
                        require($GLOBALS["blog__index"]);
                }
