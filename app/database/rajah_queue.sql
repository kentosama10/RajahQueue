-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2025 at 09:23 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rajah_queue`
--

-- --------------------------------------------------------

--
-- Table structure for table `counters`
--

CREATE TABLE `counters` (
  `id` int(11) NOT NULL,
  `counter_number` int(11) NOT NULL,
  `active_user_id` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `counters`
--

INSERT INTO `counters` (`id`, `counter_number`, `active_user_id`, `updated_at`) VALUES
(1, 1, 10, '2025-01-24 08:12:13'),
(2, 2, NULL, '2025-01-24 08:00:26'),
(3, 3, NULL, '2025-01-24 04:47:41'),
(4, 4, NULL, '2025-01-24 06:39:23'),
(5, 5, 13, '2025-01-24 07:41:10'),
(6, 6, NULL, '2025-01-24 06:39:36'),
(7, 7, NULL, '2025-01-24 04:44:25'),
(8, 8, NULL, '2025-01-24 06:39:41'),
(9, 9, NULL, '2025-01-15 09:39:04'),
(10, 10, NULL, '2025-01-22 03:36:42');

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

CREATE TABLE `queue` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `region` varchar(50) DEFAULT NULL,
  `priority` enum('Yes','No') NOT NULL DEFAULT 'No',
  `priority_type` enum('PWD','Pregnant','Senior Citizen') DEFAULT NULL,
  `queue_number` varchar(10) NOT NULL,
  `status` enum('Waiting','Serving','No Show','Recalled','Skipped','Done') DEFAULT 'Waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payment_status` enum('Pending','Cancelled','Completed','Not Required') DEFAULT 'Not Required',
  `reset_flag` tinyint(1) DEFAULT 0,
  `serving_user_id` int(11) DEFAULT NULL,
  `completed_by_user_id` int(11) DEFAULT NULL,
  `payment_completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue`
--

INSERT INTO `queue` (`id`, `customer_name`, `service_type`, `region`, `priority`, `priority_type`, `queue_number`, `status`, `created_at`, `updated_at`, `payment_status`, `reset_flag`, `serving_user_id`, `completed_by_user_id`, `payment_completed_at`) VALUES
(315, 'test', 'Visa', NULL, 'No', NULL, 'V-1', 'Skipped', '2025-01-23 00:54:04', '2025-01-24 01:44:46', 'Not Required', 1, 10, NULL, NULL),
(316, 'test2', 'Tours / Cruise', NULL, 'No', NULL, 'T-1', 'Done', '2025-01-23 00:54:09', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 01:54:02'),
(317, 'test3', 'Flights', NULL, 'No', NULL, 'F-1', 'Done', '2025-01-23 00:54:15', '2025-01-24 01:44:46', 'Not Required', 1, 10, NULL, NULL),
(318, 'test4', 'Travel Insurance', NULL, 'No', NULL, 'I-1', 'Done', '2025-01-23 00:54:20', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 01:54:14'),
(319, 'test5', 'Multiple Services', NULL, 'No', NULL, 'M-1', 'Done', '2025-01-23 00:54:26', '2025-01-24 01:44:46', 'Not Required', 1, 10, NULL, NULL),
(320, 'test123', 'Multiple Services', NULL, 'No', NULL, 'M-2', 'Done', '2025-01-23 02:14:00', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 03:18:06'),
(321, 'test', 'Visa', NULL, 'No', NULL, 'V-2', 'Done', '2025-01-23 02:17:05', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 03:34:08'),
(322, 'test123', 'Travel Insurance', NULL, 'No', NULL, 'I-2', 'Done', '2025-01-23 03:23:39', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 03:24:45'),
(323, 'test123', 'Flights', NULL, 'No', NULL, 'F-2', 'Done', '2025-01-23 03:30:19', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 03:35:22'),
(324, 'test', 'Tours / Cruise', NULL, 'No', NULL, 'T-2', 'Done', '2025-01-23 03:44:20', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 03:46:36'),
(325, 'test2', 'Tours / Cruise', NULL, 'No', NULL, 'T-3', 'Done', '2025-01-23 03:45:13', '2025-01-24 01:44:46', 'Not Required', 1, 10, NULL, NULL),
(326, 'test123123', 'Visa', NULL, 'No', NULL, 'V-3', 'Done', '2025-01-23 03:47:46', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 04:02:07'),
(327, 'eqwe', 'Tours / Cruise', NULL, 'No', NULL, 'T-4', 'Done', '2025-01-23 03:48:05', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 05:44:36'),
(328, 'wewe', 'Multiple Services', NULL, 'No', NULL, 'M-3', 'Done', '2025-01-23 03:48:20', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 05:45:06'),
(329, '12323', 'Flights', NULL, 'No', NULL, 'F-3', 'Done', '2025-01-23 03:51:37', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 05:45:07'),
(330, 'tse123', 'Visa', NULL, 'No', NULL, 'V-4', 'Done', '2025-01-23 03:59:36', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 05:45:09'),
(331, '123123', 'Visa', NULL, 'No', NULL, 'V-5', 'Done', '2025-01-23 03:59:39', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 05:45:58'),
(332, '213123', 'Tours / Cruise', NULL, 'No', NULL, 'T-5', 'Done', '2025-01-23 04:06:34', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 05:46:00'),
(333, 'asdsad', 'Travel Insurance', NULL, 'No', NULL, 'I-3', 'Done', '2025-01-23 04:06:37', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 08:08:37'),
(334, 'fdgfdgfd', 'Visa', NULL, 'No', NULL, 'V-6', 'Done', '2025-01-23 04:06:40', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 08:08:39'),
(335, 'test7', 'Visa', NULL, 'No', NULL, 'V-7', 'Done', '2025-01-23 04:29:13', '2025-01-24 01:44:46', 'Completed', 1, 10, 10, '2025-01-23 08:08:40'),
(336, 'test', 'Visa', NULL, 'No', NULL, 'V-1', 'Done', '2025-01-24 01:44:46', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(337, 'test', 'Tours / Cruise', NULL, 'No', NULL, 'T-1', 'Done', '2025-01-24 01:47:48', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 04:58:20'),
(338, 'test1', 'Visa', NULL, 'No', NULL, 'V-2', 'Done', '2025-01-24 01:57:51', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 04:58:25'),
(339, 'test2', 'Flights', NULL, 'No', NULL, 'F-1', 'Done', '2025-01-24 01:57:55', '2025-01-24 08:11:39', 'Completed', 1, 10, 13, '2025-01-24 04:56:22'),
(340, 'testt3', 'Tours / Cruise', NULL, 'No', NULL, 'T-2', 'Done', '2025-01-24 01:57:59', '2025-01-24 08:11:39', 'Cancelled', 1, 13, 13, '2025-01-24 04:58:28'),
(341, 'test5', 'Visa', NULL, 'No', NULL, 'V-3', 'Done', '2025-01-24 01:58:16', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 04:58:21'),
(342, '123213', 'Multiple Services', NULL, 'No', NULL, 'M-1', 'Done', '2025-01-24 02:04:25', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 04:58:26'),
(343, 'teqwe', 'Visa', NULL, 'No', NULL, 'V-4', 'Done', '2025-01-24 02:41:51', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(344, 'qweqwe', 'Visa', NULL, 'No', NULL, 'V-5', 'Done', '2025-01-24 02:41:55', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 04:58:32'),
(345, 'etete', 'Visa', NULL, 'No', NULL, 'V-6', 'Done', '2025-01-24 02:50:36', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 04:58:34'),
(346, '123213', 'Tours / Cruise', NULL, 'No', NULL, 'T-3', 'Done', '2025-01-24 02:55:43', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(347, '123213', 'Flights', NULL, 'No', NULL, 'F-2', 'Done', '2025-01-24 02:55:47', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(348, '1231231', 'Travel Insurance', NULL, 'No', NULL, 'I-1', 'Done', '2025-01-24 02:55:50', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(349, '123123123', 'Multiple Services', NULL, 'No', NULL, 'M-2', 'Done', '2025-01-24 02:55:54', '2025-01-24 08:11:39', 'Cancelled', 1, 10, 13, '2025-01-24 05:03:45'),
(350, '123123123', 'Tours / Cruise', NULL, 'No', NULL, 'T-4', 'Done', '2025-01-24 02:55:57', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(351, '1233123', 'Visa', NULL, 'No', NULL, 'V-7', 'Done', '2025-01-24 02:56:21', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(352, '234234', 'Visa', NULL, 'No', NULL, 'V-8', 'Done', '2025-01-24 02:56:25', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(353, 'ertert', 'Visa', NULL, 'No', NULL, 'V-9', 'Done', '2025-01-24 02:56:29', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(354, 'test', 'Visa', NULL, 'No', NULL, 'V-10', 'Done', '2025-01-24 03:36:00', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(355, '123123', 'Visa', NULL, 'No', NULL, 'V-11', 'Done', '2025-01-24 03:36:30', '2025-01-24 08:11:39', 'Cancelled', 1, 13, 13, '2025-01-24 04:58:35'),
(356, '213123', 'Visa', NULL, 'No', NULL, 'V-12', 'Done', '2025-01-24 03:38:17', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:00:18'),
(357, '123123123', 'Visa', NULL, 'No', NULL, 'V-13', 'Done', '2025-01-24 03:51:20', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:00:20'),
(358, 'test', 'Flights', NULL, 'No', NULL, 'F-3', 'Done', '2025-01-24 04:14:54', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(359, '123123', 'Tours / Cruise', NULL, 'No', NULL, 'T-5', 'Done', '2025-01-24 04:38:27', '2025-01-24 08:11:39', 'Cancelled', 1, 13, 13, '2025-01-24 04:59:41'),
(360, 'sadassda', 'Travel Insurance', NULL, 'No', NULL, 'I-2', 'Done', '2025-01-24 04:38:31', '2025-01-24 08:11:39', 'Cancelled', 1, 13, 13, '2025-01-24 05:00:24'),
(361, 'asdsadasd', 'Multiple Services', NULL, 'No', NULL, 'M-3', 'Done', '2025-01-24 04:38:34', '2025-01-24 08:11:39', 'Cancelled', 1, 13, 13, '2025-01-24 05:00:28'),
(362, '21sdfsfd', 'Flights', NULL, 'No', NULL, 'F-4', 'Done', '2025-01-24 04:38:40', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:00:16'),
(363, 'ghjghfj', 'Tours / Cruise', NULL, 'No', NULL, 'T-6', 'Done', '2025-01-24 04:40:38', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:03:47'),
(364, 'fghfgh', 'Flights', NULL, 'No', NULL, 'F-5', 'Done', '2025-01-24 04:40:42', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:03:39'),
(365, 'fghfghfgh', 'Travel Insurance', NULL, 'No', NULL, 'I-3', 'Done', '2025-01-24 04:40:46', '2025-01-24 08:11:39', 'Not Required', 1, 13, NULL, NULL),
(366, 'ghjghjg', 'Multiple Services', NULL, 'No', NULL, 'M-4', 'Done', '2025-01-24 04:40:49', '2025-01-24 08:11:39', 'Not Required', 1, 13, NULL, NULL),
(367, '123123', 'Flights', NULL, 'No', NULL, 'F-6', 'Done', '2025-01-24 04:41:38', '2025-01-24 08:11:39', 'Not Required', 1, 13, NULL, NULL),
(368, '234234', 'Travel Insurance', NULL, 'No', NULL, 'I-4', 'Done', '2025-01-24 04:42:01', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:06:53'),
(369, 'dfgdfg', 'Multiple Services', NULL, 'No', NULL, 'M-5', 'Done', '2025-01-24 04:42:06', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:06:55'),
(370, 'test', 'Visa', NULL, 'No', NULL, 'V-14', 'Done', '2025-01-24 05:04:08', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:08:03'),
(371, 'gordon ramsay', 'Tours / Cruise', NULL, 'No', NULL, 'T-7', 'Done', '2025-01-24 05:07:14', '2025-01-24 08:11:39', 'Completed', 1, 10, 10, '2025-01-24 05:07:32'),
(372, 'test', 'Visa', NULL, 'No', NULL, 'V-15', 'Done', '2025-01-24 05:09:02', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:09:32'),
(373, 'testagain', 'Visa', NULL, 'No', NULL, 'V-16', 'Done', '2025-01-24 05:12:56', '2025-01-24 08:11:39', 'Completed', 1, 13, 13, '2025-01-24 05:28:34'),
(374, 'test', 'Tours / Cruise', NULL, 'No', NULL, 'T-8', 'Done', '2025-01-24 05:13:52', '2025-01-24 08:11:39', 'Completed', 1, 13, 14, '2025-01-24 05:40:24'),
(375, '1', 'Tours / Cruise', NULL, 'No', NULL, 'T-9', 'Done', '2025-01-24 05:17:04', '2025-01-24 08:11:39', 'Completed', 1, 13, 14, '2025-01-24 05:40:17'),
(376, '2', 'Flights', NULL, 'No', NULL, 'F-7', 'Done', '2025-01-24 05:17:07', '2025-01-24 08:11:39', 'Completed', 1, 13, 14, '2025-01-24 05:40:21'),
(377, '3', 'Travel Insurance', NULL, 'No', NULL, 'I-5', 'Done', '2025-01-24 05:17:11', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(378, '4', 'Travel Insurance', NULL, 'No', NULL, 'I-6', 'Done', '2025-01-24 05:17:14', '2025-01-24 08:11:39', 'Not Required', 1, 13, NULL, NULL),
(379, '5', 'Multiple Services', NULL, 'No', NULL, 'M-6', 'Done', '2025-01-24 05:17:17', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(380, '6', 'Multiple Services', NULL, 'No', NULL, 'M-7', 'Done', '2025-01-24 05:17:20', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(381, '4', 'Flights', NULL, 'No', NULL, 'F-8', 'Done', '2025-01-24 05:17:24', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(382, '523', 'Travel Insurance', NULL, 'No', NULL, 'I-7', 'Done', '2025-01-24 05:17:27', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(383, '234234', 'Tours / Cruise', NULL, 'No', NULL, 'T-10', 'Done', '2025-01-24 05:17:30', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(384, '555', 'Travel Insurance', NULL, 'No', NULL, 'I-8', 'Done', '2025-01-24 05:17:33', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(385, 'test', 'Tours / Cruise', NULL, 'No', NULL, 'T-11', 'Done', '2025-01-24 05:24:08', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(386, 'gordon ramsay', 'Tours / Cruise', NULL, 'No', NULL, 'T-12', 'Skipped', '2025-01-24 05:54:49', '2025-01-24 08:11:39', 'Not Required', 1, 15, NULL, NULL),
(387, 'abi marquez', 'Travel Insurance', NULL, 'No', NULL, 'I-9', 'Done', '2025-01-24 05:54:57', '2025-01-24 08:11:39', 'Pending', 1, 10, NULL, NULL),
(388, 'niana guerero', 'Multiple Services', NULL, 'No', NULL, 'M-8', 'Done', '2025-01-24 05:55:05', '2025-01-24 08:11:39', 'Pending', 1, 10, NULL, NULL),
(389, 'ninong ry', 'Flights', NULL, 'No', NULL, 'F-9', 'Skipped', '2025-01-24 05:55:15', '2025-01-24 08:11:39', 'Not Required', 1, 13, NULL, NULL),
(390, 'Travel Agent 1', 'Multiple Services', NULL, 'No', NULL, 'M-9', 'Done', '2025-01-24 07:14:47', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(391, 'Travel Agent 2', 'Visa', NULL, 'No', NULL, 'V-17', 'Done', '2025-01-24 07:16:44', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(392, 'Sue Ramirez', 'Flights', NULL, 'No', NULL, 'F-10', 'Done', '2025-01-24 07:23:30', '2025-01-24 08:11:39', 'Pending', 1, 10, NULL, NULL),
(393, 'test123', 'Visa', NULL, 'No', NULL, 'V-18', 'Skipped', '2025-01-24 07:25:47', '2025-01-24 08:11:39', 'Not Required', 1, 13, NULL, NULL),
(394, 'teete', 'Tours / Cruise', NULL, 'No', NULL, 'T-13', 'Skipped', '2025-01-24 07:52:27', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(395, '123213213', 'Tours / Cruise', NULL, 'No', NULL, 'T-14', 'Skipped', '2025-01-24 07:58:11', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(396, '123123123', 'Tours / Cruise', NULL, 'No', NULL, 'T-15', 'Skipped', '2025-01-24 08:00:41', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(397, '213123', 'Flights', NULL, 'No', NULL, 'F-11', 'Skipped', '2025-01-24 08:00:46', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(398, '213123', 'Tours / Cruise', NULL, 'No', NULL, 'T-16', 'Skipped', '2025-01-24 08:01:46', '2025-01-24 08:11:39', 'Not Required', 1, 10, NULL, NULL),
(399, '12312', 'Visa', NULL, 'No', NULL, 'V-1', 'Serving', '2025-01-24 08:11:39', '2025-01-24 08:12:16', 'Not Required', 0, 10, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `queue_reset`
--

CREATE TABLE `queue_reset` (
  `id` int(11) NOT NULL,
  `reset_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `queue_reset`
--

INSERT INTO `queue_reset` (`id`, `reset_date`, `created_at`) VALUES
(1, '2024-12-19', '2024-12-19 02:09:51'),
(2, '2024-12-20', '2024-12-20 05:49:57'),
(3, '2024-12-26', '2024-12-26 02:03:40'),
(4, '2025-01-02', '2025-01-02 03:11:59'),
(5, '2025-01-03', '2025-01-03 02:00:40'),
(6, '2025-01-06', '2025-01-06 03:09:59'),
(7, '2025-01-07', '2025-01-07 05:43:13'),
(8, '2025-01-08', '2025-01-08 06:08:13'),
(9, '2025-01-09', '2025-01-09 02:52:00'),
(10, '2025-01-10', '2025-01-10 00:20:15'),
(27, '2025-01-13', '2025-01-13 02:07:26'),
(28, '2025-01-14', '2025-01-14 01:29:27'),
(29, '2025-01-15', '2025-01-15 00:39:58'),
(30, '2025-01-16', '2025-01-16 00:37:55'),
(31, '2025-01-17', '2025-01-17 01:49:08'),
(32, '2025-01-19', '2025-01-19 14:11:02'),
(35, '2025-01-20', '2025-01-20 05:50:39'),
(36, '2025-01-21', '2025-01-21 02:00:02'),
(37, '2025-01-22', '2025-01-22 02:29:40'),
(38, '2025-01-23', '2025-01-23 00:50:26'),
(40, '2025-01-24', '2025-01-24 08:11:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('kiosk','docs','user','admin','cashier') NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `first_name`, `last_name`) VALUES
(10, 'admin123', '$2y$10$PjUt1q8PRyi.lDqjNn2/PO47i4a1le9h78oDUsr5B70wAx7l/mJFC', 'user', 'admin', 'admin'),
(11, 'test123', '$2y$10$yABAAhJEBqSF9ySxoWkB4uCbsAeWo5Bn74VAUgvq9.bmbft/.dhQW', 'kiosk', 'test', 'test'),
(12, 'kent', '$2y$10$DV.eKVKgp1Gek/aH6d5o7OQjFxaPKRqoIpBNjxEyYU/Zd5sWQKzbe', 'user', 'kenso', 'marie'),
(13, 'vin123', '$2y$10$OmJ.ZpQQMzaIpAL9gzk8fuyv0DkVccGL3XnbwDP7IcpB2sajzvk/a', 'user', 'Vincent', 'Virtudazo'),
(14, 'acey', '$2y$10$gHQEyKoiyCIcHmYkAUpL5OVcLI2XimRxcJ3cuYlAP5My9gviBzd6C', 'user', 'AC', 'Navida'),
(15, 'johnraf', '$2y$10$h/0yzBFlKiPDYlkwh/O54.bfxI00yqAGfDStvMxcBcCLGMgDIQlEy', 'user', 'Rafael', 'Tolentino'),
(16, 'lea', '$2y$10$etV5BC8FlUqfKYcDAoNnTe.lvzE1tdqq1bJ1vLtKQkPiXbqkicnku', 'cashier', 'Lea', 'Versoza'),
(17, 'docs', '$2y$10$LF3Uq/mcGc3qPlxmtREOhOcumSDIOoj0OLWVWi3UkKbnEAGjaM1.y', 'docs', 'docs', 'test');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `counters`
--
ALTER TABLE `counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `counter_number` (`counter_number`),
  ADD KEY `active_user_id` (`active_user_id`);

--
-- Indexes for table `queue`
--
ALTER TABLE `queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_queue_number` (`queue_number`),
  ADD KEY `idx_serving_user` (`serving_user_id`),
  ADD KEY `completed_by_user_id` (`completed_by_user_id`);

--
-- Indexes for table `queue_reset`
--
ALTER TABLE `queue_reset`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `counters`
--
ALTER TABLE `counters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `queue`
--
ALTER TABLE `queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=400;

--
-- AUTO_INCREMENT for table `queue_reset`
--
ALTER TABLE `queue_reset`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `counters`
--
ALTER TABLE `counters`
  ADD CONSTRAINT `counters_ibfk_1` FOREIGN KEY (`active_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `queue`
--
ALTER TABLE `queue`
  ADD CONSTRAINT `queue_ibfk_1` FOREIGN KEY (`serving_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `queue_ibfk_2` FOREIGN KEY (`completed_by_user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
