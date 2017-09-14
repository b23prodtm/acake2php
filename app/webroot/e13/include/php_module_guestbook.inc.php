<?php/* !  @header		librairie de fonctions pour la gestion d'un guest book  @abstract   Les messages sont stock�s dans une base donn�es, en l'occurence un fichier txt format�.  @discussion (description) */require $GLOBALS["include__php_info.class.inc"];require $GLOBALS["include__php_formulaire.class.inc"];global $Module_Guestbook;if (!isset($GLOBALS['Module_Guestbook'])) {    $Module_Guestbook = 1;    /* !      @function   enregistrerMsg      @abstract   Enregistrement d'un message dans la base      @discussion (description)      @param      #contenu Contenu du message      #auteur Auteur du message      #date Date du message      #base base de donn�es, le chemin vers le fichier txt      @result     $id du message enregistr�     */    function enregistrerMsg($contenu, $auteur, $date, $base) {        $handle = fopen($base, 'w+');        $id = crypt($auteur, "guest");        $msg = "#id \"" . addslashes($id) . "\";\n\r" .                "#contenu \"" . addslashes($contenu) . "\";\n\r" .                "#auteur \"" . addslashes($auteur) . "\";\n\r" .                "#date \"" . addslashes($date) . "\";\n\r";        fwrite($handle, $msg) ? printf("Votre message a �t� ajout� au guest book. id : " . $id) : printf("Erreur lors de l'ajout de votre message au guest book");        return $id;    }    /* !      @function   trouverPointeurs      @abstract   Recherche des pointeurs vers le message dans la base de donn�es du guest book.      @discussion (description)      @param      #id ID du message recherch�      #&handle passage par reference du pointeur du fichier de la base de donn�es.      @result true|false     */    function trouverPointeurs($id, &$handle) {        $trouve = false;        $recherche = array("#id ", "#contenu ", "#auteur ", "#date ");        $i = 0;        while (!$trouve) {            if (!is_string($n = fread($handle, $i + strlen($recherche[0]))))                break;            if ($n === $recherche[0]) {                fseek($handle, $i + strlen($recherche[0]) + 1);                $id_temp = fread($handle, 4); // lecture sur 4 octets de l'id du message trouv�                if ($id_temp == $id) {                    $trouve = true;                    fseek($handle, $i); // positionnement du pointeur au d�but de l'enregistrement du message                }            }            $i++;        }        if ($trouve) {            $pointeurs = array();            foreach ($recherche as $str) {                $i = 0;                while (!$trouve) {                    if (!is_string($n = fread($handle, $i + strlen($str))))                        break;                    if ($n === $str) {                        $trouve = true;                        $pointeurs[] = $i + strlen($str);                        fseek($handle, $i + strlen($str)); // positionnement du pointeur au d�but de l'enregistrement du message                    }                    $i++;                }            }            $i = 0;            foreach ($recherche as $str) {                $pointeurs[$i]["str"] = $str;                $i++;            }            return $pointeurs;        }        else            return false;    }    /* !      @function   lireMsg      @abstract   lecture du message dans la base de donn�s      @discussion (description)      @param      #handle le pointeur du fichier situer au d�but des donn�es      #id		id du message      @result     array("id","contenu","auteur","date")     */    function lireMsg($handle, $id) {        $msg = array();        if (!$pointeurs = trouverPointeurs($id, $handle)) {            echo "Le message (id:$id) n'a pas �t� trouv�";            return NULL;        } else {            foreach ($pointeurs as $key => $offset)                $msg[] = fread($handle, $offset + ($pointeurs[$key + 1] - $offset - strlen($pointeurs[$key + 1]["str"]) - 1));            return $msg;        }    }    /* !      @function   ModifierMsg      @abstract   Mise � jour d'un message      @discussion TODO: am�lioration de la gestion des erreurs, debugging.      @param      #id ID dans la base de donn�es      #contenu      #date Date      #base      @result     true|false      function modifierMsg($id,$contenu,$date,$base) {      $handle = fopen($base,"w+");      $pointeurs = trouverPointeurs($id,$handle);      $msg = lireMsg($handle,$id);      $msg = array($id,$contenu,$msg[2],$date);      fseek($handle,$pointeurs[0]);      foreach($pointeurs as $key=>$offset) {      (fwrite($handle,".\"".addslashes($msg[$key])."\"",$pointeurs[key+1]-strlen($pointeurs[$key+1]["str"])-1 - $pointeurs[$key])) ? continue : echo "erreur d'�criture. Possibilit� de corruption du fichier de stockage.";      }      return true;      }     */    /* !      @function   supprimerMsg      @abstract   Suppression du message      @discussion (description)      @param      #id      #base      @result     true|false      function supprimerMsg($id,$base) {      $handle = fopen($base,'w+');      $pointeurs = trouverPointeurs($id,$handle);      }     */    /* !      @function   formNouveauMsg      @abstract   Formulaire pour ins�rer un nouveau message dans le guest book      @discussion (description)      @param      #base      @result     >HTML>     */    function formNouveauMsg($base, $post) {        $form = new Formulaire("Poster un nouveau message", $post);        $contenu = new ChampAireTexte("contenu", "Contenu du message:", "Les messages doivent se limiter � 160 caract�res.", 80, 20);        $auteur = new ChampTexte("auteur", "Auteur du message:", "", 20);        $base = new ChampCache("base", $base);        $valider = new ChampValider("poster >");        $effacer = new ChampEffacer("vider les champs ^");        $form->ajouterChamp($contenu);        $form->ajouterChamp($auteur);        $form->ajouterChamp($base);        $form->ajouterChamp($valider);        $form->ajouterChamp($effacer);        $form->fin(1);    }    /* !      @function   formModifierMsg      @abstract   Formulaire pour modifier|supprimer le nouveau message      @discussion (description)      @param      #id      #base      @result     void     */    /* !      @function   afficherMsg      @abstract   affichage d'un msg selon son id      @discussion (description)      @param      #id      #base      @result     void     */    function afficherMsg($id, $base) {        $handle = fopen($base, 'r');        $msg = lireMsg($handle, $id);        if ($msg) {            $post = new Info($sql, $result, $msg[0], $msg[2], $msg[1], $msg[3]);            echo getFormated($post->getId());        }        fclose($handle);    }    /* !      @function   afficherGuestBook      @abstract   Affichage du guest book      @discussion (description)      @param      #base      #mode   mode d'affichage, 'a' tous les messages; 'n=x' x messages o� x est le nombre maximum de messages � afficher; 't' les messages  du jour; 'd=x' tous les messages depuis x jours.      #enc encodage d�sir�. jeux de caract�res support�s: UCS"4 , UCS"4BE , UCS"4LE , UCS"2 , UCS"2BE , UCS"2LE , UTF"32 , UTF"32BE ,      UTF"32LE , UCS"2LE , UTF"16 , UTF"16BE , UTF"16LE , UTF"8 , UTF"7 , ASCII , EUC"JP ,      SJIS , eucJP"win , SJIS"win , ISO"2022"JP , JIS , ISO"8859"1 , ISO"8859"2 , ISO"8859"3 ,      ISO"8859"4 , ISO"8859"5 , ISO"8859"6 , ISO"8859"7 , ISO"8859"8 , ISO"8859"9 ,      ISO"8859"10 , ISO"8859"13 , ISO"8859"14 , ISO"8859"15 , byte2be , byte2le , byte4be ,      byte4le , BASE64 , 7bit , 8bit et UTF7"IMAP      @result     code html     */    function afficherGuestBook($base, $mode = "a", $enc = MYENCODING) {        $handle = fopen($base, 'r');        $contents = fread($handle, filesize($base));        $i = 0;        while ($token = strtok($contents, "#id \"")) {            if ($mode == 'a') {                afficherMsg(substr($token, 0, 4), $base);            } elseif (strstr($mode, "n=")) {                $n = substr($mode, 2);                if ($i < $n)                    afficherMsg(substr($token, 0, 4), $base);                $i++;            } elseif (strstr($mode, "d=")) {                $d = substr($mode, 2);                $msg = lireMsg($handle, substr($token, 4));                if ($msg[3] < time())                    afficherMsg(substr($token, 0, 4), $base);            }            elseif ($mode == 't') {                $msg = lireMsg($handle, substr($token, 4));                $today = getdate(time());                $dayofmsg = getdate($msg[3]);                if ($today["mday"] . $today["mon"] . $today["year"] == $dayofmsg["mday"] . $dayofmsg["mon"] . $dayofmsg["year"])                    afficherMsg(substr($token, 0, 4), $base);            }        }    }}?>