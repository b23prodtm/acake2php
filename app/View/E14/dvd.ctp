<?php

if (stristr($p, ".php")) {
        include $GLOBALS["library"] . DS . $p;
} else if(isset($nom) && isset($base)){
// recuperer les infos du dvd
        $dvd = lireFichier($nom, $base);
        echo var_dump($dvd);
        $liste = get_dir_files($base);

        $tbl = new Tableau(3, 1);
        $tbl->setOptionsArray(array("HTML" => array("class" => "info")));
        $tbl->setContenu_cellule(0, 0, $dvd[0], array(array("class" => "info_titre")));
        $tbl->setContenu_cellule(1, 0, $dvd[1]);

        /* lier les entrees precedant et suivant le film courant */
        sort($liste);
        $courant = array_search($dvd[0] . ".dat", $liste);
        echo var_dump($liste);
        if ($courant < count($liste) - 1) {
                $suivant = $courant + 1;
        } else {
                $suivant = $courant;
        }
        if ($courant > 0) {
                $precedent = $courant - 1;
        } else {
                $precedent = $courant;
        }
        $tbl->setContenu_cellule(2, 0, HTML_lien($pageURL . "?nom=" . substr($liste[$precedent], 0, -4) . "&base=" . urlencode(filter_input(INPUT_GET, 'base')), "< enregistrement precedent") . " " . HTML_lien($pageURL . "?nom=" . substr($liste[$suivant], 0, -4) . "&base=" . urlencode(filter_input(INPUT_GET, 'base')), "enregistrement suivant >"));

        echo $tbl->fin();
} else {
        echo afficherListeDVD($post_base, $pageURL);
}
?>