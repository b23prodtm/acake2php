<?php
if (!$i_sitemap) {
        require '../include/php_index.inc.php';
}
$r = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
require $GLOBALS['include__php_page.class.inc'];
require $GLOBALS['include__php_formulaire.class.inc'];
/* functions d'affichage  -----  privées */

function publierImages(Index $r) {
        $i = 0; //compteur
        foreach ($_FILES as $image) {
                if (is_uploaded_file($image['tmp_name'])) {
                        $path = $GLOBALS['R_SITEDIR'] . "activites" . ++$i . ".jpg";
                        if (move_uploaded_file($image['tmp_name'], $path)) {
                                echo "<div class='console'><b>Image " . $image['name'] . " " . $r->lang("actionsucces","admin") . "</b></div>";
                        } else {
                                echo "<div class='console'><b>Image " . $image['name'] . " " . $r->lang("actionechec","admin") . "</b></div>";
                        }
                }
        }
}

function formImages($url, Index $r) {
        if (!filter_input(INPUT_POST, 'n_img')) {
                $n = 5;
        } else {
                $n = filter_input(INPUT_POST, 'n_img');
        }
        $f = new Formulaire($r->lang("insertimages", "content"), $url . "/image?images=publie");
        for ($i = 0; $i < $n; $i++) {
                $champ[] = new ChampFile("image_$i", "Image " . $i + 1, "format : image/jpeg. > activites" . $i . ".jpg");
                $f->ajouterChamp($champ[$i]);
        }
        $valider = new ChampValider($r->lang("insertimages", "content"));
        $f->ajouterChamp($valider);
        echo $f->fin();
}

function ftp(Index $r) {
        $srv = SERVEUR_FTP;
        $usr = CLIENT_FTP;
        $psw = MDP_FTP;
        $ftpid = ftp_ssl_connect($srv);
        if (ftp_login($ftpid, $usr, $psw)) {
                echo $r->lang("actionechec","admin") . " " . $srv . "\n";
        } else {
                trigger_error($r->lang("actionsucces","admin") . " " . $srv, E_USER_ERROR);
        }

        return $ftpid;
}

/* END fonctions  -----  privées 
  /* ----- les différentes fonctionnalités ------ */
?><center><b><?php echo $r->lang('gestionapropos', 'admin'); ?></b></center>
<?php

/* ----- (1) ------- */
if ($pMethod === "image") {
        /* ---- reception puis chargement des images ----- */
        if (filter_input(INPUT_GET, 'images') === 'publie') {
                publierImages($r);
                return;
        } else {
                formImages($this->request->base, $r);
        }
} else {
  $liste = HTML_listeDebut();
  /* ---- (1) --- */
  $liste .= HTML_listeElement(HTML_lien($r->sitemap["admin__activites"]. "/image", $r->lang("insertimages","content")));
  /* ---- (2) --- */
  $liste .= HTML_listeElement(HTML_lien($r->sitemap["admin__activites"]. "/edit", $r->lang("changepage","content")));
  $liste .= HTML_listeFin();
  echo $liste;
}

/* ----- (2) ------- */
if ($pMethod === "edit") { // formulaire changer la page activites
        if (filter_input(INPUT_GET, "page")) { /* ------ script de reception de la page ----- */
                $path = $GLOBALS['FTPDOCS'] . "activites_inc";
                $ftpid = ftp($r);
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
                        formImages($this->request->base, $r);
                }
        }
        /* ------ formulaire chargement nouvelle page ----- */
        // Pour la mise en place d'une nouvelle page Activités, un formulaire.
        $f = new Formulaire($r->lang("changerpage","content"), $r->sitemap["admin__activites"]."/edit/?page=1");
        $pageHTML = new ChampFile("page", $r->lang("nouvellepage_lab","content"),$r->lang("nouvellepage_dsc","content"));
        // array incrementé ->10
        $n = 10;
        for ($i = 0; $i < $n + 1; $i++) {
                $choix["_" . $i . "_"] = $i;
        }
        $nbImages = new ChampSelect("n_img", $r->lang("nombreimages","content"), "", $choix, 3, 0);
        $valider = new ChampValider($r->lang("send","content"), "");
        $f->ajouterChamp($pageHTML);
        $f->ajouterChamp($nbImages);
        $f->ajouterChamp($valider);
        echo $f->fin();
}
?>