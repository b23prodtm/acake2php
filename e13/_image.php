<?php

/* ! 
  @copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
  @author	www.b23prodtm.infoNA
  @date   :brunorakotoarimanana:20050110
  @filename	_image.php
 */

/* !                                             
  @script	image
  @param	id, size. fourni en _GET ou _POST
  #id: id de l'image de la table SQL "image".
  #size: dimensions de l'image: int(3)[%] ou array(int(4)[px],int(4)[px])
  ou
  #file: urlencode($filename)
 */

include("include/php_registre.inc.php");
new Registre(filter_input(INPUT_SERVER, 'PHP_SELF'));
require($GLOBALS["include__php_image.class.inc"]);
require($GLOBALS["include__php_captcha.class.inc"]);
require($GLOBALS["include__php_SQL.class.inc"]);

$image = new Image();
$w = filter_input(INPUT_GET, 'w');
$h = filter_input(INPUT_GET, 'h');
$id = filter_input(INPUT_GET, 'id');
$f = filter_input(INPUT_GET, 'file');
$motCaptcha = filter_input(INPUT_GET, 'captcha');
if ($id) {
        // connexion SQL
        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
        $image->FromSQL($sql, $id);
        $sql->close();
} elseif ($f) {
        $image->setFile(urldecode(unserialize($f)));
} elseif ($motCaptcha) {
        $captcha = new Captcha(strlen($motCaptcha));
        $image = $captcha->image($motCaptcha);
} else {
        trigger_error("image : missing a valid parameter like w,h id or file or captcha", E_USER_ERROR);
        $image->load_error();
}
if ($w && $h) {
        $image->setSize($w, $h);
        $image->resize();
}
$image->afficher();
?>