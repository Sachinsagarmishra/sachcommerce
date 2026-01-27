-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 25, 2025 at 01:56 AM
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
-- Database: `trendsone_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'admin_login', 'Admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 06:35:49'),
(2, 1, 'admin_login', 'Admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 08:44:36'),
(3, 1, 'admin_login', 'Admin logged in', '2401:4900:884a:7b62:e843:392f:6f00:a066', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-10 09:21:58'),
(4, 1, 'admin_login', 'Admin logged in', '2401:4900:88d2:d8e2:602f:2fe:831b:ed02', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36', '2025-11-23 21:08:38'),
(5, 1, 'admin_login', 'Admin logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-25 00:20:38'),
(6, 1, 'update_payment_settings', 'Updated Razorpay settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-25 00:51:53'),
(7, 1, 'update_payment_settings', 'Updated Razorpay settings', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-25 00:52:03');

-- --------------------------------------------------------

--
-- Table structure for table `blog_categories`
--

CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `status` enum('draft','published') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(100) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `variant_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `session_id`, `product_id`, `variant_id`, `quantity`, `created_at`, `updated_at`) VALUES
(5, 1, NULL, 32, NULL, 1, '2025-12-25 00:53:52', '2025-12-25 00:53:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `image`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `display_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Electronics', 'electronics', 'Latest electronic gadgets', NULL, NULL, NULL, NULL, 'active', 1, '2025-11-10 06:10:01', '2025-11-10 06:10:01'),
(2, NULL, 'Fashion', 'fashion', 'Trendy clothing', NULL, NULL, NULL, NULL, 'active', 2, '2025-11-10 06:10:01', '2025-11-10 06:10:01'),
(3, NULL, 'Home & Living', 'home-living', 'Home decor', NULL, NULL, NULL, NULL, 'active', 3, '2025-11-10 06:10:01', '2025-11-10 06:10:01'),
(4, NULL, 'Books', 'books', 'Wide range of books', NULL, NULL, NULL, NULL, 'active', 4, '2025-11-10 06:10:01', '2025-11-10 06:10:01'),
(5, NULL, 'Sports', 'sports', 'Sports equipment', NULL, NULL, NULL, NULL, 'active', 5, '2025-11-10 06:10:01', '2025-11-10 06:10:01');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `admin_reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(10,2) DEFAULT 0.00,
  `max_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_per_user` int(11) DEFAULT 1,
  `used_count` int(11) DEFAULT 0,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `status` enum('active','inactive','expired') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `status` enum('active','unsubscribed') DEFAULT 'active',
  `verification_token` varchar(100) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `subscribed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `unsubscribed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_number` varchar(50) NOT NULL,
  `order_status` enum('pending','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `payment_method` enum('razorpay','cod') NOT NULL,
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `razorpay_order_id` varchar(100) DEFAULT NULL,
  `razorpay_payment_id` varchar(100) DEFAULT NULL,
  `razorpay_signature` varchar(255) DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `coupon_code` varchar(50) DEFAULT NULL,
  `tax_amount` decimal(10,2) DEFAULT 0.00,
  `shipping_charge` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `shipping_address` text NOT NULL,
  `shipping_city` varchar(100) NOT NULL,
  `shipping_state` varchar(100) NOT NULL,
  `shipping_pincode` varchar(10) NOT NULL,
  `billing_address` text DEFAULT NULL,
  `billing_city` varchar(100) DEFAULT NULL,
  `billing_state` varchar(100) DEFAULT NULL,
  `billing_pincode` varchar(10) DEFAULT NULL,
  `billing_same_as_shipping` tinyint(1) DEFAULT 1,
  `order_notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `shipped_date` datetime DEFAULT NULL,
  `delivered_date` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_number`, `order_status`, `payment_method`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `razorpay_signature`, `subtotal`, `discount_amount`, `coupon_code`, `tax_amount`, `shipping_charge`, `total_amount`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `shipping_city`, `shipping_state`, `shipping_pincode`, `billing_address`, `billing_city`, `billing_state`, `billing_pincode`, `billing_same_as_shipping`, `order_notes`, `admin_notes`, `shipped_date`, `delivered_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'ORD202511103747', 'processing', 'razorpay', 'paid', NULL, NULL, NULL, 109999.00, 0.00, NULL, 0.00, 0.00, 109999.00, 'dfgdfg', 'admin@trendsone.com', '9032666684', 'dsfgsdf, gdfgsdfg', 'sdfgsdf', 'dfgsfdg', '221715', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-11-10 07:50:05', '2025-11-10 07:50:12'),
(2, 1, 'ORD202512251984', 'pending', 'razorpay', 'pending', 'order_RveYTcnhmLRCgR', NULL, NULL, 23993.00, 0.00, NULL, 0.00, 0.00, 23993.00, 'dfgdfg', 'admin@trendsone.com', '9032666684', 'dsfgsdf, gdfgsdfg', 'sdfgsdf', 'dfgsfdg', '221715', NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, '2025-12-25 00:52:24', '2025-12-25 00:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_sku` varchar(50) NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `variant_details` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_sku`, `product_image`, `variant_details`, `quantity`, `price`, `subtotal`, `created_at`) VALUES
(1, 1, 31, 'Samsung Galaxy S23 Ultra', '', NULL, NULL, 1, 109999.00, 109999.00, '2025-11-10 07:50:05'),
(2, 2, 39, 'Ray-Ban Aviator Sunglasses', '', NULL, NULL, 2, 6999.00, 13998.00, '2025-12-25 00:52:24'),
(3, 2, 37, 'Nike Air Max 270', '', NULL, NULL, 1, 9995.00, 9995.00, '2025-12-25 00:52:24');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_history`
--

CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `short_description` text DEFAULT NULL,
  `long_description` text DEFAULT NULL,
  `primary_image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `discount_percentage` int(11) DEFAULT 0,
  `stock_quantity` int(11) DEFAULT 0,
  `low_stock_alert` int(11) DEFAULT 10,
  `weight` decimal(8,2) DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `is_new_arrival` tinyint(1) DEFAULT 0,
  `is_best_seller` tinyint(1) DEFAULT 0,
  `meta_title` varchar(200) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `views_count` int(11) DEFAULT 0,
  `status` enum('active','inactive','out_of_stock') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `short_description`, `long_description`, `primary_image`, `price`, `sale_price`, `discount_percentage`, `stock_quantity`, `low_stock_alert`, `weight`, `length`, `width`, `height`, `is_featured`, `is_new_arrival`, `is_best_seller`, `meta_title`, `meta_description`, `meta_keywords`, `views_count`, `status`, `created_at`, `updated_at`) VALUES
(31, 1, 'Samsung Galaxy S23 Ultra', 'samsung-galaxy-s23-ultra', 'ELEC001', 'Latest flagship smartphone with 200MP camera', 'The Samsung Galaxy S23 Ultra features a stunning 6.8-inch Dynamic AMOLED display, powerful Snapdragon 8 Gen 2 processor, and an incredible 200MP camera system. Perfect for photography enthusiasts and power users.', 'samsung-galaxy-s23-ultra-1.jpg', 124999.00, 109999.00, 12, 24, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(32, 1, 'Apple iPhone 15 Pro', 'apple-iphone-15-pro', 'ELEC002', 'Premium iPhone with titanium design', 'Experience the power of A17 Pro chip, ProMotion display, and advanced camera system. The iPhone 15 Pro comes with a durable titanium design and Action button for quick access to your favorite features.', 'iphone-15-pro-1.jpg', 134900.00, 129900.00, 4, 30, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(33, 1, 'Sony WH-1000XM5 Headphones', 'sony-wh-1000xm5-headphones', 'ELEC003', 'Industry-leading noise cancellation', 'Premium wireless headphones with exceptional sound quality, 30-hour battery life, and AI-powered noise cancellation. Perfect for music lovers and frequent travelers.', 'sony-wh-1000xm5-1.jpg', 29990.00, 24990.00, 17, 50, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(34, 1, 'Dell XPS 15 Laptop', 'dell-xps-15-laptop', 'ELEC004', 'Powerful laptop for professionals', '15.6-inch 4K display, Intel Core i7, 16GB RAM, 512GB SSD. Perfect for content creators, developers, and business professionals.', 'dell-xps-15-1.jpg', 159999.00, 149999.00, 6, 15, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(35, 1, 'Apple Watch Series 9', 'apple-watch-series-9', 'ELEC005', 'Advanced health and fitness tracker', 'Track your workouts, monitor your health, and stay connected. Features include ECG, blood oxygen monitoring, and crash detection.', 'apple-watch-series-9-1.jpg', 41900.00, 39900.00, 5, 40, 10, NULL, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:23:41'),
(36, 2, 'Levis 511 Slim Fit Jeans', 'levis-511-slim-fit-jeans', 'FASH001', 'Classic slim fit denim jeans', 'Comfortable stretch denim with a modern slim fit. Perfect for everyday wear. Available in multiple washes.', 'levis-511-slim-fit-jeans-1.jpg', 3999.00, 2999.00, 25, 100, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:00:35'),
(37, 2, 'Nike Air Max 270', 'nike-air-max-270', 'FASH002', 'Iconic sneakers with Max Air cushioning', 'Comfortable and stylish sneakers featuring Nike\'s largest Air unit yet. Perfect for running or casual wear.', 'nike-air-max-270-1.jpg', 12995.00, 9995.00, 23, 74, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-12-25 00:52:24'),
(38, 2, 'Adidas Originals Hoodie', 'adidas-originals-hoodie', 'FASH003', 'Classic pullover hoodie', 'Comfortable cotton blend hoodie with iconic trefoil logo. Perfect for casual outings and lounging.', 'adidas-originals-hoodie-1.jpg', 4999.00, 3499.00, 30, 120, 10, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:27:31'),
(39, 2, 'Ray-Ban Aviator Sunglasses', 'ray-ban-aviator-sunglasses', 'FASH004', 'Iconic aviator style sunglasses', 'Classic metal frame with UV protection lenses. A timeless fashion accessory.', 'ray-ban-aviator-sunglasses-1.jpg', 8999.00, 6999.00, 22, 58, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-12-25 00:52:24'),
(40, 2, 'Fossil Gen 6 Smartwatch', 'fossil-gen-6-smartwatch', 'FASH005', 'Stylish smartwatch with Wear OS', 'Track your fitness, receive notifications, and customize your watch face. Compatible with Android and iOS.', 'fossil-gen-6-smartwatch-1.jpg', 21995.00, 18995.00, 14, 35, 10, NULL, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:28:39'),
(41, 3, 'Philips Air Fryer', 'philips-air-fryer', 'HOME001', 'Healthy cooking with 90% less fat', 'Cook your favorite fried foods with little to no oil. Features rapid air technology and digital display.', 'philips-air-fryer-1.jpg', 12995.00, 9995.00, 23, 45, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:30:27'),
(42, 3, 'Dyson V11 Vacuum Cleaner', 'dyson-v11-vacuum-cleaner', 'HOME002', 'Powerful cordless vacuum', 'Intelligent suction that adapts to different floor types. Up to 60 minutes of fade-free power.', 'dyson-v11-vacuum-cleaner-1.jpg', 49900.00, 44900.00, 10, 20, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:31:06'),
(43, 3, 'Amazon Echo Dot 5th Gen', 'amazon-echo-dot-5th-gen', 'HOME003', 'Smart speaker with Alexa', 'Control your smart home, play music, get weather updates, and more with voice commands.', 'amazon-echo-dot-5th-gen-1.jpg', 5499.00, 3999.00, 27, 80, 10, NULL, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 09:31:48'),
(44, 3, 'Prestige Induction Cooktop', 'prestige-induction-cooktop', 'HOME004', 'Energy-efficient cooking', 'Fast and safe cooking with automatic voltage regulator. Features preset menu options.', 'prestige-cooktop-1.jpg', 3499.00, 2799.00, 20, 55, 10, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(45, 3, 'IKEA Study Table', 'ikea-study-table', 'HOME005', 'Modern minimalist desk', 'Spacious work surface with cable management. Perfect for home office or study room.', 'ikea-desk-1.jpg', 8999.00, 7499.00, 17, 30, 10, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(46, 4, 'Atomic Habits by James Clear', 'atomic-habits-james-clear', 'BOOK001', 'Transform your life with tiny changes', 'Learn how small habits can lead to remarkable results. A practical guide to building good habits and breaking bad ones.', 'atomic-habits-1.jpg', 599.00, 449.00, 25, 200, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(47, 4, 'The Psychology of Money', 'psychology-of-money', 'BOOK002', 'Timeless lessons on wealth', 'Understand the strange ways people think about money and how to make better financial decisions.', 'psychology-of-money-1.jpg', 399.00, 299.00, 25, 150, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(48, 4, 'Harry Potter Complete Collection', 'harry-potter-complete-collection', 'BOOK003', 'All 7 books in one set', 'The complete magical journey of Harry Potter. Perfect gift for fans of all ages.', 'harry-potter-set-1.jpg', 4999.00, 3999.00, 20, 50, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(49, 4, 'Think Like a Monk', 'think-like-a-monk', 'BOOK004', 'Train your mind for peace', 'Jay Shetty shares wisdom from his time as a monk to help you find calm and purpose.', 'think-like-monk-1.jpg', 499.00, 349.00, 30, 180, 10, NULL, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(50, 4, 'Rich Dad Poor Dad', 'rich-dad-poor-dad', 'BOOK005', 'Financial education classic', 'Learn what the rich teach their kids about money that the poor and middle class do not.', 'rich-dad-poor-dad-1.jpg', 399.00, 299.00, 25, 220, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(51, 5, 'Yoga Mat Premium', 'yoga-mat-premium', 'SPORT001', 'Non-slip exercise mat', 'Extra thick 6mm mat with excellent grip. Perfect for yoga, pilates, and floor exercises.', 'yoga-mat-1.jpg', 1499.00, 999.00, 33, 100, 10, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(52, 5, 'Dumbbells Set 20kg', 'dumbbells-set-20kg', 'SPORT002', 'Adjustable weight dumbbells', 'Build strength at home with this versatile dumbbell set. Includes multiple weight plates.', 'dumbbells-20kg-1.jpg', 3999.00, 2999.00, 25, 40, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(53, 5, 'Decathlon Cycle 26T', 'decathlon-cycle-26t', 'SPORT003', 'Mountain bike for all terrains', 'Durable steel frame with 21-speed gears. Perfect for city commuting and weekend trails.', 'decathlon-cycle-1.jpg', 14999.00, 12999.00, 13, 25, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(54, 5, 'Nivia Football Size 5', 'nivia-football-size-5', 'SPORT004', 'Professional quality football', 'FIFA approved size 5 football with excellent grip and durability.', 'nivia-football-1.jpg', 899.00, 699.00, 22, 150, 10, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(55, 5, 'Fitbit Charge 6', 'fitbit-charge-6', 'SPORT005', 'Advanced fitness tracker', 'Track your workouts, heart rate, sleep, and stress. Built-in GPS and 7-day battery life.', 'fitbit-charge-6-1.jpg', 14999.00, 12999.00, 13, 60, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(56, 1, 'PlayStation 5 Console', 'playstation-5-console', 'GAME001', 'Next-gen gaming console', 'Experience lightning-fast loading with ultra-high speed SSD and stunning graphics with ray tracing.', 'ps5-console-1.jpg', 54990.00, 49990.00, 9, 15, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(57, 1, 'Xbox Series X', 'xbox-series-x', 'GAME002', 'Most powerful Xbox ever', '4K gaming at 120fps, quick resume, and Game Pass compatibility.', 'xbox-series-x-1.jpg', 52990.00, 47990.00, 9, 18, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(58, 1, 'Logitech G502 Gaming Mouse', 'logitech-g502-gaming-mouse', 'GAME003', 'Hero sensor gaming mouse', 'Customizable RGB lighting, 11 programmable buttons, and adjustable weights.', 'logitech-g502-1.jpg', 4995.00, 3995.00, 20, 80, 10, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(59, 1, 'Razer BlackWidow Keyboard', 'razer-blackwidow-keyboard', 'GAME004', 'Mechanical gaming keyboard', 'Tactile mechanical switches, customizable RGB, and dedicated media keys.', 'razer-blackwidow-1.jpg', 8999.00, 6999.00, 22, 50, 10, NULL, NULL, NULL, NULL, 1, 0, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16'),
(60, 1, 'Nintendo Switch OLED', 'nintendo-switch-oled', 'GAME005', 'Handheld gaming console', '7-inch OLED screen, enhanced audio, and play anywhere. Includes dock for TV mode.', 'nintendo-switch-1.jpg', 34999.00, 32999.00, 6, 30, 10, NULL, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, 0, 'active', '2025-11-10 07:20:44', '2025-11-10 08:15:16');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `alt_text` varchar(200) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `alt_text`, `is_primary`, `display_order`, `created_at`) VALUES
(48, 31, 'samsung-galaxy-s23-ultra-1.jpg', 'Samsung Galaxy S23 Ultra Phantom Black front view', 1, 1, '2025-11-10 08:04:59'),
(49, 31, 'samsung-galaxy-s23-ultra-2.jpg', 'Samsung Galaxy S23 Ultra camera close-up', 0, 2, '2025-11-10 08:04:59'),
(50, 31, 'samsung-galaxy-s23-ultra-3.jpg', 'Samsung Galaxy S23 Ultra side view with S Pen', 0, 3, '2025-11-10 08:04:59'),
(51, 32, 'iphone-15-pro-1.jpg', 'iPhone 15 Pro Natural Titanium front', 1, 1, '2025-11-10 08:04:59'),
(52, 32, 'iphone-15-pro-2.jpg', 'iPhone 15 Pro back with triple camera', 0, 2, '2025-11-10 08:04:59'),
(53, 32, 'iphone-15-pro-3.jpg', 'iPhone 15 Pro Action Button detail', 0, 3, '2025-11-10 08:04:59'),
(54, 33, 'sony-wh-1000xm5-1.jpg', 'Sony WH-1000XM5 Black overhead view', 1, 1, '2025-11-10 08:04:59'),
(55, 33, 'sony-wh-1000xm5-2.jpg', 'Sony WH-1000XM5 folded in case', 0, 2, '2025-11-10 08:04:59'),
(56, 34, 'dell-xps-15-1.jpg', 'Dell XPS 15 4K OLED display', 1, 1, '2025-11-10 08:04:59'),
(57, 34, 'dell-xps-15-2.jpg', 'Dell XPS 15 keyboard and trackpad', 0, 2, '2025-11-10 08:04:59'),
(58, 35, 'apple-watch-series9-1.jpg', 'Apple Watch Series 9 Midnight Aluminum', 1, 1, '2025-11-10 08:04:59'),
(59, 35, 'apple-watch-series9-2.jpg', 'Apple Watch Series 9 display always-on', 0, 2, '2025-11-10 08:04:59'),
(60, 36, 'levis-511-jeans-1.jpg', 'Levi\'s 511 Slim Fit Dark Wash front', 1, 1, '2025-11-10 08:04:59'),
(61, 36, 'levis-511-jeans-2.jpg', 'Levi\'s 511 back pockets', 0, 2, '2025-11-10 08:04:59'),
(62, 37, 'nike-air-max-270-1.jpg', 'Nike Air Max 270 Black White side view', 1, 1, '2025-11-10 08:04:59'),
(63, 37, 'nike-air-max-270-2.jpg', 'Nike Air Max 270 heel air unit', 0, 2, '2025-11-10 08:04:59'),
(64, 38, 'adidas-hoodie-1.jpg', 'Adidas Originals Hoodie Grey front', 1, 1, '2025-11-10 08:04:59'),
(65, 38, 'adidas-hoodie-2.jpg', 'Adidas trefoil logo close-up', 0, 2, '2025-11-10 08:04:59'),
(66, 39, 'rayban-aviator-1.jpg', 'Ray-Ban Aviator Classic Gold frame', 1, 1, '2025-11-10 08:04:59'),
(67, 39, 'rayban-aviator-2.jpg', 'Ray-Ban Aviator on face', 0, 2, '2025-11-10 08:04:59'),
(68, 40, 'fossil-gen6-1.jpg', 'Fossil Gen 6 Smartwatch Rose Gold', 1, 1, '2025-11-10 08:04:59'),
(69, 40, 'fossil-gen6-2.jpg', 'Fossil Gen 6 display with apps', 0, 2, '2025-11-10 08:04:59'),
(70, 41, 'philips-airfryer-1.jpg', 'Philips Air Fryer XXL front view', 1, 1, '2025-11-10 08:04:59'),
(71, 41, 'philips-airfryer-2.jpg', 'Philips Air Fryer basket with food', 0, 2, '2025-11-10 08:04:59'),
(72, 42, 'dyson-v11-1.jpg', 'Dyson V11 Absolute cordless vacuum', 1, 1, '2025-11-10 08:04:59'),
(73, 42, 'dyson-v11-2.jpg', 'Dyson V11 LCD screen', 0, 2, '2025-11-10 08:04:59'),
(74, 43, 'echo-dot-5-1.jpg', 'Amazon Echo Dot 5th Gen Charcoal', 1, 1, '2025-11-10 08:04:59'),
(75, 43, 'echo-dot-5-2.jpg', 'Echo Dot top view with LED', 0, 2, '2025-11-10 08:04:59'),
(76, 44, 'prestige-induction-1.jpg', 'Prestige Induction Cooktop touch panel', 1, 1, '2025-11-10 08:04:59'),
(77, 44, 'prestige-induction-2.jpg', 'Prestige Induction side angle', 0, 2, '2025-11-10 08:04:59'),
(78, 45, 'ikea-study-table-1.jpg', 'IKEA MICKE Desk White front', 1, 1, '2025-11-10 08:04:59'),
(79, 45, 'ikea-study-table-2.jpg', 'IKEA desk cable management', 0, 2, '2025-11-10 08:04:59'),
(80, 46, 'atomic-habits-1.jpg', 'Atomic Habits book cover', 1, 1, '2025-11-10 08:04:59'),
(81, 47, 'psychology-of-money-1.jpg', 'The Psychology of Money book cover', 1, 1, '2025-11-10 08:04:59'),
(82, 48, 'harry-potter-boxset-1.jpg', 'Harry Potter Complete Collection box set', 1, 1, '2025-11-10 08:04:59'),
(83, 49, 'think-like-a-monk-1.jpg', 'Think Like a Monk book cover', 1, 1, '2025-11-10 08:04:59'),
(84, 50, 'rich-dad-poor-dad-1.jpg', 'Rich Dad Poor Dad book cover', 1, 1, '2025-11-10 08:04:59'),
(85, 51, 'yoga-mat-1.jpg', 'Premium Yoga Mat Purple rolled', 1, 1, '2025-11-10 08:04:59'),
(86, 52, 'dumbbells-1.jpg', '20kg Adjustable Dumbbell Set', 1, 1, '2025-11-10 08:04:59'),
(87, 53, 'decathlon-cycle-1.jpg', 'Decathlon Rockrider 26T Mountain Bike', 1, 1, '2025-11-10 08:04:59'),
(88, 54, 'nivia-football-1.jpg', 'Nivia Storm Football Size 5', 1, 1, '2025-11-10 08:04:59'),
(89, 55, 'fitbit-charge6-1.jpg', 'Fitbit Charge 6 Obsidian front', 1, 1, '2025-11-10 08:04:59'),
(90, 56, 'ps5-1.jpg', 'PlayStation 5 Console with controller', 1, 1, '2025-11-10 08:04:59'),
(91, 57, 'xbox-series-x-1.jpg', 'Xbox Series X front view', 1, 1, '2025-11-10 08:04:59'),
(92, 58, 'logitech-g502-1.jpg', 'Logitech G502 HERO gaming mouse', 1, 1, '2025-11-10 08:04:59'),
(93, 59, 'razer-blackwidow-1.jpg', 'Razer BlackWidow V3 mechanical keyboard', 1, 1, '2025-11-10 08:04:59'),
(94, 60, 'nintendo-switch-oled-1.jpg', 'Nintendo Switch OLED White Joy-Con', 1, 1, '2025-11-10 08:04:59');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `variant_type` varchar(50) NOT NULL,
  `variant_value` varchar(100) NOT NULL,
  `price_adjustment` decimal(10,2) DEFAULT 0.00,
  `stock_quantity` int(11) DEFAULT 0,
  `sku_suffix` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `title` varchar(200) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_reply` text DEFAULT NULL,
  `helpful_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(50) DEFAULT 'text',
  `setting_group` varchar(50) DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `country` varchar(50) DEFAULT 'India',
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(100) DEFAULT NULL,
  `password_reset_token` varchar(100) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `status` enum('active','blocked') DEFAULT 'active',
  `role` enum('customer','admin') DEFAULT 'customer',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `avatar`, `address`, `city`, `state`, `pincode`, `country`, `email_verified`, `verification_token`, `password_reset_token`, `password_reset_expires`, `status`, `role`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@trendsone.com', '$2y$12$Shbr7HzI2NW2suDK6Iof9eJngSINCPKEapV6rgJ3wRcaHwsrhmKdG', '+91 9876543210', NULL, NULL, NULL, NULL, NULL, 'India', 1, NULL, NULL, NULL, 'active', 'admin', '2025-12-25 05:50:38', '2025-11-10 06:10:01', '2025-12-25 00:20:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_type` enum('home','work','other') DEFAULT 'home',
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `landmark` varchar(100) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `address_type`, `full_name`, `phone`, `address_line1`, `address_line2`, `landmark`, `city`, `state`, `pincode`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'home', 'dfgdfg', '9032666684', 'dsfgsdf', 'gdfgsdfg', NULL, 'sdfgsdf', 'dfgsfdg', '221715', 0, '2025-11-10 07:43:07', '2025-11-10 07:43:07');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(2, 1, 31, '2025-11-10 07:38:35'),
(3, 1, 40, '2025-11-10 07:51:46'),
(4, 1, 41, '2025-11-10 07:53:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `blog_categories`
--
ALTER TABLE `blog_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published_at` (`published_at`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_parent_id` (`parent_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_status` (`order_status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Indexes for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `updated_by` (`updated_by`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_group` (`setting_group`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `blog_categories`
--
ALTER TABLE `blog_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_status_history`
--
ALTER TABLE `order_status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_3` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `coupon_usage_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `coupon_usage_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_status_history`
--
ALTER TABLE `order_status_history`
  ADD CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_status_history_ibfk_2` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
