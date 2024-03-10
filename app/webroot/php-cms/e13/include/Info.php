<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:41:43 CEST 2004 @612 /Internet Time/
  @filename	php_info.class.inc
 */

if (!isset($_ENV['ClasseInfo'])) {
        $_ENV['ClasseInfo'] = 1;
        ${__FILE__} = new Index();
        include basename(${__FILE__}->r['include__php_image.class.inc']);
        include basename(${__FILE__}->r['include__php_module_cat.inc']);
        include basename(${__FILE__}->r['include__php_module_locale.inc']);

        class Info {

                protected static $_r;
                protected static function g_r(){  if(self::$_r === null) self::$_r = new Index(); return self::$_r; }
                var $r;
                var $titre, $date, $contenu, $auteur, $categorie, $published;
                var $images; // tableau d'urls des images (id ou url=>nom)
                var $langue; // langue de preference
                var $id; // id dans la base SQL

                /**
                 * @param SQL $sql prend la reference de connexion
                 * @param mysqli_result $infos l'objet d'un retour base de donn�es (ou initialiser une variable vide pour eviter une erreur fatale)
                 */

                public function __construct(SQL &$sql, mysqli_result &$infos = NULL, $t = NULL, $a = NULL, $c = NULL, $d = NULL, $l = NULL, $p = NULL) {
                        $this->r = self::g_r();
                        //init var
                        $this->langue = $l ? $l : getPrimaryLanguage();
                        $this->titre = array($this->langue => $t);
                        $this->date = $d;
                        $this->published = $p;
                        $this->categorie = $c;
                        $this->auteur = $a;
                        $this->images = array();
                        $this->contenu = array($this->langue => NULL);
                        if ($infos) {
                                $this->loadResult($sql, $infos);
                        }
                }

                function loadResult(SQL &$sql, mysqli_result &$result) {        // reception info de la base SQL
                        $dbInfo = $sql->ligneSuivante_Array($result);
                        if (!$dbInfo) {
                                return;
                        }
                        // appel fonction de la classe parente Info
                        $this->langue = $dbInfo["langue"];
                        $this->titre[$dbInfo["langue"]] = $dbInfo["titre"];
                        $this->auteur = $dbInfo["auteur"];
                        $this->categorie = $dbInfo["categorie"];
                        $this->date = $dbInfo["date"];
                        $this->published = $dbInfo["published"];
                        $this->id = $dbInfo["id"];
                        $this->contenu[$dbInfo["langue"]] = $dbInfo["contenu"];

                        // acquisition de la liste des images pour l'info
                        $image = strtok($dbInfo["images"], ",");
                        while ($image) {
                                $this->images[] = $image;
                                $image = strtok(",");
                        }
                }

                /* function convertEnc($str) {
                  return mb_convert_encoding($str, "UTF-8", "ISO-8859-1");
                  } */

                /* ----- methodes de classe "statiques"-----
                 * ------							   -----
                 * ------	partie priv�e			   ----- */

                /* formulaire pour ajouter une info dans la base SQL ou pour modifier avec $mode = "modifier" et $info contenant l'objet info a modifier
                  $pageScript devra g�rer les valeurs POSTees et notamment les images qui envoient leurs id (supprimer les anciennes dans la table image, avant.) et le champ FILE avec la nouvelle image. */

                private function formulaire_ajout($pageScript, SQL &$sql) {
                        $form = new Formulaire($this->r->lang("ajouter", "infos"), $pageScript);
                        // valeurs deja enregistree mais qui n'ont pas pu etre publiees
                        $i_titre[$this->langue] = filter_input(INPUT_POST, 'i_titre' . $this->langue);
                        $this->fm_ctitre($sql, $form, $i_titre);
                        $i_categorie = filter_input(INPUT_POST, 'i_categorie');
                        $this->fm_ccat($sql, $form, $i_categorie);
                        $i_contenu[$this->langue] = filter_input(INPUT_POST, 'i_contenu' . $this->langue);
                        $i_auteur = array_key_exists("client", $_SESSION) && array_key_exists("id", $_SESSION["client"]) ? $_SESSION["client"]['id'] : "";
                        $i_published = date('Y-m-d');
                        $this->fm_cinfo($sql, $form, $i_auteur, $i_contenu, $i_published);
                        $i_images = filter_input(INPUT_POST, 'i_images');
                        $this->fm_cimage($sql, $form, $i_images);
                        return $this->formulaire_fin($sql, $form, $this->r->lang("ajouter", "infos"));
                }

                private function formulaire_modif($pageScript, SQL &$sql) {
                        $form = new Formulaire($this->r->lang("modifier", "infos") . " id:" . $this->id, $pageScript);
                        $i_titre = $this->titre;
                        $this->fm_ctitre($sql, $form, $i_titre);
                        $i_categorie = $this->categorie;
                        $this->fm_ccat($sql, $form, $i_categorie);
                        $i_contenu = $this->contenu;
                        $i_auteur = $this->auteur;
                        $i_published = $this->published;
                        $this->fm_cinfo($sql, $form, $i_auteur, $i_contenu, $i_published);
                        $i_images = $this->images;
                        $this->fm_cimage($sql, $form, $i_images);
                        return $this->formulaire_fin($sql, $form, $this->r->lang("modifier", "infos"));
                }

                private function fm_ctitre(SQL &$sql, Formulaire &$form, $i_titre) {
                        foreach ($i_titre as $lang => $text) {
                                $info_titre = new ChampTexte('i_titre' . $lang, $this->r->lang("titre_lab", "infos"), $this->r->lang("titre_dsc", "infos"), "50", NULL, $text);
                                if ($lang != getPrimaryLanguage()) {
                                        $info_titre->desactiver();
                                }
                                $form->ajouterChamp($info_titre);
                        }
                }

                private function fm_ccat(SQL &$sql, Formulaire &$form, $i_categorie) {
                        $info_categorie = CAT_getSelect($sql, 'i_categorie', $this->r->lang("newcat_lab", "categories"), HTML_lien($this->r->sitemap['admin__cat'], $this->r->lang("modifier", "categories")), $i_categorie);
                        $form->ajouterChamp($info_categorie);
                }

                /* chaque image existante dans l'info, est affiche dans un groupe de champs checkbox; pour supprimer, decocher. pour ajouter, un champ FILE est ajout� plus bas dans le formulaire */

                private function fm_cimage(SQL &$sql, Formulaire &$form, $i_images) {
                        /* nouvelle image */
                        $info_image = new ChampFile('i_image', $this->r->lang("ajouter_lab", "images"), $this->r->lang("ajouter_dsc", "images") . " 800 kb", 800000);
                        $info_image_nom = new ChampTexte('i_image_nom', $this->r->lang("nom_lab", "images"), $this->r->lang("nom_dsc", "images"), 20);
                        $info_image_desc = new ChampAireTexte('i_image_desc', $this->r->lang("desc_lab", "images"), $this->r->lang("desc_dsc", "images"), 20, 3);
                        $info_image_mime = new ChampSelect("i_image_mime", $this->r->lang("mime_lab", "images"), $this->r->lang("mime_dsc", "images"), array("PNG" => "image/png", "JPEG" => "image/jpg", "GIF" => "image/gif"), 1, "image/jpg");
                        /** existant */
                        $champs_images = array();
                        for ($i = 0; $i < count($i_images); $i++) {
                                if (is_array($i_images[$i])) {
                                        continue;
                                }
                                $champs_images[$i] = new ChampCoche("i_images[]", $i_images[$i], "", "", TRUE);
                                $image = new Image();
                                $image->FromSQL($sql, $i_images[$i]);
                                $image->setWidth(90);
                                $champs_images[$i]->libelle = $image->afficher_db();
                        }
                        $grp = new ChampGroupe($this->r->lang("images_lab", "infos"), $this->r->lang("images_dsc", "infos"), "i_images[]", $champs_images);
                        $form->ajouterChamp($grp);
                        $form->ajouterChamp($info_image);
                        $form->ajouterChamp($info_image_nom);
                        $form->ajouterChamp($info_image_desc);
                        $form->ajouterChamp($info_image_mime);
                }

                private function fm_cinfo(SQL &$sql, Formulaire &$form, $i_auteur, $i_contenu, $i_published) {
                        foreach ($i_contenu as $lang => $text) {
                                $info_contenu = new ChampAireTexte('i_contenu' . $lang, $this->r->lang("contenu_lab", "infos"), $this->r->lang("contenu_dsc", "infos"), 30, 20, $text);
                                if ($lang !== getPrimaryLanguage()) {
                                        $info_contenu->desactiver();
                                }
                                $form->ajouterChamp($info_contenu);
                        }
                        $info_auteur = new ChampTexte('i_auteur', $this->r->lang("auteur_lab", "infos"), $this->r->lang("auteur_dsc", "infos"), 15, 20, $i_auteur);
                        $info_published = new ChampTexte("i_published", $this->r->lang("published_lab", "infos"), $this->r->lang("published_dsc", "infos"), 10, 10, $i_published);
                        $form->ajouterChamp($info_published);
                        $form->ajouterChamp($info_auteur);
                }

                private function formulaire_fin(SQL &$sql, Formulaire &$form, $texteValider) {
                        i_debug("formfin");

                        $info_langue = new ChampCache("i_lang", getPrimaryLanguage());
                        $info_effacer = new ChampEffacer($this->r->lang("effacer", "form"));
                        $info_valider = new ChampValider($texteValider);

                        $form->ajouterChamp($info_langue);
                        $form->ajouterChamp($info_effacer);
                        $form->ajouterChamp($info_valider);
                        return $form->fin();
                }

                private function champCoche($mode) {
                        switch ($mode) {
                                case "modifier":
                                        return new ChampCoche("info_a_modifier", $this->getId(), $this->r->lang("id", "infos") . " " . $this->getId(), "", FALSE, "RADIO");
                                case "supprimer":
                                        return new ChampCoche("info_a_supprimer[]", $this->getId(), $this->r->lang("id", "infos") . " " . $this->getId(), "");

                                case "afficher":
                                        return new ChampCoche("info_a_afficher[]", $this->getId(), $this->r->lang("id", "infos") . " " . $this->getId(), "");
                                default :
                                        return new ChampCache("info_a_afficher", "erreurDeMode", FALSE);
                                        break;
                        }
                }

                /* ----- partie publique ----- */
                public static function FormAjouter($pageScript, SQL &$sql) {
                        $info = new Info($sql, $result);
                        return $info->formulaire_ajout($pageScript, $sql);
                }


                public static function GetGlobalLanguages() {
                        if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                                return LANGS;
                        } else {
                                return array(getPrimaryLanguage());
                        }
                }

                public static function findLangQuery($langs = array()) {
                        if (count($langs) == 0) {
                                $langs = array(getPrimaryLanguage());
                        }
                        $sql = "(";
                        $s = "";
                        foreach ($langs as $l) {
                                $sql .= $s . "'" . $l . "'";
                                $s = ",";
                        }
                        $sql .= ")";
                        return $sql;
                }

                /*
                 * NOTE: utilisee seulement sur la page admin de gestion des infos (admin_infos.php)
                  @param $mode string afficher, modifier, supprimer
                 * */

                public static function GetListe(SQL &$sql, $mode = "modifier", $langs = array()) {
                        self::g_r();
                        // ***** FORMULAIRE DEBUT pour choisir l'info a modifier, par selection radio
                        $f = new Formulaire($mode . "_info", self::$_r->sitemap['admin__infos'] . "/" . $mode, LIBRE);
                        $html = $f->getHTML();
                        //debug("form");
                        $infos = $sql->query("SELECT * FROM info WHERE langue IN " . self::findLangQuery($langs) . " ORDER BY 'date' DESC");
                        //debug("sql query");
                        if (!$infos) {
                                $html .= $sql->listeErreurs();
                                return $html;
                        } else {
                                /** liste des champs table Info */
                                $champs = array("id", "titre", "date", "published", "langue", "categorie");
                                $tbl = new Tableau(mysqli_num_rows($infos) + 2, count($champs) + 1, self::$_r->lang($mode, "infos"));
                                $tbl->setOptionsArray(array("class" => "db_liste"));
                                $tbl->setContenu_Cellule(0, 0, $tbl->id, array("HTML" => array("COLSPAN" => $tbl->nbColonnes), "class" => "titre"));
                                $tbl->setOptionsArray_Ligne(1, array("class" => "entete"));
                                $tbl->setContenu_Ligne(1, self::$_r->lang($champs, "infos"));
                                for ($row = 2, $n = 0; $row - 2 < mysqli_num_rows($infos); $row++, $n++) {
                                        $post = new Info($sql, $infos);
                                        // lignes de couleurs altern�es
                                        $tbl->setOptionsArray_Ligne($row, array("class" => "db_a" . $n % 2));
                                        $tbl->setContenu_Ligne($row, array($post->getId(), $post->getTitre(), $post->getDate(), $post->getPublished(), $post->getLangue(), $post->getCategorie()));
                                        // mode afficher
                                        $tbl->setContenu_Cellule($row, count($champs), $post->champCoche($mode)->getHTML(LIBRE, $f->classe));
                                }
                                mysqli_free_result($infos);
                                /** TODO : Confirmer VALIDER (cakephp) */
                                $modifier = new ChampValider(self::$_r->lang($mode, "infos"));
                                return $html . $tbl->fin() . $modifier->getHTML(VERTICAL, $f->classe) . $f->fin();
                        }
                }

                function getLangue() {
                        return $this->langue;
                }

                function getId() {
                        return $this->id;
                }

                // affichage du formulaire n�cessaire pour modifier une info stockee dans la base SQL ($this)
                function formModifier(SQL &$sql, $pageScript) {
                        i_debug("formModif");
                        return $this->formulaire_modif($pageScript, $sql);
                }

                function supprimer(SQL &$sql) {
                        if (!$sql->query("DELETE FROM info WHERE id=" . $this->getId())) {
                                trigger_error($this->_r->lang("effacer_echec", "infos") . " [" . $this->getId() . "] " . $this->getTitre() . "", E_USER_NOTICE);
                        }
                        // suppression des images
                        foreach ($this->images as $id) {
                                if (is_array($id)) {
                                        continue;
                                }
                                Image::DeleteSQL($sql, $id);
                        }
                        return true;
                }

                public function __destruct() {
                        $this->contenu = array();
                        $this->titre = array();
                }

                /**
                * @param string $lang i8ln code langage
                * @param boolean $html_decode par defaut decode les caracteres speciaux (& et ;)
                */
                function getTitre($lang = NULL, $html_decode = true) {
                        $d = "";
                        if ($lang === NULL || !array_key_exists($lang, $this->titre)) {
                                $d = $this->titre[$this->langue];
                        } else {
                                $d = $this->titre[$lang];
                        }
                        if ($html_decode) {
                                return htmlspecialchars_decode($d, ENCODE_HTML);
                        } else {
                                return $d;
                        }
                }

                function getDate() {
                        return $this->date;
                }

                function getPublished() {
                        return $this->published;
                }

                /** @return bool si la publication est valide par rapport � la date du jour */
                function isPublished() {
                        $now = time();
                        $p = strtotime($this->published);
                        return $p != -1 && $p <= $now;
                }

                /**
                  * retourne le contenu (String) dans la langue du systeme client si $lang = NULL
                 * */
                function getContenu($lang = NULL, $html_decode = true, $truncate = false, $trunc_length = 80) {
                        $c = "";
                        if ($lang === NULL || !array_key_exists($lang, $this->contenu)) {
                                $c .= $this->contenu[$this->langue];
                        } else {
                                $c .= $this->contenu[$lang];
                        }
                        if($truncate) {
                                $c = substr($c, 0 , $trunc_length);
                        }
                        return $html_decode ? htmlspecialchars_decode($c, ENCODE_HTML) : $c;
                }

                function getCategorie(SQL &$sql = NULL) { // pour avoir le nom en lettres: $sql === new SQL;
                        if ($sql) {
                                return CAT_getNom(CAT_getCat($this->categorie, $sql), $sql);
                        } else {
                                return $this->categorie;
                        }
                }

                function getAuteur( $html_decode = true) {
                        return $html_decode ? htmlspecialchars_decode($this->auteur, ENCODE_HTML) : $this->auteur;
                }

                function getImageAsTag(&$sql, $n, $echo = TBL_STD) {
                        $image = $this->getImage($sql, $n);
                        if (is_a($image, "Image")) {
                                if(($echo & TBL_STD) != 0) {
                                      if ($image->getWidth() > IMAGE_MAX_LARG) {
                                              $image->setWidth(IMAGE_MAX_LARG);
                                      }
                                      if ($image->getHeight() > IMAGE_MAX_HAUT) {
                                              $image->setHeight(IMAGE_MAX_HAUT);
                                      }
                                      return $image->afficherFormatee();
                                } else {
                                      return $image->afficher();
                                }
                        }
                        return "";
                }

                /**
                 * @param $link soit array(url => nom) ou index
                 * @return Image une image URL ou SQL
                 * */
                function getImage(SQL &$sql, $link) {
                        $img = new Image;
                        if (is_array($this->images[$link])) {
                                $img->setFile($this->images[$link][0]);
                                $img->setNom($this->images[$link][1]);
                                return $img;
                        } else {
                                $img->FromSQL($sql, $this->images[$link]);
                                return $img;
                        }
                }

                /**
                 * ajoute du texte au titre existant
                 * @param string $lang si NULL la langue du systeme utilisateur est utilisee. sinon une valeur de {@link php_module_locale.php} est utilisee.
                 */
                function ajouterTitre($t, $lang = NULL) {
                        $this->setTitre($this->getTitre($lang) . $t, $lang);
                }

                function setTitre($t, $lang = NULL) {
                        if ($lang === NULL) {
                                $lang = getPrimaryLanguage();
                        }
                        $this->titre[$lang] = $t;
                }

                /**
                 * ajoute du texte au contenu existant
                 * @param string $lang si NULL la langue du systeme utilisateur est utilisee. sinon une valeur de {@link php_module_locale.php} est utilisee.
                 */
                function ajouterContenu($s, $lang = NULL) {
                        $this->setContenu($this->getContenu($lang, false) . $s, $lang);
                }

                function setContenu($s, $lang = NULL) {
                        if ($lang === NULL) {
                                $lang = getPrimaryLanguage();
                        }
                        $this->contenu[$lang] = $s;
                }

                /*
                 * ajouter une image dans une info non stock�e en base de donn�es
                 * (on utilise is_array() pour controler la variable $this->images)
                 */

                function ajouterImage($url, $desc_courte) {
                        $this->images[] = array($url, $desc_courte); // l'url et une courte description de l'image. index 0 url et index 1 description
                }

                /* ajouter une image de la base SQL table image
                  (id de l'entr�e mysql_insert_id) */

                function ajouterImageSQL($id) {
                        $this->images[] = $id;
                }

                /* retourne la liste des id des images de l'info, string */

                function listeImagesId() {
                        $images = "";
                        $sep = "";
                        foreach ($this->images as $id) {
                                if (is_array($id)) {
                                        continue;
                                }
                                $images .= $sep . $id;
                                $sep = ",";
                        }
                        return $images;
                }

                /*
                 * Cadre d'affichage avec une bordure.
                 */

                function getTableauMultiLang(SQL &$sql, $caption = NULL, $intl = TRUE) {
                        $t_bord = new Tableau(3, 1, "info_multi" . time());
                        $t_bord->setOptionsArray(array("class" => "info_multi"));
                        // pour ajouter une legende � l'info. (p.ex. id)
                        if (isset($caption)) {
                                $t_bord->setCaption($caption, "top");
                        }
                        $row = 0;
                        foreach ($this->contenu as $lang => $text) {
                                if ($text === NULL || (!$intl && $lang != $this->langue)) {
                                        i_debug("Nothing for this post : " . $this->getTitre($lang));
                                        continue;
                                } else {
                                        $t_bord->setContenu_Cellule($row++, 0, $this->getTableLangage($sql, $lang));
                                }
                                if (!$intl) {
                                        break;
                                }
                        }
                        return $t_bord->fin();
                }

                /** lien vers l'info */
                function getLien($texte, $lang = NULL) {
                        if ($lang === NULL) {
                                $lang = $this->langue;
                        }
                        return HTML_lien($this->r->sitemap["infos__index"] . "/" . implode("/", explode("-", $this->getPublished())) . "/?lang=" . $lang, $texte);
                }

                /*
                 * Lignes d'affichage de l'info.
                 */

                function getTableLangage(SQL &$sql, $lang = NULL) {
                        if ($lang === NULL) {
                                $lang = $this->langue;
                        }
                        $t = new Tableau(3, 1, "info-". time());
                        $t->setOptionsArray(array("class" => "info"));
                        // INFO LOCALISEE (lang)
                        $t->setContenu_Cellule(0, 0, $this->getLien($this->getTitre($lang, false), $lang), array("class" => "titre"));
                        $t->setContenu_Cellule(1, 0, $this->getContenu($lang, false), array("class" => "corps"));

                        $t->setContenu_Cellule(2, 0, ($sql->connect_succes() ? $this->tableauImages($sql) . "<br>" . $this->getCategorie($sql) : "") . " - " . $this->getAuteur(false), array(
                            "class" => "portofolio"
                        ));
                        return $t->fin();
                }

                /** Tableau avec les images du post
                 * @param Tableau les images
                 * @param int $echo constantes TBL_*
                 * @param $opt options de Tableau/Div 0,1,2 tableau, ligne, cellule
                 *                  */
                function tableauImages(SQL &$sql, $echo = TBL_STD) {
                        $opt = array(array(),
                                array(),
                                array());
                        $tags = ($echo & TBL_STD) != 0 ? array("table", "tr", "td") : array("", "", "div");
                        $img =  new Tableau(ceil(count($this->images) / 3), 3, "images-". time());
                        $img->setOptionsArray(array("class" => "portofolio"));
                        $this->tableauImages_r($sql, $img, 0, $echo);
                        return $img->fin($echo, $tags);
                }

                // recursive ligne
                private function tableauImages_r(SQL &$sql, Tableau &$img, $i, $echo) {
                        if ($i < $img->nbLignes) {
                                $this->tableauImages_rc($sql, $img, $i, 0, $echo);
                                $this->tableauImages_r($sql, $img, $i + 1, $echo);
                        }
                        return $img;
                }

                //recursive colonne
                private function tableauImages_rc(SQL &$sql, Tableau &$img, $i, $j, $echo) {
                        $n = $i * $img->nbColonnes + $j;
                        /* images encadrees
                         * tableau d'images associees a l'info, sur 3 colonnes et donc
                         * ceil(count($this->images)/3) lignes. */
                        if ($n < count($this->images) && $j < $img->nbColonnes) {
                                $image_html = $this->getImageAsTag($sql, $n, $echo);
                                $img->setContenu_Cellule($i, $j, $image_html);
                                $this->tableauImages_rc($sql, $img, $i, $j + 1, $echo);
                                /* images[][0] : url et images[][1] : description breve de l'image. */
                        }
                }

                /* ---- fonctions vers SQL ---- */
                /* $update = id de l'info qui est modifiee si utilise sinon false
                  // chaine ids des images (id1,id2,id3,...,idn) (reference table image de la base SQL) de l'info */

                function update(SQL &$sql, $id) {
                        // recuperer l'info sur SQL qui est modifiee
                        $result = $sql->query("SELECT * FROM info WHERE id ='" . $id . "'");
                        $infoSQL = new Info($sql, $result);
                        mysqli_free_result($result);
                        // prendre les images de l'info courante-nouvelle pour comparer les images de l'ancienne. supprimer de la base SQL les images qui n'apparaissent plus dans le nouveau tableau this->images
                        $poubelle = array_diff($infoSQL->images, $this->images);
                        foreach ($poubelle as $id_old) {
                                if (is_array($id_old)) {
                                        continue;
                                }
                                Image::DeleteSQL($sql, $id_old);
                        }
                        $query = "UPDATE info SET categorie = \"" . $this->getCategorie() . "\", titre = \"" . addSlashes(htmlspecialchars($this->getTitre(), ENCODE_HTML, ENCODE_CS)) . "\", contenu = \"" . addSlashes(htmlspecialchars($this->getContenu(), ENCODE_HTML, ENCODE_CS)) . "\", langue = \"" . $this->langue . "\", auteur = \"" . $this->getAuteur() . "\", images = \"" . $this->listeImagesId() . "\", published =\"" . $this->getPublished() . "\" WHERE id = " . $id;

                        $result2 = $sql->query($query);
                        if ($result2 instanceof mysqli_result) {
                                mysqli_free_result($result2);
                                return true;
                        } else {
                                $sql->afficheErreurs();
                                return $result2;
                        }
                }

                function publier(SQL &$sql, $update) {
                        $query = "";
                        if ($update) { // MODIFIER
                                return $this->update($sql, $update);
                        } else { // AJOUTER
                                $query = "INSERT INTO info (categorie,titre,contenu,langue,date,published,auteur,images) VALUES (\"" . $this->getCategorie() . "\",\"" . addSlashes(htmlspecialchars($this->getTitre(), ENCODE_HTML, ENCODE_CS)) . "\",\"" . addSlashes(htmlspecialchars($this->getContenu(), ENCODE_HTML, ENCODE_CS)) . "\",'" . $this->langue . "','" . $this->getDate() . "','" . $this->getPublished() . "','" . $this->getAuteur() . "',\"" . $this->listeImagesId() . "\")";
                        }
                        $result = $sql->query($query);
                        $this->id = mysqli_insert_id($sql);
                        if ($result) {
                                mysqli_free_result($result);
                                return true;
                        } else {
                                return false;
                        }
                }

        }

}
?>
