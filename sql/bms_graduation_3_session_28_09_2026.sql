-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2026 at 08:36 AM
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
-- Database: `bms_graduation_3_session`
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
(1, 'Admin', 'admin@bms.ac.lk', '$2y$10$ygq.jvBdwcXgUKMq0wW.3.gPrvrGQGN.mD0de9Qj3aZMKmDMYPn0O', 'admin', '2026-09-28 06:20:39');

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
-- Table structure for table `bulk_data_table`
--

CREATE TABLE `bulk_data_table` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `seat_no` varchar(50) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `session_time` varchar(255) DEFAULT NULL,
  `email_sent_yes_no` enum('Yes','No') DEFAULT 'No',
  `email_sent_time` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `graduated_status` varchar(10) DEFAULT NULL,
  `student_result` varchar(255) DEFAULT NULL,
  `calling_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `bulk_data_table`
--
DELIMITER $$
CREATE TRIGGER `bulk_data_table_prevent_delete` BEFORE DELETE ON `bulk_data_table` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Security Notice: Deletion of student data is not allowed for security reasons.';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `clothing_collections`
--

CREATE TABLE `clothing_collections` (
  `id` int(10) UNSIGNED NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `collect_cloak` varchar(255) DEFAULT NULL,
  `collect_slashes` varchar(255) DEFAULT NULL,
  `collect_hats` varchar(255) DEFAULT NULL,
  `collected_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_cloak` varchar(255) DEFAULT NULL,
  `return_slashes` varchar(255) DEFAULT NULL,
  `return_hats` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clothing_collections`
--

INSERT INTO `clothing_collections` (`id`, `student_id`, `student_name`, `program_name`, `collect_cloak`, `collect_slashes`, `collect_hats`, `collected_at`, `return_cloak`, `return_slashes`, `return_hats`) VALUES
(1, '918092477', 'Rahma Azmi', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-18 05:59:01', 'returned', NULL, NULL),
(2, '918092438', 'Micah daniel', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:41:37', 'returned', NULL, NULL),
(3, '918092402', 'Vaishnavi Rajinikanth', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:54:58', 'returned', NULL, NULL),
(4, '918092425', 'Abisheka Satheesh', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:55:15', 'returned', NULL, NULL),
(5, '918092434', 'Afeef Almas', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:55:47', 'returned', NULL, NULL),
(6, '918092466', 'Imthadh Basith', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:56:34', 'returned', NULL, NULL),
(7, '918092488', 'Radhif Rifkan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:56:55', 'returned', NULL, NULL),
(8, '918092429', 'Kavishake Yogaraj', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:58:39', 'returned', NULL, NULL),
(9, '918092404', 'Midushiga Premathas', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 04:59:03', 'returned', NULL, NULL),
(10, '918092481', 'Arshad Jesmy', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:00:41', 'returned', NULL, NULL),
(11, '918092431', 'Janani Vihanga Karunarathna', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:02:06', 'returned', NULL, NULL),
(12, '918092468', 'Muhammad Ali Sirajudeen', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:03:48', 'returned', NULL, NULL),
(13, '9180924109', 'Mohamed Faheem Mohamed Adhil', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:04:02', NULL, NULL, NULL),
(14, '918092492', 'Akshana Prabagaran', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:06:24', 'returned', NULL, NULL),
(15, '918092471', 'Ahamed Sulaiman', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:08:45', 'returned', NULL, NULL),
(16, '9180924112', 'Abdullah Luthfi', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:09:12', 'returned', NULL, NULL),
(17, '9180924111', 'Sharafath Fouz', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:09:41', 'returned', NULL, NULL),
(18, '918092493', 'Praveena Dharmaraja', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:13:06', 'returned', NULL, NULL),
(19, '9180924118', 'Nithiyavanie Jeyarasu', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:18:09', 'returned', NULL, NULL),
(20, '918092469', 'Haala Rozan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:18:59', 'returned', NULL, NULL),
(21, '9180924116', 'Ahamed Ali', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:26:30', 'returned', NULL, NULL),
(22, '918092421', 'Mubarak Musni', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:34:17', 'returned', NULL, NULL),
(23, '9180924121', 'Fazeel Akthaf Ahamed', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:38:04', 'returned', NULL, NULL),
(24, '918092413', 'Miska Mubeen', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 05:46:26', 'returned', NULL, NULL),
(25, '918092448', 'Sudiksha Basker', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:01:42', 'returned', NULL, NULL),
(26, '918092465', 'Hinushan Raja', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:18:46', 'returned', NULL, NULL),
(27, '918092408', 'HARISUTHAN', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:19:10', 'returned', NULL, NULL),
(28, '918092406', 'Yashwarathan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:20:03', 'returned', NULL, NULL),
(29, '918092415', 'Mohamed Nileefer Saeedh Ahamed', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:20:21', 'returned', NULL, NULL),
(30, '918092447', 'Dinushan Rajakumar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:32:22', 'returned', NULL, NULL),
(31, '918092437', 'Imara Afzal', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:37:10', 'returned', NULL, NULL),
(32, '918092453', 'Aksharath Prakashkaran', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:37:22', 'returned', NULL, NULL),
(33, '918092427', 'Thalha Tharik', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:37:35', 'returned', NULL, NULL),
(34, '918092470', 'Adithya de silva', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:39:51', 'returned', NULL, NULL),
(35, '918092439', 'Asiya Fathima', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:40:09', 'returned', NULL, NULL),
(36, '918092458', 'Fathima Zuleika', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:40:19', 'returned', NULL, NULL),
(37, '918092450', 'Rukshana Satkunaraja', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:41:16', 'returned', NULL, NULL),
(38, '918092407', 'Thulsi Hatangala', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:44:33', 'returned', NULL, NULL),
(39, '918092478', 'Sangeeth Chandramohan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:44:55', 'returned', NULL, NULL),
(40, '918092418', 'Sharana Rathitharan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:47:40', 'returned', NULL, NULL),
(41, '918092432', 'Mohomed Ashif', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:48:01', 'returned', NULL, NULL),
(42, '9180924105', 'Imaad Ikram', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:48:23', 'returned', NULL, NULL),
(43, '9180924103', 'Steen Devasagayam', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:48:50', 'returned', NULL, NULL),
(44, '918092483', 'Sajeevan karuppiah', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:51:04', 'returned', NULL, NULL),
(45, '9180924100', 'Mohamed Luqmaan Samsudeen', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 06:54:00', 'returned', NULL, NULL),
(46, '918092475', 'Salma Umar Darshan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:00:13', 'returned', NULL, NULL),
(47, '918092460', 'Shreffer Fernando', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:01:36', 'returned', NULL, NULL),
(48, '918092484', 'Sheshan kumar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:04:52', 'returned', NULL, NULL),
(49, '9180924102', 'Saarah Insaar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:05:54', 'returned', NULL, NULL),
(50, '918092459', 'Hamdha Rihan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:07:51', 'returned', NULL, NULL),
(51, '918092430', 'Sharukshan Pathmanathan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:09:35', 'returned', NULL, NULL),
(52, '9180924124', 'Hamdhaan Insaar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:10:38', 'returned', NULL, NULL),
(53, '918092491', 'Asmaa Hussain', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:11:35', 'returned', NULL, NULL),
(54, '918092409', 'Zaina Hilmy', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:11:54', 'returned', NULL, NULL),
(55, '918092497', 'Ibthishama Ihlar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:15:22', 'returned', NULL, NULL),
(56, '918092419', 'Shahla Ramiz', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:15:52', 'returned', NULL, NULL),
(57, '9180924101', 'Hajara Azhar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:16:19', 'returned', NULL, NULL),
(58, '918092487', 'Chaini Hettiarachchi', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:18:21', 'returned', NULL, NULL),
(59, '918092411', 'Samindi Pehasara', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:18:58', 'returned', NULL, NULL),
(60, '918092496', 'Raenia De Sayrah', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:20:41', 'returned', NULL, NULL),
(61, '9180924119', 'Hasni Asees', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:26:40', 'returned', NULL, NULL),
(62, '918092442', 'Tharushi Wijekoon', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:28:19', 'returned', NULL, NULL),
(63, '918092405', 'Sacha Tiffany', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:30:17', 'returned', NULL, NULL),
(64, '918092433', 'Faqeehah Sathry', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:32:18', 'returned', NULL, NULL),
(65, '918092474', 'Amaya Ranasinghe', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:33:53', 'returned', NULL, NULL),
(66, '918092479', 'Kaushika Kanagaratnam', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:34:10', 'returned', NULL, NULL),
(67, '918092476', 'Chanuli siriwardana', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:34:28', 'returned', NULL, NULL),
(68, '917032410', 'Maryam Hudha', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:36:23', 'returned', NULL, NULL),
(69, '918092486', 'Anne ameshya Julius Jebakumar', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:36:45', 'returned', NULL, NULL),
(70, '9180924125', 'Pramodh Fernando', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:39:31', 'returned', NULL, NULL),
(71, '918092499', 'Jeyakumar pavithrika', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:43:57', 'returned', NULL, NULL),
(72, '918092482', 'Krisha Nakshathra', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:44:44', 'returned', NULL, NULL),
(73, '918092485', 'Kaushalya', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 07:45:58', 'returned', NULL, NULL),
(74, '918092449', 'Shadiya Ousman', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:13:56', 'returned', NULL, NULL),
(75, '918092456', 'Sadiya Shiyam', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:14:04', 'returned', NULL, NULL),
(76, '918092472', 'Rimshani Rameshwaran', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:14:20', 'returned', NULL, NULL),
(77, '918092444', 'Shajad Mufazzel', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:14:34', 'returned', NULL, NULL),
(78, '918092455', 'Roshel Andrea', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:26:11', 'returned', NULL, NULL),
(79, '918092452', 'Shanuja Karunanithi', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:27:34', 'returned', NULL, NULL),
(80, '9180924126', 'Ashka Afra', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:39:07', 'returned', NULL, NULL),
(81, '918092461', 'Lavanya Fernando', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:44:00', 'returned', NULL, NULL),
(82, '918092422', 'Danustalini vinsent de paul', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:52:03', 'returned', NULL, NULL),
(83, '918092420', 'Jehas Ann Lovely', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:52:11', 'returned', NULL, NULL),
(84, '9180924110', 'Thishara Bodhinayaka', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:55:04', 'returned', NULL, NULL),
(85, '918092401', 'Saahir Ahamed Hamdhan', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 08:57:51', 'returned', NULL, NULL),
(86, '918092451', 'Aasir Haroon', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 09:25:49', 'returned', NULL, NULL),
(87, '918092462', 'INSAF FASLOON', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-19 10:41:54', 'returned', NULL, NULL),
(88, '918092494', 'Mahdi Rahim', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-21 05:25:56', 'returned', NULL, NULL),
(89, '918092443', 'Abdul Rahman', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-21 05:27:37', 'returned', NULL, NULL),
(90, '918092428', 'Fathima Zainab', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-21 05:54:20', 'returned', NULL, NULL),
(91, '529102422', 'Aanandi Vivekanandhan', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:02:40', 'returned', NULL, NULL),
(92, '623102410', 'Diheli Vithanage', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:04:10', 'returned', NULL, NULL),
(93, '529102435', 'Sithumya De Zoysa', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:08:26', 'returned', NULL, NULL),
(94, '529102445', 'Leona Fermi Ranasinghe', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:09:26', 'returned', NULL, NULL),
(95, '623102406', 'Niruja Nanthini Tharmarajah', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:09:55', 'returned', NULL, NULL),
(96, '529102410', 'Bavatharani Ravichandran', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:10:15', 'returned', NULL, NULL),
(97, '529102444', 'Samithna Santhisi', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:10:39', 'returned', NULL, NULL),
(98, '529102439', 'Tharuka Meegaswatte', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:11:05', 'returned', NULL, NULL),
(99, '529102424', 'Sedasna Rathnayake', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:11:16', 'returned', NULL, NULL),
(100, '529102414', 'Rikaza Khan', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:11:40', 'returned', NULL, NULL),
(101, '529102417', 'Loshini Shanmuganathan', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:11:45', 'returned', NULL, NULL),
(102, '529102430', 'Dilukshana Navaneethan', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:11:59', 'returned', NULL, NULL),
(103, '1005102405', 'Maheli Abeywardana', 'Higher Diploma in Food Science and Nutrition- Batch 05', 'collected', '', '', '2026-09-21 06:12:40', 'returned', NULL, NULL),
(104, '529102447', 'Rumana miraj', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:13:10', 'returned', NULL, NULL),
(105, '529102420', 'Nuha Naushad', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:14:24', 'returned', NULL, NULL),
(106, '1005102401', 'khadheeja arafath', 'Higher Diploma in Food Science and Nutrition- Batch 05', 'collected', '', '', '2026-09-21 06:17:20', 'returned', NULL, NULL),
(107, '1005102404', 'Farah Afrin', 'Higher Diploma in Food Science and Nutrition- Batch 05', 'collected', '', '', '2026-09-21 06:17:37', 'returned', NULL, NULL),
(108, '623102412', 'Kulasinghe', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:18:46', 'returned', NULL, NULL),
(109, '1005102403', 'Nuhansi Nadithya', 'Higher Diploma in Food Science and Nutrition- Batch 05', 'collected', '', '', '2026-09-21 06:20:19', 'returned', NULL, NULL),
(110, '529102405', 'Fathima Sameeha Shamir', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:22:40', 'returned', NULL, NULL),
(111, '623102403', 'SUBENDRA SHANDRU', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:22:59', 'returned', NULL, NULL),
(112, '623102405', 'Dewmi Niranya', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:23:19', 'returned', NULL, NULL),
(113, '623102415', 'Maryam Sanoon', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:23:26', 'returned', NULL, NULL),
(114, '529102429', 'Mohomed Afrin', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:25:01', 'returned', NULL, NULL),
(115, '529102434', 'Shreeindica Sivakumaran', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:25:08', 'returned', NULL, NULL),
(116, '529102427', 'Dejashwinth Krishnamoorthy', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:25:14', 'returned', NULL, NULL),
(117, '529102438', 'Sajithi Fonseka', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:25:19', 'returned', NULL, NULL),
(118, '1103102403', 'Amna Rizwan', 'Higher Diploma in Medical Biotechnology - Batch 03', 'collected', '', '', '2026-09-21 06:25:32', 'returned', NULL, NULL),
(119, '529102411', 'Anna Sharoniya Sathanandan', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:25:44', 'returned', NULL, NULL),
(120, '529102425', 'Raaihathul Kurdiyya Mohamed', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:27:20', 'returned', NULL, NULL),
(121, '1005102402', 'venushaalie silva', 'Higher Diploma in Food Science and Nutrition- Batch 05', 'collected', '', '', '2026-09-21 06:27:38', 'returned', NULL, NULL),
(122, '529102412', 'Timandra Meemaduma', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:27:49', 'returned', NULL, NULL),
(123, '623102414', 'Mahiru Samaraweera', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:27:55', 'returned', NULL, NULL),
(124, '529102413', 'Shanel Miranda', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:28:09', 'returned', NULL, NULL),
(125, '529102440', 'Yaalennee Kathireshan', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:28:31', 'returned', NULL, NULL),
(126, '529102437', 'Faathima Ramlaa Sabry', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:28:37', 'returned', NULL, NULL),
(127, '529102409', 'Elisha Anna Joseph', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:30:36', 'returned', NULL, NULL),
(128, '529102419', 'Marietta Belleth', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:31:32', 'returned', NULL, NULL),
(129, '529102418', 'Mohamed Haniffa Mohamed Ratheech', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:31:52', 'returned', NULL, NULL),
(130, '529102404', 'Enuri Thewanma Perera', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:35:18', 'returned', NULL, NULL),
(131, '529102433', 'Piyagi Gamage Harini', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:36:57', 'returned', NULL, NULL),
(132, '1103102402', 'Mohamed Sabwan Ahsan Sahabdeen', 'Higher Diploma in Medical Biotechnology - Batch 03', 'collected', '', '', '2026-09-21 06:37:30', 'returned', NULL, NULL),
(133, '529102431', 'Savi Sakbo Govindunath Elagedara', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:41:02', 'returned', NULL, NULL),
(134, '529102406', 'Anne Tissera', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:42:28', 'returned', NULL, NULL),
(135, '623102411', 'Khadijah Murad', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:43:13', 'returned', NULL, NULL),
(136, '623102401', 'Shanika Himanthi', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:45:19', 'returned', NULL, NULL),
(137, '529102403', 'Tarini Livinya', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 06:46:41', 'returned', NULL, NULL),
(138, '623102408', 'Shayani Kaveesha', 'Higher Diploma In Biotechnology - Batch 23', 'collected', '', '', '2026-09-21 06:46:52', 'returned', NULL, NULL),
(139, '918092410', 'Daphne Milton', 'BTEC Higher National Diploma in Business - Batch 18', 'collected', '', '', '2026-09-21 07:39:43', 'returned', NULL, NULL),
(140, '529102441', 'Prithisha Maxibolton', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 08:00:37', 'returned', NULL, NULL),
(141, '529102443', 'Gunasekaram Madusha', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 08:00:44', 'returned', NULL, NULL),
(142, '529102426', 'Atshaya Vijayaretna', 'Higher Diploma in Biomedical Science - Batch 29', 'collected', '', '', '2026-09-21 08:00:58', 'returned', NULL, NULL),
(143, '1103102401', 'Asmaa Osman', 'Higher Diploma in Medical Biotechnology - Batch 03', 'collected', '', '', '2026-09-23 10:18:23', 'returned', NULL, NULL);

--
-- Triggers `clothing_collections`
--
DELIMITER $$
CREATE TRIGGER `prevent_clothing_collections_delete` BEFORE DELETE ON `clothing_collections` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Deletion from clothing_collections table is not allowed.';
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
  `session` varchar(50) DEFAULT NULL,
  `cloak` tinyint(4) NOT NULL DEFAULT 1,
  `slashes` tinyint(4) NOT NULL DEFAULT 0,
  `hats` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `data_tables`
--

INSERT INTO `data_tables` (`id`, `programName`, `graduationFee`, `freeTicket`, `extraTicketFee`, `session`, `cloak`, `slashes`, `hats`) VALUES
(1, 'BTEC Higher National Diploma in Business - Batch 18', 12500, 1, 2500, 'MORNING', 1, 0, 0),
(2, 'Higher Diploma in Biomedical Science - Batch 29', 12500, 1, 2500, 'MORNING', 1, 0, 0),
(3, 'Higher Diploma In Biotechnology - Batch 23', 12500, 1, 2500, 'MORNING', 1, 0, 0),
(4, 'Higher Diploma in Food Science and Nutrition- Batch 05', 12500, 1, 2500, 'MORNING', 1, 0, 0),
(5, 'Higher Diploma in Medical Biotechnology - Batch 03', 12500, 1, 2500, 'MORNING', 1, 0, 0);

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
-- Table structure for table `email_log`
--

CREATE TABLE `email_log` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name_in_full` varchar(255) DEFAULT NULL,
  `email_address` varchar(255) NOT NULL,
  `program_name` varchar(255) DEFAULT NULL,
  `seat_no` varchar(50) DEFAULT NULL,
  `session_time` varchar(255) DEFAULT NULL,
  `email_type` enum('single','bulk') DEFAULT 'single',
  `status` enum('sent','failed') DEFAULT 'sent',
  `error_message` text DEFAULT NULL,
  `sent_by` int(11) DEFAULT NULL COMMENT 'Admin ID who triggered the email',
  `sent_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `email_log`
--
DELIMITER $$
CREATE TRIGGER `after_email_log_insert` AFTER INSERT ON `email_log` FOR EACH ROW BEGIN
    IF NEW.status = 'sent' THEN
        INSERT INTO `notifications` (table_name, record_id, student_id, action_type, description)
        VALUES (
            'email_log',
            NEW.id,
            NEW.student_id,
            'INSERT',
            CONCAT('Email sent to ', NEW.name_in_full, ' (', NEW.email_address, ')')
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_email_log_delete` BEFORE DELETE ON `email_log` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Deletion from email_log table is not allowed for security reasons';
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `extra_ticket_log`
--

CREATE TABLE `extra_ticket_log` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) DEFAULT NULL,
  `added_tickets` int(11) NOT NULL,
  `ticket_price` decimal(10,2) NOT NULL,
  `total_added` decimal(10,2) NOT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `extra_ticket_log`
--
DELIMITER $$
CREATE TRIGGER `trg_extra_ticket_insert` AFTER INSERT ON `extra_ticket_log` FOR EACH ROW BEGIN
    INSERT INTO notifications (
        table_name,
        record_id,
        student_id,
        action_type,
        description
    ) VALUES (
        'extra_ticket_log',
        NEW.id,
        NEW.student_id,
        'INSERT',
        CONCAT('Added ', NEW.added_tickets, ' extra ticket(s) worth Rs. ', NEW.total_added)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_extra_ticket_update` AFTER UPDATE ON `extra_ticket_log` FOR EACH ROW BEGIN
    INSERT INTO notifications (
        table_name,
        record_id,
        student_id,
        action_type,
        description
    ) VALUES (
        'extra_ticket_log',
        NEW.id,
        NEW.student_id,
        'UPDATE',
        CONCAT('Updated extra tickets: ', NEW.added_tickets, ' ticket(s), total Rs. ', NEW.total_added)
    );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_prevent_delete_extra_ticket_log` BEFORE DELETE ON `extra_ticket_log` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'DELETE is not allowed on extra_ticket_log table.';
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
(1, 'admin', 1, NULL, 'insert', 'New admin added: Admin (Role: admin)', 'unread', '2026-09-28 06:20:39'),
(2, 'admin', 1, NULL, 'update', 'Admin updated: Admin (Role: admin)', 'unread', '2026-09-28 06:25:08'),
(3, 'registered_students', 1, '123456', 'INSERT', 'New student registered: Minzar Mohamadhu Mohamed Mirshath (Program: BTEC Higher National Diploma in Business - Batch 18)', 'unread', '2026-09-28 06:32:08');

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
(1, '123456', 'Minzar Mohamadhu Mohamed Mirshath', '1999-01-19', 'mirshath.mmm@gmail.com', 'BTEC Higher National Diploma in Business - Batch 18', 766158014, 'paid', 'registered', NULL);

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
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `title` varchar(10) DEFAULT NULL,
  `calling_name` varchar(255) DEFAULT NULL,
  `confirmation` tinyint(4) NOT NULL DEFAULT 0,
  `program_name` varchar(255) DEFAULT NULL,
  `given_email_add` varchar(50) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `phone_no` varchar(15) DEFAULT NULL,
  `attend` varchar(50) DEFAULT NULL,
  `crsfee_payment_status` varchar(50) DEFAULT NULL,
  `graduation_payment_status` varchar(50) DEFAULT 'Not-Completed',
  `invitation_collected` varchar(255) DEFAULT NULL,
  `updated_at_invitation` datetime DEFAULT NULL,
  `student_meals` varchar(255) DEFAULT NULL,
  `guest_meals` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registered_students`
--

INSERT INTO `registered_students` (`id`, `student_id`, `in_no`, `dob`, `name_in_full`, `title`, `calling_name`, `confirmation`, `program_name`, `given_email_add`, `email_address`, `phone_no`, `attend`, `crsfee_payment_status`, `graduation_payment_status`, `invitation_collected`, `updated_at_invitation`, `student_meals`, `guest_meals`, `created_at`) VALUES
(1, '123456', NULL, '1999-01-19', 'Minzar Mohamadhu Mohamed Mirshath', 'Mr.', 'Minzar Mirshath', 1, 'BTEC Higher National Diploma in Business - Batch 18', 'mirshath.mmm@gmail.com', 'yournumplz@gmail.com', '+94766158014', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-09-28 12:02:08');

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
-- Indexes for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student` (`student_id`);

--
-- Indexes for table `data_tables`
--
ALTER TABLE `data_tables`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_log`
--
ALTER TABLE `email_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_email` (`email_address`),
  ADD KEY `idx_program` (`program_name`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `fk_sent_by` (`sent_by`);

--
-- Indexes for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_added_by` (`added_by`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `old_student_db`
--
ALTER TABLE `old_student_db`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_log`
--
ALTER TABLE `email_log`
  ADD CONSTRAINT `fk_sent_by` FOREIGN KEY (`sent_by`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
