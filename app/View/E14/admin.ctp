<?php

print_array_r($i_sitemap);
               if (stristr($p, ".php")) {
                        include($GLOBALS["admin"] . "/" . $p);
                } else if ($p) {
                        include($GLOBALS["admin__" . $p]);
                } else {
                        include($GLOBALS["admin__index"]);
                }
