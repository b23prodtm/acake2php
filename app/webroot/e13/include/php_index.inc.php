<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	// !!!:brunorakotoarimanana:20041121
  @filename	php_index.inc.php
 * 
 * 
  =========================================================
  configuration SITEMAP.PROPERTIES _ MENU.PROPERTIES _ CONTENT-LANG.PROPERTIES _ include/php_index.inc.php::__construct() GLOBALES
  =========================================================
 * 
 * UNE INSTANCE DE CET INDEX DOIT ETRE CREE POUR CHAQUE PAGE PHP AFIN DE POUVOIR UTILISER LES CHEMINS RELATIFS ET VARIABLES DE CONFIGURATIONS
 * 
 * 
 */
global $registreFichiers;
if (!isset($registreFichiers)) {

        $registreFichiers = 1;

        session_start();
        /** TIMEZONE http://php.net/manual/fr/timezones.php */
        date_default_timezone_set("Europe/Paris");

        require ("php_module_locale.inc.php");

        /* controle de l'existence d'une session */
        if (session_status() != PHP_SESSION_ACTIVE) {
                echo "phpSession: off " . session_status() == PHP_SESSION_DISABLED ? "(DISABLED)" : "";
        } elseif (filter_input(INPUT_GET, "debug") || array_key_exists("debug", $_SESSION)) {
                echo "phpSession: start\n"
                . "language " . getPrimaryLanguage() . "\n";
        }
        if (filter_input(INPUT_GET, "local") || array_key_exists("local", $_SESSION)) {
                echo "Local Configuration Enabled\n";
        }

        /**
         *  BEGIN         ERROR CHECKING 
         * */
        /*
         * @see condor_error
         * Registre constructeur de registre courant nécessaire sur chaque script .php
         */
        function strError($no) {
                $errno = "unknown";
                switch ($no) {
                        case E_COMPILE_ERROR: $errno = "COMPILE ERROR";
                                break;
                        case E_COMPILE_WARNING: $errno = "COMPILE WARNING";
                                break;
                        case E_CORE_ERROR: $errno = "CORE ERROR";
                                break;
                        case E_CORE_WARNING: $errno = "CORE WARNING";
                                break;
                        case E_DEPRECATED: $errno = "DEPRECATED";
                                break;
                        case E_ERROR: $errno = "ERROR";
                                break;
                        case E_NOTICE: $errno = "NOTICE";
                                break;
                        case E_PARSE: $errno = "PARSE";
                                break;
                        case E_RECOVERABLE_ERROR: $errno = "RECOVERABLE ERROR";
                                break;
                        case E_STRICT: $errno = "STRICT";
                                break;
                        case E_USER_DEPRECATED: $errno = "USER DCTED";
                                break;
                        case E_USER_ERROR: $errno = "USER ERR";
                                break;
                        case E_USER_NOTICE: $errno = "USER NOT";
                                break;
                        case E_USER_WARNING: $errno = "USER WARN";
                                break;
                        case E_WARNING: $errno = "WARNING";
                                break;
                        default:
                                break;
                }
                return $errno;
        }

        /**
          validation en sortie du type d'erreur rencontré
         *         
         * @see Index::__construct($selfScript) */
        function errValidType($errno, array $type, $variable) {
                return in_array($errno, $type) && (array_key_exists($variable, $_SESSION) || filter_input(INPUT_GET, $variable));
        }

        /**
         * Journalisation des erreurs et messages PHP.<br>
         * Ne sont affichées que si la page URL contient ?debug (&verbose pour plus de messages)
         * <br>Voir le fichier php_error.log pour les PHP FATAL ERROR (le processus s'arrete et journalise).
         * <br>debug permet de voir le fichier concerné
         * @see Index::__construct($selfScript)
         */
        function condor_error($errno, $errstr, $errfile, $errline) {
                $note = array(E_NOTICE, E_USER_NOTICE);
                $ignore = array(E_COMPILE_WARNING, E_CORE_WARNING, E_DEPRECATED, E_RECOVERABLE_ERROR, E_USER_DEPRECATED, E_USER_WARNING, E_WARNING);
                $errors = array(E_ERROR, E_USER_ERROR, E_ERROR, E_PARSE);
                $html = "<div class='error'><B>[" . strError($errno) . "] </B>" . $errstr . "<br>\n";
                if (array_key_exists("debug", $_SESSION) || filter_input(INPUT_GET, "debug")) {
                        $html .= "at line " . $errline . " of file " . pathFinder($errfile, filter_input(INPUT_SERVER, "DOCUMENT_ROOT")) . "\n"
                                . ", PHP " . PHP_VERSION . " (" . PHP_OS . ")<br>\n";
                }
                $html .= "<br></div>\n\n";
                if (in_array($errno, $errors)) {
                        while (ob_get_level() > 0) {
                                ob_end_clean();
                        }
                        echo $html;
                        exit;
                } else if (errValidType($errno, $note, "verbose") || errValidType($errno, $ignore, "warn") || (array_key_exists("debug", $_SESSION) || filter_input(INPUT_GET, "debug"))) {
                        // n'affiche que si une des conditions est valide :
                        // pour une notice et que verbose est actif
                        // pour une warning et que warn est actif
                        // si debug est actif
                        echo $html;
                }
        }

        set_error_handler("condor_error");

        function print_array_r($array, &$html = "") {
                $p = $html === "";
                if (is_array($array)) {
                        $html .= "<ul>";
                        foreach ($array as $k => $v) {
                                $html .= "<li>" . $k . " : ";
                                print_array_r($v, $html);
                                $html .= "</li>";
                        }
                        $html .= "</ul>";
                } else {
                        $html .= $array;
                }
                if ($p) {
                        echo $html;
                }
        }

        /**
         * index.php?debug affiche les E_ERROR et E_WARNING
         * index.php?debug&verbose affiche les E_NOTICE
         * enregistrement en session des parametres de journalisation
         * */
        function i_debug($texte = "about the error") {
                echo "***DEBUG***\n SCRIPT_NAME:" . filter_input(INPUT_SERVER, 'SCRIPT_NAME') . ": ";
                print_array_r($texte);
                echo "\n ***DEBUG***\n";
        }

        /**
         *  END         ERROR CHECKING 
         */
        /* fonction qui definit le chemin qui donnera acces au fichier destination depuis le fichier origine (p. ex.: PHP_SELF), attention: la racine de chaque fichier doit ?tre identique a l'autre.
         */

        function pathFinder($dest, $origine) {
                $path = "";
                // tokenization des url
                $origine_tokenized = explode("/", $origine);
                $dest_tokenized = explode("/", $dest);
                // recherche du token different
                for ($i = 0; ($i < count($origine_tokenized)) && ($i < count($dest_tokenized)); $i++) {
                        if ($origine_tokenized[$i] != $dest_tokenized[$i]) { /* tokens dissemblables (chemin non-?gaux)  difference detect?e ? l'index $i;
                          (1) calcul du nombre de tokens restant jusqu'a la fin de chaque chemin depuis l'index $i y compris.
                          (2) #path: s'il reste plus de tokens dans le chemin d'origine, c'est que le chemin de destination est repertoriellement et hierarchiquement plus haut: il faut remonter les repertoires avec des ../ prec?dant le nom du fichier de destination pour chaque token de trop depuis $i jusqu'? < count($origine_tokenized) - 1 (i.e. moins le token du nom du fichier d'origine)
                          #path: s'il reste plus de tokens dans le chemin de destination, c'est que le chemin de destination est repertoriellement et hierarchiquement plus bas: il faut entrer dans les repertoires; chaque token depuis $i moins le dernier (i.e. le nom du fichier) du chemin de destination doit pr?c?der le nom du fichier de destination
                          #path: s'il reste un nombre ?gal de token dans chacun des chemin origine et destination, c'est que le repertoire de chacun est identique. Le nom du fichier de destination ? atteindre n'est pas preced?.
                         * note : le dernier token = la destination (le fichier ou repertoire courant ./), donc omis.
                         */
                                // (1)
                                $origine_reste_rep = count($origine_tokenized) - $i;
                                $dest_reste_rep = count($dest_tokenized) - $i;

                                // (2)
                                if ($origine_reste_rep < $dest_reste_rep) {
                                        for ($j = $i; $j < count($dest_tokenized) - 1; $j++) {
                                                $path .= $dest_tokenized[$j] . "/";
                                        }
                                } elseif ($origine_reste_rep > $dest_reste_rep) {
                                        for ($j = $i; $j < count($origine_tokenized) - 1; $j++) {
                                                $path .= "../";
                                        }
                                } else {
                                        $path = "./";
                                }
                                break;
                        }//endif
                }//endfor
                $path .= $dest_tokenized[count($dest_tokenized) - 1];
                /* echo "return " . $path . "<br>"; */
                return $path; // ajoute le nom de fichier ou repertoire destination.
        }

        // fonction qui "remonte" de n dossiers dans un chemin type unix/url

        function cdup($path, $n) {
                for ($i = 0; $i < $n; $i++) {
                        $path = substr($path, 0, strrpos($path, '/'));
                }
                return $path;
        }

        // efface les caracteres de fin de chaine
        function stripEnd($string, $stringEnd = "/") {
                /* enleve le dernier slash '/' */
                return (substr($string, -1) === $stringEnd) ? stripEnd(substr($string, 0, -1), $stringEnd) : $string;
        }

        global $MENU;

        class Index {

                var $localizedStrings;
                var $sitemap;
                var $menu_ini;

                /* -----  definition de l'acces au repertoire racine du site pour les variables globales ---- 
                  recuperation des differents registres de fichiers include
                  une variable GET ?root=cheminVersHttpDocs pour debugger en local peut-être initialisée, Si et seulement SI :
                  une variable GET ?local pour utiliser constantes-local.properties initialisée avec les parametres locaux (test local).
                 * <br>
                 * une variable GET ?debug permet le debogage de toutes erreurs, y compris warning
                 * une variable GET ?warn permet le debogage des warnings
                 * variables $_SESSION [root, local];
                 *                  
                 */

                public function __construct($selfScript, $force = false) {
                        global $_instanceRegistre;
                        if (!isset($_instanceRegistre) || $force) {
                                $_instanceRegistre = 1;

                                /**
                                 * ERROR CHECKING enregistrement en session courante
                                 */
                                Index::registerGETSession(array("root" => stripEnd(filter_input(INPUT_GET, 'root')), "verbose", "local", "warn", "debug"));
                                $root = array_key_exists('root', $_SESSION) ? $_SESSION['root'] : "";
                                $cheminSite = pathFinder($root . "/e13/.", $selfScript) . "/";
                                $cheminHttpdocs = pathFinder($root . "/.", $selfScript) . "/";
                                $cheminInc = $cheminSite . "include/";
                                $cheminImg = $cheminSite . "images/";
                                $cheminAdmin = $cheminSite . "admin/";
                                /**
                                 * 
                                 *  GLOBALES des chemins relatifs et correspondant aux section du fichier sitemap.properties
                                 * 
                                 * */
                                $GLOBALS['root'] = $cheminHttpdocs;
                                $GLOBALS['e13'] = $cheminSite;
                                $GLOBALS["include"] = $cheminInc;
                                $GLOBALS["images"] = $cheminImg;
                                $GLOBALS["admin"] = $cheminAdmin;
                                $GLOBALS["etc"] = $cheminSite . "etc/";
                                $GLOBALS['locale'] = $GLOBALS["etc"] . 'locale/';
                                $GLOBALS['doc'] = $cheminSite . 'doc/';
                                $GLOBALS['blog'] = $cheminSite . 'blog/';
                                $GLOBALS['library'] = $cheminSite . 'dvd/';
                                $GLOBALS['shop'] = $cheminSite . 'shop/';
                        }
                        $this->menu_ini = $this->parseBundle($GLOBALS["etc"], "menu");
                        $this->sitemap = $this->parseBundle($GLOBALS["etc"], "sitemap");
                        $this->creerSitemapGlobals($this->sitemap);
                        $this->localizedStrings = $this->parseBundle($GLOBALS['locale'], "content-lang");
                }

                /**
                 * enregistre en session si specifie par GET 
                 */
                private static function registerGETSession($valuesGET = array()) {
                        /** les parametres de sessions pour le debogage */
                        foreach ($valuesGET as $value) {
                                $object = filter_input(INPUT_GET, $value);
                                if ($object) {
                                        Index::registerSession($value, $object ? $object : TRUE);
                                }
                        }
                }

                private function creerSitemapGlobals($bundle) {
                        foreach ($bundle as $section => $page) {
                                foreach ($page as $nom => $nomFichier) {
                                        $GLOBALS[$section . "__" . $nom] = $GLOBALS[$section] . $nomFichier;
                                }
                        }
                }

                /**
                 * enregistre une variable en session. 
                 */
                private static function registerSession($variableName, $value) {
                        /** PHP < 5.4 if (!session_is_registered($variableName)) {
                          session_register($variableName);
                          } */
                        $_SESSION[$variableName] = $value;
                        return $value;
                }

                public function getLanguage() {
                        return getPrimaryLanguage();
                }

                /**
                 * @param path $folder folder nom de repertoire se terminant avec un slash '/'
                 */
                function getLocalizedFile($folder, $bundleName, $fileExt = ".properties") {
                        $loc = getPrimaryLanguage();
                        $f = $folder . $bundleName . "_" . $loc . $fileExt;
                        if (!(file_exists($f))) {
                                $f = $folder . $bundleName . $fileExt;
                        }
                        return $f;
                }

                /**
                 * charge un fichier Bundle (parse_ini_file()).
                 * @param string $bundleName nom du fichier précedant l'extension
                 * @param string $fileExt extension du fichier (p.ex. '.ini'), par défaut = '.properties'
                 * @param path $folder folder pathname that must end with the path separator char
                 * @return table retourne un tableau deux entrées [section][clé] = chaine de caracteres
                 * 
                 */
                function parseBundle($folder, $bundleName, $fileExt = ".properties") {
                        $filename = $folder . $bundleName . $fileExt;
                        $localized = $this->getLocalizedFile($folder, $bundleName, $fileExt);
                        $bundle = parse_ini_file($filename, true);
                        if ($filename !== $localized) {
                                $bundle_localized = parse_ini_file($localized, true);
                                if (!$bundle_localized) {
                                        trigger_error($localized . " was not parsed.", E_USER_WARNING);
                                }
                                $bundle = array_replace_recursive($bundle, $bundle_localized);
                        }
                        return $bundle;
                }

                /** recherche de chaine de caracter dans le fichier locale/content-lang.properties (detection de langue automatique 
                 * selon la platforme client)
                 */
                function lang($key, $section = "default") {
                        if (is_array($key)) {
                                $result = array();
                                foreach ($key as $k) {
                                        $result[] = $this->lang($k, $section);
                                }
                                return $result;
                        } else {
                                if (!array_key_exists($section, $this->localizedStrings)) {
                                        i_debug("unknown section " . $section . " in content_lang.");
                                } else if (!array_key_exists($key, $this->localizedStrings[$section])) {
                                        i_debug("undefined key in content_lang [" . $section . "] for " . $key);
                                }
                                return $this->localizedStrings[$section][$key];
                        }
                }

        }

}
?>
