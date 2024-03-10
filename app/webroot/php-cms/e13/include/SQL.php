<?php

/* !
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date	Sat Sep 18 15:43:44 CEST 2004 @613 /Internet Time/
  @filename	php_SQL.class.inc
 */
if (!isset($_ENV['classeSQL'])) {
        $_ENV['classeSQL'] = 1;
        ${__FILE__} = new Index();
        include_once basename(${__FILE__}->r['include__php_constantes.inc']);

        /* ne pas omettre de fermer les connexions SQL avant de quitter le script */

        class SQL {

                var $connexion; // flux vers SQL
                var $serveur;
                var $port;
                var $base;
                var $tables; // array des tables utilisees dans cette connexion vers une base SQL
                var $utilisateur;
                private $mdp;

                /* connexion automatique, appeler close() en fin de script si $pconnect actif (par defaut) */

                public function __construct($serveur, $base, $utilisateur, $mdp, $port = 3306) {
                        $this->connect($serveur, $utilisateur, $mdp, $base, $port);
                        $this->base = $base;
                        $this->utilisateur = $utilisateur;
                        $this->mdp = $mdp;
                        $this->serveur = $serveur;
                        $this->port = $port;
                        if (mysqli_connect_error()) {
                                return;
                        } else {
                                $this->listeTables();
                        }
                }

                public function __destruct() {
                        $this->close();
                }

                public function close() {
                        if ($this->connexion !== NULL) {
                                $b = mysqli_close($this->connexion); // fermeture de la connexion persistante (mysqli_pconnect)
                                $this->connexion = NULL;
                                return $b;
                        }
                }

                /* ---- partie privee ---- */

                private function connect($serveur, $utilisateur, $mdp, $base, $port) {
                        if ($this->connexion !== NULL) {
                                $this->close();
                                echo "Une connexion existante &agrave; éeacute;téeacute; coup&eacute;e.";
                        }
                        $this->connexion = mysqli_init();
                        mysqli_real_connect($this->connexion, $serveur, $utilisateur, $mdp, $base, $port)
                        or i_debug("Le serveur "
                        . substr($serveur, 0, 10) . " a renvoy&eacute l'erreur suivante: "
                        . mysqli_connect_error() . ". Veuillez initialiser la base de donn&eacute;es");
                }

                /* ---- partie publique ---- */

                public function connect_succes() {
                        $e = mysqli_connect_error();
                        return $e === NULL;
                }

                /** retourne un resultat mwsqli_result (SELECT,SHOW,...) ou TRUE | FALSE (DELETE, UPDATE, INSERT). */
                public function query($string) {
                        i_debug("Query SQL : ".$string);
                        return mysqli_query($this->connexion, $string);
                }

                /** prepare un statement
                 */
                public function send_long_data($query, &$data, &$insert_id, &$stmt) {
                        $stmt = mysqli_stmt_init($this->connexion);
                        mysqli_stmt_prepare($stmt, $query);
                        $n = NULL;
                        mysqli_stmt_bind_param($stmt, "b", $n);
                        mysqli_stmt_send_long_data($stmt, 0, $data);
                        i_debug("SQL STMT data : " . strlen($data) / 1024 . " kB\n");
                        $b = mysqli_stmt_execute($stmt);
                        $insert_id = mysqli_stmt_insert_id($stmt);
                        return $b;
                }

                /**
                 * retourne la liste des erreurs de le commande precedant sous forme de liste (array() par defaut).
                 * @see mysqli_error_list
                 * @see mysqli_stmt_error_list
                 */
                public function listeErreurs(&$stmt = NULL) {
                        return $stmt ? mysqli_stmt_error_list($stmt) : mysqli_error_list($this->connexion);
                }

                /** succes de la requete SELECT */
                public function select_succes(&$result) {
                        return count($this->listeErreurs()) < 1 && mysqli_num_rows($result) > 0;
                }

                /** ecrit sur la sortie standard une liste HTML des erreurs reportees */
                public function afficheErreurs(&$stmt = NULL, $echo = 1) {
                        $html = "<ol>";
                        foreach ($this->listeErreurs($stmt) as $err) {
                                $html .= "<li>";
                                foreach ($err as $c => $v) {
                                        $html .= $c . " : " . $v . "<BR>";
                                }
                                $html .= "</li>";
                        }
                        $html .= "</ol>";
                        if($echo == 1) {
                              echo $echo;
                        }
                        return $html;
                }

                public function listeTables() {
                        $resultat = $this->query("SHOW TABLES FROM " . $this->base);
                        if ($resultat) {
                                while ($t = $this->ligneSuivante($resultat)) {
                                        $this->tables[] = $t;
                                }
                                mysqli_free_result($resultat);
                        }
                }

                public function changeUtilisateur($nom, $mdp, $base = NULL) {
                        if ($base == NULL) {
                                $base = $this->base;
                        }
                        if (mysqli_change_user($this->connexion, $nom, $mdp, $base) > 0) {
                                $this->utilisateur = $nom;
                                $this->mdp = $mdp;
                                $this->base = $base;
                                return TRUE;
                        } else {
                                echo "Impossible de changer d'utilisateur!";
                                echo "<BR>Erreur MySQL: " . mysqli_error($this->connexion);
                                return FALSE;
                        }
                }

                /* ---- methodes de base BEGIN ---- */
                /* methodes ligne suivante BEGIN */

                public function ligneSuivante(mysqli_result &$resultat) {
                        if ($resultat && is_a($resultat, "mysqli_result")) {
                                return mysqli_fetch_row($resultat);
                        } else {
                                return FALSE;
                        }
                }

                public function ligneSuivante_Array(mysqli_result &$resultat) {
                        if ($resultat && is_a($resultat, "mysqli_result")) {
                                return mysqli_fetch_array($resultat, MYSQLI_ASSOC);
                        } else {
                                return FALSE;
                        }
                        // return mysqli_fetch_array($resultat, MYSQL_ASSOC);
                        // return mysqli_fetch_array($resultat, MYSQL_BOTH);
                        // return mysqli_fetch_array($resultat, MYSQL_NUM);
                }

                public function ligneSuivante_Object(mysqli_result &$resultat) {
                        if ($resultat && is_a($resultat, "mysqli_result")) {
                                return mysqli_fetch_object($resultat);
                        } else {
                                return FALSE;
                        }
                }

                /* ligne suivante END */

                public function lignesAffectees() {
                        return mysqli_affected_rows($this->connexion);
                }

                public function selectLigne(mysqli_result &$resultat, $offset) {
                        if ($resultat && is_a($resultat, "mysqli_result")) {
                                return mysqli_data_seek($resultat, $offset);
                        } else {
                                return FALSE;
                        }
                }

        }

        /**
         * Utilisation des variables INPUT_* :
         * <pre> $post = filter_input_array(INPUT_POST);
         * $key = "info_a_supprimer";
         * if (array_key_exists($key, $post)) {
         * $query = postArrayVersQueryID($key, $post);
         * ...
         * }
         * </pre>
         * @param string $postKey
         * @param filter_input_array $post
         * @return string (id,id2,id3,...)
         */
        function postArrayVersQueryID($postKey, $post) {
                if ($post && array_key_exists($postKey, $post)) {
                        $query = "";
                        $sep = "";
                        foreach ($post[$postKey] as $id) {
                                $query .= $sep . $id;
                                $sep = ",";
                        }
                        return $query;
                }
                return "";
        }

}
?>
