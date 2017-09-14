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

        /** remplace filter_input(INPUT_SESSION,$var)
         * retourne la variable enregistree si elle existe ou NULL
         */
        function filter_session($var) {
                if (session_status() == PHP_SESSION_ACTIVE && array_key_exists($var, $_SESSION)) {
                        return $_SESSION[$var];
                } else {
                        return NULL;
                }
        }

        /** ------  REGISTRES DES ERREURS COURANTES * */
        /** error connexion SQL.class */
        define("ERROR_DB_CONNECT", 0x01);
        /** image : parametres incorrects */
        define("ERROR_IMG_PARAM", 0x02);
        /** index : pas d'objet courant */
        define("ERROR_INDEX_NULL", 0x04);

        /** ------ Debugger -------- */
        define("DEBUG_STANDARD", 0x1);
        define("DEBUG_VERBOSE", 0x2);
        define("DEBUG_LOCAL", 0x4);

        function print_array_r($array, &$i_html = "") {
                $p = $i_html === "";
                if (is_array($array)) {
                        $i_html .= "<ul>";
                        foreach ($array as $k => $v) {
                                $i_html .= "<li>" . $k . " : ";
                                print_array_r($v, $i_html);
                                $i_html .= "</li>";
                        }
                        $i_html .= "</ul>";
                } else if (is_object($array)) {
                        $i_html .= get_class($array);
                } else {
                        $i_html .= $array;
                }
                if ($p) {
                        echo $i_html;
                }
        }

        /** teste parametre local != 0 */
        function i_isVarGETSess($var) {
                $d = intval(filter_input(INPUT_GET, $var));
                $s = intval(filter_session($var));
                return ($d && $d != 0) || ($s && $s != 0);
        }

        function i_isFlagsNot($var) {
                if (is_int($var)) {
                        $v = $var;
                        $var = array();
                        if (($v & DEBUG_LOCAL) != 0 && !i_isVarGETSess("local")) {
                                return false;
                        }
                        if (($v & DEBUG_STANDARD) != 0 && !i_isVarGETSess("debug")) {
                                return false;
                        }
                        if (($v & DEBUG_VERBOSE) != 0 && !i_isVarGETSess("verbose")) {
                                return false;
                        }
                        return true;
                }
                return false;
        }

        /** teste parametre debug != 0 */
        function i_isdebug() {
                return i_isVarGETSess("debug");
        }

        /** teste parametre local != 0 */
        function i_islocal() {
                return i_isVarGETSess("local");
        }

        /**
         * index.php?debug affiche les E_ERROR et E_WARNING
         * index.php?debug&verbose affiche les E_NOTICE
         * enregistrement en session des parametres de journalisation
         * */
        function i_debug($texte = "about the error", $d = DEBUG_STANDARD) {
                if (!i_isFlagsNot($d)) {
                        return;
                }
                echo "\n<b>" . filter_input(INPUT_SERVER, 'SCRIPT_NAME') . ":/></b> ";
                print_array_r($texte);
                echo "\n<br>*********\n<br>";
        }

        if (!headers_sent()) {
                session_start();
        }
        /** TIMEZONE http://php.net/manual/fr/timezones.php */
        date_default_timezone_set("Europe/Paris");

        require "php_module_locale.inc.php";

        /* controle de l'existence d'une session */
        if (session_status() != PHP_SESSION_ACTIVE) {
                i_debug("phpSession: off " . session_status() == PHP_SESSION_DISABLED ? "(DISABLED)" : "");
        } else {
                if (i_isdebug()) {
                        i_debug("phpSession: start\n"
                                . "language " . getPrimaryLanguage() . "\n", DEBUG_STANDARD | DEBUG_VERBOSE);
                }
                if (i_islocal()) {
                        i_debug("Local Configuration Enabled\n", DEBUG_STANDARD | DEBUG_VERBOSE);
                }
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
                $s = session_status() == PHP_SESSION_ACTIVE && array_key_exists($variable, $_SESSION);
                return in_array($errno, $type) && ($s || filter_input(INPUT_GET, $variable));
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
                if (i_isdebug()) {
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
                } else if (errValidType($errno, $note, "verbose") || errValidType($errno, $ignore, "warn") || (i_isdebug())) {
                        // n'affiche que si une des conditions est valide :
                        // pour une notice et que verbose est actif
                        // pour une warning et que warn est actif
                        // si debug est actif
                        echo $html;
                }
        }

        set_error_handler("condor_error");

        /**
         *  END         ERROR CHECKING 
         */
        /* fonction qui definit le chemin qui donnera acces au fichier destination depuis le fichier origine (p. ex.: PHP_SELF), attention: si les parametres sont de meme nature (Dossiers ou Fichiers)
         */

        function pathFinder($dest, $origine, $cheminsDeFichiers = false, $pathSeparator = "/") {
                $path = "";

                // tokenization des url
                $origine_tokenized = explode($pathSeparator, $cheminsDeFichiers ? dirname($origine) : $origine);
                $dest_tokenized = explode($pathSeparator, $cheminsDeFichiers ? dirname($dest) : $dest);

                // recherche du token different
                $origine_reste_rep = count($origine_tokenized);
                $dest_reste_rep = count($dest_tokenized);
                for ($i = 0; ($i < count($origine_tokenized)) && ($i < count($dest_tokenized)); $i++) {
                        if ($origine_tokenized[$i] !== $dest_tokenized[$i]) {
                                // (1)
                                $origine_reste_rep -= $i;
                                $dest_reste_rep -= $i;
                                break;
                        }
                }
                // concatenation du chemin d'origine
                $path .= cdUp($cheminsDeFichiers ? dirname($origine) : stripEnd($origine), $origine_reste_rep);
                // enleve excedent et ajoute un pathseparator
                $path = stripEnd($path) . $pathSeparator;
                // chemin de destination fichier ou dossier
                $path .= implode($pathSeparator, array_slice($dest_tokenized, -$dest_reste_rep, $dest_reste_rep));
                if ($cheminsDeFichiers) {
                        $path .= basename($dest);
                } else {
                        $path .= $pathSeparator;
                }
                return $path; // ajoute le nom de fichier ou repertoire destination.
        }

        // fonction qui "remonte" de n dossiers dans un chemin type unix/url

        function cdUp($path, $n) {
                for ($i = 0; $i < $n; $i++) {
                        $path = dirname($path);
                }
                return $path;
        }

        // efface les caracteres de fin de chaine
        function stripEnd($string, $stringEnd = "/") {
                /* recursion ! enleve les dernier slashes '/' */
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

                public function __construct($selfScript, $force = false, $initRootPath = "/") {
                        global $_instanceRegistre;
                        /**
                         * ERROR CHECKING enregistrement en session courante
                         */
                        Index::registerGETSession(array("verbose", "local", "warn", "debug"));
                        if (!isset($_instanceRegistre) || $force) {
                                $_instanceRegistre = 1;

                                $selfScript = str_replace("//", "/", $selfScript);
                                /* */
                                i_debug($selfScript, DEBUG_STANDARD | DEBUG_VERBOSE);
                                $cheminSite = pathFinder($initRootPath . "e13/", dirname($selfScript));
                                $cheminHttpdocs = pathFinder($initRootPath . "", dirname($selfScript));
                                $cheminInc = $cheminSite . "include/";
                                $cheminImg = $cheminSite . "images/";
                                $cheminAdmin = $cheminSite . "admin/";
                                /**
                                 * 
                                 *  GLOBALS des chemins reels et relatives au fichier sitemap.properties 
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
                        $this->creerSitemapGlobals($this->parseBundle($GLOBALS["etc"], "sitemap"), $this->sitemap);
                        $this->localizedStrings = $this->parseBundle($GLOBALS['locale'], "content-lang");
                }

                /**
                 * enregistre en session si specifie par GET 
                 */
                private static function registerGETSession($valuesGET = array()) {
                        /** les parametres de sessions pour le debogage */
                        foreach ($valuesGET as $param) {
                                $value = filter_input(INPUT_GET, $param);
                                if ($value) {
                                        Index::registerSession($param, $value);
                                }
                        }
                }

                private function creerSitemapGlobals($bundle, &$out = array()) {
                        foreach ($bundle as $section => $pages) {
                                if (!is_array($pages)) {
                                        continue;
                                }
                                foreach ($pages as $nom => $nomFichier) {
                                        if ($section !== "root") {
                                                $GLOBALS[$section . "__" . $nom] = $GLOBALS[$section] . $nomFichier;
                                                $out[$section . "__" . $nom] = pathFinder($bundle["root"][$section], $bundle["root"]['index']) . $nomFichier;
                                        } else {
                                                //$GLOBALS[$nom] = $nomFichier;
                                                $out[$nom] = $nomFichier;
                                        }
                                }
                        }
                }

                /**
                 * enregistre une variable en session. 
                 */
                private static function registerSession($param, $value) {
                        /** PHP < 5.4 if (!session_is_registered($variableName)) {
                          session_register($variableName);
                          } */
                        if (session_status() == PHP_SESSION_ACTIVE) {
                                $_SESSION[$param] = $value;
                        }
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
