-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: 127.0.0.1    Database: inprotech
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
-- Current Database: `inprotech`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `inprotech` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `inprotech`;

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `state` varchar(50) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `addresses`
--

LOCK TABLES `addresses` WRITE;
/*!40000 ALTER TABLE `addresses` DISABLE KEYS */;
INSERT INTO `addresses` VALUES (10,1,'Prakash','8180099165','415503','A/p Shirdhon, Tal- Koregaon, Dist- Satara','','Pune','Maharashtra',1,'2025-07-09 10:47:00'),(11,1,'Prakash','8180099165','415502','A/p Shirdhon, Tal- Koregaon, Dist- Satara','','Kanpur','Gujarat',0,'2025-07-09 10:47:03'),(12,4,'Nirmit Shah','08208917546','411001','abc','','Pune','Maharashtra',0,'2025-07-29 15:48:48'),(13,4,'Nirmit Shah','08208917546','38011','abc','','baroda','gujarat',0,'2025-07-29 17:52:54'),(14,5,'SAMIR CHANDRAKANTBHAI SHAH','08780406230','411001','NEAR BHARAT FORGE','','Pune','Maharashtra',1,'2025-08-17 10:19:25'),(15,6,'Khushali modi','8780086227','390021','J-203 Satva flats','','Vadodara','Gujarat',1,'2025-08-27 15:45:10'),(16,10,'Nirmit Shah','08208917546','411001','Siciliaa CHS-A-602','','Pune','Maharashtra',0,'2025-12-22 05:29:01'),(19,14,'p','9999999999','415018','pune','','pune','maharashtra',0,'2026-02-13 03:53:50'),(20,16,'13549549','hgfhyguh','jhkjhkjh','knkjnkjbkk','jhjkhbjhbhbj','jhbjhgj','hgjhgh',0,'2026-02-18 11:27:07'),(28,18,'komal gaikwad','07620711816','411025','islampur','test address 1','islampur','Maharashtra',0,'2026-05-02 09:47:48'),(31,18,'gaikwad gaikwad','8668655656','411025','pune maharastra india','test line 1','islampur','Maharashtra',1,'2026-05-07 05:53:44');
/*!40000 ALTER TABLE `addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_manual_invoice_items`
--

DROP TABLE IF EXISTS `admin_manual_invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_manual_invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `hsn_code` varchar(50) DEFAULT NULL,
  `unit` varchar(30) DEFAULT 'No.',
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `item_mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `item_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `invoice_id` (`invoice_id`),
  CONSTRAINT `admin_manual_invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `admin_manual_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_manual_invoice_items`
--

LOCK TABLES `admin_manual_invoice_items` WRITE;
/*!40000 ALTER TABLE `admin_manual_invoice_items` DISABLE KEYS */;
INSERT INTO `admin_manual_invoice_items` VALUES (23,18,67,'Earmuff Karam EP21','uploads/products/68a6d5e8cfa73.png','12345678','No.',18.00,100.00,90.00,1,0),(24,19,62,'Ear Plug 3M','uploads/products/68a6d63090c5b.png','12345678','No.',18.00,100.00,90.00,1,0),(25,20,67,'Earmuff Karam EP21','uploads/products/68a6d5e8cfa73.png','12345678','No.',18.00,100.00,90.00,1,0),(26,21,64,'Earmuff Venus N-530','uploads/products/68a6d616bc1a2.png','12345678','No.',18.00,100.00,90.00,1,0),(27,22,62,'Ear Plug 3M','uploads/products/68a6d63090c5b.png','12345678','No.',18.00,100.00,90.00,1,0);
/*!40000 ALTER TABLE `admin_manual_invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_manual_invoices`
--

DROP TABLE IF EXISTS `admin_manual_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_manual_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(60) NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_mobile` varchar(30) DEFAULT NULL,
  `customer_gstin` varchar(30) DEFAULT NULL,
  `e_way_bill_no` varchar(100) DEFAULT NULL,
  `e_way_bill_date` date DEFAULT NULL,
  `buyer_po_no` varchar(100) DEFAULT NULL,
  `buyer_po_date` date DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `payment_due_date` date DEFAULT NULL,
  `transporter` varchar(100) DEFAULT NULL,
  `lr_no` varchar(100) DEFAULT NULL,
  `billed_to_name` varchar(255) DEFAULT NULL,
  `billed_to_gstin` varchar(30) DEFAULT NULL,
  `billed_to_mobile` varchar(30) DEFAULT NULL,
  `billed_to_address` text NOT NULL,
  `shipped_to_name` varchar(255) DEFAULT NULL,
  `shipped_to_gstin` varchar(30) DEFAULT NULL,
  `shipped_to_mobile` varchar(30) DEFAULT NULL,
  `shipped_to_address` text DEFAULT NULL,
  `created_by_admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `idx_manual_invoice_date` (`invoice_date`),
  KEY `idx_manual_invoice_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_manual_invoices`
--

LOCK TABLES `admin_manual_invoices` WRITE;
/*!40000 ALTER TABLE `admin_manual_invoices` DISABLE KEYS */;
INSERT INTO `admin_manual_invoices` VALUES (18,'ADM-202605-01180','2026-05-25','komal gaikwad','07620711816','00015',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'pradnya mane',NULL,NULL,'Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004','komal gaikwad',NULL,NULL,'Plot No. 12, Sai Residency, Koregaon Park, Pune, Maharashtra – 411001',1,'2026-05-11 05:55:11','2026-05-11 05:55:11'),(19,'ADM-202605-0001','2026-05-11','pradnya mane','07620711816','00012',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'komal gaikwad',NULL,NULL,'Plot No. 12, Sai Residency, Koregaon Park, Pune, Maharashtra – 411001','pradnya mane',NULL,NULL,'Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004',1,'2026-05-11 06:00:15','2026-05-11 06:00:15'),(20,'ADM-202605-0025','2026-05-11','komal gaikwad','07620711816','00012',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'komal gaikwad',NULL,NULL,'Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004','',NULL,NULL,'',1,'2026-05-11 06:00:36','2026-05-11 06:00:36'),(21,'ADM-202605-0015','2026-05-11','komal gaikwad','07620711816','0009',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'komal gaikwad',NULL,NULL,'islampur','pradnya mane',NULL,NULL,'',1,'2026-05-11 11:26:53','2026-05-11 11:26:53'),(22,'ADM-202605-0002','2026-05-15','komal gaikwad','07620711816','12222',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'komal gaikwad',NULL,NULL,'islampur','pradnya mane',NULL,NULL,'islampur',1,'2026-05-11 11:31:47','2026-05-11 11:31:47');
/*!40000 ALTER TABLE `admin_manual_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_quotation_items`
--

DROP TABLE IF EXISTS `admin_quotation_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_quotation_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `item_image` varchar(255) DEFAULT NULL,
  `hsn_code` varchar(50) DEFAULT NULL,
  `unit` varchar(30) DEFAULT 'No.',
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `item_mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `item_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `quotation_id` (`quotation_id`),
  CONSTRAINT `admin_quotation_items_ibfk_1` FOREIGN KEY (`quotation_id`) REFERENCES `admin_quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_quotation_items`
--

LOCK TABLES `admin_quotation_items` WRITE;
/*!40000 ALTER TABLE `admin_quotation_items` DISABLE KEYS */;
INSERT INTO `admin_quotation_items` VALUES (11,8,77,'All Tests','uploads/products/69eb57863c3bd.jpg','90049090','Pair',5.00,371.00,321.00,12,0),(12,9,61,'Ear Plug Venus N-101','uploads/products/68a6d641f326d.png','12345678','No.',18.00,100.00,90.00,1,0),(13,10,61,'Ear Plug Venus N-101','uploads/products/68a6d641f326d.png','12345678','No.',18.00,100.00,90.00,1,0),(14,10,67,'Earmuff Karam EP21','uploads/products/68a6d5e8cfa73.png','12345678','No.',18.00,100.00,90.00,1,1),(15,11,63,'Earmuff Venus N-510','uploads/products/68a6d622f019f.png','12345678','No.',18.00,100.00,90.00,1,0);
/*!40000 ALTER TABLE `admin_quotation_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_quotations`
--

DROP TABLE IF EXISTS `admin_quotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_quotations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quotation_number` varchar(60) NOT NULL,
  `quotation_date` date NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_mobile` varchar(30) DEFAULT NULL,
  `customer_gstin` varchar(30) DEFAULT NULL,
  `e_way_bill_no` varchar(100) DEFAULT NULL,
  `e_way_bill_date` date DEFAULT NULL,
  `buyer_po_no` varchar(100) DEFAULT NULL,
  `buyer_po_date` date DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `payment_due_date` date DEFAULT NULL,
  `transporter` varchar(100) DEFAULT NULL,
  `lr_no` varchar(100) DEFAULT NULL,
  `billed_to_name` varchar(255) DEFAULT NULL,
  `billed_to_gstin` varchar(30) DEFAULT NULL,
  `billed_to_mobile` varchar(30) DEFAULT NULL,
  `billed_to_address` text NOT NULL,
  `shipped_to_name` varchar(255) DEFAULT NULL,
  `shipped_to_gstin` varchar(30) DEFAULT NULL,
  `shipped_to_mobile` varchar(30) DEFAULT NULL,
  `shipped_to_address` text DEFAULT NULL,
  `created_by_admin_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotation_number` (`quotation_number`),
  KEY `idx_quotation_date` (`quotation_date`),
  KEY `idx_quotation_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_quotations`
--

LOCK TABLES `admin_quotations` WRITE;
/*!40000 ALTER TABLE `admin_quotations` DISABLE KEYS */;
INSERT INTO `admin_quotations` VALUES (8,'QTN-202605-0008','2026-05-13','komal gaikwad','07620711816','00012','',NULL,'',NULL,'Manual','2026-05-13','','','pradnya mane',NULL,NULL,'Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004','komal gaikwad',NULL,NULL,'Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004',1,'2026-05-13 08:32:30','2026-05-13 08:32:30'),(9,'QTN-202605-0009','2026-05-14','komal gaikwad','7620711816',NULL,'',NULL,'',NULL,'Manual','2026-05-14','','','pradnya mane','898989812222222','7620711816','islampur islampur','','','','',1,'2026-05-14 09:32:19','2026-05-14 09:32:19'),(10,'QTN-202605-0010','2026-05-14','komal gaikwad','7620711816',NULL,'',NULL,'',NULL,'Manual','2026-05-15','','','komal gaikwad','898989812222222','7620711816','Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004','','','','',1,'2026-05-14 09:37:32','2026-05-14 09:37:32'),(11,'QTN-202605-0011','2026-05-14','komal gaikwad','7620711816',NULL,'',NULL,'',NULL,'Manual','2026-05-14','','','komal gaikwad','898989812222222','7620711816','Flat No. 204, Shree Ganesh Apartment, FC Road, Shivajinagar, Pune, Maharashtra – 411004','','','','',1,'2026-05-14 09:40:46','2026-05-14 09:40:46');
/*!40000 ALTER TABLE `admin_quotations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `role` enum('super_admin','admin','manager') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admins`
--

LOCK TABLES `admins` WRITE;
/*!40000 ALTER TABLE `admins` DISABLE KEYS */;
INSERT INTO `admins` VALUES (1,'Super Admin','admin@everythingb2c.com','$2y$10$nwNkYiFBWQMOxZxQ1jnNB.LjOCgVte2U7w3kWRR0T96a.x4QHLbgG',1,'super_admin',1,'2026-05-14 04:22:47','2025-06-28 06:15:55','2026-05-14 04:22:47');
/*!40000 ALTER TABLE `admins` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` text DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(500) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `banners`
--

LOCK TABLES `banners` WRITE;
/*!40000 ALTER TABLE `banners` DISABLE KEYS */;
INSERT INTO `banners` VALUES (1,'uploads/banners/69f867b29a4cf-WhatsApp-Image-2026-05-04-at-2.32.06-PM.webp','','','','',1,2,'2026-04-23 05:54:18'),(2,'uploads/banners/69e9b46606fb8-BANNER-2.jpg','Premium Quality Safety Classes at Affordable Prices!','','More Details','http://localhost/inprotech/category.php?slug=eye-protection',1,1,'2026-04-23 05:55:50');
/*!40000 ALTER TABLE `banners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `logo_path` varchar(255) NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'VENUS','uploads/brands/69e6106fde19f-client-3.jpg',1,2,'2026-04-20 11:39:27'),(2,'UDYOGI','uploads/brands/69e610a9add62-client-1.jpg',1,4,'2026-04-20 11:40:11'),(7,'test','uploads/brands/69e87df411ece-69e610f7335d6-client-2.webp',1,1,'2026-04-22 07:51:16'),(8,'test','uploads/brands/69e87e006a16f-69e610f7335d6-client-2.webp',1,3,'2026-04-22 07:51:28');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `variation_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_cart_user` (`user_id`),
  KEY `idx_cart_variation_id` (`variation_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cart_variation` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (218,16,8,NULL,10,'2026-02-18 09:06:17'),(224,1,4,NULL,1,'2026-02-22 19:53:49'),(293,18,77,NULL,24,'2026-05-27 06:27:22');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `product_count` int(11) DEFAULT 0,
  `is_approved` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_parent_category` (`parent_id`),
  KEY `idx_seller_id` (`seller_id`),
  KEY `idx_categories_seller` (`seller_id`),
  CONSTRAINT `fk_categories_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_parent_category` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,NULL,'Hand Protection','hand-protection','uploads/categories/69ddd127799c8.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',2,1,'2025-06-22 22:25:18',9,5),(2,NULL,'Body Protection','body-protection','uploads/categories/69ddd1598f55a.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',4,1,'2025-06-22 22:25:18',9,6),(3,NULL,'Fire Extinguishers','fire-extinguishers','uploads/categories/69ddd1a384e5c.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',3,1,'2025-06-22 22:25:18',9,10),(9,NULL,'Industrial Safety Products','industrial-safety-products','uploads/categories/68a0c320072fc.webp','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',0,1,'2025-07-23 06:22:44',NULL,0),(16,NULL,'Eye Protection','eye-protection','uploads/categories/696da1e815108.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',0,1,'2025-08-01 09:01:00',9,2),(17,NULL,'Foot Protection','foot-protection','uploads/categories/696da1f7c4c28.png','Foot Protection',0,1,'2025-08-01 09:27:36',9,8),(18,NULL,'Head Protection','head-protection','uploads/categories/696da20ad6b54.png','',0,1,'2025-08-01 10:04:10',9,1),(19,NULL,'Safety Belt','safety-belt','uploads/categories/69ddc3825e5d4.png','Safety Belt',0,1,'2025-08-12 10:12:02',9,9),(20,NULL,'Ear Protection','ear-protection','uploads/categories/696da1d2a9b59.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',0,1,'2025-08-12 10:21:08',9,4),(25,NULL,'Face Protection','face-protection','uploads/categories/69dcdd244d0b6.png','Test',0,1,'2026-04-13 12:09:29',9,3),(26,NULL,'Safety Jacket','safety-jacket','uploads/categories/69ddd1fde521d.png','Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',0,1,'2026-04-14 05:34:53',9,7);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_submissions`
--

DROP TABLE IF EXISTS `contact_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `service` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_submissions`
--

LOCK TABLES `contact_submissions` WRITE;
/*!40000 ALTER TABLE `contact_submissions` DISABLE KEYS */;
INSERT INTO `contact_submissions` VALUES (3,'komal','gaikwad','gaikwadkoamlgaikwad2000@gmail.com','7620711816','Bulk Order','test msg from dev team','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-05-06 07:50:28');
/*!40000 ALTER TABLE `contact_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `industries`
--

DROP TABLE IF EXISTS `industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `industries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `industries`
--

LOCK TABLES `industries` WRITE;
/*!40000 ALTER TABLE `industries` DISABLE KEYS */;
INSERT INTO `industries` VALUES (1,'Automotive & Manufacturing','automotive-manufacturing','assets/Website-Images/Industries-1.jpg',1,'2026-04-21 12:26:34'),(2,'Construction & Infrastructure','construction-infrastructure','assets/Website-Images/Industries-2.jpg',2,'2026-04-21 12:26:34'),(3,'Chemical & Pharmaceutical','chemical-pharmaceutical','assets/Website-Images/Industries-3.jpg',3,'2026-04-21 12:26:34'),(4,'Electronics & Semiconductor','electronics-semiconductor','assets/Website-Images/Industries-4.jpg',4,'2026-04-21 12:26:34');
/*!40000 ALTER TABLE `industries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `hsn` varchar(20) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (11,12,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(12,13,10,NULL,1,35.00,35.00,18.00,6.30,0.00,0.00),(13,14,8,NULL,1,95.00,95.00,18.00,17.10,0.00,0.00),(14,15,8,NULL,1,95.00,95.00,18.00,17.10,0.00,0.00),(16,17,10,NULL,3,105.00,35.00,18.00,18.90,0.00,0.00),(17,18,10,NULL,3,105.00,35.00,18.00,18.90,0.00,0.00),(18,19,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(19,20,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(21,22,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(22,23,10,NULL,7,245.00,35.00,18.00,44.10,0.00,0.00),(23,24,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(24,26,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(29,32,8,NULL,3,285.00,95.00,18.00,51.30,0.00,0.00),(30,33,4,NULL,2,636.00,318.00,18.00,114.48,0.00,0.00),(31,34,4,NULL,4,1272.00,318.00,18.00,228.96,450.00,318.00),(32,34,10,NULL,1,35.00,35.00,18.00,6.30,45.00,35.00),(33,35,10,NULL,5,175.00,35.00,18.00,31.50,45.00,35.00),(34,36,4,NULL,1,318.00,318.00,18.00,57.24,450.00,318.00),(35,37,4,NULL,1,318.00,318.00,18.00,57.24,450.00,318.00),(36,38,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(37,39,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(38,40,8,NULL,2,190.00,95.00,18.00,34.20,0.00,0.00),(39,40,4,NULL,1,318.00,318.00,18.00,57.24,0.00,0.00),(40,41,4,NULL,1,318.00,318.00,18.00,57.24,450.00,318.00),(41,42,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(42,43,10,'12345678',4,140.00,35.00,18.00,25.20,45.00,35.00),(43,43,4,'12345678',2,636.00,318.00,18.00,114.48,450.00,318.00),(44,43,6,'12345678',2,240.00,120.00,18.00,43.20,150.00,120.00),(45,44,4,'12345678',2,636.00,318.00,18.00,114.48,450.00,318.00),(46,44,5,'12345678',1,280.00,280.00,18.00,50.40,290.00,280.00),(48,44,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(49,44,7,'12345678',1,65.00,65.00,18.00,11.70,80.00,65.00),(50,44,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(51,45,10,'12345678',6,210.00,35.00,18.00,37.80,45.00,35.00),(52,46,6,'12345678',2,240.00,120.00,18.00,43.20,150.00,120.00),(53,46,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(54,46,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(55,46,8,'12345678',3,285.00,95.00,18.00,51.30,120.00,95.00),(56,47,10,'12345678',4,140.00,35.00,18.00,25.20,45.00,35.00),(57,47,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(58,47,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(59,47,9,'12345678',2,320.00,160.00,18.00,57.60,200.00,160.00),(61,48,4,'12345678',2,636.00,318.00,18.00,114.48,450.00,318.00),(62,48,10,'12345678',4,140.00,35.00,18.00,25.20,45.00,35.00),(63,48,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(64,48,6,'12345678',4,480.00,120.00,18.00,86.40,150.00,120.00),(65,48,9,'12345678',5,800.00,160.00,18.00,144.00,200.00,160.00),(67,49,8,'12345678',2,190.00,95.00,18.00,34.20,120.00,95.00),(68,49,10,'12345678',3,105.00,35.00,18.00,18.90,45.00,35.00),(69,50,10,'12345678',3,105.00,35.00,18.00,18.90,45.00,35.00),(70,50,8,'12345678',3,285.00,95.00,18.00,51.30,120.00,95.00),(71,51,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(72,51,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(73,51,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(74,52,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(75,53,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(76,53,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(77,54,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(78,54,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(79,55,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(80,55,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(81,56,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(82,56,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(83,56,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(84,56,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(85,57,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(86,57,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(87,57,65,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(88,57,58,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(89,58,65,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(90,58,6,'12345678',1,120.00,120.00,18.00,21.60,150.00,120.00),(91,59,64,'12345678',2,180.00,90.00,18.00,32.40,100.00,90.00),(92,60,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(93,60,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(94,60,8,'12345678',1,95.00,95.00,18.00,17.10,120.00,95.00),(95,60,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(96,61,69,'HSN 1111',1,90.00,90.00,18.00,16.20,100.00,90.00),(97,61,21,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(98,62,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(99,63,23,'90049090',1,60.00,60.00,18.00,10.80,93.00,60.00),(100,63,67,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(101,63,66,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(102,63,60,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(103,63,61,'12345678',5,450.00,90.00,18.00,81.00,100.00,90.00),(104,63,59,'12345678',4,360.00,90.00,18.00,64.80,100.00,90.00),(105,64,15,'12345678',2,180.00,90.00,18.00,32.40,100.00,90.00),(106,65,75,'90049090',1,918.00,918.00,18.00,165.24,1400.00,918.00),(107,66,75,'90049090',1,918.00,918.00,18.00,165.24,1400.00,918.00),(108,67,10,'12345678',1,35.00,35.00,18.00,6.30,45.00,35.00),(109,68,68,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(110,68,75,'90049090',1,918.00,918.00,18.00,165.24,1400.00,918.00),(111,68,67,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(112,69,74,'90049090',3,285.00,95.00,18.00,51.30,123.00,95.00),(113,70,68,'12345678',4,360.00,90.00,18.00,64.80,100.00,90.00),(114,70,67,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(115,70,66,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(116,70,65,'12345678',3,270.00,90.00,18.00,48.60,100.00,90.00),(117,71,75,'90049090',2,1836.00,918.00,18.00,330.48,1400.00,918.00),(118,72,75,'90049090',2,1836.00,918.00,18.00,330.48,1400.00,918.00),(119,73,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(120,74,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(121,75,4,'12345678',1,318.00,318.00,18.00,57.24,450.00,318.00),(122,76,63,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(123,77,78,'12345678',1,222.00,222.00,18.00,39.96,225.00,222.00),(124,77,52,'12345678',10,900.00,90.00,18.00,162.00,100.00,90.00),(125,77,51,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(126,77,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(127,77,10,'12345678',5,175.00,35.00,18.00,31.50,45.00,35.00),(128,77,46,'12345678',10,900.00,90.00,18.00,162.00,100.00,90.00),(129,77,78,'12345678',11,2508.00,228.00,18.00,451.44,290.00,228.00),(130,78,74,'90049090',4,380.00,95.00,18.00,68.40,123.00,95.00),(131,78,78,'12345678',350,1554.00,222.00,18.00,279.72,225.00,222.00),(132,79,3,'12345678',12,290.00,290.00,18.00,52.20,310.00,290.00),(133,79,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(134,79,65,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(135,79,64,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(136,80,77,'90049090',48,1284.00,321.00,5.00,64.20,371.00,321.00),(137,81,77,'90049090',12,321.00,321.00,5.00,16.05,371.00,321.00),(138,82,52,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(139,83,21,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(140,84,66,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(141,85,9,'12345678',1,160.00,160.00,18.00,28.80,200.00,160.00),(142,86,10,'12345678',6,210.00,35.00,18.00,37.80,45.00,35.00),(143,87,78,'12345678',350,1554.00,222.00,18.00,279.72,225.00,222.00),(144,88,67,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00),(145,88,50,'12345678',1,90.00,90.00,18.00,16.20,100.00,90.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_status_history`
--

DROP TABLE IF EXISTS `order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `order_status_id` int(11) NOT NULL,
  `status_description` text DEFAULT NULL,
  `updated_by` enum('admin','system','user') DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_status_id` (`order_status_id`),
  KEY `idx_order_status_history_order` (`order_id`),
  CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_status_history`
--

LOCK TABLES `order_status_history` WRITE;
/*!40000 ALTER TABLE `order_status_history` DISABLE KEYS */;
INSERT INTO `order_status_history` VALUES (11,12,1,'Order placed successfully','system','2025-07-08 08:56:06'),(12,13,1,'Order placed successfully','system','2025-07-08 08:57:10'),(13,14,1,'Order placed successfully','system','2025-07-08 09:07:20'),(14,15,1,'Order placed successfully','system','2025-07-08 09:19:04'),(15,16,1,'Order placed successfully','system','2025-07-08 09:21:23'),(16,16,1,'','admin','2025-07-08 09:28:47'),(17,16,1,'','admin','2025-07-08 09:29:19'),(18,17,1,'Order placed successfully','system','2025-07-08 10:33:09'),(19,18,1,'Order placed successfully','system','2025-07-08 10:34:08'),(20,16,1,'','admin','2025-07-08 11:04:56'),(21,19,1,'Order placed successfully','system','2025-07-09 10:47:33'),(22,20,1,'Order placed successfully','system','2025-07-09 13:55:43'),(23,21,1,'Order placed successfully','system','2025-07-09 13:58:23'),(24,22,1,'Order placed successfully','system','2025-07-09 14:04:45'),(25,23,1,'Order placed successfully','system','2025-07-09 14:09:30'),(26,24,1,'Order placed successfully','system','2025-07-09 14:16:11'),(27,28,1,'Order placed successfully','system','2025-07-09 14:23:23'),(28,31,1,'Order placed successfully','system','2025-07-09 14:26:57'),(29,32,1,'Order placed successfully','system','2025-07-09 14:29:10'),(30,32,2,'your order is in processing status','admin','2025-07-09 14:36:17'),(31,32,3,'Order shipped from Delhi','admin','2025-07-09 14:36:58'),(32,32,4,'Order is in Transit','admin','2025-07-09 14:37:57'),(33,32,5,'Out for Delivery','admin','2025-07-09 14:38:31'),(34,32,13,'Order Delivered successfully','admin','2025-07-09 14:39:23'),(35,32,34,'','admin','2025-07-09 14:42:41'),(36,33,1,'Order placed successfully','system','2025-07-09 15:03:42'),(37,34,1,'Order placed successfully','system','2025-07-09 15:19:36'),(38,35,1,'Order placed successfully','system','2025-07-09 16:27:35'),(39,36,1,'Order placed successfully','system','2025-07-09 16:30:58'),(40,37,1,'Order placed successfully','system','2025-07-09 16:31:31'),(41,38,1,'Order placed successfully','system','2025-07-09 16:32:29'),(42,39,1,'Order placed successfully','system','2025-07-09 18:39:52'),(43,40,1,'Order placed successfully','system','2025-07-09 18:52:11'),(44,41,1,'Order placed successfully','system','2025-07-09 19:15:29'),(45,42,1,'Order placed successfully','system','2025-07-09 19:51:21'),(46,43,1,'Order placed successfully','system','2025-07-10 05:11:04'),(47,44,1,'Order placed successfully','system','2025-07-13 06:40:38'),(48,45,1,'Order placed successfully','system','2025-07-16 13:38:58'),(49,46,1,'Order placed successfully','system','2025-07-22 17:57:28'),(50,47,1,'Order placed successfully','system','2025-07-26 14:22:13'),(51,48,1,'Order placed successfully','system','2025-07-29 15:39:03'),(52,49,1,'Order placed successfully','system','2025-07-29 15:49:02'),(53,50,1,'Order placed successfully','system','2025-07-29 17:54:09'),(54,51,1,'Order placed successfully','system','2025-08-17 10:19:58'),(55,52,1,'Order placed successfully','system','2025-08-27 15:45:18'),(56,48,1,'','admin','2025-09-14 11:17:12'),(57,52,3,'','admin','2025-09-14 13:46:01'),(58,52,1,'','admin','2025-09-14 13:47:30'),(59,52,3,'','admin','2025-09-14 13:58:11'),(60,51,1,'','admin','2025-09-14 16:14:05'),(61,51,1,'','admin','2025-09-14 16:23:07'),(62,52,1,'','admin','2025-09-14 16:42:54'),(63,51,1,'','admin','2025-09-14 16:43:23'),(64,51,3,'','admin','2025-09-14 16:44:51'),(65,53,1,'Order placed successfully','system','2025-09-14 16:47:57'),(66,53,10,'','admin','2025-09-14 16:48:47'),(67,53,1,'','admin','2025-09-14 16:51:07'),(68,53,3,'','admin','2025-09-14 16:51:15'),(69,53,1,'','admin','2025-09-14 16:52:33'),(70,54,1,'Order placed successfully','system','2025-09-22 06:04:22'),(71,55,1,'Order placed successfully','system','2025-09-22 06:09:24'),(72,56,1,'Order placed successfully','system','2025-09-23 15:13:18'),(73,57,1,'Order placed successfully','system','2025-09-27 06:03:00'),(74,58,1,'Order placed successfully','system','2025-09-27 06:04:10'),(75,59,1,'Order placed successfully','system','2025-09-27 06:44:00'),(76,60,1,'Order placed successfully','system','2025-09-27 06:53:23'),(77,61,1,'Order placed successfully','system','2025-12-22 05:29:14'),(78,62,1,'Order placed successfully','system','2026-01-31 06:43:06'),(79,63,1,'Order placed successfully','system','2026-01-31 06:47:49'),(80,63,24,'','admin','2026-02-07 14:56:40'),(81,63,31,'','admin','2026-02-07 14:59:24'),(82,60,38,'','admin','2026-02-07 14:59:58'),(83,60,3,'','admin','2026-02-07 15:22:25'),(84,64,1,'Order placed successfully','system','2026-02-07 15:43:08'),(85,65,1,'Order placed successfully','system','2026-02-07 15:43:32'),(86,66,1,'Order placed successfully','system','2026-02-11 09:21:53'),(87,67,1,'Order placed successfully','system','2026-02-13 10:01:48'),(88,68,1,'Order placed successfully','system','2026-02-17 10:10:31'),(89,69,1,'Order placed successfully','system','2026-02-17 14:17:34'),(90,70,1,'Order placed successfully','system','2026-02-18 05:54:28'),(91,71,1,'Order placed successfully','system','2026-02-19 12:00:57'),(92,72,1,'Order placed successfully','system','2026-02-22 19:44:28'),(93,73,1,'Order placed successfully','system','2026-02-22 19:48:33'),(94,74,1,'Order placed successfully','system','2026-02-22 19:51:03'),(95,75,1,'Order placed successfully','system','2026-02-22 19:53:02'),(96,76,1,'Order placed successfully','system','2026-02-23 10:57:35'),(97,77,1,'Order placed successfully','system','2026-05-02 09:55:46'),(98,77,1,'','admin','2026-05-05 08:57:34'),(99,77,27,'','admin','2026-05-05 08:58:00'),(100,77,27,'','admin','2026-05-05 08:58:13'),(101,77,2,'','admin','2026-05-05 08:58:19'),(102,77,2,'','admin','2026-05-05 09:04:44'),(103,77,17,'','admin','2026-05-05 09:04:56'),(104,77,6,'','admin','2026-05-05 09:08:33'),(105,78,1,'Order placed successfully','system','2026-05-26 06:08:49'),(106,79,1,'Order placed successfully','system','2026-05-26 06:14:53'),(107,80,1,'Order placed successfully','system','2026-05-26 06:17:42'),(108,81,1,'Order placed successfully','system','2026-05-26 06:24:50'),(109,82,1,'Order placed successfully','system','2026-05-26 06:28:28'),(110,83,1,'Order placed successfully','system','2026-05-26 06:30:11'),(111,84,1,'Order placed successfully','system','2026-05-26 06:33:30'),(112,85,1,'Order placed successfully','system','2026-05-26 06:34:48'),(113,86,1,'Order placed successfully','system','2026-05-26 06:56:34'),(114,87,1,'Order placed successfully','system','2026-05-27 04:22:28'),(115,88,1,'Order placed successfully','system','2026-05-27 06:06:28');
/*!40000 ALTER TABLE `order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_statuses`
--

DROP TABLE IF EXISTS `order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT '#007bff',
  `is_system` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `upi_screenshot` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  UNIQUE KEY `tracking_id` (`tracking_id`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  KEY `idx_orders_created` (`created_at`),
  KEY `idx_orders_shipping` (`shipping_zone_id`,`billing_state`,`billing_city`),
  KEY `fk_orders_status` (`order_status_id`),
  KEY `idx_orders_tracking_id` (`tracking_id`),
  KEY `idx_orders_payment_status` (`payment_status`),
  KEY `fk_orders_address` (`address_id`),
  KEY `idx_seller_orders` (`seller_id`),
  CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_orders_status` FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (12,1,NULL,NULL,'ORDER202507088016',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-08 08:56:06','Everythingb2c66236680',NULL,NULL,1,'pending','order_QqVyxPfVtgAjfJ',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(13,1,NULL,NULL,'ORDER202507083957',41.30,35.00,6.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-08 08:57:10','Everythingb2c61103209',NULL,NULL,1,'pending','order_QqW04auv0iB84m',NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(14,1,NULL,NULL,'ORDER202507082717',112.10,95.00,17.10,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-08 09:07:20','Everythingb2c47141244',NULL,NULL,1,'paid','order_QqWAnXnE5OTBtV','pay_QqWB1WsU6Emezq',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(15,1,NULL,NULL,'ORDER202507087112',112.10,95.00,17.10,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 09:19:04','Everythingb2c55889181',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(16,1,NULL,NULL,'ORDER202507083491',483.80,410.00,73.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 09:21:23','Everythingb2c03110318','','',1,'paid',NULL,NULL,NULL,NULL,NULL,0,'',NULL,NULL,NULL,NULL),(17,1,NULL,NULL,'ORDER202507083976',123.90,105.00,18.90,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 10:33:09','Everythingb2c39930075',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(18,1,NULL,NULL,'ORDER202507085520',123.90,105.00,18.90,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-08 10:34:08','Everythingb2c95007545',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(19,1,NULL,11,'ORDER202507091088',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 10:47:33','Everythingb2c81543477',NULL,NULL,1,'pending',NULL,NULL,NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL),(20,1,NULL,10,'ORDER202507091739',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 13:55:43','Everythingb2c45276575',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(21,1,NULL,11,'ORDER202507098748',483.80,410.00,73.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 13:58:23','Everythingb2c77567796',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(22,1,NULL,11,'ORDER202507097304',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:04:45','Everythingb2c08456249',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(23,1,NULL,11,'ORDER202507092077',289.10,245.00,44.10,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:09:30','Everythingb2c34380893',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(24,1,NULL,11,'ORDER202507099621',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 14:16:11','Everythingb2c27316660',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(26,1,NULL,11,'',445.24,318.00,57.24,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:19:29',NULL,NULL,NULL,1,'paid','order_Qr01iDO4UxsRRl','pay_Qr020GbZ978g5k',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(28,1,NULL,10,'ORDER202507099782',236.00,200.00,36.00,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:22:54','Everythingb2c11526692',NULL,NULL,1,'paid','order_Qr05J4DKD8JdpH','pay_Qr05XJ2YvzqdRO',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(29,1,NULL,11,'ORDER202507093476',553.80,410.00,73.80,70.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:25:47','Everythingb2c34216156',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(30,1,NULL,11,'ORDER202507099997',553.80,410.00,73.80,70.00,NULL,NULL,NULL,NULL,'pending',NULL,'razorpay','2025-07-09 14:25:54','Everythingb2c12999204',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(31,1,NULL,11,'ORDER202507092005',553.80,410.00,73.80,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:26:26','Everythingb2c70593336',NULL,NULL,1,'paid','order_Qr093IhEB0E0ou','pay_Qr09JIfa6ymA01',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(32,1,NULL,11,'ORDER202507091604',406.30,285.00,51.30,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 14:28:42','Everythingb2c21194149','TRCK001','https://google.com',34,'paid','order_Qr0BRB2Xh1z0k7','pay_Qr0Beaz5CpNc2e',NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(33,1,NULL,11,'ORDER202507092941',820.48,636.00,114.48,70.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 15:03:12','Everythingb2c44660028',NULL,NULL,1,'paid','order_Qr0ltzik1brFYj','pay_Qr0m7q8XLupiWq',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(34,1,NULL,11,'ORDER202507097506',1612.26,1307.00,235.26,70.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 15:19:36','Everythingb2c49775529',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(35,1,NULL,10,'ORDER202507094251',206.50,175.00,31.50,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 16:27:35','Everythingb2c77827159',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(36,1,NULL,10,'ORDER202507094409',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 16:30:58','Everythingb2c19101886',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(37,1,NULL,10,'ORDER202507097085',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 16:31:31','Everythingb2c43079069',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(38,1,NULL,10,'ORDER202507094606',375.24,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 16:31:53','Everythingb2c42563697',NULL,NULL,1,'paid','order_Qr2HX6uc6IjXAP','pay_Qr2HsHpxgP2wS9',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(39,1,NULL,11,'ORDER202507099260',475.24,318.00,57.24,100.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 18:39:21','Everythingb2c37464001',NULL,NULL,1,'paid','order_Qr4SAWGDAmP7FL','pay_Qr4SRNBMWDGgPU',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(40,1,NULL,10,'ORDER202507091693',599.44,508.00,91.44,0.00,NULL,NULL,NULL,NULL,'confirmed',NULL,'razorpay','2025-07-09 18:51:41','Everythingb2c49484736',NULL,NULL,1,'paid','order_Qr4fCURPLFQmWI','pay_Qr4fRMSQkqD9qX',NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(41,1,NULL,11,'ORDER202507098897',475.24,318.00,57.24,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 19:15:29','Everythingb2c15029790',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(42,1,NULL,11,'ORDER202507092585',288.80,160.00,28.80,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-09 19:51:21','Everythingb2c26118502',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(43,1,NULL,11,'ORDER202507104883',1298.88,1016.00,182.88,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-10 05:11:04','Everythingb2c36327833',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(44,1,NULL,10,'ORDER202507132525',1895.08,1606.00,289.08,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-13 06:40:38','Everythingb2c34482495',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(45,1,NULL,10,'ORDER202507166754',247.80,210.00,37.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'direct_payment','2025-07-16 13:38:58','Everythingb2c30742473',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,'txn_12345678trd',NULL),(46,1,NULL,10,'ORDER202507228961',1036.04,878.00,158.04,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-22 17:57:28','EverythingB2C74666831',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(47,1,NULL,10,'ORDER202507262876',1030.14,873.00,157.14,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-26 14:22:13','EverythingB2C56758332',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(48,1,NULL,10,'ORDER202507299494',4201.00,4201.00,706.98,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-29 15:39:03','EverythingB2C26475301','D1005560078','',1,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(49,4,NULL,12,'ORDER202507296952',295.00,295.00,53.10,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-29 15:49:02','EverythingB2C41600018',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(50,4,NULL,13,'ORDER202507296039',490.00,390.00,70.20,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-07-29 17:54:09','EverythingB2C15663702',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(51,5,NULL,14,'ORDER202508173761',513.00,513.00,92.34,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-08-17 10:19:58','EverythingB2C48972865','D3003398857','',3,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(52,6,NULL,15,'ORDER202508279563',260.00,160.00,28.80,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-08-27 15:45:18','EverythingB2C49310619','D3003398857','',1,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(53,5,NULL,14,'ORDER202509144626',280.00,280.00,50.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-14 16:47:57','EverythingB2C70958473','7D154319924','',1,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(54,5,NULL,14,'ORDER202509222629',408.00,408.00,73.44,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-22 06:04:22','EverythingB2C95022888',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(55,5,NULL,14,'ORDER202509227857',215.00,215.00,38.70,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-22 06:09:24','EverythingB2C05414971',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(56,5,NULL,14,'ORDER202509235384',568.00,568.00,102.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-23 15:13:18','EverythingB2C42141670',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(57,5,NULL,14,'ORDER202509274935',435.00,435.00,78.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:03:00','EverythingB2C55652995',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(58,5,NULL,14,'ORDER202509275880',210.00,210.00,37.80,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:04:10','EverythingB2C58880258',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(59,5,NULL,14,'ORDER202509278874',180.00,180.00,32.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:44:00','EverythingB2C46307437',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(60,1,NULL,10,'ORDER202509276184',538.00,538.00,96.84,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-09-27 06:53:23','EverythingB2C86135192','D0005640777','',3,'pending',NULL,NULL,NULL,'',NULL,0,'','2026-02-13',NULL,NULL,NULL),(61,10,NULL,16,'ORDER202512229031',180.00,180.00,32.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2025-12-22 05:29:14','EverythingB2C27347219',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(62,12,NULL,NULL,'ORDER202601313627',418.00,318.00,57.24,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-01-31 06:43:06','EverythingB2C88217404',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(63,12,NULL,NULL,'ORDER202601310622',1420.00,1320.00,237.60,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-01-31 06:47:49','EverythingB2C40020212','D0005640777','',31,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(64,1,NULL,10,'ORDER202602073532',180.00,180.00,32.40,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-07 15:43:08','EverythingB2C06672299',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(65,1,NULL,10,'ORDER202602079897',918.00,918.00,165.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-07 15:43:32','EverythingB2C23627084',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(66,14,NULL,NULL,'ORDER202602112074',918.00,918.00,165.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-11 09:21:53','EverythingB2C04296840',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(67,14,NULL,19,'ORDER202602138774',35.00,35.00,6.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-13 10:01:48','EverythingB2C56713881',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(68,1,NULL,10,'ORDER202602170426',1458.00,1458.00,262.44,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-17 10:10:31','EverythingB2C71461666',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(69,1,NULL,10,'ORDER202602173100',285.00,285.00,51.30,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'direct_payment','2026-02-17 14:17:34','EverythingB2C98682869',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,'T123456789',NULL),(70,5,NULL,14,'ORDER202602180174',990.00,990.00,178.20,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-18 05:54:28','EverythingB2C75404818',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(71,1,NULL,10,'ORDER202602191783',1836.00,1836.00,330.48,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'direct_payment','2026-02-19 12:00:57','EverythingB2C14764343',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,'txn_12345678trd',NULL),(72,1,NULL,10,'ORDER202602221547',1836.00,1836.00,330.48,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:44:28','EverythingB2C37600793',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(73,1,NULL,10,'ORDER202602228555',318.00,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:48:33','EverythingB2C26897791',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(74,1,NULL,10,'ORDER202602228872',318.00,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:51:03','EverythingB2C49607989',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(75,1,NULL,10,'ORDER202602222825',318.00,318.00,57.24,0.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-22 19:53:02','EverythingB2C70855919',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(76,12,NULL,NULL,'ORDER202602231022',190.00,90.00,16.20,100.00,NULL,NULL,NULL,NULL,'pending',NULL,'cod','2026-02-23 10:57:34','EverythingB2C07453030',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(77,18,NULL,NULL,'ORDER202605022725',4955.00,4885.00,879.30,70.00,NULL,'Maharashtra','islampur1','411025','pending','komal gaikwad\nislampur\nthanapude\nislampur1, Maharashtra - 411025\nPhone: 07620711816','cod','2026-05-02 09:55:46','Inprotech65638097','','',6,'pending',NULL,NULL,NULL,'',NULL,0,'',NULL,NULL,NULL,NULL),(78,18,NULL,31,'ORDER202605262440',2004.00,1934.00,348.12,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:08:48','Inprotech68603105',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(79,18,NULL,31,'ORDER202605268499',630.00,560.00,100.80,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:14:53','Inprotech81126945',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(80,18,NULL,31,'ORDER202605267173',1354.00,1284.00,64.20,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:17:42','Inprotech44842969',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(81,18,NULL,31,'ORDER202605269520',391.00,321.00,16.05,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:24:50','Inprotech78290747',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(82,18,NULL,31,'ORDER202605268846',160.00,90.00,16.20,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:28:28','Inprotech50076762',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(83,18,NULL,31,'ORDER202605269891',160.00,90.00,16.20,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:30:11','Inprotech46742532',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(84,18,NULL,31,'ORDER202605263341',160.00,90.00,16.20,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:33:30','Inprotech89488623',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(85,18,NULL,31,'ORDER202605269197',230.00,160.00,28.80,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:34:48','Inprotech79231013',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(86,18,NULL,31,'ORDER202605265968',280.00,210.00,37.80,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-26 06:56:34','Inprotech31627179',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(87,18,NULL,31,'ORDER202605273743',1624.00,1554.00,279.72,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-27 04:22:28','Inprotech87533882',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL),(88,18,NULL,31,'ORDER202605276692',250.00,180.00,32.40,70.00,NULL,'Maharashtra','islampur','411025','pending','gaikwad gaikwad\npune maharastra india\ntest line 1\nislampur, Maharashtra - 411025\nPhone: 8668655656','cod','2026-05-27 06:06:28','Inprotech60541470',NULL,NULL,1,'pending',NULL,NULL,NULL,'',NULL,0,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_transactions`
--

DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'view_dashboard','View Dashboard','Can view admin dashboard','Dashboard',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(2,'view_products','View Products','Can view products list','Products',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(3,'add_product','Add Product','Can add new products','Products',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(4,'edit_product','Edit Product','Can edit existing products','Products',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(5,'delete_product','Delete Product','Can delete products','Products',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(6,'manage_product_approval','Manage Product Approval','Can approve/reject seller products','Products',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(7,'view_categories','View Categories','Can view categories','Categories',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(8,'add_category','Add Category','Can add new categories','Categories',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(9,'edit_category','Edit Category','Can edit categories','Categories',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(10,'delete_category','Delete Category','Can delete categories','Categories',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(11,'view_orders','View Orders','Can view orders','Orders',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(12,'edit_order','Edit Order','Can edit orders','Orders',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(13,'delete_order','Delete Order','Can delete orders','Orders',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(14,'view_users','View Users','Can view users list','Users',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(15,'add_user','Add User','Can add new users','Users',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(16,'edit_user','Edit User','Can edit user details','Users',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(17,'delete_user','Delete User','Can delete users','Users',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(18,'view_sellers','View Sellers','Can view sellers','Sellers',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(19,'add_seller','Add Seller','Can add new sellers','Sellers',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(20,'edit_seller','Edit Seller','Can edit seller details','Sellers',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(21,'delete_seller','Delete Seller','Can delete sellers','Sellers',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(22,'manage_seller_products','Manage Seller Products','Can manage seller products','Sellers',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(23,'view_shipping','View Shipping','Can view shipping settings','Shipping',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(24,'edit_shipping','Edit Shipping','Can edit shipping settings','Shipping',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(25,'manage_pincodes','Manage Pincodes','Can manage pincodes','Shipping',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(26,'view_reports','View Reports','Can view reports','Reports',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(27,'export_reports','Export Reports','Can export reports','Reports',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(28,'manage_admins','Manage Admin Users','Can add/edit/delete admin users','Admin',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(29,'manage_roles','Manage Roles','Can create and manage roles','Admin',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(30,'manage_permissions','Manage Permissions','Can manage permissions','Admin',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(31,'view_settings','View Settings','Can view settings','Settings',1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(32,'edit_settings','Edit Settings','Can edit settings','Settings',1,'2026-04-13 12:05:42','2026-04-13 12:05:42');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `popup_settings`
--

DROP TABLE IF EXISTS `popup_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `popup_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
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
-- Table structure for table `product_attribute_associations`
--

DROP TABLE IF EXISTS `product_attribute_associations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attribute_associations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `value_id` (`value_id`),
  CONSTRAINT `product_attribute_associations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_associations_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_associations_ibfk_3` FOREIGN KEY (`value_id`) REFERENCES `product_attribute_values` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attribute_associations`
--

LOCK TABLES `product_attribute_associations` WRITE;
/*!40000 ALTER TABLE `product_attribute_associations` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attribute_associations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attribute_selections`
--

DROP TABLE IF EXISTS `product_attribute_selections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attribute_selections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `attribute_value_id` (`attribute_value_id`),
  CONSTRAINT `product_attribute_selections_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_selections_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_attribute_selections_ibfk_3` FOREIGN KEY (`attribute_value_id`) REFERENCES `product_attribute_values` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attribute_selections`
--

LOCK TABLES `product_attribute_selections` WRITE;
/*!40000 ALTER TABLE `product_attribute_selections` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_attribute_selections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attribute_values`
--

DROP TABLE IF EXISTS `product_attribute_values`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attribute_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `attribute_id` (`attribute_id`),
  CONSTRAINT `product_attribute_values_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attribute_values`
--

LOCK TABLES `product_attribute_values` WRITE;
/*!40000 ALTER TABLE `product_attribute_values` DISABLE KEYS */;
INSERT INTO `product_attribute_values` VALUES (11,2,'pink','2026-04-21 11:13:31'),(12,2,'blue','2026-04-21 11:13:37'),(13,2,'white','2026-04-21 11:13:47'),(14,2,'black','2026-04-21 11:13:53'),(22,1,'6','2026-04-23 10:49:45'),(23,1,'9','2026-04-23 10:49:45'),(24,1,'10','2026-04-23 10:49:45'),(25,1,'12','2026-04-23 10:49:45');
/*!40000 ALTER TABLE `product_attribute_values` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_attributes`
--

DROP TABLE IF EXISTS `product_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_attributes`
--

LOCK TABLES `product_attributes` WRITE;
/*!40000 ALTER TABLE `product_attributes` DISABLE KEYS */;
INSERT INTO `product_attributes` VALUES (1,'size','2026-04-21 06:26:17'),(2,'colour','2026-04-21 06:36:19');
/*!40000 ALTER TABLE `product_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
INSERT INTO `product_images` VALUES (7,4,'Home page/Products Offering Discount Images/P4.webp',1,1),(8,5,'Home page/Products Offering Discount Images/P5.webp',1,1),(131,61,'uploads/products/68a6d64a1891f.png',1,1),(132,59,'uploads/products/68a6d680ab658.png',1,1),(133,58,'uploads/products/68a6d6bd3aa93.png',0,1),(134,57,'uploads/products/68a6d6d9ef7e8.png',1,1),(135,56,'uploads/products/68a6d6f33d268.png',0,1),(136,55,'uploads/products/68a6d70eb20dc.png',0,1),(137,52,'uploads/products/68a6d756231a9.jpg',0,1),(138,52,'uploads/products/68a6d7562336e.jpg',0,2),(139,52,'uploads/products/68a6d7562352a.jpg',0,3),(140,38,'uploads/products/68a6d85a0a0b9.png',0,1),(141,38,'uploads/products/68a6d85a0a32e.png',0,2),(142,38,'uploads/products/68a6d85a0a4c5.png',0,3),(143,38,'uploads/products/68a6d85a0a731.png',0,4),(144,37,'uploads/products/68a6d8845b5ff.png',0,1),(145,37,'uploads/products/68a6d8845b7d9.png',0,2),(146,37,'uploads/products/68a6d8845b93b.png',0,3),(147,37,'uploads/products/68a6d8845bae6.png',0,4),(148,36,'uploads/products/68a6d8ae16e17.png',0,1),(149,36,'uploads/products/68a6d8ae17132.png',0,2),(150,36,'uploads/products/68a6d8ae173ce.png',0,3),(151,36,'uploads/products/68a6d8ae17656.png',0,4),(152,35,'uploads/products/68a6d8daa1382.png',0,1),(153,35,'uploads/products/68a6d8daa15cb.png',0,2),(154,35,'uploads/products/68a6d8daa1745.png',0,3),(155,35,'uploads/products/68a6d8daa1952.png',0,4),(156,34,'uploads/products/68a6d90a51cb9.png',0,1),(157,34,'uploads/products/68a6d90a51f62.png',0,2),(158,34,'uploads/products/68a6d90a521c0.png',0,3),(159,34,'uploads/products/68a6d90a52461.png',0,4),(160,33,'uploads/products/68a6d93433ce8.png',0,1),(161,33,'uploads/products/68a6d93433ee6.png',0,2),(162,33,'uploads/products/68a6d934341c7.png',0,3),(163,33,'uploads/products/68a6d9343442b.png',0,4),(164,31,'uploads/products/68a6d964e9bb3.jpg',0,1),(165,31,'uploads/products/68a6d964e9e5d.jpg',0,2),(166,31,'uploads/products/68a6d964ea09a.jpg',0,3),(167,30,'uploads/products/68a6d98bb98c1.jpg',0,1),(168,30,'uploads/products/68a6d98bb9b44.jpg',0,2),(169,30,'uploads/products/68a6d98bb9d71.jpg',0,3),(170,30,'uploads/products/68a6d98bba034.jpg',0,4),(171,29,'uploads/products/68a6d9b188987.jpg',0,1),(172,29,'uploads/products/68a6d9b188bc0.jpg',0,2),(173,29,'uploads/products/68a6d9b188d82.jpg',0,3),(174,29,'uploads/products/68a6d9b188fd8.jpg',0,4),(175,28,'uploads/products/68a6d9d611d17.jpg',0,1),(176,28,'uploads/products/68a6d9d611f67.jpg',0,2),(177,28,'uploads/products/68a6d9d612171.jpg',0,3),(178,28,'uploads/products/68a6d9d6123de.jpg',0,4),(179,27,'uploads/products/68a6da0d03c04.jpg',0,1),(180,27,'uploads/products/68a6da0d03f75.jpg',0,2),(181,27,'uploads/products/68a6da0d041f7.jpg',0,3),(182,27,'uploads/products/68a6da0d0451b.jpg',0,4),(183,22,'uploads/products/68a6db5575211.png',0,1),(184,22,'uploads/products/68a6db5575400.png',0,2),(185,22,'uploads/products/68a6db5575696.png',0,3),(186,22,'uploads/products/68a6db5575856.png',0,4),(187,21,'uploads/products/68a6db79b5ac0.png',0,1),(188,21,'uploads/products/68a6db79b5dd4.png',0,2),(189,20,'uploads/products/68a6db9681b86.png',0,1),(190,19,'uploads/products/68a6dbb34d29c.png',0,1),(191,19,'uploads/products/68a6dbb34d500.png',0,2),(192,18,'uploads/products/68a6dbd1cf939.png',0,1),(193,18,'uploads/products/68a6dbd1cfb19.png',0,2),(203,77,'uploads/products/69eb57911b146.webp',1,1),(204,3,'uploads/products/69f58997294aa.webp',1,1);
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_industries`
--

DROP TABLE IF EXISTS `product_industries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_industries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `industry_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_product_industry` (`product_id`,`industry_id`),
  KEY `industry_id` (`industry_id`),
  CONSTRAINT `product_industries_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_industries_ibfk_2` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_industries`
--

LOCK TABLES `product_industries` WRITE;
/*!40000 ALTER TABLE `product_industries` DISABLE KEYS */;
INSERT INTO `product_industries` VALUES (101,78,1,'2026-05-14 05:59:18'),(102,78,2,'2026-05-14 05:59:18');
/*!40000 ALTER TABLE `product_industries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variation_attributes`
--

DROP TABLE IF EXISTS `product_variation_attributes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variation_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variation_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `variation_id` (`variation_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `attribute_value_id` (`attribute_value_id`),
  CONSTRAINT `product_variation_attributes_ibfk_1` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_variation_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`),
  CONSTRAINT `product_variation_attributes_ibfk_3` FOREIGN KEY (`attribute_value_id`) REFERENCES `product_attribute_values` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variation_attributes`
--

LOCK TABLES `product_variation_attributes` WRITE;
/*!40000 ALTER TABLE `product_variation_attributes` DISABLE KEYS */;
INSERT INTO `product_variation_attributes` VALUES (28,20,1,25),(29,20,2,12),(30,21,1,25),(31,21,2,14);
/*!40000 ALTER TABLE `product_variation_attributes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_variations`
--

DROP TABLE IF EXISTS `product_variations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `mrp` decimal(10,2) DEFAULT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_variations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_variations`
--

LOCK TABLES `product_variations` WRITE;
/*!40000 ALTER TABLE `product_variations` DISABLE KEYS */;
INSERT INTO `product_variations` VALUES (20,78,'',225.00,222.00,350,NULL,1,'2026-04-23 11:37:38','2026-05-14 04:38:56'),(21,78,'',290.00,228.00,400,NULL,1,'2026-04-23 11:43:12','2026-05-14 04:38:56');
/*!40000 ALTER TABLE `product_variations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `package_quantity` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `is_approved` tinyint(1) DEFAULT 1,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_discounted` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `max_quantity_per_order` int(11) DEFAULT NULL COMMENT 'Maximum quantity allowed per order. NULL means no limit.',
  `sort_order` int(11) DEFAULT 0,
  `industry` varchar(255) DEFAULT NULL,
  `industry_id` int(11) DEFAULT NULL,
  `unit` varchar(20) DEFAULT 'No.',
  `per_unit_price` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_active` (`is_active`),
  KEY `idx_products_featured` (`is_featured`),
  KEY `idx_products_discounted` (`is_discounted`),
  KEY `idx_products_max_quantity` (`max_quantity_per_order`),
  KEY `idx_seller_id` (`seller_id`),
  KEY `idx_is_approved` (`is_approved`),
  KEY `idx_products_seller_approved` (`seller_id`,`is_approved`,`is_active`),
  KEY `fk_product_industry` (`industry_id`),
  CONSTRAINT `fk_product_industry` FOREIGN KEY (`industry_id`) REFERENCES `industries` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (3,NULL,'JK COPIER A4 Size Paper','jk-copier-a4-size-paper','PR003','12345678','<p>Premium A4 size copier paper</p><p></p>',310.00,290.00,6,'sgst_cgst',18.00,NULL,25,'uploads/products/69f58a5250baa.webp',75,12,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',100,0,NULL,NULL,'No.',NULL),(4,NULL,'JK Cedar A4 Size Paper','jk-cedar-a4-size-paper','PR004','12345678','Cedar brand A4 size paper',450.00,318.00,29,'sgst_cgst',18.00,NULL,2,'Home page/Products Offering Discount Images/P4.webp',30,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(5,NULL,'JK Easy A4 Size Paper','jk-easy-a4-size-paper','PR005','12345678','Easy brand A4 size paper',290.00,280.00,3,'sgst_cgst',18.00,NULL,2,'Home page/Products Offering Discount Images/P5.webp',60,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(6,NULL,'Drain It Drain Cleaner Powder','drain-it-drain-cleaner-powder','PR006','12345678','Effective drain cleaning powder',150.00,120.00,20,'sgst_cgst',18.00,NULL,3,'asset/household and cleaning/page1/Drain It Drain Cleaner Powder.webp',40,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(7,NULL,'Duster Big Heavy','duster-big-heavy','PR007','12345678','Heavy duty duster for cleaning',80.00,65.00,19,'sgst_cgst',18.00,NULL,3,'asset/household and cleaning/page1/Duster Big Heavy.webp',25,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(8,NULL,'Floor Duster','floor-duster','PR008','12345678','Professional floor duster',120.00,95.00,21,'sgst_cgst',18.00,NULL,3,'asset/household and cleaning/page1/Floor Duster.webp',35,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(9,NULL,'Disposable Gloves Plastic','disposable-gloves-plastic','PR009','12345678','Plastic disposable gloves',200.00,160.00,20,'sgst_cgst',18.00,NULL,1,'asset/kitchen/Disposable Gloves Plastic 80 Pcs.-2nd.webp',80,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(10,NULL,'Exo Anti-Bacterial Dishwash Bar','exo-anti-bacterial-dishwash-bar','PR010','12345678','Anti-bacterial dishwashing bar',45.00,35.00,22,'sgst_cgst',18.00,NULL,1,'asset/kitchen/Exo Anti-Bacterial Dishwash Bar.webp',100,1,1,1,NULL,NULL,NULL,0,1,'2025-06-22 22:25:18',NULL,0,NULL,NULL,'No.',NULL),(14,NULL,'Clear Lens Safety Goggle  Punk Type Generic','clear-lens-safety-goggle-punk-type-generic','EYP PUSGCL 010','90041000','Clear Lens Safety Goggle\r\nPunk Type Generic',34.00,28.00,18,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dc0e93b60.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:06:34',10,5,NULL,NULL,'No.',NULL),(15,NULL,'Clear Lens Safety Goggle  Udyogi UD 71','clear-lens-safety-goggle-udyogi-ud-71','EYP UD71 020','12345678','Clear Lens Safety Goggle\r\nUdyogi UD 71',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbffaffbd.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:09:37',10,4,NULL,NULL,'No.',NULL),(16,NULL,'Clear Lens Safety Goggle  Udyogi UD 91','clear-lens-safety-goggle-udyogi-ud-91','EYP UD91 030','12345678','Clear Lens Safety Goggle\r\nUdyogi UD 91',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbf0c5f34.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:10:32',10,3,NULL,NULL,'No.',NULL),(17,NULL,'Over-The-Spec Safety Goggle   Udyogi UD 30','over-the-spec-safety-goggle-udyogi-ud-30','EYP UD30 040','12345678','Over-The-Spec Safety Goggle\r\n Udyogi UD 30',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbe162dd4.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:11:49',10,2,NULL,NULL,'No.',NULL),(18,NULL,'Clear Lens Safety Goggle  Venus E 102','clear-lens-safety-goggle-venus-e-102','EYP E102 050','12345678','Clear Lens Safety Goggle\r\nVenus E 102',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbd1cf74c.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:13:01',10,0,NULL,NULL,'No.',NULL),(19,NULL,'Stylish Anti-Fog Safety Goggle  Venus E 306','stylish-anti-fog-safety-goggle-venus-e-306','EYP E306 060','12345678','Stylish Anti-Fog Safety Goggle\r\nVenus E 306',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dbb34cf8c.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:15:24',10,1,NULL,NULL,'No.',NULL),(20,NULL,'Over-The-Spec Safety Goggle  Venus E 603','over-the-spec-safety-goggle-venus-e-603','EYP E603 070','12345678','Over-The-Spec Safety Goggle\r\nVenus E 603',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6db96818ef.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:16:26',10,13,NULL,NULL,'No.',NULL),(21,NULL,'Chemical Splash Protection Goggle  Venus E 503','chemical-splash-protection-goggle-venus-e-503','EYP E503 080','12345678','Chemical Splash Protection Goggle\r\nVenus E 503',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6db79b5836.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:17:35',10,12,NULL,NULL,'No.',NULL),(22,NULL,'Clear Lens Safety Goggle  Karam ES 001','clear-lens-safety-goggle-karam-es-001','EYP ES01CL 020','90049090','Clear Lens Safety Goggle\r\nKaram ES 001',90.00,57.00,37,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6db5574f40.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:21:35',100,11,NULL,NULL,'No.',NULL),(23,NULL,'Smoke Lens Safety Goggle  Karam ES 001','smoke-lens-safety-goggle-karam-es-001','EYP ES01BK 021','90049090','Smoke Lens Safety Goggle\r\nKaram ES 001',93.00,60.00,35,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6daf9069de.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:22:46',10,10,NULL,NULL,'No.',NULL),(24,NULL,'Executive Clear Lens Safety Goggle  Karam ES 005','executive-clear-lens-safety-goggle-karam-es-005','EYP ES05 110','12345678','Executive Clear Lens Safety Goggle\r\nKaram ES 005',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dae9c3ccc.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:23:36',10,9,NULL,NULL,'No.',NULL),(25,NULL,'Executive Smoke Lens Safety Goggle  Karam ES 005','executive-smoke-lens-safety-goggle-karam-es-005','EYP ES05 120','12345678','Executive Smoke Lens Safety Goggle\r\nKaram ES 005',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dada3a6c1.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:24:41',10,8,NULL,NULL,'No.',NULL),(26,NULL,'Over-The-Spec Safety Goggle  Karam ES 007','over-the-spec-safety-goggle-karam-es-007','EYP ES07 130','12345678','Over-The-Spec Safety Goggle\r\nKaram ES 007',100.00,90.00,10,'sgst_cgst',18.00,NULL,16,'uploads/products/68a6dacbe97d3.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:25:58',10,7,NULL,NULL,'No.',NULL),(27,NULL,'Labour Safety Shoes  Model - ROCK','labour-safety-shoes-model-rock','FTP ROCK 010','12345678','Labour Safety Shoes\r\nModel - ROCK',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6da0d0389d.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:29:22',10,12,NULL,NULL,'No.',NULL),(28,NULL,'ISI Safety Shoes  Model - POWER PLUS','isi-safety-shoes-model-power-plus','FTP PLUS 020','12345678','ISI Safety Shoes\r\nModel - POWER PLUS',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d9d6119aa.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:30:57',10,11,NULL,NULL,'No.',NULL),(29,NULL,'Labour Safety Shoes  Model - DÉCOR SAFE','labour-safety-shoes-model-d-cor-safe','FTP SAFE 030','12345678','Labour Safety Shoes\r\nModel - DÉCOR SAFE',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d9b1886a0.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:32:28',10,10,NULL,NULL,'No.',NULL),(30,NULL,'ISI Safety Shoes  Model - BESTGO ISI','isi-safety-shoes-model-bestgo-isi','FTP BEST 040','12345678','ISI Safety Shoes\r\nModel - BESTGO ISI',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d98bb95b6.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:35:22',10,9,NULL,NULL,'No.',NULL),(31,NULL,'Executive ISI Safety Shoes  Model - AWSOME','executive-isi-safety-shoes-model-awsome','FTP AWSM 050','12345678','Executive ISI Safety Shoes\r\nModel - AWSOME',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d964e985a.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:37:05',10,8,NULL,NULL,'No.',NULL),(32,NULL,'Ladies Safety Shoes','ladies-safety-shoes','FTP LADY 060','12345678','Ladies Safety Shoes',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d94856e1c.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:38:27',10,7,NULL,NULL,'No.',NULL),(33,NULL,'Gum Boots  Model - BUSY, Height - 11','gum-boots-model-busy-height-11','FTP BUSY 070','12345678','Gum Boots\r\nModel - BUSY, Height - 11\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d93433a43.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:47:04',10,6,NULL,NULL,'No.',NULL),(34,NULL,'Gum Boots  Model - CHOTA GHODA, Height - 11','gum-boots-model-chota-ghoda-height-11','FTP CHGH 080','12345678','Gum Boots\r\nModel - CHOTA GHODA, Height - 11\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d90a5198a.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:48:11',10,5,NULL,NULL,'No.',NULL),(35,NULL,'Gum Boots  Model - CLOUD WITH STEEL TOE ISI, Height - 14','gum-boots-model-cloud-with-steel-toe-isi-height-14','FTP CLUD 090','12345678','Gum Boots\r\nModel - CLOUD WITH STEEL TOE ISI, Height - 14\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8daa1113.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:50:58',10,4,NULL,NULL,'No.',NULL),(36,NULL,'Gum Boots Model - CLOUD W/O STEEL TOE ISI, Height - 14','gum-boots-model-cloud-w-o-steel-toe-isi-height-14','FTP CLUD 100','12345678','Gum Boots\r\nModel - CLOUD W/O STEEL TOE ISI, Height - 14\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8ae16ab5.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:53:26',10,3,NULL,NULL,'No.',NULL),(37,NULL,'Gum Boots  Model - RAINFALL WITH STEEL TOE ISI, Height - 16','gum-boots-model-rainfall-with-steel-toe-isi-height-16','FTP RAIN 110','12345678','Gum Boots\r\nModel - RAINFALL WITH STEEL TOE ISI, Height - 16\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8845b3cd.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:56:07',10,2,NULL,NULL,'No.',NULL),(38,NULL,'Gum Boots  Model - RAINFALL W/O STEEL TOE ISI, Height - 16','gum-boots-model-rainfall-w-o-steel-toe-isi-height-16','FTP RAIN 120','12345678','Gum Boots\r\nModel - RAINFALL W/O STEEL TOE ISI, Height - 16\"',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d85a09e02.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 09:57:28',10,0,NULL,NULL,'No.',NULL),(39,NULL,'Shoe Cover  Plastic Disposable','shoe-cover-plastic-disposable','FTP COVR 130','12345678','Shoe Cover\r\nPlastic Disposable',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d8315b44d.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:00:10',10,19,NULL,NULL,'No.',NULL),(40,NULL,'ESD Slipper','esd-slipper','FTP ESDS 140','12345678','ESD Slipper',100.00,90.00,10,'sgst_cgst',12.00,NULL,17,'uploads/products/68a6d82534f36.jpg',100,1,1,1,NULL,NULL,NULL,0,0,'2025-08-01 10:00:52',10,18,NULL,NULL,'No.',NULL),(41,NULL,'Leather Leg Guard  ESAB','leather-leg-guard-esab','FTP ESAB 150','12345678','Leather Leg Guard\r\nESAB',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/68a6d817a36f9.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:01:40',10,17,NULL,NULL,'No.',NULL),(42,NULL,'Safety Helmet Nape Type  Generic ISI','safety-helmet-nape-type-generic-isi','HED NAPP 010','12345678','Safety Helmet Nape Type\r\nGeneric ISI',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7eb85405.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:05:15',10,12,NULL,NULL,'No.',NULL),(43,NULL,'Safety Helmet Loader  Generic','safety-helmet-loader-generic','HED LOAD 020','12345678','Safety Helmet Loader\r\nGeneric',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7dec6894.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:06:12',10,11,NULL,NULL,'No.',NULL),(44,NULL,'Safety Helmet Nape Type  Karam PN501 White','safety-helmet-nape-type-karam-pn501-white','HED 0501 030','12345678','Safety Helmet Nape Type\r\nKaram PN501 White',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7cd844f2.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:07:26',10,10,NULL,NULL,'No.',NULL),(45,NULL,'Safety Helmet Nape Type  Karam PN501 Grey','safety-helmet-nape-type-karam-pn501-grey','HED 0501 040','12345678','Safety Helmet Nape Type\r\nKaram PN501 Grey',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7ad8c9ea.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:08:16',10,9,NULL,NULL,'No.',NULL),(46,NULL,'Safety Helmet Nape Type  Karam PN501 Blue','safety-helmet-nape-type-karam-pn501-blue','HED 0501 050','12345678','Safety Helmet Nape Type\r\nKaram PN501 Blue',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d7a265513.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:09:36',10,8,NULL,NULL,'No.',NULL),(47,NULL,'Safety Helmet Nape Type  Karam PN501 Yellow','safety-helmet-nape-type-karam-pn501-yellow','HED 0501 060','12345678','Safety Helmet Nape Type\r\nKaram PN501 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d797d9f9e.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:11:32',10,7,NULL,NULL,'No.',NULL),(48,NULL,'Safety Helmet Ratchet Type  Karam PN521 White','safety-helmet-ratchet-type-karam-pn521-white','HED 0521 070','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 White',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d78b5a01f.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:12:56',10,6,NULL,NULL,'No.',NULL),(49,NULL,'Safety Helmet Ratchet Type  Karam PN521 Grey','safety-helmet-ratchet-type-karam-pn521-grey','HED 0521 080','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 Grey',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d77d884d6.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:13:53',10,5,NULL,NULL,'No.',NULL),(50,NULL,'Safety Helmet Ratchet Type  Karam PN521 Blue','safety-helmet-ratchet-type-karam-pn521-blue','HED 0521 090','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 Blue',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d77229dd2.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:14:40',10,4,NULL,NULL,'No.',NULL),(51,NULL,'Safety Helmet Ratchet Type  Karam PN521 Yellow','safety-helmet-ratchet-type-karam-pn521-yellow','HED 0521 100','12345678','Safety Helmet Ratchet Type\r\nKaram PN521 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d767e7816.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:16:06',10,3,NULL,NULL,'No.',NULL),(52,NULL,'Safety Helmet Nape Type  Udyogi UI1211 Yellow','safety-helmet-nape-type-udyogi-ui1211-yellow','HED 1211 110','12345678','Safety Helmet Nape Type\r\nUdyogi UI1211 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d75622fac.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:18:18',10,2,NULL,NULL,'No.',NULL),(53,NULL,'Safety Helmet Ratchet Type  Udyogi Ultra 5000 Yellow','safety-helmet-ratchet-type-udyogi-ultra-5000-yellow','HED 5000 120','12345678','Safety Helmet Ratchet Type\r\nUdyogi Ultra 5000 Yellow',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d732e6d3d.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:19:03',10,0,NULL,NULL,'No.',NULL),(54,NULL,'Disposable Bouffant Cap  Generic','disposable-bouffant-cap-generic','HED BCAP 130','12345678','Disposable Bouffant Cap\r\nGeneric',100.00,90.00,10,'sgst_cgst',18.00,NULL,18,'uploads/products/68a6d723e3cf6.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-01 10:20:12',10,1,NULL,NULL,'No.',NULL),(55,NULL,'Full Body Harness Single Lanyard  Model - E01','full-body-harness-single-lanyard-model-e01','SFB E001 010','12345678','Full Body Harness Single Lanyard\r\nModel - E01',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d70eb1df1.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:13:52',10,3,NULL,NULL,'No.',NULL),(56,NULL,'Full Body Harness Double Lanyard  Model - E02','full-body-harness-double-lanyard-model-e02','SFB E002 020','12345678','Full Body Harness Double Rope\r\nModel - E02',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6f33cfde.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:15:31',10,1,NULL,NULL,'No.',NULL),(57,NULL,'Full Body Harness Single Rope Shock Absorber  Model - E01','full-body-harness-single-rope-shock-absorber-model-e01','SFB E01A 030','12345678','Full Body Harness Single Rope Shock Absorber\r\nModel - E01',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6d1ba52d.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:16:51',10,2,NULL,NULL,'No.',NULL),(58,NULL,'Full Body Harness Double Rope Shock Absorber  Model - E02','full-body-harness-double-rope-shock-absorber-model-e02','SFB E02A 040','12345678','Full Body Harness Double Rope Shock Absorber\r\nModel - E02',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6bd3a820.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:17:51',10,19,NULL,NULL,'No.',NULL),(59,NULL,'Half Body Harness Single Rope  Model - HB1','half-body-harness-single-rope-model-hb1','SFB HB01 050','12345678','Half Body Harness Single Rope\r\nModel - HB1',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d678714b6.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:19:15',10,18,NULL,NULL,'No.',NULL),(60,NULL,'Full Body Harness Double Rope Shock Absorber  Model - Udyogi Eco 04','full-body-harness-double-rope-shock-absorber-model-udyogi-eco-04','SFB ECO4 060','12345678','Full Body Harness Double Rope Shock Absorber\r\nModel - Udyogi Eco 04',100.00,90.00,10,'sgst_cgst',18.00,NULL,19,'uploads/products/68a6d6645c9df.jpg',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:20:51',10,17,NULL,NULL,'No.',NULL),(61,NULL,'Ear Plug  Venus N-101','ear-plug-venus-n-101','EAR N101 010','12345678','Ear Plug\r\nVenus N-101',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d641f326d.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:23:25',10,7,NULL,NULL,'No.',NULL),(62,NULL,'Ear Plug  3M','ear-plug-3m','EAR 3MEP 020','12345678','Ear Plug\r\n3M',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d63090c5b.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:24:08',10,6,NULL,NULL,'No.',NULL),(63,NULL,'Earmuff  Venus N-510','earmuff-venus-n-510','EAR N510 030','12345678','Earmuff\r\nVenus N-510',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d622f019f.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:26:09',10,5,NULL,NULL,'No.',NULL),(64,NULL,'Earmuff  Venus N-530','earmuff-venus-n-530','EAR N530 040','12345678','Earmuff\r\nVenus N-530',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d616bc1a2.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:26:57',10,4,NULL,NULL,'No.',NULL),(65,NULL,'Earmuff  Venus N-550','earmuff-venus-n-550','EAR N550 050','12345678','Earmuff\r\nVenus N-550',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d60b159bc.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:27:59',10,3,NULL,NULL,'No.',NULL),(66,NULL,'Earmuff  Venus N-555','earmuff-venus-n-555','EAR N555 060','12345678','Earmuff\r\nVenus N-555',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d5fb2b955.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:28:54',10,2,NULL,NULL,'No.',NULL),(67,NULL,'Earmuff  Karam EP21','earmuff-karam-ep21','EAR EP21 070','12345678','Earmuff\r\nKaram EP21',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d5e8cfa73.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:29:48',10,0,NULL,NULL,'No.',NULL),(68,NULL,'Helmet Attachable Earmuff  Karam EP23','helmet-attachable-earmuff-karam-ep23','EAR EP23 080','12345678','Helmet Attachable Earmuff\r\nKaram EP23',100.00,90.00,10,'sgst_cgst',18.00,NULL,20,'uploads/products/68a6d5da2d98f.png',100,1,1,1,NULL,NULL,NULL,1,1,'2025-08-12 10:30:41',10,1,NULL,NULL,'No.',NULL),(69,2,'Classmate Book','classmate-book','BOOK1','HSN 1111','Classmate Book',100.00,90.00,10,'sgst_cgst',18.00,NULL,17,'uploads/products/6948d21b96f8a.jpg',100,1,1,1,'2025-12-22 05:18:46',1,'Please add HSN code.',0,0,'2025-12-22 05:07:39',NULL,8,NULL,NULL,'No.',NULL),(71,2,'note Book','note-book','BOOK1','90049090','hi',100.00,90.00,10,'sgst_cgst',0.00,NULL,3,NULL,100,1,1,1,'2026-01-02 06:16:25',1,NULL,0,0,'2025-12-31 13:26:07',NULL,7,NULL,NULL,'No.',NULL),(72,2,'gumboots','gumboots','EYP E102 050','12345678','hiii',100.00,90.00,10,'sgst_cgst',12.00,NULL,17,NULL,100,1,1,1,'2026-01-01 16:11:36',1,NULL,0,0,'2026-01-01 12:06:26',NULL,6,NULL,NULL,'No.',NULL),(73,2,'Gum Boots  Model - RAINFALL W/O STEEL n','gum-boots-model-rainfall-w-o-steel-n','EYP E306 060','12345678','nn',120.00,90.00,25,'sgst_cgst',18.00,NULL,17,NULL,100,1,1,1,'2026-04-21 11:57:36',1,NULL,0,0,'2026-01-02 06:13:02',NULL,5,NULL,NULL,'No.',NULL),(74,NULL,'Clear Anti-Fog Lens Goggle Karam ES001','clear-anti-fog-lens-goggle-karam-es001','EYP ES01AF 022','90049090','Clear Antifog Lens Goggle',123.00,95.00,23,'sgst_cgst',18.00,NULL,16,'uploads/products/696daf38d9941.png',100,1,1,1,NULL,NULL,NULL,1,1,'2026-01-19 04:12:40',100,6,NULL,NULL,'No.',NULL),(75,NULL,'Gas Welder\'s Goggle  Karam ES003','gas-welder-s-goggle-karam-es003','EYP ES03GW 023','90049090','Gas Welder\'s Goggle Karam ES003',1400.00,918.00,34,'sgst_cgst',18.00,NULL,25,NULL,10,1,1,1,NULL,NULL,NULL,1,0,'2026-01-19 05:10:30',10,3,NULL,NULL,'No.',NULL),(77,NULL,'All <br>Tests','all-tests','EYP ES03GW 023','90049090','<p>test product</p>',371.00,321.00,13,'sgst_cgst',5.00,NULL,25,'uploads/products/69eb57863c3bd.jpg',300,12,1,1,NULL,NULL,NULL,0,1,'2026-04-20 07:58:49',200,2,NULL,NULL,'Pair',45.00),(78,NULL,'Tests1','tests1','SKU-0021','12345678','<p>test new</p>',225.00,222.00,1,'sgst_cgst',18.00,NULL,25,'uploads/products/69ea09d9e6d81.webp',500,50,1,1,NULL,NULL,NULL,0,1,'2026-04-20 08:07:16',400,1,'Automotive & Manufacturing',1,'Pair',20.00);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  KEY `fk_rp_permission` (`permission_id`),
  CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,8,'2026-04-13 12:05:42'),(2,1,3,'2026-04-13 12:05:42'),(3,1,19,'2026-04-13 12:05:42'),(4,1,15,'2026-04-13 12:05:42'),(5,1,10,'2026-04-13 12:05:42'),(6,1,13,'2026-04-13 12:05:42'),(7,1,5,'2026-04-13 12:05:42'),(8,1,21,'2026-04-13 12:05:42'),(9,1,17,'2026-04-13 12:05:42'),(10,1,9,'2026-04-13 12:05:42'),(11,1,12,'2026-04-13 12:05:42'),(12,1,4,'2026-04-13 12:05:42'),(13,1,20,'2026-04-13 12:05:42'),(14,1,32,'2026-04-13 12:05:42'),(15,1,24,'2026-04-13 12:05:42'),(16,1,16,'2026-04-13 12:05:42'),(17,1,27,'2026-04-13 12:05:42'),(18,1,28,'2026-04-13 12:05:42'),(19,1,30,'2026-04-13 12:05:42'),(20,1,25,'2026-04-13 12:05:42'),(21,1,6,'2026-04-13 12:05:42'),(22,1,29,'2026-04-13 12:05:42'),(23,1,22,'2026-04-13 12:05:42'),(24,1,7,'2026-04-13 12:05:42'),(25,1,1,'2026-04-13 12:05:42'),(26,1,11,'2026-04-13 12:05:42'),(27,1,2,'2026-04-13 12:05:42'),(28,1,26,'2026-04-13 12:05:42'),(29,1,18,'2026-04-13 12:05:42'),(30,1,31,'2026-04-13 12:05:42'),(31,1,23,'2026-04-13 12:05:42'),(32,1,14,'2026-04-13 12:05:42');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system_role` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Admin','Full access to all features',1,1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(2,'Admin','Full access to most features',1,1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(3,'Manager','Limited access to manage products and orders',1,1,'2026-04-13 12:05:42','2026-04-13 12:05:42'),(4,'Editor','Can edit products and categories',1,1,'2026-04-13 12:05:42','2026-04-13 12:05:42');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seller_activity_log`
--

DROP TABLE IF EXISTS `seller_activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `activity_description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_seller_activity` (`seller_id`,`created_at`),
  CONSTRAINT `seller_activity_log_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller_activity_log`
--

LOCK TABLES `seller_activity_log` WRITE;
/*!40000 ALTER TABLE `seller_activity_log` DISABLE KEYS */;
INSERT INTO `seller_activity_log` VALUES (1,1,'seller_created','Seller account created','2401:4900:8fca:8f5f:45b2:ec42:4608:96a','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','2025-10-09 07:52:11'),(2,1,'status_changed','Seller account deactivated','2401:4900:8fca:8f5f:45b2:ec42:4608:96a','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','2025-10-09 08:02:44'),(3,1,'status_changed','Seller account activated','2401:4900:8fca:8f5f:45b2:ec42:4608:96a','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0','2025-10-09 08:02:55'),(4,2,'seller_created','Seller account created','2401:4900:8fca:b33e:b4fe:6f21:89ac:68e5','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-22 05:01:24'),(5,2,'login','Seller logged in','67.82.81.61','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-22 05:05:28'),(6,2,'product_added','Product \'Classmate Book\' added (ID: 69, pending approval)','67.82.81.61','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-22 05:07:39'),(7,2,'product_rejected','Product ID 69 rejected: Please add HSN code.','2401:4900:8fca:b33e:b4fe:6f21:89ac:68e5','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-22 05:13:05'),(8,2,'product_approved','Product ID 69 approved','2401:4900:8fca:b33e:b4fe:6f21:89ac:68e5','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-22 05:18:46'),(9,2,'login','Seller logged in','2405:201:200e:b93e:698a:dd95:2d29:ac86','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-31 13:25:28'),(10,2,'product_added','Product \'note Book\' added (ID: 71, pending approval)','2405:201:200e:b93e:698a:dd95:2d29:ac86','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-31 13:26:07'),(11,2,'product_rejected','Product ID 71 rejected: not valid','2405:201:200e:b93e:698a:dd95:2d29:ac86','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-31 13:26:34'),(12,2,'product_rejected','Product ID 71 rejected: Please add HSN CODE. You need to enter product description and MRP as well. MRP should be greater than selling price.','2405:201:200e:b93e:698a:dd95:2d29:ac86','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2025-12-31 13:28:41'),(13,2,'product_added','Product \'gumboots\' added (ID: 72, pending approval)','2405:201:200e:b93e:5ce5:f767:f559:5a76','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-01 12:06:26'),(14,2,'product_rejected','Product ID 72 rejected: The hsn code isnt correct.\r\nthe product needs a pic.\r\nthe mrp is not set reasonably.\r\ngst rate is incorrect.','2405:201:200e:b93e:5ce5:f767:f559:5a76','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-01 12:07:51'),(15,2,'login','Seller logged in','2405:201:200e:b93e:5ce5:f767:f559:5a76','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-01 12:17:14'),(16,2,'login','Seller logged in','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 15:55:42'),(17,2,'product_resubmitted','Product \'gumboots-updated\' (ID: 72) resubmitted for approval after rejection','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:05:14'),(18,2,'product_updated','Product \'gumboots-updated\' (ID: 72) updated','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:05:25'),(19,2,'product_rejected','Product ID 72 rejected: product name needs to be updated properly','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:08:04'),(20,2,'product_resubmitted','Product \'gumboots-updated\' (ID: 72) resubmitted for approval after rejection','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:10:23'),(21,2,'product_rejected','Product ID 72 rejected: product name needs to be updated','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:10:46'),(22,2,'product_resubmitted','Product \'gumboots\' (ID: 72) resubmitted for approval after rejection','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:11:11'),(23,2,'product_approved','Product ID 72 approved','2402:e280:3e13:180:8bd7:6d6a:c993:d404','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36','2026-01-01 16:11:36'),(24,2,'login','Seller logged in','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 05:39:42'),(25,2,'product_resubmitted','Product \'note Book\' (ID: 71) resubmitted for approval after rejection','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 05:40:10'),(26,2,'product_added','Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' added (ID: 73, pending approval)','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 06:13:02'),(27,2,'product_rejected','Product ID 73 rejected: hsn code inccorect\r\nname is not right\r\nmrp not realistic\r\nstock quantity invalid','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 06:13:44'),(28,2,'product_resubmitted','Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' (ID: 73) resubmitted for approval after rejection','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 06:15:20'),(29,2,'product_updated','Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' (ID: 73) updated','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 06:15:50'),(30,2,'product_approved','Product ID 71 approved','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 06:16:25'),(31,2,'product_updated','Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' (ID: 73) updated','2405:201:200e:b93e:184e:8a2b:3822:3442','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-02 06:17:04'),(32,2,'login','Seller logged in','2405:201:200e:b93e:ade3:6d0d:e495:bf22','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0','2026-01-18 05:48:53'),(33,2,'product_approved','Product ID 73 approved','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','2026-04-21 11:57:36');
/*!40000 ALTER TABLE `seller_activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `seller_order_items`
--

DROP TABLE IF EXISTS `seller_order_items`;
/*!50001 DROP VIEW IF EXISTS `seller_order_items`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `seller_order_items` AS SELECT
 1 AS `id`,
  1 AS `order_id`,
  1 AS `order_number`,
  1 AS `tracking_id`,
  1 AS `order_date`,
  1 AS `order_total`,
  1 AS `order_status_id`,
  1 AS `order_status`,
  1 AS `product_id`,
  1 AS `product_name`,
  1 AS `seller_id`,
  1 AS `seller_name`,
  1 AS `quantity`,
  1 AS `price`,
  1 AS `unit_price`,
  1 AS `gst_amount`,
  1 AS `item_total`,
  1 AS `customer_name`,
  1 AS `email`,
  1 AS `phone` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `seller_permissions`
--

DROP TABLE IF EXISTS `seller_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `can_manage_products` tinyint(1) DEFAULT 1,
  `can_manage_categories` tinyint(1) DEFAULT 1,
  `can_view_orders` tinyint(1) DEFAULT 1,
  `can_view_reports` tinyint(1) DEFAULT 1,
  `can_update_settings` tinyint(1) DEFAULT 1,
  `can_add_products` tinyint(1) DEFAULT 1,
  `can_edit_products` tinyint(1) DEFAULT 1,
  `can_delete_products` tinyint(1) DEFAULT 0,
  `max_products` int(11) DEFAULT 100,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_seller_permissions` (`seller_id`),
  CONSTRAINT `seller_permissions_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller_permissions`
--

LOCK TABLES `seller_permissions` WRITE;
/*!40000 ALTER TABLE `seller_permissions` DISABLE KEYS */;
INSERT INTO `seller_permissions` VALUES (1,1,1,1,1,1,1,1,1,0,100,'2025-10-09 07:52:11','2025-10-09 07:52:11'),(2,2,1,1,1,1,1,1,1,0,100,'2025-12-22 05:01:24','2025-12-22 05:01:24');
/*!40000 ALTER TABLE `seller_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seller_product_approval_history`
--

DROP TABLE IF EXISTS `seller_product_approval_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_product_approval_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `action_by` int(11) NOT NULL,
  `action_date` timestamp NULL DEFAULT current_timestamp(),
  `comments` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `seller_id` (`seller_id`),
  KEY `action_by` (`action_by`),
  CONSTRAINT `seller_product_approval_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_product_approval_history_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seller_product_approval_history_ibfk_3` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller_product_approval_history`
--

LOCK TABLES `seller_product_approval_history` WRITE;
/*!40000 ALTER TABLE `seller_product_approval_history` DISABLE KEYS */;
INSERT INTO `seller_product_approval_history` VALUES (1,69,2,'rejected',1,'2025-12-22 05:13:05','Please add HSN code.'),(2,69,2,'approved',1,'2025-12-22 05:18:46','Product approved by admin'),(3,71,2,'rejected',1,'2025-12-31 13:26:34','not valid'),(4,71,2,'rejected',1,'2025-12-31 13:28:41','Please add HSN CODE. You need to enter product description and MRP as well. MRP should be greater than selling price.'),(5,72,2,'rejected',1,'2026-01-01 12:07:51','The hsn code isnt correct.\r\nthe product needs a pic.\r\nthe mrp is not set reasonably.\r\ngst rate is incorrect.'),(6,72,2,'rejected',1,'2026-01-01 16:08:04','product name needs to be updated properly'),(7,72,2,'rejected',1,'2026-01-01 16:10:46','product name needs to be updated'),(8,72,2,'approved',1,'2026-01-01 16:11:36','Product approved by admin'),(9,73,2,'rejected',1,'2026-01-02 06:13:44','hsn code inccorect\r\nname is not right\r\nmrp not realistic\r\nstock quantity invalid'),(10,71,2,'approved',1,'2026-01-02 06:16:25','Product approved by admin'),(11,73,2,'approved',1,'2026-04-21 11:57:36','Product approved by admin');
/*!40000 ALTER TABLE `seller_product_approval_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seller_statistics`
--

DROP TABLE IF EXISTS `seller_statistics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `seller_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seller_id` int(11) NOT NULL,
  `total_products` int(11) DEFAULT 0,
  `active_products` int(11) DEFAULT 0,
  `pending_approval_products` int(11) DEFAULT 0,
  `total_orders` int(11) DEFAULT 0,
  `total_revenue` decimal(10,2) DEFAULT 0.00,
  `commission_paid` decimal(10,2) DEFAULT 0.00,
  `pending_commission` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_seller_stats` (`seller_id`),
  CONSTRAINT `seller_statistics_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seller_statistics`
--

LOCK TABLES `seller_statistics` WRITE;
/*!40000 ALTER TABLE `seller_statistics` DISABLE KEYS */;
INSERT INTO `seller_statistics` VALUES (1,1,0,NULL,NULL,0,0.00,0.00,0.00,'2025-10-09 07:52:45'),(5,2,4,4,0,1,90.00,0.00,9.00,'2026-04-21 11:57:36');
/*!40000 ALTER TABLE `seller_statistics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sellers`
--

DROP TABLE IF EXISTS `sellers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sellers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `gst_number` varchar(50) DEFAULT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `business_phone` varchar(20) DEFAULT NULL,
  `bank_account_name` varchar(255) DEFAULT NULL,
  `bank_account_number` varchar(50) DEFAULT NULL,
  `bank_ifsc_code` varchar(20) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `commission_percentage` decimal(5,2) DEFAULT 10.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_seller` (`user_id`),
  CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sellers`
--

LOCK TABLES `sellers` WRITE;
/*!40000 ALTER TABLE `sellers` DISABLE KEYS */;
INSERT INTO `sellers` VALUES (1,4,'Online','Sole Proprietorship','','','','','','','','','',10.00,1,'2025-10-09 07:52:11','2025-10-09 08:02:55'),(2,10,'Nirmit Group of Companies','Sole Proprietorship','','','','','','','','','',10.00,1,'2025-12-22 05:01:24','2025-12-22 05:01:24');
/*!40000 ALTER TABLE `sellers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `serviceable_pincodes`
--

DROP TABLE IF EXISTS `serviceable_pincodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `serviceable_pincodes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pincode` varchar(10) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `pincode` (`pincode`),
  KEY `idx_serviceable_pincodes_pincode` (`pincode`),
  KEY `idx_serviceable_pincodes_active` (`is_active`)
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `charge_type` enum('fixed','percentage') NOT NULL DEFAULT 'fixed',
  `charge_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_order_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_shipping_charges_zone` (`zone_id`,`is_active`),
  CONSTRAINT `shipping_charges_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `zone_id` int(11) NOT NULL,
  `location_type` enum('country','state','city','pincode') NOT NULL,
  `location_value` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_location` (`zone_id`,`location_type`,`location_value`),
  KEY `idx_shipping_zone_locations` (`location_type`,`location_value`),
  CONSTRAINT `shipping_zone_locations_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_email` (`email`),
  KEY `idx_users_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'prakash raje','prakash.raje7@gmail.com','$2y$10$xZYAgVOHQ7QSZz0mh/0avOY562C4quE.Z5u4I5rhbGXmUgeBodScy','customer',0,NULL,NULL,'08788316633',NULL,NULL,NULL,NULL,1,'2025-06-22 22:27:50',NULL,NULL),(4,'Nirmit Shah','nirmitshah2006@gmail.com','$2y$10$i7sstMwnwFl3KKdxUByjA.MX19qccKhmXP2bWF3r68ibOqt/k1/lO','seller',1,'2025-10-09 07:52:11',1,'8208917546',NULL,NULL,NULL,NULL,1,'2025-07-29 15:48:02',NULL,NULL),(5,'SAMIR SHAH','inprotech.cto@gmail.com','$2y$10$.61YdqsFwPLIdEWnYTwRt.THuktqIXgPltPIoVwWLVC.3HqaXUzky','customer',0,NULL,NULL,'8780406230',NULL,NULL,NULL,NULL,1,'2025-08-17 10:18:06',NULL,NULL),(6,'Khushali Modi','khushalimodi1998@gmail.com','$2y$10$Dg9rJJFvn47h6IaCIY5Ft.mGP9YAXvY6UE.PIqpjt8t2IdidvXZpy','customer',0,NULL,NULL,'8780086227',NULL,NULL,NULL,NULL,1,'2025-08-27 15:43:12',NULL,NULL),(10,'Nirmit Shah','nirmitsamirshah123@gmail.com','$2y$10$COquJkvO/Slz3GNzmCqNyORSEKL.Q9G548Si8P28fOuyJtbWpdHtO','seller',1,'2025-12-22 05:01:24',1,'08208917546',NULL,NULL,NULL,NULL,1,'2025-12-22 04:55:06',NULL,NULL),(12,'sachin ringe','sachinringe@yahoo.co.in','$2y$10$d08bgD9eV7nN9Vtk8vI2Lu/ylL9dcrhVzt9KsGU4VFkunpP6EvQYK','customer',0,NULL,NULL,'9998724576',NULL,NULL,NULL,NULL,1,'2026-01-31 06:27:19',NULL,NULL),(14,'p m','mpradnya5@gmail.com','$2y$10$fwfPPI6xAvIxNLbdx1jW8uwZvpGU6RlWteFoIk0Uq6wtKtf6avnOa','customer',0,NULL,NULL,'9999999999',NULL,NULL,NULL,NULL,1,'2026-02-11 04:08:34',NULL,NULL),(16,'Shital Yelkar','yelkar.shital@gmail.com','$2y$10$rF8P3b1bHt9hF90RxtSP/u48hQreMqufaTep.oVbKj93WBKe5DmNi','customer',0,NULL,NULL,'7387942042',NULL,NULL,NULL,NULL,1,'2026-02-18 07:31:10',NULL,NULL),(18,'k gaikwad','gaikwadkomalgaikwad2000@gmail.com','$2y$10$Xg2Xg6COJuxx/j3fEK0lL.z0GcYdUdPsdz5i2KwqwEdY767VxYjha','customer',0,NULL,NULL,'9270316633',NULL,NULL,NULL,NULL,1,'2026-04-23 12:43:10',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wishlist`
--

DROP TABLE IF EXISTS `wishlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_wishlist_user` (`user_id`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wishlist`
--

LOCK TABLES `wishlist` WRITE;
/*!40000 ALTER TABLE `wishlist` DISABLE KEYS */;
INSERT INTO `wishlist` VALUES (8,1,4,'2025-07-23 06:43:46'),(9,1,8,'2025-07-23 13:05:12'),(13,5,66,'2026-02-18 06:25:35'),(14,16,7,'2026-02-18 07:32:01'),(76,18,53,'2026-05-07 07:36:59');
/*!40000 ALTER TABLE `wishlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Current Database: `inprotech`
--

USE `inprotech`;

--
-- Final view structure for view `seller_order_items`
--

/*!50001 DROP VIEW IF EXISTS `seller_order_items`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`u141519101_everythingb2c1`@`127.0.0.1` SQL SECURITY DEFINER */
/*!50001 VIEW `seller_order_items` AS select `oi`.`id` AS `id`,`oi`.`order_id` AS `order_id`,`o`.`order_number` AS `order_number`,`o`.`tracking_id` AS `tracking_id`,`o`.`created_at` AS `order_date`,`o`.`total_amount` AS `order_total`,`o`.`order_status_id` AS `order_status_id`,`os`.`name` AS `order_status`,`oi`.`product_id` AS `product_id`,`p`.`name` AS `product_name`,`p`.`seller_id` AS `seller_id`,`s`.`business_name` AS `seller_name`,`oi`.`quantity` AS `quantity`,`oi`.`price` AS `price`,`oi`.`unit_price` AS `unit_price`,`oi`.`gst_amount` AS `gst_amount`,`oi`.`price` * `oi`.`quantity` AS `item_total`,`u`.`name` AS `customer_name`,`u`.`email` AS `email`,`u`.`phone` AS `phone` from (((((`order_items` `oi` join `products` `p` on(`oi`.`product_id` = `p`.`id`)) left join `sellers` `s` on(`p`.`seller_id` = `s`.`id`)) join `orders` `o` on(`oi`.`order_id` = `o`.`id`)) left join `order_statuses` `os` on(`o`.`order_status_id` = `os`.`id`)) join `users` `u` on(`o`.`user_id` = `u`.`id`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-30 10:25:34
