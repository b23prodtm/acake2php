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
                  var $langue;
                var $id; // id dans la base SQL


                public function __construct(SQL &$sql, $infos, $t = NULL, $a = NULL, $c = NULL, $d = NULL) {
                        //init var
                        $this->titre = $t;
                        $this->date = $d;
                        $this->categorie = $c;
                        $this->auteur = $a;
                        $this->images = array();
                        $this->contenu = array(FR => NULL, EN => NULL, DE => NULL);
                        if (!$infos) {
                                return;
                        }
                        // init var
                        // reception info de la base SQL
                        $info = $sql->ligneSuivante_Array($infos);
                        // appel fonction de la classe parente Info
                        $this->titre = $info["titre"];
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
                 * ------	partie priv�e			   ----- */

                /* formulaire pour ajouter une info dans la base SQL ou pour modifier avec $mode = "modifier" et $info contenant l'objet info a modifier
                  $pageScript devra g�rer les valeurs POSTees et notamment les images qui envoient leurs id (supprimer les anciennes dans la table image, avant.) et le champ FILE avec la nouvelle image. */

                private static function Formulaire($pageScript, $mode, Info &$info, SQL &$sql) {
                        if ($mode === "ajouter") {
                                return $info->formulaire_ajout($pageScript, $sql);
                        } else if ($mode === "modifier") { // assume $mode=="modifier", $info==(Info)    
                                return $info->formulaire_modif($pageScript, $sql);
                        }
                }

                private function formulaire_ajout($pageScript, SQL &$sql) {
                        $form = new Formulaire("ajouter une info", $pageScript);
                        $info_valider = new ChampValider("Publier l'info");
                        // valeurs deja enregistree mais qui n'ont pas pu etre publiees
                        $i_titre = filter_input(INPUT_POST, 'i_titre');
                        $i_categorie = filter_input(INPUT_POST, 'i_categorie');
                        $i_contenu = array();
                        foreach ($this->contenu as $lang => $text) {
                                $i_contenu[$lang] = filter_input(INPUT_POST, 'i_contenu' . $lang);
                        }
                        $i_auteur = array_key_exists("client", $_SESSION) && array_key_exists("id", $_SESSION["client"]) ? $_SESSION["client"]['id'] : "";
                        return $this->formulaire_fin($sql, $i_titre, $i_categorie, $i_auteur, $i_contenu, NULL, $info_valider, $form);
                }

                private function formulaire_modif($pageScript, SQL &$sql) {
                        $form = new Formulaire("modifier une info id:" . $this->id, $pageScript);
                        $info_valider = new ChampValider("Sauvegarder les modifications");
                        $i_titre = $this->titre;
                        $i_categorie = $this->categorie;
                        $i_contenu = $this->contenu;
                        $i_auteur = $this->auteur;
                        /* chaque image existante dans l'info, est affiche dans un groupe de champs checkbox; pour supprimer, decocher. pour ajouter, un champ FILE est ajout� plus bas dans le formulaire */
                        $champs_images = array();
                        foreach ($this->images as $key => $id) {
                                $image = $this->getImage($sql, $key); // retourne l'image en objet Image, l'id est dans l'objet Info (->images[])
                                if (is_a($image, "Image")) {
                                        $champs_images[] = new ChampCoche("i_images[]", $id, $image->afficher(), "", TRUE);
                                }
                        }

                        $info_images = new ChampGroupe("Images:", "Pour supprimer une image, enlever le coche.", "i_images[]", $champs_images);
                        return $this->formulaire_fin($sql, $i_titre, $i_categorie, $i_auteur, $i_contenu, $info_images, $info_valider, $form);
                }

                private function formulaire_fin(SQL &$sql, $i_titre, $i_categorie, $i_auteur, $i_contenu, $info_images, $info_valider, Formulaire &$form) {
                        debug("formfin");
                        $info_titre = new ChampTexte('i_titre', "Titre:", "Le titre de l'information " . Info::GetLanguages(), "50", NULL, $i_titre);
                        $info_categorie = CAT_getSelect($sql, 'i_categorie', "Categorie:", "Pour en ajouter une nouvelle, voir page " . HTML_lien($GLOBALS['admin__cat'], "gestion cat�gorie"), $i_categorie);

                        $info_image = new ChampFile('i_image', "Ajouter une image JPEG:", "Taille maximale 200ko.", 200000);
                        $info_image_nom = new ChampTexte('i_image_nom', "Titre de l'image:", "Le titre de l'image, qui appara�tra au-dessous de celle-ci.", "20");
                        $info_image_desc = new ChampAireTexte('i_image_desc', "Description (courte) de l'image:", "facultatif", "20", "3");
                        $info_auteur = new ChampTexte('i_auteur', "Auteur:", "Nom/surnom de l'auteur de l'info.", "15", "20", $i_auteur);
                        $info_effacer = new ChampEffacer("Effacer les champs");

                        $form->ajouterChamp($info_titre);
                        $form->ajouterChamp($info_categorie);
                        foreach ($this->contenu as $lang => $text) {
                                $info_contenu[$lang] = new ChampAireTexte('i_contenu' . $lang, "Info (" . $lang . ") : ", "Le contenu de l'information.", "30", "20", $i_contenu[$lang]);
                                $form->ajouterChamp($info_contenu[$lang]);
                        }
                        if (isset($info_images)) {
                                $form->ajouterChamp($info_images);
                        }
                        $form->ajouterChamp($info_image);
                        $form->ajouterChamp($info_image_nom);
                        $form->ajouterChamp($info_image_desc);
                        $form->ajouterChamp($info_auteur);
                        $form->ajouterChamp($info_effacer);
                        $form->ajouterChamp($info_valider);
                        return $form->fin();
                }

                /* ----- partie publique ----- */

                public static function FormAjouter($pageScript, SQL &$sql) {
                        return Info::Formulaire($pageScript, "ajouter", new Info($sql, NULL), $sql);
                }

                public static function GetLanguages() {
                        return Info::GetLanguagesDeArray($GLOBALS["LANGS"]);
                }

                public static function GetLanguagesDeArray($langs = array()) {
                        if (count($langs) == 0) {
                                $langs = array(getPrimaryLanguage());
                        }
                        $sql = "(";
                        $s = "";
                        foreach ($langs as $l) {
                                $sql .= $s . "'" . $l . "'";
                                $s = ",";
                        }
                        $sql .=")";
                        return $sql;
                }

                // NOTE: utilisee seulement sur la page admin de gestion des infos (admin_infos.php)
                public static function GetListe(SQL &$sql, $mode = "modifier", $langs = array()) {
                        // ***** FORMULAIRE DEBUT pour choisir l'info a modifier, par selection radio
                        $f = new Formulaire($mode . "_info", $GLOBALS['admin__infos'] . "?" . $mode . "=1", LIBRE);
                        $html = $f->getHTML();
                        //debug("form");
                        $infos = $sql->query("SELECT * FROM info WHERE langue IN " . Info::GetLanguagesDeArray($langs) . " ORDER BY 'date' DESC");
                        //debug("sql query");
                        if (!$infos) {
                                $html .= $sql->listeErreurs();
                                return $html;
                        } else {
                                $tbl = new Tableau(mysqli_num_rows($infos) + 2, 6, "infos");
                                $tbl->setOptionsArray(array("HTML" => array("BORDER" => 0),
                                    "class" => "liste"));
                                $tbl->setContenu_Cellule(0, 0, $tbl->id, array("HTML" => array("COLSPAN" => $tbl->nbColonnes),
                                    "class" => "titre"));
                                $e = array("class" => "entete");
                                $tbl->setContenu_Cellule(1, 0, "id", $e);
                                $tbl->setContenu_Cellule(1, 1, "titre", $e);
                                $tbl->setContenu_Cellule(1, 2, "date", $e);
                                $tbl->setContenu_Cellule(1, 3, "langue", $e);
                                $tbl->setContenu_Cellule(1, 4, "categorie", $e);

                                for ($i = 2, $n = 0; $i - 2 < mysqli_num_rows($infos); $i++) {
                                        $nfo = new Info($sql, $infos);
                                        // lignes de couleurs altern�es
                                        $a = array("class" => "A" . ($n++) % 2);
                                        $tbl->setOptionsArray_Ligne($i, $a);

                                        $tbl->setContenu_Cellule($i, 0, $nfo->getId());
                                        $tbl->setContenu_Cellule($i, 1, $nfo->getTitre());
                                        $tbl->setContenu_Cellule($i, 2, $nfo->getDate());
                                        $tbl->setContenu_Cellule($i, 3, $nfo->getLangue());
                                        $tbl->setContenu_Cellule($i, 4, $nfo->getCategorie());
                                        // mode afficher
                                        $champCoche = NULL;
                                        if ($mode == "modifier") {
                                                // ****** FORMULAIRE SUITE champ radio pour modifier l'info desiree
                                                $champCoche = new ChampCoche("info_a_modifier", $nfo->getId(), "info id: " . $nfo->getId(), "", FALSE, "RADIO");
                                        }
                                        // mode supprimer....
                                        if ($mode == "supprimer") {
                                                $champCoche = new ChampCoche("info_a_supprimer[]", $nfo->getId(), "info id: " . $nfo->getId(), "");
                                        }
                                        // mode afficher
                                        if ($mode == "afficher") {
                                                $champCoche = new ChampCoche("info_a_afficher[]", $nfo->getId(), "info id: " . $nfo->getId(), "");
                                        }
                                        $tbl->setContenu_Cellule($i, 5, $champCoche->getHTML(LIBRE, $f->classe), $a);
                                }
                                mysqli_free_result($infos);
                                $modifier = new ChampValider($mode, "Si vous avez choisi l'info � $mode, cliquez sur $mode.");
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
                }

                function getTitre() {
                        return $this->titre;
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
                                return $this->contenu[getPrimaryLanguage()];
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
                                                // il s'agit de l'id d'une image stock�e dans la base SQL, table Image
                                                $img = new Image;
                                                $img->loadFromSQL($sql, $id);
                                                return $img;
                                        } else {
                                                return $this->images[$n];
                                        } // retourne l'image stock�e en URL sous forme d'un array ([0] => $url, [1] => $desc_courte)
                                        break;
                                }
                        }
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

                /*                 * * l'info sous forme preformatee prete a l'affichage. */

                function getFormated(SQL &$sql, $caption = NULL, $alllanguages = FALSE) {
                        $t_bord = new Tableau(1, 1, "info_border");
                        // pour ajouter une legende � l'info. (p.ex. id)
                        if (isset($caption)) {
                                $t_bord->setCaption($caption, "top");
                        }

                        $t_bord->setOptionsArray(array("HTML" => array("ALIGN" => "CENTER",
                                "CELLPADDING" => 1,
                                "WIDTH" => "80%"),
                            "class" => "info"));
                        $t = new Tableau(4, 1, "info");
                        $t->setOptionsArray(array("HTML" => array("WIDTH" => "100%",
                                "ALIGN" => "CENTER",
                                "BORDER" => 0,
                                "CELLSPACING" => 0,
                                "CELLPADDING" => 5)));
                        $t->setContenu_Cellule(0, 0, "<b><pre>infos/" . $this->getAuteur() . "/" . $this->getCategorie($sql) . "/" . $this->getDate() . "/_</pre><br>" . stripSlashes($this->getTitre()) . "_</b>");

                        // INFO LOCALISEE (lang)
                        $row = 1;
                        foreach ($this->contenu as $lang => $text) {
                                if ($text != NULL) {
                                        if (!$alllanguages && $lang != getPrimaryLanguage()) {
                                                continue;
                                        } else {
                                                $t->setContenu_Cellule($row, 0, stripSlashes($text));
                                        }
                                        if (!$alllanguages) {
                                                break;
                                        } else {
                                                $row++;
                                        }
                                }
                        }
                        $t->setOptionsArray_Cellule(0, 0, array("HTML" => array("BGCOLOR" => "#ffbb44"),
                            "class" => "info_titre"));

                        $t->setContenu_Cellule(3, 0, $this->tableauImages($sql)->fin());
                        $t_bord->setContenu_Cellule(0, 0, $t->fin());
                        return $t_bord->fin();
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
                private function tableauImages_rc(SQL &$sql, $img, $i, $j, $n) {
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
                                foreach ($infoSQL->images as $id_old) {
                                        $garde = false;
                                        foreach ($this->images as $id) {
                                                // pour les images a supprimer
                                                if ($id == $id_old) {
                                                        $garde = true;
                                                }
                                        }
                                        // si garde est false, suppression de la base de l'image id_old, plus desiree
                                        if (!$garde) {
                                                Image::DeleteSQL($sql, $id_old);
                                        }
                                }
                        }

                        // ENVOI SUR SQL
                        foreach ($this->contenu as $lang => $text) { // gestion multi-langues
                                if ($text != "") {
                                        $query = "";
                                        if ($update) { // MODIFIER
                                                $query = "UPDATE info SET categorie = \"" . $this->getCategorie() . "\", titre = \"" . addSlashes($this->getTitre()) . "\", contenu = \"" . addSlashes($text) . "\", langue = \"" . $lang . "\", date = \"" . $this->getDate() . "\", auteur = \"" . $this->getAuteur() . "\", images = \"" . $this->listeImagesId() . "\" WHERE id = " . $update;
                                        } else { // AJOUTER
                                                $query = "INSERT INTO info (categorie,titre,contenu,langue,date,auteur,images) VALUES (\"" . $this->getCategorie() . "\",\"" . addSlashes($this->getTitre()) . "\",\"" . addSlashes($text) . "\",'" . $lang . "','" . $this->getDate() . "','" . $this->getAuteur() . "',\"" . $this->listeImagesId() . "\")";
                                        }

                                        if (!$sql->query($query)) {
                                                $sql->afficheErreurs();
                                        }
                                }
                        }
                        return TRUE;
                }

        }
}
?>