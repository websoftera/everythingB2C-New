-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: b2c
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
-- Current Database: `b2c`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `b2c` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `b2c`;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (10,1,'Prakash','8180099165','415503','A/p Shirdhon, Tal- Koregaon, Dist- Satara','','Pune','Maharashtra',1,'2025-07-09 10:47:00'),(11,1,'Prakash','8180099165','415502','A/p Shirdhon, Tal- Koregaon, Dist- Satara','','Kanpur','Gujarat',0,'2025-07-09 10:47:03'),(12,4,'Nirmit Shah','08208917546','411001','abc','','Pune','Maharashtra',0,'2025-07-29 15:48:48'),(13,4,'Nirmit Shah','08208917546','38011','abc','','baroda','gujarat',0,'2025-07-29 17:52:54'),(14,5,'SAMIR CHANDRAKANTBHAI SHAH','08780406230','411001','NEAR BHARAT FORGE','','Pune','Maharashtra',1,'2025-08-17 10:19:25'),(15,6,'Khushali modi','8780086227','390021','J-203 Satva flats','','Vadodara','Gujarat',1,'2025-08-27 15:45:10'),(16,10,'Nirmit Shah','08208917546','411001','Siciliaa CHS-A-602','','Pune','Maharashtra',0,'2025-12-22 05:29:01'),(19,14,'p','9999999999','415018','pune','','pune','maharashtra',0,'2026-02-13 03:53:50'),(20,16,'13549549','hgfhyguh','jhkjhkjh','knkjnkjbkk','jhjkhbjhbhbj','jhbjhgj','hgjhgh',0,'2026-02-18 11:27:07');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','admin','manager') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Super Admin','admin@everythingb2c.com','$2y$10$nwNkYiFBWQMOxZxQ1jnNB.LjOCgVte2U7w3kWRR0T96a.x4QHLbgG','super_admin',1,'2026-02-22 19:09:24','2025-06-28 06:15:55','2026-02-22 19:09:24');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (183,11,32,1,'2026-01-06 04:24:28'),(184,11,30,1,'2026-01-06 04:24:28'),(186,11,31,1,'2026-01-12 08:46:37'),(195,13,23,1,'2026-02-07 04:49:27'),(196,13,4,3,'2026-02-07 10:58:27'),(218,16,8,10,'2026-02-18 09:06:17'),(224,1,4,1,'2026-02-22 19:53:49');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `product_count` int(11) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'Kitchen','kitchen','Home page/Product Categories images/KITCHEN.webp','Kitchen essentials and supplies',2,1,'2025-06-22 22:25:18',NULL),(2,NULL,'Office Stationery','office-stationery','uploads/categories/68a0c32d46553.webp','Office and school stationery items',4,1,'2025-06-22 22:25:18',NULL),(3,NULL,'Cleaning & Household','cleaning-household','uploads/categories/68a0c4633da4c.webp','Cleaning and household products',3,1,'2025-06-22 22:25:18',NULL),(4,NULL,'Personal Care','personal-care','uploads/categories/68a0c5288eebc.webp','Personal care and hygiene products',0,1,'2025-06-22 22:25:18',NULL),(5,NULL,'Diapers & Wipes','diapers-wipes','uploads/categories/68a0c2f24718c.webp','Baby care products',0,1,'2025-06-22 22:25:18',NULL),(6,NULL,'Home & Garden','home-garden','uploads/categories/68a0c315ed059.webp','Home and garden supplies',0,1,'2025-06-22 22:25:18',NULL),(7,NULL,'Other','other','uploads/categories/68a0c4835e89a.webp','Other miscellaneous products',1,1,'2025-06-22 22:25:18',NULL),(9,NULL,'Industrial Safety Products','industrial-safety-products','uploads/categories/68a0c320072fc.webp','',0,1,'2025-07-23 06:22:44',NULL),(16,NULL,'Eye Protection','eye-protection','uploads/categories/696da1e815108.png','Eye Protection',0,1,'2025-08-01 09:01:00',9),(17,NULL,'Foot Protection','foot-protection','uploads/categories/696da1f7c4c28.png','Foot Protection',0,1,'2025-08-01 09:27:36',9),(18,NULL,'Head Protection','head-protection','uploads/categories/696da20ad6b54.png','',0,1,'2025-08-01 10:04:10',9),(19,NULL,'Safety Belt','safety-belt','uploads/categories/696da21acaee5.png','Safety Belt',0,1,'2025-08-12 10:12:02',9),(20,NULL,'Ear Protection','ear-protection','uploads/categories/696da1d2a9b59.png','',0,1,'2025-08-12 10:21:08',9),(21,NULL,'School Stationary','school-stationary','uploads/categories/68a0c349c530b.webp','School Stationary',0,1,'2025-08-16 17:37:35',NULL),(22,NULL,'Packing Materials','packing-materials','uploads/categories/68a0c338e3bd3.webp','',0,1,'2025-08-16 17:38:57',NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `hsn` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (11,12,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(12,13,10,NULL,1,35.00,35.00,18.00,6.30,0.00,0.00),(13,14,8,NULL,1,95.00,95.00,18.00,17.10,0.00,0.00),(14,15,8,NULL,1,95.00,95.00,18.00,17.10,0.00,0.00),(16,17,10,NULL,3,105.00,35.00,18.00,18.90,0.00,0.00),(17,18,10,NULL,3,105.00,35.00,18.00,18.90,0.00,0.00),(18,19,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(19,20,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(21,22,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(22,23,10,NULL,7,245.00,35.00,18.00,44.10,0.00,0.00),(23,24,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(24,26,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(29,32,8,NULL,3,285.00,95.00,18.00,51.30,0.00,0.00),(30,33,4,NULL,2,636.00,318.00,18.00,114.48,0.00,0.00),(31,34,4,NULL,4,1272.00,318.00,18.00,228.96,450.00,318.00),(32,34,10,NULL,1,35.00,35.00,18.00,6.30,45.00,35.00),(33,35,10,NULL,5,175.00,35.00,18.00,31.50,45.00,35.00),(34,36,4,NULL,1,318.00,318.00,18.00,57.24,450.00,318.00),(35,37,4,NULL,1,318.00,318.00,18.00,57.24,450.00,318.00),(36,38,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(37,39,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(38,40,8,NULL,2,190.00,95.00,18.00,34.20,0.00,0.00),(39,40,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(40,41,4,NULL,1,318.00,318.00,18.00,57.24,450.00,318.00),(41,42,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(42,43,10,'12345678',4,140.00,35.00,18.00,25.20,45.00,35.00),(43,43,4,'12345678',2,636.00,318.00,18.00,114.48,450.00,318.00),(44,43,6,'12345678',2,240.00,120.00,18.00,43.20,150.00,120.00),(45,44,4,'12345678',2,636.00,318.00,18.00,114.48,450.00,318.00),(46,44,5,'12345678',1,280.00,280.00,18.00,50.40,290.00,280.00),(48,44,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(49,44,7,'12345678',1,65.00,65.00,18.00,11.70,80.00,65.00),(50,44,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(51,45,10,'12345678',6,210.00,35.00,18.00,37.80,45.00,35.00),(52,46,6,'12345678',2,240.00,120.00,18.00,43.20,150.00,120.00),(53,46,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(54,46,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(55,46,8,'12345678',3,285.00,95.00,18.00,51.30,120.00,95.00),(56,47,10,'12345678',4,140.00,35.00,18.00,25.20,45.00,35.00),(57,47,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(58,47,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(59,47,9,'12345678',2,320.00,160.00,18.00,57.60,200.00,160.00),(61,48,4,'12345678',2,636.00,318.00,18.00,114.48,450.00,318.00),(62,48,10,'12345678',4,140.00,35.00,18.00,25.20,45.00,35.00),(63,48,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(64,48,6,'12345678',4,480.00,120.00,18.00,86.40,150.00,120.00),(65,48,9,'12345678',5,800.00,160.00,18.00,144.00,200.00,160.00),(67,49,8,'12345678',2,190.00,95.00,18.00,34.20,120.00,95.00),(68,49,10,'12345678',3,105.00,35.00,18.00,18.90,45.00,35.00),(69,50,10,'12345678',3,105.00,35.00,18.00,18.90,45.00,35.00),(70,50,8,'12345678',3,285.00,95.00,18.00,51.30,120.00,95.00),(71,51,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(72,51,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(73,51,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(74,52,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(75,53,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(76,53,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(77,54,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(78,54,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(79,55,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(80,55,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(81,56,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(82,56,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(83,56,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(84,56,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(85,57,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(86,57,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(87,57,65,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(88,57,58,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(89,58,65,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(90,58,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(91,59,64,'12345678',2,180.00,90.00,18.00,32.40,100.00,90.00),(92,60,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(93,60,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(94,60,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(95,60,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(96,61,69,'HSN 1111',1,90.00,90.00,18.00,16.20,100.00,90.00),(97,61,21,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(98,62,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(99,63,23,'90049090',1,60.00,60.00,18.00,10.80,93.00,60.00),(100,63,67,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(101,63,66,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(102,63,60,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(103,63,61,'12345678',5,450.00,90.00,18.00,81.00,100.00,90.00),(104,63,59,'12345678',4,360.00,90.00,18.00,64.80,100.00,90.00),(105,64,15,'12345678',2,180.00,90.00,18.00,32.40,100.00,90.00),(106,65,75,'90049090',1,918.00,918.00,18.00,165.24,1400.00,918.00),(107,66,75,'90049090',1,918.00,918.00,18.00,165.24,1400.00,918.00),(108,67,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(109,68,68,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(110,68,75,'90049090',1,918.00,918.00,18.00,165.24,1400.00,918.00),(111,68,67,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(112,69,74,'90049090',3,285.00,95.00,18.00,51.30,123.00,95.00),(113,70,68,'12345678',4,360.00,90.00,18.00,64.80,100.00,90.00),(114,70,67,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(115,70,66,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(116,70,65,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(117,71,75,'90049090',2,1836.00,918.00,18.00,330.48,1400.00,918.00),(118,72,75,'90049090',2,1836.00,918.00,18.00,330.48,1400.00,918.00),(119,73,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(120,74,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(121,75,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(122,76,63,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_status_id` int(11) NOT NULL,
  `status_description` text DEFAULT NULL,
  `updated_by` enum('admin','system','user') DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_history`
--

LOCK TABLES `order_status_history` WRITE;
/*!40000 ALTER TABLE `order_status_history` DISABLE KEYS */;
INSERT INTO `order_status_history` VALUES (11,12,1,'Order placed successfully','system','2025-07-08 08:56:06'),(12,13,1,'Order placed successfully','system','2025-07-08 08:57:10'),(13,14,1,'Order placed successfully','system','2025-07-08 09:07:20'),(14,15,1,'Order placed successfully','system','2025-07-08 09:19:04'),(15,16,1,'Order placed successfully','system','2025-07-08 09:21:23'),(16,16,1,'','admin','2025-07-08 09:28:47'),(17,16,1,'','admin','2025-07-08 09:29:19'),(18,17,1,'Order placed successfully','system','2025-07-08 10:33:09'),(19,18,1,'Order placed successfully','system','2025-07-08 10:34:08'),(20,16,1,'','admin','2025-07-08 11:04:56'),(21,19,1,'Order placed successfully','system','2025-07-09 10:47:33'),(22,20,1,'Order placed successfully','system','2025-07-09 13:55:43'),(23,21,1,'Order placed successfully','system','2025-07-09 13:58:23'),(24,22,1,'Order placed successfully','system','2025-07-09 14:04:45'),(25,23,1,'Order placed successfully','system','2025-07-09 14:09:30'),(26,24,1,'Order placed successfully','system','2025-07-09 14:16:11'),(27,28,1,'Order placed successfully','system','2025-07-09 14:23:23'),(28,31,1,'Order placed successfully','system','2025-07-09 14:26:57'),(29,32,1,'Order placed successfully','system','2025-07-09 14:29:10'),(30,32,2,'your order is in processing status','admin','2025-07-09 14:36:17'),(31,32,3,'Order shipped from Delhi','admin','2025-07-09 14:36:58'),(32,32,4,'Order is in Transit','admin','2025-07-09 14:37:57'),(33,32,5,'Out for Delivery','admin','2025-07-09 14:38:31'),(34,32,13,'Order Delivered successfully','admin','2025-07-09 14:39:23'),(35,32,34,'','admin','2025-07-09 14:42:41'),(36,33,1,'Order placed successfully','system','2025-07-09 15:03:42'),(37,34,1,'Order placed successfully','system','2025-07-09 15:19:36'),(38,35,1,'Order placed successfully','system','2025-07-09 16:27:35'),(39,36,1,'Order placed successfully','system','2025-07-09 16:30:58'),(40,37,1,'Order placed successfully','system','2025-07-09 16:31:31'),(41,38,1,'Order placed successfully','system','2025-07-09 16:32:29'),(42,39,1,'Order placed successfully','system','2025-07-09 18:39:52'),(43,40,1,'Order placed successfully','system','2025-07-09 18:52:11'),(44,41,1,'Order placed successfully','system','2025-07-09 19:15:29'),(45,42,1,'Order placed successfully','system','2025-07-09 19:51:21'),(46,43,1,'Order placed successfully','system','2025-07-10 05:11:04'),(47,44,1,'Order placed successfully','system','2025-07-13 06:40:38'),(48,45,1,'Order placed successfully','system','2025-07-16 13:38:58'),(49,46,1,'Order placed successfully','system','2025-07-22 17:57:28'),(50,47,1,'Order placed successfully','system','2025-07-26 14:22:13'),(51,48,1,'Order placed successfully','system','2025-07-29 15:39:03'),(52,49,1,'Order placed successfully','system','2025-07-29 15:49:02'),(53,50,1,'Order placed successfully','system','2025-07-29 17:54:09'),(54,51,1,'Order placed successfully','system','2025-08-17 10:19:58'),(55,52,1,'Order placed successfully','system','2025-08-27 15:45:18'),(56,48,1,'','admin','2025-09-14 11:17:12'),(57,52,3,'','admin','2025-09-14 13:46:01'),(58,52,1,'','admin','2025-09-14 13:47:30'),(59,52,3,'','admin','2025-09-14 13:58:11'),(60,51,1,'','admin','2025-09-14 16:14:05'),(61,51,1,'','admin','2025-09-14 16:23:07'),(62,52,1,'','admin','2025-09-14 16:42:54'),(63,51,1,'','admin','2025-09-14 16:43:23'),(64,51,3,'','admin','2025-09-14 16:44:51'),(65,53,1,'Order placed successfully','system','2025-09-14 16:47:57'),(66,53,10,'','admin','2025-09-14 16:48:47'),(67,53,1,'','admin','2025-09-14 16:51:07'),(68,53,3,'','admin','2025-09-14 16:51:15'),(69,53,1,'','admin','2025-09-14 16:52:33'),(70,54,1,'Order placed successfully','system','2025-09-22 06:04:22'),(71,55,1,'Order placed successfully','system','2025-09-22 06:09:24'),(72,56,1,'Order placed successfully','system','2025-09-23 15:13:18'),(73,57,1,'Order placed successfully','system','2025-09-27 06:03:00'),(74,58,1,'Order placed successfully','system','2025-09-27 06:04:10'),(75,59,1,'Order placed successfully','system','2025-09-27 06:44:00'),(76,60,1,'Order placed successfully','system','2025-09-27 06:53:23'),(77,61,1,'Order placed successfully','system','2025-12-22 05:29:14'),(78,62,1,'Order placed successfully','system','2026-01-31 06:43:06'),(79,63,1,'Order placed successfully','system','2026-01-31 06:47:49'),(80,63,24,'','admin','2026-02-07 14:56:40'),(81,63,31,'','admin','2026-02-07 14:59:24'),(82,60,38,'','admin','2026-02-07 14:59:58'),(83,60,3,'','admin','2026-02-07 15:22:25'),(84,64,1,'Order placed successfully','system','2026-02-07 15:43:08'),(85,65,1,'Order placed successfully','system','2026-02-07 15:43:32'),(86,66,1,'Order placed successfully','system','2026-02-11 09:21:53'),(87,67,1,'Order placed successfully','system','2026-02-13 10:01:48'),(88,68,1,'Order placed successfully','system','2026-02-17 10:10:31'),(89,69,1,'Order placed successfully','system','2026-02-17 14:17:34'),(90,70,1,'Order placed successfully','system','2026-02-18 05:54:28'),(91,71,1,'Order placed successfully','system','2026-02-19 12:00:57'),(92,72,1,'Order placed successfully','system','2026-02-22 19:44:28'),(93,73,1,'Order placed successfully','system','2026-02-22 19:48:33'),(94,74,1,'Order placed successfully','system','2026-02-22 19:51:03'),(95,75,1,'Order placed successfully','system','2026-02-22 19:53:02'),(96,76,1,'Order placed successfully','system','2026-02-23 10:57:35');
/*!40000 ALTER TABLE `order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_statuses`
--

DROP TABLE IF EXISTS `order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT '#007bff',
  `is_system` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_statuses`
--

LOCK TABLES `order_statuses` WRITE;
/*!40000 ALTER TABLE `order_statuses` DISABLE KEYS */;
INSERT INTO `order_statuses` VALUES (1,'Pending','Order has been placed and is awaiting confirmation','#ffc107',1,1,'2025-06-28 09:21:58'),(2,'Processing','Order is being processed and prepared for shipping','#17a2b8',1,2,'2025-06-28 09:21:58'),(3,'Shipped','Order has been shipped from our warehouse','#28a745',1,3,'2025-06-28 09:21:58'),(4,'In Transit','Order is in transit to delivery location','#6f42c1',1,4,'2025-06-28 09:21:58'),(5,'Out for Delivery','Order is out for delivery to your address','#fd7e14',1,5,'2025-06-28 09:21:58'),(6,'Delivered','Order has been successfully delivered','#20c997',1,6,'2025-06-28 09:21:58'),(7,'Canceled','Order has been canceled','#dc3545',1,7,'2025-06-28 09:21:58'),(8,'Pending','Order has been placed and is awaiting confirmation','#ffc107',1,1,'2025-06-28 09:23:39'),(9,'Processing','Order is being processed and prepared for shipping','#17a2b8',1,2,'2025-06-28 09:23:39'),(10,'Shipped','Order has been shipped from our warehouse','#28a745',1,3,'2025-06-28 09:23:39'),(11,'In Transit','Order is in transit to delivery location','#6f42c1',1,4,'2025-06-28 09:23:39'),(12,'Out for Delivery','Order is out for delivery to your address','#fd7e14',1,5,'2025-06-28 09:23:40'),(13,'Delivered','Order has been successfully delivered','#20c997',1,6,'2025-06-28 09:23:40'),(14,'Canceled','Order has been canceled','#dc3545',1,7,'2025-06-28 09:23:40'),(15,'Pending','Order has been placed and is awaiting confirmation','#ffc107',1,1,'2025-06-28 12:39:57'),(16,'Processing','Order is being processed and prepared for shipping','#17a2b8',1,2,'2025-06-28 12:39:57'),(17,'Shipped','Order has been shipped from our warehouse','#28a745',1,3,'2025-06-28 12:39:57'),(18,'In Transit','Order is in transit to delivery location','#6f42c1',1,4,'2025-06-28 12:39:57'),(19,'Out for Delivery','Order is out for delivery to your address','#fd7e14',1,5,'2025-06-28 12:39:57'),(20,'Delivered','Order has been successfully delivered','#20c997',1,6,'2025-06-28 12:39:57'),(21,'Canceled','Order has been canceled','#dc3545',1,7,'2025-06-28 12:39:57'),(22,'Pending','Order has been placed and is awaiting confirmation','#ffc107',1,1,'2025-06-28 12:42:05'),(23,'Processing','Order is being processed and prepared for shipping','#17a2b8',1,2,'2025-06-28 12:42:05'),(24,'Shipped','Order has been shipped from our warehouse','#28a745',1,3,'2025-06-28 12:42:05'),(25,'In Transit','Order is in transit to delivery location','#6f42c1',1,4,'2025-06-28 12:42:05'),(26,'Out for Delivery','Order is out for delivery to your address','#fd7e14',1,5,'2025-06-28 12:42:05'),(27,'Delivered','Order has been successfully delivered','#20c997',1,6,'2025-06-28 12:42:05'),(28,'Canceled','Order has been canceled','#dc3545',1,7,'2025-06-28 12:42:05'),(29,'Pending','Order has been placed and is awaiting confirmation','#ffc107',1,1,'2025-06-28 12:42:19'),(30,'Processing','Order is being processed and prepared for shipping','#17a2b8',1,2,'2025-06-28 12:42:19'),(31,'Shipped','Order has been shipped from our warehouse','#28a745',1,3,'2025-06-28 12:42:19'),(32,'In Transit','Order is in transit to delivery location','#6f42c1',1,4,'2025-06-28 12:42:19'),(33,'Out for Delivery','Order is out for delivery to your address','#fd7e14',1,5,'2025-06-28 12:42:19'),(34,'Delivered','Order has been successfully delivered','#20c997',1,6,'2025-06-28 12:42:19'),(35,'Canceled','Order has been canceled','#dc3545',1,7,'2025-06-28 12:42:19'),(36,'Pending','Order has been placed and is awaiting confirmation','#ffc107',1,1,'2025-06-28 12:47:20'),(37,'Processing','Order is being processed and prepared for shipping','#17a2b8',1,2,'2025-06-28 12:47:20'),(38,'Shipped','Order has been shipped from our warehouse','#28a745',1,3,'2025-06-28 12:47:20'),(39,'In Transit','Order is in transit to delivery location','#6f42c1',1,4,'2025-06-28 12:47:20'),(40,'Out for Delivery','Order is out for delivery to your address','#fd7e14',1,5,'2025-06-28 12:47:20'),(41,'Delivered','Order has been successfully delivered','#20c997',1,6,'2025-06-28 12:47:20'),(42,'Canceled','Order has been canceled','#dc3545',1,7,'2025-06-28 12:47:20');
/*!40000 ALTER TABLE `order_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_zone_id` int(11) DEFAULT NULL,
  `billing_state` varchar(50) DEFAULT NULL,
  `billing_city` varchar(50) DEFAULT NULL,
  `billing_pincode` varchar(10) DEFAULT NULL,
  `status` enum('pending','confirmed','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `tracking_id` varchar(50) DEFAULT NULL,
  `external_tracking_id` varchar(100) DEFAULT NULL,
  `external_tracking_link` text DEFAULT NULL,
  `order_status_id` int(11) DEFAULT 1,
  `payment_status` enum('pending','paid','unpaid','failed','refunded') DEFAULT 'pending',
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `gst_number` varchar(20) DEFAULT NULL,
  `company_name` varchar(100) DEFAULT NULL,
  `is_business_purchase` tinyint(1) DEFAULT 0,
  `status_description` text DEFAULT NULL,
  `estimated_delivery_date` date DEFAULT NULL,
  `actual_delivery_date` date DEFAULT NULL,
  `upi_transaction_id` varchar(100) DEFAULT NULL,
  `upi_screenshot` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (12,1,NULL,NULL,'ORDER202507088016',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-08 08:56:06','Everythingb2c66236680',NULL,NULL,1,'pending','order_QqVyxPfVtgAjfJ',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(13,1,NULL,NULL,'ORDER202507083957',41.30,35.00,6.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-08 08:57:10','Everythingb2c61103209',NULL,NULL,1,'pending','order_QqW04auv0iB84m',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(14,1,NULL,NULL,'ORDER202507082717',112.10,95.00,17.10,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-08 09:07:20','Everythingb2c47141244',NULL,NULL,1,'paid','order_QqWAnXnE5OTBtV','pay_QqWB1WsU6Emezq',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(15,1,NULL,NULL,'ORDER202507087112',112.10,95.00,17.10,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 09:19:04','Everythingb2c55889181',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(16,1,NULL,NULL,'ORDER202507083491',483.80,410.00,73.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 09:21:23','Everythingb2c03110318','','',1,'paid',NULL,NULL,NULL,NULL,NULL,0,'',NULL,NULL,NULL,NULL),(17,1,NULL,NULL,'ORDER202507083976',123.90,105.00,18.90,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 10:33:09','Everythingb2c39930075',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(18,1,NULL,NULL,'ORDER202507085520',123.90,105.00,18.90,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 10:34:08','Everythingb2c95007545',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(19,1,NULL,11,'ORDER202507091088',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 10:47:33','Everythingb2c81543477',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(20,1,NULL,10,'ORDER202507091739',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 13:55:43','Everythingb2c45276575',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(21,1,NULL,11,'ORDER202507098748',483.80,410.00,73.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 13:58:23','Everythingb2c77567796',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(22,1,NULL,11,'ORDER202507097304',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:04:45','Everythingb2c08456249',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(23,1,NULL,11,'ORDER202507092077',289.10,245.00,44.10,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:09:30','Everythingb2c34380893',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(24,1,NULL,11,'ORDER202507099621',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 14:16:11','Everythingb2c27316660',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(26,1,NULL,11,'',445.24,318.00,57.24,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:19:29',NULL,NULL,NULL,1,'paid','order_Qr01iDO4UxsRRl','pay_Qr020GbZ978g5k',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(28,1,NULL,10,'ORDER202507099782',236.00,200.00,36.00,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:22:54','Everythingb2c11526692',NULL,NULL,1,'paid','order_Qr05J4DKD8JdpH','pay_Qr05XJ2YvzqdRO',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(29,1,NULL,11,'ORDER202507093476',553.80,410.00,73.80,70.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:25:47','Everythingb2c34216156',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(30,1,NULL,11,'ORDER202507099997',553.80,410.00,73.80,70.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:25:54','Everythingb2c12999204',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(31,1,NULL,11,'ORDER202507092005',553.80,410.00,73.80,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:26:26','Everythingb2c70593336',NULL,NULL,1,'paid','order_Qr093IhEB0E0ou','pay_Qr09JIfa6ymA01',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(32,1,NULL,11,'ORDER202507091604',406.30,285.00,51.30,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:28:42','Everythingb2c21194149','TRCK001','https://google.com',34,'paid','order_Qr0BRB2Xh1z0k7','pay_Qr0Beaz5CpNc2e',NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(33,1,NULL,11,'ORDER202507092941',820.48,636.00,114.48,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 15:03:12','Everythingb2c44660028',NULL,NULL,1,'paid','order_Qr0ltzik1brFYj','pay_Qr0m7q8XLupiWq',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(34,1,NULL,11,'ORDER202507097506',1612.26,1307.00,235.26,70.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 15:19:36','Everythingb2c49775529',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(35,1,NULL,10,'ORDER202507094251',206.50,175.00,31.50,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 16:27:35','Everythingb2c77827159',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(36,1,NULL,10,'ORDER202507094409',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 16:30:58','Everythingb2c19101886',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(37,1,NULL,10,'ORDER202507097085',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 16:31:31','Everythingb2c43079069',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(38,1,NULL,10,'ORDER202507094606',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 16:31:53','Everythingb2c42563697',NULL,NULL,1,'paid','order_Qr2HX6uc6IjXAP','pay_Qr2HsHpxgP2wS9',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(39,1,NULL,11,'ORDER202507099260',475.24,318.00,57.24,100.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 18:39:21','Everythingb2c37464001',NULL,NULL,1,'paid','order_Qr4SAWGDAmP7FL','pay_Qr4SRNBMWDGgPU',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(40,1,NULL,10,'ORDER202507091693',599.44,508.00,91.44,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 18:51:41','Everythingb2c49484736',NULL,NULL,1,'paid','order_Qr4fCURPLFQmWI','pay_Qr4fRMSQkqD9qX',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(41,1,NULL,11,'ORDER202507098897',475.24,318.00,57.24,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 19:15:29','Everythingb2c15029790',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(42,1,NULL,11,'ORDER202507092585',288.80,160.00,28.80,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 19:51:21','Everythingb2c26118502',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(43,1,NULL,11,'ORDER202507104883',1298.88,1016.00,182.88,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-10 05:11:04','Everythingb2c36327833',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(44,1,NULL,10,'ORDER202507132525',1895.08,1606.00,289.08,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-13 06:40:38','Everythingb2c34482495',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(45,1,NULL,10,'ORDER202507166754',247.80,210.00,37.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'direct_payment','2025-07-16 13:38:58','Everythingb2c30742473',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,'txn_12345678trd',NULL),(46,1,NULL,10,'ORDER202507228961',1036.04,878.00,158.04,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-22 17:57:28','EverythingB2C74666831',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(47,1,NULL,10,'ORDER202507262876',1030.14,873.00,157.14,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-26 14:22:13','EverythingB2C56758332',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(48,1,NULL,10,'ORDER202507299494',4201.00,4201.00,706.98,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-29 15:39:03','EverythingB2C26475301','D1005560078','',1,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(49,4,NULL,12,'ORDER202507296952',295.00,295.00,53.10,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-29 15:49:02','EverythingB2C41600018',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(50,4,NULL,13,'ORDER202507296039',490.00,390.00,70.20,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-29 17:54:09','EverythingB2C15663702',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(51,5,NULL,14,'ORDER202508173761',513.00,513.00,92.34,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-08-17 10:19:58','EverythingB2C48972865','D3003398857','',3,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(52,6,NULL,15,'ORDER202508279563',260.00,160.00,28.80,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-08-27 15:45:18','EverythingB2C49310619','D3003398857','',1,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(53,5,NULL,14,'ORDER202509144626',280.00,280.00,50.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-14 16:47:57','EverythingB2C70958473','7D154319924','',1,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(54,5,NULL,14,'ORDER202509222629',408.00,408.00,73.44,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-22 06:04:22','EverythingB2C95022888',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(55,5,NULL,14,'ORDER202509227857',215.00,215.00,38.70,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-22 06:09:24','EverythingB2C05414971',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(56,5,NULL,14,'ORDER202509235384',568.00,568.00,102.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-23 15:13:18','EverythingB2C42141670',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(57,5,NULL,14,'ORDER202509274935',435.00,435.00,78.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:03:00','EverythingB2C55652995',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(58,5,NULL,14,'ORDER202509275880',210.00,210.00,37.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:04:10','EverythingB2C58880258',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(59,5,NULL,14,'ORDER202509278874',180.00,180.00,32.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:44:00','EverythingB2C46307437',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(60,1,NULL,10,'ORDER202509276184',538.00,538.00,96.84,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:53:23','EverythingB2C86135192','D0005640777','',3,'pending',NULL,NULL,NULL,'',NULL,0,'','2026-02-13',NULL,NULL,NULL),(61,10,NULL,16,'ORDER202512229031',180.00,180.00,32.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-12-22 05:29:14','EverythingB2C27347219',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(62,12,NULL,NULL,'ORDER202601313627',418.00,318.00,57.24,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-01-31 06:43:06','EverythingB2C88217404',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(63,12,NULL,NULL,'ORDER202601310622',1420.00,1320.00,237.60,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-01-31 06:47:49','EverythingB2C40020212','D0005640777','',31,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(64,1,NULL,10,'ORDER202602073532',180.00,180.00,32.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-07 15:43:08','EverythingB2C06672299',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(65,1,NULL,10,'ORDER202602079897',918.00,918.00,165.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-07 15:43:32','EverythingB2C23627084',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(66,14,NULL,NULL,'ORDER202602112074',918.00,918.00,165.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-11 09:21:53','EverythingB2C04296840',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(67,14,NULL,19,'ORDER202602138774',35.00,35.00,6.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-13 10:01:48','EverythingB2C56713881',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(68,1,NULL,10,'ORDER202602170426',1458.00,1458.00,262.44,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-17 10:10:31','EverythingB2C71461666',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(69,1,NULL,10,'ORDER202602173100',285.00,285.00,51.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'direct_payment','2026-02-17 14:17:34','EverythingB2C98682869',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,'T123456789',NULL),(70,5,NULL,14,'ORDER202602180174',990.00,990.00,178.20,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-18 05:54:28','EverythingB2C75404818',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(71,1,NULL,10,'ORDER202602191783',1836.00,1836.00,330.48,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'direct_payment','2026-02-19 12:00:57','EverythingB2C14764343',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,'txn_12345678trd',NULL),(72,1,NULL,10,'ORDER202602221547',1836.00,1836.00,330.48,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:44:28','EverythingB2C37600793',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(73,1,NULL,10,'ORDER202602228555',318.00,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:48:33','EverythingB2C26897791',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(74,1,NULL,10,'ORDER202602228872',318.00,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:51:03','EverythingB2C49607989',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(75,1,NULL,10,'ORDER202602222825',318.00,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:53:02','EverythingB2C70855919',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(76,12,NULL,NULL,'ORDER202602231022',190.00,90.00,16.20,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-23 10:57:34','EverythingB2C07453030',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('cod','razorpay') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) DEFAULT 'INR',
  `status` enum('pending','success','failed','refunded') DEFAULT 'pending',
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `transaction_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transaction_data`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_transactions`
--

LOCK TABLES `payment_transactions` WRITE;
/*!40000 ALTER TABLE `payment_transactions` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popup_settings`
--

DROP TABLE IF EXISTS `popup_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popup_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `popup_settings`
--

LOCK TABLES `popup_settings` WRITE;
/*!40000 ALTER TABLE `popup_settings` DISABLE KEYS */;
INSERT INTO `popup_settings` VALUES (1,'popup_enabled','1','2025-08-12 09:54:08','2025-08-12 09:54:08'),(2,'popup_title','Check Delivery Availability','2025-08-12 09:54:08','2025-08-12 09:54:08'),(3,'popup_message','We Deliver Orders In Maharashtra, Gujarat, Bangalore, And Hyderabad Only.','2025-08-12 09:54:08','2025-08-12 09:54:08'),(4,'popup_instruction','Please Enter Your Pincode To Check Delivery Availability.','2025-08-12 09:54:08','2025-08-12 09:54:08'),(5,'service_available_message','Great! We deliver to your area.','2025-08-12 09:54:08','2025-08-12 09:54:08'),(6,'service_unavailable_message','We are not providing service to this area.','2025-08-12 09:54:08','2025-08-12 09:54:08');
/*!40000 ALTER TABLE `popup_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (6,3,'Home page/Products Offering Discount Images/P3.webp',1,1),(7,4,'Home page/Products Offering Discount Images/P4.webp',1,1),(8,5,'Home page/Products Offering Discount Images/P5.webp',1,1),(131,61,'uploads/products/68a6d64a1891f.png',1,1),(132,59,'uploads/products/68a6d680ab658.png',1,1),(133,58,'uploads/products/68a6d6bd3aa93.png',0,1),(134,57,'uploads/products/68a6d6d9ef7e8.png',1,1),(135,56,'uploads/products/68a6d6f33d268.png',0,1),(136,55,'uploads/products/68a6d70eb20dc.png',0,1),(137,52,'uploads/products/68a6d756231a9.jpg',0,1),(138,52,'uploads/products/68a6d7562336e.jpg',0,2),(139,52,'uploads/products/68a6d7562352a.jpg',0,3),(140,38,'uploads/products/68a6d85a0a0b9.png',0,1),(141,38,'uploads/products/68a6d85a0a32e.png',0,2),(142,38,'uploads/products/68a6d85a0a4c5.png',0,3),(143,38,'uploads/products/68a6d85a0a731.png',0,4),(144,37,'uploads/products/68a6d8845b5ff.png',0,1),(145,37,'uploads/products/68a6d8845b7d9.png',0,2),(146,37,'uploads/products/68a6d8845b93b.png',0,3),(147,37,'uploads/products/68a6d8845bae6.png',0,4),(148,36,'uploads/products/68a6d8ae16e17.png',0,1),(149,36,'uploads/products/68a6d8ae17132.png',0,2),(150,36,'uploads/products/68a6d8ae173ce.png',0,3),(151,36,'uploads/products/68a6d8ae17656.png',0,4),(152,35,'uploads/products/68a6d8daa1382.png',0,1),(153,35,'uploads/products/68a6d8daa15cb.png',0,2),(154,35,'uploads/products/68a6d8daa1745.png',0,3),(155,35,'uploads/products/68a6d8daa1952.png',0,4),(156,34,'uploads/products/68a6d90a51cb9.png',0,1),(157,34,'uploads/products/68a6d90a51f62.png',0,2),(158,34,'uploads/products/68a6d90a521c0.png',0,3),(159,34,'uploads/products/68a6d90a52461.png',0,4),(160,33,'uploads/products/68a6d93433ce8.png',0,1),(161,33,'uploads/products/68a6d93433ee6.png',0,2),(162,33,'uploads/products/68a6d934341c7.png',0,3),(163,33,'uploads/products/68a6d9343442b.png',0,4),(164,31,'uploads/products/68a6d964e9bb3.jpg',0,1),(165,31,'uploads/products/68a6d964e9e5d.jpg',0,2),(166,31,'uploads/products/68a6d964ea09a.jpg',0,3),(167,30,'uploads/products/68a6d98bb98c1.jpg',0,1),(168,30,'uploads/products/68a6d98bb9b44.jpg',0,2),(169,30,'uploads/products/68a6d98bb9d71.jpg',0,3),(170,30,'uploads/products/68a6d98bba034.jpg',0,4),(171,29,'uploads/products/68a6d9b188987.jpg',0,1),(172,29,'uploads/products/68a6d9b188bc0.jpg',0,2),(173,29,'uploads/products/68a6d9b188d82.jpg',0,3),(174,29,'uploads/products/68a6d9b188fd8.jpg',0,4),(175,28,'uploads/products/68a6d9d611d17.jpg',0,1),(176,28,'uploads/products/68a6d9d611f67.jpg',0,2),(177,28,'uploads/products/68a6d9d612171.jpg',0,3),(178,28,'uploads/products/68a6d9d6123de.jpg',0,4),(179,27,'uploads/products/68a6da0d03c04.jpg',0,1),(180,27,'uploads/products/68a6da0d03f75.jpg',0,2),(181,27,'uploads/products/68a6da0d041f7.jpg',0,3),(182,27,'uploads/products/68a6da0d0451b.jpg',0,4),(183,22,'uploads/products/68a6db5575211.png',0,1),(184,22,'uploads/products/68a6db5575400.png',0,2),(185,22,'uploads/products/68a6db5575696.png',0,3),(186,22,'uploads/products/68a6db5575856.png',0,4),(187,21,'uploads/products/68a6db79b5ac0.png',0,1),(188,21,'uploads/products/68a6db79b5dd4.png',0,2),(189,20,'uploads/products/68a6db9681b86.png',0,1),(190,19,'uploads/products/68a6dbb34d29c.png',0,1),(191,19,'uploads/products/68a6dbb34d500.png',0,2),(192,18,'uploads/products/68a6dbd1cf939.png',0,1),(193,18,'uploads/products/68a6dbd1cfb19.png',0,2);
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `hsn` varchar(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `mrp` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) NOT NULL,
  `discount_percentage` int(11) DEFAULT 0,
  `gst_type` enum('sgst_cgst','igst') NOT NULL DEFAULT 'sgst_cgst',
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `shipping_charge` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `main_image` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_approved` tinyint(1) DEFAULT 1,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_discounted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `max_quantity_per_order` int(11) DEFAULT NULL COMMENT 'Maximum quantity allowed per order. NULL means no limit.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (3,NULL,'JK COPIER A4 Size Paper','jk-copier-a4-size-paper','PR003','12345678','Premium A4 size copier paper',310.00,290.00,6,'sgst_cgst',18.00,NULL,2,'Home page/Products Offering Discount Images/P3.webp',75,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(4,NULL,'JK Cedar A4 Size Paper','jk-cedar-a4-size-paper','PR004','12345678','Cedar brand A4 size paper',450.00,318.00,29,'sgst_cgst',18.00,NULL,2,'Home page/Products Offering Discount Images/P4.webp',30,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(5,NULL,'JK Easy A4 Size Paper','jk-easy-a4-size-paper','PR005','12345678','Easy brand A4 size paper',290.00,280.00,3,'sgst_cgst',18.00,NULL,2,'Home page/Products Offering Discount Images/P5.webp',60,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(6,NULL,'Drain It Drain Cleaner Powder','drain-it-drain-cleaner-powder','PR006','12345678','Effective drain cleaning powder',150.00,120.00,20,'sgst_cgst',18.00,NULL,3,'asset/household and cleaning/page1/Drain It Drain Cleaner Powder.webp',40,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(7,NULL,'Duster Big Heavy','duster-big-heavy','PR007','12345678','Heavy duty duster for cleaning',80.00,65.00,19,'sgst_cgst',18.00,NULL,3,'asset/household and cleaning/page1/Duster Big Heavy.webp',25,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(8,NULL,'Floor Duster','floor-duster','PR008','12345678','Professional floor duster',120.00,95.00,21,'sgst_cgst',18.00,NULL,3,'asset/household and cleaning/page1/Floor Duster.webp',35,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(9,NULL,'Disposable Gloves Plastic','disposable-gloves-plastic','PR009','12345678','Plastic disposable gloves',200.00,160.00,20,'sgst_cgst',18.00,NULL,1,'asset/kitchen/Disposable Gloves Plastic 80 Pcs.-2nd.webp',80,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(10,NULL,'Exo Anti-Bacterial Dishwash Bar','exo-anti-bacterial-dishwash-bar','PR010','12345678','Anti-bacterial dishwashing bar',45.00,35.00,22,'sgst_cgst',18.00,NULL,1,'asset/kitchen/Exo Anti-Bacterial Dishwash Bar.webp',100,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL),(14,NULL,'Clear Lens Safety Goggle  Punk Type Generic','clear-lens-safety-goggle-punk-type-generic','EYP PUSGCL 010','90041000','Clear Lens Safety Goggle\r\nPunk Type Generic',34.00,28.00,18,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dc0e93b60.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:06:34',10),(15,NULL,'Clear Lens Safety Goggle  Udyogi UD 71','clear-lens-safety-goggle-udyogi-ud-71','EYP UD71 020','12345678','Clear Lens Safety Goggle\r\nUdyogi UD 71',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbffaffbd.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:09:37',10),(16,NULL,'Clear Lens Safety Goggle  Udyogi UD 91','clear-lens-safety-goggle-udyogi-ud-91','EYP UD91 030','12345678','Clear Lens Safety Goggle\r\nUdyogi UD 91',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbf0c5f34.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:10:32',10),(17,NULL,'Over-The-Spec Safety Goggle   Udyogi UD 30','over-the-spec-safety-goggle-udyogi-ud-30','EYP UD30 040','12345678','Over-The-Spec Safety Goggle\r\n Udyogi UD 30',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbe162dd4.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:11:49',10),(18,NULL,'Clear Lens Safety Goggle  Venus E 102','clear-lens-safety-goggle-venus-e-102','EYP E102 050','12345678','Clear Lens Safety Goggle\r\nVenus E 102',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbd1cf74c.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:13:01',10),(19,NULL,'Stylish Anti-Fog Safety Goggle  Venus E 306','stylish-anti-fog-safety-goggle-venus-e-306','EYP E306 060','12345678','Stylish Anti-Fog Safety Goggle\r\nVenus E 306',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbb34cf8c.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:15:24',10),(20,NULL,'Over-The-Spec Safety Goggle  Venus E 603','over-the-spec-safety-goggle-venus-e-603','EYP E603 070','12345678','Over-The-Spec Safety Goggle\r\nVenus E 603',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6db96818ef.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:16:26',10),(21,NULL,'Chemical Splash Protection Goggle  Venus E 503','chemical-splash-protection-goggle-venus-e-503','EYP E503 080','12345678','Chemical Splash Protection Goggle\r\nVenus E 503',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6db79b5836.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:17:35',10),(22,NULL,'Clear Lens Safety Goggle  Karam ES 001','clear-lens-safety-goggle-karam-es-001','EYP ES01CL 020','90049090','Clear Lens Safety Goggle\r\nKaram ES 001',90.00,57.00,37,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6db5574f40.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:21:35',100),(23,NULL,'Smoke Lens Safety Goggle  Karam ES 001','smoke-lens-safety-goggle-karam-es-001','EYP ES01BK 021','90049090','Smoke Lens Safety Goggle\r\nKaram ES 001',93.00,60.00,35,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6daf9069de.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:22:46',10),(24,NULL,'Executive Clear Lens Safety Goggle  Karam ES 005','executive-clear-lens-safety-goggle-karam-es-005','EYP ES05 110','12345678','Executive Clear Lens Safety Goggle\r\nKaram ES 005',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dae9c3ccc.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:23:36',10),(25,NULL,'Executive Smoke Lens Safety Goggle  Karam ES 005','executive-smoke-lens-safety-goggle-karam-es-005','EYP ES05 120','12345678','Executive Smoke Lens Safety Goggle\r\nKaram ES 005',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dada3a6c1.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:24:41',10),(26,NULL,'Over-The-Spec Safety Goggle  Karam ES 007','over-the-spec-safety-goggle-karam-es-007','EYP ES07 130','12345678','Over-The-Spec Safety Goggle\r\nKaram ES 007',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dacbe97d3.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:25:58',10),(27,NULL,'Labour Safety Shoes  Model - ROCK','labour-safety-shoes-model-rock','FTP ROCK 010','12345678','Labour Safety Shoes\r\nModel - ROCK',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6da0d0389d.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:29:22',10),(28,NULL,'ISI Safety Shoes  Model - POWER PLUS','isi-safety-shoes-model-power-plus','FTP PLUS 020','12345678','ISI Safety Shoes\r\nModel - POWER PLUS',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d9d6119aa.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:30:57',10),(29,NULL,'Labour Safety Shoes  Model - DÉCOR SAFE','labour-safety-shoes-model-d-cor-safe','FTP SAFE 030','12345678','Labour Safety Shoes\r\nModel - DÉCOR SAFE',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d9b1886a0.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:32:28',10),(30,NULL,'ISI Safety Shoes  Model - BESTGO ISI','isi-safety-shoes-model-bestgo-isi','FTP BEST 040','12345678','ISI Safety Shoes\r\nModel - BESTGO ISI',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d98bb95b6.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:35:22',10),(31,NULL,'Executive ISI Safety Shoes  Model - AWSOME','executive-isi-safety-shoes-model-awsome','FTP AWSM 050','12345678','Executive ISI Safety Shoes\r\nModel - AWSOME',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d964e985a.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:37:05',10),(32,NULL,'Ladies Safety Shoes','ladies-safety-shoes','FTP LADY 060','12345678','Ladies Safety Shoes',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d94856e1c.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:38:27',10),(33,NULL,'Gum Boots  Model - BUSY, Height - 11','gum-boots-model-busy-height-11','FTP BUSY 070','12345678','Gum Boots\r\nModel - BUSY, Height - 11\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d93433a43.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:47:04',10),(34,NULL,'Gum Boots  Model - CHOTA GHODA, Height - 11','gum-boots-model-chota-ghoda-height-11','FTP CHGH 080','12345678','Gum Boots\r\nModel - CHOTA GHODA, Height - 11\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d90a5198a.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:48:11',10),(35,NULL,'Gum Boots  Model - CLOUD WITH STEEL TOE ISI, Height - 14','gum-boots-model-cloud-with-steel-toe-isi-height-14','FTP CLUD 090','12345678','Gum Boots\r\nModel - CLOUD WITH STEEL TOE ISI, Height - 14\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8daa1113.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:50:58',10),(36,NULL,'Gum Boots Model - CLOUD W/O STEEL TOE ISI, Height - 14','gum-boots-model-cloud-w-o-steel-toe-isi-height-14','FTP CLUD 100','12345678','Gum Boots\r\nModel - CLOUD W/O STEEL TOE ISI, Height - 14\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8ae16ab5.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:53:26',10),(37,NULL,'Gum Boots  Model - RAINFALL WITH STEEL TOE ISI, Height - 16','gum-boots-model-rainfall-with-steel-toe-isi-height-16','FTP RAIN 110','12345678','Gum Boots\r\nModel - RAINFALL WITH STEEL TOE ISI, Height - 16\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8845b3cd.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:56:07',10),(38,NULL,'Gum Boots  Model - RAINFALL W/O STEEL TOE ISI, Height - 16','gum-boots-model-rainfall-w-o-steel-toe-isi-height-16','FTP RAIN 120','12345678','Gum Boots\r\nModel - RAINFALL W/O STEEL TOE ISI, Height - 16\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d85a09e02.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:57:28',10),(39,NULL,'Shoe Cover  Plastic Disposable','shoe-cover-plastic-disposable','FTP COVR 130','12345678','Shoe Cover\r\nPlastic Disposable',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8315b44d.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:00:10',10),(40,NULL,'ESD Slipper','esd-slipper','FTP ESDS 140','12345678','ESD Slipper',100.00,90.00,10,'sgst_cgst',12.00,NULL,17,'uploads/products/68a6d82534f36.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:00:52',10),(41,NULL,'Leather Leg Guard  ESAB','leather-leg-guard-esab','FTP ESAB 150','12345678','Leather Leg Guard\r\nESAB',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d817a36f9.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:01:40',10),(42,NULL,'Safety Helmet Nape Type  Generic ISI','safety-helmet-nape-type-generic-isi','HED NAPP 010','12345678','Safety Helmet Nape Type\r\nGeneric ISI',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7eb85405.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:05:15',10),(43,NULL,'Safety Helmet Loader  Generic','safety-helmet-loader-generic','HED LOAD 020','12345678','Safety Helmet Loader\r\nGeneric',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7dec6894.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:06:12',10),(44,NULL,'Safety Helmet Nape Type  Karam PN501 White','safety-helmet-nape-type-karam-pn501-white','HED 0501 030','12345678','Safety Helmet Nape Type\r\nKaram PN501 White',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7cd844f2.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:07:26',10),(45,NULL,'Safety Helmet Nape Type  Karam PN501 Grey','safety-helmet-nape-type-karam-pn501-grey','HED 0501 040','12345678','Safety Helmet Nape Type\r\nKaram PN501 Grey',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7ad8c9ea.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:08:16',10),(46,NULL,'Safety Helmet Nape Type  Karam PN501 Blue','safety-helmet-nape-type-karam-pn501-blue','HED 0501 050','12345678','Safety Helmet Nape Type\r\nKaram PN501 Blue',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7a265513.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:09:36',10),(47,NULL,'Safety Helmet Nape Type  Karam PN501 Yellow','safety-helmet-nape-type-karam-pn501-yellow','HED 0501 060','12345678','Safety Helmet Nape Type\r\nKaram PN501 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d797d9f9e.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:11:32',10),(48,NULL,'Safety Helmet Ratchet Type  Karam PN521 White','safety-helmet-ratchet-type-karam-pn521-white','HED 0521 070','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 White',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d78b5a01f.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:12:56',10),(49,NULL,'Safety Helmet Ratchet Type  Karam PN521 Grey','safety-helmet-ratchet-type-karam-pn521-grey','HED 0521 080','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 Grey',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d77d884d6.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:13:53',10),(50,NULL,'Safety Helmet Ratchet Type  Karam PN521 Blue','safety-helmet-ratchet-type-karam-pn521-blue','HED 0521 090','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 Blue',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d77229dd2.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:14:40',10),(51,NULL,'Safety Helmet Ratchet Type  Karam PN521 Yellow','safety-helmet-ratchet-type-karam-pn521-yellow','HED 0521 100','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d767e7816.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:16:06',10),(52,NULL,'Safety Helmet Nape Type  Udyogi UI1211 Yellow','safety-helmet-nape-type-udyogi-ui1211-yellow','HED 1211 110','12345678','Safety Helmet Nape Type\r\nUdyogi UI1211 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d75622fac.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:18:18',10),(53,NULL,'Safety Helmet Ratchet Type  Udyogi Ultra 5000 Yellow','safety-helmet-ratchet-type-udyogi-ultra-5000-yellow','HED 5000 120','12345678','Safety Helmet Ratchet Type\r\nUdyogi Ultra 5000 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d732e6d3d.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:19:03',10),(54,NULL,'Disposable Bouffant Cap  Generic','disposable-bouffant-cap-generic','HED BCAP 130','12345678','Disposable Bouffant Cap\r\nGeneric',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d723e3cf6.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:20:12',10),(55,NULL,'Full Body Harness Single Lanyard  Model - E01','full-body-harness-single-lanyard-model-e01','SFB E001 010','12345678','Full Body Harness Single Lanyard\r\nModel - E01',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d70eb1df1.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:13:52',10),(56,NULL,'Full Body Harness Double Lanyard  Model - E02','full-body-harness-double-lanyard-model-e02','SFB E002 020','12345678','Full Body Harness Double Rope\r\nModel - E02',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6f33cfde.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:15:31',10),(57,NULL,'Full Body Harness Single Rope Shock Absorber  Model - E01','full-body-harness-single-rope-shock-absorber-model-e01','SFB E01A 030','12345678','Full Body Harness Single Rope Shock Absorber\r\nModel - E01',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6d1ba52d.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:16:51',10),(58,NULL,'Full Body Harness Double Rope Shock Absorber  Model - E02','full-body-harness-double-rope-shock-absorber-model-e02','SFB E02A 040','12345678','Full Body Harness Double Rope Shock Absorber\r\nModel - E02',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6bd3a820.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:17:51',10),(59,NULL,'Half Body Harness Single Rope  Model - HB1','half-body-harness-single-rope-model-hb1','SFB HB01 050','12345678','Half Body Harness Single Rope\r\nModel - HB1',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d678714b6.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:19:15',10),(60,NULL,'Full Body Harness Double Rope Shock Absorber  Model - Udyogi Eco 04','full-body-harness-double-rope-shock-absorber-model-udyogi-eco-04','SFB ECO4 060','12345678','Full Body Harness Double Rope Shock Absorber\r\nModel - Udyogi Eco 04',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6645c9df.jpg',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:20:51',10),(61,NULL,'Ear Plug  Venus N-101','ear-plug-venus-n-101','EAR N101 010','12345678','Ear Plug\r\nVenus N-101',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d641f326d.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:23:25',10),(62,NULL,'Ear Plug  3M','ear-plug-3m','EAR 3MEP 020','12345678','Ear Plug\r\n3M',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d63090c5b.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:24:08',10),(63,NULL,'Earmuff  Venus N-510','earmuff-venus-n-510','EAR N510 030','12345678','Earmuff\r\nVenus N-510',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d622f019f.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:26:09',10),(64,NULL,'Earmuff  Venus N-530','earmuff-venus-n-530','EAR N530 040','12345678','Earmuff\r\nVenus N-530',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d616bc1a2.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:26:57',10),(65,NULL,'Earmuff  Venus N-550','earmuff-venus-n-550','EAR N550 050','12345678','Earmuff\r\nVenus N-550',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d60b159bc.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:27:59',10),(66,NULL,'Earmuff  Venus N-555','earmuff-venus-n-555','EAR N555 060','12345678','Earmuff\r\nVenus N-555',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d5fb2b955.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:28:54',10),(67,NULL,'Earmuff  Karam EP21','earmuff-karam-ep21','EAR EP21 070','12345678','Earmuff\r\nKaram EP21',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d5e8cfa73.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:29:48',10),(68,NULL,'Helmet Attachable Earmuff  Karam EP23','helmet-attachable-earmuff-karam-ep23','EAR EP23 080','12345678','Helmet Attachable Earmuff\r\nKaram EP23',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d5da2d98f.png',100,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:30:41',10),(69,2,'Classmate Book','classmate-book','BOOK1','HSN 1111','Classmate Book',100.00,90.00,10,'sgst_cgst',18.00,NULL,21,'uploads/products/6948d21b96f8a.jpg',100,1,1,'2025-12-22 05:18:46',1,'Please add HSN code.',0,0,'2025-12-22 05:07:39',NULL),(71,2,'note Book','note-book','BOOK1','90049090','hi',100.00,90.00,10,'sgst_cgst',0.00,NULL,3,NULL,100,1,1,'2026-01-02 06:16:25',1,NULL,0,0,'2025-12-31 13:26:07',NULL),(72,2,'gumboots','gumboots','EYP E102 050','12345678','hiii',100.00,90.00,10,'sgst_cgst',12.00,NULL,7,NULL,100,1,1,'2026-01-01 16:11:36',1,NULL,0,0,'2026-01-01 12:06:26',NULL),(73,2,'Gum Boots  Model - RAINFALL W/O STEEL n','gum-boots-model-rainfall-w-o-steel-n','EYP E306 060','12345678','nn',120.00,90.00,25,'sgst_cgst',18.00,NULL,17,NULL,100,1,0,NULL,NULL,NULL,0,0,'2026-01-02 06:13:02',NULL),(74,NULL,'Clear Anti-Fog Lens Goggle Karam ES001','clear-anti-fog-lens-goggle-karam-es001','EYP ES01AF 022','90049090','Clear Antifog Lens Goggle',123.00,95.00,23,'sgst_cgst',18.00,NULL,16,'uploads/products/696daf38d9941.png',100,1,1,NULL,NULL,NULL,1,1,'2026-01-19 04:12:40',100),(75,NULL,'Gas Welder\'s Goggle </br> Karam ES003','gas-welder-s-goggle-br-karam-es003','EYP ES03GW 023','90049090','Gas Welder\'s Goggle Karam ES003',1400.00,918.00,34,'sgst_cgst',18.00,NULL,16,NULL,10,1,1,NULL,NULL,NULL,1,1,'2026-01-19 05:10:30',10);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seller_order_items`
--

DROP TABLE IF EXISTS `seller_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_order_items` (
  `id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `tracking_id` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `order_total` decimal(10,2) DEFAULT NULL,
  `order_status_id` int(11) DEFAULT NULL,
  `order_status` varchar(100) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `seller_id` int(11) DEFAULT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `gst_amount` decimal(10,2) DEFAULT NULL,
  `item_total` decimal(20,2) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller_order_items`
--

LOCK TABLES `seller_order_items` WRITE;
/*!40000 ALTER TABLE `seller_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `seller_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `serviceable_pincodes`
--

DROP TABLE IF EXISTS `serviceable_pincodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serviceable_pincodes` (
  `id` int(11) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `serviceable_pincodes`
--

LOCK TABLES `serviceable_pincodes` WRITE;
/*!40000 ALTER TABLE `serviceable_pincodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `serviceable_pincodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_charges`
--

DROP TABLE IF EXISTS `shipping_charges`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_charges` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `charge_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `charge_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_order_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_charges`
--

LOCK TABLES `shipping_charges` WRITE;
/*!40000 ALTER TABLE `shipping_charges` DISABLE KEYS */;
INSERT INTO `shipping_charges` VALUES (2,2,'fixed',100.00,0.00,NULL,1,'2025-06-28 06:57:09'),(3,3,'fixed',0.00,499.00,NULL,1,'2025-06-28 06:57:09'),(4,4,'fixed',200.00,0.00,NULL,1,'2025-06-28 06:57:09'),(7,1,'fixed',70.00,0.00,NULL,1,'2025-07-09 08:02:30');
/*!40000 ALTER TABLE `shipping_charges` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_zone_locations`
--

DROP TABLE IF EXISTS `shipping_zone_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_zone_locations` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `location_type` enum('country','state','city','pincode') NOT NULL,
  `location_value` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zone_locations`
--

LOCK TABLES `shipping_zone_locations` WRITE;
/*!40000 ALTER TABLE `shipping_zone_locations` DISABLE KEYS */;
INSERT INTO `shipping_zone_locations` VALUES (1,1,'state','Maharashtra','2025-06-28 06:57:09'),(2,2,'state','Delhi','2025-06-28 06:57:09'),(3,2,'state','Karnataka','2025-06-28 06:57:09'),(4,2,'state','Tamil Nadu','2025-06-28 06:57:09'),(5,2,'state','Gujarat','2025-06-28 06:57:09'),(6,2,'state','Rajasthan','2025-06-28 06:57:09'),(7,2,'state','Uttar Pradesh','2025-06-28 06:57:09'),(8,2,'state','West Bengal','2025-06-28 06:57:09'),(9,2,'state','Telangana','2025-06-28 06:57:09'),(10,2,'state','Andhra Pradesh','2025-06-28 06:57:09'),(11,2,'state','Kerala','2025-06-28 06:57:09'),(12,3,'city','Mumbai','2025-06-28 06:57:09'),(13,3,'city','Pune','2025-06-28 06:57:09'),(14,3,'city','Nagpur','2025-06-28 06:57:09'),(15,4,'city','Srinagar','2025-06-28 06:57:09'),(16,4,'city','Leh','2025-06-28 06:57:09'),(17,4,'city','Port Blair','2025-06-28 06:57:09');
/*!40000 ALTER TABLE `shipping_zone_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shipping_zones`
--

DROP TABLE IF EXISTS `shipping_zones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shipping_zones`
--

LOCK TABLES `shipping_zones` WRITE;
/*!40000 ALTER TABLE `shipping_zones` DISABLE KEYS */;
INSERT INTO `shipping_zones` VALUES (1,'Standard Shipping (Within Maharashtra)','Deliveries within the same state (SGST + CGST)',1,'2025-06-28 06:57:09'),(2,'National Shipping (Other States)','Deliveries to different states (IGST)',1,'2025-06-28 06:57:09'),(3,'Free Shipping – Metro Cities','Areas with free shipping',1,'2025-06-28 06:57:09'),(4,'Remote Area Shipping','Areas with premium shipping charges',1,'2025-06-28 06:57:09');
/*!40000 ALTER TABLE `shipping_zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_role` enum('customer','seller','admin') DEFAULT 'customer',
  `is_seller_approved` tinyint(1) DEFAULT 0,
  `seller_approved_at` timestamp NULL DEFAULT NULL,
  `seller_approved_by` int(11) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'prakash raje','prakash.raje7@gmail.com','$2y$10$xZYAgVOHQ7QSZz0mh/0avOY562C4quE.Z5u4I5rhbGXmUgeBodScy','customer',0,NULL,NULL,'08788316633',NULL,NULL,NULL,NULL,1,'2025-06-22 22:27:50'),(2,'prakash raje','prakashraje020@gmail.com','$2y$10$c3VW3JgExYTQClVeLKcxDeWlg7zWM96N9GUjKuiXZI.AgXL5K2MQi','customer',0,NULL,NULL,'08788316633',NULL,NULL,NULL,NULL,1,'2025-06-22 23:44:13'),(3,'prakash raje','prakash.raje8@gmail.com','$2y$10$NEseXDhvicQEwz.c5H.u6OrIqslhGflbJjGzQvKaKUBzTr9OSMZNe','customer',0,NULL,NULL,'08788316633',NULL,NULL,NULL,NULL,1,'2025-06-22 23:47:27'),(4,'Nirmit Shah','nirmitshah2006@gmail.com','$2y$10$i7sstMwnwFl3KKdxUByjA.MX19qccKhmXP2bWF3r68ibOqt/k1/lO','seller',1,'2025-10-09 07:52:11',1,'8208917546',NULL,NULL,NULL,NULL,1,'2025-07-29 15:48:02'),(5,'SAMIR SHAH','inprotech.cto@gmail.com','$2y$10$.61YdqsFwPLIdEWnYTwRt.THuktqIXgPltPIoVwWLVC.3HqaXUzky','customer',0,NULL,NULL,'8780406230',NULL,NULL,NULL,NULL,1,'2025-08-17 10:18:06'),(6,'Khushali Modi','khushalimodi1998@gmail.com','$2y$10$Dg9rJJFvn47h6IaCIY5Ft.mGP9YAXvY6UE.PIqpjt8t2IdidvXZpy','customer',0,NULL,NULL,'8780086227',NULL,NULL,NULL,NULL,1,'2025-08-27 15:43:12'),(7,'jsinrhuxrn qxmkwoyxsh','tktquxhz@testform.xyz','$2y$10$NESvuEStMxrNZu11zwuu4O1dFr/AKoJUxchWN8hu6gaqXaImf29B6','customer',0,NULL,NULL,'+1-228-056-1524',NULL,NULL,NULL,NULL,1,'2025-10-05 21:55:46'),(8,'ekhgzjvrqx kmpxxyrgiz','pkwrrhtk@testform.xyz','$2y$10$MsEWIxudmdh.fO3p6pzX4OuuWcRvq/eMBbM6SrA7SfSSAOQkT0wNu','customer',0,NULL,NULL,'+1-980-328-7735',NULL,NULL,NULL,NULL,1,'2025-11-11 07:35:14'),(9,'syumtldxug kfdfptdvme','jdnhxmwu@testform.xyz','$2y$10$A33o6DBkLt1tC7Ud18Mt7OUmd986/M9mpQ8DpBakKz08WpVIteq8G','customer',0,NULL,NULL,'+1-106-331-1125',NULL,NULL,NULL,NULL,1,'2025-11-21 19:14:41'),(10,'Nirmit Shah','nirmitsamirshah123@gmail.com','$2y$10$COquJkvO/Slz3GNzmCqNyORSEKL.Q9G548Si8P28fOuyJtbWpdHtO','seller',1,'2025-12-22 05:01:24',1,'08208917546',NULL,NULL,NULL,NULL,1,'2025-12-22 04:55:06'),(11,'ganesh bansode','ganeshbansode1221@gmail.com','$2y$10$2rTotlP7Yfqw5K2.s6hnlO1l.3M61HEw44MJw6q9ngvk4n..T55Iy','customer',0,NULL,NULL,'7854895875',NULL,NULL,NULL,NULL,1,'2026-01-06 04:24:14'),(12,'sachin ringe','sachinringe@yahoo.co.in','$2y$10$d08bgD9eV7nN9Vtk8vI2Lu/ylL9dcrhVzt9KsGU4VFkunpP6EvQYK','customer',0,NULL,NULL,'9998724576',NULL,NULL,NULL,NULL,1,'2026-01-31 06:27:19'),(13,'p m','tester@gmail.com','$2y$10$1Zp6a8XJyKNaplYs8NnUqu7ycAa37JbFbjps0jpUT8ADv2q.pk.pe','customer',0,NULL,NULL,'999999999',NULL,NULL,NULL,NULL,1,'2026-02-07 04:46:01'),(14,'p m','mpradnya5@gmail.com','$2y$10$fwfPPI6xAvIxNLbdx1jW8uwZvpGU6RlWteFoIk0Uq6wtKtf6avnOa','customer',0,NULL,NULL,'9999999999',NULL,NULL,NULL,NULL,1,'2026-02-11 04:08:34'),(15,'51465465 65566546','Atul@vbtek.in','$2y$10$ih68iEPmX1eS1.llu8qLku4.MLfAKjCPmcJztCm/LZ8fPmggLYQ2a','customer',0,NULL,NULL,'afaefefwef',NULL,NULL,NULL,NULL,1,'2026-02-18 03:50:00'),(16,'Shital Yelkar','yelkar.shital@gmail.com','$2y$10$rF8P3b1bHt9hF90RxtSP/u48hQreMqufaTep.oVbKj93WBKe5DmNi','customer',0,NULL,NULL,'7387942042',NULL,NULL,NULL,NULL,1,'2026-02-18 07:31:10');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 10:25:30
