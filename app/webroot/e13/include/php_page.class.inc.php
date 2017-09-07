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
global $ClassePage;
if (!isset($ClassePage)) {
        $ClassePage = 1;

        // Classe gérant toutes les pages php du site
        require ($GLOBALS['include__php_module_html.inc']);
        require ($GLOBALS["include__php_tbl.class.inc"]);
        require ($GLOBALS["include__php_menu.class.inc"]);
        require ($GLOBALS['include__php_info.class.inc']);
        require ($GLOBALS['include__php_SQL.class.inc']);
        require ($GLOBALS['include__php_image.class.inc']);
        require ($GLOBALS['include__php_formulaire.class.inc']);
        require ($GLOBALS['include__php_constantes.inc']);
        require ($GLOBALS["include__php_module_DVD.inc"]);
        require ($GLOBALS["include__php_module_locale.inc"]);
        require ($GLOBALS["include__php_captcha.class.inc"]);

        define("BUF_OFF", false);

        /*
         * 
          @class		Page
          @abstract   Elle definit les pages HTML dans leur globalité. Le code HTML est écrit sur la sortie à l'appel de la methode fin(), en dernier donc. un tableau associatif $proprietes contient les proprietes de la page que l'on peut modifier. | MENU | une variable d'instance $menu contient un objet de classe Menu qui s'affiche avec la rubrique concernant la *page en cours* mise en evidence. on peut a tout moment retourner a une rubrique hierachiquement superieure. | CONTENU | un tableau php associatif rassemble le contenu de la page, $this->tbl. le contenu de la page est formaté à l'aide de tableaux HTML (classe php Tableau). | SITUATION-HISTORIQUE | un des tableau HTML contient le nom de la *page en cours* ainsi que son chemin virtuel depuis la page racine du site (situation). Le chemin (par ex. Home/Admin/Gestion Infos/_) affiché depend de la hierarchie du Menu de la Page, ordinairement correspondant au plan du site. | LOGO-ONGLETS | une barre d'onglets donne acces aux rubriques principales du site, affichee sous le logo. Elle est dynamique et permet | ADMIN_PAGE | cette sous-classe étend les possibilités de Page vers une page verouillée par un accès réservé aux administrateurs. voir class ADMIN_Page (plus bas...)
          @discussion | MENU | possibilité de développer l'affichage du menu de la page en DHTML pour un design et un espace d'affichage dans le tableau[contenu] améliorés. | LOGO-ONGLETS | a terminer graphiquement sous photoshop et a integrer dans la page pour avoir une synchronisation avec le menu.
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
                  @param pgnews boolean pour afficher ou non les news en marge de la page
                 * @param String $localeKey clé vers le fichier locale/content-lang
                 */
                function __construct($r, $sitemapKey, $pgnews = false, $sessionId = NULL, $enc = MYENCODING, $adsense = true) {
// ********* output buffer ********
                        mb_http_output($enc);
                        mb_internal_encoding("ISO-8859-15");
                        if (!BUF_OFF) {
                                ob_start("mb_output_handler");
                        }
                        $this->r = $r;
                        global $MENU;
                        Menu::creerMenuGlobals($this->r->menu_ini, $MENU);
                        // ********* Entetes HTTP *********
                        header("Default-Charset: $enc");
                        header("Content-Type: text/html");

                        // ********************************
                        // ----------------- PROPRIETES ------------------
                        $this->proprietes = array("titre" => $this->r->lang(MENU::versLangLocalized($sitemapKey, $this->r->menu_ini)),
                            "pgnews" => $pgnews, // TRUE ou FALSE
                            "URL" => $MENU[$sitemapKey]->url,
                            "contenu" => "",
                            "securite" => 0,
                            "sessionId" => $sessionId,
                            "adsense" => $adsense,
                            "enteteLogo" => TRUE);
                        // -----------------------------------------------
                        // Jeu de tableaux pour la mise en page
                        $this->tbl["body"] = new Tableau(5, 1, "body"); /* la page entiere, contiendra tous les autres tableaux.
                          cellule 1,0: affichage de l'emplacement actuel du navigateur sur le site */
                        $this->tbl["menu"] = new Tableau(3, 1, "menu"); // le menu
                        $this->tbl["contenu"] = new Tableau(1, 1, "contenu"); // le contenu informel de la page; le contenu de ce tableau correspond au contenu de $proprietes["contenu"] est modifie par la fonction ajouterContenu
                        $this->tbl["entete"] = new Tableau(1, 2, "entete"); // l'entete avec le logo et la date/heure
                        $this->tbl["logo_onglets"] = new Tableau(2, 1, "logo_onglets"); // le logo et dessous les onglets
                        $this->tbl["onglets"] = new Tableau(1, 11, "onglets"); // les onglets séparés

                        /*                         * *************************************
                          réglages apparences de chaque tableau */

                        $this->tbl["body"]->options = array("HTML" => array("BORDER" => 3,
                                "ALIGN" => "CENTER",
                                "CELLPADDING" => 0,
                                "CELLSPACING" => 0),
                            "css" => array("font-family" => "Geneva"),
                            "class" => "body"
                        );
                        // cellule emplacement sur le site
                        $this->tbl["body"]->setOptionsArray_Cellule(1, 0, array("css" => array("color" => "black",
                                "background-color" => "white")
                                )
                        );

                        $this->tbl["menu"]->options = array("HTML" => array("WIDTH" => "50",
                                "BORDER" => 1,
                                "ALIGN" => "LEFT",
                                "CELLPADDING" => 5,
                                "CELLSPACING" => 0),
                            "class" => "menu");
                        $this->tbl["contenu"]->options = array("HTML" => array("BORDER" => 1,
                                "ALIGN" => "LEFT",
                                "CELLPADDING" => 5,
                                "CELLSPACING" => 5),
                            "class" => "contenu"
                        );

                        $this->tbl["entete"]->options = array("HTML" => array("WIDTH" => "100%",
                                "BORDER" => 0,
                                "ALIGN" => "RIGHT",
                                "CELLPADDING" => 0,
                                "CELLSPACING" => 0)
                        );
                        // cellule date/heure/titre
                        $this->tbl["entete"]->setOptionsArray_Cellule(0, 1, array("class" => "entete_titre",
                            "HTML" => array("WIDTH" => "100%")
                                )
                        );
                        $this->tbl["logo_onglets"]->options = array("HTML" => array("BORDER" => 0,
                                "ALIGN" => "left",
                                "CELLPADDING" => 0,
                                "CELLSPACING" => 0,
                                "WIDTH" => "100%"));
                        // cellule logo
                        $this->tbl["logo_onglets"]->setOptionsArray_Cellule(0, 0, array("HTML" => array("align" => "right",
                                "CELLPADDING" => 0)));
                        $this->tbl["onglets"]->options = array("HTML" => array("HEIGHT" => "100%", "BORDER" => 0, "ALIGN" => "RIGHT", "CELLPADDING" => 0, "CELLSPACING" => 0)
                        );

                        $this->menu = $MENU['root__index'];
                        $this->menu->ouvrirBonneRubrique($this->getURL());

                        /*                         * ****************** */

                        /* google adsense */
                        if ($adsense)
                                $this->enableAdSense();
                        else
                                $this->disableAdsense();
                        if ($pgnews)
                                $this->enablePgnews();
                        else
                                $this->disablePgnews();


                        /*                         * ******* output starts HERE ************* */
                        $this->entete();
                }

                /* ------------------------------------------------- PARTIE PRIVEE */

                /* Fonction pour ecrire le code HTML en attente d'ecriture en memoire;
                  stockage dans allHTML pour une evtl. reutilisation future
                 */

                function writeHTML() {

                        /* write */
                        echo stripslashes($this->HTML);
                        $this->allHTML .= $this->HTML;
                        $this->HTML = NULL;
                }

                function setStyles() {
                        
                }

                function afficheOnglets() {
                        die("methode instable Page.afficheOnglets()");
                        $n = 0; // quel onglet doit apparaitre presse? $emplacement, "Accueil" ... rubrique corrspondante au numéro de l'onglet
                        $emplacement = $this->menu->getRubriqueOuverte();
                        if (strstr($emplacement->nom, $MENU['e13__index']->nom))
                                $n = 1;
                        if (strstr($emplacement->nom, $MENU['e13__index']->nom))
                                $n = 2;
                        if (strstr($emplacement->nom, $MENU['e13__activites']->nom))
                                $n = 3;
                        if (strstr($emplacement->nom, $MENU['e13__contacts']->nom))
                                $n = 4;
                        if (strstr($emplacement->nom, $MENU['e13__blog']->nom))
                                $n = 5;

                        /**
                         * tableau html pour les onglets
                         */
                        for ($i = 1; $i < 12; $i++) {
                                $lien_image = "/";
                                if ($i == 2)
                                        $lien_image = HTML_lien($MENU['e13__index']->url, $MENU['e13__index']->nom);
                                if ($i == 4)
                                        $lien_image = HTML_lien($MENU['e13__infos']->url, $MENU['e13__infos']->nom);
                                if ($i == 6)
                                        $lien_image = HTML_lien($MENU['e13__activites']->url, $MENU['e13__activtes']->nom);
                                if ($i == 8)
                                        $lien_image = HTML_lien($MENU['e13__contacts']->url, $MENU['e13__contacts']->nom);
                                if ($i == 10)
                                        $lien_image = HTML_lien($MENU['e13__blog']->url, $MENU['e13__blog']->nom);
                                /* les images sont nommees onglets-$i.jpg OU onglets.presse-$n.$i.jpg */
                                if ($n != 0)
                                        $presse = ".presse-$n.";
                                else
                                        $presse = "-";
                                if ($i == $n * 2)
                                        $lien_image = HTML_image($MENU["onglets" . $presse . $i . ".jpg"]);
                                /** afficher lien ou cellule vide ($i /= $n * 2) */
                                $this->tbl["onglets"]->setContenu_Cellule(0, ($i - 1), $lien_image);
                        }
                }

                function entete($options = array()) {
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
                        $this->HTML = "<html>\n";
                        $this->HTML .= "<head><title>" . $this->r->lang("siteTitle") . " " . $this->proprietes['titre'] . " ::</title>\n";
                        $this->setStyles();
                        $this->HTML .= "<LINK REL=stylesheet HREF=\"" . $GLOBALS['etc__stylesheet.css'] . "\" TYPE=\"text/css\">";
                        $this->HTML .= "</head>\n";
                        $o = optionsArrayToHTML($options);
                        $this->HTML .= "<body " . $o['javascript'] . ">\n";
                        /* Ancre pour remonter au haut de chaque page */
                        $this->HTML .= "<!-- Ancre de haut de page #top //-->\n";
                        $this->HTML .= "<div align='center'><a name=\"top\"></a></div>\n";
                        $this->HTML .= $this->choixLang("center");
                }

                function choixLang($align = "") {
                        /** choix lang */
                        $HTML = "<div class='lang' align='$align'>";
                        $o = array("HTML" => array("WIDTH" => "16"));
                        $HTML .= HTML_lien($this->getURL() . "?lang=" . EN, HTML_image($GLOBALS["images__uk"], $o));
                        $HTML .= HTML_lien($this->getURL() . "?lang=" . FR, HTML_image($GLOBALS["images__fr"], $o));
                        $HTML .= HTML_lien($this->getURL() . "?lang=" . DE, HTML_image($GLOBALS["images__de"], $o));
                        $HTML .= "</div>\n";
                        return $HTML;
                }

                function piedDePage() {
                        $t = new Tableau(1, 3, "copyright");
                        $t->setOptionsArray(array("HTML" => array("WIDTH" => "100%",
                                "class" => "copyright")));

                        $t->setContenu_Cellule(0, 0, filter_input(INPUT_SERVER, 'SERVER_SIGNATURE'), array("css" => array("text-align" => "left")));
                        $t->setContenu_Cellule(0, 1, "&copy; " . $this->r->lang("copyYear") . " " . filter_input(INPUT_SERVER, 'SERVER_NAME'), array("css" => array("text-align" => "center")));
                        $t->setContenu_Cellule(0, 2, HTML_lien("mailto:" . $this->r->lang("mailto"), $this->r->lang("mailtoLabel")), array("css" => array("text-align" => "right")));
                        return $t->fin();
                }

                function siteMap() {
                        $nSub = 0;
                        for ($i = 0; $i < $this->menu->countRubriques(); $i++) {
                                $n = $this->menu->rubriques[$i]->countRubriques();
                                if ($n > $nSub)
                                        $nSub = $n;
                        }
                        $s = new Tableau($nSub + 2, $this->menu->countRubriques(), "sitemap");
                        $s->setOptionsArray(array("HTML" => array("BORDER" => "0", "WIDTH" => "100%"), "class" => "sitemap"));
                        $s->setContenu_Cellule(0, 0, ".::::::::: " . $this->r->lang("sitemap") . " :::::::::.", array("HTML" => array("ALIGN" => "center", "COLSPAN" => $s->nbColonnes)));

                        for ($i = 0; $i < $s->nbColonnes; $i++) {
                                $r = $this->menu->rubriques[$i];
                                // n'affiche pas la section administration
                                if (substr($r->nomBnd, 0, 5) === "admin" && !ADMIN_Page::sessionAdminValide()) {
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

                function remplirTableaux() {
                        $date = getdate(time());
                        $date = $date["weekday"] . ", " . $date["month"] . " " . $date["mday"] . " " . $date["year"];
                        /* les onglets */
                        //$this->afficheOnglets();
                        if ($this->isEnteteLogoEnabled()) { 
                                $this->tbl["logo_onglets"]->setContenu_Cellule(0, 0, HTML_image($GLOBALS['images__logo-sur-fond.logo.jpg'], array("HTML" => array("ALT" => filter_input(INPUT_SERVER, 'SERVER_NAME')), "class" => "shrink logo")));
                        } else {
                               $this->tbl["logo_onglets"]->setContenu_Cellule(0, 0, $date);
                        }
                        $this->tbl["logo_onglets"]->setContenu_Cellule(1, 0, $this->tbl["onglets"]->fin());
                        $this->tbl["entete"]->setContenu_Cellule(0, 0, $this->tbl["logo_onglets"]->fin());
                        
                        if ($this->isEnteteLogoEnabled()) {
                                $this->tbl["entete"]->setContenu_Cellule(0, 1, "<div class='page_titre'>" . $this->proprietes["titre"] . "</div>" . HTML_lien($GLOBALS['e13__index'], HTML_image($GLOBALS['images__logo_full.png'], array("HTML" => array("ALIGN" => "RIGHT"), "class" => "badge"))));
                        } else {
                                $this->tbl["entete"]->setContenu_Cellule(0, 1, "<H1>" . $this->proprietes["titre"] . "</h1>" . HTML_lien($GLOBALS['e13__index'], $this->r->lang("siteTitle")));
                        }
                        // affichage menu
                        $this->tbl["menu"]->setContenu_Cellule(0, 0, $this->menu->getBoutonOuvrirRubriques("siteName") . $this->menu->getHTML(), array("class" => "menu_contents"));
                        $this->tbl["menu"]->setContenu_Cellule(1, 0, $this->adSense($GLOBALS['include__AdSense_search']), array("class" => "search",
                            "css" => array("background-color" => "white")));
                        $this->setInfo($this->getInfoFlash());
                        $this->tbl["body"]->setContenu_Cellule(0, 0, $this->tbl["entete"]->fin());
                        $this->tbl["body"]->setContenu_Cellule(1, 0, "<table width=\"100%\"><tr><td width=0><div class='rubriquebanniere'>" . $this->r->lang("vousetesici") . " : " . $this->menu->getRubriqueOuverte() . "</div></td><td width=\"100%\"><div class=\"adsense\" style='text-align:right;font-style:italic'>" . $this->adSense($GLOBALS['include__AdSense_banniere']) . "</div></td></tr></table>", array("class" => "rubrique"));
                        $this->tbl["body"]->setContenu_Cellule(2, 0, "<TABLE><TR VALIGN='TOP'><TD>" . $this->tbl["menu"]->fin() . "</TD><TD><TABLE BORDER=0 CELLPADDING=2>" . $this->tbl["contenu"]->fin() . "</TABLE></TD></TR></TABLE>");
                        $this->tbl["body"]->setContenu_Cellule(3, 0, $this->piedDePage());
                        $this->tbl["body"]->setContenu_Cellule(4, 0, $this->siteMap());
                }

                function getInfoFlash() {
                        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
                        $infos = $sql->query("SELECT * FROM info WHERE langue ='" . getPrimaryLanguage() . "' ORDER BY date DESC LIMIT 1");
                        // déplace le curseur à la derniere ligne
                        if ($sql->selectLigne($infos, mysqli_num_rows($infos) - 1)) {
                                $lastInfo = new Info($sql, $infos);
                        } else {
                                $lastInfo = NULL;
                        }
                        mysqli_free_result($infos);
                        return $lastInfo;
                }

                function setInfo($info) {
                        if ($info) {
                                $lang = $info->getLangue();
                                $this->tbl["menu"]->setContenu_Cellule(2, 0, HTML_lien($GLOBALS["e13__index"],"<div class='info_flash'>" . $info->getDate() . " - " . $info->getTitre() . " : " . $info->getContenu($lang)."</div>"));
                        }
                }

                /* ------------------------------------------------- PARTIE PUBLIQUE */

                function setTitre($s) {
                        $this->proprietes["titre"] = $s;
                }

                function getTitre() {
                        return $this->proprietes["titre"];
                }

                function enablePgnews() {
                        $this->proprietes["pgnews"] = TRUE;
                }

                function disablePgnews() {
                        $this->proprietes["pgnews"] = FALSE;
                }

                function enableAdSense() {
                        $this->proprietes["adsense"] = TRUE;
                }

                function disableAdSense() {
                        $this->proprietes["adsense"] = FALSE;
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

                function ajouterContenu($s) { // $s est un contenu HTML (ex. texte formate HTML)
                        $this->proprietes["contenu"] .= $s;
                        $this->tbl["contenu"]->setContenu_Cellule(0, 0, $this->proprietes["contenu"]);
                }

                function ajouterMessage($s) {
                        $this->ajouterContenu("<div class='console'>" . $s . "</div>");
                }

                function adSense($script) {
                        if ($this->proprietes["adsense"] && !array_key_exists('local', $_SESSION)) {
                                ob_start("mb_output_handler");
                                include($script);
                                $html = ob_get_contents();
                                ob_end_clean();
                        } else {
                                $html = "";
                        }
                        return $html;
                }

                function pgnews($script) {
                        if ($this->proprietes["pgnews"]) {
                                ob_start("mb_output_handler");
                                include($script);
                                $html = ob_get_contents();
                                ob_end_clean();
                        } else
                                $html = "";
                        return $html;
                }

                /**
                 * écrit tout le contenu du buffer (ex. les E_* erreurs levées) vers un message html
                 * @param boolean $close déclenche tout le buffer si "true" ou relance un buffer apres vidage si "false".
                 */
                function flushBuffer($close = false) {
                        /*                         * ***** recuperation du buffer ********* */
                        while (ob_get_level() > 0) {
                                $this->ajouterMessage(BUF_OFF ? "_ob off_" : @ob_get_clean());
                        }
                        if (!$close && !BUF_OFF) {
                                ob_start("mb_output_handler");
                        }
                }

                /**
                 * Pour terminer une page 
                 * @param int $mode 0 retourne le code HTML, 1 to output vers le browser (par defaut)
                 */
                function fin($mode = 1) {
                        $this->ajouterContenu("<br><div align='right'><a href='#top'>^top^</a></div>");
                        $this->flushBuffer(true);
                        $this->remplirTableaux();
                        $this->HTML .= $this->tbl["body"]->fin();
                        $this->HTML .= "</body></html>\n";

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
                  d�sactive l'entete avec logo et images, pour all�ger la page (admin)
                 * */
                function setEnteteLogoEnabled($b) {
                        $this->proprietes['enteteLogo'] = $b;
                }

                function isEnteteLogoEnabled() {
                        return $this->proprietes['enteteLogo'];
                }

                function get_dir_files($dir = ".") {
                        $handle = opendir($dir);
                        global $file_list;
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

        }

        /* page en mode administration */

        class ADMIN_Page extends Page {

                var $mdp; // mode passe pour $this->user ($user hérité de Page...)

                function __construct($r, $sitemapKey, $sessionId) {

                        parent::__construct($r, $sitemapKey, FALSE, $sessionId);
                        $this->setEnteteLogoEnabled(false);
                        $this->user = "staff";
                        /* mdp de la page ADMIN look include/getHashPassword.php */
                        $this->mdp = PASSWORD_ADMIN;
                        echo $this->r->lang("login", "admin");
                        /** nothing set in session and no captcha */
                                $captcha = "";
                        if (!Captcha::verification($this, $captcha) && (!array_key_exists('client', $_SESSION) || !array_key_exists('mdp', $_SESSION['client']))) {                                        
                                $this->ajouterContenu("<br><br><center><b>" . $this->r->lang("section", "admin") . "</b></center><br>" . $this->r->lang("loginsvp", "admin") ."<br>".$captcha);
                                $this->form_authentification($this->getURL());
                                $this->fin();
                        } else {
                                $mdpmd5 = filter_input(INPUT_POST, 'motdepasse') ? md5(filter_input(INPUT_POST, 'motdepasse')) : $_SESSION['client']['mdp'];
                                $this->user = filter_input(INPUT_POST, 'identifiant') ? filter_input(INPUT_POST, 'identifiant') : $_SESSION['client']['id'];
                                echo $this->r->lang("enregistrementsession", "admin");                                      
                                /** utilise le salt de PASSWORD_ADMIN pour crypter motdepasse, et donc retourne le meme hashcode si le
                                 * motdepasse est le meme que celui enregistré par PASSWORD_ADMIN  */
                                if (ADMIN_Page::valide($mdpmd5)) {
                                        /** la safeSession est activée (voir classe formulaire et balise FORM), qui desactive les formulaires si non activee */
                                        $_SESSION["client"]['id'] = $this->user;
                                        $_SESSION["client"]['mdp'] = $mdpmd5;
                                        $this->ajouterContenu("<br><br><center><b>" . $this->r->lang("section", "admin") . "</b></center><br>" . $this->r->lang("vousetesauthentifie", "admin") . " : " . $this->user);
                                } else {
                                        $this->ajouterContenu("<br><br><center><b>" . $this->r->lang("section", "admin") . "</b></center><br>$this->user/:>" . $this->r->lang("erreurauth", "admin") . (filter_input(INPUT_POST, 'motdepasse') != filter_input(INPUT_POST, 'motdepasse_confirm') ? $this->r->lang("confirmermdp", "admin") : $this->r->lang("confirmerutilisateur", "admin")));
                                        $this->form_authentification($this->getURL());
                                        $this->fin();
                                }
                        }
                        echo $this->r->lang("succes", "admin");
                        $this->menu->ouvrirBonneRubrique($this->getURL());
                }

                private final static function valide($mdpmd5) {
                        return crypt($mdpmd5, PASSWORD_ADMIN) === PASSWORD_ADMIN;
                }

                final static function sessionAdminValide() {
                        return (array_key_exists('client', $_SESSION) && array_key_exists('mdp', $_SESSION['client'])) ? ADMIN_Page::valide($_SESSION['client']["mdp"]) : false;
                }

                /* ----- partie privée ----- */

                function form_authentification($script) {
                        echo "formulaire";
                        $form_auth = new Formulaire("Authentification", $script, VERTICAL, "formulaire");
                        // champs
                        $user = new ChampTexte("identifiant", $this->r->lang("utilisateur", "form"), $this->r->lang("entrerutilisateur", "form"), 10, 20, $this->user, TRUE);
                        $form_auth->ajouterchamp($user);
                        $user_mdp = new ChampPassword("motdepasse", $this->r->lang("mdp", "form"), $this->r->lang("entrermdp", "form"), 10);
                        $form_auth->ajouterchamp($user_mdp);
                        $captcha = new ChampCaptcha(5);
                        $form_auth->ajouterChamp($captcha);
                        $valider = new ChampValider("OK");
                        $form_auth->ajouterChamp($valider);
                        $effacer = new ChampEffacer("Effacer");
                        $form_auth->ajouterChamp($effacer);
                        // fin formulaire
                        $this->ajouterContenu($form_auth->fin());
                }

                /* ----- partie publique ----- */
        }

}
?>
