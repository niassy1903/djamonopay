-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: djamonopay
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agents`
--

LOCK TABLES `agents` WRITE;
/*!40000 ALTER TABLE `agents` DISABLE KEYS */;
/*!40000 ALTER TABLE `agents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clients`
--

DROP TABLE IF EXISTS `clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clients`
--

LOCK TABLES `clients` WRITE;
/*!40000 ALTER TABLE `clients` DISABLE KEYS */;
/*!40000 ALTER TABLE `clients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comptes`
--

DROP TABLE IF EXISTS `comptes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comptes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `numero_compte` varchar(32) DEFAULT NULL,
  `solde` decimal(18,2) NOT NULL DEFAULT 0.00,
  `devise` char(3) NOT NULL DEFAULT 'XOF',
  `statut` varchar(20) NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id`),
  UNIQUE KEY `comptes_numero_compte_unique` (`numero_compte`),
  KEY `comptes_user_id_foreign` (`user_id`),
  CONSTRAINT `comptes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users2` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comptes`
--

LOCK TABLES `comptes` WRITE;
/*!40000 ALTER TABLE `comptes` DISABLE KEYS */;
INSERT INTO `comptes` VALUES (29,29,'2026-10-02 22:32:45','2026-10-02 22:32:45','DP0000000029',0.00,'XOF','actif'),(30,30,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000030',0.00,'XOF','actif'),(31,31,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000031',0.00,'XOF','actif'),(32,32,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000032',0.00,'XOF','actif'),(33,33,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000033',0.00,'XOF','actif'),(34,34,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000034',0.00,'XOF','actif'),(35,35,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000035',0.00,'XOF','actif'),(36,36,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000036',0.00,'XOF','actif'),(37,37,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000037',0.00,'XOF','actif'),(38,38,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000038',0.00,'XOF','actif'),(39,39,'2026-10-02 22:32:46','2026-10-03 07:35:06','DP0000000039',10000000.00,'XOF','actif'),(40,40,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000040',0.00,'XOF','actif'),(41,41,'2026-10-02 22:32:46','2026-10-02 22:32:46','DP0000000041',0.00,'XOF','actif'),(42,42,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000042',0.00,'XOF','actif'),(43,43,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000043',0.00,'XOF','actif'),(44,44,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000044',0.00,'XOF','actif'),(45,45,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000045',0.00,'XOF','actif'),(46,46,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000046',0.00,'XOF','actif'),(47,47,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000047',0.00,'XOF','actif'),(48,48,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000048',0.00,'XOF','actif'),(49,49,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000049',0.00,'XOF','actif'),(50,50,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000050',0.00,'XOF','actif'),(51,51,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000051',0.00,'XOF','actif'),(52,52,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000052',0.00,'XOF','actif'),(53,53,'2026-10-02 22:32:47','2026-10-02 22:32:47','DP0000000053',0.00,'XOF','actif'),(54,54,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000054',0.00,'XOF','actif'),(55,55,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000055',0.00,'XOF','actif'),(56,56,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000056',0.00,'XOF','actif'),(57,57,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000057',0.00,'XOF','actif'),(58,58,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000058',0.00,'XOF','actif'),(59,59,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000059',0.00,'XOF','actif'),(60,60,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000060',0.00,'XOF','actif'),(61,61,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000061',0.00,'XOF','actif'),(62,62,'2026-10-02 22:32:48','2026-10-02 22:32:48','DP0000000062',0.00,'XOF','actif'),(63,63,'2026-10-02 22:32:48','2026-10-02 22:36:48','DP0000000063',500000.00,'XOF','actif'),(64,64,'2026-10-02 22:32:48','2026-10-03 07:35:06','DP0000000064',-2000.00,'XOF','actif'),(65,65,'2026-10-02 22:32:48','2026-10-02 23:01:52','DP0000000065',1960.00,'XOF','actif'),(66,66,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000066',0.00,'XOF','actif'),(67,67,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000067',0.00,'XOF','actif'),(68,68,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000068',0.00,'XOF','actif'),(69,69,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000069',0.00,'XOF','actif'),(70,70,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000070',0.00,'XOF','actif'),(71,71,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000071',0.00,'XOF','actif'),(72,72,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000072',0.00,'XOF','actif'),(73,73,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000073',0.00,'XOF','actif'),(74,74,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000074',0.00,'XOF','actif'),(75,75,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000075',0.00,'XOF','actif'),(76,76,'2026-10-02 22:32:49','2026-10-02 22:32:49','DP0000000076',0.00,'XOF','actif'),(77,77,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000077',0.00,'XOF','actif'),(78,78,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000078',0.00,'XOF','actif'),(79,79,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000079',0.00,'XOF','actif'),(80,80,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000080',0.00,'XOF','actif'),(81,81,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000081',0.00,'XOF','actif'),(82,82,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000082',0.00,'XOF','actif'),(83,83,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000083',0.00,'XOF','actif'),(84,84,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000084',0.00,'XOF','actif'),(85,85,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000085',0.00,'XOF','actif'),(86,86,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000086',0.00,'XOF','actif'),(87,87,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000087',0.00,'XOF','actif'),(88,88,'2026-10-02 22:32:50','2026-10-02 22:32:50','DP0000000088',0.00,'XOF','actif'),(89,89,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000089',0.00,'XOF','actif'),(90,90,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000090',0.00,'XOF','actif'),(91,91,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000091',0.00,'XOF','actif'),(92,92,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000092',0.00,'XOF','actif'),(93,93,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000093',0.00,'XOF','actif'),(94,94,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000094',0.00,'XOF','actif'),(95,95,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000095',0.00,'XOF','actif'),(96,96,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000096',0.00,'XOF','actif'),(97,97,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000097',0.00,'XOF','actif'),(98,98,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000098',0.00,'XOF','actif'),(99,99,'2026-10-02 22:32:51','2026-10-02 22:32:51','DP0000000099',0.00,'XOF','actif'),(100,100,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000100',0.00,'XOF','actif'),(101,101,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000101',0.00,'XOF','actif'),(102,102,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000102',0.00,'XOF','actif'),(103,103,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000103',0.00,'XOF','actif'),(104,104,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000104',0.00,'XOF','actif'),(105,105,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000105',0.00,'XOF','actif'),(106,106,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000106',0.00,'XOF','actif'),(107,107,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000107',0.00,'XOF','actif'),(108,108,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000108',0.00,'XOF','actif'),(109,109,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000109',0.00,'XOF','actif'),(110,110,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000110',0.00,'XOF','actif'),(111,111,'2026-10-02 22:32:52','2026-10-02 22:32:52','DP0000000111',0.00,'XOF','actif'),(112,112,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000112',0.00,'XOF','actif'),(113,113,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000113',0.00,'XOF','actif'),(114,114,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000114',0.00,'XOF','actif'),(115,115,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000115',0.00,'XOF','actif'),(116,116,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000116',0.00,'XOF','actif'),(117,117,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000117',0.00,'XOF','actif'),(118,118,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000118',0.00,'XOF','actif'),(119,119,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000119',0.00,'XOF','actif'),(120,120,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000120',0.00,'XOF','actif'),(121,121,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000121',0.00,'XOF','actif'),(122,122,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000122',0.00,'XOF','actif'),(123,123,'2026-10-02 22:32:53','2026-10-02 22:32:53','DP0000000123',0.00,'XOF','actif'),(124,124,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000124',0.00,'XOF','actif'),(125,125,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000125',0.00,'XOF','actif'),(126,126,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000126',0.00,'XOF','actif'),(127,127,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000127',0.00,'XOF','actif'),(128,128,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000128',0.00,'XOF','actif'),(129,129,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000129',0.00,'XOF','actif'),(130,130,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000130',0.00,'XOF','actif'),(131,131,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000131',0.00,'XOF','actif'),(132,132,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000132',0.00,'XOF','actif'),(133,133,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000133',0.00,'XOF','actif'),(134,134,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000134',0.00,'XOF','actif'),(135,135,'2026-10-02 22:32:54','2026-10-02 22:32:54','DP0000000135',0.00,'XOF','actif'),(136,136,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000136',0.00,'XOF','actif'),(137,137,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000137',0.00,'XOF','actif'),(138,138,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000138',0.00,'XOF','actif'),(139,139,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000139',0.00,'XOF','actif'),(140,140,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000140',0.00,'XOF','actif'),(141,141,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000141',0.00,'XOF','actif'),(142,142,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000142',0.00,'XOF','actif'),(143,143,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000143',0.00,'XOF','actif'),(144,144,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000144',0.00,'XOF','actif'),(145,145,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000145',0.00,'XOF','actif'),(146,146,'2026-10-02 22:32:55','2026-10-02 22:32:55','DP0000000146',0.00,'XOF','actif'),(147,147,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000147',0.00,'XOF','actif'),(148,148,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000148',0.00,'XOF','actif'),(149,149,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000149',0.00,'XOF','actif'),(150,150,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000150',0.00,'XOF','actif'),(151,151,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000151',0.00,'XOF','actif'),(152,152,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000152',0.00,'XOF','actif'),(153,153,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000153',0.00,'XOF','actif'),(154,154,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000154',0.00,'XOF','actif'),(155,155,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000155',0.00,'XOF','actif'),(156,156,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000156',0.00,'XOF','actif'),(157,157,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000157',0.00,'XOF','actif'),(158,158,'2026-10-02 22:32:56','2026-10-02 22:32:56','DP0000000158',0.00,'XOF','actif'),(159,159,'2026-10-02 22:32:57','2026-10-02 22:32:57','DP0000000159',0.00,'XOF','actif'),(160,160,'2026-10-02 22:32:57','2026-10-02 22:32:57','DP0000000160',0.00,'XOF','actif'),(161,161,'2026-10-02 22:32:57','2026-10-02 22:32:57','DP0000000161',0.00,'XOF','actif'),(162,162,'2026-10-02 22:32:57','2026-10-02 22:32:57','DP0000000162',0.00,'XOF','actif'),(163,163,'2026-10-02 22:32:57','2026-10-02 22:32:57','DP0000000163',0.00,'XOF','actif'),(164,164,'2026-10-02 22:44:44','2026-10-02 22:44:44','DP0000000164',0.00,'XOF','actif'),(165,165,'2026-10-02 22:53:11','2026-10-02 22:53:11','DP0000000165',0.00,'XOF','actif'),(166,166,'2026-10-03 08:19:05','2026-10-03 08:19:32','DP0000000166',10000000.00,'XOF','actif');
/*!40000 ALTER TABLE `comptes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `distributeurs`
--

DROP TABLE IF EXISTS `distributeurs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distributeurs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `distributeurs`
--

LOCK TABLES `distributeurs` WRITE;
/*!40000 ALTER TABLE `distributeurs` DISABLE KEYS */;
/*!40000 ALTER TABLE `distributeurs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2014_10_12_200000_add_two_factor_columns_to_users_table',1),(4,'2019_08_19_000000_create_failed_jobs_table',1),(5,'2019_12_14_000001_create_personal_access_tokens_table',1),(6,'2024_10_23_093843_create_sessions_table',1),(7,'2024_10_23_102027_create_agents_table',1),(8,'2024_10_23_102043_create_distributeurs_table',1),(9,'2024_10_23_102101_create_clients_table',1),(10,'2024_10_23_102107_create_comptes_table',1),(11,'2024_10_23_102118_create_transactions_table',1),(12,'2024_10_23_102141_create_system_loggers_table',1),(13,'2024_10_23_104827_create_users2_table',1),(14,'2026_10_02_000000_add_remember_token_to_users2_table',1),(15,'2026_10_02_000001_add_dashboard_data_to_financial_tables',1),(16,'2026_10_02_000002_add_transaction_idempotency_key',1),(17,'2026_10_02_000003_add_transaction_actors_and_reversals',1),(18,'2026_10_03_000000_create_notifications_table',2),(19,'2026_10_03_000001_create_transaction_cancellation_requests_table',2);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
INSERT INTO `password_resets` VALUES ('niassy.lamine10@gmail.com','$2y$10$tmV0snp1pfY2P52DE3tPB.Jt3KdFBhaznAYy/xslvIAWEQ4nuGod2','2026-10-02 23:03:06');
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('waYaLeubse0j60yMb21mno77d8wGqcy0HXP416FH',29,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV0loVXJiVm96c1dhNHdtOXVETVRHWENJOG1lSXFwMVRmVDhxaEFNbyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQvZGFzaGJvYXJkLXV0aWxpc2F0ZXVycyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI5O30=',1791199378),('XKIDDlgnzuhTY2PuqizjKFpvOQoZ4Zjd27cvr5dJ',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSXY1d1REV2VTck9Yb1N4bE5LQVVoRDI3eDNtaXlXbG9PZjVQNmFjOCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO319',1791015882),('zE0bjekSxhWaHKWayvJfbsmRLyl2u1r32HxspiEs',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.140.0 Chrome/150.0.7871.250 Electron/43.7.3 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiNXQ3NzZNMjV1RG5lZWNSNWtFVVRxaW5aRHZ6c2I3VFFOMG8xMzc3cyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1791015335);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_loggers`
--

DROP TABLE IF EXISTS `system_loggers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_loggers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `adresse_ip` varchar(45) DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `system_loggers_user_id_foreign` (`user_id`),
  CONSTRAINT `system_loggers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users2` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_loggers`
--

LOCK TABLES `system_loggers` WRITE;
/*!40000 ALTER TABLE `system_loggers` DISABLE KEYS */;
INSERT INTO `system_loggers` VALUES (4,'2026-10-02 22:36:48','2026-10-02 22:36:48',29,'wallet.distributor.credited','Crédit de 500000 XOF au distributeur DP0000000063.','127.0.0.1','App\\Models\\Transaction',3),(5,'2026-10-02 22:53:11','2026-10-02 22:53:11',29,'user.created','Création du compte agent niassy.lamine11@gmail.com.','127.0.0.1','App\\Models\\Users2',165),(6,'2026-10-02 22:56:49','2026-10-02 22:56:49',29,'wallet.distributor.credited','Crédit de 10000000 XOF au distributeur DP0000000039.','127.0.0.1','App\\Models\\Transaction',4),(7,'2026-10-02 22:59:17','2026-10-02 22:59:17',39,'wallet.deposit.completed','Dépôt de 10000 XOF; bonus distributeur de 100 XOF.','127.0.0.1','App\\Models\\Transaction',5),(8,'2026-10-02 23:01:52','2026-10-02 23:01:52',64,'wallet.payment.completed','Transfert de 2000 XOF, frais de 40 XOF vers DP0000000065.','127.0.0.1','App\\Models\\Transaction',7),(9,'2026-10-03 07:35:06','2026-10-03 07:35:06',39,'wallet.transaction.reversed','Transaction DEP-01M3ZDFSYSGE34HWC3N1WVSCE5 annulée; solde négatif autorisé conformément à la politique.','127.0.0.1','App\\Models\\Transaction',5),(10,'2026-10-03 08:19:05','2026-10-03 08:19:05',29,'user.created','Création du compte distributeur niassy.lamine12@gmail.com.','127.0.0.1','App\\Models\\Users2',166),(11,'2026-10-03 08:19:32','2026-10-03 08:19:32',29,'wallet.distributor.credited','Crédit de 10000000 XOF au distributeur DP0000000166.','127.0.0.1','App\\Models\\Transaction',10);
/*!40000 ALTER TABLE `system_loggers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaction_cancellation_requests`
--

DROP TABLE IF EXISTS `transaction_cancellation_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transaction_cancellation_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` bigint(20) unsigned NOT NULL,
  `requester_id` bigint(20) unsigned NOT NULL,
  `verified_reference` varchar(255) NOT NULL,
  `verified_amount` decimal(15,2) NOT NULL,
  `verified_phone` varchar(30) NOT NULL,
  `reason` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `reviewed_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `review_note` text DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transaction_cancellation_requests_transaction_id_foreign` (`transaction_id`),
  KEY `transaction_cancellation_requests_requester_id_foreign` (`requester_id`),
  KEY `transaction_cancellation_requests_reviewed_by_user_id_foreign` (`reviewed_by_user_id`),
  KEY `transaction_cancellation_requests_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `transaction_cancellation_requests_requester_id_foreign` FOREIGN KEY (`requester_id`) REFERENCES `users2` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaction_cancellation_requests_reviewed_by_user_id_foreign` FOREIGN KEY (`reviewed_by_user_id`) REFERENCES `users2` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transaction_cancellation_requests_transaction_id_foreign` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaction_cancellation_requests`
--

LOCK TABLES `transaction_cancellation_requests` WRITE;
/*!40000 ALTER TABLE `transaction_cancellation_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `transaction_cancellation_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transactions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `reference` varchar(40) DEFAULT NULL,
  `compte_source_id` bigint(20) unsigned DEFAULT NULL,
  `compte_destination_id` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(24) NOT NULL DEFAULT 'transfert',
  `montant` decimal(18,2) NOT NULL DEFAULT 0.00,
  `frais` decimal(18,2) NOT NULL DEFAULT 0.00,
  `devise` char(3) NOT NULL DEFAULT 'XOF',
  `statut` varchar(24) NOT NULL DEFAULT 'en_attente',
  `description` text DEFAULT NULL,
  `traitee_at` timestamp NULL DEFAULT NULL,
  `idempotency_key` char(36) DEFAULT NULL,
  `actor_id` bigint(20) unsigned DEFAULT NULL,
  `processed_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `cancelled_by_user_id` bigint(20) unsigned DEFAULT NULL,
  `parent_transaction_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transactions_reference_unique` (`reference`),
  UNIQUE KEY `transactions_idempotency_key_unique` (`idempotency_key`),
  KEY `transactions_compte_source_id_foreign` (`compte_source_id`),
  KEY `transactions_compte_destination_id_foreign` (`compte_destination_id`),
  KEY `transactions_actor_id_foreign` (`actor_id`),
  KEY `transactions_processed_by_user_id_foreign` (`processed_by_user_id`),
  KEY `transactions_cancelled_by_user_id_foreign` (`cancelled_by_user_id`),
  KEY `transactions_parent_transaction_id_foreign` (`parent_transaction_id`),
  KEY `transactions_type_statut_compte_source_id_index` (`type`,`statut`,`compte_source_id`),
  KEY `transactions_type_statut_compte_destination_id_index` (`type`,`statut`,`compte_destination_id`),
  CONSTRAINT `transactions_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `users2` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_cancelled_by_user_id_foreign` FOREIGN KEY (`cancelled_by_user_id`) REFERENCES `users2` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_compte_destination_id_foreign` FOREIGN KEY (`compte_destination_id`) REFERENCES `comptes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_compte_source_id_foreign` FOREIGN KEY (`compte_source_id`) REFERENCES `comptes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_parent_transaction_id_foreign` FOREIGN KEY (`parent_transaction_id`) REFERENCES `transactions` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_processed_by_user_id_foreign` FOREIGN KEY (`processed_by_user_id`) REFERENCES `users2` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (3,'2026-10-02 22:36:48','2026-10-02 22:36:48','AGT-01M3ZC6M5P3BQZ4P0TKF3WM3XW',NULL,63,'credit_agent',500000.00,0.00,'XOF','terminee','Crédit de trésorerie agent au distributeur.','2026-10-02 22:36:48','bcbe6b6e-4bef-4de7-a1f9-99db1e1b4427',29,29,NULL,NULL),(4,'2026-10-02 22:56:49','2026-10-02 22:56:49','AGT-01M3ZDB9KY8WCHK6C0R5J2WED5',NULL,39,'credit_agent',10000000.00,0.00,'XOF','terminee','Crédit de trésorerie agent au distributeur.','2026-10-02 22:56:49','bbab60c0-81d2-4466-bc95-989058a28472',29,29,NULL,NULL),(5,'2026-10-02 22:59:17','2026-10-03 07:35:06','DEP-01M3ZDFSYSGE34HWC3N1WVSCE5',39,64,'depot',10000.00,0.00,'XOF','annulee','Dépôt espèces effectué par le distributeur.','2026-10-02 22:59:17','c008e178-3456-4e9b-ba98-c2ab0c35baf3',39,NULL,39,NULL),(6,'2026-10-02 22:59:17','2026-10-03 07:35:06','BON-01M3ZDFSYTK94BCM814M2PKT5E',NULL,39,'bonus_distributeur',100.00,0.00,'XOF','annulee','Bonus de 1 % pour la transaction DEP-01M3ZDFSYSGE34HWC3N1WVSCE5.','2026-10-02 22:59:17',NULL,39,39,39,5),(7,'2026-10-02 23:01:52','2026-10-02 23:01:52','PAY-01M3ZDMGWBGG60PKZE51Q9Z6R0',64,65,'paiement_qr',2000.00,40.00,'XOF','terminee','Transfert client par QR code ou numéro de compte; frais de 2 % déduits du montant reçu.','2026-10-02 23:01:52','58a11867-e724-4484-9d28-8182b9440a97',64,NULL,NULL,NULL),(8,'2026-10-03 07:35:06','2026-10-03 07:35:06','REV-01M40B09YJTMWYA3AD7MMYE33R',64,39,'remboursement_annulation',10000.00,0.00,'XOF','terminee','Annulation de DEP-01M3ZDFSYSGE34HWC3N1WVSCE5; les soldes sont restaurés.','2026-10-03 07:35:06',NULL,39,NULL,NULL,5),(9,'2026-10-03 07:35:06','2026-10-03 07:35:06','REV-01M40B09YS9RF3SA8V1XA9Y9BY',39,NULL,'annulation_bonus',100.00,0.00,'XOF','terminee','Reprise du bonus lié à DEP-01M3ZDFSYSGE34HWC3N1WVSCE5','2026-10-03 07:35:06',NULL,39,NULL,NULL,6),(10,'2026-10-03 08:19:32','2026-10-03 08:19:32','AGT-01M40DHMJX3HJQTD80YNNDW1Q1',NULL,166,'credit_agent',10000000.00,0.00,'XOF','terminee','Crédit de trésorerie agent au distributeur.','2026-10-03 08:19:32','8ceafc4a-e299-489a-8d2f-eae7d4673436',29,29,NULL,NULL);
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `current_team_id` bigint(20) unsigned DEFAULT NULL,
  `profile_photo_path` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users2`
--

DROP TABLE IF EXISTS `users2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users2` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('client','distributeur','agent') NOT NULL,
  `telephone` varchar(255) NOT NULL,
  `adresse` varchar(255) NOT NULL,
  `date_naissance` date NOT NULL,
  `numero_identite` varchar(255) NOT NULL,
  `etat_compte` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users2_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users2`
--

LOCK TABLES `users2` WRITE;
/*!40000 ALTER TABLE `users2` DISABLE KEYS */;
INSERT INTO `users2` VALUES (29,'Green','Lynn','agent@djamonopay.test',NULL,'$2y$10$p40w4KwbBQP8cscmAS.nhenOolgFtrU615wda4kSI3lcZBkJDRi.K','agent','771716028','New Dave, Sénégal','1979-05-12','DEMO-AGENT-001',1,'2026-10-02 22:32:45','2026-10-02 22:32:45',NULL),(30,'Doyle','Camron','agent.002@djamonopay.test',NULL,'$2y$10$3zMpNPHvFnDuDTreaGiId.GuQBJHRQDgvtioX/ECo8SIvCFgNgAsu','agent','775024396','Lake Einar, Sénégal','1985-02-16','DEMO-AGENT-002',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(31,'Herzog','Elouise','agent.003@djamonopay.test',NULL,'$2y$10$4WFxEw5Dfztad/uTmU4ks.0Gy1gl25RsSLjj8qQ2V52XetADob2Au','agent','776306785','North Chanelle, Sénégal','1992-07-16','DEMO-AGENT-003',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(32,'Jast','Sunny','agent.004@djamonopay.test',NULL,'$2y$10$x0yIO5TKX9T1Mz.v3rnNbuNS8PmzWjumJCcc7UKlezX1stIhr0tYO','agent','771295316','Purdyland, Sénégal','2005-09-30','DEMO-AGENT-004',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(33,'Christiansen','Mozell','agent.005@djamonopay.test',NULL,'$2y$10$fzObRTLhP7hPd9aIjlrMjeR9MPA/tBts2aZYIjdiUZbWiH76.4bu.','agent','778898295','West Karolannshire, Sénégal','1990-10-03','DEMO-AGENT-005',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(34,'Graham','Lesley','agent.006@djamonopay.test',NULL,'$2y$10$O7vmk6SkBd6OQWeBWQJgXuaMWAb7YgLzYrQnTpNMSZlSMZaysI3QC','agent','770688486','West Luther, Sénégal','1980-07-31','DEMO-AGENT-006',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(35,'Rau','Derick','agent.007@djamonopay.test',NULL,'$2y$10$Aq2Dnd3xbAB1lN4qjCz6Vewd7wgLCM57Gnyk.NbkrU311A/D6DBDm','agent','779598123','Wymanton, Sénégal','1994-08-04','DEMO-AGENT-007',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(36,'Graham','Gino','agent.008@djamonopay.test',NULL,'$2y$10$KarQtRJpD.IDl9QVUw94e.LTg2x.YdMKgJuJ9Y3vfWQ/vSACb1qDu','agent','773704648','North Makennafurt, Sénégal','1993-12-16','DEMO-AGENT-008',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(37,'Hammes','Frederik','agent.009@djamonopay.test',NULL,'$2y$10$PAQz8HD6e1cvPRv.TZuV/.7ttIG0dLlrEGW.jo/Brob9CImOZPBSy','agent','772765434','Klockofurt, Sénégal','1973-07-09','DEMO-AGENT-009',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(38,'Weimann','Elliott','agent.010@djamonopay.test',NULL,'$2y$10$poRQC/eYlPK1/z/wDupp6O2dkU7ho9QlmpvBxOITThJq6pgbALX9O','agent','774142695','Volkmanborough, Sénégal','1964-04-22','DEMO-AGENT-010',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(39,'Schaden','Anissa','distributeur.001@djamonopay.test',NULL,'$2y$10$JGbXEpYzKHr/9tPVRJFpxe/Nu69OKkIh42VVmYbtGDB.PcqKZPNji','distributeur','773821216','Lydamouth, Sénégal','1973-05-25','DEMO-DISTRIBUTEUR-001',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(40,'Jenkins','Jackson','distributeur.002@djamonopay.test',NULL,'$2y$10$Xndj/pRObCwgKNli/zX08O8UJykmh8r7uIVQ7NiziyQxM4QOr0R.6','distributeur','777753029','Lake Ramona, Sénégal','1986-02-23','DEMO-DISTRIBUTEUR-002',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(41,'Koelpin','Scarlett','distributeur.003@djamonopay.test',NULL,'$2y$10$UlS6wLijkpYWEGtrv/4fyutKar9PpFEQ7AhjBDnWf29H0grSN5W6a','distributeur','776439030','New Joyce, Sénégal','1986-12-20','DEMO-DISTRIBUTEUR-003',1,'2026-10-02 22:32:46','2026-10-02 22:32:46',NULL),(42,'Oberbrunner','Eriberto','distributeur.004@djamonopay.test',NULL,'$2y$10$GTpX.WQDcmWzvngKf7zJfeIP51O85E4b6/bM5sU5ZnYml9UphQ0wC','distributeur','770911771','New Abbie, Sénégal','2005-03-31','DEMO-DISTRIBUTEUR-004',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(43,'Tillman','Guillermo','distributeur.005@djamonopay.test',NULL,'$2y$10$ijfY8K7GhgSlVhhtBLZNUuUx/.eTZqSaGk2f00yGxXvcDWdwk0VUS','distributeur','772987621','Lake Carroll, Sénégal','1982-01-01','DEMO-DISTRIBUTEUR-005',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(44,'Labadie','Wilton','distributeur.006@djamonopay.test',NULL,'$2y$10$uD9Uy8bRZPfncYc9nMI4LegCkVIfgOPhDRQvlDt9Zz3NgwGJlzPW6','distributeur','771414912','Yostville, Sénégal','1997-07-20','DEMO-DISTRIBUTEUR-006',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(45,'Gleichner','Susan','distributeur.007@djamonopay.test',NULL,'$2y$10$AUycCxn9M1mI6IpAD9pCLulQUSrQt6fWngQU.obqDmbFKqVQIkctO','distributeur','778574077','West Christinebury, Sénégal','1969-12-08','DEMO-DISTRIBUTEUR-007',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(46,'Daniel','Carmelo','distributeur.008@djamonopay.test',NULL,'$2y$10$otTlwGzlVC0QSnhuoU9Etu7Rxy.UWs4U/JjJdcqgLvWUcpLn4WMfS','distributeur','776025210','Rodstad, Sénégal','2002-08-09','DEMO-DISTRIBUTEUR-008',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(47,'Champlin','Matteo','distributeur.009@djamonopay.test',NULL,'$2y$10$7FWmJrz3xecNlapmO8BIgexSUgYEEPhwD5u4epFg19AHHsDagmJKW','distributeur','778220786','Port Kane, Sénégal','1999-10-13','DEMO-DISTRIBUTEUR-009',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(48,'Schultz','Abdiel','distributeur.010@djamonopay.test',NULL,'$2y$10$EIenuCHSjxrMfcVaSFggZOoLafmjREcqOsbRL9UqivEoA.sHDRwbO','distributeur','778308055','McCulloughborough, Sénégal','1976-05-12','DEMO-DISTRIBUTEUR-010',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(49,'Glover','Matilda','distributeur.011@djamonopay.test',NULL,'$2y$10$TvtXMySppOPwI0tAKuRTKe7DAWAyhxn/U3eZ7GTAVsT10qAk5b7iK','distributeur','776806471','Skilestown, Sénégal','1993-11-08','DEMO-DISTRIBUTEUR-011',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(50,'Donnelly','Abigale','distributeur.012@djamonopay.test',NULL,'$2y$10$bHK/XG5UrkwkW7q791h29eqQ/tj/AMutRzgr/0t7CWTqFqn/WX6Hi','distributeur','772183938','Lake Sabrynaview, Sénégal','1974-06-24','DEMO-DISTRIBUTEUR-012',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(51,'Cormier','Jayce','distributeur.013@djamonopay.test',NULL,'$2y$10$WludcCRAPLyAjHNH76DAY.g1ou3NGTB8lxF7ZElP6jSp.GAbiQ3TC','distributeur','775848296','North Edton, Sénégal','1980-01-26','DEMO-DISTRIBUTEUR-013',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(52,'Robel','Liliane','distributeur.014@djamonopay.test',NULL,'$2y$10$c7YU3dTib8E2.gd0IYXnPOwWIbqw/MSEpjwrSsBHJ87F3b7oNkxLi','distributeur','778792709','Gibsonchester, Sénégal','1971-12-23','DEMO-DISTRIBUTEUR-014',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(53,'Lind','Berenice','distributeur.015@djamonopay.test',NULL,'$2y$10$W.Guz45M4CmhiSiTjNC21O9AO3XKQsC6POSO04waKKZK777xcJgP6','distributeur','772062111','Haskellport, Sénégal','1985-02-05','DEMO-DISTRIBUTEUR-015',1,'2026-10-02 22:32:47','2026-10-02 22:32:47',NULL),(54,'Feest','Lexus','distributeur.016@djamonopay.test',NULL,'$2y$10$N5jNZrdNxRTP6fKdi80WyuBDRstMhvNe/kZcQlQ5nsrgh3TyWaIli','distributeur','773823082','Lavontown, Sénégal','1998-05-24','DEMO-DISTRIBUTEUR-016',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(55,'Hills','Myrtie','distributeur.017@djamonopay.test',NULL,'$2y$10$8O5GCaTHkxhDeg4aPb8xrOCGKBRqs8QLfK3HtvUkle76kJknENI..','distributeur','773936046','Ricardostad, Sénégal','1991-05-09','DEMO-DISTRIBUTEUR-017',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(56,'Kling','Chanel','distributeur.018@djamonopay.test',NULL,'$2y$10$7ynX30wi7CbbawZ2wYBHC.PuBSmWSpV72A9ZMl3X.oDUcTMmGgg0G','distributeur','777803751','East Moses, Sénégal','1994-08-31','DEMO-DISTRIBUTEUR-018',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(57,'Corwin','Gabrielle','distributeur.019@djamonopay.test',NULL,'$2y$10$4dhlx7xRMUfllzhFX2iJIu9IZzaB30azaru/5.ourK8oQxEoOaok.','distributeur','777052628','Romagueratown, Sénégal','1967-03-12','DEMO-DISTRIBUTEUR-019',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(58,'Pacocha','Raegan','distributeur.020@djamonopay.test',NULL,'$2y$10$3Tz59cecZ8cT8hZ.wn6uTeWyqOMvRkTYV.0ztxSowfgZSXYdfuZuu','distributeur','775275235','McKenzieton, Sénégal','1982-03-04','DEMO-DISTRIBUTEUR-020',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(59,'Nikolaus','April','distributeur.021@djamonopay.test',NULL,'$2y$10$MwWVbYveCdckeOnREeaO1esKM8B2xIhcCBbXFI7x1L/Ngl7wRfqWS','distributeur','777374964','East Brant, Sénégal','2004-12-25','DEMO-DISTRIBUTEUR-021',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(60,'Orn','Rudolph','distributeur.022@djamonopay.test',NULL,'$2y$10$crIeI6/EdCCpi8iAZnDCVeGlGE7Y4jUrTmi1QzlTDTVJDbE0KK71K','distributeur','777733015','South Payton, Sénégal','1981-10-07','DEMO-DISTRIBUTEUR-022',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(61,'Nader','Rhiannon','distributeur.023@djamonopay.test',NULL,'$2y$10$vP1LTeDWGDQqiMHynYQ2KOCPwfIJSc6AqCJZf4R7Q23V0bOzHjVCK','distributeur','779310602','Morarfurt, Sénégal','1988-10-11','DEMO-DISTRIBUTEUR-023',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(62,'Dooley','Elliot','distributeur.024@djamonopay.test',NULL,'$2y$10$gDIx9YrE8neMQsZHu.7qYuuO3BAzsp17SjgsfwwBGXHSjH0wekXbC','distributeur','770648811','Celiaport, Sénégal','1998-12-12','DEMO-DISTRIBUTEUR-024',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(63,'Armstrong','Clotilde','distributeur.025@djamonopay.test',NULL,'$2y$10$4AcppWpoKtRM7JGNKB38U.t2rxcxvcwblsluHNkurHgguknO4.iXy','distributeur','778795622','North Justen, Sénégal','1971-02-24','DEMO-DISTRIBUTEUR-025',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(64,'Wiegand','Ara','client.001@djamonopay.test',NULL,'$2y$10$S5Nmg.DznFxSkxdKSr2QGeoEOnDD7H1met7UF9yTsiL.Yyu.1bciK','client','770137353','New Leatha, Sénégal','1962-05-11','DEMO-CLIENT-001',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(65,'Mertz','Dave','client.002@djamonopay.test',NULL,'$2y$10$MSqhmtZ32tVf2bEPJOOLCeTAvW3DF0.BghDz1p93eVG5BoX4lC4Ia','client','774568293','Ebertborough, Sénégal','1975-12-27','DEMO-CLIENT-002',1,'2026-10-02 22:32:48','2026-10-02 22:32:48',NULL),(66,'Cassin','Paris','client.003@djamonopay.test',NULL,'$2y$10$HkrlSQNlyH153TL8Nwakv.AWa/6BhGSfDbGsGRRIl8B6nP54IZgja','client','774867283','Stefanieport, Sénégal','1983-12-31','DEMO-CLIENT-003',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(67,'Deckow','Maryam','client.004@djamonopay.test',NULL,'$2y$10$YYgLLzbEQPflCMmm1kSoNOwt7M430a0oKx5saQSENh1soNn0N1dxm','client','775743650','West Julie, Sénégal','1971-07-03','DEMO-CLIENT-004',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(68,'Price','Akeem','client.005@djamonopay.test',NULL,'$2y$10$5XJ8FT3RzgzNXuOWYA8nsuDBIOWTdLHecEenJSCaSdQmYC0b1uW6G','client','776727059','North Alexandro, Sénégal','2002-07-20','DEMO-CLIENT-005',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(69,'Kirlin','Saul','client.006@djamonopay.test',NULL,'$2y$10$4ByOr2UPBXv0NJYtD0ywK.DYh1aUKN/snwIWCJV6oINeZgfwgWXhi','client','777796650','Suzanneport, Sénégal','1994-01-25','DEMO-CLIENT-006',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(70,'Kozey','Alexandra','client.007@djamonopay.test',NULL,'$2y$10$y/eZkCHmmF0XqB.XMZUwF.XCjC7FJvWcVTP5HdhZkl85ZLzVpt.Oi','client','773413091','East Marianeview, Sénégal','2002-06-24','DEMO-CLIENT-007',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(71,'Reynolds','Sydni','client.008@djamonopay.test',NULL,'$2y$10$UAUdWj.aBVApjit9OHb4leJ9HaekAdozXSll.tA1fCZ4WEU/Sav76','client','774688892','South Taya, Sénégal','1983-12-30','DEMO-CLIENT-008',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(72,'Lesch','Frank','client.009@djamonopay.test',NULL,'$2y$10$2m1uQ0CRW81nVVDncJpowuwoQUqn.EYaS8/l6orn3MPcALna08y3m','client','778849521','North Buck, Sénégal','1992-10-15','DEMO-CLIENT-009',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(73,'Schinner','Kitty','client.010@djamonopay.test',NULL,'$2y$10$3zJ4.7yd2iW3h9/hd/oVqOMzKMTEKyzjvgBF4ks5Fd944nmDOABqi','client','778293681','East Vida, Sénégal','2000-09-09','DEMO-CLIENT-010',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(74,'Kuphal','Ludie','client.011@djamonopay.test',NULL,'$2y$10$J3CeJYL.sjcDIxpsAor2xu1uBIlY4DJ.AFIggUMtCESOInBhb5.DO','client','777042120','New Willard, Sénégal','1968-04-04','DEMO-CLIENT-011',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(75,'Keebler','Alberta','client.012@djamonopay.test',NULL,'$2y$10$t.4XyzZ7e6SzFhyCNrZy9uDr7ofVi7SeIn5pu22a8dECmFswtJq5y','client','777661619','Wendyshire, Sénégal','1969-10-05','DEMO-CLIENT-012',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(76,'Bode','Unique','client.013@djamonopay.test',NULL,'$2y$10$EoB2Cx4Ujcmr5Dxf0/NaFOgkvhjn1qXiFcYv/lMmJVP1eKkKPwVNi','client','775726243','Schmelerhaven, Sénégal','1964-10-01','DEMO-CLIENT-013',1,'2026-10-02 22:32:49','2026-10-02 22:32:49',NULL),(77,'Weimann','Heath','client.014@djamonopay.test',NULL,'$2y$10$wLJyAmgSELlFcv78x058D.Cq1dLhKOsJfWsNipQ9YFqhZoX60l6PK','client','777840749','North Jacklynton, Sénégal','2001-10-11','DEMO-CLIENT-014',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(78,'Welch','Devin','client.015@djamonopay.test',NULL,'$2y$10$xSRpG6FnPv1GqGufXugjsu5WTO2FZFjN.qSWN3PRRDNVuLH3X1cbi','client','770809866','West Russell, Sénégal','1982-08-31','DEMO-CLIENT-015',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(79,'Huels','Chad','client.016@djamonopay.test',NULL,'$2y$10$HBTxRAERi6qnBdAI7T8Wqe4sa2Iu87MdHUkFawbJDcYIlAaqNWfIm','client','771465193','Padbergborough, Sénégal','1973-02-19','DEMO-CLIENT-016',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(80,'Erdman','Stacy','client.017@djamonopay.test',NULL,'$2y$10$K7pmDzZ9xbccklkFFO7Wt..WpTP5SkTM57l4DSJvPE5vqeLI/WRfi','client','773152549','North Jonathantown, Sénégal','2006-03-22','DEMO-CLIENT-017',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(81,'Anderson','Mateo','client.018@djamonopay.test',NULL,'$2y$10$dgJaBR0yXRY2rx3ClK1gGuFVkPnsmJcQYyt61gmslzOuJFpvPWjd2','client','770654349','Port Rachelfurt, Sénégal','2004-08-15','DEMO-CLIENT-018',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(82,'Bechtelar','Heidi','client.019@djamonopay.test',NULL,'$2y$10$d1y8o3Jc/z3OX9WSvW/dqOKZge7b5/maP0MdyVl1fpxWY9KzkJHO6','client','778847969','Lake Kingfort, Sénégal','1988-10-30','DEMO-CLIENT-019',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(83,'Lockman','Carlee','client.020@djamonopay.test',NULL,'$2y$10$c10sNTyD93dFe0nJvNSUE.KsVSxDZkCkNKbgQcYCPKvdJ/kys/loW','client','778136985','Port Bennie, Sénégal','1977-05-06','DEMO-CLIENT-020',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(84,'Strosin','Simone','client.021@djamonopay.test',NULL,'$2y$10$KaSfYHn56hLLi20/UvB.CerMLE6gfX2y60maPE1YMY8KeSP0OPsZm','client','778891583','East Karichester, Sénégal','1969-06-28','DEMO-CLIENT-021',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(85,'Rosenbaum','Uriel','client.022@djamonopay.test',NULL,'$2y$10$QFrMrilNo2ykjGi6JA5RK.giLXZ4a7XtobMcCb4PRMmc8asAiO9Ym','client','779865385','South Dariusmouth, Sénégal','1993-01-03','DEMO-CLIENT-022',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(86,'Paucek','Carmine','client.023@djamonopay.test',NULL,'$2y$10$TfxCVT3UfWrB5DMw/W1s2.rgOvb94eypA0C6DPwgGFm.J.2Dd1RyC','client','779218706','North Lorena, Sénégal','1994-09-27','DEMO-CLIENT-023',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(87,'Kilback','Claire','client.024@djamonopay.test',NULL,'$2y$10$KKj.RGbFYwqtlzCySLn2buYeLhhoMTzDfZHTe2oKZ0daRYvsEMvdG','client','771133006','Janiebury, Sénégal','1990-07-06','DEMO-CLIENT-024',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(88,'Robel','Dandre','client.025@djamonopay.test',NULL,'$2y$10$ByDIbBGJ3/6qGvlm.Xm/iu2aYVNa26oXcYeQQdEFRs4Boe/j8o2cO','client','779306563','Lake Lue, Sénégal','1989-06-25','DEMO-CLIENT-025',1,'2026-10-02 22:32:50','2026-10-02 22:32:50',NULL),(89,'Abbott','Solon','client.026@djamonopay.test',NULL,'$2y$10$YdNJ55CMH1/xaUMRG97ZeudPVBeZdCDLiSAj9puL2ZCU3QiOgW/wK','client','772644494','West Winonaburgh, Sénégal','2008-06-10','DEMO-CLIENT-026',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(90,'Spencer','Onie','client.027@djamonopay.test',NULL,'$2y$10$LZHaROOKKV78fli/dHfJuu3cSGM/vyGEvua6EEutfGpevkJrrAjmq','client','774851943','Angusborough, Sénégal','1995-04-12','DEMO-CLIENT-027',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(91,'Bosco','Demarco','client.028@djamonopay.test',NULL,'$2y$10$v2mJk52DCL5F2vlrG9sNyunb1XylIzja9pF0D/g8WnTyadwTO2ria','client','778402279','South Katarina, Sénégal','2006-01-27','DEMO-CLIENT-028',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(92,'Deckow','Alicia','client.029@djamonopay.test',NULL,'$2y$10$rfQcUfp1Nt8/9jA6wrtSoOJ7uMmr5SCg2U09nbjECDdi5kN9f2sYa','client','775059504','Vallieview, Sénégal','1971-09-29','DEMO-CLIENT-029',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(93,'Emard','Michele','client.030@djamonopay.test',NULL,'$2y$10$It43yNSRd/fxPajfE3Zq5O4AS2WV0ziMbS/9.TIhgDyXY4.j6dBH2','client','770987248','East Arvilla, Sénégal','2005-03-30','DEMO-CLIENT-030',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(94,'Kulas','Estelle','client.031@djamonopay.test',NULL,'$2y$10$knqcZ8lXxwSGWUxNDSQgkOYLTZ76P.HNJkSv87V4UJNkW9GfBliM.','client','777783219','West Missouri, Sénégal','1969-11-11','DEMO-CLIENT-031',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(95,'Crist','Brandon','client.032@djamonopay.test',NULL,'$2y$10$K9vP6VREs.cUHxSiTM9moOjrMqElsMQCVWVx5d0xrNbaktIpcnela','client','778855746','Maxmouth, Sénégal','1980-12-25','DEMO-CLIENT-032',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(96,'Torp','Cydney','client.033@djamonopay.test',NULL,'$2y$10$6WrX3ljHvCQ2aD5viyjlheA5Qzy.d/9p/TSKWMo4/r4U2S3Q77A9G','client','777346152','Creminfort, Sénégal','1969-10-18','DEMO-CLIENT-033',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(97,'Schmeler','Jesse','client.034@djamonopay.test',NULL,'$2y$10$ifOl.q2AgFIyA8YRYXnTpuHFghmr3OtZ4IXT/osLpPeDGBoDjQcV2','client','770662477','Lake Zionfurt, Sénégal','1981-06-27','DEMO-CLIENT-034',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(98,'Lueilwitz','Vada','client.035@djamonopay.test',NULL,'$2y$10$KXzc4aSXr3nDaiUsTTU6x.DI6A1f8HFLCDP1DonG6PksGugicZFAm','client','771862594','Domingoshire, Sénégal','1962-07-08','DEMO-CLIENT-035',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(99,'Frami','Katharina','client.036@djamonopay.test',NULL,'$2y$10$08RkWaYFRarOMf1N0WfQN.Owxshf55W5J14vSplzkbhB7r3uStZV2','client','774373326','Cassandrafort, Sénégal','1981-07-26','DEMO-CLIENT-036',1,'2026-10-02 22:32:51','2026-10-02 22:32:51',NULL),(100,'Gulgowski','Rusty','client.037@djamonopay.test',NULL,'$2y$10$pk0MFghDsFB4D0volcyXNOuNyb48rM1TCfBKwSOsEbBLmdeNTErKS','client','774565295','East Juana, Sénégal','1985-03-17','DEMO-CLIENT-037',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(101,'Bernhard','Gia','client.038@djamonopay.test',NULL,'$2y$10$3et003uLOKaM9d32dFqj9uS6tsNcXvgz/r8.juoMoGPuBvmVq3HCu','client','778830000','Kuhlmanville, Sénégal','1990-12-02','DEMO-CLIENT-038',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(102,'Zulauf','Stewart','client.039@djamonopay.test',NULL,'$2y$10$odvwOW3swlU0.M6CvVnV3OkxJmCW1tkKFCCkAFKLDfmNfDInSkES2','client','772605162','Howellview, Sénégal','1996-09-27','DEMO-CLIENT-039',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(103,'Price','Christa','client.040@djamonopay.test',NULL,'$2y$10$ah0Kj1l0N6HpVcxUQzPW/ObDMa5J5Hv7CBjmh.9.N.SkVoOeY/XtC','client','777045831','South Mittie, Sénégal','1996-08-16','DEMO-CLIENT-040',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(104,'Haag','Kiera','client.041@djamonopay.test',NULL,'$2y$10$1YsQD5bAVD4RaQRXH3Ewlu4hIp9EDELTylo93RRukv0Ag8dapp6z6','client','772189727','Glendahaven, Sénégal','1988-11-11','DEMO-CLIENT-041',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(105,'Gerhold','Bridget','client.042@djamonopay.test',NULL,'$2y$10$4EQb8RTiitve/pgSShqsye9/GllfQMHb4OHkrQKxYD1fGGadnjZX2','client','776689572','Jailynville, Sénégal','1971-06-06','DEMO-CLIENT-042',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(106,'Wyman','Nicklaus','client.043@djamonopay.test',NULL,'$2y$10$2Co8gl6LMYEII3asxKq/GeUDEKtl5zU7jb6Ecsvk/v19WklrmF496','client','773683497','McLaughlinshire, Sénégal','1985-10-15','DEMO-CLIENT-043',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(107,'Sanford','Lionel','client.044@djamonopay.test',NULL,'$2y$10$5IN9UJ5FPMNV3YfbQYoBr.VQ9RHZKvGsHSQD3TKMg78j6JOxABjLm','client','773089366','Scottieland, Sénégal','1970-03-14','DEMO-CLIENT-044',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(108,'Bode','Graciela','client.045@djamonopay.test',NULL,'$2y$10$EfZ9uQtjS/wjejrhIML1C.sGoGSo3dCf7DKfXdwTc9FUmX169X5kO','client','773380614','West Adam, Sénégal','1982-07-18','DEMO-CLIENT-045',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(109,'Morissette','Daren','client.046@djamonopay.test',NULL,'$2y$10$OqrEalQ5.tNpCMZ09hbo1eKCfmMnd2/totlKH2.wmiXq06D4flJt2','client','778937419','North Karelleborough, Sénégal','1992-10-21','DEMO-CLIENT-046',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(110,'Johnson','Fabian','client.047@djamonopay.test',NULL,'$2y$10$cetEbYh2/438RXb4x2qTYu.hNZt20zlUvu/VSzhN58WhYlqQCe22a','client','771063464','Granttown, Sénégal','1983-01-29','DEMO-CLIENT-047',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(111,'Kreiger','Carrie','client.048@djamonopay.test',NULL,'$2y$10$MXIj9EXWfVzHZVIwOVfpFOZ3zv5bq024PKto8ubIVKw2vxClM2QDu','client','779692386','South Cale, Sénégal','2007-02-07','DEMO-CLIENT-048',1,'2026-10-02 22:32:52','2026-10-02 22:32:52',NULL),(112,'Farrell','Meredith','client.049@djamonopay.test',NULL,'$2y$10$QqqY/F8.wEDBrIHcTOT0LO0089irgodKqexhbWf/p3vfVcWDTU7M6','client','776027501','Mariontown, Sénégal','1988-10-29','DEMO-CLIENT-049',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(113,'Bernhard','Kristy','client.050@djamonopay.test',NULL,'$2y$10$EyUQTRCWpswGndqHpYAy1u0drkUQdgzouCxaWMwDG5vlfO2/rHfc.','client','779301207','Port Felix, Sénégal','1989-11-19','DEMO-CLIENT-050',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(114,'Olson','Vivienne','client.051@djamonopay.test',NULL,'$2y$10$ghIbxnpVoteEykW9raqZ3.lA626IWBTtQOceozFB7EaiCN.DlDKhO','client','777758767','Port Margot, Sénégal','2006-12-07','DEMO-CLIENT-051',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(115,'Borer','Sarai','client.052@djamonopay.test',NULL,'$2y$10$kXaVdAHau8DR6LYOk3TjMufOOEMf3B.h.Fuyw6dLvc4I.ChVJRdd2','client','779777060','Kihnside, Sénégal','1975-11-06','DEMO-CLIENT-052',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(116,'Hayes','Mireya','client.053@djamonopay.test',NULL,'$2y$10$oJSZnqov83KcS2wl32l8ge0YMas7F3LivutPtwn5U4qMQ84cSHmk.','client','776270279','Hicklefort, Sénégal','1988-02-15','DEMO-CLIENT-053',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(117,'Batz','Valentin','client.054@djamonopay.test',NULL,'$2y$10$UI8lINrAJLbzm5s/33aM2OsArMYD92A2sdPbTATffDS5PtwQxGGMS','client','773815742','West Alfred, Sénégal','1981-07-15','DEMO-CLIENT-054',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(118,'Parisian','Lloyd','client.055@djamonopay.test',NULL,'$2y$10$P56.gab5oaeBA0Et0GBZl.0oWSyPHKs8kBV89n7NEZLK9FUpv.Nyi','client','776346537','Lake Jadonfurt, Sénégal','1973-02-07','DEMO-CLIENT-055',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(119,'Emard','Danika','client.056@djamonopay.test',NULL,'$2y$10$CzN3LewCDmweAsmh184So.G.vFYTjByyBfyG8Iy9Mf3AB2OwuxPHK','client','772834131','Halliechester, Sénégal','1984-11-14','DEMO-CLIENT-056',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(120,'Friesen','June','client.057@djamonopay.test',NULL,'$2y$10$C12VXYLEhOP4F7uudFyG2uBbn09h1rWxOs/kWeXXDLLNISGskYJPm','client','775688942','East Giovanna, Sénégal','2003-09-28','DEMO-CLIENT-057',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(121,'Hartmann','Reva','client.058@djamonopay.test',NULL,'$2y$10$d4cSbh1uhH8VMwcrktxkge0zV802RfzCM5I/L4PVkaxCCHkx06ryu','client','779232521','Fionabury, Sénégal','2003-06-09','DEMO-CLIENT-058',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(122,'Brown','George','client.059@djamonopay.test',NULL,'$2y$10$Ux.u.LFy4Szpwd5QR3j1/.ggSainBZwpjbSK6vWx/JyyQZ6TfSPLi','client','779387850','South Marlene, Sénégal','1975-09-18','DEMO-CLIENT-059',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(123,'Pacocha','Julien','client.060@djamonopay.test',NULL,'$2y$10$yIS.ac.icKxKoP4ZLmpNieF7kS6GRiF4I9Di/UNyiXtQPS6XuuWDe','client','779328944','Lefflerburgh, Sénégal','1996-10-06','DEMO-CLIENT-060',1,'2026-10-02 22:32:53','2026-10-02 22:32:53',NULL),(124,'Cummings','Lorna','client.061@djamonopay.test',NULL,'$2y$10$AJtZj1BFV2JrXf36ZupKu.HNf5dgMW0TOa1AJYnFY6l1OK6xbcAC.','client','772186129','Madalynburgh, Sénégal','1982-10-15','DEMO-CLIENT-061',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(125,'Pagac','Marcelino','client.062@djamonopay.test',NULL,'$2y$10$M7Eyf4s7.bjNO6qn0wFjgOJh2xPJ4XSEQYnvOte3ZOiqYx/DH.NOe','client','775466472','Cordeliaburgh, Sénégal','1967-07-21','DEMO-CLIENT-062',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(126,'Hartmann','Horacio','client.063@djamonopay.test',NULL,'$2y$10$WFzGaJclf04O4E9BWuJguO6670fp3H9OImB4UuiEaurMJoKQsimXO','client','770155780','North Whitney, Sénégal','1971-11-06','DEMO-CLIENT-063',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(127,'Legros','Angie','client.064@djamonopay.test',NULL,'$2y$10$4A0kbLeuTC8HWLRMJuOwU.yySIe6QrCLJ5vewowfRNQ/IDcrojL9G','client','778803626','Port Ebonyfurt, Sénégal','1997-06-01','DEMO-CLIENT-064',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(128,'Jerde','Keven','client.065@djamonopay.test',NULL,'$2y$10$nkOVx993dNj6/2m0NTWvUecyQjGVi1CFGucrSQc8G8SN1EGdP1qeG','client','774241755','New Elvie, Sénégal','1971-03-02','DEMO-CLIENT-065',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(129,'Hermann','Allen','client.066@djamonopay.test',NULL,'$2y$10$zMxLUsiFXPfs85oRgHRPMe07uYtuaK/ydJONIUpBi0NAEhaAMnl2O','client','772736521','East Margaritaview, Sénégal','1978-09-03','DEMO-CLIENT-066',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(130,'Schuppe','Neal','client.067@djamonopay.test',NULL,'$2y$10$s20o/cIeEspDiovfgeXtv.KvGkXqiPmLVB4RY7nEfc0yBXjIddipC','client','770496935','East Beaulahfort, Sénégal','1975-11-18','DEMO-CLIENT-067',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(131,'Yundt','Kiana','client.068@djamonopay.test',NULL,'$2y$10$QbHeyq7lBdELvWluB8dJ4eufNZcUuxvMDXwHh0xEo520QXC7Dvj/O','client','770188441','Grimesfort, Sénégal','1965-03-09','DEMO-CLIENT-068',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(132,'Lebsack','Adrian','client.069@djamonopay.test',NULL,'$2y$10$pyJM6urh/JaRZl6QxasR2OVVAxVAvML3emNlgvxoTARz80xjWEt6.','client','777001699','Rubyport, Sénégal','1997-05-22','DEMO-CLIENT-069',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(133,'Ryan','Ali','client.070@djamonopay.test',NULL,'$2y$10$r7gGA.bHdMjGtLDXG4H7MuDP2H2pxm2rLvwnDLCkn4LaTcffD5IFi','client','775096395','East Lydia, Sénégal','1993-01-15','DEMO-CLIENT-070',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(134,'Cummerata','Maxie','client.071@djamonopay.test',NULL,'$2y$10$mLW/4ZiKK5KvzKzjVYKnMe74sNO50r.LxLPgbBFz5a6XhOekEEPCy','client','775765146','South Phoebe, Sénégal','1990-05-05','DEMO-CLIENT-071',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(135,'Waelchi','Matteo','client.072@djamonopay.test',NULL,'$2y$10$SrY0s2rhTqrL2uJZYuDqM.8w3.UBUkgc1zxHg2VRshmM3jGUpYdRW','client','778199353','Port Tod, Sénégal','1978-05-24','DEMO-CLIENT-072',1,'2026-10-02 22:32:54','2026-10-02 22:32:54',NULL),(136,'Pfannerstill','Kaci','client.073@djamonopay.test',NULL,'$2y$10$yQJQ.k0.mBdc/tW.0oDWYeN4ZWEYYZxMxv82LrjarFR7KwUO4XZKq','client','776996693','Maribelborough, Sénégal','1974-08-17','DEMO-CLIENT-073',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(137,'O\'Connell','Georgette','client.074@djamonopay.test',NULL,'$2y$10$RNChXu2weeAAhP2T/ALUh.UT9UKD3JtCjVA1kmjMd8XgSOCK.A1tC','client','777642871','North Hallieberg, Sénégal','1978-07-07','DEMO-CLIENT-074',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(138,'Schaden','Joaquin','client.075@djamonopay.test',NULL,'$2y$10$wYq2H7SKmVBlHzX35SmCzeBKXqOUhGw6C/rmrtOOjJcZMULnmnhhK','client','773030515','Dickinsonbury, Sénégal','2004-03-04','DEMO-CLIENT-075',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(139,'Jones','Dayne','client.076@djamonopay.test',NULL,'$2y$10$v743xfZcSylSLP37lM/Sb.jp2hsw1kbVvRiMcKzPvd/ZeGUD1kOS.','client','777397585','Lake Nashberg, Sénégal','1999-06-10','DEMO-CLIENT-076',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(140,'Considine','Bernie','client.077@djamonopay.test',NULL,'$2y$10$kVMWebFRwi3EkRe1bXCTiOiqHZsDUsrJ1hRJ2Urj4tG0lzW6ri59m','client','774274128','Davisport, Sénégal','1963-08-03','DEMO-CLIENT-077',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(141,'Bosco','Clint','client.078@djamonopay.test',NULL,'$2y$10$XYZYff1ORLYn/kPD71GQL.HJ.2nBKVIcF3AAtMLi0rMP8kjwJlRQC','client','773167486','East Jacklynside, Sénégal','1992-06-03','DEMO-CLIENT-078',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(142,'Greenfelder','Alyce','client.079@djamonopay.test',NULL,'$2y$10$1Z8us8N2C5G14Bjx/lU/QeOKWLzhiH1GKxRLiRHPOsi2sXCpn6HbK','client','775128261','Braunside, Sénégal','1967-02-06','DEMO-CLIENT-079',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(143,'Rempel','Guillermo','client.080@djamonopay.test',NULL,'$2y$10$HLFqLbAHM22zsFJOzXETBOEZAV2IALJ41L0Ee8mSuVhY9J4bIo/sW','client','775055807','North Grace, Sénégal','1963-03-15','DEMO-CLIENT-080',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(144,'Morar','Adriel','client.081@djamonopay.test',NULL,'$2y$10$uVByj/E3z04hXTWGfbqRMe0UVTidvdTqAPDMEApW7f9TN4ntd9QOu','client','779338269','New Skylar, Sénégal','2004-01-22','DEMO-CLIENT-081',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(145,'Kulas','Alexie','client.082@djamonopay.test',NULL,'$2y$10$sTlb757swmJ1S519CB4ljuoS651RBkjsxHgFSIw4eKP3rKQSb0jvC','client','772873361','Lyrichaven, Sénégal','1996-11-18','DEMO-CLIENT-082',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(146,'Cummings','Ken','client.083@djamonopay.test',NULL,'$2y$10$UTBa/VZCP56fF7nLzqGK/ejYan70dBHvtu3rQvIR7iXFW49gMjWtm','client','774917565','Doloresshire, Sénégal','1986-04-20','DEMO-CLIENT-083',1,'2026-10-02 22:32:55','2026-10-02 22:32:55',NULL),(147,'Kuphal','Doyle','client.084@djamonopay.test',NULL,'$2y$10$Nw8u9JAmzSU4Qzu0YM.Xe.fBxbuIdkZi4XiBlcjwjQtY5BBSNNpQm','client','775615973','Lake Traceyland, Sénégal','1969-02-03','DEMO-CLIENT-084',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(148,'Schneider','Adaline','client.085@djamonopay.test',NULL,'$2y$10$b06pItGB0xC8lfXijc0FaeUkqvakuKpkLGRFe3rv/8t9C7mtulV3K','client','776050103','Wittingmouth, Sénégal','1978-06-20','DEMO-CLIENT-085',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(149,'Fahey','Rebecca','client.086@djamonopay.test',NULL,'$2y$10$1f4Jd68VgN5D5iy8SCu.4OyI3FTcyYpRg5wViTHfpbq4ABo5LsJRu','client','770497646','North Duaneland, Sénégal','1981-02-08','DEMO-CLIENT-086',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(150,'Rodriguez','Lesly','client.087@djamonopay.test',NULL,'$2y$10$sAdsRHfP8r1L5n83/KeIVeUCAwWfnyqfFeoPTwdhz4KEeTbLignEW','client','770070424','Mullerchester, Sénégal','1990-05-04','DEMO-CLIENT-087',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(151,'McCullough','Antonina','client.088@djamonopay.test',NULL,'$2y$10$MuUb3RsBtfFSylC9VA3fV.BX.1SmnY.U8dOHsV73f724j3f6X9Reu','client','778697846','Hartmannfort, Sénégal','1995-07-31','DEMO-CLIENT-088',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(152,'Zieme','Aletha','client.089@djamonopay.test',NULL,'$2y$10$WLOuUnf36r1QI9TjsA6APOacBd/DntVLuA2Vw1zf9psafI1b1ZTO.','client','770607821','New Lillaland, Sénégal','1962-10-23','DEMO-CLIENT-089',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(153,'Dickens','Willa','client.090@djamonopay.test',NULL,'$2y$10$WZzxq4II1E.82HMzFLX5tutklxJpZrwL.R1CaqjYmAbIVAuLtMdDK','client','775971544','Janatown, Sénégal','1992-10-25','DEMO-CLIENT-090',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(154,'Bosco','Gudrun','client.091@djamonopay.test',NULL,'$2y$10$Y4tFVXC.xJLK9DV3plZtoecMefSvlNN7QCQemWepuQloWchAeBgpe','client','776430603','West Clementinebury, Sénégal','1983-05-07','DEMO-CLIENT-091',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(155,'Blick','Newell','client.092@djamonopay.test',NULL,'$2y$10$oegw7i//bmYSx4ISejhm4uhbEFZPrMDhU4Di2S1TJPkLN3784anGW','client','770290549','South Philip, Sénégal','1964-11-19','DEMO-CLIENT-092',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(156,'Rowe','Leonie','client.093@djamonopay.test',NULL,'$2y$10$.sEv7s9zGnC9Y7kDctuk3uxVvAdegkMIpUrjYWOGeOXq7RQOxNmZm','client','772673340','Lorenzofort, Sénégal','1964-03-20','DEMO-CLIENT-093',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(157,'Koelpin','Noemi','client.094@djamonopay.test',NULL,'$2y$10$7lcD7OfTTGc9DYyZGA0I7ezP5BRELSlsHlrRCOX9ACTlpAcJyogYO','client','772760315','Harrisside, Sénégal','1979-11-03','DEMO-CLIENT-094',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(158,'Durgan','Kenyon','client.095@djamonopay.test',NULL,'$2y$10$n.1LaE7NruZvUES4GctmtOdojhYdv3sRPTE4//aJ4vI54EBD2Kyoy','client','776595512','New Georgiana, Sénégal','2002-05-26','DEMO-CLIENT-095',1,'2026-10-02 22:32:56','2026-10-02 22:32:56',NULL),(159,'Abernathy','Devyn','client.096@djamonopay.test',NULL,'$2y$10$4dO9MU4ey.UsZ2d1bvVfHuEorv2hMpj.MU0heCxuyVjv.BXx1AdcS','client','776645757','North Vella, Sénégal','1995-09-04','DEMO-CLIENT-096',1,'2026-10-02 22:32:57','2026-10-02 22:32:57',NULL),(160,'Klocko','Dina','client.097@djamonopay.test',NULL,'$2y$10$r2As/s7T3ytPf565y//yheYHxOD7smm4h/Rb45svyhOIXpu1kN4Re','client','773405779','Waterschester, Sénégal','1962-07-24','DEMO-CLIENT-097',1,'2026-10-02 22:32:57','2026-10-02 22:32:57',NULL),(161,'Hilpert','Sofia','client.098@djamonopay.test',NULL,'$2y$10$Nrb1JBGbU1Ao52BCPVRfgOg/N6NkpqZae/p7rbQP5dOWWnhkVGB3W','client','773621558','East Bridie, Sénégal','1964-10-24','DEMO-CLIENT-098',1,'2026-10-02 22:32:57','2026-10-02 22:32:57',NULL),(162,'Littel','Janice','client.099@djamonopay.test',NULL,'$2y$10$KDkJvvp1oSuXxrq357KbHe/YgaQYao9NerFP3rVYvrRk6ww3FniY.','client','776484220','Lake Sterling, Sénégal','1997-01-13','DEMO-CLIENT-099',1,'2026-10-02 22:32:57','2026-10-02 22:32:57',NULL),(163,'Kertzmann','Alejandrin','client.100@djamonopay.test',NULL,'$2y$10$VMVqZ3Dpiiv4iBl56BtuTetZ6WVtQEmdXjkPUzmdzlCt6GH/uoLg2','client','770770715','Daphneton, Sénégal','1970-02-02','DEMO-CLIENT-100',1,'2026-10-02 22:32:57','2026-10-02 22:32:57',NULL),(164,'NIASSY','MAMADOU LAMINE','niassy.lamine10@gmail.com',NULL,'$2y$10$UGk1ITIgyHX5mG6evMXnv.cbFFCa7TVo4NcgAoDTVrQDtTFYFX80i','client','+221704611894','Dakar , Keur Massar , cité Aïnoumady villa 537, Keur Massar','1995-03-19','1870199902491',1,'2026-10-02 22:44:44','2026-10-02 22:44:44','GtMxKhdHdgbpg23N0Tib7aBkJN7drboXuMi9JhkOOSXhNwKVDyJEiI7Y2Qx8'),(165,'NIASSY','MAMADOU LAMINE','niassy.lamine11@gmail.com',NULL,'$2y$10$mmqxwj6eTGxJX/N/YwjmLe1umSwn.2iVl0px5x2NX7i5bv5WppwR6','agent','+221704611894','Dakar , Keur Massar , cité Aïnoumady villa 537, Keur Massar','2026-09-30','1870199902490',1,'2026-10-02 22:53:11','2026-10-02 22:53:11',NULL),(166,'NIASSY','MAMADOU LAMINE','niassy.lamine12@gmail.com',NULL,'$2y$10$Tq5yHvakixdNjCH6S2rnL.mPD.EMgsZnpZqTGcE3bXEZ1so1ytyDi','distributeur','+221704611894','Dakar , Keur Massar , cité Aïnoumady villa 537, Keur Massar','2026-10-01','1870199902492',1,'2026-10-03 08:19:05','2026-10-03 08:19:05',NULL);
/*!40000 ALTER TABLE `users2` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-10-05 13:54:38
