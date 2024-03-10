<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to b23|prod)
  @author	www.b23prodtm.info
  @date	Sat Sep 18 14:43:50 CEST 2004 @572 /Internet Time/
  @filename php_tbl.class.inc
 */


/* comment ecrire un tableau HTML?
  / un tableau en HTML ici est un objet PHP contenant parmi ses variables un tableau a double entree qui s'agrandit a chaque nouvelle ligne/cellule
  / *nouveau tableau* -> tbl;
  / tbl->setCell(tbl_nouv_ligne,tbl_nouv_cell,'contenu');
  /				tbl_nouv_ligne et cell renvoient les indices de la nouvelle case dans le tableau
  / - pour donner le contenu d'une cellule:
  /				String s = tbl->getCell(indiceLigne,indiceColonne);
  / - refermer un tableau et l'afficher (ecrire le code HTML):
  /				tbl->fin();
  / **DEBUG** pour indiquer des commandes de debugging
 */
if (!isset($_ENV['ClasseTableau'])) {
        $_ENV['ClasseTableau'] = 1;

        ${__FILE__} = new Index();
        include_once basename(${__FILE__}->r["include__php_module_html.inc"]);
        /**
         * envoie sur la sortie echo peut etre jointe a TBL_DIV ou TBL_STD avec la pipe ('|')
         *          */
        define("TBL_OUT", 0x1);
        /**
         * affiche en mode tag <div> : le rendu fin() utilise un tag par ligne et par cellule
         *          */
        define("TBL_DIV", 0x2);
        /**
         * affiche en mode standard <table> : le rendu fin() utilise un tableau normal <pre><table><tr><td>..</pre>
         *          */
        define("TBL_STD", 0x4);
        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
             /**
               * @since PHP 7*/
                  define("TBL_STD_TAGS", array("table", "tr", "td"));
              /**
               * @since PHP 7*/
                  define("TBL_DIV_TAGS", array("div", "span", "div"));
        }

        class Tableau {

                var $HTML, $options; // la variable $options est un tableau indexe
                var $nbLignes, $nbColonnes;
                var $opt_ligne; // pour pouvoir attribuer des options aux lignes
                var $tableau;
                var $id;
                var $caption;

                /*
                 * @param $id il faut un id unique pour l'environment ajax
                 */
                function __construct($nL, $nC, $id = "") {
                        $this->nbLignes = $nL;
                        $this->nbColonnes = $nC;
                        // init. options pour les lignes TR
                        for ($i = 0; $i < $nL; $i++) {
                                $this->opt_ligne[$i] = array("html" => array());
                        }
                        /* generer un id unique */
                        if ($id === "") {
                                $id = "tbl-" . time();
                        }
                        $this->id = $id;
                        $this->caption = "";
                        // Definition des options PAR DEFAUT d'un tableau HTML.
                        $this->options = array("html" => array());

                        $this->init_Tableau($nL, $nC);
                }

                /* ----- partie privee ----- */

                /** tags indesirables */
                var $html_tags_void = array("font", "border", "width", "height", "align", "cellpadding", "cellspacing");

                /** fonction interne de controle de parametres deprecies */
                private function _standardizeTags($o = array()) {
                        if(!is_array($o)) {
                              return array();
                        }
                        if(count($o) == 0) {
                              return $o;
                        }
                        $keys = array("html", "css");
                        $o = array_change_key_case($o);
                        foreach ($keys as $k) {
                                if (array_key_exists($k, $o) && is_array($o[$k])) {
                                        $o[$k] = $this->__standardizeTags($o[$k]);
                                }
                        }
                        return $o;
                }

                /** fonction interne de controle de parametres deprecies */
                private function __standardizeTags($subo) {
                        foreach ($this->html_tags_void as $tag) {
                                $subo = array_change_key_case($subo);
                                if(array_key_exists($tag, $subo)) {
                                        i_debug("HTML Tag is Deprecated : " . $tag, DEBUG_WARNING);
                                }
                        }
                        return $subo;
                }

                /* ----- parite publique ----- */

                function setCaption($s, $position = "bottom") {
                        $this->caption = "<CAPTION ALIGN='$position'>$s</CAPTION>";
                }

                function init_Tableau($n, $m) {
                        //initialisation du tableau
                        for ($i = 0; $i < $n; $i++) {
                                for ($j = 0; $j < $m; $j++) { // Definition d'une cellule d'un tableau HTML
                                        $this->setCellule($i, $j, array('options' => array(),
                                            // HTML EN MAJUSCULES, css en minuscules
                                            'contenu' => ""));
                                }
                        }
                        $this->actualiserProprietes_Tableau();
                }

                function is_upper($s) {
                        if (strToLower($s) === $s) { //echo "**DEBUG**S est MINUSCULES";
                                return FALSE;
                        } //$s est en minuscules
                        if (strToUpper($s) === $s) {
                                return TRUE;
                        } //$s est en majuscules
                        return FALSE;
                }

                function actualiserProprietes_Tableau() {
                        $n = count($this->tableau); //lignes
                        $m = is_array($this->tableau[0]) ? count($this->tableau[0]) : 1; //colonnes

                        $this->nbLignes = $n;
                        $this->nbColonnes = $m;
                }

                /**
                 * red�finit la dimension du tableau. NOTE : le contenu existant est effac�.
                 * */
                function reset($lignes, $colonnes) {
                        $this->init_Tableau($lignes, $colonnes);
                }

                /**
                * methode parente protegee, fusionne les options : array_merge(existant, nouveau)
                */
                protected function setCellule($i, $j, $c = array('options' => array(),
                    'contenu' => '')) {
                        $old = $this->getCellule($i, $j);
                        $this->tableau[$i][$j]= $c;
                        /* le tabeau d'options est fusionne*/
                        $this->tableau[$i][$j]["options"] = array_merge($old["options"], $c["options"]);
                        $this->actualiserProprietes_Tableau();
                }

                /**
                 * @return array options=>array("class","html",..), contenu=>""
                 *                  */
                function getCellule($i, $j) {
                        $default = array('options' => array(),
                            'contenu' => '');
                        if ($i < $this->nbLignes && $j < $this->nbColonnes) {
                                //debug("tbl.class80: nbLignes = $this->nbLignes, nbColonnes = $this->nbColonnes, tbl.nom = $this->id.");
                                if (is_array($this->tableau) && array_key_exists($i, $this->tableau) && is_array($this->tableau[$i]) && array_key_exists($j, $this->tableau[$i]) && is_array($this->tableau[$i][$j])) {
                                        return $this->tableau[$i][$j];
                                }
                        }
                        return $default;
                }

                function getOptionsArray() {
                        return $this->options;
                }

                /**
                *
                * @param array $options fusionne avec les options existantes */
                function setOptionsArray($options = array("html" => array(),
                    "css" => array(),
                    "javascript" => array(),
                    "class" => "")) {
                        $options = $this->_standardizeTags($options);
                        $this->options = is_array($this->options) ? array_merge($this->options, $options) : $options;
                }

                /** eviter  les options HTML de mise en page (align, border, etc.) et preferer les
                 * references de classes CSS */
                function setContenu_Cellule($i, $j, $s = '', $o = array()) {
                        $c = array("options" => $this->_standardizeTags($o),
                            "contenu" => $s);
                        $this->setCellule($i, $j, $c);
                }

                function setContenu_Ligne($i, $cellules = array(), $o = array()) {
                        for ($j = 0; $j < count($cellules); $j++) {
                                $this->setContenu_Cellule($i, $j, $cellules[$j], $o);
                        }
                }

                function setContenu_Colonne($j, $cellules = array(), $o = array()) {
                        for ($i = 0; $i < count($cellules); $i++) {
                                $this->setContenu_Cellule($i, $j, $cellules[$i], $o);
                        }
                }

                private function getCellule_key($i, $j, $key, $array = FALSE) {
                        $c = $this->getCellule($i, $j);
                        if(array_key_exists($key, $c)) {
                                return $c[$key];
                        } else {
                                return $array ? array() : NULL;
                        }
                }

                function getContenu_Cellule($i, $j) {
                        return $this->getCellule_key($i, $j, 'contenu');
                }

                function getOptionsArray_Cellule($i, $j) {
                        return $this->getCellule_key($i, $j, 'options', TRUE);
                }

                function setOptionsArray_Cellule($i, $j, $o = array()) {
                        $this->setCellule($i, $j, array('options' => $this->_standardizeTags($o),
                            'contenu' => $this->getContenu_Cellule($i, $j))
                        );
                }

                /**
                * @param $o array fusionne avec les options existantes
                */
                function setOptionsArray_Ligne($n, $o = array()) {
                        $o = $this->_standardizeTags($o);
                        $this->opt_ligne[$n] = is_array($this->opt_ligne[$n]) ? array_merge($this->opt_ligne[$n], $o) : $o;
                }

                function getOptionsArray_Ligne($n) {
                        if (array_key_exists($n, $this->opt_ligne)) {
                                return $this->opt_ligne[$n];
                        } else {
                                return array();
                        }
                }

                function getOptionsArrayToHTML($i = -1, $j = -1) {
                        $options = array();
                        //pour le tableau
                        if ($i == -1 && $j == -1) {
                                $options = $this->options;
                        } elseif ($i != -1 && $j != -1) {
                                $options = $this->getOptionsArray_Cellule($i, $j);
                        } elseif ($i != -1 && $j == -1) {
                                $options = $this->getOptionsArray_Ligne($i);
                        }
                        return count($options) == 0 ? "" : optionsArrayToHTML($options, true);
                }

                function writeHTML() {
                        echo stripslashes($this->HTML);
                        $this->HTML = NULL;
                }

                /** evalue la valeur du parametre de span
                 * @return int (01-99), retourne 1 si aucun parametre */
                function parseSpan_TD($i, $j, $param = "rowspan") {
                        $strOpt = array_change_key_case($this->getOptionsArray_Cellule($i, $j));
                        if (array_key_exists("html", $strOpt) && is_array($strOpt["html"])) {
                                $a = array_change_key_case($strOpt["html"]);
                                if (array_key_exists($param, $a)) {
                                        return $a[$param];
                                }
                        } else {
                                return 1;
                        }
                }

                /** fonction recursive d'ecriture de cellule par ligne de tableau
                  @param array $span array($row, 'colspan', $col, 'rowspan')
                 *                  */
                private function _fin_C($i, $j, &$span, $tags = array("table", "tr", "td")) {
                        if ($j >= $this->nbColonnes) {
                                return;
                        } else {
                                /** si pas de span actif (== 1), affiche alors la cellule  */
                                $d = 0x0;
                                /* lire de nouveau si aucun actif (== 1), les params rowspan et colspan pour une cellule du tableau */
                                if ($span["colspan"] == 1) {
                                        $span = array(
                                            "row" => $i, "colspan" => $this->parseSpan_TD($i, $j, "colspan"),
                                            "col" => $span["col"], "rowspan" => $span["rowspan"]
                                        );
                                        $d = 0x1;
                                        /* COLSPAN */
                                } else if ($span["colspan"] > 1 && $span["row"] == $i) {
                                        /* decrementation sans afficher la cellule (ligne $i) */
                                        $span["colspan"] --;
                                        $d = 0x0;
                                } else {
                                        $d = 0x1;
                                }
                                /* si aucun span actif (== 1), lire de nouveau les params rowspan et colspan pour une cellule du tableau */
                                if ($span["rowspan"] == 1) {
                                        $span = array(
                                            "row" => $span["row"], "colspan" => $span["colspan"],
                                            "col" => $j, "rowspan" => $this->parseSpan_TD($i, $j, "rowspan")
                                        );
                                        $d &= 0x1;
                                        /* ROWSPAN */
                                } else if ($span["rowspan"] > 1 && $span["col"] == $j) {
                                        /* decrementation sans afficher la cellule (colonne $j) */
                                        $span["rowspan"] --;
                                        i_debug("rowspan ($i,$j)=" . $span["rowspan"], DEBUG_VERBOSE);
                                        $d = 0x0;
                                } else {
                                        $d &= 0x1;
                                }
                                /* affichage eventuel de la cellule TD $i,$j */
                                if (($d & 0x1) == 0x1) {
                                        if($tags[2] !== "") {
                                                $this->HTML .= "\n\t\t\t<" . $tags[2] . $this->getOptionsArrayToHTML($i, $j) . ">";
                                        }
                                        $this->HTML .= $this->getContenu_Cellule($i, $j);
                                        if($tags[2] !== "") {
                                                $this->HTML .= "</" . $tags[2] . ">\n";
                                        }
                                }
                                /** continuer vers la prochaine cellule de la ligne $i et colonne $j+1 */
                                $this->_fin_C($i, $j + 1, $span, $tags);
                        }
                }

                /** fonction recursive d'ecriture de ligne de tableau
                  @param array $span array("row", "colspan", "col", "rowspan") */
                private function _fin_L($i, &$span, $mode, $tags = array("table", "tr", "td")) {
                        if ($i >= $this->nbLignes) {
                                /* ecriture ligne apres ligne :
                                 * pour la lisibilite debogueur */
                                if ($mode == 1) {
                                        $this->writeHTML();
                                }
                                return;
                        }
                        if($tags[1] !== "") {
                                $this->HTML .= "\t\t<" . $tags[1] . $this->getOptionsArrayToHTML($i) . ">\n";
                        }
                        $this->_fin_C($i, 0, $span, $tags);
                        if($tags[1] !== "") {
                                $this->HTML .= "\t\t</" . $tags[1] . ">\n";
                        }
                        $this->_fin_L($i + 1, $span, $mode, $tags);
                }

                /**
                * @param array $tags balises p.ex : TBL_DIV_TAGS => array(div,span,div) empty "" ne signifie qu'aucune balise encadrant demandee
                */
                function fin($mode = TBL_STD, $tags = array("table", "tr", "td")) {
                          /* Ecriture du code HTML du tableau; mode = 1: la meth renv. la chaine de car. HTML. si mode 1, faire passer fin(1) dans un stripslashes!!*/
                        if (!($mode & (TBL_STD | TBL_DIV))) {
                                trigger_error("Tableau: no valid mode found for tag=0x" . dechex($mode), E_USER_ERROR);
                        }

                        $this->HTML = "<!-- BEGIN MODULE TABLE id " . $this->id . "-->\n\t";
                        if($tags[0] !== "") {
                                $this->HTML .= "<" . $tags[0] . " id=\"" . $this->id . "\"";
                                // options du tableau (style, ...) avec transcryptage en HTML/styleCSS
                                $this->HTML .= $this->getOptionsArrayToHTML() . ">\n";
                        }
                        // caption
                        $this->HTML .= $this->caption;

                        /** valeurs span et fin recursive */
                        $span = array("row" => 0, "colspan" => 1, "col" => 0, "rowspan" => 1);
                        $this->_fin_L(0, $span, $mode, $tags);
                        if($tags[0] !== "") {
                                $this->HTML .= "\t</" . $tags[0] . ">\n";
                        }
                        $this->HTML .= "\t<!-- END MODULE TABLE id " . $this->id . "-->\n";
                        //La methode peut retourner le code HTML ou l'�crire directement sur la sortie standard
                        $mode &= TBL_OUT;
                        switch ($mode) {
                                case 0:
                                        return $this->HTML;
                                case 1:
                                        $this->writeHTML();
                                        return true;
                                default:
                                        return false;
                        }
                }

        }

}
?>
