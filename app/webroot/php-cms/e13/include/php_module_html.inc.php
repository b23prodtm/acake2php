<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:42:56 CEST 2004 @613 /Internet Time/
  @filename	php_module_html.inc
 */

if (!isset($_ENV['ModuleHTML'])) {

        $_ENV['ModuleHTML'] = 1;

        /**
         * @param string $separateur1 entre chaque paire de cle/valeur
         * @param string $separateur2 entre une cle et sa valeur
         * @return string la conversion du tableau en separant les elements cle-valeur
         */
        function indexedArray_toString($options, $separateur1 = " ", $separateur2 = "=", $strings = TRUE) {
                if ($strings) {
                        $s = "\"";
                } else {
                        $s = "";
                }
                $o = "";
                $sep = "";
                while (list ($key, $value) = each($options)) {
                        if ($value != -1) {
                                $o .= $sep . $key . $separateur2 . $s . htmlspecialchars($value) . $s;
                                $sep = $separateur1;
                        }
                }
                return $o;
        }

        /**
         * TRADUCTION des options de style d'une balise HTML. placer le tableau d'options en attributs
         * @param array $options avec un index explicite ("p.ex. "html", "css", "javascript", ou autre attributs)
         *  */
        function optionsArrayToHTML($options, $toString = FALSE) {
                /* valeurs de retour par defaut */
                $ret = array("html" => "",
                    "css" => "",
                    "javascript" => "", "class" => "");
                if (!is_array($options) || count($options) == 0) {
                        // goto return
                        $ret = $toString ? array() : $ret;
                } else {
                        foreach (array_change_key_case($options) as $key => $value) {
                                switch ($key) {
                                        case "html":
                                                $ret["html"] = indexedArray_toString($value, " ", "=");
                                                break;
                                        case "css":
                                                $ret["css"] = "style = \"" . indexedArray_toString($value, ";", ":", FALSE) . "\"";
                                                break;
                                        case "javascript":
                                                $ret["javascript"] = indexedArray_toString($value, " ", "=");
                                                break;
                                        default:
                                                /* par defaut les attributs de balise sont en lower_case */
                                                if ($value !== "" && $value !== NULL) {
                                                        $ret[$key] = $key . "=\"" . htmlspecialchars($value) . "\"";
                                                } else {
                                                        $ret[$key] = "";
                                                }
                                                break;
                                }
                        }
                }
                return $toString ? " " . implode(" ", $ret) : $ret;
        }

        /* module de production de balises HTML */

        function HTML_lien($url, $libelle, $options = array(), $cible = "_self") {
                $o = optionsArrayToHTML($options, true);
                return "<a href=\"$url\" target='$cible' " . $o . ">$libelle</a>\n";
        }

        /**
         * @param array $options sont autorises les options
        * @see optionsArrayToHTML()
         */
        function HTML_image($origine, $options = array()) {

                $o = optionsArrayToHTML($options);
                $chaine = "";
                if ($o["class"] != "") {
                        $chaine = "<div " . $o["class"] . ">";
                }
                $chaine .= "<img src=\"" . htmlspecialchars(htmlspecialchars_decode($origine)) . "\" " . implode(" ", array($o["html"],
                        $o["css"],
                        $o["javascript"])) .">\n";
                if ($o["class"] != "") {
                        $chaine .= "</div>\n";
                }
                return $chaine;
        }

        // bouton ouvrant une page

        function HTML_boutonLoad($page, $label) {
                $chaine_bouton = "<input type=\"button\" name=\"Bouton\" value=\"$label\" onClick=\"location.href='$page'\">";
                return $chaine_bouton;
        }

        // LISTES
        function HTML_listeDebut($modenum = FALSE) {
                return HTML_listeDebut_div($modenum);
        }

        function HTML_listeDebut_div($modenum = FALSE, $options = array()) {
                $o = optionsArrayToHTML($options, true);
                $listedebut = "<div " . $o . ">";
                if ($modenum)
                        $listedebut .= "<ol>\n";
                else
                        $listedebut .= "<ul>\n";
                return $listedebut;
        }

        function HTML_listeDebut_num($options = array()) {
                return HTML_listeDebut(TRUE, $options);
        }

        function HTML_listeFin($modenum = FALSE) {
                if ($modenum)
                        return "</ol></div>\n";
                else
                        return "</ul></div>\n";
        }

        function HTML_listeFin_num() {
                return HTML_listeFin(TRUE);
        }

        function HTML_listeElement($contenu) {
                $html = "";
                if (is_array($contenu)) {
                        $nbElements = count($contenu);
                        for ($i = 0; $i < $nbElements; $i++) {
                                $html .= "<li>";
                                $html .= $contenu[$i];
                                $html .= "</li>\n";
                        }
                } else {
                        $html .= "<li>";
                        $html .= $contenu;
                        $html .= "</li>\n";
                }
                return $html;
        }

}
?>
