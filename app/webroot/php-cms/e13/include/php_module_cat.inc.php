<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:42:22 CEST 2004 @612 /Internet Time/
  @filename	php_module_cat.inc

  Module Cat ~ MVC (non-CakePhp) ~ classement par sujet des billets POST du CMS
  Le modele phpcms.categorie est implicitement lie a la VUE (module).
  Le Controleur CRUD est dans View/E14/admin_cat.ctp.
  VUE du modele phpcms.categorie :
 */
if (!isset($_ENV['ModuleCat'])) {
        $_ENV['ModuleCat'] = 1;
        ${__FILE__} = new Index();
        include basename(${__FILE__}->r['include__php_SQL.class.inc']);
        include basename(${__FILE__}->r['include__php_image.class.inc']);
        include basename(${__FILE__}->r['include__php_formulaire.class.inc']);

        /*
         @return un array contenant les champs de la table Categorie et index�s, ou false
        */
        function CAT_getCat($id, SQL &$sql) {
                $res = $sql->query("SELECT * FROM categorie WHERE id='$id'");
                if ($res) {
                        $ret = $sql->LigneSuivante_array($res);
                        mysqli_free_result($res);
                        return $ret;
                }
                return false;
        }
        /*
         @return le nom de la categorie identifiee $cat
        */
        function CAT_getNom($cat, SQL &$sql, $sep = "/") { // $cat === mysql result array
                if (is_array($cat)) {
                        $c = $cat;
                        $nom = $cat['nom'];
                        // gestion de la hierarchie: grand-m�re/categorie_m�re/categorie/
                        while ($c['parent'] > 0) {
                                $res = $sql->query("SELECT * FROM categorie WHERE id='" . $c['parent'] . "'");
                                if ($res === NULL || !($parent = $sql->LigneSuivante_array($res))) {
                                        break;
                                }
                                if ($res) {
                                        mysqli_free_result($res);
                                }
                                $nom = $parent['nom'] . $sep . $nom;
                                $c = $parent;
                        }
                        return $nom;
                } else {
                        return "no_category";
                }
        }

        /*
         @return l'instance image pour la categorie identifiee par $id
        */
        function CAT_getImage($id, SQL &$sql) {
                $cat = CAT_getCat($id, $sql);
                if ($cat) {
                        $cat_img = new Image;
                        $cat_img->FromSQL($cat['id'], $sql);
                        return $cat_img;
                }
        }

        /*
          * cree un champ SELECT avec toutes les categories existantes, valeur par defaut:
          * "aucune" => id == -1
            */
        function CAT_getSelect(SQL &$sql, $name, $libelle, $desc = "", $vPdefaut = -1, $exclure = "-----", $sep = "/") {
                // acqu�rir les categories existantes SQL
                $choix = array("---" => -1);
                $choix += CAT_getArray($sql , $sep, $exclure);
                $champ = new ChampSelect($name, $libelle, $desc, $choix, 1, $vPdefaut);
                return $champ;
        }

        /**
        * @return array nom => id
        */
        function CAT_getArray(SQL &$sql, $sep = "/", $exclure= "-----") {
                $cats = $sql->query("SELECT * FROM categorie");
                if($cats === NULL) {
                      return NULL;
                }
                $choix = array();
                while ($cat = $sql->LigneSuivante_array($cats)) {
                      // creer liste de choix
                      $nom = CAT_getNom($cat, $sql, $sep);
                      if (!stripos($nom, $exclure)) {
                            $choix[$nom] = $cat["id"];
                      }
                }
                if ($cats) {
                      mysqli_free_result($cats);
                }
                return $choix;
        }

}
?>
