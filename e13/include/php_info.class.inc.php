<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:41:43 CEST 2004 @612 /Internet Time/
  @filename	php_info.class.inc
 */

global $ClasseInfo;
if (!isset($ClasseInfo)) {
        $ClasseInfo = 1;
        require($GLOBALS['include__php_image.class.inc']);
        require($GLOBALS['include__php_module_cat.inc']);
        require($GLOBALS['include__php_module_locale.inc']);

        class Info {

                var $titre, $date, $contenu, $auteur, $categorie;
                var $images; // tableau d'urls des images
                var $langue; // langue de préférence
                var $id; // id dans la base SQL

                /**
                 * @param SQL $sql prend la reference de connexion
                 * @param mysqli_result $infos l'objet d'un retour base de données (ou initialiser une variable vide pour eviter une erreur fatale)
                 */

                public function __construct(SQL &$sql, mysqli_result &$infos, $t = NULL, $a = NULL, $c = NULL, $d = NULL) {
                        //init var
                        $this->titre = array(FR => NULL, EN => NULL, DE => NULL);
                        $this->date = $d;
                        $this->categorie = $c;
                        $this->auteur = $a;
                        $this->images = array();
                        $this->contenu = array(FR => NULL, EN => NULL, DE => NULL);
                        $this->loadResult($sql, $infos);
                }

                private function loadResult(SQL &$sql, mysqli_result &$result) {        // reception info de la base SQL
                        $info = $sql->ligneSuivante_Array($result);
                        if (!$info) {
                                return;
                        }
                        // appel fonction de la classe parente Info
                        $this->ajouterTitre($info["titre"], $this->langue);
                        $this->auteur = $info["auteur"];
                        $this->categorie = $info["categorie"];
                        $this->date = $info["date"];

                        $this->id = $info["id"];
                        $this->langue = $info["langue"];
                        $this->ajouterContenu($info["contenu"], $this->langue);

                        // acquisition de la liste des images pour l'info
                        if ($info["images"] != "") {
                                $image = strtok($info["images"], ',');
                                while ($image) {
                                        $this->ajouterImageSQL($image);
                                        $image = strtok(',');
                                }
                        }
                }

                /* function convertEnc($str) {
                  return mb_convert_encoding($str, "UTF-8", "ISO-8859-1");
                  } */

                /* ----- methodes de classe "statiques"-----
                 * ------							   -----
                 * ------	partie privée			   ----- */

                /* formulaire pour ajouter une info dans la base SQL ou pour modifier avec $mode = "modifier" et $info contenant l'objet info a modifier
                  $pageScript devra gérer les valeurs POSTees et notamment les images qui envoient leurs id (supprimer les anciennes dans la table image, avant.) et le champ FILE avec la nouvelle image. */

                private static function Formulaire($pageScript, $mode, Info &$info, SQL &$sql) {
                        if ($mode === "ajouter") {
                                return $info->formulaire_ajout($pageScript, $sql);
                        } else if ($mode === "modifier") { // assume $mode=="modifier", $info==(Info)    
                                return $info->formulaire_modif($pageScript, $sql);
                        }
                }

                private function formulaire_ajout($pageScript, SQL &$sql) {
                        $form = new Formulaire("ajouter une info", $pageScript);
                        // valeurs deja enregistree mais qui n'ont pas pu etre publiees
                        $i_titre = filter_input(INPUT_POST, 'i_titre');
                        $this->fm_ctitre($sql, $form, $i_titre);
                        $i_categorie = filter_input(INPUT_POST, 'i_categorie');
                        $this->fm_ccat($sql, $form, $i_categorie);
                        $i_contenu[$this->langue] = filter_input(INPUT_POST, 'i_contenu' . $this->langue);
                        $i_auteur = array_key_exists("client", $_SESSION) && array_key_exists("id", $_SESSION["client"]) ? $_SESSION["client"]['id'] : "";
                        $this->fm_cinfo($sql, $form, $i_auteur, $i_contenu);
                        $info_images = filter_input(INPUT_POST, 'i_images');
                        $this->fm_cimage($sql, $form, $info_images);
                        return $this->formulaire_fin($sql, $form, "Publier l'info");
                }

                private function formulaire_modif($pageScript, SQL &$sql) {
                        $form = new Formulaire("modifier une info id:" . $this->id, $pageScript);
                        $i_titre = $this->titre;
                        $this->fm_ctitre($sql, $form, $i_titre);
                        $i_categorie = $this->categorie;
                        $this->fm_ccat($sql, $form, $i_categorie);
                        $i_contenu = array($this->langue, $this->contenu[$this->langue]);
                        $i_auteur = $this->auteur;
                        $this->fm_cinfo($sql, $form, $i_auteur, $i_contenu);
                        /* chaque image existante dans l'info, est affiche dans un groupe de champs checkbox; pour supprimer, decocher. pour ajouter, un champ FILE est ajouté plus bas dans le formulaire */
                        $champs_images = array();
                        foreach ($this->images as $key => $id) {
                                $image = $this->getImage($sql, $key); // retourne l'image en objet Image, l'id est dans l'objet Info (->images[])
                                if (is_a($image, "Image")) {
                                        $champs_images[] = new ChampCoche("i_images[]", $id, $image->afficher(), "", TRUE);
                                }
                        }

                        $info_images = new ChampGroupe("Images:", "Pour supprimer une image, enlever le coche.", "i_images[]", $champs_images);
                        $this->fm_cimage($sql, $form, $info_images);
                        return $this->formulaire_fin($sql, $form, "Sauvegarder les modifications");
                }

                private function fm_ctitre(SQL &$sql, Formulaire &$form, $i_titre) {
                        $info_titre = new ChampTexte('i_titre', "Titre:", "Le titre de l'information " . Info::findLangQuery(Info::GetGlobalLanguages()), "50", NULL, $i_titre);
                        $form->ajouterChamp($info_titre);
                }

                private function fm_ccat(SQL &$sql, Formulaire &$form, $i_categorie) {
                        $info_categorie = CAT_getSelect($sql, 'i_categorie', "Categorie:", "Pour en ajouter une nouvelle, voir page " . HTML_lien($GLOBALS['admin__cat'], "gestion catégorie"), $i_categorie);
                        $form->ajouterChamp($info_categorie);
                }

                private function fm_cimage(SQL &$sql, Formulaire &$form, $info_images) {
                        $info_image = new ChampFile('i_image', "Ajouter une image JPEG:", "Taille maximale 200ko.", 200000);
                        $info_image_nom = new ChampTexte('i_image_nom', "Titre de l'image:", "Le titre de l'image, qui apparaîtra au-dessous de celle-ci.", "20");
                        $info_image_desc = new ChampAireTexte('i_image_desc', "Description (courte) de l'image:", "facultatif", "20", "3");
                        if (isset($info_images)) {
                                $form->ajouterChamp($info_images);
                        }
                        $form->ajouterChamp($info_image);
                        $form->ajouterChamp($info_image_nom);
                        $form->ajouterChamp($info_image_desc);
                }

                private function fm_cinfo(SQL &$sql, Formulaire &$form, $i_auteur, $i_contenu) {
                        foreach ($i_contenu as $lang => $text) {
                                $info_contenu[$lang] = new ChampAireTexte('i_contenu' . $lang, "Info (" . $lang . ") : ", "Le contenu de l'information.", "30", "20", $text);
                                $form->ajouterChamp($info_contenu[$lang]);
                        }
                        $info_auteur = new ChampTexte('i_auteur', "Auteur:", "Nom/surnom de l'auteur de l'info.", "15", "20", $i_auteur);
                        $form->ajouterChamp($info_auteur);
                }

                private function formulaire_fin(SQL &$sql, Formulaire &$form, $texteValider) {
                        debug("formfin");

                        $info_effacer = new ChampEffacer("Effacer les champs");
                        $info_valider = new ChampValider($texteValider);

                        $form->ajouterChamp($info_effacer);
                        $form->ajouterChamp($info_valider);
                        return $form->fin();
                }

                private function champCoche($mode) {
                        switch ($mode) {
                                case "modifier":
                                        return new ChampCoche("info_a_modifier", $this->getId(), "info id: " . $this->getId(), "", FALSE, "RADIO");
                                case "supprimer":
                                        return new ChampCoche("info_a_supprimer[]", $this->getId(), "info id: " . $this->getId(), "");

                                case "afficher":
                                        return new ChampCoche("info_a_afficher[]", $this->getId(), "info id: " . $this->getId(), "");
                                default :
                                        return new ChampCache("info_a_afficher", "erreurDeMode", FALSE);
                                        break;
                        }
                }

                /* ----- partie publique ----- */

                public static function FormAjouter($pageScript, SQL &$sql) {
                        return Info::Formulaire($pageScript, "ajouter", new Info($sql, $result), $sql);
                }

                public static function GetGlobalLanguages() {
                        return $GLOBALS["LANGS"];
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

                // NOTE: utilisee seulement sur la page admin de gestion des infos (admin_infos.php)
                public static function GetListe(SQL &$sql, $mode = "modifier", $langs = array()) {
                        // ***** FORMULAIRE DEBUT pour choisir l'info a modifier, par selection radio
                        $f = new Formulaire($mode . "_info", $GLOBALS['admin__infos'] . "?" . $mode . "=1", LIBRE);
                        $html = $f->getHTML();
                        //debug("form");
                        $infos = $sql->query("SELECT * FROM info WHERE langue IN " . Info::findLangQuery($langs) . " ORDER BY 'date' DESC");
                        //debug("sql query");
                        if (!$infos) {
                                $html .= $sql->listeErreurs();
                                return $html;
                        } else {
                                $tbl = new Tableau(mysqli_num_rows($infos) + 2, 6, "infos");
                                $tbl->setOptionsArray(array("HTML" => array("BORDER" => 0), "class" => "liste"));
                                $tbl->setContenu_Cellule(0, 0, $tbl->id, array("HTML" => array("COLSPAN" => $tbl->nbColonnes), "class" => "titre"));
                                $tbl->setOptionsArray_Ligne(1, array("class" => "entete"));
                                $tbl->setContenu_Ligne(1, array("id", "titre", "date", "langue", "categorie"));
                                for ($row = 2, $n = 0; $row - 2 < mysqli_num_rows($infos); $row++) {
                                        $nfo = new Info($sql, $infos);
                                        // lignes de couleurs alternées
                                        $tbl->setOptionsArray_Ligne($row, array("class" => "A" . ($n++) % 2));
                                        $tbl->setContenu_Ligne($row, array($nfo->getId(), $nfo->getTitre(), $nfo->getDate(), $nfo->getLangue(), $nfo->getCategorie()));
                                        // mode afficher                                      
                                        $tbl->setContenu_Cellule($row, 5, $nfo->champCoche($mode)->getHTML(LIBRE, $f->classe));
                                }
                                mysqli_free_result($infos);
                                $modifier = new ChampValider($mode, "Si vous avez choisi l'info à $mode, cliquez sur $mode.");
                                return $html . $tbl->fin() . $modifier->getHTML(VERTICAL, $f->classe) . $f->fin();
                        }
                }

                function getLangue() {
                        return $this->langue;
                }

                function getId() {
                        return $this->id;
                }

                // affichage du formulaire nécessaire pour modifier une info stockee dans la base SQL ($this)
                function formModifier(SQL &$sql, $pageScript) {
                        debug("formModif");
                        return Info::Formulaire($pageScript, "modifier", $this, $sql);
                }

                function supprimer(SQL &$sql) {
                        if (!$sql->query("DELETE FROM info WHERE id=" . $this->getId())) {
                                die("Impossible d'effectuer la suppression de l'info " . $this->getTitre());
                        }
                        // suppression des images
                        foreach ($this->images as $key => $id) {
                                Image::DeleteSQL($sql, $id);
                        }
                        return true;
                }

                public function __destruct() {
                        $this->contenu = array();
                        $this->titre = array();
                }

                function getTitre($lang = NULL) {
                        if ($lang == NULL || !array_key_exists($lang, $this->titre)) {
                                return $this->titre[$this->langue];
                        } else {
                                return $this->titre[$lang];
                        }
                }

                function getDate() {
                        return $this->date;
                }

                /**
                  retourne le contenu (String) dans la langue du systeme client si $lang = NULL
                 *               
                 * */
                function getContenu($lang = NULL) {
                        if ($lang == NULL || !array_key_exists($lang, $this->contenu)) {
                                return $this->contenu[$this->langue];
                        } else {
                                return $this->contenu[$lang];
                        }
                }

                function getCategorie(SQL &$sql = NULL) { // pour avoir le nom en lettres: $sql === new SQL;
                        if ($sql) {
                                return CAT_getNom(CAT_getCat($this->categorie, $sql), $sql);
                        } else {
                                return $this->categorie;
                        }
                }

                function getAuteur() {
                        return $this->auteur;
                }

                function getImage(SQL &$sql, $n) {
                        foreach ($this->images as $key => $id) {
                                if ($n == $key) {
                                        if (!is_array($this->images[$n])) {
                                                // il s'agit de l'id d'une image stockée dans la base SQL, table Image
                                                $img = new Image;
                                                $img->loadFromSQL($sql, $id);
                                                return $img;
                                        } else {
                                                return $this->images[$n];
                                        } // retourne l'image stockée en URL sous forme d'un array ([0] => $url, [1] => $desc_courte)
                                        break;
                                }
                        }
                }

                /**
                  ajoute du texte au titre existant
                 * @param string $lang si NULL la langue du systeme utilisateur est utilisee. sinon une valeur de {@link php_module_locale.php} est utilisee.                  
                 */
                function ajouterTitre($t, $lang = NULL) {
                        $this->setTitre($this->getTitre($lang) . $t, $lang);
                }

                function setTitre($t, $lang = NULL) {
                        if ($lang == NULL) {
                                $lang = getPrimaryLanguage();
                        }
                        $this->titre[$lang] = $t;
                }

                /**
                  ajoute du texte au contenu existant
                 * @param string $lang si NULL la langue du systeme utilisateur est utilisee. sinon une valeur de {@link php_module_locale.php} est utilisee.                  
                 */
                function ajouterContenu($s, $lang = NULL) {
                        $this->setContenu($this->getContenu($lang) . $s, $lang);
                }

                function setContenu($s, $lang = NULL) {
                        if ($lang == NULL) {
                                $lang = getPrimaryLanguage();
                        }
                        $this->contenu[$lang] = $s;
                }

                /* ajouter une image dans une info non SQL, stockee dans la page php */

                function ajouterImage($url, $desc_courte) {
                        /* !!!!! image non geree pour etre envoyee par SQL !!!!! */
                        $this->images[] = array($url, $desc_courte); // l'url et une courte description de l'image. index 0 url et index 1 description
                }

                /* ajouter une image de la base SQL table image */

                function ajouterImageSQL($id) {
                        $this->images[] = $id;
                }

                /* retourne la liste des id des images de l'info, string */

                function listeImagesId() {
                        $images = "";
                        foreach ($this->images as $id) {
                                $images .= @$sep . $id;
                                $sep = ",";
                        }
                        return $images;
                }

                /*
                 * l'info pour l'affichage HTML. 
                 */

                function getTableauMultiLang(SQL &$sql, $caption = NULL, $intl = TRUE) {
                        $t_bord = new Tableau(3, 1, "info_border");
                        // pour ajouter une legende à l'info. (p.ex. id)
                        if (isset($caption)) {
                                $t_bord->setCaption($caption, "top");
                        }

                        $t_bord->setOptionsArray(array("HTML" => array("ALIGN" => "CENTER",
                                "CELLPADDING" => 1,
                                "WIDTH" => "80%"),
                            "class" => "info"));
                        $row = 0;
                        foreach ($this->contenu as $lang => $text) {
                                if ($text == NULL || (!$intl && $lang != $this->langue)) {
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

                function getTableLangage(SQL &$sql, $lang = NULL) {
                        if ($lang == NULL) {
                                $lang = $this->langue;
                        }
                        $t = new Tableau(3, 1, "info");
                        $t->setOptionsArray(array("HTML" => array("WIDTH" => "100%",
                                "ALIGN" => "CENTER",
                                "BORDER" => 0,
                                "CELLSPACING" => 0,
                                "CELLPADDING" => 5)));
                        // INFO LOCALISEE (lang)
                        $t->setContenu_Cellule(0, 0, "news://" . $lang . "/" . $this->getAuteur() . "/" . $this->getCategorie($sql) . "/" . $this->getDate() . "/<div class='info_titre'>" . stripSlashes($this->getTitre($lang)) . "</div>");
                        $t->setContenu_Cellule(1, 0, stripSlashes($this->getContenu($lang)));

                        $t->setOptionsArray_Cellule(0, 0, array("HTML" => array("BGCOLOR" => "#ffbb44"), "class" => "info_titre"));
                        $t->setContenu_Cellule(2, 0, $this->tableauImages($sql)->fin());
                        return $t->fin();
                }

                function tableauImages(SQL &$sql) {
                        $img = $this->tableauImages_r($sql, new Tableau(ceil(count($this->images) / 3), 3, "images"), 0, 0);
                        // afficher images
                        $img->setOptionsArray(array("HTML" => array("BORDER" => 0,
                                "ALIGN" => "RIGHT"),
                            "class" => "portofolio"
                        ));
                        return $img;
                }

                // recursive ligne
                private function tableauImages_r(SQL &$sql, $img, $i, $n) {
                        if ($i < $img->nbLignes) {
                                $this->tableauImages_rc($sql, $img, $i, 0, $n);
                                return $this->tableauImages_r($sql, $img, ++$i, $n);
                        } else {
                                return $img;
                        }
                }

                //recursive colonne
                private function tableauImages_rc(SQL &$sql, Tableau &$img, $i, $j, $n) {
                        /* images 150px */
                        // tableau d'images associees a l'info, sur 3 colonnes et donc ceil(count($this->images)/3) lignes.
                        if ($n < count($this->images)) {
                                if ($j < $img->nbColonnes) {
                                        $image = $this->getImage($sql, $n);
                                        if (is_a($image, "Image")) {
                                                $image->setSize("150x150");
                                                $image->resize();
                                                $image_html = $image->afficherFormatee();
                                        } else {
                                                $image_html = HTML_image($image[0]) . "<br>" . $image[1];
                                        } // cas info non sql, image url
                                        $img->setContenu_Cellule($i, $j, $image_html, array("css" => array("text-align" => "center")));
                                        $this->tableauImages_rc($sql, $img, $i, ++$j, ++$n);
                                        /* images[][0] : url et images[][1] : description breve de l'image. */
                                }
                        }
                }

                /* ---- fonctions vers SQL ---- */

                function publier(SQL &$sql, $update) { // $update = id de l'info qui est modifiee si utilise sinon false
                        // chaine ids des images (id1,id2,id3,...,idn) (reference table image de la base SQL) de l'info
                        // PREPARATION
                        if ($update) { // MODIFIER
                                // recuperer l'info sur SQL qui est modifiee
                                $result = $sql->query("SELECT * FROM info WHERE id ='" . $update . "'");
                                $infoSQL = new Info($sql, $result);
                                mysqli_free_result($result);
                                // prendre les images de l'info courante-nouvelle pour comparer les images de l'ancienne. supprimer de la base SQL les images qui n'apparaissent plus dans le nouveau tableau this->images
                                $poubelle = array_diff($infoSQL->images, $this->images);
                                foreach ($poubelle as $id_old) {
                                        Image::DeleteSQL($sql, $id_old);
                                }
                        }

                        // ENVOI SUR SQL
                        $query = "";
                        if ($update) { // MODIFIER
                                $query = "UPDATE info SET categorie = \"" . $this->getCategorie() . "\", titre = \"" . addSlashes($this->getTitre()) . "\", contenu = \"" . addSlashes($this->contenu[$this->langue]) . "\", langue = \"" . $this->langue . "\", date = \"" . $this->getDate() . "\", auteur = \"" . $this->getAuteur() . "\", images = \"" . $this->listeImagesId() . "\" WHERE id = " . $update;
                        } else { // AJOUTER
                                $query = "INSERT INTO info (categorie,titre,contenu,langue,date,auteur,images) VALUES (\"" . $this->getCategorie() . "\",\"" . addSlashes($this->getTitre()) . "\",\"" . addSlashes($this->contenu[$this->langue]) . "\",'" . $this->langue . "','" . $this->getDate() . "','" . $this->getAuteur() . "',\"" . $this->listeImagesId() . "\")";
                        }
                        return $sql->query($query);
                }

        }

}
?>
