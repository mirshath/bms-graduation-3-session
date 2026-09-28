-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 10, 2026 at 04:04 PM
-- Server version: 10.11.15-MariaDB
-- PHP Version: 8.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bms2102ac_graduation`
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
(1, 'Admin', 'admin@bms.ac.lk', '$2y$10$TG.k2OM2.6Sol0y4rKPVRO/nH7V9Of5FnivJsEP9yODj1YtEY3C.i', 'admin', '2025-10-28 04:32:06'),
(2, 'D', 'D@d.d', '$2y$10$KYcxJi1fCwXBKeQjs9EB8e5MDs0uZbogi5HUw5JRuNpnelt4otuVW', 'finance', '2025-10-28 04:33:51'),
(3, 'Geshani', 'geshani.w@bms.ac.lk', '$2y$10$kPUUqaUUgWxP0LSw3LgpL.7ndDSREGwfYrHn8YuXPGA2fZkybMOdW', 'invitation', '2025-10-28 04:36:14'),
(4, 'Nimmi', 'costing@bms.ac.lk', '$2y$10$V/WXNacQP2WuHQeZKG0AcehbcTBuWbs92WHI0ICAQPL9UkchrYLci', 'finance', '2025-11-01 07:22:16'),
(5, 'keerththana', 'keerththana.p@bms.ac.lk', '$2y$10$KgLeBoT12UbHX8xF4E2fCO4NdsA4lPTLNH.5oTSgYNFh/TIlHUlSG', 'invitation', '2025-11-01 07:39:16'),
(6, 'Thamodha', 'accounts@bms.ac.lk', '$2y$10$5yXaallqN2xW4UZilveDiuj1vWr9yUh6C6TDFttz56Et3TYrliVXu', 'registrationDesk', '2025-11-02 03:18:03'),
(7, 'Ruwani', 'ruwani.f@bms.ac.lk', '$2y$10$iGsY4khRzK.qgNZ9g29qJOnxJDtjkgAXd73LITD6F7AlTxLPatGgO', 'invitation', '2025-11-12 06:54:09'),
(8, 'Shivajini', 'student.registration@bms.ac.lk', '$2y$10$.wABhI3Y37HVpcZ6FmFuauhC9Dbq.82Z44PR21xku9UGRxZq1yFqW', 'invitation', '2025-11-20 04:24:36'),
(9, 'mirshath', 'mirshath.m@cgs.lk', '$2y$10$MFWWMIvDdUQ8BqpwzrQ.2ubnoaXut57nSPvgzQZJhsu6CYD7wIH1a', 'registrationDesk', '2025-11-23 06:18:21'),
(10, 'cloak', 'cloak@gmail.com', '$2y$10$xvZCOjCF4V1FVzPosbsNqu56Yk09INDsHHcKAA6BOsdjUtX61o/BG', 'cloakCollectReturn', '2025-11-24 06:38:18'),
(11, 'Malithi', 'finance.officer@bms.ac.lk', '$2y$10$YTmpjSBJdlSPZBR9yHAd3e6oPLmNjTWEZg4CLAcMTnbcVd6V1B/06', 'registrationDesk', '2025-11-24 06:59:10'),
(12, 'Ruzniya', 'finance.asst@bms.ac.lk', '$2y$10$cq37x0xWGC1AhKWqfz6KYu5hTTyjtfK4EUKP.83CLM1FPcoXgjO0u', 'registrationDesk', '2025-11-24 07:07:23'),
(13, 'Priyanka', 'studentsupport@bms.ac.lk', '$2y$10$me.xz2g65Dbz0G9wuVA63.3V8a0pDlf.ss3CtKzwqz4xoK65Gf66C', 'registrationDesk', '2025-11-24 07:10:56'),
(14, 'Cloak', 'library@bms.ac.lk', '$2y$10$ocJdfG9pVB1t836Y7Keg9.qZ3/dbn4dqjXb3fJhq7LKST2eXCrU7m', 'cloakCollectReturn', '2025-11-24 07:22:32'),
(15, 'Zahana', 'finance@bms.ac.lk', '$2y$10$XbpBbUTpBQ1jsDMCa4aF5OpRxbQ0H/eq3Ty9Lnq3Z9oG2pMNpf8Sq', 'registrationDesk', '2025-11-24 07:44:38'),
(16, 'Clancy', 'hnd.admin@bms.ac.lk', '$2y$10$3HgN3VzvYgdhN09r0OeUA.bUIWboselU6T8E9.pemQzC3qhmtIG3e', 'admin', '2025-11-24 07:52:25'),
(17, 'Dulangi', 'dulangi.h@bms.ac.lk', '$2y$10$M9cJgWB5PXciOQnhITUD6eznmlK145h68.Pue7PgXAOyhdYAR7/dK', 'registrationDesk', '2025-11-24 07:53:57'),
(18, 'Saheera', 'accounts.citycampus@bms.ac.lk', '$2y$10$jcGNj8261fFq5bTs18YKUeDVfB.MBhgc1xFH1rxKdrd8DQrbiJaeO', 'registrationDesk', '2025-11-24 07:55:41'),
(19, 'Thaksheniya', 'thaksheniya.s@bms.ac.lk', '$2y$10$/tsn3.tKXEaNzdUsjGvvM.xGJfhkqU1N9.yvRfGWCUqegvNNQjLYm', 'finance', '2026-03-03 04:14:53'),
(20, 'Ayesha', 'examscience@bms.ac.lk', '$2y$10$8yo99jlh0dj7uQy4oYXrAO5pcxt74l28L8IYjAQapTaDHSbn5JFBS', 'admin', '2026-03-07 07:02:44'),
(21, 'Darshika', 'certificate.exam@bms.ac.lk', '$2y$10$4GDCOE1CrclioHSVYXkUJu3DUQv/nfQ3f/m.jaRhyT01l97RP4JOS', 'admin', '2026-03-07 07:10:33'),
(22, 'Erangee', 'bioadmin@bms.ac.lk', '$2y$10$wPDfHUg/SYpKOF9WJ5ykK.OjBS/C1MvtWMktddY4Mah9m8CJ0A39q', 'admin', '2026-03-09 06:22:31'),
(23, 'Suraj', 'suraj@bms.ac.lk', '$2y$10$8egjUfyEWathXxI7RY3LTeTvOqKmo4Z6IXvjpyyqIA1gtaFCXjOua', 'admin', '2026-03-10 09:17:21');

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
-- Triggers `clothing_collections`
--
DELIMITER $$
CREATE TRIGGER `prevent_clothing_collections_delete` BEFORE DELETE ON `clothing_collections` FOR EACH ROW BEGIN
    SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = '❌ Deletion from clothing_collections table is not allowed.';
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
(1, 'BTEC Higher National Diploma in Business - Batch 17', 10000, 1, 2000, 'MORNING', 1, 0, 0),
(2, 'Higher Diploma in Biomedical Science - Batch 28', 10000, 1, 2000, 'MORNING', 1, 0, 0),
(3, 'Higher Diploma In Biotechnology Science - Batch 21', 10000, 1, 2000, 'MORNING', 1, 0, 0),
(4, 'Higher Diploma In Biotechnology Science - Batch 22', 10000, 1, 2000, 'MORNING', 1, 0, 0),
(5, 'Higher Diploma in Food Science and Nutrition- Batch 04', 10000, 1, 2000, 'MORNING', 1, 0, 0),
(6, 'Higher Diploma in Medical Biotechnology - Batch 02', 10000, 1, 2000, 'MORNING', 1, 0, 0);

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
-- Dumping data for table `email_log`
--

INSERT INTO `email_log` (`id`, `student_id`, `name_in_full`, `email_address`, `program_name`, `seat_no`, `session_time`, `email_type`, `status`, `error_message`, `sent_by`, `sent_at`) VALUES
(1, '528032422', 'Ms. Sandeepa Sevmini de Silva', 'sandeepasevmini@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-03 13:13:47'),
(2, '528032401', 'Ms. Mohammed Safrin Aafrin Aysha', 'Aafrinaysha2001@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-03 13:28:52'),
(3, '528032407', 'Ms. E.G. Narmada Hemadrie De Silva', 'narmadahdesilva@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-03 15:58:17'),
(4, '527102330', 'Ms. Tharuki Subanya Pieris', 'tharukipeiris8@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-04 12:11:00'),
(5, '528032416', 'Ms. Siddeeq Fathima Shaheera', 'shaheerasiddeeq@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-04 13:32:05'),
(6, '622032406', 'Ms. Ranumi Dahanaggama Arachchi', 'dammsprom@yahoo.com', 'Higher Diploma In Biotechnology Science - Batch 22', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-04 13:50:36'),
(7, '622032402', 'Ms. Fathima Nuha Hakeem', 'hakeemnuha@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-04 13:52:14'),
(8, '622032411', 'Themavee Wijekoon', 'themaveesw07@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-04 13:54:50'),
(9, '528032402', 'Ms. Kiruthika Paramananthan', 'paramananthankiruththika@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-04 16:23:26'),
(10, '917032429', 'Lacshithi Saravanan', 'lacshithisaravanan1111@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-05 09:21:37'),
(11, '917032407', 'Ms. Ursula Pavithree Wannige', 'Ursula.Pavithree@outlook.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-05 09:48:02'),
(12, '528032434', 'Ms.  Kosgodage Thewni Nadara Dharmasiri', 'thewnidharmasiri@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-05 10:39:03'),
(13, '528032420', 'Mr. Ugendraraj Vijayakumar', 'vijayakumar.ugendraraj@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-06 10:25:11'),
(14, '622032401', 'Ms. Ama Ranathunga', 'aranathunga04@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-06 10:40:12'),
(15, '528032406', 'Ms. Gayashi Anupama Jayawardana', 'gayashianupama00@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-06 13:06:47'),
(16, '528032418', 'Ms. Asini  Fernando', 'asinikithma@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-06 13:08:24'),
(17, '917032470', 'Mr. Umair Imthiyasdeen', 'mohamedumair999@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-06 14:49:28'),
(18, '9170324100', 'Seyed Muhammed Hiraz Hibshy Mowlana', 'hirazmowlana@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 10:34:03'),
(19, '917032446', 'Mr. Krithigan Sugumaran', 'skrithigan@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 10:42:12'),
(20, '528032442', 'Ms. Apitha Suresh', 'apithasuresh624@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 10:50:15'),
(21, '528032419', 'Ms. Harithra Ramesh', 'harithraramesh7@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 10:51:17'),
(22, '1004032401', 'Ms. Wijesiri Narange Ashani Samudika', 'samudika99@outlook.com', 'Higher Diploma in Food Science and Nutrition- Batch 04', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 11:39:26'),
(23, '917032425', 'Ms. Dharshini Chandran', 'dharshini.chandran@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 12:22:25'),
(24, '917032434', 'Ms. Robeka Subramaniam', 'subramaniamrobeka@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 12:23:51'),
(25, '917032433', 'Ms. Fathima Shamla Imnaz', 'imnazshamla@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 12:59:21'),
(26, '917032448', 'Ms. Habeeba Zareen', 'habeebazareen6@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 13:03:14'),
(27, '917032459', 'Sinthiya Madanmohan', 'sinthiyamathanmohan@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 14:31:09'),
(28, '917032432', 'Ms. Rishka Fazaal', 'rishkafazaal@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 15:42:16'),
(29, '917032464', 'Ms. Vismida Thachanamoorthy', 'mtmvismida.08t@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 15:43:20'),
(30, '917032423', 'Mr. Mohamed Mawfeen Umar', 'mawfeen.umar@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 15:44:32'),
(31, '917032437', 'Nimenma Methsiluni', 'nimethma.arachchi@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 15:53:03'),
(32, '917032449', 'Mr. Iqbal Abdul Muyeeth', 'muyeeth4@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 16:07:20'),
(33, '917032415', 'Ms. Vishalani Mahendran', 'mahendranvishalani1708@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-07 16:21:56'),
(34, '917032401', 'Sangeetha Sivanesan', 'sangeetha.sivanesan@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 10:35:27'),
(35, '917032403', 'Ms. Aminath Ayamin Rasheed', 'ayaako567@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 11:09:58'),
(36, '917032456', 'Ms. Rithika Chandravathanan', 'rithikka21@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 11:11:31'),
(37, '917032438', 'Ms. Naysa Rishona Amarasinghe', 'naysa.amarasinghe@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 11:12:51'),
(38, '917032439', 'Mr. Thanooj Kathirason', 'Thanooj987@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 11:19:09'),
(39, '917032478', 'Ms. Chathupama Perera', 'chathupamaperera1234@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 11:52:05'),
(40, '9170324102', 'Mr.Enosh Ganesh', 'enoshganesh1@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 12:00:03'),
(41, '917032496', 'Ms. Risda Sahna Rizwan', 'risda.rizwan@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-08 12:18:38'),
(42, '622032408', 'Ms. Moksha Chathurika Dananjane', 'dhanushka2002426@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 10:31:42'),
(43, '917032471', 'Mr. Abdul Rahman Ashroff', 'aliabdurrahman2042001@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 10:49:56'),
(44, '917032494', 'Yuthmini Malsiluni', 'yuthmini.malsiluni@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:24:19'),
(45, '917032485', 'Hettiarachchilage Prabodha Vidumini', 'Praboda.vidumini@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:25:36'),
(46, '528032427', 'Mr. V Kanistan Agash', 'kanistanakash15@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:26:22'),
(47, '528032412', 'Ms. Abinaya Balasingam', 'abinaya.balasingam@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:27:22'),
(48, '528032405', 'Ms. Sayini Pushpakumar', 'sayinipushpakumar@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:28:14'),
(49, '917032430', 'Muhammad Luqman Azwar', 'luqmanazwar7@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:40:38'),
(50, '917032488', 'Adeeshan Saravanamohan', 'adeeshan08@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:41:33'),
(51, '917032450', 'Ms. Abdul Rasal Fahma', 'abdul.fahma@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 11:44:20'),
(52, '528032431', 'Mr. Mohammed Azwear Ali Mohammed Aadhil Najmi', 'aadhilnajmi2004@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 12:06:11'),
(53, '528032433', 'Ms. Shamila Nijamdeen', 'shamila.nijamdeen@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 12:07:57'),
(54, '528032430', 'Ms. Sajitha Makenthiran', 'sajitha.makenthiran@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 12:08:58'),
(55, '528032438', 'Ms. N.Chamathka Edirirathna', 'nizayainsaad@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 12:14:23'),
(56, '1002032304', 'Mr. Arunasalam Rathushan', 'Arunasalam.rathushan@bms.ac.lk', 'Higher Diploma in Food Science and Nutrition- Batch 04', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 12:15:55'),
(57, '1102032401', 'Suha Ahmed Naseerdeen', 'suhasan1131@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 02', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 12:18:49'),
(58, '917032427', 'Ms. Thachchana Moorthy Stelina', 'moorthystalina@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 13:52:27'),
(59, '917032480', 'Mushab Aslam', 'mushab.mahmood@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 14:22:02'),
(60, '917032452', 'Ms. Lakshi Karnan', 'lakshikarunan840@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 14:24:13'),
(61, '917032461', 'Mr. Yoosuf Sulaiman', 'yoosufsulaiman14@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 14:26:49'),
(62, '917032469', 'Ms. Suvidana Selvaraja', 'suvisuvidana@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 14:28:04'),
(63, '917032466', 'Mr. Mohomad Fazeem Wazeem', 'fazeemwazeem@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 14:30:11'),
(64, '917032404', 'Ms. Radinka Jinelli Fernando', 'radhinkafernando@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 14:32:00'),
(65, '917032482', 'Miss. Harishmi Balaratnarajah Mohanakumar', 'harishmi.mohanakumar@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:30:20'),
(66, '528032409', 'Ms. Nandhujah Gunasheharan', 'nandhujahvarma@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:37:54'),
(67, '528032415', 'Ms. Saraniya Muralidaran', 'muralitharansaraniya@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:39:29'),
(68, '917032416', 'Ms. Fathima Amra Najimudeen', 'amranajimudeen23@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:49:34'),
(69, '528032421', 'Mr. Ahamed Lebbe Ahamed Sahee', 'callmesahee02@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:50:59'),
(70, '528032423', 'Ms. Malshi Imesha Pathiranage', 'malshi.pathiranage@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:52:36'),
(71, '528032403', 'Thiththalapitiyage Shonaleen Varsha Fonseka', 'shonaleenfonseka@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:53:58'),
(72, '528032436', 'Ms.  Zahara Ismail', 'zaharaismail0075@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 15:59:59'),
(73, '917032402', 'Ms. Minda Oliniya Rozairo', 'mindaoliniya@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-09 16:16:54'),
(74, '917032472', 'Ms. Fathima Rishadha Uvais', 'rishadha525@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 08:40:30'),
(75, '917032476', 'Ms. Rabiyah Badurdeen', 'fathima.badurdeen@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 09:15:08'),
(76, '528032428', 'Mr. Mohamed Iesa Ismail', 'iesai3214@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 11:13:26'),
(77, '528032444', 'Ms. Thisuri Cyara Jayaweera', 'thisuri.jayaweera@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 11:14:30'),
(78, '528032429', 'Ms. A.M.M Chathumini Hansika Jayawardene', 'chathumini.jayawardena@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 11:15:24'),
(79, '528032408', 'Ms. Sahar Khushbu Nadeem', 'sahar.nadeem@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 11:19:39'),
(80, '528032443', 'Mr. Dhanushan Sekar', 'dhanushansekar@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 11:49:09'),
(81, '917032424', 'Avinga Inosh Piyathilaka', 'avinga.inosh@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 13:50:26'),
(82, '917032405', 'Ms. M. R. Rahmath Rashidha', 'rashidharizan0206@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:18:09'),
(83, '917032468', 'Ms. Fathima Haleema Yoosuf', 'haleemayoosuf678@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:19:23'),
(84, '917032419', 'Leena Manohar', 'leenamnhr@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:31:27'),
(85, '917032447', 'Ms. Lenin Leno Sharon Olivia', 'leninolivia1@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:33:21'),
(86, '917032481', 'Ms. Rakhsshaa Ravikumar', 'rakhsshaa.ravikumar@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:34:07'),
(87, '917032499', 'Mr. Dhakhshesh Shivashankar', 'dhakhsheshsiva@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:35:14'),
(88, '917032420', 'Ms. Ifla Imran', 'iflaimran29@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 14:36:09'),
(89, '528032411', 'Sri Ranjan Bathanchaliy Meenaambal', 'bathanchaliy.ranjan@bms.ac.lk', 'Higher Diploma in Biomedical Science - Batch 28', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 15:28:22'),
(90, '917032413', 'Ms. Nilakshi Sivathas', 'nila41767@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 15:48:42'),
(91, '917032431', 'Ms. Julious Berny Blinda', 'bernyblinda200403@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', NULL, NULL, 'single', 'sent', NULL, 19, '2026-03-10 16:01:54');

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
-- Dumping data for table `extra_ticket_log`
--

INSERT INTO `extra_ticket_log` (`id`, `student_id`, `added_tickets`, `ticket_price`, `total_added`, `added_by`, `added_on`) VALUES
(1, NULL, 1, 2000.00, 2000.00, 19, '2026-03-06 14:54:26'),
(2, NULL, 2, 2000.00, 4000.00, 19, '2026-03-07 13:25:20'),
(3, NULL, 1, 2000.00, 2000.00, 19, '2026-03-07 15:54:29'),
(4, NULL, 1, 2000.00, 2000.00, 19, '2026-03-08 11:58:28'),
(5, NULL, 1, 2000.00, 2000.00, 19, '2026-03-10 14:20:53'),
(6, NULL, 1, 2000.00, 2000.00, 19, '2026-03-10 14:32:14'),
(7, NULL, 1, 2000.00, 2000.00, 19, '2026-03-10 15:52:27');

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
(1, 'registered_students', 1, '528032427', 'INSERT', 'New student registered: Mr. V Kanistan Agash (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-27 04:41:27'),
(2, 'registered_students', 2, '9170324102', 'INSERT', 'New student registered: Mr.Enosh Ganesh (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:09:14'),
(3, 'registered_students', 3, '917032403', 'INSERT', 'New student registered: Ms. Aminath Ayamin Rasheed (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:10:20'),
(4, 'registered_students', 4, '917032464', 'INSERT', 'New student registered: Ms. Vismida Thachanamoorthy (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:11:12'),
(5, 'registered_students', 5, '917032425', 'INSERT', 'New student registered: Ms. Dharshini Chandran (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:12:17'),
(6, 'registered_students', 6, '917032490', 'INSERT', 'New student registered: Nathan De Silva (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:40:06'),
(7, 'registered_students', 7, '917032429', 'INSERT', 'New student registered: Lacshithi Saravanan (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:49:14'),
(8, 'registered_students', 8, '9170324100', 'INSERT', 'New student registered: Seyed Muhammed Hiraz Hibshy Mowlana (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 10:51:16'),
(9, 'registered_students', 9, '917032434', 'INSERT', 'New student registered: Ms. Robeka Subramaniam (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 11:53:28'),
(10, 'registered_students', 10, '528032432', 'INSERT', 'New student registered: Ms. Rushdha Nazar (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-27 13:32:01'),
(11, 'registered_students', 11, '917032456', 'INSERT', 'New student registered: Ms. Rithika Chandravathanan (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 13:40:40'),
(12, 'registered_students', 12, '917032450', 'INSERT', 'New student registered: Ms. Abdul Rasal Fahma (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 15:24:05'),
(13, 'registered_students', 13, '917032433', 'INSERT', 'New student registered: Ms. Fathima Shamla Imnaz (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 16:20:24'),
(14, 'registered_students', 14, '528032422', 'INSERT', 'New student registered: Ms. Sandeepa Sevmini de Silva (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-27 17:40:19'),
(15, 'registered_students', 15, '622032402', 'INSERT', 'New student registered: Ms. Fathima Nuha Hakeem (Program: Higher Diploma In Biotechnology Science - Batch 22)', 'read', '2026-02-27 17:56:16'),
(16, 'registered_students', 16, '528032437', 'INSERT', 'New student registered: Ms. Amana Fathima Naflar (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-27 18:20:54'),
(17, 'registered_students', 17, '917032431', 'INSERT', 'New student registered: Ms. Julious Berny Blinda (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-27 19:13:09'),
(18, 'registered_students', 18, '528032402', 'INSERT', 'New student registered: Ms. Kiruthika Paramananthan (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 05:08:01'),
(19, 'registered_students', 19, '528032403', 'INSERT', 'New student registered: Thiththalapitiyage Shonaleen Varsha Fonseka (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 05:11:57'),
(20, 'registered_students', 20, '528032407', 'INSERT', 'New student registered: Ms. E.G. Narmada Hemadrie De Silva (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 05:36:56'),
(21, 'registered_students', 21, '622032406', 'INSERT', 'New student registered: Ms. Ranumi Dahanaggama Arachchi (Program: Higher Diploma In Biotechnology Science - Batch 22)', 'read', '2026-02-28 05:37:35'),
(22, 'registered_students', 22, '622032408', 'INSERT', 'New student registered: Ms. Moksha Chathurika Dananjane (Program: Higher Diploma In Biotechnology Science - Batch 22)', 'read', '2026-02-28 06:15:51'),
(23, 'registered_students', 23, '528032440', 'INSERT', 'New student registered: Sivatharshan Mohanakumar (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 06:52:27'),
(24, 'registered_students', 24, '528032430', 'INSERT', 'New student registered: Ms. Sajitha Makenthiran (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 07:28:27'),
(25, 'registered_students', 25, '917032407', 'INSERT', 'New student registered: Ms. Ursula Pavithree Wannige (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-28 07:58:39'),
(26, 'registered_students', 26, '622032401', 'INSERT', 'New student registered: Ms. Ama Ranathunga (Program: Higher Diploma In Biotechnology Science - Batch 22)', 'read', '2026-02-28 08:10:28'),
(27, 'registered_students', 27, '528032401', 'INSERT', 'New student registered: Ms. Mohammed Safrin Aafrin Aysha (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 08:30:16'),
(28, 'registered_students', 28, '1102032401', 'INSERT', 'New student registered: Suha Ahmed Naseerdeen (Program: Higher Diploma in Medical Biotechnology - Batch 02)', 'read', '2026-02-28 08:46:39'),
(29, 'registered_students', 29, '528032411', 'INSERT', 'New student registered: Sri Ranjan Bathanchaliy Meenaambal (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 12:18:37'),
(30, 'registered_students', 30, '528032409', 'INSERT', 'New student registered: Ms. Nandhujah Gunasheharan (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 13:03:09'),
(31, 'registered_students', 31, '917032432', 'INSERT', 'New student registered: Ms. Rishka Fazaal (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-28 13:29:23'),
(32, 'registered_students', 32, '528032416', 'INSERT', 'New student registered: Ms. Siddeeq Fathima Shaheera (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-02-28 16:08:44'),
(33, 'registered_students', 33, '917032447', 'INSERT', 'New student registered: Ms. Lenin Leno Sharon Olivia (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-02-28 18:15:25'),
(34, 'registered_students', 34, '528032442', 'INSERT', 'New student registered: Ms. Apitha Suresh (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-03-01 05:09:18'),
(35, 'registered_students', 35, '528032419', 'INSERT', 'New student registered: Ms. Harithra Ramesh (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-03-01 05:09:41'),
(36, 'registered_students', 36, '917032416', 'INSERT', 'New student registered: Ms. Fathima Amra Najimudeen (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-03-01 08:55:41'),
(37, 'registered_students', 37, '528032438', 'INSERT', 'New student registered: Ms. N.Chamathka Edirirathna (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-03-01 14:59:46'),
(38, 'registered_students', 38, '528032423', 'INSERT', 'New student registered: Ms. Malshi Imesha Pathiranage (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-03-02 00:46:42'),
(39, 'registered_students', 39, '917032404', 'INSERT', 'New student registered: Ms. Radinka Jinelli Fernando (Program: BTEC Higher National Diploma in Business - Batch 17)', 'read', '2026-03-02 05:55:51'),
(40, 'registered_students', 40, '528032433', 'INSERT', 'New student registered: Ms. Shamila Nijamdeen (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-03-02 07:22:05'),
(41, 'registered_students', 41, '1004032401', 'INSERT', 'New student registered: Ms. Wijesiri Narange Ashani Samudika (Program: Higher Diploma in Food Science and Nutrition- Batch 04)', 'read', '2026-03-02 11:40:05'),
(42, 'registered_students', 42, '528032406', 'INSERT', 'New student registered: Ms. Gayashi Anupama Jayawardana (Program: Higher Diploma in Biomedical Science - Batch 28)', 'read', '2026-03-02 15:31:54'),
(43, 'admin', 2, NULL, 'update', 'Admin updated: D (Role: finance)', 'read', '2026-03-03 04:13:35'),
(44, 'admin', 2, NULL, 'update', 'Admin updated: D (Role: finance)', 'read', '2026-03-03 04:13:52'),
(45, 'admin', 19, NULL, 'insert', 'New admin added: Thaksheniya (Role: finance)', 'read', '2026-03-03 04:14:53'),
(46, 'registered_students', 43, '528032443', 'INSERT', 'New student registered: Mr. Dhanushan Sekar (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-03 04:50:37'),
(47, 'registered_students', 44, '528032418', 'INSERT', 'New student registered: Ms. Asini  Fernando (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-03 07:19:37'),
(48, 'registered_students', 45, '527102330', 'INSERT', 'New student registered: Ms. Tharuki Subanya Pieris (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-03 07:41:14'),
(49, 'payment_records', 1, '528032422', 'INSERT', 'New payment record added for student ID 528032422, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-03 07:43:47'),
(50, 'email_log', 1, '528032422', 'INSERT', 'Email sent to Ms. Sandeepa Sevmini de Silva (sandeepasevmini@gmail.com)', 'unread', '2026-03-03 07:43:47'),
(51, 'registered_students', 14, '528032422', 'UPDATE', 'Student record updated for Ms. Sandeepa Sevmini de Silva, graduation payment status: paid', 'unread', '2026-03-03 07:43:47'),
(52, 'payment_records', 2, '528032401', 'INSERT', 'New payment record added for student ID 528032401, program: Higher Diploma in Biomedical Science - Batch 28, amount: 16000', 'unread', '2026-03-03 07:58:52'),
(53, 'email_log', 2, '528032401', 'INSERT', 'Email sent to Ms. Mohammed Safrin Aafrin Aysha (Aafrinaysha2001@gmail.com)', 'unread', '2026-03-03 07:58:52'),
(54, 'registered_students', 27, '528032401', 'UPDATE', 'Student record updated for Ms. Mohammed Safrin Aafrin Aysha, graduation payment status: paid', 'unread', '2026-03-03 07:58:52'),
(55, 'registered_students', 46, '917032496', 'INSERT', 'New student registered: Ms. Risda Sahna Rizwan (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-03 08:19:34'),
(56, 'registered_students', 47, '917032448', 'INSERT', 'New student registered: Ms. Habeeba Zareen (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-03 08:57:01'),
(57, 'registered_students', 48, '917032470', 'INSERT', 'New student registered: Mr. Umair Imthiyasdeen (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-03 09:56:11'),
(58, 'payment_records', 3, '528032407', 'INSERT', 'New payment record added for student ID 528032407, program: Higher Diploma in Biomedical Science - Batch 28, amount: 16000', 'unread', '2026-03-03 10:28:17'),
(59, 'email_log', 3, '528032407', 'INSERT', 'Email sent to Ms. E.G. Narmada Hemadrie De Silva (narmadahdesilva@gmail.com)', 'unread', '2026-03-03 10:28:17'),
(60, 'registered_students', 20, '528032407', 'UPDATE', 'Student record updated for Ms. E.G. Narmada Hemadrie De Silva, graduation payment status: paid', 'unread', '2026-03-03 10:28:17'),
(61, 'registered_students', 49, '528032404', 'INSERT', 'New student registered: Ms. Gajani Rajeswaran (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-03 12:24:27'),
(62, 'registered_students', 50, '1002032304', 'INSERT', 'New student registered: Mr. Arunasalam Rathushan (Program: Higher Diploma in Food Science and Nutrition- Batch 04)', 'unread', '2026-03-03 14:39:45'),
(63, 'payment_records', 4, '527102330', 'INSERT', 'New payment record added for student ID 527102330, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-04 06:41:00'),
(64, 'email_log', 4, '527102330', 'INSERT', 'Email sent to Ms. Tharuki Subanya Pieris (tharukipeiris8@gmail.com)', 'unread', '2026-03-04 06:41:00'),
(65, 'registered_students', 45, '527102330', 'UPDATE', 'Student record updated for Ms. Tharuki Subanya Pieris, graduation payment status: paid', 'unread', '2026-03-04 06:41:00'),
(66, 'registered_students', 51, '528032421', 'INSERT', 'New student registered: Mr. Ahamed Lebbe Ahamed Sahee (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-04 08:00:40'),
(67, 'registered_students', 52, '528032445', 'INSERT', 'New student registered: Ms. Sharuniya Pradaa Mahendran (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-04 08:00:41'),
(68, 'payment_records', 5, '528032416', 'INSERT', 'New payment record added for student ID 528032416, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-04 08:02:05'),
(69, 'email_log', 5, '528032416', 'INSERT', 'Email sent to Ms. Siddeeq Fathima Shaheera (shaheerasiddeeq@gmail.com)', 'unread', '2026-03-04 08:02:05'),
(70, 'registered_students', 32, '528032416', 'UPDATE', 'Student record updated for Ms. Siddeeq Fathima Shaheera, graduation payment status: paid', 'unread', '2026-03-04 08:02:05'),
(71, 'registered_students', 53, '622032411', 'INSERT', 'New student registered: Themavee Wijekoon (Program: Higher Diploma In Biotechnology Science - Batch 22)', 'unread', '2026-03-04 08:08:06'),
(72, 'registered_students', 54, '528032415', 'INSERT', 'New student registered: Ms. Saraniya Muralidaran (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-04 08:09:18'),
(73, 'payment_records', 6, '622032406', 'INSERT', 'New payment record added for student ID 622032406, program: Higher Diploma In Biotechnology Science - Batch 22, amount: 12000', 'unread', '2026-03-04 08:20:36'),
(74, 'email_log', 6, '622032406', 'INSERT', 'Email sent to Ms. Ranumi Dahanaggama Arachchi (dammsprom@yahoo.com)', 'unread', '2026-03-04 08:20:36'),
(75, 'registered_students', 21, '622032406', 'UPDATE', 'Student record updated for Ms. Ranumi Dahanaggama Arachchi, graduation payment status: paid', 'unread', '2026-03-04 08:20:36'),
(76, 'payment_records', 7, '622032402', 'INSERT', 'New payment record added for student ID 622032402, program: Higher Diploma In Biotechnology Science - Batch 22, amount: 10000', 'unread', '2026-03-04 08:22:14'),
(77, 'email_log', 7, '622032402', 'INSERT', 'Email sent to Ms. Fathima Nuha Hakeem (hakeemnuha@gmail.com)', 'unread', '2026-03-04 08:22:14'),
(78, 'registered_students', 15, '622032402', 'UPDATE', 'Student record updated for Ms. Fathima Nuha Hakeem, graduation payment status: paid', 'unread', '2026-03-04 08:22:14'),
(79, 'payment_records', 8, '622032411', 'INSERT', 'New payment record added for student ID 622032411, program: Higher Diploma In Biotechnology Science - Batch 22, amount: 12000', 'unread', '2026-03-04 08:24:50'),
(80, 'email_log', 8, '622032411', 'INSERT', 'Email sent to Themavee Wijekoon (themaveesw07@gmail.com)', 'unread', '2026-03-04 08:24:50'),
(81, 'registered_students', 53, '622032411', 'UPDATE', 'Student record updated for Themavee Wijekoon, graduation payment status: paid', 'unread', '2026-03-04 08:24:50'),
(82, 'registered_students', 55, '917032413', 'INSERT', 'New student registered: Ms. Nilakshi Sivathas (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-04 09:00:42'),
(83, 'payment_records', 9, '528032402', 'INSERT', 'New payment record added for student ID 528032402, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-04 10:53:26'),
(84, 'email_log', 9, '528032402', 'INSERT', 'Email sent to Ms. Kiruthika Paramananthan (paramananthankiruththika@gmail.com)', 'unread', '2026-03-04 10:53:26'),
(85, 'registered_students', 18, '528032402', 'UPDATE', 'Student record updated for Ms. Kiruthika Paramananthan, graduation payment status: paid', 'unread', '2026-03-04 10:53:26'),
(86, 'registered_students', 56, '917032427', 'INSERT', 'New student registered: Ms. Thachchana Moorthy Stelina (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-04 11:20:03'),
(87, 'registered_students', 57, '917032415', 'INSERT', 'New student registered: Ms. Vishalani Mahendran (Program: BTEC Higher National Diploma in Business Management - Batch 17)', 'unread', '2026-03-04 11:49:45'),
(88, 'registered_students', 58, '917032478', 'INSERT', 'New student registered: Ms. Chathupama Perera (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-04 17:37:54'),
(89, 'registered_students', 59, '917032451', 'INSERT', 'New student registered: Ms. Niamath Shakeel (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-04 23:41:47'),
(90, 'payment_records', 10, '917032429', 'INSERT', 'New payment record added for student ID 917032429, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-05 03:51:37'),
(91, 'email_log', 10, '917032429', 'INSERT', 'Email sent to Lacshithi Saravanan (lacshithisaravanan1111@gmail.com)', 'unread', '2026-03-05 03:51:37'),
(92, 'registered_students', 7, '917032429', 'UPDATE', 'Student record updated for Lacshithi Saravanan, graduation payment status: paid', 'unread', '2026-03-05 03:51:37'),
(93, 'payment_records', 11, '917032407', 'INSERT', 'New payment record added for student ID 917032407, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-05 04:18:02'),
(94, 'email_log', 11, '917032407', 'INSERT', 'Email sent to Ms. Ursula Pavithree Wannige (Ursula.Pavithree@outlook.com)', 'unread', '2026-03-05 04:18:02'),
(95, 'registered_students', 25, '917032407', 'UPDATE', 'Student record updated for Ms. Ursula Pavithree Wannige, graduation payment status: paid', 'unread', '2026-03-05 04:18:02'),
(96, 'registered_students', 60, '528032434', 'INSERT', 'New student registered: Ms.  Kosgodage Thewni Nadara Dharmasiri (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-05 05:05:36'),
(97, 'payment_records', 12, '528032434', 'INSERT', 'New payment record added for student ID 528032434, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-05 05:09:03'),
(98, 'email_log', 12, '528032434', 'INSERT', 'Email sent to Ms.  Kosgodage Thewni Nadara Dharmasiri (thewnidharmasiri@gmail.com)', 'unread', '2026-03-05 05:09:03'),
(99, 'registered_students', 60, '528032434', 'UPDATE', 'Student record updated for Ms.  Kosgodage Thewni Nadara Dharmasiri, graduation payment status: paid', 'unread', '2026-03-05 05:09:03'),
(100, 'registered_students', 61, '917032402', 'INSERT', 'New student registered: Ms. Minda Oliniya Rozairo (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-05 11:44:06'),
(101, 'registered_students', 62, '528032431', 'INSERT', 'New student registered: Mr. Mohammed Azwear Ali Mohammed Aadhil Najmi (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-06 02:54:41'),
(102, 'registered_students', 63, '917032423', 'INSERT', 'New student registered: Mr. Mohamed Mawfeen Umar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-06 04:38:02'),
(103, 'registered_students', 64, '528032420', 'INSERT', 'New student registered: Mr. Ugendraraj Vijayakumar (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-06 04:49:25'),
(104, 'payment_records', 13, '528032420', 'INSERT', 'New payment record added for student ID 528032420, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-06 04:55:11'),
(105, 'email_log', 13, '528032420', 'INSERT', 'Email sent to Mr. Ugendraraj Vijayakumar (vijayakumar.ugendraraj@bms.ac.lk)', 'unread', '2026-03-06 04:55:11'),
(106, 'registered_students', 64, '528032420', 'UPDATE', 'Student record updated for Mr. Ugendraraj Vijayakumar, graduation payment status: paid', 'unread', '2026-03-06 04:55:11'),
(107, 'payment_records', 14, '622032401', 'INSERT', 'New payment record added for student ID 622032401, program: Higher Diploma In Biotechnology Science - Batch 22, amount: 12000', 'unread', '2026-03-06 05:10:12'),
(108, 'email_log', 14, '622032401', 'INSERT', 'Email sent to Ms. Ama Ranathunga (aranathunga04@gmail.com)', 'unread', '2026-03-06 05:10:12'),
(109, 'registered_students', 26, '622032401', 'UPDATE', 'Student record updated for Ms. Ama Ranathunga, graduation payment status: paid', 'unread', '2026-03-06 05:10:12'),
(110, 'registered_students', 65, '528032414', 'INSERT', 'New student registered: Ms. Thewaratantrige Sathmini Navodya Fernando (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-06 05:49:06'),
(111, 'registered_students', 66, '917032472', 'INSERT', 'New student registered: Ms. Fathima Rishadha Uvais (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-06 05:58:10'),
(112, 'registered_students', 67, '528032412', 'INSERT', 'New student registered: Ms. Abinaya Balasingam (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-06 07:27:33'),
(113, 'payment_records', 15, '528032406', 'INSERT', 'New payment record added for student ID 528032406, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-06 07:36:47'),
(114, 'email_log', 15, '528032406', 'INSERT', 'Email sent to Ms. Gayashi Anupama Jayawardana (gayashianupama00@gmail.com)', 'unread', '2026-03-06 07:36:47'),
(115, 'registered_students', 42, '528032406', 'UPDATE', 'Student record updated for Ms. Gayashi Anupama Jayawardana, graduation payment status: paid', 'unread', '2026-03-06 07:36:47'),
(116, 'payment_records', 16, '528032418', 'INSERT', 'New payment record added for student ID 528032418, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-06 07:38:24'),
(117, 'email_log', 16, '528032418', 'INSERT', 'Email sent to Ms. Asini  Fernando (asinikithma@gmail.com)', 'unread', '2026-03-06 07:38:24'),
(118, 'registered_students', 44, '528032418', 'UPDATE', 'Student record updated for Ms. Asini  Fernando, graduation payment status: paid', 'unread', '2026-03-06 07:38:24'),
(119, 'registered_students', 68, '917032487', 'INSERT', 'New student registered: Ms. Muhammad Siraj Fathima Safiyya (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-06 09:04:24'),
(120, 'extra_ticket_log', 1, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-06 09:16:06'),
(121, 'extra_ticket_log', 1, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-06 09:19:17'),
(122, 'payment_records', 17, '917032470', 'INSERT', 'New payment record added for student ID 917032470, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-06 09:19:28'),
(123, 'email_log', 17, '917032470', 'INSERT', 'Email sent to Mr. Umair Imthiyasdeen (mohamedumair999@gmail.com)', 'unread', '2026-03-06 09:19:28'),
(124, 'registered_students', 48, '917032470', 'UPDATE', 'Student record updated for Mr. Umair Imthiyasdeen, graduation payment status: paid', 'unread', '2026-03-06 09:19:28'),
(125, 'extra_ticket_log', 2, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-06 09:20:42'),
(126, 'extra_ticket_log', 1, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-06 09:24:26'),
(127, 'registered_students', 69, '528032428', 'INSERT', 'New student registered: Mr. Mohamed Iesa Ismail (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-06 11:18:32'),
(128, 'registered_students', 70, '622032405', 'INSERT', 'New student registered: Ms. Nauththuduwa Liyanage Don Kavisha Kalhari (Program: Higher Diploma In Biotechnology Science - Batch 22)', 'unread', '2026-03-06 13:51:59'),
(129, 'registered_students', 71, '528032408', 'INSERT', 'New student registered: Ms. Sahar Khushbu Nadeem (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-06 18:58:28'),
(130, 'registered_students', 72, '528032413', 'INSERT', 'New student registered: Ms. Tharuniya Anpalakan (Program: Biomedical science)', 'unread', '2026-03-06 23:09:03'),
(131, 'registered_students', 73, '917032446', 'INSERT', 'New student registered: Mr. Krithigan Sugumaran (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 04:58:57'),
(132, 'payment_records', 18, '9170324100', 'INSERT', 'New payment record added for student ID 9170324100, program: BTEC Higher National Diploma in Business - Batch 17, amount: 14000', 'unread', '2026-03-07 05:04:03'),
(133, 'email_log', 18, '9170324100', 'INSERT', 'Email sent to Seyed Muhammed Hiraz Hibshy Mowlana (hirazmowlana@gmail.com)', 'unread', '2026-03-07 05:04:03'),
(134, 'registered_students', 8, '9170324100', 'UPDATE', 'Student record updated for Seyed Muhammed Hiraz Hibshy Mowlana, graduation payment status: paid', 'unread', '2026-03-07 05:04:03'),
(135, 'payment_records', 19, '917032446', 'INSERT', 'New payment record added for student ID 917032446, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-07 05:12:12'),
(136, 'email_log', 19, '917032446', 'INSERT', 'Email sent to Mr. Krithigan Sugumaran (skrithigan@gmail.com)', 'unread', '2026-03-07 05:12:12'),
(137, 'registered_students', 73, '917032446', 'UPDATE', 'Student record updated for Mr. Krithigan Sugumaran, graduation payment status: paid', 'unread', '2026-03-07 05:12:12'),
(138, 'payment_records', 20, '528032442', 'INSERT', 'New payment record added for student ID 528032442, program: Higher Diploma in Biomedical Science - Batch 28, amount: 14000', 'unread', '2026-03-07 05:20:15'),
(139, 'email_log', 20, '528032442', 'INSERT', 'Email sent to Ms. Apitha Suresh (apithasuresh624@gmail.com)', 'unread', '2026-03-07 05:20:15'),
(140, 'registered_students', 34, '528032442', 'UPDATE', 'Student record updated for Ms. Apitha Suresh, graduation payment status: paid', 'unread', '2026-03-07 05:20:15'),
(141, 'payment_records', 21, '528032419', 'INSERT', 'New payment record added for student ID 528032419, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-07 05:21:17'),
(142, 'email_log', 21, '528032419', 'INSERT', 'Email sent to Ms. Harithra Ramesh (harithraramesh7@gmail.com)', 'unread', '2026-03-07 05:21:17'),
(143, 'registered_students', 35, '528032419', 'UPDATE', 'Student record updated for Ms. Harithra Ramesh, graduation payment status: paid', 'unread', '2026-03-07 05:21:17'),
(144, 'registered_students', 74, '917032488', 'INSERT', 'New student registered: Adeeshan Saravanamohan (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 05:24:00'),
(145, 'registered_students', 75, '917032430', 'INSERT', 'New student registered: Muhammad Luqman Azwar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 05:31:24'),
(146, 'registered_students', 76, '917032459', 'INSERT', 'New student registered: Sinthiya Madanmohan (Program: BTEC Higher National Diploma in Business- Batch 17)', 'unread', '2026-03-07 05:35:02'),
(147, 'payment_records', 22, '1004032401', 'INSERT', 'New payment record added for student ID 1004032401, program: Higher Diploma in Food Science and Nutrition- Batch 04, amount: 12000', 'unread', '2026-03-07 06:09:26'),
(148, 'email_log', 22, '1004032401', 'INSERT', 'Email sent to Ms. Wijesiri Narange Ashani Samudika (samudika99@outlook.com)', 'unread', '2026-03-07 06:09:26'),
(149, 'registered_students', 41, '1004032401', 'UPDATE', 'Student record updated for Ms. Wijesiri Narange Ashani Samudika, graduation payment status: paid', 'unread', '2026-03-07 06:09:26'),
(150, 'registered_students', 77, '917032437', 'INSERT', 'New student registered: Nimenma Methsiluni (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 06:14:05'),
(151, 'payment_records', 23, '917032425', 'INSERT', 'New payment record added for student ID 917032425, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-07 06:52:25'),
(152, 'email_log', 23, '917032425', 'INSERT', 'Email sent to Ms. Dharshini Chandran (dharshini.chandran@bms.ac.lk)', 'unread', '2026-03-07 06:52:25'),
(153, 'registered_students', 5, '917032425', 'UPDATE', 'Student record updated for Ms. Dharshini Chandran, graduation payment status: paid', 'unread', '2026-03-07 06:52:25'),
(154, 'payment_records', 24, '917032434', 'INSERT', 'New payment record added for student ID 917032434, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-07 06:53:51'),
(155, 'email_log', 24, '917032434', 'INSERT', 'Email sent to Ms. Robeka Subramaniam (subramaniamrobeka@gmail.com)', 'unread', '2026-03-07 06:53:51'),
(156, 'registered_students', 9, '917032434', 'UPDATE', 'Student record updated for Ms. Robeka Subramaniam, graduation payment status: paid', 'unread', '2026-03-07 06:53:51'),
(157, 'admin', 20, NULL, 'insert', 'New admin added: Ayesha (Role: admin)', 'unread', '2026-03-07 07:02:44'),
(158, 'admin', 21, NULL, 'insert', 'New admin added: Darshika (Role: admin)', 'unread', '2026-03-07 07:10:33'),
(159, 'payment_records', 25, '917032433', 'INSERT', 'New payment record added for student ID 917032433, program: BTEC Higher National Diploma in Business - Batch 17, amount: 14000', 'unread', '2026-03-07 07:29:21'),
(160, 'email_log', 25, '917032433', 'INSERT', 'Email sent to Ms. Fathima Shamla Imnaz (imnazshamla@gmail.com)', 'unread', '2026-03-07 07:29:21'),
(161, 'registered_students', 13, '917032433', 'UPDATE', 'Student record updated for Ms. Fathima Shamla Imnaz, graduation payment status: paid', 'unread', '2026-03-07 07:29:21'),
(162, 'payment_records', 26, '917032448', 'INSERT', 'New payment record added for student ID 917032448, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-07 07:33:14'),
(163, 'email_log', 26, '917032448', 'INSERT', 'Email sent to Ms. Habeeba Zareen (habeebazareen6@gmail.com)', 'unread', '2026-03-07 07:33:14'),
(164, 'registered_students', 47, '917032448', 'UPDATE', 'Student record updated for Ms. Habeeba Zareen, graduation payment status: paid', 'unread', '2026-03-07 07:33:14'),
(165, 'extra_ticket_log', 2, NULL, 'INSERT', 'Added 2 extra ticket(s) worth Rs. 4000.00', 'unread', '2026-03-07 07:55:20'),
(166, 'registered_students', 76, '917032459', 'UPDATE', 'Student record updated for Sinthiya Madanmohan, graduation payment status: Not-Completed', 'unread', '2026-03-07 08:59:03'),
(167, 'payment_records', 27, '917032459', 'INSERT', 'New payment record added for student ID 917032459, program: BTEC Higher National Diploma in Business - Batch 17, amount: 14000', 'unread', '2026-03-07 09:01:09'),
(168, 'email_log', 27, '917032459', 'INSERT', 'Email sent to Sinthiya Madanmohan (sinthiyamathanmohan@gmail.com)', 'unread', '2026-03-07 09:01:09'),
(169, 'registered_students', 76, '917032459', 'UPDATE', 'Student record updated for Sinthiya Madanmohan, graduation payment status: paid', 'unread', '2026-03-07 09:01:09'),
(170, 'registered_students', 78, '528032435', 'INSERT', 'New student registered: Ms. Haribaashini Muralitharan (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-07 09:55:32'),
(171, 'payment_records', 28, '917032432', 'INSERT', 'New payment record added for student ID 917032432, program: BTEC Higher National Diploma in Business - Batch 17, amount: 14000', 'unread', '2026-03-07 10:12:16'),
(172, 'email_log', 28, '917032432', 'INSERT', 'Email sent to Ms. Rishka Fazaal (rishkafazaal@gmail.com)', 'unread', '2026-03-07 10:12:16'),
(173, 'registered_students', 31, '917032432', 'UPDATE', 'Student record updated for Ms. Rishka Fazaal, graduation payment status: paid', 'unread', '2026-03-07 10:12:16'),
(174, 'payment_records', 29, '917032464', 'INSERT', 'New payment record added for student ID 917032464, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-07 10:13:20'),
(175, 'email_log', 29, '917032464', 'INSERT', 'Email sent to Ms. Vismida Thachanamoorthy (mtmvismida.08t@gmail.com)', 'unread', '2026-03-07 10:13:20'),
(176, 'registered_students', 4, '917032464', 'UPDATE', 'Student record updated for Ms. Vismida Thachanamoorthy, graduation payment status: paid', 'unread', '2026-03-07 10:13:20'),
(177, 'payment_records', 30, '917032423', 'INSERT', 'New payment record added for student ID 917032423, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-07 10:14:32'),
(178, 'email_log', 30, '917032423', 'INSERT', 'Email sent to Mr. Mohamed Mawfeen Umar (mawfeen.umar@bms.ac.lk)', 'unread', '2026-03-07 10:14:32'),
(179, 'registered_students', 63, '917032423', 'UPDATE', 'Student record updated for Mr. Mohamed Mawfeen Umar, graduation payment status: paid', 'unread', '2026-03-07 10:14:32'),
(180, 'payment_records', 31, '917032437', 'INSERT', 'New payment record added for student ID 917032437, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-07 10:23:03'),
(181, 'email_log', 31, '917032437', 'INSERT', 'Email sent to Nimenma Methsiluni (nimethma.arachchi@bms.ac.lk)', 'unread', '2026-03-07 10:23:03'),
(182, 'registered_students', 77, '917032437', 'UPDATE', 'Student record updated for Nimenma Methsiluni, graduation payment status: paid', 'unread', '2026-03-07 10:23:03'),
(183, 'extra_ticket_log', 3, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-07 10:24:29'),
(184, 'registered_students', 79, '917032449', 'INSERT', 'New student registered: Mr. Iqbal Abdul Muyeeth (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 10:36:12'),
(185, 'payment_records', 32, '917032449', 'INSERT', 'New payment record added for student ID 917032449, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-07 10:37:20'),
(186, 'email_log', 32, '917032449', 'INSERT', 'Email sent to Mr. Iqbal Abdul Muyeeth (muyeeth4@gmail.com)', 'unread', '2026-03-07 10:37:20'),
(187, 'registered_students', 79, '917032449', 'UPDATE', 'Student record updated for Mr. Iqbal Abdul Muyeeth, graduation payment status: paid', 'unread', '2026-03-07 10:37:20'),
(188, 'registered_students', 57, '917032415', 'UPDATE', 'Student record updated for Ms. Vishalani Mahendran, graduation payment status: Not-Completed', 'unread', '2026-03-07 10:50:37'),
(189, 'registered_students', 57, '917032415', 'UPDATE', 'Student record updated for Ms. Vishalani Mahendran, graduation payment status: Not-Completed', 'unread', '2026-03-07 10:50:47'),
(190, 'payment_records', 33, '917032415', 'INSERT', 'New payment record added for student ID 917032415, program: BTEC Higher National Diploma in Business - Batch 17, amount: 14000', 'unread', '2026-03-07 10:51:56'),
(191, 'email_log', 33, '917032415', 'INSERT', 'Email sent to Ms. Vishalani Mahendran (mahendranvishalani1708@gmail.com)', 'unread', '2026-03-07 10:51:56'),
(192, 'registered_students', 57, '917032415', 'UPDATE', 'Student record updated for Ms. Vishalani Mahendran, graduation payment status: paid', 'unread', '2026-03-07 10:51:56'),
(193, 'registered_students', 80, '917032494', 'INSERT', 'New student registered: Yuthmini Malsiluni (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 10:52:08'),
(194, 'registered_students', 81, '528032405', 'INSERT', 'New student registered: Ms. Sayini Pushpakumar (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-07 12:13:54'),
(195, 'registered_students', 82, '917032458', 'INSERT', 'New student registered: M.M. Yoonus Ahamedh (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 13:10:17'),
(196, 'registered_students', 83, '917032436', 'INSERT', 'New student registered: Nadanasabapathy Dhilrukshi (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-07 13:15:00'),
(197, 'registered_students', 84, '917032438', 'INSERT', 'New student registered: Ms. Naysa Rishona Amarasinghe (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 03:01:36'),
(198, 'registered_students', 85, '917032401', 'INSERT', 'New student registered: Sangeetha Sivanesan (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 05:01:58'),
(199, 'payment_records', 34, '917032401', 'INSERT', 'New payment record added for student ID 917032401, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-08 05:05:27'),
(200, 'email_log', 34, '917032401', 'INSERT', 'Email sent to Sangeetha Sivanesan (sangeetha.sivanesan@bms.ac.lk)', 'unread', '2026-03-08 05:05:27'),
(201, 'registered_students', 85, '917032401', 'UPDATE', 'Student record updated for Sangeetha Sivanesan, graduation payment status: paid', 'unread', '2026-03-08 05:05:27'),
(202, 'payment_records', 35, '917032403', 'INSERT', 'New payment record added for student ID 917032403, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-08 05:39:58'),
(203, 'email_log', 35, '917032403', 'INSERT', 'Email sent to Ms. Aminath Ayamin Rasheed (ayaako567@gmail.com)', 'unread', '2026-03-08 05:39:58'),
(204, 'registered_students', 3, '917032403', 'UPDATE', 'Student record updated for Ms. Aminath Ayamin Rasheed, graduation payment status: paid', 'unread', '2026-03-08 05:39:58'),
(205, 'payment_records', 36, '917032456', 'INSERT', 'New payment record added for student ID 917032456, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-08 05:41:31'),
(206, 'email_log', 36, '917032456', 'INSERT', 'Email sent to Ms. Rithika Chandravathanan (rithikka21@gmail.com)', 'unread', '2026-03-08 05:41:31'),
(207, 'registered_students', 11, '917032456', 'UPDATE', 'Student record updated for Ms. Rithika Chandravathanan, graduation payment status: paid', 'unread', '2026-03-08 05:41:31'),
(208, 'payment_records', 37, '917032438', 'INSERT', 'New payment record added for student ID 917032438, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-08 05:42:51'),
(209, 'email_log', 37, '917032438', 'INSERT', 'Email sent to Ms. Naysa Rishona Amarasinghe (naysa.amarasinghe@bms.ac.lk)', 'unread', '2026-03-08 05:42:51'),
(210, 'registered_students', 84, '917032438', 'UPDATE', 'Student record updated for Ms. Naysa Rishona Amarasinghe, graduation payment status: paid', 'unread', '2026-03-08 05:42:51'),
(211, 'registered_students', 86, '917032439', 'INSERT', 'New student registered: Mr. Thanooj Kathirason (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 05:45:24'),
(212, 'payment_records', 38, '917032439', 'INSERT', 'New payment record added for student ID 917032439, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-08 05:49:09'),
(213, 'email_log', 38, '917032439', 'INSERT', 'Email sent to Mr. Thanooj Kathirason (Thanooj987@gmail.com)', 'unread', '2026-03-08 05:49:09'),
(214, 'registered_students', 86, '917032439', 'UPDATE', 'Student record updated for Mr. Thanooj Kathirason, graduation payment status: paid', 'unread', '2026-03-08 05:49:09'),
(215, 'payment_records', 39, '917032478', 'INSERT', 'New payment record added for student ID 917032478, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-08 06:22:05'),
(216, 'email_log', 39, '917032478', 'INSERT', 'Email sent to Ms. Chathupama Perera (chathupamaperera1234@gmail.com)', 'unread', '2026-03-08 06:22:05'),
(217, 'registered_students', 58, '917032478', 'UPDATE', 'Student record updated for Ms. Chathupama Perera, graduation payment status: paid', 'unread', '2026-03-08 06:22:05'),
(218, 'extra_ticket_log', 4, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-08 06:28:28'),
(219, 'payment_records', 40, '9170324102', 'INSERT', 'New payment record added for student ID 9170324102, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-08 06:30:03'),
(220, 'email_log', 40, '9170324102', 'INSERT', 'Email sent to Mr.Enosh Ganesh (enoshganesh1@gmail.com)', 'unread', '2026-03-08 06:30:03'),
(221, 'registered_students', 2, '9170324102', 'UPDATE', 'Student record updated for Mr.Enosh Ganesh, graduation payment status: paid', 'unread', '2026-03-08 06:30:03'),
(222, 'payment_records', 41, '917032496', 'INSERT', 'New payment record added for student ID 917032496, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-08 06:48:38'),
(223, 'email_log', 41, '917032496', 'INSERT', 'Email sent to Ms. Risda Sahna Rizwan (risda.rizwan@bms.ac.lk)', 'unread', '2026-03-08 06:48:38'),
(224, 'registered_students', 46, '917032496', 'UPDATE', 'Student record updated for Ms. Risda Sahna Rizwan, graduation payment status: paid', 'unread', '2026-03-08 06:48:38'),
(225, 'registered_students', 87, '917032466', 'INSERT', 'New student registered: Mr. Mohomad Fazeem Wazeem (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 06:52:01'),
(226, 'registered_students', 88, '917032461', 'INSERT', 'New student registered: Mr. Yoosuf Sulaiman (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 06:57:22'),
(227, 'registered_students', 89, '917032452', 'INSERT', 'New student registered: Ms. Lakshi Karnan (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 07:00:19'),
(228, 'registered_students', 90, '917032418', 'INSERT', 'New student registered: Ms. Dushyanthy Rajendran (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 07:09:25'),
(229, 'registered_students', 91, '9170324101', 'INSERT', 'New student registered: Mr. Ahmed Walid Imthiyas (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 07:19:37'),
(230, 'registered_students', 92, '917032405', 'INSERT', 'New student registered: Ms. M. R. Rahmath Rashidha (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 07:57:14'),
(231, 'registered_students', 93, '917032468', 'INSERT', 'New student registered: Ms. Fathima Haleema Yoosuf (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 07:58:54'),
(232, 'registered_students', 94, '917032442', 'INSERT', 'New student registered: Ms. Fathima Nuzrath Mohammed Naizer (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 08:50:35'),
(233, 'registered_students', 95, '917032443', 'INSERT', 'New student registered: Mr. Aathif Anver (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 09:11:08'),
(234, 'registered_students', 96, '914032338', 'INSERT', 'New student registered: Mr. Mohomed Ruzain Imad (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 10:42:38'),
(235, 'registered_students', 97, '917032469', 'INSERT', 'New student registered: Ms. Suvidana Selvaraja (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 13:18:29'),
(236, 'registered_students', 98, '917032480', 'INSERT', 'New student registered: Mushab Aslam (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-08 23:47:01'),
(237, 'registered_students', 99, '917032485', 'INSERT', 'New student registered: Hettiarachchilage Prabodha Vidumini (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-09 02:41:32'),
(238, 'payment_records', 42, '622032408', 'INSERT', 'New payment record added for student ID 622032408, program: Higher Diploma In Biotechnology Science - Batch 22, amount: 12000', 'unread', '2026-03-09 05:01:42'),
(239, 'email_log', 42, '622032408', 'INSERT', 'Email sent to Ms. Moksha Chathurika Dananjane (dhanushka2002426@gmail.com)', 'unread', '2026-03-09 05:01:42'),
(240, 'registered_students', 22, '622032408', 'UPDATE', 'Student record updated for Ms. Moksha Chathurika Dananjane, graduation payment status: paid', 'unread', '2026-03-09 05:01:42'),
(241, 'registered_students', 100, '917032471', 'INSERT', 'New student registered: Mr. Abdul Rahman Ashroff (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-09 05:09:04'),
(242, 'payment_records', 43, '917032471', 'INSERT', 'New payment record added for student ID 917032471, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 05:19:56'),
(243, 'email_log', 43, '917032471', 'INSERT', 'Email sent to Mr. Abdul Rahman Ashroff (aliabdurrahman2042001@gmail.com)', 'unread', '2026-03-09 05:19:56'),
(244, 'registered_students', 100, '917032471', 'UPDATE', 'Student record updated for Mr. Abdul Rahman Ashroff, graduation payment status: paid', 'unread', '2026-03-09 05:19:56'),
(245, 'payment_records', 44, '917032494', 'INSERT', 'New payment record added for student ID 917032494, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-09 05:54:19'),
(246, 'email_log', 44, '917032494', 'INSERT', 'Email sent to Yuthmini Malsiluni (yuthmini.malsiluni@bms.ac.lk)', 'unread', '2026-03-09 05:54:19'),
(247, 'registered_students', 80, '917032494', 'UPDATE', 'Student record updated for Yuthmini Malsiluni, graduation payment status: paid', 'unread', '2026-03-09 05:54:19'),
(248, 'payment_records', 45, '917032485', 'INSERT', 'New payment record added for student ID 917032485, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 05:55:36'),
(249, 'email_log', 45, '917032485', 'INSERT', 'Email sent to Hettiarachchilage Prabodha Vidumini (Praboda.vidumini@bms.ac.lk)', 'unread', '2026-03-09 05:55:36'),
(250, 'registered_students', 99, '917032485', 'UPDATE', 'Student record updated for Hettiarachchilage Prabodha Vidumini, graduation payment status: paid', 'unread', '2026-03-09 05:55:36'),
(251, 'payment_records', 46, '528032427', 'INSERT', 'New payment record added for student ID 528032427, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-09 05:56:22'),
(252, 'email_log', 46, '528032427', 'INSERT', 'Email sent to Mr. V Kanistan Agash (kanistanakash15@gmail.com)', 'unread', '2026-03-09 05:56:22'),
(253, 'registered_students', 1, '528032427', 'UPDATE', 'Student record updated for Mr. V Kanistan Agash, graduation payment status: paid', 'unread', '2026-03-09 05:56:22'),
(254, 'payment_records', 47, '528032412', 'INSERT', 'New payment record added for student ID 528032412, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 05:57:22'),
(255, 'email_log', 47, '528032412', 'INSERT', 'Email sent to Ms. Abinaya Balasingam (abinaya.balasingam@bms.ac.lk)', 'unread', '2026-03-09 05:57:22'),
(256, 'registered_students', 67, '528032412', 'UPDATE', 'Student record updated for Ms. Abinaya Balasingam, graduation payment status: paid', 'unread', '2026-03-09 05:57:22'),
(257, 'payment_records', 48, '528032405', 'INSERT', 'New payment record added for student ID 528032405, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-09 05:58:14'),
(258, 'email_log', 48, '528032405', 'INSERT', 'Email sent to Ms. Sayini Pushpakumar (sayinipushpakumar@gmail.com)', 'unread', '2026-03-09 05:58:14'),
(259, 'registered_students', 81, '528032405', 'UPDATE', 'Student record updated for Ms. Sayini Pushpakumar, graduation payment status: paid', 'unread', '2026-03-09 05:58:14'),
(260, 'payment_records', 49, '917032430', 'INSERT', 'New payment record added for student ID 917032430, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 06:10:38'),
(261, 'email_log', 49, '917032430', 'INSERT', 'Email sent to Muhammad Luqman Azwar (luqmanazwar7@gmail.com)', 'unread', '2026-03-09 06:10:38'),
(262, 'registered_students', 75, '917032430', 'UPDATE', 'Student record updated for Muhammad Luqman Azwar, graduation payment status: paid', 'unread', '2026-03-09 06:10:38'),
(263, 'payment_records', 50, '917032488', 'INSERT', 'New payment record added for student ID 917032488, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-09 06:11:33'),
(264, 'email_log', 50, '917032488', 'INSERT', 'Email sent to Adeeshan Saravanamohan (adeeshan08@gmail.com)', 'unread', '2026-03-09 06:11:33'),
(265, 'registered_students', 74, '917032488', 'UPDATE', 'Student record updated for Adeeshan Saravanamohan, graduation payment status: paid', 'unread', '2026-03-09 06:11:33'),
(266, 'payment_records', 51, '917032450', 'INSERT', 'New payment record added for student ID 917032450, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 06:14:20'),
(267, 'email_log', 51, '917032450', 'INSERT', 'Email sent to Ms. Abdul Rasal Fahma (abdul.fahma@bms.ac.lk)', 'unread', '2026-03-09 06:14:20'),
(268, 'registered_students', 12, '917032450', 'UPDATE', 'Student record updated for Ms. Abdul Rasal Fahma, graduation payment status: paid', 'unread', '2026-03-09 06:14:20'),
(269, 'admin', 22, NULL, 'insert', 'New admin added: Erangee (Role: admin)', 'unread', '2026-03-09 06:22:31'),
(270, 'payment_records', 52, '528032431', 'INSERT', 'New payment record added for student ID 528032431, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 06:36:11'),
(271, 'email_log', 52, '528032431', 'INSERT', 'Email sent to Mr. Mohammed Azwear Ali Mohammed Aadhil Najmi (aadhilnajmi2004@gmail.com)', 'unread', '2026-03-09 06:36:11');
INSERT INTO `notifications` (`id`, `table_name`, `record_id`, `student_id`, `action_type`, `description`, `status`, `created_at`) VALUES
(272, 'registered_students', 62, '528032431', 'UPDATE', 'Student record updated for Mr. Mohammed Azwear Ali Mohammed Aadhil Najmi, graduation payment status: paid', 'unread', '2026-03-09 06:36:11'),
(273, 'payment_records', 53, '528032433', 'INSERT', 'New payment record added for student ID 528032433, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 06:37:57'),
(274, 'email_log', 53, '528032433', 'INSERT', 'Email sent to Ms. Shamila Nijamdeen (shamila.nijamdeen@bms.ac.lk)', 'unread', '2026-03-09 06:37:57'),
(275, 'registered_students', 40, '528032433', 'UPDATE', 'Student record updated for Ms. Shamila Nijamdeen, graduation payment status: paid', 'unread', '2026-03-09 06:37:57'),
(276, 'payment_records', 54, '528032430', 'INSERT', 'New payment record added for student ID 528032430, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-09 06:38:58'),
(277, 'email_log', 54, '528032430', 'INSERT', 'Email sent to Ms. Sajitha Makenthiran (sajitha.makenthiran@bms.ac.lk)', 'unread', '2026-03-09 06:38:58'),
(278, 'registered_students', 24, '528032430', 'UPDATE', 'Student record updated for Ms. Sajitha Makenthiran, graduation payment status: paid', 'unread', '2026-03-09 06:38:58'),
(279, 'payment_records', 55, '528032438', 'INSERT', 'New payment record added for student ID 528032438, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 06:44:23'),
(280, 'email_log', 55, '528032438', 'INSERT', 'Email sent to Ms. N.Chamathka Edirirathna (nizayainsaad@gmail.com)', 'unread', '2026-03-09 06:44:23'),
(281, 'registered_students', 37, '528032438', 'UPDATE', 'Student record updated for Ms. N.Chamathka Edirirathna, graduation payment status: paid', 'unread', '2026-03-09 06:44:23'),
(282, 'payment_records', 56, '1002032304', 'INSERT', 'New payment record added for student ID 1002032304, program: Higher Diploma in Food Science and Nutrition- Batch 04, amount: 10000', 'unread', '2026-03-09 06:45:55'),
(283, 'email_log', 56, '1002032304', 'INSERT', 'Email sent to Mr. Arunasalam Rathushan (Arunasalam.rathushan@bms.ac.lk)', 'unread', '2026-03-09 06:45:55'),
(284, 'registered_students', 50, '1002032304', 'UPDATE', 'Student record updated for Mr. Arunasalam Rathushan, graduation payment status: paid', 'unread', '2026-03-09 06:45:55'),
(285, 'registered_students', 24, '528032430', 'UPDATE', NULL, 'unread', '2026-03-09 06:47:50'),
(286, 'payment_records', 57, '1102032401', 'INSERT', 'New payment record added for student ID 1102032401, program: Higher Diploma in Medical Biotechnology - Batch 02, amount: 12000', 'unread', '2026-03-09 06:48:49'),
(287, 'email_log', 57, '1102032401', 'INSERT', 'Email sent to Suha Ahmed Naseerdeen (suhasan1131@gmail.com)', 'unread', '2026-03-09 06:48:49'),
(288, 'registered_students', 28, '1102032401', 'UPDATE', 'Student record updated for Suha Ahmed Naseerdeen, graduation payment status: paid', 'unread', '2026-03-09 06:48:49'),
(289, 'registered_students', 101, '917032453', 'INSERT', 'New student registered: Mohamed Waseem Mashoor (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-09 08:04:01'),
(290, 'payment_records', 58, '917032427', 'INSERT', 'New payment record added for student ID 917032427, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-09 08:22:27'),
(291, 'email_log', 58, '917032427', 'INSERT', 'Email sent to Ms. Thachchana Moorthy Stelina (moorthystalina@gmail.com)', 'unread', '2026-03-09 08:22:27'),
(292, 'registered_students', 56, '917032427', 'UPDATE', 'Student record updated for Ms. Thachchana Moorthy Stelina, graduation payment status: paid', 'unread', '2026-03-09 08:22:27'),
(293, 'payment_records', 59, '917032480', 'INSERT', 'New payment record added for student ID 917032480, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 08:52:02'),
(294, 'email_log', 59, '917032480', 'INSERT', 'Email sent to Mushab Aslam (mushab.mahmood@bms.ac.lk)', 'unread', '2026-03-09 08:52:02'),
(295, 'registered_students', 98, '917032480', 'UPDATE', 'Student record updated for Mushab Aslam, graduation payment status: paid', 'unread', '2026-03-09 08:52:02'),
(296, 'payment_records', 60, '917032452', 'INSERT', 'New payment record added for student ID 917032452, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-09 08:54:13'),
(297, 'email_log', 60, '917032452', 'INSERT', 'Email sent to Ms. Lakshi Karnan (lakshikarunan840@gmail.com)', 'unread', '2026-03-09 08:54:13'),
(298, 'registered_students', 89, '917032452', 'UPDATE', 'Student record updated for Ms. Lakshi Karnan, graduation payment status: paid', 'unread', '2026-03-09 08:54:13'),
(299, 'payment_records', 61, '917032461', 'INSERT', 'New payment record added for student ID 917032461, program: BTEC Higher National Diploma in Business - Batch 17, amount: 14000', 'unread', '2026-03-09 08:56:49'),
(300, 'email_log', 61, '917032461', 'INSERT', 'Email sent to Mr. Yoosuf Sulaiman (yoosufsulaiman14@gmail.com)', 'unread', '2026-03-09 08:56:49'),
(301, 'registered_students', 88, '917032461', 'UPDATE', 'Student record updated for Mr. Yoosuf Sulaiman, graduation payment status: paid', 'unread', '2026-03-09 08:56:49'),
(302, 'payment_records', 62, '917032469', 'INSERT', 'New payment record added for student ID 917032469, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 08:58:04'),
(303, 'email_log', 62, '917032469', 'INSERT', 'Email sent to Ms. Suvidana Selvaraja (suvisuvidana@gmail.com)', 'unread', '2026-03-09 08:58:04'),
(304, 'registered_students', 97, '917032469', 'UPDATE', 'Student record updated for Ms. Suvidana Selvaraja, graduation payment status: paid', 'unread', '2026-03-09 08:58:04'),
(305, 'payment_records', 63, '917032466', 'INSERT', 'New payment record added for student ID 917032466, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 09:00:11'),
(306, 'email_log', 63, '917032466', 'INSERT', 'Email sent to Mr. Mohomad Fazeem Wazeem (fazeemwazeem@gmail.com)', 'unread', '2026-03-09 09:00:11'),
(307, 'registered_students', 87, '917032466', 'UPDATE', 'Student record updated for Mr. Mohomad Fazeem Wazeem, graduation payment status: paid', 'unread', '2026-03-09 09:00:11'),
(308, 'payment_records', 64, '917032404', 'INSERT', 'New payment record added for student ID 917032404, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 09:02:00'),
(309, 'email_log', 64, '917032404', 'INSERT', 'Email sent to Ms. Radinka Jinelli Fernando (radhinkafernando@gmail.com)', 'unread', '2026-03-09 09:02:00'),
(310, 'registered_students', 39, '917032404', 'UPDATE', 'Student record updated for Ms. Radinka Jinelli Fernando, graduation payment status: paid', 'unread', '2026-03-09 09:02:00'),
(311, 'registered_students', 102, '917032482', 'INSERT', 'New student registered: Miss. Harishmi Balaratnarajah Mohanakumar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-09 09:54:25'),
(312, 'payment_records', 65, '917032482', 'INSERT', 'New payment record added for student ID 917032482, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 10:00:20'),
(313, 'email_log', 65, '917032482', 'INSERT', 'Email sent to Miss. Harishmi Balaratnarajah Mohanakumar (harishmi.mohanakumar@bms.ac.lk)', 'unread', '2026-03-09 10:00:20'),
(314, 'registered_students', 102, '917032482', 'UPDATE', 'Student record updated for Miss. Harishmi Balaratnarajah Mohanakumar, graduation payment status: paid', 'unread', '2026-03-09 10:00:20'),
(315, 'payment_records', 66, '528032409', 'INSERT', 'New payment record added for student ID 528032409, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 10:07:54'),
(316, 'email_log', 66, '528032409', 'INSERT', 'Email sent to Ms. Nandhujah Gunasheharan (nandhujahvarma@gmail.com)', 'unread', '2026-03-09 10:07:54'),
(317, 'registered_students', 30, '528032409', 'UPDATE', 'Student record updated for Ms. Nandhujah Gunasheharan, graduation payment status: paid', 'unread', '2026-03-09 10:07:54'),
(318, 'payment_records', 67, '528032415', 'INSERT', 'New payment record added for student ID 528032415, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 10:09:29'),
(319, 'email_log', 67, '528032415', 'INSERT', 'Email sent to Ms. Saraniya Muralidaran (muralitharansaraniya@gmail.com)', 'unread', '2026-03-09 10:09:29'),
(320, 'registered_students', 54, '528032415', 'UPDATE', 'Student record updated for Ms. Saraniya Muralidaran, graduation payment status: paid', 'unread', '2026-03-09 10:09:29'),
(321, 'registered_students', 103, '528032436', 'INSERT', 'New student registered: Ms.  Zahara Ismail (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-09 10:17:57'),
(322, 'registered_students', 36, '917032416', 'UPDATE', 'Student record updated for Ms. Fathima Amra Najimudeen, graduation payment status: Not-Completed', 'unread', '2026-03-09 10:18:41'),
(323, 'payment_records', 68, '917032416', 'INSERT', 'New payment record added for student ID 917032416, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-09 10:19:34'),
(324, 'email_log', 68, '917032416', 'INSERT', 'Email sent to Ms. Fathima Amra Najimudeen (amranajimudeen23@gmail.com)', 'unread', '2026-03-09 10:19:34'),
(325, 'registered_students', 36, '917032416', 'UPDATE', 'Student record updated for Ms. Fathima Amra Najimudeen, graduation payment status: paid', 'unread', '2026-03-09 10:19:34'),
(326, 'payment_records', 69, '528032421', 'INSERT', 'New payment record added for student ID 528032421, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-09 10:20:59'),
(327, 'email_log', 69, '528032421', 'INSERT', 'Email sent to Mr. Ahamed Lebbe Ahamed Sahee (callmesahee02@gmail.com)', 'unread', '2026-03-09 10:20:59'),
(328, 'registered_students', 51, '528032421', 'UPDATE', 'Student record updated for Mr. Ahamed Lebbe Ahamed Sahee, graduation payment status: paid', 'unread', '2026-03-09 10:20:59'),
(329, 'payment_records', 70, '528032423', 'INSERT', 'New payment record added for student ID 528032423, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 10:22:36'),
(330, 'email_log', 70, '528032423', 'INSERT', 'Email sent to Ms. Malshi Imesha Pathiranage (malshi.pathiranage@bms.ac.lk)', 'unread', '2026-03-09 10:22:36'),
(331, 'registered_students', 38, '528032423', 'UPDATE', 'Student record updated for Ms. Malshi Imesha Pathiranage, graduation payment status: paid', 'unread', '2026-03-09 10:22:36'),
(332, 'payment_records', 71, '528032403', 'INSERT', 'New payment record added for student ID 528032403, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 10:23:58'),
(333, 'email_log', 71, '528032403', 'INSERT', 'Email sent to Thiththalapitiyage Shonaleen Varsha Fonseka (shonaleenfonseka@gmail.com)', 'unread', '2026-03-09 10:23:58'),
(334, 'registered_students', 19, '528032403', 'UPDATE', 'Student record updated for Thiththalapitiyage Shonaleen Varsha Fonseka, graduation payment status: paid', 'unread', '2026-03-09 10:23:58'),
(335, 'registered_students', 103, '528032436', 'UPDATE', 'Student record updated for Ms.  Zahara Ismail, graduation payment status: Not-Completed', 'unread', '2026-03-09 10:27:46'),
(336, 'payment_records', 72, '528032436', 'INSERT', 'New payment record added for student ID 528032436, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-09 10:29:59'),
(337, 'email_log', 72, '528032436', 'INSERT', 'Email sent to Ms.  Zahara Ismail (zaharaismail0075@gmail.com)', 'unread', '2026-03-09 10:29:59'),
(338, 'registered_students', 103, '528032436', 'UPDATE', 'Student record updated for Ms.  Zahara Ismail, graduation payment status: paid', 'unread', '2026-03-09 10:29:59'),
(339, 'registered_students', 104, '917032455', 'INSERT', 'New student registered: Haseef Ahamed (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-09 10:39:20'),
(340, 'payment_records', 73, '917032402', 'INSERT', 'New payment record added for student ID 917032402, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-09 10:46:54'),
(341, 'email_log', 73, '917032402', 'INSERT', 'Email sent to Ms. Minda Oliniya Rozairo (mindaoliniya@gmail.com)', 'unread', '2026-03-09 10:46:54'),
(342, 'registered_students', 61, '917032402', 'UPDATE', 'Student record updated for Ms. Minda Oliniya Rozairo, graduation payment status: paid', 'unread', '2026-03-09 10:46:54'),
(343, 'registered_students', 105, '528032441', 'INSERT', 'New student registered: Ms. Ramudhi Pemodhya De Silva (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-09 13:17:29'),
(344, 'payment_records', 74, '917032472', 'INSERT', 'New payment record added for student ID 917032472, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 03:10:30'),
(345, 'email_log', 74, '917032472', 'INSERT', 'Email sent to Ms. Fathima Rishadha Uvais (rishadha525@gmail.com)', 'unread', '2026-03-10 03:10:30'),
(346, 'registered_students', 66, '917032472', 'UPDATE', 'Student record updated for Ms. Fathima Rishadha Uvais, graduation payment status: paid', 'unread', '2026-03-10 03:10:30'),
(347, 'registered_students', 106, '917032476', 'INSERT', 'New student registered: Ms. Rabiyah Badurdeen (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 03:20:41'),
(348, 'payment_records', 75, '917032476', 'INSERT', 'New payment record added for student ID 917032476, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 03:45:08'),
(349, 'email_log', 75, '917032476', 'INSERT', 'Email sent to Ms. Rabiyah Badurdeen (fathima.badurdeen@bms.ac.lk)', 'unread', '2026-03-10 03:45:08'),
(350, 'registered_students', 106, '917032476', 'UPDATE', 'Student record updated for Ms. Rabiyah Badurdeen, graduation payment status: paid', 'unread', '2026-03-10 03:45:08'),
(351, 'registered_students', 107, '917032426', 'INSERT', 'New student registered: Ms. Malindri Laleesha Wijeyesinghe (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 04:10:44'),
(352, 'registered_students', 108, '528032444', 'INSERT', 'New student registered: Ms. Thisuri Cyara Jayaweera (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-10 05:13:16'),
(353, 'registered_students', 109, '528032429', 'INSERT', 'New student registered: Ms. A.M.M Chathumini Hansika Jayawardene (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-10 05:20:35'),
(354, 'payment_records', 76, '528032428', 'INSERT', 'New payment record added for student ID 528032428, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-10 05:43:26'),
(355, 'email_log', 76, '528032428', 'INSERT', 'Email sent to Mr. Mohamed Iesa Ismail (iesai3214@gmail.com)', 'unread', '2026-03-10 05:43:26'),
(356, 'registered_students', 69, '528032428', 'UPDATE', 'Student record updated for Mr. Mohamed Iesa Ismail, graduation payment status: paid', 'unread', '2026-03-10 05:43:26'),
(357, 'payment_records', 77, '528032444', 'INSERT', 'New payment record added for student ID 528032444, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-10 05:44:30'),
(358, 'email_log', 77, '528032444', 'INSERT', 'Email sent to Ms. Thisuri Cyara Jayaweera (thisuri.jayaweera@bms.ac.lk)', 'unread', '2026-03-10 05:44:30'),
(359, 'registered_students', 108, '528032444', 'UPDATE', 'Student record updated for Ms. Thisuri Cyara Jayaweera, graduation payment status: paid', 'unread', '2026-03-10 05:44:30'),
(360, 'payment_records', 78, '528032429', 'INSERT', 'New payment record added for student ID 528032429, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-10 05:45:24'),
(361, 'email_log', 78, '528032429', 'INSERT', 'Email sent to Ms. A.M.M Chathumini Hansika Jayawardene (chathumini.jayawardena@bms.ac.lk)', 'unread', '2026-03-10 05:45:24'),
(362, 'registered_students', 109, '528032429', 'UPDATE', 'Student record updated for Ms. A.M.M Chathumini Hansika Jayawardene, graduation payment status: paid', 'unread', '2026-03-10 05:45:24'),
(363, 'payment_records', 79, '528032408', 'INSERT', 'New payment record added for student ID 528032408, program: Higher Diploma in Biomedical Science - Batch 28, amount: 12000', 'unread', '2026-03-10 05:49:39'),
(364, 'email_log', 79, '528032408', 'INSERT', 'Email sent to Ms. Sahar Khushbu Nadeem (sahar.nadeem@bms.ac.lk)', 'unread', '2026-03-10 05:49:39'),
(365, 'registered_students', 71, '528032408', 'UPDATE', 'Student record updated for Ms. Sahar Khushbu Nadeem, graduation payment status: paid', 'unread', '2026-03-10 05:49:39'),
(366, 'admin', 16, NULL, 'update', 'Admin updated: Clancy (Role: registrationDesk)', 'unread', '2026-03-10 06:09:09'),
(367, 'admin', 16, NULL, 'update', 'Admin updated: Clancy (Role: admin)', 'unread', '2026-03-10 06:10:32'),
(368, 'payment_records', 80, '528032443', 'INSERT', 'New payment record added for student ID 528032443, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-10 06:19:09'),
(369, 'email_log', 80, '528032443', 'INSERT', 'Email sent to Mr. Dhanushan Sekar (dhanushansekar@gmail.com)', 'unread', '2026-03-10 06:19:09'),
(370, 'registered_students', 43, '528032443', 'UPDATE', 'Student record updated for Mr. Dhanushan Sekar, graduation payment status: paid', 'unread', '2026-03-10 06:19:09'),
(371, 'registered_students', 110, '917032419', 'INSERT', 'New student registered: Leena Manohar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 06:29:21'),
(372, 'registered_students', 111, '528032425', 'INSERT', 'New student registered: Ms. Hidhushi Mahendran (Program: Higher Diploma in Biomedical Science - Batch 28)', 'unread', '2026-03-10 06:56:07'),
(373, 'registered_students', 112, '917032420', 'INSERT', 'New student registered: Ms. Ifla Imran (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 07:06:13'),
(374, 'registered_students', 113, '917032444', 'INSERT', 'New student registered: Mr. Don Anarga Joshua Colombege (Program: BTEC Higher National Diploma in Business)', 'unread', '2026-03-10 07:56:28'),
(375, 'registered_students', 114, '917032481', 'INSERT', 'New student registered: Ms. Rakhsshaa Ravikumar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 08:04:12'),
(376, 'registered_students', 115, '917032424', 'INSERT', 'New student registered: Avinga Inosh Piyathilaka (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 08:16:43'),
(377, 'payment_records', 81, '917032424', 'INSERT', 'New payment record added for student ID 917032424, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-10 08:20:26'),
(378, 'email_log', 81, '917032424', 'INSERT', 'Email sent to Avinga Inosh Piyathilaka (avinga.inosh@bms.ac.lk)', 'unread', '2026-03-10 08:20:26'),
(379, 'registered_students', 115, '917032424', 'UPDATE', 'Student record updated for Avinga Inosh Piyathilaka, graduation payment status: paid', 'unread', '2026-03-10 08:20:26'),
(380, 'registered_students', 116, '917032422', 'INSERT', 'New student registered: Ms. Asma Fahim (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 08:34:06'),
(381, 'payment_records', 82, '917032405', 'INSERT', 'New payment record added for student ID 917032405, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 08:48:09'),
(382, 'email_log', 82, '917032405', 'INSERT', 'Email sent to Ms. M. R. Rahmath Rashidha (rashidharizan0206@gmail.com)', 'unread', '2026-03-10 08:48:09'),
(383, 'registered_students', 92, '917032405', 'UPDATE', 'Student record updated for Ms. M. R. Rahmath Rashidha, graduation payment status: paid', 'unread', '2026-03-10 08:48:09'),
(384, 'payment_records', 83, '917032468', 'INSERT', 'New payment record added for student ID 917032468, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 08:49:23'),
(385, 'email_log', 83, '917032468', 'INSERT', 'Email sent to Ms. Fathima Haleema Yoosuf (haleemayoosuf678@gmail.com)', 'unread', '2026-03-10 08:49:23'),
(386, 'registered_students', 93, '917032468', 'UPDATE', 'Student record updated for Ms. Fathima Haleema Yoosuf, graduation payment status: paid', 'unread', '2026-03-10 08:49:23'),
(387, 'extra_ticket_log', 5, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-10 08:50:53'),
(388, 'registered_students', 117, '917032428', 'INSERT', 'New student registered: Ms. Ashani Hanks (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 08:52:56'),
(389, 'registered_students', 118, '917032499', 'INSERT', 'New student registered: Mr. Dhakhshesh Shivashankar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 08:58:09'),
(390, 'payment_records', 84, '917032419', 'INSERT', 'New payment record added for student ID 917032419, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 09:01:27'),
(391, 'email_log', 84, '917032419', 'INSERT', 'Email sent to Leena Manohar (leenamnhr@gmail.com)', 'unread', '2026-03-10 09:01:27'),
(392, 'registered_students', 110, '917032419', 'UPDATE', 'Student record updated for Leena Manohar, graduation payment status: paid', 'unread', '2026-03-10 09:01:27'),
(393, 'extra_ticket_log', 6, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-10 09:02:14'),
(394, 'payment_records', 85, '917032447', 'INSERT', 'New payment record added for student ID 917032447, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 09:03:21'),
(395, 'email_log', 85, '917032447', 'INSERT', 'Email sent to Ms. Lenin Leno Sharon Olivia (leninolivia1@gmail.com)', 'unread', '2026-03-10 09:03:21'),
(396, 'registered_students', 33, '917032447', 'UPDATE', 'Student record updated for Ms. Lenin Leno Sharon Olivia, graduation payment status: paid', 'unread', '2026-03-10 09:03:21'),
(397, 'payment_records', 86, '917032481', 'INSERT', 'New payment record added for student ID 917032481, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 09:04:07'),
(398, 'email_log', 86, '917032481', 'INSERT', 'Email sent to Ms. Rakhsshaa Ravikumar (rakhsshaa.ravikumar@bms.ac.lk)', 'unread', '2026-03-10 09:04:07'),
(399, 'registered_students', 114, '917032481', 'UPDATE', 'Student record updated for Ms. Rakhsshaa Ravikumar, graduation payment status: paid', 'unread', '2026-03-10 09:04:07'),
(400, 'payment_records', 87, '917032499', 'INSERT', 'New payment record added for student ID 917032499, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 09:05:14'),
(401, 'email_log', 87, '917032499', 'INSERT', 'Email sent to Mr. Dhakhshesh Shivashankar (dhakhsheshsiva@gmail.com)', 'unread', '2026-03-10 09:05:14'),
(402, 'registered_students', 118, '917032499', 'UPDATE', 'Student record updated for Mr. Dhakhshesh Shivashankar, graduation payment status: paid', 'unread', '2026-03-10 09:05:14'),
(403, 'payment_records', 88, '917032420', 'INSERT', 'New payment record added for student ID 917032420, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-10 09:06:09'),
(404, 'email_log', 88, '917032420', 'INSERT', 'Email sent to Ms. Ifla Imran (iflaimran29@gmail.com)', 'unread', '2026-03-10 09:06:09'),
(405, 'registered_students', 112, '917032420', 'UPDATE', 'Student record updated for Ms. Ifla Imran, graduation payment status: paid', 'unread', '2026-03-10 09:06:09'),
(406, 'admin', 23, NULL, 'insert', 'New admin added: Suraj (Role: admin)', 'unread', '2026-03-10 09:17:21'),
(407, 'payment_records', 89, '528032411', 'INSERT', 'New payment record added for student ID 528032411, program: Higher Diploma in Biomedical Science - Batch 28, amount: 10000', 'unread', '2026-03-10 09:58:22'),
(408, 'email_log', 89, '528032411', 'INSERT', 'Email sent to Sri Ranjan Bathanchaliy Meenaambal (bathanchaliy.ranjan@bms.ac.lk)', 'unread', '2026-03-10 09:58:22'),
(409, 'registered_students', 29, '528032411', 'UPDATE', 'Student record updated for Sri Ranjan Bathanchaliy Meenaambal, graduation payment status: paid', 'unread', '2026-03-10 09:58:22'),
(410, 'payment_records', 90, '917032413', 'INSERT', 'New payment record added for student ID 917032413, program: BTEC Higher National Diploma in Business - Batch 17, amount: 12000', 'unread', '2026-03-10 10:18:42'),
(411, 'email_log', 90, '917032413', 'INSERT', 'Email sent to Ms. Nilakshi Sivathas (nila41767@gmail.com)', 'unread', '2026-03-10 10:18:42'),
(412, 'registered_students', 55, '917032413', 'UPDATE', 'Student record updated for Ms. Nilakshi Sivathas, graduation payment status: paid', 'unread', '2026-03-10 10:18:42'),
(413, 'extra_ticket_log', 7, NULL, 'INSERT', 'Added 1 extra ticket(s) worth Rs. 2000.00', 'unread', '2026-03-10 10:22:27'),
(414, 'registered_students', 119, '917032454', 'INSERT', 'New student registered: Mr. Anfaas Insaar (Program: BTEC Higher National Diploma in Business - Batch 17)', 'unread', '2026-03-10 10:23:39'),
(415, 'payment_records', 91, '917032431', 'INSERT', 'New payment record added for student ID 917032431, program: BTEC Higher National Diploma in Business - Batch 17, amount: 10000', 'unread', '2026-03-10 10:31:54'),
(416, 'email_log', 91, '917032431', 'INSERT', 'Email sent to Ms. Julious Berny Blinda (bernyblinda200403@gmail.com)', 'unread', '2026-03-10 10:31:54'),
(417, 'registered_students', 17, '917032431', 'UPDATE', 'Student record updated for Ms. Julious Berny Blinda, graduation payment status: paid', 'unread', '2026-03-10 10:31:54');

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
(3, '527102330', 'Ms. Tharuki Subanya Pieris', '2005-10-02', 'tharukipeiris@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 772158587, 'paid', 'registered', 1),
(4, '528032401', 'Ms. Mohammed Safrin Aafrin Aysha', '2001-06-20', 'aafrinasha2001@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 701777979, 'paid', 'registered', 2),
(5, '528032402', 'Ms. Kiruthika Paramananthan', '2002-09-17', 'shansathurshan884@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 763891884, 'paid', 'registered', 3),
(6, '528032403', 'Ms. Shonaleen Fonseka', '2005-02-22', 'shonaleenfonseka@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 770388478, 'paid', 'registered', 4),
(7, '528032404', 'Ms. Gajani Rajeswaran', '2001-03-26', 'gajani.rajeshwaran@icloud.com', 'Higher Diploma in Biomedical Science - Batch 28', 778161553, 'paid', 'registered', 5),
(8, '528032405', 'Ms. Sayini Pushpakumar', '2002-06-25', 'sayinipushpakumar@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 768071385, 'paid', 'registered', 6),
(9, '528032406', 'Ms. Gayashi Anupama Jayawardana', '2000-07-19', 'gayashianupama00@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 710430181, 'paid', 'registered', 7),
(10, '528032407', 'Ms. Narmada De Silva', '2003-05-08', 'narmadahdesilva@gmaill.com', 'Higher Diploma in Biomedical Science - Batch 28', 757023071, 'paid', 'registered', 8),
(11, '528032408', 'Ms. Sahar Khushbu Nadeem', '2004-10-06', 'saharnadeem458@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 765302386, 'paid', 'registered', 9),
(12, '528032409', 'Ms. Nandhujah Gunasheharan', '1998-04-16', 'nandhujahvarma@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 767307271, 'paid', 'registered', 10),
(13, '528032411', 'Ms. Sri Ranjan Bathanchaliy Meenaambal', '2001-01-12', 'srihary.b200112@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 776977009, 'paid', 'registered', 40),
(14, '528032412', 'Ms. Abinaya Balasingam ', '1999-03-07', 'abiyabalan73@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 761999122, 'paid', 'registered', 11),
(15, '528032413', 'Ms. Tharuniya Anpalakan', '2003-05-26', 'tharuniya7972@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 772927084, 'paid', 'registered', 12),
(16, '528032414', 'Ms. Thewaratantrige Sathmini Navodya Fernando', '2004-04-29', 'navodyafernando841@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 762138340, 'paid', 'registered', 13),
(17, '528032415', 'Ms. Saraniya Muralidaran', '2002-10-17', 'muralitharansaraniya@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 762494639, 'paid', 'registered', 14),
(18, '528032416', 'Ms. Siddeeq Fathima Shaheera', '2003-09-21', 'shaheerasiddeeq@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 756087706, 'paid', 'registered', 15),
(19, '528032418', 'Ms. Asini  Fernando', '2004-10-05', 'asinikithma@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 740024769, 'paid', 'registered', 16),
(20, '528032419', 'Ms. Harithra Ramesh', '2004-11-30', 'harithraramesh7@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 716464634, 'paid', 'registered', 17),
(21, '528032420', 'Mr. Vijayakumar Ugendraraj', '2003-11-25', 'ugendraraj1325@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 768583212, 'paid', 'registered', 18),
(22, '528032421', 'Mr. Ahamed Lebbe Ahamed Sahee', '2002-02-12', 'callmesahee02@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 750780633, 'paid', 'registered', 19),
(23, '528032422', 'Ms. Sandeepa Sevmini de Silva', '2005-09-17', 'sandeepasevmini@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 717769094, 'paid', 'registered', 20),
(24, '528032423', 'Ms. Malshi Imesha Pathiranage', '2003-12-13', 'mal.oOime@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 701167400, 'paid', 'registered', 21),
(25, '528032425', 'Ms. Hidhushi Mahendran', '1999-03-27', 'hidhushim@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 761813287, 'paid', 'registered', 22),
(26, '528032427', 'Mr. V Kanistan Agash', '2002-10-15', 'kanistanakash15@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 714826075, 'paid', 'registered', 23),
(27, '528032428', 'Mr. Mohamed Iesa Ismail', '2005-01-16', 'iesai3214@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 754407729, 'paid', 'registered', 24),
(28, '528032429', 'Ms. A.M.M Chathumini Hansika Jayawardene', '2005-05-06', 'chathumini48hansika@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 773109593, 'paid', 'registered', 25),
(29, '528032430', 'Ms. Sajitha Makenthiran', '2002-04-08', 'sajithamahenthiran@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 767669489, 'paid', 'registered', 41),
(30, '528032431', 'Mr. Mohammed Azwear Ali Mohammed Aadhil Najmi', '2004-04-22', 'Aadhilnajmi2004@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 752422414, 'paid', 'registered', 26),
(31, '528032432', 'Ms. Rushdha Nazar', '2001-03-22', 'rushdhanazar929@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 776710953, 'paid', 'registered', 27),
(32, '528032433', 'Ms. Shamila Nijamdeen', '2003-11-23', 'shami2311jesus@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 773873928, 'paid', 'registered', 28),
(33, '528032434', 'Ms.  Thewni  Dharmasiri', '2004-03-02', 'thewnidharmasiri@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 775717425, 'paid', 'registered', 29),
(34, '528032435', 'Ms. Haribaashini Muralitharan', '2003-01-30', 'baashini2003@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 726442239, 'paid', 'registered', 30),
(35, '528032436', 'Ms.  Zahara Ismail', '2001-10-17', 'zaharaismail0075@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 764300321, 'paid', 'registered', 31),
(36, '528032437', 'Ms. Amana Fathima Naflar', '2001-03-09', 'amaranaf1925@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 763253989, 'paid', 'registered', 32),
(37, '528032438', 'Ms. N.Chamathka Edirirathna', '2003-06-13', 'nizayainsaad@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 763037637, 'paid', 'registered', 33),
(38, '528032440', 'Mr. Mohanakumar Sivatharshan', '2003-12-29', 'sivatharshanmohanakumar@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 773084668, 'paid', 'registered', 34),
(39, '528032441', 'Ms. Ramudhi Pemodhya De Silva', '2005-06-17', 'ramudhidesilva@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 711626018, 'paid', 'registered', 35),
(40, '528032442', 'Ms. Apitha Suresh', '2004-11-05', 'apithasuresh624@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 765950813, 'paid', 'registered', 36),
(41, '528032443', 'Mr. Dhanushan Sekar', '2004-01-21', 'dhanushansekar@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 726899927, 'paid', 'registered', 37),
(42, '528032444', 'Ms. Thisuri Cyara Jayaweera', '2005-09-02', 'thisurijayaweera@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 781092794, 'paid', 'registered', 38),
(43, '528032445', 'Ms. Sharuniya Pradaa Mahendran', '2004-08-19', 'sharuniyapradaam@gmail.com', 'Higher Diploma in Biomedical Science - Batch 28', 763047211, 'paid', 'registered', 39),
(44, '621102323', 'Ms. Maryam Sharfa Saliheen', '2002-03-06', 'maryamsaliheen@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 21', 774563845, 'paid', NULL, 51),
(45, '622032401', 'Ms. Ama Ranathunga', '2004-04-22', 'aranathunga04@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 740011330, 'paid', 'registered', 42),
(46, '622032402', 'Ms. Fathima Nuha Hakeem', '2003-02-22', 'hakeemnuha@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 760502003, 'paid', 'registered', 43),
(47, '622032403', 'Mr. Binara Minsara', '2001-05-10', 'binaraminsara2@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 752257551, 'paid', NULL, 44),
(48, '622032405', 'Ms. Kavisha Kalhari ', '2006-08-19', 'kavishakalhari06@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 763287647, 'paid', 'registered', 45),
(49, '622032406', 'Ms. Ranumi Dahanaggama Arachchi', '2005-04-29', 'dammsprom@yahoo.com', 'Higher Diploma In Biotechnology Science - Batch 22', 751244789, 'paid', 'registered', 46),
(50, '622032408', 'Ms. Moksha Chathurika Dananjane ', '2005-10-01', 'dhanushka2002426@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 750395006, 'paid', 'registered', 47),
(51, '622032411', 'Ms. Themavee Wijekoon ', '2007-09-06', 'themaveesw07@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 760951687, 'paid', 'registered', 48),
(52, '622032412', 'Ms. Janudi Wickrama Gunarathne', '2005-05-01', 'janudiwickrama@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 718453608, 'paid', NULL, 49),
(53, '622032413', 'Ms. J A A Nethmini Wickramasinghe', '2004-01-03', 'amashanethmini20@gmail.com', 'Higher Diploma In Biotechnology Science - Batch 22', 766887079, 'paid', NULL, 50),
(54, '1002032304', 'Mr. Arunasalam Rathushan', '2003-04-17', 'radushanr@gmail.com', 'Higher Diploma in Food Science and Nutrition- Batch 04', 760480528, 'paid', 'registered', 52),
(55, '1004032401', 'Ms. Wijesiri Narange Ashani Samudika', '1999-02-06', 'itsashani@gmail.com', 'Higher Diploma in Food Science and Nutrition- Batch 04', 761660222, 'paid', 'registered', 53),
(56, '1102032401', 'Suha Ahmed Naseerdeen', '2003-09-25', 'suhasan1131@gmail.com', 'Higher Diploma in Medical Biotechnology - Batch 02', 765818909, 'paid', 'registered', 54),
(57, '917032402', 'Ms. Minda Oliniya Rozairo', '2004-09-12', 'mindaoliniya@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 770599598, 'Paid', 'registered', 55),
(58, '917032403', 'Ms. Aminath Ayamin Rasheed', '2005-03-05', 'ayaako567@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 768500598, 'Paid', 'registered', 56),
(59, '917032404', 'Ms. Radinka Jinelli Fernando', '2004-08-23', 'radinkafernando@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 775748422, 'Paid', 'registered', 57),
(60, '917032405', 'Ms. M. R. Rahmath Rashidha', '2003-02-06', 'rashidhari2an0206@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 771852481, 'Paid', 'registered', 58),
(61, '917032407', 'Ms. Ursula Pavithree Wannige', '2003-05-01', 'shashiprabha20060@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 741340422, 'Paid', 'registered', 59),
(62, '917032408', 'Ms. Tharushi Christeen Jayawardana', '2006-05-05', 'taruhtgj0505@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 775369137, 'Paid', NULL, 60),
(63, '917032409', 'Mr. H.V. Anuja Nethmika Wijayathilaka', '2004-03-16', 'anujawork55555@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 760203439, 'Paid', NULL, 61),
(64, '917032413', 'Ms. Nilakshi Sivathas', '2005-10-07', 'nila41767@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 765450060, 'Paid', 'registered', 62),
(65, '917032414', 'Mr. A.R.M. Ruhaif', '2004-04-23', 'mohamedruhaif@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 716307558, 'Paid', NULL, 63),
(66, '917032415', 'Ms. Mahendran Vishalani', '2005-03-17', 'mahendranvishalani1708@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 767659775, 'Paid', 'registered', 64),
(67, '917032416', 'Ms. Fathima Amra Najimudeen', '2003-08-20', 'amranajimudeen23@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 754897989, 'paid', 'registered', 65),
(68, '917032418', 'Ms. Dushyanthi Rajendran', '2003-08-20', 'dushyanthiraj003@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 778551657, 'Paid', 'registered', 66),
(69, '917032420', 'Ms. Ifla Imran', '2005-07-29', 'iflaimran29@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 777413049, 'Paid', 'registered', 67),
(70, '917032422', 'Ms. Asma Fahim', '2003-09-18', 'asmafahim03@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 742283553, 'Paid', 'registered', 68),
(71, '917032423', 'Mr. Mohamed Mawfeen Umar', '2004-06-18', 'mohamedumar3030@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 768781313, 'Paid', 'registered', 69),
(72, '917032425', 'Ms. Dharshini Chandran', '2004-08-14', 'dharshanichandran802@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 773460369, 'Paid', 'registered', 70),
(73, '917032426', 'Ms. Malindri Laleesha Wijeyesinghe', '2003-12-24', 'malindriwijey@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 777349159, 'Paid', 'registered', 71),
(74, '917032427', 'Ms. Stalina Moorthy', '2004-02-03', 'moorthystalina@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 765819291, 'Paid', 'registered', 72),
(75, '917032428', 'Ms. Ashani Hanks', '2004-03-16', 'ashanihanks@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 716099972, 'Paid', 'registered', 73),
(76, '917032429', 'Ms. Lacshithi Saravanan', '2004-07-11', 'lacshithisaravanan1111@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 761754555, 'Paid', 'registered', 74),
(77, '917032431', 'Ms. Julious Berny Blinda', '2004-03-12', 'bernyblinda3120@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 760615552, 'Paid', 'registered', 75),
(78, '917032432', 'Ms. Rishka Fazaal', '2002-10-15', 'rishkafazaal@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 743489207, 'Paid', 'registered', 76),
(79, '917032433', 'Ms. Fathima Shamla Imnaz', '2004-07-27', 'imnazshamla@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 740507614, 'Paid', 'registered', 77),
(80, '917032434', 'Ms. Robeka Subramaniam', '2005-01-31', 'subramaniamrobeka@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 776759733, 'Paid', 'registered', 78),
(81, '917032438', 'Mr. Naysa Amarasinghe', '2006-09-02', 'naysaamarasinghe@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 702473749, 'Paid', 'registered', 79),
(82, '917032442', 'Mr. Fathima Nuzrath Mohammed Naizer', '2004-02-27', 'fathimanushrath272@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 776370388, 'Paid', 'registered', 80),
(83, '917032443', 'Mr. Aathif Anver', '2004-03-27', 'aathifanver@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 754056308, 'Paid', 'registered', 81),
(84, '917032444', 'Mr. D. Anarga Joshua Colombege', '2004-11-05', 'josh.colombege@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 750117112, 'Paid', 'registered', 82),
(85, '917032446', 'Mr. Krithigan Sugumaran', '2004-12-17', 'skrithigan@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 713500939, 'Paid', 'registered', 83),
(86, '917032447', 'Ms. Lenin Sharon Olivia', '2004-03-10', 'leninolivia73@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 750821679, 'Paid', 'registered', 84),
(87, '917032448', 'Ms. Habeeba Zareen', '2005-03-20', 'habeebazareen6@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 759886651, 'Paid', 'registered', 85),
(88, '917032449', 'Mr. Iqbal Abdul Muyeeth', '2004-04-17', 'muyeeth4@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 776179890, 'Paid', 'registered', 86),
(89, '917032450', 'Ms. Abdul Rasal Fahma', '2003-03-10', 'abdulrasalfahma@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 775488221, 'Paid', 'registered', 87),
(90, '917032451', 'Ms. Niamath Shakeel', '2007-05-10', 'niamath3890@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 789804004, 'unpaid', 'registered', 88),
(91, '917032452', 'Ms. Lakshi Karnan', '2004-09-04', 'lakshikarnan840@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 763037668, 'Paid', 'registered', 89),
(92, '917032456', 'Ms. Rithika Chandravathanan', '2006-01-21', 'rithikka21@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 767575606, 'Paid', 'registered', 90),
(93, '917032461', 'Mr. Yoosuf Sulaiman', '2005-08-25', 'yoosufsulaiman14@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 756090473, 'Paid', 'registered', 91),
(94, '917032464', 'Ms. Vismida Thachanamoorthy', '2005-02-17', 'vismidathachanamoorthy@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 740614050, 'Paid', 'registered', 92),
(95, '917032465', 'Mr. Thulith Gayantha ', '2006-10-24', 'thulithgayantha@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 775950736, 'Paid', NULL, 93),
(96, '917032466', 'Mr. Mohomad Fazeem Wazeem', '2004-02-27', 'wazeem_.2702@icloud.com', 'BTEC Higher National Diploma in Business - Batch 17', 722057535, 'Paid', 'registered', 94),
(97, '917032468', 'Ms. Fathima Haleema Yoosuf', '2004-11-18', 'haleemayoosuf2004@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 763119931, 'Paid', 'registered', 95),
(98, '917032469', 'Ms. Suvidana Selvaraja', '2003-11-02', 'suvisuvidana@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 785067878, 'Paid', 'registered', 96),
(99, '917032470', 'Mr. Umair Imthiyasdeen', '2005-10-06', 'mohamedumair999@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 764305573, 'Paid', 'registered', 97),
(100, '917032472', 'Ms. Fathima Rishadha Uvais', '2004-02-14', 'rishada90heerdv@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 786089365, 'Paid', 'registered', 98),
(101, '917032476', 'Ms. Rabhiya Badurdeen', '2005-06-23', 'rabhiyabd@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 766976896, 'Paid', 'registered', 99),
(102, '917032477', 'Ms. Oneli Hansini Samarakoon Jayawardena', '2004-05-10', 'onelijayawardena2004@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 725253898, 'Paid', NULL, 100),
(103, '917032478', 'Ms. Chathupama Perera', '2004-11-15', 'chathupamaperera1234@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 777655093, 'Paid', 'registered', 101),
(104, '917032482', 'Ms. Harishmi Balaratnarajah Mohanakumar', '2005-10-01', 'b.m.harishmi110@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 777263642, 'Paid', 'registered', 102),
(105, '917032483', 'Ms. Maria Celsia Leo', '2002-09-10', 'mariacelsialeo@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 773731166, 'Paid', NULL, 103),
(106, '917032484', 'Mr. Shervon Karandawela ', '2004-03-17', 'shervoncaniclous14@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 770325018, 'Paid', NULL, 104),
(107, '917032487', 'Ms. Muhammad Siraj Fathima Safiyya', '2004-04-08', 'safiyyasiraj@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 763635365, 'Paid', 'registered', 105),
(108, '917032489', 'Mr. H.F. Dev Yeshan Fonseka', '2004-07-29', 'devyeshan@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 761384074, 'Paid', NULL, 106),
(109, '917032490', 'Mr. B.C. Nathan Aloka De Silva', '2006-03-07', 'nathanaloka@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 767370717, 'Paid', 'registered', 107),
(110, '917032496', 'Ms. Risda Sahna Rizwan', '2003-09-16', 'risdasahna18@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 760872932, 'Paid', 'registered', 108),
(111, '917032497', 'Mr. Seyed Luqmaan Moulana', '2005-02-10', 'seyedmoulana51@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 768323709, 'Paid', NULL, 109),
(112, '917032499', 'Mr. Dhakhshesh Shivashankar', '2007-07-28', 'dhakhsheshshiva@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 777648241, 'Paid', 'registered', 110),
(113, '9170324100', 'Mr.Hiraz Mowlana', '2003-05-13', 'mowlanahiraz@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 779267059, 'Paid', 'registered', 111),
(114, '9170324102', 'Mr.Enosh Ganesh', '2005-01-12', 'enoshganesh1@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 761762435, 'Paid', 'registered', 112),
(115, '917032401', 'Sangeetha Sivanesan', '1999-11-19', 'sangeethanesan99@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 776990825, 'paid', 'registered', 113),
(116, '917032419', 'Leena Manohar', '2004-05-17', 'manohar.leena@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', 768904647, 'paid', 'registered', 114),
(117, '917032424', 'Avinga Inosh Piyathilaka', '2006-11-16', 'ffavinga@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 775537146, 'paid', 'registered', 115),
(118, '917032430', 'Muhammed Azwer M. Luqman', '2001-08-01', 'luqmanazwer7@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 766370496, 'paid', 'registered', 116),
(119, '917032436', 'Dhilrukshi Nadanasabapathy', '2003-07-31', 'dhi.nada2003@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 772124057, 'paid', 'registered', 117),
(120, '917032437', 'Nimenma Methsiluni ', '2004-07-29', 'nimenmaarachchi@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', 762440115, 'paid', 'registered', 118),
(121, '917032445', 'Mohamed Minsaf Jahufer', '2004-09-01', 'minsafjahufer2004@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 757783165, 'paid', '', 119),
(122, '917032453', 'Mohamed Waseem Mashoor', '2004-01-07', 'mohammedwaseem0701@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 776880259, 'paid', 'registered', 120),
(123, '917032455', 'Haseef Ahamed', '2005-01-08', 'haseefahamed44@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 763793318, 'paid', 'registered', 121),
(124, '917032458', 'M.M. Yoonus Ahamedh', '2005-06-15', 'yoonus.ahamedh@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', 701780659, 'paid', 'registered', 122),
(125, '917032459', 'Sinthiya Mdanmohan', '2003-11-20', 'sinthiyamdanmohan@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 761755945, 'paid', 'registered', 123),
(126, '917032474', 'Rizhan Ahmed Nawaz', '2003-08-22', 'rizhanahmed73@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 761547171, 'paid', '', 124),
(127, '917032475', 'Shayan Mutateesa', '2005-06-25', 'shayanadhithya@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 769788134, 'paid', '', 125),
(128, '917032479', 'Jeevanandam Vithulan', '2004-08-09', 'vithulan.jeevanandam@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', 776944741, 'paid', '', 126),
(129, '917032480', 'Mushab Aslam', '2005-12-24', 'mushabmahmood@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 740551777, 'paid', 'registered', 127),
(130, '917032485', 'Praboda Vidumini', '2004-11-22', 'prabodavidumini26@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 760605697, 'paid', 'registered', 128),
(131, '917032488', 'Adeeshan Saravanamohan', '2005-01-01', 'adeeshan08@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 753150580, 'paid', 'registered', 129),
(132, '917032491', 'Himaru Enoch De Silva', '2007-06-06', 'himarue748@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 756892071, 'paid', '', 130),
(133, '917032494', 'Yuthmini Malsiluni', '2002-10-12', 'yuthminimalsiluni@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 767796485, 'paid', 'registered', 131),
(134, '917032498', 'Mohamed Sharaf Safder', '2003-08-24', 'safdershihana@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 725147229, 'paid', '', 132),
(135, '917032406', 'Mr. Husain Huzaifa Taherally', '2004-08-02', 'hussainhuzaifa15@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 766061111, 'paid', '', 133),
(136, '914032338', 'Mr. Mohomed Ruzain Imad', '2003-06-21', 'imadruzain131@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 702473749, 'paid', 'registered', 134),
(137, '917032439', 'Mr. Thanooj Kathirason', '2007-01-23', 'thanooj987@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 766371333, 'paid', 'registered', 135),
(138, '917032457', 'Mr. Huzaifa Zayed', '2005-07-11', 'huzaifa.zayed@bms.ac.lk', 'BTEC Higher National Diploma in Business - Batch 17', 778410711, 'paid', '', 136),
(139, '917032460', 'Mr. Fathima Zainab Hashim Ameer', '2002-09-01', 'zhashimameer@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 705829272, 'paid', '', 137),
(140, '917032471', 'Mr. Abdul Rahman Ashroff', '2001-04-20', 'aliabdurrahman2042001@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 778619237, 'paid', 'registered', 138),
(141, '917032481', 'Mr. Rakhsshaa Ravikumar', '2006-06-07', 'rakhsshaar@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 761690821, 'paid', 'registered', 139),
(142, '917032495', 'Mr. Mohamed Raihan Ozeer', '2003-02-09', 'raihanozeer@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 721947896, 'paid', '', 140),
(143, '9170324101', 'Mr. Ahmed Walid Imthiyas', '2005-07-01', 'walidimthiyas11@gmail.com', 'BTEC Higher National Diploma in Business - Batch 17', 763509206, 'paid', 'registered', 141),
(144, '917032473', 'Mr. Mohamed Rafee Mohamed Rasmy', '2004-10-07', 'rafeesl@outlook.com', 'BTEC Higher National Diploma in Business - Batch 17', 767605687, 'paid', '', 142),
(145, '917032454', 'Mr. Anfaas Insaar', '2002-12-22', 'anfaas112@gmail.com ', 'BTEC Higher National Diploma in Business - Batch 17', 771222810, 'paid', 'registered', 143);

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
-- Dumping data for table `payment_records`
--

INSERT INTO `payment_records` (`id`, `student_id`, `program_name`, `graduation_fee`, `free_ticket_count`, `extra_ticket_count`, `extra_ticket_fee`, `total_amount`, `receipt_number`, `payment_date`, `created_by`) VALUES
(1, '528032422', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20253260', '2026-03-03 07:43:47', 'Thaksheniya'),
(2, '528032401', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 3, 6000.00, 16000, 'GC20252264', '2026-03-03 07:58:52', 'Thaksheniya'),
(3, '528032407', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 3, 6000.00, 16000, 'GC20256847', '2026-03-03 10:28:17', 'Thaksheniya'),
(4, '527102330', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20254178', '2026-03-04 06:41:00', 'Thaksheniya'),
(5, '528032416', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20255069', '2026-03-04 08:02:05', 'Thaksheniya'),
(6, '622032406', 'Higher Diploma In Biotechnology Science - Batch 22', 10000.00, 1, 1, 2000.00, 12000, 'GC20257334', '2026-03-04 08:20:36', 'Thaksheniya'),
(7, '622032402', 'Higher Diploma In Biotechnology Science - Batch 22', 10000.00, 1, 0, 0.00, 10000, 'GC20253618', '2026-03-04 08:22:14', 'Thaksheniya'),
(8, '622032411', 'Higher Diploma In Biotechnology Science - Batch 22', 10000.00, 1, 1, 2000.00, 12000, 'GC20253165', '2026-03-04 08:24:50', 'Thaksheniya'),
(9, '528032402', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20257492', '2026-03-04 10:53:26', 'Thaksheniya'),
(10, '917032429', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20254423', '2026-03-05 03:51:37', 'Thaksheniya'),
(11, '917032407', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20258040', '2026-03-05 04:18:02', 'Thaksheniya'),
(12, '528032434', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20256428', '2026-03-05 05:09:03', 'Thaksheniya'),
(13, '528032420', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20251461', '2026-03-06 04:55:11', 'Thaksheniya'),
(14, '622032401', 'Higher Diploma In Biotechnology Science - Batch 22', 10000.00, 1, 1, 2000.00, 12000, 'GC20256666', '2026-03-06 05:10:12', 'Thaksheniya'),
(15, '528032406', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20258647', '2026-03-06 07:36:47', 'Thaksheniya'),
(16, '528032418', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20251103', '2026-03-06 07:38:24', 'Thaksheniya'),
(17, '917032470', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20257042', '2026-03-06 09:19:28', 'Thaksheniya'),
(18, '9170324100', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 2, 4000.00, 14000, 'GC20257166', '2026-03-07 05:04:03', 'Thaksheniya'),
(19, '917032446', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20259999', '2026-03-07 05:12:12', 'Thaksheniya'),
(20, '528032442', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 2, 4000.00, 14000, 'GC20256983', '2026-03-07 05:20:15', 'Thaksheniya'),
(21, '528032419', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20255560', '2026-03-07 05:21:17', 'Thaksheniya'),
(22, '1004032401', 'Higher Diploma in Food Science and Nutrition- Batch 04', 10000.00, 1, 1, 2000.00, 12000, 'GC20255015', '2026-03-07 06:09:26', 'Thaksheniya'),
(23, '917032425', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20256323', '2026-03-07 06:52:25', 'Thaksheniya'),
(24, '917032434', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20259685', '2026-03-07 06:53:51', 'Thaksheniya'),
(25, '917032433', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 2, 4000.00, 14000, 'GC20257875', '2026-03-07 07:29:21', 'Thaksheniya'),
(26, '917032448', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20257520', '2026-03-07 07:33:14', 'Thaksheniya'),
(27, '917032459', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 2, 4000.00, 14000, 'GC20253350', '2026-03-07 09:01:09', 'Thaksheniya'),
(28, '917032432', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 2, 4000.00, 14000, 'GC20259483', '2026-03-07 10:12:16', 'Thaksheniya'),
(29, '917032464', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20252449', '2026-03-07 10:13:20', 'Thaksheniya'),
(30, '917032423', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20252812', '2026-03-07 10:14:32', 'Thaksheniya'),
(31, '917032437', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20251410', '2026-03-07 10:23:03', 'Thaksheniya'),
(32, '917032449', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20259002', '2026-03-07 10:37:20', 'Thaksheniya'),
(33, '917032415', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 2, 4000.00, 14000, 'GC20259336', '2026-03-07 10:51:56', 'Thaksheniya'),
(34, '917032401', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20259614', '2026-03-08 05:05:27', 'Thaksheniya'),
(35, '917032403', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20255095', '2026-03-08 05:39:58', 'Thaksheniya'),
(36, '917032456', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20259481', '2026-03-08 05:41:31', 'Thaksheniya'),
(37, '917032438', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20258622', '2026-03-08 05:42:51', 'Thaksheniya'),
(38, '917032439', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20256796', '2026-03-08 05:49:09', 'Thaksheniya'),
(39, '917032478', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20251061', '2026-03-08 06:22:05', 'Thaksheniya'),
(40, '9170324102', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20256653', '2026-03-08 06:30:03', 'Thaksheniya'),
(41, '917032496', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20253440', '2026-03-08 06:48:38', 'Thaksheniya'),
(42, '622032408', 'Higher Diploma In Biotechnology Science - Batch 22', 10000.00, 1, 1, 2000.00, 12000, 'GC20254344', '2026-03-09 05:01:42', 'Thaksheniya'),
(43, '917032471', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20258647', '2026-03-09 05:19:56', 'Thaksheniya'),
(44, '917032494', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20253525', '2026-03-09 05:54:19', 'Thaksheniya'),
(45, '917032485', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20258268', '2026-03-09 05:55:36', 'Thaksheniya'),
(46, '528032427', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20252223', '2026-03-09 05:56:22', 'Thaksheniya'),
(47, '528032412', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20256426', '2026-03-09 05:57:22', 'Thaksheniya'),
(48, '528032405', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20259529', '2026-03-09 05:58:14', 'Thaksheniya'),
(49, '917032430', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20258523', '2026-03-09 06:10:38', 'Thaksheniya'),
(50, '917032488', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20252299', '2026-03-09 06:11:33', 'Thaksheniya'),
(51, '917032450', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20252907', '2026-03-09 06:14:20', 'Thaksheniya'),
(52, '528032431', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20254249', '2026-03-09 06:36:11', 'Thaksheniya'),
(53, '528032433', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20252558', '2026-03-09 06:37:57', 'Thaksheniya'),
(55, '528032438', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20257561', '2026-03-09 06:44:23', 'Thaksheniya'),
(56, '1002032304', 'Higher Diploma in Food Science and Nutrition- Batch 04', 10000.00, 1, 0, 0.00, 10000, 'GC20255197', '2026-03-09 06:45:55', 'Thaksheniya'),
(57, '1102032401', 'Higher Diploma in Medical Biotechnology - Batch 02', 10000.00, 1, 1, 2000.00, 12000, 'GC20256559', '2026-03-09 06:48:49', 'Thaksheniya'),
(58, '917032427', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20252179', '2026-03-09 08:22:27', 'Thaksheniya'),
(59, '917032480', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20258776', '2026-03-09 08:52:02', 'Thaksheniya'),
(60, '917032452', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20256737', '2026-03-09 08:54:13', 'Thaksheniya'),
(61, '917032461', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 2, 4000.00, 14000, 'GC20257199', '2026-03-09 08:56:49', 'Thaksheniya'),
(62, '917032469', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20255829', '2026-03-09 08:58:04', 'Thaksheniya'),
(63, '917032466', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20258763', '2026-03-09 09:00:11', 'Thaksheniya'),
(64, '917032404', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20254622', '2026-03-09 09:02:00', 'Thaksheniya'),
(65, '917032482', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20255570', '2026-03-09 10:00:20', 'Thaksheniya'),
(66, '528032409', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20253801', '2026-03-09 10:07:54', 'Thaksheniya'),
(67, '528032415', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20256726', '2026-03-09 10:09:29', 'Thaksheniya'),
(68, '917032416', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20257788', '2026-03-09 10:19:34', 'Thaksheniya'),
(69, '528032421', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20254076', '2026-03-09 10:20:59', 'Thaksheniya'),
(70, '528032423', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20255768', '2026-03-09 10:22:36', 'Thaksheniya'),
(71, '528032403', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20258939', '2026-03-09 10:23:58', 'Thaksheniya'),
(72, '528032436', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20259904', '2026-03-09 10:29:59', 'Thaksheniya'),
(73, '917032402', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20251106', '2026-03-09 10:46:54', 'Thaksheniya'),
(74, '917032472', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20259440', '2026-03-10 03:10:30', 'Thaksheniya'),
(75, '917032476', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20257072', '2026-03-10 03:45:08', 'Thaksheniya'),
(76, '528032428', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20254266', '2026-03-10 05:43:26', 'Thaksheniya'),
(77, '528032444', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20255170', '2026-03-10 05:44:30', 'Thaksheniya'),
(78, '528032429', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20258689', '2026-03-10 05:45:24', 'Thaksheniya'),
(79, '528032408', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 1, 2000.00, 12000, 'GC20252053', '2026-03-10 05:49:39', 'Thaksheniya'),
(80, '528032443', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20252386', '2026-03-10 06:19:09', 'Thaksheniya'),
(81, '917032424', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20253470', '2026-03-10 08:20:26', 'Thaksheniya'),
(82, '917032405', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20255838', '2026-03-10 08:48:09', 'Thaksheniya'),
(83, '917032468', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20256248', '2026-03-10 08:49:23', 'Thaksheniya'),
(84, '917032419', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20257533', '2026-03-10 09:01:27', 'Thaksheniya'),
(85, '917032447', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20257608', '2026-03-10 09:03:21', 'Thaksheniya'),
(86, '917032481', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20254112', '2026-03-10 09:04:07', 'Thaksheniya'),
(87, '917032499', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20253379', '2026-03-10 09:05:14', 'Thaksheniya'),
(88, '917032420', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20256657', '2026-03-10 09:06:09', 'Thaksheniya'),
(89, '528032411', 'Higher Diploma in Biomedical Science - Batch 28', 10000.00, 1, 0, 0.00, 10000, 'GC20259865', '2026-03-10 09:58:22', 'Thaksheniya'),
(90, '917032413', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 1, 2000.00, 12000, 'GC20256105', '2026-03-10 10:18:42', 'Thaksheniya'),
(91, '917032431', 'BTEC Higher National Diploma in Business - Batch 17', 10000.00, 1, 0, 0.00, 10000, 'GC20251426', '2026-03-10 10:31:54', 'Thaksheniya');

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
(1, '528032427', NULL, '2002-10-15', 'Mr. V Kanistan Agash', 'Mr.', 'Kanistan Agash', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'kanistanakash15@gmail.com', 'kanistanakash15@gmail.com', '+94714826075', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-02-27 10:11:27'),
(2, '9170324102', NULL, '2005-01-12', 'Mr.Enosh Ganesh', 'Mr.', 'Enosh Ganesh', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'enoshganesh1@gmail.com', 'enoshganesh1@gmail.com', '+94761762435', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 15:39:14'),
(3, '917032403', NULL, '2005-03-05', 'Ms. Aminath Ayamin Rasheed', 'Ms.', 'Ayamin Rasheed', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'ayaako567@gmail.com', 'ayaako567@gmail.com', '+94768500598', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 15:40:20'),
(4, '917032464', NULL, '2005-02-17', 'Ms. Vismida Thachanamoorthy', 'Ms.', 'Vismida Thachanamoorthy', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'vismidathachanamoorthy@gmail.com', 'mtmvismida.08t@gmail.com', '+94740614050', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-02-27 15:41:12'),
(5, '917032425', NULL, '2004-08-14', 'Ms. Dharshini Chandran', 'Miss.', 'Dharshini Chandran', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'dharshanichandran802@gmail.com', 'dharshini.chandran@bms.ac.lk', '+94770411796', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 15:42:17'),
(6, '917032490', NULL, '2006-03-07', 'Nathan De Silva', 'Mr.', 'Nathan De Silva', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'nathanaloka@gmail.com', 'nathanaloka34@gmail.com', '+94767370717', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 16:10:06'),
(7, '917032429', NULL, '2004-07-11', 'Lacshithi Saravanan', 'Ms.', 'Lacshithi Saravanan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'lacshithisaravanan1111@gmail.com', 'lacshithisaravanan1111@gmail.com', '+94761754555', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 16:19:14'),
(8, '9170324100', NULL, '2003-05-13', 'Seyed Muhammed Hiraz Hibshy Mowlana', 'Mr.', 'Seyed Hiraz Hibshy Mowlana', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mowlanahiraz@gmail.com', 'hirazmowlana@gmail.com', '+94779267059', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 16:21:16'),
(9, '917032434', NULL, '2005-01-31', 'Ms. Robeka Subramaniam', 'Ms.', 'Robeka Subramaniam', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'subramaniamrobeka@gmail.com', 'subramaniamrobeka@gmail.com', '+94776759733', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 17:23:28'),
(10, '528032432', NULL, '2001-03-22', 'Ms. Rushdha Nazar', 'Ms.', 'Rushdha Nazar', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'rushdhanazar929@gmail.com', 'rushdha.nazar@bms.ac.lk', '+94776710953', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 19:02:01'),
(11, '917032456', NULL, '2006-01-21', 'Ms. Rithika Chandravathanan', 'Ms.', 'Rithikka Chandravathanan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'rithikka21@gmail.com', 'rithikka21@gmail.com', '+94767575606', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 19:10:40'),
(12, '917032450', NULL, '2003-03-10', 'Ms. Abdul Rasal Fahma', 'Miss.', 'Fahma Rasal', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'abdulrasalfahma@gmail.com', 'abdul.fahma@bms.ac.lk', '+94775488221', NULL, 'Paid', 'paid', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-02-27 20:54:05'),
(13, '917032433', NULL, '2004-07-27', 'Ms. Fathima Shamla Imnaz', 'Ms.', 'Shamla Imnaz', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'imnazshamla@gmail.com', 'imnazshamla@gmail.com', '+94740507614', NULL, 'Paid', 'paid', NULL, NULL, 'Vegetarian', 'Non-Vegetarian', '2026-02-27 21:50:24'),
(14, '528032422', NULL, '2005-09-17', 'Ms. Sandeepa Sevmini de Silva', 'Ms.', 'Sandeepa Sevmini de Silva', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'sandeepasevmini@gmail.com', 'sandeepasevmini@gmail.com', '+94717769094', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 23:10:19'),
(15, '622032402', NULL, '2003-02-22', 'Ms. Fathima Nuha Hakeem', 'Ms.', 'Nuha Hakeem', 1, 'Higher Diploma In Biotechnology Science - Batch 22', 'hakeemnuha@gmail.com', 'hakeemnuha@gmail.com', '+94760502003', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 23:26:16'),
(16, '528032437', NULL, '2001-03-09', 'Ms. Amana Fathima Naflar', 'Mrs.', 'Amana Fathima Naflar', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'amaranaf1925@gmail.com', 'riyazahaniffa92@gmail.com', '+94785950613', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-27 23:50:54'),
(17, '917032431', NULL, '2004-03-12', 'Ms. Julious Berny Blinda', 'Ms.', 'Julious Berny Blinda', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'bernyblinda3120@gmail.com', 'bernyblinda200403@gmail.com', '+94760615552', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 00:43:09'),
(18, '528032402', NULL, '2002-09-17', 'Ms. Kiruthika Paramananthan', 'Ms.', 'Paramananthan kiruththika', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'shansathurshan884@gmail.com', 'paramananthankiruththika@gmail.com', '+94768052605', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 10:38:01'),
(19, '528032403', NULL, '2005-02-22', 'Thiththalapitiyage Shonaleen Varsha Fonseka', 'Ms.', 'Shonaleen Fonseka', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'shonaleenfonseka@gmail.com', 'shonaleenfonseka@gmail.com', '+94770388478', NULL, 'paid', 'paid', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-02-28 10:41:57'),
(20, '528032407', NULL, '2003-05-08', 'Ms. E.G. Narmada Hemadrie De Silva', 'Ms.', 'Narmada Hemadrie De Silva', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'narmadahdesilva@gmaill.com', 'narmadahdesilva@gmail.com', '+94757023071', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 11:06:56'),
(21, '622032406', NULL, '2005-04-29', 'Ms. Ranumi Dahanaggama Arachchi', 'Ms.', 'Ranumi Dahanaggama Arachchi', 1, 'Higher Diploma In Biotechnology Science - Batch 22', 'dammsprom@yahoo.com', 'dammsprom@yahoo.com', '+94755074828', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 11:07:35'),
(22, '622032408', NULL, '2005-10-01', 'Ms. Moksha Chathurika Dananjane', 'Miss.', 'Moksha Chathurika', 1, 'Higher Diploma In Biotechnology Science - Batch 22', 'dhanushka2002426@gmail.com', 'dhanushka2002426@gmail.com', '+94750395006', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 11:45:51'),
(23, '528032440', NULL, '2003-12-29', 'Sivatharshan Mohanakumar', 'Mr.', 'Sivatharshan Mohanakumar', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'sivatharshanmohanakumar@gmail.com', 'sivatharshanmohanakumar@gmail.com', '+94773084668', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 12:22:27'),
(24, '528032430', NULL, '2002-04-08', 'Ms. Sajitha Makenthiran', 'Miss.', 'Sajitha Makenthiran', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'sajithamahenthiran@gmail.com', 'sajitha.makenthiran@bms.ac.lk', '+94767669489', NULL, 'paid', NULL, NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 12:58:27'),
(25, '917032407', NULL, '2003-05-01', 'Ms. Ursula Pavithree Wannige', 'Miss.', 'Ursula pavithree', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'shashiprabha20060@gmail.com', 'Ursula.Pavithree@outlook.com', '+94741340422', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 13:28:39'),
(26, '622032401', NULL, '2004-04-22', 'Ms. Ama Ranathunga', 'Ms.', 'Ama Ranathunga', 1, 'Higher Diploma In Biotechnology Science - Batch 22', 'aranathunga04@gmail.com', 'aranathunga04@gmail.com', '+94740011330', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 13:40:28'),
(27, '528032401', NULL, '2001-06-20', 'Ms. Mohammed Safrin Aafrin Aysha', 'Miss.', 'Aysha Safrin', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'aafrinasha2001@gmail.com', 'Aafrinaysha2001@gmail.com', '+94701777979', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 14:00:16'),
(28, '1102032401', NULL, '2003-09-25', 'Suha Ahmed Naseerdeen', 'Ms.', 'Suha Ahmed Naseerdeen', 1, 'Higher Diploma in Medical Biotechnology - Batch 02', 'suhasan1131@gmail.com', 'suhasan1131@gmail.com', '+94765818909', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 14:16:39'),
(29, '528032411', NULL, '2001-01-12', 'Sri Ranjan Bathanchaliy Meenaambal', 'Ms.', 'SRI RANJAN', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'srihary.b200112@gmail.com', 'bathanchaliy.ranjan@bms.ac.lk', '+94776977009', NULL, 'paid', 'paid', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-02-28 17:48:37'),
(30, '528032409', NULL, '1998-04-16', 'Ms. Nandhujah Gunasheharan', 'Miss.', 'Nandhujah Gunasheharan', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'nandhujahvarma@gmail.com', 'nandhujahvarma@gmail.com', '+94767307271', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 18:33:09'),
(31, '917032432', NULL, '2002-10-15', 'Ms. Rishka Fazaal', 'Ms.', 'Rishka Fazaal', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'rishkafazaal@gmail.com', 'rishkafazaal@gmail.com', '+94743489207', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 18:59:23'),
(32, '528032416', NULL, '2003-09-21', 'Ms. Siddeeq Fathima Shaheera', 'Miss.', 'Shaheera siddeeq', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'shaheerasiddeeq@gmail.com', 'shaheerasiddeeq@gmail.com', '+94774009037', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 21:38:44'),
(33, '917032447', NULL, '2004-03-10', 'Ms. Lenin Leno Sharon Olivia', 'Miss.', 'Lenin Sharon Olivia', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'leninolivia73@gmail.com', 'leninolivia1@gmail.com', '+94750821679', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-02-28 23:45:25'),
(34, '528032442', NULL, '2004-11-05', 'Ms. Apitha Suresh', 'Ms.', 'Apitha Suresh', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'apithasuresh624@gmail.com', 'apithasuresh624@gmail.com', '+94742854547', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-01 10:39:18'),
(35, '528032419', NULL, '2004-11-30', 'Ms. Harithra Ramesh', 'Ms.', 'Harithra Ramesh', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'harithraramesh7@gmail.com', 'harithraramesh7@gmail.com', '+94768392203', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-01 10:39:41'),
(36, '917032416', NULL, '2003-08-20', 'Ms. Fathima Amra Najimudeen', 'Ms.', 'Fathima Amra Najimudeen', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'amranajimudeen23@gmail.com', 'amranajimudeen23@gmail.com', '+94754897989', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-01 14:25:41'),
(37, '528032438', NULL, '2003-06-13', 'Ms. N.Chamathka Edirirathna', 'Ms.', 'N Chamathka Edirirathna', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'nizayainsaad@gmail.com', 'nizayainsaad@gmail.com', '+94763037637', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-01 20:29:46'),
(38, '528032423', NULL, '2003-12-13', 'Ms. Malshi Imesha Pathiranage', 'Ms.', 'Malshi Pathiranage', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'mal.oOime@gmail.com', 'malshi.pathiranage@bms.ac.lk', '+94701167400', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-02 06:16:42'),
(39, '917032404', NULL, '2004-08-23', 'Ms. Radinka Jinelli Fernando', 'Miss.', 'Radinka FerNANDO', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'radinkafernando@gmail.com', 'radhinkafernando@gmail.com', '+94775748422', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-02 11:25:51'),
(40, '528032433', NULL, '2003-11-23', 'Ms. Shamila Nijamdeen', 'Ms.', 'Shamila Nijamdeen', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'shami2311jesus@gmail.com', 'shamila.nijamdeen@bms.ac.lk', '+94773873928', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-02 12:52:05'),
(41, '1004032401', NULL, '1999-02-06', 'Ms. Wijesiri Narange Ashani Samudika', 'Miss.', 'Ashani  Samudika', 1, 'Higher Diploma in Food Science and Nutrition- Batch 04', 'itsashani@gmail.com', 'samudika99@outlook.com', '+94761660222', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-02 17:10:05'),
(42, '528032406', NULL, '2000-07-19', 'Ms. Gayashi Anupama Jayawardana', 'Ms.', 'Gayashi Anupama Jayawardana', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'gayashianupama00@gmail.com', 'gayashianupama00@gmail.com', '+94710430181', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-02 21:01:54'),
(43, '528032443', NULL, '2004-01-21', 'Mr. Dhanushan Sekar', 'Mr.', 'Dhanushan Sekar', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'dhanushansekar@gmail.com', 'dhanushansekar@gmail.com', '+94726899927', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 10:20:37'),
(44, '528032418', NULL, '2004-10-05', 'Ms. Asini  Fernando', 'Ms.', 'Asini Fernando', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'asinikithma@gmail.com', 'asinikithma@gmail.com', '+94740024769', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 12:49:37'),
(45, '527102330', NULL, '2005-10-02', 'Ms. Tharuki Subanya Pieris', 'Ms.', 'Tharuki subanya Peiris', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'tharukipeiris@gmail.com', 'tharukipeiris8@gmail.com', '+94772158587', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 13:11:14'),
(46, '917032496', NULL, '2003-09-16', 'Ms. Risda Sahna Rizwan', 'Miss.', 'Risda Rizwan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'risdasahna18@gmail.com', 'risda.rizwan@bms.ac.lk', '+94760872932', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 13:49:34'),
(47, '917032448', NULL, '2005-03-20', 'Ms. Habeeba Zareen', 'Ms.', 'Habeeba Zareen', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'habeebazareen6@gmail.com', 'habeebazareen6@gmail.com', '+94759886651', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 14:27:01'),
(48, '917032470', NULL, '2005-10-06', 'Mr. Umair Imthiyasdeen', 'Mr.', 'Umair Imthiyasdeen', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mohamedumair999@gmail.com', 'mohamedumair999@gmail.com', '+94764305573', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 15:26:11'),
(49, '528032404', NULL, '2001-03-26', 'Ms. Gajani Rajeswaran', 'Ms.', 'Gajani Rajeswaran', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'gajani.rajeshwaran@icloud.com', 'gajani.rajeshwaran@icloud.com', '+94778161553', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-03-03 17:54:27'),
(50, '1002032304', NULL, '2003-04-17', 'Mr. Arunasalam Rathushan', 'Mr.', 'Arunasalam Rathushan', 1, 'Higher Diploma in Food Science and Nutrition- Batch 04', 'radushanr@gmail.com', 'Arunasalam.rathushan@bms.ac.lk', '+94760480528', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-03 20:09:45'),
(51, '528032421', NULL, '2002-02-12', 'Mr. Ahamed Lebbe Ahamed Sahee', 'Mr.', 'Ahamed Sahee', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'callmesahee02@gmail.com', 'callmesahee02@gmail.com', '+94781163530', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-03-04 13:30:40'),
(52, '528032445', NULL, '2004-08-19', 'Ms. Sharuniya Pradaa Mahendran', 'Ms.', 'Sharuniyapradaa Mahendran', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'sharuniyapradaam@gmail.com', 'sharuniyapradaam@gmail.com', '+94763047211', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-04 13:30:41'),
(53, '622032411', NULL, '2007-09-06', 'Themavee Wijekoon', 'Miss.', 'Themavee Wijekoon', 1, 'Higher Diploma In Biotechnology Science - Batch 22', 'themaveesw07@gmail.com', 'themaveesw07@gmail.com', '+94760951687', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-04 13:38:06'),
(54, '528032415', NULL, '2002-10-17', 'Ms. Saraniya Muralidaran', 'Ms.', 'Saraniya Muralidaran', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'muralitharansaraniya@gmail.com', 'muralitharansaraniya@gmail.com', '+94762494639', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-03-04 13:39:18'),
(55, '917032413', NULL, '2005-10-07', 'Ms. Nilakshi Sivathas', 'Ms.', 'Nilakshi Sivadhas', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'nila41767@gmail.com', 'nila41767@gmail.com', '+94765450060', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-04 14:30:42'),
(56, '917032427', NULL, '2004-02-03', 'Ms. Thachchana Moorthy Stelina', 'Ms.', 'Stalina Moorthy', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'moorthystalina@gmail.com', 'moorthystalina@gmail.com', '+94764256230', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-04 16:50:03'),
(57, '917032415', NULL, '2005-03-17', 'Ms. Vishalani Mahendran', 'Ms.', 'Vishalani Mahendran', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mahendranvishalani1708@gmail.com', 'mahendranvishalani1708@gmail.com', '+94767659775', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-04 17:19:45'),
(58, '917032478', NULL, '2004-11-15', 'Ms. Chathupama Perera', 'Ms.', 'Chathupama Devmini', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'chathupamaperera1234@gmail.com', 'chathupamaperera1234@gmail.com', '+94784607091', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-04 23:07:54'),
(59, '917032451', NULL, '2007-05-10', 'Ms. Niamath Shakeel', 'Ms.', 'Niamath Shakeel', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'niamath3890@gmail.com', 'niamath3890@gmail.com', '+94789804004', NULL, 'unpaid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-05 05:11:47'),
(60, '528032434', NULL, '2004-03-02', 'Ms.  Kosgodage Thewni Nadara Dharmasiri', 'Ms.', 'Thewni Dharmasiri', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'thewnidharmasiri@gmail.com', 'thewnidharmasiri@gmail.com', '+94775717425', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-05 10:35:36'),
(61, '917032402', NULL, '2004-09-12', 'Ms. Minda Oliniya Rozairo', 'Ms.', 'Minda Rozairo', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mindaoliniya@gmail.com', 'mindaoliniya@gmail.com', '+94770599598', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-05 17:14:06'),
(62, '528032431', NULL, '2004-04-22', 'Mr. Mohammed Azwear Ali Mohammed Aadhil Najmi', 'Mr.', 'Azwear Ali Mohammed Aadhil Najmi', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'Aadhilnajmi2004@gmail.com', 'aadhilnajmi2004@gmail.com', '+94752422414', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 08:24:41'),
(63, '917032423', NULL, '2004-06-18', 'Mr. Mohamed Mawfeen Umar', 'Mr.', 'Mohamed Umar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mohamedumar3030@gmail.com', 'mawfeen.umar@bms.ac.lk', '+94702018031', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 10:08:02'),
(64, '528032420', NULL, '2003-11-25', 'Mr. Ugendraraj Vijayakumar', 'Mr.', 'Ugendraraj Vijayakumar', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'ugendraraj1325@gmail.com', 'vijayakumar.ugendraraj@bms.ac.lk', '+94768583212', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 10:19:25'),
(65, '528032414', NULL, '2004-04-29', 'Ms. Thewaratantrige Sathmini Navodya Fernando', 'Miss.', 'Navodya Fernando', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'navodyafernando841@gmail.com', 'sathmini.fernando@bms.ac.lk', '+94762138340', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 11:19:06'),
(66, '917032472', NULL, '2004-02-14', 'Ms. Fathima Rishadha Uvais', 'Ms.', 'Fathima Rishadha Uvais', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'rishada90heerdv@gmail.com', 'rishadha525@gmail.com', '+94781499112', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 11:28:10'),
(67, '528032412', NULL, '1999-03-07', 'Ms. Abinaya Balasingam', 'Ms.', 'Abinaya Balasingam', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'abiyabalan73@gmail.com', 'abinaya.balasingam@bms.ac.lk', '+94761999122', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 12:57:33'),
(68, '917032487', NULL, '2004-04-08', 'Ms. Muhammad Siraj Fathima Safiyya', 'Ms.', 'Safiyya Siraj', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'safiyyasiraj@gmail.com', 'safiyyasiraj@gmail.com', '+94763635365', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 14:34:24'),
(69, '528032428', NULL, '2005-01-16', 'Mr. Mohamed Iesa Ismail', 'Mr.', 'iesa ismail', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'iesai3214@gmail.com', 'iesai3214@gmail.com', '+94754407729', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 16:48:32'),
(70, '622032405', NULL, '2006-08-19', 'Ms. Nauththuduwa Liyanage Don Kavisha Kalhari', 'Miss.', 'Kavisha Kalhari', 1, 'Higher Diploma In Biotechnology Science - Batch 22', 'kavishakalhari06@gmail.com', 'kavishakalhari06@gmail.com', '+94763287647', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-06 19:21:59'),
(71, '528032408', NULL, '2004-10-06', 'Ms. Sahar Khushbu Nadeem', 'Ms.', 'Sahar Khushbu Nadeem', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'saharnadeem458@gmail.com', 'sahar.nadeem@bms.ac.lk', '+94765302386', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 00:28:28'),
(72, '528032413', NULL, '2003-05-26', 'Ms. Tharuniya Anpalakan', 'Ms.', 'Tharuniya Anpalakan', 1, 'Biomedical science', 'tharuniya7972@gmail.com', 'tharuniya.anpalakan@bms.ac.lk', '+94770582685', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 04:39:03'),
(73, '917032446', NULL, '2004-12-17', 'Mr. Krithigan Sugumaran', 'Mr.', 'Krithigan Sugumaran', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'skrithigan@gmail.com', 'skrithigan@gmail.com', '+94713500939', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 10:28:57'),
(74, '917032488', NULL, '2005-01-01', 'Adeeshan Saravanamohan', 'Mr.', 'Adeeshan saravanamohan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'adeeshan08@gmail.com', 'adeeshan08@gmail.com', '+94753150580', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 10:54:00'),
(75, '917032430', NULL, '2001-08-01', 'Muhammad Luqman Azwar', 'Mr.', 'Luqman Azwar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'luqmanazwer7@gmail.com', 'luqmanazwar7@gmail.com', '+94766370496', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 11:01:24'),
(76, '917032459', NULL, '2003-11-20', 'Sinthiya Madanmohan', 'Miss.', 'Sinthiya Madanmohan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'sinthiyamdanmohan@gmail.com', 'sinthiyamathanmohan@gmail.com', '+94767967443', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 11:05:02'),
(77, '917032437', NULL, '2004-07-29', 'Nimenma Methsiluni', 'Miss.', 'Nimenma Methsiluni', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'nimenmaarachchi@bms.ac.lk', 'nimethma.arachchi@bms.ac.lk', '+94762440115', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 11:44:05'),
(78, '528032435', NULL, '2003-01-30', 'Ms. Haribaashini Muralitharan', 'Ms.', 'Haribaashini Muralitharan', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'baashini2003@gmail.com', 'baashini2003@gmail.com', '+94761227983', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 15:25:32'),
(79, '917032449', NULL, '2004-04-17', 'Mr. Iqbal Abdul Muyeeth', 'Mr.', 'ABDHUL MUYEETH IQBAL', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'muyeeth4@gmail.com', 'muyeeth4@gmail.com', '+94770327501', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 16:06:12'),
(80, '917032494', NULL, '2002-10-12', 'Yuthmini Malsiluni', 'Miss.', 'Yuthmini Malsiluni', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'yuthminimalsiluni@gmail.com', 'yuthmini.malsiluni@bms.ac.lk', '+94767796485', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 16:22:08'),
(81, '528032405', NULL, '2002-06-25', 'Ms. Sayini Pushpakumar', 'Ms.', 'Sayini Pushpakumar', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'sayinipushpakumar@gmail.com', 'sayinipushpakumar@gmail.com', '+94768071385', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-03-07 17:43:54'),
(82, '917032458', NULL, '2005-06-15', 'M.M. Yoonus Ahamedh', 'Mr.', 'Yoonus Ahamedh', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'yoonus.ahamedh@bms.ac.lk', 'yoonus.ahamedh@bms.ac.lk', '+94701780659', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 18:40:17'),
(83, '917032436', NULL, '2003-07-31', 'Nadanasabapathy Dhilrukshi', 'Ms.', 'Nadanasabapathy Dhilrukshi', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'dhi.nada2003@gmail.com', 'nadanasabapathy.dhilrukshi@bms.ac.lk', '+94768457438', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-07 18:45:00'),
(84, '917032438', NULL, '2006-09-02', 'Ms. Naysa Rishona Amarasinghe', 'Ms.', 'Naysa Amarasinghe', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'naysaamarasinghe@gmail.com', 'naysa.amarasinghe@bms.ac.lk', '+94702473749', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-03-08 08:31:36'),
(85, '917032401', NULL, '1999-11-19', 'Sangeetha Sivanesan', 'Miss.', 'Sangeetha Sivanesan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'sangeethanesan99@gmail.com', 'sangeetha.sivanesan@bms.ac.lk', '+94776990825', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 10:31:58'),
(86, '917032439', NULL, '2007-01-23', 'Mr. Thanooj Kathirason', 'Mr.', 'Thanooj kathirason', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'thanooj987@gmail.com', 'Thanooj987@gmail.com', '+94766371333', NULL, 'paid', 'paid', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-03-08 11:15:24'),
(87, '917032466', NULL, '2004-02-27', 'Mr. Mohomad Fazeem Wazeem', 'Mr.', 'Mohamad Fazeem Wazeem', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'wazeem_.2702@icloud.com', 'fazeemwazeem@gmail.com', '+94722057535', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 12:22:01'),
(88, '917032461', NULL, '2005-08-25', 'Mr. Yoosuf Sulaiman', 'Mr.', 'Yoosuf Sulaiman', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'yoosufsulaiman14@gmail.com', 'yoosufsulaiman14@gmail.com', '+94756090473', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 12:27:22'),
(89, '917032452', NULL, '2004-09-04', 'Ms. Lakshi Karnan', 'Ms.', 'Lakshi Karnan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'lakshikarnan840@gmail.com', 'lakshikarunan840@gmail.com', '+94763037668', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 12:30:19'),
(90, '917032418', NULL, '2003-08-20', 'Ms. Dushyanthy Rajendran', 'Miss.', 'Dushyanthy Rajendran', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'dushyanthiraj003@gmail.com', 'dushyathyraj003@gmail.com', '+94778551657', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Vegetarian', '2026-03-08 12:39:25'),
(91, '9170324101', NULL, '2005-07-01', 'Mr. Ahmed Walid Imthiyas', 'Mr.', 'Ahmed Walid Inthiyas', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'walidimthiyas11@gmail.com', 'ahmed.inthiyas@bms.ac.lk', '+94763509206', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 12:49:37'),
(92, '917032405', NULL, '2003-02-06', 'Ms. M. R. Rahmath Rashidha', 'Ms.', 'Rahmath Rashidha Rizan', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'rashidhari2an0206@gmail.com', 'rashidharizan0206@gmail.com', '+94771852481', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 13:27:14'),
(93, '917032468', NULL, '2004-11-18', 'Ms. Fathima Haleema Yoosuf', 'Ms.', 'Fathima Haleema Yoosuf', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'haleemayoosuf2004@gmail.com', 'haleemayoosuf678@gmail.com', '+94763119931', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 13:28:54'),
(94, '917032442', NULL, '2004-02-27', 'Ms. Fathima Nuzrath Mohammed Naizer', 'Ms.', 'Fathima Nuzrath Naizer', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'fathimanushrath272@gmail.com', 'fathimanuzrath272@gmail.com', '+94776370388', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 14:20:35'),
(95, '917032443', NULL, '2004-03-27', 'Mr. Aathif Anver', 'Mr.', 'Mohamed Anver Mohamed Aathif', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'aathifanver@gmail.com', 'aathifanver123@gmail.com', '+94754056308', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 14:41:08'),
(96, '914032338', NULL, '2003-06-21', 'Mr. Mohomed Ruzain Imad', 'Mr.', 'Imad Ruzain', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'imadruzain131@gmail.com', 'imadruzain131@gmail.com', '+94767390199', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 16:12:38'),
(97, '917032469', NULL, '2003-11-02', 'Ms. Suvidana Selvaraja', 'Ms.', 'Suvidana Selvaraja', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'suvisuvidana@gmail.com', 'suvisuvidana@gmail.com', '+94785067878', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-08 18:48:29'),
(98, '917032480', NULL, '2005-12-24', 'Mushab Aslam', 'Mr.', 'Mushab Mahmood', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mushabmahmood@gmail.com', 'mushab.mahmood@bms.ac.lk', '+94740551777', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-09 05:17:01'),
(99, '917032485', NULL, '2004-11-22', 'Hettiarachchilage Prabodha Vidumini', 'Miss.', 'Prabodha Vidumini', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'prabodavidumini26@gmail.com', 'Praboda.vidumini@bms.ac.lk', '+94760605697', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-09 08:11:32'),
(100, '917032471', NULL, '2001-04-20', 'Mr. Abdul Rahman Ashroff', 'Mr.', 'Ali Abdurrahman', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'aliabdurrahman2042001@gmail.com', 'aliabdurrahman2042001@gmail.com', '+94778619237', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-09 10:39:04'),
(101, '917032453', NULL, '2004-01-07', 'Mohamed Waseem Mashoor', 'Mr.', 'Waseem Mashoor', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'mohammedwaseem0701@gmail.com', 'mohamed.waseem@bms.ac.lk', '+94776880259', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-09 13:34:01'),
(102, '917032482', NULL, '2005-10-01', 'Miss. Harishmi Balaratnarajah Mohanakumar', 'Miss.', 'Harishmi Balaratnarajah Mohanakumar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'b.m.harishmi110@gmail.com', 'harishmi.mohanakumar@bms.ac.lk', '+94777263642', NULL, 'Paid', 'paid', NULL, NULL, 'Vegetarian', 'Vegetarian', '2026-03-09 15:24:25'),
(103, '528032436', NULL, '2001-10-17', 'Ms.  Zahara Ismail', 'Ms.', 'Zahara Ismail', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'zaharaismail0075@gmail.com', 'zaharaismail0075@gmail.com', '+94764300321', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-09 15:47:57'),
(104, '917032455', NULL, '2005-01-08', 'Haseef Ahamed', 'Mr.', 'Haseef', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'haseefahamed44@gmail.com', 'haseefahamed44@gmail.com', '+94763793318', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-09 16:09:20'),
(105, '528032441', NULL, '2005-06-17', 'Ms. Ramudhi Pemodhya De Silva', 'Ms.', 'Ramudhi de Silva', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'ramudhidesilva@gmail.com', 'ramudhipdesilva@gmail.com', '+94711626018', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Vegetarian', 'Non-Vegetarian', '2026-03-09 18:47:29'),
(106, '917032476', NULL, '2005-06-23', 'Ms. Rabiyah Badurdeen', 'Miss.', 'Rabiyah Badurdeen', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'rabhiyabd@gmail.com', 'fathima.badurdeen@bms.ac.lk', '+94766976896', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 08:50:41'),
(107, '917032426', NULL, '2003-12-24', 'Ms. Malindri Laleesha Wijeyesinghe', 'Ms.', 'Malindri Wijeyesinghe', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'malindriwijey@gmail.com', 'malindri.wijeyesinghe@bms.ac.lk', '+94723300230', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 09:40:44'),
(108, '528032444', NULL, '2005-09-02', 'Ms. Thisuri Cyara Jayaweera', 'Ms.', 'Thisuri Cyara Jayaweera', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'thisurijayaweera@gmail.com', 'thisuri.jayaweera@bms.ac.lk', '+94781092794', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 10:43:16'),
(109, '528032429', NULL, '2005-05-06', 'Ms. A.M.M Chathumini Hansika Jayawardene', 'Ms.', 'Chathumini Hansika Jayawardene', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'chathumini48hansika@gmail.com', 'chathumini.jayawardena@bms.ac.lk', '+94773109593', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 10:50:35'),
(110, '917032419', NULL, '2004-05-17', 'Leena Manohar', 'Miss.', 'Leena Manohar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'manohar.leena@bms.ac.lk', 'leenamnhr@gmail.com', '+94768904647', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 11:59:21'),
(111, '528032425', NULL, '1999-03-27', 'Ms. Hidhushi Mahendran', 'Ms.', 'Hidhushi Mahendran', 1, 'Higher Diploma in Biomedical Science - Batch 28', 'hidhushim@gmail.com', 'hidhushi.mahendran@bms.ac.lk', '+94761813287', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 12:26:07'),
(112, '917032420', NULL, '2005-07-29', 'Ms. Ifla Imran', 'Ms.', 'Ifla Imran', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'iflaimran29@gmail.com', 'iflaimran29@gmail.com', '+94777413049', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 12:36:13'),
(113, '917032444', NULL, '2004-11-05', 'Mr. Don Anarga Joshua Colombege', 'Mr.', 'Joshua Colombege', 1, 'BTEC Higher National Diploma in Business', 'josh.colombege@gmail.com', 'josh.colombege@outlook.com', '+94750117112', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 13:26:28'),
(114, '917032481', NULL, '2006-06-07', 'Ms. Rakhsshaa Ravikumar', 'Ms.', 'Rakhsshaa Ravikumar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'rakhsshaar@gmail.com', 'rakhsshaa.ravikumar@bms.ac.lk', '+94704452737', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 13:34:12'),
(115, '917032424', NULL, '2006-11-16', 'Avinga Inosh Piyathilaka', 'Mr.', 'Avinga', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'ffavinga@gmail.com', 'avinga.inosh@bms.ac.lk', '+94775537146', NULL, 'paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 13:46:43'),
(116, '917032422', NULL, '2003-09-18', 'Ms. Asma Fahim', 'Ms.', 'Asma Fahim', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'asmafahim03@gmail.com', 'asmafahim03@gmail.com', '+94742283553', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 14:04:06'),
(117, '917032428', NULL, '2004-03-16', 'Ms. Ashani Hanks', 'Ms.', 'Ashani Hanks', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'ashanihanks@gmail.com', 'ashanihanks@gmail.com', '+94716099972', NULL, 'Paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 14:22:56'),
(118, '917032499', NULL, '2007-07-28', 'Mr. Dhakhshesh Shivashankar', 'Mr.', 'Dhakhshesh Shivashankar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'dhakhsheshshiva@gmail.com', 'dhakhsheshsiva@gmail.com', '+94777648241', NULL, 'Paid', 'paid', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 14:28:09'),
(119, '917032454', NULL, '2002-12-22', 'Mr. Anfaas Insaar', 'Mr.', 'Mohamed Anfaas Insaar', 1, 'BTEC Higher National Diploma in Business - Batch 17', 'anfaas112@gmail.com', 'anfaas112@gmail.com', '+94771222810', NULL, 'paid', 'Not-Completed', NULL, NULL, 'Non-Vegetarian', 'Non-Vegetarian', '2026-03-10 15:53:39');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bulk_data_table`
--
ALTER TABLE `bulk_data_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clothing_collections`
--
ALTER TABLE `clothing_collections`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `data_tables`
--
ALTER TABLE `data_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `email_log`
--
ALTER TABLE `email_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `extra_ticket_log`
--
ALTER TABLE `extra_ticket_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=418;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `payment_records`
--
ALTER TABLE `payment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `registered_students`
--
ALTER TABLE `registered_students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

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
