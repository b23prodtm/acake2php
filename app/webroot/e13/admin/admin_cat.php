<?php

if (!$i_sitemap) { require '../include/php_index.inc.php'; }
$r = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
require $GLOBALS['include__php_page.class.inc'];
require $GLOBALS['include__php_formulaire.class.inc'];
require $GLOBALS['include__php_SQL.class.inc'];
require $GLOBALS['include__php_constantes.inc'];
require $GLOBALS['include__php_module_html.inc'];
require $GLOBALS['include__php_module_cat.inc'];

$pCat = new ADMIN_Page($r, "admin__cat", session_id());
$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

$pCat->ajouterContenu("<center><b>" . $pCat->getTitre() . "</b></center><br>");
$liste = HTML_listeDebut();
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__cat'] . "?ajouter=1", $r->lang("ajouter","categories")));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__cat'] . "?supprimer=1", $r->lang("supprimer","categories")));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__cat'] . "?modifier=1", $r->lang("modifier","categories")));
$liste .= HTML_listeFin();
$pCat->ajouterContenu($liste);

/* AJOUTER */
if (filter_input(INPUT_GET, 'ajouter')) {
        if (filter_input(INPUT_GET, 'ajouter') === 'publie') {
                $cp = "";
                if (filter_input(INPUT_POST, 'newcat_parent') > 0) {
                        $cp = filter_input(INPUT_POST, 'newcat_parent');
                } else {
                        $cp = "0";
                }
                if (!$sql->query("INSERT INTO categorie (nom, parent) VALUES ('" . filter_input(INPUT_POST, 'newcat') . "', '$cp')")) {
                        $pCat->ajouterMessage($r->lang("cat_sauve_echec", "categories"));
                        $sql->afficheErreurs();
                } else {
                        $pCat->ajouterMessage($r->lang("cat_sauve", "categories") . " (".filter_input(INPUT_POST, 'newcat') . ").");
                }
                unset($_POST);
        }

        $f = new Formulaire($r->lang("ajouter", "categories"), filter_input(INPUT_SERVER, 'PHP_SELF') . "?ajouter=publie");

        $newCat = new ChampTexte("newcat", $r->lang("newcat_lab","categories"), $r->lang("newcat_dsc","categories"), 20, 15);

        $newCat_parent = CAT_getSelect($sql, "newcat_parent", $r->lang("newcat_parent_lab","categories"), $r->lang("newcat_parent_dsc","categories"));

        $effacer = new ChampEffacer($r->lang("effacer", "form"));
        $valider = new ChampValider($r->lang("valider", "form"), $r->lang("ajouter", "categories"));

        $f->ajouterChamp($newCat);
        $f->ajouterChamp($newCat_parent);
        $f->ajouterChamp($effacer);
        $f->ajouterChamp($valider);

        $pCat->ajouterContenu($f->fin());
}
/* SUPPRIMER */
if (filter_input(INPUT_GET, 'supprimer')) {
        if (filter_input(INPUT_GET, 'supprimer') === 'publie') {
                $post = filter_input_array(INPUT_POST);
                $key = "cat";
                if ($post && array_key_exists($key, $post)) {
                        $catQuery = postArrayVersQueryID($key, $post);

                        if ($catQuery != "") {
                                $res = $sql->query("SELECT id FROM categorie WHERE parent IN ($catQuery)");
                                while ($cat = $sql->LigneSuivante_Array($res)) {
                                        $catQuery .= $sep . $cat['id'];
                                        $sep = ',';
                                }
                                $catQuery .= $sep . $catQuery;
                                mysqli_free_result($res);
                        }
                        // suppression
                        $res = $sql->query("DELETE FROM categorie WHERE id IN($catQuery)");
                        if ($res) {
                                $pCat->ajouterMessage($r->lang("cat_effacees", "categories") . " (". $sql->lignesAffectees() . ")");
                        }
                }
        }
        $f = new Formulaire($r->lang("supprimer","categories"), $GLOBALS['admin__cat'] . "?supprimer=publie", VERTICAL);
        // nombre de champs
        $n = 5;
        for ($i = 0; $i < $n; $i++) {
                $chSelect = CAT_getSelect($sql, "cat[]", $r->lang("cat_select_lab", "categories"), $r->lang("cat_select_dsc", "categories"));
                $f->ajouterChamp($chSelect);
        }
        $valid = new ChampValider($r->lang("valider","form"));
        $e = new ChampEffacer($r->lang("annuler","form"));
        $f->ajouterChamp($valid);
        $f->ajouterChamp($e);
        $pCat->ajouterContenu($f->fin(0));
}

/* MODIFIER */
if (filter_input(INPUT_GET, 'modifier')) {
        if (filter_input(INPUT_POST, 'new_nom') && filter_input(INPUT_GET, 'modifier') === 'publie') {
                $cat = CAT_getCat(filter_input(INPUT_POST, 'cat_mod'), $sql);
                //les valeurs par defaut correspondent à:
                $nom = filter_input(INPUT_POST, 'new_nom');
                if (filter_input(INPUT_POST, 'new_parent') == -1) {
                        $parent = "NULL";
                } else {
                        $parent = filter_input(INPUT_POST, 'new_parent');
                }
                $id = $cat['id'];
                if ($nom === NULL || !$nom) {
                        $pCat->ajouterMessage($r->lang("cat_incomplet","categories"));
                } elseif (!$sql->query("UPDATE categorie SET nom = '" . $nom . "', parent = " . $parent . " WHERE id = " . $id)) {
                        $pCat->ajouterMessage($r->lang("cat_sauve_echec","categories"));
                } else {
                        $pCat->ajouterMessage($r->lang("cat_sauve", "categories"));
                }
        }
        // formulaire
        $f = new Formulaire($r->lang("modifier", "categories"), $GLOBALS['admin__cat'] . "?modifier=publie", VERTICAL);
        $chSelect = CAT_getSelect($sql, "cat_mod", $r->lang("cat_select_lab", "categories"));
        if (filter_input(INPUT_POST, 'cat_mod')) {
                $chSelect = new ChampCache("cat_mod", filter_input(INPUT_POST, 'cat_mod'));
                $cat = CAT_getCat(filter_input(INPUT_POST, 'cat_mod'), $sql); // données de la categorie
                $f->nom = $r->lang("modifier", "categories") . CAT_getNom($cat, $sql);

                $chNom = new ChampTexte("new_nom", $r->lang("newcat_lab", "categories"),$r->lang("newcat_dsc", "categories"), 10, NULL, $cat["nom"]);
                $f->ajouterChamp($chNom);
                $chParent = CAT_getSelect($sql, "new_parent", $r->lang("newcat_parent_lab", "categories"),$r->lang("newcat_parent_dsc", "categories")." (" . $cat["parent"] . ")", $cat['parent'],$cat["nom"]);
                $f->ajouterChamp($chParent);
        }
        $valid = new ChampValider($r->lang("valider", "form"));
        $e = new ChampEffacer($r->lang("effacer", "form"));
        $f->ajouterChamp($chSelect);
        $f->ajouterChamp($valid);
        $f->ajouterChamp($e);
        $pCat->ajouterContenu($f->fin(0));
}

$sql->close();
$pCat->fin();
?>