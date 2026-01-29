-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2026 at 12:48 AM
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
-- Database: `elimo_real_estate`
--

-- --------------------------------------------------------

--
-- Table structure for table `blog_posts`
--

CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `excerpt` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'creative',
  `image` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `author_id` int(11) DEFAULT NULL,
  `status` enum('published','draft') DEFAULT 'published',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `video` varchar(255) DEFAULT NULL,
  `youtube_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `first_name`, `last_name`, `email`, `phone`, `message`, `status`, `created_at`) VALUES
(1, '', '', 'ghislain82@gmail.com', '0781262526', 'Testing', 'read', '2026-01-12 12:55:14'),
(2, 'ISHIMWE', 'GHISLAIN', 'ghislain82@gmail.com', '0781262526', 'Hey', 'read', '2026-01-13 11:13:54'),
(3, 'ISHIMWE', 'GHISLAIN', 'ghislain82@gmail.com', '0781262526', 'Hey', 'read', '2026-01-13 11:14:31'),
(4, 'ISHIMWE', 'GHISLAIN', 'ghislain82@gmail.com', '0781262526', 'Testing', 'read', '2026-01-13 11:27:39'),
(5, 'ISHIMWE', 'GHISLAIN', 'ghislain82@gmail.com', '0781262526', 'hi', 'read', '2026-01-13 12:04:35'),
(6, 'Testing', 'Contacts', 'ghislainishimwe22@gmail.com', '0781262526', 'Hello my friends', 'new', '2026-01-14 08:31:03'),
(7, 'Kwizera', 'Frank', 'linda@gmail.com', '0781262526', 'Hello', 'new', '2026-01-14 09:20:35'),
(8, 'Still Testing', 'Stop', 'ghislainishimwe22@gmail.com', '0788991187', 'Stephen Curry', 'new', '2026-01-14 09:33:06'),
(9, 'ISHIMWE', 'GHISLAIN', 'ishimweghislain82@gmail.com', '0781262526', 'Testing toast messages', 'read', '2026-01-14 11:25:53');

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL,
  `category` enum('selling','renting','developments','general') DEFAULT 'general',
  `order_index` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqs`
--

INSERT INTO `faqs` (`id`, `question`, `answer`, `category`, `order_index`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'How can we help?', 'Elimo Real Estate provides comprehensive property management, valuation, and consulting services to help you find your perfect property in Rwanda.', 'renting', 0, 1, '2026-01-12 11:50:26', '2026-01-13 10:08:01'),
(3, 'Do you store any of my information?', 'We only store necessary information required to provide our services. Your data is protected according to our privacy policy.', 'developments', 0, 1, '2026-01-12 11:50:26', '2026-01-13 10:08:18');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `is_active`, `created_at`) VALUES
(3, 'ghislainishimwe22@gmail.com', 1, '2026-01-14 11:25:02');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `prop_id` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('Residential','Commercial','Developments','Land') NOT NULL,
  `property_type` enum('Apartment','House','Townhouse','Semi Detached','Office','Retail','Industrial','Land') NOT NULL,
  `status` enum('for-rent','for-sale','under-construction','sold','rented','draft') DEFAULT 'for-rent',
  `price` decimal(12,2) DEFAULT NULL,
  `location` varchar(100) NOT NULL,
  `province` varchar(50) DEFAULT NULL,
  `district` varchar(50) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT NULL,
  `bathrooms` int(11) DEFAULT NULL,
  `garage` int(11) DEFAULT 0,
  `size_sqm` decimal(8,2) DEFAULT NULL,
  `plot_size` decimal(10,2) DEFAULT NULL,
  `zoning` varchar(100) DEFAULT NULL,
  `views` varchar(100) DEFAULT NULL,
  `ideal_for` varchar(255) DEFAULT NULL,
  `proximity` text DEFAULT NULL,
  `year_built` int(11) DEFAULT NULL,
  `stories` int(11) DEFAULT NULL,
  `furnished` varchar(50) DEFAULT NULL,
  `multi_family` varchar(10) DEFAULT NULL,
  `image_main` varchar(255) DEFAULT NULL,
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `youtube_url` varchar(255) DEFAULT NULL,
  `instagram_url` varchar(255) DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `prop_id`, `title`, `description`, `category`, `property_type`, `status`, `price`, `location`, `province`, `district`, `bedrooms`, `bathrooms`, `garage`, `size_sqm`, `plot_size`, `zoning`, `views`, `ideal_for`, `proximity`, `year_built`, `stories`, `furnished`, `multi_family`, `image_main`, `images`, `features`, `amenities`, `is_featured`, `created_at`, `updated_at`, `youtube_url`, `instagram_url`, `video`) VALUES
(33, 'P2920', 'Luxury House in Lando', 'This property is the best for a couple', 'Residential', 'Apartment', 'for-sale', 600000.00, 'Kiyovu, Kigali', 'Kigali City', 'Nyarugenge', 4, 2, 1, 1000.00, 100.00, 'Nothing', 'City', 'Single Person', 'who knows', 2022, 5, 'Fully Furnished', 'Yes', '6966ff6e372cc_4.jpg', '[\"696818c31bef2_8.jpg\",\"696818daa7e88_8.jpg\",\"696818daa8477_7.jpg\",\"696818daa88e5_5.jpg\",\"696818daa8d87_4.jpg\"]', '[\"Air Conditioner\",\"Optic Fiber\",\"Built in wardrobes\",\"Proximity to schools\",\"Tarmac road\",\"Proximity to shops\",\"Proximity to public transport\",\"Water Tank\",\"Garden\",\"Open Plan Kitchen\"]', '[\"Cleaning services\",\"Laundry\",\"Garbage collection\",\"Security\"]', 1, '2026-01-13 13:28:12', '2026-01-15 10:09:51', 'https://www.youtube.com/watch?v=823LWuBFPJw&amp;amp;list=RDMM&amp;amp;index=4', '', '696973bfbf9ff_BAyra_Starr_-_Commas__Lyric_Video_.mp4'),
(34, 'P2920', 'Texas House', '', 'Developments', 'Apartment', 'for-rent', 100000.00, 'Kiyovu, Kigali', 'Kigali City', 'Nyarugenge', 0, 0, 0, 0.00, 0.00, 'Nothing', 'City', 'Single Person', '', 2026, 1, '', '', '6969639fb1a2a_5.jpg', '[\"696818542fe62_6.jpg\"]', '[]', '[]', 1, '2026-01-13 14:21:45', '2026-01-15 09:01:03', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `property_inquiries`
--

CREATE TABLE `property_inquiries` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('new','contacted','closed') DEFAULT 'new',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_inquiries`
--

INSERT INTO `property_inquiries` (`id`, `property_id`, `name`, `email`, `phone`, `message`, `status`, `created_at`) VALUES
(8, 33, 'ISHIMWE GHISLAIN', 'ishimweghislain82@gmail.com', NULL, 'I like this', 'new', '2026-01-13 14:18:34'),
(9, 34, 'Testing Inquiry', 'ghislainishimwe22@gmail.com', NULL, 'comeon', 'new', '2026-01-14 09:37:22'),
(10, 34, 'Testing Toasts messages', 'linda@gmail.com', NULL, 'Hello', 'new', '2026-01-14 09:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `description`, `updated_at`) VALUES
(1, 'site_name', 'Elimo Real Estate', 'Website name', '2026-01-12 11:50:26'),
(2, 'site_description', 'Your trusted resourceful companion on your real estate journey', 'Site description', '2026-01-12 11:50:26'),
(3, 'contact_email', 'info@elimo.rw', 'Contact email', '2026-01-12 11:50:26'),
(4, 'contact_phone', '0781262526', 'Contact phone', '2026-01-13 07:44:30'),
(5, 'contact_address', 'KG 622, Street 19 P.O. BOX 4566 Rugando - Kigali Rwanda', 'Contact address', '2026-01-12 11:50:26');

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `social_links` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`social_links`)),
  `listed_properties` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`id`, `name`, `position`, `email`, `phone`, `bio`, `image`, `social_links`, `listed_properties`, `is_active`, `created_at`, `updated_at`) VALUES
(7, 'Isimbi Sonia', 'Prequisite', 'ishimweghislain82@gmail.com', '0781262526', 'Nothing much im a hustler', '6966b945a7204_mama.png', NULL, 8, 1, '2026-01-13 07:49:33', '2026-01-13 08:29:41'),
(8, 'ISHIMWE GHISLAIN', 'Coporal Agent', 'comeon@gmail.com', '+250790739050', 'Biography', '6966b8b54dfad_fam.jpeg', '{\"twitter\":\"\",\"facebook\":\"\",\"linkedin\":\"https:\\/\\/www.linkedin.com\\/in\\/ishimwe-ghislain-b26ba6288\\/\"}', 5, 1, '2026-01-13 07:55:54', '2026-01-14 10:01:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `role`, `phone`, `created_at`, `updated_at`) VALUES
(2, 'admin', 'admin@elimo.rw', '$2y$10$JKqLuyu/GPlqS3BDLB2AreVuU97P8rl0Di/4OZ659rv11bgme/Fim', 'Administrator', 'admin', NULL, '2026-01-12 12:17:09', '2026-01-12 12:17:09');
CREATE TABLE IF NOT EXISTS `property_features_master` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `type` enum('feature','amenity') NOT NULL DEFAULT 'feature',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_name_type` (`name`, `type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert default features
INSERT INTO `property_features_master` (`name`, `type`, `is_active`) VALUES
('Air Conditioner', 'feature', 1),
('Optic Fiber', 'feature', 1),
('Built in wardrobes', 'feature', 1),
('Proximity to schools', 'feature', 1),
('Tarmac road', 'feature', 1),
('Proximity to shops', 'feature', 1),
('Proximity to public transport', 'feature', 1),
('Water Tank', 'feature', 1),
('Garden', 'feature', 1),
('Open Plan Kitchen', 'feature', 1);

-- Insert default amenities
INSERT INTO `property_features_master` (`name`, `type`, `is_active`) VALUES
('Cleaning services', 'amenity', 1),
('Laundry', 'amenity', 1),
('Garbage collection', 'amenity', 1),
('Security', 'amenity', 1);
--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `property_inquiries`
--
ALTER TABLE `property_inquiries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_posts`
--
ALTER TABLE `blog_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `property_inquiries`
--
ALTER TABLE `property_inquiries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_posts`
--
ALTER TABLE `blog_posts`
  ADD CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `property_inquiries`
--
ALTER TABLE `property_inquiries`
  ADD CONSTRAINT `property_inquiries_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
