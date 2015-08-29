<?php

require("../include/php_registre.inc.php");
$r = new Registre(filter_input(INPUT_SERVER, 'PHP_SELF'));
require($GLOBALS['include__php_page.class.inc']);
require($GLOBALS['include__php_formulaire.class.inc']);
require($GLOBALS['include__php_SQL.class.inc']);
require($GLOBALS['include__php_constantes.inc']);
require($GLOBALS['include__php_module_html.inc']);
require($GLOBALS['include__php_module_cat.inc']);

$pCat = new ADMIN_Page($r, "admin__cat", session_id());
$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

$pCat->ajouterContenu("<center><b>" . $pCat->getTitre() . "</b></center><br>");
$liste = HTML_listeDebut();
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__cat'] . "?ajouter=1", "> Ajouter une cat�gorie"));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__cat'] . "?supprimer=1", "> Supprimer une cat�gorie"));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__cat'] . "?modifier=1", "> Modifier une cat�gorie"));
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
                        $pCat->ajouterMessage("Erreur d'insertion!! ");
                        $sql->afficheErreurs();
                } else {
                        $pCat->ajouterMessage("Cat�gorie " . filter_input(INPUT_POST, 'newcat') . " ajout�e.");
                }
                unset($_POST);
        }

        $f = new Formulaire("Ajouter une categorie", filter_input(INPUT_SERVER, 'PHP_SELF') . "?ajouter=publie");

        $newCat = new ChampTexte("newcat", "Nom de la nouvelle cat�gorie (15 char. max)", "Cette cat�gorie sera r�pertori�e dans une base de donn�e, utile pour les infos et les produits en stock.", 20, 15);

        $newCat_parent = CAT_getSelect($sql, "newcat_parent", "Nom de la cat�gorie m�re", "Si la nouvelle cat�gorie doit faire partie d'une cat�gorie d�j� existante, s�lectionner ici la correspondance");

        $effacer = new ChampEffacer("Effacer");
        $valider = new ChampValider("Ajouter", "La categorie sera ajouter � la base de donn�e, elle sera utilisable imm�diatement dans les autres parties du site");

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
                if (array_key_exists($key, $post)) {
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
                                $pCat->ajouterMessage("Les cat�gories ont �t� supprim�es. (" . $sql->lignesAffectees() . " affected rows)");
                        }
                        unset($_POST);
                }
        }
        $f = new Formulaire("Supprimer une/des cat�gorie/s", $GLOBALS['admin__cat'] . "?supprimer=publie", VERTICAL);
        // nombre de champs
        $n = 5;
        for ($i = 0; $i < $n; $i++) {
                $chSelect = CAT_getSelect($sql, "cat[]", "S�lectionner la cat�gorie:", "Toutes les cat�gories descendantes seront supprim�es!! ex: supprimer <i>Consoles</i>, et toutes les cat�gories <i>Consoles/.../...</i> seront d�finitivement supprim�es.");
                $f->ajouterChamp($chSelect);
        }
        $valid = new ChampValider("Supprimer");
        $e = new ChampEffacer("Annuler");
        $f->ajouterChamp($valid);
        $f->ajouterChamp($e);
        $pCat->ajouterContenu($f->fin(0));
}

/* MODIFIER */
if (filter_input(INPUT_GET, 'modifier')) {
        if (filter_input(INPUT_POST, 'new_nom') && filter_input(INPUT_GET, 'modifier') === 'publie') {
                $cat = CAT_getCat(filter_input(INPUT_POST, 'cat_mod'), $sql);
                //les valeurs par defaut correspondent �:
                $nom = filter_input(INPUT_POST, 'new_nom');
                if (filter_input(INPUT_POST, 'new_parent') == -1) {
                        $parent = "NULL";
                } else {
                        $parent = filter_input(INPUT_POST, 'new_parent');
                }
                $id = $cat['id'];
                if ($nom === NULL || !$nom) {
                        $pCat->ajouterMessage("Nom de la cat�gorie incomplet (NULL).");
                } elseif (!$sql->query("UPDATE categorie SET nom = '" . $nom . "', parent = " . $parent . " WHERE id = " . $id)) {
                        $pCat->ajouterMessage("Erreur d'insertion dans la base!");
                } else {
                        $pCat->ajouterMessage("La cat�gorie $nom est enregistr�e!");
                }
                unset($_POST);
        }
        // formulaire
        $f = new Formulaire("Modifier une cat�gorie", $GLOBALS['admin__cat'] . "?modifier=publie", VERTICAL);
        $chSelect = CAT_getSelect($sql, "cat_mod", "S�lectionner la cat�gorie:");
        if (filter_input(INPUT_POST, 'cat_mod')) {
                $chSelect = new ChampCache("cat_mod", filter_input(INPUT_POST, 'cat_mod'));
                $cat = CAT_getCat(filter_input(INPUT_POST, 'cat_mod'), $sql); // donn�es de la categorie
                $f->nom = "Modifier la cat�gorie " . CAT_getNom($cat, $sql);

                $chNom = new ChampTexte("new_nom", "Quel est le nouveau nom ?", "", 10, NULL, $cat["nom"]);
                $f->ajouterChamp($chNom);
                $chParent = CAT_getSelect($sql, "new_parent", "Quel est la cat�gorie m�re ?", "Valeur actuelle (" . $cat["parent"] . ")", $cat['parent']);
                $f->ajouterChamp($chParent);
        }
        $valid = new ChampValider("OK");
        $e = new ChampEffacer("Effacer");
        $f->ajouterChamp($chSelect);
        $f->ajouterChamp($valid);
        $f->ajouterChamp($e);
        $pCat->ajouterContenu($f->fin(0));
}

$sql->close();
$pCat->fin();
?>