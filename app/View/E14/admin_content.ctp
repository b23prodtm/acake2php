<?php
if (!$i_sitemap) {
        require '../include/php_index.inc.php';
}
$r = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
require $GLOBALS['include__php_page.class.inc'];
require $GLOBALS['include__php_formulaire.class.inc'];
/* functions d'affichage  -----  privées */

function publierImages() {
        $i = 0; //compteur
        foreach ($_FILES as $image) {
                if (is_uploaded_file($image['tmp_name'])) {
                        $path = $GLOBALS['R_SITEDIR'] . "activites" . ++$i . ".jpg";
                        if (move_uploaded_file($image['tmp_name'], $path)) {
                                echo "<div class='console'><b>Image " . $image['name'] . " chargée avec succès!</b></div>";
                        } else {
                                echo "<div class='console'><b>Image " . $image['name'] . " pas chargée. ERREUR SURVENUE!</b></div>";
                        }
                }
        }
}

function formImages($url) {
        if (!filter_input(INPUT_POST, 'n_img')) {
                $n = 5;
        } else {
                $n = filter_input(INPUT_POST, 'n_img');
        }
        $f = new Formulaire("Ajouter les images", $url . "?images=publie");
        for ($i = 0; $i < $n; $i++) {
                $champ[] = new ChampFile("image_$i", "Image " . $i + 1, "format: image/jpeg. L'image prendra le nom de activites" . $i . ".jpg");
                $f->ajouterChamp($champ[$i]);
        }
        $valider = new ChampValider("Ajouter et publier");
        $f->ajouterChamp($valider);
        echo $f->fin();
}

function ftp() {
        $srv = SERVEUR_FTP;
        $usr = CLIENT_FTP;
        $psw = MDP_FTP;
        $ftpid = ftp_ssl_connect($srv);
        if (ftp_login($ftpid, $usr, $psw)) {
                echo "connecté au serveur ftp " . $srv . "\n";
        } else {
                trigger_error("impossible de se connecter au ftp :" . $srv, E_USER_ERROR);
        }

        return $ftpid;
}

/* END fonctions  -----  privées 



  $pAdmin = new ADMIN_Page($r, "admin__activites", session_id());
  /* ----- les différentes fonctionnalités ------ */
?><center><b><?php echo $r->lang('gestionapropos', 'admin'); ?></b></center>
<?php
$liste = HTML_listeDebut();
/* ---- (1) --- */
$liste .= HTML_listeElement(HTML_lien($this->request->here(false) . "?images=1", ">Insérer des images"));
/* ---- (2) --- */
$liste .= HTML_listeElement(HTML_lien($this->request->here(false) . "/edit?page=1", ">Changer la page Activités"));
$liste .= HTML_listeFin();
echo $liste;

/* ----- (1) ------- */
if (filter_input(INPUT_GET, 'images')) {
        /* ---- reception puis chargement des images ----- */
        if (filter_input(INPUT_GET, 'images') === 'publie') {
                publierImages();
                return;
        } else {
                formImages($this->request->base);
        }
}

/* ----- (2) ------- */
if ($pMethod == "edit") { // formulaire changer la page activites
        if (filter_input(INPUT_GET, "page")) { /* ------ script de reception de la page ----- */
                $path = $GLOBALS['FTPDOCS'] . "activites_inc";
                $ftpid = ftp();
                echo "<div class='console'>" . $r->lang('chargefichier', 'admin') . " $path :<br>";
                $ret = ftp_nb_put($ftpid, $path, $_FILES['page']['tmp_name'], FTP_ASCII);
                while ($ret == FTP_MOREDATA) {
                        echo ".";
                        $ret = ftp_nb_continue($ftpid);
                }
                if ($ret == FTP_FINISHED) {
                        echo "<br><b>" . $r->lang('actionsucces', 'admin') . "</b></div>";
                } else {
                        echo "<br><b>" . $r->lang('actionechec', 'admin') . "</b></div>";
                }

                /* ----- formulaire insertion d'images ----- */
                if (filter_input(INPUT_POST, 'n_img') > 0) {
                        formImages($this->request->base);
                }
        }
        /* ------ formulaire chargement nouvelle page ----- */
        // Pour la mise en place d'une nouvelle page Activités, un formulaire.
        $f = new Formulaire("Changer la page HTML", $this->request->base . "/edit/page=1&images=1");
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
        echo $f->fin();
}
?>