-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 28, 2025 at 03:02 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel_angular_api`
--
CREATE DATABASE IF NOT EXISTS `laravel_angular_api` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `laravel_angular_api`;

-- --------------------------------------------------------

--
-- Table structure for table `business_units`
--

CREATE TABLE `business_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_unit` varchar(255) NOT NULL,
  `active` enum('y','n') NOT NULL DEFAULT 'y',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_units`
--

INSERT INTO `business_units` (`id`, `business_unit`, `active`, `created_at`, `updated_at`) VALUES
(1, 'Batam', 'y', '2025-11-21 05:36:19', '2025-11-28 07:00:11'),
(2, 'Jakarta', 'y', '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(3, 'Surabaya', 'y', '2025-11-21 05:36:19', '2025-11-21 06:30:04');

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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `business_unit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `address`, `business_unit_id`, `created_at`, `updated_at`) VALUES
(4, 'PT Global Jakarta', 'global@jakarta.com', '021-123456', 'Jl. Sudirman No. 100 Jakarta', 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(5, 'CV Mandiri Jakarta', 'mandiri@jakarta.com', '021-234567', 'Jl. Thamrin No. 50 Jakarta', 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(6, 'Toko Buku Jakarta', 'buku@jakarta.com', '021-345678', 'Jl. Cikini No. 20 Jakarta', 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(10, 'skdjsdkf', 'skfksmn2@gmail.com', '029409', 'kskfsmdf', 2, '2025-11-21 06:27:45', '2025-11-21 06:27:45'),
(43, 'Customer Bulky 1', 'bulky1@example.com', '087755554301', 'Jalan Otomasi Cepat No. 1, Unit Surabaya', 3, '2025-11-28 05:56:19', '2025-11-28 05:56:19'),
(44, 'Customer Bulky 2', 'bulky2@example.com', '087755554302', 'Jalan Otomasi Cepat No. 2, Unit Surabaya', 3, '2025-11-28 05:56:19', '2025-11-28 05:56:19'),
(45, 'Customer Bulky 3', 'bulky3@example.com', '087755554303', 'Jalan Otomasi Cepat No. 3, Unit Surabaya', 3, '2025-11-28 05:56:20', '2025-11-28 05:56:20'),
(46, 'Customer Bulky 4', 'bulky4@example.com', '087755554304', 'Jalan Otomasi Cepat No. 4, Unit Surabaya', 3, '2025-11-28 05:56:21', '2025-11-28 05:56:21'),
(47, 'Customer Bulky 5', 'bulky5@example.com', '087755554305', 'Jalan Otomasi Cepat No. 5, Unit Surabaya', 3, '2025-11-28 05:56:22', '2025-11-28 05:56:22'),
(48, 'Customer Bulky 6', 'bulky6@example.com', '087755554306', 'Jalan Otomasi Cepat No. 6, Unit Surabaya', 3, '2025-11-28 05:56:23', '2025-11-28 05:56:23'),
(49, 'Customer Bulky 7', 'bulky7@example.com', '087755554307', 'Jalan Otomasi Cepat No. 7, Unit Surabaya', 3, '2025-11-28 05:56:23', '2025-11-28 05:56:23'),
(50, 'Customer Bulky 8', 'bulky8@example.com', '087755554308', 'Jalan Otomasi Cepat No. 8, Unit Surabaya', 3, '2025-11-28 05:56:24', '2025-11-28 05:56:24'),
(51, 'Customer Bulky 9', 'bulky9@example.com', '087755554309', 'Jalan Otomasi Cepat No. 9, Unit Surabaya', 3, '2025-11-28 05:56:25', '2025-11-28 05:56:25'),
(52, 'Customer Bulky 10', 'bulky10@example.com', '087755554310', 'Jalan Otomasi Cepat No. 10, Unit Surabaya', 3, '2025-11-28 05:56:26', '2025-11-28 05:56:26'),
(53, 'Customer Bulky 11', 'bulky11@example.com', '087755554311', 'Jalan Otomasi Cepat No. 11, Unit Surabaya', 3, '2025-11-28 05:56:27', '2025-11-28 05:56:27'),
(54, 'Customer Bulky 12', 'bulky12@example.com', '087755554312', 'Jalan Otomasi Cepat No. 12, Unit Surabaya', 3, '2025-11-28 05:56:27', '2025-11-28 05:56:27'),
(55, 'Customer Bulky 13', 'bulky13@example.com', '087755554313', 'Jalan Otomasi Cepat No. 13, Unit Surabaya', 3, '2025-11-28 05:56:28', '2025-11-28 05:56:28'),
(56, 'Customer Bulky 14', 'bulky14@example.com', '087755554314', 'Jalan Otomasi Cepat No. 14, Unit Surabaya', 3, '2025-11-28 05:56:29', '2025-11-28 05:56:29'),
(57, 'Customer Bulky 15', 'bulky15@example.com', '087755554315', 'Jalan Otomasi Cepat No. 15, Unit Surabaya', 3, '2025-11-28 05:56:30', '2025-11-28 05:56:30'),
(58, 'Customer Bulky 16', 'bulky16@example.com', '087755554316', 'Jalan Otomasi Cepat No. 16, Unit Surabaya', 3, '2025-11-28 05:56:31', '2025-11-28 05:56:31'),
(59, 'Customer Bulky 17', 'bulky17@example.com', '087755554317', 'Jalan Otomasi Cepat No. 17, Unit Surabaya', 3, '2025-11-28 05:56:31', '2025-11-28 05:56:31'),
(60, 'Customer Bulky 18', 'bulky18@example.com', '087755554318', 'Jalan Otomasi Cepat No. 18, Unit Surabaya', 3, '2025-11-28 05:56:32', '2025-11-28 05:56:32'),
(61, 'Customer Bulky 19', 'bulky19@example.com', '087755554319', 'Jalan Otomasi Cepat No. 19, Unit Surabaya', 3, '2025-11-28 05:56:33', '2025-11-28 05:56:33'),
(62, 'Customer Bulky 20', 'bulky20@example.com', '087755554320', 'Jalan Otomasi Cepat No. 20, Unit Surabaya', 3, '2025-11-28 05:56:34', '2025-11-28 05:56:34'),
(64, 'Customer Bulky 22', 'bulky22@example.com', '087755554322', 'Jalan Otomasi Cepat No. 22, Unit Surabaya', 3, '2025-11-28 05:56:35', '2025-11-28 05:56:35'),
(65, 'Customer Bulky 23', 'bulky23@example.com', '087755554323', 'Jalan Otomasi Cepat No. 23, Unit Surabaya', 3, '2025-11-28 05:56:36', '2025-11-28 05:56:36'),
(66, 'Customer Bulky 24', 'bulky24@example.com', '087755554324', 'Jalan Otomasi Cepat No. 24, Unit Surabaya', 3, '2025-11-28 05:56:37', '2025-11-28 05:56:37'),
(67, 'Customer Bulky 25', 'bulky25@example.com', '087755554325', 'Jalan Otomasi Cepat No. 25, Unit Surabaya', 3, '2025-11-28 05:56:38', '2025-11-28 05:56:38'),
(68, 'Customer Bulky 26', 'bulky26@example.com', '087755554326', 'Jalan Otomasi Cepat No. 26, Unit Surabaya', 3, '2025-11-28 05:56:39', '2025-11-28 05:56:39'),
(69, 'Customer Bulky 27', 'bulky27@example.com', '087755554327', 'Jalan Otomasi Cepat No. 27, Unit Surabaya', 3, '2025-11-28 05:56:39', '2025-11-28 05:56:39'),
(70, 'Customer Bulky 28', 'bulky28@example.com', '087755554328', 'Jalan Otomasi Cepat No. 28, Unit Surabaya', 3, '2025-11-28 05:56:40', '2025-11-28 05:56:40'),
(73, 'Customer Batam 1', 'batam.bulky1@example.com', '085600001001', 'Komp. Ruko Batam Center No. 1, Unit Batam', 1, '2025-11-28 06:00:01', '2025-11-28 06:00:01'),
(74, 'Customer Batam 2', 'batam.bulky2@example.com', '085600001002', 'Komp. Ruko Batam Center No. 2, Unit Batam', 1, '2025-11-28 06:00:02', '2025-11-28 06:00:02'),
(75, 'Customer Batam 3', 'batam.bulky3@example.com', '085600001003', 'Komp. Ruko Batam Center No. 3, Unit Batam', 1, '2025-11-28 06:00:03', '2025-11-28 06:00:03'),
(76, 'Customer Batam 4', 'batam.bulky4@example.com', '085600001004', 'Komp. Ruko Batam Center No. 4, Unit Batam', 1, '2025-11-28 06:00:03', '2025-11-28 06:00:03'),
(77, 'Customer Batam 5', 'batam.bulky5@example.com', '085600001005', 'Komp. Ruko Batam Center No. 5, Unit Batam', 1, '2025-11-28 06:00:04', '2025-11-28 06:00:04'),
(78, 'Customer Batam 6', 'batam.bulky6@example.com', '085600001006', 'Komp. Ruko Batam Center No. 6, Unit Batam', 1, '2025-11-28 06:00:05', '2025-11-28 06:00:05'),
(79, 'Customer Batam 7', 'batam.bulky7@example.com', '085600001007', 'Komp. Ruko Batam Center No. 7, Unit Batam', 1, '2025-11-28 06:00:06', '2025-11-28 06:00:06'),
(80, 'Customer Batam 8', 'batam.bulky8@example.com', '085600001008', 'Komp. Ruko Batam Center No. 8, Unit Batam', 1, '2025-11-28 06:00:07', '2025-11-28 06:00:07'),
(81, 'Customer Batam 9', 'batam.bulky9@example.com', '085600001009', 'Komp. Ruko Batam Center No. 9, Unit Batam', 1, '2025-11-28 06:00:07', '2025-11-28 06:00:07'),
(82, 'Customer Batam 10', 'batam.bulky10@example.com', '085600001010', 'Komp. Ruko Batam Center No. 10, Unit Batam', 1, '2025-11-28 06:00:08', '2025-11-28 06:00:08'),
(83, 'Customer Batam 11', 'batam.bulky11@example.com', '085600001011', 'Komp. Ruko Batam Center No. 11, Unit Batam', 1, '2025-11-28 06:00:09', '2025-11-28 06:00:09'),
(84, 'Customer Batam 12', 'batam.bulky12@example.com', '085600001012', 'Komp. Ruko Batam Center No. 12, Unit Batam', 1, '2025-11-28 06:00:10', '2025-11-28 06:00:10'),
(85, 'Customer Batam 13', 'batam.bulky13@example.com', '085600001013', 'Komp. Ruko Batam Center No. 13, Unit Batam', 1, '2025-11-28 06:00:11', '2025-11-28 06:00:11'),
(86, 'Customer Batam 14', 'batam.bulky14@example.com', '085600001014', 'Komp. Ruko Batam Center No. 14, Unit Batam', 1, '2025-11-28 06:00:11', '2025-11-28 06:00:11'),
(87, 'Customer Batam 15', 'batam.bulky15@example.com', '085600001015', 'Komp. Ruko Batam Center No. 15, Unit Batam', 1, '2025-11-28 06:00:12', '2025-11-28 06:00:12'),
(88, 'Customer Batam 16', 'batam.bulky16@example.com', '085600001016', 'Komp. Ruko Batam Center No. 16, Unit Batam', 1, '2025-11-28 06:00:13', '2025-11-28 06:00:13'),
(89, 'Customer Batam 17', 'batam.bulky17@example.com', '085600001017', 'Komp. Ruko Batam Center No. 17, Unit Batam', 1, '2025-11-28 06:00:14', '2025-11-28 06:00:14'),
(90, 'Customer Batam 18', 'batam.bulky18@example.com', '085600001018', 'Komp. Ruko Batam Center No. 18, Unit Batam', 1, '2025-11-28 06:00:15', '2025-11-28 06:00:15'),
(91, 'Customer Batam 19', 'batam.bulky19@example.com', '085600001019', 'Komp. Ruko Batam Center No. 19, Unit Batam', 1, '2025-11-28 06:00:15', '2025-11-28 06:00:15'),
(92, 'Customer Batam 20', 'batam.bulky20@example.com', '085600001020', 'Komp. Ruko Batam Center No. 20, Unit Batam', 1, '2025-11-28 06:00:16', '2025-11-28 06:00:16'),
(93, 'Customer Batam 21', 'batam.bulky21@example.com', '085600001021', 'Komp. Ruko Batam Center No. 21, Unit Batam', 1, '2025-11-28 06:00:17', '2025-11-28 06:00:17'),
(94, 'Customer Batam 22', 'batam.bulky22@example.com', '085600001022', 'Komp. Ruko Batam Center No. 22, Unit Batam', 1, '2025-11-28 06:00:18', '2025-11-28 06:00:18'),
(95, 'Customer Batam 23', 'batam.bulky23@example.com', '085600001023', 'Komp. Ruko Batam Center No. 23, Unit Batam', 1, '2025-11-28 06:00:19', '2025-11-28 06:00:19'),
(96, 'Customer Batam 24', 'batam.bulky24@example.com', '085600001024', 'Komp. Ruko Batam Center No. 24, Unit Batam', 1, '2025-11-28 06:00:19', '2025-11-28 06:00:19'),
(97, 'Customer Batam 25', 'batam.bulky25@example.com', '085600001025', 'Komp. Ruko Batam Center No. 25, Unit Batam', 1, '2025-11-28 06:00:20', '2025-11-28 06:00:20'),
(98, 'Customer Batam 26', 'batam.bulky26@example.com', '085600001026', 'Komp. Ruko Batam Center No. 26, Unit Batam', 1, '2025-11-28 06:00:21', '2025-11-28 06:00:21'),
(99, 'Customer Batam 27', 'batam.bulky27@example.com', '085600001027', 'Komp. Ruko Batam Center No. 27, Unit Batam', 1, '2025-11-28 06:00:22', '2025-11-28 06:00:22'),
(100, 'Customer Batam 28', 'batam.bulky28@example.com', '085600001028', 'Komp. Ruko Batam Center No. 28, Unit Batam', 1, '2025-11-28 06:00:23', '2025-11-28 06:00:23'),
(101, 'Customer Batam 29', 'batam.bulky29@example.com', '085600001029', 'Komp. Ruko Batam Center No. 29, Unit Batam', 1, '2025-11-28 06:00:23', '2025-11-28 06:00:23'),
(103, 'A', 'a@gmail.com', '1', 'aoskokaosd', 1, '2025-11-28 06:04:35', '2025-11-28 06:04:35'),
(104, 'Andrew', 'andrew@gmail.com', '102930192', 'Akjksadkaj', 3, '2025-11-28 06:51:49', '2025-11-28 06:51:49');

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
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_menu` varchar(255) NOT NULL,
  `url_link` varchar(255) NOT NULL,
  `parent` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `nama_menu`, `url_link`, `parent`, `created_at`, `updated_at`) VALUES
(1, 'Dashboard', '/dashboard', NULL, '2025-11-21 05:36:19', '2025-11-28 06:50:28'),
(2, 'Customers', '/customers', NULL, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(3, 'Master Data', '/master', NULL, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(4, 'Users', '/users', 3, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(5, 'Business Units', '/business-units', 3, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(6, 'Menus', '/menus', 3, '2025-11-21 05:36:19', '2025-11-21 06:26:37');

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
(4, '2025_10_31_125004_create_personal_access_tokens_table', 1),
(5, '2025_10_31_125151_create_menus_table', 1),
(6, '2025_10_31_125201_create_privilege_users_table', 1),
(7, '2025_10_31_125202_create_business_units_table', 1),
(8, '2025_11_07_123314_add_allowed_column_to_privilege_users_table', 1),
(9, '2025_11_07_123335_create_transaksis_table', 1),
(10, '2025_11_07_134201_remove_user_id_from_business_units_table', 1),
(11, '2025_11_07_134239_add_business_unit_id_to_personal_access_tokens_table', 1),
(12, '2025_11_14_121619_add_user_id_back_to_business_units_table', 1),
(13, '2025_11_14_121623_drop_privilege_users_table', 1),
(14, '2025_11_14_121624_create_customers_table', 1),
(15, '2025_11_14_121624_drop_menus_table', 1),
(16, '2025_11_14_121624_drop_privilege_users_table', 1),
(17, '2025_11_14_121624_drop_transaksis_table', 1),
(18, '2025_11_14_134105_create_menus_table_v4', 1),
(19, '2025_11_14_134110_create_user_business_units_table', 1),
(20, '2025_11_14_134112_create_user_menus_table', 1),
(21, '2025_11_14_134113_remove_user_id_from_business_units_v4', 1);

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
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `business_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `business_unit_id`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(43, 'App\\Models\\User', 1, 'auth-token', '13113ee54e956df7ec83a9f06ab829f9d0fd06f05b82bfd900672aad4399cc6f', '[\"*\"]', NULL, '2025-11-28 07:00:26', NULL, '2025-11-28 06:55:17', '2025-11-28 07:00:26');

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

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` varchar(255) NOT NULL DEFAULT 'user',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `level`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$xJeNlz1V83wZgEY9a9x6Luc7ziXAQv3gyBUoKX6QQ1agxSzsJ.zF2', 'admin', 1, NULL, '2025-11-21 05:36:18', '2025-11-21 05:36:18'),
(2, 'user1', '$2y$12$frlbULDIKZrmYeki64xQM.fAYXz/KnDyTt37SDjAR2N/V1UfWjkNC', 'user', 1, NULL, '2025-11-21 05:36:19', '2025-11-21 06:30:55'),
(3, 'user2', '$2y$12$Qg02wS5nGtcKxzRLsCAbXO1DgWjdx4ZNKwsnTKgoUPVbJWzARmzuS', 'user', 1, NULL, '2025-11-21 05:36:19', '2025-11-21 07:22:13');

-- --------------------------------------------------------

--
-- Table structure for table `user_business_units`
--

CREATE TABLE `user_business_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `business_unit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_business_units`
--

INSERT INTO `user_business_units` (`id`, `user_id`, `business_unit_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(2, 1, 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(3, 1, 3, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(4, 2, 1, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(5, 2, 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(6, 3, 3, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(8, 3, 2, '2025-11-28 04:38:10', '2025-11-28 04:38:10'),
(15, 2, 3, '2025-11-28 06:52:42', '2025-11-28 06:52:42');

-- --------------------------------------------------------

--
-- Table structure for table `user_menus`
--

CREATE TABLE `user_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_menus`
--

INSERT INTO `user_menus` (`id`, `user_id`, `menu_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(2, 1, 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(3, 1, 3, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(4, 1, 4, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(5, 1, 5, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(6, 1, 6, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(8, 2, 1, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(9, 2, 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(11, 3, 1, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(12, 3, 2, '2025-11-21 05:36:19', '2025-11-21 05:36:19'),
(17, 2, 3, '2025-11-28 06:52:42', '2025-11-28 06:52:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_units`
--
ALTER TABLE `business_units`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD KEY `customers_business_unit_id_foreign` (`business_unit_id`);

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
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menus_parent_foreign` (`parent`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`),
  ADD KEY `personal_access_tokens_business_unit_id_foreign` (`business_unit_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `user_business_units`
--
ALTER TABLE `user_business_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_business_units_user_id_business_unit_id_unique` (`user_id`,`business_unit_id`),
  ADD KEY `user_business_units_business_unit_id_foreign` (`business_unit_id`);

--
-- Indexes for table `user_menus`
--
ALTER TABLE `user_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_menus_user_id_menu_id_unique` (`user_id`,`menu_id`),
  ADD KEY `user_menus_menu_id_foreign` (`menu_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_units`
--
ALTER TABLE `business_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

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
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_business_units`
--
ALTER TABLE `user_business_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_menus`
--
ALTER TABLE `user_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_parent_foreign` FOREIGN KEY (`parent`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD CONSTRAINT `personal_access_tokens_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_business_units`
--
ALTER TABLE `user_business_units`
  ADD CONSTRAINT `user_business_units_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_business_units_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_menus`
--
ALTER TABLE `user_menus`
  ADD CONSTRAINT `user_menus_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_menus_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
