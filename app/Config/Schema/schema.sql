/* database dump file commmand
"/usr/bin/php" "app/Console/cake.php" "schema" "dump" "--write" "schema.sql"
*/

DROP TABLE IF EXISTS `phpcms`.`achat`;
DROP TABLE IF EXISTS `phpcms`.`cake_sessions`;
DROP TABLE IF EXISTS `phpcms`.`categorie`;
DROP TABLE IF EXISTS `phpcms`.`classification`;
DROP TABLE IF EXISTS `phpcms`.`client`;
DROP TABLE IF EXISTS `phpcms`.`commande`;
DROP TABLE IF EXISTS `phpcms`.`compte`;
DROP TABLE IF EXISTS `phpcms`.`description`;
DROP TABLE IF EXISTS `phpcms`.`disponibilite`;
DROP TABLE IF EXISTS `phpcms`.`editeur`;
DROP TABLE IF EXISTS `phpcms`.`exemplaire`;
DROP TABLE IF EXISTS `phpcms`.`facture`;
DROP TABLE IF EXISTS `phpcms`.`fournisseur`;
DROP TABLE IF EXISTS `phpcms`.`image`;
DROP TABLE IF EXISTS `phpcms`.`info`;
DROP TABLE IF EXISTS `phpcms`.`message`;
DROP TABLE IF EXISTS `phpcms`.`motdepasse`;
DROP TABLE IF EXISTS `phpcms`.`php4u_bookmarks`;


CREATE TABLE `phpcms`.`achat` (
	`fk_reference_commande` int(11) DEFAULT 0 NOT NULL,
	`fk_reference_facture` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`fk_reference_magasin` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`fk_reference_commande`, `fk_reference_facture`, `fk_reference_magasin`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`cake_sessions` (
	`id` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`data` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`expires` int(11) DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`categorie` (
	`id` int(11) NOT NULL,
	`nom` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`parent` int(3) DEFAULT NULL,
	`image` int(11) DEFAULT NULL,	PRIMARY KEY  (`id`, `nom`),
	KEY `parent` (`parent`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`classification` (
	`reference_classe` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`nom` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`fk_reference_categorie` int(11) DEFAULT 0 NOT NULL,	PRIMARY KEY  (`reference_classe`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`client` (
	`identifiant` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`email` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`fk_id_mdp` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`nom` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`prenom` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`annee_de_naissance` text(4) DEFAULT '0000' NOT NULL,
	`adresse` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`ville` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`pays` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`numero_tel` int(20) DEFAULT NULL,	PRIMARY KEY  (`identifiant`),
	UNIQUE KEY `fk_id_mdp` (`fk_id_mdp`),
	UNIQUE KEY `fk_id_mdp_2` (`fk_id_mdp`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`commande` (
	`reference` int(11) NOT NULL AUTO_INCREMENT,
	`date_de_commande` date DEFAULT '0000-00-00' NOT NULL,
	`fk_reference_produit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`fk_reference_promotion` int(11) DEFAULT 0 NOT NULL,	PRIMARY KEY  (`reference`),
	KEY `date_de_commande` (`date_de_commande`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`compte` (
	`fk_identifiant` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`nb_de_produits_achetes` int(11) DEFAULT NULL,
	`montant_d_achat_total` decimal(6,1) DEFAULT '0.0' NOT NULL,
	`date_ouverture_du_compte` date DEFAULT '0000-00-00' NOT NULL,	PRIMARY KEY  (`fk_identifiant`),
	KEY `nb_de_produits_achetes` (`nb_de_produits_achetes`, `montant_d_achat_total`, `date_ouverture_du_compte`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`description` (
	`fk_reference_categorie` int(11) DEFAULT 0 NOT NULL,
	`fk_reference_produit` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`fk_reference_mot_cle` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`fk_reference_categorie`, `fk_reference_produit`, `fk_reference_mot_cle`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`disponibilite` (
	`fk_id_produit` int(5) DEFAULT 0 NOT NULL,
	`fk_reference_exemplaire` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`fk_id_produit`, `fk_reference_exemplaire`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`editeur` (
	`code_editeur` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`image` int(5) DEFAULT NULL,
	`nom` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`code_editeur`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`exemplaire` (
	`code_reference` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`date_de_livraison` date DEFAULT '0000-00-00' NOT NULL,
	`fk_id_produit` int(5) DEFAULT 0 NOT NULL,	PRIMARY KEY  (`code_reference`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`facture` (
	`reference` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`montant_facture` decimal(6,1) DEFAULT NULL,
	`date_de_facturation` date DEFAULT '0000-00-00' NOT NULL,
	`mode_de_paiement` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`fk_identifiant` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`reference`),
	KEY `mode_de_paiement` (`mode_de_paiement`, `fk_identifiant`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`fournisseur` (
	`code_fournisseur` varchar(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`nom` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`adresse` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`numero_tel` int(20) DEFAULT 0 NOT NULL,
	`ville` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`pays` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`code_fournisseur`),
	KEY `nom` (`nom`, `ville`, `pays`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB,
	COMMENT='Fournisseurs des produits';

CREATE TABLE `phpcms`.`image` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`nom` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
	`image` blob NOT NULL,
	`mime` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'image/png' NOT NULL,
	`description` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`info` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`categorie` int(11) DEFAULT NULL,
	`titre` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`auteur` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`contenu` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`langue` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`date` date DEFAULT '0000-00-00' NOT NULL,
	`published` date DEFAULT '0000-00-00' NOT NULL,
	`images` varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`message` (
	`id` int(11) NOT NULL AUTO_INCREMENT,
	`titre` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`texte` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`pseudo` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`date` timestamp DEFAULT '0000-00-00 00:00:00' NOT NULL,	PRIMARY KEY  (`id`),
	KEY `date` (`date`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`motdepasse` (
	`id_unique` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`motdepasse` varchar(8) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`id_unique`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

CREATE TABLE `phpcms`.`php4u_bookmarks` (
	`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`dbase` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`user` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`label` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
	`query` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,	PRIMARY KEY  (`id`)) 	DEFAULT CHARSET=utf8,
	COLLATE=utf8_general_ci,
	ENGINE=InnoDB;

