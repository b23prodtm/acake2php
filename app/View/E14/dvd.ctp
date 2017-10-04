<?php

if($nom && $base && $pIndex){
        
        $r = new Index(null);
        require_once $GLOBALS["include__php_page.class.inc"];
        require_once $GLOBALS["include__php_module_html.inc"];
        require_once $GLOBALS["include__php_module_DVD.inc"];

        $clefPage = $pIndex;
        $pageUrl = $r->sitemap[$clefPage];
        // recuperer les infos du dvd
        $dvd = lireFichier($nom, $base);
        i_debug(var_dump($dvd));
        $liste = get_dir_files($base);

        $tbl = new Tableau(3, 1);
        $tbl->setOptionsArray(array("HTML" => array("class" => "info")));
        $tbl->setContenu_cellule(0, 0, $dvd[0], array(array("class" => "info_titre")));
        $tbl->setContenu_cellule(1, 0, $this->Markdown->transform($dvd[1]));

        /* lier les entrees precedant et suivant le film courant */
        sort($liste);
        $courant = array_search($dvd[0] . OBJET_ext, $liste);
        i_debug(var_dump($liste));
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
        $tbl->setContenu_cellule(2, 0, HTML_lien($pageUrl . "/" . substr($liste[$precedent], 0, -4), "< --") . " " . HTML_lien($pageUrl . "/" . substr($liste[$suivant], 0, -4), "-- >"));

        echo $tbl->fin();
} else if(isset($base)){
        echo afficherListeDVD($base, $pageURL);
} else {
        echo "NO BASE FOLDER";
}
        
?>