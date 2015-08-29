<?php
require("include/php_registre.inc.php");
$r = new Registre(filter_input(INPUT_SERVER,'PHP_SELF'));
require($GLOBALS['include__php_page.class.inc']);
require($GLOBALS['include__php_info.class.inc']);
require($GLOBALS['include__php_SQL.class.inc']);
require($GLOBALS['include__php_constantes.inc']);

$p_infos = new Page($r, 'e13__index', true, true);
$contenu = $p_infos->r->lang("contents", "infos");

// info SQL
$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
$p_infos->ajouterContenu("<H1>".$p_infos->r->lang("info")."</H1>");
$infos = $sql->query("SELECT * FROM info WHERE langue IN ".  Info::GetLanguagesDeArray()." ORDER BY date DESC LIMIT 10");
for ($i = 0; $i < mysqli_num_rows($infos); $i++) {
    $info_SQL = new Info($sql, $infos);    
    $p_infos->ajouterContenu($info_SQL->getFormated($sql));
}
mysqli_free_result($infos);

// info normale
$info = new Info($sql, FALSE, $p_infos->r->lang("message", "infos"), "webmaster", "admin/update", "19/10/2013");
$info->ajouterImage($GLOBALS['images__wip.png'], "Rebuilding...");
$info->ajouterContenu($p_infos->r->lang("visitus", "infos")." : ".HTML_lien($GLOBALS["e13__blog"], $p_infos->r->lang("blog")));

/* // enregistrement dans une var de session, serialize obligatoire unserialize pour le recuperer
  $_SESSION['info'] = serialize($info);
  $p_infos->setInfo($info);
 */

$p_infos->ajouterContenu($contenu . "<BR>" . $info->getFormated($sql));
$sql->close();
$p_infos->fin();
?>