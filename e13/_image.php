<?php
/*! 
@copyrights b23|prod:www.b23prodtm.info - 2004 (all rights reserved to author)
@author	www.b23prodtm.infoNA
@date   :brunorakotoarimanana:20050110 
@filename	_image.php                                              
*/                                              

/*!                                             
@script	image                                   
@param	id, size. fourni en _GET ou _POST       
#id: id de l'image de la table SQL "image". 
#size: dimensions de l'image: int(3)[%] ou array(int(4)[px],int(4)[px])
ou
#file: urlencode($filename)
*/

include("include/php_registre.inc.php");  
new Registre(filter_input(INPUT_SERVER,'PHP_SELF'));
require($GLOBALS["include__php_image.class.inc"]);
require($GLOBALS["include__php_SQL.class.inc"]);        

$image=new Image();  
if(!filter_input(INPUT_GET,'id')) {
	if(filter_input(INPUT_GET,'file')) {
		if(filter_input(INPUT_GET,'size')) {
			$image->setSize(filter_input(INPUT_GET,'size'));
			$image->resize();
		}
		$image->setFile(urldecode(unserialize(filter_input(INPUT_GET,'file'))));
        } else
		$image->erreurJpeg();                               
	$image->afficher(1);               
}
else {
	// connexion SQL
	$sql = new SQL(SERVEUR,BASE,CLIENT,CLIENT_MDP);              
	$image->FromSQL($sql,filter_input(INPUT_GET,'id'));
	if(filter_input(INPUT_GET,'size')) {
		$image->setSize(filter_input(INPUT_GET,'size'));
		$image->resize();
	}
	else
		$image->setSize();
	
	$image->afficher(1);
	$sql->close();
}
?>