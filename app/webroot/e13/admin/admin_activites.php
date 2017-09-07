<?php
require("../include/php_index.inc.php");
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF'));
require($GLOBALS['include__php_page.class.inc']);
require($GLOBALS['include__php_formulaire.class.inc']);

/* functions d'affichage  -----  privées */

function publierImages(&$pAdmin) {
    $i = 0; //compteur
    foreach ($_FILES as $image) {
        if (is_uploaded_file($image['tmp_name'])) {
            $path = $GLOBALS['R_SITEDIR'] . "activites" . ++$i . ".jpg";
            if (move_uploaded_file($image['tmp_name'], $path)) {
                                $pAdmin->ajouterContenu("<div class='console'><b>Image " . $image['name'] . " chargée avec succès!</b></div>");
                        } else {
                                $pAdmin->ajouterContenu("<div class='console'><b>Image " . $image['name'] . " pas chargée. ERREUR SURVENUE!</b></div>");
                        }
                }
    }
    $pAdmin->fin();
}

function formImages(&$pAdmin) {
    if (!filter_input(INPUT_POST,'n_img'))
        $n = 5;
    else
        $n = filter_input(INPUT_POST,'n_img');
    $f = new Formulaire("Ajouter les images", $pAdmin->getURL() . "?images=publie");
    for ($i = 0; $i < $n; $i++) {
        $champ[] = new ChampFile("image_$i", "Image " . $i + 1, "format: image/jpeg. L'image prendra le nom de activites" . $i . ".jpg");
        $f->ajouterChamp($champ[$i]);
    }
    $valider = new ChampValider("Ajouter et publier");
    $f->ajouterChamp($valider);
    $pAdmin->ajouterContenu($f->fin());
}

function ftp() {
    $srv = SERVEUR_FTP;
    $usr = CLIENT_FTP;
    $psw = MDP_FTP;
    $ftpid = @ftp_ssl_connect($srv);
    if (@ftp_login($ftpid, $usr, $psw)) {
                echo "connecté au serveur ftp " . $srv . "\n";
        } else {
                die("impossible de se connecter au ftp :" . $srv);
        }

        return $ftpid;
}

/* END fonctions  -----  privées */



$pAdmin = new ADMIN_Page($r, "admin__activites", session_id());
/* ----- les différentes fonctionnalités ------ */
$pAdmin->ajouterContenu("<center><b>Gestion page Activités</b></center>");

$liste = HTML_listeDebut();
/* ---- (1) --- */
$liste .= HTML_listeElement(HTML_lien($pAdmin->getURL() . "?images=1", ">Insérer des images"));
/* ---- (2) --- */
$liste .= HTML_listeElement(HTML_lien($pAdmin->getURL() . "?page=1", ">Changer la page Activités"));
$liste .= HTML_listeFin();
$pAdmin->ajouterContenu($liste);

/* ----- (1) ------- */
if (filter_input(INPUT_GET,'images')) {
    /* ---- reception puis chargement des images ----- */
    if (filter_input(INPUT_GET, 'images') === 'publie') {
                publierImages($pAdmin);
        } else {
                formImages($pAdmin);
        }
}

/* ----- (2) ------- */
if (filter_input(INPUT_GET,'page')) { // formulaire changer la page activites
        /* ------ script de reception de la page ----- */
        $path = $GLOBALS['FTPDOCS'] . "activites.html";
        $ftpid = ftp();
        $pAdmin->ajouterContenu("<div class='console'>Chargement du fichier $path:<br>");
        $ret = ftp_nb_put($ftpid, $path, $_FILES['page']['tmp_name'], FTP_ASCII);
        while ($ret == FTP_MOREDATA) {
            echo ".";
            $ret = ftp_nb_continue($ftpid);
        }
        if ($ret == FTP_FINISHED) {
                $pAdmin->ajouterContenu("<br><b>Fichier $path transmis avec succes!</b></div>");
        } else {
                $pAdmin->ajouterContenu("<br><b>Erreur de transfert de fichier!</b></div>");
        }
   
    /* ----- formulaire insertion d'images ----- */
    if (filter_input(INPUT_POST,'n_img') > 0) {
        formImages($pAdmin);
    }
    /* ------ formulaire chargement nouvelle page ----- */
    // Pour la mise en place d'une nouvelle page Activités, un formulaire.
    $f = new Formulaire("Changer la page HTML", $pAdmin->getURL() . "?page=1&images=1");
    $pageHTML = new ChampFile("page", "Nouvelle page Activités : ", "Choisir le fichier text/html. Indiquer le nombre d'images incluses dans la page. Dans le   prochain champ.");
    // array incrementé ->10
    $n = 10;
    for ($i = 0; $i < $n + 1; $i++) {
                $choix["_" . $i . "_"] = $i;
        }
        $nbImages = new ChampSelect("n_img", "Nombre d'images incluses dans la page : ", "Sélectionner le nombre. Maximum 10.", $choix, 3, 0);
    $valider = new ChampValider("Transmettre la page", "Si des images sont incluses, alors il vous sera demandé apres ça de les charger sur le serveur.");
    $f->ajouterChamp($pageHTML);
    $f->ajouterChamp($nbImages);
    $f->ajouterChamp($valider);
    $pAdmin->ajouterContenu($f->fin());
    $pAdmin->fin();
}

$pAdmin->fin();
?>