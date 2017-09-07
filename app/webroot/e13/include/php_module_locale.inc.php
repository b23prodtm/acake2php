<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

global $Module_Locale;
if (!isset($GLOBALS['Module_Locale'])) {

        $Module_Locale = 1;

        // gestion des langues
        define("FR", "fr");
        define("DE", "de");
        define("EN", "en");

        global $LANGS;
        $LANGS = array(EN, FR, DE);

        /**
         * retourne une langue correspondant au systeme client si elle est supportee
         */
        function getPrimaryLanguage() {
                $lang = substr(_getDefaultLanguage(), 0, 2);
                if (!in_array($lang, $GLOBALS["LANGS"], TRUE)) {
                        $lang = EN;
                }
                /** la session peut comporter un parametre lang */
                if (array_key_exists('lang', $_SESSION)) {
                        $lang = $_SESSION['lang'];
                }
                /** le parametre est specifié dans l'url */
                if (filter_input(INPUT_GET, 'lang')) {
                        $lang = filter_input(INPUT_GET, 'lang');
                }
                return $lang;
        }

        /* ########################################################
          # Copyright © 2008 Darrin Yeager                        #
          # http://www.dyeager.org/                               #
          # Licensed under BSD license.                           #
          #   http://www.dyeager.org/downloads/license-bsd.txt    #
          ######################################################## */

        /**
          retourne la langue du systeme client tel que definie par http_accept_language
         *          
         */
        function _getDefaultLanguage() {
                if (filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE')) {
                        return _parseDefaultLanguage(filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'));
                } else {
                        return _parseDefaultLanguage(NULL);
                }
        }

        function _parseDefaultLanguage($http_accept, $deflang = "en") {
                if (isset($http_accept) && strlen($http_accept) > 1) {
                        # Split possible languages into array
                        $x = explode(",", $http_accept);
                        foreach ($x as $val) {
                                #check for q-value and create associative array. No q-value means 1 by rule
                                if (preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i", $val, $matches)) {
                                        $lang[$matches[1]] = (float) $matches[2];
                                } else {
                                        $lang[$val] = 1.0;
                                }
                        }

                        #return default language (highest q-value)
                        $qval = 0.0;
                        foreach ($lang as $key => $value) {
                                if ($value > $qval) {
                                        $qval = (float) $value;
                                        $deflang = $key;
                                }
                        }
                }
                return strtolower($deflang);
        }

}
?>
