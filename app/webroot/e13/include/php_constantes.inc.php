<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to b23|prod)
  @author	www.b23prodtm.info
  @date	Sat Sep 18 15:36:40 CEST 2004 @608 /Internet Time/
  @filename	php_constantes.inc
 * 
 * Ce script evalue les fichiers etc/constantes(_local).properties
 */
global $registreConstantes;
if (!isset($registreConstantes)) {
        $registreConstantes = 1;
        $registre = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
        function varEnv(&$val) {
                $d = stripos($val,"\$\{")+1;
                $f = stripos($val, "\}");
                if ($d && $f) {
                        $val = substr($val, $d + 1, $f - $d);
                        return true;
                } else {
                        return false;
                }
        } 
        $local = array_key_exists('local', $_SESSION) ? "_local" : "";
        $constantes = $registre->parseBundle($GLOBALS["etc"], "constantes" . $local);
        foreach ($constantes as $key => $val) {
                if (varEnv($val)) {
                        $val = getenv($val);
                }
                define($key, $val);
        }
}
?>
