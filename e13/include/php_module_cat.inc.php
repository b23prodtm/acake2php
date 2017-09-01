<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:42:22 CEST 2004 @612 /Internet Time/
  @filename	php_module_cat.inc
 */
global $ModuleCat;
if (!isset($ModuleCat)) {
        $ModuleCat = 1;
        require($GLOBALS['include__php_SQL.class.inc']);
        require($GLOBALS['include__php_image.class.inc']);
        require($GLOBALS['include__php_formulaire.class.inc']);

        // retourne un array contenant les champs de la table Categorie et indexés, ou false
        function CAT_getCat($id, SQL &$sql) {
                $res = $sql->query("SELECT * FROM categorie WHERE id='$id'");
                $ret = $sql->LigneSuivante_array($res);
                if ($res) {
                        mysqli_free_result($res);
                }
                return $ret;
        }

        function CAT_getNom($cat, SQL &$sql) { // $cat === mysql result array
                if (is_array($cat)) {
                        $c = $cat;
                        $nom = $cat['nom'];
                        // gestion de la hierarchie: grand-mère/categorie_mère/categorie/
                        while ($c['parent'] > 0) {
                                $res = $sql->query("SELECT * FROM categorie WHERE id='" . $c['parent'] . "'");
                                if (!($parent = $sql->LigneSuivante_array($res))) {
                                        break;
                                }
                                if ($res) {
                                        mysqli_free_result($res);
                                }
                                $nom = $parent['nom'] . "/" . $nom;
                                $c = $parent;
                        }
                        return $nom;
                } else {
                        return "no_category";
                }
        }

        function CAT_getImage($id, SQL &$sql) {
                $cat = CAT_getCat($id, $sql);
                if ($cat) {
                        $cat_img = new Image;
                        $cat_img->FromSQL($cat['id'], $sql);
                        return $cat_img;
                }
        }

        // cree un champ SELECT avec toutes les categories existantes, valeur par defaut: "aucune" => id == -1
        function CAT_getSelect(SQL &$sql, $name, $libelle, $desc = "", $vPdefaut = -1, $exclure = "") {
                // acquérir les categories existantes SQL
                $choix = array("---" => -1);
                $cats = $sql->query("SELECT * FROM categorie");
                while ($cat = $sql->LigneSuivante_array($cats)) {
                        // creer liste de choix
                        $nom = CAT_getNom($cat, $sql);
                        if (!stripos($nom, $exclure)) {
                                $choix[$nom] = $cat["id"];
                        }
                }
                if ($cats) {
                        mysqli_free_result($cats);
                }

                $champ = new ChampSelect($name, $libelle, $desc, $choix, 1, $vPdefaut);
                return $champ;
        }

}
?>