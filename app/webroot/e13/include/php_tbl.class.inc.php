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
global $ClasseTableau;
if (!isset($ClasseTableau)) {
        $ClasseTableau = 1;

        require($GLOBALS["include__php_module_html.inc"]);

        class Tableau {

                var $HTML, $options; // la variable $options est un tableau indexe
                var $nbLignes, $nbColonnes;
                var $opt_ligne; // pour pouvoir attribuer des options aux lignes
                var $tableau;
                var $id;
                var $caption;

                function __construct($nL, $nC, $id = "Tableau") {
                        $this->nbLignes = $nL;
                        $this->nbColonnes = $nC;
                        // init. options pour les lignes TR
                        for ($i = 0; $i < $nL; $i++) {
                                $this->opt_ligne[$i] = array("HTML" => array());
                        }
                        $this->id = $id;
                        $this->caption = "";
                        // Definition des options PAR DEFAUT d'un tableau HTML.
                        $this->options = array("HTML" => array("ALIGN" => "CENTER",
                                "BORDER" => 1,
                                "CELLSPACING" => 0)
                        );

                        $this->init_Tableau($nL, $nC);
                }

                /* ----- partie privee ----- */






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
                        $m = count($this->tableau[0]); //colonnes

                        $this->nbLignes = $n;
                        $this->nbColonnes = $m;
                }

                /**
                 * redéfinit la dimension du tableau. NOTE : le contenu existant est effacé.
                 * */
                function reset($lignes, $colonnes) {
                        $this->init_Tableau($lignes, $colonnes);
                }

                function setCellule($i, $j, $c = array('options' => array(),
                    'contenu' => '')) {
                        $this->tableau[$i][$j] = $c;
                        $this->actualiserProprietes_Tableau();
                }

                function getCellule($i, $j) {
                        if ($i < $this->nbLignes && $j < $this->nbColonnes) {
                                //debug("tbl.class80: nbLignes = $this->nbLignes, nbColonnes = $this->nbColonnes, tbl.nom = $this->id.");
                                return $this->tableau[$i][$j];
                        } else {
                                return array();
                        }
                }

                function getOptionsArray() {
                        return $options;
                }

                function setOptionsArray($options = array("HTML" => array(),
                    "css" => array(),
                    "javascript" => array()
                )
                ) {
                        $this->options = $options;
                }

                function setContenu_Cellule($i, $j, $s = '', $o = -1) {

                        if ($o != -1) {//?? Gestion codage string -> HTML ??
                                $options = $o;
                        } else {
                                $options = array();
                        }

                        $c = array("options" => $options,
                            "contenu" => $s);

                        $this->setCellule($i, $j, $c);
                }
                
                function setContenu_Ligne($i, $cellules = array(), $o = -1) {
                        for ($j = 0; $j < count($cellules); $j++) {
                                $this->setContenu_Cellule($i, $j, $cellules[$j], $o);
                        }
                }
                
                function setContenu_Colonne($j, $cellules = array(), $o = -1) {
                        for ($i = 0; $i < count($cellules); $i++) {
                                $this->setContenu_Cellule($i, $j, $cellules[$i], $o);
                        }
                }

                private function getCellule_key($i, $j, $key, $array = FALSE) {
                        $c = $this->getCellule($i, $j);
                        return array_key_exists($key, $c) ? $c[$key] : ($array ? array() : NULL);
                }

                function getContenu_Cellule($i, $j) {
                        return $this->getCellule_key($i, $j, 'contenu');
                }

                function getOptionsArray_Cellule($i, $j) {
                        return $this->getCellule_key($i, $j, 'options');
                }

                function setOptionsArray_Cellule($i, $j, $o = array()) {
                        $this->setCellule($i, $j, array('options' => $o,
                            'contenu' => $this->getContenu_Cellule($i, $j))
                        );
                }

                function setOptionsArray_Ligne($n, $o = array()) {
                        $this->opt_ligne[$n] = $o;
                }

                function getOptionsArrayToHTML($i = -1, $j = -1) {
                        //pour le tableau
                        if ($i == -1 && $j == -1) {
                                $options = $this->options;
                        } elseif ($i != -1 && $j != -1) {// pour une certaine cellule
                                $options = $this->getOptionsArray_Cellule($i, $j);
                        } elseif ($i != -1 && $j == -1) {
                                $options = $this->opt_ligne[$i];
                        } else {
                                $options = array();
                        }
                        $options = optionsArrayToHTML($options);
                        return $options["HTML"] . $options["css"] . $options["javascript"] . $options["class"];
                }

                function writeHTML() {
                        echo stripslashes($this->HTML);
                        $this->HTML = NULL;
                }

                function fin($mode = '0') { // Ecriture du code HTML du tableau; mode = 1: la meth renv. la chaine de car. HTML. si mode 1, faire passer fin(1) dans un stripslashes!!
                        $this->HTML = "<!-- BEGIN MODULE TABLE id " . $this->id . "-->\n\t<TABLE ID=\"" . $this->id . "\" "; // retour à  la ligne pour la lisibilité du code source HTML
                        // options du tableau (style, ...) avec transcryptage en HTML/styleCSS     

                        $this->HTML .= $this->getOptionsArrayToHTML() . '>';

                        // caption
                        $this->HTML .= $this->caption;

                        for ($i = 0; $i < $this->nbLignes; $i++) {
                                $this->HTML .= "\n\t\t<TR " . @$this->getOptionsArrayToHTML($i) . '>';
                                for ($j = 0; $j < $this->nbColonnes; $j++) {
                                        $this->HTML .= "\n\t\t\t<TD " . $this->getOptionsArrayToHTML($i, $j) . ' >' . $this->getContenu_Cellule($i, $j) . '</TD>';
                                }
                                $this->HTML .= "\n\t\t</TR>";
                                // ecriture ligne apres ligne
                                if ($mode == '1') {
                                        $this->writeHTML();
                                }
                        }

                        $this->HTML .= "\n\t</TABLE><!-- END MODULE TABLE id " . $this->id . '-->';
                        //La methode peut retourner le code HTML ou l'écrire directement sur la sortie standard
                        switch ($mode) {
                                case 0:
                                        return $this->HTML;
                                case 1:
                                        $this->writeHTML();
                                        break;
                                default:break;
                        }
                }

        }

}
?>
