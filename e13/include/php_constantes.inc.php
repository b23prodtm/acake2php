<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to b23|prod)
  @author	www.b23prodtm.info
  @date	Sat Sep 18 15:36:40 CEST 2004 @608 /Internet Time/
  @filename	php_constantes.inc
 */
global $registreConstantes;
if (!isset($registreConstantes)) {
        $registreConstantes = 1;
        $registre = new Registre(filter_input(INPUT_SERVER,'PHP_SELF'));
        $local = array_key_exists('local', $_SESSION) ? "_local" : "";
        $constantes = $registre->parseBundle($GLOBALS["etc"], "constantes" . $local);
        foreach ($constantes as $key => $val) {
                define($key, $val);
        }
}
?>
