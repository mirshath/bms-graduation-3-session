-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 11:45 AM
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
-- Database: `bmsgraduation205`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `admin_name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Invitation', 'mmirshath@gmail.com', '$2y$10$v.0fQ3SYrE2OJv8Q2touGeZRm8gEUoNwYBxSS102wiej56OvA.naS', 'invitation', '2025-10-13 04:16:07'),
(2, 'Mirshath', 'm@gmail.com', '$2y$10$v.0fQ3SYrE2OJv8Q2touGeZRm8gEUoNwYBxSS102wiej56OvA.naS', 'admin', '2025-10-15 06:41:22'),
(3, 'finance', 'finance@gmail.com', '$2y$10$PMCjct9jnXAMxMST6LqxcePkazUA79V3UidG3.JSLl9kYuO58d.Dm', 'finance', '2025-10-26 07:18:55');

--
-- Triggers `admin`
--
DELIMITER $$
CREATE TRIGGER `after_admin_insert` AFTER INSERT ON `admin` FOR EACH ROW BEGIN
    INSERT INTO `notifications` (table_name, record_id, student_id, action_type, description)
    VALUES (
        'admin',
        NEW.id,
        NULL,
        'insert',
        CONCAT('New admin added: ', NEW.admin_name, ' (Role: ', NEW.role, ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_admin_update` AFTER UPDATE ON `admin` FOR EACH ROW BEGIN
    INSERT INTO `notifications` (table_name, record_id, student_id, action_type, description)
    VALUES (
        'admin',
        NEW.id,
        NULL,
        'update',
        CONCAT('Admin updated: ', NEW.admin_name, ' (Role: ', NEW.role, ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_admin_delete` BEFORE DELETE ON `admin` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from admin table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `data_tables`
--

CREATE TABLE `data_tables` (
  `id` int(11) NOT NULL,
  `programName` varchar(255) DEFAULT NULL,
  `graduationFee` int(20) DEFAULT NULL,
  `freeTicket` int(50) DEFAULT NULL,
  `extraTicketFee` int(50) DEFAULT NULL,
  `session` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_tables`
--

INSERT INTO `data_tables` (`id`, `programName`, `graduationFee`, `freeTicket`, `extraTicketFee`, `session`) VALUES
(1, 'GDM', 20000, 2, 3000, 'MORNING'),
(2, 'ECM', 30000, 3, 4500, 'EVENING');

--
-- Triggers `data_tables`
--
DELIMITER $$
CREATE TRIGGER `prevent_data_tables_delete` BEFORE DELETE ON `data_tables` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from data_tables table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `table_name` varchar(100) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `table_name`, `record_id`, `student_id`, `action_type`, `description`, `status`, `created_at`) VALUES
(1, 'admin', 2, NULL, 'update', 'Admin updated: Mirshath (Role: admin)', 'read', '2025-10-28 03:48:20'),
(2, 'registered_students', 1, '123', 'INSERT', 'New student registered: Mir (Program: GDM)', 'read', '2025-10-28 05:30:38'),
(3, 'payment_records', 1, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 05:31:16'),
(4, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 05:31:16'),
(5, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paids', 'read', '2025-10-28 05:35:47'),
(6, 'payment_records', 2, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 32000', 'read', '2025-10-28 05:36:03'),
(7, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 05:36:03'),
(8, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 05:54:49'),
(9, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: unpaid', 'read', '2025-10-28 05:54:51'),
(10, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: unpaid', 'read', '2025-10-28 06:30:10'),
(11, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: unpaid', 'read', '2025-10-28 06:32:04'),
(12, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: unpaid', 'read', '2025-10-28 06:33:17'),
(13, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: unpaid', 'read', '2025-10-28 06:35:30'),
(14, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: unpaid', 'read', '2025-10-28 06:36:44'),
(15, 'payment_records', 3, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 23000', 'read', '2025-10-28 06:36:55'),
(16, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 06:36:55'),
(17, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paids', 'read', '2025-10-28 09:32:52'),
(18, 'payment_records', 4, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'read', '2025-10-28 09:37:01'),
(19, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 09:37:01'),
(20, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paidd', 'read', '2025-10-28 09:38:51'),
(21, 'payment_records', 5, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 32000', 'read', '2025-10-28 09:40:14'),
(22, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 09:40:14'),
(23, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddf', 'read', '2025-10-28 09:40:25'),
(24, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddfd', 'read', '2025-10-28 09:49:00'),
(25, 'payment_records', 6, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 09:49:08'),
(26, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 09:49:08'),
(27, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddfdf', 'read', '2025-10-28 09:49:59'),
(28, 'payment_records', 7, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'read', '2025-10-28 09:50:08'),
(29, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 09:50:08'),
(30, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddfdfd', 'read', '2025-10-28 09:51:13'),
(31, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddfdfdd', 'read', '2025-10-28 09:56:01'),
(32, 'payment_records', 8, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 09:56:07'),
(33, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 09:56:07'),
(34, 'payment_records', 9, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 09:56:07'),
(35, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 09:56:07'),
(36, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 10:18:09'),
(37, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paidd', 'read', '2025-10-28 10:32:53'),
(38, 'payment_records', 10, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 32000', 'read', '2025-10-28 10:33:02'),
(39, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 10:33:02'),
(40, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paidd', 'read', '2025-10-28 10:48:32'),
(41, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddd', 'read', '2025-10-28 10:56:56'),
(42, 'payment_records', 11, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 11:19:51'),
(43, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 11:19:51'),
(44, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paidddd', 'read', '2025-10-28 11:22:11'),
(45, 'payment_records', 12, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'read', '2025-10-28 11:22:22'),
(46, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 11:22:22'),
(47, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paiddddd', 'read', '2025-10-28 11:24:15'),
(48, 'payment_records', 13, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 11:24:24'),
(49, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 11:24:24'),
(50, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paidddddd', 'read', '2025-10-28 11:37:25'),
(51, 'payment_records', 1, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 11:37:53'),
(52, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 11:37:53'),
(53, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paidf', 'read', '2025-10-28 11:44:15'),
(54, 'payment_records', 2, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'read', '2025-10-28 11:44:22'),
(55, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 11:44:22'),
(56, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paids', 'read', '2025-10-28 12:16:27'),
(57, 'payment_records', 3, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'read', '2025-10-28 12:16:46'),
(58, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mir, graduation payment status: paid', 'read', '2025-10-28 12:16:46'),
(59, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:17:34'),
(60, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paidss', 'read', '2025-10-28 12:17:38'),
(61, 'payment_records', 4, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 12:17:50'),
(62, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:17:50'),
(63, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paidssd', 'read', '2025-10-28 12:19:41'),
(64, 'payment_records', 5, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 12:19:50'),
(65, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:19:50'),
(66, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paidssdd', 'read', '2025-10-28 12:23:33'),
(67, 'payment_records', 6, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'read', '2025-10-28 12:23:55'),
(68, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:23:55'),
(69, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: c', 'read', '2025-10-28 12:24:56'),
(70, 'payment_records', 7, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'read', '2025-10-28 12:26:45'),
(71, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:26:45'),
(72, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: cs', 'read', '2025-10-28 12:31:06'),
(73, 'payment_records', 8, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 32000', 'read', '2025-10-28 12:31:12'),
(74, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:31:12'),
(75, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: css', 'read', '2025-10-28 12:31:36'),
(76, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: csss', 'read', '2025-10-28 12:32:41'),
(77, 'payment_records', 1, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 32000', 'read', '2025-10-28 12:32:50'),
(78, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'read', '2025-10-28 12:32:50'),
(79, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: csssf', 'read', '2025-10-28 12:47:00'),
(80, 'payment_records', 2, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 20000', 'unread', '2025-10-29 03:57:29'),
(81, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 03:57:29'),
(82, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paidd', 'unread', '2025-10-29 04:01:19'),
(83, 'payment_records', 1, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'unread', '2025-10-29 05:30:28'),
(84, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 05:30:28'),
(85, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paids', 'unread', '2025-10-29 05:40:38'),
(86, 'payment_records', 2, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 32000', 'unread', '2025-10-29 05:44:04'),
(87, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 05:44:04'),
(88, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paidsd', 'unread', '2025-10-29 05:59:28'),
(89, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paidsds', 'unread', '2025-10-29 06:11:07'),
(90, 'payment_records', 3, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 20000', 'unread', '2025-10-29 06:11:23'),
(91, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 06:11:23'),
(92, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: c', 'unread', '2025-10-29 06:18:45'),
(93, 'payment_records', 4, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 29000', 'unread', '2025-10-29 06:18:56'),
(94, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 06:18:56'),
(95, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: cc', 'unread', '2025-10-29 06:47:54'),
(96, 'payment_records', 5, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 26000', 'unread', '2025-10-29 06:53:50'),
(97, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 06:53:50'),
(98, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: ccd', 'unread', '2025-10-29 08:36:31'),
(99, 'payment_records', 6, '123', 'INSERT', 'New payment record added for student ID 123, program: GDM, amount: 41000', 'unread', '2025-10-29 08:36:53'),
(100, 'registered_students', 1, '123', 'UPDATE', 'Student record updated for Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  , graduation payment status: paid', 'unread', '2025-10-29 08:36:53');

--
-- Triggers `notifications`
--
DELIMITER $$
CREATE TRIGGER `prevent_notifications_delete` BEFORE DELETE ON `notifications` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from notifications table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `old_student_db`
--

CREATE TABLE `old_student_db` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `DOB` date NOT NULL,
  `given_email` varchar(50) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `mobile_no` int(15) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `in_no` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `old_student_db`
--

INSERT INTO `old_student_db` (`id`, `student_id`, `name`, `DOB`, `given_email`, `program`, `mobile_no`, `payment_status`, `status`, `in_no`) VALUES
(1, '123', 'Mir', '2025-10-23', 'mirshath.mmm@gmail.com', 'GDM', 25490455, 'paid', 'registered', 10);

--
-- Triggers `old_student_db`
--
DELIMITER $$
CREATE TRIGGER `prevent_old_student_delete` BEFORE DELETE ON `old_student_db` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from old_student_db table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_records`
--

CREATE TABLE `payment_records` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `graduation_fee` decimal(10,2) NOT NULL,
  `free_ticket_count` int(5) DEFAULT 0,
  `extra_ticket_count` int(5) DEFAULT 0,
  `extra_ticket_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` int(11) DEFAULT NULL,
  `receipt_number` varchar(20) NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_records`
--

INSERT INTO `payment_records` (`id`, `student_id`, `program_name`, `graduation_fee`, `free_ticket_count`, `extra_ticket_count`, `extra_ticket_fee`, `total_amount`, `receipt_number`, `payment_date`) VALUES
(1, '123', 'GDM', 20000.00, 2, 3, 9000.00, 29000, 'GC20255680', '2025-10-29 05:30:28'),
(2, '123', 'GDM', 20000.00, 2, 4, 12000.00, 32000, 'GC20256251', '2025-10-29 05:44:04'),
(3, '123', 'GDM', 20000.00, 2, 0, 0.00, 20000, 'GC20251968', '2025-10-29 06:11:23'),
(4, '123', 'GDM', 20000.00, 2, 3, 9000.00, 29000, 'GC20254269', '2025-10-29 06:18:56'),
(5, '123', 'GDM', 20000.00, 2, 2, 6000.00, 26000, 'GC20258166', '2025-10-29 06:53:50'),
(6, '123', 'GDM', 20000.00, 2, 7, 21000.00, 41000, 'GC20255896', '2025-10-29 08:36:53');

--
-- Triggers `payment_records`
--
DELIMITER $$
CREATE TRIGGER `log_payment_insert` AFTER INSERT ON `payment_records` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'payment_records',
        NEW.id,
        NEW.student_id,
        'INSERT',
        CONCAT('New payment record added for student ID ', NEW.student_id,
               ', program: ', NEW.program_name,
               ', amount: ', NEW.total_amount)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_payment_update` AFTER UPDATE ON `payment_records` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'payment_records',
        NEW.id,
        NEW.student_id,
        'UPDATE',
        CONCAT('Payment record updated for student ID ', NEW.student_id,
               ', receipt number: ', NEW.receipt_number)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_payment_records_delete` BEFORE DELETE ON `payment_records` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from payment_records table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `registered_students`
--

CREATE TABLE `registered_students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `in_no` int(11) DEFAULT NULL,
  `dob` date NOT NULL,
  `name_in_full` varchar(255) NOT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  `given_email_add` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) NOT NULL,
  `phone_no` varchar(15) DEFAULT NULL,
  `attend` varchar(50) DEFAULT NULL,
  `crsfee_payment_status` varchar(50) DEFAULT NULL,
  `graduation_payment_status` varchar(50) DEFAULT 'Not-Completed',
  `invitation_collected` varchar(255) DEFAULT NULL,
  `updated_at_invitation` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_students`
--

INSERT INTO `registered_students` (`id`, `student_id`, `in_no`, `dob`, `name_in_full`, `program_name`, `given_email_add`, `email_address`, `phone_no`, `attend`, `crsfee_payment_status`, `graduation_payment_status`, `invitation_collected`, `updated_at_invitation`, `created_at`) VALUES
(1, '123', 10, '2025-10-23', 'Mirsath Mohamed  Mirsath Mohamed  Mirsath Mohamed  ', 'GDM', 'mirshath.mmm@gmail.com', 'mirshath.mmm@gmail.com', '25490455', NULL, 'paid', 'paid', 'collected', '2025-10-28 15:48:09', '2025-10-28 11:00:38');

--
-- Triggers `registered_students`
--
DELIMITER $$
CREATE TRIGGER `log_registered_insert` AFTER INSERT ON `registered_students` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'registered_students',
        NEW.id,
        NEW.student_id,
        'INSERT',
        CONCAT('New student registered: ', NEW.name_in_full,
               ' (Program: ', NEW.program_name, ')')
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_registered_update` AFTER UPDATE ON `registered_students` FOR EACH ROW BEGIN
    INSERT INTO notifications (table_name, record_id, student_id, action_type, description)
    VALUES (
        'registered_students',
        NEW.id,
        NEW.student_id,
        'UPDATE',
        CONCAT('Student record updated for ', NEW.name_in_full,
               ', graduation payment status: ', NEW.graduation_payment_status)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_registered_students_delete` BEFORE DELETE ON `registered_students` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Deletion from registered_students table is not allowed for security reasons';
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `data_tables`
--
ALTER TABLE `data_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `old_student_db`
--
ALTER TABLE `old_student_db`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_records`
--
ALTER TABLE `payment_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registered_students`
--
ALTER TABLE `registered_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
