-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: timetable_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Current Database: `timetable_db`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `timetable_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `timetable_db`;

--
-- Table structure for table `lectures`
--

DROP TABLE IF EXISTS `lectures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lectures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `per_week` int(11) DEFAULT NULL,
  `duration` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lectures`
--

LOCK TABLES `lectures` WRITE;
/*!40000 ALTER TABLE `lectures` DISABLE KEYS */;
INSERT INTO `lectures` VALUES (5,'Web','Spp','CR2',2,'1 Hour'),(6,'ML','AJS','CR3',3,'1 Hour'),(7,'SE','VAk','CR1',3,'1 Hour'),(8,'Minor','PDP','civil semi',3,'1 Hour');
/*!40000 ALTER TABLE `lectures` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `practicals`
--

DROP TABLE IF EXISTS `practicals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `practicals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `per_week` int(11) DEFAULT NULL,
  `batches` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `practicals`
--

LOCK TABLES `practicals` WRITE;
/*!40000 ALTER TABLE `practicals` DISABLE KEYS */;
INSERT INTO `practicals` VALUES (4,'WEB P','SPP','Network',1,'T1'),(5,'Web ','SPP','CGL',1,'T2');
/*!40000 ALTER TABLE `practicals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `saved_timetables`
--

DROP TABLE IF EXISTS `saved_timetables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saved_timetables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `timetable_data` longtext DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `saved_timetables`
--

LOCK TABLES `saved_timetables` WRITE;
/*!40000 ALTER TABLE `saved_timetables` DISABLE KEYS */;
INSERT INTO `saved_timetables` VALUES (1,'2026-04-28 19:17:25','[{\"day\":\"Monday\",\"slot\":0,\"data\":{\"subject\":\"OS\",\"faculty\":\"VAK\",\"venue\":\"CR2\",\"type\":\"LECTURE\"}},{\"day\":\"Monday\",\"slot\":1,\"data\":null},{\"day\":\"Monday\",\"slot\":2,\"data\":null},{\"day\":\"Monday\",\"slot\":3,\"data\":null},{\"day\":\"Monday\",\"slot\":4,\"data\":{\"subject\":\"Java\",\"faculty\":\"ARP\",\"venue\":\"CR4\",\"type\":\"LECTURE\"}},{\"day\":\"Monday\",\"slot\":5,\"data\":null},{\"day\":\"Tuesday\",\"slot\":0,\"data\":{\"subject\":\"DBMS\",\"faculty\":\"Patil\",\"venue\":\"CR4\",\"type\":\"LECTURE\"}},{\"day\":\"Tuesday\",\"slot\":1,\"data\":{\"subject\":\"Java\",\"faculty\":\"ARP\",\"venue\":\"CR4\",\"type\":\"LECTURE\"}},{\"day\":\"Tuesday\",\"slot\":2,\"data\":null},{\"day\":\"Tuesday\",\"slot\":3,\"data\":null},{\"day\":\"Tuesday\",\"slot\":4,\"data\":null},{\"day\":\"Tuesday\",\"slot\":5,\"data\":{\"subject\":\"OS\",\"faculty\":\"VAK\",\"venue\":\"CR2\",\"type\":\"LECTURE\"}},{\"day\":\"Wednesday\",\"slot\":0,\"data\":null},{\"day\":\"Wednesday\",\"slot\":1,\"data\":null},{\"day\":\"Wednesday\",\"slot\":2,\"data\":null},{\"day\":\"Wednesday\",\"slot\":3,\"data\":null},{\"day\":\"Wednesday\",\"slot\":4,\"data\":null},{\"day\":\"Wednesday\",\"slot\":5,\"data\":{\"subject\":\"DBMS\",\"faculty\":\"Patil\",\"venue\":\"CR4\",\"type\":\"LECTURE\"}},{\"day\":\"Thursday\",\"slot\":0,\"data\":{\"subject\":\"CN\",\"faculty\":\"VAP\",\"venue\":\"CGL\",\"type\":\"LAB\",\"batch\":\"S3\"}},{\"day\":\"Thursday\",\"slot\":1,\"data\":{\"subject\":\"CN\",\"faculty\":\"VAP\",\"venue\":\"CGL\",\"type\":\"LAB\",\"batch\":\"S3\"}},{\"day\":\"Thursday\",\"slot\":2,\"data\":{\"subject\":\"DBMS\",\"faculty\":\"Patil\",\"venue\":\"CR4\",\"type\":\"LECTURE\"}},{\"day\":\"Thursday\",\"slot\":3,\"data\":null},{\"day\":\"Thursday\",\"slot\":4,\"data\":null},{\"day\":\"Thursday\",\"slot\":5,\"data\":null},{\"day\":\"Friday\",\"slot\":0,\"data\":null},{\"day\":\"Friday\",\"slot\":1,\"data\":{\"subject\":\"OS\",\"faculty\":\"VAK\",\"venue\":\"CR2\",\"type\":\"LECTURE\"}},{\"day\":\"Friday\",\"slot\":2,\"data\":{\"subject\":\"java \",\"faculty\":\"ARP\",\"venue\":\"Network\",\"type\":\"LAB\",\"batch\":\"T1\"}},{\"day\":\"Friday\",\"slot\":3,\"data\":{\"subject\":\"java \",\"faculty\":\"ARP\",\"venue\":\"Network\",\"type\":\"LAB\",\"batch\":\"T1\"}},{\"day\":\"Friday\",\"slot\":4,\"data\":null},{\"day\":\"Friday\",\"slot\":5,\"data\":null}]'),(2,'2026-04-28 19:22:48','[]'),(3,'2026-04-30 10:45:59','[]');
/*!40000 ALTER TABLE `saved_timetables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day_name` varchar(20) DEFAULT NULL,
  `slot_time` varchar(50) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `faculty` varchar(100) DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timetable`
--

LOCK TABLES `timetable` WRITE;
/*!40000 ALTER TABLE `timetable` DISABLE KEYS */;
/*!40000 ALTER TABLE `timetable` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 10:25:38
