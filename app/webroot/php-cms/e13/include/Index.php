<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	// !!!:brunorakotoarimanana:20041121
  @filename	php_index.inc.php
 *
 *
  =========================================================
  configuration SITEMAP.PROPERTIES _ MENU.PROPERTIES _ CONTENT-LANG.PROPERTIES _ include/Index.php#__construct
  =========================================================
 *
 * UNE INSTANCE DE CET INDEX DOIT ETRE CREE POUR CHAQUE PAGE PHP AFIN DE POUVOIR UTILISER LES CHEMINS RELATIFS ET VARIABLES DE CONFIGURATIONS
 *
 *
 */
if (!isset($_ENV['registreFichiers'])) {


        $_ENV['registreFichiers'] = 1;
        /** separator de fichiers */
        define("ds", DIRECTORY_SEPARATOR);

        /** remplace filter_input(INPUT_SESSION,$var)
         * @return la variable enregistree si elle existe ou NULL
         */
        function filter_session($var) {
                if (session_status() == PHP_SESSION_ACTIVE && isset($_SESSION) && array_key_exists($var, $_SESSION)) {
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
        define("DEBUG_ERROR", 0x0);
        define("DEBUG_STANDARD", 0x1);
        define("DEBUG_VERBOSE", 0x2);
        define("DEBUG_TEST", 0x4);
        define("DEBUG_WARNING", 0x8);
        define("DEBUG_DUMP", 0x10);
        /**
        * encodage htmlspecialchars CRUD
        */
        define("ENCODE_HTML", ENT_IGNORE | ENT_COMPAT | ENT_HTML5);
        define("ENCODE_CS", 'UTF-8');


        /**
            *    @return sttring un extrait de $array en list ou si objet get_class
         *          */
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

        /** valide les parametres */
        function i_isVarGETSess($var) {
                $d = intval(!array_key_exists($var, $_GET) ? false : $_GET[$var]);
                $s = intval(filter_session($var));
                /** efface la session si valeur GET*/
                return !array_key_exists($var, $_GET) ? $d != 0 || $s != 0 : $d != 0;
        }

        /**
         * @param $var int TOUS les DEBUG_* sp�cifi�s en argument sont activ�s en session ("?debug, ?warn, ?test, ...") la methode retourne "true"
         * @return  dans tous les autres cas false.
         */
        function i_isFlags($v) {
                if (is_int($v)) {
                        if (($v & DEBUG_TEST) != 0 && !i_isVarGETSess("test")) {
                                return false;
                        }
                        if (($v & DEBUG_STANDARD) != 0 && !i_isVarGETSess("debug")) {
                                return false;
                        }
                        if (($v & DEBUG_VERBOSE) != 0 && !i_isVarGETSess("verbose")) {
                                return false;
                        }
                        if (($v & DEBUG_WARNING) != 0 && !i_isVarGETSess("warn")) {
                                return false;
                        }
                        if (($v & DEBUG_DUMP) != 0 && !i_isVarGETSess("dump")) {
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

        /** == i_isTest() */
        function i_islocal() {
                return i_isTest();
        }
        /** teste parametre test != 0 */
        function i_isTest() {
                return i_isVarGETSess("test");
        }

        /**
         * index.php?debug affiche les E_ERROR
         * index.php?warn affiche les E_WARNING
         * index.php?verbose affiche les E_NOTICE
         * enregistrement en session des parametres de journalisation
         * */
        function i_debug($texte = "about the error", $d = DEBUG_STANDARD) {
                if (!i_isFlags($d)) {
                        return;
                }
                echo "\n<b>" . filter_input(INPUT_SERVER, 'SCRIPT_NAME') . ":/></b> ";
                print_array_r($texte);
                echo "\n<br>*********\n<br>";
        }

        /** check sessions not started and start if possible (headers not sent)*/
        if (!headers_sent() && session_id() === "") {
                session_start();
        }
        /** TIMEZONE http://php.net/manual/fr/timezones.php */
        date_default_timezone_set("Europe/Paris");

        include "php_module_locale.inc.php";

        /* controle de l'existence d'une session */
        if (!isset($_SESSION)) {
                i_debug("phpSession: off " . session_status() == PHP_SESSION_DISABLED ? "(DISABLED)" : "");
        } else {
                if (i_isdebug()) {
                        i_debug("phpSession: start\n"
                                . "language " . getPrimaryLanguage() . "\n", DEBUG_STANDARD | DEBUG_VERBOSE);
                }
                if (i_isTest()) {
                        i_debug("Local Configuration Enabled\n", DEBUG_STANDARD | DEBUG_VERBOSE);
                }
        }

        /**
         *  BEGIN         ERROR CHECKING
         * */
        /*
         * @see condor_error
         * Registre constructeur de registre courant n�cessaire sur chaque script .php
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
         * validation en sortie du type d'erreur rencontr�
         *
         * @see Index::__construct($selfScript) */
        function errValidType($errno, array $type, $variable) {
                $s = filter_session($variable);
                return in_array($errno, $type) && ($s || filter_input(INPUT_GET, $variable));
        }

        /**
         * Journalisation des erreurs et messages PHP.<br>
         * Ne sont affich�es que si la page URL contient ?debug (&verbose pour plus de messages)
         * <br>Voir le fichier php_error.log pour les PHP FATAL ERROR (le processus s'arrete et journalise).
         * <br>debug permet de voir le fichier concern�
         * @see Index::__construct($selfScript)
         */
        function condor_error($errno, $errstr, $errfile, $errline) {
                $note = array(E_NOTICE, E_USER_NOTICE);
                $ignore = array(E_COMPILE_WARNING, E_CORE_WARNING, E_DEPRECATED, E_RECOVERABLE_ERROR, E_USER_DEPRECATED, E_USER_WARNING, E_WARNING);
                $errors = array(E_ERROR, E_USER_ERROR, E_ERROR, E_PARSE);
                $html = "<div class='error'><B>[" . strError($errno) . "] </B>" . $errstr . "<br>\n";
                if (i_isdebug()) {
                        $html .= "at line " . $errline . " of file " . $errfile . "\n"
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

        /** "monte" par pre-concatenation '..' de n dossiers dans un chemin type unix/url
        * @param int $i recursion init = 0
        * @param int $n recursion count = n
        */
        function cdUp($path, $i, $n, $ds = ds) {
                if($i < $n) {
                        return cdUp(".." . $ds . $path, $i + 1, $n, $ds);
                } else {
                        return $path;
                }
        }

        /**
         * efface tous les caracteres barres obliques de fin de chaine
         */
        function noSlashEnd($string, $ds = ds, $pattern = '/^(.*)[\/\\\\]+$/') {
                return preg_replace($pattern, "$1", $string);
        }
        /**
          * remplace les doubles barres par une simple du type #ds
          * @param $pattern expression reguliere '/[\/\\\\]{2,}/' capture pour barres obliques droit ou gauche
        */
        function noDoubleSlashes($path, $ds = ds, $pattern = '/([\/\\\\]{2,})/') {
                return preg_replace($pattern, $ds, $path);
        }

        class Index {

                var $localizedStrings;
                /** plan du site en <chemin> relatives "<http://hostname/><chemin>" */
                var $sitemap;
                var $menu_references;
                var $view;
                var $footprint;
                /** registre chemins de fichiers */
                var $r;
                protected $sitemap_ini;
                protected $menu_ini;
                protected $constantes_ini;
                protected static $_instance;

                /**  definition de l'acces au repertoire racine du site pour le sitemap et registre statique, chemins relatifs ----
                  * @param View vue courante
                  * @param string $selfScript __FILE__ pour designer le script appelant
                  * @param string $cmsRoot reinitialise le registre vers le dossier de e13/ (www_root . ds . php-cms), chemins absolus (par ex. dirname(dirname(__FIlE__)))
                  * @param bool $relative registre en chemins absolus par defaut, chemins relatifs TRUE. Recherche le dossier $cmsRoot depuis $selfScript.
                 */

                public function __construct(View $view=NULL, $selfScript = __FILE__, $relative = false, $cmsRoot = __DIR__ . ds . '..' . ds . '..', $ds = ds, $force = false) {
                        /**
                         * ERROR CHECKING enregistrement en session courante
                         */
                        self::registerGETSession(array("verbose", "test", "warn", "debug", "dump"));
                        $this->view = $view;
                        $this->footprint = self::gFootprint($selfScript, time());
                        if (!isset(self::$_instance)) {
                                self::$_instance = $this;
                                $force = true;
                        }
                        if($force || $relative) {
                                noDoubleSlashes($selfScript, $ds);
                                noDoubleSlashes($cmsRoot, $ds);
                                /*
                                  chemin vers le repertoire root du site
                                */
                                $cheminHttpdocs = dirname($cmsRoot) . $ds;
                                if($relative) $cheminHttpdocs = self::pathFinder($cheminHttpdocs, dirname($selfScript) . $ds, $ds);
                                /*
                                  chemin vers le repertoire du site
                                */
                                $cheminSite = $cmsRoot . $ds . "e13" . $ds;
                                if($relative) $cheminSite = self::pathFinder($cheminSite, dirname($selfScript) . $ds, $ds);
                                /**
                                 *
                                 *  GLOBALS des dossiers relatifs au [sections] du fichier sitemap.properties
                                 *
                                 * */
                                $selfDir = $relative ? dirname($selfScript) . $ds: "";
                                $this->r['default'] = $cheminHttpdocs;
                                $this->r['index'] = $cheminSite . "index" . $ds;
                                $this->r['e13'] = $cheminSite;
                                $this->r['activites'] = $cheminSite;
                                $this->r['users'] = $cheminSite . "users" . $ds;
                                $this->r['contactus'] = $cheminSite . "contactus" . $ds;
                                $this->r['include'] = $cheminSite . "include" . $ds;
                                $this->r['images'] = $cheminSite . "images" . $ds;
                                $this->r['admin'] = $cheminSite . "admin" . $ds;
                                $this->r['etc'] = $cheminSite . 'etc' . $ds;
                                $this->r['locale'] = $this->r['etc'] . 'locale' . $ds;
                                $this->r['doc'] = $cheminSite . 'doc' . $ds;
                                $this->r['blog'] = $cheminSite . 'blog' . $ds;
                                $this->r['library'] = $cheminSite . 'dvd' . $ds;
                                $this->r['shop'] = $cheminSite . 'shop' . $ds;
                                $this->r['infos'] = $cheminSite;
                                $this->r['cat'] = $cheminSite;
                                $this->menu_ini = self::parseBundle($selfDir . $this->r['etc'], "menu");
                                $this->sitemap_ini = self::parseBundle($selfDir . $this->r['etc'], "sitemap");
                                $this->localizedStrings = self::parseBundle($selfDir . $this->r['locale'], "content-lang");
                                $this->constantes_ini = self::parseBundle($selfDir . $this->r["etc"], "constantes");
                                foreach ($this->sitemap_ini as $section => $pages) {
                                        if (!is_array($pages)) {
                                                break;
                                        }
                                        foreach ($pages as $nom => $nomFichier) {
                                                $this->creerSitemap($section, $nom, $nomFichier, $this->sitemap, $ds);
                                        }
                                }
                                include_once "php_constantes.inc.php";
                                include_once "Menu.php";
                                $this->menu_references = Menu::creerMenuGlobals($this->sitemap, $this->menu_ini);
                        } else {
                          /* shallow copy */
                          $this->r = self::$_instance->r;
                          $this->sitemap_ini = self::$_instance->sitemap_ini;
                          $this->menu_ini = self::$_instance->menu_ini;
                          $this->localizedStrings = self::$_instance->localizedStrings;
                          $this->constantes_ini = self::$_instance->constantes_ini;
                          $this->sitemap = self::$_instance->sitemap;
                        }
                        if(i_isFlags(DEBUG_DUMP) && self::$_instance->footprint === $this->footprint) {
                          print_r($selfScript);
                          print_array_r(self::$_instance->r);
                          print_r("menu_ini");
                          print_array_r(self::$_instance->menu_ini);
                          print_r("sitemap_ini");
                          print_array_r(self::$_instance->sitemap_ini);
                        }
                }

                /** concatenation des chemins d'inclusion en fonction de la [section] et nom=nomFichier */
                private function creerSitemap($section, $nom, $nomFichier, &$out, $uds = '/') {
                        if(!array_key_exists($section, $this->r)) throw new Exception("ERROR : Undefined Index " . $section);
                        $this->r[$section . "__" . $nom] = $this->r[$section] . $nomFichier;
                        if ($section !== "default") {
                                $out[$section . "__" . $nom] = noSlashEnd($this->sitemap_ini['default']['index']) . $uds . noSlashEnd($this->sitemap_ini['default'][$section]) . $uds . $nomFichier;
                        } else {
                                $out[$section . "__" . $nom] = noSlashEnd($this->sitemap_ini['default']['index']) . $uds . $nomFichier;
                        }
                }

                public function equals(Index $index) {
                        return $index != NULL && $this->footprint === $index->footprint;
                }

                /** recherche de chaine de caracteres dans le fichier locale/content-lang.properties (detection de langue automatique
                 * selon la platforme client)
                 * @return mixed si $key est array() un tableau simple ou une chaine non-vide
                 */
                public function lang($key, $section = "default") {
                        if (is_array($key)) {
                                $result = array();
                                foreach ($key as $k) {
                                        $result[] = $this->lang($k, $section);
                                }
                                return $result;
                        } else {
                                if (!array_key_exists($section, $this->localizedStrings)) {
                                        i_debug("Unknown section " . $section . " in content_lang for " . $key);
                                } else if (!array_key_exists($key, $this->localizedStrings[$section])) {
                                        i_debug("Undefined key in content_lang [" . $section . "] for " . $key);
                                }
                                return $this->localizedStrings[$section][$key];
                        }
                }

                /**
                * @param string $dest, $fichier chemins reels
                * @return mixed un code erreur ou Boolean
                */
                private static function bothFilesOrDirs($dest, $origine) {
                  $fichier = file_exists($dest) ? (
                    file_exists($origine) ? (
                      is_dir($dest) ? (
                        is_file($origine) ? 0x21:0x22
                      ):(
                        is_file($origine) ? 0x11:0x12
                      )
                    ):(
                      is_dir($dest) ? 0x20:0x10
                    )
                  ):(
                    file_exists($origine) ? 0x03:0x00
                  );
                  if (!($fichier & 0x0F))
                          i_debug("ERROR: $origine not found.");
                  if (!(0xF0 & $fichier))
                          i_debug("ERROR : $dest not found.");
                  if (($fichier & 0xF0) >> 4 == ($fichier & 0x0F))
                          return is_file($dest);
                  else
                          i_debug("ERROR : paths $origine and $dest must be both files or directories.", DEBUG_ERROR);
                  return $fichier;
                }

                /* definit le chemin relatif qui donnera acces au fichier destination depuis le fichier origine (p. ex.: PHP_SELF),
                * attention: les parametres doivent rester de meme nature (Dossiers ou Fichiers)
                * @param string $fichier les chemins a comparer sont des fichiers
                * @return retourne chemin relatif (../) ou absolu en cas d'erreur (DEBUG_STANDARD)
                */
                private static function pathFinder($dest, $origine, $ds = ds) {
                       $path = "";
                       $fichier = self::bothFilesOrDirs($dest, $origine);
                       if(is_int($fichier)) return $dest;
                       // tokenization des url de dossiers,
                       $origine_tokenized = explode($ds, $fichier ? dirname($origine) : noSlashEnd($origine));
                       $dest_tokenized = explode($ds, $fichier ? dirname($dest) : noSlashEnd($dest));
                       // recherche du token different
                       $origine_reste_rep = count($origine_tokenized);
                       $dest_reste_rep = count($dest_tokenized);
                       $inter = array_intersect_assoc($origine_tokenized, $dest_tokenized);
                       $origine_reste_rep -= count($inter);
                       $dest_reste_rep -= count($inter);
                       // chemin de destination
                       $path .= implode($ds, array_slice($dest_tokenized, -$dest_reste_rep, $dest_reste_rep));
                       // monte en relatif vers le chemin de destination
                       $path = cdUp($path, 0, $origine_reste_rep, $ds);
                       // ajoute le nom de fichier et "vide" pour les chemins du dossier courant
                       if ($fichier) {
                                if($path === "") $path = basename($dest);
                                else $path .= $ds . basename($dest);
                       } else {
                                if($path !== "") $path .= $ds;
                       }
                       return $path; // ajoute le nom de fichier ou repertoire destination.
                }

                /**
                 * enregistre en session si specifie par GET
                 */
                private static function registerGETSession($valuesGET = array()) {
                        /** les parametres de sessions pour le debogage */
                        foreach ($valuesGET as $param) {
                                $value = filter_input(INPUT_GET, $param);
                                if ($value) {
                                        self::registerSession($param, $value);
                                }
                        }
                }

                /**
                 * enregistre une variable en session.
                 */
                private static function registerSession($param, $value) {
                        /** PHP < 5.4 if (!session_is_registered($variableName)) {
                          * session_register($variableName);
                          * } */
                        if (isset($_SESSION)) {
                                $_SESSION[$param] = $value;
                        }
                        return $value;
                }

                public static function gFootprint($selfScript, $salt) {
                    return crypt(md5($selfScript), $salt);
                }

                /**
                 * @param path $folder folder nom de repertoire se terminant avec un slash '/'
                 */
                public static function getLocalizedFile($folder, $iniName, $fileExt = ".properties") {
                        $loc = getPrimaryLanguage();
                        $f = $folder . $iniName . "_" . $loc . $fileExt;
                        if (!(file_exists($f))) {
                                $f = $folder . $iniName . $fileExt;
                        }
                        return $f;
                }

                /**
                 * charge un fichier Bundle (parse_ini_file()).
                 * @param string $iniName nom du fichier pr�cedant l'extension
                 * @param string $fileExt extension du fichier (p.ex. '.ini'), par d�faut = '.properties'
                 * @param path $folder folder pathname that must end with the path separator char
                 * @return table retourne un tableau deux entr�es [section][cl�] = chaine de caracteres
                 *
                 */
                public static function parseBundle($folder, $iniName, $fileExt = ".properties") {
                        $filename = $folder . $iniName . $fileExt;
                        $localized = self::getLocalizedFile($folder, $iniName, $fileExt);
                        $ini = parse_ini_file($filename, true);
                        if ($filename !== $localized) {
                                $ini_localized = parse_ini_file($localized, true);
                                if (!$ini_localized) {
                                        trigger_error($localized . " was not parsed.", E_USER_WARNING);
                                }
                                $ini = array_replace_recursive($ini, $ini_localized);
                        }
                        return $ini;
                }

                public static function getLanguage() {
                        return getPrimaryLanguage();
                }
        }

}
?>
