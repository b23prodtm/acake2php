<?php
/** TODO : pass cakephp tools */
if (!$i_sitemap) {
	require '../include/php_index.inc.php';
}
$pageUrl = filter_input(INPUT_SERVER, 'PHP_SELF');
$r = new Index($pageUrl);
require $GLOBALS["include__php_page.class.inc"];
require $GLOBALS["include__php_module_html.inc"];
require $GLOBALS["include__php_module_DVD.inc"];

$clefPage = "library__index";
$post_base = filter_input(INPUT_POST, 'base');
if (!$post_base) {
	$post_base = "data/";
}
 $ajouter = $pageURL . "/ajouter";
$modifier = $pageURL . "/modifier";
if ($pMethod === 'ajouter') {
	if (filter_input(INPUT_GET, 'do')) {
		ajouterDVD(filter_input(INPUT_POST, 'dossier'), filter_input(INPUT_POST, 'nom'), filter_input(INPUT_POST, 'contenu'));
	}
	echo formAjouterDVD($ajouter . "&do", $post_base);
}
if ($pMethod ===  'modifier') {
	if (filter_input(INPUT_GET, 'do')) {
		if (filter_input(INPUT_POST, 'action') === "modifier") {
			if (filter_input(INPUT_GET, 'exec')) {
				modifierDVD($post_base, filter_input(INPUT_POST, 'nom'), filter_input(INPUT_POST, 'contenu'), filter_input(INPUT_POST, 'fichier_original'));
			}
			echo formModifierDVD($modifier . "&do&exec", filter_input(INPUT_POST, 'nom'), $post_base);
		}
		if (filter_input(INPUT_POST, 'action') === "supprimer") {
			supprimerDVD($post_base . filter_input(INPUT_POST, 'nom') . ".inc");
		}
	} else {
		echo formModiSuppDVD($modifier . "&do", $post_base);
	}
}

?>