-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 04, 2026 at 04:37 AM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u728317772_everythingb2c`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

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

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `name`, `phone`, `pincode`, `address_line1`, `address_line2`, `city`, `state`, `is_default`, `created_at`) VALUES
(10, 1, 'Prakash', '8180099165', '415503', 'A/p Shirdhon, Tal- Koregaon, Dist- Satara', '', 'Pune', 'Maharashtra', 1, '2025-07-09 10:47:00'),
(12, 4, 'Nirmit Shah', '08208917546', '411001', 'abc', '', 'Pune', 'Maharashtra', 0, '2025-07-29 15:48:48'),
(13, 4, 'Nirmit Shah', '08208917546', '38011', 'abc', '', 'baroda', 'gujarat', 0, '2025-07-29 17:52:54'),
(14, 5, 'SAMIR CHANDRAKANTBHAI SHAH', '08780406230', '411001', 'NEAR BHARAT FORGE', '', 'Pune', 'Maharashtra', 1, '2025-08-17 10:19:25'),
(15, 6, 'Khushali modi', '8780086227', '390021', 'J-203 Satva flats', '', 'Vadodara', 'Gujarat', 1, '2025-08-27 15:45:10'),
(19, 14, 'p', '9999999999', '415018', 'pune', '', 'pune', 'maharashtra', 0, '2026-02-13 03:53:50'),
(22, 18, 'Suman Ray', '7063364293', '736135', 'Ward No. 07', 'Boarding Para', 'Dinhata', 'West Bengal', 0, '2026-02-27 21:14:19'),
(25, 17, 'shital', '7387942042', '412307', 'nandini springfield', 'hadapsar', 'pune', 'maharashtra', 0, '2026-03-18 04:29:57'),
(26, 17, 'shital', '7387942042', '444001', '132 kv power house colony', 'gorakshan road', 'akola', 'maharashtra', 0, '2026-03-24 10:53:28'),
(27, 10, 'nim', '08208917546', '411001', 'Siciliaa', '', 'Pune', 'Maharashtra', 0, '2026-03-25 06:34:50'),
(34, 24, 'Shital Yelkar', '7387942042', '412307', 'nandini springfield tk ng', 'hadapsar', 'pune', 'maharashtra', 1, '2026-03-31 11:43:09'),
(43, 30, 'shital', '7387942042', '412307', 'nandini springfielddibdkjbvikbv kdjbvskdjvbwivkbwvik', 'b 604 sodhfssdkvnws', 'pune fhnwkdrvjnwkfvn', 'Maharashtra', 0, '2026-06-13 08:09:19'),
(44, 30, 'shital', '9876543210', '411001', 'abc 123', 'abc 123', 'mumbai', 'maharashtra', 1, '2026-06-13 08:11:48'),
(48, 30, '12356165 6546546 654654+56-+-+', 'abcdefghsdjvhskdfvjb', '0000000000', '12345678 12345', '123445123457852562395623956326523', '211345618562385621356230586230', 'Port Blair', 0, '2026-06-19 09:43:51'),
(49, 31, 'sachin ringe', '9998724576', '390015', 'A-3, anant park soc, near sabri school, vasna road', 'vadodara', 'vadodara', 'gujarat', 1, '2026-09-30 04:20:18'),
(50, 12, 'sachin ringe', '9998724576', '390015', 'A-3, anant park soc, near sabri school, vasna road', 'vadodara', 'vadodara', 'gujarat', 1, '2026-10-05 05:03:40');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `role` enum('super_admin','admin','manager') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role_id`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'admin@everythingb2c.com', '$2y$10$nwNkYiFBWQMOxZxQ1jnNB.LjOCgVte2U7w3kWRR0T96a.x4QHLbgG', 1, 'super_admin', 1, '2026-10-30 10:53:53', '2025-06-28 06:15:55', '2026-10-30 10:53:53'),
(2, 'secondary admin', 'admin@everythingb2c.in', '$2y$10$mhMsd/ag0qKP3yx6XSbZW.9VDOw.kf4N3AYp6JwPr83USTnYgIFIu', 4, 'admin', 1, '2026-06-23 05:55:16', '2026-03-08 19:50:08', '2026-06-23 05:55:16'),
(3, 'Sachin', 'productmanager@everythingb2c.co.in', '$2y$10$vC/0s8VmznJLY.phtV3aLek6wDO.ZxkukdZ4Lpy.WcyHaooMN29S6', 5, 'admin', 1, '2026-03-16 10:05:32', '2026-03-10 09:35:51', '2026-03-16 10:05:32');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `mobile_image_path` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `button_config` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `image_path`, `mobile_image_path`, `title`, `is_active`, `order_index`, `created_at`, `button_config`) VALUES
(1, 'uploads/banners/6a196526953b7-b1.webp', 'uploads/banners/9abb11999d581f14bab90fdebaea65e0.jpg', '', 1, 4, '2026-05-29 10:06:30', '{\"enabled\":false,\"text\":\"Start Selling\",\"url\":\"seller/login.php\",\"left\":35.1,\"top\":71.8,\"width\":11.3,\"height\":14}'),
(2, 'uploads/banners/6a1965455ef6c-b2.webp', 'uploads/banners/bd084ec4e61e8716d07617df679f8ca5.jpg', '', 1, 5, '2026-05-29 10:07:01', '{\"enabled\":false,\"text\":\"Start Selling\",\"url\":\"seller/login.php\",\"left\":35.1,\"top\":71.8,\"width\":11.3,\"height\":14}'),
(3, 'uploads/banners/6a19654f1cf8b-b3.webp', 'uploads/banners/4743894531246587de059c0417c9c851.jpg', '', 1, 3, '2026-05-29 10:07:11', '{\"enabled\":false,\"text\":\"Start Selling\",\"url\":\"seller/login.php\",\"left\":35.1,\"top\":71.8,\"width\":11.3,\"height\":14}'),
(5, 'uploads/banners/18edf285887fabe5bdc5ff1bc959822b.jpg', 'uploads/banners/72facc3b524cb900e0db04dd9c2dc315.jpg', '', 1, 1, '2026-09-25 05:37:41', '{\"enabled\":true,\"text\":\"Start Selling\",\"url\":\"seller/login.php\",\"left\":35.1,\"top\":71.8,\"width\":11.3,\"height\":14}'),
(6, 'uploads/banners/6ab78c292bb71-banner_5.jpg', 'uploads/banners/dc5e5e48e485060a843dcce8db619468.jpg', '', 1, 2, '2026-09-25 08:11:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `image`, `created_at`) VALUES
(1, 'Apsara', NULL, '2026-09-23 10:45:41'),
(2, 'Doms', NULL, '2026-09-23 10:49:42');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `variation_id` int(11) DEFAULT NULL,
  `selected_attributes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `variation_id`, `selected_attributes`) VALUES
(314, 17, 212, 2, '2026-03-24 10:18:18', NULL, NULL),
(315, 17, 211, 1, '2026-03-24 10:20:07', NULL, NULL),
(404, 1, 235, 1, '2026-03-27 10:34:05', NULL, NULL),
(410, 24, 235, 2, '2026-03-31 14:44:34', NULL, NULL),
(411, 24, 233, 1, '2026-04-06 13:03:57', NULL, NULL),
(412, 24, 234, 1, '2026-04-14 11:59:38', NULL, NULL),
(462, 31, 362, 1, '2026-10-02 09:45:33', NULL, '[]'),
(463, 31, 361, 1, '2026-10-02 09:45:34', NULL, '[]'),
(464, 31, 290, 1, '2026-10-02 09:45:54', NULL, '[]'),
(465, 31, 297, 1, '2026-10-02 09:45:56', NULL, '[]'),
(470, 23, 233, 1, '2026-10-05 08:36:06', NULL, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

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

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `seller_id`, `name`, `slug`, `image`, `description`, `product_count`, `is_approved`, `created_at`, `parent_id`) VALUES
(2, NULL, 'Office Stationery', 'office-stationery', 'uploads/categories/6ac9d0dc3639c.jpg', 'Office and school stationery items', 4, 1, '2025-06-22 22:25:18', NULL),
(4, NULL, 'Personal Care', 'personal-care', 'uploads/categories/6ac9db7843320.jpg', 'Personal care and hygiene products', 0, 1, '2025-06-22 22:25:18', NULL),
(6, NULL, 'Home & Garden', 'home-garden', 'uploads/categories/68a0c315ed059.webp', 'Home and garden supplies', 0, 1, '2025-06-22 22:25:18', NULL),
(9, NULL, 'Industrial Safety Products', 'industrial-safety-products', 'uploads/categories/68a0c320072fc.webp', '', 0, 1, '2025-07-23 06:22:44', NULL),
(16, NULL, 'Eye Protection', 'eye-protection', 'uploads/categories/696da1e815108.png', 'Eye Protection', 0, 1, '2025-08-01 09:01:00', 9),
(17, NULL, 'Foot Protection', 'foot-protection', 'uploads/categories/696da1f7c4c28.png', 'Foot Protection', 0, 1, '2025-08-01 09:27:36', 9),
(18, NULL, 'Head Protection', 'head-protection', 'uploads/categories/696da20ad6b54.png', '', 0, 1, '2025-08-01 10:04:10', 9),
(19, NULL, 'Fall Protection', 'fall-protection', 'uploads/categories/696da21acaee5.png', 'Fall Protection', 0, 1, '2025-08-12 10:12:02', 9),
(20, NULL, 'Ear Protection', 'ear-protection', 'uploads/categories/696da1d2a9b59.png', '', 0, 1, '2025-08-12 10:21:08', 9),
(21, NULL, 'School Stationery', 'school-stationery', 'uploads/categories/6ab4fb608913e.jpg', 'School Stationery', 0, 1, '2025-08-16 17:37:35', NULL),
(22, NULL, 'Packaging Material', 'packaging-material', 'uploads/categories/6ac9d2251f86c.jpg', '', 0, 1, '2025-08-16 17:38:57', NULL),
(25, NULL, 'Protective Workwear', 'protective-workwear', 'uploads/categories/69b7edc1454e2.jpg', '', 0, 1, '2026-03-06 06:55:31', 9),
(26, NULL, 'Respiratory Protection', 'respiratory-protection', 'uploads/categories/69b7dfa6f3dc0.jpg', '', 0, 1, '2026-03-16 10:47:02', 9),
(28, NULL, 'Face Protection', 'face-protection', 'uploads/categories/69b7e47ae1fc1.jpg', '', 0, 1, '2026-03-16 11:07:38', 9),
(29, NULL, 'Arm Protection', 'arm-protection', 'uploads/categories/69b7e817de0bb.jpg', '', 0, 1, '2026-03-16 11:23:03', 9),
(30, NULL, 'Hand Protection', 'hand-protection', 'uploads/categories/69b7f3e14a740.jpg', '', 0, 1, '2026-03-16 12:13:21', 9),
(31, NULL, 'Fire Extinguishers', 'fire-extinguishers', 'uploads/categories/69ba95a8df988.jpg', 'Fire Extinguishers', 0, 1, '2026-03-18 12:08:08', 9),
(32, NULL, 'Construction Industry', 'construction-industry', 'uploads/categories/69ba99045bcde.jpg', 'Construction Industry', 0, 1, '2026-03-18 12:22:28', 9),
(34, NULL, 'Boxes & Cartons', 'boxes-cartons', 'uploads/categories/6a9522dae3538.jpg', '', 0, 1, '2026-08-20 06:53:43', 22),
(35, NULL, 'Courier & Shipping Bags', 'courier-shipping-bags', 'uploads/categories/6a951c0ade3c6.jpg', '', 0, 1, '2026-08-20 06:54:26', 22),
(36, NULL, 'Bubble Wrap & Air Bubble Rolls', 'bubble-wrap-air-bubble-rolls', 'uploads/categories/6a951b917a502.jpg', '', 0, 1, '2026-08-20 06:54:41', 22),
(37, NULL, 'Stretch Film & Cling Film', 'stretch-film-cling-film', 'uploads/categories/6a9520c4ee1d7.jpg', '', 0, 1, '2026-08-20 06:54:55', 22),
(38, NULL, 'Packaging Tapes', 'packaging-tapes', 'uploads/categories/6a95202432a65.jpg', '', 0, 1, '2026-08-20 06:55:06', 22),
(39, NULL, 'Packaging Paper', 'packaging-paper', 'uploads/categories/6a951f317ff18.jpg', '', 0, 1, '2026-08-20 06:55:17', 22),
(40, NULL, 'Wrapping Materials', 'wrapping-materials', 'uploads/categories/6a952233e5a8a.jpg', '', 0, 1, '2026-08-20 06:55:46', 22),
(41, NULL, 'Thermocol & Foam Packaging', 'thermocol-foam-packaging', 'uploads/categories/6a9521beecf8a.jpg', '', 0, 1, '2026-08-20 06:55:57', 22),
(42, NULL, 'Packaging Straps & Accessories', 'packaging-straps-accessories', 'uploads/categories/6a951f9941b5f.jpg', '', 0, 1, '2026-08-20 06:56:19', 22),
(43, NULL, 'Labels & Stickers', 'labels-stickers', 'uploads/categories/6a951ec1a6f48.jpg', '', 0, 1, '2026-08-20 06:56:37', 22),
(44, NULL, 'Packaging Tools & Equipment', 'packaging-tools-equipment', 'uploads/categories/6a952083255a1.jpg', '', 0, 1, '2026-08-20 06:56:49', 22),
(45, NULL, 'Food Packaging Materials', 'food-packaging-materials', 'uploads/categories/6a951c8342ce2.jpg', '', 0, 1, '2026-08-20 06:57:02', 22),
(46, NULL, 'Garment & Clothing Packaging', 'garment-clothing-packaging', 'uploads/categories/6a951d3944477.jpg', '', 0, 1, '2026-08-20 06:57:16', 22),
(47, NULL, 'Gift Packaging', 'gift-packaging', 'uploads/categories/6a951dc019ccf.jpg', '', 0, 1, '2026-08-20 06:57:27', 22),
(48, NULL, 'Industrial Packaging Materials', 'industrial-packaging-materials', 'uploads/categories/6a951e7d46fd2.jpg', '', 0, 1, '2026-08-20 06:57:40', 22),
(49, NULL, 'Punch', 'punch', 'uploads/categories/6a8846a2a34e1.jpg', '', 0, 1, '2026-08-21 12:37:54', 2),
(50, NULL, 'Stapler & Pins', 'stapler-pins', 'uploads/categories/6a9284a289b42.jpg', '', 0, 1, '2026-08-21 12:38:41', 2),
(51, NULL, 'Office and Copier Paper', 'office-and-copier-paper', 'uploads/categories/6a893054642b5.jpg', '', 0, 1, '2026-08-22 05:15:00', 2),
(52, NULL, 'Photo Paper', 'photo-paper', 'uploads/categories/6a8946f22f896.jpg', '', 0, 1, '2026-08-22 06:51:30', 2),
(53, NULL, 'Scissors', 'scissors', 'uploads/categories/6a8960e78e336.jpg', '', 0, 1, '2026-08-22 08:41:47', 2),
(54, NULL, 'Rubber Bands', 'rubber-bands', 'uploads/categories/6a89679087f58.jpg', '', 0, 1, '2026-08-22 09:10:40', 2),
(55, NULL, 'Glue Sticks', 'glue-sticks', 'uploads/categories/6a896dbd70b35.jpg', '', 0, 1, '2026-08-22 09:37:01', 2),
(56, NULL, 'Art & Craft', 'art-craft', 'uploads/categories/6a8c0c17880ed.jpg', '', 0, 1, '2026-08-24 09:17:11', 21),
(57, NULL, 'Pens & Refills', 'pens-refills', 'uploads/categories/6a8c10775f224.jpg', '', 0, 1, '2026-08-24 09:31:56', 2),
(58, NULL, 'Markers & Highlighters', 'markers-highlighters', 'uploads/categories/6a8c1d860d9f2.jpg', '', 0, 1, '2026-08-24 10:27:13', 2),
(59, NULL, 'Notebooks, Writing Pads & Diaries', 'notebooks-writing-pads-diaries', 'uploads/categories/6a8c264ec0c65.jpg', '', 0, 1, '2026-08-24 11:09:02', 2),
(60, NULL, 'Beauty', 'beauty', 'uploads/categories/6a8d2d3f3d88e.jpg', '', 0, 1, '2026-08-25 05:50:55', 4),
(61, NULL, 'Sewing', 'sewing', 'uploads/categories/6a8fc34f3b1fc.jpg', '', 0, 1, '2026-08-27 04:38:18', 6),
(62, NULL, 'Pins & Pincushions', 'pins-pincushions', 'uploads/categories/6a8fd6bb566a3.jpg', '', 0, 1, '2026-08-27 06:18:35', 4),
(63, NULL, 'Pencils', 'pencils', 'uploads/categories/6a9263741d346.jpg', '', 0, 1, '2026-08-29 04:41:56', 21),
(64, NULL, 'Eraser', 'eraser', 'uploads/categories/6ab0e1c554f88.jpg', '', 0, 1, '2026-08-29 05:07:44', 21),
(65, NULL, 'Geometry Box', 'geometry-box', 'uploads/categories/6a926c2f06cf0.jpg', '', 0, 1, '2026-08-29 05:20:47', 21),
(66, NULL, 'Glue Stick', 'glue-stick', 'uploads/categories/6a926fcb12191.jpg', '', 0, 1, '2026-08-29 05:36:11', 21),
(67, NULL, 'Stamp Pad', 'stamp-pad', 'uploads/categories/6a927dba9f779.jpg', '', 0, 1, '2026-08-29 06:35:38', 2),
(68, NULL, 'Scale', 'scale', 'uploads/categories/6a92aba9b1b0a.jpg', '', 0, 1, '2026-08-29 09:51:37', 21),
(69, NULL, 'Sharpener', 'sharpener', 'uploads/categories/6a92ae35c9b5f.jpg', '', 0, 1, '2026-08-29 10:02:29', 21),
(70, NULL, 'Sketch Pen', 'sketch-pen', 'uploads/categories/6a92b17d25d2b.jpg', '', 0, 1, '2026-08-29 10:16:29', 21),
(71, NULL, 'Cutters', 'cutters', 'uploads/categories/6aafb3409b9f5.jpg', 'Cutters', 0, 1, '2026-09-20 10:19:44', 2),
(72, NULL, 'Return Gifts', 'return-gifts', 'uploads/categories/6aafb9ad4f4bb.jpg', 'Return Gifts', 0, 1, '2026-09-20 10:47:09', 21);

-- --------------------------------------------------------

--
-- Table structure for table `category_parent_assignments`
--

CREATE TABLE `category_parent_assignments` (
  `category_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category_parent_assignments`
--

INSERT INTO `category_parent_assignments` (`category_id`, `parent_id`, `is_primary`, `created_at`) VALUES
(16, 9, 1, '2026-09-01 05:30:05'),
(17, 9, 1, '2026-09-01 05:30:05'),
(18, 9, 1, '2026-09-01 05:30:05'),
(19, 9, 1, '2026-09-01 05:30:05'),
(20, 9, 1, '2026-09-01 05:30:05'),
(25, 9, 1, '2026-09-01 05:30:05'),
(26, 9, 1, '2026-09-01 05:30:05'),
(28, 9, 1, '2026-09-01 05:30:05'),
(29, 9, 1, '2026-09-01 05:30:05'),
(30, 9, 1, '2026-09-01 05:30:05'),
(31, 9, 1, '2026-09-01 05:30:05'),
(32, 9, 1, '2026-09-01 05:30:05'),
(34, 22, 1, '2026-09-01 05:30:05'),
(35, 22, 1, '2026-09-01 05:30:05'),
(36, 22, 1, '2026-09-01 05:30:05'),
(37, 22, 1, '2026-09-01 05:30:05'),
(38, 22, 1, '2026-09-01 05:30:05'),
(39, 22, 1, '2026-09-01 05:30:05'),
(40, 22, 1, '2026-09-01 05:30:05'),
(41, 22, 1, '2026-09-01 05:30:05'),
(42, 22, 1, '2026-09-01 05:30:05'),
(43, 22, 1, '2026-09-01 05:30:05'),
(44, 22, 1, '2026-09-01 05:30:05'),
(45, 22, 1, '2026-09-01 05:30:05'),
(46, 22, 1, '2026-09-01 05:30:05'),
(47, 22, 1, '2026-09-01 05:30:05'),
(48, 22, 1, '2026-09-01 05:30:05'),
(49, 2, 1, '2026-10-10 05:14:22'),
(49, 21, 0, '2026-10-10 05:14:22'),
(50, 2, 1, '2026-10-10 05:15:02'),
(50, 21, 0, '2026-10-10 05:15:02'),
(51, 2, 1, '2026-10-10 05:13:36'),
(51, 21, 0, '2026-10-10 05:13:36'),
(52, 2, 1, '2026-10-10 05:14:13'),
(52, 21, 0, '2026-10-10 05:14:13'),
(53, 2, 1, '2026-10-10 05:14:40'),
(53, 21, 0, '2026-10-10 05:14:40'),
(54, 2, 1, '2026-10-10 05:14:31'),
(54, 21, 0, '2026-10-10 05:14:31'),
(55, 2, 1, '2026-09-01 05:30:05'),
(56, 21, 1, '2026-09-01 05:30:05'),
(57, 2, 1, '2026-10-10 05:14:01'),
(57, 21, 0, '2026-10-10 05:14:01'),
(58, 2, 1, '2026-10-10 05:12:47'),
(58, 21, 0, '2026-10-10 05:12:47'),
(59, 2, 1, '2026-10-10 05:13:24'),
(59, 21, 0, '2026-10-10 05:13:24'),
(60, 4, 1, '2026-09-01 05:30:05'),
(61, 6, 1, '2026-09-01 05:30:05'),
(62, 4, 1, '2026-09-01 05:30:05'),
(63, 2, 0, '2026-10-10 05:16:52'),
(63, 21, 1, '2026-10-10 05:16:52'),
(64, 2, 0, '2026-09-21 07:50:29'),
(64, 21, 1, '2026-09-21 07:50:29'),
(65, 21, 1, '2026-09-01 05:30:05'),
(66, 2, 0, '2026-10-10 05:15:37'),
(66, 21, 1, '2026-10-10 05:15:37'),
(67, 2, 1, '2026-09-01 05:30:05'),
(68, 2, 0, '2026-10-10 05:19:56'),
(68, 21, 1, '2026-10-10 05:19:56'),
(69, 2, 0, '2026-10-10 05:20:44'),
(69, 21, 1, '2026-10-10 05:20:44'),
(70, 2, 0, '2026-09-02 06:19:52'),
(70, 21, 1, '2026-09-02 06:19:52'),
(71, 2, 1, '2026-09-20 10:19:44'),
(71, 21, 0, '2026-09-20 10:19:44'),
(72, 21, 1, '2026-09-20 10:47:09');

-- --------------------------------------------------------

--
-- Table structure for table `footer_pages`
--

CREATE TABLE `footer_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `content` mediumtext DEFAULT NULL,
  `style_settings` text DEFAULT NULL,
  `legacy_path` varchar(100) DEFAULT NULL,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `footer_pages`
--

INSERT INTO `footer_pages` (`id`, `title`, `slug`, `content`, `style_settings`, `legacy_path`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about-us', '<div class=\"about-section\">\r\n                    <p class=\"intro-text\">\r\n                        Welcome to <b>everythingb2c.in</b> - your one-stop destination for all your business and personal needs. We are committed to providing high-quality products at competitive prices with exceptional customer service.\r\n                    </p>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">Our Story</h2>\r\n                    <p>\r\n                        <strong>EverythingB2C is a local eCommerce marketplace created to connect local businesses with local customers.</strong> We are starting our journey in <strong>Vadodara</strong> with a simple objective: to help local businesses sell online while making it easier for customers to discover and purchase products from businesses around them.\r\n                    </p>\r\n                    <p>\r\n                        At the same time, <b>EverythingB2C</b> helps customers get products at <strong>competitive prices</strong> by allowing them to compare prices from multiple local sellers. Since our operations are managed locally, sellers can benefit from lower platform fees, reduced packaging costs, and optimized logistics. This creates a <strong>win-win situation for both sellers and buyers.</strong>\r\n                    </p>\r\n                    <p>\r\n                        <strong>EverythingB2C provides the marketplace, technology, and pickup &amp; delivery support</strong>, allowing local sellers to focus on their products and customers while we take care of order fulfilment and delivery.\r\n                    </p>\r\n                    <p><strong>Local Sellers. Local Buyers. Better Savings. Faster Delivery.</strong></p>\r\n                    <p><strong>An eCommerce startup founded by technocrats and IIM Calcutta alumni.</strong></p>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">Our Mission</h2>\r\n                    <p>\r\n                        To provide businesses and consumers with access to a comprehensive range of high-quality products at competitive prices, delivered with exceptional service and reliability.\r\n                    </p>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">Our Vision</h2>\r\n                    <p>\r\n                        To become India\'s leading B2C platform, known for quality, reliability, and customer satisfaction, while continuously expanding our product range and improving our services.\r\n                    </p>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">What We Offer</h2>\r\n                    <div class=\"features-grid\">\r\n                        <div class=\"feature-item\">\r\n                            <h3>Wide Product Range</h3>\r\n                            <p>From office stationery to personal care products, cleaning supplies to home essentials - we have everything you need under one roof.</p>\r\n                        </div>\r\n                        <div class=\"feature-item\">\r\n                            <h3>Quality Assurance</h3>\r\n                            <p>Every product in our catalog is carefully selected and tested to ensure it meets our high quality standards.</p>\r\n                        </div>\r\n                        <div class=\"feature-item\">\r\n                            <h3>Competitive Pricing</h3>\r\n                            <p>We offer the best prices in the market without compromising on quality, helping you save on your purchases.</p>\r\n                        </div>\r\n                        <div class=\"feature-item\">\r\n                            <h3>Fast Delivery</h3>\r\n                            <p>Quick and reliable delivery across India with real-time tracking and secure packaging.</p>\r\n                        </div>\r\n                        <div class=\"feature-item\">\r\n                            <h3>Customer Support</h3>\r\n                            <p>Our dedicated customer support team is always ready to help you with any queries or concerns.</p>\r\n                        </div>\r\n                        <div class=\"feature-item\">\r\n                            <h3>Secure Shopping</h3>\r\n                            <p>Safe and secure payment options with SSL encryption to protect your personal and financial information.</p>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">Our Product Categories</h2>\r\n                    <ul class=\"category-list\">\r\n                        <li><strong>Office Stationery:</strong> A complete range of office supplies for your everyday business needs.</li>\r\n                        <li><strong>School Stationery:</strong> Essential stationery and supplies for students, teachers, and classrooms.</li>\r\n                        <li><strong>Industrial Safety Products:</strong> Protective equipment and safety essentials for workplaces and industry.</li>\r\n                        <li><strong>Packaging Material:</strong> Reliable packaging supplies for storing, protecting, and shipping products.</li>\r\n                        <li><strong>Personal Care:</strong> Everyday personal care products for hygiene and wellness.</li>\r\n                        <li><strong>Home &amp; Garden:</strong> Useful products to care for and improve your home and outdoor spaces.</li>\r\n                    </ul>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">Why Choose everythingb2c?</h2>\r\n                    <div class=\"why-choose-us\">\r\n                        <div class=\"reason-item\">\r\n                            <h3>For Buyers</h3>\r\n                            <ul>\r\n                                <li>Shop from local sellers</li>\r\n                                <li>Competitive prices</li>\r\n                                <li>Same-day delivery*</li>\r\n                                <li>Easy returns</li>\r\n                            </ul>\r\n                        </div>\r\n                        <div class=\"reason-item\">\r\n                            <h3>For Sellers</h3>\r\n                            <ul>\r\n                                <li>Reach local customers</li>\r\n                                <li>EverythingB2C pickup</li>\r\n                                <li>Easy online selling</li>\r\n                                <li>Introductory 0% platform fee*</li>\r\n                            </ul>\r\n                        </div>\r\n                    </div>\r\n                </div>\r\n\r\n                <div class=\"about-section\">\r\n                    <h2 style=\"font-size:20px\">Contact Information</h2>\r\n                    <div class=\"contact-details\">\r\n                        <p><strong>Email:</strong> <a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></p>\r\n                        <p><strong>Phone:</strong> <a href=\"tel:+916355837347\">+91 63558 37347</a></p>\r\n                        <p><strong>Business Hours:</strong> Monday to Saturday, 9:00 AM - 9:00 PM</p>\r\n                    </div>\r\n                </div>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15}', 'about.php', 10, 1, '2026-10-08 07:35:30', '2026-10-08 12:47:53'),
(2, 'Returns & Refunds', 'returns-refunds', '<div class=\"policy-section\">\r\n                    <p class=\"MsoNormal\"><b>Last Updated: 2 October 2026</b></p><p class=\"intro-text\">\r\n\r\n</p><p class=\"MsoNormal\"><b>EverythingB2C</b> allows customers to request returns, subject\r\nto the conditions set out below and any product-specific return conditions.</p>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Return Eligibility</h2>\r\n                    <p class=\"MsoNormal\">A return may be accepted where the product:<br></p><ul><li><span>Is defective or damaged;</span></li><li><span>Is materially different from the product ordered;</span></li><li><span>Is incorrect or incomplete; or</span></li><li><span>Qualifies for return under the applicable product-specific return conditions.</span></li></ul><p></p><p></p><p class=\"MsoNormal\">The return eligibility period may vary depending on the\r\nproduct category and seller.</p><p>\r\n\r\n\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">The applicable return period, where specified, will be\r\ndisplayed on the product page or communicated with the order.</p>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Condition of Returned Products<br></h2><div>Unless the return is due to a defect, damage, incorrect product or another issue attributable to the seller or fulfilment process, returned products should generally be:<br><br></div><div><ul><li>Unused;</li><li>In their original condition;</li><li>In the original packaging where applicable;</li><li>Accompanied by accessories, manuals and other items supplied with the product; and</li><li>Accompanied by the invoice/order information where required.</li></ul><div>Products that have been used, damaged by the customer, altered or otherwise rendered unsuitable for resale may not qualify for return, subject to applicable law.<br></div></div><br>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Products That May Have Special Return Conditions</h2>\r\n                    <p class=\"MsoNormal\">Certain products may not be returnable due to their nature.</p><p class=\"MsoNormal\">These may include:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Consumable\r\n     products;</li>\r\n <li class=\"MsoNormal\">Personal-care\r\n     products;</li>\r\n <li class=\"MsoNormal\">Hygiene-sensitive\r\n     products;</li>\r\n <li class=\"MsoNormal\" style=\"font-size:14px\">Customized\r\n     products;</li>\r\n <li class=\"MsoNormal\">Products\r\n     specifically ordered or prepared for the customer;</li>\r\n <li class=\"MsoNormal\">Products\r\n     with opened or tampered packaging; and</li>\r\n <li class=\"MsoNormal\">Other\r\n     products identified as non-returnable or subject to special conditions.</li>\r\n</ul><p>\r\n\r\n\r\n\r\n\r\n\r\n<span>Any such restrictions will be communicated where\r\napplicable.</span><br></p>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Damaged or Incorrect Products</h2>\r\n                    <p class=\"MsoNormal\">Customers should report damaged, defective or incorrect\r\nproducts as soon as reasonably possible after delivery.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">Customers may be asked to provide photographs, videos,\r\npackaging information or other evidence to help investigate the issue.</p><ul class=\"policy-list\">\r\n                    </ul>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Upon successful cancellation:</h2>\r\n                    <ul class=\"policy-list\">\r\n                        <li>A full refund will be processed to the original method of payment</li>\r\n                        <li>Refunds will be initiated within 5–7 business days from the date of confirmation</li>\r\n                    </ul>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Return Request</h2>\r\n                    <p class=\"MsoNormal\">To request a return, contact:</p><p class=\"MsoNormal\"><span class=\"MsoHyperlink\"><b><span><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></span></b></span></p><p class=\"MsoNormal\">Please provide:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Order\r\n     number;</li>\r\n <li class=\"MsoNormal\">Product\r\n     name;</li>\r\n <li class=\"MsoNormal\">Reason\r\n     for return; and</li>\r\n</ul><p>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<span>Photographs or other supporting information\r\nwhere applicable.</span><br></p>\r\n                </div>\r\n\r\n                <div class=\"policy-section\">\r\n                    <h2 style=\"font-size:20px\">Return Approval</h2>\r\n                    <p class=\"MsoNormal\">A return request may require verification before approval.<br><span>Once approved, </span><b>EverythingB2C</b><span> or its delivery partner may\r\narrange pickup of the product, where applicable.</span></p><p class=\"MsoNormal\"></p>\r\n                </div><div class=\"policy-section\"><h2 style=\"font-size:20px\">Refund</h2><p class=\"MsoNormal\">Where a refund is approved, the refundable amount will be\r\nprocessed after applicable verification and receipt/inspection of the returned\r\nproduct where required.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">For POD orders, the customer may be required to provide\r\nappropriate bank account or other refund details.</p></div><div class=\"policy-section\"><h2 style=\"font-size:20px\">Refund Amount</h2><p class=\"MsoNormal\">The refund amount will depend on the circumstances of the\r\nreturn and the applicable policy.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">Where a return is due to a defective, damaged, incorrect or\r\notherwise improperly supplied product, applicable amounts will be handled in\r\naccordance with the customer\'s legal rights and the applicable circumstances.</p></div><div class=\"policy-section\"><h2 style=\"font-size:20px\">Replacement</h2><p class=\"MsoNormal\">Where appropriate and subject to availability, <b>EverythingB2C</b>\r\nor the seller may offer replacement instead of a refund.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">The customer will be informed of the available resolution.</p></div><div class=\"policy-section\"><h2 style=\"font-size:20px\">Seller Responsibility</h2><p class=\"MsoNormal\">The seller remains responsible for the quality,\r\nspecifications, authenticity and applicable warranty or guarantee of the\r\nproducts supplied by the seller.</p><p>\r\n\r\n</p><p class=\"MsoNormal\"><b>EverythingB2C</b> will reasonably assist customers in\r\ncoordinating return or complaint resolution.</p></div><div class=\"policy-section\"><h2 style=\"font-size:20px\">Contact</h2><p class=\"MsoNormal\">For return or refund assistance:</p><p>\r\n\r\n<b><span>INPROTECH — EverythingB2C</span></b><span><br>\r\nEmail: <span class=\"MsoHyperlink\"><b><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></b></span></span><br></p></div>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"mobile_description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15,\"mobile_description_size\":12}', 'returns.php', 20, 1, '2026-10-08 07:35:30', '2026-10-14 05:12:38'),
(4, 'FAQ', 'faq', '<div class=\"faq-section\"><h2>General Questions</h2><div class=\"faq-item\"><h3 class=\"faq-question\">What is everythingb2c.in?</h3><div class=\"faq-answer\">\r\n                            <p>everythingb2c.in is a comprehensive B2C platform offering a wide range of products including office stationery, personal care items, cleaning supplies, home essentials, and more. We provide quality products at competitive prices with reliable delivery across India.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">How do I place an order?</h3><div class=\"faq-answer\">\r\n                            <p>Placing an order is simple:</p>\r\n                            <ol>\r\n                                <li>Browse our product categories and select the items you need</li>\r\n                                <li>Add products to your cart</li>\r\n                                <li>Review your cart and proceed to checkout</li>\r\n                                <li>Enter your shipping details and payment information</li>\r\n                                <li>Confirm your order</li>\r\n                            </ol>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">What payment methods do you accept?</h3><div class=\"faq-answer\">\r\n                            <p>We accept various payment methods including:</p>\r\n                            <ul>\r\n                                <li>Credit/Debit Cards (Visa, MasterCard, RuPay)</li>\r\n                                <li>Net Banking</li>\r\n                                <li>UPI Payments</li>\r\n                                <li>Digital Wallets</li>\r\n                                <li>Cash on Delivery (COD) - subject to availability</li>\r\n                            </ul>\r\n                        </div></div></div><div class=\"faq-section\"><h2>Shipping &amp; Delivery</h2><div class=\"faq-item\"><h3 class=\"faq-question\">How long does delivery take?</h3><div class=\"faq-answer\">\r\n                            <p>Delivery times vary by location:</p>\r\n                            <ul>\r\n                                <li><strong>Metro cities:</strong> 2-3 business days</li>\r\n                                <li><strong>Tier 2 cities:</strong> 3-5 business days</li>\r\n                                <li><strong>Other locations:</strong> 5-7 business days</li>\r\n                            </ul>\r\n                            <p>Delivery times may be extended during festivals or peak seasons.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">Do you deliver to all locations in India?</h3><div class=\"faq-answer\">\r\n                            <p>Yes, we deliver to most locations across India. However, delivery to remote areas may take longer or may not be available. You can check delivery availability by entering your pincode during checkout.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">Can I track my order?</h3><div class=\"faq-answer\">\r\n                            <p>Yes, once your order is dispatched, you will receive a tracking number via SMS and email. You can use this tracking number to monitor your order\'s progress on our website or the courier company\'s website.</p>\r\n                        </div></div></div><div class=\"faq-section\"><h2>Returns &amp; Refunds</h2><div class=\"faq-item\"><h3 class=\"faq-question\">What is your return policy?</h3><div class=\"faq-answer\">\r\n                            <p>We offer a 7-day return policy from the date of invoice. Products can be returned if:</p>\r\n                            <ul>\r\n                                <li>The product has not been used or altered</li>\r\n                                <li>The product is in original condition with tags</li>\r\n                                <li>You have the original invoice</li>\r\n                            </ul>\r\n                            <p>Please note that we only provide refunds, not exchanges.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">How long does it take to process a refund?</h3><div class=\"faq-answer\">\r\n                            <p>Refunds are typically processed within 5-7 business days after we receive and verify the returned product. The refund will be credited to the same payment method used for the original purchase.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">Can I cancel my order?</h3><div class=\"faq-answer\">\r\n                            <p>You can cancel your order before it is dispatched from our warehouse. Once the order has been shipped, you cannot cancel it, but you can return the product after receiving it according to our return policy.</p>\r\n                        </div></div></div><div class=\"faq-section\"><h2>Account &amp; Security</h2><div class=\"faq-item\"><h3 class=\"faq-question\">Do I need to create an account to shop?</h3><div class=\"faq-answer\">\r\n                            <p>While you can browse our products without an account, creating an account allows you to:</p>\r\n                            <ul>\r\n                                <li>Save your shipping addresses</li>\r\n                                <li>Track your order history</li>\r\n                                <li>Save items to your wishlist</li>\r\n                                <li>Faster checkout process</li>\r\n                            </ul>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">Is my personal information secure?</h3><div class=\"faq-answer\">\r\n                            <p>Yes, we take your privacy and security seriously. We use SSL encryption to protect your personal and payment information. We never share your personal details with third parties without your consent.</p>\r\n                        </div></div></div><div class=\"faq-section\"><h2>Product Information</h2><div class=\"faq-item\"><h3 class=\"faq-question\">Are the product images accurate?</h3><div class=\"faq-answer\">\r\n                            <p>We strive to provide accurate product images and descriptions. However, actual colors may vary slightly due to monitor settings. If you have any concerns about a product, please contact our customer support before placing your order.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">What if a product is out of stock?</h3><div class=\"faq-answer\">\r\n                            <p>If a product is out of stock, it will be marked as \"Out of Stock\" on the product page. You can sign up for notifications to be alerted when the product becomes available again.</p>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">Do you offer bulk discounts?</h3><div class=\"faq-answer\">\r\n                            <p>Yes, we offer special pricing for bulk orders. Please contact our sales team at info@everythingb2c.in for bulk pricing and custom quotes.</p>\r\n                        </div></div></div><div class=\"faq-section\"><h2>Customer Support</h2><div class=\"faq-item\"><h3 class=\"faq-question\">How can I contact customer support?</h3><div class=\"faq-answer\">\r\n                            <p>You can reach our customer support team through:</p>\r\n                            <ul>\r\n                                <li><strong>Email:</strong> info@everythingb2c.in</li>\r\n                                <li><strong>Phone:</strong> +91 63558 37347</li>\r\n                                <li><strong>Business Hours:</strong> Monday to Saturday, 9:00 AM - 9:00 PM</li>\r\n                            </ul>\r\n                        </div></div><div class=\"faq-item\"><h3 class=\"faq-question\">What if I have a complaint about product quality?</h3><div class=\"faq-answer\">\r\n                            <p>For quality-related complaints, please contact the manufacturer\'s customer care number provided on the product packaging. For issues with our service or delivery, please contact our customer support team.</p>\r\n                        </div></div></div>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"mobile_description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15,\"mobile_description_size\":12}', 'faq.php', 40, 1, '2026-10-08 07:35:30', '2026-10-19 05:53:24'),
(73, 'Terms & Conditions', 'terms-conditions', '<p class=\"MsoNormal\"><b>Last Updated: 2 October 2026</b></p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">Welcome to EverythingB2C.</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> is an online marketplace operated by <b>INPROTECH</b>,\r\nhaving its business address at <b>Diwalipura, Vadodara, Gujarat, India</b>. The\r\nplatform connects customers with local sellers and facilitates the purchase,\r\nfulfillment and delivery of products within Vadodara.</p>\r\n\r\n<p class=\"MsoNormal\">By accessing or using the <b>EverythingB2C</b> website, placing an\r\norder, creating an account, or otherwise using our services, you agree to be\r\nbound by these Terms &amp; Conditions.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">1. About EverythingB2C</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> operates as an online marketplace through\r\nwhich independent sellers may offer products to customers.</p>\r\n\r\n<p class=\"MsoNormal\">Depending on the product and transaction, the seller is\r\nresponsible for the product supplied, including its quality, specifications,\r\nwarranty or guarantee where applicable, while <b>EverythingB2C</b> facilitates the\r\nmarketplace transaction and may provide pickup, packing, fulfillment and\r\ndelivery services.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">2. Eligibility</h2>\r\n\r\n<p class=\"MsoNormal\">You must provide accurate information when creating an\r\naccount or placing an order.</p>\r\n\r\n<p class=\"MsoNormal\">By using <b>EverythingB2C</b>, you confirm that the information\r\nprovided by you is accurate and that you are legally capable of entering into a\r\ntransaction under applicable law.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">3. Product Information</h2>\r\n\r\n<p class=\"MsoNormal\">We make reasonable efforts to ensure that product\r\ndescriptions, photographs, specifications, prices and availability displayed on\r\nthe website are accurate.</p>\r\n\r\n<p class=\"MsoNormal\">However, certain information may be provided by individual\r\nsellers and may occasionally contain errors, omissions or outdated information.</p>\r\n\r\n<p class=\"MsoNormal\">Customers should review the product information before\r\nplacing an order.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">4. Pricing</h2>\r\n\r\n<p class=\"MsoNormal\">All prices displayed on<b> EverythingB2C</b> are in Indian Rupees\r\n(INR).</p>\r\n\r\n<p class=\"MsoNormal\">Unless otherwise stated, displayed product prices are\r\ninclusive of applicable GST.</p>\r\n\r\n<p class=\"MsoNormal\">Prices may vary between sellers and may change from time to\r\ntime.</p>\r\n\r\n<p class=\"MsoNormal\">The price applicable to an order will generally be the price\r\ndisplayed at the time the order is successfully placed, subject to pricing\r\nerrors and other provisions contained in our Pricing &amp; Availability Policy.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">5. Product Availability</h2>\r\n\r\n<p class=\"MsoNormal\">Products displayed on the website are subject to\r\navailability.</p>\r\n\r\n<p class=\"MsoNormal\">Availability may change due to sales, stock discrepancies,\r\nseller updates or other operational circumstances.</p>\r\n\r\n<p class=\"MsoNormal\">If an ordered product becomes unavailable, <b>EverythingB2C</b> may\r\ncontact the customer to discuss an alternative, revised delivery arrangement,\r\nor cancellation and applicable refund.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">6. Placing an Order</h2>\r\n\r\n<p class=\"MsoNormal\">When you place an order, you are making a request to\r\npurchase the selected products at the displayed price and under the applicable\r\nterms.</p>\r\n\r\n<p class=\"MsoNormal\">An order is subject to acceptance and availability.</p>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may decline or cancel an order in\r\ncircumstances including, but not limited to:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Product\r\n     unavailability</li>\r\n <li class=\"MsoNormal\">Incorrect\r\n     pricing</li>\r\n <li class=\"MsoNormal\">Incorrect\r\n     product information</li>\r\n <li class=\"MsoNormal\">Suspected\r\n     fraudulent activity</li>\r\n <li class=\"MsoNormal\">Delivery\r\n     limitations</li>\r\n <li class=\"MsoNormal\">Unusual\r\n     or bulk quantities requiring separate confirmation</li>\r\n <li class=\"MsoNormal\">Technical\r\n     or system errors</li>\r\n <li class=\"MsoNormal\">Other\r\n     legitimate operational reasons</li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\">Where payment has already been received for an order that is\r\ncancelled by <b>EverythingB2C</b>, the applicable amount will be refunded. However,\r\nPay on Delivery is currently the only payment method available for customers in\r\nVadodara.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">7. Pay on Delivery</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C </b>currently offers Pay on Delivery (POD),\r\nsubject to availability and applicable order or delivery conditions.</p>\r\n\r\n<p class=\"MsoNormal\">The customer must pay the applicable order amount to the\r\ndelivery representative at the time of delivery.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">8. Delivery</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> currently provides delivery within <b>Vadodara,\r\nGujarat</b>.</p>\r\n\r\n<p class=\"MsoNormal\">Delivery times displayed on the website are estimates and\r\nmay vary depending on product availability, seller location, order processing,\r\ntraffic, weather, operational conditions and other circumstances.</p>\r\n\r\n<p class=\"MsoNormal\">Same-day delivery, where offered, is subject to applicable\r\ncut-off times, product availability and delivery-area restrictions.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">9. Cancellation</h2>\r\n\r\n<p class=\"MsoNormal\">Customers may request cancellation of an order <b>before the\r\norder is dispatched</b>.</p>\r\n\r\n<p class=\"MsoNormal\">Once an order has been dispatched, cancellation may no\r\nlonger be available.</p>\r\n\r\n<p class=\"MsoNormal\">Please refer to our Cancellation Policy for further details.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">10. Returns and Refunds</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> allows returns subject to the applicable\r\nproduct-specific and Return &amp; Refund Policy.</p>\r\n\r\n<p class=\"MsoNormal\">Certain products may have specific return conditions because\r\nof their nature, usage, hygiene considerations, consumability, customization or\r\nother legitimate reasons.</p>\r\n\r\n<p class=\"MsoNormal\">Customers should review the applicable return conditions\r\nbefore placing an order.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">11. Seller Responsibility</h2>\r\n\r\n<p class=\"MsoNormal\">Individual sellers are responsible for ensuring that the\r\nproducts supplied by them:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Match\r\n     the description and specifications provided;</li>\r\n <li class=\"MsoNormal\">Are\r\n     genuine and legally saleable;</li>\r\n <li class=\"MsoNormal\">Meet\r\n     applicable quality and safety requirements;</li>\r\n <li class=\"MsoNormal\">Are\r\n     properly packaged where applicable;</li>\r\n <li class=\"MsoNormal\">Comply\r\n     with applicable laws and regulations; and</li>\r\n <li class=\"MsoNormal\">Include\r\n     applicable warranty or guarantee information where offered.</li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\">EverythingB2C may facilitate communication and resolution\r\nbetween the customer and seller where appropriate.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">12. Intellectual Property</h2>\r\n\r\n<p class=\"MsoNormal\">The <b>EverythingB2C</b> name, logo, website design, text,\r\ngraphics, photographs, software and other original content are owned by or\r\nlicensed, unless otherwise stated.</p>\r\n\r\n<p class=\"MsoNormal\">You may not reproduce, copy, modify, distribute or\r\ncommercially exploit such content without prior written permission.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">13. Prohibited Use</h2>\r\n\r\n<p class=\"MsoNormal\">You must not use the website to:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Commit\r\n     or facilitate unlawful activities;</li>\r\n <li class=\"MsoNormal\">Provide\r\n     false or misleading information;</li>\r\n <li class=\"MsoNormal\">Attempt\r\n     unauthorized access to the website or its systems;</li>\r\n <li class=\"MsoNormal\">Interfere\r\n     with website operations;</li>\r\n <li class=\"MsoNormal\">Use\r\n     automated systems to extract data without permission;</li>\r\n <li class=\"MsoNormal\">Conduct\r\n     fraudulent transactions; or</li>\r\n <li class=\"MsoNormal\">Misuse\r\n     promotional offers or marketplace services.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">14. Website Availability</h2>\r\n\r\n<p class=\"MsoNormal\">We aim to keep the website available and operational;\r\nhowever, uninterrupted access cannot be guaranteed.</p>\r\n\r\n<p class=\"MsoNormal\">The website may occasionally be unavailable due to\r\nmaintenance, technical problems, upgrades, security issues or circumstances\r\nbeyond our reasonable control.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">15. Limitation of Responsibility</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> acts as a marketplace and fulfilment\r\nfacilitator.</p>\r\n\r\n<p class=\"MsoNormal\">Nothing in these Terms is intended to exclude or limit any\r\nconsumer right or liability that cannot legally be excluded or limited under\r\napplicable law.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">16. Changes to These Terms</h2>\r\n\r\n<p class=\"MsoNormal\">We may update these Terms &amp; Conditions from time to\r\ntime.</p>\r\n\r\n<p class=\"MsoNormal\">The updated version will be published on the website with\r\nthe revised \"Last Updated\" date.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">17. Governing Law</h2>\r\n\r\n<p class=\"MsoNormal\">These Terms shall be governed by the laws applicable in\r\nIndia.</p>\r\n\r\n<p class=\"MsoNormal\">Subject to applicable consumer protection laws, disputes\r\nshall be subject to the jurisdiction of the appropriate courts and authorities\r\nhaving jurisdiction.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">18. Contact</h2>\r\n\r\n<p class=\"MsoNormal\"><b>INPROTECH — EverythingB2C</b><br>\r\nDiwalipura, Vadodara, Gujarat, India<br>\r\nEmail: <a><b>info@everythingb2c.in</b></a></p>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15}', NULL, 50, 1, '2026-10-08 07:38:20', '2026-10-08 12:51:19'),
(94, 'Shipping & Delivery Policy', 'shipping-delivery-policy', '<p class=\"MsoNormal\"><b>Last Updated: 2 October 2026</b></p>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> currently provides local delivery services\r\nwithin <b>Vadodara, Gujarat</b>.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">1. Delivery Area</h2>\r\n\r\n<p class=\"MsoNormal\">At present, <b>EverythingB2C</b> delivers orders within Vadodara.</p>\r\n\r\n<p class=\"MsoNormal\">Delivery availability may depend on the customer\'s PIN code,\r\nseller location, product availability and operational delivery capacity.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">2. Order Processing</h2>\r\n\r\n<p class=\"MsoNormal\">After an order is placed, <b>EverythingB2C</b> coordinates with the\r\nrelevant seller for product availability and order fulfilment.</p>\r\n\r\n<p class=\"MsoNormal\">Where applicable, <b>EverythingB2C</b> may:<br></p><p class=\"MsoNormal\"><ul><li>Receive the order;</li><li>Pick up the product from the seller;</li><li>Inspect and/or process the shipment as applicable;</li><li>Pack the order;</li><li>Arrange delivery to the customer.</li></ul></p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">3. Delivery Time</h2>\r\n\r\n<p class=\"MsoNormal\">Estimated delivery time will be communicated or displayed\r\nwherever reasonably practicable.</p>\r\n\r\n<p class=\"MsoNormal\">Delivery estimates are not guaranteed and may be affected\r\nby:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Product\r\n     availability;</li>\r\n <li class=\"MsoNormal\">Seller\r\n     processing time;</li>\r\n <li class=\"MsoNormal\">Customer\r\n     location;</li>\r\n <li class=\"MsoNormal\">Traffic\r\n     and road conditions;</li>\r\n <li class=\"MsoNormal\">Weather;</li>\r\n <li class=\"MsoNormal\">Holidays;</li>\r\n <li class=\"MsoNormal\">Operational\r\n     issues; and</li>\r\n <li class=\"MsoNormal\">Other\r\n     circumstances beyond our reasonable control.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">4. Same-Day Delivery</h2>\r\n\r\n<p class=\"MsoNormal\">Where same-day delivery is offered, it is subject to:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Product\r\n     availability;</li>\r\n <li class=\"MsoNormal\">Seller\r\n     availability;</li>\r\n <li class=\"MsoNormal\">Order\r\n     placement time;</li>\r\n <li class=\"MsoNormal\">Delivery\r\n     location;</li>\r\n <li class=\"MsoNormal\">Operational\r\n     capacity; and</li>\r\n <li class=\"MsoNormal\">Applicable\r\n     cut-off times.</li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\">A same-day delivery indication does not guarantee delivery\r\nat a specific hour unless expressly confirmed.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">5. Delivery Charges</h2>\r\n\r\n<p class=\"MsoNormal\">Any applicable delivery charge will be displayed to the\r\ncustomer before order completion, wherever applicable.</p>\r\n\r\n<p class=\"MsoNormal\">Promotional or free-delivery offers may be subject to\r\nspecific terms.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">6. Delivery Attempt</h2>\r\n\r\n<p class=\"MsoNormal\">Customers are expected to provide a complete and accurate\r\ndelivery address and remain reasonably available to receive the order.</p>\r\n\r\n<p class=\"MsoNormal\">If delivery cannot be completed because of an incorrect\r\naddress, customer unavailability, refusal to accept the order, or other\r\ncircumstances attributable to the customer, an additional delivery attempt or\r\napplicable charge may apply.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">7. POD Orders</h2>\r\n\r\n<p class=\"MsoNormal\">For Pay on Delivery orders, the customer must pay the\r\napplicable amount at the time of delivery.</p>\r\n\r\n<p class=\"MsoNormal\">The delivery representative may not be able to hand over the\r\norder until the required payment is received.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">8. Damaged Package</h2>\r\n\r\n<p class=\"MsoNormal\">Customers should inspect the external condition of the\r\npackage at the time of delivery where reasonably possible.</p>\r\n\r\n<p class=\"MsoNormal\">If the package appears damaged, customers should report the\r\nissue to <b>EverythingB2C</b> as soon as possible and provide photographs or other\r\nsupporting information where requested.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">9. Delayed Delivery</h2>\r\n\r\n<p class=\"MsoNormal\">If an order is delayed, customers may contact <b>EverythingB2C</b>\r\nat:</p>\r\n\r\n<p class=\"MsoNormal\"><a><b>info@everythingb2c.in</b></a></p>\r\n\r\n<p class=\"MsoNormal\">We will make reasonable efforts to assist and provide an\r\nupdated delivery status.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">10. Delivery Outside Vadodara</h2>\r\n\r\n<p class=\"MsoNormal\">Orders outside the current delivery area may not be\r\naccepted.</p>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may expand its delivery area in the future and\r\nwill update the website accordingly.</p>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15}', NULL, 60, 1, '2026-10-08 07:43:17', '2026-10-09 04:08:26'),
(131, 'Cancellation Policy', 'cancellation-policy', '<p class=\"MsoNormal\"><b>Last Updated: 2 October 2026</b></p>\r\n\r\n<h2 style=\"font-size:20px\">1. Customer Cancellation</h2><p class=\"MsoNormal\"><span>Customers may request cancellation of an order </span><b>before the\r\norder is dispatched</b><span>.</span><br></p>\r\n\r\n<h2 style=\"font-size:20px\">2. Orders Already Dispatched</h2>\r\n\r\n<p class=\"MsoNormal\">Once an order has been dispatched, it may not be possible to\r\ncancel the order.</p>\r\n\r\n<p class=\"MsoNormal\">In such cases, the customer may be able to initiate a return\r\nafter receiving the product, subject to the applicable Return &amp; Refund\r\nPolicy and product-specific return conditions.</p>\r\n\r\n<h2 style=\"font-size:20px\">3. POD Orders</h2>\r\n\r\n<p class=\"MsoNormal\">For POD orders cancelled before dispatch, there is generally\r\nno payment refund because payment has not yet been collected.</p>\r\n\r\n<p class=\"MsoNormal\">Repeated refusal or cancellation of POD orders may result in\r\nrestrictions on future POD availability where reasonably necessary to prevent\r\nmisuse.</p>\r\n\r\n<h2 style=\"font-size:20px\">4. Cancellation by EverythingB2C</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may cancel an order in circumstances\r\nincluding:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Product\r\n     unavailability;</li>\r\n <li class=\"MsoNormal\">Incorrect\r\n     price or product information;</li>\r\n <li class=\"MsoNormal\">Seller\r\n     inability to fulfil the order;</li>\r\n <li class=\"MsoNormal\">Delivery\r\n     restrictions;</li>\r\n <li class=\"MsoNormal\">Suspected\r\n     fraudulent or abusive activity;</li>\r\n <li class=\"MsoNormal\">Technical\r\n     errors; or</li>\r\n <li class=\"MsoNormal\">Other\r\n     legitimate operational circumstances.</li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\">If payment has already been received, the applicable amount\r\nwill be refunded.</p>\r\n\r\n<h2 style=\"font-size:20px\">5. Refund Following Cancellation</h2>\r\n\r\n<span>Where a refund is applicable, it will be\r\nprocessed through the applicable refund method and within the applicable\r\nprocessing period.</span>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15}', NULL, 70, 1, '2026-10-08 07:49:31', '2026-10-08 12:53:04');
INSERT INTO `footer_pages` (`id`, `title`, `slug`, `content`, `style_settings`, `legacy_path`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(144, 'Seller Terms & Conditions', 'seller-terms-conditions', '<h2 class=\"MsoNormal\"><b>Last Updated: 2 October 2026</b></h2>\r\n\r\n<p class=\"MsoNormal\">These Seller Terms &amp; Conditions govern the participation\r\nof sellers on the <b>EverythingB2C</b> marketplace.<span></span></p>\r\n\r\n<p class=\"MsoNormal\">By registering as a seller on <b>EverythingB2C</b>, the seller\r\nagrees to these terms.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">1. Marketplace Model</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> operates an online marketplace connecting\r\nlocal sellers with customers.</p>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may provide technology, marketplace services,\r\norder management, pickup, packing and delivery support.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">2. Seller Information</h2>\r\n\r\n<p class=\"MsoNormal\">The seller must provide accurate and complete information\r\nrequested during registration, including applicable:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Business\r\n     name;</li>\r\n <li class=\"MsoNormal\">Contact\r\n     information;</li>\r\n <li class=\"MsoNormal\">Business\r\n     address;</li>\r\n <li class=\"MsoNormal\">Bank/payment\r\n     information;</li>\r\n <li class=\"MsoNormal\">GST\r\n     information, where applicable;</li>\r\n <li class=\"MsoNormal\">PAN\r\n     and other identification details;</li>\r\n <li class=\"MsoNormal\">Product\r\n     information; and</li>\r\n <li class=\"MsoNormal\">Other\r\n     information reasonably required for marketplace operations or legal\r\n     compliance.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">3. Product Listing</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers must ensure that product listings are accurate and\r\ncomplete.</p>\r\n\r\n<p class=\"MsoNormal\">This includes:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Product\r\n     name;</li>\r\n <li class=\"MsoNormal\">Brand;</li>\r\n <li class=\"MsoNormal\">Specifications;</li>\r\n <li class=\"MsoNormal\">Images;</li>\r\n <li class=\"MsoNormal\">MRP,\r\n     where applicable;</li>\r\n <li class=\"MsoNormal\">Selling\r\n     price;</li>\r\n <li class=\"MsoNormal\">Applicable\r\n     taxes;</li>\r\n <li class=\"MsoNormal\">Stock\r\n     availability;</li>\r\n <li class=\"MsoNormal\">Country\r\n     of origin where required;</li>\r\n <li class=\"MsoNormal\">Warranty/guarantee\r\n     information, where applicable; and</li>\r\n <li class=\"MsoNormal\">Other\r\n     legally required product information.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">4. Pricing</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers are responsible for providing accurate pricing\r\ninformation.</p>\r\n\r\n<p class=\"MsoNormal\">Sellers must not provide misleading or deceptive prices,\r\ndiscounts or promotional claims.</p>\r\n\r\n<p class=\"MsoNormal\">Prices supplied by sellers may be displayed to customers\r\nthrough the <b>EverythingB2C</b> platform.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">5. Product Availability</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers must keep stock information reasonably accurate and\r\npromptly update <b>EverythingB2C</b> when a product becomes unavailable.</p>\r\n\r\n<p class=\"MsoNormal\">Repeated acceptance of orders for unavailable products may\r\nresult in account review or other appropriate action.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">6. Product Quality</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers are responsible for ensuring that products supplied\r\nto customers:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Match\r\n     their listings;</li>\r\n <li class=\"MsoNormal\">Are\r\n     genuine where applicable;</li>\r\n <li class=\"MsoNormal\">Are\r\n     not counterfeit;</li>\r\n <li class=\"MsoNormal\">Are\r\n     safe and legally saleable;</li>\r\n <li class=\"MsoNormal\">Meet\r\n     applicable standards;</li>\r\n <li class=\"MsoNormal\">Are\r\n     properly packed where required; and</li>\r\n <li class=\"MsoNormal\">Comply\r\n     with applicable laws.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">7. Order Fulfilment</h2>\r\n\r\n<p class=\"MsoNormal\">After receiving an order, the seller must make the product\r\navailable for pickup or fulfilment within the applicable processing period\r\ncommunicated by <b>EverythingB2C</b>.</p>\r\n\r\n<p class=\"MsoNormal\">The seller must not intentionally delay, reject or\r\nmanipulate orders without a legitimate reason.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">8. Pickup and Delivery</h2>\r\n\r\n<p class=\"MsoNormal\">Where <b>EverythingB2C</b> provides pickup and delivery services,\r\nthe seller must:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Keep\r\n     the product ready for pickup;</li>\r\n <li class=\"MsoNormal\">Provide\r\n     accurate pickup information;</li>\r\n <li class=\"MsoNormal\">Package\r\n     the product appropriately where required; and</li>\r\n <li class=\"MsoNormal\">Cooperate\r\n     with<b> EverythingB2C\'s</b> fulfilment process.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">9. Returns</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers must cooperate with legitimate return requests and\r\nprovide information reasonably required to investigate customer complaints.</p>\r\n\r\n<p class=\"MsoNormal\">Where a product is defective, incorrect, damaged due to\r\nseller packaging, materially different from its description, or otherwise\r\nimproperly supplied, the seller may be responsible for the applicable\r\nresolution.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">10. Customer Complaints</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers must cooperate with <b>EverythingB2C</b> in resolving\r\ncustomer complaints.</p>\r\n\r\n<p class=\"MsoNormal\">Where required, seller contact information and\r\ngrievance-related information may be displayed to customers in accordance with\r\napplicable law.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">11. Prohibited Products</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers must not list products prohibited by law or products\r\nthat <b>EverythingB2C</b> has expressly prohibited from being sold through the\r\nplatform.</p>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may remove listings that violate applicable\r\nlaw or marketplace rules.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">12. Seller Conduct</h2>\r\n\r\n<p class=\"MsoNormal\">Sellers must not:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Provide\r\n     false information;</li>\r\n <li class=\"MsoNormal\">Manipulate\r\n     prices or reviews;</li>\r\n <li class=\"MsoNormal\">Misrepresent\r\n     products;</li>\r\n <li class=\"MsoNormal\">Circumvent\r\n     <b>EverythingB2C</b> to transact with customers obtained through the platform;</li>\r\n <li class=\"MsoNormal\">Upload\r\n     unauthorized or infringing content;</li>\r\n <li class=\"MsoNormal\">Engage\r\n     in fraudulent activity; or</li>\r\n <li class=\"MsoNormal\">Misuse\r\n     customer information.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">13. Customer Data</h2>\r\n\r\n<p class=\"MsoNormal\">Customer information received through <b>EverythingB2C</b> must be\r\nused only for legitimate purposes connected with fulfilling orders and\r\nproviding marketplace services.</p>\r\n\r\n<p class=\"MsoNormal\">Sellers must protect customer information and must not sell,\r\ndisclose or misuse such information.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">14. Suspension or Termination</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may suspend or terminate a seller account\r\nwhere there is reasonable evidence of:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Fraud;</li>\r\n <li class=\"MsoNormal\">Repeated\r\n     customer complaints;</li>\r\n <li class=\"MsoNormal\">Counterfeit\r\n     or prohibited products;</li>\r\n <li class=\"MsoNormal\">Misleading\r\n     listings;</li>\r\n <li class=\"MsoNormal\">Repeated\r\n     order failures;</li>\r\n <li class=\"MsoNormal\">Misuse\r\n     of customer information;</li>\r\n <li class=\"MsoNormal\">Violation\r\n     of these terms; or</li>\r\n <li class=\"MsoNormal\">Violation\r\n     of applicable law.</li>\r\n</ul>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">15. Seller Taxes</h2>\r\n\r\n<p class=\"MsoNormal\">The seller is responsible for its own tax, GST, invoicing\r\nand statutory compliance obligations applicable to its business.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">16. Indemnification</h2>\r\n\r\n<p class=\"MsoNormal\">To the extent permitted by applicable law, the seller shall\r\nbe responsible for losses, claims, penalties or expenses arising from the\r\nseller\'s breach of these terms, unlawful conduct, inaccurate product\r\ninformation, counterfeit products, or failure to comply with applicable laws.</p>\r\n\r\n<h2 class=\"MsoNormal\" style=\"font-size:20px\">17. Changes to Seller Terms</h2>\r\n\r\n<p class=\"MsoNormal\"><b>EverythingB2C</b> may update these terms from time to time.</p>\r\n\r\n<p class=\"MsoNormal\">Continued use of the marketplace after the updated terms\r\ntake effect constitutes acceptance of the revised terms, subject to applicable\r\nlaw.</p>\r\n\r\n<h2 class=\"MsoNormal\">18. Contact</h2>\r\n\r\n<div><p class=\"MsoNormal\"><span>EverythingB2C</span><br>Diwalipura, Vadodara, Gujarat, India<br>Email: <span class=\"MsoHyperlink\"><span><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></span></span></p><p></p><p class=\"MsoNormal\"><span>Grievance Officer:</span><br>Name: <span>Sachin Ringe</span><br>Designation: <span>Grievance Officer</span><br>Email: <span class=\"MsoHyperlink\"><span><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></span></span></p></div>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"mobile_description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15,\"mobile_description_size\":12}', NULL, 80, 1, '2026-10-08 07:51:32', '2026-10-19 05:52:15'),
(331, 'Privacy Policy', 'privacy-policy', '<div class=\"privacy-section\">\r\n                    <p class=\"MsoNormal\"><b>Last Updated: 2 October 2026<br></b></p><p class=\"MsoNormal\">This Privacy Policy explains how <b>INPROTECH</b>, operating\r\nthe <b>EverythingB2C</b> online marketplace (\"<b>EverythingB2C</b>\",\r\n\"we\", \"us\", or \"our\"), collects, uses, stores,\r\nshares and protects personal information when you visit or use our website,\r\ncreate an account, place an order, contact us, or otherwise use our services.</p><p class=\"MsoNormal\">\r\n\r\n</p><p class=\"MsoNormal\"><b>EverythingB2C</b> is a local eCommerce marketplace that connects\r\ncustomers with local businesses and sellers. We currently provide delivery\r\nservices within <b>Vadodara, Gujarat</b>.<br></p><p class=\"MsoNormal\">By using <b>EverythingB2C</b>, you acknowledge that you have read\r\nand understood this Privacy Policy.</p>\r\n                </div>\r\n\r\n                <div class=\"privacy-section\">\r\n                    <h2 style=\"font-size:20px\">About Us</h2><p class=\"MsoNormal\"><b>Legal Entity:</b> INPROTECH<br>\r\n<b>Brand/Marketplace:</b> EverythingB2C<br>\r\n<b>Business Address:</b> Diwalipura, Vadodara, Gujarat, India<br>\r\n<b>Customer Support Email:</b> <span class=\"MsoHyperlink\"><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></span></p><p class=\"MsoNormal\">For privacy-related questions or requests, you may contact\r\nus at:</p><p>\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\"><span class=\"MsoHyperlink\"><b><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></b></span></p><ul class=\"privacy-list\">\r\n                    </ul>\r\n                </div>\r\n\r\n                <div class=\"privacy-section\">\r\n                    <h2 style=\"font-size:20px\">Information We Collect</h2>\r\n                    <p class=\"MsoNormal\">We may collect different categories of information depending\r\non how you use <b>EverythingB2C</b>.<br></p><p class=\"MsoNormal\"><b>2.1 Information You Provide Directly<br></b><span>When you create an account, place an order, contact us, or\r\nuse our services, we may collect:<br></span></p><p class=\"MsoNormal\"></p><ul><li>Full name</li><li>Mobile number</li><li>Email address</li><li>Delivery address</li><li>Billing address, where applicable</li><li>PIN code</li><li>Account login information</li><li>Order details</li><li>Product information associated with your orders</li><li>Customer support communications</li><li>Information you provide when submitting a return, refund or cancellation request</li><li>Information required to process refunds, where applicable</li></ul><div></div><p></p><p class=\"MsoNormal\"><b>2.2 Order and Transaction Information<br></b><span>When you place an order, we may collect and process\r\ninformation relating to:<br></span></p><p class=\"MsoNormal\"></p><ul><li>Order number</li><li>Products purchased</li><li>Seller from whom the product is purchased</li><li>Quantity</li><li>Product price</li><li>Applicable taxes</li><li>Delivery charges, where applicable</li><li>Order status</li><li>Delivery information</li><li>Cancellation and return information</li><li>Refund information</li><li>Payment method<br></li></ul><p></p><p class=\"MsoNormal\"><b>EverythingB2C</b> currently offers <b>Pash on Delivery (POD)</b>.<br><span>We do not currently operate our own online payment gateway.\r\nIf an online payment facility is introduced in the future, payment-related\r\ninformation may be processed by the relevant payment service provider in\r\naccordance with its privacy policy and applicable law.</span></p><p class=\"MsoNormal\"><b>2.3 Device and Technical Information<br></b><span>When you visit our website, certain technical information\r\nmay be collected automatically, including, where applicable:<br></span></p><p class=\"MsoNormal\"></p><ul><li>IP address</li><li>Browser type</li><li>Device type</li><li>Operating system</li><li>Website pages visited</li><li>Date and time of visits</li><li>Referring website</li><li>General website usage information</li><li>Technical logs and diagnostic information<br></li></ul><div></div><p></p><p class=\"MsoNormal\">This information may be used to maintain website security,\r\nimprove performance and understand how customers use our website.</p><p class=\"MsoNormal\"><b>2.4 Cookies and Similar Technologies<br></b><span><b>EverythingB2C</b></span><span> may use cookies and similar technologies to:</span></p><p class=\"MsoNormal\"></p><ul><li>Keep you signed in;</li><li>Maintain shopping cart functionality;</li><li>Remember preferences;</li><li>Improve website functionality;</li><li>Understand website usage;</li><li>Improve user experience; and</li><li>Detect security or technical issues.</li></ul><div></div><p></p><p class=\"MsoNormal\">You may be able to control or disable cookies through your\r\nbrowser settings. However, disabling certain cookies may affect website\r\nfunctionality.</p><p class=\"MsoNormal\"><span></span></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\"><span></span></p><p class=\"MsoNormal\"></p><div><p class=\"MsoNormal\"></p></div><p class=\"MsoNormal\"><span></span></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\"><span></span></p><p class=\"MsoNormal\"></p><ul class=\"privacy-list\">\r\n                    </ul>\r\n                </div>\r\n\r\n                <div class=\"privacy-section\">\r\n                    <h2 style=\"font-size:20px\">Why We Collect and Use Your Information</h2>\r\n                    <p class=\"MsoNormal\">We may process your personal information for purposes\r\nincluding:<br></p><p class=\"MsoNormal\"><b>Providing Our Services<br></b></p><p class=\"MsoNormal\"></p><ul><li>Creating and managing your account;</li><li>Processing and fulfilling orders;</li><li>Providing product and seller information;</li><li>Arranging pickup and fulfilment;</li><li>Arranging delivery;</li><li>Providing order tracking;</li><li>Processing returns, cancellations and refunds;</li><li>Providing customer support.</li></ul><div></div><p></p><p class=\"MsoNormal\"><b>Marketplace Operations<br></b><span>Because </span><span><b>EverythingB2C</b></span><span> operates as a marketplace, certain\r\ninformation may need to be shared with the relevant seller to enable the seller\r\nto fulfil the order.<br></span></p><p class=\"MsoNormal\">This may include information such as:<br></p><p class=\"MsoNormal\"></p><ul><li>Customer name;</li><li>Delivery information;</li><li>Contact information where necessary;</li><li>Products ordered; and</li><li>Order-related information.</li></ul><div></div><p></p><p class=\"MsoNormal\"><b>Communication<br></b><span>We may use your contact information to:</span></p><p class=\"MsoNormal\"></p><ul><li>Confirm orders;</li><li>Provide order status updates;</li><li>Communicate delivery information;</li><li>Respond to customer support requests;</li><li>Contact you regarding returns, cancellations or refunds; and</li><li>Send important service-related communications.</li></ul><p></p><p class=\"MsoNormal\"><span></span></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\"><b>Improving EverythingB2C<br></b><span>We may analyse information about website and service usage\r\nto:</span></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\">\r\n\r\n\r\n\r\n</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Improve\r\n     website functionality;</li>\r\n <li class=\"MsoNormal\">Improve\r\n     product discovery;</li>\r\n <li class=\"MsoNormal\">Improve\r\n     marketplace operations;</li>\r\n <li class=\"MsoNormal\">Understand\r\n     customer requirements;</li>\r\n <li class=\"MsoNormal\">Detect\r\n     technical problems; and</li>\r\n <li class=\"MsoNormal\">Improve\r\n     our services.</li>\r\n</ul><p class=\"MsoNormal\"><b>Security and Fraud Prevention<br></b><span>Information may be processed to:</span></p><p class=\"MsoNormal\"></p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Protect\r\n     customer accounts;</li>\r\n <li class=\"MsoNormal\">Detect\r\n     suspicious activity;</li>\r\n <li class=\"MsoNormal\">Prevent\r\n     fraudulent transactions;</li>\r\n <li class=\"MsoNormal\">Protect\r\n     the website and marketplace;</li>\r\n <li class=\"MsoNormal\">Investigate\r\n     misuse; and</li>\r\n <li class=\"MsoNormal\">Maintain\r\n     the security of our systems.</li>\r\n</ul><p class=\"MsoNormal\"><b>Legal and Regulatory Requirements<br></b><span>We may process and retain information where\r\nnecessary to comply with applicable laws, regulations, legal processes,\r\ngovernmental requests, accounting requirements or other lawful obligations.</span></p>\r\n                </div>\r\n\r\n                <div class=\"privacy-section\">\r\n                    <h2 style=\"font-size:20px\">Sharing of Personal Information</h2>\r\n                    <p class=\"MsoNormal\">We do not sell your personal information as a product.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">However, information may be shared with third parties where\r\nreasonably necessary to provide our services or comply with legal obligations.<br></p><p class=\"MsoNormal\"><b>4.1 Sellers<br></b><span>Because </span><span><b>EverythingB2C</b></span><span> is a marketplace, relevant customer\r\ninformation may be shared with the seller responsible for fulfilling an order.</span></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\">For example, a seller may need the customer\'s:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Name;</li>\r\n <li class=\"MsoNormal\">Delivery\r\n     address;</li>\r\n <li class=\"MsoNormal\">Contact\r\n     information;</li>\r\n <li class=\"MsoNormal\">Order\r\n     details; and</li>\r\n <li class=\"MsoNormal\">Other\r\n     information reasonably necessary to fulfil the order.</li>\r\n</ul><p class=\"MsoNormal\">Sellers are expected to use customer information only for\r\nlegitimate purposes connected with the transaction and applicable marketplace\r\nservices.</p><p class=\"MsoNormal\"><b>4.2 Delivery and Fulfilment Providers<br></b><span>We may share necessary information with our delivery\r\npersonnel, logistics providers, pickup personnel or other fulfilment partners.</span></p><p class=\"MsoNormal\"></p><p class=\"MsoNormal\">This may include:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Customer\r\n     name;</li>\r\n <li class=\"MsoNormal\">Delivery\r\n     address;</li>\r\n <li class=\"MsoNormal\">Mobile\r\n     number;</li>\r\n <li class=\"MsoNormal\">Order\r\n     number; and</li>\r\n <li class=\"MsoNormal\">Delivery-related\r\n     information.</li>\r\n</ul><p class=\"MsoNormal\">Such information is provided for purposes such as pickup,\r\npacking, transportation, delivery and order-related communication.</p><p class=\"MsoNormal\"><b>4.3 Technology and Service Providers<br></b><span>We may use third-party service providers for functions such\r\nas:</span></p><p class=\"MsoNormal\"></p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Website\r\n     hosting;</li>\r\n <li class=\"MsoNormal\">Cloud\r\n     services;</li>\r\n <li class=\"MsoNormal\">Website\r\n     security;</li>\r\n <li class=\"MsoNormal\">Email;</li>\r\n <li class=\"MsoNormal\">SMS\r\n     or communication services;</li>\r\n <li class=\"MsoNormal\">Analytics;</li>\r\n <li class=\"MsoNormal\">Customer\r\n     support;</li>\r\n <li class=\"MsoNormal\">Data\r\n     storage;</li>\r\n <li class=\"MsoNormal\">Technical\r\n     maintenance; and</li>\r\n <li class=\"MsoNormal\">Other\r\n     services necessary to operate <b>EverythingB2C</b>.</li>\r\n</ul><p class=\"MsoNormal\">Such providers may process personal information only as\r\nreasonably necessary to provide the relevant service and subject to applicable\r\ncontractual, technical or legal safeguards.</p><p class=\"MsoNormal\"><b>4.4 Government and Legal Authorities<br></b><span>We may disclose information where required or\r\npermitted by applicable law, court order, governmental direction, regulatory\r\nrequirement, investigation or legal process.</span></p><p class=\"MsoNormal\"><b></b></p>\r\n                </div>\r\n\r\n                <div class=\"privacy-section\">\r\n                    <h2 style=\"font-size:20px\">Customer Account Information</h2>\r\n                    <p class=\"MsoNormal\">If you create an account on <b>EverythingB2C</b>, you are\r\nresponsible for maintaining the confidentiality of your login credentials.</p><p class=\"MsoNormal\">You should notify us if you believe your account has been\r\naccessed without authorization.</p><p>\r\n\r\n\r\n\r\n<span>We may take reasonable steps to verify your\r\nidentity before processing certain account-related requests.</span><br></p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Marketing Communications</h2><p class=\"MsoNormal\">We may send service-related communications that are\r\nnecessary for your orders or account.</p><p class=\"MsoNormal\">Where we send promotional or marketing communications, we will\r\nprovide an appropriate mechanism for managing or opting out of such\r\ncommunications where required by applicable law.</p><p>\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">Opting out of promotional communications will not\r\nnecessarily stop essential service communications relating to your account or\r\norders.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Data Retention</h2><p class=\"MsoNormal\">We retain personal information only for as long as\r\nreasonably necessary for the purposes for which it was collected, including:</p>\r\n\r\n<ul type=\"disc\">\r\n <li class=\"MsoNormal\">Providing\r\n     our services;</li>\r\n <li class=\"MsoNormal\">Maintaining\r\n     business and transaction records;</li>\r\n <li class=\"MsoNormal\">Resolving\r\n     disputes;</li>\r\n <li class=\"MsoNormal\">Processing\r\n     returns and refunds;</li>\r\n <li class=\"MsoNormal\">Preventing\r\n     fraud;</li>\r\n <li class=\"MsoNormal\">Maintaining\r\n     security;</li>\r\n <li class=\"MsoNormal\">Complying\r\n     with legal, tax and accounting obligations; and</li>\r\n <li class=\"MsoNormal\">Exercising\r\n     or defending legal rights.</li>\r\n</ul>\r\n\r\n<p class=\"MsoNormal\">When information is no longer reasonably required, we may\r\ndelete, anonymise or otherwise dispose of it in accordance with applicable law\r\nand our operational requirements.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Data Security</h2><p class=\"MsoNormal\">We take reasonable technical and organisational measures to\r\nprotect personal information against unauthorized access, misuse, alteration,\r\nloss or disclosure.</p><p class=\"MsoNormal\">However, no website, electronic transmission or storage\r\nsystem can be guaranteed to be completely secure.</p><p>\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">Customers should also take reasonable steps to protect their\r\naccount credentials and devices.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Customer Rights and Requests</h2><p class=\"MsoNormal\">Subject to applicable law and the relevant provisions coming\r\ninto force, customers may have rights relating to their personal information,\r\nincluding rights to:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Obtain\r\n     information about personal data being processed;</li>\r\n <li class=\"MsoNormal\">Request\r\n     correction of inaccurate or incomplete personal information;</li>\r\n <li class=\"MsoNormal\">Request\r\n     deletion of personal information where applicable;</li>\r\n <li class=\"MsoNormal\">Withdraw\r\n     consent where processing is based on consent;</li>\r\n <li class=\"MsoNormal\">Raise\r\n     a grievance regarding processing of personal information; and</li>\r\n <li class=\"MsoNormal\">Exercise\r\n     other rights available under applicable law.</li>\r\n</ul><p class=\"MsoNormal\">The Digital Personal Data Protection Act, 2023 provides\r\nrights including access to information about personal data and\r\ncorrection/erasure in applicable circumstances.</p><p class=\"MsoNormal\">To submit a privacy-related request, contact:</p><p class=\"MsoNormal\"><span class=\"MsoHyperlink\"><b><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></b></span></p><p>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n<span>We may need to verify your identity before\r\nprocessing certain requests.</span><br></p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Withdrawal of Consent</h2><p class=\"MsoNormal\">Where processing is based on your consent, you may request\r\nwithdrawal of that consent, subject to applicable law and the consequences of\r\nwithdrawal.</p><p class=\"MsoNormal\">Withdrawal of consent may affect our ability to provide\r\ncertain services where the relevant information is necessary to provide those\r\nservices.</p><p class=\"MsoNormal\">For example, if information necessary to fulfil an order is\r\nwithdrawn before fulfilment, we may not be able to complete that order.</p><p>\r\n\r\n\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">The 2025 Rules provide for consent withdrawal mechanisms and\r\nrequire the withdrawal process to be as easy as the process through which\r\nconsent was given.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Accuracy of Information</h2><p class=\"MsoNormal\">Customers are responsible for providing accurate and current\r\ninformation.</p><p class=\"MsoNormal\">Customers should ensure that their:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Name;</li>\r\n <li class=\"MsoNormal\">Mobile\r\n     number;</li>\r\n <li class=\"MsoNormal\">Email\r\n     address;</li>\r\n <li class=\"MsoNormal\">Delivery\r\n     address; and</li>\r\n <li class=\"MsoNormal\">PIN\r\n     code</li>\r\n</ul><p class=\"MsoNormal\">are accurate when placing an order.</p><p>\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">Incorrect information may result in delivery delays or\r\nfailed delivery.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Children\'s Privacy</h2><p class=\"MsoNormal\"><b>EverythingB2C</b> is intended for general consumers and is not\r\nspecifically directed at children.</p><p class=\"MsoNormal\">We do not knowingly seek to collect personal information\r\nfrom children except where permitted and handled in accordance with applicable\r\nlaw.</p><p class=\"MsoNormal\">If you believe that a child has provided personal\r\ninformation to us improperly, please contact us at:</p><p>\r\n\r\n\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\"><span class=\"MsoHyperlink\"><b><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></b></span></p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Third-Party Websites</h2><p class=\"MsoNormal\">The <b>EverythingB2C</b> website may contain links to third-party\r\nwebsites, services or platforms.</p><p class=\"MsoNormal\">This Privacy Policy does not govern the privacy practices of\r\nthose third parties.</p><p>\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">Customers should review the privacy policies of third-party\r\nwebsites before providing personal information to them.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Seller Access to Customer Information</h2><p class=\"MsoNormal\">As <b>EverythingB2C</b> is a marketplace, sellers may receive\r\nlimited customer information necessary to fulfil orders.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">Sellers are not permitted to use customer information\r\nobtained through <b>EverythingB2C</b> for unrelated marketing, resale of customer\r\ninformation, unauthorized communication or other purposes not connected with\r\nlegitimate order fulfilment or marketplace operations.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Data Breaches and Security Incidents</h2><p class=\"MsoNormal\">Where required by applicable law, <b>EverythingB2C</b> will take\r\nappropriate steps in response to a personal data breach, including applicable\r\nnotifications to affected individuals and/or authorities.</p><p>\r\n\r\n</p><p class=\"MsoNormal\">We will also take reasonable steps to contain, investigate\r\nand address security incidents.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Changes to This Privacy Policy</h2><p class=\"MsoNormal\">We may update this Privacy Policy from time to time to\r\nreflect:</p><ul type=\"disc\">\r\n <li class=\"MsoNormal\">Changes\r\n     in our services;</li>\r\n <li class=\"MsoNormal\">Changes\r\n     in how we process information;</li>\r\n <li class=\"MsoNormal\">Changes\r\n     in technology;</li>\r\n <li class=\"MsoNormal\">Changes\r\n     in applicable laws or regulations; or</li>\r\n <li class=\"MsoNormal\">Other\r\n     operational requirements.</li>\r\n</ul><p class=\"MsoNormal\">The updated version will be published on this page with a\r\nrevised \"Last Updated\" date.</p><p>\r\n\r\n\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\">Where required, we will provide an appropriate notice\r\nregarding material changes.</p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Grievance and Privacy Contact</h2><p class=\"MsoNormal\">For questions, complaints, requests or concerns relating to\r\nprivacy or personal information, contact:</p><p class=\"MsoNormal\"><b>EverythingB2C</b><br>\r\nDiwalipura, Vadodara, Gujarat, India<br>\r\nEmail: <span class=\"MsoHyperlink\"><b><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></b></span></p><p>\r\n\r\n\r\n\r\n</p><p class=\"MsoNormal\"><b>Grievance Officer:</b><br>\r\nName: <b>Sachin Ringe</b><br>\r\nDesignation: <b>Grievance Officer</b><br>\r\nEmail: <span class=\"MsoHyperlink\"><b><a href=\"mailto:info@everythingb2c.in\">info@everythingb2c.in</a></b></span></p></div><div class=\"privacy-section\"><h2 style=\"font-size:20px\">Governing Law</h2><p class=\"MsoNormal\">This Privacy Policy shall be governed by applicable laws of\r\nIndia.</p></div>', '{\"title_color\":\"#333333\",\"subtitle_color\":\"#2c539f\",\"description_color\":\"#666666\",\"bullet_color\":\"#1683e8\",\"title_size\":32,\"subtitle_size\":24,\"description_size\":15}', 'privacy.php', 30, 1, '2026-10-08 08:04:27', '2026-10-08 14:06:38');

-- --------------------------------------------------------

--
-- Table structure for table `manual_invoices`
--

CREATE TABLE `manual_invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(60) NOT NULL,
  `invoice_date` date NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `eway_bill_no` varchar(80) DEFAULT NULL,
  `eway_bill_date` date DEFAULT NULL,
  `buyer_po_no` varchar(80) DEFAULT NULL,
  `buyer_po_date` date DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT 'Manual',
  `payment_date` date DEFAULT NULL,
  `transporter` varchar(150) DEFAULT NULL,
  `lr_no` varchar(80) DEFAULT NULL,
  `bill_to_name` varchar(150) DEFAULT NULL,
  `bill_to_gstin` varchar(30) DEFAULT NULL,
  `bill_to_mobile` varchar(30) DEFAULT NULL,
  `bill_to_address` text DEFAULT NULL,
  `ship_to_name` varchar(150) DEFAULT NULL,
  `ship_to_gstin` varchar(30) DEFAULT NULL,
  `ship_to_mobile` varchar(30) DEFAULT NULL,
  `ship_to_address` text DEFAULT NULL,
  `taxable_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cgst_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sgst_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock_deducted` tinyint(1) NOT NULL DEFAULT 0,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `tax_type` varchar(20) NOT NULL DEFAULT 'within_state',
  `igst_total` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manual_invoices`
--

INSERT INTO `manual_invoices` (`id`, `invoice_no`, `invoice_date`, `customer_name`, `mobile_no`, `eway_bill_no`, `eway_bill_date`, `buyer_po_no`, `buyer_po_date`, `payment_terms`, `payment_date`, `transporter`, `lr_no`, `bill_to_name`, `bill_to_gstin`, `bill_to_mobile`, `bill_to_address`, `ship_to_name`, `ship_to_gstin`, `ship_to_mobile`, `ship_to_address`, `taxable_total`, `cgst_total`, `sgst_total`, `grand_total`, `stock_deducted`, `created_by`, `created_at`, `updated_at`, `tax_type`, `igst_total`) VALUES
(3, 'B2C-202609-001', '2026-09-12', 'Mr. Jayant', '8446289732', '-', NULL, 'Telephonic', '2026-09-11', 'Cash', '2026-09-11', 'Tempo', '-', 'Shree Enterprises', '27ACAPH6714Q1ZA', '8446289732', 'Pune Solapur Road, Vitthal Nagar, Akashwani, Near Tekawde Petrol Pump, Hadapsar, Pune - 411028', 'Shree Enterprises', '27ACAPH6714Q1ZA', '8446289732', 'Pune Solapur Road, Vitthal Nagar, Akashwani, Near Tekawde Petrol Pump, Hadapsar, Pune - 411028', 4515.00, 0.00, 0.00, 5217.00, 1, 1, '2026-09-11 11:52:30', '2026-09-11 13:21:29', 'out_of_state', 702.00),
(4, 'B2C-202610-0002', '2026-10-30', 'Mr. Jayant', '8446289732', '-', '2026-10-30', 'Telephonic', '2026-10-30', 'Manual', '2026-10-30', 'Tempo', '-', 'Shree Enterprises', '27ACAPH6714Q1ZA', '8446289732', 'Pune Solapur Road, Vitthal Nagar, Akashwani, Near\r\nTekawde Petrol Pump, Hadapsar, Pune - 411028', 'Shree Enterprises', '27ACAPH6714Q1ZA', '8446289732', 'Pune Solapur Road, Vitthal Nagar, Akashwani, Near\r\nTekawde Petrol Pump, Hadapsar, Pune - 411028', 1519.05, 37.98, 37.97, 1595.00, 1, 1, '2026-10-30 11:01:48', '2026-10-30 11:13:08', 'within_state', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `manual_invoice_items`
--

CREATE TABLE `manual_invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `hsn` varchar(40) DEFAULT NULL,
  `unit` varchar(30) DEFAULT 'No.',
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `item_mrp` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `taxable_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `igst_amount` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `manual_invoice_items`
--

INSERT INTO `manual_invoice_items` (`id`, `invoice_id`, `product_id`, `product_name`, `product_image`, `hsn`, `unit`, `gst_rate`, `item_mrp`, `item_price`, `quantity`, `taxable_value`, `cgst_amount`, `sgst_amount`, `total_price`, `sort_order`, `created_at`, `igst_amount`) VALUES
(62, 3, 341, 'Bubble Wrap | 1 meter x 100 meter', 'uploads/products/6aa3e7bd62997.webp', '39201012', 'No.', 18.00, 1700.00, 1534.00, 3.00, 3900.00, 0.00, 0.00, 4602.00, 0, '2026-09-11 13:21:29', 702.00),
(63, 3, 241, '5 Ply | 13 x 9.5 x 10 Inch | 5 kg', 'uploads/products/6aa3f7cf0cbd8.jpg', '48191010', 'No.', 0.00, 85.00, 65.00, 1.00, 65.00, 0.00, 0.00, 65.00, 1, '2026-09-11 13:21:29', 0.00),
(64, 3, 242, '5 Ply | 18 x 14 x 10 Inch | 10 kg', 'uploads/products/6aa3f81f56d17.jpg', '48191010', 'No.', 0.00, 140.00, 105.00, 1.00, 105.00, 0.00, 0.00, 105.00, 2, '2026-09-11 13:21:29', 0.00),
(65, 3, 243, '5 Ply | 21 x 13 x 14 Inch | 15 kg', 'uploads/products/6aa3f86c7be76.jpg', '48191010', 'No.', 0.00, 170.00, 130.00, 1.00, 130.00, 0.00, 0.00, 130.00, 3, '2026-09-11 13:21:29', 0.00),
(66, 3, 342, '5 Ply | 16 x 12 x 3 Inch | Laptop Box', 'uploads/products/6aa3f28f6196b.webp', '48191010', 'No.', 0.00, 100.00, 75.00, 1.00, 75.00, 0.00, 0.00, 75.00, 4, '2026-09-11 13:21:29', 0.00),
(67, 3, 343, 'Alpha Power | A4 Size | 75 GSM | 500 Sheets', 'uploads/products/6aa3fbc35d378.jpg', '48025610', 'No.', 0.00, 350.00, 240.00, 1.00, 240.00, 0.00, 0.00, 240.00, 5, '2026-09-11 13:21:29', 0.00),
(80, 4, 240, '5 Ply | 10 x 9 x 5 Inch | 3 kg', 'uploads/products/6aa3f75d13290.jpg', '48191010', 'No.', 5.00, 545.00, 42.00, 10.00, 400.00, 10.00, 10.00, 420.00, 0, '2026-10-30 11:13:08', 0.00),
(81, 4, 241, '5 Ply | 13 x 9.5 x 10 Inch | 6 kg', 'uploads/products/6aa3f7cf0cbd8.jpg', '48191010', 'No.', 5.00, 780.00, 65.00, 10.00, 619.05, 15.48, 15.47, 650.00, 1, '2026-10-30 11:13:08', 0.00),
(82, 4, 242, '5 Ply | 18 x 14 x 10 Inch | 10 kg', 'uploads/products/6aa3f81f56d17.jpg', '48191010', 'No.', 5.00, 1130.00, 105.00, 5.00, 500.00, 12.50, 12.50, 525.00, 2, '2026-10-30 11:13:08', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

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
  `upi_screenshot` varchar(255) DEFAULT NULL,
  `reordered_from_order_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `seller_id`, `address_id`, `order_number`, `total_amount`, `subtotal`, `gst_amount`, `shipping_charge`, `shipping_zone_id`, `billing_state`, `billing_city`, `billing_pincode`, `status`, `shipping_address`, `payment_method`, `created_at`, `tracking_id`, `external_tracking_id`, `external_tracking_link`, `order_status_id`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `paid_at`, `gst_number`, `company_name`, `is_business_purchase`, `status_description`, `estimated_delivery_date`, `actual_delivery_date`, `upi_transaction_id`, `upi_screenshot`, `reordered_from_order_id`) VALUES
(12, 1, NULL, NULL, 'ORDER202507088016', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'razorpay', '2025-07-08 08:56:06', 'Everythingb2c66236680', NULL, NULL, 1, 'pending', 'order_QqVyxPfVtgAjfJ', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(13, 1, NULL, NULL, 'ORDER202507083957', 41.30, 35.00, 6.30, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'razorpay', '2025-07-08 08:57:10', 'Everythingb2c61103209', NULL, NULL, 1, 'pending', 'order_QqW04auv0iB84m', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(14, 1, NULL, NULL, 'ORDER202507082717', 112.10, 95.00, 17.10, 0.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-08 09:07:20', 'Everythingb2c47141244', NULL, NULL, 1, 'paid', 'order_QqWAnXnE5OTBtV', 'pay_QqWB1WsU6Emezq', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 1, NULL, NULL, 'ORDER202507087112', 112.10, 95.00, 17.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-08 09:19:04', 'Everythingb2c55889181', NULL, NULL, 1, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 1, NULL, NULL, 'ORDER202507083491', 483.80, 410.00, 73.80, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-08 09:21:23', 'Everythingb2c03110318', '', '', 1, 'paid', NULL, NULL, NULL, NULL, NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(17, 1, NULL, NULL, 'ORDER202507083976', 123.90, 105.00, 18.90, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-08 10:33:09', 'Everythingb2c39930075', NULL, NULL, 1, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 1, NULL, NULL, 'ORDER202507085520', 123.90, 105.00, 18.90, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-08 10:34:08', 'Everythingb2c95007545', NULL, NULL, 1, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 1, NULL, NULL, 'ORDER202507091088', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 10:47:33', 'Everythingb2c81543477', NULL, NULL, 1, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 1, NULL, 10, 'ORDER202507091739', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 13:55:43', 'Everythingb2c45276575', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 1, NULL, NULL, 'ORDER202507098748', 483.80, 410.00, 73.80, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 13:58:23', 'Everythingb2c77567796', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 1, NULL, NULL, 'ORDER202507097304', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'razorpay', '2025-07-09 14:04:45', 'Everythingb2c08456249', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 1, NULL, NULL, 'ORDER202507092077', 289.10, 245.00, 44.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'razorpay', '2025-07-09 14:09:30', 'Everythingb2c34380893', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 1, NULL, NULL, 'ORDER202507099621', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 14:16:11', 'Everythingb2c27316660', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 1, NULL, NULL, '', 445.24, 318.00, 57.24, 70.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 14:19:29', NULL, NULL, NULL, 1, 'paid', 'order_Qr01iDO4UxsRRl', 'pay_Qr020GbZ978g5k', NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 1, NULL, 10, 'ORDER202507099782', 236.00, 200.00, 36.00, 0.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 14:22:54', 'Everythingb2c11526692', NULL, NULL, 1, 'paid', 'order_Qr05J4DKD8JdpH', 'pay_Qr05XJ2YvzqdRO', NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 1, NULL, NULL, 'ORDER202507093476', 553.80, 410.00, 73.80, 70.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'razorpay', '2025-07-09 14:25:47', 'Everythingb2c34216156', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 1, NULL, NULL, 'ORDER202507099997', 553.80, 410.00, 73.80, 70.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'razorpay', '2025-07-09 14:25:54', 'Everythingb2c12999204', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 1, NULL, NULL, 'ORDER202507092005', 553.80, 410.00, 73.80, 70.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 14:26:26', 'Everythingb2c70593336', NULL, NULL, 1, 'paid', 'order_Qr093IhEB0E0ou', 'pay_Qr09JIfa6ymA01', NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 1, NULL, NULL, 'ORDER202507091604', 406.30, 285.00, 51.30, 70.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 14:28:42', 'Everythingb2c21194149', 'TRCK001', 'https://google.com', 34, 'paid', 'order_Qr0BRB2Xh1z0k7', 'pay_Qr0Beaz5CpNc2e', NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(33, 1, NULL, NULL, 'ORDER202507092941', 820.48, 636.00, 114.48, 70.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 15:03:12', 'Everythingb2c44660028', NULL, NULL, 1, 'paid', 'order_Qr0ltzik1brFYj', 'pay_Qr0m7q8XLupiWq', NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 1, NULL, NULL, 'ORDER202507097506', 1612.26, 1307.00, 235.26, 70.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 15:19:36', 'Everythingb2c49775529', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 1, NULL, 10, 'ORDER202507094251', 206.50, 175.00, 31.50, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 16:27:35', 'Everythingb2c77827159', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 1, NULL, 10, 'ORDER202507094409', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 16:30:58', 'Everythingb2c19101886', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 1, NULL, 10, 'ORDER202507097085', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 16:31:31', 'Everythingb2c43079069', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 1, NULL, 10, 'ORDER202507094606', 375.24, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 16:31:53', 'Everythingb2c42563697', NULL, NULL, 1, 'paid', 'order_Qr2HX6uc6IjXAP', 'pay_Qr2HsHpxgP2wS9', NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 1, NULL, NULL, 'ORDER202507099260', 475.24, 318.00, 57.24, 100.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 18:39:21', 'Everythingb2c37464001', NULL, NULL, 1, 'paid', 'order_Qr4SAWGDAmP7FL', 'pay_Qr4SRNBMWDGgPU', NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 1, NULL, 10, 'ORDER202507091693', 599.44, 508.00, 91.44, 0.00, NULL, NULL, NULL, NULL, 'confirmed', NULL, 'razorpay', '2025-07-09 18:51:41', 'Everythingb2c49484736', 'D0005640777', '', 1, 'paid', 'order_Qr4fCURPLFQmWI', 'pay_Qr4fRMSQkqD9qX', NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(41, 1, NULL, NULL, 'ORDER202507098897', 475.24, 318.00, 57.24, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 19:15:29', 'Everythingb2c15029790', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 1, NULL, NULL, 'ORDER202507092585', 288.80, 160.00, 28.80, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-09 19:51:21', 'Everythingb2c26118502', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 1, NULL, NULL, 'ORDER202507104883', 1298.88, 1016.00, 182.88, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-10 05:11:04', 'Everythingb2c36327833', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 1, NULL, 10, 'ORDER202507132525', 1895.08, 1606.00, 289.08, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-13 06:40:38', 'Everythingb2c34482495', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 1, NULL, 10, 'ORDER202507166754', 247.80, 210.00, 37.80, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'direct_payment', '2025-07-16 13:38:58', 'Everythingb2c30742473', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, 'txn_12345678trd', NULL, NULL),
(46, 1, NULL, 10, 'ORDER202507228961', 1036.04, 878.00, 158.04, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-22 17:57:28', 'EverythingB2C74666831', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 1, NULL, 10, 'ORDER202507262876', 1030.14, 873.00, 157.14, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-26 14:22:13', 'EverythingB2C56758332', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 1, NULL, 10, 'ORDER202507299494', 4201.00, 4201.00, 706.98, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-29 15:39:03', 'EverythingB2C26475301', 'D1005560078', '', 1, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(49, 4, NULL, 12, 'ORDER202507296952', 295.00, 295.00, 53.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-29 15:49:02', 'EverythingB2C41600018', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 4, NULL, 13, 'ORDER202507296039', 490.00, 390.00, 70.20, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-07-29 17:54:09', 'EverythingB2C15663702', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 5, NULL, 14, 'ORDER202508173761', 513.00, 513.00, 92.34, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-08-17 10:19:58', 'EverythingB2C48972865', 'D3003398857', '', 3, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(52, 6, NULL, 15, 'ORDER202508279563', 260.00, 160.00, 28.80, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-08-27 15:45:18', 'EverythingB2C49310619', 'D3003398857', '', 1, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(53, 5, NULL, 14, 'ORDER202509144626', 280.00, 280.00, 50.40, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-14 16:47:57', 'EverythingB2C70958473', '7D154319924', '', 1, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(54, 5, NULL, 14, 'ORDER202509222629', 408.00, 408.00, 73.44, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-22 06:04:22', 'EverythingB2C95022888', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 5, NULL, 14, 'ORDER202509227857', 215.00, 215.00, 38.70, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-22 06:09:24', 'EverythingB2C05414971', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 5, NULL, 14, 'ORDER202509235384', 568.00, 568.00, 102.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-23 15:13:18', 'EverythingB2C42141670', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 5, NULL, 14, 'ORDER202509274935', 435.00, 435.00, 78.30, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-27 06:03:00', 'EverythingB2C55652995', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 5, NULL, 14, 'ORDER202509275880', 210.00, 210.00, 37.80, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-27 06:04:10', 'EverythingB2C58880258', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 5, NULL, 14, 'ORDER202509278874', 180.00, 180.00, 32.40, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-27 06:44:00', 'EverythingB2C46307437', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 1, NULL, 10, 'ORDER202509276184', 538.00, 538.00, 96.84, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-09-27 06:53:23', 'EverythingB2C86135192', 'D0005640777', '', 3, 'pending', NULL, NULL, NULL, '', NULL, 0, '', '2026-02-13', NULL, NULL, NULL, NULL),
(61, 10, NULL, NULL, 'ORDER202512229031', 180.00, 180.00, 32.40, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2025-12-22 05:29:14', 'EverythingB2C27347219', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 12, NULL, NULL, 'ORDER202601313627', 418.00, 318.00, 57.24, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-01-31 06:43:06', 'EverythingB2C88217404', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 12, NULL, NULL, 'ORDER202601310622', 1420.00, 1320.00, 237.60, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-01-31 06:47:49', 'EverythingB2C40020212', 'D0005640777', '', 31, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(64, 1, NULL, 10, 'ORDER202602073532', 180.00, 180.00, 32.40, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-07 15:43:08', 'EverythingB2C06672299', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 1, NULL, 10, 'ORDER202602079897', 918.00, 918.00, 165.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-07 15:43:32', 'EverythingB2C23627084', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 14, NULL, NULL, 'ORDER202602112074', 918.00, 918.00, 165.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-11 09:21:53', 'EverythingB2C04296840', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 14, NULL, 19, 'ORDER202602138774', 35.00, 35.00, 6.30, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-13 10:01:48', 'EverythingB2C56713881', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 1, NULL, 10, 'ORDER202602170426', 1458.00, 1458.00, 262.44, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-17 10:10:31', 'EverythingB2C71461666', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 1, NULL, 10, 'ORDER202602173100', 285.00, 285.00, 51.30, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'direct_payment', '2026-02-17 14:17:34', 'EverythingB2C98682869', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, 'T123456789', NULL, NULL),
(70, 5, NULL, 14, 'ORDER202602180174', 990.00, 990.00, 178.20, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-18 05:54:28', 'EverythingB2C75404818', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 1, NULL, 10, 'ORDER202602191783', 1836.00, 1836.00, 330.48, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'direct_payment', '2026-02-19 12:00:57', 'EverythingB2C14764343', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, 'txn_12345678trd', NULL, NULL),
(72, 1, NULL, 10, 'ORDER202602221547', 1836.00, 1836.00, 330.48, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-22 19:44:28', 'EverythingB2C37600793', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 1, NULL, 10, 'ORDER202602228555', 318.00, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-22 19:48:33', 'EverythingB2C26897791', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 1, NULL, 10, 'ORDER202602228872', 318.00, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-22 19:51:03', 'EverythingB2C49607989', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 1, NULL, 10, 'ORDER202602222825', 318.00, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-22 19:53:02', 'EverythingB2C70855919', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 12, NULL, NULL, 'ORDER202602231022', 190.00, 90.00, 16.20, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-23 10:57:34', 'EverythingB2C07453030', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 18, NULL, 22, 'ORDER202602276174', 280.00, 180.00, 32.40, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-27 21:14:30', 'EverythingB2C24023253', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 18, NULL, 22, 'ORDER202602270500', 280.00, 180.00, 32.40, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-02-27 21:20:33', 'EverythingB2C86289793', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 1, NULL, 10, 'ORDER202603055068', 740.00, 740.00, 133.20, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-05 07:18:34', 'EverythingB2C74778317', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 17, NULL, NULL, 'ORDER202603140807', 95.00, 95.00, 17.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'direct_payment', '2026-03-14 07:52:19', 'EverythingB2C21660384', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, 'yelkar.shital@okicici', NULL, NULL),
(81, 17, NULL, NULL, 'ORDER202603144868', 95.00, 95.00, 17.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'direct_payment', '2026-03-14 09:24:15', 'EverythingB2C24231587', '', '', 1, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, 'yelkar.shital@okicici', NULL, NULL),
(82, 17, NULL, NULL, 'ORDER202603140948', 95.00, 95.00, 17.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-14 09:52:21', 'EverythingB2C05879311', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 1, NULL, 10, 'ORDER202603247542', 1350.00, 1350.00, 243.00, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-24 08:30:16', 'EverythingB2C75680915', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 1, NULL, 10, 'ORDER202603246999', 1350.00, 1350.00, 243.00, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-24 08:32:26', 'EverythingB2C94227172', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 10, NULL, NULL, 'ORDER202603254908', 645.00, 645.00, 116.10, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 06:35:09', 'EverythingB2C13807297', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 1, NULL, NULL, 'ORDER202603258620', 1693.00, 1693.00, 304.74, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 10:53:53', 'everythingb2c40383992', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 1, NULL, NULL, 'ORDER202603258851', 318.00, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 10:54:48', 'everythingb2c71473491', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 1, NULL, NULL, 'ORDER202603256149', 318.00, 318.00, 57.24, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 10:55:31', 'everythingb2c70350906', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 1, NULL, NULL, 'ORDER202603252178', 408.00, 408.00, 73.44, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 10:57:50', 'everythingb2c56343153', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 1, NULL, NULL, 'ORDER202603257694', 408.00, 408.00, 73.44, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 10:59:53', 'everythingb2c20720455', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 1, NULL, NULL, 'ORDER202603255781', 1513.00, 1513.00, 272.34, 0.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 13:26:30', 'everythingb2c07648760', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 1, NULL, NULL, 'ORDER202603254287', 2238.00, 2138.00, 384.84, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-25 14:02:12', 'everythingb2c63045133', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 23, NULL, NULL, 'ORDER202603261903', 160.00, 90.00, 16.20, 70.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-26 08:56:10', 'everythingb2c48713506', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 1, NULL, NULL, 'ORDER202603270814', 1751.00, 1651.00, 297.18, 100.00, NULL, NULL, NULL, NULL, 'pending', NULL, 'cod', '2026-03-27 10:30:28', 'everythingb2c97180521', 'P79187925', '', 1, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(95, 30, NULL, NULL, 'ORDER202606106249', 636.00, 636.00, 114.48, 0.00, NULL, '123', '123', '412307', 'pending', '123\n123\n123\n123, 123 - 412307\nPhone: Abcdefghj', 'cod', '2026-06-10 11:53:17', 'everythingb2c23109474', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 30, NULL, NULL, 'ORDER202606108162', 318.00, 318.00, 57.24, 0.00, NULL, '123', '123', '412307', 'pending', '123\n123\n123\n123, 123 - 412307\nPhone: Abcdefghj', 'cod', '2026-06-10 11:54:48', 'everythingb2c26484233', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 30, NULL, NULL, 'ORDER202606115587', 630.00, 630.00, 113.40, 0.00, NULL, 'MP', 'akola', '000000', 'pending', 'abc\nabc def\ndef\nakola, MP - 000000\nPhone: 9876543210', 'cod', '2026-06-11 11:58:36', 'everythingb2c56913496', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 23, NULL, NULL, 'ORDER202606121910', 370.00, 300.00, 54.00, 70.00, NULL, 'Maharashtra', 'islampur', '411025', 'pending', 'komal gaikwad\nislampur\nislampur, Maharashtra - 411025\nPhone: 07620711816', 'cod', '2026-06-12 07:12:37', 'everythingb2c73804843', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 23, NULL, NULL, 'ORDER202606124015', 170.00, 100.00, 18.00, 70.00, NULL, 'Maharashtra', 'islampur', '411025', 'pending', 'komal gaikwad\nislampur\nislampur, Maharashtra - 411025\nPhone: 07620711816', 'cod', '2026-06-12 07:24:09', 'everythingb2c58735081', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 23, NULL, NULL, 'ORDER202606126039', 250.00, 180.00, 32.40, 70.00, NULL, 'Maharashtra', 'islampur', '411025', 'pending', 'komal gaikwad\nislampur\nislampur, Maharashtra - 411025\nPhone: 07620711816', 'cod', '2026-06-12 08:39:17', 'everythingb2c21313406', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 23, NULL, NULL, 'ORDER202606122556', 370.00, 300.00, 54.00, 70.00, NULL, 'Maharashtra', 'islampur', '411025', 'pending', 'komal gaikwad\nislampur\nislampur, Maharashtra - 411025\nPhone: 07620711816', 'cod', '2026-06-12 08:41:26', 'everythingb2c81745411', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 30, NULL, NULL, 'ORDER202606124331', 160.00, 160.00, 28.80, 0.00, NULL, 'mh', 'pune', '412307', 'pending', 'shital\nabc 123\nabc\npune, mh - 412307\nPhone: 7387942042', 'cod', '2026-06-12 10:47:14', 'everythingb2c40998195', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 30, NULL, NULL, 'ORDER202606127826', 325.00, 325.00, 58.50, 0.00, NULL, 'mh', 'pune', '190002', 'pending', 'shital\nabc 123\nabc\npune, mh - 190002\nPhone: 7387942042', 'cod', '2026-06-12 12:50:03', 'everythingb2c62709364', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 23, NULL, NULL, 'ORDER202606131889', 255.00, 185.00, 33.30, 70.00, NULL, 'Maharashtra', 'islampur', '411025', 'pending', 'komal gaikwad\nsangli\nurun\nislampur, Maharashtra - 411025\nPhone: 07620711816', 'cod', '2026-06-13 08:14:50', 'everythingb2c98045786', '', '', 31, 'pending', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(105, 30, NULL, 44, 'ORDER202606131385', 100.00, 100.00, 18.00, 0.00, NULL, 'maharashtra', 'Pune', '411001', 'pending', 'shital\nabc 123\nabc 123\nPune, maharashtra - 411001\nPhone: 987654321', 'cod', '2026-06-13 08:57:29', 'everythingb2c83684099', '', '', 1, 'paid', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(106, 30, NULL, 44, 'ORDER202606156737', 180.00, 180.00, 32.40, 0.00, NULL, 'maharashtra', 'Pune', '411001', 'pending', 'shital\nabc 123\nabc 123\nPune, maharashtra - 411001\nPhone: 987654321', 'cod', '2026-06-15 06:13:11', 'everythingb2c91123204', '', '', 3, 'paid', NULL, NULL, NULL, '', NULL, 0, 'payment done', '2026-06-15', NULL, NULL, NULL, NULL),
(107, 30, NULL, 44, 'ORDER202606151687', 200.00, 200.00, 36.00, 0.00, NULL, 'maharashtra', 'Pune', '560001', 'pending', 'shital\nabc 123\nabc 123\nPune, maharashtra - 560001\nPhone: 987654321', 'cod', '2026-06-15 10:14:40', 'everythingb2c14237980', '', '', 6, 'paid', NULL, NULL, NULL, '', NULL, 0, '', '2026-06-15', NULL, NULL, NULL, NULL),
(108, 30, NULL, NULL, 'ORDER202606151268', 240.00, 240.00, 43.20, 0.00, NULL, 'hariyana', 'hariyana', '200000', 'pending', 'shital\n123 abc\nabc 123\nhariyana, hariyana - 200000\nPhone: 9886543210', 'cod', '2026-06-15 12:59:00', 'everythingb2c29188788', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 30, NULL, NULL, 'ORDER202606154067', 180.00, 180.00, 32.40, 0.00, NULL, 'hariyana', 'hariyana', '200000', 'pending', 'shital\n123 abc\nabc 123\nhariyana, hariyana - 200000\nPhone: 9886543210', 'cod', '2026-06-15 13:03:11', 'everythingb2c88486637', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 30, NULL, 48, 'ORDER202606192752', 200.00, 200.00, 36.00, 0.00, NULL, 'nashik', '211345618562385621356230586230', '0000000000', 'pending', '12356165 6546546 654654+56-+-+\n12345678 12345\n123445123457852562395623956326523\n211345618562385621356230586230, nashik - 0000000000\nPhone: abcdefghsdjvhskdfvjb', 'cod', '2026-06-19 09:57:20', 'everythingb2c35907125', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 30, NULL, 48, 'ORDER202606191315', 636.00, 636.00, 114.48, 0.00, NULL, 'Port Blair', '211345618562385621356230586230', '0000000000', 'pending', '12356165 6546546 654654+56-+-+\n12345678 12345\n123445123457852562395623956326523\n211345618562385621356230586230, Port Blair - 0000000000\nPhone: abcdefghsdjvhskdfvjb', 'cod', '2026-06-19 11:08:42', 'everythingb2c34435110', '', '', 6, 'paid', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(112, 30, NULL, 48, 'ORDER202606235290', 270.00, 270.00, 48.60, 0.00, NULL, 'Port Blair', '211345618562385621356230586230', '0000000000', 'pending', '12356165 6546546 654654+56-+-+\n12345678 12345\n123445123457852562395623956326523\n211345618562385621356230586230, Port Blair - 0000000000\nPhone: abcdefghsdjvhskdfvjb', 'cod', '2026-06-23 05:58:16', 'everythingb2c38364194', '', '', 5, 'paid', NULL, NULL, NULL, '', NULL, 0, '', NULL, NULL, NULL, NULL, NULL),
(113, 31, NULL, 49, 'ORDER202609307770', 219.00, 119.00, 21.42, 100.00, NULL, 'gujarat', 'vadodara', '390015', 'pending', 'sachin ringe\nA-3, anant park soc, near sabri school, vasna road\nvadodara\nvadodara, gujarat - 390015\nPhone: 9998724576', 'cod', '2026-09-30 04:23:32', 'everythingb2c43733135', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 31, NULL, 49, 'ORDER202610025531', 230.00, 130.00, 23.40, 100.00, NULL, 'gujarat', 'vadodara', '390015', 'pending', 'sachin ringe\nA-3, anant park soc, near sabri school, vasna road\nvadodara\nvadodara, gujarat - 390015\nPhone: 9998724576', 'cod', '2026-10-02 09:16:49', 'everythingb2c25949483', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 12, NULL, 50, 'ORDER202610053074', 755.00, 655.00, 117.90, 100.00, NULL, 'gujarat', 'vadodara', '390015', 'pending', 'sachin ringe\nA-3, anant park soc, near sabri school, vasna road\nvadodara\nvadodara, gujarat - 390015\nPhone: 9998724576', 'cod', '2026-10-05 05:04:18', 'everythingb2c83247753', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 12, NULL, 50, 'ORDER202610106208', 320.00, 220.00, 39.60, 100.00, NULL, 'gujarat', 'vadodara', '390015', 'pending', 'sachin ringe\nA-3, anant park soc, near sabri school, vasna road\nvadodara\nvadodara, gujarat - 390015\nPhone: 9998724576', 'cod', '2026-10-10 04:29:36', 'everythingb2c44741754', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 12, NULL, 50, 'ORDER202610199421', 2966.00, 2866.00, 515.88, 100.00, NULL, 'gujarat', 'vadodara', '390015', 'pending', 'sachin ringe\nA-3, anant park soc, near sabri school, vasna road\nvadodara\nvadodara, gujarat - 390015\nPhone: 9998724576', 'cod', '2026-10-19 11:43:59', 'everythingb2c00062387', NULL, NULL, 1, 'pending', NULL, NULL, NULL, '', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

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
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `variation_id` int(11) DEFAULT NULL,
  `selected_attributes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `hsn`, `quantity`, `price`, `unit_price`, `gst_rate`, `gst_amount`, `mrp`, `selling_price`, `variation_id`, `selected_attributes`) VALUES
(135, 83, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(136, 83, 233, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(137, 83, 232, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(138, 83, 231, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(139, 83, 77, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(140, 83, 230, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(141, 83, 229, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(142, 83, 234, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(143, 83, 227, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(144, 83, 190, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(145, 83, 191, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(146, 83, 192, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(147, 83, 193, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(148, 83, 194, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(149, 83, 195, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(150, 84, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(151, 84, 234, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(152, 84, 233, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(153, 84, 232, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(154, 84, 231, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(155, 84, 230, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(156, 84, 229, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(157, 84, 228, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(158, 84, 227, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(159, 84, 226, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(160, 84, 225, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(161, 84, 224, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(162, 84, 223, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(163, 84, 222, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(164, 84, 221, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(166, 85, 232, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(167, 85, 150, '12345678', 3, 270.00, 90.00, 18.00, 48.60, 100.00, 90.00, NULL, NULL),
(170, 86, 41, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(171, 86, 77, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(175, 86, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(176, 86, 234, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(177, 86, 232, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(178, 86, 231, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(180, 86, 233, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(181, 86, 230, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(182, 86, 229, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(183, 86, 228, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(186, 89, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(188, 90, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(191, 91, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(197, 91, 77, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(198, 91, 41, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(199, 91, 233, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(200, 91, 231, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(201, 91, 230, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(202, 91, 229, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(203, 91, 228, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(204, 92, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(205, 92, 234, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(206, 92, 233, '12345678', 3, 270.00, 90.00, 18.00, 48.60, 100.00, 90.00, NULL, NULL),
(207, 92, 231, '12345678', 5, 450.00, 90.00, 18.00, 81.00, 100.00, 90.00, NULL, NULL),
(208, 92, 230, '12345678', 2, 180.00, 90.00, 18.00, 32.40, 100.00, 90.00, NULL, NULL),
(209, 92, 229, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(210, 92, 228, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(215, 92, 41, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(216, 92, 77, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(218, 93, 232, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(219, 94, 235, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(222, 94, 233, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(224, 94, 232, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(225, 94, 231, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(229, 94, 41, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(230, 94, 77, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(233, 97, 232, '12345678', 7, 630.00, 90.00, 18.00, 113.40, 100.00, 90.00, NULL, NULL),
(234, 98, 235, '12345678', 40, 200.00, 100.00, 18.00, 36.00, 120.00, 100.00, NULL, NULL),
(235, 98, 235, '12345678', 20, 100.00, 100.00, 18.00, 18.00, 140.00, 100.00, NULL, NULL),
(236, 99, 235, '12345678', 20, 100.00, 100.00, 18.00, 18.00, 140.00, 100.00, NULL, NULL),
(237, 100, 234, '12345678', 2, 180.00, 90.00, 18.00, 32.40, 100.00, 90.00, NULL, NULL),
(238, 101, 235, '12345678', 60, 300.00, 100.00, 18.00, 54.00, 140.00, 100.00, NULL, NULL),
(242, 104, 149, '12345678', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(244, 105, 235, '12345678', 20, 100.00, 100.00, 18.00, 18.00, 120.00, 100.00, NULL, NULL),
(245, 106, 114, '12345678', 2, 180.00, 90.00, 18.00, 32.40, 100.00, 90.00, NULL, NULL),
(246, 107, 235, '12345678', 40, 200.00, 100.00, 18.00, 36.00, 140.00, 100.00, NULL, NULL),
(248, 109, 232, '12345678', 2, 180.00, 90.00, 18.00, 32.40, 100.00, 90.00, NULL, NULL),
(249, 110, 235, '12345678', 40, 200.00, 100.00, 18.00, 36.00, 120.00, 100.00, NULL, NULL),
(251, 112, 149, '12345678', 3, 270.00, 90.00, 18.00, 48.60, 100.00, 90.00, NULL, NULL),
(252, 113, 405, '40169200', 1, 19.00, 19.00, 18.00, 3.42, 20.00, 19.00, NULL, NULL),
(253, 113, 321, '-', 1, 100.00, 100.00, 18.00, 18.00, 110.00, 100.00, NULL, NULL),
(254, 114, 362, '82034090', 1, 130.00, 130.00, 18.00, 23.40, 140.00, 130.00, NULL, NULL),
(255, 115, 297, '96081019', 1, 400.00, 400.00, 18.00, 72.00, 445.00, 400.00, NULL, NULL),
(256, 115, 290, '-', 1, 130.00, 130.00, 18.00, 23.40, 145.00, 130.00, NULL, NULL),
(257, 115, 286, '-', 1, 125.00, 125.00, 18.00, 22.50, 139.00, 125.00, NULL, NULL),
(258, 116, 410, '96081019', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, NULL),
(259, 116, 290, '48202000', 1, 130.00, 130.00, 18.00, 23.40, 145.00, 130.00, NULL, NULL),
(260, 117, 362, '82034090', 1, 130.00, 130.00, 18.00, 23.40, 140.00, 130.00, NULL, '[]'),
(261, 117, 297, '96081019', 1, 400.00, 400.00, 18.00, 72.00, 445.00, 400.00, NULL, '[]'),
(262, 117, 268, '37031010', 1, 199.00, 199.00, 18.00, 35.82, 222.00, 199.00, NULL, '[]'),
(263, 117, 246, '84729099', 1, 117.00, 117.00, 18.00, 21.06, 130.00, 117.00, NULL, '[]'),
(264, 117, 321, '96082000', 1, 100.00, 100.00, 18.00, 18.00, 110.00, 100.00, NULL, '[]'),
(265, 117, 292, '48202000', 1, 155.00, 155.00, 18.00, 27.90, 173.00, 155.00, NULL, '[]'),
(266, 117, 258, '48025610', 1, 299.00, 299.00, 18.00, 53.82, 333.00, 299.00, NULL, '[]'),
(267, 117, 250, '84729010', 1, 60.00, 60.00, 18.00, 10.80, 70.00, 60.00, NULL, '[]'),
(268, 117, 293, '48202000', 1, 169.00, 169.00, 18.00, 30.42, 188.00, 169.00, NULL, '[]'),
(269, 117, 318, '73269099', 1, 100.00, 100.00, 18.00, 18.00, 111.00, 100.00, NULL, '[]'),
(270, 117, 317, '73269099', 1, 105.00, 105.00, 18.00, 18.90, 117.00, 105.00, NULL, '[]'),
(271, 117, 333, '96081019', 3, 510.00, 170.00, 18.00, 91.80, 189.00, 170.00, NULL, '[]'),
(272, 117, 331, '96081019', 2, 306.00, 153.00, 18.00, 55.08, 170.00, 153.00, NULL, '[]'),
(273, 117, 353, '73269099', 1, 126.00, 126.00, 18.00, 22.68, 140.00, 126.00, NULL, '[]'),
(274, 117, 344, '84729010', 1, 90.00, 90.00, 18.00, 16.20, 100.00, 90.00, NULL, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `order_statuses`
--

CREATE TABLE `order_statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(20) DEFAULT '#007bff',
  `is_system` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_statuses`
--

INSERT INTO `order_statuses` (`id`, `name`, `description`, `color`, `is_system`, `sort_order`, `created_at`) VALUES
(1, 'Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', 1, 1, '2025-06-28 09:21:58'),
(2, 'Processing', 'Order is being processed and prepared for shipping', '#17a2b8', 1, 2, '2025-06-28 09:21:58'),
(3, 'Shipped', 'Order has been shipped from our warehouse', '#28a745', 1, 3, '2025-06-28 09:21:58'),
(4, 'In Transit', 'Order is in transit to delivery location', '#6f42c1', 1, 4, '2025-06-28 09:21:58'),
(5, 'Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', 1, 5, '2025-06-28 09:21:58'),
(6, 'Delivered', 'Order has been successfully delivered', '#20c997', 1, 6, '2025-06-28 09:21:58'),
(7, 'Canceled', 'Order has been canceled', '#dc3545', 1, 7, '2025-06-28 09:21:58'),
(8, 'Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', 1, 1, '2025-06-28 09:23:39'),
(9, 'Processing', 'Order is being processed and prepared for shipping', '#17a2b8', 1, 2, '2025-06-28 09:23:39'),
(10, 'Shipped', 'Order has been shipped from our warehouse', '#28a745', 1, 3, '2025-06-28 09:23:39'),
(11, 'In Transit', 'Order is in transit to delivery location', '#6f42c1', 1, 4, '2025-06-28 09:23:39'),
(12, 'Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', 1, 5, '2025-06-28 09:23:40'),
(13, 'Delivered', 'Order has been successfully delivered', '#20c997', 1, 6, '2025-06-28 09:23:40'),
(14, 'Canceled', 'Order has been canceled', '#dc3545', 1, 7, '2025-06-28 09:23:40'),
(15, 'Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', 1, 1, '2025-06-28 12:39:57'),
(16, 'Processing', 'Order is being processed and prepared for shipping', '#17a2b8', 1, 2, '2025-06-28 12:39:57'),
(17, 'Shipped', 'Order has been shipped from our warehouse', '#28a745', 1, 3, '2025-06-28 12:39:57'),
(18, 'In Transit', 'Order is in transit to delivery location', '#6f42c1', 1, 4, '2025-06-28 12:39:57'),
(19, 'Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', 1, 5, '2025-06-28 12:39:57'),
(20, 'Delivered', 'Order has been successfully delivered', '#20c997', 1, 6, '2025-06-28 12:39:57'),
(21, 'Canceled', 'Order has been canceled', '#dc3545', 1, 7, '2025-06-28 12:39:57'),
(22, 'Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', 1, 1, '2025-06-28 12:42:05'),
(23, 'Processing', 'Order is being processed and prepared for shipping', '#17a2b8', 1, 2, '2025-06-28 12:42:05'),
(24, 'Shipped', 'Order has been shipped from our warehouse', '#28a745', 1, 3, '2025-06-28 12:42:05'),
(25, 'In Transit', 'Order is in transit to delivery location', '#6f42c1', 1, 4, '2025-06-28 12:42:05'),
(26, 'Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', 1, 5, '2025-06-28 12:42:05'),
(27, 'Delivered', 'Order has been successfully delivered', '#20c997', 1, 6, '2025-06-28 12:42:05'),
(28, 'Canceled', 'Order has been canceled', '#dc3545', 1, 7, '2025-06-28 12:42:05'),
(29, 'Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', 1, 1, '2025-06-28 12:42:19'),
(30, 'Processing', 'Order is being processed and prepared for shipping', '#17a2b8', 1, 2, '2025-06-28 12:42:19'),
(31, 'Shipped', 'Order has been shipped from our warehouse', '#28a745', 1, 3, '2025-06-28 12:42:19'),
(32, 'In Transit', 'Order is in transit to delivery location', '#6f42c1', 1, 4, '2025-06-28 12:42:19'),
(33, 'Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', 1, 5, '2025-06-28 12:42:19'),
(34, 'Delivered', 'Order has been successfully delivered', '#20c997', 1, 6, '2025-06-28 12:42:19'),
(35, 'Canceled', 'Order has been canceled', '#dc3545', 1, 7, '2025-06-28 12:42:19'),
(36, 'Pending', 'Order has been placed and is awaiting confirmation', '#ffc107', 1, 1, '2025-06-28 12:47:20'),
(37, 'Processing', 'Order is being processed and prepared for shipping', '#17a2b8', 1, 2, '2025-06-28 12:47:20'),
(38, 'Shipped', 'Order has been shipped from our warehouse', '#28a745', 1, 3, '2025-06-28 12:47:20'),
(39, 'In Transit', 'Order is in transit to delivery location', '#6f42c1', 1, 4, '2025-06-28 12:47:20'),
(40, 'Out for Delivery', 'Order is out for delivery to your address', '#fd7e14', 1, 5, '2025-06-28 12:47:20'),
(41, 'Delivered', 'Order has been successfully delivered', '#20c997', 1, 6, '2025-06-28 12:47:20'),
(42, 'Canceled', 'Order has been canceled', '#dc3545', 1, 7, '2025-06-28 12:47:20'),
(43, 'Packed', 'Order has been packed and is ready for shipping', '#20c997', 1, 3, '2026-06-15 07:01:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_status_id` int(11) NOT NULL,
  `status_description` text DEFAULT NULL,
  `updated_by` enum('admin','system','user') DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_history`
--

INSERT INTO `order_status_history` (`id`, `order_id`, `order_status_id`, `status_description`, `updated_by`, `created_at`) VALUES
(11, 12, 1, 'Order placed successfully', 'system', '2025-07-08 08:56:06'),
(12, 13, 1, 'Order placed successfully', 'system', '2025-07-08 08:57:10'),
(13, 14, 1, 'Order placed successfully', 'system', '2025-07-08 09:07:20'),
(14, 15, 1, 'Order placed successfully', 'system', '2025-07-08 09:19:04'),
(15, 16, 1, 'Order placed successfully', 'system', '2025-07-08 09:21:23'),
(16, 16, 1, '', 'admin', '2025-07-08 09:28:47'),
(17, 16, 1, '', 'admin', '2025-07-08 09:29:19'),
(18, 17, 1, 'Order placed successfully', 'system', '2025-07-08 10:33:09'),
(19, 18, 1, 'Order placed successfully', 'system', '2025-07-08 10:34:08'),
(20, 16, 1, '', 'admin', '2025-07-08 11:04:56'),
(21, 19, 1, 'Order placed successfully', 'system', '2025-07-09 10:47:33'),
(22, 20, 1, 'Order placed successfully', 'system', '2025-07-09 13:55:43'),
(23, 21, 1, 'Order placed successfully', 'system', '2025-07-09 13:58:23'),
(24, 22, 1, 'Order placed successfully', 'system', '2025-07-09 14:04:45'),
(25, 23, 1, 'Order placed successfully', 'system', '2025-07-09 14:09:30'),
(26, 24, 1, 'Order placed successfully', 'system', '2025-07-09 14:16:11'),
(27, 28, 1, 'Order placed successfully', 'system', '2025-07-09 14:23:23'),
(28, 31, 1, 'Order placed successfully', 'system', '2025-07-09 14:26:57'),
(29, 32, 1, 'Order placed successfully', 'system', '2025-07-09 14:29:10'),
(30, 32, 2, 'your order is in processing status', 'admin', '2025-07-09 14:36:17'),
(31, 32, 3, 'Order shipped from Delhi', 'admin', '2025-07-09 14:36:58'),
(32, 32, 4, 'Order is in Transit', 'admin', '2025-07-09 14:37:57'),
(33, 32, 5, 'Out for Delivery', 'admin', '2025-07-09 14:38:31'),
(34, 32, 13, 'Order Delivered successfully', 'admin', '2025-07-09 14:39:23'),
(35, 32, 34, '', 'admin', '2025-07-09 14:42:41'),
(36, 33, 1, 'Order placed successfully', 'system', '2025-07-09 15:03:42'),
(37, 34, 1, 'Order placed successfully', 'system', '2025-07-09 15:19:36'),
(38, 35, 1, 'Order placed successfully', 'system', '2025-07-09 16:27:35'),
(39, 36, 1, 'Order placed successfully', 'system', '2025-07-09 16:30:58'),
(40, 37, 1, 'Order placed successfully', 'system', '2025-07-09 16:31:31'),
(41, 38, 1, 'Order placed successfully', 'system', '2025-07-09 16:32:29'),
(42, 39, 1, 'Order placed successfully', 'system', '2025-07-09 18:39:52'),
(43, 40, 1, 'Order placed successfully', 'system', '2025-07-09 18:52:11'),
(44, 41, 1, 'Order placed successfully', 'system', '2025-07-09 19:15:29'),
(45, 42, 1, 'Order placed successfully', 'system', '2025-07-09 19:51:21'),
(46, 43, 1, 'Order placed successfully', 'system', '2025-07-10 05:11:04'),
(47, 44, 1, 'Order placed successfully', 'system', '2025-07-13 06:40:38'),
(48, 45, 1, 'Order placed successfully', 'system', '2025-07-16 13:38:58'),
(49, 46, 1, 'Order placed successfully', 'system', '2025-07-22 17:57:28'),
(50, 47, 1, 'Order placed successfully', 'system', '2025-07-26 14:22:13'),
(51, 48, 1, 'Order placed successfully', 'system', '2025-07-29 15:39:03'),
(52, 49, 1, 'Order placed successfully', 'system', '2025-07-29 15:49:02'),
(53, 50, 1, 'Order placed successfully', 'system', '2025-07-29 17:54:09'),
(54, 51, 1, 'Order placed successfully', 'system', '2025-08-17 10:19:58'),
(55, 52, 1, 'Order placed successfully', 'system', '2025-08-27 15:45:18'),
(56, 48, 1, '', 'admin', '2025-09-14 11:17:12'),
(57, 52, 3, '', 'admin', '2025-09-14 13:46:01'),
(58, 52, 1, '', 'admin', '2025-09-14 13:47:30'),
(59, 52, 3, '', 'admin', '2025-09-14 13:58:11'),
(60, 51, 1, '', 'admin', '2025-09-14 16:14:05'),
(61, 51, 1, '', 'admin', '2025-09-14 16:23:07'),
(62, 52, 1, '', 'admin', '2025-09-14 16:42:54'),
(63, 51, 1, '', 'admin', '2025-09-14 16:43:23'),
(64, 51, 3, '', 'admin', '2025-09-14 16:44:51'),
(65, 53, 1, 'Order placed successfully', 'system', '2025-09-14 16:47:57'),
(66, 53, 10, '', 'admin', '2025-09-14 16:48:47'),
(67, 53, 1, '', 'admin', '2025-09-14 16:51:07'),
(68, 53, 3, '', 'admin', '2025-09-14 16:51:15'),
(69, 53, 1, '', 'admin', '2025-09-14 16:52:33'),
(70, 54, 1, 'Order placed successfully', 'system', '2025-09-22 06:04:22'),
(71, 55, 1, 'Order placed successfully', 'system', '2025-09-22 06:09:24'),
(72, 56, 1, 'Order placed successfully', 'system', '2025-09-23 15:13:18'),
(73, 57, 1, 'Order placed successfully', 'system', '2025-09-27 06:03:00'),
(74, 58, 1, 'Order placed successfully', 'system', '2025-09-27 06:04:10'),
(75, 59, 1, 'Order placed successfully', 'system', '2025-09-27 06:44:00'),
(76, 60, 1, 'Order placed successfully', 'system', '2025-09-27 06:53:23'),
(77, 61, 1, 'Order placed successfully', 'system', '2025-12-22 05:29:14'),
(78, 62, 1, 'Order placed successfully', 'system', '2026-01-31 06:43:06'),
(79, 63, 1, 'Order placed successfully', 'system', '2026-01-31 06:47:49'),
(80, 63, 24, '', 'admin', '2026-02-07 14:56:40'),
(81, 63, 31, '', 'admin', '2026-02-07 14:59:24'),
(82, 60, 38, '', 'admin', '2026-02-07 14:59:58'),
(83, 60, 3, '', 'admin', '2026-02-07 15:22:25'),
(84, 64, 1, 'Order placed successfully', 'system', '2026-02-07 15:43:08'),
(85, 65, 1, 'Order placed successfully', 'system', '2026-02-07 15:43:32'),
(86, 66, 1, 'Order placed successfully', 'system', '2026-02-11 09:21:53'),
(87, 67, 1, 'Order placed successfully', 'system', '2026-02-13 10:01:48'),
(88, 68, 1, 'Order placed successfully', 'system', '2026-02-17 10:10:31'),
(89, 69, 1, 'Order placed successfully', 'system', '2026-02-17 14:17:34'),
(90, 70, 1, 'Order placed successfully', 'system', '2026-02-18 05:54:28'),
(91, 71, 1, 'Order placed successfully', 'system', '2026-02-19 12:00:57'),
(92, 72, 1, 'Order placed successfully', 'system', '2026-02-22 19:44:28'),
(93, 73, 1, 'Order placed successfully', 'system', '2026-02-22 19:48:33'),
(94, 74, 1, 'Order placed successfully', 'system', '2026-02-22 19:51:03'),
(95, 75, 1, 'Order placed successfully', 'system', '2026-02-22 19:53:02'),
(96, 76, 1, 'Order placed successfully', 'system', '2026-02-23 10:57:35'),
(97, 77, 1, 'Order placed successfully', 'system', '2026-02-27 21:14:30'),
(98, 78, 1, 'Order placed successfully', 'system', '2026-02-27 21:20:33'),
(99, 79, 1, 'Order placed successfully', 'system', '2026-03-05 07:18:34'),
(100, 80, 1, 'Order placed successfully', 'system', '2026-03-14 07:52:19'),
(101, 81, 1, 'Order placed successfully', 'system', '2026-03-14 09:24:15'),
(102, 82, 1, 'Order placed successfully', 'system', '2026-03-14 09:52:21'),
(103, 83, 1, 'Order placed successfully', 'system', '2026-03-24 08:30:16'),
(104, 84, 1, 'Order placed successfully', 'system', '2026-03-24 08:32:26'),
(105, 85, 1, 'Order placed successfully', 'system', '2026-03-25 06:35:09'),
(106, 86, 1, 'Order placed successfully', 'system', '2026-03-25 10:53:53'),
(107, 87, 1, 'Order placed successfully', 'system', '2026-03-25 10:54:48'),
(108, 88, 1, 'Order placed successfully', 'system', '2026-03-25 10:55:31'),
(109, 89, 1, 'Order placed successfully', 'system', '2026-03-25 10:57:50'),
(110, 90, 1, 'Order placed successfully', 'system', '2026-03-25 10:59:53'),
(111, 91, 1, 'Order placed successfully', 'system', '2026-03-25 13:26:30'),
(112, 92, 1, 'Order placed successfully', 'system', '2026-03-25 14:02:12'),
(113, 81, 1, '', 'admin', '2026-03-26 06:59:58'),
(114, 93, 1, 'Order placed successfully', 'system', '2026-03-26 08:56:10'),
(115, 94, 1, 'Order placed successfully', 'system', '2026-03-27 10:30:28'),
(116, 40, 1, '', 'admin', '2026-04-18 01:53:12'),
(117, 94, 1, '', 'admin', '2026-04-18 01:53:43'),
(118, 94, 1, '', 'admin', '2026-04-18 01:54:54'),
(119, 95, 1, 'Order placed successfully', 'system', '2026-06-10 11:53:17'),
(120, 96, 1, 'Order placed successfully', 'system', '2026-06-10 11:54:48'),
(121, 97, 1, 'Order placed successfully', 'system', '2026-06-11 11:58:36'),
(122, 98, 1, 'Order placed successfully', 'system', '2026-06-12 07:12:37'),
(123, 99, 1, 'Order placed successfully', 'system', '2026-06-12 07:24:09'),
(124, 100, 1, 'Order placed successfully', 'system', '2026-06-12 08:39:17'),
(125, 101, 1, 'Order placed successfully', 'system', '2026-06-12 08:41:26'),
(126, 102, 1, 'Order placed successfully', 'system', '2026-06-12 10:47:14'),
(127, 103, 1, 'Order placed successfully', 'system', '2026-06-12 12:50:03'),
(128, 104, 1, 'Order placed successfully', 'system', '2026-06-13 08:14:50'),
(129, 104, 9, '', 'admin', '2026-06-13 08:18:28'),
(130, 104, 5, '', 'admin', '2026-06-13 08:19:16'),
(131, 104, 31, '', 'admin', '2026-06-13 08:19:44'),
(132, 105, 1, 'Order placed successfully', 'system', '2026-06-13 08:57:29'),
(133, 105, 1, '', 'admin', '2026-06-15 05:30:45'),
(134, 105, 9, '', 'admin', '2026-06-15 05:30:52'),
(135, 105, 1, '', 'admin', '2026-06-15 05:31:05'),
(136, 106, 1, 'Order placed successfully', 'system', '2026-06-15 06:13:11'),
(137, 106, 6, '', 'admin', '2026-06-15 06:25:56'),
(138, 106, 1, '', 'admin', '2026-06-15 06:27:55'),
(139, 106, 13, '', 'admin', '2026-06-15 06:31:48'),
(140, 106, 1, '', 'admin', '2026-06-15 06:32:01'),
(141, 106, 1, 'payment done', 'admin', '2026-06-15 06:32:32'),
(142, 106, 6, '', 'admin', '2026-06-15 06:33:11'),
(143, 106, 27, 'payment done', 'admin', '2026-06-15 06:33:45'),
(144, 106, 2, 'payment done', 'admin', '2026-06-15 07:01:52'),
(145, 106, 3, 'payment done', 'admin', '2026-06-15 07:02:14'),
(146, 107, 1, 'Order placed successfully', 'system', '2026-06-15 10:14:40'),
(147, 107, 6, '', 'admin', '2026-06-15 11:02:24'),
(148, 108, 1, 'Order placed successfully', 'system', '2026-06-15 12:59:00'),
(149, 109, 1, 'Order placed successfully', 'system', '2026-06-15 13:03:11'),
(150, 110, 1, 'Order placed successfully', 'system', '2026-06-19 09:57:20'),
(151, 111, 1, 'Order placed successfully', 'system', '2026-06-19 11:08:42'),
(152, 111, 2, '', 'admin', '2026-06-19 11:22:21'),
(153, 111, 5, '', 'admin', '2026-06-19 11:23:55'),
(154, 111, 6, '', 'admin', '2026-06-19 11:24:12'),
(155, 112, 1, 'Order placed successfully', 'system', '2026-06-23 05:58:16'),
(156, 112, 5, '', 'admin', '2026-06-23 05:59:53'),
(157, 113, 1, 'Order placed successfully', 'system', '2026-09-30 04:23:32'),
(158, 114, 1, 'Order placed successfully', 'system', '2026-10-02 09:16:49'),
(159, 115, 1, 'Order placed successfully', 'system', '2026-10-05 05:04:18'),
(160, 116, 1, 'Order placed successfully', 'system', '2026-10-10 04:29:36'),
(161, 117, 1, 'Order placed successfully', 'system', '2026-10-19 11:43:59');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `name`, `description`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'view_dashboard', 'View Dashboard', 'Can view admin dashboard', 'Dashboard', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(2, 'view_products', 'View Products', 'Can view products list', 'Products', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(3, 'add_product', 'Add Product', 'Can add new products', 'Products', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(4, 'edit_product', 'Edit Product', 'Can edit existing products', 'Products', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(5, 'delete_product', 'Delete Product', 'Can delete products', 'Products', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(6, 'manage_product_approval', 'Manage Product Approval', 'Can approve/reject seller products', 'Products', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(7, 'view_categories', 'View Categories', 'Can view categories', 'Categories', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(8, 'add_category', 'Add Category', 'Can add new categories', 'Categories', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(9, 'edit_category', 'Edit Category', 'Can edit categories', 'Categories', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(10, 'delete_category', 'Delete Category', 'Can delete categories', 'Categories', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(11, 'view_orders', 'View Orders', 'Can view orders', 'Orders', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(12, 'edit_order', 'Edit Order', 'Can edit orders', 'Orders', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(13, 'delete_order', 'Delete Order', 'Can delete orders', 'Orders', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(14, 'view_users', 'View Users', 'Can view users list', 'Users', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(15, 'add_user', 'Add User', 'Can add new users', 'Users', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(16, 'edit_user', 'Edit User', 'Can edit user details', 'Users', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(17, 'delete_user', 'Delete User', 'Can delete users', 'Users', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(18, 'view_sellers', 'View Sellers', 'Can view sellers', 'Sellers', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(19, 'add_seller', 'Add Seller', 'Can add new sellers', 'Sellers', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(20, 'edit_seller', 'Edit Seller', 'Can edit seller details', 'Sellers', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(21, 'delete_seller', 'Delete Seller', 'Can delete sellers', 'Sellers', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(22, 'manage_seller_products', 'Manage Seller Products', 'Can manage seller products', 'Sellers', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(23, 'view_shipping', 'View Shipping', 'Can view shipping settings', 'Shipping', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(24, 'edit_shipping', 'Edit Shipping', 'Can edit shipping settings', 'Shipping', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(25, 'manage_pincodes', 'Manage Pincodes', 'Can manage pincodes', 'Shipping', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(26, 'view_reports', 'View Reports', 'Can view reports', 'Reports', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(27, 'export_reports', 'Export Reports', 'Can export reports', 'Reports', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(28, 'manage_admins', 'Manage Admin Users', 'Can add/edit/delete admin users', 'Admin', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(29, 'manage_roles', 'Manage Roles', 'Can create and manage roles', 'Admin', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(30, 'manage_permissions', 'Manage Permissions', 'Can manage permissions', 'Admin', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(31, 'view_settings', 'View Settings', 'Can view settings', 'Settings', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(32, 'edit_settings', 'Edit Settings', 'Can edit settings', 'Settings', 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(33, 'manage_manual_invoices', 'Manage Manual Invoices', 'Can create and download manual tax invoices', 'Orders', 1, '2026-06-17 08:03:45', '2026-06-17 08:03:45'),
(35, 'manage_quotations', 'Manage Quotations', 'Can create and download quotations', 'Orders', 1, '2026-06-17 08:10:35', '2026-06-17 08:10:35'),
(172, 'manage_reviews', 'Manage Reviews', 'Can view, edit, moderate, and delete product reviews', 'Reviews', 1, '2026-09-28 12:01:15', '2026-09-28 12:01:15'),
(175, 'manage_footer_pages', 'Manage Footer Pages', 'Can create, edit, publish, and delete footer pages', 'Website', 1, '2026-10-08 07:35:30', '2026-10-08 07:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `popup_settings`
--

CREATE TABLE `popup_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `popup_settings`
--

INSERT INTO `popup_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'popup_enabled', '1', '2025-08-12 09:54:08', '2025-08-12 09:54:08'),
(2, 'popup_title', 'Check Delivery Availability', '2025-08-12 09:54:08', '2025-08-12 09:54:08'),
(3, 'popup_message', 'We Deliver Orders in Vadodara Only.', '2025-08-12 09:54:08', '2026-09-26 05:41:40'),
(4, 'popup_instruction', 'Please Enter PIN Code to Check Delivery Availability.', '2025-08-12 09:54:08', '2026-09-26 05:41:40'),
(5, 'service_available_message', 'We Provide Delivery to Your Area', '2025-08-12 09:54:08', '2026-09-26 05:41:40'),
(6, 'service_unavailable_message', 'We Don’t Provide Delivery to Your Area', '2025-08-12 09:54:08', '2026-09-26 05:41:40');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

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
  `pay_per_unit` decimal(10,2) DEFAULT NULL,
  `unit_label` varchar(20) NOT NULL DEFAULT 'No.',
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
  `has_variations` tinyint(1) DEFAULT 0,
  `brand_id` int(11) DEFAULT NULL,
  `seller_name` varchar(255) DEFAULT NULL,
  `seller_code` varchar(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `name`, `slug`, `sku`, `hsn`, `description`, `mrp`, `selling_price`, `pay_per_unit`, `unit_label`, `discount_percentage`, `gst_type`, `gst_rate`, `shipping_charge`, `category_id`, `main_image`, `stock_quantity`, `package_quantity`, `is_active`, `is_approved`, `approved_at`, `approved_by`, `rejection_reason`, `is_featured`, `is_discounted`, `created_at`, `max_quantity_per_order`, `sort_order`, `has_variations`, `brand_id`, `seller_name`, `seller_code`) VALUES
(41, NULL, 'Leather Leg Guard ESAB', 'leather-leg-guard-esab', 'FTP ESAB 060', '12345678', 'Leather Leg Guard ESAB', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/68a6d817a36f9.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2025-08-01 10:01:40', 10, 0, 0, NULL, NULL, NULL),
(77, NULL, 'Welding Hood Inprotech', 'welding-hood-inprotech', 'HED WEHD 061', '12345678', 'Welding Hood Inprotech', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b4fc58df156.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:12:40', 10, 0, 0, NULL, NULL, NULL),
(78, NULL, 'Plastic Disposable Cap Generic IP 001', 'plastic-disposable-cap-generic-ip-001', 'HED PDCP 060', '12345678', 'Plastic Disposable Cap Generic IP 001', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b4fec35eb89.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:22:59', 10, 0, 0, NULL, NULL, NULL),
(79, NULL, 'Safety Helmet Rachet Udyogi 5000 White', 'safety-helmet-rachet-udyogi-5000-white', 'HED 5000 051', '12345678', 'Safety Helmet Rachet Udyogi 5000 White', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b4ff1f74d82.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:24:31', 10, 0, 0, NULL, NULL, NULL),
(80, NULL, 'Safety Helmet Rachet Udyogi 5000 Yellow', 'safety-helmet-rachet-udyogi-5000-yellow', 'HED 5000 050', '12345678', 'Safety Helmet Rachet Udyogi 5000 Yellow', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b4ff78b351d.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:26:00', 10, 0, 0, NULL, NULL, NULL),
(81, NULL, 'Safety Helmet Nape Udyogi UI 1211 White', 'safety-helmet-nape-udyogi-ui-1211-white', 'HED 1211 041', '12345678', 'Safety Helmet Nape Udyogi UI 1211 White', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b4ffdb32f67.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:27:39', 10, 0, 0, NULL, NULL, NULL),
(83, NULL, 'Safety Helmet Nape Udyogi UI 1211 Yellow', 'safety-helmet-nape-udyogi-ui-1211-yellow', 'HED 1211 040', '12345678', 'Safety Helmet Nape Udyogi UI 1211 Yellow', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b5009f3a311.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:30:55', 10, 0, 0, NULL, NULL, NULL),
(84, NULL, 'Safety Helmet Rachet Karam PN 521 Blue', 'safety-helmet-rachet-karam-pn-521-blue', 'HED 0521 033', '12345678', 'Safety Helmet Rachet Karam PN 521 Blue', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b500ecc45c9.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:32:12', 10, 0, 0, NULL, NULL, NULL),
(85, NULL, 'Safety Helmet Rachet Karam PN 521 Grey', 'safety-helmet-rachet-karam-pn-521-grey', 'HED 0521 032', '12345678', 'Safety Helmet Rachet Karam PN 521 Grey', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b50128788cb.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:33:12', 10, 0, 0, NULL, NULL, NULL),
(86, NULL, 'Safety Helmet Rachet Karam PN 521 White', 'safety-helmet-rachet-karam-pn-521-white', 'HED 0521 031', '12345678', 'Safety Helmet Rachet Karam PN 521 White', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b5016203f24.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:34:10', 10, 0, 0, NULL, NULL, NULL),
(87, NULL, 'Safety Helmet Rachet Karam PN 521 Yellow', 'safety-helmet-rachet-karam-pn-521-yellow', 'HED 0521 030', '12345678', 'Safety Helmet Rachet Karam PN 521 Yellow', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b502574684d.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:38:15', 10, 0, 0, NULL, NULL, NULL),
(88, NULL, 'Safety Helmet Nape Karam PN 501 Blue', 'safety-helmet-nape-karam-pn-501-blue', 'HED 0501 023', '12345678', 'Safety Helmet Nape Karam PN 501 Blue', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b5031ab099c.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:41:30', 10, 0, 0, NULL, NULL, NULL),
(89, NULL, 'Safety Helmet Nape Karam PN 501 Grey', 'safety-helmet-nape-karam-pn-501-grey', 'HED 0501 022', '12345678', 'Safety Helmet Nape Karam PN 501 Grey', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b5035ad87dc.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:42:34', 10, 0, 0, NULL, NULL, NULL),
(90, NULL, 'Safety Helmet Nape Karam PN 501 White', 'safety-helmet-nape-karam-pn-501-white', 'HED 0501 021', '12345678', 'Safety Helmet Nape Karam PN 501 White', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b503aece0ba.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:43:58', 10, 0, 0, NULL, NULL, NULL),
(91, NULL, 'Safety Helmet Nape Karam PN 501 Yellow', 'safety-helmet-nape-karam-pn-501-yellow', 'HED 0501 020', '12345678', 'Safety Helmet Nape Karam PN 501 Yellow', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b503e0c7ec6.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:44:48', 10, 0, 0, NULL, NULL, NULL),
(92, NULL, 'Safety Helmet Nape ISI Generic IP 011', 'safety-helmet-nape-isi-generic-ip-011', 'HED 0501 011', '12345678', 'Safety Helmet Nape ISI Generic IP 011', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b5041360289.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:45:39', 10, 0, 0, NULL, NULL, NULL),
(93, NULL, 'Safety Helmet Loader Generic IP 010', 'safety-helmet-loader-generic-ip-010', 'HED LOAD 010', '12345678', 'Safety Helmet Loader Generic IP 010', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 18, 'uploads/products/69b504458ca6e.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 06:46:29', 10, 0, 0, NULL, NULL, NULL),
(94, NULL, 'Chemical Splash Protection 3M 1621', 'chemical-splash-protection-3m-1621', 'EYP 1621CP 054', '12345678', 'Chemical Splash Protection 3M 1621', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b522e16696a.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 08:57:05', 10, 0, 0, NULL, NULL, NULL),
(95, NULL, 'Over-The-Spec Safety Goggle 3M 1611', 'over-the-spec-safety-goggle-3m-1611', 'EYP 1611OS 053', '12345678', 'Over-The-Spec Safety Goggle 3M 1611', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b52330999a3.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 08:58:24', 10, 0, 0, NULL, NULL, NULL),
(96, NULL, 'Clear Antifog Lens Goggle 3M Virtua 11880', 'clear-antifog-lens-goggle-3m-virtua-11880', 'EYP 11880AF 052', '12345678', 'Clear Antifog Lens Goggle 3M Virtua 11880', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5238333230.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 08:59:47', 10, 0, 0, NULL, NULL, NULL),
(97, NULL, 'Hardcoat Clear Lens Goggles 3M Virtua 11850IN', 'hardcoat-clear-lens-goggles-3m-virtua-11850in', 'EYP 11850IN 051', '12345678', 'Hardcoat Clear Lens Goggles 3M Virtua 11850IN', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b523bf6f584.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:00:47', 10, 0, 0, NULL, NULL, NULL),
(98, NULL, 'Hardcoat Clear Lens Goggle 3M 1709IN', 'hardcoat-clear-lens-goggle-3m-1709in', 'EYP 1709IN 050', '12345678', 'Hardcoat Clear Lens Goggle 3M 1709IN', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b523f3acba3.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:01:39', 10, 0, 0, NULL, NULL, NULL),
(99, NULL, 'Chemical Splash Protection Goggle Udyogi UD 39', 'chemical-splash-protection-goggle-udyogi-ud-39', 'EYP UD39 043', '12345678', 'Chemical Splash Protection Goggle Udyogi UD 39', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b524225fd3c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:02:26', 10, 0, 0, NULL, NULL, NULL),
(100, NULL, 'Over-The-Spec Safety Goggle Udyogi UD 30', 'over-the-spec-safety-goggle-udyogi-ud-30', 'EYP UD30 042', '12345678', 'Over-The-Spec Safety Goggle Udyogi UD 30', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5296d10924.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:03:08', 10, 0, 0, NULL, NULL, NULL),
(101, NULL, 'Clear Lens Safety Goggle Udyogi UD 91', 'clear-lens-safety-goggle-udyogi-ud-91', 'EYP UD91 041', '12345678', 'Clear Lens Safety Goggle Udyogi UD 91', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b529f443490.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:03:51', 10, 0, 0, NULL, NULL, NULL),
(102, NULL, 'Clear Lens Safety Goggle Udyogi UD 71', 'clear-lens-safety-goggle-udyogi-ud-71', 'EYP UD70 040', '12345678', 'Clear Lens Safety Goggle Udyogi UD 71', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b529bcc0aa0.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:04:37', 10, 0, 0, NULL, NULL, NULL),
(103, NULL, 'Chemical Splash Protection Goggle Venus E 503', 'chemical-splash-protection-goggle-venus-e-503', 'EYP E503CP 034', '12345678', 'Chemical Splash Protection Goggle Venus E 503', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b524dd45c07.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:05:33', 10, 0, 0, NULL, NULL, NULL),
(104, NULL, 'Over-The-Spec Safety Goggle Venus E 603', 'over-the-spec-safety-goggle-venus-e-603', 'EYP E603OS 033', '12345678', 'Over-The-Spec Safety Goggle Venus E 603', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b525118369c.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:06:25', 10, 0, 0, NULL, NULL, NULL),
(105, NULL, 'Clear Antifog Lens Goggle Venus E 306', 'clear-antifog-lens-goggle-venus-e-306', 'EYP E306AF 032', '12345678', 'Clear Antifog Lens Goggle Venus E 306', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5253e619a0.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:07:10', 10, 0, 0, NULL, NULL, NULL),
(106, NULL, 'Clear Antifog Lens Goggle Venus E 305', 'clear-antifog-lens-goggle-venus-e-305', 'EYP E305AF 031', '12345678', 'Clear Antifog Lens Goggle Venus E 305', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5256d47d2c.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:07:57', 10, 0, 0, NULL, NULL, NULL),
(107, NULL, 'Clear Lens Safety Goggle Venus E 102', 'clear-lens-safety-goggle-venus-e-102', 'EYP E102CL 030', '12345678', 'Clear Lens Safety Goggle Venus E 102', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5259e73cae.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:08:46', 10, 0, 0, NULL, NULL, NULL),
(108, NULL, 'Chemical Splash Protection Karam ES 009', 'chemical-splash-protection-karam-es-009', 'EYP ES09CP 029', '12345678', 'Chemical Splash Protection Karam ES 009', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5282a2a1f4.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:09:32', 10, 0, 0, NULL, NULL, NULL),
(109, NULL, 'Executive Clear Lens Goggle Karam ES 006', 'executive-clear-lens-goggle-karam-es-006', 'EYP ES06CL 028', '12345678', 'Executive Clear Lens Goggle Karam ES 006', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b528dae6b40.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:10:15', 10, 0, 0, NULL, NULL, NULL),
(110, NULL, 'Executive Clear Antifog Goggle Karam ES 005', 'executive-clear-antifog-goggle-karam-es-005', 'EYP ES05AF 027', '12345678', 'Executive Clear Antifog Goggle Karam ES 005', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5262d1656a.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:11:09', 10, 0, 0, NULL, NULL, NULL),
(111, NULL, 'Executive Smoked Lens Goggle Karam ES 005', 'executive-smoked-lens-goggle-karam-es-005', 'EYP ES05BK 026', '12345678', 'Executive Smoked Lens Goggle Karam ES 005', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5265831d85.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:11:52', 10, 0, 0, NULL, NULL, NULL),
(112, NULL, 'Executive Clear Lens Goggle Karam ES 005', 'executive-clear-lens-goggle-karam-es-005', 'EYP ES05CL 025', '12345678', 'Executive Clear Lens Goggle Karam ES 005', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b52684c5f83.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:12:36', 10, 0, 0, NULL, NULL, NULL),
(113, NULL, 'Electric Arc Welder\'s Goggle Karam ES 004', 'electric-arc-welder-s-goggle-karam-es-004', 'EYP ES04AW 024', '12345678', 'Electric Arc Welder\'s Goggle Karam ES 004', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5289a9c659.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:13:21', 10, 0, 0, NULL, NULL, NULL),
(114, NULL, 'Gas Welder\'s Goggle Karam ES 003', 'gas-welder-s-goggle-karam-es-003', 'EYP ES03GW 023', '12345678', 'Gas Welder\'s Goggle Karam ES 003', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5291dc7bf6.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:14:00', 10, 0, 0, NULL, NULL, NULL),
(115, NULL, 'Clear Antifog Lens Goggle Karam ES 001', 'clear-antifog-lens-goggle-karam-es-001', 'EYP ES01AF 022', '12345678', 'Clear Antifog Lens Goggle Karam ES 001', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5271b829d4.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:15:07', 10, 0, 0, NULL, NULL, NULL),
(116, NULL, 'Black Smoked Lens Goggle Karam ES 001', 'black-smoked-lens-goggle-karam-es-001', 'EYP ES01BK 021', '12345678', 'Black Smoked Lens Goggle Karam ES 001', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b527df4a4e9.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:15:43', 10, 0, 0, NULL, NULL, NULL),
(117, NULL, 'Clear Lens Safety Goggle Karam ES 001', 'clear-lens-safety-goggle-karam-es-001', 'EYP ES01CL 020', '12345678', 'Clear Lens Safety Goggle Karam ES 001', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5276586bed.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:16:21', 10, 0, 0, NULL, NULL, NULL),
(118, NULL, 'Clear Lens Safety Goggle Generic IP 001', 'clear-lens-safety-goggle-generic-ip-001', 'EYP ES01CL 020', '12345678', 'Clear Lens Safety Goggle Generic IP 001', 100.00, 90.00, 90.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 16, 'uploads/products/69b5279074690.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-14 09:17:04', 10, 0, 1, NULL, NULL, NULL),
(121, NULL, 'Ear Muffs High dB Foldable Karam EP 24', 'ear-muffs-high-db-foldable-karam-ep-24', 'EAPR EP24EM 236', '12345678', 'Ear Muffs High dB Foldable Karam EP 24', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7d990435b8.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:21:04', 10, 0, 0, NULL, NULL, NULL),
(122, NULL, 'Helmet Attachable Ear Muffs Karam EP 23', 'helmet-attachable-ear-muffs-karam-ep-23', 'EAPR EP23EM 235', '12345678', 'Helmet Attachable Ear Muffs Karam EP 23', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7d9bf28bf9.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:21:51', 10, 0, 0, NULL, NULL, NULL),
(123, NULL, 'Ear Muffs Foldable Deluxe Karam EP 22', 'ear-muffs-foldable-deluxe-karam-ep-22', 'EAPR EP22EM 234', '12345678', 'Ear Muffs Foldable Deluxe Karam EP 22', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7d9ea42311.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:22:34', 10, 0, 0, NULL, NULL, NULL),
(124, NULL, 'Ear Muffs Classic Karam EP 21', 'ear-muffs-classic-karam-ep-21', 'EAPR EP21EM 233', '12345678', 'Ear Muffs Classic Karam EP 21', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7da1b51fe5.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:23:23', 10, 0, 0, NULL, NULL, NULL),
(125, NULL, 'Ear Muff N-555 Venus N-555', 'ear-muff-n-555-venus-n-555', 'EAPR N555EM 232', '12345678', 'Ear Muff N-555 Venus N-555', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7da444431e.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:24:04', 10, 0, 0, NULL, NULL, NULL),
(126, NULL, 'Ear Muff N-530 Venus N-530', 'ear-muff-n-530-venus-n-530', 'EAPR N530EM 231', '12345678', 'Ear Muff N-530 Venus N-530', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7da6d9034d.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:24:45', 10, 0, 0, NULL, NULL, NULL),
(127, NULL, 'Ear Muff N-510 Venus N-510', 'ear-muff-n-510-venus-n-510', 'EAPR N510EM 230', '12345678', 'Ear Muff N-510 Venus N-510', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7da9808ff4.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:25:28', 10, 0, 0, NULL, NULL, NULL),
(128, NULL, 'PU Foam Corded Ear Plug 3M 1110', 'pu-foam-corded-ear-plug-3m-1110', 'EAPR 1110CO 223', '12345678', 'PU Foam Corded Ear Plug 3M 1110', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7dac0a16d2.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:26:08', 10, 0, 0, NULL, NULL, NULL),
(129, NULL, 'PU Foam Corded Ear Plug Honeywell Bilsom 304L', 'pu-foam-corded-ear-plug-honeywell-bilsom-304l', 'EAPR 304LCO 222', '12345678', 'PU Foam Corded Ear Plug Honeywell Bilsom 304L', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7dae7073e1.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:26:47', 10, 0, 0, NULL, NULL, NULL),
(130, NULL, 'Reusable Corded Ear Plugs Venus N-303', 'reusable-corded-ear-plugs-venus-n-303', 'EAPR N303RE 221', '12345678', 'Reusable Corded Ear Plugs Venus N-303', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7db0ab935f.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:27:22', 10, 0, 0, NULL, NULL, NULL),
(131, NULL, 'Disposable Corded Ear Plug Venus N-101', 'disposable-corded-ear-plug-venus-n-101', 'EAPR N101CO 220', '12345678', 'Disposable Corded Ear Plug Venus N-101', 100.00, 90.00, 90.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 20, 'uploads/products/69b7db32ae1d0.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:28:02', 10, 0, 0, NULL, NULL, NULL),
(132, NULL, 'Matty Mask Generic IP 012', 'matty-mask-generic-ip-012', 'REPR MASK 231', '12345678', 'Matty Mask Generic IP 012', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e1c9c2708.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:56:09', 10, 0, 0, NULL, NULL, NULL),
(133, NULL, '3 Ply Mask Generic IP 011', '3-ply-mask-generic-ip-011', 'REPR 3PLY 230', '12345678', '3 Ply Mask Generic IP 011', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e24ab51e7.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:58:18', 10, 0, 0, NULL, NULL, NULL),
(134, NULL, 'Cool Flow Exhalation Valve 3M 9504INV', 'cool-flow-exhalation-valve-3m-9504inv', 'REPR 9504 221', '12345678', 'Cool Flow Exhalation Valve 3M 9504INV', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e27b470ed.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 10:59:07', 10, 0, 0, NULL, NULL, NULL),
(135, NULL, 'Welded Two-Strap Design 3M 8210 - N95', 'welded-two-strap-design-3m-8210-n95', 'REPR 8210 220', '12345678', 'Welded Two-Strap Design 3M 8210 - N95', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e2bc29973.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:00:12', 10, 0, 0, NULL, NULL, NULL),
(136, NULL, 'Fold Flat - Large Filter Venus V-430 SLV', 'fold-flat-large-filter-venus-v-430-slv', 'REPR V430 213', '12345678', 'Fold Flat - Large Filter Venus V-430 SLV', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e2f735d69.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:01:11', 10, 0, 0, NULL, NULL, NULL),
(137, NULL, 'Fold Flat - Large Filter Venus V-420 SLV', 'fold-flat-large-filter-venus-v-420-slv', 'REPR V420 212', '12345678', 'Fold Flat - Large Filter Venus V-420 SLV', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e35be7232.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:02:51', 10, 0, 0, NULL, NULL, NULL),
(138, NULL, 'Fold Flat - Large Filter Venus V-410 SLV', 'fold-flat-large-filter-venus-v-410-slv', 'REPR V410 211', '12345678', 'Fold Flat - Large Filter Venus V-410 SLV', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e38800d75.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:03:36', 10, 0, 0, NULL, NULL, NULL),
(139, NULL, 'Dust Mask Yellow Venus V-44+ Mask', 'dust-mask-yellow-venus-v-44-mask', 'REPR VV44 210', '12345678', 'Dust Mask Yellow Venus V-44+ Mask', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 26, 'uploads/products/69b7e3ac4d642.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:04:12', 10, 0, 0, NULL, NULL, NULL),
(140, NULL, 'Face Shield Generic IP Face Shield (9X12)', 'face-shield-generic-ip-face-shield-9x12', 'FACE IP01 041', '12345678', 'Face Shield Generic IP Face Shield (9X12)', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e60eab0db.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:14:22', 10, 0, 0, NULL, NULL, NULL),
(141, NULL, 'Windsor 512 Face Shield (8X16)', 'windsor-512-face-shield-8x16', 'FACE 0512 031', '12345678', 'Windsor 512 Face Shield (8X16)', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e646a67da.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:15:18', 10, 0, 0, NULL, NULL, NULL),
(142, NULL, 'Welding Shield Udyogi 6PAV', 'welding-shield-udyogi-6pav', 'FACE 6PAV 022', '12345678', 'Welding Shield Udyogi 6PAV', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e671240df.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:16:01', 10, 0, 0, NULL, NULL, NULL),
(143, NULL, 'Face Shield Udyogi FC58', 'face-shield-udyogi-fc58', 'FACE 6PAV 021', '12345678', 'Face Shield Udyogi FC58', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e6c07a084.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:17:20', 10, 0, 0, NULL, NULL, NULL),
(144, NULL, 'Welding Shield Karam ES71', 'welding-shield-karam-es71', 'WELD ES71 012', '12345678', 'Welding Shield Karam ES71', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e777a2428.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:17:57', 10, 0, 0, NULL, NULL, NULL),
(145, NULL, 'Welding Shield Karam ES61', 'welding-shield-karam-es61', 'WELD ES61 011', '12345678', 'Welding Shield Karam ES61', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e7630b480.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:18:39', 18, 0, 0, NULL, NULL, NULL),
(146, NULL, 'Face Shield Karam ES51', 'face-shield-karam-es51', 'FACE ES51 010', '12345678', 'Face Shield Karam ES51', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 28, 'uploads/products/69b7e7bee292a.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:21:34', 10, 0, 0, NULL, NULL, NULL),
(147, NULL, 'Leather Sleeves - ESAB', 'leather-sleeves-esab', 'ARM ESAB 185', '12345678', 'Leather Sleeves - ESAB', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 29, 'uploads/products/69b7ebc3463fe.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:38:43', 10, 0, 0, NULL, NULL, NULL),
(148, NULL, 'Cotton Jeans Hand Sleeve 18 Inch', 'cotton-jeans-hand-sleeve-18-inch', 'ARM COJE 184', '12345678', 'Cotton Jeans Hand Sleeve 18 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 29, 'uploads/products/69b7ebeb47495.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:39:23', 10, 0, 0, NULL, NULL, NULL),
(149, NULL, 'Ashoka CR 4 Arm Cover and Sleeve', 'ashoka-cr-4-arm-cover-and-sleeve', 'ARM ASH4 183', '12345678', 'Ashoka CR 4 Arm Cover and Sleeve', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 29, 'uploads/products/69b7ec14be90f.jpg', 97, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:40:04', 10, 0, 0, NULL, NULL, NULL),
(150, NULL, 'Ashoka BL DR CR 3 Arm Cover and Hand Sleeve', 'ashoka-bl-dr-cr-3-arm-cover-and-hand-sleeve', 'ARM ASH3 182', '12345678', 'Ashoka BL DR CR 3 Arm Cover and Hand Sleeve', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 29, 'uploads/products/69b7ec441cdc4.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:40:52', 10, 0, 0, NULL, NULL, NULL),
(151, NULL, 'Karna CR 2 Arm Cover and Hand Sleeve', 'karna-cr-2-arm-cover-and-hand-sleeve', 'ARM KRNA 181', '12345678', 'Karna CR 2 Arm Cover and Hand Sleeve', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 29, 'uploads/products/69b7ec7303ce8.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:41:39', 10, 0, 0, NULL, NULL, NULL),
(152, NULL, 'Evergreen DL CR 1 Arm Cover and Sleeve', 'evergreen-dl-cr-1-arm-cover-and-sleeve', 'ARM EVRG 180', '12345678', 'Evergreen DL CR 1 Arm Cover and Sleeve', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 29, 'uploads/products/69b7ecabede15.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 11:42:16', 10, 0, 0, NULL, NULL, NULL),
(154, NULL, 'Cotton Apron Inprotech', 'cotton-apron-inprotech', 'WOWE APRN 205', '12345678', 'Cotton Apron Inprotech', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f1480b743.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:02:16', 10, 0, 0, NULL, NULL, NULL),
(155, NULL, 'Leather Apron ESAB', 'leather-apron-esab', 'WOWE APRN 204', '12345678', 'Leather Apron ESAB', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f1dbb3c5a.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:04:43', 10, 0, 0, NULL, NULL, NULL),
(156, NULL, 'Full Body Jeans Apron Inprotech', 'full-body-jeans-apron-inprotech', 'WOWE JEAP 203', '12345678', 'Full Body Jeans Apron Inprotech', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f20f3a331.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:05:35', 10, 0, 0, NULL, NULL, NULL),
(157, NULL, 'Para Aramid Fire Suit Two Layers Jacket & Pant', 'para-aramid-fire-suit-two-layers-jacket-pant', 'WOWE FISU 202', '12345678', 'Para Aramid Fire Suit Two Layers Jacket & Pant', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f2418b39c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:06:25', 10, 0, 0, NULL, NULL, NULL),
(158, NULL, 'Boiler Suit Cotton Cloth Colour Options Available', 'boiler-suit-cotton-cloth-colour-options-available', 'WOWE BOSU 201', '12345678', 'Boiler Suit Cotton Cloth Colour Options Available', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f2761ca2d.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:07:18', 10, 0, 0, NULL, NULL, NULL),
(159, NULL, 'ESD Cover All', 'esd-cover-all', 'WOWE ESDC 200', '12345678', 'ESD Cover All', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f2a339643.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:08:03', 10, 0, 0, NULL, NULL, NULL),
(160, NULL, '2\" Tape Velcro Safety Jacket [Green]', '2-tape-velcro-safety-jacket-green', 'WOWE GR02 193', '12345678', '2\" Tape Velcro Safety Jacket [Green]', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f2d5f10e3.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:08:53', 10, 0, 0, NULL, NULL, NULL),
(161, NULL, '2\" Tape Velcro Safety Jacket [Orange]', '2-tape-velcro-safety-jacket-orange', 'WOWE OR02 192', '12345678', '2\" Tape Velcro Safety Jacket [Orange]', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f3031e958.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:09:39', 10, 0, 0, NULL, NULL, NULL),
(162, NULL, '1\" Tape Velcro Safety Jacket [Green]', '1-tape-velcro-safety-jacket-green', 'WOWE GR01 191', '12345678', '1\" Tape Velcro Safety Jacket [Green]', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f337d82c8.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:10:31', 10, 0, 0, NULL, NULL, NULL),
(163, NULL, '1\" Tape Velcro Safety Jacket [Orange]', '1-tape-velcro-safety-jacket-orange', 'WOWE OR01 190', '12345678', '1\" Tape Velcro Safety Jacket [Orange]', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 25, 'uploads/products/69b7f37366b2c.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-16 12:11:31', 10, 0, 0, NULL, NULL, NULL),
(164, NULL, 'ESD Gloves', 'esd-gloves', 'HND ESDG 170', '123456789', 'ESD Gloves', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7653aca3c.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:54:27', 10, 0, 0, NULL, NULL, NULL),
(165, NULL, 'Plastic Disposable Hand Gloves', 'plastic-disposable-hand-gloves', 'HND PLST 161', '12345678', 'Plastic Disposable Hand Gloves', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7686b27d5.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:55:18', 10, 0, 0, NULL, NULL, NULL),
(166, NULL, 'Latex Examination Gloves', 'latex-examination-gloves', 'HND LATX 160', '12345678', 'Latex Examination Gloves', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba76c28fcc9.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:56:18', 10, 0, 0, NULL, NULL, NULL),
(167, NULL, 'Chola CR-5 Impact Resistant Gloves', 'chola-cr-5-impact-resistant-gloves', 'HND CCR5 151', '12345678', 'Chola CR-5 Impact Resistant Gloves', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba76f6c0cf2.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:57:10', 10, 0, 0, NULL, NULL, NULL),
(168, NULL, 'Shiva CR-3 Impact Resistant Gloves', 'shiva-cr-3-impact-resistant-gloves', 'HND SCR3 150', '12345678', 'Shiva CR-3 Impact Resistant Gloves', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba771ecbecb.jpeg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:57:50', 10, 0, 0, NULL, NULL, NULL),
(169, NULL, 'Industrial Unlined Rubber Gloves Top - 22 Inch', 'industrial-unlined-rubber-gloves-top-22-inch', 'HND TOPU 144', '12345678', 'Industrial Unlined Rubber Gloves Top - 22 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba775082da1.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:58:40', 10, 0, 0, NULL, NULL, NULL),
(170, NULL, 'Industrial Unlined Rubber Gloves Top - 16 Inch', 'industrial-unlined-rubber-gloves-top-16-inch', 'HND TOPU 143', '12345678', 'Industrial Unlined Rubber Gloves Top - 16 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba777b5111f.webp', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 09:59:23', 10, 0, 0, NULL, NULL, NULL),
(171, NULL, 'Industrial Unlined Rubber Gloves Senstouch - 16 Inch', 'industrial-unlined-rubber-gloves-senstouch-16-inch', 'HND SENS 142', '12345678', 'Industrial Unlined Rubber Gloves Senstouch - 16 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba77a9a6032.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:00:09', 10, 0, 0, NULL, NULL, NULL),
(172, NULL, 'Latex Industrial Glove Ferra Guard 12 Inch', 'latex-industrial-glove-ferra-guard-12-inch', 'HND FEGU 141', '12345678', 'Latex Industrial Glove Ferra Guard 12 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba77e3470a7.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:01:07', 10, 0, 0, NULL, NULL, NULL),
(173, NULL, 'Household Rubber Gloves Senstouch 12 Inch', 'household-rubber-gloves-senstouch-12-inch', 'HND SENS 140', '12345678', 'Household Rubber Gloves Senstouch 12 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba781501db1.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:01:57', 10, 0, 0, NULL, NULL, NULL),
(174, NULL, 'Household Hand Gloves Generic 12 Inch', 'household-hand-gloves-generic-12-inch', 'HND IP12 130', '12345678', 'Household Hand Gloves Generic 12 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba784118cf3.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:02:41', 10, 0, 0, NULL, NULL, NULL),
(175, NULL, 'Red Leather Premium Gloves 14 Inch', 'red-leather-premium-gloves-14-inch', 'HND WELD 124', '12345678', 'Red Leather Premium Gloves 14 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba787ca8da4.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:03:40', 10, 0, 0, NULL, NULL, NULL),
(176, NULL, 'Red Leather Industrial Gloves 14 Inch', 'red-leather-industrial-gloves-14-inch', 'HND WELD 123', '12345678', 'Red Leather Industrial Gloves 14 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba78a4a3c1e.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:04:20', 10, 0, 0, NULL, NULL, NULL),
(177, NULL, 'ESAB Welding Hand Gloves 14 Inch', 'esab-welding-hand-gloves-14-inch', 'HND WELD 122', '12345678', 'ESAB Welding Hand Gloves 14 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba78c924473.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:04:57', 10, 0, 0, NULL, NULL, NULL),
(178, NULL, 'MIG Welding Hand Gloves Chrome', 'mig-welding-hand-gloves-chrome', 'HND WELD 121', '12345678', 'MIG Welding Hand Gloves Chrome', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba78ef6d8a7.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:05:35', 10, 0, 0, NULL, NULL, NULL),
(179, NULL, 'Leather Hand Gloves 14 Inch', 'leather-hand-gloves-14-inch', 'HND LTHR 120', '12345678', 'Leather Hand Gloves 14 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba791f68a4f.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:06:23', 10, 0, 0, NULL, NULL, NULL),
(180, NULL, 'Asbestos Hand Gloves 14 inch', 'asbestos-hand-gloves-14-inch', 'HND ASBS 110', '12345678', 'Asbestos Hand Gloves 14 inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba79f59a692.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:09:57', 10, 0, 0, NULL, NULL, NULL),
(181, NULL, 'Canvas Hand Gloves 14 Inch', 'canvas-hand-gloves-14-inch', 'HND CANV 100', '12345678', 'Canvas Hand Gloves 14 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7a1caa819.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:10:36', 10, 0, 0, NULL, NULL, NULL),
(182, NULL, 'Jeans Hand Gloves', 'jeans-hand-gloves', 'HND JENS 090', '12345678', 'Jeans Hand Gloves', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7a4256f81.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:11:14', 10, 0, 0, NULL, NULL, NULL),
(183, NULL, 'Hosiery Hand Gloves Double Layer', 'hosiery-hand-gloves-double-layer', 'HND HOSR 081', '12345678', 'Hosiery Hand Gloves Double Layer', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7a6d95710.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:11:57', 10, 0, 0, NULL, NULL, NULL),
(184, NULL, 'Hosiery Hand Gloves Single Layer', 'hosiery-hand-gloves-single-layer', 'HND HOSR 080', '12345678', 'Hosiery Hand Gloves Single Layer', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7a90e6a15.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:12:32', 10, 0, 0, NULL, NULL, NULL),
(185, NULL, 'Cotton Drill Hand Gloves 14 Inch', 'cotton-drill-hand-gloves-14-inch', 'HND CODR 070', '12345678', 'Cotton Drill Hand Gloves 14 Inch', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7ac5a4fec.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:13:25', 10, 0, 0, NULL, NULL, NULL),
(186, NULL, 'Polka Dotted Hand Gloves 60 gm', 'polka-dotted-hand-gloves-60-gm', 'HND POLK60 061', '12345678', 'Polka Dotted Hand Gloves 60 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7af267aa5.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:14:10', 10, 0, 0, NULL, NULL, NULL),
(187, NULL, 'Polka Dotted Hand Gloves 50 gm', 'polka-dotted-hand-gloves-50-gm', 'HND POLK50 060', '12345678', 'Polka Dotted Hand Gloves 50 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7b20b2935.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:14:56', 10, 0, 0, NULL, NULL, NULL),
(188, NULL, 'Cotton Knitted Hand Gloves 60 gm', 'cotton-knitted-hand-gloves-60-gm', 'HND KNTD60 052', '12345678', 'Cotton Knitted Hand Gloves 60 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7b48aa6ce.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:15:36', 10, 0, 0, NULL, NULL, NULL),
(189, NULL, 'Cotton Knitted Hand Gloves 50 gm', 'cotton-knitted-hand-gloves-50-gm', 'HND KNTD50 051', '12345678', 'Cotton Knitted Hand Gloves 50 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7b6e3e7ab.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:16:14', 10, 0, 0, NULL, NULL, NULL),
(190, NULL, 'Cotton Knitted Hand Gloves 40 gm', 'cotton-knitted-hand-gloves-40-gm', 'HND KNTD40 050', '12345678', 'Cotton Knitted Hand Gloves 40 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7b9105fe8.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:16:49', 10, 0, 0, NULL, NULL, NULL),
(191, NULL, 'PU Coated Hand Gloves Udyogi Cut Level 5 HPU-5', 'pu-coated-hand-gloves-udyogi-cut-level-5-hpu-5', 'HND UDHPU5 043', '12345678', 'PU Coated Hand Gloves Udyogi Cut Level 5 HPU-5', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7bbf84997.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:17:35', 10, 0, 0, NULL, NULL, NULL),
(192, NULL, 'PU Coated Hand Gloves Udyogi Cut Level 3 HPU-3', 'pu-coated-hand-gloves-udyogi-cut-level-3-hpu-3', 'HND UDHPU3 042', '12345678', 'PU Coated Hand Gloves Udyogi Cut Level 3 HPU-3', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7be34c6ba.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:18:11', 10, 0, 0, NULL, NULL, NULL),
(193, NULL, 'PU Coated Hand Gloves Udyogi NPU', 'pu-coated-hand-gloves-udyogi-npu', 'HND UDNPU 041', '12345678', 'PU Coated Hand Gloves Udyogi NPU', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7c142b01f.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:19:00', 10, 0, 0, NULL, NULL, NULL),
(194, NULL, 'Nitrile Coated Hand Gloves Udyogi NNC-1310', 'nitrile-coated-hand-gloves-udyogi-nnc-1310', 'HND UD1310 040', '12345678', 'Nitrile Coated Hand Gloves Udyogi NNC-1310', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7c38f1c2e.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:19:36', 10, 0, 0, NULL, NULL, NULL),
(195, NULL, 'Nitrile Coated Hand Gloves Karam HS32', 'nitrile-coated-hand-gloves-karam-hs32', 'HND KAHS32 030', '12345678', 'Nitrile Coated Hand Gloves Karam HS32', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7c6381ed7.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:20:19', 10, 0, 0, NULL, NULL, NULL),
(196, NULL, 'Nitrile Coated Hand Gloves Rifa N101A', 'nitrile-coated-hand-gloves-rifa-n101a', 'HND RIN101 020', '12345678', 'Nitrile Coated Hand Gloves Rifa N101A', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7c837f43a.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:20:51', 10, 0, 0, NULL, NULL, NULL),
(197, NULL, 'Latex Coated Hand Gloves Yellow Orange', 'latex-coated-hand-gloves-yellow-orange', 'HND LATXYO 015', '12345678', 'Latex Coated Hand Gloves Yellow Orange', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7cc6d8767.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:21:58', 10, 0, 0, NULL, NULL, NULL),
(198, NULL, 'PU Coated Hand Gloves Black On Black', 'pu-coated-hand-gloves-black-on-black', 'HND PUCOBB 014', '12345678', 'PU Coated Hand Gloves Black On Black', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7cf9b92fe.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:22:49', 10, 0, 0, NULL, NULL, NULL),
(199, NULL, 'Nitrile Coated Hand Gloves Black On Black', 'nitrile-coated-hand-gloves-black-on-black', 'HND NTRLBB 013', '12345678', 'Nitrile Coated Hand Gloves Black On Black', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7d28bace1.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:23:36', 10, 0, 0, NULL, NULL, NULL),
(200, NULL, 'Nitrile Coated Hand Gloves Red & Black', 'nitrile-coated-hand-gloves-red-black', 'HND NTRLRB 012', '12345678', 'Nitrile Coated Hand Gloves Red & Black', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7d63b714d.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:24:35', 10, 0, 0, NULL, NULL, NULL),
(201, NULL, 'Nitrile Coated Hand Gloves 40 gm', 'nitrile-coated-hand-gloves-40-gm', 'HND NTRL40 011', '12345678', 'Nitrile Coated Hand Gloves 40 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7d95316e6.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:25:25', 10, 0, 0, NULL, NULL, NULL),
(202, NULL, 'Nitrile Coated Hand Gloves 32 gm', 'nitrile-coated-hand-gloves-32-gm', 'HND NTRL32 010', '12345678', 'Nitrile Coated Hand Gloves 32 gm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 30, 'uploads/products/69ba7db7f1a44.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 10:25:59', 10, 0, 0, NULL, NULL, NULL),
(203, NULL, 'ESD Slipper Generic IP 050', 'esd-slipper-generic-ip-050', 'FTP ESDS 050', '12345678', 'ESD Slipper Generic IP 050', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8a3b13888.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:19:23', 10, 0, 0, NULL, NULL, NULL),
(204, NULL, 'Plastic Disposable Shoe Cover Generic IP 040', 'plastic-disposable-shoe-cover-generic-ip-040', 'FTP COVR 040', '12345678', 'Plastic Disposable Shoe Cover Generic IP 040', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8a6fe1af6.png', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:20:15', 10, 0, 0, NULL, NULL, NULL),
(205, NULL, 'Cloud 14” 2C', 'cloud-14-2c', 'FTP CLUD 030', '12345678', 'Cloud 14” 2C', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8a9c17683.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:21:00', 10, 0, 0, NULL, NULL, NULL),
(206, NULL, 'Rainfall 12” 2C', 'rainfall-12-2c', 'FTP RAIN 020', '12345678', 'Rainfall 12” 2C', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8ac17d0f9.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:21:37', 10, 0, 0, NULL, NULL, NULL),
(207, NULL, 'Chota Ghoda 11”', 'chota-ghoda-11', 'FTP CHGH 011', '12345678', 'Chota Ghoda 11”', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8aeea3c08.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:22:22', 10, 0, 0, NULL, NULL, NULL),
(208, NULL, 'Busy Gum Boot 11”', 'busy-gum-boot-11', 'FTP BUSY 010', '12345678', 'Busy Gum Boot 11”', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8b1bd2900.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:23:07', 10, 0, 0, NULL, NULL, NULL),
(209, NULL, 'Ladies Safety Shoes PU Miss Double Density', 'ladies-safety-shoes-pu-miss-double-density', 'FTP LADY 031', '12345678', 'Ladies Safety Shoes PU Miss Double Density', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8b5db2bf4.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:24:13', 10, 0, 0, NULL, NULL, NULL),
(210, NULL, 'Ladies Safety Shoes PU Miss Single Density', 'ladies-safety-shoes-pu-miss-single-density', 'FTP LADY 030', '12345678', 'Ladies Safety Shoes PU Miss Single Density', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8bb6c477b.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:24:51', 10, 0, 0, NULL, NULL, NULL),
(211, NULL, 'PVC Safety Shoes Model - Power Plus', 'pvc-safety-shoes-model-power-plus', 'FTP LADY 021', '12345678', 'PVC Safety Shoes Model - Power Plus', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8be19fc55.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:26:25', 10, 0, 0, NULL, NULL, NULL),
(212, NULL, 'Executive Safety Shoes PVC Model - Bestgo', 'executive-safety-shoes-pvc-model-bestgo', 'FTP BEST 020', '12345678', 'Executive Safety Shoes PVC Model - Bestgo', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8c08c1db2.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:27:04', 10, 0, 0, NULL, NULL, NULL),
(213, NULL, 'Labour Safety Shoes PVC Model - Décor Safe', 'labour-safety-shoes-pvc-model-d-cor-safe', 'FTP SAFE 011', '12345678', 'Labour Safety Shoes PVC Model - Décor Safe', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 17, 'uploads/products/69ba8c38cc70c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:27:52', 10, 0, 0, NULL, NULL, NULL),
(215, NULL, 'Udyogi Safety Belt Eco 04', 'udyogi-safety-belt-eco-04', 'SBLT EC04 245', '12345678', 'Udyogi Safety Belt Eco 04', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 19, 'uploads/products/69ba932c90d90.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:57:32', 10, 0, 0, NULL, NULL, NULL),
(216, NULL, 'HB-1 Safety Belt', 'hb-1-safety-belt', 'SBLT HB01 244', '12345678', 'HB-1 Safety Belt', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 19, 'uploads/products/69ba934d50eb6.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:58:05', 10, 0, 0, NULL, NULL, NULL),
(217, NULL, 'E-02 with Shock Absorber Safety Belt', 'e-02-with-shock-absorber-safety-belt', 'SBLT E2SA243', '12345678', 'E-02 with Shock Absorber Safety Belt', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 19, 'uploads/products/69ba937d1e572.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:58:53', 10, 0, 0, NULL, NULL, NULL),
(218, NULL, 'E-01 with Shock Absorber Safety Belt', 'e-01-with-shock-absorber-safety-belt', 'SBLT E1SA 242', '12345678', 'E-01 with Shock Absorber Safety Belt', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 19, 'uploads/products/69ba93a6d59d5.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 11:59:34', 10, 0, 0, NULL, NULL, NULL),
(219, NULL, 'E-02 Safety Belt', 'e-02-safety-belt', 'SBLT E002 241', '12345678', 'E-02 Safety Belt', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 19, 'uploads/products/69ba93c4c186c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:00:04', 10, 0, 0, NULL, NULL, NULL),
(220, NULL, 'E-01 Safety Belt', 'e-01-safety-belt', 'SBLT E001 240', '12345678', 'E-01 Safety Belt', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 19, 'uploads/products/69ba93e20a578.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:00:34', 10, 0, 0, NULL, NULL, NULL),
(221, NULL, 'Co2 4.5 kg', 'co2-4-5-kg', 'FIEX CO45 261', '12345678', 'Co2 4.5 kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba975fd1992.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:15:27', 10, 0, 0, NULL, NULL, NULL),
(222, NULL, 'Co2 2kg', 'co2-2kg', 'FIEX CO22 260', '12345678', 'Co2 2kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba9780d673a.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:16:00', 10, 0, 0, NULL, NULL, NULL),
(223, NULL, 'Fire Extinguisher - ABC - 9 kg', 'fire-extinguisher-abc-9-kg', 'FIEX ABC9 254', '12345678', 'Fire Extinguisher - ABC - 9 kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba97b06eabc.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:16:48', 10, 0, 0, NULL, NULL, NULL),
(224, NULL, 'Fire Extinguisher - ABC - 6 kg', 'fire-extinguisher-abc-6-kg', 'FIEX ABC6 253', '12345678', 'Fire Extinguisher - ABC - 6 kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba97d53e58e.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:17:25', 10, 0, 0, NULL, NULL, NULL),
(225, NULL, 'Fire Extinguisher - ABC - 4 kg', 'fire-extinguisher-abc-4-kg', 'FIEX ABC4 252', '12345678', 'Fire Extinguisher - ABC - 4 kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba97fc94214.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:18:04', 10, 0, 0, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `seller_id`, `name`, `slug`, `sku`, `hsn`, `description`, `mrp`, `selling_price`, `pay_per_unit`, `unit_label`, `discount_percentage`, `gst_type`, `gst_rate`, `shipping_charge`, `category_id`, `main_image`, `stock_quantity`, `package_quantity`, `is_active`, `is_approved`, `approved_at`, `approved_by`, `rejection_reason`, `is_featured`, `is_discounted`, `created_at`, `max_quantity_per_order`, `sort_order`, `has_variations`, `brand_id`, `seller_name`, `seller_code`) VALUES
(226, NULL, 'Fire Extinguisher - ABC - 2 kg', 'fire-extinguisher-abc-2-kg', 'FIEX ABC2 251', '12345678', 'Fire Extinguisher - ABC - 2 kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba9821a3d0c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:18:41', 10, 0, 0, NULL, NULL, NULL),
(227, NULL, 'Fire Extinguisher - ABC - 1 kg', 'fire-extinguisher-abc-1-kg', 'FIEX ABC1 250', '12345678', 'Fire Extinguisher - ABC - 1 kg', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 31, 'uploads/products/69ba9845dd31f.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:19:17', 10, 0, 0, NULL, NULL, NULL),
(228, NULL, 'Windsock', 'windsock', 'CONS WNDS 277', '12345678', 'Windsock', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9acf71ac3.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:30:07', 10, 0, 0, NULL, NULL, NULL),
(229, NULL, 'S Type Cable Hanger 6\", 9\"', 's-type-cable-hanger-6-9', 'CONS SCAH 276', '12345678', 'S Type Cable Hanger 6\", 9\"', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9af9290ac.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:30:49', 10, 0, 0, NULL, NULL, NULL),
(230, NULL, 'Plastic Rebar Cap 16 to 32 mm', 'plastic-rebar-cap-16-to-32-mm', 'CONS RECP 275', '12345678', 'Plastic Rebar Cap 16 to 32 mm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9b212e244.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:31:29', 10, 0, 0, NULL, NULL, NULL),
(231, NULL, 'Plastic Rebar Cap 8 to 16 mm', 'plastic-rebar-cap-8-to-16-mm', 'CONS RECP 274', '12345678', 'Plastic Rebar Cap 8 to 16 mm', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9b48ba895.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:32:08', 10, 0, 0, NULL, NULL, NULL),
(232, NULL, 'Safety Net Knotted Type Double Layer', 'safety-net-knotted-type-double-layer', 'CONS SFTN 273', '12345678', 'Safety Net Knotted Type Double Layer', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9b6fec699.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:32:47', 10, 0, 0, NULL, NULL, NULL),
(233, NULL, 'Barrication Tape', 'barrication-tape', 'CONS CATP 272', '12345678', 'Barrication Tape', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9b97d0a85.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:33:27', 10, 0, 0, NULL, NULL, NULL),
(234, NULL, 'Welding Fire Blanket 1*1 Meter', 'welding-fire-blanket-1-1-meter', 'CONS WFRB 271', '12345678', 'Welding Fire Blanket 1*1 Meter', 100.00, 90.00, NULL, 'No.', 10, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9bbd305c7.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 1, 1, '2026-03-18 12:34:05', 10, 0, 0, NULL, NULL, NULL),
(235, NULL, 'Leather Shoulder Guard', 'leather-shoulder-guard', 'CONS LSGU 270', '12345678', 'Leather Shoulder Guard', 120.00, 100.00, 90.00, 'No.', 17, 'sgst_cgst', 18.00, NULL, 32, 'uploads/products/69ba9bdfb86cf.jpg', 100, 20, 1, 1, NULL, NULL, NULL, 0, 0, '2026-03-18 12:34:39', 60, 0, 1, NULL, NULL, NULL),
(240, NULL, '5 Ply | 10 x 9 x 5 Inch | 3 kg', '5-ply-10-x-9-x-5-inch-3-kg', '123456', '48191010', '<div><b>5 Ply 10 x 9 x 5 Inch</b></div><div>Corrugated Box – Heavy Duty Packaging Box for E-commerce &amp; Industrial Shipping in India</div><div><br></div><div>Strong 5 ply 10 x 9 x 5 inch corrugated box designed for heavy-duty packaging and secure shipping. Durable, compact, and ideal for e-commerce and industrial use across India.</div><div><br></div><div><ul><li>Weight Holding Capacity: 3 kg</li><li>Empty Box Weight: 300 GM</li><li>Dimensions: 255 × 230 × 153 mm</li><li>Select Quantity (Pcs)*: 10, 20, 30</li></ul></div>', 545.00, 490.00, 49.00, 'No.', 10, 'sgst_cgst', 5.00, NULL, 34, 'uploads/products/6aa3f75d13290.jpg', 90, 10, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-20 10:48:49', 10, 1, 0, NULL, NULL, NULL),
(241, NULL, '5 Ply | 13 x 9.5 x 10 Inch | 6 kg', '5-ply-13-x-9-5-x-10-inch-6-kg', '123456', '48191010', '<div><b>5 Ply 13 x 9.5 x 10 Inch</b><br></div><div>Corrugated Box – Heavy Duty Packaging Box for E-commerce &amp; Industrial Shipping in India</div><div><br></div><div>Strong 5 ply 13 x 9.5 x 10 inch corrugated box designed for heavy-duty packaging and secure shipping. Durable, compact, and ideal for e-commerce and industrial use across India.</div><div><br></div><div><ul><li>Weight Holding Capacity: 6 kg</li><li>Empty Box Weight: 500 GM</li><li>Dimensions: 330 × 242 × 255 mm</li><li>Select Quantity (Pcs)*: 10, 20, 30</li></ul></div>', 780.00, 700.00, 70.00, 'No.', 10, 'sgst_cgst', 5.00, NULL, 34, 'uploads/products/6aa3f7cf0cbd8.jpg', 89, 10, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-20 10:54:00', 10, 2, 0, NULL, NULL, NULL),
(242, NULL, '5 Ply | 18 x 14 x 10 Inch | 10 kg', '5-ply-18-x-14-x-10-inch-10-kg', '1234567', '48191010', '<div><span style=\"font-weight: bolder;\">5 Ply 18 x 14 x 10 Inch</span></div><div>Corrugated Box – Heavy Duty Packaging Box for E-commerce &amp; Industrial Shipping in India</div><div><br></div><div>Strong 5 ply 18 x 14 x 10 inch corrugated box designed for heavy-duty packaging and secure shipping. Durable, compact, and ideal for e-commerce and industrial use across India.</div><div><br></div><div><ul><li>Weight Holding Capacity: 10 kg</li><li>Empty Box Weight: 1.0 KG</li><li>Dimensions: 457 × 355 × 255 mm</li><li>Select Quantity (Pcs)*: 10, 20, 30</li></ul></div>', 1130.00, 1020.00, 102.00, 'No.', 10, 'sgst_cgst', 5.00, NULL, 34, 'uploads/products/6aa3f81f56d17.jpg', 94, 10, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-20 11:05:02', 10, 3, 0, NULL, NULL, NULL),
(243, NULL, '5 Ply | 21 x 13 x 14 Inch | 15 kg', '5-ply-21-x-13-x-14-inch-15-kg', '1234567', '48191010', '<div><span style=\"font-weight: bolder;\">5 Ply 21 x 13 x 14 Inch</span></div><div>Corrugated Box – Heavy Duty Packaging Box for E-commerce &amp; Industrial Shipping in India</div><div><br></div><div>Strong 5 ply 21 x 13 x 14 inch corrugated box designed for heavy-duty packaging and secure shipping. Durable, compact, and ideal for e-commerce and industrial use across India.</div><div><br></div><div><ul><li>Weight Holding Capacity: 15 kg</li><li>Empty Box Weight: 1.1 KG</li><li>Dimensions: 534 × 330 × 355 mm</li><li>Select Quantity (Pcs)*: 10, 20, 30</li></ul></div>', 1410.00, 1270.00, 127.00, 'No.', 10, 'sgst_cgst', 5.00, NULL, 34, 'uploads/products/6aa3f86c7be76.jpg', 99, 10, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-20 11:11:50', 10, 4, 0, NULL, NULL, NULL),
(244, NULL, '5 Ply | 23 x 17 x 17 Inch | 25 kg', '5-ply-23-x-17-x-17-inch-25-kg', '1234567', '48191010', '<div><span style=\"font-weight: bolder;\">5 Ply 23 x 17 x 17 Inch</span></div><div>Corrugated Box – Heavy Duty Packaging Box for E-commerce &amp; Industrial Shipping in India</div><div><br></div><div>Strong 5 ply 23 x 17 x 17 inch corrugated box designed for heavy-duty packaging and secure shipping. Durable, compact, and ideal for e-commerce and industrial use across India.</div><div><br></div><div><ul><li>Weight Holding Capacity: 25 kg</li><li>Empty Box Weight: 1.5 KG</li><li>Dimensions: 585 × 432 × 432 mm</li><li>Select Quantity (Pcs)*: 10, 20, 30</li></ul></div>', 1950.00, 1760.00, 176.00, 'No.', 10, 'sgst_cgst', 5.00, NULL, 34, 'uploads/products/6aa3f8add5039.jpg', 100, 10, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-20 11:18:39', 10, 5, 0, NULL, NULL, NULL),
(245, NULL, 'Kangaro Punch DP-52', 'kangaro-punch-dp-52', '-', '84729099', '<b>Kangaro Punch DP-52</b><div><div><div><span style=\"font-weight: bolder;\">Features:</span></div><div style=\"text-align: justify;\"><font color=\"#212529\"><span style=\"font-size: 16px;\">Mini punch. All metal construction with removable chip tray.</span><br></font><br></div></div><div><b>About this item:</b><br></div><div><ul><li><b>Punching Capacity : </b>10 Sheets of 75~80 gsm paper</li><li><b>No. of Holes : </b>2</li><li><b>Hole Distance : </b>80mm</li><li><b>Punching Dia : </b>5.5mm</li></ul></div></div><div></div>', 112.00, 101.00, 101.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 49, 'uploads/products/6a88488a4f271.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 12:46:02', 10, 2, 0, NULL, NULL, NULL),
(246, NULL, 'Kangaro Punch DP-280 (Blue)', 'kangaro-punch-dp-280-blue', '-', '84729099', '<b>Kangaro Punch DP-280 (Blue)</b><div><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Features:</span><b><br></b><div style=\"font-weight: bolder; text-align: start;\"><span style=\"color: rgb(33, 37, 41); font-size: 16px; text-align: justify; font-weight: 500;\">Classic mini punch. All metal construction with removable chip tray.</span></div></span><div><div style=\"text-align: justify;\"><br></div></div><div><b>About this item:</b><br></div><div><div><ul><li><b>Punching Capacity : </b>11 Sheets of 75~80 gsm paper</li><li><b>No. of Holes : </b>2</li><li><b>Hole Distance : </b>80mm</li><li><b>Punching Dia : </b>5.5mm</li></ul></div></div></div>', 130.00, 117.00, 117.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 49, 'uploads/products/6a884ac652550.jpg', 9, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 12:53:19', 10, 1, 0, NULL, NULL, NULL),
(247, NULL, 'Kangaro Punch DP-480', 'kangaro-punch-dp-480', '-', '84729099', '<b>Kangaro Punch DP-480<br></b><div><div><span style=\"font-weight: bolder;\">Features:</span></div><div style=\"text-align: justify;\"><font color=\"#212529\"><span style=\"font-size: 16px;\">All metal construction with removable chip tray.</span><br></font><br></div></div><div><b>About this item:</b><br></div><div><ul><li><b>Punching Capacity : </b>12 Sheets of 75~80 gsm paper</li><li><b>No. of Holes : </b>2</li><li><b>Hole Distance : </b>80mm</li><li><b>Punching Dia : </b>5.5mm</li></ul></div>', 98.00, 88.00, 88.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 49, 'uploads/products/6a884c1ddd51e.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 13:01:17', 10, 3, 0, NULL, NULL, NULL),
(248, NULL, 'Kangaro Punch DP-600', 'kangaro-punch-dp-600', '-', '84729099', '<b>Kangaro Punch DP-600<br></b><div><div><span style=\"font-weight: bolder;\">Features:</span></div><div style=\"text-align: justify;\"><font color=\"#212529\"><span style=\"font-size: 16px;\">Medium size punch. All metal construction with removable chip tray.</span><br></font><br></div></div><div><b>About this item:</b><br></div><div><ul><li><span style=\"font-weight: bolder;\">Punching Capacity :&nbsp;</span>22 Sheets of 75~80 gsm paper</li><li><span style=\"font-weight: bolder;\">No. of Holes :&nbsp;</span>2</li><li><span style=\"font-weight: bolder;\">Hole Distance :&nbsp;</span>80mm</li><li><span style=\"font-weight: bolder;\">Punching Dia :&nbsp;</span>5.5mm</li></ul></div>', 225.00, 202.00, 202.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 49, 'uploads/products/6a884cde9ca7e.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 13:04:30', 10, 4, 0, NULL, NULL, NULL),
(249, NULL, 'Kangaro 10 Stapler', 'kangaro-10-stapler', '-', '84729010', '<b>Kangaro 10 Stapler</b><div><div><div><span style=\"font-weight: bolder;\">Features:</span></div></div><div>All steel construction. Built in staple remover and reload indicator.<br></div><div><br></div><div><div><span style=\"text-align: var(--bs-body-text-align);\"><b>About this item:</b></span></div><div><ul><li><span style=\"font-weight: bolder;\">Staple Use :</span>&nbsp;No.10</li><li><span style=\"font-weight: bolder;\">Loading Capacity :</span>&nbsp;50 Staples (No.10)</li><li><span style=\"font-weight: bolder;\">Stapling Capacity :</span>&nbsp;20 Sheets of 75~80 gsm paper</li><li><span style=\"font-weight: bolder;\">Throat Depth :</span>&nbsp;52mm</li><li><span style=\"font-weight: bolder;\">Loading Type :&nbsp;</span>Manual</li><li><span style=\"font-weight: bolder;\">Stapling Type :&nbsp;</span>Conventional</li><li><span style=\"font-weight: bolder;\">Tacking :</span>&nbsp;Yes</li></ul></div></div></div>', 75.00, 64.00, 64.00, 'No.', 15, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a884dfeb2a41.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 13:09:18', 10, 1, 0, NULL, NULL, NULL),
(250, NULL, 'Kangaro Mini-10 Stapler', 'kangaro-mini-10-stapler', '-', '84729010', '<b>Kangaro Mini-10 Stapler</b><div><div style=\"\"><b>Features:</b></div><div style=\"\">Plastic body mini stapler with quality steel components. Quick loading mechanism. Built-in staple remover and reload indicator.</div></div><div style=\"\"><br></div><div style=\"\"><div><b>About this item:</b><br></div><div><ul><li><b>Staple Use :</b> No.10</li><li><b>Loading Capacity :</b> 50 Staples (No.10)</li><li><b>Stapling Capacity :</b> 10 Sheets of 75~80 gsm paper</li><li><b>Throat Depth :</b> 25mm</li><li><b>Loading Type : </b>Quick</li><li><b>Stapling Type : </b>Conventional</li><li><b>Tacking :</b> Yes</li></ul></div></div>', 70.00, 60.00, 60.00, 'No.', 14, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a884f1c1fb39.jpg', 9, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 13:14:04', 10, 2, 0, NULL, NULL, NULL),
(252, NULL, 'Kangaro HP-45', 'kangaro-hp-45', '-', '84729010', '<b>Kangaro HP-45</b><div><b>Features:</b><br>Half strip all metal construction with plastic cover. Quick loading mechanism. Rotating anvil &amp; reload indicator. Ideal for frequent stapling.<br><br><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"margin-bottom: 0px; padding: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; line-height: 24px !important;\"><div style=\"\"><span style=\"font-size: 15px;\">About this item:</span><br></div><div style=\"color: rgb(0, 0, 0); font-family: &quot;Amazon Ember&quot; !important; font-size: 15px; font-weight: 500;\"><ul><li><span style=\"font-weight: bolder;\">Staple Use :</span>&nbsp;24/6, 26/6</li><li><span style=\"font-weight: bolder;\">Loading Capacity :</span>&nbsp;50 Staples (24/6), 100 Staples (26/6)</li><li><span style=\"font-weight: bolder;\">Stapling Capacity :</span>&nbsp;30 Sheets of 75~80 gsm paper</li><li><span style=\"font-weight: bolder;\">Throat Depth :</span>&nbsp;45mm</li><li><span style=\"font-weight: bolder;\">Loading Type :&nbsp;</span>Quick</li><li><span style=\"font-weight: bolder;\">Stapling Type :&nbsp;</span>Conventional</li><li><span style=\"font-weight: bolder;\">Tacking :</span>&nbsp;Yes</li></ul></div></h1></div></div>', 300.00, 255.00, 255.00, 'No.', 15, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a8851c86aca0.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-21 13:25:28', 10, 5, 0, NULL, NULL, NULL),
(253, NULL, 'Kangaro Stapler HD-10D', 'kangaro-stapler-hd-10d', '-', '84729010', '<b>Kangaro Stapler HD-10D</b><div><div><div><span style=\"font-weight: bolder;\">Features:</span></div><div>Metal body with plastic casing. Built-in staple remover with staple holder. Holds removed staples, which can be easily disposed.</div></div><div><br></div><div><div><b>About this item:</b><br></div><div><ul><li><span style=\"font-weight: bolder;\">Staple Use :</span>&nbsp;No.10</li><li><span style=\"font-weight: bolder;\">Loading Capacity :</span>&nbsp;100 Staples (No.10)</li><li><span style=\"font-weight: bolder;\">Stapling Capacity :</span>&nbsp;20 Sheets of 75~80 gsm paper</li><li><span style=\"font-weight: bolder;\">Throat Depth :</span>&nbsp;63mm</li><li><span style=\"font-weight: bolder;\">Loading Type :&nbsp;</span>Manual</li><li><span style=\"font-weight: bolder;\">Stapling Type :&nbsp;</span>Conventional</li><li><span style=\"font-weight: bolder;\">Tacking :</span>&nbsp;Yes</li></ul></div></div></div>', 125.00, 113.00, 113.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a8926cd91731.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 04:34:21', 10, 3, 0, NULL, NULL, NULL),
(254, NULL, 'Kangaro Stapler HD-45', 'kangaro-stapler-hd-45', '-', '84729010', '<b>Kangaro Stapler HD-45<br></b><div><span style=\"font-weight: bolder; text-align: var(--bs-body-text-align);\">Features:<br></span>Metal body half strip stapler with plastic casing. Quick loading mechanism. Rubber feet to avoid desk top from scratches. Built-in staple remover with staple holder. Holds removed staples, which can be easily disposed. Reload indicator.<br><br><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; line-height: 24px !important;\"><div style=\"\"><span style=\"font-size: 15px;\">About this item:</span><br></div><div style=\"color: rgb(0, 0, 0); font-family: &quot;Amazon Ember&quot; !important; font-size: 15px; font-weight: 500;\"><ul><li><span style=\"font-weight: bolder;\">Staple Use :</span>&nbsp;24/6, 26/6</li><li><span style=\"font-weight: bolder;\">Loading Capacity :</span>&nbsp;50 Staples (24/6), 100 Staples (26/6)</li><li><span style=\"font-weight: bolder;\">Stapling Capacity :</span>&nbsp;30 Sheets of 75~80 gsm paper</li><li><span style=\"font-weight: bolder;\">Throat Depth :</span>&nbsp;64mm</li><li><span style=\"font-weight: bolder;\">Loading Type :&nbsp;</span>Quick</li><li><span style=\"font-weight: bolder;\">Stapling Type :&nbsp;</span>Conventional</li><li><span style=\"font-weight: bolder;\">Tacking :</span>&nbsp;Yes</li></ul></div></h1></div></div>', 180.00, 162.00, 150.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a8927a7072ca.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 04:37:59', 10, 4, 0, NULL, NULL, NULL),
(255, NULL, 'Kangaro Staple Pin No 10-1M | Pack of 20', 'kangaro-staple-pin-no-10-1m-pack-of-20', '-', '83052000', '<b>Kangaro Staple Pin No 10-1M&nbsp;&nbsp;| Pack of 20</b><br><div style=\"\"><b>About this item:</b><br></div><div style=\"\"><ul><li><b>Strip : </b>50 nos.</li><li><b>Packing : </b>1000 staples.</li><li><b>Bxs./Ctn : </b>800 bxs.</li><li><b>Volume/Ctn : </b>0.0219 cbm.</li><li><b>Weight/Ctn : </b>19.04 Kgs.</li></ul></div><div style=\"font-weight: bold;\"><br></div>', 240.00, 205.00, 10.00, 'Pack', 15, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a892cd365012.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:00:03', 10, 7, 0, NULL, NULL, NULL),
(256, NULL, 'Kangaro Stapler Pin 23/24-H', 'kangaro-stapler-pin-23-24-h', '-', '83052000', '<b>Kangaro Stapler Pin 23/24-H</b><div><div><b>About this item:</b><br></div><div><ul><li><span style=\"font-weight: bolder;\">Strip :&nbsp;</span>100 nos.</li><li><span style=\"font-weight: bolder;\">Packing :&nbsp;</span>1000 staples.</li><li><span style=\"font-weight: bolder;\">Bxs./Ctn :&nbsp;</span>100 bxs.</li><li><span style=\"font-weight: bolder;\">Volume/Ctn :&nbsp;</span>0.0245 cbm.</li><li><span style=\"font-weight: bolder;\">Weight/Ctn :&nbsp;</span>21.85 Kgs.</li></ul></div></div>', 135.00, 121.00, 121.00, 'Pack', 10, 'sgst_cgst', 18.00, NULL, 50, 'uploads/products/6a892d88db11a.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:03:04', 10, 8, 0, NULL, NULL, NULL),
(257, NULL, 'JK Easy Copier Paper | A4 Size | 70 GSM | 500 Sheets', 'jk-easy-copier-paper-a4-size-70-gsm-500-sheets', '-', '48025610', '<h1 id=\"title\" class=\"a-size-large a-spacing-none\" style=\"padding: 0px; margin-right: 0px; margin-left: 0px; padding-block-end: 0px; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; margin-bottom: 0px !important; font-size: 24px !important; line-height: 32px !important;\"><span style=\"color: rgb(0, 0, 0); font-size: 15px; text-align: var(--bs-body-text-align);\"><b>JK Easy Copier Paper | A4 Size | 70 GSM</b></span><br></h1><div><span style=\"text-align: var(--bs-body-text-align);\">500 Sheets | White Paper, 1 Ream | For Laserjet &amp; Inkjet Printer | Fast Drying | Both Side Print | Eco Friendly | ColorLok (Pack of 1) | One Ream</span></div><div><span style=\"text-align: var(--bs-body-text-align);\"><ul><li><b>Brand:</b> JK</li><li><b>Colour: </b>White</li><li><b>Item Weight: </b>2190 Grams</li><li><b>Paper Finish:</b> Matte</li><li><b>Sheet Size:</b> A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Colour: White</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Minimum machine jamming</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Ideal for photocopying</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">ColorLok technology - Faster drying, bolder blacks, vivid colors</span></li></ul></div></span></div>', 328.00, 295.00, 295.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a8933a3f2a66.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:27:13', 1, 1, 0, NULL, NULL, NULL),
(258, NULL, 'JK Copier Paper | A4 Paper Size | 75 GSM | 500 Sheets', 'jk-copier-paper-a4-paper-size-75-gsm-500-sheets', '-', '48025610', '<div><b>JK Copier Paper - A4, 75 GSM</b></div><div>1 Ream, 500 Sheets</div><div><ul><li><b>Brand:</b> JK</li><li><b>Colour:</b> White</li><li><b>Item Weight:</b> 2180 Grams</li><li><b>Paper Finish: </b>Smooth</li><li><b>Sheet Size: </b>A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Premium Multipurpose Office Paper</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Manufactured from Plantation Wood Pulp : 100% ECF</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">High whiteness &amp; brightness – acid-free long lasting paper</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Colorlok Technology to provide bolder black, vivid colour &amp; faster drying</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Highly automated manufacturing technology with TWT</span></li></ul></div></div>', 333.00, 299.00, 299.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a89351020e35.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:35:12', 10, 2, 0, NULL, NULL, NULL),
(259, NULL, 'JK Cedar | A4 Paper Size | 100 GSM | 500 Sheets', 'jk-cedar-a4-paper-size-100-gsm-500-sheets', '-', '48025610', '<div><b style=\"text-align: var(--bs-body-text-align);\">JK Cedar A4 Paper, 100 GSM</b><br></div><div><div>500 Sheets, Premium Printing and Copying Paper</div></div><div><div><ul><li><b>Brand:</b> JK Paper</li><li><b>Colour: </b>White</li><li><b>Item Weight:</b> 3.12 Kilograms</li><li><b>Paper Finish: </b>Uncoated</li><li><b>Sheet Size:</b> A4</li></ul><div><b>About this item:</b><br></div></div></div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">PREMIUM QUALITY: 100 GSM thickness ensures superior paper quality for both printing and copying needs</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">VERSATILE USE: Perfect for office documents, school assignments, presentations, and general everyday printing requirements</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">PACK CONTENTS: Contains 500 sheets of crisp white A4 size paper measuring 21.0 x 29.7 centimetres</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">SMOOTH FINISH: High-grade paper surface ensures clear, sharp text and vibrant image reproduction</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">PRINTER COMPATIBLE: Works excellently with all types of inkjet printers, laser printers, and photocopy machines</span></li></ul>', 510.00, 458.00, 458.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a8937445ff9c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:44:36', 10, 3, 0, NULL, NULL, NULL),
(260, NULL, 'JK Sparkle | A4 Paper Size | 70 GSM | 500 Sheets', 'jk-sparkle-a4-paper-size-70-gsm-500-sheets', '-', '48025610', '<div><b style=\"text-align: var(--bs-body-text-align);\">JK Sparkle | 70 GSM | A4 Size</b><br></div><div><div>500 Sheets</div></div><div><ul><li><b>Brand: </b>JK Paper</li><li><b>Colour: </b>White</li><li><b>Item Weight: </b>70 Grams</li><li><b>Paper Finish:</b> Smooth</li><li><b>Sheet Size:</b> A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">A4 Size, 70 GSM, 1 Ream (500 Sheets)</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Color - White</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Paper Finish - Smooth</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Suitable for any job on office printer, inkjet and laserjet</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">ColorLok technology: Faster drying, bolder blacks, vivid colors</span></li></ul></div></div>', 550.00, 494.00, 494.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a89389417df1.png', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:50:12', 10, 4, 0, NULL, NULL, NULL),
(261, NULL, 'JK Excel Bond | A4 Paper Size | 100 GSM | 100 Sheets', 'jk-excel-bond-a4-paper-size-100-gsm-100-sheets', '-', '48025610', '<b>JK Excel Bond Paper of size A4, 100 Sheets Pack, 90 GSM</b><div><div style=\"\"><ul><li><b>Brand:</b> JK Paper</li><li><b>Colour:</b> Black</li><li><b>Item Weight:</b> 560 Grams</li><li><b>Paper Finish:</b> Ultra Smooth</li><li><b>Sheet Size:</b> A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Watermarked Bond paper</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Superfine Ultra white</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Excellent Stiffness</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Compatible with all type of Inkjet/ Laserjet Printers</span></li></ul></div></div></div>', 129.00, 116.00, 116.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a893a5c3d2d8.png', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 05:57:48', 10, 10, 0, NULL, NULL, NULL),
(262, NULL, 'TNPL Copier Paper | A4 Paper Size | 70 GSM | 500 Sheets', 'tnpl-copier-paper-a4-paper-size-70-gsm-500-sheets', '-', '48025610', '<h1 id=\"title\" class=\"a-size-large a-spacing-none\" style=\"padding: 0px; margin-right: 0px; margin-left: 0px; padding-block-end: 0px; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; margin-bottom: 0px !important; font-size: 24px !important; line-height: 32px !important;\"><span style=\"color: rgb(0, 0, 0); font-size: 15px; text-align: var(--bs-body-text-align);\"><b>TNPL Copier Paper A4, 500 Sheets, 70 GSM&nbsp;</b></span><br></h1><div><div>(1 Ream)</div></div><div><ul><li><b>Brand:</b> LJL Traders</li><li><b>Colour:</b> White</li><li><b>Item Weight:</b> 1999 Grams</li><li><b>Paper Finish:</b> Glossy</li><li><b>Sheet Size:</b> A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Ideal for Photocopying</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Package Content: 1Ream of A4 Pack, 500 Sheets</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">High Brightness and opacity</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Offers you absolute copying freedom</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Reliable for day-to-day use</span></li></ul></div></div>', 650.00, 585.00, 585.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a893d6e6e5ff.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 06:09:31', 10, 5, 0, NULL, NULL, NULL),
(263, NULL, 'Copy Power Paper | A4 Paper Size | 75 GSM | 500 Sheets', 'copy-power-paper-a4-paper-size-75-gsm-500-sheets', '-', '48025610', '<div><b>Copy Power Paper | A4 Size | 75 GSM | 500 Sheets</b></div><div>White, 1 Ream | For Laserjet &amp; Inkjet Printer | Fast Drying | Both Side Print</div><div><ul><li><b>Brand:</b> Bilt</li><li><b>Colour: </b>White</li><li><b>Item Weight:</b> 75 Grams</li><li><b>Paper Finish:</b> Ultra Smooth</li><li><b>Sheet Size: </b>A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">FAST DRYING INK ABSORPTION: The paper\'s fast-drying surface prevents smudging and ensures crisp, clean prints every time.</span></li></ul></div></div><div><br></div><div><br></div>', 377.00, 339.00, 339.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a893ff5e7235.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 06:21:41', 10, 6, 0, NULL, NULL, NULL),
(264, NULL, 'JK Paper Ledger - FS | 80 GSM | 500 Sheets', 'jk-paper-ledger-fs-80-gsm-500-sheets', '-', '48025610', '<div><b>JK Paper Ledger - FS, 80 GSM</b></div><div>500 Sheets, 1 Ream</div><div><ul><li><b>Brand: </b>JK Paper</li><li><b>Colour: </b>Green</li><li><b>Item Weight: </b>80 Grams</li><li><b>Paper Finish: </b>Smooth</li><li><b>Sheet Size: </b>8.5 x 14 inches</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Colour: Green</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Laid marked paper</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Precision cutting</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Longer shelf life</span></li></ul></div></div>', 665.00, 599.00, 599.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a89423a9137a.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 06:31:22', 10, 9, 0, NULL, NULL, NULL),
(265, NULL, 'B2B Copier Paper | A4 Paper Size | 70 GSM | 500 Sheets', 'b2b-copier-paper-a4-paper-size-70-gsm-500-sheets', '-', '48025610', '<div><b>B2B Copier Paper, A4 Paper Size, 70 GSM</b></div><div>500 Sheet</div><div><ul><li><b>Brand: </b>B2B</li><li><b>Colour:</b> White</li><li><b>Item Weight:</b> 2.24 Kilograms</li><li><b>Paper Finish:</b> Smooth</li><li><b>Sheet Size:</b> A4</li></ul><div><b>About this item:</b><br></div></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Quality Product From A Trusted Brand</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Made from Eco-friendly ECF pulp</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">70 GSM Paper Well Suited For Copying and Printing</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Jam Free High Speed Colour Print &amp; Copyig.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Color: White</span></li></ul></div><div><br></div><div><br></div>', 384.00, 345.00, 345.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a8943d28fdb1.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 06:38:10', 10, 8, 0, NULL, NULL, NULL),
(266, NULL, 'PaperOne | A4 Paper Size | 100 GSM | 500 Sheets', 'paperone-a4-paper-size-100-gsm-500-sheets', '-', '48025610', '<div><b>PaperOne A4 Printing Paper, 100 Gsm</b></div><div>500 Sheets</div><div><ul><li><b>Brand: </b>Paperone</li><li><b>Colour: </b>White</li><li><b>Item Weight:</b> 3.12 Kilograms</li><li><b>Paper Finish:</b> Smooth</li><li><b>Sheet Size: </b>A4</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">High Quality Paper</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Colour: White</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Size: A4 100GSM</span></li></ul></div></div>', 745.00, 670.00, 670.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6a8944ec5475e.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 06:42:52', 10, 7, 0, NULL, NULL, NULL),
(267, NULL, 'Kodak Photo Paper | A4 Glossy | 200 GSM | 20 Sheets', 'kodak-photo-paper-a4-glossy-200-gsm-20-sheets', '-', '37031010', '<b>Kodak Photo Paper | A4 Glossy | 200 GSM | 20 Sheets<br></b><span style=\"text-align: var(--bs-body-text-align);\">Water Resistant, Compatible with all Inkjet Printers</span><div><span style=\"text-align: var(--bs-body-text-align);\"><ul><li><b>Brand:</b> KODAK</li><li><b>Colour:</b> White</li><li><b>Item Weight:</b> 220 Grams</li><li><b>Paper Finish: </b>Glossy</li><li><b>Sheet Size: </b>A4</li></ul><div><div><b>About this item:</b></div><div><ul><li>We are the exclusive distributors of Kodak Papers PAN India.</li><li>It is High Grade with Higher percentage of Photo Paper and Compatiable With Epson /Canon/Lexmark Inkjet Printers</li><li>Paper Quality (GSM): 200 GSM Professional and Waterproof.Use these sheets with any inkjet printer and get quality prints that dry fast and are ready in a jiffy</li><li>Pass the prints around the room so that everyone can admire your photography without you worrying about any smudges or smears on the fresh prints</li><li>Product Type: High Glossy, Size: A4, No of Sheets: 20</li></ul></div></div></span></div>', 288.00, 259.00, 259.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 52, 'uploads/products/6a8948ef4c8ed.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 06:59:13', 10, 2, 0, NULL, NULL, NULL),
(268, NULL, 'Kodak Photo Paper | A4 Glossy | 180 GSM | 20 Sheets', 'kodak-photo-paper-a4-glossy-180-gsm-20-sheets', '-', '37031010', '<span style=\"font-weight: bolder;\">Kodak Photo Paper | A4 Glossy | 180 GSM | 20 Sheets<br></span><span style=\"text-align: var(--bs-body-text-align);\">Water Resistant, Compatible with all Inkjet Printers</span><div><span style=\"text-align: var(--bs-body-text-align);\"><ul><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;KODAK</li><li><span style=\"font-weight: bolder;\">Colour:</span>&nbsp;White</li><li><span style=\"font-weight: bolder;\">Item Weight:</span>&nbsp;200 Grams</li><li><span style=\"font-weight: bolder;\">Paper Finish:&nbsp;</span>Glossy</li><li><span style=\"font-weight: bolder;\">Sheet Size:&nbsp;</span>A4</li></ul><div><div><b>About this item:</b></div><div><ul><li>We are the exclusive distributors of Kodak Papers PAN India.</li><li>Glossy white 180 GSM (210x297mm) A4 Photo Paper is CC(Cast Coated) and water resistant. Brilliant and full color output with perfect color saturation. Quantity : 20 sheets</li><li>This photo paper is designed with new ink absorption technology which makes it long lasting and instant drying.</li><li>Compatible with all kinds of modern inkjet printers namely Epson, HP, Canon, Brother Inkjet Printer among others. Suitable for Piezo-electric printers.</li><li>Suitable for printing modes of 1440dpi up to 5700dpi</li></ul></div></div></span></div>', 222.00, 199.00, 199.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 52, 'uploads/products/6a894a7e88bf0.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 07:06:38', 10, 1, 0, NULL, NULL, NULL),
(270, NULL, 'Kodak Photo Paper | A4 Glossy | 230 GSM | 20 Sheets', 'kodak-photo-paper-a4-glossy-230-gsm-20-sheets', '-', '37031010', '<span style=\"font-weight: bolder;\">Kodak Photo Paper | A4 Glossy | 230 GSM | 20 Sheets<br></span><span style=\"text-align: var(--bs-body-text-align);\">Water Resistant, Pack of 20 sheets, Compatible with all Inkjet Printers</span><div><span style=\"text-align: var(--bs-body-text-align);\"><ul><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;KODAK</li><li><span style=\"font-weight: bolder;\">Colour:</span>&nbsp;White</li><li><span style=\"font-weight: bolder;\">Item Weight:</span>&nbsp;310 Grams</li><li><span style=\"font-weight: bolder;\">Paper Finish:&nbsp;</span>Glossy</li><li><span style=\"font-weight: bolder;\">Sheet Size:&nbsp;</span>A4</li></ul><div><div><b>About this item:</b></div><div><ul><li>We are the exclusive distributors of Kodak Papers PAN India.</li><li>It is High Grade with Higher percentage of Photo Paper and Compatiable With Epson /Canon/Lexmark Inkjet Printers</li><li>Paper Quality (GSM): 230 GSM Professional and Waterproof.Use these sheets with any inkjet printer and get quality prints that dry fast and are ready in a jiffy</li><li>Pass the prints around the room so that everyone can admire your photography without you worrying about any smudges or smears on the fresh prints</li><li>Product Type: High Glossy, Size: A4, No of Sheets: 20</li></ul></div></div></span></div>', 288.00, 259.00, 259.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 52, 'uploads/products/6a894b6ed38b0.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 07:10:38', 10, 3, 0, NULL, NULL, NULL),
(271, NULL, 'Kodak 4R | 4x6 | 180 GSM | 100 Sheets', 'kodak-4r-4x6-180-gsm-100-sheets', '-', '37031010', '<span style=\"font-weight: bolder;\">Kodak Photo Paper | 4X6 | 180 GSM | 100 Sheets<br></span><span style=\"text-align: var(--bs-body-text-align);\">Water Resistant, Compatible with all Inkjet Printers</span><div><span style=\"text-align: var(--bs-body-text-align);\"><ul><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;KODAK</li><li><span style=\"font-weight: bolder;\">Colour:</span>&nbsp;White</li><li><span style=\"font-weight: bolder;\">Item Weight:</span>&nbsp;280 Grams</li><li><span style=\"font-weight: bolder;\">Paper Finish:&nbsp;</span>Glossy</li><li><span style=\"font-weight: bolder;\">Sheet Size:&nbsp;</span>4x6 Inches</li></ul><div><div><b>About this item:</b></div><div><ul><li>Kodak Photo Paper comes in a variety of sizes, including 4x6, 4R, 5R, and A4, to meet all your printing needs.</li><li>Choose from our range of pack sizes, from 20 to 100 sheets, to get the quantity that best fits your printing frequency.</li><li>Our photo paper comes in multiple qualities, including glossy, high gloss, and premium ultra-glossy, to achieve the exact look you want.</li><li>Compatible with all inkjet printers, our photo paper is quick-drying and smudge-free, ensuring that your prints will be ready to handle right away.</li><li>This photo paper is 4R (4 x 6 inch) size only. It is NOT A4 size. Please verify the required photo size before placing your order.</li></ul></div></div></span></div>', 288.00, 259.00, 259.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 52, 'uploads/products/6a894d4c4f29e.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 07:18:36', 10, 4, 0, NULL, NULL, NULL),
(272, NULL, 'Kodak 4R | 4x6 | 200 GSM | 100 Sheets', 'kodak-4r-4x6-200-gsm-100-sheets', '-', '37031010', '<span style=\"font-weight: bolder;\">Kodak Photo Paper | 4X6 | 200 GSM | 100 Sheets<br></span><span style=\"text-align: var(--bs-body-text-align);\">Water Resistant, Compatible with all Inkjet Printers</span><div><span style=\"text-align: var(--bs-body-text-align);\"><ul><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;KODAK</li><li><span style=\"font-weight: bolder;\">Colour:</span>&nbsp;White</li><li><span style=\"font-weight: bolder;\">Item Weight:</span>&nbsp;330 Grams</li><li><span style=\"font-weight: bolder;\">Paper Finish: </span>High<span style=\"font-weight: bolder;\">&nbsp;</span>Glossy</li><li><span style=\"font-weight: bolder;\">Sheet Size:&nbsp;</span>Post Card</li></ul><div><div><b>About this item:</b></div><div><ul><li>It is High Grade with Higher percentage of Photo Paper and Compatiable With Epson /Canon/Lexmark Inkjet Printers. Paper Quality (GSM): 200 GSM Professional and Waterproof</li><li>Use these sheets with any inkjet printer and get quality prints that dry fast and are ready in a jiffy</li><li>Pass the prints around the room so that everyone can admire your photography without you worrying about any smudges or smears on the fresh prints</li><li>Product Type: High Glossy, Size: 4R (4x6 inch), No of Sheets: 100</li><li>This photo paper is 4R (4 x 6 inch) size only. It is NOT A4 size. Please verify the required photo size before placing your order.</li></ul></div></div></span></div>', 310.00, 279.00, 279.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 52, 'uploads/products/6a894ec1c55fe.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 07:24:49', 10, 5, 0, NULL, NULL, NULL),
(273, NULL, 'Small Folding Cutting Scissor', 'small-folding-cutting-scissor', '-', '82130000', '<div><b>Small Folding Cutting Scissor&nbsp;</b></div><div>For Travelling/Pocket Scissor|Mini folding pocket scissors/Student Scissor/Used For Craft Work General Cutting Beauty Personal Care All Purpose</div><div><ul><li><b>Colour:</b> Silver</li><li><b>Material:</b> Stainless Steel</li><li><b>Brand:</b> ScentRose</li><li><b>Blade Material:</b> Stainless Steel</li><li><b>Product Dimensions:</b> 10L x 3W Centimeters</li></ul><div><div><b>About this item:</b></div><div><ul><li>DURABLE QUALITY – These professional hair cutting scissors are premium quality, made from 100% percent highly durable stainless steel.</li><li>EFFICIENT AND SMOOTH CUTTING –you can work efficiently with these scissors, providing a smooth and clean cut.</li><li>NON-SLIP HANDLES – These shears have non-slip handles ensuring a comfortable grip and can fit easily in your fingers.</li><li>LIGHTWEIGHT – With such a light-weight, anybody can handle these hair cutting scissors with ease as they offer optimum control and balance.</li><li>ERGONOMIC DESIGN – Our hair cutting scissors are perfect for salon workers, barbers, and home usage. It has a distinctive ergonomic design with removable inserts and sharp blades.</li><li>Made of high-quality stainless steel, it is durable and well-made. It is very sharp and suitable for fast cutting thread, paper and other items.</li><li>The tip of the scissors is covered when folded to protect you from potential danger. It is safe to use for both adults and children.</li><li>Can be used to cut many items, such as tape, paper, cloth, hair, thread, and cardboard. Suitable for crafting, camping, emergency, survival, outdoors, etc.</li></ul></div></div></div>', 126.00, 113.00, 113.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 53, 'uploads/products/6a8962781399b.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 08:48:56', 10, 0, 0, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `seller_id`, `name`, `slug`, `sku`, `hsn`, `description`, `mrp`, `selling_price`, `pay_per_unit`, `unit_label`, `discount_percentage`, `gst_type`, `gst_rate`, `shipping_charge`, `category_id`, `main_image`, `stock_quantity`, `package_quantity`, `is_active`, `is_approved`, `approved_at`, `approved_by`, `rejection_reason`, `is_featured`, `is_discounted`, `created_at`, `max_quantity_per_order`, `sort_order`, `has_variations`, `brand_id`, `seller_name`, `seller_code`) VALUES
(274, NULL, 'Kangaroo Folding Scissors | FL-1243 | 112 mm | 4.5\"', 'kangaroo-folding-scissors-fl-1243-112-mm-4-5', '-', '82130000', '<div><b>Kangaroo Folding Scissors | FL-1243 | 112 mm | 4.5\"&nbsp;</b></div><div>Stainless Steel Foldable Scissors | Pointed Tip Blade with Lock Mechanism | Asymmetrical Handles for Easy Grip</div><div><ul><li><b>Material: </b>Stainless Steel</li><li><b>Brand:</b> Generic</li><li><b>Blade Material:</b> Stainless Steel</li><li><b>Product Dimensions:</b> 11.2L x 3W Centimeters</li><li><b>Style:</b> Foldable</li></ul><div><div><b>About this item:</b></div><div><ul><li>Versatile Scissors: These 112 mm (4.5 inches) folding scissors feature stainless steel blades with a pointed tip for precise cutting.</li><li>Ergonomic Design: Double-loop asymmetrical handles provide a comfortable grip for both right and left-handed users.</li><li>Smooth Operation: Orbital riveting ensures smooth movement of the blades during use.</li><li>Locking Mechanism: A lock mechanism keeps the scissors securely closed when not in use.</li><li>Durable Construction: Glass-filled nylon handles offer long-lasting durability.</li></ul></div></div></div>', 88.00, 80.00, 80.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 53, 'uploads/products/6a8963c4a2d08.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 08:54:28', 10, 0, 0, NULL, NULL, NULL),
(275, NULL, 'Munix Folding Scissors | FL-1243 | 112 mm | 4.5\"', 'munix-folding-scissors-fl-1243-112-mm-4-5', '-', '82130000', '<div><b>Munix Folding Scissors | FL-1243 | 112 mm | 4.5\"</b></div><div>Steel Foldable Scissors | Pointed Tip Blade with Lock Mechanism | Asymmetrical Handles for Easy Grip | Green</div><div><b>About this item:</b><br></div><div><div><ul><li><b>Colour:</b> Green</li><li><b>Material: </b>Stainless Steel</li><li><b>Brand:</b> Munix</li><li><b>Blade Material: </b>Stainless Steel</li><li><b>Product Dimensions:</b> 12L x 7.2W Centimeters</li></ul></div></div>', 91.00, 82.00, 82.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 53, 'uploads/products/6a8966207c9e4.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 09:03:51', 10, 0, 0, NULL, NULL, NULL),
(276, NULL, 'Rubber Bands | 3 inch | 50 gm', 'rubber-bands-3-inch-50-gm', '-', '40169920', '<div><b>Rubber Bands | 3 inch</b></div><div>Enhanced Stretchability and Durability | Multicolour&nbsp;<span style=\"text-align: var(--bs-body-text-align);\">|&nbsp;</span><span style=\"text-align: var(--bs-body-text-align);\">Pack of 50 gm</span></div><div><ul><li><b>Material:</b> Rubber</li><li><b>Brand:</b> Fliptic</li><li><b>Colour: </b>Multicolor</li><li><b>Net Quantity:</b> 50.0 Grams</li><li><b>Product Dimensions:</b> 15L x 10W Centimeters</li></ul><div><div><b>About this item:</b></div><div><ul><li>Enhanced Stretchability: Enjoy superior stretchability compared to ordinary rubber bands, providing a secure hold.</li><li>Secure Stretch Lock Mechanism: The rubber band locks at maximum stretch, preventing breakage and ensuring reliability.</li><li>Resilient Elasticity: Returns to its original shape after use, maintaining its effectiveness over time.</li><li>High-Grade Latex Material: Crafted from high-quality latex material, ensuring durability and longevity.</li><li>Odourless and Residue-Free: Free from oil and odour, providing a pleasant user experience without any lingering smell.</li><li>Lower Weight, Higher Quantity: Lightweight design allows for easy storage and transport, offering more bands per gram.</li></ul></div></div></div>', 143.00, 129.00, 129.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 54, 'uploads/products/6a8969c65c61f.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 09:19:08', 10, 4, 0, NULL, NULL, NULL),
(277, NULL, 'Rubber Bands | 1 inch | 50 gm', 'rubber-bands-1-inch-50-gm', '1234567', '-', '<div><span style=\"font-weight: bolder;\">Rubber Bands | 1 inch</span></div><div>Enhanced Stretchability and Durability | Multicolour |&nbsp;<span style=\"text-align: var(--bs-body-text-align);\">Pack of 50 gm</span></div><div><br></div><div><ul><li><span style=\"font-weight: bolder;\">Material:</span>&nbsp;Rubber</li><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;Fliptic</li><li><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Multicolor</li><li><span style=\"font-weight: bolder;\">Net Quantity:</span>&nbsp;50.0 Grams</li><li><span style=\"font-weight: bolder;\">Product Dimensions:</span>&nbsp;15L x 10W Centimeters</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-weight: 700 !important; font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><h1 class=\"a-size-base-plus a-text-bold\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgb(15, 17, 17); padding: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; font-weight: 700 !important; line-height: 24px !important; font-size: 1.25rem !important; font-family: &quot;Amazon Ember&quot; !important;\"><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px; font-weight: 400;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Enhanced Stretchability: Enjoy superior stretchability compared to ordinary rubber bands, providing a secure hold.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Secure Stretch Lock Mechanism: The rubber band locks at maximum stretch, preventing breakage and ensuring reliability.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Resilient Elasticity: Returns to its original shape after use, maintaining its effectiveness over time.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">High-Grade Latex Material: Crafted from high-quality latex material, ensuring durability and longevity.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Odourless and Residue-Free: Free from oil and odour, providing a pleasant user experience without any lingering smell.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Lower Weight, Higher Quantity: Lightweight design allows for easy storage and transport, offering more bands per gram.</span></li></ul></h1></div></div>', 143.00, 129.00, 129.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 54, 'uploads/products/6a896aa4a2f3f.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 09:23:48', 10, 1, 0, NULL, NULL, NULL),
(278, NULL, 'Rubber Bands | 1.5 inch | 200 gm', 'rubber-bands-1-5-inch-200-gm', '-', '40169920', '<div><span style=\"font-weight: bolder;\">Rubber Bands | 1.5 inch</span></div><div>Enhanced Stretchability and Durability | Multicolour&nbsp;| Pack of 200 gm</div><div><ul><li><span style=\"font-weight: bolder;\">Material:</span>&nbsp;Rubber</li><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;Fliptic</li><li><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Multicolor</li><li><span style=\"font-weight: bolder;\">Net Quantity:</span>&nbsp;200 Grams</li><li><span style=\"font-weight: bolder;\">Product Dimensions:</span>&nbsp;15L x 10W Centimeters</li></ul><div><div><b>About this item:</b></div><div><ul><li>Enhanced Stretchability: Enjoy superior stretchability compared to ordinary rubber bands, providing a secure hold.</li><li>Secure Stretch Lock Mechanism: The rubber band locks at maximum stretch, preventing breakage and ensuring reliability.</li><li>Resilient Elasticity: Returns to its original shape after use, maintaining its effectiveness over time.</li><li>High-Grade Latex Material: Crafted from high-quality latex material, ensuring durability and longevity.</li><li>Odourless and Residue-Free: Free from oil and odour, providing a pleasant user experience without any lingering smell.</li><li>Lower Weight, Higher Quantity: Lightweight design allows for easy storage and transport, offering more bands per gram.</li></ul></div></div></div>', 143.00, 129.00, 129.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 54, 'uploads/products/6a896bb0a558d.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 09:28:16', 10, 2, 0, NULL, NULL, NULL),
(279, NULL, 'Rubber Bands | 2 inch | 50 gm', 'rubber-bands-2-inch-50-gm', '-', '40169920', '<div><span style=\"font-weight: bolder;\">Rubber Bands | 2 inch</span></div><div>Enhanced Stretchability and Durability | Multicolour&nbsp;<span style=\"text-align: var(--bs-body-text-align);\">|&nbsp;</span><span style=\"text-align: var(--bs-body-text-align);\">Pack of 50 gm</span></div><div><ul><li><span style=\"font-weight: bolder;\">Material:</span>&nbsp;Rubber</li><li><span style=\"font-weight: bolder;\">Brand:</span>&nbsp;Fliptic</li><li><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Multicolor</li><li><span style=\"font-weight: bolder;\">Net Quantity:</span>&nbsp;50.0 Grams</li><li><span style=\"font-weight: bolder;\">Product Dimensions:</span>&nbsp;15L x 10W Centimeters</li></ul><div><div><b>About this item:</b></div><div><ul><li>Enhanced Stretchability: Enjoy superior stretchability compared to ordinary rubber bands, providing a secure hold.</li><li>Secure Stretch Lock Mechanism: The rubber band locks at maximum stretch, preventing breakage and ensuring reliability.</li><li>Resilient Elasticity: Returns to its original shape after use, maintaining its effectiveness over time.</li><li>High-Grade Latex Material: Crafted from high-quality latex material, ensuring durability and longevity.</li><li>Odourless and Residue-Free: Free from oil and odour, providing a pleasant user experience without any lingering smell.</li><li>Lower Weight, Higher Quantity: Lightweight design allows for easy storage and transport, offering more bands per gram.</li></ul></div></div></div>', 143.00, 129.00, 129.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 54, 'uploads/products/6a896cb1f3ee9.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 09:32:33', 10, 3, 0, NULL, NULL, NULL),
(280, NULL, 'Glue Sticks for Glue Gun | 07mm x 165mm in Length | Pack of 10', 'glue-sticks-for-glue-gun-07mm-x-165mm-in-length-pack-of-10', '-', '-', '<div><b>Glue Sticks for Glue Gun | 07mm x 165mm in Length&nbsp;</b></div><div>Hot Melt Transparent Glue Stick - Pack of 10 - Bonding Adhesive Glue Sticks for DIY - Arts Crafts - Schools and Industries</div><div><ul><li><b>Brand: </b>VCR</li><li><b>Specific Uses For Product: </b>Craft, Fixing, Packaging, School, Electronics, Repair, Art, Model, Cable, Gift, Décor</li><li><b>Material: </b>EVA Resin</li><li><b>Item Form: </b>Stick</li><li><b>Special Feature: </b>Fast Application, Non-Drip Formula</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Strong Bond on Multiple Materials VCR hot melt glue sticks provide strong, lasting adhesion on a wide range of surfaces including paper, cardboard, wood, plastic, fabric, leather, foam, and ceramics. Perfect for crafting, fixing, packaging, or school projects, they deliver dependable performance without cracking or peeling.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Quick Melting and Smooth Application These glue sticks melt rapidly and flow evenly through standard glue guns, making them ideal for precise DIY work. The non-drip formula reduces clogging and mess, ensuring clean application for detailed tasks in crafts, electronics, and repairs.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Safe, Non-Toxic, and Odor-Free Made from high-quality EVA resin, VCR glue sticks are non-toxic and low in odor, making them safe for indoor use. Suitable for home, office, or classroom environments, they are a safe choice for all age groups when used under supervision.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Clear Finish with Minimal Residue Dries quickly to a transparent finish with minimal stringing or residue. Ideal for decorative work, school models, and any visible application where appearance matters. The glue sets firmly and cleanly, supporting a polished, professional result.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Multipurpose and Long-Lasting VCR glue sticks are suitable for a wide range of uses including gift wrapping, craft decorations, light repairs, and electronic wire fixing. Available in multiple pack sizes and sealed in moisture-resistant pouches to maintain quality and extend shelf life.</span></li></ul></div></div>', 120.00, 108.00, 108.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 55, 'uploads/products/6a896eeb9602c.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-22 09:41:13', 10, 0, 0, NULL, NULL, NULL),
(281, NULL, 'Doms Pastel Brush Pens | 14 Shades | 12 + 1 + 1 | Pack of 1 | Multicolor', 'doms-pastel-brush-pens-14-shades-12-1-1-pack-of-1-multicolor', '-', '-', '<div><b>Doms Pastel Brush Pens |&nbsp;<span style=\"text-align: var(--bs-body-text-align);\">14 Shades | 12 + 1 + 1 | Pack of 1 | Multicolor</span></b></div><div><ul><li><b>Brand:</b> Doms</li><li><b>Writing instrument form:</b> Brush Pen</li><li><b>Colour:</b> Multicoloured</li><li><b>Ink Colour:</b> Assorted</li><li><b>Age Range (Description):</b> Teen</li></ul><div><div><b>About this item:</b></div><div><ul><li>Super Smooth Tip to Create Thin, Medium &amp; Bold Strokes.</li><li>The inks are water based and do not dry out easily. Pen-style makes it convenient for students and early learners to go hassel free and not worrying about getting their hands and clothes dirty.</li><li>Available in 13 Pastel Shades and comes with 1 Colourless Blender Pen to blend 2 or more Shades creating an Ombre Effect.</li><li>These Brush Pens are Ideal for Illustraions, Doodling, Calligraphy and much more.</li></ul></div></div></div>', 200.00, 184.00, 13.14, 'No.', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6a8c0e15d6f32.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 09:25:41', 10, 1, 0, NULL, NULL, NULL),
(282, NULL, 'FLAIR Glass Gel Pen Wallet Pack | Pack of 10', 'flair-glass-gel-pen-wallet-pack-pack-of-10', '-', '96081019', '<div><b>FLAIR Glass Gel Pen Wallet Pack | Pack of 10</b></div><div>Tip Size 0.6 mm | Water Proof Ink For Smooth Flow System | Low-Viscosity Ink For Smudge Free Writing | Blue ink</div><div><ul><li><b>Brand: </b>Flair</li><li><b>Writing instrument form:</b> Roller Ball Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour:</b> Blue</li><li><b>Age Range (Description):</b> Adult</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Package Includes - Flair Glass Gel Pen, Tip Size - 0.6 mm, Material - Plastic, Blue Ink, Packaging - Wallet Pack of 10 Pens</li><li>Flair Glass Gel Pen Gives You The Smooth Writing Experience For Comfortable Writing.</li><li>Writes Great Without Skipping, Smearing &amp; Stuttering.</li><li>Ergonomic Grip For Easy Handling of The Pen.</li><li>Ideal For School, College &amp; Office. Ideal for Writing, Journaling, Note-Taking &amp; Underlining.</li></ul></div></div>', 75.00, 68.00, 7.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a8c12c2b0451.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 09:45:38', 10, 21, 0, NULL, NULL, NULL),
(283, NULL, 'FLAIR Move Gel Pen | Set Of 10', 'flair-move-gel-pen-set-of-10', '-', '96081019', '<div><b>FLAIR Move Gel Pen | Set Of 10</b></div><div>Minimalistic &amp; Aesthetic Matt Finish Pastel Body Colors | Effortless Writing With Sleek Clip Design | Blue Ink</div><div><ul><li><b>Brand: </b>Flair</li><li><b>Writing instrument form: </b>Gel Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour: </b>Blue</li><li><b>Age Range (Description): </b>Adult</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Package Includes - Flair Move Gel Pen, Material - Plastic, Ink Color - Blue, 10 Pieces.</li><li>Flair Move Gel Pen Gives You The Smooth Writing Experience For Comfortable Writing.</li><li>Ergonomic Grip For Easy Handling Of The Pen.</li><li>Writes Great Without Skipping, Smearing &amp; Stuttering.</li><li>Ideal For School, College &amp; Office. Perfect for Writing, Journaling, Note-Taking &amp; Underlining.</li></ul></div></div>', 111.00, 100.00, 10.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a8c1426430db.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 09:51:34', 10, 20, 0, NULL, NULL, NULL),
(284, NULL, 'Fevicryl Acrylic Colours Sunflower Kit | 10 Colours x 15 ml', 'fevicryl-acrylic-colours-sunflower-kit-10-colours-x-15-ml', '-', '32139000', '<div><div><b>Fevicryl Acrylic Colours Sunflower Kit | 10 Colours x 15 ml</b></div><div>DIY Paint, Rich Pigment | Multicolour &amp; Multi-surface | Non-Cracking Paint for Canvas, Wood, Leather, Earthenware, Metal</div></div><div><ul><li><b>Brand: </b>Fevicryl</li><li><b>Colour: </b>Multicolor</li><li><b>Finish Type: </b>Gloss</li><li><b>Size: </b>150 ml (Pack of 1)</li><li><b>Item Volume: </b>150 Millilitres</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>🎨10 vibrant shades: The Sunflower Acrylic Kit has acrylic colours for creating art and tones that bring out your artistic mood. It lets artists give depth and dimension to their creations.</li><li>🎨Multi surface Use: The kit comprises of refined shades that work across a wide variety of mediums like wood, canvas, leather, terracotta and even fabrics.</li><li>🎨Beginner-Friendly: You don’t have to be an expert artist to use this. Grab a piece of cloth or paper, set up your colouring palette and get ready to paint</li><li>🎨Clean finish: These colours emit an impressive sheen &amp; lustre once painted, along with a long-lasting stay. They are wash-proof and can be used without a binder or medium</li></ul></div></div>', 246.00, 221.00, 22.10, 'No.', 10, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6ab20caff11c0.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 09:57:59', 10, 3, 0, NULL, NULL, NULL),
(285, NULL, '100 Pieces Colour Sheets | A4 Sheets | 10 Sheets each color', '100-pieces-colour-sheets-a4-sheets-10-sheets-each-color', '-', '-', '<div><b>100 Pieces Colour Sheets | A4 Sheets | 10 Sheets each color</b></div><div>Copy Printing Papers/Art and Craft Paper Double Sided Coloured Origami Folding DIY Craft Smooth Finish Home, School, Office Stationery</div><div><div id=\"twister_feature_div\" class=\"celwidget\" data-feature-name=\"twister\" data-csa-c-type=\"widget\" data-csa-c-content-id=\"twister\" data-csa-c-slot-id=\"twister_feature_div\" data-csa-c-asin=\"B07SCZB24S\" data-csa-c-is-in-initial-active-row=\"false\" data-csa-c-id=\"qiry14-oi4myu-xeqm7y-15f0l3\" data-cel-widget=\"twister_feature_div\" style=\"\"><div id=\"twister-plus-desktop-twister-container\" data-csa-c-content-id=\"twister-plus-inline-twister-container\" data-csa-c-slot-id=\"twister-plus-inline-twister-container\" data-csa-c-type=\"slot\" class=\"a-cardui-deck inline-twister-container-margins\" name=\"a-cardui-deck-autoname-1\" data-csa-c-id=\"smp65y-d7c8ez-bikh19-eeu44r\" style=\"background-color: transparent; padding-top: 0.1px; padding-bottom: 0.1px; margin: 0px;\"><div id=\"twister-plus-inline-twister-card\" class=\"a-cardui inline-twister-card-padding\" data-a-card-type=\"basic\" name=\"a-cardui-deck-autoname-1-card0\" style=\"margin: 0px; background-color: var(--__dChNmAmGoMXsPxT,#fff); overflow: visible; padding: 0px;\"><div class=\"a-cardui-body\" style=\"padding: 0px; position: relative;\"><div id=\"twister-plus-inline-twister\" class=\"a-section\" style=\"margin-bottom: 0px;\"><div id=\"inline-twister-singleton-header-pattern_name\" class=\"a-section a-spacing-none inline-twister-singleton-header\" style=\"color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px; margin-bottom: 0px; padding: 4px 0px;\"><b><span class=\"a-size-base a-color-secondary inline-twister-dim-title\" style=\"line-height: 20px !important; color: var(--__dChNmAwPaMXsw4B,#565959)  !important;\">Pattern Name:&nbsp;</span><span id=\"inline-twister-expanded-dimension-text-pattern_name\" class=\"a-size-base inline-twister-dim-title-value-truncate a-text-bold\" style=\"line-height: 20px !important;\">Color Sheet</span></b></div><div id=\"inline-twister-singleton-header-pattern_name\" class=\"a-section a-spacing-none inline-twister-singleton-header\" style=\"color: rgb(15, 17, 17); font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px; margin-bottom: 0px; padding: 4px 0px;\"><span class=\"a-size-base inline-twister-dim-title-value-truncate a-text-bold\" style=\"line-height: 20px !important;\"><div id=\"inline-twister-singleton-header-pattern_name\" class=\"a-section a-spacing-none inline-twister-singleton-header\" style=\"font-weight: bold; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; margin-bottom: 0px; padding: 4px 0px;\">About this item:</div><div id=\"inline-twister-singleton-header-pattern_name\" class=\"a-section a-spacing-none inline-twister-singleton-header\" style=\"font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; margin-bottom: 0px; padding: 4px 0px;\"><ul style=\"\"><li style=\"\">Can be used for making card, menu, memo, craft, advertisement,etc</li><li style=\"\">Many kinds of color can be for select, for you of office on a silver platter</li><li style=\"\">Paper smooth bright, color harmonious bright</li><li style=\"\">Come with 100 individual sheets, one color one sheet</li><li style=\"\">Country of Origin: India</li></ul></div></span></div></div></div></div></div></div></div>', 165.00, 148.00, 15.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6a8c28970c621.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 10:16:52', 10, 5, 0, NULL, NULL, NULL),
(286, NULL, 'FLAIR Pastel 5 Shades Hi-lighter | Pack of 1', 'flair-pastel-5-shades-hi-lighter-pack-of-1', '-', '96082000', '<div><b>FLAIR Pastel 5 Shades Hi-lighter | Pack of 1</b></div><div>Pouch Pack | Flexible Line Width | Quick Drying &amp; Smudge Proof Pastel Ink | Non-Toxic Ink, Safe For Childrens | 5 Smoothing Colors</div><div><ul><li><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><b>Brand: </b>Flair</span></font></li><li><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><b>Writing instrument form: </b>Marker Pen</span></font></li><li><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><b>Colour: </b>Multicolor</span></font></li><li><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><b>Ink Colour: </b>Multicolor</span></font></li><li><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><b>Age Range (Description):</b> Adult</span></font></li></ul><div><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><b>About this item:</b></span></font></div></div><div><font color=\"#0f1111\"><span style=\"font-size: 14px;\"><div style=\"\"><ul style=\"\"><li style=\"\">Package Includes : Flair Pastel 5 Shades Hi-lighter | Flexible Line Width | Quick Drying &amp; Smudge Proof Pastel Ink | Non-Toxic Ink, Safe For Childrens | 5 Smoothing Colors, Pack of 1</li><li style=\"\">Flair Pastel 5 Shades Highlighter Gives You The Smooth Writing Experience For Comfortable Writing.</li><li style=\"\">Pocket Clip For Convenience Of Carrying.</li><li style=\"\">Its Non-Toxic Ink Is Ideal For Writing On Normal Or Special Paper.</li><li style=\"\">Ideal For School, College &amp; Office. Perfect for Writing, Journaling, Note-Taking &amp; Underlining.</li></ul></div></span></font></div><div></div>', 139.00, 125.00, 25.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a8c1ef7a3014.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 10:37:43', 10, 1, 0, NULL, NULL, NULL),
(287, NULL, 'Hauser XO Multicolor Gel Pen | Set Of 12', 'hauser-xo-multicolor-gel-pen-set-of-12', '-', '96081019', '<div><b>Hauser XO Multicolor Gel Pen with Hard Box Case | Set Of 12 Bright Ink Colors</b></div><div>Sleek Body &amp; Minimalistic Design | Matt Finish | Ultra Durable Tip</div><div><ul><li><b>Brand: </b>Hauser</li><li><b>Writing instrument form: </b>Roller Ball Pen</li><li><b>Colour: </b>Multicolor Ink</li><li><b>Ink Colour: </b>Multicolor</li><li><b>Age Range (Description):</b> Adult</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Package Includes - XO Multicolor Gel Pen with Hard Box Case | Pack of 12 Pens.</li><li>The smooth-flowing precision fine tip will deliver consistent and crisp lines with minimal bleed-through. They are great for everyday school and office writing jobs and your art and craft projects as well. Just let your creativity sparkle and shine!</li><li>The sleek stick pen body is designed to give you a good grip and will fit perfectly in your hand, making writing and drawing easy and comfortable. The large cartridges and durable tips will stand up to the most demanding user and ambitious projects.</li><li>Our pens are crafted with materials that are safe and non-toxic, making them ideal for children.</li><li>These gel pens are versatile and suitable for use in various settings, including journals, diaries, sketchbooks, artwork, writing, school projects, office documents, and DIY projects. They are also perfect gifts for kids on birthdays or special occasions. Whether you are a student, working from home, or in an office, these pens offer limitless possibilities for inspiration and creativity in your writing and artwork.</li></ul></div></div>', 166.00, 150.00, 13.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a8c20328d852.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 10:42:58', 10, 22, 0, NULL, NULL, NULL),
(288, NULL, 'DOMS 50 Shades Oil Pastel With Case | Pack Of 1 | Multi color', 'doms-50-shades-oil-pastel-with-case-pack-of-1-multi-color', '-', '32139000', '<div><b>DOMS 50 Shades Oil Pastel With Case | Pack Of 1 |&nbsp;</b><span style=\"text-align: var(--bs-body-text-align);\"><b>Multi color</b></span></div><div>Smooth Color Intermixing For Better Effect | Bright&amp;Intense Colors | Free Scrapping Tool | Non-Toxic &amp; Safe For Children</div><div><ul><li><b>Brand: </b>Doms</li><li><b>Writing instrument form: </b>Pastel Stick</li><li><b>Colour: </b>Multicolour</li><li><b>Ink Colour: </b>Multicolor</li><li><b>Age Range (Description): </b>Adult</li></ul><div><div><b>About this item:</b></div><div><ul><li>Bright colours</li><li>Smooth colour laydown</li><li>Scrapper tool for effective colouring techniques</li><li>Even colouring texture</li><li>Ideal for intermixing, shading, highlighting and texturing</li><li>Country of Origin: India</li></ul></div></div></div>', 235.00, 211.00, 4.22, 'No.', 10, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6ab20c6a47ce9.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 10:52:07', 10, 2, 0, NULL, NULL, NULL),
(289, NULL, 'Fevicryl Acrylic Colour White 500ml', 'fevicryl-acrylic-colour-white-500ml', '-', '32139000', '<div><b>Fevicryl Acrylic Colour White 500ml</b></div><div>Art &amp; Craft, DIY, Rich Pigment, Non-Craking Paint for Canvas, Wood, Leather, Earthenware, Metal | Ideal for school projects, Hobbyists, Artists</div><div><div><ul><li><b>Brand: </b>Fevicryl</li><li><b>Colour: </b>White</li><li><b>Finish Type: </b>Gloss</li><li><b>Size: </b>500 ml (Pack of 1)</li><li><b>Item Volume: </b>500 Millilitres</li></ul><div><b>About this item:</b><br></div></div></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Fevicryl Acrylic Colour in a 500ml capacity.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">No medium or binder required &amp; these Fevicryl Acrylic Colours are versatile and can be used on multiple surfaces</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">They are waterproof even on porous substrates, these colours are long lasting</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">The colours have a lustre to them</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Non Toxic Fevicryl Acrylic Colour can be used on wood, MDF, terracotta/earthernware, metal, walls with stencilling work etc</span></li></ul></div>', 380.00, 342.00, 342.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6a8c23f7cf631.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 10:59:03', 10, 4, 0, NULL, NULL, NULL),
(290, NULL, 'Classmate Premium 6 Subject Notebook - Single Line | 203mm X 267mm | 300 Pages', 'classmate-premium-6-subject-notebook-single-line-203mm-x-267mm-300-pages', '-', '48202000', '<div><b>Classmate Premium 6 Subject Notebook -&nbsp;<span style=\"text-align: var(--bs-body-text-align);\">Single Line</span><span style=\"text-align: var(--bs-body-text-align);\">&nbsp;| 203mm x 267mm | 300 Pages</span></b></div><div><b>Soft Cover</b></div><div><br></div><div><ul><li><b>Brand: </b>Classmate</li><li><b>Colour: </b>Blue</li><li><b>Theme: </b>Plain</li><li><b>Sheet Size: </b>Government letter</li><li><b>Pages: </b>300</li></ul><div><div><b>About this item:</b></div><div><ul><li>The series comes in both lay flat and spiral binding with single subject and 6 subject options</li><li>Available in attractive cover designs</li><li>Notebook consists of high quality papers</li><li>The cover design of the notebook is subject to change, it depends on stock availability</li><li>Country of Origin: India</li></ul></div></div></div>', 145.00, 130.00, 130.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 59, 'uploads/products/6a8c2a5740d69.jpg', 98, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 11:26:15', 10, 1, 0, NULL, NULL, NULL),
(291, NULL, 'Oddy 180 GSM A4 Size Glossy Photo Paper | Pack of 50 Sheets', 'oddy-180-gsm-a4-size-glossy-photo-paper-pack-of-50-sheets', '-', '37031010', '<div><b>Oddy 180 GSM A4 Size Glossy Photo Paper | Pack of 50 Sheets</b></div><div>Water Proof, Instant Drying, , Compatible with Inkjet Printer</div><div><ul><li><b>Brand: </b>Oddy</li><li><b>Colour: </b>White</li><li><b>Item Weight: </b>500 Grams</li><li><b>Paper Finish: </b>Glossy</li><li><b>Sheet Size: </b>A4</li></ul><div><div><b>About this item:</b></div><div><ul><li>Pack of 50 Sheets, in A4 size. Compatible with Inkjet printer</li><li>Resin Coated paper for excellent gloss and smoothness, for image with high color density and balanced color gradation and image sharpness</li><li>Water resistant, acid &amp; lignin free.</li><li>Compatible with mostly all inkjet printers like Canon, Epson, HP etc.</li><li>Instant drying, ready to handle after print. No smearing or smudging.</li></ul></div></div><div><br></div></div>', 210.00, 189.00, 189.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 52, 'uploads/products/6a8c2be020ceb.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 11:32:48', 10, 0, 0, NULL, NULL, NULL),
(292, NULL, 'Classmate Pulse 6 Subject Notebook | 267mm*203mm | 300 Pages', 'classmate-pulse-6-subject-notebook-267mm-203mm-300-pages', '-', '48202000', '<div><b>Classmate Pulse 6 Subject Notebook | 267mm*203mm | 300 Pages</b></div><div>Unruled, Spiral Binding</div><div><br></div><div><ul><li><b>Pattern Name:</b> Notebook</li><li><b>Brand: </b>Classmate</li><li><b>Colour:</b> Assorted</li><li><b>Theme:</b> Plain</li><li><b>Sheet Size: </b>26.7 x 20.3 centimetres</li><li><b>Pages:</b> 300</li></ul><div><div><b>About this item:</b></div><div><ul><li>The cover design of the notebook is subject to change, it depends on stock availability</li><li>6 Subject Notebook-Unruled, 300 Pages, Spiral Binding, Soft Cover, 267mm*203mm</li><li>Six subjects! One notebook! Only the best from Classmate</li><li>Classmate uses eco friendly and elemental chlorine free paper</li><li>This notebook consists of papers</li></ul></div></div></div>', 173.00, 155.00, 155.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 59, 'uploads/products/6a8c2d494f862.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 11:38:49', 10, 2, 0, NULL, NULL, NULL),
(293, NULL, 'Luxor 5 Subject Single Ruled Notebook | A5 Size | 300 Pages | 70 GSM', 'luxor-5-subject-single-ruled-notebook-a5-size-300-pages-70-gsm', '-', '48202000', '<b>Luxor 5 Subject Single Ruled Notebook | A5 Size | 300 Pages | 70 GSM</b><div><b><br></b></div><div><div style=\"\"><ul><li><b>Brand: </b>Luxor</li><li><b>Colour: </b>Off-White</li><li><b>Theme: </b>Plain</li><li><b>Sheet Size: </b>A5</li><li><b>Pages: </b>300</li></ul><div><div><b>About this item:</b></div><div><ul><li>Perfect for capturing minutes of business meetings and professional notes with a clean, single-ruled format.</li><li>Boasts 300 pages for comprehensive note-taking, ensuring you never run out of space during important discussions.</li><li>Compact A5 size (14.0*21.6 cm) ensures it fits snugly in any briefcase or office bag.</li><li>Utilizes high-quality paper for a smooth writing experience, enhancing readability.</li><li>The sturdy cover adds a touch of professionalism and ensures longevity.</li></ul></div></div></div></div>', 188.00, 169.00, 169.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 59, 'uploads/products/6a8c2fe7eb240.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 11:49:59', 10, 3, 0, NULL, NULL, NULL),
(294, NULL, 'Classmate Pulse Long Book Ruled Notebook | 29.7x21cm | 180 Pages | Pack of 3', 'classmate-pulse-long-book-ruled-notebook-29-7x21cm-180-pages-pack-of-3', '-', '48202000', '<b>Classmate Pulse Long Book Ruled Notebook | 29.7x21cm | 180 Pages |&nbsp;Pack of 3</b><div>Single Line Wiro Binding - Premium Paper Quality for Students Note Taking Assignments<br></div><div><br></div><div><ul><li><b>Brand: </b>Classmate</li><li><b>Colour:</b> Multi</li><li><b>Theme:</b> Plain</li><li><b>Sheet Size:</b> 21 x 29.7 centimetres</li><li><b>Pages:</b>&nbsp;180 Pages</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">SMOOTH AND THICK PAGES — Premium quality bright white paper ensures clear writing with minimal ink bleed, perfect for detailed note taking and assignments that require neat, organized presentation.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">VALUE FOR MONEY PACK — Get 3 ruled notebooks with 180 pages each, providing exceptional value for students who need reliable notebooks for multiple subjects and long-term academic use.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">NICE COVER DESIGNS — Attractive colorful covers that stand out in your stationery collection while maintaining professional appearance for classroom and exam use.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">DURABLE WIRO BINDING — Strong spiral binding construction ensures pages stay secure and allow easy flipping during intensive study sessions, assignments, and project work.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">LONG SIZE FORMAT — Generous 29.7cm x 21cm dimensions provide more writing space per page, ideal for students who need extra room for detailed notes, diagrams, and comprehensive exam preparation.</span></li></ul></div></div>', 295.00, 265.00, 88.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 59, 'uploads/products/6a8c311a128dc.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 11:55:06', 10, 4, 0, NULL, NULL, NULL),
(295, NULL, 'Mario Mini Transparent 7mm Hot Melt Glue Sticks for 20W Glue Gun | 25 Pcs', 'mario-mini-transparent-7mm-hot-melt-glue-sticks-for-20w-glue-gun-25-pcs', '-', '-', '<b>Mario Mini Transparent 7mm Hot Melt Glue Sticks for 20W Glue Gun | 25 Pcs<br></b><span style=\"text-align: var(--bs-body-text-align);\">Strong Adhesive for DIY, Art &amp; Craft, Repairs | Mini Glue Gun Sticks</span><div><ul><li><b>Brand:</b> Mario</li><li><b>Specific Uses For Product:</b> Craft, Lace, Model, School</li><li><b>Material:</b> EVA Resin, Epoxy Resin</li><li><b>Item Form:</b> Stick</li><li><b>Special Feature:</b> Dries Clear, Fast Drying, Strong</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">🔥 FAST MELTING MINI GLUE STICKS FOR 20W GLUE GUN – 7mm transparent hot melt glue sticks engineered specifically for 20W mini glue guns; heats up rapidly for consistent, smooth glue flow and precise application with zero clogging or mess.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">💎 CRYSTAL CLEAR STRONG ADHESIVE BOND – Premium quality transparent hot glue sticks that dry completely clear and invisible after application, delivering a neat, professional finish on paper, fabric, wood, plastic, glass, foam, and cardboard.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">🎨 IDEAL FOR DIY, ART &amp; CRAFT PROJECTS – Perfect for school craft projects, scrapbooking, gift wrapping, floral decoration, hobby work, home décor, jewellery making, and quick household repairs — compatible with all standard 7mm mini glue guns.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">🔫 JAM-FREE &amp; LOW DRIP PERFORMANCE – Advanced formula ensures even, uniform melting with minimal drip and clog-free operation for clean, controlled bonding; saves time and reduces glue wastage during detailed craft work.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">📦 ECONOMICAL VALUE PACKS FOR REGULAR USE – Available in Pack of 25, 30, 50 &amp; 100 sticks; long-lasting hot melt adhesive formula makes these mini glue sticks the most cost-effective bonding solution for home, school, and professional craft use.</span></li></ul></div></div>', 166.00, 149.00, 149.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 55, 'uploads/products/6a8c327f4de38.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 12:01:03', 10, 0, 0, NULL, NULL, NULL),
(296, NULL, 'Hauser XO Ball Pen Set | 20 Pens', 'hauser-xo-ball-pen-set-20-pens', '-', '96081019', '<b>Hauser XO Ball Pen Set</b><div>20 Pens&nbsp;in Storage Jar, Blue Black Red Assorted</div><div><div><ul><li><b>Brand:</b> Hauser</li><li><b>Writing instrument form:</b> Ballpoint Pen</li><li><b>Colour: </b>Blue/Black/Red/green</li><li><b>Ink Colour:</b> Blue</li><li><b>Age Range (Description): </b>Adult</li></ul><div><b>About this item:</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">PACK CONTENTS: Set of 20 ball pens in a convenient storage jar - 16 blue pens, 3 black pens, and 1 red pen for varied writing needs</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">STORAGE SOLUTION: Transparent jar with light blue cap keeps pens organised and easily accessible while protecting from dust</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">DESIGN: Sleek and professional-looking ball point pens perfect for everyday writing at home, office, or school</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">CONVENIENCE: Portable jar design allows for easy transport and storage of multiple pens in one compact container</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">VERSATILITY: Assorted colours suitable for different applications - blue for regular use, black for formal documents, and red for corrections</span></li></ul></div></div><div><br></div></div>', 220.00, 198.00, 99.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a8c339e65ed5.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-24 12:05:50', 10, 23, 0, NULL, NULL, NULL),
(297, NULL, 'Parker Vector Standard Fountain Pen', 'parker-vector-standard-fountain-pen', '-', '96081019', '<div><b>Parker Vector Standard Fountain Pen</b></div><div><br></div><div><div><b>About this item</b></div><div><ul><li>A solid plastic body of the glossy black design, a contrasting stainless-steel clip, and a molded plastic barrel.</li><li>Parker Vector Standard Chrome Trim Ball Pen features a push mechanism closer and closes with a satisfying click.</li><li>Quinkflow technology for a smoother, cleaner &amp; more consistent writing performance, in a word the greatest Parker ballpoint ever.</li><li>Practical body shape is easy to hold for a comfortable writing experience</li><li>2 years of warrenty against manufacturing defects.</li></ul></div></div><div><br></div>', 400.00, 360.00, 360.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6ad9a51d88553.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 05:41:39', 10, 1, 0, NULL, NULL, NULL),
(298, NULL, 'SSKR Bell Nail Cutter Finger Nail Cutter', 'sskr-bell-nail-cutter-finger-nail-cutter', '1234567', '-', '<div><b>SSKR Bell Nail Cutter Finger Nail Cutter&nbsp;</b></div><div>for Thick and Ingrown Toenails Stainless Steel for Men\'s and Women (Multicolor)</div><div><br></div><div><div><ul><li><b>Colour: </b>Multicolour</li><li><b>Brand: </b>SSKR</li><li><b>Blade Edge: </b>Straight</li><li><b>Blade Material: </b>Stainless Steel</li><li><b>Target Audience: </b>Unisex Adult</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">High-quality stainless steel construction for durability and longevity</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Designed specifically for thick and ingrown toenails</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Ergonomic handle for comfortable and precise trimming</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Sharp and sturdy blades for easy and efficient cutting</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Suitable for both men and women</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Helps maintain proper nail hygiene and prevent nail-related issues</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Compact size for easy storage and travel convenience</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Versatile tool for maintaining healthy and well-groomed nails</span></li></ul></div></div></div>', 182.00, 164.00, 164.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 60, 'uploads/products/6a8d2ebad36f3.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 05:56:27', 10, 0, 0, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `seller_id`, `name`, `slug`, `sku`, `hsn`, `description`, `mrp`, `selling_price`, `pay_per_unit`, `unit_label`, `discount_percentage`, `gst_type`, `gst_rate`, `shipping_charge`, `category_id`, `main_image`, `stock_quantity`, `package_quantity`, `is_active`, `is_approved`, `approved_at`, `approved_by`, `rejection_reason`, `is_featured`, `is_discounted`, `created_at`, `max_quantity_per_order`, `sort_order`, `has_variations`, `brand_id`, `seller_name`, `seller_code`) VALUES
(299, NULL, 'MAPLE Original Bell Nail Cutter Clipper with Nail File | Set of 2', 'maple-original-bell-nail-cutter-clipper-with-nail-file-set-of-2', '1234567', '-', '<b>MAPLE Original Bell Nail Cutter Clipper with Nail File | Set of 2</b><div>Made in Korea<br></div><div><br></div><div><ul><li><b>Colour: </b>Assorted</li><li><b>Brand: </b>MAPLE</li><li><b>Material: </b>Stainless Steel</li><li><b>Blade Edge: </b>Straight</li><li><b>Blade Material:</b> Stainless Steel</li><li><b>Target Audience:</b> Men</li></ul><div><br></div></div><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Premium Stainless Steel - Bell nail clipper set is made of high-quality stainless steel, which means they are very sturdy and durable. Moreover, the tempered stainless steel edge makes our nail cutter Kit are extremely rust prevention.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Wide Nail Clippers: Made of premium stainless steel, rust resistant &amp; does not break. 180 degree wide jaw opening nail clippers for thick nails up to 15mm is suitable for thick fingernails and tough toenails.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Nail Cutter Set for Men Sharpe Blades: This thick toenail clipper with sharp blades cuts smoothly, trims nails with precision. The nail file makes the nail surface smoother.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Nail Cutter Ergonomics: Perfect wide wing and non-slip handle is reinforced for extra strength and the large toenail clipper is very comfortable no matter how you clip thick nails.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Durable Nail Clippers: Strong spring provides long-lasting use. With enough spring resistance, the little piece in the middle allows the toenail trimmer to open and close easily.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Package Quantity : Bell Original Nail Cutter ( Pack of 2 Pcs )</span></li></ul></div>', 300.00, 269.00, 135.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 60, 'uploads/products/6a8d30b015619.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 06:02:15', 10, 0, 0, NULL, NULL, NULL),
(300, NULL, 'Camlin White Board Marker Pen | Green | Pack of 10', 'camlin-white-board-marker-pen-green-pack-of-10', '-', '96082000', '<b>Camlin White Board Marker Pen | Green | Pack of 10</b><div><div style=\"\"><ul><li><b>Brand: </b>Camlin</li><li><b>Ink Colour:</b> Green</li><li><b>Number of Items: </b>10</li><li><b>Point Type:</b> Broad</li><li><b>Recommended Uses For Product:</b> Writing</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>Refillable, Bright Ink, Easy to erase with duster, tissue and cloth</li><li>CE Certified</li><li>Bright Ink for better visibility</li><li>USP: Available in - 4 Colours (Red, Blue, Green and Red)</li></ul></div></div></div></div>', 256.00, 230.00, 23.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a8d356a7082f.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 06:18:05', 10, 8, 0, NULL, NULL, NULL),
(301, NULL, 'Camlin White Board Marker Pen | Blue | Pack of 10', 'camlin-white-board-marker-pen-blue-pack-of-10', '-', '96082000', '<span style=\"font-weight: bolder;\">Camlin White Board Marker Pen | Blue | Pack of 10</span><div>Fine Point Type<div><ul><li><span style=\"font-weight: bolder;\">Brand:&nbsp;</span>Camlin</li><li><span style=\"font-weight: bolder;\">Ink Colour:</span>&nbsp;Blue</li><li><span style=\"font-weight: bolder;\">Number of Items:&nbsp;</span>10</li><li><span style=\"font-weight: bolder;\">Point Type:</span>&nbsp;Broad</li><li><span style=\"font-weight: bolder;\">Recommended Uses For Product:</span>&nbsp;Writing</li></ul><div><b>About this item:</b></div></div></div><div><div style=\"\"><ul><li>Refillable, Bright Ink, Easy to erase with duster, tissue and cloth</li><li>CE Certified</li><li>Bright Ink for better visibility</li><li>USP: Available in - 4 Colours (Red, Blue, Green and Red)</li></ul></div></div>', 156.00, 140.00, 14.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a8d34ac284c8.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 06:22:36', 10, 7, 0, NULL, NULL, NULL),
(302, NULL, 'Camlin White Board Marker Pen | Black | Pack of 10', 'camlin-white-board-marker-pen-black-pack-of-10', '-', '96082000', '<span style=\"font-weight: bolder;\">Camlin White Board Marker Pen | Black | Pack of 10</span><div>Fine Point Type<div><ul><li><span style=\"font-weight: bolder;\">Brand:&nbsp;</span>Camlin</li><li><span style=\"font-weight: bolder;\">Ink Colour:</span>&nbsp;Black</li><li><span style=\"font-weight: bolder;\">Number of Items:&nbsp;</span>10</li><li><span style=\"font-weight: bolder;\">Point Type:</span>&nbsp;Broad</li><li><span style=\"font-weight: bolder;\">Recommended Uses For Product:</span>&nbsp;Writing</li></ul><div><b>About this item:</b></div></div></div><div><div style=\"\"><ul><li>Refillable, Bright Ink, Easy to erase with duster, tissue and cloth</li><li>CE Certified</li><li>Bright Ink for better visibility</li><li>USP: Available in - 4 Colours (Red, Blue, Green and Red)</li></ul></div></div>', 182.00, 164.00, 16.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a8d365091116.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 06:29:36', 10, 6, 0, NULL, NULL, NULL),
(303, NULL, 'Camlin White Board Marker Pen | Red | Pack of 10', 'camlin-white-board-marker-pen-red-pack-of-10', '-', '96082000', '<span style=\"font-weight: bolder;\">Camlin White Board Marker Pen | Red | Pack of 10</span><div>Fine Point Type<div><ul><li><span style=\"font-weight: bolder;\">Brand:&nbsp;</span>Camlin</li><li><span style=\"font-weight: bolder;\">Ink Colour:</span>&nbsp;Red</li><li><span style=\"font-weight: bolder;\">Number of Items:&nbsp;</span>10</li><li><span style=\"font-weight: bolder;\">Point Type:</span>&nbsp;Broad</li><li><span style=\"font-weight: bolder;\">Recommended Uses For Product:</span>&nbsp;Writing</li></ul><div><b>About this item:</b></div></div></div><div><div style=\"\"><ul><li>Refillable, Bright Ink, Easy to erase with duster, tissue and cloth</li><li>CE Certified</li><li>Bright Ink for better visibility</li><li>USP: Available in - 4 Colours (Red, Blue, Green and Red)</li></ul></div></div>', 226.00, 203.00, 20.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a8d37238b999.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-25 06:32:04', 10, 5, 0, NULL, NULL, NULL),
(304, NULL, 'Eclet Durable Soft Sewing Tailor Tape | 1.50 Meter | 150 cm', 'eclet-durable-soft-sewing-tailor-tape-1-50-meter-150-cm', '1234567', '-', '<b>Eclet Durable Soft Sewing Tailor Tape | 1.50 Meter | 150 cm</b><div>Body Measuring Dress-Making Measure Ruler, Multicolour(I)<br></div><div><br></div><div><div><ul><li><b>Colour:</b> Multicolour</li><li><b>Brand: </b>ECLET</li><li><b>Item Weight:</b> 50 Grams</li><li><b>Measurement Accuracy:</b> ±1/16 inch per 10 feet</li><li><b>Case Material Type:</b> Plastic</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Body Measuring</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Dress-Making Measure</span></li></ul></div></div></div>', 79.00, 69.00, 69.00, 'No.', 13, 'sgst_cgst', 18.00, NULL, 61, 'uploads/products/6a8fc4dc10f1b.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 05:02:20', 10, 0, 0, NULL, NULL, NULL),
(305, NULL, 'Mor Chaap Premium 9 Inch Heavy Duty Professional Tailor Scissors', 'mor-chaap-premium-9-inch-heavy-duty-professional-tailor-scissors', '1234567', '-', '<div><b>Mor Chaap Premium 9 Inch Heavy Duty Professional Tailor Scissors</b></div><div>Forged Steel Fabric Cutting Shears for Tailoring, Sewing, Dress Making, Leather, Denim, Canvas, Upholstery | Made in India</div><div><br></div><div><div><ul><li><b>Colour: </b>Gold</li><li><b>Material: </b>Alloy Steel, High Carbon Steel</li><li><b>Brand: </b>Generic</li><li><b>Product Dimensions: </b>25L x 5W Centimeters</li><li><b>Style: </b>Classic</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✅ Mor Chaap Premium Professional Tailor Scissors Designed for professional tailors, boutiques, fashion designers and garment manufacturers who require outstanding cutting performance and dependable quality every day.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✅ Heavy Duty Forged Steel Construction Premium forged steel body offers exceptional strength, superior balance and increased cutting power for smooth, controlled and accurate fabric cutting.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✅ Razor Sharp Long Lasting Blades Precision-ground blades remain sharper for longer and provide effortless cutting with clean edges and minimal fabric fraying.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✅ Perfect for Multiple Materials Effortlessly cuts Cotton, Silk, Linen, Denim, Canvas, Leather, Polyester, Upholstery Fabric, Uniform Fabric, Felt, Quilting Material and multiple fabric layers.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✅ Built for Long Working Hours The balanced heavy-duty design minimizes hand fatigue while providing excellent comfort, stability and control for continuous professional use.</span></li></ul></div></div></div>', 555.00, 499.00, 499.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 61, 'uploads/products/6a8fc5ef2a3fa.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 05:06:55', 10, 0, 0, NULL, NULL, NULL),
(306, NULL, 'Quilting Needle Set | 30 PCs', 'quilting-needle-set-30-pcs', '1234567', '-', '<div><b>Quilting Needle Set | 30 PCs</b></div><div>Self Threading Quilting Needle with Wooden Case | Embroidery Needles Threader Inserter Set | Stitching Clothes Hand Sewing Needle Set</div><div><br></div><div><div><ul><li><b>Material: </b>Stainless Steel, Wood Case</li><li><b>Brand:</b> Generic</li><li><b>Colour:</b> Multicolor</li><li><b>Point Type: </b>Sharp</li><li><b>Recommended Uses For Product: </b>Quilting, Embroidery, Hand Sewing</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">SELF-THREADING QUILTING NEEDLE: Including 30pcs long quilting needle of various sizes, meets your various sewing needs. Specially desined self threading quilting needle features with an opening at the top that allows for easier threading, saving the trouble of tricky threading. Great options for people who have difficulty threading traditional needles.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">PREMIUM AND WELL MADE: These quilting needles are made of premium metal with fine nickeling process to ensure their durability and strength for quilting projects. The fine nickeling process also creates a smooth surface on the needles, reducing friction with the fabric and ensuring smooth stitching. The needles are well-made and carefully crafted to achieve precision and accuracy when sewing through multiple layers of thick quilting fabric.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">VERSATILE USAGE: Self threading quilting needle is designed for helping people with an efficient and easy quilting job on various sewing projects. These self threading quilting needle can be used for quilting, embroidery, hand sewing, etc.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">EASY TO USE: To use a self-threading quilting needle, simply insert the thread through the opening at the top of the needle and pull it all the way through. This needle can save time and reduce eye strain, making quilting and sewing easier for beginners or those with vision problems.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">EXQUISITE WOODEN NEEDLE CASE: Except the 30pcs quilting needles included, we also offer you an exquisite wooden needle case for carrying and storage. Smooth wooden surface and exquiste details add more aesthetic enjoyment in your tool box.</span></li></ul></div></div></div>', 166.00, 149.00, 149.00, 'Set', 10, 'sgst_cgst', 18.00, NULL, 61, 'uploads/products/6a8fd51c5fe16.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 06:11:40', 10, 0, 0, NULL, NULL, NULL),
(307, NULL, 'Colored Safety Pins | Pack of 50', 'colored-safety-pins-pack-of-50', '1234567', '-', '<div><b>Colored Safety Pins Set | Pack of 50</b></div><div>Multipurpose Colorful Safety Pin Pack for Home Office Personal Use | Strong Durable Metal Pins for Fabric Craft Sewing Travel Essentials</div><div><br></div><div><div><ul><li><b>Colour:</b> Multicolour</li><li><b>Material:</b> Metal</li><li><b>Brand: </b>SECURE FIRE</li><li><b>Item dimensions L x W x H: </b>50 x 50 x 30 Millimeters</li><li><b>Number of Pieces: </b>50</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Durable Metal Construction: Made from high-quality metal with smooth color coating for strength, rust resistance, and long-term usability.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Multipurpose Utility: Ideal for sewing, tailoring, organizing fabrics, securing papers, crafts, and general household or office use.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Secure Grip Design: Features a strong spring mechanism that holds fabric and material tightly without tearing or slipping.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Colorful Assorted Set: Includes multiple vibrant colors for easy identification, organization, and creative DIY applications.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Compact &amp; Portable Pack: Comes in a handy 50-piece set that’s lightweight, easy to store, and convenient for travel or everyday use.</span></li></ul></div></div></div>', 154.00, 138.00, 138.00, 'Set', 10, 'sgst_cgst', 18.00, NULL, 62, 'uploads/products/6a8fd8d4c4be4.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 06:27:32', 10, 1, 0, NULL, NULL, NULL),
(308, NULL, 'SAFETY PINS | 1 inch | Pack of 10', 'safety-pins-1-inch-pack-of-10', '1234567', '-', '<div><b>SAFETY PINS | 1 inch | Pack of 10</b><br></div><div>Rust-Resistant Nickel Plated Steel, Sharp Edge Pins for Clothes, Sewing, Arts &amp; Craft</div><div><br></div><div><ul><li><span style=\"text-align: var(--bs-body-text-align);\"><b>Colour: </b>Silver</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><b>Material:</b>&nbsp;</span><span style=\"text-align: var(--bs-body-text-align);\">Metal</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><b>Brand: </b>Generic</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><b>Item dimensions:</b> 1 Inch</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><b>Number of Pieces:</b> 10</span></li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ 5 Different Sizes of Heavy Duty Safety Pins: We want to ensure your creative work, sewing, crafting, dress designing and more is easier than ever before, which is why we’ve included big safety pins, small safety pins, and mini safety pins in a convenient reusable storage box</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ The Ultimate Safety Pin Assortment: Our durable and rust proof safety pin assorted kit comes in 5 various sizes: (30mm) 100-count, (35mm) 80-count, (40mm) 60-count, (44mm) 40-count and (55mm) 20-count. Our large and medium safety pins is the most versatile option available today</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Durable Safety Pins for Clothes, Projects: We went ahead and made reinforced nickel-plated steel pins that will stand the test of time. Plus, they are sharp and won’t get bent or rust easily. The standard for our large safety pins with sturdy design will ensure lifelong use for all your multiple needs</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Made With Safety in Mind: We want to keep you safe, which is why our mixed size safety pins pack will protect your fingers from sharp marks and pricks. Get creative, complete small tasks, and work around the house with a convenient pin safety clasp</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Perfect Tiny Gift for Everyday Use: These baby safety pins with back are something everyone can use, which is why they are a perfect tiny present for a crafter, traveler, artist, or a parent. They’re safe, versatile, and perfect for stitching, patching, clothing, quilting or any around-the-house project</span></li></ul></div></div>', 11.00, 10.00, 1.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 62, 'uploads/products/6a8fdc9abb980.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 06:43:38', 10, 2, 0, NULL, NULL, NULL),
(309, NULL, 'SAFETY PINS | 2 inch | Pack of 10', 'safety-pins-2-inch-pack-of-10', '1234567', '-', '<div><b>SAFETY PINS | 2 inch | Pack of 10</b><br></div><div>Rust-Resistant Nickel Plated Steel, Sharp Edge Pins for Clothes, Sewing, Arts &amp; Craft</div><div><br></div><div><ul><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Silver</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Material:</span>&nbsp;</span><span style=\"text-align: var(--bs-body-text-align);\">Metal</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Brand:&nbsp;</span>Generic</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Item dimensions:</span>&nbsp;2 Inch</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Number of Pieces:</span>&nbsp;10</span></li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"margin-bottom: 0px; color: rgb(15, 17, 17); padding: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; line-height: 24px !important; font-size: 1.25rem !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 18px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; color: rgb(15, 17, 17); font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ 5 Different Sizes of Heavy Duty Safety Pins: We want to ensure your creative work, sewing, crafting, dress designing and more is easier than ever before, which is why we’ve included big safety pins, small safety pins, and mini safety pins in a convenient reusable storage box</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ The Ultimate Safety Pin Assortment: Our durable and rust proof safety pin assorted kit comes in 5 various sizes: (30mm) 100-count, (35mm) 80-count, (40mm) 60-count, (44mm) 40-count and (55mm) 20-count. Our large and medium safety pins is the most versatile option available today</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Durable Safety Pins for Clothes, Projects: We went ahead and made reinforced nickel-plated steel pins that will stand the test of time. Plus, they are sharp and won’t get bent or rust easily. The standard for our large safety pins with sturdy design will ensure lifelong use for all your multiple needs</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Made With Safety in Mind: We want to keep you safe, which is why our mixed size safety pins pack will protect your fingers from sharp marks and pricks. Get creative, complete small tasks, and work around the house with a convenient pin safety clasp</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Perfect Tiny Gift for Everyday Use: These baby safety pins with back are something everyone can use, which is why they are a perfect tiny present for a crafter, traveler, artist, or a parent. They’re safe, versatile, and perfect for stitching, patching, clothing, quilting or any around-the-house project</span></li></ul></div></div>', 11.00, 10.00, 1.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 62, 'uploads/products/6a8fdd0a3c0d2.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 06:45:30', 10, 3, 0, NULL, NULL, NULL),
(310, NULL, 'SAFETY PINS | 3 inch | Pack of 10', 'safety-pins-3-inch-pack-of-10', '1234567', '-', '<div><span style=\"font-weight: bolder;\">SAFETY PINS | 3 inch | Pack of 10</span><br></div><div>Rust-Resistant Nickel Plated Steel, Sharp Edge Pins for Clothes, Sewing, Arts &amp; Craft</div><div><br></div><div><ul><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Silver</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Material:</span>&nbsp;</span><span style=\"text-align: var(--bs-body-text-align);\">Metal</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Brand:&nbsp;</span>Generic</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Item dimensions:</span>&nbsp;3 Inch</span></li><li><span style=\"text-align: var(--bs-body-text-align);\"><span style=\"font-weight: bolder;\">Number of Pieces:</span>&nbsp;10</span></li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"margin-bottom: 0px; color: rgb(15, 17, 17); padding: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; line-height: 24px !important; font-size: 1.25rem !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"padding: 0px; margin-right: 0px; margin-bottom: 0px; margin-left: 18px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; color: rgb(15, 17, 17); font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ 5 Different Sizes of Heavy Duty Safety Pins: We want to ensure your creative work, sewing, crafting, dress designing and more is easier than ever before, which is why we’ve included big safety pins, small safety pins, and mini safety pins in a convenient reusable storage box</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ The Ultimate Safety Pin Assortment: Our durable and rust proof safety pin assorted kit comes in 5 various sizes: (30mm) 100-count, (35mm) 80-count, (40mm) 60-count, (44mm) 40-count and (55mm) 20-count. Our large and medium safety pins is the most versatile option available today</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Durable Safety Pins for Clothes, Projects: We went ahead and made reinforced nickel-plated steel pins that will stand the test of time. Plus, they are sharp and won’t get bent or rust easily. The standard for our large safety pins with sturdy design will ensure lifelong use for all your multiple needs</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Made With Safety in Mind: We want to keep you safe, which is why our mixed size safety pins pack will protect your fingers from sharp marks and pricks. Get creative, complete small tasks, and work around the house with a convenient pin safety clasp</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">✓ Perfect Tiny Gift for Everyday Use: These baby safety pins with back are something everyone can use, which is why they are a perfect tiny present for a crafter, traveler, artist, or a parent. They’re safe, versatile, and perfect for stitching, patching, clothing, quilting or any around-the-house project</span></li></ul></div></div>', 11.00, 10.00, 1.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 62, 'uploads/products/6a8fdd5c83f16.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 06:46:52', 10, 4, 0, NULL, NULL, NULL),
(311, NULL, 'Lily Dressing Hair Combs For Women | Pack Of 3', 'lily-dressing-hair-combs-for-women-pack-of-3', '1234567', '-', '<b>Lily Dressing Hair Combs For Women | Pack Of 3</b><div><b><br></b></div><div><div style=\"\"><ul><li><b>Material: </b>Cellulose Acetate</li><li><b>Brand: </b>LILY</li><li><b>Size: </b>3 Count (Pack of 1)</li><li><b>Number of Items: </b>1</li><li><b>Net Quantity: </b>3 Count</li><li><b>Hair Type: </b>All</li><li><b>Manufacturer: </b>LILY INDIA PRIVATE LIMITED, 3C, Camac Street, Park Street area, Kolkata, West Bengal, India 700016, Lily</li><li><b>Colour: </b>Mutli-Color</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Nurtures the scalp and make hair smoother and shinier.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">The glossy finish and high transparency is aesthetically appealing.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Specially designed teeth are smooth on the scalp &amp; prevent tearing of hair.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">High Transparency &amp; Virgin Quality material makes the comb more hygienic.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Perfect for wet hair &amp; helps in removing knots and tangles.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">This unique collection is available in set of 3</span></li></ul></div></div></div>', 224.00, 201.00, 67.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 60, 'uploads/products/6a8fe060f2606.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-27 06:59:44', 10, 0, 0, NULL, NULL, NULL),
(312, NULL, 'Camlin Colour Pen Pencil | Multicolour', 'camlin-colour-pen-pencil-multicolour', '-', '96081019', '<b>Camlin Colour Pen Pencil | Multicolour</b><div>with Lead Tube</div><div><ul><li><b>Brand:</b> Camlin</li><li><b>Writing Instrument Form:</b> Multifunction Writing Instruments</li><li><b>Colour:</b> Multicolour</li><li><b>Ink Colour:</b> Multicolor</li><li><b>Age Range (Description): </b>Adult</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Indias First 2.0mm Colour Pen Pencil With Easy Click Mechanism</li><li>This Pack Contains 1 Pen Pencil 2.0mm Each With 12 Colour Leads Refill Tube</li><li>Grip For Comfortable Drawing With Nontoxic Leads</li><li>Its Simple Mechanism Allows You To Easily Refill Leads</li></ul></div></div>', 167.00, 150.00, 150.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a9260a3db95d.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 04:31:31', 10, 18, 0, NULL, NULL, NULL),
(313, NULL, 'Camlin & Camel Colour Pencil Set | Kokuyo | 24-Shade', 'camlin-camel-colour-pencil-set-kokuyo-24-shade', '1234567', '-', '<b>Camlin &amp; Camel Colour Pencil Set | Kokuyo | 24-Shade</b><div>Full Size Colour Pencil Set (Assorted)<br></div><div><br></div><div><ul><li><b>Brand: </b>Camlin</li><li><b>Writing Instrument Form:</b> Crayon</li><li><b>Age Range (Description):</b> Adult</li><li><b>Model Name:</b> Kokuyo 4192567</li><li><b>Body Shape: </b>Cylindrical</li></ul><div><b>About this item:</b></div></div><div><div><ul><li>Well formulated to provide bright, vibrant colours and are break resistant.</li><li>Softness of the lead is perfect for mixing and blending</li><li>Can be easily sharpened to a fine point to allow exquisite detail</li><li>Pack of 20 Assorted Colours</li><li>Confirms to safety standard EN 71 - 3</li><li>Best result can be seen on Drawing Paper, Sketching Papers, textured paper, smooth tinted paper or even on Canvas</li><li>Item Weight: 290 grams</li></ul></div></div>', 460.00, 413.00, 17.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 63, 'uploads/products/6a92643713753.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 04:46:47', 10, 0, 0, NULL, NULL, NULL),
(314, NULL, 'Faber-Castell Color Pencils | Assorted | 12 Count Carton', 'faber-castell-color-pencils-assorted-12-count-carton', '1234567', '-', '<b>Faber-Castell Color Pencils | Assorted | 12 Count Carton</b><div><div style=\"\"><ul><li><b>Brand: </b>Faber-Castell</li><li><b>Writing Instrument Form: </b>Colored Pencil</li><li><b>Colour: </b>Multicolored</li><li><b>Ink Colour:</b> Multicolor</li><li><b>Age Range (Description): </b>Adult</li></ul><div><b>About this item:</b></div><div><div><ul><li>Best grip triangular</li><li>Pigment rich leads for brilliant colouring</li><li>Special bond of leads provide break resistant and easy sharpening</li></ul></div></div></div></div>', 109.00, 98.00, 10.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 63, 'uploads/products/6a9265617effd.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 04:51:45', 10, 0, 0, NULL, NULL, NULL),
(315, NULL, 'Camel Plastic Crayons | 12 Shades', 'camel-plastic-crayons-12-shades', '-', '96099090', '<b>Camel Plastic Crayons | 12 Shades</b><div><div style=\"\"><ul><li>12 Assorted Shades</li><li>Confirms to safety standard EN 71 - 3</li><li>Camel art contest entry coupon inside the pack</li><li>Best result can be seen on Drawing Paper</li><li>Attractive and Durable Tin packing</li></ul></div></div>', 66.00, 59.00, 5.00, 'No.', 11, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6a9267d9cfc41.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 05:02:17', 10, 0, 0, NULL, NULL, NULL),
(316, NULL, 'Apsara Non Dust Erasers | Pack of 20', 'apsara-non-dust-erasers-pack-of-20', '-', '40169200', '<div><span style=\"font-weight: bolder;\">Apsara Non Dust Erasers | Pack of 20<br></span><div><ul><li><span style=\"font-weight: bolder;\">Brand: </span>Apsara</li><li><span style=\"font-weight: bolder;\">Colour: </span>White</li><li><span style=\"font-weight: bolder;\">Material; </span>Rubber</li><li><span style=\"font-weight: bolder;\">Product Dimensions: </span>33L x 17W Millimeters</li><li><span style=\"font-weight: bolder;\">Recommended Uses For Product:</span> Neat &amp; Clean Erasing</li></ul></div><span style=\"font-weight: bolder;\">About this item<br></span><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"padding: 0px; margin: 0px 0px 0px 18px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; color: rgb(15, 17, 17); font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\">Soft and smooth</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\">Erases graphite gently without damaging paper</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\">Picks up dust while erasing</span></li></ul></div><div><br style=\"color: rgb(33, 37, 41); font-size: 16px;\"></div>', 100.00, 90.00, 4.50, 'No.', 10, 'sgst_cgst', 18.00, NULL, 64, 'uploads/products/6a926ab745604.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 05:14:31', 10, 0, 0, 1, NULL, NULL),
(317, NULL, 'Camlin Kokuyo Exam Mathematical Drawing Instruments', 'camlin-kokuyo-exam-mathematical-drawing-instruments', '-', '73269099', '<b>Camlin Kokuyo Exam Mathematical Drawing Instruments</b><div><ul><li><b>Brand: </b>Camlin</li></ul><div><div><b>About this item:</b></div><div><ul><li>Also available Mini Mechanical pencil for compass; The plastic used in ruler, protractor and set square are made of high transparency plastic and comes with precise marking for easy reading and accurate drawings</li><li>The special technique of marking ensures that the marking are clearly visible after extensive use</li><li>Specially designed self centering compass, for ease and accuracy while drawing circles and angles</li><li>Both divider and compass are made with non-rusting strong material to last long and remain in shape and shine</li><li>Closure Type: Retractable</li></ul></div></div></div>', 117.00, 105.00, 105.00, 'Set', 10, 'sgst_cgst', 18.00, NULL, 65, 'uploads/products/6a926f3db7bf4.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 05:26:19', 10, 1, 0, NULL, NULL, NULL),
(318, NULL, 'Camlin Kokuyo Nouvel Geometry Box | Multicolor', 'camlin-kokuyo-nouvel-geometry-box-multicolor', '-', '73269099', '<b>Camlin Kokuyo Nouvel Geometry Box | Multicolor</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>1st time in a geometry box, a full size compass pen pencil free</li><li>A self -centering compass for better grip</li><li>0.7mm pen pencil for super precise arcs and fine drawing/writing</li><li>Pack includes - compass, divider, ruler, protractor and set square, sharpener, eraser, mechanical pencil</li><li>Attractive design</li></ul></div></div>', 111.00, 100.00, 100.00, 'Set', 10, 'sgst_cgst', 18.00, NULL, 65, 'uploads/products/6a926f083d890.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 05:31:53', 10, 2, 0, NULL, NULL, NULL),
(319, NULL, 'Pidilite Fevistik Nontoxic Glue Stick | 8 Gram', 'pidilite-fevistik-nontoxic-glue-stick-8-gram', '1234567', '-', '<b>Pidilite Multi-Purpose Fevistik Nontoxic Glue Stick | 8 Gram&nbsp;</b><div>for Decorations &amp; Craft Projects<br></div><div><br></div><div><div><ul><li><b>Brand: </b>Pidilite</li><li><b>Specific Uses For Product:</b> School</li><li><b>Material: </b>Plastic</li><li><b>Item Form:</b> Stick</li><li><b>Special Feature:</b> Fast Drying, Non Toxic</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Apart from the rotational opening and easy to apply functionality this glue stick provides you a smooth applying experience without much hassle</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Fevistik comes with a fast drying formulation which provides an instant solution to all of your paper to paper gluing requirements</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Fevistik is a transparent adhesive that does not leave any traces or marks upon drying and thereby providing a clean and nice finish</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Fevistik is a glue stick that has a superior quality in terms of bonding non-toxic non-staining and hygiene</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">This glue stick by Pidilite is an easy to store and easy to carry glue stick with a lipstick like opening which makes it an ideal adhesive to be used at home at school or at work</span></li></ul></div></div></div>', 55.00, 50.00, 50.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 66, 'uploads/products/6a92716204bf9.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 05:42:58', 10, 0, 0, NULL, NULL, NULL),
(320, NULL, 'Camlin CD-DVD Marker Pen | Pack of 10', 'camlin-cd-dvd-marker-pen-pack-of-10', '-', '96082000', '<b>Camlin CD-DVD Marker Pen&nbsp;&nbsp;| Pack of 10</b><div><div style=\"\"><ul><li><b>Brand: </b>Camlin</li><li><b>Ink Colour: </b>Blue</li><li><b>Number of Items:</b> 1</li><li><b>Point Type: </b>Fine</li><li><b>Target Audience: </b>Adult, Kid</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>Consistent ink flow</li><li>Smooth and dark marking</li><li>Marks on CD, DVD, metal, OHP sheets, wood, plastic, glass and ceramics</li><li>Water resistant and UV resistant</li></ul></div></div></div></div>', 111.00, 100.00, 10.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a927a0394715.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 06:19:47', 10, 4, 0, NULL, NULL, NULL),
(321, NULL, 'Faber-Castell Classic Chisel Point Textliner | Multicolour | Pack of 5', 'faber-castell-classic-chisel-point-textliner-multicolour-pack-of-5', '-', '96082000', '<b>Faber-Castell Classic Chisel Point Textliner | Multicolour | Pack of 5</b><div><b><br></b></div><div><div style=\"\"><ul><li><b>Brand: </b>Faber-Castell</li><li><b>Ink Colour: </b>Multicolor</li><li><b>Number of Items: </b>5</li><li><b>Point Type: </b>Chisel</li><li><b>Recommended Uses For Product: </b>Highlighting</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>Brighter marking with super fluorescent ink</li><li>Chisel tip for 3 line widths</li><li>Refillable with Faber-castell text liner ink</li></ul></div></div></div></div>', 110.00, 100.00, 20.00, 'No.', 9, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a927b64051fb.jpg', 98, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 06:25:40', 10, 2, 0, NULL, NULL, NULL),
(322, NULL, 'Faber-Castell Whiteboard Bold Point Type Marker | Black | Pack of 10', 'faber-castell-whiteboard-bold-point-type-marker-black-pack-of-10', '-', '96082000', '<b>Faber-Castell Whiteboard Bold Point Type Marker | Black | Pack of 10</b><div><div style=\"\"><ul><li><b>Brand: </b>Faber-Castell</li><li><b>Ink Colour: </b>Black</li><li><b>Number of Items: </b>10</li><li><b>Point Type: </b>Bold</li><li><b>Recommended Uses For Product: </b>Drawing</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>White board marker pen; Easy dry wipe marker pens</li><li>Pigmented drawing ink</li><li>High light resistance</li></ul></div></div></div></div>', 267.00, 240.00, 24.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 58, 'uploads/products/6a927c66d4ef8.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 06:29:58', 10, 3, 0, NULL, NULL, NULL),
(323, NULL, 'Camlin Kokuyo Stamp Pad | Violet | 9.5 cm X 5 cm', 'camlin-kokuyo-stamp-pad-violet-9-5-cm-x-5-cm', '-', '96122000', '<b>Camlin Kokuyo Stamp Pad | Violet | 9.5 cm X 5 cm</b><div><div><b>About this item:</b></div><div><ul><li>Stamp pad</li><li>9.5*5cm</li><li>Violet</li></ul></div></div>', 67.00, 60.00, 60.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 67, 'uploads/products/6a927f8758672.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 06:42:26', 10, 0, 0, NULL, NULL, NULL),
(324, NULL, 'Faber Castell Stamp Pad, Small | Size 88mm X 54mm | Violet', 'faber-castell-stamp-pad-small-size-88mm-x-54mm-violet', '-', '96122000', '<b>Faber Castell Stamp Pad, Small | Size 88mm X 54mm | Violet</b><div><div style=\"\"><ul><li><b>Brand: </b>Faber-Castell</li><li><b>Size: </b>88mm X 54mm</li><li><b>Colour: </b>Violet</li><li><b>Ink Colour: </b>Purple</li><li><b>Age Range (Description): </b>Adult</li></ul><div><div><b>About this item:</b></div><div><ul><li>Micro woven inkpad holds ink for a longer period and does not sag</li><li>Smudge free impression</li><li>Durable plastic case</li></ul></div></div></div><div><b><br></b></div><div><b><br></b><div><br></div></div></div>', 39.00, 35.00, 35.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 67, 'uploads/products/6a9280e48a689.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 06:49:08', 10, 0, 0, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `seller_id`, `name`, `slug`, `sku`, `hsn`, `description`, `mrp`, `selling_price`, `pay_per_unit`, `unit_label`, `discount_percentage`, `gst_type`, `gst_rate`, `shipping_charge`, `category_id`, `main_image`, `stock_quantity`, `package_quantity`, `is_active`, `is_approved`, `approved_at`, `approved_by`, `rejection_reason`, `is_featured`, `is_discounted`, `created_at`, `max_quantity_per_order`, `sort_order`, `has_variations`, `brand_id`, `seller_name`, `seller_code`) VALUES
(325, NULL, 'Glitter Foam 2 mm Thick Sheet Sparkles | A4 Size | 10 Pcs', 'glitter-foam-2-mm-thick-sheet-sparkles-a4-size-10-pcs', '1234567', '-', '<div><b>Glitter Foam 2 mm Thick Sheet Sparkles | A4 Size | 10 Pcs</b></div><div>(Non - Adhesive) with Foam Back for Art &amp; Craft, DIY Work Decoration, Gift Wrapping Muklti Color (10)</div><div><br></div><div><div><ul><li><b>Colour:</b> Multi</li><li><b>Brand: </b>Adaamya</li><li><b>Number of Pieces: </b>10</li><li><b>Item Form: </b>Stick</li><li><b>Recommended Uses For Product:</b> Arts</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">These sheets make awesome holiday gifts, stickers, scrapbook design ideas and many other crafts and hobbies, greeting cards and other creative projects</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Glitter Sheets are perfect gifts for kids / adults who love art and craft and are creative.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">The pack will have 10 different colors and you can cut the shape you desire and peel the back side and paste.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">You can also make tapes by cutting the sheets.</span></li></ul></div></div></div>', 248.00, 223.00, 22.30, 'No.', 10, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6a9282c8493aa.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 06:57:12', 10, 0, 0, NULL, NULL, NULL),
(326, NULL, 'Camlin Champ Round Brush Set | Pack of 4 | Multicolor', 'camlin-champ-round-brush-set-pack-of-4-multicolor', '1234567', '-', '<b>Camlin Champ Round Brush Set | Pack of 4 | Multicolor</b><div><b><br></b></div><div><div style=\"\"><ul><li><b>Pattern Name:</b> Round Brush Set</li><li><b>Colour: </b>Multicolour</li><li><b>Material: </b>Plastic</li><li><b>Size: </b>Size: 0, Size: 2, Size: 4, Size: 6</li><li><b>Brand: </b>Camlin</li><li><b>Bristle Type: </b>Synthetic</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Round brushes - 0, 2, 4, 6</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Fine quality synthetic bristles</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Attractive neon color handles</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Aluminum ferrules</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Country of Origin: India</span></li></ul></div></div></div>', 56.00, 50.00, 12.50, 'Set', 11, 'sgst_cgst', 18.00, NULL, 56, 'uploads/products/6a928705cdde5.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 07:15:17', 10, 0, 0, NULL, NULL, NULL),
(327, NULL, 'Camlin Tora Ball Pen | Pack of 8 | Multicolor', 'camlin-tora-ball-pen-pack-of-8-multicolor', '-', '96081019', '<b>Camlin Tora Ball Pen | Pack of 8 | Multicolor</b><div><b>About this item:</b><br><div><div style=\"\"><ul><li><b>Brand: </b>Camlin</li><li><b>Ink Colour: </b>Multicolor</li><li><b>Special Feature: </b>Retractable</li><li><b>Body Shape: </b>Round</li></ul></div></div></div>', 89.00, 80.00, 8.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a928d982447e.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 07:43:20', 10, 17, 0, NULL, NULL, NULL),
(328, NULL, 'Cello Butterflow Ball Pen', 'cello-butterflow-ball-pen', '-', '96081019', '<b>Cello Butterflow Ball Pen</b><div><div style=\"\"><ul><li><b>Brand: </b>Cello</li><li><b>Writing Instrument Form: </b>Ballpoint Pen</li><li><b>Colour: </b>Assorted</li><li><b>Ink Colour: </b>Blue</li><li><b>Age Range (Description): </b>Adult, Teen</li></ul><div><b>About this item:</b><br></div><div><div style=\"\"><ul><li>No.1 Pen Brand: Continuing the promise of good quality and best product experience of Cello, Butterflow is No.1 pen brand in India.</li><li>Exclusive Ink System: Equipped with an advanced tip design &amp; ink system, the Cello Butterflow Simply Ball Pen delivers the smoothest writing experience. The vibrant dark blue ink with 0.7mm tip for fine writing that sets it apart from the rest.</li><li>Ergonomic Grip for Comfort: The Cello Butterflow Simply Ball Pen is designed for comfort and control, featuring a \"S Wave\" grip that allows you to write for longer periods without fatigue.</li><li>Smart Choice: These Ball Pens are user friendly in a smart storage jar and ideal for daily use for students and professional both.</li><li>Convenient Ink Level Window: The Ball pen ink indicator is visible through a window on the pen body, allowing you to effortlessly keep track of the ink levels.</li></ul></div></div></div></div>', 28.00, 25.00, 25.00, 'No.', 11, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a928f0e681c7.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 07:49:34', 10, 15, 0, NULL, NULL, NULL),
(329, NULL, 'Cello Fine Grip Ball Pen Set | Pack of 25 | Blue', 'cello-fine-grip-ball-pen-set-pack-of-25-blue', '-', '96081019', '<b>Cello Fine Grip Ball Pen Set | Pack of 25 | Blue</b><div><div style=\"\"><ul><li><b>Brand: </b>Cello</li><li><b>Writing Instrument Form: </b>Ballpoint Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour: </b>Blue</li><li><b>Age Range (Description): </b>Adult</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>The Cello Finegrip Ball Pen has a soft tip that enables sleek and smooth writing</li><li>This ball pen has a strong and durable body that makes it ideal for everyday use</li><li>These ball point pens have a 0.7mm tip size for fine writing and equipped with a unique Elasto grip for comfortable writing</li><li>These ball pens are suitable for all ages; The pen jar contains 25 blue ball pens</li><li>Item Weight: 100.0 Grams</li></ul></div></div></div></div>', 195.00, 175.00, 7.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a9298b810207.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 08:30:48', 10, 14, 0, NULL, NULL, NULL),
(330, NULL, 'Cello Gripper Ball Pen Set | Pack of 25 | Blue', 'cello-gripper-ball-pen-set-pack-of-25-blue', '-', '96081019', '<b>Cello Gripper Ball Pen Set | Pack of 25 | Blue</b><div><div style=\"\"><ul><li><b>Brand: </b>Cello</li><li><b>Writing Instrument Form: </b>Ballpoint Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour: </b>Blue</li><li><b>Age Range (Description): </b>Adult, Kid</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>The Cello Gripper Ball Pen has a unique Elasto grip for the most comfortable writing experience</li><li>This ball pen has a swiss metal tip made from 0.5 mm nickel silver for pressure-free, fine writing</li><li>These Cello pens feature a transparent barrel, cap and clip for a sleek and stylish finish</li><li>These ball pens are lightweight and easy to use</li><li>This ball point pen set is suitable for all ages</li></ul></div></div></div></div>', 272.00, 245.00, 9.80, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a929a0f2ac70.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 08:36:31', 10, 13, 0, NULL, NULL, NULL),
(331, NULL, 'Cello Maxriter Ball Pen Set | Blue  | Pack of 10', 'cello-maxriter-ball-pen-set-blue-pack-of-10', '-', '96081019', '<b>Cello Maxriter Ball Pen Set | Pack of 10 | Blue</b><div><div style=\"\"><ul><li><b>Brand: </b>Cello</li><li><b>Writing Instrument Form: </b>Ballpoint Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour: </b>Blue</li><li><b>Age Range (Description): </b>Adult, Kid</li></ul><div><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Unique Elasto grip.</li><li>Light to hold and easy to carry makes it convenient for long writing.</li><li>Apt for pressure free writing.</li><li>Perfect for writing exams.</li><li>Item Weight: 80 grams</li></ul></div></div></div></div>', 170.00, 153.00, 15.30, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a929dc24bf3b.jpg', 98, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 08:52:18', 10, 10, 0, NULL, NULL, NULL),
(332, NULL, 'Cello Pinpoint Ballpoint Pens | Blue | Pack of 10', 'cello-pinpoint-ballpoint-pens-blue-pack-of-10', '-', '96081019', '<b>Cello Pinpoint Ballpoint Pens | Blue | Pack of 10<br></b><div><ul><li><span style=\"text-align: var(--bs-body-text-align); font-weight: bolder;\">Brand:&nbsp;</span><span style=\"text-align: var(--bs-body-text-align);\">Cello</span></li><li><span style=\"font-weight: bolder;\">Writing Instrument Form:&nbsp;</span>Ballpoint Pen</li><li><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Blue</li><li><span style=\"font-weight: bolder;\">Ink Colour:&nbsp;</span>Blue</li><li><span style=\"font-weight: bolder;\">Age Range (Description):&nbsp;</span>Adult, Kid</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Barrel and Cap: Transparent, Clip: Solid Plastic Transparent with Matching Ink Colour Casing</li><li>Nozzle: Metallized, Button: Plastic Blue Ink Colour</li><li>Grip: Elasto Grip for pressure free writing</li></ul></div></div>', 189.00, 170.00, 17.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a929f47a20ed.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 08:57:41', 10, 9, 0, NULL, NULL, NULL),
(333, NULL, 'Cello Technotip Ball Pens | Blue | Pack of 10', 'cello-technotip-ball-pens-blue-pack-of-10', '-', '96081019', '<span style=\"font-weight: bolder;\">Cello Pinpoint Ballpoint Pens | Blue | Pack of 10</span><b><br></b><span style=\"text-align: var(--bs-body-text-align);\">Lightweight Ball Pens for Pressure Free and Fine Writing | Pens with Comfortable Grip | For School and Office Use</span><div><ul><li><span style=\"font-weight: bolder;\">Brand:&nbsp;</span>Cello</li><li><span style=\"font-weight: bolder;\">Writing Instrument Form:&nbsp;</span>Ballpoint Pen</li><li><span style=\"font-weight: bolder;\">Colour:&nbsp;</span>Blue</li><li><span style=\"font-weight: bolder;\">Ink Colour:&nbsp;</span>Blue</li><li><span style=\"font-weight: bolder;\">Age Range (Description):&nbsp;</span>Adult, Kid</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Unique Elasto grip ensures pressure-free writing;Light to hold and easy to carry makes it convenient for long writing</li><li>Lubriflow ink system for ultra smooth writing</li><li>Specially created for exam writing; 0.6mm tip for fine writing</li><li>This pack includes 10 blue ball pens</li><li>Item Weight: 100 grams</li></ul></div></div>', 189.00, 170.00, 17.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a92a143d32d6.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 09:07:15', 10, 8, 0, NULL, NULL, NULL),
(334, NULL, 'Classmate Octane Gel Pen | Neon | Pack of 10', 'classmate-octane-gel-pen-neon-pack-of-10', '-', '96081019', '<b>Classmate Octane Gel Pen | Neon | Pack of 10</b><div><div style=\"\"><ul><li><b>Brand: </b>Generic</li><li><b>Writing instrument form: </b>Ballpoint Pen</li><li><b>Colour: </b>Multicolor</li><li><b>Ink Colour: </b>Multicolor</li><li><b>Age Range (Description): </b>Adult</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li>Material : Plastic</li><li>Color : Multicolor</li><li>Special Feature : Eco Friendly</li></ul></div></div></div></div>', 133.00, 120.00, 12.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a92a2b12c0b6.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 09:13:21', 10, 11, 0, NULL, NULL, NULL),
(335, NULL, 'Flair Ezee Click Ball Pen Pouch Pack | 0.7 to 1 mm Tip Size | Pack of 5', 'flair-ezee-click-ball-pen-pouch-pack-0-7-to-1-mm-tip-size-pack-of-5', '-', '96081019', '<b>Flair Ezee Click Ball Pen Pouch Pack | 0.7 to 1 mm Tip Size | Pack of 5</b><div>Retractable Mechanism With Comfortable Grip For Smooth Writing | Different Playful Body Colors | Blue Ink</div><div><ul><li><b>Brand:</b> Flair</li><li><b>Writing instrument form:</b> Ballpoint Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour:</b> Blue</li><li><b>Age Range (Description):</b> Adult</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Package Includes - Flair Ezee Click Ball Pen, Material - Plastic, Ink Color - Blue, Body Color - Multicolor</li><li>Flair Ezee Click Ball Pen Gives You The Smooth Writing Experience For Comfortable Writing.</li><li>Ergonomic Grip For Easy Handling Of The Pen.</li><li>Writes Great Without Skipping, Smearing &amp; Stuttering.</li><li>Ideal For School, College &amp; Office. Perfect for Writing, Journaling, Note-Taking &amp; Underlining.</li></ul></div></div>', 195.00, 175.00, 35.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a92a5b3d3228.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 09:26:11', 10, 16, 0, NULL, NULL, NULL),
(336, NULL, 'FLAIR Glitter Xtra Sparkle Gel Pen | 1.0mm | Set of 10', 'flair-glitter-xtra-sparkle-gel-pen-1-0mm-set-of-10', '-', '96081019', '<b>FLAIR Glitter Xtra Sparkle Gel Pen | 1.0mm | Set of 10</b><div>Different Ink Colours, Transparent Body, for Tattoos, Cards, Colouring</div><div><ul><li><b>Brand:</b> Flair</li><li><b>Writing Instrument Form:</b> Ballpoint Pen</li><li><b>Colour:</b> Multicolor</li><li><b>Ink Colour:</b> Multicolor</li><li><b>Age Range (Description):</b> Adult</li></ul><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>Package Includes : Glitter Sparkle Gel Pen, Tip Size : 1 mm, Ink Colour : 10 Shades, Tip : Stainless Steel Tip, Ink Characteristics : Fluid Gel Ink, Pack Size : Blister Pack of 10.</li><li>Long-lasting gel ink writes first time, every time.</li><li>Gel pen with no smear, no bleed technology.</li><li>Comfortable grip for a smooth writing experience.</li><li>These writing instruments are ideal gifts for the guest book table at conventions, parties and so much more. These pens are suitable for school, office and home. Ideal for writing, journaling, note-taking, underlining, sketching and more.</li></ul></div></div>', 146.00, 131.00, 13.10, 'No.', 10, 'sgst_cgst', 0.00, NULL, 57, 'uploads/products/6a92a80381c8d.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 09:36:03', 10, 19, 0, NULL, NULL, NULL),
(337, NULL, 'Reynolds 045 Ball Pen | Blue | Set of 10', 'reynolds-045-ball-pen-blue-set-of-10', '-', '96081019', '<b>Reynolds 045 Ball Pen | Blue | Set of 10</b><div><div style=\"\"><ul><li><b>Brand: </b>Reynolds</li><li><b>Writing Instrument Form: </b>Ballpoint Pen</li><li><b>Colour: </b>Blue</li><li><b>Ink Colour: </b>Blue</li><li><b>Age Range (Description): </b>10</li></ul><div><b>About this item:</b></div><div><div style=\"\"><ul><li><span style=\"text-align: var(--bs-body-text-align);\">Laser Smooth Writing experience;Laser Tip technology;Solid body type;Blue colour</span></li></ul></div></div></div></div>', 111.00, 100.00, 10.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 57, 'uploads/products/6a92a8e26a0f7.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 09:39:46', 10, 12, 0, NULL, NULL, NULL),
(338, NULL, 'Camlin Kokuyo Exam Standard Scale | 30cm | Pack of 10', 'camlin-kokuyo-exam-standard-scale-30cm-pack-of-10', '1234567', '-', '<b>Camlin Kokuyo Exam Standard Scale | 30cm | Pack of 10</b><div><b><br></b></div><div><div style=\"\"><ul><li><b>Material: </b>Plastic</li><li><b>Brand: </b>Camlin</li><li><b>Colour: </b>Clear</li><li><b>Style: </b>Compact</li><li><b>Graduation Range: </b>0-30 Centimeters</li></ul><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"padding: 0px; margin-bottom: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; color: rgb(15, 17, 17); font-size: 1.25rem !important; line-height: 24px !important; font-family: &quot;Amazon Ember&quot; !important;\">About this item</h1><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Highly Transparent plastic scale with precise marking for easy reading and accurate drawings.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Smooth tapered edges that lie flat on the surface to give sharp lines.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Round edges that do not chip</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Clear and distinctly visible markings</span></li></ul></div></div></div>', 111.00, 100.00, 10.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 68, 'uploads/products/6a92ac615e3a6.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 09:54:41', 10, 0, 0, NULL, NULL, NULL),
(339, NULL, 'Apsara Long Point Sharpeners | Pack 0f 20', 'apsara-long-point-sharpeners-pack-0f-20', '-', '82141010', '<div><b>Apsara Long Point Sharpeners | Pack 0f 20</b></div><div>Comfortable Grip, Highly Durable, Anti-Rust, Scientifically Designed Blade, Child Safe, for Students, Artists &amp; Professionals</div><div><ul><li><b>Brand: </b>Apsara</li><li><b>Writing Instrument Form: </b>Graphite Pencil</li><li><b>Colour:</b> Multi</li><li><b>Age Range (Description):</b> All Ages</li><li><b>Point Type: </b>Long</li></ul><div><b>About this item</b><br></div><div><ul class=\"a-unordered-list a-vertical a-spacing-mini\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 18px; color: rgb(15, 17, 17); padding: 0px; font-family: &quot;Amazon Ember&quot;, Arial, sans-serif; font-size: 14px;\"><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">The information below is per-pack only</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Scientifically Designed Blade for Elegant Long Tip: This sharpener features a scientifically designed blade that allows you to achieve an elegant and long pencil tip with precision.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Contoured Body for an Easy Grip: The sharpener is designed with a contoured body that provides an easy and comfortable grip.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Anti-Rust Coating for Durability: Equipped with an anti-rust coating, this sharpener is built to last, making it a reliable choice for children to use over an extended period.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Attractive Colour Options: It\'s available in five different attractive body colours, adding a touch of vibrancy and personalization to your stationery collection.</span></li><li class=\"a-spacing-mini\" style=\"list-style: disc; overflow-wrap: break-word; margin: 0px;\"><span class=\"a-list-item\" style=\"color: var(--__dChNmAmGoMXsw4B,#0f1111);\">Versatile and User-Friendly: The Apsara Long Point Sharpener is versatile and user-friendly, suitable for students, artists, and professionals who desire a precise and elegant pencil point.</span></li></ul></div></div><div><br></div>', 192.00, 172.00, 8.60, 'No.', 10, 'sgst_cgst', 18.00, NULL, 69, 'uploads/products/6a92af5807d69.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 10:07:20', 10, 0, 0, NULL, NULL, NULL),
(340, NULL, 'Faber-Castell Connector Sketch Pens | Pack of 25 | Multicolor', 'faber-castell-connector-sketch-pens-pack-of-25-multicolor', '1234567', '-', '<b>Faber-Castell Connector Sketch Pens | Pack of 25 | Multicolor</b><div><div style=\"\"><ul><li><b>Brand: </b>Faber-Castell</li><li><b>Writing instrument form: </b>Roller Ball Pen</li><li><b>Colour: </b>Multicolor</li><li><b>Ink Colour: </b>Multicolor</li><li><b>Age Range (Description): </b>Kid</li></ul><div><div><b>About this item:</b></div><div><ul><li>Water-based non-toxic ink</li><li>Available in a range of brilliant colors</li><li>Washable from most clothing and surfaces</li></ul></div></div></div></div>', 167.00, 150.00, 6.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 70, 'uploads/products/6a92b24132b0b.jpg', 100, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-08-29 10:19:45', 10, 0, 0, NULL, NULL, NULL),
(341, NULL, 'Bubble Wrap | 1 meter x 100 meter', 'bubble-wrap-1-meter-x-100-meter', '1234567', '39201012', '<b>Bubble Wrap | 1 meter x 100 meters</b><div><b><br></b></div><div><div style=\"\"><b>Description</b></div><div style=\"\"><ul><li><span style=\"text-align: var(--bs-body-text-align);\">The bubble wrap packing roll is better than traditional packing material such as the newspaper which can not protect the fragile products well</span><br></li><li>Lightweight &amp; Durable - The bubble roll packing offers maximum protection with minimum weight. The bubble wrap roll is lightweight and versatile which perfect for packing, shipping and moving items.</li><li>Reusable - Made from high quality nylon bubble, strong, flexible, lightweight, well tear, and moisture resistant</li></ul></div><div><br></div></div>', 1700.00, 1300.00, 1300.00, 'No.', 24, 'sgst_cgst', 18.00, NULL, 36, 'uploads/products/6aa3e7bd62997.webp', 97, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-11 11:36:29', 10, 0, 0, NULL, NULL, NULL),
(342, NULL, '5 Ply | 16 x 12 x 3 Inch | Laptop Box', '5-ply-16-x-12-x-3-inch-laptop-box', '1234567', '48191010', '<b>5 Ply 16 x 12 x 3 Inch</b><div><b><br></b></div><div><div style=\"\"><ul><li><b>Color: Brown</b></li><li><b>Type of Product: Corrugated Carton Box</b></li><li><b>Material: Paper and Cardboard</b></li><li><b>Ply: 5 Ply</b></li></ul></div></div>', 100.00, 75.00, 75.00, 'No.', 25, 'sgst_cgst', 5.00, NULL, 34, 'uploads/products/6aa3f28f6196b.webp', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-11 12:22:39', 10, 0, 0, NULL, NULL, NULL),
(343, NULL, 'Alpha Power | A4 Size | 75 GSM | 500 Sheets', 'alpha-power-a4-size-75-gsm-500-sheets', '-', '48025610', '<b>Alpha Power | A4 Size | 75 GSM | 500 Sheets | 1 Ream | White Printing Paper | For Laserjet &amp; Inkjet Printer | Fast Drying | Both Side Print | Eco Friendly</b><div><div style=\"\"><ul><li><b>Brand: </b>Generic</li><li><b>Colour: </b>White</li><li><b>Item Weight: </b>2300 Grams</li><li><b>Paper Finish: </b>Smooth</li><li><b>Sheet Size: </b>29.7*21 ( A4 ) centimetres</li></ul><div><div><b>About this item:</b></div></div><div><div style=\"\"><ul><li>A4 Size Printing Paper</li><li>75 Gsm A4 Size Paper</li><li>Alpha Power A4 75 Gsm Paper</li><li>White A4 Size Paper</li></ul></div></div></div></div>', 350.00, 325.00, 325.00, 'No.', 7, 'sgst_cgst', 18.00, NULL, 51, 'uploads/products/6aa3fbc35d378.jpg', 99, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-11 13:01:55', 10, 0, 0, NULL, NULL, NULL),
(344, NULL, 'Kangaro HS-G10', 'kangaro-hs-g10', '-', '84729010', '<b>Kangaro HS-G10</b><br><div><span style=\"font-weight: bolder;\">Features:</span><br>Metal bottom with high quality plastic top. Built-in staple remover &amp; reload indicator.<br><br><div><h1 class=\"a-size-base-plus a-text-bold\" style=\"margin-bottom: 0px; padding: 0px; padding-block-end: 0.25rem; text-rendering: optimizelegibility; line-height: 24px !important;\"><div style=\"\"><span style=\"font-size: 15px;\">About this item:</span><br></div><div style=\"color: rgb(0, 0, 0); font-family: &quot;Amazon Ember&quot; !important; font-size: 15px;\"><div style=\"\"><ul style=\"\"><li style=\"\">Staple Use :<span style=\"font-weight: 500;\"> No.10</span></li><li style=\"\">Loading Capacity : <span style=\"font-weight: 500;\">50 Staples (No.10)</span></li><li style=\"\">Stapling Capacity :<span style=\"font-weight: 500;\"> 20 Sheets of 75~80 gsm paper</span></li><li style=\"\">Throat Depth :<span style=\"font-weight: 500;\"> 52mm</span></li><li style=\"\">Loading Type :<span style=\"font-weight: 500;\"> Manual</span></li><li style=\"\">Stapling Type :<span style=\"font-weight: 500;\"> Conventional</span></li><li style=\"\">Tacking :<span style=\"font-weight: 500;\"> Yes</span></li></ul></div></div></h1></div></div>', 100.00, 90.00, 90.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aad1e21e6923.jpg', 9, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-18 11:17:00', 10, 0, 0, NULL, NULL, NULL),
(345, NULL, 'Kangaro Staple Pin 24/6-1M | Pack of 5', 'kangaro-staple-pin-24-6-1m-pack-of-5', '-', '83052000', '<b>Kangaro Staple Pin 24/6-1M</b><span style=\"font-weight: bolder;\">&nbsp; | Pack of 5</span><br><div><b>About this item:</b><br></div><div><ul><li><span style=\"font-weight: bolder;\">Strip :&nbsp;</span>50 nos.</li><li><span style=\"font-weight: bolder;\">Packing :&nbsp;</span>1000 staples.</li><li><span style=\"font-weight: bolder;\">Bxs./Ctn :&nbsp;</span>300 bxs.</li><li><span style=\"font-weight: bolder;\">Volume/Ctn :&nbsp;</span>0.0184 cbm.</li><li><span style=\"font-weight: bolder;\">Weight/Ctn :&nbsp;</span>15.40 Kgs.</li></ul></div>', 130.00, 98.00, 26.00, 'Pack', 25, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aad240161114.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 0, '2026-09-18 11:42:47', 10, 0, 0, NULL, NULL, NULL),
(346, NULL, 'Kangaro Staple Pin 23-24-H | Pack of 1', 'kangaro-staple-pin-23-24-h-pack-of-1', '-', '83052000', '<b>Kangaro Staple Pin 23-24-H | Pack of 1</b><br><div><b>About this item:</b><br></div><div><ul><li><span style=\"font-weight: bolder;\">Strip :&nbsp;</span>100 nos.</li><li><span style=\"font-weight: bolder;\">Packing :&nbsp;</span>1000 staples.</li><li><span style=\"font-weight: bolder;\">Bxs./Ctn :&nbsp;</span>100 bxs.</li><li><span style=\"font-weight: bolder;\">Volume/Ctn :&nbsp;</span>0.0245<span style=\"margin: 0px; padding: 0px; font-weight: bolder; color: rgb(33, 37, 41); font-family: SourceSansPro-Regular; font-size: 16px; background-color: rgb(251, 250, 249);\">&nbsp;cbm.</span></li><li><span style=\"font-weight: bolder;\">Weight/Ctn :&nbsp;</span>21.85<span style=\"color: rgb(33, 37, 41); font-family: SourceSansPro-Regular; font-size: 16px; background-color: rgb(235, 235, 235);\">&nbsp;</span><span style=\"margin: 0px; padding: 0px; font-weight: bolder; color: rgb(33, 37, 41); font-family: SourceSansPro-Regular; font-size: 16px;\">Kgs.</span></li></ul></div>', 125.00, 119.00, 119.00, 'Pack', 5, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aad2670bfb8d.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-18 11:54:24', 10, 0, 0, NULL, NULL, NULL),
(347, NULL, 'Kangaro Staple Pin 23/10-H | Pack of 10', 'kangaro-staple-pin-23-10-h-pack-of-10', '-', '83052000', '<b>Kangaro Staple Pin 23/10-H | Pack of 10</b><br><div><b>About this item:</b><br></div><div><ul><li><span style=\"font-weight: bolder;\">Strip :&nbsp;</span>100 nos.</li><li><span style=\"font-weight: bolder;\">Packing :&nbsp;</span>1000 staples.</li><li><span style=\"font-weight: bolder;\">Bxs./Ctn :&nbsp;</span>100 bxs.</li><li><span style=\"font-weight: bolder;\">Volume/Ctn :&nbsp;</span>0.0108<span style=\"margin: 0px; padding: 0px; color: rgb(33, 37, 41); font-family: SourceSansPro-Regular; font-size: 16px; background-color: rgb(251, 250, 249);\"><span style=\"font-weight: bolder;\">&nbsp;</span>cbm.</span></li><li><span style=\"font-weight: bolder;\">Weight/Ctn :&nbsp;</span>10.95<span style=\"background-color: rgb(235, 235, 235);\">&nbsp;</span><b><span style=\"margin: 0px; padding: 0px; color: rgb(33, 37, 41); font-family: SourceSansPro-Regular; font-size: 16px;\">Kgs.</span></b></li></ul></div>', 480.00, 416.00, 42.00, 'Pack', 13, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aad27a67c768.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-18 11:59:34', 10, 0, 0, NULL, NULL, NULL),
(348, NULL, 'Sundaram | Scribbling Pad 1/8 – 40 Sheets | 80 Pages | 14 x 21.5 cm | Pack of 12 Pcs', 'sundaram-scribbling-pad-1-8-40-sheets-80-pages-14-x-21-5-cm-pack-of-12-pcs', '-', '48201090', '<b>Sundaram | Scribbling Pad 1/8 – 40 Sheets | 80 Pages | 14 x 21.5 cm&nbsp;| Pack of 12 Pcs</b><div><div style=\"\"><ul><li><b>Brand : </b>Sundaram</li><li><b>Colour : </b>White</li><li><b>Material : </b>Paper</li><li><b>Recommended </b>Uses For Product : Sketching, Doodling, Writing</li><li><b>Sheet Count : </b>40</li></ul><div><div><b>About this item:</b><br></div><div><ul><li>Light weight, Easy to carry, Pocket friendly.</li><li>High-quality FSC certified paper offering a smooth surface for writing.</li><li>Sundaram Notebooks use finest quality paper and ensures one of the best quality products.</li><li>Ideal for writing notes, to-do lists, memories, expenses, composing new ideas, planning &amp; much more.</li><li>Binding: Stapled &amp; Micro-Perforated</li><li>Multi use- public &amp; private sector offices, shopkeepers, store, service, hotels, home etc.</li><li>Recyclable.</li><li>64 GSM.</li></ul></div></div></div></div>', 360.00, 324.00, 27.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aad2f836349a.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-18 12:33:07', 10, 0, 0, NULL, NULL, NULL),
(349, NULL, 'Sundaram | Scribbling Pad 1/16 – 40 Sheets | 80 Pages | 10.5 x 14 cm | 2 | Pack of 12 Pcs', 'sundaram-scribbling-pad-1-16-40-sheets-80-pages-10-5-x-14-cm-2-pack-of-12-pcs', '-', '48201090', '<b>Sundaram | Scribbling Pad 1/16 – 40 Sheets | 80 Pages | 10.5 x 14 cm | 2</b><span style=\"font-weight: bolder;\">&nbsp;| Pack of 12 Pcs</span><div><ul><li><span style=\"font-weight: bolder;\">Brand :&nbsp;</span>Sundaram</li><li><span style=\"font-weight: bolder;\">Colour :&nbsp;</span>White</li><li><span style=\"font-weight: bolder;\">Material :&nbsp;</span>Paper</li><li><span style=\"font-weight: bolder;\">Recommended&nbsp;</span>Uses For Product : Sketching, Doodling, Writing</li><li><span style=\"font-weight: bolder;\">Sheet Count :&nbsp;</span>40</li></ul><div><div><b>About this item:</b><br></div><div><ul><li>Light weight, Easy to carry, Pocket friendly.</li><li>High-quality FSC certified paper offering a smooth surface for writing.</li><li>Sundaram Notebooks use finest quality paper and ensures one of the best quality products.</li><li>Ideal for writing notes, to-do lists, memories, expenses, composing new ideas, planning &amp; much more.</li><li>Binding: Stapled &amp; Micro-Perforated</li><li>Multi use- public &amp; private sector offices, shopkeepers, store, service, hotels, home etc.</li><li>Recyclable.</li><li>64 GSM.</li></ul></div></div></div>', 480.00, 432.00, 36.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aad3aea3fc34.png', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-18 13:21:46', 10, 0, 0, NULL, NULL, NULL),
(350, NULL, 'Camlin Colour Pencil | 24 Shades with  Sharpener', 'camlin-colour-pencil-24-shades-with-sharpener', '-', '96091000', '<b>Camlin Colour Pencil | 24 Shades&nbsp;with Sharpener</b><div><div style=\"\"><b>Features:</b></div><div style=\"\"><ul><li>Vibrant colour formulation</li><li>Comfortable grip to hold, draw, and fill</li><li>Soft leads that are perfect for sketching, mixing, and blending</li><li>Smooth leads that don\'t break for long-lasting fun</li><li>100% vegan</li></ul></div></div>', 120.00, 110.00, 4.00, 'No.', 8, 'sgst_cgst', 18.00, NULL, 63, 'uploads/products/6aae1994a9f34.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 05:11:48', 10, 0, 0, NULL, NULL, NULL),
(351, NULL, 'Camlin Colour Pencil | 12 Shades with Sharpener', 'camlin-colour-pencil-12-shades-with-sharpener', '-', '96091000', '<span style=\"font-weight: bolder;\">Camlin Colour Pencil | 12 Shades&nbsp;with Sharpener</span><div><div><span style=\"font-weight: bolder;\">Features:</span></div><div><ul><li>Vibrant colour formulation</li><li>Comfortable grip to hold, draw, and fill</li><li>Soft leads that are perfect for sketching, mixing, and blending</li><li>Smooth leads that don\'t break for long-lasting fun</li><li>100% vegan</li></ul></div></div>', 80.00, 74.00, 6.00, 'No.', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aae1fb62d85d.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 05:37:58', 10, 0, 0, NULL, NULL, NULL),
(352, NULL, 'Camlin Colour Pencils | 10 shades Half Size', 'camlin-colour-pencils-10-shades-half-size', '-', '96091000', '<b>Camlin Colour Pencils | 10 shades Half Size</b><br><div><div><span style=\"font-weight: bolder;\">About this item:</span><br></div><div><ul><li>Vibrant colour formulation</li><li>Comfortable grip to hold, draw, and fill</li><li>Soft leads that are perfect for sketching, mixing, and blending</li><li>Smooth leads that don\'t break for long-lasting fun</li><li>100% vegan</li></ul></div></div>', 25.00, 23.00, 2.00, 'No.', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aae24ec37b9c.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 06:00:12', 10, 0, 0, NULL, NULL, NULL),
(353, NULL, 'Camlin Scholar Geometry Box', 'camlin-scholar-geometry-box', '-', '73269099', '<b>Camlin Scholar Geometry Box</b><div><div style=\"\"><b>Included in this pack:</b></div><div style=\"\"><ul><li>Compass</li><li>Divider</li><li>45° Set Square</li><li>60° Set Square</li><li>180° Protractor</li><li>15 cm Scale</li><li>Eraser</li><li>Sharpener</li><li>Pencil</li></ul><div><div><b>About this item:</b><br></div><div><ul><li>Bestseller since 1971</li><li>Self-centering compass and divider for accurate results</li><li>Transparent plastic components with accurate calibration</li><li>Neatly placed in the tray for convenient handling and storage</li></ul></div></div></div></div>', 140.00, 126.00, 126.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aae30450a4f2.jpg', 9, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 06:09:18', 10, 0, 0, NULL, NULL, NULL),
(354, NULL, 'Camlin Plastic Scale | 15 cm', 'camlin-plastic-scale-15-cm', '-', '84238110', '<b>Camlin Plastic Scale | 15 cm</b><div><div style=\"\"><b>Features:</b></div><div style=\"\"><ul><li>Thick and sturdy with rounded edges</li><li>Perfect, bold calibration for accurate marking</li><li>Made of transparent virgin material</li></ul></div></div>', 6.00, 5.00, 5.00, 'No.', 17, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aae29d2ae7a2.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 06:21:06', 10, 0, 0, NULL, NULL, NULL),
(355, NULL, 'Camlin Plastic Scale | 30 cm', 'camlin-plastic-scale-30-cm', '-', '84238110', '<span style=\"font-weight: bolder;\">Camlin Plastic Scale | 30 cm</span><div><div><span style=\"font-weight: bolder;\">Features:</span></div><div><ul><li>Thick and sturdy with rounded edges</li><li>Perfect, bold calibration for accurate marking</li><li>Made of transparent virgin material</li></ul></div></div>', 12.00, 11.00, 11.00, 'No.', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aae2b296a837.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 06:26:49', 10, 0, 0, NULL, NULL, NULL),
(356, NULL, 'Camlin Supreme Long Point Sharpener', 'camlin-supreme-long-point-sharpener', '-', '82141010', '<b>Camlin Supreme Long Point Sharpener</b><div><div style=\"\"><span style=\"font-weight: bolder;\">About this item:</span><br></div><div style=\"\"><ul><li>Scientifically designed blade for an aesthetically long tip</li><li>Can enable to draw tiny details and lines</li><li>Sharper point for a longer period of time</li><li>Requires lesser frequency of sharpening</li></ul></div></div>', 7.00, 6.00, 6.00, 'No.', 14, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aae2eff50280.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 06:41:02', 10, 0, 1, NULL, NULL, NULL),
(357, NULL, 'Camlin Nova Glowing Pencils | Box of 10 Pencils with Sharpener', 'camlin-nova-glowing-pencils-box-of-10-pencils-with-sharpener', '-', '96091000', '<b>Camlin Nova Glowing Pencils | Box of 10 Pencils with Sharpener</b><div><div style=\"\"><span style=\"font-weight: bolder;\">About this item:</span><br></div><div style=\"\"><ul><li>Vibrant pencil colours that make writing fun</li><li>Eraser tip attached for quick correction</li><li>Triangular design for a comfortable grip</li><li>High-quality lead for dark and smooth writing</li></ul></div></div>', 70.00, 60.00, 6.00, 'No.', 14, 'sgst_cgst', 18.00, NULL, 63, 'uploads/products/6aae34341f114.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-19 07:05:24', 10, 0, 0, NULL, NULL, NULL),
(358, NULL, 'Natraj 621 BE Bold Pencil | Pack of 10', 'natraj-621-be-bold-pencil-pack-of-10', '-', '96091000', '<b>Natraj 621 BE Bold Pencil | Pack of 10</b><br><div><div style=\"\"><b>About this item:</b></div><div style=\"\">Experience bold and sharp writing with our HB bonded lead, designed for precision and durability. Trusted for its superior performance, this pencil offers long-lasting lead for smooth, comfortable writing and is easy to sharpen for a hassle-free experience.</div></div><div style=\"\"><br></div>', 50.00, 45.00, 4.50, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aafada480e41.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 09:54:27', 10, 0, 0, NULL, NULL, NULL),
(359, NULL, 'Nataraj Classic Ball Pen Blue Ink | Pack Of 20 Pens', 'nataraj-classic-ball-pen-blue-ink-pack-of-20-pens', '-', '96081019', '<b>Nataraj Classic Ball Pen Blue Ink | Pack Of 20 Pens</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Comfortable writing with non fading ink.</li><li>Hexagonal shape for better grip while writing.</li><li>Fine NSTC tip for break free writing.</li><li>For smooth and long writing.</li></ul></div></div>', 80.00, 72.00, 3.60, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aafafda9e7cc.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:04:19', 10, 30, 0, NULL, NULL, NULL),
(360, NULL, 'Natraj 621 Sharpener | Pack of 20', 'natraj-621-sharpener-pack-of-20', '-', '82141010', '<b>Natraj 621 Sharpener | Pack of 20</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Specially angled blade for easy sharpening</li><li>Antirust coating for long lasting blade edge</li><li>Contoured body for firm grip</li><li>Available in 5 attractive colours</li></ul></div></div>', 100.00, 90.00, 4.50, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aafb1696d356.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:11:53', 10, 0, 0, NULL, NULL, NULL),
(361, NULL, 'Nataraj Large Cutter', 'nataraj-large-cutter', '-', '82034090', '<b>Nataraj Large Cutter</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\">Crafted from tough steel for precise cutting, this cutter features anti-rust coated blades for long-lasting durability. Its smooth and effortless cutting action is enhanced by an auto-lock device, ensuring the blade stays securely in place for safe use.</div></div>', 30.00, 29.00, 29.00, 'No.', 3, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aafb4db91d3f.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:26:35', 10, 0, 0, NULL, NULL, NULL),
(362, NULL, 'Nataraj Mini Cutter | Pack of 10', 'nataraj-mini-cutter-pack-of-10', '-', '82034090', '<b>Nataraj Mini Cutter</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\">Crafted from tough steel for precise cutting, this cutter features anti-rust coated blades for long-lasting durability. Its smooth and effortless cutting action is enhanced by an auto-lock device, ensuring the blade stays securely in place for safe use.</div></div>', 140.00, 130.00, 13.00, 'No.', 7, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aafb6d90ac4d.jpg', 8, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:35:05', 10, 0, 0, NULL, NULL, NULL),
(363, NULL, 'Nataraj Gelix Gel Pen | Pack of 5', 'nataraj-gelix-gel-pen-pack-of-5', '-', '96081019', '<b>Nataraj Gelix Gel Pen | Pack of 5</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\">Specially formulated Japanese waterproof ink ensures quick drying and smooth, effortless writing. Designed to glide easily on paper without applying pressure, it delivers clear and precise strokes for a seamless writing experience.</div></div>', 35.00, 33.00, 6.60, 'No.', 6, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6aafb80a9e09f.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:40:10', 10, 29, 0, NULL, NULL, NULL),
(364, NULL, 'Nataraj Best of Luck Kit', 'nataraj-best-of-luck-kit', '-', '-', '<b>Nataraj Best of Luck Kit</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\">This ideal stationery pack contains all essential items for a seamless writing experience. Lightweight and easy to carry, it’s perfect for exams and daily use. With an attractive look, it also makes a great personal or gift option</div></div>', 15.00, 14.00, 14.00, 'Kit', 7, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aafba8fe0549.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:50:55', 10, 0, 0, NULL, NULL, NULL),
(365, NULL, 'Nataraj Study Kit', 'nataraj-study-kit', '-', '-', '<b>Nataraj Study Kit</b><div><div style=\"\"><b>Item Included</b></div><div style=\"\"><ul><li>1 x Dust Clear Eraser</li><li>1 x Pencil</li><li>1 x Sharpener</li><li>1 x SuperX Ball Pen</li><li>1 x 15 cm Scale</li></ul></div><div><div style=\"\"><b>About this item:</b></div><div style=\"\">The Nataraj Study Kit is your all-in-one solution for hassle-free study time! Thoughtfully packed with everyday stationery essentials, this kit is perfect for school-goers who love to stay organized, neat, and ready to learn.</div></div><div style=\"\"><br></div></div>', 20.00, 18.00, 18.00, 'Kit', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6aafbba16961f.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 10:55:29', 10, 0, 0, NULL, NULL, NULL),
(366, NULL, 'Apsara Platinum Extra Dark Pencil | Pack of 10', 'apsara-platinum-extra-dark-pencil-pack-of-10', '-', '96091000', '<b>Apsara Platinum Extra Dark Pencil | Pack of 10</b><div><div style=\"\"><b>Item Included</b></div><div style=\"\"><ul><li>10 x Apsara Platinum Pencils</li><li>1 x Apsara Long Point Sharpener</li><li>1 x Apsara Non Dust Grippo Eraser</li></ul></div><div><div style=\"\"><b>About this item:</b></div><div style=\"\">The platinum pencil is designed with extra dark lead, ensuring good handwriting and requiring minimal pressure while writing. Its classic design is widely admired by students across India. Made from soft wood, it is easy to sharpen, and its hexagonal shape provides a comfortable grip. The lead is extra strong, offering durability, and the pencil is both easy to sharpen and erase, making it an ideal choice for everyday writing needs.</div></div></div>', 60.00, 54.00, 5.40, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab01a4827227.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 17:39:20', 10, 0, 0, NULL, NULL, NULL),
(367, NULL, 'Apsara Spider-Man Pencil | Extra Dark Pencils | Pack of 10', 'apsara-spider-man-pencil-extra-dark-pencils-pack-of-10', '-', '96091000', '<b>Apsara Spider-Man Pencil | Extra Dark Pencils | Pack of 10</b><div><div style=\"\"><b>Item Included</b></div><div style=\"\"><ul><li>10 x Pencils</li><li>1 x Eraser</li><li>1 x Sharpener</li></ul></div><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Add adventure to your pencil box with your favourite Marvel Spider-man.</li><li>Superior transfer-film technology to make characters life-like.</li><li>Extra-dark lead and soft wood, for easy writing and sharpening.</li></ul></div></div></div>', 100.00, 95.00, 9.50, 'No.', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab01c3fc709c.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 17:47:43', 10, 0, 0, NULL, NULL, NULL),
(368, NULL, 'Apsara Disney Princess Pencil | Pack of 10', 'apsara-disney-princess-pencil-pack-of-10', '-', '96091000', '<b>Apsara Disney Princess Pencil | Pack of 10</b><div><div style=\"\"><b>Colour : Multi Colour</b></div><div style=\"\"><b>Item Included</b></div><div style=\"\"><ul><li>10 x Pencils</li><li>1 x Sharpener</li><li>1 x Topper Eraser</li></ul><div><div><b>About this item:</b></div><div>Add a touch of royal magic to your stationery with the Apsara Disney Princess Pencil! Featuring beloved princesses like Cinderella, Belle, and Ariel, these pencils offer smooth, break-resistant lead and a comfortable grip. Perfect for schoolwork or creative fun, they’re a dream come true for every Disney Princess fan!</div></div></div></div>', 100.00, 95.00, 9.50, 'No.', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab01d98b1389.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 17:53:28', 10, 0, 0, NULL, NULL, NULL),
(369, NULL, 'Apsara Disney Mickey Mouse & Friends Pencil | Pack of 10', 'apsara-disney-mickey-mouse-friends-pencil-pack-of-10', '-', '96091000', '<b>Apsara Disney Mickey Mouse &amp; Friends Pencil | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\">Bring a touch of adventure to your pencil box with Disney’s Mickey Mouse and Friends! Featuring superior transfer-film technology, these pencils bring the beloved characters to life in vivid detail. With extra dark lead and soft wood, they make writing smooth and sharpening easy. The round shape ensures a comfortable grip, while the extra strong lead is both durable and easy to erase. These pencils combine fun, functionality, and a bit of Disney magic for all your writing needs</div></div>', 100.00, 95.00, 9.50, 'No.', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab01eba1c0b5.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 17:58:18', 10, 0, 0, NULL, NULL, NULL),
(370, NULL, 'Camlin Charcoal Pencil Set | Pack of 3 Grades', 'camlin-charcoal-pencil-set-pack-of-3-grades', '-', '96091000', '<b>Camlin Charcoal Pencil Set | Pack of 3 Grades</b><div><div style=\"\"><span style=\"font-weight: bolder;\">About this item:</span><br></div><div style=\"\"><ul><li>Charcoal in three different variations</li><li>Hard variant for line work</li><li>Medium variant for strokes</li><li>Soft variant for shading and filling</li><li>Ideal for drawing and sketching</li><li>100% vegan</li></ul></div></div>', 90.00, 85.00, 8.50, 'No.', 6, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0236711ffb.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 18:18:15', 10, 0, 0, NULL, NULL, NULL),
(371, NULL, 'Camel Sketch Pen | 12 Shades | Multicolor', 'camel-sketch-pen-12-shades-multicolor', '-', '-', '<b>Camel Sketch Pen | 12 Shades |&nbsp;Multicolor</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>12 Assorted shades</li><li>Confirms to safety standard EN 71 - 3</li><li>Camel art contest entry coupon inside the pack</li><li>Best result can be seen on Drawing Paper; Pouch Packing</li><li>Item Weight: 0.13 pounds</li></ul></div></div>', 30.00, 27.00, 2.25, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab027320fc4c.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 18:34:26', 10, 0, 0, NULL, NULL, NULL);
INSERT INTO `products` (`id`, `seller_id`, `name`, `slug`, `sku`, `hsn`, `description`, `mrp`, `selling_price`, `pay_per_unit`, `unit_label`, `discount_percentage`, `gst_type`, `gst_rate`, `shipping_charge`, `category_id`, `main_image`, `stock_quantity`, `package_quantity`, `is_active`, `is_approved`, `approved_at`, `approved_by`, `rejection_reason`, `is_featured`, `is_discounted`, `created_at`, `max_quantity_per_order`, `sort_order`, `has_variations`, `brand_id`, `seller_name`, `seller_code`) VALUES
(372, NULL, 'Camel Bold Sketch Pens | 24 Shades | Multicolor', 'camel-bold-sketch-pens-24-shades-multicolor', '-', '-', '<b>Camel Bold Sketch Pens | 24 Shades | Multicolor</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>24 vibrant shades with the free Stencil inside the pack</li><li>Bright and vibrant colors</li><li>Bold tip for easy coverage of large areas</li><li>Country of Origin: India</li><li>Item Weight: 100 grams</li></ul></div></div>', 100.00, 90.00, 3.75, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab02c45d21d2.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-20 18:56:05', 10, 0, 0, NULL, NULL, NULL),
(375, NULL, 'DOMS Hexel Ballpoint Pen | Pack of 20 | Blue Ink', 'doms-hexel-ballpoint-pen-pack-of-20-blue-ink', '-', '96081019', '<b>DOMS Hexel Ballpoint Pen | Pack of 20 | Blue Ink</b><div><b>About this item:</b><br><div><div style=\"\"><ul><li>Hexagonal Pens</li><li>Smooth Writing</li><li>Leak-Proof Pens</li><li>Causes No Smudge</li><li>Available in 3 Ink Colours</li></ul></div></div></div>', 100.00, 95.00, 4.75, 'No.', 5, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab0caf74b300.webp', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 06:13:11', 10, 28, 0, NULL, NULL, NULL),
(376, NULL, 'Doms 12 Shades Poster Colours', 'doms-12-shades-poster-colours', '-', '32139000', '<b>Doms 12 Shades Poster Colours</b><div><b>About this item:</b><br><div><div style=\"\"><ul><li>Brilliant &amp; Opaque Colours</li><li>Superior Intermixing Quality</li><li>Comes with Agitator to stir the paint before use</li><li>Rich Pigments &amp; Smooth Flow</li></ul></div></div></div>', 190.00, 174.00, 14.50, 'No.', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0d229a3824.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 06:43:53', 10, 0, 0, NULL, NULL, NULL),
(377, NULL, 'DOMS Aqua Colour Cakes 30MM | 24 Colours', 'doms-aqua-colour-cakes-30mm-24-colours', '-', '32139000', '<b>DOMS Aqua Colour Cakes 30MM | 24 Colours</b><div><b>About this item:</b><br><div><div style=\"\"><ul><li>Deeply Rich &amp; Vibrant Pigments</li><li>Transparent &amp; Translucent effect on Water Colour Surfaces</li><li>Easy Intermixing</li></ul></div></div></div>', 200.00, 180.00, 7.50, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0d33d00c01.webp', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 06:48:29', 10, 0, 0, NULL, NULL, NULL),
(378, NULL, 'DOMS Modelling Clay | 12 Shades', 'doms-modelling-clay-12-shades', '-', '-', '<b>DOMS Modelling Clay | 12 Shades</b><div><b>About this item:</b><br><div><div style=\"\"><ul><li>Super Soft &amp; Easily Kneaded</li><li>Improves Hand-Eye Co-ordination &amp; Fine Motor Skills</li><li>Vibrant Shades</li><li>Specially Designed Moulded Toys</li></ul></div></div></div>', 100.00, 98.00, 8.16, 'No.', 2, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0d4a567246.webp', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 06:54:29', 10, 0, 0, NULL, NULL, NULL),
(379, NULL, 'DOMS Colour Pencils Round Tin | 24 Shades | Free Gold & Silver Color Pencil', 'doms-colour-pencils-round-tin-24-shades-free-gold-silver-color-pencil', '-', '96091000', '<b>DOMS Colour Pencils Round Tin | 24 Shades | Free Gold &amp; Silver Color Pencil</b><div><span style=\"font-weight: bolder;\">About this item:</span><b><br></b><div><div style=\"\"><ul><li>Rich Pigments &amp; Creamy Texture</li><li>Effortless &amp; Smooth Colouring Experience</li><li>Superior Blendability</li><li>Bright &amp; Vibrant Colours</li></ul></div></div></div>', 180.00, 175.00, 7.29, 'No.', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0d71a4453a.webp', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 07:04:58', 10, 0, 0, NULL, NULL, NULL),
(380, NULL, 'DOMS Art Strokes Kit', 'doms-art-strokes-kit', '-', '-', '<b>DOMS Art Strokes Kit</b><div>1N Drawing Book, 2N Pencils, 1N Oil Pastels Pack (12 Shades), 1N Eraser, 1N Bi Colour Pencil Pack (12 Shades), 1N Wax Crayons Pack (12 Shades), 1N Plastic Crayons Pack (6 Shades), 1N Sharpener<br></div><div><b>About this item:</b><br></div><div><div><ul><li>A Comprehensive Combination Kit to satisfy your Kids’ Learning needs</li><li>Perfect Combination Kit for Little Artists</li><li>Attractive Pack for the Joy of Gifting as well as Personal use</li></ul></div></div>', 150.00, 145.00, 145.00, 'Pack', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0d86d0a4c7.webp', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 07:08:38', 10, 0, 0, NULL, NULL, NULL),
(381, NULL, 'Apsara Plastic Crayons 28 Shades | Sharpener Free', 'apsara-plastic-crayons-28-shades-sharpener-free', '-', '96099090', '<b>Apsara Plastic Crayons 28 Shades | Sharpener Free</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>28 Shades: A wide range of vibrant colors for kids to explore and use creatively.</li><li>Smooth &amp; Bright Colours: Provides rich and even color application, making their artwork pop.</li><li>Better Grip: Designed with a comfortable grip, ensuring easy handling for young hands.</li><li>Non-messy: A clean option for kids, helping to reduce the mess usually associated with crayons.</li><li>Free Sharpener: Includes a sharpener, ensuring the crayons stay in tip-top shape.</li></ul></div></div>', 100.00, 95.00, 3.39, 'No.', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0da1e6e024.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 07:17:50', 10, 0, 0, NULL, NULL, NULL),
(382, NULL, 'DOMS Colour Pencils 24 Shades | Sharpener Free', 'doms-colour-pencils-24-shades-sharpener-free', '-', '96091000', '<b>DOMS Colour Pencils 24 Shades | Sharpener Free</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Product : Colour Pencil</li><li>Bright colours</li><li>High quality natural pigments</li><li>Smooth performance</li><li>Soft colour lead</li></ul></div></div>', 120.00, 115.00, 4.80, 'No.', 4, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0db7b782be.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 07:23:39', 10, 0, 0, NULL, NULL, NULL),
(383, NULL, 'DOMS Colour Pencils 50 Shades | 2 Sharpener Free', 'doms-colour-pencils-50-shades-2-sharpener-free', '-', '96091000', '<b>DOMS Colour Pencils 50 Shades | 2 Sharpener Free</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Complete Pack Contents: Bright &amp; Super Smooth Pre-Sharpened 50 Shades DOMS Colour Pencils Pack Including 2 Pencil Sharpeners FREE</li><li>Hexagonal Body Shape: Provides a firm grip for smooth and comfortable colouring experience</li><li>Rich Pigmented Colours: Bright, richly pigmented, super smooth colours with accurately bonded pencils for best results</li><li>Soft &amp; Smooth Lead: Pencil lead is soft and smooth, allowing effortless sharpening. Useful for students, artists and professionals. This product is non-toxic and safe for children</li><li>Thoughtful Gift Option: A birthday gift option for your loved ones</li><li>Pre-Sharpened Convenience: All 50 colour pencils come pre-sharpened and ready to use right out of the box</li><li>Accurate Bonding Process: Features superior bonding technology that prevents lead breakage during use and sharpening</li></ul></div></div>', 325.00, 300.00, 6.00, 'No.', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0de9c63eed.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 07:37:00', 10, 0, 0, NULL, NULL, NULL),
(384, NULL, 'DOMS Modelling Dough | 10 Shades | 150 GM with 2 Toys & 1 Hand Roller', 'doms-modelling-dough-10-shades-150-gm-with-2-toys-1-hand-roller', '-', '-', '<b>DOMS Modelling Dough | 10 Shades | 150 GM with 2 Toys &amp; 1 Hand Roller</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Soft and vibrant</li><li>Child safe colours</li><li>Can be shaped using moulding toys</li></ul></div></div>', 85.00, 84.00, 8.40, 'No.', 1, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0e049069c0.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 07:44:09', 10, 0, 0, NULL, NULL, NULL),
(385, NULL, 'DOMS Smart Kit', 'doms-smart-kit', '-', '-', '<b>DOMS Smart Kit</b><div><div style=\"\">1N Colouring Book, 1N Pencil Pack (5 Pencils), 1N Eraser Pack, 1N Bi Colour Pencil Pack (12 Shades), 1N Plastic Crayons Pack (24 Shades), 1N Oil Pastels Pack (12 Shades), 1N Water Colours Pen Pack (12 Shades), 1N Water Colour Cake (12 Shades), 1N Scale, 1N Sharpener, 1N Eraser</div></div><div style=\"\"><br></div><div style=\"\"><div><b>About this item:</b></div><div><ul><li>All-in-one Blend of Drawing &amp; Colouring Materials to spark the Imagination</li><li>Special Designed Kit for Children with Artistic Minds</li><li>Ideal for Gifting</li></ul></div></div>', 500.00, 460.00, 460.00, 'Kit', 8, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0ea70eafe9.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 08:23:27', 10, 0, 0, NULL, NULL, NULL),
(386, NULL, 'DOMS Plastic Crayons | 28 Shades | 1 Eraser & 1 Sharpener', 'doms-plastic-crayons-28-shades-1-eraser-1-sharpener', '-', '96099090', '<b>DOMS Plastic Crayons | 28 Shades | 1 Eraser &amp; 1 Sharpener</b><div><b>About this item:<br></b><div><div style=\"\"><ul><li>Comfortable &amp; Slip-Proof Grip</li><li>Smudge – Free Erasing</li><li>Smooth Colour Application</li><li>Non-Toxic &amp; User – Friendly</li></ul></div></div></div>', 100.00, 90.00, 3.21, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0ec381f174.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 08:35:04', 10, 0, 0, NULL, NULL, NULL),
(387, NULL, 'DOMS Wow Kit', 'doms-wow-kit', '-', '-', '<b>DOMS Wow Kit</b><div>1N Wax Crayons Pack (12 Shades), 1N Colour Pencil Pack (6 Shades), 1N Eraser, 1N Scale, 1N Pencil, 1N Sharpener, 1N Water Colour Pen Pack (6 Shades)<br></div><div><br></div><div><b>About this item:</b></div><div><div><ul><li>A Comprehensive Combination Kit to satisfy your Kids’ Learning Needs</li><li>It is a Well Curated Kit that includes a mix of Colours &amp; Stationery Products</li></ul></div></div>', 50.00, 45.00, 45.00, 'Kit', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab0edc0e3942.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 08:41:36', 10, 0, 0, NULL, NULL, NULL),
(388, NULL, 'DOMS Non-Toxic Chalk & Oil Pastel Holder | Set of 5 Pcs', 'doms-non-toxic-chalk-oil-pastel-holder-set-of-5-pcs', '-', '96099090', '<b>DOMS Non-Toxic Chalk &amp; Oil Pastel Holder | Set of 5 Pcs</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Product : Chalk Holder, Available Color : Green, Yellow, Orange, Blue</li><li>Specially designed for convinient writing</li><li>Ensures complete usage of both chalk and oil pastels</li><li>Comes in four beautiful colours</li></ul></div></div>', 100.00, 98.00, 19.60, 'No.', 2, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab103439d48c.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 10:13:23', 10, 0, 0, NULL, NULL, NULL),
(389, NULL, 'Doms INXKLIK Ball Point Pen | Pack of 5', 'doms-inxklik-ball-point-pen-pack-of-5', '-', '96081019', '<b>Doms INXKLIK Ball Point Pen | Pack of 5</b><div><div>0.7mm needle tip Lv ink technology for smooth writing Textured grip for fatigue less writing Available in 5 body colors<br></div></div>', 30.00, 28.00, 5.60, 'No.', 7, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab10921341e6.webp', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 10:38:25', 10, 27, 0, NULL, NULL, NULL),
(390, NULL, 'Doms Water Colour Tube | 18 Shades', 'doms-water-colour-tube-18-shades', '-', '32139000', '<b>Doms Water Colour Tube | 18 Shades</b><div><b>About this item:</b></div><div><div style=\"\"><ul><li>🎨 18 Brilliant Shades for Endless Creativity Includes 18 rich and vibrant watercolour tubes perfect for blending, layering, and creating depth in your artwork.</li><li>🌈 Deeply Pigmented for Bold Results Formulated with high-quality pigments to deliver intense, long-lasting colour on a variety of watercolour papers and surfaces.</li><li>💧 Transparent &amp; Translucent Effects Achieve stunning washes and light glazes with excellent transparency — ideal for both delicate and bold styles.</li><li>⏱️ Quick-Drying Formula Reduces smudging and allows for faster layering and detailing. Perfect for on-the-go painting or classroom settings.</li><li>🖌️ Artist-Friendly &amp; Easy to Use Whether you\'re a beginner, student, or professional, Doms Water Colour Tubes offer smooth application and easy blending with brush or water.</li></ul></div><div><b><br></b></div></div>', 160.00, 143.00, 7.94, 'No.', 11, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab10ae086d0b.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 10:45:52', 10, 0, 0, NULL, NULL, NULL),
(391, NULL, 'Luxor Finewriter 05 | Pack 0f 10', 'luxor-finewriter-05-pack-0f-10', '-', '96081019', '<b>Luxor Finewriter 05 | Pack 0f 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Fine Liner Pen</li><li>Ink color: Blue Warranty: 2 Years</li><li>Country of Origin: India</li><li>Item Type Name: Pen Refills; Closure Type: Click-Off Cap</li><li>Fine Liner Pen</li><li>Ink color: Blue Warranty: 2 Years</li><li>Country of Origin: India</li></ul></div></div>', 100.00, 94.00, 9.40, 'No.', 6, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab10f070bc74.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 11:03:35', 10, 26, 0, NULL, NULL, NULL),
(392, NULL, 'Doms Superio Super Dark Graphite Pencils | Pack of 10 | With FREE 1 Eraser 1 Pencil Sharpner 1 Point Protection Cap & 1 Cleaning Brush| With Storage Case Metal Body', 'doms-superio-super-dark-graphite-pencils-pack-of-10-with-free-1-eraser-1-pencil-sharpner-1-point-protection-cap-1-cleaning-brush-with-storage-case-metal-body', '-', '96091000', '<b>Doms Superio Super Dark Graphite Pencils | Pack of 10 | With FREE 1 Eraser 1 Pencil Sharpner 1 Point Protection Cap &amp; 1 Cleaning Brush| With Storage Case Metal Body</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Smooth Writing Experience: The high-quality graphite core offers effortless writing and drawing.</li><li>10-Pencil Tin Box: Includes 10 pencils, a sharpener, an eraser pencil with brush, and a point protection cap.</li><li>Easy to Sharpen: Compatible with standard sharpeners, allowing for a fine point every time.</li><li>Break-Resistant Lead: Durable graphite reduces the chances of snapping during writing or sharpening.</li><li>Ergonomic Triangular Shape: Designed for a comfortable grip, reducing hand fatigue during extended use.</li></ul></div></div>', 125.00, 120.00, 12.00, 'No.', 4, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab111b37a884.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 11:14:59', 10, 0, 0, NULL, NULL, NULL),
(393, NULL, 'DOMS Artist Kneadable Eraser | Pack of 2', 'doms-artist-kneadable-eraser-pack-of-2', '-', '40169200', '<b>DOMS Artist Kneadable Eraser | Pack of 2</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Amariz kneadable erasers are specially crafted for sketching enthusiasts, where they can easily pickup charcoal/graphite from their artwork for better detailing.</li><li>Our kneadable erasers are very easily kneadable and soft, making it easily to give it any preferred shape for erasing abilities and also for storage in any shape.</li><li>Amariz kneadable erasers are the first ever kneadable erasers to provide a tin box for the storage of erasers, increasing life of use and also making it easy to carry.</li></ul></div></div>', 110.00, 99.00, 49.50, 'No.', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab113949e2e3.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 11:21:26', 10, 0, 0, NULL, NULL, NULL),
(394, NULL, 'Apsara White Dustless Chalks | Pack of 10', 'apsara-white-dustless-chalks-pack-of-10', '-', '96099090', '<b>Apsara White Dustless Chalks | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>White dustless chalks</li><li>No. 1 recommended stationary by teachers for children</li><li>Designed to aid legibility</li></ul></div></div>', 200.00, 179.00, 17.90, 'No.', 11, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab1163941245.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 11:34:17', 10, 0, 0, NULL, NULL, NULL),
(395, NULL, 'DOMS Extra Long Eraser Box | Pack of 20', 'doms-extra-long-eraser-box-pack-of-20', '-', '40169200', '<b>DOMS Extra Long Eraser Box | Pack of 20</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Soft &amp; Easy to use</li><li>Dust Free Erasing</li><li>Superior Quality</li></ul></div></div>', 100.00, 95.00, 4.75, 'No.', 5, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab1182830a68.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 11:39:40', 10, 0, 0, NULL, NULL, NULL),
(396, NULL, 'DOMS Neon Rubber Tipped Pencils Box | Pack Of 10', 'doms-neon-rubber-tipped-pencils-box-pack-of-10', '-', '96091000', '<b>DOMS Neon Rubber Tipped Pencils Box | Pack Of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Product : Doms Neon Rubber Tipped HB/2 Graphite Pencils Box Pack | 50 Pencils</li><li>An Ergonomic Writing Tool Designed To Provide A Smooth &amp; Stress-Free Writing Experience.</li><li>Its Round Design Provides A Comfortable Grip, Even With Long Periods Of Use. The Lead Is Extra Dark, Allowing For Precise Details When Sketching &amp; Scribbling.</li><li>Non-Toxic, Easy To Use &amp; Safe For Childrens.</li><li>Perfect For Artists &amp; Students, Durable Set Of Pencils That Will Last For A Long Time.</li></ul></div></div>', 70.00, 66.00, 6.60, 'No.', 6, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab11a8c135e1.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 11:52:44', 10, 0, 0, NULL, NULL, NULL),
(397, NULL, 'DOMS All The Best Kit', 'doms-all-the-best-kit', '-', '-', '<b>DOMS All The Best Kit</b><div>1N Pencil, 1N Eraser, 1N Sharpener, 1N Scale, 1N Ballpoint Pen<br></div><div><br></div><div><b>About this item:</b></div><div><div style=\"\"><ul><li>All-in-one Blend of Drawing &amp; Colouring Materials to spark the Imagination</li><li>Perfect Combination Kit for Little Artists</li><li>Ideal for Gifting</li></ul></div></div>', 20.00, 19.00, 19.00, 'Kit', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab11c98d2b83.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 12:01:28', 10, 0, 0, NULL, NULL, NULL),
(398, NULL, 'DOMS Ultimate Dark Triangle Pencil | Pack of 10', 'doms-ultimate-dark-triangle-pencil-pack-of-10', '-', '96091000', '<b>DOMS Ultimate Dark Triangle Pencil | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Product : Graphie Pencil, Pack of 1.</li><li>Premium quality graphite lead</li><li>Superior quality writing</li><li>Accurate bonding process</li><li>Soft and well treated wood</li></ul></div><div style=\"font-weight: bold;\"><br></div></div>', 60.00, 58.00, 5.80, 'No.', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab11f45f1b53.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 12:12:53', 10, 0, 0, NULL, NULL, NULL),
(399, NULL, 'DOMS Bi-Colour Pencils | 24 Shades | 12 Pencils', 'doms-bi-colour-pencils-24-shades-12-pencils', '-', '96091000', '<b>DOMS Bi-Colour Pencils | 24 Shades | 12 Pencils</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Product : Colour Pencil</li><li>Bright colours</li><li>High quality natural pigments</li><li>Smooth performance</li><li>Soft colour lead</li></ul></div></div>', 120.00, 115.00, 9.58, 'No.', 4, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab1224833e12.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 12:25:44', 10, 0, 0, NULL, NULL, NULL),
(400, NULL, 'DOMS Speedz Kit', 'doms-speedz-kit', '-', '-', '<b>DOMS Speedz Kit</b><div><div style=\"\">1N Tin Box, 1N Wax Crayons Pack (12 Shades), 2N Pencil, 1N Eraser, 1N Scale, 1N Sharpener</div><div style=\"\"><div><b>About this item:</b></div><div><ul><li>All Basic Stationery Needs in One</li><li>Perfect Combination Kit for Little Artists</li><li>Ideal for Gifting</li><li>Pack to make Learning Fun</li></ul></div></div></div>', 150.00, 140.00, 140.00, 'Kit', 7, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab1245faa282.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 12:34:39', 10, 0, 0, NULL, NULL, NULL),
(401, NULL, 'DOMS Karbon Eraser Tipped Super Dark Pencil', 'doms-karbon-eraser-tipped-super-dark-pencil', '-', '96091000', '<b>DOMS Karbon Eraser Tipped Super Dark Pencil</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Super Dark</li><li>Eraser Tipped</li><li>Hexagonal Shaped Pencil</li><li>Ergonomic Design</li><li>Easy to sharpen</li></ul></div></div>', 70.00, 68.00, 6.80, 'No.', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab12877960a1.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 12:52:07', 10, 0, 0, NULL, NULL, NULL),
(402, NULL, 'DOMS Painting Kit', 'doms-painting-kit', '-', '-', '<b>DOMS Painting Kit</b><div>1N Drawing Book, 1N Bi Colour Pencil Pack (12 Shades), 1N Oil Pastel Pack (12 Shades), 1N Water Colour Pen Pack (12 Shades), 1N Water Colour Cake Pack (12 Shades), 1N Glitter Colour Pack(3 Shades), 1N Pencil, 1N Eraser, 1N Sharpener<br></div><div><br></div><div><div><b>About this item:</b></div><div><ul><li>All-in-one Blend of Drawing &amp; Colouring Materials to Spark the Imagination</li><li>Perfectly Compliments a Child’s Painting Instincts</li><li>Ideal for Gifting</li></ul></div></div>', 199.00, 180.00, 180.00, 'Kit', 10, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab129ff2d93b.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 12:58:39', 10, 0, 0, NULL, NULL, NULL),
(403, NULL, 'DOMS X1 X-TRA Super Dark Pencil | Pack of 10', 'doms-x1-x-tra-super-dark-pencil-pack-of-10', '-', '96091000', '<b>DOMS X1 X-TRA Super Dark Pencil | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>X-TRA Super Dark</li><li>Hexagonal Shaped Pencil</li><li>Ergonomic Design</li><li>Easy to sharpen</li></ul></div></div>', 60.00, 58.00, 5.80, 'No.', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab12afe2514b.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 13:02:54', 10, 0, 0, NULL, NULL, NULL),
(404, NULL, 'DOMS Inxglo Glow in Dark Ballpen - Blue Ink - 0.7mm Needle Tip | Pack of 10', 'doms-inxglo-glow-in-dark-ballpen-blue-ink-0-7mm-needle-tip-pack-of-10', '-', '96081019', '<b>DOMS Inxglo Glow in Dark Ballpen - Blue Ink - 0.7mm Needle Tip | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Self-Illuminating Ink – Glows in the dark without any external light source.</li><li>Smooth Writing – Same effortless ink flow as a regular ball pen with 0.7mm Tip</li><li>Transparent/Neon Barrel – Lets users see the glowing ink inside.</li><li>Dual Mode – Visible as a light color in daylight, glows vibrantly in darkness.</li></ul></div></div>', 100.00, 96.00, 9.60, 'No.', 4, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab131b50bffc.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-21 13:31:33', 10, 25, 0, NULL, NULL, NULL),
(405, NULL, 'DOMS Rocket Point Erasner (Eraser + Sharpener)', 'doms-rocket-point-erasner-eraser-sharpener', '-', '40169200', '<b>DOMS Rocket Point Erasner&nbsp;(Eraser + Sharpener)</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>2-in-1 Sharpener with Built-in Eraser</li><li>Fun &amp; Playful Design</li><li>Dust Collector</li><li>Fine – Point Sharpening</li><li>Assorted Shapes &amp; Colours</li><li>Firm Grip</li></ul></div></div>', 20.00, 19.00, 19.00, 'No.', 5, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab206d15a7d5.jpg', 9, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-22 04:40:49', 10, 0, 0, NULL, NULL, NULL),
(406, NULL, 'DOMS Zillion Pencil | X-Tra Super Dark | Hexagonal Shaped Pencil | Pack of 10', 'doms-zillion-pencil-x-tra-super-dark-hexagonal-shaped-pencil-pack-of-10', '-', '96091000', '<b>DOMS Zillion Pencil | X-Tra Super Dark | Hexagonal Shaped Pencil | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Hexagonal Shaped Pencil</li><li>X-Tra Super Dark</li><li>Ergonomic Shape</li><li>Easy to Sharpen</li><li>Premium dual-tone design with Gold &amp; Silver on one side and sleek Black on the other</li></ul></div></div>', 60.00, 58.00, 5.80, 'No.', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab208f3071de.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-22 04:49:55', 10, 0, 0, 2, NULL, NULL),
(407, NULL, 'DOMS Dommymate Plastic Crayons', 'doms-dommymate-plastic-crayons', '-', '96099090', '<b>DOMS Dommymate Plastic Crayons</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Comfortable &amp; Slip-Proof Grip</li><li>Smudge – Free Erasing</li><li>Smooth Colour Application</li><li>Non-Toxic &amp; User – Friendly</li></ul></div></div>', 100.00, 95.00, 7.91, 'No.', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab20b79f3219.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-22 05:00:41', 10, 0, 0, 2, NULL, NULL),
(408, NULL, 'DOMS Water Colour Pencils | 12 Assorted Shades', 'doms-water-colour-pencils-12-assorted-shades', '-', '96091000', '<b>DOMS Water Colour Pencils</b><div>12 Assorted Shades, 1N Water Brush, 1N Sharpener, 5N Drawing Sheets<br></div><div><div><b>About this item:</b></div><div><ul><li>Rich and Vibrant Water Colour Lead Pencils</li><li>Gives a Translucent Colour Effect</li><li>Bright &amp; Brilliant Colours</li><li>Uniform Dispersion of Colours</li></ul></div></div>', 175.00, 170.00, 14.16, 'No.', 3, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab2103551ba8.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-22 05:20:53', 10, 0, 0, 2, NULL, NULL),
(409, NULL, 'DOMS MECHANICAL Pencil 0.7 mm | Pack of 10', 'doms-mechanical-pencil-0-7-mm-pack-of-10', '-', '96091000', '<b>DOMS MECHANICAL Pencil 0.7 mm | Pack of 10</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Smooth Writing</li><li>Vibrant Colours</li><li>Ergonomic Grip</li></ul></div></div>', 100.00, 95.00, 9.50, 'No.', 5, 'sgst_cgst', 18.00, NULL, 21, 'uploads/products/6ab4a8682319b.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-24 04:34:48', 10, 0, 0, 2, NULL, NULL),
(410, NULL, 'DOMS Everyday Ball Pen | Pack of 20 | Blue Ink', 'doms-everyday-ball-pen-pack-of-20-blue-ink', '-', '96081019', '<b>DOMS Everyday Ball Pen | Pack of 20 | Blue Ink</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Easy to Write</li><li>Sleek Design</li><li>Available in 3 Ink Colours. Blue, Black, Red</li></ul></div></div>', 100.00, 90.00, 4.60, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab4c29f2abe4.jpg', 9, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-24 06:26:39', 10, 24, 0, 2, NULL, NULL),
(411, NULL, 'DOMS E-RACER ERASER | Pack of 2', 'doms-e-racer-eraser-pack-of-2', '-', '40169200', '<b>DOMS E-RACER ERASER | Pack of 2</b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Fun &amp; Playful Design</li><li>Soft &amp; Easy to use</li><li>Available in Vibrant Colours</li><li>Dust Free Erasing</li><li>Superior Quality</li></ul></div></div>', 20.00, 19.00, 9.50, 'No.', 5, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ab4c9f6d9695.jpg', 0, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-09-24 06:57:58', 10, 0, 0, 2, NULL, NULL),
(412, NULL, 'Parker Beta Neo', 'parker-beta-neo', '-', '96081019', '<div><b>Parker Beta Neo</b></div><div>Ball Pen With Coated Clip</div><div><br></div><div><div><b>About this item:</b></div><div><ul><li>Durable Parker Beta Neo Ball Pen with stylish chrome trim.</li><li>Suited for office professionals, college students, and personal use.</li><li>Ergonomic design ensures comfort and ease of writing.</li><li>Sleek and modern look enhances your professional image.</li><li>Reliable performance for everyday writing tasks.</li></ul></div></div>', 170.00, 153.00, 153.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ad9a2fb3e379.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-10-22 05:45:31', 10, 3, 0, NULL, NULL, NULL),
(413, NULL, 'Parker Beta Premium Gold', 'parker-beta-premium-gold', '-', '96081019', '<b>Parker Beta Premium Gold Trim Fountain Pen</b><br><b>with 1 Ink Cartridge | Ink Color - Blue | Intutive Design | Comfortable Grip</b><div><b><br></b><div><div style=\"\"><b>About this item:</b></div><div style=\"\"><ul><li>Elegant Gold Trim Design: Features a black body with Chrome trim, offering a timeless and sophisticated look</li><li>Smooth &amp; Consistent Writing: Comes with a high-quality blue ink cartridge for a smooth, uninterrupted writing experience</li><li>Intuitive, Comfortable Grip: Designed for ease of use with an ergonomic grip that ensures comfort during extended writing sessions</li><li>Refillable Fountain Pen: Easily refillable for long-term use, making it both eco-friendly and cost-effective</li><li>Includes Free Ink Cartridge: Comes with one blue ink cartridge, ensuring you’re ready to write right out of the box</li><li>Perfect for Everyday Use: Ideal for personal writing, professional settings, or as a gift for fountain pen lovers</li></ul></div></div></div>', 260.00, 234.00, 234.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ad9a6faa936f.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-10-22 06:02:34', 10, 5, 0, NULL, NULL, NULL),
(414, NULL, 'Parker Vector Standard Gold', 'parker-vector-standard-gold', '-', '96081019', '<div><b>Parker Vector Standard Gold Trim Fountain Pen</b></div><div>Metallic Blue Body Colour | Ink Colour – Blue<br><br></div><div><div><b>About this item:</b></div><div><ul><li>Cap on/ Cap off action</li><li>Standard nib grades available in fine</li><li>For refilling use ink cartridge or ink converter with ink bottle</li><li>Gift oriented full blister packaging</li></ul></div></div>', 325.00, 292.00, 292.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ad9ab2e47350.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-10-22 06:20:30', 10, 6, 0, NULL, NULL, NULL),
(415, NULL, 'Parker Classic Gold', 'parker-classic-gold', '-', '96081019', '<div><b>Parker Classic Gold Trim Ball Pen</b></div><div>Blue Ink | Ballpoint Pen for Smooth Writing | Iconic Parker Craftsmanship | Premium Gifting Choice</div><div><br></div><div><div><b>About this item:</b></div><div><ul><li>Smooth Writing Experience – Fitted with blue ink for clean, consistent, and comfortable writing every time.</li><li>Refillable &amp; Durable – Designed for long-lasting use; easily refillable for continued performance and value.</li><li>Timeless Gold Trim Design – Classic Parker craftsmanship meets modern elegance with a sophisticated gold-accented finish.</li><li>Everyday Elegance – A reliable and stylish ballpoint pen perfect for office, school, or daily writing tasks.</li><li>Premium Gift Choice – Features a sleek stainless steel body with elegant gold trim, making it an ideal gift for professionals, students, and pen enthusiasts.</li></ul></div></div>', 750.00, 675.00, 675.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ad9bb98d5854.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-10-22 07:30:32', 10, 7, 0, NULL, NULL, NULL),
(416, NULL, 'Parker Vector Standard Gold Roller Ball Pen', 'parker-vector-standard-gold-roller-ball-pen', '-', '96081019', '<div><b>Parker Vector Standard Gold Trim Roller Ball Pen</b></div><div>Ink Color - Blue | Black Body Color | Ergonomic Design | Built for Long Writing Sessions</div><div><div><b>About this item:</b></div><div><ul><li>Elegant Gold Trim Design: Features a sophisticated black body with a classic gold trim for a stylish, professional look</li><li>Smooth &amp; Consistent Writing: Includes a high-quality blue ink refill that ensures a fluid, consistent writing experience</li><li>Ergonomic Design for Comfort: Crafted for long writing sessions with an ergonomic shape, reducing hand strain and enhancing comfort</li><li>Refillable Rollerball Pen: Designed for long-term use, allowing easy replacement of Parker rollerball refills—eco-friendly and cost-effective</li><li>Perfect for Daily Use: Ideal for work, school, and personal use, providing a premium writing experience without the premium price</li></ul></div></div>', 480.00, 432.00, 432.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ad9bda91a14e.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-10-22 07:39:21', 10, 2, 0, NULL, NULL, NULL),
(417, NULL, 'Parker Classic Matte Black Chrome Trim Ball Pen', 'parker-classic-matte-black-chrome-trim-ball-pen', '-', '96081019', '<div><b>Parker Classic Matte Black Chrome Trim Ball Pen</b></div><div>Ink Color - Blue | Perfect for Professional Use | Elite Pen for Corporate Purposes</div><div><div><b>About this item:</b></div><div><ul><li>Perfect for office professionals, college students, and personal use.</li><li>Matte black finish with chrome trim, stylish and reliable.</li><li>Smooth writing experience for various tasks.</li><li>A high-quality pen that combines style and functionality.</li><li>Suitable for those who value a stylish and reliable writing tool.</li></ul></div></div>', 580.00, 522.00, 522.00, 'No.', 10, 'sgst_cgst', 18.00, NULL, 2, 'uploads/products/6ad9bf5982f22.jpg', 10, 1, 1, 1, NULL, NULL, NULL, 0, 1, '2026-10-22 07:46:33', 10, 4, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_attributes`
--

CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(170) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_attributes`
--

INSERT INTO `product_attributes` (`id`, `name`, `slug`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Safety Shoes Size', 'safety-shoes-size', 1, 1, '2026-06-03 07:52:46', '2026-06-03 07:52:46'),
(3, 'colour', 'colour', 2, 1, '2026-08-24 10:08:07', '2026-08-24 10:08:07');

-- --------------------------------------------------------

--
-- Table structure for table `product_attribute_values`
--

CREATE TABLE `product_attribute_values` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(150) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_attribute_values`
--

INSERT INTO `product_attribute_values` (`id`, `attribute_id`, `value`, `sort_order`, `created_at`) VALUES
(1, 1, '5', 1, '2026-06-03 07:52:46'),
(2, 1, '8', 2, '2026-06-03 07:52:46'),
(3, 1, '12', 3, '2026-06-03 07:52:46'),
(4, 1, '14', 4, '2026-06-03 07:52:46'),
(8, 3, 'Black', 1, '2026-08-24 10:08:07'),
(9, 3, 'Blue', 2, '2026-08-24 10:08:07'),
(10, 3, 'Pink', 3, '2026-09-19 07:11:59');

-- --------------------------------------------------------

--
-- Table structure for table `product_category_assignments`
--

CREATE TABLE `product_category_assignments` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_category_assignments`
--

INSERT INTO `product_category_assignments` (`product_id`, `category_id`, `is_primary`, `created_at`) VALUES
(6, 1, 0, '2026-09-24 07:22:30'),
(6, 3, 1, '2026-09-24 07:22:30'),
(7, 3, 1, '2026-09-24 07:22:55'),
(8, 3, 1, '2026-09-24 07:23:14'),
(9, 1, 1, '2026-09-24 07:24:11'),
(10, 1, 1, '2026-09-24 07:24:35'),
(41, 17, 1, '2026-08-29 11:49:33'),
(72, 7, 1, '2026-09-24 08:46:01'),
(77, 18, 1, '2026-08-29 11:49:33'),
(78, 18, 1, '2026-08-29 11:49:33'),
(79, 18, 1, '2026-08-29 11:49:33'),
(80, 18, 1, '2026-08-29 11:49:33'),
(81, 18, 1, '2026-08-29 11:49:33'),
(83, 18, 1, '2026-08-29 11:49:33'),
(84, 18, 1, '2026-08-29 11:49:33'),
(85, 18, 1, '2026-08-29 11:49:33'),
(86, 18, 1, '2026-08-29 11:49:33'),
(87, 18, 1, '2026-08-29 11:49:33'),
(88, 18, 1, '2026-08-29 11:49:33'),
(89, 18, 1, '2026-08-29 11:49:33'),
(90, 18, 1, '2026-08-29 11:49:33'),
(91, 18, 1, '2026-08-29 11:49:33'),
(92, 18, 1, '2026-08-29 11:49:33'),
(93, 18, 1, '2026-08-29 11:49:33'),
(94, 16, 1, '2026-08-29 11:49:33'),
(95, 16, 1, '2026-08-29 11:49:33'),
(96, 16, 1, '2026-08-29 11:49:33'),
(97, 16, 1, '2026-08-29 11:49:33'),
(98, 16, 1, '2026-08-29 11:49:33'),
(99, 16, 1, '2026-08-29 11:49:33'),
(100, 16, 1, '2026-08-29 11:49:33'),
(101, 16, 1, '2026-08-29 11:49:33'),
(102, 16, 1, '2026-08-29 11:49:33'),
(103, 16, 1, '2026-08-29 11:49:33'),
(104, 16, 1, '2026-08-29 11:49:33'),
(105, 16, 1, '2026-08-29 11:49:33'),
(106, 16, 1, '2026-08-29 11:49:33'),
(107, 16, 1, '2026-08-29 11:49:33'),
(108, 16, 1, '2026-08-29 11:49:33'),
(109, 16, 1, '2026-08-29 11:49:33'),
(110, 16, 1, '2026-08-29 11:49:33'),
(111, 16, 1, '2026-08-29 11:49:33'),
(112, 16, 1, '2026-08-29 11:49:33'),
(113, 16, 1, '2026-08-29 11:49:33'),
(114, 16, 1, '2026-08-29 11:49:33'),
(115, 16, 1, '2026-08-29 11:49:33'),
(116, 16, 1, '2026-08-29 11:49:33'),
(117, 16, 1, '2026-08-29 11:49:33'),
(118, 16, 1, '2026-08-29 11:49:33'),
(121, 20, 1, '2026-08-29 11:49:33'),
(122, 20, 1, '2026-08-29 11:49:33'),
(123, 20, 1, '2026-08-29 11:49:33'),
(124, 20, 1, '2026-08-29 11:49:33'),
(125, 20, 1, '2026-08-29 11:49:33'),
(126, 20, 1, '2026-08-29 11:49:33'),
(127, 20, 1, '2026-08-29 11:49:33'),
(128, 20, 1, '2026-08-29 11:49:33'),
(129, 20, 1, '2026-08-29 11:49:33'),
(130, 20, 1, '2026-08-29 11:49:33'),
(131, 20, 1, '2026-08-29 11:49:33'),
(132, 26, 1, '2026-08-29 11:49:33'),
(133, 26, 1, '2026-08-29 11:49:33'),
(134, 26, 1, '2026-08-29 11:49:33'),
(135, 26, 1, '2026-08-29 11:49:33'),
(136, 26, 1, '2026-08-29 11:49:33'),
(137, 26, 1, '2026-08-29 11:49:33'),
(138, 26, 1, '2026-08-29 11:49:33'),
(139, 26, 1, '2026-08-29 11:49:33'),
(140, 28, 1, '2026-08-29 11:49:33'),
(141, 28, 1, '2026-08-29 11:49:33'),
(142, 28, 1, '2026-08-29 11:49:33'),
(143, 28, 1, '2026-08-29 11:49:33'),
(144, 28, 1, '2026-08-29 11:49:33'),
(145, 28, 1, '2026-08-29 11:49:33'),
(146, 28, 1, '2026-08-29 11:49:33'),
(147, 29, 1, '2026-08-29 11:49:33'),
(148, 29, 1, '2026-08-29 11:49:33'),
(149, 29, 1, '2026-08-29 11:49:33'),
(150, 29, 1, '2026-08-29 11:49:33'),
(151, 29, 1, '2026-08-29 11:49:33'),
(152, 29, 1, '2026-08-29 11:49:33'),
(154, 25, 1, '2026-08-29 11:49:33'),
(155, 25, 1, '2026-08-29 11:49:33'),
(156, 25, 1, '2026-08-29 11:49:33'),
(157, 25, 1, '2026-08-29 11:49:33'),
(158, 25, 1, '2026-08-29 11:49:33'),
(159, 25, 1, '2026-08-29 11:49:33'),
(160, 25, 1, '2026-08-29 11:49:33'),
(161, 25, 1, '2026-08-29 11:49:33'),
(162, 25, 1, '2026-08-29 11:49:33'),
(163, 25, 1, '2026-08-29 11:49:33'),
(164, 30, 1, '2026-08-29 11:49:33'),
(165, 30, 1, '2026-08-29 11:49:33'),
(166, 30, 1, '2026-08-29 11:49:33'),
(167, 30, 1, '2026-08-29 11:49:33'),
(168, 30, 1, '2026-08-29 11:49:33'),
(169, 30, 1, '2026-08-29 11:49:33'),
(170, 30, 1, '2026-08-29 11:49:33'),
(171, 30, 1, '2026-08-29 11:49:33'),
(172, 30, 1, '2026-08-29 11:49:33'),
(173, 30, 1, '2026-08-29 11:49:33'),
(174, 30, 1, '2026-08-29 11:49:33'),
(175, 30, 1, '2026-08-29 11:49:33'),
(176, 30, 1, '2026-08-29 11:49:33'),
(177, 30, 1, '2026-08-29 11:49:33'),
(178, 30, 1, '2026-08-29 11:49:33'),
(179, 30, 1, '2026-08-29 11:49:33'),
(180, 30, 1, '2026-08-29 11:49:33'),
(181, 30, 1, '2026-08-29 11:49:33'),
(182, 30, 1, '2026-08-29 11:49:33'),
(183, 30, 1, '2026-08-29 11:49:33'),
(184, 30, 1, '2026-08-29 11:49:33'),
(185, 30, 1, '2026-08-29 11:49:33'),
(186, 30, 1, '2026-08-29 11:49:33'),
(187, 30, 1, '2026-08-29 11:49:33'),
(188, 30, 1, '2026-08-29 11:49:33'),
(189, 30, 1, '2026-08-29 11:49:33'),
(190, 30, 1, '2026-08-29 11:49:33'),
(191, 30, 1, '2026-08-29 11:49:33'),
(192, 30, 1, '2026-08-29 11:49:33'),
(193, 30, 1, '2026-08-29 11:49:33'),
(194, 30, 1, '2026-08-29 11:49:33'),
(195, 30, 1, '2026-08-29 11:49:33'),
(196, 30, 1, '2026-08-29 11:49:33'),
(197, 30, 1, '2026-08-29 11:49:33'),
(198, 30, 1, '2026-08-29 11:49:33'),
(199, 30, 1, '2026-08-29 11:49:33'),
(200, 30, 1, '2026-08-29 11:49:33'),
(201, 30, 1, '2026-08-29 11:49:33'),
(202, 30, 1, '2026-08-29 11:49:33'),
(203, 17, 1, '2026-08-29 11:49:33'),
(204, 17, 1, '2026-08-29 11:49:33'),
(205, 17, 1, '2026-08-29 11:49:33'),
(206, 17, 1, '2026-08-29 11:49:33'),
(207, 17, 1, '2026-08-29 11:49:33'),
(208, 17, 1, '2026-08-29 11:49:33'),
(209, 17, 1, '2026-08-29 11:49:33'),
(210, 17, 1, '2026-08-29 11:49:33'),
(211, 17, 1, '2026-08-29 11:49:33'),
(212, 17, 1, '2026-08-29 11:49:33'),
(213, 17, 1, '2026-08-29 11:49:33'),
(215, 19, 1, '2026-08-29 11:49:33'),
(216, 19, 1, '2026-08-29 11:49:33'),
(217, 19, 1, '2026-08-29 11:49:33'),
(218, 19, 1, '2026-08-29 11:49:33'),
(219, 19, 1, '2026-08-29 11:49:33'),
(220, 19, 1, '2026-08-29 11:49:33'),
(221, 31, 1, '2026-08-29 11:49:33'),
(222, 31, 1, '2026-08-29 11:49:33'),
(223, 31, 1, '2026-08-29 11:49:33'),
(224, 31, 1, '2026-08-29 11:49:33'),
(225, 31, 1, '2026-08-29 11:49:33'),
(226, 31, 1, '2026-08-29 11:49:33'),
(227, 31, 1, '2026-08-29 11:49:33'),
(228, 32, 1, '2026-08-29 11:49:33'),
(229, 32, 1, '2026-08-29 11:49:33'),
(230, 32, 1, '2026-08-29 11:49:33'),
(231, 32, 1, '2026-08-29 11:49:33'),
(232, 32, 1, '2026-08-29 11:49:33'),
(233, 32, 1, '2026-08-29 11:49:33'),
(234, 32, 1, '2026-08-29 11:49:33'),
(235, 9, 0, '2026-09-24 12:20:03'),
(235, 32, 1, '2026-09-24 12:20:03'),
(240, 22, 0, '2026-09-11 12:43:27'),
(240, 34, 1, '2026-09-11 12:43:27'),
(241, 22, 0, '2026-10-30 11:02:31'),
(241, 34, 1, '2026-10-30 11:02:31'),
(242, 22, 0, '2026-09-11 12:46:23'),
(242, 34, 1, '2026-09-11 12:46:23'),
(243, 22, 0, '2026-09-11 12:47:40'),
(243, 34, 1, '2026-09-11 12:47:40'),
(244, 22, 0, '2026-09-11 12:48:45'),
(244, 34, 1, '2026-09-11 12:48:45'),
(245, 2, 0, '2026-10-06 06:54:13'),
(245, 49, 1, '2026-10-06 06:54:13'),
(246, 2, 0, '2026-10-06 06:54:00'),
(246, 49, 1, '2026-10-06 06:54:00'),
(247, 2, 0, '2026-10-06 06:54:22'),
(247, 49, 1, '2026-10-06 06:54:22'),
(248, 2, 0, '2026-10-06 06:54:30'),
(248, 49, 1, '2026-10-06 06:54:30'),
(249, 2, 0, '2026-10-06 07:15:56'),
(249, 50, 1, '2026-10-06 07:15:56'),
(250, 2, 0, '2026-10-06 07:16:08'),
(250, 50, 1, '2026-10-06 07:16:08'),
(251, 2, 0, '2026-09-18 10:15:28'),
(251, 50, 1, '2026-09-18 10:15:28'),
(252, 2, 0, '2026-10-06 07:17:19'),
(252, 50, 1, '2026-10-06 07:17:19'),
(253, 2, 0, '2026-10-06 07:16:36'),
(253, 50, 1, '2026-10-06 07:16:36'),
(254, 2, 0, '2026-10-06 07:17:40'),
(254, 50, 1, '2026-10-06 07:17:40'),
(255, 2, 0, '2026-10-06 07:17:59'),
(255, 50, 1, '2026-10-06 07:17:59'),
(256, 2, 0, '2026-10-06 07:18:11'),
(256, 50, 1, '2026-10-06 07:18:11'),
(257, 2, 0, '2026-10-06 05:22:34'),
(257, 51, 1, '2026-10-06 05:22:34'),
(258, 2, 0, '2026-10-06 05:22:52'),
(258, 51, 1, '2026-10-06 05:22:52'),
(259, 2, 0, '2026-10-06 05:23:08'),
(259, 51, 1, '2026-10-06 05:23:08'),
(260, 2, 0, '2026-10-06 05:23:17'),
(260, 51, 1, '2026-10-06 05:23:17'),
(261, 2, 0, '2026-10-06 05:24:53'),
(261, 51, 1, '2026-10-06 05:24:53'),
(262, 2, 0, '2026-10-06 05:23:33'),
(262, 51, 1, '2026-10-06 05:23:33'),
(263, 2, 0, '2026-10-06 05:23:44'),
(263, 51, 1, '2026-10-06 05:23:44'),
(264, 2, 0, '2026-10-06 05:24:43'),
(264, 51, 1, '2026-10-06 05:24:43'),
(265, 2, 0, '2026-10-06 05:24:30'),
(265, 51, 1, '2026-10-06 05:24:30'),
(266, 2, 0, '2026-10-06 05:24:08'),
(266, 51, 1, '2026-10-06 05:24:08'),
(267, 2, 0, '2026-10-06 06:43:07'),
(267, 52, 1, '2026-10-06 06:43:07'),
(268, 2, 0, '2026-10-06 06:42:21'),
(268, 52, 1, '2026-10-06 06:42:21'),
(270, 2, 0, '2026-10-06 06:43:46'),
(270, 52, 1, '2026-10-06 06:43:46'),
(271, 2, 0, '2026-10-06 06:44:26'),
(271, 52, 1, '2026-10-06 06:44:26'),
(272, 2, 0, '2026-10-06 06:45:05'),
(272, 52, 1, '2026-10-06 06:45:05'),
(273, 2, 0, '2026-10-06 07:02:38'),
(273, 53, 1, '2026-10-06 07:02:38'),
(274, 2, 0, '2026-10-06 07:01:19'),
(274, 53, 1, '2026-10-06 07:01:19'),
(275, 2, 0, '2026-10-06 07:00:22'),
(275, 53, 1, '2026-10-06 07:00:22'),
(276, 2, 0, '2026-10-06 06:58:47'),
(276, 54, 1, '2026-10-06 06:58:47'),
(277, 54, 1, '2026-08-29 11:49:33'),
(278, 2, 0, '2026-10-06 06:57:25'),
(278, 54, 1, '2026-10-06 06:57:25'),
(279, 2, 0, '2026-10-06 06:58:06'),
(279, 54, 1, '2026-10-06 06:58:06'),
(280, 2, 0, '2026-10-06 05:02:12'),
(280, 55, 1, '2026-10-06 05:02:12'),
(281, 2, 0, '2026-10-06 07:11:50'),
(281, 21, 1, '2026-10-06 07:11:50'),
(281, 70, 0, '2026-10-06 07:11:50'),
(282, 2, 0, '2026-10-06 06:02:00'),
(282, 57, 1, '2026-10-06 06:02:00'),
(283, 2, 0, '2026-10-06 06:01:01'),
(283, 57, 1, '2026-10-06 06:01:01'),
(284, 21, 0, '2026-09-22 05:05:51'),
(284, 56, 1, '2026-09-22 05:05:51'),
(285, 21, 0, '2026-10-06 05:36:35'),
(285, 56, 1, '2026-10-06 05:36:35'),
(286, 2, 0, '2026-10-06 06:08:05'),
(286, 58, 1, '2026-10-06 06:08:05'),
(287, 2, 0, '2026-10-06 06:02:34'),
(287, 57, 1, '2026-10-06 06:02:34'),
(288, 21, 0, '2026-10-06 05:30:21'),
(288, 56, 1, '2026-10-06 05:30:21'),
(289, 21, 0, '2026-10-06 05:34:48'),
(289, 56, 1, '2026-10-06 05:34:48'),
(290, 2, 0, '2026-10-06 05:19:12'),
(290, 59, 1, '2026-10-06 05:19:12'),
(291, 2, 0, '2026-10-06 06:46:04'),
(291, 52, 1, '2026-10-06 06:46:04'),
(292, 2, 0, '2026-10-06 05:19:21'),
(292, 59, 1, '2026-10-06 05:19:21'),
(293, 2, 0, '2026-10-06 05:19:34'),
(293, 59, 1, '2026-10-06 05:19:34'),
(294, 2, 0, '2026-10-06 05:19:50'),
(294, 59, 1, '2026-10-06 05:19:50'),
(295, 2, 0, '2026-10-06 05:01:17'),
(295, 55, 1, '2026-10-06 05:01:17'),
(296, 2, 0, '2026-10-06 06:05:23'),
(296, 57, 1, '2026-10-06 06:05:23'),
(297, 2, 0, '2026-10-22 06:57:27'),
(297, 21, 0, '2026-10-22 06:57:27'),
(297, 57, 1, '2026-10-22 06:57:27'),
(298, 60, 1, '2026-08-29 11:49:33'),
(299, 60, 1, '2026-08-29 11:49:33'),
(300, 2, 0, '2026-10-06 06:35:07'),
(300, 58, 1, '2026-10-06 06:35:07'),
(301, 2, 0, '2026-10-06 06:34:36'),
(301, 58, 1, '2026-10-06 06:34:36'),
(302, 2, 0, '2026-10-06 06:34:05'),
(302, 58, 1, '2026-10-06 06:34:05'),
(303, 2, 0, '2026-10-06 06:33:28'),
(303, 58, 1, '2026-10-06 06:33:28'),
(304, 6, 0, '2026-09-24 08:48:52'),
(304, 61, 1, '2026-09-24 08:48:52'),
(305, 6, 0, '2026-09-24 08:48:44'),
(305, 61, 1, '2026-09-24 08:48:44'),
(306, 6, 0, '2026-09-24 08:48:36'),
(306, 61, 1, '2026-09-24 08:48:36'),
(307, 62, 1, '2026-08-29 11:49:33'),
(308, 62, 1, '2026-08-29 11:49:33'),
(309, 62, 1, '2026-08-29 11:49:33'),
(310, 62, 1, '2026-08-29 11:49:33'),
(311, 60, 1, '2026-08-29 11:49:33'),
(312, 2, 0, '2026-10-06 05:59:24'),
(312, 57, 1, '2026-10-06 05:59:24'),
(313, 21, 0, '2026-09-21 07:26:22'),
(313, 56, 0, '2026-09-21 07:26:22'),
(313, 63, 1, '2026-09-21 07:26:22'),
(314, 21, 0, '2026-09-21 07:27:09'),
(314, 56, 0, '2026-09-21 07:27:09'),
(314, 63, 1, '2026-09-21 07:27:09'),
(315, 21, 0, '2026-09-21 07:30:39'),
(315, 56, 1, '2026-09-21 07:30:39'),
(316, 2, 0, '2026-09-23 10:50:43'),
(316, 21, 0, '2026-09-23 10:50:43'),
(316, 64, 1, '2026-09-23 10:50:43'),
(317, 21, 0, '2026-10-06 07:22:09'),
(317, 65, 1, '2026-10-06 07:22:09'),
(318, 21, 0, '2026-10-07 05:05:15'),
(318, 65, 1, '2026-10-07 05:05:15'),
(319, 66, 1, '2026-08-29 11:49:33'),
(320, 2, 0, '2026-10-06 06:32:50'),
(320, 58, 1, '2026-10-06 06:32:50'),
(321, 2, 0, '2026-10-06 06:15:47'),
(321, 58, 1, '2026-10-06 06:15:47'),
(322, 2, 0, '2026-10-06 06:30:16'),
(322, 58, 1, '2026-10-06 06:30:16'),
(323, 2, 0, '2026-10-06 07:14:36'),
(323, 67, 1, '2026-10-06 07:14:36'),
(324, 2, 0, '2026-10-06 07:13:53'),
(324, 67, 1, '2026-10-06 07:13:53'),
(325, 56, 1, '2026-08-29 11:49:33'),
(326, 56, 1, '2026-08-29 11:49:33'),
(327, 2, 0, '2026-10-06 05:58:53'),
(327, 57, 1, '2026-10-06 05:58:53'),
(328, 2, 0, '2026-09-21 06:23:14'),
(328, 57, 1, '2026-09-21 06:23:14'),
(329, 2, 0, '2026-10-06 05:56:00'),
(329, 57, 1, '2026-10-06 05:56:00'),
(330, 2, 0, '2026-10-06 05:55:33'),
(330, 57, 1, '2026-10-06 05:55:33'),
(331, 2, 0, '2026-10-06 05:52:59'),
(331, 57, 1, '2026-10-06 05:52:59'),
(332, 2, 0, '2026-10-06 05:52:25'),
(332, 57, 1, '2026-10-06 05:52:25'),
(333, 2, 0, '2026-10-22 05:45:01'),
(333, 21, 0, '2026-10-22 05:45:01'),
(333, 57, 1, '2026-10-22 05:45:01'),
(334, 2, 0, '2026-10-06 05:53:47'),
(334, 57, 1, '2026-10-06 05:53:47'),
(335, 2, 0, '2026-10-06 05:56:51'),
(335, 57, 1, '2026-10-06 05:56:51'),
(336, 2, 0, '2026-10-06 06:00:10'),
(336, 57, 1, '2026-10-06 06:00:10'),
(337, 2, 0, '2026-10-06 05:54:58'),
(337, 57, 1, '2026-10-06 05:54:58'),
(338, 68, 1, '2026-08-29 11:49:33'),
(339, 21, 0, '2026-10-06 05:47:22'),
(339, 69, 1, '2026-10-06 05:47:22'),
(340, 2, 0, '2026-10-06 07:12:27'),
(340, 21, 0, '2026-10-06 07:12:27'),
(340, 70, 1, '2026-10-06 07:12:27'),
(341, 36, 1, '2026-09-11 11:50:14'),
(342, 22, 0, '2026-09-11 12:38:44'),
(342, 34, 1, '2026-09-11 12:38:44'),
(343, 2, 0, '2026-10-06 05:25:06'),
(343, 51, 1, '2026-10-06 05:25:06'),
(344, 2, 1, '2026-10-06 07:18:55'),
(344, 50, 0, '2026-10-06 07:18:55'),
(345, 2, 1, '2026-10-06 07:18:39'),
(345, 50, 0, '2026-10-06 07:18:39'),
(346, 2, 1, '2026-10-06 07:18:30'),
(346, 50, 0, '2026-10-06 07:18:30'),
(347, 2, 1, '2026-10-06 07:18:22'),
(347, 50, 0, '2026-10-06 07:18:22'),
(348, 2, 1, '2026-10-06 04:58:37'),
(348, 59, 0, '2026-10-06 04:58:37'),
(349, 2, 1, '2026-10-06 04:58:26'),
(349, 59, 0, '2026-10-06 04:58:26'),
(350, 21, 0, '2026-09-21 07:25:15'),
(350, 56, 0, '2026-09-21 07:25:15'),
(350, 63, 1, '2026-09-21 07:25:15'),
(351, 21, 1, '2026-09-21 07:28:46'),
(351, 56, 0, '2026-09-21 07:28:46'),
(351, 63, 0, '2026-09-21 07:28:46'),
(352, 21, 1, '2026-10-06 05:29:21'),
(352, 56, 0, '2026-10-06 05:29:21'),
(352, 63, 0, '2026-10-06 05:29:21'),
(353, 21, 1, '2026-10-06 07:21:28'),
(353, 65, 0, '2026-10-06 07:21:28'),
(354, 21, 1, '2026-09-19 06:21:06'),
(354, 68, 0, '2026-09-19 06:21:06'),
(355, 21, 1, '2026-09-19 06:26:49'),
(355, 68, 0, '2026-09-19 06:26:49'),
(356, 21, 1, '2026-10-06 05:47:09'),
(356, 69, 0, '2026-10-06 05:47:09'),
(357, 21, 0, '2026-10-06 05:29:11'),
(357, 63, 1, '2026-10-06 05:29:11'),
(358, 21, 1, '2026-09-20 09:58:33'),
(358, 63, 0, '2026-09-20 09:58:33'),
(359, 2, 1, '2026-09-20 10:05:14'),
(359, 57, 0, '2026-09-20 10:05:14'),
(360, 21, 1, '2026-09-20 10:11:53'),
(360, 69, 0, '2026-09-20 10:11:53'),
(361, 2, 1, '2026-09-20 10:26:35'),
(361, 21, 0, '2026-09-20 10:26:35'),
(361, 71, 0, '2026-09-20 10:26:35'),
(362, 2, 1, '2026-09-20 10:35:05'),
(362, 21, 0, '2026-09-20 10:35:05'),
(362, 71, 0, '2026-09-20 10:35:05'),
(363, 2, 1, '2026-09-20 10:40:10'),
(363, 57, 0, '2026-09-20 10:40:10'),
(364, 21, 1, '2026-09-21 08:28:16'),
(364, 72, 0, '2026-09-21 08:28:16'),
(365, 21, 1, '2026-09-21 08:28:05'),
(365, 72, 0, '2026-09-21 08:28:05'),
(366, 21, 1, '2026-09-20 17:44:40'),
(366, 63, 0, '2026-09-20 17:44:40'),
(367, 21, 1, '2026-09-20 17:47:43'),
(367, 63, 0, '2026-09-20 17:47:43'),
(368, 21, 1, '2026-09-20 17:53:28'),
(368, 63, 0, '2026-09-20 17:53:28'),
(369, 21, 1, '2026-09-20 17:58:18'),
(369, 63, 0, '2026-09-20 17:58:18'),
(370, 21, 1, '2026-10-06 05:28:23'),
(370, 63, 0, '2026-10-06 05:28:23'),
(371, 2, 1, '2026-09-20 18:39:02'),
(371, 21, 0, '2026-09-20 18:39:02'),
(371, 70, 0, '2026-09-20 18:39:02'),
(372, 2, 1, '2026-09-20 18:56:05'),
(372, 21, 0, '2026-09-20 18:56:05'),
(372, 70, 0, '2026-09-20 18:56:05'),
(375, 2, 1, '2026-10-06 06:06:19'),
(375, 57, 0, '2026-10-06 06:06:19'),
(376, 21, 1, '2026-10-06 05:45:11'),
(376, 56, 0, '2026-10-06 05:45:11'),
(377, 21, 1, '2026-10-06 05:45:01'),
(377, 56, 0, '2026-10-06 05:45:01'),
(378, 21, 1, '2026-10-06 05:44:50'),
(378, 56, 0, '2026-10-06 05:44:50'),
(379, 21, 1, '2026-10-06 05:28:11'),
(379, 56, 0, '2026-10-06 05:28:11'),
(379, 63, 0, '2026-10-06 05:28:11'),
(380, 21, 1, '2026-10-06 05:44:28'),
(380, 56, 0, '2026-10-06 05:44:28'),
(381, 21, 1, '2026-09-21 07:31:14'),
(381, 56, 0, '2026-09-21 07:31:14'),
(382, 21, 1, '2026-09-21 07:23:39'),
(382, 56, 0, '2026-09-21 07:23:39'),
(382, 63, 0, '2026-09-21 07:23:39'),
(383, 21, 1, '2026-09-21 07:37:00'),
(383, 56, 0, '2026-09-21 07:37:00'),
(383, 63, 0, '2026-09-21 07:37:00'),
(384, 21, 1, '2026-09-21 07:44:09'),
(384, 56, 0, '2026-09-21 07:44:09'),
(385, 21, 1, '2026-09-21 08:27:28'),
(385, 72, 0, '2026-09-21 08:27:28'),
(386, 21, 1, '2026-09-21 08:35:04'),
(386, 56, 0, '2026-09-21 08:35:04'),
(387, 21, 1, '2026-09-21 08:41:36'),
(387, 72, 0, '2026-09-21 08:41:36'),
(388, 21, 1, '2026-09-21 10:13:23'),
(388, 56, 0, '2026-09-21 10:13:23'),
(389, 2, 1, '2026-10-06 06:06:02'),
(389, 57, 0, '2026-10-06 06:06:02'),
(390, 21, 1, '2026-09-21 10:45:52'),
(390, 56, 0, '2026-09-21 10:45:52'),
(391, 2, 1, '2026-09-21 11:03:35'),
(391, 57, 0, '2026-09-21 11:03:35'),
(392, 21, 1, '2026-09-21 11:14:59'),
(392, 63, 0, '2026-09-21 11:14:59'),
(393, 2, 0, '2026-09-21 11:23:00'),
(393, 21, 1, '2026-09-21 11:23:00'),
(393, 64, 0, '2026-09-21 11:23:00'),
(394, 21, 1, '2026-09-21 11:34:17'),
(394, 56, 0, '2026-09-21 11:34:17'),
(395, 2, 1, '2026-09-21 11:42:32'),
(395, 21, 0, '2026-09-21 11:42:32'),
(395, 64, 0, '2026-09-21 11:42:32'),
(396, 21, 1, '2026-09-21 11:52:44'),
(396, 63, 0, '2026-09-21 11:52:44'),
(397, 21, 1, '2026-09-21 12:01:28'),
(397, 72, 0, '2026-09-21 12:01:28'),
(398, 21, 1, '2026-09-21 12:12:53'),
(398, 63, 0, '2026-09-21 12:12:53'),
(399, 21, 1, '2026-09-21 12:25:44'),
(399, 63, 0, '2026-09-21 12:25:44'),
(400, 21, 1, '2026-09-21 12:34:39'),
(400, 72, 0, '2026-09-21 12:34:39'),
(401, 21, 1, '2026-09-21 12:52:07'),
(401, 63, 0, '2026-09-21 12:52:07'),
(402, 21, 1, '2026-09-21 12:58:39'),
(402, 72, 0, '2026-09-21 12:58:39'),
(403, 21, 1, '2026-09-21 13:02:54'),
(403, 63, 0, '2026-09-21 13:02:54'),
(404, 2, 1, '2026-09-21 16:34:16'),
(404, 57, 0, '2026-09-21 16:34:16'),
(405, 2, 1, '2026-09-22 04:41:17'),
(405, 21, 0, '2026-09-22 04:41:17'),
(405, 64, 0, '2026-09-22 04:41:17'),
(406, 21, 1, '2026-09-23 10:50:17'),
(406, 63, 0, '2026-09-23 10:50:17'),
(407, 21, 1, '2026-10-06 05:39:16'),
(407, 56, 0, '2026-10-06 05:39:16'),
(408, 21, 1, '2026-09-23 10:49:54'),
(408, 56, 0, '2026-09-23 10:49:54'),
(408, 63, 0, '2026-09-23 10:49:54'),
(409, 21, 1, '2026-09-24 04:34:48'),
(409, 63, 0, '2026-09-24 04:34:48'),
(410, 2, 1, '2026-09-24 06:26:39'),
(410, 57, 0, '2026-09-24 06:26:39'),
(411, 2, 1, '2026-09-30 03:59:43'),
(411, 21, 0, '2026-09-30 03:59:43'),
(411, 64, 0, '2026-09-30 03:59:43'),
(412, 2, 1, '2026-10-22 06:57:35'),
(412, 21, 0, '2026-10-22 06:57:35'),
(412, 57, 0, '2026-10-22 06:57:35'),
(413, 2, 1, '2026-10-22 06:57:46'),
(413, 21, 0, '2026-10-22 06:57:46'),
(413, 57, 0, '2026-10-22 06:57:46'),
(414, 2, 1, '2026-10-22 06:57:56'),
(414, 21, 0, '2026-10-22 06:57:56'),
(414, 57, 0, '2026-10-22 06:57:56'),
(415, 2, 1, '2026-10-22 07:30:32'),
(415, 21, 0, '2026-10-22 07:30:32'),
(415, 57, 0, '2026-10-22 07:30:32'),
(416, 2, 1, '2026-10-22 07:39:21'),
(416, 21, 0, '2026-10-22 07:39:21'),
(416, 57, 0, '2026-10-22 07:39:21'),
(417, 2, 1, '2026-10-22 07:46:33'),
(417, 21, 0, '2026-10-22 07:46:33'),
(417, 57, 0, '2026-10-22 07:46:33');

-- --------------------------------------------------------

--
-- Table structure for table `product_category_parent_visibility`
--

CREATE TABLE `product_category_parent_visibility` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_category_parent_visibility`
--

INSERT INTO `product_category_parent_visibility` (`product_id`, `category_id`, `parent_id`, `created_at`) VALUES
(281, 70, 2, '2026-10-06 07:11:50'),
(281, 70, 21, '2026-10-06 07:11:50'),
(297, 57, 2, '2026-10-22 06:57:27'),
(297, 57, 21, '2026-10-22 06:57:27'),
(316, 64, 2, '2026-09-23 10:50:43'),
(316, 64, 21, '2026-09-23 10:50:43'),
(333, 57, 2, '2026-10-22 05:45:01'),
(333, 57, 21, '2026-10-22 05:45:01'),
(340, 70, 2, '2026-10-06 07:12:27'),
(340, 70, 21, '2026-10-06 07:12:27'),
(361, 71, 2, '2026-09-20 10:26:35'),
(361, 71, 21, '2026-09-20 10:26:35'),
(362, 71, 2, '2026-09-20 10:35:05'),
(362, 71, 21, '2026-09-20 10:35:05'),
(371, 70, 2, '2026-09-20 18:39:02'),
(371, 70, 21, '2026-09-20 18:39:02'),
(372, 70, 2, '2026-09-20 18:56:05'),
(372, 70, 21, '2026-09-20 18:56:05'),
(393, 64, 2, '2026-09-21 11:23:00'),
(393, 64, 21, '2026-09-21 11:23:00'),
(395, 64, 2, '2026-09-21 11:42:32'),
(395, 64, 21, '2026-09-21 11:42:32'),
(405, 64, 2, '2026-09-22 04:41:17'),
(405, 64, 21, '2026-09-22 04:41:17'),
(411, 64, 2, '2026-09-30 03:59:43'),
(411, 64, 21, '2026-09-30 03:59:43'),
(412, 57, 2, '2026-10-22 06:57:35'),
(412, 57, 21, '2026-10-22 06:57:35'),
(413, 57, 2, '2026-10-22 06:57:46'),
(413, 57, 21, '2026-10-22 06:57:46'),
(414, 57, 2, '2026-10-22 06:57:56'),
(414, 57, 21, '2026-10-22 06:57:56'),
(415, 57, 2, '2026-10-22 07:30:32'),
(415, 57, 21, '2026-10-22 07:30:32'),
(416, 57, 2, '2026-10-22 07:39:21'),
(416, 57, 21, '2026-10-22 07:39:21'),
(417, 57, 2, '2026-10-22 07:46:33'),
(417, 57, 21, '2026-10-22 07:46:33');

-- --------------------------------------------------------

--
-- Table structure for table `product_discount_selections`
--

CREATE TABLE `product_discount_selections` (
  `product_id` int(11) NOT NULL,
  `selected_at` datetime(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_discount_selections`
--

INSERT INTO `product_discount_selections` (`product_id`, `selected_at`) VALUES
(241, '2026-10-30 11:02:31.979853'),
(245, '2026-10-06 06:54:13.252912'),
(246, '2026-10-06 06:54:00.009291'),
(247, '2026-10-06 06:54:22.178673'),
(248, '2026-10-06 06:54:30.989605'),
(249, '2026-10-06 07:15:56.912701'),
(250, '2026-10-06 07:16:08.054282'),
(252, '2026-10-06 07:17:19.245716'),
(253, '2026-10-06 07:16:36.253883'),
(254, '2026-10-06 07:17:40.616334'),
(255, '2026-10-06 07:17:59.627586'),
(256, '2026-10-06 07:18:11.202644'),
(257, '2026-10-06 05:22:34.834503'),
(258, '2026-10-06 05:22:52.312649'),
(259, '2026-10-06 05:23:08.565193'),
(260, '2026-10-06 05:23:17.964268'),
(261, '2026-10-06 05:24:53.167533'),
(262, '2026-10-06 05:23:33.468449'),
(263, '2026-10-06 05:23:44.758222'),
(264, '2026-10-06 05:24:43.078590'),
(265, '2026-10-06 05:24:30.085307'),
(266, '2026-10-06 05:24:08.345826'),
(267, '2026-10-06 06:43:07.347578'),
(268, '2026-10-06 06:42:21.901563'),
(270, '2026-10-06 06:43:46.511594'),
(271, '2026-10-06 06:44:26.948450'),
(272, '2026-10-06 06:45:05.991542'),
(273, '2026-10-06 07:02:38.970340'),
(274, '2026-10-06 07:01:19.084001'),
(275, '2026-10-06 07:00:22.242278'),
(276, '2026-10-06 06:58:47.809041'),
(278, '2026-10-06 06:57:25.926756'),
(279, '2026-10-06 06:58:06.145870'),
(280, '2026-10-06 05:02:12.942813'),
(281, '2026-10-06 07:11:50.965971'),
(282, '2026-10-06 06:02:00.192277'),
(283, '2026-10-06 06:01:01.736342'),
(285, '2026-10-06 05:36:35.644468'),
(286, '2026-10-06 06:08:05.594647'),
(287, '2026-10-06 06:02:34.505007'),
(288, '2026-10-06 05:30:21.025332'),
(289, '2026-10-06 05:34:48.291934'),
(290, '2026-10-06 05:19:12.130619'),
(291, '2026-10-06 06:46:04.476480'),
(292, '2026-10-06 05:19:21.928178'),
(293, '2026-10-06 05:19:34.636260'),
(294, '2026-10-06 05:19:50.642607'),
(295, '2026-10-06 05:01:17.325620'),
(296, '2026-10-06 06:05:23.528339'),
(297, '2026-10-22 06:57:27.609894'),
(300, '2026-10-06 06:35:07.543747'),
(301, '2026-10-06 06:34:36.892502'),
(302, '2026-10-06 06:34:05.216625'),
(303, '2026-10-06 06:33:28.068970'),
(312, '2026-10-06 05:59:24.343188'),
(317, '2026-10-06 07:22:09.618460'),
(318, '2026-10-07 05:05:15.654505'),
(320, '2026-10-06 06:32:50.993258'),
(321, '2026-10-06 06:15:47.357246'),
(322, '2026-10-06 06:30:16.815762'),
(323, '2026-10-06 07:14:36.429966'),
(324, '2026-10-06 07:13:53.616644'),
(327, '2026-10-06 05:58:53.472066'),
(329, '2026-10-06 05:56:00.989301'),
(330, '2026-10-06 05:55:33.008992'),
(331, '2026-10-06 05:52:59.963750'),
(332, '2026-10-06 05:52:25.379354'),
(333, '2026-10-22 05:45:01.774953'),
(334, '2026-10-06 05:53:47.873770'),
(335, '2026-10-06 05:56:51.089298'),
(336, '2026-10-06 06:00:10.092772'),
(337, '2026-10-06 05:54:58.027789'),
(339, '2026-10-06 05:47:22.794637'),
(340, '2026-10-06 07:12:27.249943'),
(343, '2026-10-06 05:25:06.719258'),
(344, '2026-10-06 07:18:55.038484'),
(346, '2026-10-06 07:18:30.815227'),
(347, '2026-10-06 07:18:22.123957'),
(348, '2026-10-06 04:58:37.661378'),
(349, '2026-10-06 04:58:26.732734'),
(352, '2026-10-06 05:29:21.276858'),
(353, '2026-10-06 07:21:28.495051'),
(356, '2026-10-06 05:47:09.894354'),
(357, '2026-10-06 05:29:11.244939'),
(370, '2026-10-06 05:28:23.463797'),
(375, '2026-10-06 06:06:19.631932'),
(376, '2026-10-06 05:45:11.916286'),
(377, '2026-10-06 05:45:01.041119'),
(378, '2026-10-06 05:44:50.354359'),
(379, '2026-10-06 05:28:11.899077'),
(380, '2026-10-06 05:44:28.670494'),
(389, '2026-10-06 06:06:02.506580'),
(407, '2026-10-06 05:39:16.695703'),
(411, '2026-09-30 03:59:43.353532'),
(412, '2026-10-22 06:57:35.995666'),
(413, '2026-10-22 06:57:46.295595'),
(414, '2026-10-22 06:57:56.889833'),
(415, '2026-10-22 07:30:32.873914'),
(416, '2026-10-22 07:39:21.106134'),
(417, '2026-10-22 07:46:33.535326');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `is_main`, `sort_order`) VALUES
(194, 244, 'uploads/products/6a86e28f04cb5.jpg', 0, 1),
(195, 243, 'uploads/products/6a86e2bc7f453.jpg', 1, 1),
(196, 245, 'uploads/products/6a88488a4f581.jpg', 0, 1),
(197, 249, 'uploads/products/6a884dfeb2d45.jpg', 0, 1),
(198, 252, 'uploads/products/6a8851c86b0e4.jpg', 0, 1),
(201, 253, 'uploads/products/6a8926cd91a42.jpg', 0, 1),
(203, 257, 'uploads/products/6a8933311a48d.jpg', 0, 1),
(204, 259, 'uploads/products/6a893744601e7.jpg', 0, 1),
(205, 259, 'uploads/products/6a8937446035e.jpg', 0, 2),
(206, 262, 'uploads/products/6a893d1be898f.jpg', 0, 1),
(207, 262, 'uploads/products/6a893d1be8b10.jpg', 0, 2),
(208, 265, 'uploads/products/6a8943d2900da.jpg', 0, 1),
(209, 266, 'uploads/products/6a8944ec54988.jpg', 0, 1),
(210, 267, 'uploads/products/6a8948c1521c6.jpg', 0, 1),
(211, 267, 'uploads/products/6a8948c1523e7.jpg', 0, 2),
(212, 267, 'uploads/products/6a8948c15264e.jpg', 0, 3),
(213, 268, 'uploads/products/6a894a7e88e80.jpg', 0, 1),
(214, 268, 'uploads/products/6a894a7e89058.jpg', 0, 2),
(215, 268, 'uploads/products/6a894a7e89255.jpg', 0, 3),
(216, 270, 'uploads/products/6a894b6ed3b70.jpg', 0, 1),
(217, 270, 'uploads/products/6a894b6ed3d85.jpg', 0, 2),
(218, 270, 'uploads/products/6a894b6ed3fb6.jpg', 0, 3),
(219, 271, 'uploads/products/6a894d4c4f52c.jpg', 0, 1),
(220, 271, 'uploads/products/6a894d4c4f70b.jpg', 0, 2),
(221, 271, 'uploads/products/6a894d4c4f909.jpg', 0, 3),
(222, 272, 'uploads/products/6a894ec1c588a.jpg', 0, 1),
(223, 272, 'uploads/products/6a894ec1c5a45.jpg', 0, 3),
(224, 272, 'uploads/products/6a894ec1c5c5f.jpg', 0, 4),
(225, 273, 'uploads/products/6a89627813bcc.jpg', 0, 1),
(226, 273, 'uploads/products/6a89627813dd3.jpg', 0, 2),
(227, 273, 'uploads/products/6a89627813f72.jpg', 0, 3),
(228, 273, 'uploads/products/6a896278140e0.jpg', 0, 4),
(229, 273, 'uploads/products/6a89627814279.jpg', 0, 5),
(230, 275, 'uploads/products/6a8965f7b5ae4.jpg', 0, 1),
(231, 276, 'uploads/products/6a89698cd624c.jpg', 0, 1),
(232, 276, 'uploads/products/6a89698cd63f1.jpg', 0, 2),
(233, 277, 'uploads/products/6a896aa4a327f.jpg', 0, 1),
(234, 277, 'uploads/products/6a896aa4a3427.jpg', 0, 2),
(235, 278, 'uploads/products/6a896bb0a583a.jpg', 0, 1),
(236, 278, 'uploads/products/6a896bb0a5993.jpg', 0, 2),
(237, 279, 'uploads/products/6a896cb1f4232.jpg', 0, 1),
(238, 279, 'uploads/products/6a896cb20019a.jpg', 0, 2),
(239, 281, 'uploads/products/6a8c0e15d71f2.jpg', 0, 1),
(240, 281, 'uploads/products/6a8c0e15d7387.jpg', 0, 2),
(241, 281, 'uploads/products/6a8c0e15d75bb.jpg', 0, 3),
(242, 281, 'uploads/products/6a8c0e15d77e9.jpg', 0, 4),
(243, 281, 'uploads/products/6a8c0e15d793f.jpg', 0, 5),
(244, 281, 'uploads/products/6a8c0e15d7b73.jpg', 0, 6),
(245, 282, 'uploads/products/6a8c12c2b06ab.jpg', 0, 1),
(246, 282, 'uploads/products/6a8c12c2b0811.jpg', 0, 2),
(247, 282, 'uploads/products/6a8c12c2b092b.jpg', 0, 3),
(248, 282, 'uploads/products/6a8c12c2b0aa5.jpg', 0, 4),
(249, 282, 'uploads/products/6a8c12c2b0bd0.jpg', 0, 5),
(250, 283, 'uploads/products/6a8c142643337.jpg', 0, 1),
(251, 283, 'uploads/products/6a8c14264349e.jpg', 0, 2),
(252, 283, 'uploads/products/6a8c142643608.jpg', 0, 3),
(253, 284, 'uploads/products/6a8c15a7154c9.jpg', 0, 1),
(254, 284, 'uploads/products/6a8c15a7156e0.jpg', 0, 2),
(255, 284, 'uploads/products/6a8c15a7158a0.jpg', 0, 3),
(256, 284, 'uploads/products/6a8c15a715a35.jpg', 0, 4),
(257, 284, 'uploads/products/6a8c15a715c0a.jpg', 0, 5),
(258, 284, 'uploads/products/6a8c15a715dca.jpg', 0, 6),
(265, 286, 'uploads/products/6a8c1ef7a3326.jpg', 0, 1),
(266, 286, 'uploads/products/6a8c1ef7a3526.jpg', 0, 2),
(267, 286, 'uploads/products/6a8c1ef7a3729.jpg', 0, 3),
(268, 286, 'uploads/products/6a8c1ef7a393b.jpg', 0, 4),
(269, 286, 'uploads/products/6a8c1ef7a3b50.jpg', 0, 5),
(270, 287, 'uploads/products/6a8c20328db07.jpg', 0, 1),
(271, 287, 'uploads/products/6a8c20328dc86.jpg', 0, 2),
(272, 287, 'uploads/products/6a8c20328de73.jpg', 0, 3),
(273, 287, 'uploads/products/6a8c20328dffe.jpg', 0, 4),
(274, 287, 'uploads/products/6a8c20328e179.jpg', 0, 5),
(275, 288, 'uploads/products/6a8c22575b8f4.jpg', 0, 1),
(276, 288, 'uploads/products/6a8c22575bb02.jpg', 0, 2),
(277, 288, 'uploads/products/6a8c22575bcf8.jpg', 0, 3),
(278, 288, 'uploads/products/6a8c22575bf31.jpg', 0, 4),
(279, 289, 'uploads/products/6a8c23f7cf975.jpg', 0, 1),
(280, 289, 'uploads/products/6a8c23f7cfc5d.jpg', 0, 2),
(281, 289, 'uploads/products/6a8c23f7cfe50.jpg', 0, 3),
(282, 289, 'uploads/products/6a8c23f7d00a2.jpg', 0, 4),
(283, 289, 'uploads/products/6a8c23f7d0269.jpg', 0, 5),
(284, 289, 'uploads/products/6a8c23f7d0467.jpg', 0, 6),
(285, 289, 'uploads/products/6a8c23f7d0607.jpg', 0, 7),
(286, 285, 'uploads/products/6a8c28678542b.jpg', 0, 1),
(287, 285, 'uploads/products/6a8c2867855bd.jpg', 0, 2),
(288, 290, 'uploads/products/6a8c2a5741052.jpg', 0, 1),
(289, 290, 'uploads/products/6a8c2a5741243.jpg', 0, 2),
(290, 290, 'uploads/products/6a8c2a5741403.jpg', 0, 3),
(291, 290, 'uploads/products/6a8c2a5741695.jpg', 0, 4),
(292, 290, 'uploads/products/6a8c2a574195b.jpg', 0, 5),
(293, 290, 'uploads/products/6a8c2a5741ad4.jpg', 0, 6),
(294, 291, 'uploads/products/6a8c2be02107f.jpg', 0, 1),
(295, 291, 'uploads/products/6a8c2be0213c5.jpg', 0, 2),
(296, 291, 'uploads/products/6a8c2be021601.jpg', 0, 3),
(297, 291, 'uploads/products/6a8c2be0218e2.jpg', 0, 4),
(298, 291, 'uploads/products/6a8c2be021abb.jpg', 0, 5),
(299, 291, 'uploads/products/6a8c2be021d5e.jpg', 0, 6),
(300, 291, 'uploads/products/6a8c2be021f6d.jpg', 0, 7),
(301, 292, 'uploads/products/6a8c2d494fb54.jpg', 0, 1),
(302, 292, 'uploads/products/6a8c2d494fd81.jpg', 0, 2),
(303, 292, 'uploads/products/6a8c2d494ffb0.jpg', 0, 3),
(304, 292, 'uploads/products/6a8c2d4950195.jpg', 0, 4),
(305, 292, 'uploads/products/6a8c2d4950350.jpg', 0, 5),
(306, 293, 'uploads/products/6a8c2fe7eb637.jpg', 0, 1),
(307, 293, 'uploads/products/6a8c2fe7eb964.jpg', 0, 2),
(308, 293, 'uploads/products/6a8c2fe7ebe19.jpg', 0, 3),
(309, 294, 'uploads/products/6a8c311a12b78.jpg', 0, 1),
(310, 294, 'uploads/products/6a8c311a12d89.jpg', 0, 2),
(311, 294, 'uploads/products/6a8c311a12f8f.jpg', 0, 3),
(312, 294, 'uploads/products/6a8c311a131bd.jpg', 0, 4),
(313, 295, 'uploads/products/6a8c327f4e11d.jpg', 0, 1),
(314, 295, 'uploads/products/6a8c327f4e298.jpg', 0, 2),
(315, 295, 'uploads/products/6a8c327f4e3db.jpg', 0, 3),
(316, 295, 'uploads/products/6a8c327f4e512.jpg', 0, 4),
(317, 295, 'uploads/products/6a8c327f4e6c9.jpg', 0, 5),
(318, 295, 'uploads/products/6a8c327f4e845.jpg', 0, 6),
(319, 295, 'uploads/products/6a8c327f4e9e6.jpg', 0, 7),
(320, 295, 'uploads/products/6a8c327f4eb64.jpg', 0, 8),
(321, 296, 'uploads/products/6a8c339e6620c.jpg', 0, 1),
(322, 296, 'uploads/products/6a8c339e663de.jpg', 0, 2),
(323, 296, 'uploads/products/6a8c339e66574.jpg', 0, 3),
(324, 296, 'uploads/products/6a8c339e66744.jpg', 0, 4),
(333, 298, 'uploads/products/6a8d2e8be04e9.jpg', 0, 1),
(334, 299, 'uploads/products/6a8d2fe79c95e.jpg', 0, 1),
(335, 299, 'uploads/products/6a8d2fe79cb1c.jpg', 0, 2),
(336, 299, 'uploads/products/6a8d2fe79cd06.jpg', 0, 3),
(337, 299, 'uploads/products/6a8d2fe79ceb2.jpg', 0, 4),
(338, 300, 'uploads/products/6a8d356a70a4e.jpg', 0, 1),
(339, 304, 'uploads/products/6a8fc4dc1124e.jpg', 0, 1),
(340, 304, 'uploads/products/6a8fc4dc11430.jpg', 0, 2),
(341, 305, 'uploads/products/6a8fc5ef2a674.jpg', 0, 1),
(342, 305, 'uploads/products/6a8fc5ef2a90d.jpg', 0, 2),
(343, 305, 'uploads/products/6a8fc5ef2abbf.jpg', 0, 3),
(344, 305, 'uploads/products/6a8fc5ef2ae0a.jpg', 0, 4),
(345, 305, 'uploads/products/6a8fc5ef2b055.jpg', 0, 5),
(346, 305, 'uploads/products/6a8fc5ef2b291.jpg', 0, 6),
(347, 305, 'uploads/products/6a8fc5ef2b4a3.jpg', 0, 7),
(348, 305, 'uploads/products/6a8fc5ef2b6f9.jpg', 0, 8),
(349, 306, 'uploads/products/6a8fd51c600fd.jpg', 0, 1),
(350, 306, 'uploads/products/6a8fd51c60303.jpg', 0, 2),
(351, 306, 'uploads/products/6a8fd51c604d8.jpg', 0, 3),
(352, 306, 'uploads/products/6a8fd51c60641.jpg', 0, 4),
(353, 306, 'uploads/products/6a8fd51c6081a.jpg', 0, 5),
(354, 306, 'uploads/products/6a8fd51c609a8.jpg', 0, 6),
(355, 306, 'uploads/products/6a8fd51c60ba7.jpg', 0, 7),
(356, 306, 'uploads/products/6a8fd51c60e78.jpg', 0, 8),
(357, 307, 'uploads/products/6a8fd8d4c4f28.jpg', 0, 1),
(358, 307, 'uploads/products/6a8fd8d4c520e.jpg', 0, 2),
(359, 307, 'uploads/products/6a8fd8d4c54f7.jpg', 0, 3),
(360, 307, 'uploads/products/6a8fd8d4c574a.jpg', 0, 4),
(361, 307, 'uploads/products/6a8fd8d4c5999.jpg', 0, 5),
(362, 308, 'uploads/products/6a8fdc9abbb24.jpg', 0, 1),
(363, 309, 'uploads/products/6a8fdd0a3c2c0.jpg', 0, 1),
(364, 310, 'uploads/products/6a8fdd5c841a7.jpg', 0, 1),
(365, 311, 'uploads/products/6a8fe060f2853.jpg', 0, 1),
(366, 311, 'uploads/products/6a8fe060f29c0.jpg', 0, 2),
(367, 312, 'uploads/products/6a9260a3dbc7b.jpg', 0, 1),
(368, 312, 'uploads/products/6a9260a3dbdd7.jpg', 0, 2),
(369, 313, 'uploads/products/6a92643713a96.jpg', 0, 1),
(370, 313, 'uploads/products/6a92643713d52.jpg', 0, 2),
(371, 314, 'uploads/products/6a9265617f362.jpg', 0, 1),
(372, 316, 'uploads/products/6a926ab7458d1.jpg', 0, 1),
(373, 316, 'uploads/products/6a926ab745a5c.jpg', 0, 2),
(374, 316, 'uploads/products/6a926ab745c29.jpg', 0, 3),
(375, 317, 'uploads/products/6a926d7b20437.jpg', 0, 1),
(376, 317, 'uploads/products/6a926d7b2060f.jpg', 0, 2),
(377, 318, 'uploads/products/6a926ec9151a3.jpg', 0, 1),
(378, 318, 'uploads/products/6a926ec915350.jpg', 0, 2),
(379, 319, 'uploads/products/6a92716204f09.jpg', 0, 1),
(380, 319, 'uploads/products/6a92716205111.jpg', 0, 2),
(381, 319, 'uploads/products/6a92716205291.jpg', 0, 3),
(382, 319, 'uploads/products/6a92716205429.jpg', 0, 4),
(383, 319, 'uploads/products/6a927162055b3.jpg', 0, 5),
(384, 319, 'uploads/products/6a9271620572c.jpg', 0, 6),
(385, 320, 'uploads/products/6a927a0394976.jpg', 0, 1),
(386, 320, 'uploads/products/6a927a0394af8.jpg', 0, 2),
(387, 321, 'uploads/products/6a927b64055ac.jpg', 0, 1),
(388, 322, 'uploads/products/6a927c66d5233.jpg', 0, 1),
(389, 322, 'uploads/products/6a927c66d54f4.jpg', 0, 2),
(390, 323, 'uploads/products/6a927f52ec9f4.jpg', 0, 1),
(391, 324, 'uploads/products/6a9280e48a974.jpg', 0, 1),
(392, 324, 'uploads/products/6a9280e48ab5e.jpg', 0, 2),
(393, 325, 'uploads/products/6a9282c84987d.jpg', 0, 1),
(394, 325, 'uploads/products/6a9282c849b11.jpg', 0, 2),
(395, 325, 'uploads/products/6a9282c849d70.jpg', 0, 3),
(396, 325, 'uploads/products/6a9282c84a016.jpg', 0, 4),
(397, 329, 'uploads/products/6a9298b810515.jpg', 0, 1),
(398, 329, 'uploads/products/6a9298b8106dd.jpg', 0, 2),
(399, 329, 'uploads/products/6a9298b8108ef.jpg', 0, 3),
(400, 330, 'uploads/products/6a929a0f2aefc.jpg', 0, 1),
(401, 330, 'uploads/products/6a929a0f2b084.jpg', 0, 2),
(402, 330, 'uploads/products/6a929a0f2b1dd.jpg', 0, 3),
(403, 330, 'uploads/products/6a929a0f2b340.jpg', 0, 4),
(404, 330, 'uploads/products/6a929a0f2b4c9.jpg', 0, 5),
(405, 331, 'uploads/products/6a929dc24c3d3.jpg', 0, 1),
(406, 331, 'uploads/products/6a929dc24c555.jpg', 0, 2),
(407, 331, 'uploads/products/6a929dc24c6fd.jpg', 0, 3),
(408, 331, 'uploads/products/6a929dc24c8fb.jpg', 0, 4),
(409, 332, 'uploads/products/6a929f055f34a.jpg', 0, 1),
(410, 332, 'uploads/products/6a929f055f52e.jpg', 0, 2),
(411, 332, 'uploads/products/6a929f055f6fb.jpg', 0, 3),
(412, 332, 'uploads/products/6a929f055f8f3.jpg', 0, 4),
(413, 332, 'uploads/products/6a929f055faa4.jpg', 0, 5),
(414, 333, 'uploads/products/6a92a143d36eb.jpg', 0, 1),
(415, 333, 'uploads/products/6a92a143d3897.jpg', 0, 2),
(416, 333, 'uploads/products/6a92a143d3a52.jpg', 0, 3),
(417, 334, 'uploads/products/6a92a2b12c2a8.jpg', 0, 1),
(418, 334, 'uploads/products/6a92a2b12c3dc.jpg', 0, 2),
(419, 335, 'uploads/products/6a92a5b3d34ca.jpg', 0, 1),
(420, 335, 'uploads/products/6a92a5b3d367d.jpg', 0, 2),
(421, 335, 'uploads/products/6a92a5b3d3808.jpg', 0, 3),
(422, 335, 'uploads/products/6a92a5b3d3968.jpg', 0, 4),
(423, 339, 'uploads/products/6a92af5808045.jpg', 0, 2),
(424, 339, 'uploads/products/6a92af58081d5.jpg', 0, 3),
(425, 339, 'uploads/products/6a92af58083a1.jpg', 0, 4),
(426, 339, 'uploads/products/6a92afe10f761.jpg', 1, 1),
(427, 342, 'uploads/products/6aa3f28f61bd4.webp', 0, 1),
(428, 342, 'uploads/products/6aa3f28f61d45.webp', 0, 2),
(429, 252, 'uploads/products/6aad0e25c21e6.jpg', 1, 1),
(430, 252, 'uploads/products/6aad0e25c252b.jpg', 0, 2),
(431, 255, 'uploads/products/6aad21987a88b.jpg', 1, 1),
(432, 348, 'uploads/products/6aad2f836369a.jpg', 0, 1),
(433, 348, 'uploads/products/6aad2f8363859.jpg', 0, 2),
(434, 348, 'uploads/products/6aad2f8363a23.jpg', 0, 3),
(435, 349, 'uploads/products/6aad3aea3fe2d.jpeg', 0, 1),
(436, 349, 'uploads/products/6aad3aea3ff56.jpeg', 0, 2),
(437, 349, 'uploads/products/6aad3aea400af.jpeg', 0, 3),
(438, 350, 'uploads/products/6aae1994aa14f.jpg', 0, 1),
(439, 350, 'uploads/products/6aae1994aa355.jpg', 0, 2),
(440, 350, 'uploads/products/6aae1994aa4ab.jpg', 0, 3),
(441, 350, 'uploads/products/6aae1994aa61f.jpg', 0, 4),
(442, 350, 'uploads/products/6aae1994aa7d4.jpg', 0, 5),
(443, 351, 'uploads/products/6aae1fb62daad.jpg', 0, 1),
(444, 351, 'uploads/products/6aae1fb62dc5a.jpg', 0, 2),
(445, 351, 'uploads/products/6aae1fb62ddcf.jpg', 0, 3),
(446, 351, 'uploads/products/6aae1fb62df36.jpg', 0, 4),
(447, 351, 'uploads/products/6aae1fb62e0b5.jpg', 0, 5),
(448, 351, 'uploads/products/6aae1fb62e25b.jpg', 0, 6),
(449, 352, 'uploads/products/6aae24ec37e48.jpg', 0, 1),
(450, 352, 'uploads/products/6aae24ec38002.jpg', 0, 2),
(451, 352, 'uploads/products/6aae24ec381ba.jpg', 0, 3),
(452, 352, 'uploads/products/6aae24ec38374.jpg', 0, 4),
(453, 352, 'uploads/products/6aae24ec38513.jpg', 0, 5),
(457, 354, 'uploads/products/6aae29d2ae9dd.jpg', 0, 1),
(458, 355, 'uploads/products/6aae2b296aa51.jpg', 0, 1),
(465, 353, 'uploads/products/6aae2fed5de97.jpg', 0, 1),
(466, 353, 'uploads/products/6aae2fed5e06e.jpg', 0, 2),
(467, 353, 'uploads/products/6aae2fed5e1f3.jpg', 0, 3),
(468, 357, 'uploads/products/6aae34341f346.jpg', 0, 1),
(469, 357, 'uploads/products/6aae34341f479.jpg', 0, 2),
(470, 357, 'uploads/products/6aae34341f5a8.jpg', 0, 3),
(471, 357, 'uploads/products/6aae34341f6d6.jpg', 0, 4),
(472, 357, 'uploads/products/6aae34341f822.jpg', 0, 5),
(473, 357, 'uploads/products/6aae34341f95d.jpg', 0, 6),
(474, 357, 'uploads/products/6aae34341fa9f.jpg', 0, 7),
(475, 357, 'uploads/products/6aae34341fc1f.jpg', 0, 8),
(477, 356, 'uploads/products/6aae36280c5ab.jpg', 1, 1),
(478, 356, 'uploads/products/6aae36280c78a.jpg', 0, 2),
(479, 356, 'uploads/products/6aae36280c8fb.jpg', 0, 3),
(480, 358, 'uploads/products/6aafad5383250.png', 0, 1),
(481, 358, 'uploads/products/6aafad53834b9.png', 0, 2),
(482, 358, 'uploads/products/6aafad5383781.jpg', 0, 3),
(483, 358, 'uploads/products/6aafad5383916.jpg', 0, 4),
(484, 359, 'uploads/products/6aafafa3b71c7.png', 0, 1),
(485, 359, 'uploads/products/6aafafa3b7321.png', 0, 2),
(486, 360, 'uploads/products/6aafb1696d5f5.jpg', 0, 1),
(487, 360, 'uploads/products/6aafb1696d798.jpg', 0, 2),
(488, 360, 'uploads/products/6aafb1696da01.png', 0, 3),
(489, 361, 'uploads/products/6aafb4db91f78.jpg', 0, 1),
(490, 361, 'uploads/products/6aafb4db9210e.jpg', 0, 2),
(491, 361, 'uploads/products/6aafb4db92272.jpg', 0, 3),
(492, 362, 'uploads/products/6aafb6d90af1c.png', 0, 1),
(493, 362, 'uploads/products/6aafb6d90b19e.jpg', 0, 2),
(494, 362, 'uploads/products/6aafb6d90b329.jpg', 0, 3),
(495, 362, 'uploads/products/6aafb6d90b48c.png', 0, 4),
(496, 363, 'uploads/products/6aafb80a9e2b7.jpg', 0, 1),
(497, 363, 'uploads/products/6aafb80a9e40b.jpg', 0, 2),
(498, 363, 'uploads/products/6aafb80a9e532.png', 0, 3),
(499, 364, 'uploads/products/6aafba8fe0766.jpg', 0, 1),
(500, 365, 'uploads/products/6aafbba169881.webp', 0, 1),
(501, 366, 'uploads/products/6ab01a4827487.png', 0, 1),
(502, 366, 'uploads/products/6ab01a4827757.jpg', 0, 2),
(503, 366, 'uploads/products/6ab01a4827a2c.jpg', 0, 3),
(504, 366, 'uploads/products/6ab01a4827c0e.jpg', 0, 4),
(505, 366, 'uploads/products/6ab01a4827db6.jpg', 0, 5),
(506, 367, 'uploads/products/6ab01c3fc7334.webp', 0, 1),
(507, 367, 'uploads/products/6ab01c3fc748b.webp', 0, 2),
(508, 367, 'uploads/products/6ab01c3fc7596.webp', 0, 3),
(509, 368, 'uploads/products/6ab01d98b15d8.webp', 0, 1),
(510, 368, 'uploads/products/6ab01d98b1732.webp', 0, 2),
(511, 368, 'uploads/products/6ab01d98b187e.webp', 0, 3),
(512, 368, 'uploads/products/6ab01d98b19b9.webp', 0, 4),
(513, 369, 'uploads/products/6ab01eba1c298.webp', 0, 1),
(514, 369, 'uploads/products/6ab01eba1c3fc.png', 0, 2),
(515, 370, 'uploads/products/6ab023671222a.jpg', 0, 1),
(516, 370, 'uploads/products/6ab023671238d.jpg', 0, 2),
(517, 370, 'uploads/products/6ab02367124e5.jpg', 0, 3),
(518, 371, 'uploads/products/6ab027320ff98.jpg', 0, 1),
(519, 371, 'uploads/products/6ab0273210132.jpg', 0, 2),
(520, 371, 'uploads/products/6ab02732102ef.jpg', 0, 3),
(521, 372, 'uploads/products/6ab02c45d2459.jpg', 0, 1),
(522, 372, 'uploads/products/6ab02c45d25c1.jpg', 0, 2),
(523, 372, 'uploads/products/6ab02c45d26e9.jpg', 0, 3),
(524, 372, 'uploads/products/6ab02c45d284d.jpg', 0, 4),
(525, 372, 'uploads/products/6ab02c45d294f.jpg', 0, 5),
(526, 375, 'uploads/products/6ab0caf74b5a2.webp', 0, 1),
(527, 375, 'uploads/products/6ab0caf74b7d5.webp', 0, 2),
(528, 375, 'uploads/products/6ab0caf74b996.webp', 0, 3),
(529, 376, 'uploads/products/6ab0d229a3bbf.jpg', 0, 1),
(530, 376, 'uploads/products/6ab0d229a3f9d.jpg', 0, 2),
(531, 380, 'uploads/products/6ab0d86d0a77d.webp', 0, 1),
(532, 380, 'uploads/products/6ab0d86d0aa38.webp', 0, 2),
(533, 381, 'uploads/products/6ab0da1e6e421.jpg', 0, 1),
(534, 381, 'uploads/products/6ab0da1e6e7cb.jpg', 0, 2),
(535, 382, 'uploads/products/6ab0db7b78582.jpg', 0, 1),
(536, 382, 'uploads/products/6ab0db7b78773.jpg', 0, 2),
(537, 382, 'uploads/products/6ab0db7b7898d.jpg', 0, 3),
(538, 383, 'uploads/products/6ab0de9c6416d.jpg', 0, 1),
(539, 383, 'uploads/products/6ab0de9c64310.jpg', 0, 2),
(540, 383, 'uploads/products/6ab0de9c64485.jpg', 0, 3),
(541, 383, 'uploads/products/6ab0de9c645f0.jpg', 0, 4),
(542, 384, 'uploads/products/6ab0e04906c63.jpg', 0, 1),
(543, 384, 'uploads/products/6ab0e04906ea2.jpg', 0, 2),
(544, 384, 'uploads/products/6ab0e0490703a.jpg', 0, 3),
(545, 384, 'uploads/products/6ab0e049071c8.jpg', 0, 4),
(549, 385, 'uploads/products/6ab0ea70eb332.jpg', 0, 1),
(550, 385, 'uploads/products/6ab0ea70eb662.jpg', 0, 2),
(551, 385, 'uploads/products/6ab0ea70eb8bc.jpg', 0, 3),
(552, 386, 'uploads/products/6ab0ec381f554.jpg', 0, 1),
(553, 386, 'uploads/products/6ab0ec381f95a.jpg', 0, 2),
(554, 387, 'uploads/products/6ab0edc0e3cf0.jpg', 0, 1),
(555, 387, 'uploads/products/6ab0edc0e4043.jpg', 0, 2),
(556, 388, 'uploads/products/6ab103439d6b8.jpg', 0, 1),
(557, 388, 'uploads/products/6ab103439d8d9.jpg', 0, 2),
(558, 388, 'uploads/products/6ab103439da54.jpg', 0, 3),
(559, 388, 'uploads/products/6ab103439dc17.jpg', 0, 4),
(560, 388, 'uploads/products/6ab103439dd8c.jpg', 0, 5),
(561, 388, 'uploads/products/6ab103439ded1.jpg', 0, 6),
(562, 388, 'uploads/products/6ab103439e026.jpg', 0, 7),
(563, 390, 'uploads/products/6ab10ae086faa.jpg', 0, 1),
(564, 390, 'uploads/products/6ab10ae08722b.jpg', 0, 2),
(565, 390, 'uploads/products/6ab10ae0873e0.jpg', 0, 3),
(566, 391, 'uploads/products/6ab10f070bf50.jpg', 0, 1),
(567, 391, 'uploads/products/6ab10f070c0ff.jpg', 0, 2),
(568, 391, 'uploads/products/6ab10f070c2af.jpg', 0, 3),
(569, 391, 'uploads/products/6ab10f070c416.jpg', 0, 4),
(570, 392, 'uploads/products/6ab111b37ab4e.jpg', 0, 1),
(571, 392, 'uploads/products/6ab111b37acf0.jpg', 0, 2),
(572, 392, 'uploads/products/6ab111b37aec7.jpg', 0, 3),
(573, 392, 'uploads/products/6ab111b37b05c.jpg', 0, 4),
(574, 394, 'uploads/products/6ab1163941506.jpg', 0, 1),
(575, 394, 'uploads/products/6ab1163941700.jpg', 0, 2),
(576, 394, 'uploads/products/6ab11639418e6.jpg', 0, 3),
(577, 394, 'uploads/products/6ab1163941b15.jpg', 0, 4),
(579, 395, 'uploads/products/6ab1182830e0f.jpg', 0, 1),
(580, 396, 'uploads/products/6ab11a8c13809.jpg', 0, 1),
(581, 396, 'uploads/products/6ab11a8c139a6.jpg', 0, 2),
(582, 396, 'uploads/products/6ab11a8c13b17.jpg', 0, 3),
(583, 396, 'uploads/products/6ab11a8c13c73.jpg', 0, 4),
(584, 397, 'uploads/products/6ab11c98d2de7.jpg', 0, 1),
(585, 397, 'uploads/products/6ab11c98d2fad.jpg', 0, 2),
(586, 398, 'uploads/products/6ab11f45f1dac.jpg', 0, 1),
(587, 398, 'uploads/products/6ab11f45f1f09.jpg', 0, 2),
(588, 398, 'uploads/products/6ab11f45f20b3.jpg', 0, 3),
(589, 399, 'uploads/products/6ab1224834144.jpg', 0, 1),
(590, 399, 'uploads/products/6ab1224834380.jpg', 0, 2),
(591, 399, 'uploads/products/6ab12248345a0.jpg', 0, 3),
(592, 399, 'uploads/products/6ab12248348b2.jpg', 0, 4),
(593, 399, 'uploads/products/6ab1224834b6e.jpg', 0, 5),
(594, 399, 'uploads/products/6ab1224834cfc.jpg', 0, 6),
(595, 400, 'uploads/products/6ab1245faa5e8.jpg', 0, 1),
(596, 400, 'uploads/products/6ab1245faaa04.jpg', 0, 2),
(597, 400, 'uploads/products/6ab1245faacb6.jpg', 0, 3),
(598, 401, 'uploads/products/6ab1287796355.jpg', 0, 1),
(599, 401, 'uploads/products/6ab1287796558.jpg', 0, 2),
(600, 401, 'uploads/products/6ab12877966e0.jpg', 0, 3),
(601, 402, 'uploads/products/6ab129ff2dcba.jpg', 0, 1),
(602, 402, 'uploads/products/6ab129ff2e3a0.jpg', 0, 2),
(603, 404, 'uploads/products/6ab131b50c2de.webp', 0, 1),
(604, 404, 'uploads/products/6ab131b50c436.webp', 0, 2),
(605, 405, 'uploads/products/6ab206d15a9f4.jpg', 0, 1),
(606, 405, 'uploads/products/6ab206d15abde.jpg', 0, 2),
(607, 405, 'uploads/products/6ab206d15ae23.jpg', 0, 3),
(608, 405, 'uploads/products/6ab206d15afaa.jpg', 0, 4),
(609, 405, 'uploads/products/6ab206d15b132.jpg', 0, 5),
(610, 406, 'uploads/products/6ab208f3075a6.jpg', 0, 1),
(611, 406, 'uploads/products/6ab208f30776b.jpg', 0, 2),
(612, 407, 'uploads/products/6ab20b79f368b.jpg', 0, 1),
(613, 407, 'uploads/products/6ab20b79f38c7.jpg', 0, 2),
(614, 407, 'uploads/products/6ab20b79f3b5c.jpg', 0, 3),
(615, 408, 'uploads/products/6ab2103551ff3.jpg', 0, 1),
(616, 408, 'uploads/products/6ab2103552233.jpg', 0, 2),
(617, 409, 'uploads/products/6ab4a86823490.jpg', 0, 1),
(618, 410, 'uploads/products/6ab4c29f2afe7.jpg', 0, 1),
(619, 411, 'uploads/products/6ab4c9f6d999b.jpg', 0, 1),
(620, 411, 'uploads/products/6ab4c9f6d9bfd.jpg', 0, 2),
(621, 412, 'uploads/products/6ad9a2fb3e61b.jpg', 0, 1),
(622, 412, 'uploads/products/6ad9a2fb3e82a.jpg', 0, 2),
(623, 412, 'uploads/products/6ad9a2fb3ea70.jpg', 0, 3),
(624, 297, 'uploads/products/6ad9a51d88816.jpg', 0, 1),
(625, 297, 'uploads/products/6ad9a51d889c5.jpg', 0, 2),
(626, 297, 'uploads/products/6ad9a51d88bb1.jpg', 0, 3),
(627, 297, 'uploads/products/6ad9a51d88d85.jpg', 0, 5),
(628, 297, 'uploads/products/6ad9a51d88f07.jpg', 0, 6),
(629, 413, 'uploads/products/6ad9a6faa957b.jpg', 0, 1),
(630, 413, 'uploads/products/6ad9a6faa96c0.jpg', 0, 2),
(631, 413, 'uploads/products/6ad9a6faa9830.jpg', 0, 3),
(632, 413, 'uploads/products/6ad9a6faa9951.jpg', 0, 4),
(633, 414, 'uploads/products/6ad9ab2e475c6.jpg', 0, 1),
(634, 414, 'uploads/products/6ad9ab2e476ef.jpg', 0, 2),
(635, 414, 'uploads/products/6ad9ab2e47844.jpg', 0, 3),
(636, 414, 'uploads/products/6ad9ab2e479b8.jpg', 0, 4),
(637, 414, 'uploads/products/6ad9ab2e47af8.jpg', 0, 5),
(638, 415, 'uploads/products/6ad9bb98d5aab.jpg', 0, 1),
(639, 415, 'uploads/products/6ad9bb98d5c57.jpg', 0, 2),
(640, 415, 'uploads/products/6ad9bb98d5ddf.jpg', 0, 3),
(641, 415, 'uploads/products/6ad9bb98d5f26.jpg', 0, 4),
(642, 415, 'uploads/products/6ad9bb98d60b2.jpg', 0, 5),
(643, 416, 'uploads/products/6ad9bda91a371.jpg', 0, 1),
(644, 416, 'uploads/products/6ad9bda91a4c3.jpg', 0, 2),
(645, 416, 'uploads/products/6ad9bda91a60b.jpg', 0, 3),
(646, 417, 'uploads/products/6ad9bf5983174.jpg', 0, 1),
(647, 417, 'uploads/products/6ad9bf59832f8.jpg', 0, 2),
(648, 417, 'uploads/products/6ad9bf5983460.jpg', 0, 3),
(649, 417, 'uploads/products/6ad9bf5983596.jpg', 0, 4),
(650, 417, 'uploads/products/6ad9bf59836ef.jpg', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `seller_code` varchar(40) DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review_title` varchar(200) NOT NULL,
  `review_content` text NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('pending','approved','rejected','spam') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `customer_id`, `seller_code`, `rating`, `review_title`, `review_content`, `is_verified`, `status`, `created_at`, `updated_at`, `approved_at`) VALUES
(1, 233, 23, 'DM0002', 5, 'komal gaikwad', 'Good quality gloves with a 14-inch length. They provide good hand and wrist coverage and are suitable for heat-resistant industrial work. The material feels durable and comfortable to use.', 0, 'approved', '2026-10-14 05:25:55', '2026-10-14 05:27:30', '2026-10-14 10:57:30'),
(2, 233, 14, 'Seller_012', 5, 'komal gaikwad', 'Good quality gloves with a 14-inch length. They provide good hand and wrist coverage and are suitable for heat-resistant industrial work. The material feels durable and comfortable to use.', 0, 'approved', '2026-10-14 05:29:25', '2026-10-14 05:29:25', '2026-10-14 10:59:25'),
(3, 362, 31, 'S001', 5, 'Good Product', 'Dummy text (placeholder text) is a nonsensical or scrambled sequence of words used to fill space in a layout or design. It lets designers, developers, and writers preview fonts, spacing, and templates without distracting the reader with real', 1, 'approved', '2026-10-26 05:10:15', '2026-10-26 05:11:48', '2026-10-26 10:41:48');

-- --------------------------------------------------------

--
-- Table structure for table `product_unit_options`
--

CREATE TABLE `product_unit_options` (
  `id` int(11) NOT NULL,
  `label` varchar(20) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_unit_options`
--

INSERT INTO `product_unit_options` (`id`, `label`, `is_default`, `created_at`) VALUES
(1, 'No.', 1, '2026-08-24 12:05:50'),
(2, 'Pair', 1, '2026-08-24 12:05:50'),
(277, 'Set', 0, '2026-08-27 06:07:10'),
(1967, 'Pack', 0, '2026-09-18 11:30:39'),
(3066, 'Kit', 0, '2026-09-21 08:20:14');

-- --------------------------------------------------------

--
-- Table structure for table `product_variations`
--

CREATE TABLE `product_variations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variation_label` varchar(255) NOT NULL,
  `attributes_json` text DEFAULT NULL,
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `image_path` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variations`
--

INSERT INTO `product_variations` (`id`, `product_id`, `variation_label`, `attributes_json`, `mrp`, `selling_price`, `stock_quantity`, `image_path`, `sort_order`, `created_at`, `updated_at`) VALUES
(71, 235, 'Safety Shoes Size: 5', '[{\"attribute_id\":1,\"attribute_name\":\"Safety Shoes Size\",\"value_id\":1,\"value\":\"5\"}]', 140.00, 100.00, 120, NULL, 1, '2026-09-24 12:20:03', '2026-09-24 12:20:03'),
(72, 235, 'Safety Shoes Size: 8', '[{\"attribute_id\":1,\"attribute_name\":\"Safety Shoes Size\",\"value_id\":2,\"value\":\"8\"}]', 120.00, 100.00, 90, NULL, 2, '2026-09-24 12:20:03', '2026-09-24 12:20:03'),
(73, 356, 'colour: Blue', '[{\"attribute_id\":3,\"attribute_name\":\"colour\",\"value_id\":9,\"value\":\"Blue\"}]', 7.00, 6.00, 10, NULL, 1, '2026-10-06 05:47:09', '2026-10-06 05:47:09'),
(74, 356, 'colour: Pink', '[{\"attribute_id\":3,\"attribute_name\":\"colour\",\"value_id\":10,\"value\":\"Pink\"}]', 7.00, 6.00, 10, NULL, 2, '2026-10-06 05:47:09', '2026-10-06 05:47:09');

-- --------------------------------------------------------

--
-- Table structure for table `product_variation_attributes`
--

CREATE TABLE `product_variation_attributes` (
  `id` int(11) NOT NULL,
  `variation_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_variation_attributes`
--

INSERT INTO `product_variation_attributes` (`id`, `variation_id`, `attribute_id`, `attribute_value_id`, `created_at`) VALUES
(65, 71, 1, 1, '2026-09-24 12:20:03'),
(66, 72, 1, 2, '2026-09-24 12:20:03'),
(67, 73, 3, 9, '2026-10-06 05:47:09'),
(68, 74, 3, 10, '2026-10-06 05:47:09');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int(11) NOT NULL,
  `quotation_no` varchar(60) NOT NULL,
  `quotation_date` date NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `mobile_no` varchar(30) DEFAULT NULL,
  `eway_bill_no` varchar(80) DEFAULT NULL,
  `eway_bill_date` date DEFAULT NULL,
  `buyer_po_no` varchar(80) DEFAULT NULL,
  `buyer_po_date` date DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT 'Manual',
  `payment_date` date DEFAULT NULL,
  `transporter` varchar(150) DEFAULT NULL,
  `lr_no` varchar(80) DEFAULT NULL,
  `bill_to_name` varchar(150) DEFAULT NULL,
  `bill_to_gstin` varchar(30) DEFAULT NULL,
  `bill_to_mobile` varchar(30) DEFAULT NULL,
  `bill_to_address` text DEFAULT NULL,
  `ship_to_name` varchar(150) DEFAULT NULL,
  `ship_to_gstin` varchar(30) DEFAULT NULL,
  `ship_to_mobile` varchar(30) DEFAULT NULL,
  `ship_to_address` text DEFAULT NULL,
  `taxable_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cgst_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sgst_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `quotation_no`, `quotation_date`, `customer_name`, `mobile_no`, `eway_bill_no`, `eway_bill_date`, `buyer_po_no`, `buyer_po_date`, `payment_terms`, `payment_date`, `transporter`, `lr_no`, `bill_to_name`, `bill_to_gstin`, `bill_to_mobile`, `bill_to_address`, `ship_to_name`, `ship_to_gstin`, `ship_to_mobile`, `ship_to_address`, `taxable_total`, `cgst_total`, `sgst_total`, `grand_total`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'QTN-202606-0001', '2026-06-19', 'shital', '0133456877456', '', NULL, '', NULL, 'Manual', '2026-06-19', '', '', 'shital', '0101010101', '012345678954454', '321645650165162164623056526036456230562', '', '', '', '', 228.81, 20.60, 20.60, 270.00, 1, '2026-06-19 12:47:07', '2026-06-19 12:47:07');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` int(11) NOT NULL,
  `quotation_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `hsn` varchar(40) DEFAULT NULL,
  `unit` varchar(30) DEFAULT 'No.',
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `item_mrp` decimal(12,2) NOT NULL DEFAULT 0.00,
  `item_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `taxable_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `cgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sgst_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `product_id`, `product_name`, `product_image`, `hsn`, `unit`, `gst_rate`, `item_mrp`, `item_price`, `quantity`, `taxable_value`, `cgst_amount`, `sgst_amount`, `total_price`, `sort_order`, `created_at`) VALUES
(1, 1, 208, 'Busy Gum Boot 11”', 'uploads/products/69ba8b1bd2900.jpg', '12345678', 'No.', 18.00, 100.00, 90.00, 3.00, 228.81, 20.60, 20.60, 270.00, 0, '2026-06-19 12:47:07');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system_role` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`, `is_system_role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'Full access to all features', 1, 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(2, 'Admin', 'Full access to most features', 1, 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(3, 'Manager', 'Limited access to manage products and orders', 1, 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(4, 'Editor', 'Can edit products and categories', 1, 1, '2026-03-08 19:07:52', '2026-03-08 19:07:52'),
(5, 'Product Manager', 'Add New Categories, Products Portfolio', 0, 1, '2026-03-10 09:27:11', '2026-03-10 09:39:17');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `created_at`) VALUES
(1, 1, 8, '2026-03-08 19:07:52'),
(2, 1, 3, '2026-03-08 19:07:52'),
(3, 1, 19, '2026-03-08 19:07:52'),
(4, 1, 15, '2026-03-08 19:07:52'),
(5, 1, 10, '2026-03-08 19:07:52'),
(6, 1, 13, '2026-03-08 19:07:52'),
(7, 1, 5, '2026-03-08 19:07:52'),
(8, 1, 21, '2026-03-08 19:07:52'),
(9, 1, 17, '2026-03-08 19:07:52'),
(10, 1, 9, '2026-03-08 19:07:52'),
(11, 1, 12, '2026-03-08 19:07:52'),
(12, 1, 4, '2026-03-08 19:07:52'),
(13, 1, 20, '2026-03-08 19:07:52'),
(14, 1, 32, '2026-03-08 19:07:52'),
(15, 1, 24, '2026-03-08 19:07:52'),
(16, 1, 16, '2026-03-08 19:07:52'),
(17, 1, 27, '2026-03-08 19:07:52'),
(18, 1, 28, '2026-03-08 19:07:52'),
(19, 1, 30, '2026-03-08 19:07:52'),
(20, 1, 25, '2026-03-08 19:07:52'),
(21, 1, 6, '2026-03-08 19:07:52'),
(22, 1, 29, '2026-03-08 19:07:52'),
(23, 1, 22, '2026-03-08 19:07:52'),
(24, 1, 7, '2026-03-08 19:07:52'),
(25, 1, 1, '2026-03-08 19:07:52'),
(26, 1, 11, '2026-03-08 19:07:52'),
(27, 1, 2, '2026-03-08 19:07:52'),
(28, 1, 26, '2026-03-08 19:07:52'),
(29, 1, 18, '2026-03-08 19:07:52'),
(30, 1, 31, '2026-03-08 19:07:52'),
(31, 1, 23, '2026-03-08 19:07:52'),
(32, 1, 14, '2026-03-08 19:07:52'),
(64, 2, 8, '2026-03-08 19:07:52'),
(65, 2, 3, '2026-03-08 19:07:52'),
(66, 2, 19, '2026-03-08 19:07:52'),
(67, 2, 15, '2026-03-08 19:07:52'),
(68, 2, 10, '2026-03-08 19:07:52'),
(69, 2, 13, '2026-03-08 19:07:52'),
(70, 2, 5, '2026-03-08 19:07:52'),
(71, 2, 21, '2026-03-08 19:07:52'),
(72, 2, 17, '2026-03-08 19:07:52'),
(73, 2, 9, '2026-03-08 19:07:52'),
(74, 2, 12, '2026-03-08 19:07:52'),
(75, 2, 4, '2026-03-08 19:07:52'),
(76, 2, 20, '2026-03-08 19:07:52'),
(77, 2, 32, '2026-03-08 19:07:52'),
(78, 2, 24, '2026-03-08 19:07:52'),
(79, 2, 16, '2026-03-08 19:07:52'),
(80, 2, 27, '2026-03-08 19:07:52'),
(81, 2, 25, '2026-03-08 19:07:52'),
(82, 2, 6, '2026-03-08 19:07:52'),
(83, 2, 22, '2026-03-08 19:07:52'),
(84, 2, 7, '2026-03-08 19:07:52'),
(85, 2, 1, '2026-03-08 19:07:52'),
(86, 2, 11, '2026-03-08 19:07:52'),
(87, 2, 2, '2026-03-08 19:07:52'),
(88, 2, 26, '2026-03-08 19:07:52'),
(89, 2, 18, '2026-03-08 19:07:52'),
(90, 2, 31, '2026-03-08 19:07:52'),
(91, 2, 23, '2026-03-08 19:07:52'),
(92, 2, 14, '2026-03-08 19:07:52'),
(95, 3, 12, '2026-03-08 19:07:52'),
(96, 3, 4, '2026-03-08 19:07:52'),
(97, 3, 22, '2026-03-08 19:07:52'),
(98, 3, 1, '2026-03-08 19:07:52'),
(99, 3, 11, '2026-03-08 19:07:52'),
(100, 3, 2, '2026-03-08 19:07:52'),
(101, 3, 18, '2026-03-08 19:07:52'),
(102, 3, 14, '2026-03-08 19:07:52'),
(110, 4, 8, '2026-03-08 19:07:52'),
(111, 4, 3, '2026-03-08 19:07:52'),
(112, 4, 9, '2026-03-08 19:07:52'),
(113, 4, 4, '2026-03-08 19:07:52'),
(114, 4, 7, '2026-03-08 19:07:52'),
(115, 4, 1, '2026-03-08 19:07:52'),
(116, 4, 2, '2026-03-08 19:07:52'),
(117, 2, 33, '2026-06-17 08:03:45'),
(118, 1, 33, '2026-06-17 08:03:45'),
(121, 2, 35, '2026-06-17 08:10:35'),
(122, 1, 35, '2026-06-17 08:10:35'),
(260, 2, 172, '2026-09-28 12:01:15'),
(261, 1, 172, '2026-09-28 12:01:15'),
(265, 2, 175, '2026-10-08 07:35:30'),
(266, 1, 175, '2026-10-08 07:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `business_name`, `business_type`, `gst_number`, `pan_number`, `business_address`, `business_email`, `business_phone`, `bank_account_name`, `bank_account_number`, `bank_ifsc_code`, `bank_name`, `commission_percentage`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 4, 'Online', 'Sole Proprietorship', '', '', '', '', '', '', '', '', '', 10.00, 1, '2025-10-09 07:52:11', '2025-10-09 08:02:55'),
(2, 10, 'Nirmit Group of Companies', 'Sole Proprietorship', '', '', '', '', '', '', '', '', '', 10.00, 1, '2025-12-22 05:01:24', '2025-12-22 05:01:24'),
(3, 12, 'sachinfotech', 'Private Limited', '', '', 'A-3, Anant park Society, Near Sabri School, Vasana Road, Vadodara', 'sachinringe@gmail.com', '9998724576', '', '', '', '', 10.00, 1, '2026-03-25 08:36:00', '2026-03-25 08:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `seller_activity_log`
--

CREATE TABLE `seller_activity_log` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `activity_type` varchar(100) NOT NULL,
  `activity_description` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_activity_log`
--

INSERT INTO `seller_activity_log` (`id`, `seller_id`, `activity_type`, `activity_description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'seller_created', 'Seller account created', '2401:4900:8fca:8f5f:45b2:ec42:4608:96a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-09 07:52:11'),
(2, 1, 'status_changed', 'Seller account deactivated', '2401:4900:8fca:8f5f:45b2:ec42:4608:96a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-09 08:02:44'),
(3, 1, 'status_changed', 'Seller account activated', '2401:4900:8fca:8f5f:45b2:ec42:4608:96a', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', '2025-10-09 08:02:55'),
(4, 2, 'seller_created', 'Seller account created', '2401:4900:8fca:b33e:b4fe:6f21:89ac:68e5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-22 05:01:24'),
(5, 2, 'login', 'Seller logged in', '67.82.81.61', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-22 05:05:28'),
(6, 2, 'product_added', 'Product \'Classmate Book\' added (ID: 69, pending approval)', '67.82.81.61', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-22 05:07:39'),
(7, 2, 'product_rejected', 'Product ID 69 rejected: Please add HSN code.', '2401:4900:8fca:b33e:b4fe:6f21:89ac:68e5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-22 05:13:05'),
(8, 2, 'product_approved', 'Product ID 69 approved', '2401:4900:8fca:b33e:b4fe:6f21:89ac:68e5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-22 05:18:46'),
(9, 2, 'login', 'Seller logged in', '2405:201:200e:b93e:698a:dd95:2d29:ac86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-31 13:25:28'),
(10, 2, 'product_added', 'Product \'note Book\' added (ID: 71, pending approval)', '2405:201:200e:b93e:698a:dd95:2d29:ac86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-31 13:26:07'),
(11, 2, 'product_rejected', 'Product ID 71 rejected: not valid', '2405:201:200e:b93e:698a:dd95:2d29:ac86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-31 13:26:34'),
(12, 2, 'product_rejected', 'Product ID 71 rejected: Please add HSN CODE. You need to enter product description and MRP as well. MRP should be greater than selling price.', '2405:201:200e:b93e:698a:dd95:2d29:ac86', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2025-12-31 13:28:41'),
(13, 2, 'product_added', 'Product \'gumboots\' added (ID: 72, pending approval)', '2405:201:200e:b93e:5ce5:f767:f559:5a76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-01 12:06:26'),
(14, 2, 'product_rejected', 'Product ID 72 rejected: The hsn code isnt correct.\r\nthe product needs a pic.\r\nthe mrp is not set reasonably.\r\ngst rate is incorrect.', '2405:201:200e:b93e:5ce5:f767:f559:5a76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-01 12:07:51'),
(15, 2, 'login', 'Seller logged in', '2405:201:200e:b93e:5ce5:f767:f559:5a76', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-01 12:17:14'),
(16, 2, 'login', 'Seller logged in', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 15:55:42'),
(17, 2, 'product_resubmitted', 'Product \'gumboots-updated\' (ID: 72) resubmitted for approval after rejection', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:05:14'),
(18, 2, 'product_updated', 'Product \'gumboots-updated\' (ID: 72) updated', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:05:25'),
(19, 2, 'product_rejected', 'Product ID 72 rejected: product name needs to be updated properly', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:08:04'),
(20, 2, 'product_resubmitted', 'Product \'gumboots-updated\' (ID: 72) resubmitted for approval after rejection', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:10:23'),
(21, 2, 'product_rejected', 'Product ID 72 rejected: product name needs to be updated', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:10:46'),
(22, 2, 'product_resubmitted', 'Product \'gumboots\' (ID: 72) resubmitted for approval after rejection', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:11:11'),
(23, 2, 'product_approved', 'Product ID 72 approved', '2402:e280:3e13:180:8bd7:6d6a:c993:d404', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 16:11:36'),
(24, 2, 'login', 'Seller logged in', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 05:39:42'),
(25, 2, 'product_resubmitted', 'Product \'note Book\' (ID: 71) resubmitted for approval after rejection', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 05:40:10'),
(26, 2, 'product_added', 'Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' added (ID: 73, pending approval)', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 06:13:02'),
(27, 2, 'product_rejected', 'Product ID 73 rejected: hsn code inccorect\r\nname is not right\r\nmrp not realistic\r\nstock quantity invalid', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 06:13:44'),
(28, 2, 'product_resubmitted', 'Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' (ID: 73) resubmitted for approval after rejection', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 06:15:20'),
(29, 2, 'product_updated', 'Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' (ID: 73) updated', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 06:15:50'),
(30, 2, 'product_approved', 'Product ID 71 approved', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 06:16:25'),
(31, 2, 'product_updated', 'Product \'Gum Boots  Model - RAINFALL W/O STEEL n\' (ID: 73) updated', '2405:201:200e:b93e:184e:8a2b:3822:3442', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-02 06:17:04'),
(32, 2, 'login', 'Seller logged in', '2405:201:200e:b93e:ade3:6d0d:e495:bf22', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-18 05:48:53'),
(33, 3, 'seller_created', 'Seller account created', '123.201.95.33', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-25 08:36:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `seller_order_items`
-- (See below for the actual view)
--
CREATE TABLE `seller_order_items` (
`id` int(11)
,`order_id` int(11)
,`order_number` varchar(50)
,`tracking_id` varchar(50)
,`order_date` timestamp
,`order_total` decimal(10,2)
,`order_status_id` int(11)
,`order_status` varchar(100)
,`product_id` int(11)
,`product_name` varchar(255)
,`seller_id` int(11)
,`seller_name` varchar(255)
,`quantity` int(11)
,`price` decimal(10,2)
,`unit_price` decimal(10,2)
,`gst_amount` decimal(10,2)
,`item_total` decimal(20,2)
,`customer_name` varchar(100)
,`email` varchar(100)
,`phone` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `seller_permissions`
--

CREATE TABLE `seller_permissions` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_permissions`
--

INSERT INTO `seller_permissions` (`id`, `seller_id`, `can_manage_products`, `can_manage_categories`, `can_view_orders`, `can_view_reports`, `can_update_settings`, `can_add_products`, `can_edit_products`, `can_delete_products`, `max_products`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 100, '2025-10-09 07:52:11', '2025-10-09 07:52:11'),
(2, 2, 1, 1, 1, 1, 1, 1, 1, 0, 100, '2025-12-22 05:01:24', '2025-12-22 05:01:24'),
(3, 3, 1, 1, 1, 1, 1, 1, 1, 0, 100, '2026-03-25 08:36:00', '2026-03-25 08:36:00');

-- --------------------------------------------------------

--
-- Table structure for table `seller_product_approval_history`
--

CREATE TABLE `seller_product_approval_history` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL,
  `action_by` int(11) NOT NULL,
  `action_date` timestamp NULL DEFAULT current_timestamp(),
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_registration_applications`
--

CREATE TABLE `seller_registration_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firm_type` varchar(40) NOT NULL,
  `person_name` varchar(150) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `business_name` varchar(180) NOT NULL,
  `email` varchar(190) NOT NULL,
  `business_address` text DEFAULT NULL,
  `business_category` varchar(150) NOT NULL,
  `pan_number` varchar(20) DEFAULT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `gstin_document` varchar(255) DEFAULT NULL,
  `pan_document` varchar(255) DEFAULT NULL,
  `aadhaar_document` varchar(255) DEFAULT NULL,
  `status` enum('pending','contacted','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_registration_applications`
--

INSERT INTO `seller_registration_applications` (`id`, `firm_type`, `person_name`, `mobile`, `business_name`, `email`, `business_address`, `business_category`, `pan_number`, `gstin`, `gstin_document`, `pan_document`, `aadhaar_document`, `status`, `created_at`) VALUES
(1, 'Proprietorship', 'Samir Shah', '8780406230', 'INPROTECH', 'info@inprotech.in', 'Gujarat', 'School Stationery', NULL, NULL, NULL, NULL, NULL, 'pending', '2026-10-01 08:52:13'),
(2, 'Limited Company', 'sachin', '9998724576', 'Ringe Infotech', 'sachinringe@gmail.com', 'Testing purpose', 'Packaging Material', NULL, NULL, NULL, NULL, NULL, 'pending', '2026-10-02 09:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `seller_statistics`
--

CREATE TABLE `seller_statistics` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `total_products` int(11) DEFAULT 0,
  `active_products` int(11) DEFAULT 0,
  `pending_approval_products` int(11) DEFAULT 0,
  `total_orders` int(11) DEFAULT 0,
  `total_revenue` decimal(10,2) DEFAULT 0.00,
  `commission_paid` decimal(10,2) DEFAULT 0.00,
  `pending_commission` decimal(10,2) DEFAULT 0.00,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_statistics`
--

INSERT INTO `seller_statistics` (`id`, `seller_id`, `total_products`, `active_products`, `pending_approval_products`, `total_orders`, `total_revenue`, `commission_paid`, `pending_commission`, `last_updated`) VALUES
(1, 1, 0, NULL, NULL, 0, 0.00, 0.00, 0.00, '2025-10-09 07:52:45'),
(5, 2, 4, 3, 1, 1, 90.00, 0.00, 9.00, '2026-01-02 06:16:25'),
(38, 3, 0, NULL, NULL, 0, 0.00, 0.00, 0.00, '2026-03-25 08:36:18');

-- --------------------------------------------------------

--
-- Table structure for table `serviceable_pincodes`
--

CREATE TABLE `serviceable_pincodes` (
  `id` int(11) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `serviceable_pincodes`
--

INSERT INTO `serviceable_pincodes` (`id`, `pincode`, `city`, `state`, `is_active`, `created_at`, `updated_at`) VALUES
(47, '390001', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(48, '390002', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(49, '390003', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(50, '390004', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-26 05:40:44'),
(51, '390005', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(52, '390006', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(53, '390007', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(54, '390008', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(55, '390010', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(56, '390011', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(57, '390012', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(58, '390013', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(59, '390015', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(60, '390016', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(61, '390017', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(62, '390018', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(63, '390019', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(64, '390020', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(65, '390021', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(66, '390023', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48'),
(67, '390024', NULL, NULL, 1, '2026-09-25 12:57:48', '2026-09-25 12:57:48');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_charges`
--

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

--
-- Dumping data for table `shipping_charges`
--

INSERT INTO `shipping_charges` (`id`, `zone_id`, `charge_type`, `charge_value`, `min_order_amount`, `max_order_amount`, `is_active`, `created_at`) VALUES
(2, 2, 'fixed', 100.00, 0.00, NULL, 1, '2025-06-28 06:57:09'),
(3, 3, 'fixed', 0.00, 200.00, NULL, 1, '2025-06-28 06:57:09'),
(4, 4, 'fixed', 200.00, 0.00, NULL, 1, '2025-06-28 06:57:09'),
(7, 1, 'fixed', 70.00, 0.00, NULL, 1, '2025-07-09 08:02:30'),
(10, 8, 'fixed', 49.00, 199.00, NULL, 1, '2026-10-06 10:35:29');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_zones`
--

CREATE TABLE `shipping_zones` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_zones`
--

INSERT INTO `shipping_zones` (`id`, `name`, `description`, `is_active`, `created_at`) VALUES
(1, 'Standard Shipping (Within Maharashtra)', 'Deliveries within the same state (SGST + CGST)', 1, '2025-06-28 06:57:09'),
(2, 'National Shipping (Other States)', 'Deliveries to different states (IGST)', 1, '2025-06-28 06:57:09'),
(3, 'Free Shipping – Metro Cities', 'Areas with free shipping', 1, '2025-06-28 06:57:09'),
(4, 'Remote Area Shipping', 'Areas with premium shipping charges', 1, '2025-06-28 06:57:09'),
(8, 'Vadodara', '', 1, '2026-10-06 10:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_zone_locations`
--

CREATE TABLE `shipping_zone_locations` (
  `id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  `location_type` enum('country','state','city','pincode') NOT NULL,
  `location_value` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_zone_locations`
--

INSERT INTO `shipping_zone_locations` (`id`, `zone_id`, `location_type`, `location_value`, `created_at`) VALUES
(1, 1, 'state', 'Maharashtra', '2025-06-28 06:57:09'),
(2, 2, 'state', 'Delhi', '2025-06-28 06:57:09'),
(3, 2, 'state', 'Karnataka', '2025-06-28 06:57:09'),
(4, 2, 'state', 'Tamil Nadu', '2025-06-28 06:57:09'),
(5, 2, 'state', 'Gujarat', '2025-06-28 06:57:09'),
(6, 2, 'state', 'Rajasthan', '2025-06-28 06:57:09'),
(7, 2, 'state', 'Uttar Pradesh', '2025-06-28 06:57:09'),
(8, 2, 'state', 'West Bengal', '2025-06-28 06:57:09'),
(9, 2, 'state', 'Telangana', '2025-06-28 06:57:09'),
(10, 2, 'state', 'Andhra Pradesh', '2025-06-28 06:57:09'),
(11, 2, 'state', 'Kerala', '2025-06-28 06:57:09'),
(12, 3, 'city', 'Mumbai', '2025-06-28 06:57:09'),
(13, 3, 'city', 'Pune', '2025-06-28 06:57:09'),
(14, 3, 'city', 'Nagpur', '2025-06-28 06:57:09'),
(15, 4, 'city', 'Srinagar', '2025-06-28 06:57:09'),
(16, 4, 'city', 'Leh', '2025-06-28 06:57:09'),
(17, 4, 'city', 'Port Blair', '2025-06-28 06:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'google_search_visibility', 'hidden', '2026-08-25 10:45:03', '2026-08-28 08:26:54'),
(6326, 'main_category_order', '[2,21,9,22,4,6,1,3,5,7]', '2026-09-24 08:49:00', '2026-09-24 08:49:44');

-- --------------------------------------------------------

--
-- Table structure for table `top_deal_category_order`
--

CREATE TABLE `top_deal_category_order` (
  `category_id` int(11) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 0,
  `offer_text` varchar(100) NOT NULL DEFAULT '30-20% OFF',
  `offer_override` varchar(100) NOT NULL DEFAULT '',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `top_deal_category_order`
--

INSERT INTO `top_deal_category_order` (`category_id`, `sort_order`, `is_visible`, `offer_text`, `offer_override`, `updated_at`) VALUES
(16, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(17, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(18, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(19, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(20, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(25, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(26, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(28, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(29, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(30, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(31, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45'),
(32, 0, 1, '30-20% OFF', '', '2026-10-01 07:44:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_role`, `is_seller_approved`, `seller_approved_at`, `seller_approved_by`, `phone`, `address`, `city`, `state`, `pincode`, `is_active`, `reset_token`, `reset_token_expiry`, `created_at`) VALUES
(1, 'prakash raje', 'prakash.raje7@gmail.com', '$2y$10$xZYAgVOHQ7QSZz0mh/0avOY562C4quE.Z5u4I5rhbGXmUgeBodScy', 'customer', 0, NULL, NULL, '08788316633', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-06-22 22:27:50'),
(2, 'prakash raje', 'prakashraje020@gmail.com', '$2y$10$c3VW3JgExYTQClVeLKcxDeWlg7zWM96N9GUjKuiXZI.AgXL5K2MQi', 'customer', 0, NULL, NULL, '08788316633', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-06-22 23:44:13'),
(3, 'prakash raje', 'prakash.raje8@gmail.com', '$2y$10$NEseXDhvicQEwz.c5H.u6OrIqslhGflbJjGzQvKaKUBzTr9OSMZNe', 'customer', 0, NULL, NULL, '08788316633', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-06-22 23:47:27'),
(4, 'Nirmit Shah', 'nirmitshah2006@gmail.com', '$2y$10$i7sstMwnwFl3KKdxUByjA.MX19qccKhmXP2bWF3r68ibOqt/k1/lO', 'seller', 1, '2025-10-09 07:52:11', 1, '8208917546', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-07-29 15:48:02'),
(5, 'SAMIR SHAH', 'inprotech.cto@gmail.com', '$2y$10$.61YdqsFwPLIdEWnYTwRt.THuktqIXgPltPIoVwWLVC.3HqaXUzky', 'customer', 0, NULL, NULL, '8780406230', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-08-17 10:18:06'),
(6, 'Khushali Modi', 'khushalimodi1998@gmail.com', '$2y$10$Dg9rJJFvn47h6IaCIY5Ft.mGP9YAXvY6UE.PIqpjt8t2IdidvXZpy', 'customer', 0, NULL, NULL, '8780086227', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-08-27 15:43:12'),
(7, 'jsinrhuxrn qxmkwoyxsh', 'tktquxhz@testform.xyz', '$2y$10$NESvuEStMxrNZu11zwuu4O1dFr/AKoJUxchWN8hu6gaqXaImf29B6', 'customer', 0, NULL, NULL, '+1-228-056-1524', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-10-05 21:55:46'),
(8, 'ekhgzjvrqx kmpxxyrgiz', 'pkwrrhtk@testform.xyz', '$2y$10$MsEWIxudmdh.fO3p6pzX4OuuWcRvq/eMBbM6SrA7SfSSAOQkT0wNu', 'customer', 0, NULL, NULL, '+1-980-328-7735', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-11-11 07:35:14'),
(9, 'syumtldxug kfdfptdvme', 'jdnhxmwu@testform.xyz', '$2y$10$A33o6DBkLt1tC7Ud18Mt7OUmd986/M9mpQ8DpBakKz08WpVIteq8G', 'customer', 0, NULL, NULL, '+1-106-331-1125', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-11-21 19:14:41'),
(10, 'Nirmit Shah', 'nirmitsamirshah123@gmail.com', '$2y$10$COquJkvO/Slz3GNzmCqNyORSEKL.Q9G548Si8P28fOuyJtbWpdHtO', 'seller', 1, '2025-12-22 05:01:24', 1, '08208917546', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-12-22 04:55:06'),
(11, 'ganesh bansode', 'ganeshbansode1221@gmail.com', '$2y$10$2rTotlP7Yfqw5K2.s6hnlO1l.3M61HEw44MJw6q9ngvk4n..T55Iy', 'customer', 0, NULL, NULL, '7854895875', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-06 04:24:14'),
(12, 'sachin ringe', 'sachinringe@yahoo.co.in', '$2y$10$d08bgD9eV7nN9Vtk8vI2Lu/ylL9dcrhVzt9KsGU4VFkunpP6EvQYK', 'seller', 1, '2026-03-25 08:36:00', 1, '9998724576', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-31 06:27:19'),
(14, 'p m', 'mpradnya5@gmail.com', '$2y$10$fwfPPI6xAvIxNLbdx1jW8uwZvpGU6RlWteFoIk0Uq6wtKtf6avnOa', 'customer', 0, NULL, NULL, '9999999999', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-11 04:08:34'),
(17, 'Shital Yelkar', 'fairy.shitu@gmail.com', '$2y$10$8SMXs9q7NmKNDB02DFbJGubMkgaRTDaVXqSUpLPQd7ZmaVoZb1xCO', 'customer', 0, NULL, NULL, '7387942042', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-27 12:24:50'),
(18, 'Suman Ray', 'mynamesuman931@gmail.com', '$2y$10$aNJ/EhbogMjAI32cJaLISuYRlcdUTJsrnKugjE3ySkYp.WWlHWpGq', 'customer', 0, NULL, NULL, '7063364293', NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-02-27 21:12:59'),
(23, 'komal gaikwad', 'gaikwadkomalgaikwad2000@gmail.com', '$2y$10$E1BaUMJ0HCbR8gSn7vMYf.Rsk8jxbd2/o7PN12AreQGiOiwwAooKK', 'customer', 0, NULL, NULL, '8668644234', NULL, NULL, NULL, NULL, 1, '61027ac999cb49b215fe0f1441b14e2ce69b902fdc6c8c65bb6da5a44a60e894', '2026-03-26 14:12:32', '2026-03-26 07:24:22'),
(24, 'Shital Patil', 'yelkarshital11@gmail.com', '$2y$10$oUQTA3u2OCuITNQLpSB04OmDETpSvR06OzaXp2swrIjlQzeS3Q396', 'customer', 0, NULL, NULL, '7387942042', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-26 07:41:03'),
(26, 'Vijaykumar Kamble', 'kamblev6260@gmail.com', '$2y$10$4BB7akcVslYao2YpGogpheQl4W69c1BskmRM5ScR0bNrYa5//TROG', 'customer', 0, NULL, NULL, '07397911928', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-04-03 21:13:36'),
(27, 'Vijaykumar9 Kamble', 'Vijaylamble@gmail.com', '$2y$10$RwaoYAPzk4j3UsDNlVLoZuh5Q38WHDEq7hZxlZRFhQPj1rqFqBUJ6', 'customer', 0, NULL, NULL, '9765081039', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-06-04 05:52:44'),
(30, 'shital yelkar P', 'yelkar.shital@gmail.com', '$2y$10$ltV5wnaG1pBILo0sdpaDde6dT5sKUBOd/w2aTEVE2kKZCHFzgChsG', 'customer', 0, NULL, NULL, '7387942042', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-06-10 06:37:09'),
(31, 'sachin ringe', 'sachinringe@gmail.com', '$2y$10$71XsF0naWzCZlADmwkvPHeXxjmbamIPstFZDuPa50DplsCRgEKcfq', 'customer', 0, NULL, NULL, '9998724576', NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-09-30 04:14:12');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(32, 1, 235, '2026-03-24 09:54:28'),
(33, 1, 233, '2026-03-24 09:54:38'),
(43, 23, 116, '2026-03-26 09:09:22'),
(44, 23, 117, '2026-03-26 09:09:22'),
(45, 23, 118, '2026-03-26 09:09:23'),
(46, 23, 113, '2026-03-26 09:09:24'),
(47, 23, 112, '2026-03-26 09:09:25'),
(49, 23, 110, '2026-03-26 09:09:27'),
(50, 23, 109, '2026-03-26 09:09:28'),
(51, 23, 115, '2026-03-26 09:09:30'),
(52, 23, 114, '2026-03-26 09:09:32'),
(53, 23, 106, '2026-03-26 09:09:33'),
(54, 23, 107, '2026-03-26 09:09:34'),
(55, 23, 108, '2026-03-26 09:09:35'),
(57, 23, 104, '2026-03-26 09:09:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_admins_role` (`role_id`);

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_brand_name` (`name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_cart_user` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_parent_category` (`parent_id`),
  ADD KEY `idx_seller_id` (`seller_id`),
  ADD KEY `idx_categories_seller` (`seller_id`);

--
-- Indexes for table `category_parent_assignments`
--
ALTER TABLE `category_parent_assignments`
  ADD PRIMARY KEY (`category_id`,`parent_id`),
  ADD KEY `idx_category_parent_parent` (`parent_id`),
  ADD KEY `idx_category_parent_primary` (`category_id`,`is_primary`);

--
-- Indexes for table `footer_pages`
--
ALTER TABLE `footer_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_footer_pages_slug` (`slug`),
  ADD UNIQUE KEY `uq_footer_pages_legacy_path` (`legacy_path`),
  ADD KEY `idx_footer_pages_active_order` (`is_active`,`sort_order`,`id`);

--
-- Indexes for table `manual_invoices`
--
ALTER TABLE `manual_invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`);

--
-- Indexes for table `manual_invoice_items`
--
ALTER TABLE `manual_invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_manual_invoice_items_invoice_id` (`invoice_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD UNIQUE KEY `tracking_id` (`tracking_id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_status` (`status`),
  ADD KEY `idx_orders_created` (`created_at`),
  ADD KEY `idx_orders_shipping` (`shipping_zone_id`,`billing_state`,`billing_city`),
  ADD KEY `fk_orders_status` (`order_status_id`),
  ADD KEY `idx_orders_tracking_id` (`tracking_id`),
  ADD KEY `idx_orders_payment_status` (`payment_status`),
  ADD KEY `fk_orders_address` (`address_id`),
  ADD KEY `idx_seller_orders` (`seller_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_statuses`
--
ALTER TABLE `order_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_status_id` (`order_status_id`),
  ADD KEY `idx_order_status_history_order` (`order_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `popup_settings`
--
ALTER TABLE `popup_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_active` (`is_active`),
  ADD KEY `idx_products_featured` (`is_featured`),
  ADD KEY `idx_products_discounted` (`is_discounted`),
  ADD KEY `idx_products_max_quantity` (`max_quantity_per_order`),
  ADD KEY `idx_seller_id` (`seller_id`),
  ADD KEY `idx_is_approved` (`is_approved`),
  ADD KEY `idx_products_seller_approved` (`seller_id`,`is_approved`,`is_active`),
  ADD KEY `idx_products_brand` (`brand_id`);

--
-- Indexes for table `product_attributes`
--
ALTER TABLE `product_attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attribute_value` (`attribute_id`,`value`),
  ADD KEY `idx_attribute_values_attribute` (`attribute_id`);

--
-- Indexes for table `product_category_assignments`
--
ALTER TABLE `product_category_assignments`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `idx_product_category_category` (`category_id`),
  ADD KEY `idx_product_category_primary` (`product_id`,`is_primary`);

--
-- Indexes for table `product_category_parent_visibility`
--
ALTER TABLE `product_category_parent_visibility`
  ADD PRIMARY KEY (`product_id`,`category_id`,`parent_id`),
  ADD KEY `idx_product_parent_visibility_category` (`category_id`,`parent_id`),
  ADD KEY `idx_product_parent_visibility_product` (`product_id`);

--
-- Indexes for table `product_discount_selections`
--
ALTER TABLE `product_discount_selections`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_reviews_product_status_date` (`product_id`,`status`,`created_at`),
  ADD KEY `idx_product_reviews_status_date` (`status`,`created_at`),
  ADD KEY `idx_product_reviews_customer` (`customer_id`),
  ADD KEY `idx_product_reviews_seller_code` (`seller_code`),
  ADD KEY `idx_product_reviews_rating` (`rating`);

--
-- Indexes for table `product_unit_options`
--
ALTER TABLE `product_unit_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `label` (`label`);

--
-- Indexes for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_variations_product` (`product_id`);

--
-- Indexes for table `product_variation_attributes`
--
ALTER TABLE `product_variation_attributes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_variation_attribute` (`variation_id`,`attribute_id`),
  ADD KEY `idx_variation_attributes_variation` (`variation_id`),
  ADD KEY `idx_variation_attributes_lookup` (`attribute_id`,`attribute_value_id`),
  ADD KEY `fk_variation_attributes_value` (`attribute_value_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotation_no` (`quotation_no`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_quotation_items_quotation_id` (`quotation_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  ADD KEY `permission_id` (`permission_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_seller` (`user_id`);

--
-- Indexes for table `seller_activity_log`
--
ALTER TABLE `seller_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seller_activity` (`seller_id`,`created_at`);

--
-- Indexes for table `seller_permissions`
--
ALTER TABLE `seller_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seller_permissions` (`seller_id`);

--
-- Indexes for table `seller_product_approval_history`
--
ALTER TABLE `seller_product_approval_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `action_by` (`action_by`);

--
-- Indexes for table `seller_registration_applications`
--
ALTER TABLE `seller_registration_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_seller_registration_status_created` (`status`,`created_at`),
  ADD KEY `idx_seller_registration_email` (`email`),
  ADD KEY `idx_seller_registration_mobile` (`mobile`);

--
-- Indexes for table `seller_statistics`
--
ALTER TABLE `seller_statistics`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_seller_stats` (`seller_id`);

--
-- Indexes for table `serviceable_pincodes`
--
ALTER TABLE `serviceable_pincodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pincode` (`pincode`),
  ADD KEY `idx_serviceable_pincodes_pincode` (`pincode`),
  ADD KEY `idx_serviceable_pincodes_active` (`is_active`);

--
-- Indexes for table `shipping_charges`
--
ALTER TABLE `shipping_charges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_shipping_charges_zone` (`zone_id`,`is_active`);

--
-- Indexes for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shipping_zone_locations`
--
ALTER TABLE `shipping_zone_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_location` (`zone_id`,`location_type`,`location_value`),
  ADD KEY `idx_shipping_zone_locations` (`location_type`,`location_value`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `top_deal_category_order`
--
ALTER TABLE `top_deal_category_order`
  ADD PRIMARY KEY (`category_id`),
  ADD KEY `idx_top_deal_category_order_sort` (`sort_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_email` (`email`),
  ADD KEY `idx_users_active` (`is_active`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_wishlist_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=487;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `footer_pages`
--
ALTER TABLE `footer_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42042;

--
-- AUTO_INCREMENT for table `manual_invoices`
--
ALTER TABLE `manual_invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `manual_invoice_items`
--
ALTER TABLE `manual_invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT for table `order_statuses`
--
ALTER TABLE `order_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10722;

--
-- AUTO_INCREMENT for table `popup_settings`
--
ALTER TABLE `popup_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT for table `product_attributes`
--
ALTER TABLE `product_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=651;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_unit_options`
--
ALTER TABLE `product_unit_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5431;

--
-- AUTO_INCREMENT for table `product_variations`
--
ALTER TABLE `product_variations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `product_variation_attributes`
--
ALTER TABLE `product_variation_attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10814;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seller_activity_log`
--
ALTER TABLE `seller_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `seller_permissions`
--
ALTER TABLE `seller_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `seller_product_approval_history`
--
ALTER TABLE `seller_product_approval_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `seller_registration_applications`
--
ALTER TABLE `seller_registration_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `seller_statistics`
--
ALTER TABLE `seller_statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `serviceable_pincodes`
--
ALTER TABLE `serviceable_pincodes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `shipping_charges`
--
ALTER TABLE `shipping_charges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `shipping_zones`
--
ALTER TABLE `shipping_zones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shipping_zone_locations`
--
ALTER TABLE `shipping_zone_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20800;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

-- --------------------------------------------------------

--
-- Structure for view `seller_order_items`
--
DROP TABLE IF EXISTS `seller_order_items`;

CREATE ALGORITHM=UNDEFINED DEFINER=`u728317772_everythingb2c`@`127.0.0.1` SQL SECURITY DEFINER VIEW `seller_order_items`  AS SELECT `oi`.`id` AS `id`, `oi`.`order_id` AS `order_id`, `o`.`order_number` AS `order_number`, `o`.`tracking_id` AS `tracking_id`, `o`.`created_at` AS `order_date`, `o`.`total_amount` AS `order_total`, `o`.`order_status_id` AS `order_status_id`, `os`.`name` AS `order_status`, `oi`.`product_id` AS `product_id`, `p`.`name` AS `product_name`, `p`.`seller_id` AS `seller_id`, `s`.`business_name` AS `seller_name`, `oi`.`quantity` AS `quantity`, `oi`.`price` AS `price`, `oi`.`unit_price` AS `unit_price`, `oi`.`gst_amount` AS `gst_amount`, `oi`.`price`* `oi`.`quantity` AS `item_total`, `u`.`name` AS `customer_name`, `u`.`email` AS `email`, `u`.`phone` AS `phone` FROM (((((`order_items` `oi` join `products` `p` on(`oi`.`product_id` = `p`.`id`)) left join `sellers` `s` on(`p`.`seller_id` = `s`.`id`)) join `orders` `o` on(`oi`.`order_id` = `o`.`id`)) left join `order_statuses` `os` on(`o`.`order_status_id` = `os`.`id`)) join `users` `u` on(`o`.`user_id` = `u`.`id`)) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `fk_admins_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_parent_category` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `manual_invoice_items`
--
ALTER TABLE `manual_invoice_items`
  ADD CONSTRAINT `fk_manual_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `manual_invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_orders_status` FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`order_status_id`) REFERENCES `order_statuses` (`id`);

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_attribute_values`
--
ALTER TABLE `product_attribute_values`
  ADD CONSTRAINT `fk_attribute_values_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `fk_product_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variations`
--
ALTER TABLE `product_variations`
  ADD CONSTRAINT `fk_product_variations_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variation_attributes`
--
ALTER TABLE `product_variation_attributes`
  ADD CONSTRAINT `fk_variation_attributes_attribute` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_variation_attributes_value` FOREIGN KEY (`attribute_value_id`) REFERENCES `product_attribute_values` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_variation_attributes_variation` FOREIGN KEY (`variation_id`) REFERENCES `product_variations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `fk_quotation_items_quotation` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_activity_log`
--
ALTER TABLE `seller_activity_log`
  ADD CONSTRAINT `seller_activity_log_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_permissions`
--
ALTER TABLE `seller_permissions`
  ADD CONSTRAINT `seller_permissions_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_product_approval_history`
--
ALTER TABLE `seller_product_approval_history`
  ADD CONSTRAINT `seller_product_approval_history_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_product_approval_history_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seller_product_approval_history_ibfk_3` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_statistics`
--
ALTER TABLE `seller_statistics`
  ADD CONSTRAINT `seller_statistics_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_charges`
--
ALTER TABLE `shipping_charges`
  ADD CONSTRAINT `shipping_charges_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping_zone_locations`
--
ALTER TABLE `shipping_zone_locations`
  ADD CONSTRAINT `shipping_zone_locations_ibfk_1` FOREIGN KEY (`zone_id`) REFERENCES `shipping_zones` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `top_deal_category_order`
--
ALTER TABLE `top_deal_category_order`
  ADD CONSTRAINT `fk_top_deal_category_order_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
