<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:41:32 CEST 2004 @612 /Internet Time/
  @filename	php_image.class.inc
 */
if (!isset($_ENV['ClasseImage'])) {

        $_ENV['ClasseImage'] = 1;
        ${__FILE__} = new Index();
        include basename(${__FILE__}->r['include__php_tbl.class.inc']);



        /* !
          @class Image
          @abstract   Elle va definir une image dont l'affichage est gere par la libraire GD de PHP. Les deux constructeurs permettent deux types de creation de l'image: depuis un fichier (generalement temporaire) et depuis une chaine binaire (venant generalement d'une base SQL)
          Prise en charge du format JPEG UNIQUEMENT | SQL | Les Images sont stockees dans la table Image de la base SQL sous forme binaire.
          @discussion Uncomplete class
          TODO: V�rifier ToSQL() et LoadBin, probl�me stockage SQL et (d�)chargement SQL des images. Stockage en binaire des images. imagefromstring OK pour lecture Binaire des fichiers images. <=/=> SQL (BLOB) -> PHP (imagefromstring(String)) -> PHP output NOK
         */


        define("JPEG_QUALITY", 60);
        define("PNG_QUALITY", 6);
        define("BYTE_MODE", 0x0);
        define("FILE_MODE", 0x1);
        define("DB_MODE", 0x2);

        class Image {


                protected static $_r;
                var $r;
                var $img; // ressource image (GD2)
                var $id; // dans la base SQL
                var $file;
                var $nom;
                var $desc;
                var $scale = 1.;
                var $w = 0;
                var $h = 0;
                var $mime;
                var $mode;

                /* @constructor Image
                  @abstract constructeur libre.
                  @discussion
                 */

                function __construct($nom = "image") {
                        if (self::$_r === null) {
                            self::$_r = new Index();
                        }
                        $this->r = self::$_r;
                        $this->id = 0;
                        $this->mode = BYTE_MODE;
                        $this->nom = $nom;
                }

                function __destruct() {
                        if ($this->img) {
                                imagedestroy($this->img);
                        }
                }

                /* ----- partie priv�e ----- */

                private function load($filename) {
                        if (!file_exists($filename)) {
                                return NULL;
                        }
                        $h = fopen($filename, 'r');
                        $imagedata = "";
                        while (!feof($h)) {
                                $imagedata .= fread($h, filesize($filename));
                        }
                        fclose($h);
                        return $imagedata;
                }

                private function loadFromBytes($string, $nom = "image") {
                        // init nom
                        if ($nom == "") {
                                $this->setNom("Image" . time('U'));
                        } else {
                                $this->setNom($nom);
                        }
                        // init img
                        $this->img = imagecreatefromstring($string);
                        if (!$this->img || !isset($this->img) || $this->img == "") {
                                $this->load_error("empty");
                        }
                        $this->w = imagesx($this->img);
                        $this->h = imagesy($this->img);
                        $this->mode = BYTE_MODE;
                        return TRUE;
                }

                /** prend en chagre les URLS distantes et les noms de fichiers locaux */
                private function loadfromfile($nom = "image") {
                        $imagedata = "";
                        if (isset($this->file) && ($imagedata = $this->load($this->file))) {
                                // image ressource
                                $this->loadFromBytes($imagedata, $nom);
                                $this->mode = FILE_MODE;
                                return true;
                        } else {
                                $this->mode &= ~FILE_MODE;
                                return false;
                        }
                }

                private function loadFromSQL(SQL &$sql, $id) {
                        $result = $sql->query("SELECT * FROM image WHERE id = '$id'");
                        $dbImg = $sql->LigneSuivante_Array($result);
                        mysqli_free_result($result);
                        if ($dbImg) {
                                $nom = $dbImg['nom'];
                                $this->desc = $dbImg['description'];
                                $this->id = $id;
                                $this->loadFromBytes($dbImg['image'], $nom);
                                $this->mime = $dbImg["mime"];
                                $this->mode = DB_MODE;
                                return true;
                        } else { // il n'y a pas d'image correspondant a id dans la table
                                //echo "<b>Attention!</b>: image id:$id n'existe pas dans la base SQL.";
                                $this->load_error();
                                return false;
                        }
                }

                /* ----- partie publique ----- */

                /**
                 * Sortie image vers navigateur ou fichier (GD support png,gif and jpeg
                 * @param string $filename Le chemin reel vers le fichier de sauvegarde (acces en ecriture) Si NULL l'image est envoyee sur la sortie brute.          TODO: trouver un dossier de sauvegarde accessible sur le serveur.
                 * */
                function image($filename = NULL) {
                        if (($this->mode & FILE_MODE) != 0) {
                                $this->loadfromfile();
                        }
                        if ($this->img) {
                                $this->resize();
                                switch ($this->mime) {
                                        case "image/jpeg": case "image/jpg":
                                                imagejpeg($this->img, $filename ? $filename .= ".jpg" : NULL, JPEG_QUALITY);
                                                break;
                                        case "image/gif":
                                                imagegif($this->img, $filename ? $filename .= ".gif" : NULL);
                                                break;
                                        case "image/png":
                                                imagepng($this->img, $filename ? $filename .= ".png" : NULL, PNG_QUALITY);
                                                break;
                                        default:
                                                i_debug($this->nom . " : No support " . $this->mime);
                                                $this->load_error("Mime Type " . $this->mime);
                                                $this->setMime("image/png");
                                                $this->image($filename);
                                                break;
                                }
                        }
                        return $filename;
                }

                function setFile($file) {
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
                        //			$nom = str_replace(array("�","�","�"),"e", $nom);
                        $this->nom = $nom;
                }

                function setId($id) {
                        $this->id = $id;
                }

                function setDesc($desc) {
                        $this->desc = $desc;
                }

                /** zoom en pourcentage */
                function setScale($scale = 100) {
                        $this->scale = $scale / 100.;
                }

                function setSize($w, $h) {
                        $this->w = $w;
                        $this->h = $h;
                }

                /** pr�serve le ratio de w/h */
                function setWidth($w) {
                        if ($this->w != 0) {
                                $this->setSize($w, $this->h * ($w / $this->w));
                        } else {
                                $this->setSize($w, $this->h);
                        }
                }

                /** pr�serve le ratio de w/h */
                function setHeight($h) {
                        if ($this->h != 0) {
                                $this->setSize($h, $this->w * ($h / $this->h));
                        } else {
                                $this->setSize($h, $this->w);
                        }
                }

                /** zoom percentage */
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
                        $res = true;
                        if (!isset($this->img)) {
                                trigger_error("Image resource not defined, can't resize().", E_USER_WARNING);
                                $res = false;
                        }
                        if (isset($this->scale) && $res) {
                                $width = imagesx($this->img);
                                $height = imagesy($this->img);
                                $dst = $this->img;
                                $nw = $this->w * $this->scale;
                                $nh = $this->h * $this->scale;
                                if ($nw != $width || $nh != $height) {
                                        $dst = imagecreate($nw, $nh);
                                        $res = imagecopyresampled($dst, $this->img, 0, 0, 0, 0, $nw, $nh, $width, $height);
                                }
                                $this->img = $dst;
                        }
                        if (!$res) {
                                trigger_error($this->id . " resizing failed.");
                        }
                        return $res;
                }

                // probleme d'acces au fichier
                function setMime($mime) {
                        $this->mime = $mime;
                }

                // wrapper erreurimage
                function load_error($errmsg = "Erreur") {
                        $this->img = self::ErreurImage($errmsg);
                        $this->setSize(imagesx($this->img), imagesy($this->img));
                        $this->mode = BYTE_MODE;
                }

                function setImageGD(&$img, $mime) {
                        $this->img = $img;
                        if ($img) {
                                $this->w = imagesx($img);
                                $this->h = imagesy($img);
                        }
                        $this->setMime($mime);
                        $this->mode = BYTE_MODE;
                }

                public static function ErreurImage($errmsg) {
                        $font = 1;
                        // gestion d'erreur : creer une image vide
                        $img = imagecreate(strlen($errmsg) * imagefontwidth($font) + 10, 30); /* Cr�ation d'une image blanche */
                        $bgc = imagecolorallocate($img, 255, 255, 255);
                        $tc = imagecolorallocate($img, 0, 0, 0);
                        imagefilledrectangle($img, 0, 0, 150, 30, $bgc);
                        // Affichage d'un message d'erreur
                        imagestring($img, $font, 5, 5, $errmsg, $tc);
                        return $img;
                }

                /**
                 * ecrit sur la sortie ou attend flush (echo = 0)
                */
                function raw_http_bytes($echo = 1, $file = NULL) {
                        // conversion de la sortie pour les images
                        mb_http_output("pass");
                        ob_start("mb_output_handler");
                        header("Content-type: " . $this->mime);
                        $cache = $this->image($file);
                        if ($cache) {
                                $this->setFile($cache);
                        }
                        if ($echo == 1) {
                                ob_flush();
                        }
                }

                /**
                 * retourner balise HTML avec $this->file comme source de l'image
                 */
                function afficher_file($echo = 0) {
                        $w = "";
                        if (isset($this->file)) {
                                $opt = array(
                                  "id" => $this->id,
                                  "w" => round($this->w * $this->scale),
                                  "h" => round($this->h * $this->scale)
                                );
                                $image = $this->file;
                                $window = array(
                                  "width"=>$opt["w"],
                                  "height"=>$opt["h"],
                                  "status" => "no",
                                  "directories"=>"no",
                                  "toolbar"=>"no",
                                  "location"=>"no",
                                  "menubar"=>"no",
                                  "scrollbar"=>"no",
                                  "resizable"=>"yes");
                                /* data-u pour slider.min.js */
                                $w .= HTML_image($image, array("html" => array("data-u" => "image", ""), "javascript" => array('onClick' => "window.open('" . $image . "','zoom ^','" . indexedArray_toString($window, ",","=", false) . "'")));
                        } else {
                                trigger_error("The variable file isn't initialized. " . $this->nom, E_USER_WARNING);
                        }
                        if ($echo == 1) {
                                echo $w;
                        } else {
                                return $w;
                        }
                }

                /**
                 * retourner une balise pour l'affichage d'image depuis la base de donn�es
                 * fichier temporaire sur le disque, depuis un script _image.php?id=n&size=n
                 *  */
                function afficher_db($echo = 0) {
                        $r = $this->r;
                        $opt = array(
                            "id" => $this->id,
                            "w" => round($this->w * $this->scale),
                            "h" => round($this->h * $this->scale)
                        );
                        if (i_islocal()) {
                                $opt["local"] = 1;
                        }
                        /* if(i_isdebug())
                          $opt["debug"] = 1; */
                        $image = $r->sitemap["e13___image"] . "?" . indexedArray_toString($opt, "&", "=", FALSE);
                        $opt = array(
                            "id" => $this->id,
                            "w" => $this->w,
                            "h" => $this->h
                        );
                        $imageScale = $r->sitemap["e13___image"] . "?" . indexedArray_toString($opt, "&", "=", FALSE);
                        /* data-u pour slider.min.js ,
                          click pour visualiser en popup
                         * */
                        $window = array(
                                "width"=>$opt["w"],
                                "height"=>$opt["h"],
                                "status" => "no",
                                "directories"=>"no",
                                "toolbar"=>"no",
                                "location"=>"no",
                                "menubar"=>"no",
                                "scrollbar"=>"no",
                                "resizable"=>"yes");
                        $w = HTML_image($image, array("html" => array("data-u" => "image"),
                              "javascript" => array('onClick' => "window.open('" . $imageScale . "','zoom ^','" . indexedArray_toString($window, ",","=", false) . "'); return;")));

                        if ($echo == 1) {
                                echo $w;
                        } else {
                                return $w;
                        }
                }

                function afficher($echo = 0) {
                        if (($this->mode & FILE_MODE) != 0) {
                                return $this->afficher_file($echo);
                        } elseif (($this->mode & DB_MODE) != 0) {
                                return $this->afficher_db($echo);
                        } else {
                                return $this->raw_http_bytes($echo);
                        }
                }

                /**
                * @param int $echo constantes TBL_*
                 * @param bool $desc avec description
                 **/
                function afficherFormatee($echo = TBL_STD, $desc = TRUE) {
                        $tbl = new Tableau(2, 1, str_replace(" ", "_", $this->nom));
                        $tbl->setOptionsArray(array("class" => "image"));
                        $tbl->setContenu_Cellule(0, 0, (0 != ($this->mode & (DB_MODE | FILE_MODE)) ? $this->afficher() : "<i>Img format error</i>"));
                        if ($desc) {
                                $tbl->setContenu_Cellule(1, 0, $this->desc);
                        } else {
                                $tbl->setContenu_Cellule(1, 0, $this->nom);
                        }
                        return $tbl->fin($echo);
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
                        if (($this->mode & BYTE_MODE) != 0) {
                                $tmp = tempnam($GLOBALS["images"], $this->nom);
                                $this->image($tmp);
                                $this->file = $tmp;
                        }
                        $imagedata = $this->load($this->file);
                        $stmt = NULL;
                        if ($imagedata && $sql->send_long_data("INSERT INTO image (nom, image, description,mime) VALUES (\"" . addslashes($this->nom) . "\", ?, \"" . addslashes($this->desc) . "\",\"" . $this->mime . "\")", $imagedata, $this->id, $stmt)) {
                                mysqli_stmt_close($stmt);
                                return $this->id;
                        }
                        i_debug("Error uploading " . $this->file . " " . $sql->afficheErreurs($stmt, 0));
                        mysqli_stmt_close($stmt);
                        return 0;
                }

                /* fonctions de base, de classe */
                /* ----- partie publique ----- */

                static function DeleteSQL(SQL &$sql, $id) {
                        if ($sql->query("DELETE FROM image WHERE id = $id")) {
                                return TRUE;
                        } else {
                                return FALSE;
                        }
                }

        }

}
?>
