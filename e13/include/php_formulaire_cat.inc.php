<?php
	$f = new Formulaire("Ajouter une categorie",filter_input(INPUT_SERVER,'PHP_SELF')."?ajouter=publie");
	
	$newCat = new ChampTexte("newcat","Nom de la nouvelle cat&eacute;gorie (15 char. max)","Cette cat&eacute;gorie sera r&eacute;pertori&eacute;e dans une base de donn&eacute;e, utile pour les infos et les produits en stock.",20,15);
	
	//	// acqu&eacute;rir les categories existantes SQL
	//	$choix["aucune"] = -1;
	//	
	//	$cats = $sql->query("SELECT * FROM categorie");
	//	while($cat = $sql->LigneSuivante_array($cats)) {
	//		$nom = CAT_getNom($cat,$sql);
	//		$choix[$nom] = $cat['id'];
	//	}
	//	
	//	$newCat_parent = new ChampSelect("newcat_parent","Nom de la cat&eacute;gorie m&eacute;re","Si la nouvelle cat&eacute;gorie doit faire partie d'une cat&eacute;gorie d&eacute;j&eacute; existante, s&eacute;lectionner ici la correspondance",$choix,1,-1);
	$newCat_parent = CAT_getSelect($sql,"newcat_parent","Nom de la cat&eacute;gorie m&eacute;re","Si la nouvelle cat&eacute;gorie doit faire partie d'une cat&eacute;gorie d&eacute;j&eacute; existante, s&eacute;lectionner ici la correspondance");
	
	$effacer = new ChampEffacer("Effacer");
	$valider = new ChampValider("Ajouter","La cat&eacute;gorie sera ajouter &eacute; la base de donn&eacute;e, elle sera utilisable imm&eacute;diatement dans les autres parties du site");
	
	$f->ajouterChamp($newCat);
	$f->ajouterChamp($newCat_parent);
	$f->ajouterChamp($effacer);
	$f->ajouterChamp($valider);
	
	$f->fin(1);
?>