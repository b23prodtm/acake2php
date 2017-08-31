<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:41:32 CEST 2004 @612 /Internet Time/
  @filename	php_image.class.inc
 */
global $ClasseImage;
if (!isset($ClasseImage)) {

        $ClasseImage = 1;
        require($GLOBALS['include__php_tbl.class.inc']);


        /* !
          @class Image
          @abstract   Elle va definir une image dont l'affichage est gere par la libraire GD de PHP. Les deux constructeurs permettent deux types de creation de l'image: depuis un fichier (generalement temporaire) et depuis une chaine binaire (venant generalement d'une base SQL)
          Prise en charge du format JPEG UNIQUEMENT | SQL | Les Images sont stockees dans la table Image de la base SQL sous forme binaire.
          @discussion Uncomplete class
          TODO: Vérifier ToSQL() et LoadBin, problème stockage SQL et (dé)chargement SQL des images. Stockage en binaire des images. imagefromstring OK pour lecture Binaire des fichiers images. <=/=> SQL (BLOB) -> PHP (imagefromstring(String)) -> PHP output NOK
         */


        define("JPEG_QUALITY", 100);
        define("BYTE_MODE", 0x0);
        define("FILE_MODE", 0x1);
        define("DB_MODE", 0x2);

        class Image {

                var $img; // ressource image (GD2)
                var $id; // dans la base SQL
                var $file;
                var $nom;
                var $desc;
                var $scale;
                var $w, $h;
                var $mime;
                var $mode;

                /* @constructor Image
                  @abstract constructeur libre.
                  @discussion
                 */

                function __construct() {
                        $this->id = 0;
                        $this->mode = BYTE_MODE;
                }

                function __destruct() {
                        if ($this->img) {
                                imagedestroy($this->img);
                        }
                }

                /* ----- partie privée ----- */

                function loadfromfile($nom = "image") {
                        if (isset($this->file) && fopen($this->file, 'r')) {
                                $imagedata = fread($this->file, MAX_FILE_SIZE);
                                fclose($this->file);
                                // image ressource
                                $this->loadFromBytes($imagedata, $nom);
                                $this->mode = FILE_MODE;
                                return true;
                        } else {
                                $this->mode &= ~FILE_MODE;
                                return false;
                        }
                }

                function loadFromBytes($string, $nom = "image") {
                        // init nom
                        if ($nom == "") {
                                $this->setNom("Image" . time('U'));
                        } else {
                                $this->setNom($nom);
                        }
                        // init img
                        $this->img = imagecreatefromstring($string);

                        if (!$this->img || !isset($this->img) || $this->img == "") {
                                $this->load_error();
                        }
                        $this->w = imagesx($this->img);
                        $this->h = imagesy($this->img);
                        $this->file = NULL;
                        $this->mode = BYTE_MODE;
                        return TRUE;
                }

                /* ----- partie publique ----- */

                function setFile(&$file) {
                        $this->file = $file;
                        if ($file) {
                                $this->mode = FILE_MODE;
                        } else {
                                $this->mode &= ~FILE_MODE;
                        }
                }

                function setNom($nom) {
                        // nettoyage du nom de l'image pour stockage sur fichier
                        //			$nom = str_replace(array(" ","'","+"),array("_","","-"), $nom);
                        //			$nom = str_replace(array("é","è","ê"),"e", $nom);
                        $this->nom = $nom;
                }

                function setId($id) {
                        $this->id = $id;
                }

                function setDesc($desc) {
                        $this->desc = $desc;
                }

                function setScale($scale = "100") { // pour le zoom $size=int() et en px size=string(int()."x".int())
                        $this->scale = $scale / 100.;
                }

                function setSize($w, $h) {
                        $this->w = $w;
                        $this->h = $h;
                }

                function getScale() {
                        return ceil($this->scale * 100);
                }

                function getWidth() {
                        return $this->w;
                }

                function getHeight() {
                        return $this->h;
                }

                function resize() {
                        if (isset($this->img) && isset($this->scale)) {
                                $width = imagesX($this->img);
                                $height = imagesY($this->img);
                                $dst = $this->img;
                                $nw = $this->w * $this->scale;
                                if ($nw != $width) {
                                        $dst = imagescale($this->img, $nw);
                                }
                                $nh = $this->h * $this->scale;
                                if ($nh != $height) {
                                        $dst = imagescale($this->img, $nh);
                                }
                                $this->img = $dst;
                                return true;
                        } else {
                                trigger_error("Image ressource not defined, can't do resize().", E_WARNING);
                        }
                }

                // problème d'accès au fichier
                function setMime($mime) {
                        /* list($width,$height,$type,$attr) = getimagesize($file);
                          $this->mime = image_type_to_mime_type($type); */
                        $this->mime = $mime;
                }

                // wrapper erreurJpeg
                function load_error() {
                        $this->img = Image::ErreurImage();
                        $this->setSize(imagesx($this->img), imagesy($this->img));
                        $this->mode = BYTE_MODE;
                }

                function setImageGD(&$img, $mime) {
                        $this->img = $img;
                        $this->setMime($mime);
                        $this->mode = BYTE_MODE;
                }

                public static function ErreurImage() {
                        // gestion d'erreur JPG: créer une image vide
                        $img = imagecreate(150, 30); /* Création d'une image blanche */
                        $bgc = imagecolorallocate($img, 255, 255, 255);
                        $tc = imagecolorallocate($img, 0, 0, 0);
                        imagefilledrectangle($img, 0, 0, 150, 30, $bgc);
                        // Affichage d'un message d'erreur
                        imagestring($img, 1, 5, 5, "Erreur de chargement de l'image", $tc);
                        return $img;
                }

                /**
                  affichage sur stdout, envoyer directement au navigateur l'image stockée dans $this->file ou $this->img
                 * enclenchement de la bufferisation de sortie --output buffering--, fonction callback qui appelle la fonction de conversion avant l'envoi de la sortie au navigateur. Les entetes http ne sont pas affectés, ils sont envoyés à leur apppel. */
                function afficher_bytes() {
                        // conversion de la sortie pour les images
                        mb_http_output("pass");
                        ob_start("mb_output_handler");
                        header("Content-type: " . $this->mime);
                        $this->resize();
                        switch ($this->mime) {
                                case "image/jpeg": case "image/jpg":
                                        imagejpeg($this->img);
                                        break;
                                case "image/gif":
                                        imagegif($this->img);
                                        break;
                                case "image/png":
                                        imagepng($this->img);
                                        break;
                                default:
                                        die("Pas de support $this->mime sur cette page.");
                                        break;
                        }
                        imagedestroy($this->img);
                        ob_end_flush();
                }

                /**
                 * retourner balise HTML avec $this->file comme source de l'image
                 */
                function afficher_file() {
                        if (isset($this->file)) {
                                if (file_exists($this->file)) {
                                        error_reporting($old);
                                        $image = $GLOBALS["e13___image"] . "?file=" . urlencode($this->file) . "&size=" . (4 * $this->scale);
                                        return HTML_image($image, array("javascript" => array('onClick' => "window.open('" . $image . "','b23::Open Window ^','width=this.width*4, height=this.height*4, status=no, directories=no, toolbar=no, location=no, menubar=no,scrollbars=no, resizable=yes'")));
                                } else {
                                        trigger_error("display(2):File $this->file not found! display() returned unsuccessfully.", E_WARNING);
                                }
                        } else {
                                trigger_error("display(2):Function call setFile() expected for variable file isn't initialized.", E_WARNING);
                        }
                }

                /**
                 * retourner une balise pour l'affichage d'image depuis la base de données
                 * fichier temporaire sur le disque, depuis un script _image.php?id=n&size=n 
                 *  */
                function afficher_db() {
                        if ($this->scale == "") {
                                $this->setSize("100");
                        }
                        $image = $GLOBALS["include___image"] . "?file=" . urlencode(serialize($this->file)) . "&size=" . (4 * $this->scale);
                        return HTML_image($GLOBALS["include___image"] . "?id=" . $this->id . "&size=" . $this->scale, array("javascript" => array('onClick' => "window.open('" . $image . "','b23::Open Window ^','width=this.width*4, height=this.height*4, status=no, directories=no, toolbar=no, location=no, menubar=no,scrollbars=no, resizable=yes'")));
                }

                function afficher() {
                        if (($this->mode & FILE_MODE) != 0) {
                                $this->afficher_file();
                        } elseif (($this->mode & DB_MODE) != 0) {
                                $this->afficher_db();
                        } else {
                                $this->afficher_bytes();
                        }
                }

                function afficherFormatee($mode = 0, $desc = TRUE) {
                        $tbl = new Tableau(2, 1, str_replace(" ", "_", $this->nom));
                        $tbl->setOptionsArray(array("class" => "image"));
                        $tbl->setContenu_Cellule(0, 0, "<center>" . (0 != ($this->mode & (DB_MODE | FILE_MODE)) ? $this->afficher() : "<i>No Img</i>") . "<center>");
                        if ($desc) {
                                $tbl->setContenu_Cellule(1, 0, $this->nom . " : " . $this->desc);
                        } else {
                                $tbl->setContenu_Cellule(1, 0, "<center>" . $this->nom . "</center>");
                        }
                        if ($mode == 0) {
                                return $tbl->fin();
                        } // HTML
                        if ($mode == 1) {
                                $tbl->fin(1);
                        } // to stdout
                        return true;
                }

                /* PRINCIPALES ---- fonctions vers/depuis la base SQL */

                // wrappers
                function ToSQL(SQL &$sql) {
                        $this->saveToSQL($sql);
                }

                function FromSQL(SQL &$sql, $id) {
                        $this->loadFromSQL($sql, $id);
                }

                function saveToSQL(SQL &$sql) {
                        if (isset($this->file)) {
                                // mime type
                                /* le fichier uploadé $this->file est temporairement accessible , s'il est touché il s'efface. image_type_to_mime_type "touche" le fichier alors il faut une copie pour préserver le fichier a charger sur la base. */
                                /* $tmp = tempnam(realpath(filter_input(INPUT_SERVER,'SCRIPT_NAME')), "IMG_");
                                  if(copy($this->file,$tmp)) debug ("Fichier déplacé avec succès. $this->file -> $tmp");
                                  chmod($tmp, 0777); */
                                //$this->setMime($this->file);

                                if (!$sql->query("INSERT INTO image (nom, image, description,mime) VALUES (\"" . addslashes($this->nom) . "\", \"\", \"" . addslashes($this->desc) . "\",\"" . $this->mime . "\")")) {
                                        die("L'image n'a pas correctement ete stockee sur la base SQL: " . $sql->afficheErreurs());
                                }
                                // init id
                                $this->setId(mysqli_insert_id($sql->connexion));
                                $imagedata = fread($this->file, 2000000);
                                fclose($this->file);
                                // insertion du contenu de l'image
                                //if(!$sql->query("UPDATE image SET image=LOAD_FILE('$this->file') WHERE id=$this->id")) die("L'image n'a pas correctement ete stockee sur la base SQL: ".mysqli_error());
                                if (!$sql->query("UPDATE image SET image=\"" . addslashes($imagedata) . "\" WHERE id=$this->id")) {
                                        die("L'image n'a pas correctement ete stockee sur la base SQL: " . $sql->afficheErreurs());
                                }
                                echo HTML_image($GLOBALS["include___image"] . "?id=" . $this->id);
                                // retourner l'id de l'image ainsi stockée pour ne pas perdre sa trace
                                return $this->id;
                        } else {
                                die("image.class: ToSQL(): Pas d'image chargée!!!");
                        }
                }

                function loadFromSQL(SQL &$sql, $id) {
                        $image = $sql->query("SELECT * FROM image WHERE id = '$id'");
                        $img = $sql->LigneSuivante_Array($image);
                        mysqli_free_result($image);
                        if ($img) {
                                $nom = stripslashes($img['nom']);
                                $this->desc = stripslashes($img['description']);
                                $this->setId($id);
                                $this->loadFromBytes($img['image'], $nom);
                                $this->mime = stripslashes($img["mime"]);
                                return true;
                        } else { // il n'y a pas d'image correspondant a id dans la table                                                         
                                //echo "<b>Attention!</b>: image id:$id n'existe pas dans la base SQL.";
                                $this->loadFromBytes("", "Image introuvable");
                                return false;
                        }
                }

                /* fonctions de base, de classe */
                /* ----- partie publique ----- */

                function DeleteSQL(SQL &$sql, $id) {
                        if ($sql->query("DELETE FROM image WHERE id = $id")) {
                                return TRUE;
                        } else {
                                return FALSE;
                        }
                }

        }

}
?>