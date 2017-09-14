<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:43:58 CEST 2004 @613 /Internet Time/
  @filename	php_stock.class.inc
 */

require $GLOBALS["php_module_html.inc.php"];
require $GLOBALS["php_tbl.class.inc"];
require $GLOBALS["php_SQL.class.inc"];
require $GLOBALS["php_image.class.inc"];
require $GLOBALS["php_formulaire.class.inc"];

global $ClasseStock;
if (!isset($ClasseStock)) {
        $ClasseStock = 1;
        /* !
          @class		Client
          @abstract   -- eShop -- Le client du magasin, il a accËs aux produits en Stock, et il peut effectuer des commandes en ligne. Il est associÈ ‡ un Caddie pour ses achats. La sauvegarde de la derniËre session se fait sur cookie.
          @discussion TODO image reprÈsentant le client.
         */
        /* !
          @const		CADDIE,COMMANDE,INFOS
          @abstract   diffÈrents modes d'afficher les attributs d'un Client. voir mÈthode afficher($mode)
          @discussion (description)
         */
        define("CADDIE", 0);
        define("COMMANDE", 1);
        define("INFOS", 2);

        define("HREF_CONDITIONS_VENTE", Client::R()->sitemap["shop__cgv"]);

        class Client {

                static $R = NULL;

                static function R() {
                        if (Info::$R === null) {
                                Info::$R = new Index(NULL);
                        }
                        return Info::$R;
                }

                var $attributs; // attributs primaires [],
                var $profil;
                var $sql; // SQL associÈ
                var $caddie; // Caddie associÈ
                var $stock; // Stock associÈ

                function __construct(SQL &$sql, &$stock, &$caddie, $identifiant, $mdp) {
                        $this->sql = $sql;
                        $this->stock = $stock;
                        $this->attributs = array("id" => $identifiant);
                        if (strlen($mdp) != 32) {
                                $this->attributs["motdepasse"] = md5($mdp);
                        } // codage md5 du mot de passe
                        else {
                                $this->attributs["motdepasse"] = $mdp;
                        }
                        /* si le client s'est dÈj‡ authentifiÈ .... */
                        $this->charger();

                        if ($this->authentification()) {
                                // crÈer un caddie pour le client si nÈcessaire
                                if (is_a($caddie, "Caddie"))
                                        $this->caddie = & $caddie;
                                $this->caddie = new Caddie;

                                /* gestion sÈcuritÈ */
                                // sauvegarde en session de l'id client courant, en session
                                $_SESSION["client"]["id"] = $this->attributs["id"];
                                $_SESSION["client"]["mdp"] = $this->attributs["motdepasse"];
                                // sauvegarde en cookie de l'objet client, il peut ainsi se reconnecter sur le site rapidement, apres fermeture du navigateur.
                                $this->sauver();
                        }
                }

                /* ----- partie privÈe ----- */

                /* !
                  @method     authentification
                  @abstract   authentification de l'utilisateur.
                  @discussion (description)
                 */

                function authentification() {
                        $res = $this->sql->query("SELECT fk-id-mdp FROM client WHERE identifiant =\"" . $this->attributs["identifiant"] . "\"");
                        $client = $this->sql->ligneSuivante_Array($res);
                        mysqli_free_result($res);

                        // test mot de passe
                        if ($client["fk-id-mdp"] === $this->attributs["motdepasse"])
                                return true;
                        else {
                                return false;
                        } // la page d'erreur est affichÈe
                }

                /* ----- partie publique ----- */

                /* !
                  @method     initCaddie
                  @abstract   remise ‡ zÈro du Caddie. ou rechargement caddie avec l'argument $newcaddie.
                  @discussion (description)
                 */

                function initCaddie($newcaddie = NULL) {
                        if ($newcaddie)
                                $this->caddie = $newcaddie;
                        else
                                $this->caddie = new Caddie;
                }

                /* !
                  @method		ajouter
                  @abstract   MÈthode pour ajouter un produit au Caddie du client. accËs SQL.
                  @discussion (description)
                 */

                function ajouter($id, $qte = 1);

                /* !
                  @method     supprimer
                  @abstract   MÈthode pour supprimer un produit du Caddie
                  @discussion (description)
                 */

                function supprimer($id, $qte = 1);

                /* !
                  @method     afficher
                  @abstract   mÈthode pour afficher les attributs du client. plusieurs choix: CADDIE,INFOS
                  @discussion (description)
                 */

                function afficher($mode = INFOS) {
                        switch ($mode) {
                                case INFOS:
                                        $tbl = new Tableau(5, 2);
                                        $tbl->setOptions_Array(array("class" => "infos"));
                                        $tbl->setContenu_Cellule(0, 0, "client eShop " . $attributs["identifiant"], array("HTML" => array("COLSPAN" => 2),
                                            "class" => "titre"));
                                        $tbl->setContenu_Cellule(1, 0, strtoupper($this->attributs["nom"]));
                                        $tbl->setContenu_Cellule(1, 1, $this->attributs["prenom"]);
                                        $tbl->setContenu_Cellule(2, 0, "AnnÈe de naissance:");
                                        $tbl->setContenu_Cellule(2, 1, $this->attributs["annee-de-naissance"]);
                                        $tbl->setContenu_Cellule(3, 0, "E-mail:");
                                        $tbl->setContenu_Cellule(3, 1, $this->attributs["email"]);
                                        $tbl->setContenu_Cellule(4, 0, "Adresse:");
                                        $tbl->setContenu_Cellule(4, 1, $this->attributs["adresse"] . ", " . $attributs["ville"] . ", " . $attributs["pays"], array("HTML" => array("" => "WRAP")));
                                        $tbl->setContenu_Cellule(5, 0, "N∞ TÈl.:");
                                        $tbl->setContenu_Cellule(5, 1, $this->attributs["numero-tel"]);
                                        return $tbl->fin();
                                        break;
                                case CADDIE:
                                        return $this->caddie->afficher();
                                        break;
                                default: break;
                        }
                }

                /* !
                  @method     getNom
                  @abstract   (description)
                  @discussion (description)
                 */

                function getNom() {
                        return $this->prenom . " " . $this->nom;
                }

                /* !
                  @method     getEmail
                  @abstract   (description)
                  @discussion (description)
                 */

                function getEmail() {
                        return $this->email;
                }

                /* !
                  @method     getMdp
                  @abstract   (description)
                  @discussion (description)
                 */

                function getMdp() {
                        return $this->mdp;
                }

                /* !
                  @method     setAttributs
                  @abstract   MÈthode pour dÈfinir les attributs tels que nom et prenom ainsi que les attributs secondaires.
                  @discussion (description)
                 */

                function setAttributs($email, $nom, $prenom, $annee, $ville, $pays, $email = NULL, $adresse = NULL, $telephone = NULL) {
                        $this->attributs["email"] = $email;
                        $this->attributs["nom"] = $nom;
                        $this->attributs["prenom"] = $prenom;
                        $this->attributs["adresse"] = $adresse;
                        $this->attributs["numero-tel"] = $telephone;
                }

                /* !
                  @method     sauver
                  @abstract   MÈthode pour sauvegarder (serialize()) le Client courant dans un cookie. Il peut se reconnecter aisÈment sur le site, le caddie est conservÈ.
                  @discussion (description)
                 */

                function sauver() {
                        setcookie("eShopClient", serialize($this), "", "/~traffic/", ".b23prodtm.info");
                }

                /* !
                  @method     charger
                  @abstract   MÈthode inverse de sauver (unserialize()).
                  @discussion (description)
                 */

                function charger() {
                        if (isset(filter_input(INPUT_COOKIE, 'eShopClient'))) {
                                $client = unserialize(filter_input(INPUT_COOKIE, 'eShopClient'));
                                $this->attributs = $client->attributs;
                                $this->initCaddie($client->caddie);
                                /* !!! L'id du client comme son mot de passe ne sont pas rechargÈs pour des raisons de sÈcuritÈ, SAUF si le temps limite est ok. */
                        }
                }

                /* !
                  @method     sendEmail
                  @abstract   envoi d'un email au client courant.
                  @discussion (description)
                 */

                function sendEmail($sujet, $message, $adresse_retour) {
                        if (mail($this->attributs["email"], $sujet, $message, "Reply-to:" . $adresse_retour))
                                return "Message envoyÈ ‡ " . $this->attributs["email"] . "!";
                        else
                                return "Le message n'a pas ÈtÈ envoyÈ. Une erreur est survenue.";
                }

                /* ----- mÈthodes de base ----- */

                /* !
                  @method     formAuthentification
                  @abstract   formulaire d'authentification du client, affichable partout
                  @discussion (description)
                 */

                function formAuthentification($page) {
                        $f = new Formulaire("client eShop", $page, VERTICAL);
                        $identifiant = new ChampTexte("eShopClient['id']", "votre identifiant :", "20 caracteres max.", 20, 20);
                        $motdepasse = new ChampPassword("eShopClient['mdp']", "votre mot de passe :", "8 carac. max.", 10, 8);
                        $ok = new ChampValider("OK");
                        $f->ajouterChamp($identifiant);
                        $f->ajouterChamp($motdepasse);
                        $f->ajouterChamp($ok);
                        return $f->fin();
                }

                /* !
                  @method     formInscription
                  @abstract   formulaire d'inscription pour le client.
                  @discussion (description)
                 */

                function formInscription($page) {
                        $f = new Formulaire("inscription client eShop", $page, VERTICAL);
                        $nom = new ChampTexte("nom", "Donnez votre nom :", "", 30, 30);
                        $prenom = new ChampTexte("prenom", "Donnez votre prÈnom :", "", 30, 30);
                        $annee = new ChampTexte("annee", "Donnez votre annÈe de naissance :", "", 4, 4);
                        $identifiant = new ChampTexte("identifiant", "Donnez un identifiant :", "20 carac. max.", 20, 20);
                        $email = new ChampTexte("email", "Donnez votre adresse e-mail :", "une adresse e-mail correcte est nÈcessaire", 20, 20);
                        $mdp = new ChampPassword("motdepasse", "Donnez un mot de passe :", "", 8, 8);
                        $ok = new ChampValider("OK");
                        $effacer = new ChampEffacer("Effacer");
                        $f->ajouterChamp($nom);
                        $f->ajouterChamp($prenom);
                        $f->ajouterChamp($annee);
                        $f->ajouterChamp($email);
                        $f->ajouterChamp($identifiant);
                        $f->ajouterChamp($motdepasse);
                        $f->ajouterChamp($ok);
                        $f->ajouterChamp($effacer);
                }

                /* !
                  @method     inscrire
                  @abstract   MÈthode pour inscrire le client dans la base de donnÈes SQL.
                  #attributs : un tableau contenant les attrbuts du client, i.e. array("identifiant" => '$identifiant',
                  "nom" => '$nom',
                  "prenom" => '$prenom',
                  "annee-de-naissance" => '$annee',
                  "ville" => '$ville',
                  "pays" => "$pays",
                  'email' => "$email",
                  "id-unique" => "$id-unique-du-mot-de-passe", // 32 carac. transformation en clÈ($motdepasse)
                  "adresse" => "$adresse",
                  "numero-tel" => $numero,
                  );
                  @discussion (description)
                 */

                function inscrire(SQL &$sql, $attributs, $motdepasse) {
                        $res = $sql->query("INSERT INTO motdepasse (id-unique, motdepasse) VALUES (\"" . $attributs["id-unique"] . "\", \"" . $motdepasse . "\")");
                        if (!$res) {
                                $this->authentification();
                        }
                        $res = $sql->query("INSERT INTO client (identifiant, nom, prenom, annee-de-naissance, ville, pays, email, fk-id-mdp, adresse, numero-tel) VALUES (\"" . $attributs["identifiant"] . "\", \"" . $attributs["nom"] . "\", \"" . $attributs["prenom"] . "\", \"" . $attributs["annee-de-naissance"] . "\", \"" . $attributs["ville"] . "\", \"" . $attributs["pays"] . "\", \"" . $attributs["email"] . "\", \"" . $attributs["id-unique"] . "\", \"" . $attributs["adresse"] . "\", \"" . $attributs["numero-tel"] . "\")");
                        if ($sql->LigneAffectees() > 0) {
                                $message = "Le client a bien ÈtÈ inscrit en base de donnÈes. Votre identifiant est " . $attributs["identifiant"] . ". Veuillez conserver votre mot de passe!";
                        } else {
                                $message = "Erreur! mysqli_error: " . $sql->listeErreurs();
                        }
                        return $message;
                }

                /* !
                  @method	    desinscrire
                  @abstract   MÈthode pour dÈsinscrire le client de la base de donnÈes SQL.
                  @discussion
                 */

                function desinscrire(SQL &$sql, $identifiant, $motdepasse) {
                        $res = $sql->query("DELETE motdepasse FROM client,motdepasse INNER JOIN ON id-unique = fk-id-mdp WHERE identifiant=\"$identifiant\" AND motdepasse = \"$motdepasse\""); // controle mdp -<>
                        if ($sql->LigneAffectees() > 0) {
                                $message = "Le client $email a ÈtÈ supprimÈ de la base de donnÈe!";
                        } else {
                                $message = "Erreur! mysqli_error: " . $sql->listeErreurs();
                        }
                }

        }

        /* !
          @class          Caddie
          @abstract       Un caddie pour chaque client qui se connecte au site
          @discussion     TODO: mÈthode afficher()

         */

        class Caddie {

                var $produits;  // tableaux d'id des produits dans le caddie
                var $taxes; // rabais,commission, appliquÈ au client.
                var $sql; // connexion SQL
                var $stock; // objet Stock, pour l'accËs aux produits

                function __construct(SQL &$sql, &$stock) {
                        $this->sql &= $sql;
                        $this->stock &= $stock;
                }

                /*  ----- partie privÈe ----- */

                /* !
                  @method         infosProduit
                  @abstract       MÈthode pour obtenir les infos du produit en Stock. retourne un array indexÈ
                  @discussion
                 */

                function infosProduit($id) {
                        $res = $this->sql->query("SELECT * FROM produit WHERE reference-produit=\"$id\" LIMIT 1");
                        $ret = $this->sql->LigneSuivante_Array($res);
                        mysqli_free_result($res);
                        return $ret;
                }

                /* ----- partie publique ----- */

                /* !
                  @method         countItems
                  @abstract       MÈthode pour compter le nombre de produits prÈsents dans le caddie.
                  @discussion
                 */

                function countItems() {
                        return count($this->produits);
                }

                /* !
                  @method         ajouter
                  @abstract       MÈthode pour ajouter un produit au caddie.
                  @discussion
                 */

                function ajouter($id) {
                        $this->produits[] = $id;
                }

                /* !
                  @method         supprimer
                  @abstract       MÈthode pour supprimer un produit du caddie.
                  @discussion
                 */

                function supprimer($id) {
                        foreach ($this->produits as $key => $ref) {
                                if ($ref == $id)
                                        unset($this->produits[$key]);
                                else
                                        continue;
                        }
                }

                /* !
                  @method         soustotal
                  @abstract       MÈthode qui retourne le sous-total de la commande, i.e. sans les rabais client et/ou commissions
                  @discussion
                 */

                function soustotal() {
                        $soustotal = 0;
                        foreach ($this->produits as $key => $ref) {
                                $query = "SELECT * FROM produit WHERE ref ='$ref';";
                                $article = $this->stock->getProduit($query);
                                $soustotal += $article["prix-de-vente"];
                        }
                        return $soustotal;
                }

                /* !
                  @method         total
                  @abstract       MÈthode qui retourne le total de la commande.
                  @discussion
                 */

                function total($devise = "CHF") {
                        $taxes = $this->taxes;
                        $tbl = new Tableau(2 + count($taxes), 2);
                        $i = 0;
                        $total = $this->soustotal();
                        $tbl->setContenu_Cellule(0, 0, "<i>sous-total: </i>");
                        $tbl->setContenu_Cellule(0, 1, $this->soustotal() . $devise);
                        // TAXES,RABAIS,FRAIS DE TRANSPORT,...
                        foreach ($taxes as $nomination => $montant) {
                                $total += $montant;
                                $tbl->setContenu_Cellule($i++, 0, $nomination . ": ");
                                $tbl->setContenu_Cellule($i, 1, $montant . $devise);
                        }
                        $tbl->setContenu_Cellule($tbl->nbLignes - 1, 0, "<B>TOTAL: </B>");
                        $tbl->setContenu_Cellule($tbl->nbLignes - 1, 1, $total . $devise);
                        return $tbl->fin();
                }

                /* !
                  @method         afficher
                  @abstract       affiche le contenu du Caddie.
                  @discussion
                 */

                function afficher() {
                        $tbl = new Tableau(3 + count($this->produit), 5);
                        $i = 0;
                        $tbl->setContenu_Cellule(0, 0, "RÈf. produit");
                        $tbl->setContenu_Cellule(0, 1, "RÈf.Èditeur");
                        $tbl->setContenu_Cellule(0, 2, "Nom");
                        $tbl->setContenu_Cellule(0, 3, "Prix");
                        $tbl->setContenu_Cellule(0, 4, "Action");
                        $tbl->setContenu_Cellule($this->nbLignes - 1, 0, "Conditions de vente - " . HTML_lien(URL_CONDITIONS_VENTE, "Consulter les conditions de vente", array("HTML" => array("COLSPAN" => 5)), "_parent"));
                        $tbl->setContenu_Cellule($this->nbLignes - 2, 3, $this->total());
                        foreach ($this->produit as $key => $ref) {
                                $query = "SELECT * FROM produit WHERE ref ='$ref';";
                                $article = $this->stock->getProduit($query);
                                $tbl->setContenu_Cellule($i++, 0, $article["reference-produit"]);
                                $tbl->setContenu_Cellule(0, 1, $article["fk-reference-editeur"]);
                                $tbl->setContenu_Cellule(0, 2, $article["nom"]);
                                $tbl->setContenu_Cellule(0, 3, $article["prix-de-vente"]);
                                $tbl->setContenu_Cellule(0, 4, HTML_boutonLoad("#_self?supprimer=" . $ref, "supprimer"));
                        }
                        return $tbl->fin();
                }

        }

        /* !
          @class		Stock
          @abstract	Classe gérant les produits en stock. voir les méthodes disponibles.
          @discussion
         */

        class Stock {

                var $sql; //connexion à SQLDB
                var $produit; //produit courant {array <= mysqli_fetch_array()}
                var $transport; //transporteur

                function __construct(SQL &$sql, $transport) {
                        $this->sql &= $sql;
                        $this->transport = $transport;
                        $this->produit = NULL;
                }

                /* !
                  @method		getProduit
                  @abstract	Méthode pour obtenir un/des produits en stock selon la syntaxe de l'argument $query. Syntaxe: 	- REQUETE SQL.
                  - idProduit SQL
                  Retourne un array (une ligne par produit) <= mysqli_fetch_array()
                  @discussion
                 */

                function getProduit($query) {
                        /** TODO : CORRIGER LA REQUETE SQL */
                        $res = $this->sql->query($query);
                        $ret = $this->sql->LigneSuivante_Array($res);
                        mysqli_free_result($res);
                        return $ret;
                }

                /* !
                  @method		buyProduit
                  @abstract	Méethode qui effectue un achat, pour autant qu'il y ait des exemplaires du produit. | $id | peut etre composé d'une id SQL ou de plusieurs ( array($id1,$id2,...,$idn) )
                  Retourne les reference-exmplaire et fk-reference-produit
                  @discussion	Le stock est-il à quantité limitée? voir besoin du magasin.
                 */

                function buyProduit($id) {
                        $ids = $id;
                        if (is_array($id)) {
                                $sep = "";
                                foreach ($id as $ref) {
                                        $ids .= $sep . "\"$ref\"";
                                        $sep = ",";
                                }
                        }
                        $res = $this->sql->query("SELECT reference-exemplaire,fk-reference-produit FROM exemplaire WHERE fk-reference-produit IN ($ids)");
                        $ret = $this->sql->LigneSuivante_Array($res);
                        mysqli_free_result($res);
                        return $ret;
                }

                /* !
                  @method     sellProduit
                  @abstract   Attention! Cette methode efface l'exemplaire en stock, sans possibilitÈ de retour => prÈvoir une demande de confirmation avant execution!
                  @discussion (description)
                 */

                function sellProduit($ref) {
                        $res = $this->sql->query("DELETE FROM exemplaire WHERE fk-reference-produit = \"$ref\"");
                        return $this->sql->LignesAffectees();
                }

                /* !
                  @method		addProduit
                  @abstract	Méthode qui ajoute UN nouveau produit au stock. TOUS les champs doivent etre fournis, NULL est synonyme de valeur non communiquée.
                  @param		#fields array(fk-reference-editeur,nom,prix-d-achat,prix_de_vente, fk-reference-categorie, image-filename => NULL )
                  @discussion
                 */

                function addProduit($fields) {
                        $res = $this->sql->query("INSERT INTO TABLE produit ('fk-reference-editeur','nom','prix-d-achat','prix-de-vente','fk-reference-categorie') VALUES (\"" . $fields["fk-reference-editeur"] . "\",\"" . $fields["nom"] . "\".\"" . $fields["prix-d-achat"] . "\",\"" . $fields["prix-de-vente"] . "\",\"" . $fields["fk-reference-categorie"] . "\"");
                        $reference = mysqli_insert_id($this->sql->connexion);
                        if (is_array($fields["images"]["filename"])) {
                                foreach ($fields["images"]["filename"] as $file) {
                                        $img = new Image();
                                        $img->setFile($file);
                                        // sauver en referenÁant chaque image pour le produit ajoutÈ ‡ la base SQL
                                        $img->saveToSQL($this->sql, $reference);
                                }
                        }
                        return $reference;
                }

                /* !
                  @method 	deleteProduit
                  @abstract	Méthode pour supprimer un/des produits du stock. Seule l'id doit etre fournie. Utiliser un array pour supprimer plusieurs produits, e.g. array($id1,$id2,...)
                  @discussion
                 */

                function deleteProduit($id) {
                        $ids = $id;
                        if (is_array($id)) {
                                $sep = "";
                                foreach ($id as $ref) {
                                        $ids .= $sep . "\"$ref\"";
                                        $sep = ",";
                                }
                        }
                        $res = $this->sql->query("DELETE FROM produit WHERE reference-produit = \"$ids\"");
                        return $this->sql->LignesAffectees();
                }

                /* !
                  @method		addListe
                  @abstract	Méthode qui ajoute une liste complète de produits. la liste est un array, chaque ligne correspond à un produit
                  retourne les references nouvellement crÈes.
                  @discussion
                 */

                function addListe($produits) {
                        $ids = array();
                        foreach ($produits as $produit)
                                $ids[] = $this->addProduit($produit);
                        return $ids; // last inserted id's
                }

                /* !
                  @method     addExemplaire
                  @abstract   ajouter un ou plusieurs exemplaires du produit reference par le deuxieme argument $ref_prod
                  @discussion (description)
                 */

                function addExemplaire($n, $ref_prod) {
                        for ($i = 0; $i < $n; $i++) {
                                $ref_exp = mhash_keygen_s2k(MHASH_MD5, $ref_prod, time(), 4);
                                $res = $this->sql->query("INSERT INTO exemplaire (reference-exemplaire, fk-reference-produit) VALUES (\"$ref_exp\",\"$ref_prod\")");
                        }
                }

        }

        /* !
          @class		Transport
          @abstract	Classe de transport des produits entre les revendeurs et le Stock du magasin, et evtl. du stock vers le client. Notamment prise en charge de réaprovisionnement du Stock par fichiers XML.
          @discussion
         */

        class Transport {

                var $protocole; // XML, TXT, HTML/FORM ;-)
                var $fournisseur; // instance Client
                var $stock; // instance Stock

                function __construct($protocole, &$fournisseur, &$stock) {
                        $this->protocole = $protocole;
                        $this->fournisseur &= $fournisseur;
                        $this->stock &= $stock;
                }

                /* ----- partie privée ----- */
                /* !
                  @method     charger
                  @abstract   (description)
                  @discussion (description)
                 */

                function charger(&$produits) {
                        // check field
                        $field = "nom";
                        foreach ($produits as $key => $produit) {
                                $query = "SELECT * FROM produit WHERE $field =\"" . $produit[$field] . "\";";
                                $res = $this->stock->getProduit($query);
                                $stock = $this->sql->LigneSuivante_Array($res);
                                if ($produit[$field] == $stock[$field]) {
                                        unset($produits[$key]);
                                }
                        }
                        $ids = $this->stock->addList($produits);
                        echo "Product N∞ " . $ids[$i] . " added!";
                }

                /* !
                  @method     decharger
                  @abstract   met a jour la liste des exmplaires (table SQL exemplaire) avec de la nouvelle fourniture = array($exmplaire1,$2,$3,..). un exemplaire = array(reference-exemplaire,fk-reference-produit)
                  @discussion (description)
                 */

                function decharger(&$fourniture) {
                        $references = $this->stock->addListe($fourniture);
                        foreach ($references as $id) {
                                // champ reference
                                $reference = "reference-produit";
                                $this->stock->addExemplaire(count($fourniture[$id]), $fourniture[$id][$reference]);
                        }
                }

                /* !
                  @method     saisirXML
                  @abstract   (description)
                  @discussion (description)
                 */

                /* !
                  @method     saisirTXT
                  @abstract   (description)
                  @discussion (description)
                 */
                /* !
                  @method     saisirPOST
                  @abstract   Saisie des produits envoyÈs par formulaire HTML.
                  retourne un tableau php qui contient un produit.
                  @discussion (description)
                 */

                function saisirPOST($donneesPost) {
                        $produit = $donneesPost;
                        return $produit;
                }

                /* ----- partie publique ----- */

                /* !
                  @method     setProtocole
                  @abstract   (description)
                  @discussion (description)
                 */

                function setProtocole($mode = "POST") {
                        $this->protocole = $mode;
                }

                /* !
                  @method     fournir
                  @abstract   (description)
                  @discussion (description)
                 */

                function fournir($produits) {
                        $this->charger($produits);
                        $this->decharger($produits);
                }

                /* !
                  @method     formFournir
                  @abstract   (description)
                  @discussion TODO: Champs CommentÈs
                 */

                function formProduit($mode) {
                        if ($mode == "POST") {
                                $donnees = $this->saisirPOST($_POST);
                        }
                        $images = $donnees["images"];
                        $image = $donnees["image"];
                        $f = new Formulaire("Fournir le produit", URL_STOCK);
                        // champ selection classe
                        include($GLOBALS['phps_formulaire_categorie.inc.phps']);
                        $champ = new ChampTexte("nom", "Nom de votre Produit: ", "30 carac. maximum", 30, 30);
                        $f->ajouterChamp($champ);
                        $champ = new ChampTexte("editeur", "Nom de l'Èditeur/fabricant: ", "4 carac. maximum", 4, 4);
                        $f->ajouterChamp($champ);
                        // champ description produit
                        $champ = new ChampAireTexte("description", "Description: ", "", 80, 20);
                        // champ selection images
                        $images = new Formulaire("images", URL_STOCK);
                        for ($i = 0; $i < 3; $i++) {
                                $champImages[] = ChampFichier("image[]", "Illustration n∞$i: ", "$n illustration", 10);
                                $champ = new ChampCoche("images['garde'][]", $image[$i]["filename"], $image[$i]["filename"], ${'image' . $i});
                                $f->ajouterChamp($champ);
                        }
                        $champ = new ChampGroupe("Illustration de la fiche du produit", "Illustrer par 1-3 images.", "image[]", $champImages);
                        $champ = new ChampValider("Ajouter");
                        $images->ajouterChamp($champ);
                        $champ = new ChampCoche("images", "images[]", "images", "Images insÈrÈes " . $images->fin(), $images['garde']);
                        $f->ajouterChamp($champ);
                        $champ = new ChampTexte("prix-de-vente", "Prix de vente: ", "0000.0", 5, 5);
                        $f->ajouterChamp($champ);
                        // information sur le fournisseur dÈmarrage sÈcurisÈ
                        $client = new Client($this->sql, $this->stock, new Caddie($this->sql, $this->stock), $_SESSION['client']["id"], $_SESSION['client']["mdp"]);
                        $champ = new ChampGroupe("Authentification client eShop", "remplissage nÈcessaire de tous les champs", "client[]", Client::formAuthentification(URL_STOCK));
                        $f->ajouterChamp($champ);
                        $champ = new ChampGroupe("Inscription vendeur eShop", "remplissage nÈcessaire de tous les champs", "client[]", Client::formInscription(URL_STOCK));
                        $f->ajouterChamp($champ);
                        $champ = new ChampCache("code-fournisseur", mhash_keygen_s2k(MHASH_MD5, $_SESSION['client']['id'], "code", 4));
                        $f->ajouterChamp($champ);
                        $champ = new ChampValider("Valider");
                        $f->ajouterChamp($champ);
                        $champ = new ChampEffacer("Effacer");
                        $f->ajouterChamp($champ);

                        return $f->fin();
                }

        }

}
?>