<?php

require('../include/php_registre.inc.php');
$r = new Registre(filter_input(INPUT_SERVER, 'PHP_SELF'));
require($GLOBALS['include__php_page.class.inc']);
require($GLOBALS['include__php_formulaire.class.inc']);
require($GLOBALS['include__php_SQL.class.inc']);
require($GLOBALS['include__php_constantes.inc']);
require($GLOBALS['include__php_info.class.inc']);
require($GLOBALS['include__php_image.class.inc']);

$pAdmin = new ADMIN_Page($r, "admin__infos", session_id());
$pAdmin->ajouterContenu("<br><center><b>Gestion des infos</b></center><br>");
$liste = HTML_listeDebut();
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__infos'] . "?ajouter=1", $r->lang("ajouter","infos")));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__infos'] . "?modifier=1", $r->lang("modifier","infos")));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__infos'] . "?supprimer=1", $r->lang("supprimer", "infos")));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__infos'] . "?afficher=1", $r->lang("voirtous","infos")));
$liste .= HTML_listeElement(HTML_lien($GLOBALS['admin__index'], $r->lang("retouradmin", "infos")));
$liste .= HTML_listeFin();
$pAdmin->ajouterContenu($liste);


/* scripts pour ajouter, modifier puis publier une info dans la table SQL info */
$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

// publication d'une info dans la base SQL
if (filter_input(INPUT_GET, 'ajouter') === "publie" || filter_input(INPUT_GET, 'modifier') === "publie") {

        /* definition de la date */
        $date = getdate(time());
        $lang = filter_input(INPUT_POST,'i_lang');
        $info = new Info($sql, $result, filter_input(INPUT_POST, 'i_titre' . $lang), filter_input(INPUT_POST, 'i_auteur'), filter_input(INPUT_POST, 'i_categorie'), $date['year'] . "-" . $date['mon'] . "-" . $date['mday'], $lang);
        $info->ajouterContenu(filter_input(INPUT_POST, 'i_contenu' . $lang), $lang);
        // verifier l'image champ info_image:_nom:_desc postee et la stocker en base SQL deja
        /* -- stockage de l'image -- */
        if (is_uploaded_file($_FILES['i_image']['tmp_name'])) {
                $image = new Image();
                $file = fopen($_FILES['i_image']['tmp_name'], 'r');
                $image->setFile($file);
                // format d'image
                $image->setMime("image/png");
                //			debug(print_r($_FILES['i_image']));
                //			die();
                if (!filter_input(INPUT_POST, 'i_image_nom') || strlen(filter_input(INPUT_POST, 'i_image_nom')) < 1) {
                        $image_nom = $_FILES['i_image']['name'];
                } else {
                        $image_nom = filter_input(INPUT_POST, 'i_image_nom');
                }
                $image->setNom($image_nom);
                //$image->loadBin(file_get_contents($_FILES['i_image']['tmp_name']), $image_nom);
                $image->setDesc(filter_input(INPUT_POST, 'i_image_desc'));
                $image_id = $image->saveToSQL($sql);
                $info->ajouterImageSQL($image_id);
        }

        if (filter_input(INPUT_GET, 'modifier')) {
                // ajouter les id des images existantes a l'info
                $post = filter_input_array(INPUT_POST);
                $key = 'i_images';
                if ($post && array_key_exists($key, $post)) {
                        foreach ($post[$key] as $id) {
                                $info->ajouterImageSQL($id);
                        }
                }
                $update = $_SESSION['i_id'];
        } else {
                $update = FALSE;
        } // donner l'id stocké en session pour une modification de l'info
        if ($info->publier($sql, $update)) {
                $pAdmin->ajouterContenu("<b><center><br>[ " . filter_input(INPUT_POST, 'i_titre') . " ] " . $r->lang("publiesucces", "infos") . "</center></b>");
                if (filter_input(INPUT_GET, 'modifier')) {
                        // effacer l'id de l'info en session
                        unset($_SESSION['i_id']);
                }
        } else {
                $sql->afficheErreurs();
        }
}
/** INTERDIT ! vidage de la mémoire POST !
  unset($_POST); */
// formulaire ajouter
if (filter_input(INPUT_GET, 'ajouter')) {
        // ajouter une info, affichage d'un formulaire
        $form = Info::FormAjouter($GLOBALS['admin__infos'] . "?ajouter=publie", $sql);
        $pAdmin->ajouterContenu($form);
}

// liste des infos - formulaire modifier
if (filter_input(INPUT_GET, 'modifier')) {
        if (filter_input(INPUT_POST, 'info_a_modifier')) {
                // placer l'id de l'info en session pour la retrouver dans une requete UPDATE, prochaine etape
                $_SESSION['i_id'] = filter_input(INPUT_POST, 'info_a_modifier');
                //debug("post");
                $info = $sql->query("SELECT * FROM info WHERE id=" . filter_input(INPUT_POST, 'info_a_modifier'));
                //debug("query");
                $infoSQL = new Info($sql, $info);
                debug("info");
                $form = $infoSQL->formModifier($sql, $GLOBALS['admin__infos'] . "?modifier=publie");
                debug("form");
                $pAdmin->ajouterContenu($form);

                // vidage de la mémoire POST
                mysqli_free_result($info);
        }

        /* affichage de la liste des infos avec des coches radio */
        $listeInfosModif = Info::GetListe($sql, "modifier");
        $pAdmin->ajouterContenu($listeInfosModif);
}

// liste des infos - supprimer
if (filter_input(INPUT_GET, 'supprimer')) {
        $post = filter_input_array(INPUT_POST);
        $key = "info_a_supprimer";
        if ($post && array_key_exists($key, $post)) {
                $query = postArrayVersQueryID($key, $post);
                $infos_a_supp = $sql->query("SELECT * FROM info WHERE id IN (" . $query . ")");
                for ($i = 0; $i < mysqli_num_rows($infos_a_supp); $i++) {
                        $infoSQL = new Info($sql, $infos_a_supp);
                        $infoSQL->supprimer($sql);
                }
                mysqli_free_result($infos_a_supp);
        }
        /* affichage de la liste des infos avec des coches checkbox */
        $listeInfosSupp = Info::GetListe($sql, "supprimer");
        $pAdmin->ajouterContenu($listeInfosSupp);
}

if (filter_input(INPUT_GET, 'afficher')) {
        $post = filter_input_array(INPUT_POST);
        $key = "info_a_afficher";
        if ($post && array_key_exists($key, $post)) {
                $query = postArrayVersQueryID($key, $post);
                $infos = $sql->query("SELECT * FROM info WHERE id IN (" . $query . ") ORDER BY date DESC");
                for ($i = 0; $i < mysqli_num_rows($infos); $i++) {
                        $info = new Info($sql, $infos);
                        $pAdmin->ajouterContenu("<br>" . $info->getTableauMultiLang($sql, "id: " . $info->getId() . "<br>"));
                }
                mysqli_free_result($infos);
        }
        $listeInfos = Info::GetListe($sql, "afficher", Info::GetGlobalLanguages());
        $pAdmin->ajouterContenu($listeInfos);
}
$sql->close();
$pAdmin->fin();
?>