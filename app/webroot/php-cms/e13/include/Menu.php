<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:41:50 CEST 2004 @612 /Internet Time/
  @filename	php_menu.class.inc
 */
if (!isset($_ENV['ClasseMenu'])) {

        $_ENV['ClasseMenu'] = 1;
        ${__FILE__} = new Index();
        include_once basename(${__FILE__}->r["include__php_tbl.class.inc"]);
        include_once basename(${__FILE__}->r["include__php_module_html.inc"]);

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
                protected static $_r;
                var $r;
                var $logo;

                /** $_references[clef] = Menu/rubrique reprennent index du tableau $sitemap[clef] = clef de texte content-lang.properties */
                protected static $_references = array();
                /**
                * @return $mixed Array $_references ou Menu/rubrique correspondant à la clef $nomBnd
                */
                public static function gReferences($nomBnd = "") {
                    if($nomBnd !== "" && array_key_exists($nomBnd, self::$_references))
                          return self::$_references[$nomBnd];
                    else if($nomBnd === "")
                          return self::$_references;
                    throw new Exception("Error: Undefined Index " . $nomBnd);
                }
                /**
                 * @param string $cleLocale cle appartenant au fichier de langue locale/content-lang.properties (gestion
                 * multilangue i8n.
                 */
                function __construct($cleLocale, $url, $rubriques = array()) {
                        if (self::$_r === null) {
                            self::$_r = new Index();
                        }
                        $this->r = self::$_r;
                        $this->nom = $this->r->lang($cleLocale);
                        $this->nomBnd = $cleLocale;
                        $this->url = $url;
                        $this->rubriques = $rubriques; // les rubriques sont aussi des instances de Menu.class ! rassemblees dans un tableau

                        $this->setActif();

                        $this->fermer();
                }

                /* ------------- PARTIE PRIVEE ---------------- */

                private function afficherRubriques() {
                        for ($i = 0; $i < $this->countRubriques(); $i++) {
                                if (!isset($this->tbl)) {
                                        $this->tbl = new Tableau($this->countRubriques() + 1, 1, $this->nomBnd);
                                        $this->tbl->setOptionsArray(array("class" => "menu"));
                                }
                                /** ligne $i = 0 est pour le nom de la rubrique parente */
                                $l = $i + 1;
                                $text = "";
                                if ($this->rubriques[$i]->logo) {
                                       $text .= $this->rubriques[$i]->getLogo();
                                } else {
                                       $text .= $this->rubriques[$i]->getHTML();
                                }
                                /* ajoute une tabulation pour les elements de rubrique sans descendant ouvert*/
                                $tab = $this->rubriques[$i]->countRubriques() > 0 ?($this->rubriques[$i]->getEtat() === OUVERT ?"":"&nbsp;&nbsp;&nbsp;&nbsp;"):($this->rubriques[$i]->getEtat() === OUVERT ? ">&nbsp;&nbsp;":"&nbsp;&nbsp;&nbsp;&nbsp;");
                                /** definit la classe parent pour les elements avec descendant ouvert (un sous-tableau s'affiche) */
                                $rubClass = $this->rubriques[$i]->countRubriques() > 0 && $this->rubriques[$i]->getEtat() === OUVERT ? array("class" => "parent") : array("class" => "rubriques");
                                /* rubrique $i + 1 = $l*/
                                $this->tbl->setContenu_Cellule($l, 0, $tab.$text, $rubClass);
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
                        if($this === $menu) throw new Exception("ERROR: Attempt to produce an infinite loop. $\menu === \$this");
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
                                $this->HTML = HTML_lien($this->url, "<b>" . $this->nom . "</b>");
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
                 * @param string $pageEnCours URL de la page en cours, corrspondant à un lien dans le menu.
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
                 * Pour obtenir l'instance Menu de la rubrique ouverte, utiliser plutot la methode $page->menu->ouvrirBonneRubrique($page->getURL())
                 * @return string rubrique ouverte sous forme "breadcrumb"
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
                                                                return ($logoRubrique ? HTML_lien($this->url, $this->getLogo()) : $chemin . " / ") . $rubriqueOuverte;
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

                /** ecrit directement sur la sortie echo la valeur retour de getHTML()*/
                function writeHTML() {
                        echo stripSlashes($this->getHTML());
                }

                /** le nom de la rubrique seule si elle est fermee ou un tableau
                  * avec les sous-rurbriques si elle est ouverte.
                 **/
                function getHTML() {
                        if ($this->getEtat() == OUVERT) {
                                if (isset($this->tbl)) {
                                        $this->tbl->setContenu_Cellule(0, 0, $this->HTML, array("class" => "parent rubriques"));
                                        return $this->tbl->fin();
                                }
                        }
                        return $this->HTML;
                }

                function countRubriques() {
                        return count($this->rubriques);
                }

                /**
                 * Retourne sous forme de liste <ol>
                 * @param boolean $recursive false par defaut, retourne les rubriques sous-jacentes
                 * * */
                function getRubriquesListe($recursive = false) {
                        $liste = HTML_listeDebut($recursive);
                        for ($i = 0; $i < $this->countRubriques(); $i++) {
                                $rub = $this->rubriques[$i];
                                if ($rub instanceof Menu) {
                                        /** ne pas afficher les pages administration si session invalide */
                                        if (!Page::sessionAdminValide() && substr($rub->nomBnd, 0, 5) === "admin") {
                                                continue;
                                        }
                                        $liste .= HTML_listeElement(HTML_lien($rub->url, $rub->nom));
                                        if ($rub->countRubriques() > 0 && $recursive) {
                                                $liste .= "&nbsp;&nbsp;".$rub->getRubriquesListe(false);
                                        }

                                }
                        }
                        $liste .= HTML_listeFin($recursive);
                        return $liste;
                }

                /** le fichier etc/menu contient des clefs references du fichier
                 * etc/sitemap pour lier le menu URL (valeur etc/sitemap) avec un titre de page (valeur etc/menu)
                 * <br>Lire dans le tableau global sections => clef => valeur :<br>
                 * http://php.net/manual/en/function.parse-ini-file.php
                 * @param bundle $sitemap $this->sitemap = $this->parseBundle($GLOBALS["etc"], "sitemap");
                 * @param bundle $menu_ini $this->menu = $this->parseBundle($GLOBALS["etc"], "menu");
                 * @param Menu $parent une instance Menu ou NULL
                 * @return Array self::$_references
                 * @comment une clef locale du $menu_ini : array(page => "@key") "@" signifie sera masquee
                 */
                public static function creerMenuGlobals($sitemap, $menu_ini) {
                        foreach ($menu_ini as $section => $array) {
                                foreach ($array as $p => $cleLang) {
                                      if (substr($cleLang, 0, 1) === "@") {
                                              continue;
                                      }
                                      /** LIBELLE , URL */
                                      if(!array_key_exists($p, $sitemap)) {
                                              throw new Exception("Undefined key " . $p . " for " . $cleLang . " in \$sitemap.
                                               Please assign this key in sitemap.properties or change it in menu.properties !");
                                      }
                                      $m = new Menu($cleLang, $sitemap[$p]);
                                      self::$_references[$p] = $m;
                                      $parent = self::gReferences($section . "__index");
                                      if ($parent instanceof Menu && $parent !== $m) {
                                              $parent->ajouterRubrique($m);
                                      }
                              }
                        }
                        return self::$_references;
                }

                /**
                 * Responsive CSS pour le support des ecrans de petites tailles.
                 * par un clic utilisateur, le menu $IdName (clef <div class=$idName>) va s'afficher.
                 * Un timeout va masquer le menu ~8000 ms apres. Le tag DIV affecte sera modifie par le script etc/js/slide.js.
                 * @param string $docElement document element id <div id="$docElement" class="slide"> concernant le menu de navigation
                 */
                function getBoutonOuvrirRubriques($docElement) {
                        $timeout = $this->countRubriques() * 2000; // timeout ms en fonction du nb de rubriques
                        /** initialise un script js pour cacher l'element a gauche (c.f. la classe de style .swipe) */
                        $divElement = "var element = document.getElementById('" . $docElement . "');\n";
                        /** definir un bouton d'activation avec un retour sur l'evenement souris ou touch onClick */
                        $js = $divElement . " sM(element, " . $timeout . ",'absolute');\n";
                        $html = "<script src='" . $this->r->sitemap["etc__slide_js"] . "'></script>";
                        $html .= "<div id='bMenu' class='mobile'>";
                        $html .= HTML_image($this->r->sitemap["images__boutonMenu_small"], array(
                          "javascript" => array("onClick" => $js . " return;")
                        )) . "</div>";
                        return $html;
                }

        }

}
?>
