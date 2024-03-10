<?php

/*
 * Copyright 2017 wwwb23prodtminfo <b23prodtm at sourceforge.net>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/*
 * @date	Sat Sep 18 15:43:19 CEST 2004 @613 /Internet Time/
 * @filename	php_page.class.inc
 */
if (!isset($_ENV['ClassePage'])) {
        $_ENV['ClassePage'] = 1;

        // Classe gerant toutes les pages php du site
        ${__FILE__} = new Index();
        include_once basename(${__FILE__}->r['include__php_module_html.inc']);
        include_once basename(${__FILE__}->r["include__php_tbl.class.inc"]);
        include_once basename(${__FILE__}->r["include__php_menu.class.inc"]);
        include_once basename(${__FILE__}->r['include__php_info.class.inc']);
        include_once basename(${__FILE__}->r['include__php_SQL.class.inc']);
        include_once basename(${__FILE__}->r['include__php_image.class.inc']);
        include_once basename(${__FILE__}->r['include__php_formulaire.class.inc']);
        include_once basename(${__FILE__}->r['include__php_constantes.inc']);
        include_once basename(${__FILE__}->r["include__php_module_DVD.inc"]);
        include_once basename(${__FILE__}->r["include__php_module_locale.inc"]);
        include_once basename(${__FILE__}->r["include__php_captcha.class.inc"]);

        define("BUF_OFF", false);

        /*
         *
          @class		Page
          @abstract   Elle definit les pages HTML dans leur globalite. Le code HTML est ecrit sur la sortie à l'appel de la methode fin(), en dernier donc. un tableau associatif $proprietes contient les proprietes de la page que l'on peut modifier. | MENU | permet de naviguer a travers le "sitemap" | CONTENU | un tableau php associatif rassemble le contenu de la page, $this->tbl. le contenu de la page est formate a l'aide de tableaux HTML (classe php Tableau). | ENTETE-OUEST| il contient le nom de la *page en cours* ainsi que son chemin virtuel depuis la page racine du site (situation). Le chemin (par ex. Home/Admin/Gestion Infos/_) affiche depend de la hierarchie du Menu de la Page, ordinairement correspondant au plan du site. Breadcrumb | AdminPAGE | cette sous-classe etend les possibilites de Page vers une page verouillee par un accès reserve aux administrateurs. voir class AdminePage (plus bas...)
         */

        class Page {
                /* Variables pour stocker le code de la page
                  HTML code a ecrire
                  allHTML code deja ecrit
                 */

                var $HTML, $allHTML;

                /**
                 * utilisateur qui a ouvert une session
                 */
                var $user;

                /**
                 * tableau associatif
                 */
                var $proprietes;

                /**
                 * les tableaux composant la page
                 */
                var $tbl;

                /**
                 * le menu dynamique de la page
                 */
                var $menu;

                /**
                 * Registre (php_index.inc.php)
                 */
                var $r;

                /**
                 * @param Index r une instance de Registre de la page courante (Registre(filter_input(INPUT_SERVER,'PHP_SELF') pour
                 * instancier tous les parametres locaux (liens html).
                 * @param boolean $gtag not used
                 * @param String $localeKey cle vers le fichier locale/content-lang
                 */
                function __construct(Index $r, $sitemapKey, $sendHeaders = true, $sessionId = NULL, $enc = ENCODE_CS, $ads = true) {
                        /********* output buffer ********/
                        mb_http_output($enc);
                        mb_internal_encoding($enc);
                        if (!BUF_OFF) {
                                ob_start("mb_output_handler");
                        }
                        $this->r = $r;
                        $this->menu = MENU::gReferences('default__index');
                        if($this->menu !== NULL) {
                          $this->menu->ouvrirBonneRubrique(MENU::gReferences($sitemapKey)->url);
                        } else {
                          trigger_error("Wrong sitemap root__index in the global MENU::gReferences", DEBUG_ERROR);
                          var_dump(Menu::gReferences());
                        }
                        // ********* Entetes HTTP *********
                        if ($sendHeaders) {
                                header("Default-Charset: $enc");
                                header("Content-Type: text/html");
                        }

                        // ********************************
                        // ----------------- PROPRIETES ------------------
                        $this->proprietes = array("titre" => MENU::gReferences($sitemapKey)->nom,
                            "URL" => MENU::gReferences($sitemapKey)->url,
                            "contenu" => "",
                            "securite" => 0,
                            "sessionId" => $sessionId,
                            "ads" => $ads,
                            "enteteLogo" => TRUE,
                            "headSent" => FALSE);
                        // -----------------------------------------------
                        // Jeu de tableaux pour la mise en page
                        $this->tbl["body"] = new Tableau(5, 2, "body"); /* la page entiere, contiendra tous les autres tableaux.
                          cellule 1,0: affichage de l'emplacement actuel du navigateur sur le site */
                        $this->tbl["contenu_ouest"] = new Tableau(3, 1, "contenu_ouest"); // le menu, la recherche, derniere info
                        $this->tbl["contenu_est"] = new Tableau(1, 1, "contenu_est"); // le contenu essentiel
                        $this->tbl["contenu"] = new Tableau(1, 2, "contenu"); // le menu a gauche et le contenu informel de la page a droite; le contenu de ce tableau correspond au contenu de $proprietes["contenu"] est modifie par la fonction ajouterContenu
                        $this->tbl["entete"] = new Tableau(1, 2, "entete");
                        $this->tbl["entete_ouest"] = new Tableau(2, 1, "entete_ouest"); // Image d'entete
                        $this->tbl["entete_est"] = new Tableau(3, 1, "entete_est"); // logo et titre

                        /* ads integres */
                        if ($ads) {
                                $this->enableAds();
                        } else {
                                $this->disableAds();
                        }
                }

                /* ------------------------------------------------- PARTIE PRIVEE */

                /* Fonction pour ecrire le code HTML en attente d'ecriture en memoire;
                  stockage dans allHTML pour une evtl. reutilisation future
                 */

                private function writeHTML() {

                        /* write */
                        echo stripslashes($this->HTML);
                        $this->allHTML .= $this->HTML;
                        $this->HTML = NULL;
                }

                /** contenu balise <head> */
                private function headers($headers = array("title"=>"Page",
                        "link" => array("rel"=>"stylesheet",
                                "href"=>"/css/cake.generic.css",
                                "type"=>"text/css"))) {
                          foreach($headers as $tag => $data) {
                                  $this->HTML .= "<" . $tag;
                                  if(is_array($data)) {
                                          $this->HTML .= " " . optionsArrayToHTML($data, true) . "/>\n";
                                  } else {
                                          $this->HTML .=  ">" . $data;
                                          $this->HTML .= "</" . $tag . ">\n";
                                  }
                          }
                }

                private function choixLang() {
                        /** choix lang */
                        $o = array("html" => self::$firefox_fix);
                        $HTML = HTML_lien($this->getURL() . "?lang=" . EN, HTML_image($this->r->sitemap["images__uk"], $o));
                        $HTML .= HTML_lien($this->getURL() . "?lang=" . DE, HTML_image($this->r->sitemap["images__de"], $o));
                        $HTML .= HTML_lien($this->getURL() . "?lang=" . FR, HTML_image($this->r->sitemap["images__fr"], $o));
                        return $HTML;
                }

                private function piedDePage() {
                        $t = new Tableau(1, 3, "copyright");
                        $t->setOptionsArray(array("class" => "masque"));

                        $t->setContenu_Cellule(0, 0, filter_input(INPUT_SERVER, 'SERVER_SIGNATURE') .
                        "Icons made by " . HTML_lien("https://www.freepik.com", "Freepik") .
                        " from " . HTML_lien("https://www.flaticon.com/","www.flaticon.com"));
                        $t->setContenu_Cellule(0, 1, "&copy; " . $this->r->lang("copyYear") . " " . filter_input(INPUT_SERVER, 'SERVER_NAME'));
                        $t->setContenu_Cellule(0, 2, HTML_lien("mailto:" . $this->r->lang("mailto"), $this->r->lang("mailtoLabel")));
                        return $t->fin();
                }

                private function siteMap() {
                        $nSub = 0;
                        foreach ($this->menu->rubriques as $rub) {
                                $n = $rub->countRubriques();
                                if ($n > $nSub) {
                                        $nSub = $n;
                                }
                        }
                        $s = new Tableau($nSub + 2, $this->menu->countRubriques(), "sitemap");
                        $s->setContenu_Cellule(0, 0, ".::::::::: " . $this->r->lang("sitemap") . " :::::::::.", array(
                            "html" => array("COLSPAN" => $s->nbColonnes))
                        );
                        for ($i = 0; $i < $this->menu->countRubriques(); $i++) {
                                $r = $this->menu->rubriques[$i];
                                if ($r === NULL) {
                                  i_debug("WARNING: Null Rubrique[" . $i . "] was detected ", DEBUG_WARNING);
                                        continue;
                                }
                                // n'affiche pas la section administration
                                if (substr($r->nomBnd, 0, 5) === "admin" && !self::sessionAdminValide()) {
                                        continue;
                                }
                                $nRub = $r->countRubriques();
                                $s->setContenu_Cellule(1, $i, "." . HTML_lien($r->url, $r->nom));
                                for ($j = 0; $j < $nRub; $j++) {
                                        $s->setContenu_Cellule(2 + $j, $i, "..." . HTML_lien($r->rubriques[$j]->url, $r->rubriques[$j]->nom));
                                }
                        }
                        return $s->fin();
                }

                static $firefox_fix = array("valign" => "top");

                private function remplirTableaux() {
                        /* les tableaux d'entetes */
                        $date = getdate(time());
                        $date = $date["weekday"] . ", " . $date["month"] . " " . $date["mday"] . " " . $date["year"];

                        /** afficher lien ou cellule vide ($i /= $n * 2) */
                        $this->tbl["entete_ouest"]->setContenu_Cellule(1, 0, $this->menu->getRubriqueOuverte(), array("class" => "entete_sud_ouest mobile"));

                        if ($this->isEnteteLogoEnabled()) {
                                $this->tbl["entete_ouest"]->setContenu_Cellule(0, 0, $this->getInfoFlashPhoto(true), array("class" => "slideshow"));
                        } else {
                                $this->tbl["entete_ouest"]->setContenu_Cellule(0, 0, $date);
                        }
                        $this->tbl["entete"]->setContenu_Cellule(0, 0, $this->tbl["entete_ouest"]->fin(TBL_DIV, array("","","div")), array("class" => "entete_ouest"));

                        if ($this->isEnteteLogoEnabled()) {
                                $this->tbl["entete_est"]->setContenu_Cellule(0, 0,
                                        HTML_lien($this->r->sitemap['e13__index'],
                                        HTML_image($this->r->sitemap['images__logo_full'])),
                                        array("class" => "badge"));
                        }
                        $this->tbl["entete_est"]->setContenu_Cellule(1, 0,  $this->proprietes["titre"], array("class" => "titre"));
                        $this->tbl["entete_est"]->setContenu_Cellule(2, 0,  $this->choixLang(), array("class" => "lang"));

                        $this->tbl["entete"]->setContenu_Cellule(0, 1, $this->tbl["entete_est"]->fin(), array("class" => "entete_est"));

                        /* affichage menu ">16/9" */
                        $this->tbl["contenu_ouest"]->setContenu_Cellule(0, 0, $this->getInfoFlash(), array("class" => "zoom flash masque"));
                        $this->tbl["contenu_ouest"]->setContenu_Cellule(1, 0, $this->menu->getHTML());
                        $this->tbl["contenu_ouest"]->setContenu_Cellule(2, 0, $this->getAdElement("Adsense/search"), array("class" => "search masque"));
                        $menu_html = $this->tbl["contenu_ouest"]->fin();
                        $this->tbl["contenu"]->setContenu_Cellule(0, 0, $menu_html, array("html" => self::$firefox_fix, "class" => "contenu_ouest masque"));
                        /** menu "<4/3" qui slide avec le script slide.js */
                        $this->tbl["contenu_ouest"]->id = "mySwipe";
                        $this->tbl["contenu_ouest"]->setOptionsArray(array("class" => "slide mobile"));
                        $menu_html_mobile = $this->tbl["contenu_ouest"]->fin();

                        /** "contenu_est" affiche dans $this->tbl["contenu"] 0,1 */
                        $this->tbl["contenu"]->setContenu_Cellule(0, 1, $this->tbl["contenu_est"]->fin(), array("class" => "contenu_est"));

                        /** "contenu" affiche dans $this->tbl["body"] */
                        $this->tbl["contenu"]->setOptionsArray(array("html" => self::$firefox_fix));

                        /* remplir page body */

                        /** remplit la slider bar et positionne le menu cache qui slide */
                        $l = 0;
                        $r = 1;
                        $this->tbl["body"]->setContenu_Cellule(0, $l, "<div class='sliderBar'>" . $this->menu->getBoutonOuvrirRubriques("mySwipe") . $this->getAdElement("AdSense/search") . "</div>" . "<br>" . $menu_html_mobile . "", array("html" => array_merge(self::$firefox_fix, array("rowspan" => 5))));

                        $this->tbl["body"]->setContenu_Cellule(0, $r, $this->tbl["entete"]->fin(TBL_DIV, array("div","","div")), array("class" => "entete"));
                        $this->tbl["body"]->setContenu_Cellule(1, $r, $this->getAdElement("Adsense/banner"), array("class" => "adsense masque"));
                        $this->tbl["body"]->setContenu_Cellule(2, $r, $this->tbl["contenu"]->fin(), array("html" => self::$firefox_fix, "class" => "contenu"));
                        $this->tbl["body"]->setContenu_Cellule(3, $r, $this->siteMap(), array("class" => "sitemap"));
                        $this->tbl["body"]->setContenu_Cellule(4, $r, $this->piedDePage(), array("class" => "copyright"));
                }

                  /**
                  * @return toutes les dimensions dans l'ordre des indices ['w'=>[..],'h'=>[..]]
                  */
                  function __getInfoImageSize(SQL &$sql, Info $ifo) {
                        $taille = array("w"=>array(0),"h"=>array(0));
                        for ($i = 0; $i < count($ifo->images); $i++) {
                                $img = $ifo->getImage($sql, $i);
                                $taille["w"][] = $img->getWidth();
                                $taille["h"][] = $img->getHeight();
                        }
                        return $taille;
                  }
                  /**
                   * @return string dernier post publie en langue locale
                   *                  */
                  private function __getInfoFlashInfo(SQL &$sql) {
                          $lastinfo = new Info($sql, $null, $this->r->lang("titre_dsc", "infos"), "staff", $this->r->lang("contenu_dsc", "infos"));
                          if ($sql->connect_succes()) {
                                  $dateSelect = " AND published <= CURDATE() ";
                                  $infos = $sql->query("SELECT * FROM info WHERE langue ='" . getPrimaryLanguage() . "'" . $dateSelect . " ORDER BY published DESC LIMIT 1");
                                  // deplace le curseur a� la derniere ligne
                                  if ($sql->select_succes($infos) && $sql->selectLigne($infos, 0)) {
                                          $lastinfo = new Info($sql, $infos);
                                          mysqli_free_result($infos);
                                  }
                          }
                          return $lastinfo;
                  }

/* ------------------------------------------------- PARTIE PUBLIQUE */

                /**
                 * @param bool $slider active un slider photos
                 * @return string balise de la premiere photo du dernier post  dernier post photo publie en langue locale
                 */
                function getInfoFlashPhoto($slider = false) {
                        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
                        $lastinfo = $this->__getInfoFlashInfo($sql);
                        $html = "";
                        $html = $lastinfo->tableauImages($sql, TBL_DIV);
                        /* recuperer la taille de l'image la plus grande */
                        $taille = $this->__getInfoImageSize($sql, $lastinfo);
                        if($slider) {
                              /*
                               element cakephp View/Elements/E14/slideshow.jq.ctp
                                dimensionnement en fonction de la plus grande image
                              */
                              $slider = $this->r->view->element("E14/slideshow.jq",
                                      array("photos" => $html,
                                            "maxWidth" => max($taille["w"]),
                                            "maxHeight" => max($taille["h"])),
                                      array('cache' => array('config' => 'short', 'key' => 'unique value')));
                              return $slider;
                        } else if(count($lastinfo->images) != 0) {
                              return $lastinfo->getImageAsTag($sql, 0, TBL_DIV);
                        } else {
                              return $html;
                        }
                }
                /**
                  * affiche le post le plus recent avec son contenu tronque 160 caracteres
                 */
                function getInfoFlash() {
                        return $this->r->view->Info->getInfoFlashN();
                }

                function setTitre($s) {
                        $this->proprietes["titre"] = $s;
                }

                function getTitre() {
                        return $this->proprietes["titre"];
                }

                function enableAds() {
                        $this->proprietes["ads"] = TRUE;
                }

                function disableAds() {
                        $this->proprietes["ads"] = FALSE;
                }

                function setURL($url) {
                        $this->proprietes["URL"] = $url;
                }

                function getURL() {
                        return $this->proprietes["URL"];
                }

                function is_secured() { // test page securisee
                        if ($this->securite == 0)
                                return FALSE;
                        else
                                return TRUE;
                }

                /* AJOUTER DU CONTENU HTML A LA PAGE */

                function ajouterContenu($s, $class = "corps") { // $s est un contenu HTML (ex. texte formate HTML)
                        $this->proprietes["contenu"] .= "<div class='$class'>" . $s . "</div>";
                        $this->tbl["contenu_est"]->setContenu_Cellule(0, 0, $this->proprietes["contenu"]);
                }

                /* AJOUTER un message sur la page (css .console) */

                function ajouterMessage($s) {
                        $this->ajouterContenu($s, 'console');
                }

                /** charge du contenu commme element de vue (View/Elements) et le retourne en chaine de caractere
                  si enableAdsense actif */
                function getAdElement($script) {
                        if ($this->proprietes["ads"]) {
                                return $this->r->view->element($script, array(), array("cache"=>array('config' => 'short',
                                    'key' => 'unique value')));
                        } else {
                                $html = "";
                        }
                        return $html;
                }

                /**
                 * ecrit tout le contenu du buffer (ex. les E_* erreurs levees) vers un message html
                 * @param boolean $close declenche tout le buffer si "true" ou relance un buffer apres vidage si "false".
                 */
                function flushBuffer($close = false) {
                        /*                         * ***** recuperation du buffer ********* */
                        while (ob_get_level() > 0) {
                                $this->ajouterMessage(BUF_OFF ? "_ob off_" : ob_get_clean());
                        }
                        if (!$close && !BUF_OFF) {
                                ob_start("mb_output_handler");
                        }
                }

                /** doit se situer apres l'appel constructeur
                  * @param array $options array('javascript' => array('option' => 'valeur'), 'class'=>'bodyclasse1 bodyclasse2')
                  * @param array $scriptCake un script, une stylesheet pour cakephp, tout ce qui peut etre mis en entete
                 */
                function entete($scriptCake = "<script></script>", $options = array("class" => "body")) {
                        /* definition du langage machine (ession active => lang utilisateur sauvegardee) */
                        if (filter_input(INPUT_GET, 'lang')) {
                                $_SESSION["lang"] = filter_input(INPUT_GET, 'lang');
                        } elseif (!array_key_exists('lang', $_SESSION)) {
                                $l = getPrimaryLanguage();
                                $_SESSION["lang"] = $l;
                        } else {
                                $l = $_SESSION['lang'];
                        }
                        /* Entete HTML */
                        $this->HTML = $this->r->view->Html->docType()."\n";
                        $this->HTML .= "<html>\n";
                        $this->HTML .= "<head>\n";
                        $this->headers(array("title" => $this->r->lang("siteTitle") . " - " . $this->proprietes['titre'],
                                "link" => array(
                                        "href" => $this->r->sitemap['etc__stylesheet'],
                                        "rel" => "stylesheet",
                                        "type" => "text/css"),
                                "meta" => array(
                                        "name" => "viewport",
                                        "content" => "width=device-width, initial-scale=1"
                                )));
                        $this->HTML .= $scriptCake;
                        $this->HTML .= $this->r->view->Html->charset();
                        $this->HTML .= "</head>\n";
                        $o = optionsArrayToHTML($options, TRUE);
                        $this->HTML .= "<body " . $o . ">\n";
                        /* Ancre pour remonter au haut de chaque page */
                        $this->HTML .= "<!-- Ancre de haut de page #top //-->\n";
                        $this->HTML .= "<div align='center'><a name=\"top\"></a></div>\n";
                        /* evite les doublons*/
                        $this->proprietes['headSent'] = TRUE;
                }

                /**
                 * Pour terminer une page
                 * @param int $mode 0 retourne le code HTML, 1 to output vers le browser (par defaut) et appelle exit;
                 */
                function fin($mode = 1) {
                        if ($this->proprietes['headSent'] === FALSE) {
                                $this->entete();
                        }
                        $this->ajouterContenu("<br><div align='right'><a href='#top'>top&nbsp;&nbsp;</a></div>");
                        $this->flushBuffer(true);
                        $this->remplirTableaux();
                        $this->HTML .=  $this->tbl["body"]->fin();
                        $this->HTML .= "</body>\n</html>\n";

                        switch ($mode) {
                                case 0:
                                        return $this->HTML;
                                case 1:
                                        $this->writeHTML();
                                        $this->HTML = "";
                                        break;
                                default:break;
                        }
                        exit;
                }

                /**
                 * d�sactive l'entete avec logo et images, pour all�ger la page (admin)
                 * */
                function setEnteteLogoEnabled($b) {
                        $this->proprietes['enteteLogo'] = $b;
                }

                function isEnteteLogoEnabled() {
                        return $this->proprietes['enteteLogo'];
                }

                function get_dir_files($dir = ".") {
                        $handle = opendir($dir);
                        $file_list;
                        $i = 0;
                        while (false !== ($file = readdir($handle))) {
                                if (($file != ".") && ($file != "..")) {
                                        $file_list[$i] = $file;
                                        $i++;
                                }
                        }
                        closedir($handle);
                        return $file_list;
                }

                protected final static function valide($mdpmd5) {
                        return crypt($mdpmd5, PASSWORD_ADMIN) === PASSWORD_ADMIN;
                }

                public final static function sessionAdminValide() {
                        return (filter_session('client') && array_key_exists('mdp', $_SESSION['client'])) ? self::valide($_SESSION['client']["mdp"]) : false;
                }
        }
}
?>
