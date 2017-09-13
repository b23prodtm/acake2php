<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:41:50 CEST 2004 @612 /Internet Time/
  @filename	php_menu.class.inc
 */
global $ClasseMenu;
if (!isset($ClasseMenu)) {

        $ClasseMenu = 1;
        require($GLOBALS["include__php_tbl.class.inc"]);
        require($GLOBALS["include__php_module_html.inc"]);

        define("OUVERT", 0);
        define("FERME", 1);

        class Menu {

                /** nom reellement lisible */
                var $nom;

                /** nom de clef content-lang */
                var $nomBnd;
                var $url, $rubriques;

                /** etat OUVERT|FERME, menu actif ou inactif (TRUE|FALSE); */
                var $etat;
                var $actif;
                var $tbl, $HTML;
                var $r;
                var $logo;

                /**
                 * @param string $cleLocale cle appartenant au fichier de langue locale/content-lang.properties (gestion 
                 * multilangue i8n.
                 */
                function __construct($cleLocale, $url, $rubriques = array()) {
                        $this->r = new Index(NULL);
                        $this->nom = $this->r->lang($cleLocale);
                        $this->nomBnd = $cleLocale;
                        $this->url = $url;
                        $this->rubriques = $rubriques; // les rubriques sont aussi des instances de Menu.class ! rassemblees dans un tableau

                        $this->setActif();

                        $this->fermer();
                }

                /* ------------- PARTIE PRIVEE ---------------- */

                private function afficherRubriques() {
                        $classIdName = "rubriques";
                        for ($i = 0; $i < $this->countRubriques(); $i++) {
                                if (!isset($this->tbl)) {
                                        $this->tbl = new Tableau($this->countRubriques(), 2, $this->nomBnd);
                                        $this->tbl->setOptionsArray(array("HTML" => array("ALIGN" => "LEFT",
                                                "CELLPADDING" => 3, "class" => $classIdName)));
                                }
                                $this->tbl->setContenu_Cellule($i, 0, "::");
                                $this->tbl->setOptionsArray_Cellule($i, 0, array("HTML" => array("VALIGN" => "top"),
                                    "javascript" =>
                                    array("onMouseOver" => "this.style.bgcolor = 'green'; this.style.color = 'yellow'; return;",
                                        "onMouseOut" => "this.style.bgcolor = 'transparent'; this.style.color = 'none'; return;")));
                                $this->tbl->setContenu_Cellule($i, 1, $this->rubriques[$i]->getHTML());
                        }
                }

                private function setActif() {
                        $this->actif = TRUE;
                }

                private function setInactif() {
                        $this->actif = FALSE;
                }

                // methode auxiliaire a ouvrirBonneRubrique()
                function pageEnCours($pageEnCours) {
                        if ($this->url === $pageEnCours) {
                                return TRUE;
                        } else {
                                return FALSE;
                        }
                }

                /* ------------- PARTIE PUBLIQUE ---------------- */

                function ajouterRubrique(&$menu) {
                        // rajouter une ligne au tableau contenant la liste des rubriques du menu
                        if (isset($this->tbl)) {
                                $this->tbl->nbLignes++;
                        }
                        $this->rubriques[] = $menu;
                }

                function getEtat() {
                        return $this->etat;
                }

                function ouvrir() {
                        if ($this->actif) {
                                $this->HTML = HTML_lien($this->url, "<b>" . $this->nom . "</b>", array("css" => array(
                                        "text-decoration" => "none")
                                        )
                                );
                        } else {
                                $this->HTML = "<i>" . $this->nom . "</i>";
                        } // NE PAS DONNER DE LIEN SI INACTIF
                        $this->etat = OUVERT;
                        $this->afficherRubriques();
                }

                function fermer() {
                        if ($this->actif) {
                                $this->HTML = HTML_lien($this->url, "" . $this->nom . "");
                        } else {
                                $this->HTML = "-" . $this->nom . "";
                        } // NE PAS DONNER DE LIEN SI INACTIF
                        $this->etat = FERME;
                }

                /**
                 * ***** methode recursive pour ouvrir la bonne rubrique du menu correspondant a la page en cours. 
                 * @param string $pageEnCours URL de la page en cours, corrspondant Ã  un lien dans le menu.
                 * @return Menu rubrique ouverte
                 * **** */
                function ouvrirBonneRubrique($pageEnCours) {
                        $rub = FALSE;
                        for ($i = 0; $i < count($this->rubriques); $i++) {
                                if (($rub = $this->rubriques[$i]->ouvrirBonneRubrique($pageEnCours))) { // on commence par verifier s'il la page n'est pas une des sousrubriques
                                        $this->ouvrir(); //si une sous rubrique est ouverte, la rubrique ($i) a ete ouverte (c.f. plus bas), il faut aussi ouvrir le menu-rubrique courant ($this)
                                        break;
                                } else if ($this->rubriques[$i]->pageEnCours($pageEnCours)) { //s'il n'y a pas de ssrub ou aucune n'est la page courante, test de la rubrique ($i) comme page courante
                                        $this->rubriques[$i]->ouvrir(); //si oui, alors on ouvre la rubrique
                                        $this->ouvrir(); //puis le menu-rubrique courant
                                        $rub = $this->rubriques[$i];
                                        break;
                                }
                        }
                        if (!$rub && $this->pageEnCours($pageEnCours)) {
                                $this->ouvrir();
                                $rub = $this;
                        }
                        return $rub;
                }

                /**
                 * methode utilisee pour afficher l'emplacement sur le site (ex: Telechargements/Wallpapers/) ou un logo de la rubrique
                 * @return Menu rubrique ouverte
                 */
                function getRubriqueOuverte($logoRubrique = FALSE) {
                        if ($this->getEtat() == OUVERT) {
                                $n = $this->countRubriques();
                                $chemin = HTML_lien($this->url, $this->nom);
                                if ($n != 0) { // si le menu a des rubriques il faut voir si il y en a une d'ouverte
                                        for ($i = 0; $i < $n; $i++) {
                                                if ($this->rubriques[$i]->getEtat() == OUVERT) {
                                                        $rubriqueOuverte = $this->rubriques[$i]->getRubriqueOuverte($logoRubrique);
                                                        if (is_string($rubriqueOuverte)) {
                                                                // concatenation du chemin si pas de logo
                                                                return ($logoRubrique ? "" : $chemin . " / ") . $rubriqueOuverte;
                                                        }
                                                }
                                        }
                                } else if ($logoRubrique) {
                                        return HTML_lien($this->url, $this->getLogo());
                                }
                                return $chemin;
                        } else
                                return NULL;
                }

                function getLogo($icone = TRUE) {
                        return HTML_image($logo);
                }

                function writeHTML() {
                        if (isset($this->tbl)) {
                                if ($this->getEtat() == OUVERT) {
                                        $this->tbl->fin();
                                }
                        }
                        echo stripSlashes($this->HTML);
                }

                function getHTML() {
                        if ($this->getEtat() == OUVERT) {
                                if (isset($this->tbl)) {
                                        $this->HTML .= $this->tbl->fin();
                                }
                        }
                        return $this->HTML;
                }

                function countRubriques($recursive = false) {
                        if (!$recursive) {
                                return count($this->rubriques);
                        } else {
                                $n = $this->countRubriques();
                                foreach ($this->rubriques as $m) {
                                        $n += $m->countRubriques(true);
                                }
                                return $n;
                        }
                }

                /**
                 * Retourne sous forme de liste <ol>
                 * @param boolean $recursive false par défaut, retourne les rubriques sous-jacentes
                 * * */
                function getRubriquesListe($recursive = false) {
                        $liste = HTML_listeDebut_num();
                        foreach ($this->rubriques as $rub) {
                                if ($rub instanceof Menu) {
                                        /** ne pas afficher les pages administration si session invalide */
                                        if (!ADMIN_Page::sessionAdminValide() && substr($rub->nomBnd, 0, 5) === "admin") {
                                                continue;
                                        }
                                        $liste .= HTML_listeElement(HTML_lien($rub->url, $rub->nom));
                                        if ($rub->countRubriques() > 0 && $recursive) {
                                                $liste .= $rub->getRubriquesListe(true);
                                        }
                                }
                        }
                        $liste .= HTML_listeFin_num();
                        return $liste;
                }

                /** retourne la clef etc/content-lang correspondante a la cle du Menu (commune aux
                 *  fichiers etc/menu et etc/sitemap)
                 * @param Bundle $bundleMenu Registre->menu_ini
                 *                  
                 */
                static function versLangLocalized($cleMenu, $bundleMenu) {
                        foreach ($bundleMenu as $section) {
                                foreach ($section as $page => $cleLang) {
                                        if (is_array($cleLang)) { // il y a une section du menu a rechercher
                                                return Menu::versLangLocalized($cleMenu, $cleLang);
                                        } elseif ($page === $cleMenu) { // la clef est celle demandee
                                                return $cleLang;
                                        }
                                }
                        }
                }

                /** le fichier etc/menu contient des clefs references du fichier
                 * etc/sitemap pour lier le menu URL (valeur etc/sitemap) avec un titre de page (valeur etc/menu)
                 * <br>Lire dans le tableau global sections => clef => valeur :<br>
                 * http://php.net/manual/en/function.parse-ini-file.php
                 * @param bundle $pages $this->sitemap = $this->parseBundle($GLOBALS["etc"], "sitemap");
                 * @param bundle $menu $this->menu = $this->parseBundle($GLOBALS["etc"], "menu");            
                 * @param Menu $parent une instance Menu ou NULL
                 */
                static function creerMenuGlobals($pages, &$menu, &$parent = NULL) {
                        foreach ($pages as $p => $cleLoc) {
                                if (!is_array($cleLoc)) {
                                        if (substr($cleLoc, 0, 1) === "@") {
                                                continue;
                                        }
                                        $m = new Menu($cleLoc, $GLOBALS[$p]);
                                        $menu[$p] = $m;
                                        if ($parent instanceof Menu) {
                                                $parent->ajouterRubrique($m);
                                        }
                                } else {
                                        Menu::creerMenuGlobals($cleLoc, $menu, $menu[$p . "__index"]);
                                }
                        }
                }

                /**
                 * Responsive CSS pour le support des écrans de petites tailles.
                 * par un clic utilisateur, le menu $IdName (clef <div class=$idName>) va s'afficher. 
                 * Un timeout va masquer le menu 3000 ms aprÃ¨s.
                 * @param $small pour afficher une petite icone (val par defaut : false)
                 */
                function getBoutonOuvrirRubriques($idName, $small = false) {
                        $js = "";
                        $timeout = $this->countRubriques() * 3000; // timeout ms en fonction du nb de rubriques
                        for ($i = 0; $i < $this->countRubriques(); $i++) {
                                $js .= "document.getElementById('" . $this->rubriques[$i]->nomBnd . "').style.visibility = 'visible'; 
			setTimeout(function(){
				document.getElementById('" . $this->rubriques[$i]->nomBnd . "').style.visibility = 'hidden';
				}," . $timeout . ");";
                        }
                        return "<div class='$idName'>" . HTML_lien("#", HTML_image($small ? $GLOBALS["images__boutonMenu_small"] : $GLOBALS["images__boutonMenu"], array("class" => "boutonMenu")), array("javascript" =>
                                    array("onClick" => $js . " return;"))) . "</div>";
                }

        }

}
?>