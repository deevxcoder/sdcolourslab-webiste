-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2026 at 10:40 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u953522373_sdcolourslab`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `token` varchar(100) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `api_tokens`
--

INSERT INTO `api_tokens` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(1, 104, '8744cbf194149472ddcda77b9dc2498fc09c463852f94c18d82d552b20e3d034', '2026-06-24 02:07:00', '2026-05-25 02:07:00');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `photographer_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `shipping_address` text DEFAULT NULL,
  `manual_studio_name` varchar(200) DEFAULT NULL,
  `manual_phone` varchar(20) DEFAULT NULL,
  `manual_size` varchar(50) DEFAULT NULL,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `discount_amount` decimal(10,2) DEFAULT 0.00,
  `secure_key` varchar(64) DEFAULT NULL,
  `net_pay` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `photographer_id`, `status`, `total`, `notes`, `admin_notes`, `created_at`, `updated_at`, `shipping_address`, `manual_studio_name`, `manual_phone`, `manual_size`, `discount_percent`, `discount_amount`, `secure_key`, `net_pay`) VALUES
(100, 103, 'delivered', 412.00, '', 'Delivered to Chrome Photo Studio', '2026-05-24 19:03:13', '2026-05-25 02:02:01', 'Chrome Studio\n123 Chrome Street, Tech Park\nMumbai, Odisha - 400001\nPhone: 9988776655', NULL, NULL, NULL, 0.00, 0.00, 'b166280fa43d54e82b9ca98af3824c91', 0.00),
(101, NULL, 'pending', 1500.00, '', NULL, '2026-05-25 02:04:40', '2026-05-25 02:04:40', NULL, 'Test Studio Live', '9876543210', '12x36', 0.00, 0.00, '9f5bf50e8b458ad995d7ffdc731e30ae', 1500.00),
(102, 104, 'pending', 4800.00, 'Test Photographer Checkout Order', NULL, '2026-05-25 02:07:25', '2026-05-25 02:07:25', NULL, NULL, NULL, NULL, 0.00, 0.00, NULL, 0.00),
(103, 102, 'processing', 5700.00, '', '', '2026-06-06 07:53:39', '2026-06-06 07:58:06', 'Jhirpani\nd/520\nRourkela, Odisha - 769001\nPhone: 8895838987', NULL, NULL, NULL, 0.00, 0.00, NULL, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(200) NOT NULL,
  `size` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `print_type` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `unit_price`, `notes`, `print_type`) VALUES
(100, 100, 45, '8x12 LED Frame', '8x12', 1, 412.00, '', NULL),
(101, 101, NULL, 'Test Print', '12x36', 1, 1500.00, '', 'Exclusive Pod'),
(102, 101, NULL, 'Test Print', '12x36', 1, 0.00, '', 'Tharmal'),
(103, 101, NULL, 'Test Print', '12x36', 1, 0.00, '', ''),
(104, 102, 57, 'Leatherism Combo Photo Pad – Drawerio 3 IN 1', '12x36', 1, 4800.00, '', NULL),
(105, 103, 43, 'Emboss / Foil Album (Emboss Simplex 12x18)', '12x18 (30 pages)', 1, 5700.00, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price_alt` decimal(10,2) DEFAULT NULL,
  `sizes` longtext DEFAULT NULL,
  `features` longtext DEFAULT NULL,
  `tag` varchar(100) DEFAULT NULL,
  `image` varchar(300) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `price`, `price_alt`, `sizes`, `features`, `tag`, `image`, `active`, `sort_order`, `created_at`) VALUES
(10, 'Regular Album', 'album', 'Per page printing. Sizes: 12x15, 12x18, 18x24. Multiple paper options available.', 38.00, 61.00, '[\"12x15\", \"12x18\", \"18x24\"]', '[\"Regular Glossy – ₹38/page\", \"Regular Heavy Glossy – ₹46/page\", \"Regular Matt – ₹51/page\", \"Regular Heavy Matt – ₹61/page\"]', NULL, NULL, 1, 10, '2026-05-24 18:05:51'),
(11, 'Special Album', 'album', 'Per page printing. Sizes: 12x15, 12x18, 18x24. NTR paper series with premium finish.', 52.00, 70.00, '[\"12x15\", \"12x18\", \"18x24\"]', '[\"Ntr Glossy Slim – ₹52/page\", \"Ntr Heavy Glossy – ₹62/page\", \"Ntr Matt Slim – ₹62/page\", \"Ntr Heavy Matt – ₹66/page\", \"Luster – ₹70/page\"]', NULL, NULL, 1, 11, '2026-05-24 18:05:51'),
(12, 'Metallic Album', 'album', 'Per page printing. Sizes: 12x15, 12x18, 18x24. Premium metallic and specialty finishes.', 60.00, 110.00, '[\"12x15\", \"12x18\", \"18x24\"]', '[\"Transparent Sheet – ₹90/page\", \"Regular Velvet Sheet – ₹60/page\", \"Ntr Velvet Sheet – ₹72/page\", \"Silky Metallic – ₹90/page\", \"Ultra Metallic – ₹90/page\", \"Pearl Metallic – ₹110/page\", \"Sparkle – ₹90/page\", \"3D – ₹110/page\"]', NULL, NULL, 1, 12, '2026-05-24 18:05:51'),
(13, 'Mini Book', 'album', 'Per page printing. Sizes: 9x24, 8x24. Compact booklet style album.', 28.00, 40.00, '[\"9x24\", \"8x24\"]', '[\"Regular Glossy – ₹28/page\", \"Regular Matt – ₹30/page\", \"Ntr Glossy – ₹38/page\", \"Ntr Matt – ₹40/page\"]', NULL, NULL, 1, 13, '2026-05-24 18:05:51'),
(16, '5x7 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 350.00, NULL, '[\"5x7\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 16, '2026-05-24 18:05:51'),
(17, '6x8 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 500.00, NULL, '[\"6x8\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 17, '2026-05-24 18:05:51'),
(18, '8x12 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 650.00, NULL, '[\"8x12\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 18, '2026-05-24 18:05:51'),
(19, '12x18 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 750.00, NULL, '[\"12x18\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 19, '2026-05-24 18:05:51'),
(20, '16x20 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 1550.00, NULL, '[\"16x20\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 20, '2026-05-24 18:05:51'),
(21, '20x24 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 2250.00, NULL, '[\"20x24\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 21, '2026-05-24 18:05:51'),
(22, '20x30 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 2750.00, NULL, '[\"20x30\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 22, '2026-05-24 18:05:51'),
(23, '24x36 Acrylic Photo', 'wall_acrylic', '5mm Wall Acrylic Photo Print', 3150.00, NULL, '[\"24x36\"]', '[\"5mm Thickness\", \"Crystal Clear Acrylic\"]', NULL, NULL, 1, 23, '2026-05-24 18:05:51'),
(24, 'Leather Combo Photo Pad – Leather 2 IN 1 (with Bag)', 'combo', 'Leather (2 IN 1) – Cover Leather Pad, Leather Photo Bag, 8x12 LED Frame & Wall Calendar', 1550.00, NULL, '[\"12x24\",\"12x30\",\"12x36\",\"24x15\",\"18x24\"]', '[\"Cover Leather Pad\",\"Leather Photo Bag\",\"8x12 LED Frame\",\"Wall Calendar\"]', 'Best Seller', '/images/combos/combo_6a23d1131baeb.jpg', 1, 1, '2026-05-24 18:05:51'),
(25, 'Acrylic Combo Photo Pad – Acrylic 2 IN 1', 'combo', 'Acrylic (2 IN 1) – Leather Cover Pad, Full Acrylic', 1250.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Leather Cover Pad\", \"Full Acrylic\"]', 'Best Seller', '/images/combos/acrylic-2in1.jpg', 1, 2, '2026-05-24 18:05:51'),
(26, 'Leather Combo Photo Pad – Leather 2 IN 1 (with Box)', 'combo', 'Leather (2 IN 1) – Cover Leather Pad & Box', 1550.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Cover Leather Pad\", \"Premium Box\"]', NULL, '/images/combos/leather-2in1-box.jpg', 1, 3, '2026-05-24 18:05:51'),
(27, 'Wooden Combo Photo Pad – LAWood 4 IN 1', 'combo', 'LAWood (4 IN 1) – Wooden Cover Pad, Leather Bag, Album Size LED Frame & 12x18 Wall Calendar', 1850.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Wooden Cover Pad\", \"Leather Bag\", \"Album Size LED Frame\", \"12x18 Wall Calendar\"]', NULL, '/images/combos/wooden-4in1.jpg', 1, 4, '2026-05-24 18:05:51'),
(28, 'Royal Combo Photo Pad – Royal 4 IN 1', 'combo', 'Royal (4 IN 1) – Leather Cover Pad, Leather Bag & Box, 8x12 LED Frame', 2250.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Leather Cover Pad\", \"Leather Bag & Box\", \"8x12 LED Frame\"]', NULL, '/images/combos/royal-4in1.jpg', 1, 5, '2026-05-24 18:05:51'),
(29, 'Superior Combo Photo Pad – Silver Series 3 IN 1', 'combo', 'Silver Series (3 IN 1) – Leather finished cover with design acrylic photo pad, Leather Bag, Wall Calendar', 1750.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Leather Finished Cover\", \"Design Acrylic Photo Pad\", \"Leather Bag\", \"Wall Calendar\"]', NULL, '/images/combos/superior-silver-3in1.jpg', 1, 6, '2026-05-24 18:05:51'),
(30, 'Superior Combo Photo Pad – Silver Series 4 IN 1', 'combo', 'Silver Series (4 IN 1) – Wooden finished cover with design acrylic & engraving, Leather Bag, Album size LED Frame, Wall Calendar', 2100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Wooden Finished Cover\", \"Design Acrylic with Engraving\", \"Leather Bag\", \"Album Size LED Frame\", \"Wall Calendar\"]', NULL, '/images/combos/superior-silver-4in1.jpg', 1, 7, '2026-05-24 18:05:51'),
(31, 'Superior Combo Photo Pad – Gold+ Series 6 IN 1', 'combo', 'Gold+ Series (6 IN 1) – Leather finished cover with design acrylic, Briefcase, Leather Bag, LED Frame, Wall Calendar & Mini Book', 2550.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Leather Finished Cover\", \"Design Acrylic Photo Pad\", \"Briefcase\", \"Leather Bag\", \"LED Photo Frame\", \"Wall Calendar\", \"Mini Book\"]', 'Best Seller', '/images/combos/superior-gold-6in1.jpg', 1, 8, '2026-05-24 18:05:51'),
(32, 'Superior Combo Photo Pad – Platinum Series 6 IN 1', 'combo', 'Platinum Series (6 IN 1) – Leather finished cover with design acrylic, Briefcase, Leather Bag, LED Frame, Wall Calendar & Mini Book', 3150.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Leather Finished Cover\", \"Design Acrylic Photo Pad\", \"Briefcase\", \"Leather Bag\", \"LED Photo Frame\", \"Wall Calendar\", \"Mini Book\"]', 'Premium', '/images/combos/superior-platinum-6in1.jpg', 1, 9, '2026-05-24 18:05:51'),
(33, 'Prowood Combo Photo Pad – LAWood 3 IN 1', 'combo', 'LAWood (3 IN 1) – Wooden & Leather combined briefcase and cover photo pad, Briefcase & Leather Bag', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Wooden & Leather Combined\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/prowood-lawood-3in1.jpg', 1, 10, '2026-05-24 18:05:51'),
(34, 'Inluxury Combo Photo Pad – Proluxury 5 IN 1', 'combo', 'Proluxury (5 IN 1) – Leather finished square briefcase, window acrylic cover photo pad, Briefcase & Leather Bag', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Leather Finished Square Briefcase\", \"Window Acrylic Cover Photo Pad\", \"Window Acrylic\", \"Briefcase\", \"Leather Bag\"]', 'Premium', '/images/combos/inluxury-proluxury-5in1.jpg', 1, 11, '2026-05-24 18:05:51'),
(35, 'Inluxury Combo Photo Pad – Premiumster 6 IN 1', 'combo', 'Premiumster (6 IN 1) – Leather finished cover with design acrylic. Cover photo pad, Briefcase, Leather Bag, Mini Book, Led Photo Frame & Wall Calendar.', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather Finished Cover with Design Acrylic\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\", \"Mini Book\", \"Led Photo Frame\", \"Wall Calendar\"]', 'Premium', '/images/combos/inluxury-premiumster-6in1-v2.jpg', 1, 12, '2026-05-24 18:05:51'),
(36, 'Prowood Combo Photo Pad – Wooden Standus 5 IN 1', 'combo', 'Wooden Standus (5 IN 1) – Leather combined wooden cover with design acrylic window, Briefcase, Leather Bag, Mini Book, LED Frame', 4700.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"18x24\"]', '[\"Wooden & Leather Combined Cover\", \"Design Acrylic Window\", \"Briefcase\", \"Leather Bag\", \"Mini Book\", \"LED Photo Frame\"]', 'Premium', '/images/combos/prowood-standus-5in1.jpg', 1, 13, '2026-05-24 18:05:51'),
(37, 'Prowood Combo Photo Pad – Wooden Rewood 3 IN 1', 'combo', 'Wooden Rewood (3 IN 1) – Leather combined wooden cover with design acrylic window, Cover Photo Pad, Briefcase & Leather Bag', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather & Wooden Combined Cover\", \"Design Acrylic Window\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/prowood-rewood-3in1.jpg', 1, 14, '2026-05-24 18:05:51'),
(38, 'Prowood Combo Photo Pad – Wooden Standus 5 IN 1 (Pro)', 'combo', 'Wooden Standus (5 IN 1) – Leather combined wooden cover with design acrylic window, Briefcase, Leather Bag, Mini Book & LED Frame', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather & Wooden Combined Cover\", \"Design Acrylic Window\", \"Briefcase\", \"Leather Bag\", \"Mini Book\", \"LED Photo Frame\"]', NULL, '/images/combos/prowood-standus2-5in1.jpg', 1, 15, '2026-05-24 18:05:51'),
(39, 'Prowood Combo Photo Pad – Wooden Inforest 3 IN 1', 'combo', 'Wooden Inforest (3 IN 1) – Full wooden cover with engraving, Cover Photo Pad, Briefcase & Leather Bag', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Full Wooden Cover with Engraving\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/prowood-inforest-3in1.jpg', 1, 16, '2026-05-24 18:05:51'),
(40, 'Prowood Combo Photo Pad – Wooden Engravinggio 6 IN 1', 'combo', 'Wooden Engravinggio (6 IN 1) – Full wooden cover with engraving, Briefcase, Leather Bag, 12x18 Wall Calendar, Mini Book & LED Frame', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Full Wooden Cover with Engraving\", \"Briefcase\", \"Leather Bag\", \"12x18 Wall Calendar\", \"Mini Book\", \"LED Photo Frame\"]', NULL, '/images/combos/prowood-engraving-6in1.jpg', 1, 17, '2026-05-24 18:05:51'),
(41, 'Prowood Combo Photo Pad – Wooden 360 (3 IN 1)', 'combo', 'Wooden 360 (3 IN 1) – Wooden & Leather finished combined cover and briefcase, Cover Photo Pad, Briefcase & Leather Bag', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Wooden & Leather Combined Cover\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/prowood-360-3in1.jpg', 1, 18, '2026-05-24 18:05:51'),
(42, 'Prowood Combo Photo Pad – LaWood 3 IN 1', 'combo', 'LaWood (3 IN 1) – Wooden & Leather combined briefcase and cover photo pad, Briefcase & Leather Bag', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Wooden & Leather Combined Briefcase\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/prowood-lawood2-3in1.jpg', 1, 19, '2026-05-24 18:05:51'),
(43, 'Emboss / Foil Album', 'album', 'Per page printing with premium emboss and gold foil finish.', 190.00, 250.00, '[\"12x12\", \"12x15\", \"12x18\", \"18x24\"]', '[\"Emboss Simplex 12x18 – ₹190/page\", \"Emboss + Gold Foil 12x18 – ₹250/page\"]', 'Premium', NULL, 1, 5, '2026-05-24 18:05:51'),
(44, '6x8 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 380.00, 190.00, '[\"6x8\"]', '[\"Qty 1+: ₹380\", \"Qty 15+: ₹295\", \"Qty 25+: ₹230\", \"Qty 50+: ₹190\"]', NULL, NULL, 1, 1, '2026-05-24 18:05:51'),
(45, '8x12 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 412.00, 310.00, '[\"8x12\"]', '[\"Qty 1+: ₹412\", \"Qty 15+: ₹380\", \"Qty 25+: ₹360\", \"Qty 50+: ₹310\"]', NULL, NULL, 1, 2, '2026-05-24 18:05:51'),
(46, '12x18 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 570.00, 452.00, '[\"12x18\"]', '[\"Qty 1+: ₹570\", \"Qty 15+: ₹530\", \"Qty 25+: ₹480\", \"Qty 50+: ₹452\"]', NULL, NULL, 1, 3, '2026-05-24 18:05:51'),
(47, '12x36 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 1050.00, 750.00, '[\"12x36\"]', '[\"Qty 1+: ₹1050\", \"Qty 15+: ₹1015\", \"Qty 25+: ₹895\", \"Qty 50+: ₹750\"]', NULL, NULL, 1, 4, '2026-05-24 18:05:51'),
(48, '16x20 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 1115.00, 923.00, '[\"16x20\"]', '[\"Qty 1+: ₹1115\", \"Qty 15+: ₹1052\", \"Qty 25+: ₹1010\", \"Qty 50+: ₹923\"]', NULL, NULL, 1, 5, '2026-05-24 18:05:51'),
(49, '18x24 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 1290.00, 1050.00, '[\"18x24\"]', '[\"Qty 1+: ₹1290\", \"Qty 15+: ₹1210\", \"Qty 25+: ₹1170\", \"Qty 50+: ₹1050\"]', NULL, NULL, 1, 6, '2026-05-24 18:05:51'),
(50, '24x36 LED Frame', 'led_frame', 'Includes Panel + Guard + Adaptor. Bulk quantity discounts available.', 1910.00, 1540.00, '[\"24x36\"]', '[\"Qty 1+: ₹1910\", \"Qty 15+: ₹1830\", \"Qty 25+: ₹1650\", \"Qty 50+: ₹1540\"]', NULL, NULL, 1, 7, '2026-05-24 18:05:51'),
(51, 'Leatherism Combo Photo Pad – Furio 7 IN 1', 'combo', 'Furio (7 IN 1) – Double Door. Leather finished cover with design acrylic, 2 album storage capacity, 2x Cover photo pad, Briefcase, Leather Bag, Desktop Calendar, Acrylic Stand & Pen Drive Box.', 4500.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather Finished Cover with Design Acrylic\", \"2x Cover Photo Pad (2 Album Storage)\", \"Briefcase\", \"Leather Bag\", \"Desktop Calendar\", \"Acrylic Stand\", \"Pen Drive Box\", \"Double Door Opening\"]', 'Premium', '/images/combos/leatherism-furio-7in1.jpg', 1, 20, '2026-05-24 18:05:51'),
(52, 'Leatherism Combo Photo Pad – Furable 6 IN 1', 'combo', 'Furable (6 IN 1) – Double Door. Leather finished cover with design acrylic. Cover photo pad, Briefcase, Leather Bag, Desktop Calendar, Acrylic Stand & Led Photo Frame.', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather Finished Cover with Design Acrylic\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\", \"Desktop Calendar\", \"Acrylic Stand\", \"Led Photo Frame\", \"Double Door Opening\"]', 'Premium', '/images/combos/leatherism-furable-6in1.jpg', 1, 21, '2026-05-24 18:05:51'),
(53, 'Leatherism Combo Photo Pad – Furmax 3 IN 1', 'combo', 'Furmax (3 IN 1) – Leather finished cover with design acrylic. Cover photo pad, Briefcase & Leather Bag.', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather Finished Cover with Design Acrylic\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/leatherism-furmax-3in1.jpg', 1, 22, '2026-05-24 18:05:51'),
(54, 'Inluxury Combo Photo Pad – Lastium 5 IN 1', 'combo', 'Lastium (5 IN 1) – Leather finished cover with design acrylic. Cover photo pad, Briefcase, Leather Bag, Led Photo Frame & Desktop Calendar.', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather Finished Cover with Design Acrylic\", \"Cover Photo Pad\", \"Briefcase\", \"Leather Bag\", \"Led Photo Frame\", \"Desktop Calendar\"]', 'Premium', '/images/combos/inluxury-lastium-5in1.jpg', 1, 23, '2026-05-24 18:05:51'),
(55, 'Inluxury Combo Photo Pad – Glasster 3 IN 1', 'combo', 'Glasster (3 IN 1) – Leather finished Briefcase & cover with window acrylic. Cover photo pad, Window Acrylic Briefcase & Leather Bag.', 3800.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Leather Finished Briefcase & Cover\", \"Window Acrylic Cover Photo Pad\", \"Window Acrylic Briefcase\", \"Leather Bag\"]', NULL, '/images/combos/inluxury-glasster-3in1.jpg', 1, 24, '2026-05-24 18:05:51'),
(56, 'Superior Combo Photo Pad – Bronze Series 4 IN 1', 'combo', 'Bronze Series (4 IN 1) – Wooden finished cover with design acrylic & engraving. Cover photo pad, Leather Bag, Album Size Led Photo Frame & Wall Calendar.', 4100.00, NULL, '[\"12x24\", \"12x30\", \"12x36\", \"24x15\", \"24x18\"]', '[\"Wooden Finished Cover with Design Acrylic & Engraving\", \"Cover Photo Pad\", \"Leather Bag\", \"Album Size Led Photo Frame\", \"Wall Calendar\"]', NULL, '/images/combos/superior-bronze-4in1.jpg', 1, 25, '2026-05-24 18:05:51'),
(57, 'Leatherism Combo Photo Pad – Drawerio 3 IN 1', 'combo', 'Drawerio (3 IN 1) – Double Door. Leather finished cover with design acrylic & drawer system, 2 album storage capacity. 2x Cover photo pad & Briefcase.', 4800.00, NULL, '[\"12x24\",\"12x30\",\"12x36\",\"24x15\",\"24x18\"]', '[\"Leather Finished Cover with Design Acrylic\",\"Drawer System (2 Album Storage)\",\"2x Cover Photo Pad\",\"Briefcase\",\"Double Door Opening\"]', 'Premium', '/images/combos/combo_6a23d0c90c647.jpg', 1, 26, '2026-05-24 18:05:51'),
(100, 'Chrome Test LED Frame', 'led_frame', 'This is a test LED frame created via chrome-devtools-mcp verification.', 120.00, NULL, '[\"8x10\"]', '[\"Qty 15+: 110\",\"Qty 25+: 100\",\"Qty 50+: 90\"]', 'Chrome Test', NULL, 1, 1, '2026-05-24 18:35:50'),
(101, 'Test LED Frame', 'led_frame', 'fdgdgdg', 111.00, NULL, '[\"8x13\"]', '[\"Qty 15+: 100\",\"Qty 25+: 66\",\"Qty 50+: 55\"]', 'Special Discount', NULL, 1, 1, '2026-05-24 18:41:11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','photographer') NOT NULL DEFAULT 'photographer',
  `phone` varchar(20) DEFAULT NULL,
  `studio_name` varchar(200) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `phone`, `studio_name`, `city`, `status`, `created_at`) VALUES
(1, 'Admin', 'admin@sdcolours.com', '$2y$10$r1fPpbPkZsP6kPfuuTEuzeO4prNF2Yl8YvTzFAgwOibvOQUysCZje', 'admin', NULL, NULL, NULL, 'approved', '2026-05-22 22:15:20'),
(100, 'Rahul Photography', 'rahul@studio.in', '$2y$10$T04rQeim44KciP11oSiRW.2d2hfvaLzyKviSxcnd98ukTIiA.KutW', 'photographer', '9876543210', 'Rahul Studio Delhi', 'Delhi', 'approved', '2026-05-22 22:17:19'),
(101, 'Test Photographer', 'test@photographer.com', '$2y$10$x7wiH9.7CFbUmM9/dFU4g.B8Fj6AYT3PBehzjDhIbQohaEBcHz23K', 'photographer', '9999999999', 'Test Studio', 'Test City', 'approved', '2026-05-23 19:50:39'),
(102, 'Shankar Sharma', 'shankarsharma09@gmail.com', '$2y$10$vAt1CCKU1w2agB535DV/T.bwCWMkbO8K8GJkoJ89y6UJAVYYtrqk.', 'photographer', '8895838987', 'Studio Star Digital', 'Rourkela', 'approved', '2026-05-24 13:35:38'),
(103, 'Chrome Test Photographer', 'chrometest@photographer.com', '$2y$10$Bd277hzwZHPKUT6afrt0LuTfzwcEn1buOyasGhKuhwCPEzkiK/ZKW', 'photographer', '9988776655', 'Chrome Photo Studio', 'Mumbai', 'approved', '2026-05-24 18:37:41'),
(104, 'Test Photographer', 'photo@test.com', '$2y$10$vTJWuVN8p3pHVF.dqdE.C.PCY.vwtbUJk/4f82UNrTUO9l5FTrxMi', 'photographer', '123123', 'Test Studio', 'City', 'approved', '2026-05-25 02:05:59'),
(105, 'Bandhu Tirkey', 'karmatirkey84@gmail.com', '$2y$10$WRpgoGYmu/PB0uLDuYEyN.n6FioLiGVkI/nI58bd5RgdW8I87UaMq', 'photographer', '8763907079', 'BandhulovesSanjukta', 'Rourkela Sundargarh Odisha', 'pending', '2026-05-25 05:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `label` varchar(150) NOT NULL,
  `address_line` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` varchar(20) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `label`, `address_line`, `city`, `state`, `pincode`, `phone`, `created_at`) VALUES
(1, 103, 'Chrome Studio', '123 Chrome Street, Tech Park', 'Mumbai', 'Odisha', '400001', '9988776655', '2026-05-24 19:03:13'),
(2, 102, 'Jhirpani', 'd/520', 'Rourkela', 'Odisha', '769001', '8895838987', '2026-06-06 07:53:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `fk_tokens_user` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`photographer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_order` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_addr_user` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_tokens`
--
ALTER TABLE `api_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `api_tokens`
--
ALTER TABLE `api_tokens`
  ADD CONSTRAINT `fk_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`photographer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
