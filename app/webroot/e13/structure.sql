/*
MySQL Backup
Source Host:           localhost
Source Server Version: 4.1.9-max
Source Database:       i3955959
Date:                  2005.07.24 22:45:15
*/

SET FOREIGN_KEY_CHECKS=0;
# character_set_client=utf8;
# character_set_connection=utf8;
#use pohseE13;
#----------------------------
# Table structure for achat
#----------------------------
drop table if exists achat;
CREATE TABLE `achat` (
  `fk_reference_commande` int(11) NOT NULL default '0',
  `fk_reference_facture` varchar(32) NOT NULL default '',
  `fk_reference_magasin` varchar(4) NOT NULL default '',
  PRIMARY KEY  (`fk_reference_commande`,`fk_reference_facture`,`fk_reference_magasin`)
);
#----------------------------
# No records for table achat
#----------------------------

#----------------------------
# Table structure for categorie
#----------------------------
drop table if exists categorie;
CREATE TABLE `categorie` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(15) NOT NULL default '',
  `parent` int(3) default NULL,
  `image` int(11) default NULL,
  PRIMARY KEY  (`id`,`nom`),
  KEY `parent` (`parent`)
);
#----------------------------
# Table structure for classification
#----------------------------
drop table if exists classification;
CREATE TABLE `classification` (
  `reference_classe` varchar(4) NOT NULL default '',
  `nom` varchar(30) NOT NULL default '',
  `fk_reference_categorie` int(11) NOT NULL default '0',
  PRIMARY KEY  (`reference_classe`)
);
#----------------------------
# No records for table classification
#----------------------------

#----------------------------
# Table structure for client
#----------------------------
drop table if exists client;
CREATE TABLE `client` (
  `identifiant` varchar(20) NOT NULL default '',
  `email` varchar(60) NOT NULL default '',
  `fk_id_mdp` varchar(32) default NULL,
  `nom` varchar(30) NOT NULL default '',
  `prenom` varchar(30) NOT NULL default '',
  `annee_de_naissance` year(4) NOT NULL default '0000',
  `adresse` varchar(30)  default NULL,
  `ville` varchar(20) NOT NULL default '',
  `pays` varchar(20) NOT NULL default '',
  `numero_tel` int(20) default NULL,
  PRIMARY KEY  (`identifiant`),
  UNIQUE KEY `fk_id_mdp` (`fk_id_mdp`),
  UNIQUE KEY `fk_id_mdp_2` (`fk_id_mdp`)
);

#----------------------------
# Table structure for commande
#----------------------------
drop table if exists commande;
CREATE TABLE `commande` (
  `reference` int(11) NOT NULL auto_increment,
  `date_de_commande` date NOT NULL default '0000-00-00',
  `fk_reference_produit` varchar(20) NOT NULL default '',
  `fk_reference_promotion` int(11) NOT NULL default '0',
  PRIMARY KEY  (`reference`),
  KEY `date_de_commande` (`date_de_commande`)
) ;
#----------------------------
# Table structure for compte
#----------------------------
drop table if exists compte;
CREATE TABLE `compte` (
  `fk_identifiant` varchar(20)NOT NULL default '',
  `nb_de_produits_achetes` int(11) default NULL,
  `montant_d_achat_total` decimal(6,1) NOT NULL default '0.0',
  `date_ouverture_du_compte` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`fk_identifiant`),
  KEY `nb_de_produits_achetes` (`nb_de_produits_achetes`,`montant_d_achat_total`,`date_ouverture_du_compte`)
);


#----------------------------
# Table structure for description
#----------------------------
drop table if exists description;
CREATE TABLE `description` (
  `fk_reference_categorie` int(11) NOT NULL default '0',
  `fk_reference_produit` varchar(20) NOT NULL default '',
  `fk_reference_mot_cle` varchar(4)  NOT NULL default '',
  PRIMARY KEY  (`fk_reference_categorie`,`fk_reference_produit`,`fk_reference_mot_cle`)
);
#----------------------------
# Table structure for disponibilite
#----------------------------
drop table if exists disponibilite;
CREATE TABLE `disponibilite` (
  `fk_id_produit` int(5) NOT NULL default '0',
  `fk_reference_exemplaire` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`fk_id_produit`,`fk_reference_exemplaire`)
);

#----------------------------
# Table structure for editeur
#----------------------------
drop table if exists editeur;
CREATE TABLE `editeur` (
  `code_editeur` varchar(4) NOT NULL default '',
  `image` int(5) default NULL,
  `nom` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`code_editeur`)
);
#----------------------------
# Table structure for exemplaire
#----------------------------
drop table if exists exemplaire;
CREATE TABLE `exemplaire` (
  `code_reference` varchar(20) NOT NULL default '',
  `date_de_livraison` date NOT NULL default '0000-00-00',
  `fk_id_produit` int(5) NOT NULL default '0',
  PRIMARY KEY  (`code_reference`)
) ;
#----------------------------
# Table structure for facture
#----------------------------
drop table if exists facture;
CREATE TABLE `facture` (
  `reference` varchar(32) NOT NULL default '',
  `montant_facture` decimal(6,1) default NULL,
  `date_de_facturation` date NOT NULL default '0000-00-00',
  `mode_de_paiement` varchar(4) NOT NULL default '',
  `fk_identifiant` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`reference`),
  KEY `mode_de_paiement` (`mode_de_paiement`,`fk_identifiant`)
);
#----------------------------
# Table structure for fournisseur
#----------------------------
drop table if exists fournisseur;
CREATE TABLE `fournisseur` (
  `code_fournisseur` varchar(4) NOT NULL default '',
  `nom` varchar(30) NOT NULL default '',
  `adresse` varchar(40) NOT NULL default '',
  `numero_tel` int(20) NOT NULL default '0',
  `ville` varchar(15) NOT NULL default '',
  `pays` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`code_fournisseur`),
  KEY `nom` (`nom`,`ville`,`pays`)
) COMMENT='Fournisseurs des produits';

#----------------------------
# Table structure for image
#----------------------------
drop table if exists image;
CREATE TABLE `image` (
  `id` int(11) NOT NULL auto_increment,
  `nom` varchar(250) default NULL,
  `image` longblob NOT NULL,
  `mime` varchar(250) NOT NULL default 'image/png',
  `description` mediumtext,
  PRIMARY KEY  (`id`)
);
#----------------------------
# Table structure for info
#----------------------------
drop table if exists info;
CREATE TABLE `info` (
  `id` int(11) NOT NULL auto_increment,
  `categorie` int(11) default NULL,
  `titre` text NOT NULL,
  `auteur` varchar(250) NOT NULL default '',
  `contenu` longtext NOT NULL,
  `langue` varchar(32) NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  `images` varchar(35) default NULL,
  PRIMARY KEY  (`id`)
);
#----------------------------
# Table structure for message
#----------------------------
drop table if exists message;
CREATE TABLE `message` (
  `id` int(11) NOT NULL auto_increment,
  `titre` varchar(250) NOT NULL default '',
  `texte` longtext NOT NULL,
  `pseudo` varchar(250) NOT NULL default '',
  `date` timestamp NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `date` (`date`)
);
#----------------------------
# Table structure for motdepasse
#----------------------------
drop table if exists motdepasse;
CREATE TABLE `motdepasse` (
  `id_unique` varchar(32) NOT NULL default '',
  `motdepasse` varchar(8) NOT NULL default '',
  PRIMARY KEY  (`id_unique`)
);
#----------------------------
# Table structure for php4u_bookmarks
#----------------------------
drop table if exists php4u_bookmarks;
CREATE TABLE `php4u_bookmarks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `dbase` varchar(128) NOT NULL default '',
  `user` varchar(128) NOT NULL default '',
  `label` varchar(128) NOT NULL default '',
  `query` text NOT NULL,
  PRIMARY KEY  (`id`)
);

