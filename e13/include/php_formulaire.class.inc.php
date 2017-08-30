<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to b23|prod)
  @author	www.b23prodtm.info
  @date	Sat Sep 18 15:37:02 CEST 2004 @609 /Internet Time/
  @filename	php_formulaire.class.inc
 */

/* !
  @header php_formulaire.class.inc.php
  @abstract   Contient les classes Formulaire et Champ (plus bas)
  @discussion (description)
 */
/* !
  @class		Formulaire
  @abstract   Cette classe gère les formulaires HTML dans les pages WEB. Elle nécessite la classe Tableau. Le mode d'envoi est toujours ENCTYPE=multipart/form-data pour un maximum de flexibilité.
  @discussion <br>TODO une intégration des scripts *javascript* pour effectuer les liens enfants-parents entre des champs: un champ dont la valeur est modifiee pourrait afficher un autre pour le compléter (méthode addEnfant()).
 */

global $ClasseFormulaire;

if (!isset($ClasseFormulaire)) {
        require ($GLOBALS['include__php_tbl.class.inc']);
        require ($GLOBALS['include__php_captcha.class.inc']);

        $ClasseFormulaire = 1;

        // mode affichage du formulaire
        define("HORIZONTAL", 0);
        define("VERTICAL", 1);
        define("LIBRE", 2);

        // Classe gérant les formulaires

        class Formulaire {

                var $nom;
                var $modeTbl, $tbl; // $this->tbl utilisé dans la fonction fin() de la classe
                var $nbChamps, $champs;
                var $classe; // classe de style css
                var $HTML;

                /* !
                  @method     Formulaire
                  @abstract   Constructeur de la classe.
                  #nom: le nom du formulaire, tel qu'il apparaît dans la page HTML affichée
                  #action: la page script de réception.
                  #modeTbl: VERTICAL|HORIZONTAL|LIBRE les modes VERTICAL et HORIZONTAL affiche le formulaire et les champs dans un tableau HTML à l'appel de la méthode fin(). | LIBRE | Le mode LIBRE permet d'afficher librement les champs à l'appel de leurs méthodes getHTML ou writeHTML (voir classe Champ). Remarque: Il faut appeler la méthode writeHTML de la classe Formulaire AVANT de pouvoir afficher les champs, la balise HTML <FORM..> sera ainsi placée avant les balises <INPUT>.

                  @discussion (description)
                 */

                public function __construct($nom, $action, $modeTbl = VERTICAL, $classe = "formulaire", $methode = "POST") {
                        $this->nom = $nom;

                        $this->classe = $classe;
                        $this->modeTbl = $modeTbl;

                        $this->nbChamps = 0;
                        $this->champs = array();
                        $this->HTML = "<FORM ACTION=\"" . $action . "\" METHOD='" . $methode . "' ENCTYPE=\"multipart/form-data\">";
                        $this->HTML .= "<INPUT TYPE='HIDDEN' NAME='_form' VALUE=\"" . $nom . "\">";
                }

                /* ----- partie privée ----- */

                // utilisee dans la fonction publique fin() pour inserer les champs dans le tableau HTML du formulaire. Sauf en modeTbl libre ou les champs sont directement ecrits dans le script appelant le formulaire.
                private function fin_champs($mode) { // $mode retourner (0) ou ecrire (1)
                        //initialisation affichage dans un tableau si nécessaire
                        switch ($this->modeTbl) {
                                case VERTICAL:
                                        //debug("form.class:ligne46: nbChamps = ".$this->nbChamps);
                                        $this->tbl = new Tableau($this->nbChamps + 1, 1, "formulaire_vertical"); // dans ce tableau, il y a {nbChamps} + 1 lignes : (le titre (ligne 0);les champs input (ligne 1 à ligne nbChamps-(champsValetEff=1|2)); les champs Valider et Effacer se trouvent à la derniere ligne.
                                        $this->tbl->setOptionsArray(array("HTML" => array("WIDTH" => "90%",
                                                "ALIGN" => "CENTER")));
                                        break;
                                case HORIZONTAL:
                                        $this->tbl = new Tableau(3, $this->nbChamps, "formulaire_horizontal"); // 3 lignes: titre, champs, champs Valider et Effacer
                                        $this->tbl->setOptionsArray(array("HTML" => array("WIDTH" => "10%",
                                                "ALIGN" => "CENTER")));
                                        break;
                                case LIBRE:
                                        // pas de tableau a initialiser
                                        $this->tbl = NULL;
                                        break;
                                default:
                                        break;
                        }


                        /* MODE TABLEAU  HORIZONTAL ET VERTICAL */
                        if (($this->modeTbl == HORIZONTAL) || ($this->modeTbl == VERTICAL)) {
                                // placer les champs dans le tableau si mode HORIZONTAL ou VERTICAL; ommettre les champs Valider et effacer qui se placent a la derniere ligne du tableau
                                // debug("ligne 69: formatage des champs formulaire");
                                $this->tbl->options["class"] = $this->classe;
                                $this->tbl->setContenu_Cellule(0, 0, "<b>" . $this->nom . "</b>", array("HTML" => array("WIDTH" => "100%",
                                        "COLSPAN" => $this->tbl->nbColonnes,
                                        "ALIGN" => "RIGHT"),
                                    "class" => "formulaire_titre"));
                                // index dans le tableau HTML pour inserer les champs input, on ne commence pas à 0 pour le mode VERTICAL(=ligne de titre)
                                for ($i = 0, $n = 0; $i < $this->nbChamps; $i++) {
                                        if (($this->champs[$i]->type != "SUBMIT") && ($this->champs[$i]->type != "RESET")) {
                                                /* HORIZONTAL */
                                                // debug("ligne 75: champ $i.n=".$n);
                                                if ($this->modeTbl == HORIZONTAL) { // ici, commencer à la colonne 0!
                                                        $this->tbl->setContenu_Cellule(1, $n, $this->champs[$i]->getHTML($this->modeTbl));
                                                } else { // VERTICAL ici, commencer à la ligne 1 (2e ligne)
                                                        $this->tbl->setContenu_Cellule($n + 1, 0, $this->champs[$i]->getHTML($this->modeTbl));
                                                }
//						debug("tbl.class81: cellule ".$n+1.";1".$this->tbl->getContenu_Cellule($n+1,0);
                                                $n++;
                                        } else {
                                                /* champs valider et effacer a placer, enregistrement de l'index dans le tableau de champs */
                                                if ($this->champs[$i]->type == "SUBMIT") {
                                                        //debug("index_v ok = $i"); 
                                                        $index_v = $i;
                                                } else if ($this->champs[$i]->type == "RESET") {
                                                        //debug("index_e ok = $i"); 
                                                        $index_e = $i;
                                                }
                                        }
                                }
                        }
                        // champs valider et effacer
                        if (!isset($index_e)) {
                                $index_e = NULL;
                        }
                        if (!isset($index_v)) {
                                $index_v = NULL;
                        }
                        $this->fin_champsValEff($mode, $index_e, $index_v);
                }

                // utilisee dans la fonction publique fin() pour inserer les champs valider et effacer dans le tableau HTML du formulaire. en modeTbl libre, on insere les champs oubliés dans le script appelant le formulaire.
                // $mode: retourner (0) ou ecrire (1)
                function fin_champsValEff($mode, $index_e = NULL, $index_v = NULL) {
                        /* CHAMPS VALIDER ET EFFACER */
                        switch ($this->modeTbl) {

                                case HORIZONTAL:
                                        $tbl_valider = new Tableau(1, 2, "formulaire_horizontal_chValider");
                                        $tbl_valider->setOptionsArray(array("HTML" => array("WIDTH" => "100%",
                                                "ALIGN" => "RIGHT"),
                                            "class" => $this->classe));
                                        if (isset($index_v)) {
                                                $valider = $this->champs[$index_v];
                                                $tbl_valider->setContenu_Cellule(0, 0, $valider->getHTML($this->modeTbl));
                                                if (isset($index_e)) {
                                                        $effacer = $this->champs[$index_e];
                                                        $tbl_valider->setContenu_Cellule(0, 1, $valider->getHTML($this->modeTbl) . $effacer->getHTML($this->modeTbl));
                                                }
                                                $this->tbl->setContenu_Cellule(2, 0, $tbl_valider->fin());
                                                break;
                                        }
                                        //debug("form.class: 95: modeHORIZONTAL:champ valider inexistant!!");
                                        break;
                                case VERTICAL:
                                        // debug("champs valider et effacer...");
                                        if (isset($index_v)) {
                                                //debug("103:modeVERTICAL:champs valider et effacer...");
                                                $valider = $this->champs[$index_v];
                                                $v_str = $valider->getHTML($this->modeTbl);
                                                $this->tbl->setContenu_Cellule($this->nbChamps, 0, $v_str);
                                                if (isset($index_e)) {
                                                        $effacer = $this->champs[$index_e];
                                                        $this->tbl->setContenu_Cellule($this->nbChamps, 0, $effacer->getHTML($this->modeTbl) . $v_str);
                                                }
                                                break;
                                        }
                                        //debug("form.class: 109: modeVERTICAL:champ valider inexistant!!");
                                        break;
                                case LIBRE:
                                        /* LES CHAMPS EN MODE LIBRE SONT DIRECTEMENT ECRITS A L'APPEL DE LEUR METHODE WRITEHTML() (writeHTML efface la var d'instance HTML de class Champ); contrôle des eventuels "oublis" dans le script avant de fermer le formulaire avec </form> */
                                        for ($i = 0; $i < $this->nbChamps; $i++) {
                                                if ($mode == 1) { // retourner (0) ou ecrire (1)
                                                        $this->champs[$i]->writeHTML($this->modeTbl);
                                                } else {
                                                        $this->HTML .= "<br>" . $this->champs[$i]->getHTML($this->modeTbl);
                                                }
                                        }
                                        break;
                                default:
                                        break;
                        }
                }

                /* ----- partie publique ----- */

                /* !
                  @method		getHTML
                  @abstract   mode LIBRE. pour obtenir le code HTML du formulaire. utilisée en mode LIBRE.
                  @discussion (description)
                 */

                function getHTML() {
                        $html = $this->HTML;
                        $this->HTML = "";
                        return $html;
                }

                function writeHTML(){
                        echo stripslashes($this->HTML);
                        $this->HTML = NULL;
                }
                /* !
                  @method		ajouterChamp
                  @abstract   Pour ajouter un champ au formulaire. inutile en mode LIBRE.
                  #champ: variable de type Object.Champ. Passage par référence, si l'objet Champ est modifier par une de ses méthodes apres l'ajout, la modification est reportee dans l'objet Formulaire conteneur.
                  @discussion TODO ajout multiple.
                 */

                function ajouterChamp(&$champ) {
                        $this->champs[] = & $champ;
                        $this->nbChamps = count($this->champs);
                }


                /* !
                  @method     fin
                  @abstract   Afficher le formulaire. nécessaire pour obtenir le code complet du formulaire HTML.
                  #mode: 0|1 mode de retour du code HTML | 0 : retourner sur une variable sous la forme d'une chaine de caracteres | 1 : ecrire directement sur l'output (echo PHP)
                  @discussion (description)
                 */

                function fin($mode = 0) { // $mode pour retourner le code HTML (0) ou ecrire directement sur l'output (1)
                        // affichage des champs INPUT du formulaire
                        $this->fin_champs($mode);
                        // fermeture du tableau et affichage reel du formulaire en HTML.
                        if ($this->modeTbl != LIBRE) {
                                $this->HTML .= $this->tbl->fin();
                        }
                        $this->HTML .= "</FORM>";
                        switch ($mode) {
                                case 0:
                                        return $this->HTML;
                                case 1:
                                        $this->writeHTML();
                                        break;
                                default:
                                        break;
                        }
                }

        }

        class Champ {

                var $r;
                var $libelle, $desc;
                var $actif; // pour la visibilité du champ
                var $chParents, $chEnfants;
                var $type, $nom, $valeur;
                var $js;
                var $HTML;

                function __construct($libelle, $type, $nom, $valeur, $desc, $actif = TRUE) {
                        $this->r = new Registre(NULL);
                        $this->libelle = $libelle;
                        $this->type = $type;
                        $this->nom = $nom;
                        $this->valeur = $valeur;
                        $this->desc = $desc;
                        $this->actif = $actif;
                }

                /* ---- partie Privée ---- */

                // ajoute un champ dont dependra le champ courant
                function addParent(&$champ) {
                        $this->chParents[] = $champ;

                        // si un champ parent est actif, activer le champ courant, sinon le desactiver
                        for ($i = 0; $i < count($this->chParents); $i++) {
                                if ($this->chParents[$i]->actif) {
                                        $this->activer();
                                        return;
                                }
                        }
                        $this->desactiver();
                }

                // setInput redefinie dans les sous-classes spécialisées
                function setInput() {
                        return "";
                }

                function setHTML($modeTbl, $classeForm = "") {

                        /* ACTIF/INACTIF */
                        if ($this->actif) {
                                $input = $this->setInput();
                        } else {
                                $input = "<I>- champ indisponible -</I>";
                        }
                        // debug($input);
                        $tbl = 1;
                        switch ($modeTbl) {
                                case VERTICAL:
                                        $tbl = new Tableau(1, 2, "champ_input_vertical_$this->type");
                                        $tbl->setOptionsArray(array("class" => $classeForm,
                                            "HTML" => array("WIDTH" => "100%")));
                                        $tblLibelle = new Tableau(2, 1, "champ_input_vertical_" . $this->type . "_libelle");
                                        $tblLibelle->setOptionsArray(array("HTML" => array("BORDER" => 0)));
                                        $tblLibelle->setContenu_Cellule(0, 0, $this->libelle, array("class" => "formulaire")); //libelle
                                        $tblLibelle->setContenu_Cellule(1, 0, $this->desc, array("class" => "formulaire")); //description du champ
                                        $tbl->setContenu_Cellule(0, 0, $tblLibelle->fin(), array("HTML" => array("WIDTH" => "50%")));
                                        $tbl->setContenu_Cellule(0, 1, $input);
                                        break;
                                case HORIZONTAL:
                                        $tbl = new Tableau(3, 1, "champ_input_horizontal_$this->type");
                                        $tbl->setOptionsArray(array("class" => $classeForm,
                                            "HTML" => array("WIDTH" => "100%")));
                                        $tbl->setContenu_Cellule(0, 0, $this->libelle, array("class" => "formulaire"));
                                        $tbl->setContenu_Cellule(1, 0, $this->desc, array("class" => "formulaire"));
                                        $tbl->setContenu_Cellule(2, 0, $input);
                                        break;
                                case LIBRE:
                                        $tbl = new Tableau(1, 2, "champ_input_libre_$this->type");
                                        $tbl->setOptionsArray(array("class" => $classeForm));
                                        $tbl->setContenu_Cellule(0, 0, $this->libelle, array("class" => "formulaire"));
                                        $tbl->setContenu_Cellule(0, 1, $input);
                                        break;
                                default:
                                        break;
                        }
                        // debug("formulaire 222: tableau ::: ".$tbl->fin(1));
                        $this->HTML = $tbl->fin();
                }

                /* ---- partie publique ---- */

                // ajoute un champ qui dependra du champ courant
                function addEnfant(&$champ) {
                        $this->chEnfants[] = $champ;

                        for ($i = 0; $i < count($this->chEnfants); $i++) {
                                $this->chEnfants[$i]->addParent($this);
                        }
                        if ($this->actif) {
                                $this->activer();
                        } // pour activer les champs dépendant du champ courant
                }

                // note: variable $this->js a exploiter dans la fonction setInput() pour ajouter un script javascr. au champ input
                function setJS($js = "") {
                        $this->js = $js;
                }

                function activer() {
                        $this->actif = TRUE;
                        // par la notion enfants, il faut activer tous les champs qui dépendent du champ courant
                        for ($i = 0; $i < count($this->chEnfants); $i++) {
                                $this->chEnfants[$i]->activer();
                        }
                }

                function desactiver() {
                        $this->actif = FALSE;
                        for ($i = 0; $i < count($this->chEnfants); $i++) {
                                $this->chEnfants[$i]->desactiver();
                        }
                }

                function writeHTML($modeTbl, $classeForm = NULL) {
                        $this->setHTML($modeTbl, $classeForm);                        
                        echo stripslashes($this->HTML);
                        $this->HTML = NULL;
                }

                function getHTML($modeTbl, $classeForm = NULL) {
                        // debug("getHTML: ".$this->type);
                        $this->setHTML($modeTbl, $classeForm);
                        return $this->HTML;
                }

        }

        class ChampTexte extends Champ {

                var $taille;
                var $tailleMax;

                function __construct($nom, $libelle, $desc, $taille, $tailleMax = NULL, $valeurParDefaut = "", $actif = TRUE) {
                        parent::__construct($libelle, "TEXT", $nom, $valeurParDefaut, $desc, $actif);
                        $this->taille = $taille;
                        $this->tailleMax = $tailleMax;
                }

                /* privé */

                function setInput() {

                        if (!isset($this->tailleMax)) {
                                $tMax = "";
                        } else {
                                $tMax = " MAXLENGTH='" . $this->tailleMax . "'";
                        }

                        $input = "<INPUT TYPE='" . $this->type . "' NAME='" . $this->nom . "' SIZE='" . $this->taille . "' VALUE=\"" . $this->valeur . "\"" . $tMax . ">";

                        return $input;
                }

        }

        class ChampPassword extends ChampTexte {

                function __construct($nom, $libelle, $desc, $taille, $tailleMax = NULL, $actif = TRUE) {
                        parent::__construct($nom, $libelle, $desc, $taille, $tailleMax, "", $actif);
                        $this->type = "PASSWORD";
                }

        }

        class ChampPasswordAndConfirm extends ChampPassword {

                public function __construct($nom, $libelle, $desc, $taille, $tailleMax = NULL, $actif = TRUE) {
                        parent::__construct($nom, $libelle, $desc, $taille, $tailleMax, $actif);
                }

                function setInput() {
                        $input = parent::setInput();
                        $input .= "<BR><I>" . $this->r->lang("confirmermdp", "form") . "</I><BR>";
                        $this->nom .= "_confirm";
                        $input .= parent::setInput();

                        return $input;
                }

        }

        class ChampCache extends Champ {

                function __construct($nom, $valeur, $actif = TRUE) {
                        parent::__construct("", "HIDDEN", $nom, $valeur, "", $actif);
                }

                function setInput() {
                        return "<INPUT NAME = '" . $this->nom . "' TYPE='" . $this->type . "' VALUE=\"" . $this->valeur . "\">";
                }

        }

        class ChampValider extends Champ {

                var $img;

                function __construct($libelle, $desc = "", $img = NULL) {
                        parent::__construct("", "SUBMIT", $libelle, $libelle, $desc, TRUE);
                        $this->img = $img;
                }

                function setInput() {
                        if ($this->img) {
                                $img = " SRC=\"" . $this->img . "\"";
                        } else {
                                $img = "";
                        }
                        return "<INPUT TYPE='" . $this->type . "' NAME='boutonValider' VALUE=\"" . $this->valeur . "\"" . $img . ">";
                }

        }

        class ChampCaptcha extends ChampTexte {
                var $capt;
                public function __construct($taille) {
                        parent::__construct("captcha", "", "", $taille, NULL, "", TRUE);
                        $this->capt = new Captcha($taille);
                        $this->libelle = $this->r->lang("recopierlemot","form") . " <b><i>".$this->capt->captcha()."</i></b> ";
                        }                                      
        }
        
        class ChampEffacer extends Champ {

                var $img;

                function __construct($libelle, $desc = "", $img = NULL) {
                        parent::__construct("", "RESET", $libelle, $libelle, $desc, TRUE);
                        $this->img = $img;
                }

                function setInput() {
                        if ($this->img) {
                                $img = " SRC\"" . $this->img . "\"";
                        } else {
                                $img = "";
                        }
                        return "<INPUT TYPE='" . $this->type . "' NAME='boutonEffacer' VALUE=\"" . $this->valeur . "\"" . $img . ">";
                }

        }

        class ChampCoche extends Champ {

                var $checked;

                /*
                 * @param $type est CHECKBOX ou RADIO 
                 */

                function __construct($groupe, $valeur, $libelle, $desc, $checked = FALSE, $type = "CHECKBOX", $actif = TRUE) {
                        parent::__construct($libelle, $type, $groupe, $valeur, $desc, $actif);
                        $this->checked = $checked;
                }

                function setInput() {
                        if ($this->checked) {
                                $chk = "CHECKED";
                        } else {
                                $chk = '';
                        }
                        return "<INPUT TYPE='" . $this->type . "' NAME='" . $this->nom . "' VALUE=\"" . $this->valeur . "\" " . $chk . ">";
                }

        }

        /** regroupe les champs en tableau */
        class ChampGroupe extends Champ {

                var $champs; // tableau de champs radio du meme groupe
                var $groupe; // nom du groupe rassemblant les champs radio

                function __construct($libelle, $desc, $groupe, $champs = array(), $actif = TRUE) {
                        parent::__construct($libelle, "CHAMP_GROUPE", "", "", $desc, $actif);
                        // affecter les champs au groupe, verification de la correspondance du groupe entre les champs et le groupe désiré
                        $this->groupe = $groupe;
                        $n = 0;
                        for ($i = 0; $i < count($champs); $i++) {
                                if ($this->groupe == $champs[$i]->nom) {
                                        $this->champs[$n++] = & $champs[$i];
                                        $this->addEnfant($champs[$i]);
                                }
                        }
                }

                function setInput() {
                        $tbl = new Tableau(count($this->champs), 1, "champ_input_$this->type");

                        $modeTbl = LIBRE;
                        for ($i = 0; $i < count($this->champs); $i++) {
                                $tbl->setContenu_Cellule($i, 0, $this->champs[$i]->getHTML($modeTbl));
                        }

                        $tbl->setOptionsArray(array("HTML" => array("WIDTH" => "100%",
                                "ALIGN" => "CENTER")));

                        return $tbl->fin();
                }

        }

        class ChampSelect extends Champ {

                var $choix;
                var $selected;
                var $taille;

                function __construct($nom, $libelle, $desc, $choix = array(), $taille = 3, $selected = NULL, $actif = TRUE) { // $selected prend la valeur du choix a selectionner
                        parent::__construct($libelle, "SELECT", $nom, "", $desc, $actif);
                        $this->choix = $choix;
                        $this->selected = $selected;
                        $this->taille = $taille;
                }

                function setInput() {
                        $html = "<" . $this->type . " NAME='" . $this->nom . "' SIZE=" . $this->taille . ">\n";
                        foreach ($this->choix as $nom => $valeur) {
                                if ($this->selected == $valeur) {
                                        $s = "SELECTED";
                                } else {
                                        $s = "";
                                }
                                $html .= "<OPTION VALUE='$valeur' $s>$nom\n";
                        }
                        $html = $html . "</$this->type>\n";
                        //debug("ChampSelect:setInput");
                        return $html;
                }

        }

        class ChampAireTexte extends Champ {

                var $cols, $rows;

                function __construct($nom, $libelle, $desc, $cols, $rows, $vPDefaut = "", $actif = TRUE) {
                        parent::__construct($libelle, "TEXTAREA", $nom, $vPDefaut, $desc, $actif);
                        $this->cols = $cols;
                        $this->rows = $rows;
                }

                function setInput() {
                        return "<$this->type NAME='" . $this->nom . "' COLS=" . $this->cols . " ROWS=" . $this->rows . ">$this->valeur</$this->type>\n";
                }

        }

        class ChampFile extends Champ {

                var $maxfilesize;
                var $tailleChamp;

                function __construct($nom, $libelle, $desc, $maxfilesize = NULL, $tailleChamp = 20, $actif = TRUE) {
                        parent::__construct($libelle, "FILE", $nom, "", $desc, $actif);
                        $this->maxfilesize = $maxfilesize;
                        $this->tailleChamp = $tailleChamp;
                }

                function setInput() {
                        if (isset($this->maxfilesize)) {
                                $html = "<INPUT TYPE='HIDDEN' NAME='MAX_FILE_SIZE' VALUE='" . $this->maxfilesize . "'>";
                        }
                        return @$html . "<INPUT TYPE='" . $this->type . "' SIZE='" . $this->tailleChamp . "' NAME='" . $this->nom . "'>";
                }

        }

}
?>