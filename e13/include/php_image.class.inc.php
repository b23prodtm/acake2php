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

        class Image {

                var $img; // ressource image (GD2)
                var $id; // dans la base SQL
                var $file;
                var $nom;
                var $desc;
                var $size; // en % ou en px: $nX."x".$nY
                var $mime;

                /* @constructor Image
                  @abstract constructeur libre.
                  @discussion
                 */

                function __construct() {
                        $this->id = 0;
                }

                /* ----- partie privée ----- */

                function loadfromfile($nom = "image") {
                        if (isset($this->file)) {
                                if (fopen($this->file, 'r')) {
                                        $imagedata = fread($this->file, MAX_FILE_SIZE);
                                        fclose($this->file);
                                        // image ressource
                                        $this->loadFromBinary($imagedata, $nom);
                                        return true;
                                } else
                                        return false;
                        } else
                                return false;
                }

                function loadBin($string, $nom = "image") {
                        // init nom
                        if ($nom == "") {
                                $this->setNom("Image" . time('U'));
                        } else {
                                $this->setNom($nom);
                        }
                        // init img
                        $this->img = @imagecreatefromstring($string);

                        if (!$this->img || !isset($this->img) || $this->img == "") {
                                $this->erreurJpeg();
                        }
                        // redimensionner
                        /* if(strrpos($this->size,'x')) {
                          debug("this resized");
                          $size = $this->strSize();
                          $x = $size["nX"];
                          $y = $size["nY"];
                          $tc = imagecreatetruecolor($x, $y);
                          $src = $this->img;
                          imagecopyresized($tc,$src,0,0,0,0,$x,$y,imagesx($src),imagesy($src));
                          $this->img = $tc;
                          } */
                        $this->file = FALSE;
                        return TRUE;
                }

                /* ----- partie publique ----- */

                function setFile(&$file) {
                        $this->file = $file;
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

                function setSize($size = "100") { // pour le zoom $size=int() et en px size=string(int()."x".int())
                        $this->size = $size;
                }

                /**
                  retourne un array(sizex[%;px],sizey[%;px])
                 *                  */
                function strSize() {
                        if (!strrpos($this->size, 'x')) // zoom (%)
                                return array("nX" => $this->size . "%", "nY" => $this->size . "%");
                        else { // redimensionner (resample)
                                $nX = substr($this->size, 0, strrpos($this->size, 'x'));
                                $nY = substr($this->size, strrpos($this->size, 'x') + 1, strlen($this->size));
                                return array("nX" => $nX, "nY" => $nY);
                        }
                }

                private function newSize() {
                        $size = array(0, 0);
                        if (isset($this->img)) {
                                $size[0] = imagesX($this->img);
                                $size[1] = imagesY($this->img);
                        }
                        if (isset($this->size)) {
                                $size = $this->strSize();
                                $new_height = $size["nY"];
                                $new_width = $size["nX"];
                                if (strrpos($new_width, '%')) {
                                        $new_width = (int) ceil($size[0] * (substr($size["nX"], 0, -1) / 100));
                                        $new_height = (int) ceil($size[1] * ($new_width / $size[0]));
                                }
                                return array($new_width, $new_height);
                        } else {
                                trigger_error("Image size was not defined.", E_WARNING);
                                $this->size = $size;
                                return $size;
                        }
                }

                function resize() {
                        if (isset($this->img) && isset($this->size)) {
                                $width = imagesX($this->img);
                                $height = imagesY($this->img);
                                $size = $this->newSize();
                                $dst = $this->img;
                                if ($size[0] != $width) {
                                        $dst = imagescale($this->img, $size[0]);
                                }
                                if (imagesY($dst) > $size[1]) {
                                        $dst = imagescale($dst, $size[1]);
                                }
                                $this->img = $dst;
                                $this->setSize($size[0] . "x" . $size[1]);
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

                // wrapper loadBin()
                function loadFromBinary(&$string, $nom = "image") {
                        $this->loadBin($string, $nom = "image");
                }

                // wrapper erreurJpeg
                function load_error() {
                        $this->erreurJpeg;
                }

                function erreurJpeg() {
                        // gestion d'erreur JPG: créer une image vide
                        $this->img = imagecreate(150, 30); /* Création d'une image blanche */
                        $bgc = imagecolorallocate($this->img, 255, 255, 255);
                        $tc = imagecolorallocate($this->img, 0, 0, 0);
                        imagefilledrectangle($this->img, 0, 0, 150, 30, $bgc);
                        // Affichage d'un message d'erreur
                        imagestring($this->img, 1, 5, 5, "Erreur de chargement de l'image", $tc);
                }

                /**
                  affichage sur stdout, envoyer directement au navigateur l'image stockée dans $this->file ou $this->img
                 * enclenchement de la bufferisation de sortie --output buffering--, fonction callback qui appelle la fonction de conversion avant l'envoi de la sortie au navigateur. Les entetes http ne sont pas affectés, ils sont envoyés à leur apppel. */
                function afficher_f() {
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
                        ob_end_flush();
                }

                /**
                 * retourner balise HTML avec $this->file comme source de l'image
                 */
                function afficher_h() {
                        if (isset($this->file)) {
                                if (file_exists($this->file)) {
                                        error_reporting($old);
                                        $image = $GLOBALS["e13___image"] . "?file=" . urlencode($this->file) . "&size=" . (4 * $this->size);
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
                function afficher_s() {
                        if ($this->size == "") {
                                $this->setSize("100");
                        }
                        $image = $GLOBALS["include___image"] . "?file=" . urlencode(serialize($this->file)) . "&size=" . (4 * $this->size);
                        return HTML_image($GLOBALS["include___image"] . "?id=" . $this->id . "&size=" . $this->size, array("javascript" => array('onClick' => "window.open('" . $image . "','b23::Open Window ^','width=this.width*4, height=this.height*4, status=no, directories=no, toolbar=no, location=no, menubar=no,scrollbars=no, resizable=yes'")));
                }

                function afficher($mode = 0) {
                        /* --- status:OK!! */
                        if ($mode == 1) {
                                $this->afficher_f();
                        } else if ($mode == 2) {
                                $this->afficher_h();
                        } else { // mode == 0	
                                $this->afficher_s();
                        }
                }

                function afficherFormatee($mode = 0, $desc = TRUE) {
                        $tbl = new Tableau(2, 1, str_replace(" ", "_", $this->nom));
                        $tbl->setOptionsArray(array("class" => "image"));
                        $tbl->setContenu_Cellule(0, 0, "<center>" . $this->afficher() . "<center>");
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
                function ToSQL(SQL $sql) {
                        $this->saveToSQL($sql);
                }

                function FromSQL(SQL $sql, $id) {
                        $this->loadFromSQL($sql, $id);
                }

                function saveToSQL(SQL $sql) {
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
                        } else
                                die("image.class: ToSQL(): Pas d'image chargée!!!");
                }

                function loadFromSQL(SQL $sql, $id) {
                        $image = $sql->query("SELECT * FROM image WHERE id = '$id'");
                        $img = $sql->LigneSuivante_Array($image);
                        mysqli_free_result($image);
                        if ($img) {
                                $nom = stripslashes($img['nom']);
                                $this->desc = stripslashes($img['description']);
                                $this->setId($id);
                                $this->loadBin($img['image'], $nom);
                                $this->mime = stripslashes($img["mime"]);
                                return true;
                        } else { // il n'y a pas d'image correspondant a id dans la table                                                         
                                //echo "<b>Attention!</b>: image id:$id n'existe pas dans la base SQL.";
                                $this->loadBin("", "Image introuvable");
                                return false;
                        }
                }

                /* fonctions de base, de classe */
                /* ----- partie publique ----- */

                function DeleteSQL(SQL $sql, $id) {
                        if ($sql->query("DELETE FROM image WHERE id = $id")) {
                                return TRUE;
                        } else {
                                return FALSE;
                        }
                }

                /* !
                  @function   display
                  @abstract   (description)
                  @discussion (description)
                  @param      #param 'f'|'h' f for HTML <TABLE> formatted display; 'h' for HTML <IMG> target unformatted;
                  #options '-nodesc'||'-stdout' -nodesc to avoid description displaying; -stdout to standard output (simili to print function)
                  @result     HTML string, to be printed. Warning: -stdout option prints DIRECTLY on method call!
                 */

                function display($param = 'f', $options = "") {
                        $_options = array();
                        $mode = 0;
                        $desc = TRUE;
                        // options evals
                        if ($active = TRUE) {
                                $_options["-nodesc"] = "stristr(\$options,\"-nodesc\");";
                        }
                        define("OPTION_NODESC", $_options["-nodesc"]);
                        if ($active = TRUE) {
                                $_options["-stdout"] = "stris
}tr(\$options,\"-stdout\");";
                                define("OPTION_STDOUT", $_options["-stdout"]);
                                // PARAMS
                                if ($param == 'f') {
                                        $mode = 0;
                                        $desc = TRUE;
                                } else if ($param == 'h') {
                                        if (isset($this->file)) {
                                                return $this->afficher(2);
                                        } else if (isset($this->img)) {
                                                return $this->afficher();
                                        }
                                }
                                // OPTIONS 
                                else if (eval(OPTION_NODESC)) {
                                        $desc = FALSE;
                                } else if (eval(OPTION_STDOUT)) {
                                        $mode = 1;
                                } else { // param inconnu E_ERROR thrown
                                        trigger_error("Paramètre passé à image->afficher() inconnu!", E_NOTICE);
                                }
                                return $this->afficherFormatee($mode, $desc);
                        }
                }

        }

}
?>