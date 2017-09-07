-- MySQL dump 10.13  Distrib 5.6.17, for Win32 (x86)
--
-- Host: eu-cdbr-azure-west-b.cloudapp.net    Database: b23proda79526mvj
-- ------------------------------------------------------
-- Server version	5.5.42-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achat`
--

DROP TABLE IF EXISTS `achat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `achat` (
  `fk_reference_commande` int(11) NOT NULL DEFAULT '0',
  `fk_reference_facture` varchar(32) NOT NULL DEFAULT '',
  `fk_reference_magasin` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`fk_reference_commande`,`fk_reference_facture`,`fk_reference_magasin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorie` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(15) NOT NULL DEFAULT '',
  `parent` int(3) DEFAULT NULL,
  `image` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`nom`),
  KEY `parent` (`parent`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `classification`
--

DROP TABLE IF EXISTS `classification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `classification` (
  `reference_classe` varchar(4) NOT NULL DEFAULT '',
  `nom` varchar(30) NOT NULL DEFAULT '',
  `fk_reference_categorie` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reference_classe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `client`
--

DROP TABLE IF EXISTS `client`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `client` (
  `identifiant` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `fk_id_mdp` varchar(32) DEFAULT NULL,
  `nom` varchar(30) NOT NULL DEFAULT '',
  `prenom` varchar(30) NOT NULL DEFAULT '',
  `annee_de_naissance` year(4) NOT NULL DEFAULT '0000',
  `adresse` varchar(30) DEFAULT NULL,
  `ville` varchar(20) NOT NULL DEFAULT '',
  `pays` varchar(20) NOT NULL DEFAULT '',
  `numero_tel` int(20) DEFAULT NULL,
  PRIMARY KEY (`identifiant`),
  UNIQUE KEY `fk_id_mdp` (`fk_id_mdp`),
  UNIQUE KEY `fk_id_mdp_2` (`fk_id_mdp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `commande`
--

DROP TABLE IF EXISTS `commande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `commande` (
  `reference` int(11) NOT NULL AUTO_INCREMENT,
  `date_de_commande` date NOT NULL DEFAULT '0000-00-00',
  `fk_reference_produit` varchar(20) NOT NULL DEFAULT '',
  `fk_reference_promotion` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`reference`),
  KEY `date_de_commande` (`date_de_commande`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `compte`
--

DROP TABLE IF EXISTS `compte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compte` (
  `fk_identifiant` varchar(20) NOT NULL DEFAULT '',
  `nb_de_produits_achetes` int(11) DEFAULT NULL,
  `montant_d_achat_total` decimal(6,1) NOT NULL DEFAULT '0.0',
  `date_ouverture_du_compte` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`fk_identifiant`),
  KEY `nb_de_produits_achetes` (`nb_de_produits_achetes`,`montant_d_achat_total`,`date_ouverture_du_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `description`
--

DROP TABLE IF EXISTS `description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `description` (
  `fk_reference_categorie` int(11) NOT NULL DEFAULT '0',
  `fk_reference_produit` varchar(20) NOT NULL DEFAULT '',
  `fk_reference_mot_cle` varchar(4) NOT NULL DEFAULT '',
  PRIMARY KEY (`fk_reference_categorie`,`fk_reference_produit`,`fk_reference_mot_cle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `disponibilite`
--

DROP TABLE IF EXISTS `disponibilite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disponibilite` (
  `fk_id_produit` int(5) NOT NULL DEFAULT '0',
  `fk_reference_exemplaire` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`fk_id_produit`,`fk_reference_exemplaire`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `editeur`
--

DROP TABLE IF EXISTS `editeur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `editeur` (
  `code_editeur` varchar(4) NOT NULL DEFAULT '',
  `image` int(5) DEFAULT NULL,
  `nom` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`code_editeur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `exemplaire`
--

DROP TABLE IF EXISTS `exemplaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `exemplaire` (
  `code_reference` varchar(20) NOT NULL DEFAULT '',
  `date_de_livraison` date NOT NULL DEFAULT '0000-00-00',
  `fk_id_produit` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`code_reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `facture`
--

DROP TABLE IF EXISTS `facture`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facture` (
  `reference` varchar(32) NOT NULL DEFAULT '',
  `montant_facture` decimal(6,1) DEFAULT NULL,
  `date_de_facturation` date NOT NULL DEFAULT '0000-00-00',
  `mode_de_paiement` varchar(4) NOT NULL DEFAULT '',
  `fk_identifiant` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`reference`),
  KEY `mode_de_paiement` (`mode_de_paiement`,`fk_identifiant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `fournisseur`
--

DROP TABLE IF EXISTS `fournisseur`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `fournisseur` (
  `code_fournisseur` varchar(4) NOT NULL DEFAULT '',
  `nom` varchar(30) NOT NULL DEFAULT '',
  `adresse` varchar(40) NOT NULL DEFAULT '',
  `numero_tel` int(20) NOT NULL DEFAULT '0',
  `ville` varchar(15) NOT NULL DEFAULT '',
  `pays` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`code_fournisseur`),
  KEY `nom` (`nom`,`ville`,`pays`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Fournisseurs des produits';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(250) DEFAULT NULL,
  `image` longblob NOT NULL,
  `mime` varchar(250) NOT NULL DEFAULT 'image/png',
  `description` mediumtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `info`
--

DROP TABLE IF EXISTS `info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie` int(11) DEFAULT NULL,
  `titre` text NOT NULL,
  `auteur` varchar(250) NOT NULL DEFAULT '',
  `contenu` longtext NOT NULL,
  `langue` varchar(32) NOT NULL DEFAULT '',
  `date` date NOT NULL DEFAULT '0000-00-00',
  `images` varchar(35) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(250) NOT NULL DEFAULT '',
  `texte` longtext NOT NULL,
  `pseudo` varchar(250) NOT NULL DEFAULT '',
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `motdepasse`
--

DROP TABLE IF EXISTS `motdepasse`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `motdepasse` (
  `id_unique` varchar(32) NOT NULL DEFAULT '',
  `motdepasse` varchar(8) NOT NULL DEFAULT '',
  PRIMARY KEY (`id_unique`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `php4u_bookmarks`
--

DROP TABLE IF EXISTS `php4u_bookmarks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `php4u_bookmarks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dbase` varchar(128) NOT NULL DEFAULT '',
  `user` varchar(128) NOT NULL DEFAULT '',
  `label` varchar(128) NOT NULL DEFAULT '',
  `query` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-08-27 18:29:33
