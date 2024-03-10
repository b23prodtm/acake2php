<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to b23|prod)
  @author	www.b23prodtm.info
  @date	Sat Sep 18 15:36:40 CEST 2004 @608 /Internet Time/
  @filename	php_constantes.inc
 *
 * Ce script evalue les fichiers etc/constantes(_local).properties
 */
if (!isset($_ENV['registreConstantes']) && ! defined('registreConstantes')) {
        $_ENV['registreConstantes'] = 1;
        define('registreConstantes', 1);
        $registre = new Index();

        /** evalue la variable d'environnement eventuellement imbriquee
          * contenue dans la valeur de la cle $val et modifie celle-ci en retour  */
        function varEnv($val) {
                /** derniere occurence */
                $d = strripos($val, "{");
                /** 1ere occurence */
                $f = stripos($val, "}");
                if ($d === FALSE || $f === FALSE) {
                        return $val;
                } else {
                        $valEnv = substr($val, $d + 1, $f - $d - 1);
                        i_debug("env ". $valEnv, DEBUG_VERBOSE);
                        /* substituer la {variable} par getenv(variable) */
                        $val = str_replace("{" . $valEnv . "}", getenv(strtoupper($valEnv)), $val);
                        /* v�rifie si variable imbriquee, continuer */
                        i_debug("= ". substr($val,0,1) . "*****" . substr($val,-1), DEBUG_VERBOSE);
                        return varEnv($val);
                }
        }
        /* les clefs de TEST eventuellement */
        $local = "TEST_";
        $constantes = $registre->constantes_ini;
        $define = array();
        foreach ($constantes as $key => $val) {
                $key_type = substr($key, 0, strlen($local));
                /* utiliser les clefs de test (TEST_$1) si phase de test local
                * URL?local=1 (session locale)
                */
                if(i_isLocal()) {
                        if($key_type === $local) {
                                /* renommer les clefs TEST_$1 en $1 */
                                $key = substr($key, strlen($local));
                        } else if(array_key_exists($key, $define)) {
                                /* eviter l'ecrasement de clefs de test locales */
                                continue;
                        }
                }
                /**
                * extraire les variables environnement
                */
                $e = varEnv($val);
                $define[$key] = $e;
        }
        foreach ($define as $key => $value) {
                define($key, $value);
        }
}
?>
