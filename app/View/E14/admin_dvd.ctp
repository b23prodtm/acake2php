<?php
/** TODO : pass cakephp tools */
if (!$i_sitemap) {
	require '../include/php_index.inc.php';
}
$r = new Index(null);
require_once $GLOBALS["include__php_page.class.inc"];
require_once $GLOBALS["include__php_module_html.inc"];
require_once $GLOBALS["include__php_module_DVD.inc"];

$clefPage = "admin__";
$ajouter = $r->sitemap[$clefPage.'ajouter'];
$modifier = $r->sitemap[$clefPage.'modifier'];
if ($pMethod === 'ajouter') {
	if (filter_input(INPUT_GET, 'do')) {
		ajouterDVD(filter_input(INPUT_POST, 'dossier'), filter_input(INPUT_POST, 'nom'), filter_input(INPUT_POST, 'contenu'));
	}
	echo formAjouterDVD($ajouter . "&do", $base);
}
if ($pMethod ===  'modifier') {
	if (filter_input(INPUT_GET, 'do')) {
		if (filter_input(INPUT_POST, 'action') === "modifier") {
			if (filter_input(INPUT_GET, 'exec')) {
				modifierDVD($base, filter_input(INPUT_POST, 'nom'), filter_input(INPUT_POST, 'contenu'), filter_input(INPUT_POST, 'fichier_original'));
			}
			echo formModifierDVD($modifier . "&do&exec", filter_input(INPUT_POST, 'nom'), $base);
		}
		if (filter_input(INPUT_POST, 'action') === "supprimer") {
			supprimerDVD($base . filter_input(INPUT_POST, 'nom') . ".inc");
		}
	} else {
		echo formModiSuppDVD($modifier . "&do", $base);
	}
}

?>