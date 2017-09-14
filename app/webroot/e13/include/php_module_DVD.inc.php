<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	!!!:brunorakotoarimanana:20041121
  @filename	php_module_DVD.inc.php
 */
global $Module_DVD;
// eviter la double inclusion
if (!isset($Module_DVD)) {
        $Module_DVD = 1;
        require $GLOBALS["include__php_module_html.inc"];
        require $GLOBALS["include__php_formulaire.class.inc"];

        /* !
          @function   lireFichier
          @abstract   lecture du fichier de données de l'OBJET, variable tableau retournée.
          @discussion (description)
          @param      $nom nom du OBJET, sans espace ni caractere special
          $dossier nom du dossier de stockage (terminer par un slash "/")
          @result     array("nom","contenu","nom-du-fichier")
         */

        function lireFichier($nom, $dossier) {
                $nom_du_fichier = $dossier . $nom . ".inc";
                if ($handle = fopen($nom_du_fichier, 'r')) {
                        $contenu = fread($handle, filesize($nom_du_fichier));
                        return array($nom, $contenu, $nom_du_fichier);
                } else
                        die($nom . " n'existe pas dans la base de données.");
        }

        /* !
          @function   ecrireFichier
          @abstract   ecriture du fichier de données du OBJET. Le dossier data doit etre accessible en ecriture.
          @discussion (description)
          @param      $dossier dossier de stockage (terminer par un slash "/")
          $nom nom de OBJET
          $contenu contenu a stocker, e.g. la description du film
          @result     true|false en cas de succes|echec ("log" sur la sortie stdout)
         */

        function ecrireFichier($dossier, $nom, $contenu) {
                $nom_du_fichier = $dossier . $nom . ".inc";
                if ($handle = fopen($nom_du_fichier, 'w')) {
                        if (fwrite($handle, $contenu)) {
                                echo "<br><b>fichier ecrit avec succes. $nom_du_fichier</b><br>\n";
                                return true;
                        } else {
                                echo "<br><b>erreur d'écriture. $nom_du_fichier</b><br>\n";
                                return false;
                        }
                } else {
                        echo "<br><b>le fichier n'a pu etre ouvert ou créé. $nom_du_fichier</b><br>\n";
                        return false;
                }
        }

        function get_dir_files($dir = ".") {
                $handle = opendir($dir);
                global $file_list;
                $i = 0;
                while (false !== ($file = readdir($handle))) {
                        if (($file != ".") && ($file != "..")) {
                                $file_list[$i] = $file;
                                $i++;
                        }
                }
                closedir($handle);
                return $file_list;
        }

        /* !
          @function   afficherListeDVD
          @abstract   affichage de la liste complète du stock dans la base de donnees
          @discussion (description)
          @param      $dossier dossier de stockage (terminer par un slash "/")
          $pageAfficher url de la page d'affichage du contenu de OBJET
          @result     (description)
         */

        function afficherListeDVD($dossier, $pageAfficherDVD) {
                $files = array();
                $files = get_dir_files($dossier);
                //sort($files);
                $html = "<h2>Fichiers dans la base de données '$dossier'</h2>";
                if (count($files) > 0) {
                        foreach ($files as $thefile) {
                                $nom = substr($thefile, 0, -4);
                                $html .= HTML_lien($pageAfficherDVD . "?nom=$nom&base=$dossier", $nom) . "<br>\n";
                        }
                }
                return $html;
        }

        /* !
          @function   formAjouterDVD
          @abstract   formulaire pour ajouter un DVD à la base de données
          @discussion (description)
          @param      $script url vers le script qui ajoute un dvd
          @result     (description)
         */

        function formAjouterDVD($script, $dossier) {
                $form = new Formulaire("Ajouter un " . OBJET . " à la base", $script);
                $nom = new ChampTexte("nom", "Nom du " . OBJET . " :", "8 carac. max. (compatibilité pour le nom du fichier), sans espace ni caractère spécial, e.g. (é,à,',ç,..)", 8, 8);
                $contenu = new ChampAireTexte("contenu", "Description de " . OBJET . " :", "Votre texte de description.", 20, 10);
                $base = new ChampCache("dossier", $dossier);
                $ok = new ChampValider("Ajouter >");
                $reset = new ChampEffacer("Vider les champs ^");
                $form->ajouterChamp($nom);
                $form->ajouterChamp($contenu);
                $form->ajouterChamp($base);
                $form->ajouterChamp($ok);
                $form->ajouterChamp($reset);
                return $form->fin();
        }

        /* !
          @function   ajouterDVD
          @abstract   ajouter un dvd a la base $dossier.
          @discussion (description)
          @param      $dossier dossier de stockage (terminer avec un "/")
          $nom nom du dvd
          $contenu contenu du dvd
          @result     true|false
         */

        function ajouterDVD($dossier, $nom, $contenu) {
                return ecrireFichier($dossier, $nom, $contenu);
        }

        /* !
          @function   formModiSuppDVD
          @abstract   modifier|supprimer un DVD de la base de donnee
          @discussion (description)
          @param      $dossier dossier de stockage (terminer avec un "/")
          $script url vers le script qui modifie|supprime un DVD. la variable
          @result     (description)
         */

        function formModiSuppDVD($script, $dossier) {
                $form = new Formulaire("Modifier|Supprimer un(e) " . OBJET, $script);
                $listeFichiers = get_dir_files($dossier);
                $listeDVD = array();
                foreach ($listeFichiers as $thefile) {
                        $listeDVD[substr($thefile, 0, -4)] = substr($thefile, 0, -4);
                }
                $liste = new ChampSelect("nom", "Selectionner un(e) " . OBJET . ":", "", $listeDVD, 0);
                $dossier_n = new ChampCache("base", $dossier);
                $action[] = new ChampCoche("action", "modifier", "modifier", "", TRUE, "RADIO");
                $action[] = new ChampCoche("action", "supprimer", "supprimer", "", FALSE, "RADIO");
                $validation = new ChampGroupe("Action", "Choisissez l'action désirée.", "action", $action);
                $valider = new ChampValider("valider >");
                $form->ajouterChamp($liste);
                $form->ajouterChamp($dossier_n);
                $form->ajouterChamp($validation);
                $form->ajouterChamp($valider);
                return $form->fin();
        }

        /* !
          @function   formModifierDVD
          @abstract   modifier les informations d'un DVD
          @discussion (description)
          @param		$script url vers le script qui modifie|supprime un DVD. la variable
          $nom nom du contenant les donnees
          $dossier dossier stockage
          @result     (description)
         */

        function formModifierDVD($script, $nom, $dossier) {
                $dvd = lireFichier($nom, $dossier);
                $nom_du_fichier = $dossier . $nom . ".inc";
                $form = new Formulaire("Modification des données de " . OBJET . " ($nom_du_fichier)", $script);
                $nom = new ChampTexte("nom", "Nom de " . OBJET . " :", "8 carac. max. (compatibilité pour le nom de fichier), sans espace ni caractère spécial, e.g. (é,à,',ç,..)", 8, 8, $dvd[0]);
                $contenu = new ChampAireTexte("contenu", "Contenu de " . OBJET, "Votre texte de description.", 20, 10, $dvd[1]);
                $ok = new ChampValider("modifier >");
                $fichier_original = new ChampCache("fichier_original", $nom_du_fichier);
                $dossier_n = new ChampCache("base", $dossier);
                $action = new ChampCache("action", "modifier");
                $reset = new ChampEffacer("Vider les champs ^");
                $form->ajouterChamp($nom);
                $form->ajouterChamp($contenu);
                $form->ajouterChamp($fichier_original);
                $form->ajouterChamp($dossier_n);
                $form->ajouterChamp($action);
                $form->ajouterChamp($ok);
                $form->ajouterChamp($reset);
                return $form->fin();
        }

        /* !
          @function   modifierDVD
          @abstract   modifie le DVD: effacement du fichier origine si le nom a changé et ecriture du nouveau fichier
          @discussion (description)
          @param     	$dossier dossier de stockage (terminer par un slash "/")
          $nom nom du DVD
          $contenu contenu du film
          @result     true|false
         */

        function modifierDVD($dossier, $nom, $contenu, $fichier_original) {
                if ($dossier . $nom . ".inc" !== $fichier_original)
                        unlink($fichier_original);
                return ecrireFichier($dossier, $nom, $contenu);
        }

        /* !
          @function   supprimerDVD
          @abstract   suppression d'un DVD de la base de données. nom du fichier complet requis.
          @discussion (description)
          @param      $nom_du_fichier nom du fichier avec le dossier de stockage. e.g. data/fiche.inc
          @result     (description)
         */

        function supprimerDVD($nom_du_fichier) {
                unlink($nom_du_fichier);
        }

}
