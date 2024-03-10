<?php
if (!isset($_ENV['ClasseAdminPage'])) {
        $_ENV['ClasseAdminPage'] = 1;

        // Classe gerant toutes les pages php du site
        ${__FILE__} = new Index();
        include basename(${__FILE__}->r['include__php_page.class.inc']);
        /* page en mode administration */

        class AdminPage extends Page {

                var $mdp; // mode passe pour $this->user ($user herite de Page...)

                function __construct($r, $sitemapKey, $sessionId, $namedParam = array(), $sendHeaders = TRUE) {

                        parent::__construct($r, $sitemapKey, $sendHeaders, $sessionId);
                        $sep = "?";
                        foreach ($namedParam as $key => $val) {
                                $this->proprietes["URL"] .= $sep . $key . ($val ? "=" . $val : "");
                                $sep = "&";
                        }
                        $this->setEnteteLogoEnabled(false);
                        $this->user = "staff";
                        /* mdp de la page ADMIN look include/getHashPassword.php */
                        $this->mdp = PASSWORD_ADMIN;
                        echo $this->r->lang("login", "admin");
                        /** nothing set in session and no captcha */
                        $captcha = "";
                        if (!Captcha::verification($this, $captcha) && (!array_key_exists('client', $_SESSION) || !array_key_exists('mdp', $_SESSION['client']))) {
                                $this->ajouterContenu("<br><br><center><b>" . $this->r->lang("section", "admin") . "</b></center><br>" . $this->r->lang("loginsvp", "admin") . "<br>" . $captcha);
                                $this->form_authentification($this->getURL());
                                $this->fin();
                        } else {
                                $mdpmd5 = filter_input(INPUT_POST, 'motdepasse') ? md5(filter_input(INPUT_POST, 'motdepasse')) : $_SESSION['client']['mdp'];
                                $this->user = filter_input(INPUT_POST, 'id') ? filter_input(INPUT_POST, 'id') : $_SESSION['client']['id'];
                                echo $this->r->lang("enregistrementsession", "admin");
                                /** utilise le salt de PASSWORD_ADMIN pour crypter motdepasse, et donc retourne le meme hashcode si le
                                 * motdepasse est le meme que celui enregistre par PASSWORD_ADMIN  */
                                if (self::valide($mdpmd5)) {
                                        /** la safeSession est activee (voir classe formulaire et balise FORM), qui desactive les formulaires si non activee */
                                        $_SESSION["client"]['id'] = $this->user;
                                        $_SESSION["client"]['mdp'] = $mdpmd5;
                                        $this->ajouterContenu("<br><br><center><b>" . $this->r->lang("section", "admin") . "</b></center><br>" . $this->r->lang("vousetesauthentifie", "admin") . " : " . $this->user);
                                } else {
                                        $this->ajouterContenu("<br><br><center><b>" . $this->r->lang("section", "admin") . "</b></center><br>$this->user/:>" . $this->r->lang("erreurauth", "admin") . (filter_input(INPUT_POST, 'motdepasse') != filter_input(INPUT_POST, 'motdepasse_confirm') ? $this->r->lang("confirmermdp", "admin") : $this->r->lang("confirmerutilisateur", "admin")));
                                        $this->form_authentification($this->getURL());
                                        $this->fin();
                                }
                        }
                        echo $this->r->lang("succes", "admin");
                        $this->menu->ouvrirBonneRubrique($this->getURL());
                }

                /* ----- partie privee ----- */

                function form_authentification($script) {
                        echo "formulaire";
                        $form_auth = new Formulaire("Authentification", $script, VERTICAL, "formulaire");
                        // champs
                        $user = new ChampTexte("id", $this->r->lang("utilisateur", "form"), $this->r->lang("entrerutilisateur", "form"), 10, 20, $this->user, TRUE);
                        $form_auth->ajouterchamp($user);
                        $user_mdp = new ChampPassword("motdepasse", $this->r->lang("mdp", "form"), $this->r->lang("entrermdp", "form"), 10);
                        $form_auth->ajouterchamp($user_mdp);
                        $captcha = new ChampCaptcha(5);
                        $form_auth->ajouterChamp($captcha);
                        $valider = new ChampValider("OK");
                        $form_auth->ajouterChamp($valider);
                        $effacer = new ChampEffacer("Effacer");
                        $form_auth->ajouterChamp($effacer);
                        // fin formulaire
                        $this->ajouterContenu($form_auth->fin());
                }

                /* ----- partie publique ----- */
        }
}
?>
