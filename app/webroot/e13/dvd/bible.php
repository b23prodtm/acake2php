<?php

// stylesheet  precision losss //ob_start();
if (!$i_sitemap) { require '../include/php_index.inc.php'; }
//Gestion de la chaine GET pour l'URI
$get = "";
$sep = "";
foreach ($_GET as $key => $val) {
    $get .= $sep . $key . "=" . $val;
    $sep = "&";
}
$r = new Index(filter_input(INPUT_SERVER,'PHP_SELF') . "?" . $get);
require $GLOBALS["include__php_page.class.inc"];
require $GLOBALS["include__php_module_html.inc"];
require $GLOBALS["include__php_module_DVD.inc"];

$clefPage = "library__index";
foreach ($GLOBALS["library"] as $p => $url) {
    if (stristr(filter_input(INPUT_SERVER,'PHP_SELF') . "?" . $get, $url)) {
        $clefPage = $p;
    }
}
$base = "data/";
if (!filter_input(INPUT_POST,'base'))
    $_POST['base'] = $base;
/* mode Afficher */
if (filter_input(INPUT_GET,'nom') && filter_input(INPUT_GET,'base')) {
    $page = new Page($r, $clefPage, false);
    // recuperer les infos du dvd
    $dvd = lireFichier(filter_input(INPUT_GET,'nom'), filter_input(INPUT_GET,'base'));
    echo var_dump($dvd);
    $liste = get_dir_files(filter_input(INPUT_GET,'base'));

    $tbl = new Tableau(3, 1);
    $tbl->setOptionsArray(array("HTML" => array("class" => "info")));
    $tbl->setContenu_cellule(0, 0, $dvd[0], array("HTML" => array("ALIGN" => "RIGHT", "class" => "info_titre")
            )
    );
    $tbl->setContenu_cellule(1, 0, $dvd[1]);

    /* lier les entrees precedant et suivant le film courant */
    sort($liste);
    $courant = array_search($dvd[0] . ".dat", $liste);
    echo var_dump($liste);
    if ($courant < count($liste) - 1)
        $suivant = $courant + 1;
    else
        $suivant = $courant;
    if ($courant > 0)
        $precedent = $courant - 1;
    else
        $precedent = $courant;
    $tbl->setContenu_cellule(2, 0, HTML_lien($pageURL . "?nom=" . substr($liste[$precedent], 0, -4) . "&base=" . urlencode(filter_input(INPUT_GET,'base')), "< enregistrement precedent") . " " . HTML_lien($pageURL . "?nom=" . substr($liste[$suivant], 0, -4) . "&base=" . urlencode(filter_input(INPUT_GET,'base')), "enregistrement suivant >"), array("HTML" => array("ALIGN" => "CENTER")));

    $page->ajouterContenu($tbl->fin());
} elseif (filter_input(INPUT_GET,'mode') === "admin") {
    $page = new ADMIN_Page($r, $clefPage, session_id());
    if (ADMIN_Page::sessionAdminValide()) {
        $ajouter = $pageURL . "?mode=admin&ajouter";
        $modifier = $pageURL . "?mode=admin&modifier";
        if (filter_input(INPUT_GET,'ajouter')) {
            if (filter_input(INPUT_GET,'do'))
                ajouterDVD(filter_input(INPUT_POST,'dossier'), filter_input(INPUT_POST,'nom'), filter_input(INPUT_POST,'contenu'));
            $page->ajouterContenu(formAjouterDVD($ajouter . "&do", filter_input(INPUT_POST,'base')));
        }
        if (filter_input(INPUT_GET,'modifier')) {
            if (filter_input(INPUT_GET,'do')) {
                if (filter_input(INPUT_POST,'action') === "modifier") {
                    if (filter_input(INPUT_GET,'exec'))
                        modifierDVD(filter_input(INPUT_POST,'base'), filter_input(INPUT_POST,'nom'), filter_input(INPUT_POST,'contenu'), filter_input(INPUT_POST,'fichier_original'));
                    $page->ajouterContenu(formModifierDVD($modifier . "&do&exec", filter_input(INPUT_POST,'nom'), filter_input(INPUT_POST,'base')));
                }
                if (filter_input(INPUT_POST,'action') === "supprimer")
                    supprimerDVD(filter_input(INPUT_POST,'base') . filter_input(INPUT_POST,'nom') . ".inc");
            }
            else
                $page->ajouterContenu(formModiSuppDVD($modifier . "&do", filter_input(INPUT_POST,'base')));
        }
    }
} else {
    $page = new Page($r, $clefPage, false);
    $page->ajouterContenu(afficherListeDVD($base, $pageURL));
}
//$page->ajouterMessage(ob_get_contents());
// ob_end_clean();
$page->fin();
?>