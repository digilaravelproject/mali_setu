-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2025 at 01:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ott_solo`
--

-- --------------------------------------------------------

--
-- Table structure for table `businesses`
--

CREATE TABLE `businesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` enum('product','service') NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `verification_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `verified_at` timestamp NULL DEFAULT NULL,
  `subscription_status` enum('active','expired','trial') NOT NULL DEFAULT 'trial',
  `subscription_expires_at` timestamp NULL DEFAULT NULL,
  `job_posting_limit` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `status` enum('active','suspended','banned') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `businesses`
--

INSERT INTO `businesses` (`id`, `user_id`, `business_name`, `business_type`, `category_id`, `description`, `contact_phone`, `contact_email`, `website`, `verification_status`, `verified_at`, `subscription_status`, `subscription_expires_at`, `job_posting_limit`, `created_at`, `updated_at`, `verified_by`, `rejection_reason`, `status`) VALUES
(1, 22, 'Mali Fresh Vegetables', 'product', 1, 'Fresh organic vegetables directly from farm to your table. We specialize in seasonal vegetables and maintain highest quality standards.', '+91-7393348494', 'malifreshvegetables@business.com', 'www.malifreshvegetables.com', 'approved', '2025-08-23 08:02:58', 'active', '2025-12-23 08:02:58', 8, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(2, 23, 'Sai Catering Services', 'service', 1, 'Traditional Maharashtrian cuisine catering for weddings, events, and corporate functions.', '+91-4884107524', 'saicateringservices@business.com', 'www.saicateringservices.com', 'approved', '2025-09-03 04:44:17', 'active', '2026-04-23 08:02:58', 13, '2025-08-23 08:02:58', '2025-09-03 04:44:17', 1, NULL, 'active'),
(3, 24, 'Ganesh General Store', 'product', 2, 'Your neighborhood store for daily essentials, groceries, and household items.', '+91-9628383022', 'ganeshgeneralstore@business.com', 'www.ganeshgeneralstore.com', 'approved', '2025-09-03 04:44:49', 'active', '2026-08-23 08:02:58', 11, '2025-08-23 08:02:58', '2025-09-03 04:44:49', 1, NULL, 'active'),
(4, 25, 'Shivaji Transport Services', 'service', 5, 'Reliable transportation services for goods and passengers across Maharashtra.', '+91-6596869805', 'shivajitransportservices@business.com', 'www.shivajitransportservices.com', 'pending', '2025-08-23 08:02:58', 'active', '2026-01-23 08:02:58', 7, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(5, 26, 'Laxmi Textiles', 'product', 1, 'Quality textile manufacturing with traditional and modern designs.', '+91-7869032862', 'laxmitextiles@business.com', 'www.laxmitextiles.com', 'approved', '2025-08-23 08:02:58', 'trial', '2026-02-23 08:02:58', 17, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(6, 27, 'Marathi Mandal Event Management', 'service', 3, 'Complete event management services for weddings, cultural programs, and corporate events.', '+91-9780620054', 'marathimandaleventmanagement@business.com', 'www.marathimandaleventmanagement.com', 'pending', '2025-08-23 08:02:58', 'active', '2025-12-23 08:02:58', 12, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(7, 28, 'Krishna Dairy Products', 'product', 2, 'Fresh dairy products from our own dairy farm with home delivery services.', '+91-5176215821', 'krishnadairyproducts@business.com', 'www.krishnadairyproducts.com', 'pending', '2025-08-23 08:02:58', 'active', '2026-08-23 08:02:58', 14, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(8, 35, 'Bharat Electronics Repair', 'service', 4, 'Expert repair services for all electronic appliances and gadgets.', '+91-0929270599', 'bharatelectronicsrepair@business.com', 'www.bharatelectronicsrepair.com', 'approved', '2025-08-23 08:02:58', 'expired', '2026-01-23 08:02:58', 18, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(9, 22, 'Pune Agro Solutions', 'product', 4, 'Modern farming solutions, seeds, fertilizers, and agricultural equipment.', '+91-5578247162', 'puneagrosolutions@business.com', 'www.puneagrosolutions.com', 'approved', NULL, 'active', '2025-09-23 08:02:58', 20, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active'),
(10, 23, 'Maharaja Sweets & Snacks', 'product', 5, 'Traditional Maharashtrian sweets and snacks made with authentic recipes.', '+91-3461609983', 'maharajasweets&snacks@business.com', 'www.maharajasweets&snacks.com', 'pending', '2025-08-23 08:02:58', 'expired', '2026-02-23 08:02:58', 18, '2025-08-23 08:02:58', '2025-08-23 08:02:58', NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `business_categories`
--

CREATE TABLE `business_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_categories`
--

INSERT INTO `business_categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Agriculture & Farming', 'Agricultural products and farming services', 1, '2025-08-23 08:02:57', '2025-08-23 08:02:57'),
(2, 'Food & Beverages', 'Food products and catering services', 1, '2025-08-23 08:02:57', '2025-08-23 08:02:57'),
(3, 'Retail & Trading', 'Retail shops and trading businesses', 1, '2025-08-23 08:02:57', '2025-08-23 08:02:57'),
(4, 'Services', 'Professional and personal services', 1, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(5, 'Manufacturing', 'Manufacturing and production businesses', 1, '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `business_locations`
--

CREATE TABLE `business_locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `address_line_1` varchar(255) NOT NULL,
  `address_line_2` varchar(255) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL DEFAULT 'India',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `location_type` varchar(255) NOT NULL DEFAULT 'main',
  `contact_phone` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `operating_hours` text DEFAULT NULL,
  `special_instructions` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_locations`
--

INSERT INTO `business_locations` (`id`, `business_id`, `address_line_1`, `address_line_2`, `city`, `state`, `postal_code`, `country`, `latitude`, `longitude`, `is_primary`, `is_active`, `location_type`, `contact_phone`, `contact_email`, `operating_hours`, `special_instructions`, `created_at`, `updated_at`) VALUES
(1, 1, '65, Nilima Apartments, DavidGunj', 'Street', 'Kolkata', 'Maharashtra', '722262', 'India', 19.17313400, 73.38134400, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(2, 2, '32, ShashankPur,', NULL, 'Chennai', 'Maharashtra', '852054', 'India', 18.76718400, 73.77662200, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(3, 3, '75, Babita Villas, Shanti Chowk', 'Street', 'Lucknow', 'Maharashtra', '167748', 'India', 19.45593300, 73.68014100, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 4, '32, Aslam Heights, Yeshwanthpura', NULL, 'Pune', 'Maharashtra', '308441', 'India', 19.44169300, 73.28281100, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(5, 5, '80, Surya Nagar,', NULL, 'Ahmedabad', 'Maharashtra', '750463', 'India', 18.99922000, 73.33051500, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(6, 6, '92, Pradeep Society, KrishnaPur', NULL, 'Chennai', 'Maharashtra', '848783', 'India', 18.74510800, 72.83788800, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(7, 7, '33, Raj Villas, Goregaon', NULL, 'Pune', 'Maharashtra', '038113', 'India', 18.97952900, 73.05255800, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(8, 8, '47, Mansarovar,', 'Street', 'Chennai', 'Maharashtra', '245581', 'India', 19.43153700, 73.49865300, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(9, 9, '49, Rupal Nagar,', NULL, 'Mumbai', 'Maharashtra', '871313', 'India', 18.84470100, 72.84909400, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(10, 10, '26, Hadapsar,', 'Street', 'Bangalore', 'Maharashtra', '746775', 'India', 19.08958400, 73.69360200, 1, 1, 'main', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `business_reviews`
--

CREATE TABLE `business_reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(10) UNSIGNED NOT NULL COMMENT 'Rating from 1 to 5',
  `review_text` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `moderated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `moderated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `caste_certificates`
--

CREATE TABLE `caste_certificates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `verification_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_conversations`
--

CREATE TABLE `chat_conversations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user1_id` bigint(20) UNSIGNED NOT NULL,
  `user2_id` bigint(20) UNSIGNED NOT NULL,
  `last_message_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `conversation_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message_text` text NOT NULL,
  `message_type` enum('text','image','file') NOT NULL DEFAULT 'text',
  `attachment_path` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `connection_requests`
--

CREATE TABLE `connection_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `response_message` text DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cause_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `payment_method` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `receipt_url` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `cause_id`, `amount`, `currency`, `payment_method`, `razorpay_payment_id`, `razorpay_order_id`, `status`, `receipt_url`, `message`, `anonymous`, `created_at`, `updated_at`) VALUES
(1, 22, 2, 2000.00, 'INR', 'netbanking', 'pay_erkbdkpfca', 'order_yrbalzmpoo', 'completed', NULL, 'Qui iure aperiam corrupti officia facere nam autem.', 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(2, 4, 4, 1000.00, 'INR', 'upi', 'pay_iowfftcftt', 'order_igiiyzjmzb', 'completed', NULL, 'Voluptas assumenda consequatur eaque non modi velit.', 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(3, 25, 2, 10000.00, 'INR', 'card', 'pay_vdnndedhji', 'order_aplisthbee', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 4, 1, 500.00, 'INR', 'netbanking', 'pay_roefikgzzy', 'order_wptotqrhth', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(5, 11, 2, 2000.00, 'INR', 'upi', 'pay_zhruhoqhqv', 'order_luhguvvnin', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(6, 1, 2, 2000.00, 'INR', 'wallet', 'pay_yvpfikwhuf', 'order_fyphgsllvd', 'completed', NULL, 'Voluptatem id eum culpa inventore deserunt.', 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(7, 15, 1, 1000.00, 'INR', 'upi', 'pay_mxhvjejvzq', 'order_msnmnurypq', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(8, 13, 2, 500.00, 'INR', 'netbanking', 'pay_vbwcdqnpsd', 'order_maaailherb', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(9, 14, 2, 5000.00, 'INR', 'wallet', 'pay_rprhwyoset', 'order_vldqqntecq', 'completed', NULL, NULL, 1, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(10, 22, 2, 2000.00, 'INR', 'wallet', 'pay_kogmatwyky', 'order_cwegwixyrb', 'completed', NULL, 'Cum veniam nisi quo quia esse quia delectus.', 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(11, 7, 3, 2000.00, 'INR', 'upi', 'pay_vwsshdhmkp', 'order_qgneviyqlc', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(12, 4, 3, 2000.00, 'INR', 'upi', 'pay_sjxugfkiwn', 'order_cbazxzeyqm', 'completed', NULL, NULL, 0, '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `donation_causes`
--

CREATE TABLE `donation_causes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(255) NOT NULL,
  `target_amount` decimal(10,2) NOT NULL,
  `raised_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `urgency` enum('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `location` varchar(255) DEFAULT NULL,
  `organization` varchar(255) NOT NULL,
  `contact_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`contact_info`)),
  `image_url` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('draft','active','paused','completed','cancelled') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_causes`
--

INSERT INTO `donation_causes` (`id`, `title`, `description`, `category`, `target_amount`, `raised_amount`, `urgency`, `location`, `organization`, `contact_info`, `image_url`, `start_date`, `end_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mali Community Education Fund', 'Supporting education for underprivileged children in Mali community', 'Education', 500000.00, 1500.00, 'high', 'Mumbai, Maharashtra', 'Mali Education Trust', '\"{\\\"email\\\":\\\"education@malisetu.com\\\",\\\"phone\\\":\\\"+91-9876543210\\\",\\\"address\\\":\\\"Mumbai, Maharashtra\\\"}\"', NULL, '2025-08-23', '2026-08-23', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(2, 'Healthcare Support Initiative', 'Providing medical assistance to families in need', 'Healthcare', 300000.00, 23500.00, 'critical', 'Pune, Maharashtra', 'Mali Health Foundation', '\"{\\\"email\\\":\\\"health@malisetu.com\\\",\\\"phone\\\":\\\"+91-9876543211\\\",\\\"address\\\":\\\"Pune, Maharashtra\\\"}\"', NULL, '2025-08-23', '2026-02-23', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(3, 'Women Empowerment Program', 'Supporting women entrepreneurs and skill development', 'Social Welfare', 200000.00, 4000.00, 'medium', 'Nashik, Maharashtra', 'Mali Women Welfare Society', '\"{\\\"email\\\":\\\"women@malisetu.com\\\",\\\"phone\\\":\\\"+91-9876543212\\\",\\\"address\\\":\\\"Nashik, Maharashtra\\\"}\"', NULL, '2025-08-23', '2027-02-23', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 'Emergency Relief Fund', 'Emergency assistance for natural disasters and crises', 'Emergency', 1000000.00, 1000.00, 'critical', 'Maharashtra', 'Mali Emergency Response Team', '\"{\\\"email\\\":\\\"emergency@malisetu.com\\\",\\\"phone\\\":\\\"+91-9876543213\\\",\\\"address\\\":\\\"Maharashtra\\\"}\"', NULL, '2025-08-23', NULL, 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `job_posting_id` bigint(20) UNSIGNED NOT NULL,
  `cover_letter` text DEFAULT NULL,
  `resume_url` varchar(255) DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  `status` enum('pending','reviewed','accepted','rejected') NOT NULL DEFAULT 'pending',
  `employer_notes` text DEFAULT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `requirements` text NOT NULL,
  `salary_range` varchar(255) DEFAULT NULL,
  `job_type` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `experience_level` varchar(255) DEFAULT NULL,
  `employment_type` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `skills_required` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`skills_required`)),
  `benefits` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`benefits`)),
  `application_deadline` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`id`, `business_id`, `title`, `description`, `requirements`, `salary_range`, `job_type`, `location`, `experience_level`, `employment_type`, `category`, `skills_required`, `benefits`, `application_deadline`, `status`, `is_active`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Sales Executive', 'We are looking for a dynamic Sales Executive to join our team. The candidate will be responsible for generating leads, meeting clients, and achieving sales targets.', 'Graduate with 2+ years of sales experience. Good communication skills in Hindi, Marathi, and English. Own vehicle preferred.', '₹25,000 - ₹45,000 per month', 'permanent', 'Hyderabad', 'mid_level', 'full_time', 'Sales & Marketing', '\"[\\\"Sales\\\",\\\"Communication\\\",\\\"Client Management\\\",\\\"Lead Generation\\\"]\"', '\"[\\\"Health Insurance\\\",\\\"Performance Bonus\\\",\\\"Travel Allowance\\\"]\"', '2025-09-15 08:02:58', 'approved', 1, '2025-10-04 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(2, 5, 'Delivery Boy', 'Looking for reliable delivery personnel for our food delivery service. Must have own two-wheeler and valid driving license.', '10th pass minimum. Own two-wheeler with valid license. Knowledge of local area. Age 18-35 years.', '₹15,000 - ₹25,000 per month', 'permanent', 'Delhi', 'entry_level', 'part_time', 'Delivery & Logistics', '\"[\\\"Driving\\\",\\\"Time Management\\\",\\\"Customer Service\\\"]\"', '\"[\\\"Fuel Allowance\\\",\\\"Incentives\\\",\\\"Flexible Hours\\\"]\"', '2025-08-20 08:02:58', 'approved', 0, '2025-08-11 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(3, 8, 'Store Manager', 'Experienced Store Manager required for our retail outlet. Responsible for inventory management, staff supervision, and customer service.', 'Graduate with 3+ years retail experience. Strong leadership and organizational skills. Computer literacy required.', '₹45,000 - ₹75,000 per month', 'temporary', 'Ahmedabad', 'senior_level', 'full_time', 'Retail & Management', '\"[\\\"Management\\\",\\\"Inventory Control\\\",\\\"Leadership\\\",\\\"Customer Service\\\"]\"', '\"[\\\"Health Insurance\\\",\\\"PF\\\",\\\"Annual Bonus\\\",\\\"Career Growth\\\"]\"', '2025-08-12 08:02:58', 'approved', 0, '2025-08-15 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 9, 'Farm Worker', 'Seeking dedicated farm workers for agricultural operations. Work includes planting, harvesting, and general farm maintenance.', 'Experience in farming preferred. Physical fitness required. Willingness to work in outdoor conditions.', '₹15,000 - ₹25,000 per month', 'permanent', 'Bangalore', 'entry_level', 'contract', 'Agriculture', '\"[\\\"Farming\\\",\\\"Physical Work\\\",\\\"Equipment Operation\\\"]\"', '\"[\\\"Accommodation\\\",\\\"Meals\\\",\\\"Seasonal Bonus\\\"]\"', '2025-09-08 08:02:58', 'approved', 1, '2025-10-02 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(5, 1, 'Cook/Chef', 'Experienced cook required for our catering business. Must know traditional Maharashtrian cuisine and modern cooking techniques.', 'Minimum 2 years cooking experience. Knowledge of Maharashtrian cuisine essential. Food safety certification preferred.', '₹25,000 - ₹45,000 per month', 'permanent', 'Kolkata', 'mid_level', 'full_time', 'Food & Hospitality', '\"[\\\"Cooking\\\",\\\"Food Safety\\\",\\\"Menu Planning\\\",\\\"Kitchen Management\\\"]\"', '\"[\\\"Accommodation\\\",\\\"Meals\\\",\\\"Festival Bonus\\\"]\"', '2025-09-08 08:02:58', 'approved', 1, '2025-11-14 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(6, 5, 'Accountant', 'Looking for qualified accountant to handle books of accounts, GST filing, and financial reporting for our business.', 'B.Com/M.Com with 2+ years experience. Knowledge of Tally, GST, and income tax. CA inter preferred.', '₹25,000 - ₹45,000 per month', 'temporary', 'Lucknow', 'mid_level', 'full_time', 'Finance & Accounting', '\"[\\\"Accounting\\\",\\\"Tally\\\",\\\"GST\\\",\\\"Financial Reporting\\\"]\"', '\"[\\\"Health Insurance\\\",\\\"PF\\\",\\\"Professional Development\\\"]\"', '2025-09-23 08:02:58', 'pending', 1, '2025-10-27 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(7, 8, 'Driver', 'Experienced driver required for goods transportation. Must have heavy vehicle license and clean driving record.', 'Valid heavy vehicle license. 3+ years driving experience. Clean driving record. Age 25-45 years.', '₹25,000 - ₹45,000 per month', 'contract', 'Chennai', 'mid_level', 'full_time', 'Transportation', '\"[\\\"Driving\\\",\\\"Vehicle Maintenance\\\",\\\"Route Planning\\\"]\"', '\"[\\\"Fuel Allowance\\\",\\\"Overtime Pay\\\",\\\"Insurance\\\"]\"', '2025-10-10 08:02:58', 'approved', 1, '2025-10-13 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(8, 9, 'Tailor', 'Skilled tailor needed for our textile business. Must be proficient in both traditional and modern stitching techniques.', 'Minimum 3 years tailoring experience. Knowledge of different fabrics and stitching techniques. Own sewing machine preferred.', '₹25,000 - ₹45,000 per month', 'contract', 'Lucknow', 'mid_level', 'full_time', 'Manufacturing', '\"[\\\"Tailoring\\\",\\\"Pattern Making\\\",\\\"Fabric Knowledge\\\",\\\"Quality Control\\\"]\"', '\"[\\\"Piece Rate Bonus\\\",\\\"Festival Bonus\\\",\\\"Skill Development\\\"]\"', '2025-09-16 08:02:58', 'pending', 1, '2025-11-21 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(9, 1, 'Event Coordinator', 'Dynamic event coordinator required for managing weddings and cultural events. Must have excellent organizational skills.', 'Graduate with event management experience. Excellent communication skills. Ability to work under pressure and flexible hours.', '₹25,000 - ₹45,000 per month', 'temporary', 'Kolkata', 'mid_level', 'full_time', 'Event Management', '\"[\\\"Event Planning\\\",\\\"Coordination\\\",\\\"Communication\\\",\\\"Time Management\\\"]\"', '\"[\\\"Performance Bonus\\\",\\\"Travel Allowance\\\",\\\"Networking Opportunities\\\"]\"', '2025-09-22 08:02:58', 'approved', 1, '2025-11-11 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(10, 5, 'Technician', 'Electronics repair technician needed for our service center. Must be skilled in repairing various electronic appliances.', 'ITI/Diploma in Electronics. 2+ years repair experience. Knowledge of modern electronic appliances and tools.', '₹25,000 - ₹45,000 per month', 'permanent', 'Hyderabad', 'mid_level', 'full_time', 'Technical Services', '\"[\\\"Electronics Repair\\\",\\\"Troubleshooting\\\",\\\"Customer Service\\\",\\\"Technical Knowledge\\\"]\"', '\"[\\\"Health Insurance\\\",\\\"Tool Allowance\\\",\\\"Training Programs\\\"]\"', '2025-09-02 08:02:58', 'approved', 1, '2025-11-02 08:02:58', '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `matrimony_profiles`
--

CREATE TABLE `matrimony_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `age` int(11) NOT NULL,
  `height` varchar(10) DEFAULT NULL,
  `weight` varchar(10) DEFAULT NULL,
  `complexion` varchar(50) DEFAULT NULL,
  `physical_status` varchar(50) DEFAULT NULL,
  `personal_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`personal_details`)),
  `family_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`family_details`)),
  `education_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`education_details`)),
  `professional_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`professional_details`)),
  `lifestyle_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`lifestyle_details`)),
  `location_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`location_details`)),
  `partner_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`partner_preferences`)),
  `privacy_settings` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`privacy_settings`)),
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_at` timestamp NULL DEFAULT NULL,
  `profile_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `status` enum('active','suspended','banned') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `matrimony_profiles`
--

INSERT INTO `matrimony_profiles` (`id`, `user_id`, `age`, `height`, `weight`, `complexion`, `physical_status`, `personal_details`, `family_details`, `education_details`, `professional_details`, `lifestyle_details`, `location_details`, `partner_preferences`, `privacy_settings`, `approval_status`, `approved_at`, `profile_expires_at`, `created_at`, `updated_at`, `approved_by`, `rejection_reason`, `status`) VALUES
(1, 12, 27, '156 cm', '46 kg', 'Dark', 'Normal', '{\"gender\":\"male\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Koli\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Joint\",\"father_occupation\":\"Business\",\"mother_occupation\":\"Homemaker\",\"siblings\":0}', '{\"highest_education\":\"Graduate\",\"college\":\"Sarin-Rana College\",\"field_of_study\":\"Commerce\"}', '{\"occupation\":\"Business Owner\",\"company\":\"Gade PLC\",\"annual_income\":\"20+ Lakhs\"}', '{\"diet\":\"Non-Vegetarian\",\"smoking\":\"No\",\"drinking\":\"No\"}', '{\"city\":\"Bhopal\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:49', '2025-08-23 08:02:49', '2025-08-23 08:02:49', NULL, NULL, 'active'),
(2, 13, 30, '180 cm', '78 kg', 'Dark', 'Normal', '{\"gender\":\"female\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Koli\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Nuclear\",\"father_occupation\":\"Service\",\"mother_occupation\":\"Teacher\",\"siblings\":1}', '{\"highest_education\":\"Post Graduate\",\"college\":\"Oak-Das College\",\"field_of_study\":\"Arts\"}', '{\"occupation\":\"Business Owner\",\"company\":\"Choudhary, Anne and Sachdev\",\"annual_income\":\"8-12 Lakhs\"}', '{\"diet\":\"Non-Vegetarian\",\"smoking\":\"No\",\"drinking\":\"Occasionally\"}', '{\"city\":\"Indore\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:49', '2025-08-23 08:02:49', '2025-08-23 08:02:49', NULL, NULL, 'active'),
(3, 14, 29, '181 cm', '57 kg', 'Fair', 'Normal', '{\"gender\":\"male\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Kunbi\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Nuclear\",\"father_occupation\":\"Service\",\"mother_occupation\":\"Service\",\"siblings\":0}', '{\"highest_education\":\"Professional\",\"college\":\"Bose-Saxena College\",\"field_of_study\":\"Science\"}', '{\"occupation\":\"Teacher\",\"company\":\"Arora, Bhardwaj and Kohli\",\"annual_income\":\"12-20 Lakhs\"}', '{\"diet\":\"Vegetarian\",\"smoking\":\"No\",\"drinking\":\"Occasionally\"}', '{\"city\":\"Kolkata\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:49', '2025-08-23 08:02:49', '2025-08-23 08:02:49', NULL, NULL, 'active'),
(4, 15, 31, '169 cm', '69 kg', 'Dark', 'Normal', '{\"gender\":\"female\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Kunbi\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Joint\",\"father_occupation\":\"Farmer\",\"mother_occupation\":\"Business\",\"siblings\":1}', '{\"highest_education\":\"Graduate\",\"college\":\"Choudhry, Sodhi and Mahajan College\",\"field_of_study\":\"Engineering\"}', '{\"occupation\":\"Government Employee\",\"company\":\"Pai, Ramanathan and Bassi\",\"annual_income\":\"8-12 Lakhs\"}', '{\"diet\":\"Vegetarian\",\"smoking\":\"No\",\"drinking\":\"No\"}', '{\"city\":\"Chennai\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:50', '2025-08-23 08:02:50', '2025-08-23 08:02:50', NULL, NULL, 'active'),
(5, 16, 30, '184 cm', '54 kg', 'Dark', 'Normal', '{\"gender\":\"male\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Koli\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Nuclear\",\"father_occupation\":\"Service\",\"mother_occupation\":\"Homemaker\",\"siblings\":0}', '{\"highest_education\":\"Professional\",\"college\":\"Bahri-Dhar College\",\"field_of_study\":\"Engineering\"}', '{\"occupation\":\"Doctor\",\"company\":\"Binnani-Ganesan\",\"annual_income\":\"5-8 Lakhs\"}', '{\"diet\":\"Vegetarian\",\"smoking\":\"No\",\"drinking\":\"No\"}', '{\"city\":\"Bangalore\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:50', '2025-08-23 08:02:50', '2025-08-23 08:02:50', NULL, NULL, 'active'),
(6, 17, 33, '177 cm', '46 kg', 'Dark', 'Normal', '{\"gender\":\"female\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Kunbi\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Nuclear\",\"father_occupation\":\"Service\",\"mother_occupation\":\"Homemaker\",\"siblings\":0}', '{\"highest_education\":\"Doctorate\",\"college\":\"Natt-Loke College\",\"field_of_study\":\"Medicine\"}', '{\"occupation\":\"Business Owner\",\"company\":\"Jani-More\",\"annual_income\":\"20+ Lakhs\"}', '{\"diet\":\"Non-Vegetarian\",\"smoking\":\"No\",\"drinking\":\"Occasionally\"}', '{\"city\":\"Indore\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:50', '2025-08-23 08:02:50', '2025-08-23 08:02:50', NULL, NULL, 'active'),
(7, 18, 35, '162 cm', '73 kg', 'Dark', 'Normal', '{\"gender\":\"male\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Kunbi\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Nuclear\",\"father_occupation\":\"Business\",\"mother_occupation\":\"Teacher\",\"siblings\":2}', '{\"highest_education\":\"Post Graduate\",\"college\":\"Dasgupta-Sharaf College\",\"field_of_study\":\"Commerce\"}', '{\"occupation\":\"Software Engineer\",\"company\":\"Khalsa-Dora\",\"annual_income\":\"12-20 Lakhs\"}', '{\"diet\":\"Vegetarian\",\"smoking\":\"No\",\"drinking\":\"No\"}', '{\"city\":\"Chennai\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:51', '2025-08-23 08:02:51', '2025-08-23 08:02:51', NULL, NULL, 'active'),
(8, 19, 25, '171 cm', '66 kg', 'Medium', 'Normal', '{\"gender\":\"female\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Kunbi\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Nuclear\",\"father_occupation\":\"Business\",\"mother_occupation\":\"Homemaker\",\"siblings\":3}', '{\"highest_education\":\"Doctorate\",\"college\":\"Dash, Bhat and Pardeshi College\",\"field_of_study\":\"Commerce\"}', '{\"occupation\":\"Business Owner\",\"company\":\"Sangha-Dube\",\"annual_income\":\"3-5 Lakhs\"}', '{\"diet\":\"Vegetarian\",\"smoking\":\"No\",\"drinking\":\"No\"}', '{\"city\":\"Lucknow\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:51', '2025-08-23 08:02:51', '2025-08-23 08:02:51', NULL, NULL, 'active'),
(9, 20, 33, '177 cm', '53 kg', 'Medium', 'Normal', '{\"gender\":\"male\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Koli\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Joint\",\"father_occupation\":\"Retired\",\"mother_occupation\":\"Business\",\"siblings\":3}', '{\"highest_education\":\"Graduate\",\"college\":\"Master-Patla College\",\"field_of_study\":\"Commerce\"}', '{\"occupation\":\"Government Employee\",\"company\":\"Mahabir Group\",\"annual_income\":\"5-8 Lakhs\"}', '{\"diet\":\"Non-Vegetarian\",\"smoking\":\"No\",\"drinking\":\"Occasionally\"}', '{\"city\":\"Visakhapatnam\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:52', '2025-08-23 08:02:52', '2025-08-23 08:02:52', NULL, NULL, 'active'),
(10, 21, 33, '159 cm', '48 kg', 'Fair', 'Normal', '{\"gender\":\"female\",\"marital_status\":\"single\",\"religion\":\"Hindu\",\"caste\":\"Mali\",\"subcaste\":\"Mali Koli\",\"mother_tongue\":\"Marathi\",\"about_me\":\"Looking for a life partner from Mali community with similar values and interests.\"}', '{\"family_type\":\"Joint\",\"father_occupation\":\"Retired\",\"mother_occupation\":\"Business\",\"siblings\":1}', '{\"highest_education\":\"Doctorate\",\"college\":\"Viswanathan-Pai College\",\"field_of_study\":\"Arts\"}', '{\"occupation\":\"Business Owner\",\"company\":\"Dhar-Mammen\",\"annual_income\":\"5-8 Lakhs\"}', '{\"diet\":\"Vegetarian\",\"smoking\":\"No\",\"drinking\":\"No\"}', '{\"city\":\"Nagpur\",\"state\":\"Maharashtra\",\"country\":\"India\"}', '{\"age_range\":\"22-30\",\"height_range\":\"155-175\",\"education\":\"Graduate+\",\"location\":\"Any\",\"caste\":\"Mali\"}', '{\"show_contact\":false,\"show_photos\":true,\"profile_visibility\":\"public\"}', 'approved', NULL, '2026-08-23 08:02:52', '2025-08-23 08:02:52', '2025-08-23 08:02:52', NULL, NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_01_17_000000_remove_verification_status_from_caste_certificates', 1),
(5, '2025_08_15_153055_create_caste_certificates_table', 1),
(6, '2025_08_15_153102_create_business_categories_table', 1),
(7, '2025_08_15_153116_create_businesses_table', 1),
(8, '2025_08_15_153122_create_products_table', 1),
(9, '2025_08_15_153127_create_services_table', 1),
(10, '2025_08_15_153134_create_matrimony_profiles_table', 1),
(11, '2025_08_15_153139_create_connection_requests_table', 1),
(12, '2025_08_15_153146_create_chat_conversations_table', 1),
(13, '2025_08_15_153152_create_chat_messages_table', 1),
(14, '2025_08_15_153200_create_job_postings_table', 1),
(15, '2025_08_15_153210_create_job_applications_table', 1),
(16, '2025_08_15_153220_add_fields_to_job_postings_table', 1),
(17, '2025_08_15_153452_create_personal_access_tokens_table', 1),
(18, '2025_08_15_210652_create_transactions_table', 1),
(19, '2025_08_15_210724_create_system_settings_table', 1),
(20, '2025_08_15_224435_create_payments_table', 1),
(21, '2025_08_16_085423_create_volunteer_profiles_table', 1),
(22, '2025_08_16_090202_add_status_to_users_table', 1),
(23, '2025_08_16_100000_create_business_locations_table', 1),
(24, '2025_08_16_120000_create_business_reviews_table', 1),
(25, '2025_08_17_180000_add_verification_fields_to_businesses_table', 1),
(26, '2025_08_17_181000_add_approval_fields_to_matrimony_profiles_table', 1),
(27, '2025_08_22_181500_create_volunteer_opportunities_table', 1),
(28, '2025_08_22_181538_create_volunteer_applications_table', 1),
(29, '2025_08_22_182846_add_admin_fields_to_volunteer_opportunities_table', 1),
(30, '2025_08_22_183108_create_donation_causes_table', 1),
(31, '2025_08_22_183118_create_donations_table', 1),
(32, '2025_08_22_193854_create_notifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `action_url` varchar(255) DEFAULT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `channel` enum('in_app','email','push','sms') NOT NULL DEFAULT 'in_app',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `email_sent` tinyint(1) NOT NULL DEFAULT 0,
  `email_sent_at` timestamp NULL DEFAULT NULL,
  `push_sent` tinyint(1) NOT NULL DEFAULT 0,
  `push_sent_at` timestamp NULL DEFAULT NULL,
  `related_type` varchar(255) DEFAULT NULL,
  `related_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_type` enum('business_registration','matrimony_subscription','donation','other') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `status` enum('pending','completed','failed','refunded','cancelled') NOT NULL DEFAULT 'pending',
  `payment_method` enum('razorpay','upi','card','netbanking','wallet','other') NOT NULL DEFAULT 'razorpay',
  `razorpay_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`razorpay_response`)),
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `description` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `refund_amount` decimal(10,2) DEFAULT NULL,
  `refunded_at` timestamp NULL DEFAULT NULL,
  `refund_reason` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `payment_id`, `order_id`, `transaction_id`, `payment_type`, `amount`, `currency`, `status`, `payment_method`, `razorpay_response`, `metadata`, `description`, `paid_at`, `refund_amount`, `refunded_at`, `refund_reason`, `receipt_number`, `created_at`, `updated_at`) VALUES
(1, 22, 'pay_erkbdkpfca', 'order_yrbalzmpoo', 'TXN2004185139', 'donation', 2000.00, 'INR', 'refunded', 'netbanking', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Sanjay Agarwal\\\",\\\"refund_reason\\\":\\\"Service not delivered\\\",\\\"refunded_by\\\":1,\\\"notes\\\":\\\"Refund processed as per customer request\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', 1600.00, '2025-08-23 08:02:59', NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:59'),
(2, 4, 'pay_iowfftcftt', 'order_igiiyzjmzb', 'TXN8499342810', 'donation', 1000.00, 'INR', 'refunded', 'upi', NULL, '\"{\\\"cause_id\\\":4,\\\"cause_title\\\":\\\"Emergency Relief Fund\\\",\\\"donor_name\\\":\\\"Sita Yadav\\\",\\\"refund_reason\\\":\\\"Duplicate payment\\\",\\\"refunded_by\\\":1,\\\"notes\\\":\\\"Refund processed as per customer request\\\"}\"', 'Donation for Emergency Relief Fund', '2025-08-23 08:02:58', 800.00, '2025-08-23 08:02:59', NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:59'),
(3, 7, 'pay_oxwafwfzxe', 'order_rpzmlqmjhv', 'TXN1046445550', 'donation', 2000.00, 'INR', 'failed', 'netbanking', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Sanjay Agarwal\\\"}\"', 'Donation for Healthcare Support Initiative', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 25, 'pay_vdnndedhji', 'order_aplisthbee', 'TXN1500837264', 'donation', 10000.00, 'INR', 'refunded', 'card', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Sanjay Agarwal\\\",\\\"refund_reason\\\":\\\"Technical issue\\\",\\\"refunded_by\\\":1,\\\"notes\\\":\\\"Refund processed as per customer request\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', 8000.00, '2025-08-23 08:02:59', NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:59'),
(5, 4, 'pay_roefikgzzy', 'order_wptotqrhth', 'TXN8804112216', 'donation', 500.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"cause_id\\\":1,\\\"cause_title\\\":\\\"Mali Community Education Fund\\\",\\\"donor_name\\\":\\\"Sita Yadav\\\"}\"', 'Donation for Mali Community Education Fund', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(6, 11, 'pay_zhruhoqhqv', 'order_luhguvvnin', 'TXN1455073423', 'donation', 2000.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Ashok Yadav\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(7, 1, 'pay_yvpfikwhuf', 'order_fyphgsllvd', 'TXN0641914378', 'donation', 2000.00, 'INR', 'completed', 'wallet', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Mali Setu Admin\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(8, 15, 'pay_mxhvjejvzq', 'order_msnmnurypq', 'TXN0590820785', 'donation', 1000.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"cause_id\\\":1,\\\"cause_title\\\":\\\"Mali Community Education Fund\\\",\\\"donor_name\\\":\\\"Kavita Singh\\\"}\"', 'Donation for Mali Community Education Fund', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(9, 13, 'pay_vbwcdqnpsd', 'order_maaailherb', 'TXN6505059894', 'donation', 500.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Meera Gupta\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(10, 2, 'pay_njlxhgphmo', 'order_ricntlpnen', 'TXN1736969916', 'donation', 1000.00, 'INR', 'pending', 'card', NULL, '\"{\\\"cause_id\\\":3,\\\"cause_title\\\":\\\"Women Empowerment Program\\\",\\\"donor_name\\\":\\\"Priya Sharma\\\"}\"', 'Donation for Women Empowerment Program', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(11, 15, 'pay_mjmquywwtk', 'order_zgneitmsfa', 'TXN1074156522', 'donation', 10000.00, 'INR', 'pending', 'wallet', NULL, '\"{\\\"cause_id\\\":1,\\\"cause_title\\\":\\\"Mali Community Education Fund\\\",\\\"donor_name\\\":\\\"Kavita Singh\\\"}\"', 'Donation for Mali Community Education Fund', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(12, 14, 'pay_rprhwyoset', 'order_vldqqntecq', 'TXN3234514732', 'donation', 5000.00, 'INR', 'completed', 'wallet', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Ramesh Tiwari\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(13, 22, 'pay_kogmatwyky', 'order_cwegwixyrb', 'TXN3602645985', 'donation', 2000.00, 'INR', 'completed', 'wallet', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Sanjay Agarwal\\\"}\"', 'Donation for Healthcare Support Initiative', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(14, 7, 'pay_vwsshdhmkp', 'order_qgneviyqlc', 'TXN9387775965', 'donation', 2000.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"cause_id\\\":3,\\\"cause_title\\\":\\\"Women Empowerment Program\\\",\\\"donor_name\\\":\\\"Sanjay Agarwal\\\"}\"', 'Donation for Women Empowerment Program', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(15, 4, 'pay_sjxugfkiwn', 'order_cbazxzeyqm', 'TXN2026434670', 'donation', 2000.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"cause_id\\\":3,\\\"cause_title\\\":\\\"Women Empowerment Program\\\",\\\"donor_name\\\":\\\"Sita Yadav\\\"}\"', 'Donation for Women Empowerment Program', '2025-08-23 08:02:58', NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(16, 1, 'pay_xowkzaojvq', 'order_etzppvrnwm', 'TXN9830622343', 'donation', 1000.00, 'INR', 'pending', 'netbanking', NULL, '\"{\\\"cause_id\\\":4,\\\"cause_title\\\":\\\"Emergency Relief Fund\\\",\\\"donor_name\\\":\\\"Mali Setu Admin\\\"}\"', 'Donation for Emergency Relief Fund', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(17, 23, 'pay_tbmhtyxvop', 'order_jpfovcdtgt', 'TXN3364102737', 'donation', 10000.00, 'INR', 'failed', 'netbanking', NULL, '\"{\\\"cause_id\\\":1,\\\"cause_title\\\":\\\"Mali Community Education Fund\\\",\\\"donor_name\\\":\\\"Ashok Yadav\\\"}\"', 'Donation for Mali Community Education Fund', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(18, 9, 'pay_jztvlahjhy', 'order_yhavsqufns', 'TXN6938623208', 'donation', 500.00, 'INR', 'failed', 'wallet', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Vikram Singh\\\"}\"', 'Donation for Healthcare Support Initiative', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(19, 8, 'pay_ilecawhuum', 'order_lkphbvhhdn', 'TXN1183678193', 'donation', 10000.00, 'INR', 'pending', 'wallet', NULL, '\"{\\\"cause_id\\\":2,\\\"cause_title\\\":\\\"Healthcare Support Initiative\\\",\\\"donor_name\\\":\\\"Vikram Singh\\\"}\"', 'Donation for Healthcare Support Initiative', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(20, 7, 'pay_ywamxxyfah', 'order_wcewsvtlgm', 'TXN0424687550', 'donation', 2000.00, 'INR', 'failed', 'netbanking', NULL, '\"{\\\"cause_id\\\":1,\\\"cause_title\\\":\\\"Mali Community Education Fund\\\",\\\"donor_name\\\":\\\"Sanjay Agarwal\\\"}\"', 'Donation for Mali Community Education Fund', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(21, 22, 'pay_hiuywglgvn', 'order_tppajnpycs', 'VER9291284640', 'business_registration', 2500.00, 'INR', 'pending', 'card', NULL, '\"{\\\"business_id\\\":1,\\\"business_name\\\":\\\"Mali Fresh Vegetables\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Mali Fresh Vegetables', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(22, 23, 'pay_mnzotqnrdu', 'order_ezglmuuuml', 'VER9215634990', 'business_registration', 2500.00, 'INR', 'completed', 'card', NULL, '\"{\\\"business_id\\\":2,\\\"business_name\\\":\\\"Sai Catering Services\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Sai Catering Services', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(23, 24, 'pay_eskgxpowyf', 'order_akbnlwegjc', 'VER1425720204', 'business_registration', 2500.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"business_id\\\":3,\\\"business_name\\\":\\\"Ganesh General Store\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Ganesh General Store', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(24, 25, 'pay_jzvvrpanrt', 'order_xrbbqfykjw', 'VER5342612334', 'business_registration', 2500.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"business_id\\\":4,\\\"business_name\\\":\\\"Shivaji Transport Services\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Shivaji Transport Services', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(25, 26, 'pay_auhdnwurup', 'order_rhaqsfyolb', 'VER3683920807', 'business_registration', 2500.00, 'INR', 'pending', 'netbanking', NULL, '\"{\\\"business_id\\\":5,\\\"business_name\\\":\\\"Laxmi Textiles\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Laxmi Textiles', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(26, 27, 'pay_kexzjtlrob', 'order_nyokrdhfzn', 'VER9450677202', 'business_registration', 2500.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"business_id\\\":6,\\\"business_name\\\":\\\"Marathi Mandal Event Management\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Marathi Mandal Event Management', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(27, 28, 'pay_yosupoaqji', 'order_medqglqjxc', 'VER6053771756', 'business_registration', 2500.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"business_id\\\":7,\\\"business_name\\\":\\\"Krishna Dairy Products\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Krishna Dairy Products', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(28, 35, 'pay_kaxoxqcxlz', 'order_moxqvwkiab', 'VER6618304209', 'business_registration', 2500.00, 'INR', 'pending', 'netbanking', NULL, '\"{\\\"business_id\\\":8,\\\"business_name\\\":\\\"Bharat Electronics Repair\\\",\\\"verification_type\\\":\\\"standard\\\"}\"', 'Business verification fee for Bharat Electronics Repair', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(29, 22, 'pay_grgerkpwvj', 'order_nxrvxsybpt', 'SUB8882477593', 'other', 2499.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"business_id\\\":1,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"15 job postings\\\",\\\"Advanced analytics\\\",\\\"Priority support\\\",\\\"Featured listing\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Premium subscription for Mali Fresh Vegetables', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(30, 23, 'pay_lgvlwcdklk', 'order_tqdntepuqn', 'SUB0355167180', 'other', 2499.00, 'INR', 'completed', 'card', NULL, '\"{\\\"business_id\\\":2,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"15 job postings\\\",\\\"Advanced analytics\\\",\\\"Priority support\\\",\\\"Featured listing\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Premium subscription for Sai Catering Services', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(31, 24, 'pay_kloazaabxd', 'order_eqzexcnssf', 'SUB1072892909', 'other', 2499.00, 'INR', 'pending', 'netbanking', NULL, '\"{\\\"business_id\\\":3,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"15 job postings\\\",\\\"Advanced analytics\\\",\\\"Priority support\\\",\\\"Featured listing\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Premium subscription for Ganesh General Store', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(32, 25, 'pay_picjdorasv', 'order_slhihnspxh', 'SUB5724857539', 'other', 2499.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"business_id\\\":4,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"15 job postings\\\",\\\"Advanced analytics\\\",\\\"Priority support\\\",\\\"Featured listing\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Premium subscription for Shivaji Transport Services', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(33, 26, 'pay_fwjwkhwbyi', 'order_mnhlgfwozs', 'SUB9188779116', 'other', 4999.00, 'INR', 'completed', 'card', NULL, '\"{\\\"business_id\\\":5,\\\"plan_type\\\":\\\"enterprise\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"Unlimited job postings\\\",\\\"Premium analytics\\\",\\\"24\\\\\\/7 support\\\",\\\"Featured listing\\\",\\\"Custom branding\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Enterprise subscription for Laxmi Textiles', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(34, 27, 'pay_bhmijwpwho', 'order_kkazfdjhok', 'SUB4696129159', 'other', 999.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"business_id\\\":6,\\\"plan_type\\\":\\\"basic\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"5 job postings\\\",\\\"Basic analytics\\\",\\\"Email support\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Basic subscription for Marathi Mandal Event Management', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(35, 28, 'pay_ztplgnehgs', 'order_vmefwufzvl', 'SUB5567231306', 'other', 4999.00, 'INR', 'pending', 'card', NULL, '\"{\\\"business_id\\\":7,\\\"plan_type\\\":\\\"enterprise\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"Unlimited job postings\\\",\\\"Premium analytics\\\",\\\"24\\\\\\/7 support\\\",\\\"Featured listing\\\",\\\"Custom branding\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Enterprise subscription for Krishna Dairy Products', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(36, 35, 'pay_uvwsukfecf', 'order_lntueaiuvd', 'SUB2950499416', 'other', 4999.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"business_id\\\":8,\\\"plan_type\\\":\\\"enterprise\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"Unlimited job postings\\\",\\\"Premium analytics\\\",\\\"24\\\\\\/7 support\\\",\\\"Featured listing\\\",\\\"Custom branding\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Enterprise subscription for Bharat Electronics Repair', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(37, 22, 'pay_bzelmjalpa', 'order_gwbpbbcprg', 'SUB9564586448', 'other', 999.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"business_id\\\":9,\\\"plan_type\\\":\\\"basic\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"5 job postings\\\",\\\"Basic analytics\\\",\\\"Email support\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Basic subscription for Pune Agro Solutions', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(38, 23, 'pay_hklyqrcqhs', 'order_vyfxeohwds', 'SUB8689973514', 'other', 999.00, 'INR', 'completed', 'card', NULL, '\"{\\\"business_id\\\":10,\\\"plan_type\\\":\\\"basic\\\",\\\"duration\\\":\\\"1 year\\\",\\\"features\\\":[\\\"5 job postings\\\",\\\"Basic analytics\\\",\\\"Email support\\\"],\\\"purpose\\\":\\\"business_subscription\\\"}\"', 'Basic subscription for Maharaja Sweets & Snacks', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(39, 12, 'pay_ihpdqcxxcl', 'order_vqwmvmpbte', 'MAT1936276131', 'matrimony_subscription', 6999.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"profile_id\\\":1,\\\"plan_type\\\":\\\"platinum\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"Unlimited profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\",\\\"Priority matching\\\",\\\"Dedicated support\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Platinum matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(40, 13, 'pay_ptketvywls', 'order_rpciryjpet', 'MAT8207822541', 'matrimony_subscription', 1999.00, 'INR', 'completed', 'netbanking', NULL, '\"{\\\"profile_id\\\":2,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"50 profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Premium matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(41, 14, 'pay_jjdhpurwvb', 'order_hmwuuzgzlt', 'MAT5590390420', 'matrimony_subscription', 6999.00, 'INR', 'pending', 'upi', NULL, '\"{\\\"profile_id\\\":3,\\\"plan_type\\\":\\\"platinum\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"Unlimited profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\",\\\"Priority matching\\\",\\\"Dedicated support\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Platinum matrimony membership', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(42, 15, 'pay_jgrpbmebis', 'order_cdtbvzqnvy', 'MAT9367715299', 'matrimony_subscription', 6999.00, 'INR', 'completed', 'card', NULL, '\"{\\\"profile_id\\\":4,\\\"plan_type\\\":\\\"platinum\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"Unlimited profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\",\\\"Priority matching\\\",\\\"Dedicated support\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Platinum matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(43, 16, 'pay_giskkosgxw', 'order_qegcpfdtda', 'MAT0941974505', 'matrimony_subscription', 3999.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"profile_id\\\":5,\\\"plan_type\\\":\\\"gold\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"100 profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\",\\\"Priority matching\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Gold matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(44, 17, 'pay_hiikgeeaea', 'order_okmfifsbks', 'MAT5743792095', 'matrimony_subscription', 1999.00, 'INR', 'pending', 'card', NULL, '\"{\\\"profile_id\\\":6,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"50 profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Premium matrimony membership', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(45, 18, 'pay_wmzrqfcenb', 'order_xrlwveshai', 'MAT7725119068', 'matrimony_subscription', 1999.00, 'INR', 'pending', 'upi', NULL, '\"{\\\"profile_id\\\":7,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"50 profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Premium matrimony membership', NULL, NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(46, 19, 'pay_lhqlkcdwhr', 'order_jppbwcfgyc', 'MAT2238618600', 'matrimony_subscription', 6999.00, 'INR', 'completed', 'card', NULL, '\"{\\\"profile_id\\\":8,\\\"plan_type\\\":\\\"platinum\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"Unlimited profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\",\\\"Priority matching\\\",\\\"Dedicated support\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Platinum matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(47, 20, 'pay_zuxcklsrjt', 'order_gtenozkqjt', 'MAT2146640099', 'matrimony_subscription', 3999.00, 'INR', 'completed', 'upi', NULL, '\"{\\\"profile_id\\\":9,\\\"plan_type\\\":\\\"gold\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"100 profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\",\\\"Priority matching\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Gold matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59'),
(48, 21, 'pay_plcmqysxob', 'order_yhdpxietjv', 'MAT2346478354', 'matrimony_subscription', 1999.00, 'INR', 'completed', 'card', NULL, '\"{\\\"profile_id\\\":10,\\\"plan_type\\\":\\\"premium\\\",\\\"duration\\\":\\\"6 months\\\",\\\"features\\\":[\\\"50 profile views\\\",\\\"Direct messaging\\\",\\\"Profile highlighting\\\"],\\\"purpose\\\":\\\"matrimony_premium\\\"}\"', 'Premium matrimony membership', '2025-08-23 08:02:59', NULL, NULL, NULL, NULL, '2025-08-23 08:02:59', '2025-08-23 08:02:59');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `business_id`, `name`, `description`, `cost`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Tomatoes', 'High quality tomatoes available at competitive prices.', 4477.22, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(2, 1, 'Onions', 'High quality onions available at competitive prices.', 1363.45, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(3, 1, 'Potatoes', 'High quality potatoes available at competitive prices.', 2597.53, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 1, 'Leafy Greens', 'High quality leafy greens available at competitive prices.', 3102.96, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(5, 1, 'Seasonal Vegetables', 'High quality seasonal vegetables available at competitive prices.', 1853.62, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(6, 2, 'Wedding Packages', 'High quality wedding packages available at competitive prices.', 4740.14, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(7, 2, 'Corporate Lunch', 'High quality corporate lunch available at competitive prices.', 1998.55, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(8, 2, 'Traditional Thali', 'High quality traditional thali available at competitive prices.', 4331.29, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(9, 2, 'Sweets & Snacks', 'High quality sweets & snacks available at competitive prices.', 3832.88, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(10, 3, 'Groceries', 'High quality groceries available at competitive prices.', 1129.22, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(11, 3, 'Household Items', 'High quality household items available at competitive prices.', 4841.44, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(12, 3, 'Personal Care', 'High quality personal care available at competitive prices.', 3741.02, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(13, 3, 'Stationery', 'High quality stationery available at competitive prices.', 227.70, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(14, 4, 'Truck Rental', 'High quality truck rental available at competitive prices.', 409.75, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(15, 4, 'Tempo Services', 'High quality tempo services available at competitive prices.', 1095.89, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(16, 4, 'Passenger Transport', 'High quality passenger transport available at competitive prices.', 303.13, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(17, 5, 'Cotton Fabrics', 'High quality cotton fabrics available at competitive prices.', 1652.58, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(18, 5, 'Silk Sarees', 'High quality silk sarees available at competitive prices.', 337.90, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(19, 5, 'Dress Materials', 'High quality dress materials available at competitive prices.', 4607.86, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(20, 5, 'Home Textiles', 'High quality home textiles available at competitive prices.', 4017.33, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(21, 6, 'Decoration Packages', 'High quality decoration packages available at competitive prices.', 372.57, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(22, 6, 'Sound Systems', 'High quality sound systems available at competitive prices.', 3043.72, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(23, 6, 'Photography Services', 'High quality photography services available at competitive prices.', 3868.26, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(24, 7, 'Fresh Milk', 'High quality fresh milk available at competitive prices.', 4782.53, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(25, 7, 'Paneer', 'High quality paneer available at competitive prices.', 2566.92, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(26, 7, 'Ghee', 'High quality ghee available at competitive prices.', 2223.43, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(27, 7, 'Yogurt', 'High quality yogurt available at competitive prices.', 2788.15, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(28, 7, 'Butter', 'High quality butter available at competitive prices.', 1735.54, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(29, 8, 'Spare Parts', 'High quality spare parts available at competitive prices.', 4272.46, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(30, 8, 'Refurbished Electronics', 'High quality refurbished electronics available at competitive prices.', 2133.88, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(31, 9, 'Seeds', 'High quality seeds available at competitive prices.', 730.25, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(32, 9, 'Fertilizers', 'High quality fertilizers available at competitive prices.', 662.09, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(33, 9, 'Pesticides', 'High quality pesticides available at competitive prices.', 2871.85, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(34, 9, 'Farm Tools', 'High quality farm tools available at competitive prices.', 4039.63, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(35, 10, 'Modak', 'High quality modak available at competitive prices.', 3084.41, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(36, 10, 'Puran Poli', 'High quality puran poli available at competitive prices.', 2865.86, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(37, 10, 'Chakli', 'High quality chakli available at competitive prices.', 4175.50, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(38, 10, 'Laddu', 'High quality laddu available at competitive prices.', 4116.90, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(39, 10, 'Namkeen', 'High quality namkeen available at competitive prices.', 3246.37, 'products/default-product.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `cost` decimal(10,2) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `business_id`, `name`, `description`, `cost`, `image_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Home Delivery', 'Professional home delivery service with experienced team.', 6863.54, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(2, 1, 'Bulk Supply', 'Professional bulk supply service with experienced team.', 1933.78, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(3, 1, 'Organic Certification', 'Professional organic certification service with experienced team.', 3786.65, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(4, 2, 'Event Catering', 'Professional event catering service with experienced team.', 7135.59, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(5, 2, 'Home Delivery', 'Professional home delivery service with experienced team.', 6802.64, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(6, 2, 'Custom Menu Planning', 'Professional custom menu planning service with experienced team.', 6388.33, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(7, 3, 'Home Delivery', 'Professional home delivery service with experienced team.', 5840.40, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(8, 3, 'Credit Facility', 'Professional credit facility service with experienced team.', 1156.30, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(9, 3, 'Online Ordering', 'Professional online ordering service with experienced team.', 7220.76, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(10, 4, 'Goods Transportation', 'Professional goods transportation service with experienced team.', 6893.37, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(11, 4, 'Packers & Movers', 'Professional packers & movers service with experienced team.', 1787.12, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(12, 4, 'Logistics Solutions', 'Professional logistics solutions service with experienced team.', 1220.75, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(13, 5, 'Custom Tailoring', 'Professional custom tailoring service with experienced team.', 8491.31, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(14, 5, 'Bulk Orders', 'Professional bulk orders service with experienced team.', 6963.31, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(15, 5, 'Design Consultation', 'Professional design consultation service with experienced team.', 7538.80, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(16, 6, 'Event Planning', 'Professional event planning service with experienced team.', 2777.63, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(17, 6, 'Venue Booking', 'Professional venue booking service with experienced team.', 9377.73, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(18, 6, 'Artist Management', 'Professional artist management service with experienced team.', 8001.58, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(19, 7, 'Daily Delivery', 'Professional daily delivery service with experienced team.', 8180.63, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(20, 7, 'Subscription Plans', 'Professional subscription plans service with experienced team.', 9648.42, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(21, 7, 'Bulk Supply', 'Professional bulk supply service with experienced team.', 9911.60, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(22, 8, 'TV Repair', 'Professional tv repair service with experienced team.', 3825.68, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(23, 8, 'Mobile Repair', 'Professional mobile repair service with experienced team.', 4041.45, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(24, 8, 'Home Appliance Service', 'Professional home appliance service service with experienced team.', 9910.14, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(25, 8, 'Warranty Service', 'Professional warranty service service with experienced team.', 3607.06, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(26, 9, 'Soil Testing', 'Professional soil testing service with experienced team.', 924.34, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(27, 9, 'Crop Consultation', 'Professional crop consultation service with experienced team.', 4509.90, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(28, 9, 'Equipment Rental', 'Professional equipment rental service with experienced team.', 3637.77, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(29, 10, 'Custom Orders', 'Professional custom orders service with experienced team.', 2449.01, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(30, 10, 'Festival Specials', 'Professional festival specials service with experienced team.', 3866.32, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58'),
(31, 10, 'Bulk Supply', 'Professional bulk supply service with experienced team.', 4570.37, 'services/default-service.jpg', 'active', '2025-08-23 08:02:58', '2025-08-23 08:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('igFAenwpTaezr1AKPdpyP74e02hzXj8BzruPnP1m', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibkc0U0JuUENPS1p5M1E4cEw2UUNRbU9UQXE2Z3VKR01hRnB6RFhUdCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1756894884);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key_name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `purpose` enum('business_registration','matrimony_profile','donation') NOT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `razorpay_order_id` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
  `subscription_period` int(11) DEFAULT NULL,
  `receipt_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('general','individual','business','matrimony','volunteer') NOT NULL,
  `caste_verification_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `status` enum('active','suspended','banned') NOT NULL DEFAULT 'active',
  `admin_notes` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `email_verified_at`, `password`, `user_type`, `caste_verification_status`, `status`, `admin_notes`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Mali Setu Admin', 'admin@malisetu.com', '+91-9876543210', '2025-08-23 08:02:44', '$2y$12$iO4PLiIL4rqN5dHuNzgf3Oo75m5bnRfs0.NDKihfbl0Pg3hq5vy4S', 'general', 'approved', 'active', 'System Administrator Account - Has admin privileges', NULL, '2025-08-23 08:02:44', '2025-08-23 08:02:44'),
(2, 'Priya Sharma', 'priya.sharma0@gmail.com', '+91-4324257205', '2025-08-23 08:02:45', '$2y$12$h8b68NaLf0D1HwQ7RTnkw.BNnetrFlsq9R4u./wPC4/5PfnByMqHK', 'general', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:45', '2025-08-23 08:02:45'),
(3, 'Sunita Patel', 'sunita.patel1@gmail.com', '+91-8824284492', '2025-08-23 08:02:45', '$2y$12$gvdSxXcok5P/X8hkKDVxA.FaTsYC7dtuT3stZf642yLmSiXBSVeR2', 'general', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:45', '2025-08-23 08:02:45'),
(4, 'Sita Yadav', 'sita.yadav2@gmail.com', '+91-3925202645', '2025-08-23 08:02:46', '$2y$12$f00idSBx8Ean70uuF0Tnr.sizlTbiZcxMTbEBZzjyhoQJOL4l/gcm', 'general', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:46', '2025-08-23 08:02:46'),
(5, 'Deepak Joshi', 'deepak.joshi3@gmail.com', '+91-1394904581', '2025-08-23 08:02:46', '$2y$12$U5PMPWiqlsjgYgtevroOUegOwfXWWXT6S3B3nVHw8pti3hDozq9sK', 'general', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:46', '2025-08-23 08:02:46'),
(6, 'Kavita Singh', 'kavita.singh4@gmail.com', '+91-4607830157', '2025-08-23 08:02:47', '$2y$12$G2VbPV9hp0re.Lt5/YeTceYLlVbrahYh6au2lyZX4V44xCy8rPPey', 'general', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:47', '2025-08-23 08:02:47'),
(7, 'Sanjay Agarwal', 'volunteer1@malisetu.com', '+91-3642662242', '2025-08-23 08:02:47', '$2y$12$L5hiQhU1UVNFvzXo/KZ.NuQjFPEDi4kcV17GfMm1fLiDaEPgyp3Ay', 'volunteer', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:47', '2025-08-23 08:02:47'),
(8, 'Vikram Singh', 'volunteer2@malisetu.com', '+91-1480972059', '2025-08-23 08:02:47', '$2y$12$4CvY8xDGL8A5Icy2GxrzkOifM.9JvVHbkqOdH.Bih57jE1sL9Chqe', 'volunteer', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:47', '2025-08-23 08:02:47'),
(9, 'Vikram Singh', 'volunteer3@malisetu.com', '+91-4745908012', '2025-08-23 08:02:48', '$2y$12$y5xqyVIW2Mj63ap8MRAHROK/MrieuE6brqOul/BaMlmoXlV97TdOC', 'volunteer', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:48', '2025-08-23 08:02:48'),
(10, 'Ravi Gupta', 'volunteer4@malisetu.com', '+91-3889496134', '2025-08-23 08:02:48', '$2y$12$vOnMq/9DCMVDZzsHJZHb/uSsfja8BdJ93G8r33fq1vOdrcgsrxr92', 'volunteer', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:48', '2025-08-23 08:02:48'),
(11, 'Ashok Yadav', 'volunteer5@malisetu.com', '+91-1410041683', '2025-08-23 08:02:48', '$2y$12$Bu5Bo4CPNthhE1e4FnKYp.jfvuIRECVXKk6qAEakOFfManO/paH7a', 'volunteer', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:48', '2025-08-23 08:02:48'),
(12, 'Rajesh Kumar', 'matrimony1@malisetu.com', '+91-3275266249', '2025-08-23 08:02:49', '$2y$12$KdFke4eD3GYs.wIVMwm.h.T9Tr8YCKWSwKsNJ6sBIxyMLPJ4pMCmO', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:49', '2025-08-23 08:02:49'),
(13, 'Meera Gupta', 'matrimony2@malisetu.com', '+91-1351104325', '2025-08-23 08:02:49', '$2y$12$IIeDeXWXNJnv9FEp1H8AwutGoqyYFnByILAXWTEmjNOuBPMf1/hQ2', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:49', '2025-08-23 08:02:49'),
(14, 'Ramesh Tiwari', 'matrimony3@malisetu.com', '+91-6275597884', '2025-08-23 08:02:49', '$2y$12$O2qewQKR7MYOTKvkyh7DFOgrHe60dBR7FT7sT31PP8uf36P6tMnPG', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:49', '2025-08-23 08:02:49'),
(15, 'Kavita Singh', 'matrimony4@malisetu.com', '+91-9803435686', '2025-08-23 08:02:50', '$2y$12$idIB/rmYsplLxg3J7i4Az.0WS23ZCg9F9rmxhkx.YnSnZGB8XsAaS', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:50', '2025-08-23 08:02:50'),
(16, 'Ashok Yadav', 'matrimony5@malisetu.com', '+91-8759412840', '2025-08-23 08:02:50', '$2y$12$0Ix8Q2YeEm7i7myHRz4V1OwLxUPzlzO0VKZhJA8kaf0adEscwb.KK', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:50', '2025-08-23 08:02:50'),
(17, 'Anjali Verma', 'matrimony6@malisetu.com', '+91-7900227158', '2025-08-23 08:02:50', '$2y$12$UoDP2RfhO4qByj8hfHqideN99G2ll4Se2l.7dMqgvb1eSReLhVgDK', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:50', '2025-08-23 08:02:50'),
(18, 'Vikram Singh', 'matrimony7@malisetu.com', '+91-7272250100', '2025-08-23 08:02:51', '$2y$12$JYCdras2obxE/JzungGADuLYHhm6uMxpbORRSRNQmCf5QyhtErUga', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:51', '2025-08-23 08:02:51'),
(19, 'Priya Sharma', 'matrimony8@malisetu.com', '+91-9937888145', '2025-08-23 08:02:51', '$2y$12$fRpa60BRbMEmuhcuOs6vkerfYEfFMhFqa85PZMh30BQKsudfokM5K', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:51', '2025-08-23 08:02:51'),
(20, 'Suresh Patel', 'matrimony9@malisetu.com', '+91-3025018554', '2025-08-23 08:02:52', '$2y$12$E0pY.21LmSGFphISgxlSYu1V2B3xm8tE39HK3c661cNkckVH00Eba', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:52', '2025-08-23 08:02:52'),
(21, 'Pooja Joshi', 'matrimony10@malisetu.com', '+91-4535734519', '2025-08-23 08:02:52', '$2y$12$ALzubmov.OoY0Zor9f6R2u9qsBC9nuL48y8zqTam71Je0eBt/KVim', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:52', '2025-08-23 08:02:52'),
(22, 'Sanjay Agarwal', 'business1@malisetu.com', '+91-0957488755', '2025-08-23 08:02:53', '$2y$12$qt474yASIEhiUERoikyRqOm4vu7rYGX.Zs7bpcKERUuvMjtUDihrO', 'business', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:53', '2025-08-23 08:02:53'),
(23, 'Ashok Yadav', 'business2@malisetu.com', '+91-1270595731', '2025-08-23 08:02:53', '$2y$12$UsMseepl17hxHSqCbIRhb.tUUos4WXeLSuyyX6KQcqfD6ZEZCR2eC', 'business', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:53', '2025-08-23 08:02:53'),
(24, 'Amit Sharma', 'business3@malisetu.com', '+91-9276311873', '2025-08-23 08:02:53', '$2y$12$ZhYNG1WGFlVMMcul/ReYEOOGD6GaYZ/.rU/646N/p5kOBWWOmD/A6', 'business', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:53', '2025-08-23 08:02:53'),
(25, 'Sanjay Agarwal', 'business4@malisetu.com', '+91-4959516841', '2025-08-23 08:02:54', '$2y$12$Q4vwUq4uRGUBgiEK2hWhOuwuJOtD83w8fS08T.9XgipeGG5P3VsFe', 'business', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:54', '2025-08-23 08:02:54'),
(26, 'Ramesh Tiwari', 'business5@malisetu.com', '+91-6394941096', '2025-08-23 08:02:54', '$2y$12$mqkDhBDp8DZQcg4EfCE/HOF/yVyc/VWaaEuRUmCSgAgvoAcjbsbeq', 'business', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:54', '2025-08-23 08:02:54'),
(27, 'Suresh Patel', 'unverified1@example.com', '+91-8542021988', NULL, '$2y$12$VcLLdaktRhc2h6HpxYAyd.jQu26qwVTflQrRFFtTGi762wB.gZd3i', 'business', 'pending', 'active', NULL, NULL, '2025-08-23 08:02:54', '2025-08-23 08:02:54'),
(28, 'Ravi Gupta', 'unverified2@example.com', '+91-4579331110', NULL, '$2y$12$ReIiRGDUcm66wAEAQTuq2OwJvGqpTTWffgPEz9udoXPQWUS2UL.QK', 'business', 'pending', 'active', NULL, NULL, '2025-08-23 08:02:55', '2025-08-23 08:02:55'),
(29, 'Rajesh Kumar', 'unverified3@example.com', '+91-7653159667', NULL, '$2y$12$0zVX5wOZttVE9icpvy2.LeAm./NcRyiWpWSiTAnFJXWyU2Oxvd7zW', 'matrimony', 'pending', 'active', NULL, NULL, '2025-08-23 08:02:55', '2025-08-23 08:02:55'),
(30, 'Pooja Joshi', 'unverified4@example.com', '+91-5623747553', NULL, '$2y$12$EC5e2q9PFB55F9RO7Wi5QuNdPlYqYQb6jOvSyfHCuZWxFNilTXy/.', 'volunteer', 'pending', 'active', NULL, NULL, '2025-08-23 08:02:55', '2025-08-23 08:02:55'),
(31, 'Pooja Joshi', 'unverified5@example.com', '+91-4999655903', NULL, '$2y$12$IPRizkiRafy8VBI6zhUR3edWIndZAqVbpXksjFeGfBzR58bMM39xG', 'matrimony', 'pending', 'active', NULL, NULL, '2025-08-23 08:02:55', '2025-08-23 08:02:55'),
(32, 'Manoj Verma', 'unverified6@example.com', '+91-1125530286', NULL, '$2y$12$l9zowoadIJw.xk9dnKF8h.6ak96mHQSW2rpOVIIlipPUbmzso0c2O', 'volunteer', 'rejected', 'active', NULL, NULL, '2025-08-23 08:02:56', '2025-09-03 04:42:53'),
(33, 'Sita Yadav', 'unverified7@example.com', '+91-2431012113', NULL, '$2y$12$qIzK7VVNyjhtxNzhv2/HTuryvZY.n2CohaB26HKmdTuwDCNpSMA62', 'matrimony', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:56', '2025-09-03 04:42:57'),
(34, 'Vikram Singh', 'unverified8@example.com', '+91-8563910972', NULL, '$2y$12$dfP.xEEFE5VY24.inVtdK.ANMx6.Eg83D4jkwI8unWxiVECTbwdFK', 'matrimony', 'pending', 'active', NULL, NULL, '2025-08-23 08:02:56', '2025-08-23 08:02:56'),
(35, 'Priya Sharma', 'unverified9@example.com', '+91-2912521890', NULL, '$2y$12$qoRiscs6YwwhC0nVTodS8.rq1yIb9vYeubNk2IdXn1zgWA6SoxWJq', 'business', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:57', '2025-08-23 10:42:05'),
(36, 'Radha Kumar', 'unverified10@example.com', '+91-1175310554', NULL, '$2y$12$fT5q622lYY5T//Z5/15bZeycISlqqbw9JYlM2QCsrDEjLbIEqGLJO', 'volunteer', 'approved', 'active', NULL, NULL, '2025-08-23 08:02:57', '2025-09-03 04:42:46');

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_applications`
--

CREATE TABLE `volunteer_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `volunteer_profile_id` bigint(20) UNSIGNED NOT NULL,
  `volunteer_opportunity_id` bigint(20) UNSIGNED NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','withdrawn') NOT NULL DEFAULT 'pending',
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_opportunities`
--

CREATE TABLE `volunteer_opportunities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `organization` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `required_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_skills`)),
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `volunteers_needed` int(11) NOT NULL,
  `volunteers_registered` int(11) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','completed','cancelled') NOT NULL DEFAULT 'active',
  `contact_person` varchar(255) NOT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(255) DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `time_commitment` varchar(255) DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_profiles`
--

CREATE TABLE `volunteer_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `availability` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `interests` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`interests`)),
  `status` enum('active','inactive','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `volunteer_profiles`
--

INSERT INTO `volunteer_profiles` (`id`, `user_id`, `skills`, `experience`, `availability`, `location`, `bio`, `interests`, `status`, `created_at`, `updated_at`) VALUES
(1, 7, 'Community Service, Event Management, Teaching, Healthcare Support', '10 years', 'weekends', 'Mumbai', 'Dedicated volunteer committed to serving the Mali community.', '[\"Community Development\",\"Education\",\"Healthcare\"]', 'active', '2025-08-23 08:02:47', '2025-08-23 08:02:47'),
(2, 8, 'Community Service, Event Management, Teaching, Healthcare Support', '10 years', 'weekends', 'Bangalore', 'Dedicated volunteer committed to serving the Mali community.', '[\"Community Development\",\"Education\",\"Healthcare\"]', 'active', '2025-08-23 08:02:47', '2025-08-23 08:02:47'),
(3, 9, 'Community Service, Event Management, Teaching, Healthcare Support', '4 years', 'weekends', 'Mumbai', 'Dedicated volunteer committed to serving the Mali community.', '[\"Community Development\",\"Education\",\"Healthcare\"]', 'active', '2025-08-23 08:02:48', '2025-08-23 08:02:48'),
(4, 10, 'Community Service, Event Management, Teaching, Healthcare Support', '5 years', 'weekends', 'Bhopal', 'Dedicated volunteer committed to serving the Mali community.', '[\"Community Development\",\"Education\",\"Healthcare\"]', 'active', '2025-08-23 08:02:48', '2025-08-23 08:02:48'),
(5, 11, 'Community Service, Event Management, Teaching, Healthcare Support', '2 years', 'weekends', 'Bangalore', 'Dedicated volunteer committed to serving the Mali community.', '[\"Community Development\",\"Education\",\"Healthcare\"]', 'active', '2025-08-23 08:02:48', '2025-08-23 08:02:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `businesses`
--
ALTER TABLE `businesses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `businesses_user_id_index` (`user_id`),
  ADD KEY `businesses_category_id_index` (`category_id`),
  ADD KEY `businesses_verification_status_index` (`verification_status`),
  ADD KEY `businesses_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `business_categories`
--
ALTER TABLE `business_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_categories_is_active_index` (`is_active`);

--
-- Indexes for table `business_locations`
--
ALTER TABLE `business_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `business_locations_business_id_is_active_index` (`business_id`,`is_active`),
  ADD KEY `business_locations_city_state_index` (`city`,`state`),
  ADD KEY `business_locations_latitude_longitude_index` (`latitude`,`longitude`);

--
-- Indexes for table `business_reviews`
--
ALTER TABLE `business_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `business_reviews_business_id_user_id_unique` (`business_id`,`user_id`),
  ADD KEY `business_reviews_moderated_by_foreign` (`moderated_by`),
  ADD KEY `business_reviews_business_id_status_index` (`business_id`,`status`),
  ADD KEY `business_reviews_user_id_index` (`user_id`),
  ADD KEY `business_reviews_rating_index` (`rating`),
  ADD KEY `business_reviews_created_at_index` (`created_at`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `caste_certificates`
--
ALTER TABLE `caste_certificates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caste_certificates_verified_by_foreign` (`verified_by`),
  ADD KEY `caste_certificates_user_id_index` (`user_id`),
  ADD KEY `caste_certificates_verification_status_index` (`verification_status`);

--
-- Indexes for table `chat_conversations`
--
ALTER TABLE `chat_conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chat_conversations_user1_id_user2_id_unique` (`user1_id`,`user2_id`),
  ADD KEY `chat_conversations_user1_id_index` (`user1_id`),
  ADD KEY `chat_conversations_user2_id_index` (`user2_id`),
  ADD KEY `chat_conversations_last_message_at_index` (`last_message_at`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_messages_conversation_id_index` (`conversation_id`),
  ADD KEY `chat_messages_sender_id_index` (`sender_id`),
  ADD KEY `chat_messages_created_at_index` (`created_at`),
  ADD KEY `chat_messages_is_read_index` (`is_read`);

--
-- Indexes for table `connection_requests`
--
ALTER TABLE `connection_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `connection_requests_sender_id_index` (`sender_id`),
  ADD KEY `connection_requests_receiver_id_index` (`receiver_id`),
  ADD KEY `connection_requests_status_index` (`status`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donations_user_id_foreign` (`user_id`),
  ADD KEY `donations_cause_id_foreign` (`cause_id`);

--
-- Indexes for table `donation_causes`
--
ALTER TABLE `donation_causes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_applications_user_id_status_index` (`user_id`,`status`),
  ADD KEY `job_applications_job_posting_id_status_index` (`job_posting_id`,`status`),
  ADD KEY `job_applications_applied_at_index` (`applied_at`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_postings_business_id_is_active_index` (`business_id`,`is_active`),
  ADD KEY `job_postings_expires_at_index` (`expires_at`),
  ADD KEY `job_postings_location_index` (`location`),
  ADD KEY `job_postings_category_index` (`category`),
  ADD KEY `job_postings_experience_level_index` (`experience_level`),
  ADD KEY `job_postings_employment_type_index` (`employment_type`),
  ADD KEY `job_postings_status_index` (`status`),
  ADD KEY `job_postings_application_deadline_index` (`application_deadline`);

--
-- Indexes for table `matrimony_profiles`
--
ALTER TABLE `matrimony_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matrimony_profiles_user_id_index` (`user_id`),
  ADD KEY `matrimony_profiles_age_index` (`age`),
  ADD KEY `matrimony_profiles_approval_status_index` (`approval_status`),
  ADD KEY `matrimony_profiles_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_is_read_index` (`user_id`,`is_read`),
  ADD KEY `notifications_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `notifications_type_created_at_index` (`type`,`created_at`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_payment_id_unique` (`payment_id`),
  ADD KEY `payments_user_id_status_index` (`user_id`,`status`),
  ADD KEY `payments_payment_type_status_index` (`payment_type`,`status`),
  ADD KEY `payments_paid_at_index` (`paid_at`),
  ADD KEY `payments_created_at_index` (`created_at`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_business_id_index` (`business_id`),
  ADD KEY `products_status_index` (`status`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `services_business_id_index` (`business_id`),
  ADD KEY `services_status_index` (`status`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_settings_key_name_unique` (`key_name`),
  ADD KEY `system_settings_key_name_index` (`key_name`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transactions_user_id_index` (`user_id`),
  ADD KEY `transactions_status_index` (`status`),
  ADD KEY `transactions_purpose_index` (`purpose`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD KEY `users_email_index` (`email`),
  ADD KEY `users_caste_verification_status_index` (`caste_verification_status`),
  ADD KEY `users_user_type_index` (`user_type`);

--
-- Indexes for table `volunteer_applications`
--
ALTER TABLE `volunteer_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vol_app_unique` (`volunteer_profile_id`,`volunteer_opportunity_id`),
  ADD KEY `volunteer_applications_volunteer_opportunity_id_foreign` (`volunteer_opportunity_id`);

--
-- Indexes for table `volunteer_opportunities`
--
ALTER TABLE `volunteer_opportunities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_opportunities_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `volunteer_profiles`
--
ALTER TABLE `volunteer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `volunteer_profiles_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `businesses`
--
ALTER TABLE `businesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `business_categories`
--
ALTER TABLE `business_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `business_locations`
--
ALTER TABLE `business_locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `business_reviews`
--
ALTER TABLE `business_reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `caste_certificates`
--
ALTER TABLE `caste_certificates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_conversations`
--
ALTER TABLE `chat_conversations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `connection_requests`
--
ALTER TABLE `connection_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `donation_causes`
--
ALTER TABLE `donation_causes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `matrimony_profiles`
--
ALTER TABLE `matrimony_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `volunteer_applications`
--
ALTER TABLE `volunteer_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer_opportunities`
--
ALTER TABLE `volunteer_opportunities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `volunteer_profiles`
--
ALTER TABLE `volunteer_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `businesses`
--
ALTER TABLE `businesses`
  ADD CONSTRAINT `businesses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `business_categories` (`id`),
  ADD CONSTRAINT `businesses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `businesses_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `business_locations`
--
ALTER TABLE `business_locations`
  ADD CONSTRAINT `business_locations_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business_reviews`
--
ALTER TABLE `business_reviews`
  ADD CONSTRAINT `business_reviews_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_reviews_moderated_by_foreign` FOREIGN KEY (`moderated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `business_reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `caste_certificates`
--
ALTER TABLE `caste_certificates`
  ADD CONSTRAINT `caste_certificates_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `caste_certificates_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `chat_conversations`
--
ALTER TABLE `chat_conversations`
  ADD CONSTRAINT `chat_conversations_user1_id_foreign` FOREIGN KEY (`user1_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_conversations_user2_id_foreign` FOREIGN KEY (`user2_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_conversation_id_foreign` FOREIGN KEY (`conversation_id`) REFERENCES `chat_conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `connection_requests`
--
ALTER TABLE `connection_requests`
  ADD CONSTRAINT `connection_requests_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `connection_requests_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_cause_id_foreign` FOREIGN KEY (`cause_id`) REFERENCES `donation_causes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `donations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_job_posting_id_foreign` FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `job_postings_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `matrimony_profiles`
--
ALTER TABLE `matrimony_profiles`
  ADD CONSTRAINT `matrimony_profiles_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `matrimony_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `volunteer_applications`
--
ALTER TABLE `volunteer_applications`
  ADD CONSTRAINT `volunteer_applications_volunteer_opportunity_id_foreign` FOREIGN KEY (`volunteer_opportunity_id`) REFERENCES `volunteer_opportunities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `volunteer_applications_volunteer_profile_id_foreign` FOREIGN KEY (`volunteer_profile_id`) REFERENCES `volunteer_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `volunteer_opportunities`
--
ALTER TABLE `volunteer_opportunities`
  ADD CONSTRAINT `volunteer_opportunities_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `volunteer_profiles`
--
ALTER TABLE `volunteer_profiles`
  ADD CONSTRAINT `volunteer_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
