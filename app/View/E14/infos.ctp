<?php

$r = new Index(filter_input(INPUT_SERVER, 'PHP_SELF'));
require $GLOBALS['include__php_page.class.inc'];
require $GLOBALS['include__php_info.class.inc'];
require $GLOBALS['include__php_SQL.class.inc'];
require $GLOBALS['include__php_constantes.inc'];
	
$contenu = $r->lang("contents", "infos");

// info SQL
$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);
if ($sql->connect_succes()) {
	/* select de date /1/10/at/YYYY/MM/DD */
	$dateSelect = "";
	/* pagination /np/count/*/
	$count = isset($count) ? $count : 10;
	$np = isset($np) ? $np : 1;
	if (isset($d)) {
		$dateSelect .= " AND published = '" . $d . "' ";
	} else {
		$dateSelect .= " AND published <= CURDATE() ";
	}
	i_debug($contenu . " ". $d);
	/** les posts sont selectionnes en fonction de leur date de publication */
	$infos = $sql->query("SELECT * FROM info WHERE langue IN " . Info::findLangQuery() . "" . $dateSelect . "ORDER BY date DESC LIMIT " . ($np - 1) * $count . "," . $count);
	if ($sql->select_succes($infos)) {
		for ($i = 0; $i < mysqli_num_rows($infos); $i++) {
			$info_SQL = new Info($sql, $infos);
			/** convertit le texte markdown en html */
			$info_SQL->ajouterContenu($this->Markdown->transform($info_SQL->getContenu()));
			echo $info_SQL->getTableauMultiLang($sql);
		}
		mysqli_free_result($infos);
	}

	/* info Test */
	if (i_islocal()) {
		$result = null;
		$info = new Info($sql, $result, $r->lang("message", "infos"), "webmaster", "admin/update", date("Y-m-d"));
		$info->ajouterImage($r->sitemap['images__wip'], "");
		$info->ajouterContenu(
			$this->Html->para("console","Local config enabled : ".SERVEUR.":".PORT."/".BASE.":".CLIENT." identified by ".CLIENT_MDP));
		$info->ajouterContenu($this->Markdown->transform(
		"# Testing Markdown #\n".
		"* star \n".
		"* link [to url](http://www.b23prodtm.info)\n".
		"* _strong title_ *emphasis*\n".
		"	``block of <tags> and (`)&codes;``\n".
		"**************\n".
		"<webmaster@b23prodtm.info>"
		));
		echo "<BR>" . $info->getTableauMultiLang($sql);
	}
	$sql->close();
} else {
	trigger_error("Err code : " . ERROR_DB_CONNECT, E_USER_ERROR);
}