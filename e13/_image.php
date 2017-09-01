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
$r = new Registre(filter_input(INPUT_SERVER, 'PHP_SELF'));
require($GLOBALS["include__php_image.class.inc"]);
require($GLOBALS["include__php_captcha.class.inc"]);
require($GLOBALS["include__php_SQL.class.inc"]);

$image = new Image();
$w = filter_input(INPUT_GET, 'w');
$h = filter_input(INPUT_GET, 'h');
$id = filter_input(INPUT_GET, 'id');
$captchaSize = filter_input(INPUT_GET, 'captcha'); // must have been sent through session
if ($id) {
        // connexion SQL
        $sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
        $image->FromSQL($sql, $id);
        $sql->close();
} elseif ($captchaSize) {
        $captchaSize = new Captcha($captchaSize);
        $image = $captchaSize->image($_SESSION['captcha']);
} else {
        trigger_error("image : missing a valid parameter like w,h id or captcha", E_USER_ERROR);
        $image->load_error("ERROR PARAM");
}
if ($w != 0 && $h != 0) {
        $image->setSize($w, $h);
}
$image->raw_http_bytes(1, $GLOBALS["images"]."/".$image->nom);
?>