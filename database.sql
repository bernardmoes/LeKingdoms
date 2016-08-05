-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: kingdoms
-- ------------------------------------------------------
-- Server version	5.7.11

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
-- Table structure for table `discord_player`
--

DROP TABLE IF EXISTS `discord_player`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `discord_player` (
  `guid` varbinary(50) NOT NULL,
  `username` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `discord_player`
--

LOCK TABLES `discord_player` WRITE;
/*!40000 ALTER TABLE `discord_player` DISABLE KEYS */;
/*!40000 ALTER TABLE `discord_player` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `kingdom` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `item` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `amountleft` int(11) DEFAULT '0',
  PRIMARY KEY (`kingdom`,`item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kingdom`
--

DROP TABLE IF EXISTS `kingdom`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kingdom` (
  `username` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `locations` mediumtext COLLATE utf8_unicode_ci,
  `L` int(11) DEFAULT NULL,
  `G` int(11) DEFAULT '0',
  `F` smallint(6) DEFAULT '0',
  `W` smallint(6) DEFAULT '0',
  `S` smallint(6) DEFAULT '0',
  `M` smallint(6) DEFAULT '0',
  `B` smallint(6) DEFAULT '0',
  `FA` smallint(6) DEFAULT '0',
  `MN` smallint(6) DEFAULT '0',
  `FR` smallint(6) DEFAULT '0',
  `SM` smallint(6) DEFAULT '0',
  `BT` smallint(6) DEFAULT '0',
  `U` smallint(6) DEFAULT '0',
  `WF` smallint(6) DEFAULT '0',
  `BK` smallint(6) DEFAULT '0',
  `TC` smallint(6) DEFAULT '0',
  `H` smallint(6) DEFAULT '0',
  `T` smallint(6) DEFAULT '0',
  `P` smallint(6) DEFAULT '0',
  `I` smallint(6) DEFAULT '0',
  `IA` smallint(6) DEFAULT '0',
  `PR` smallint(6) DEFAULT '0',
  `D` smallint(6) DEFAULT '0',
  `ST` smallint(6) DEFAULT '0',
  `Q` smallint(6) DEFAULT '0',
  `R` smallint(6) DEFAULT '0',
  `HO` smallint(6) DEFAULT '0',
  `WO` smallint(6) DEFAULT '0',
  `WA` smallint(6) DEFAULT '0',
  `TI` smallint(6) DEFAULT '0',
  `SI` mediumint(9) DEFAULT NULL,
  `WM` smallint(6) DEFAULT '0',
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kingdom`
--

LOCK TABLES `kingdom` WRITE;
/*!40000 ALTER TABLE `kingdom` DISABLE KEYS */;
/*!40000 ALTER TABLE `kingdom` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `user` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `report` longtext COLLATE utf8_unicode_ci,
  `timestamp` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spells`
--

DROP TABLE IF EXISTS `spells`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spells` (
  `castby` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `caston` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `duration` tinyint(4) DEFAULT '1',
  `spell` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`caston`,`castby`,`spell`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spells`
--

LOCK TABLES `spells` WRITE;
/*!40000 ALTER TABLE `spells` DISABLE KEYS */;
/*!40000 ALTER TABLE `spells` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `turnnotes`
--

DROP TABLE IF EXISTS `turnnotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `turnnotes` (
  `fromuser` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `touser` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `notes` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`fromuser`,`touser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `turnnotes`
--

LOCK TABLES `turnnotes` WRITE;
/*!40000 ALTER TABLE `turnnotes` DISABLE KEYS */;
/*!40000 ALTER TABLE `turnnotes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `worldvars`
--

DROP TABLE IF EXISTS `worldvars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worldvars` (
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `worldvars`
--

LOCK TABLES `worldvars` WRITE;
/*!40000 ALTER TABLE `worldvars` DISABLE KEYS */;
INSERT INTO `worldvars` VALUES ('ageof','#2016'),('lastturn','1470434816'),('turns','51');
/*!40000 ALTER TABLE `worldvars` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-06  1:10:24
