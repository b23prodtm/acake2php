<?php
if (!$i_sitemap) { require_once '../include/php_index.inc.php'; }
$r = new Index($this);

require_once $GLOBALS['include__php_SQL.class.inc'];
require_once $GLOBALS['include__php_constantes.inc'];
require_once $GLOBALS['include__php_module_cat.inc'];

$sql = new SQL(SERVEUR, BASE, CLIENT, CLIENT_MDP);

if ($sql->connect_succes()) {
		$list = array();
		$cats = CAT_getArray($sql, " > ");
		foreach ($cats as $nom => $id) {
				$list[] = $this->Html->link(
					$nom,
					'/cat/'.$id.'/1/10'
				, array('class' => 'titre'))."\n<br><br>";
		}
		echo $this->Html->nestedList($list);
}