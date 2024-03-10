<?php
/* !
@abstract   Les messages sont stock�s dans une base donn�es, en l'occurence un fichier txt format�.
@discussion (description)
if (!isset($_ENV['Module_Guestbook'])) {
    $_ENV['Module_Guestbook'] = 1;
    ${__FILE__} = new Index();
    include basename(${__FILE__}->r["include__php_info.class.inc"]);
    include basename(${__FILE__}->r["include__php_formulaire.class.inc"]);

/**

  *  @abstract   Enregistrement d'un message dans la base
  * @param string $contenu Contenu du message

  * @param string $date Date du message
  * @param string $base base de donnees, le chemin vers le fichier txt
  * @return int $id du message enregistre
  */

             $handle = fopen($base, 'w+');

             $msg = "#id \"" . addslashes($id) . "\";\n\r" .
             "#contenu \"" . addslashes($contenu) . "\";\n\r" .
             "#auteur \"" . addslashes($auteur) . "\";\n\r" .
             "#date \"" . addslashes($date) . "\";\n\r";
             fwrite($handle, $msg) ? printf("Votre message a �t� ajout� au guest book. id : " . $id) : printf("Erreur lors de l'ajout de votre message au guest book");
}
