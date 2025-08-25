-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 18, 2025 at 02:58 AM
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
-- Database: `clinic_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` varchar(100) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `status` enum('pending','approved','declined','rescheduled','confirmed') DEFAULT 'pending',
  `email` varchar(255) DEFAULT NULL,
  `parent_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `student_id`, `date`, `time`, `reason`, `status`, `email`, `parent_email`, `created_at`) VALUES
(1, 40, '2025-08-18', '18:17-19:18', 'Aut dolores eligendi', 'approved', 'cuwanixid@mailinator.com', 'ziqefesu@mailinator.com', '2025-08-17 10:25:53'),
(2, 34, '2025-08-18', '00:32-03:36', 'Eligendi iusto aliqu', 'approved', 'wylufaj@mailinator.com', 'luted@mailinator.com', '2025-08-17 16:48:19'),
(3, 35, '2025-08-20', '01:17-01:47', 'Non et eu ipsa natu', 'pending', 'tejeqogiz@mailinator.com', 'bekox@mailinator.com', '2025-08-17 17:18:04'),
(4, 35, '2025-08-20', '01:47-02:17', 'Animi sit irure re', 'pending', 'kykasokaby@mailinator.com', 'mucuvy@mailinator.com', '2025-08-17 17:18:09'),
(5, 35, '2025-08-19', '01:31-02:01', 'Qui eligendi dolorem', 'pending', 'mabemepo@mailinator.com', 'xukon@mailinator.com', '2025-08-17 17:32:43'),
(6, 29, '2025-08-19', '02:01-02:31', 'Eum soluta recusanda', 'pending', 'fyhot@mailinator.com', 'ryma@mailinator.com', '2025-08-17 17:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedules`
--

CREATE TABLE `doctor_schedules` (
  `id` int(11) NOT NULL,
  `doctor_name` varchar(255) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_time` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_schedules`
--

INSERT INTO `doctor_schedules` (`id`, `doctor_name`, `schedule_date`, `schedule_time`, `created_at`) VALUES
(31, 'rhona', '2025-08-19', '01:31-03:33', '2025-08-18 01:31:51');

-- --------------------------------------------------------

--
-- Table structure for table `imported_patients`
--

CREATE TABLE `imported_patients` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `dob` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `upload_year` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imported_patients`
--

INSERT INTO `imported_patients` (`id`, `student_id`, `name`, `dob`, `gender`, `address`, `civil_status`, `password`, `year_level`, `upload_year`) VALUES
(21, 'SCC-22-00015336', 'Abella, Joseph B.', '3/19/2000', 'Male', 'Camarin Vito Minglanilla Cebu', 'Single', 'Abella', '1st Year', NULL),
(22, 'SCC-22-00017358', 'Abellana, Vincent Anthony Q.', '7/8/2002', 'Male', 'Pakigne Minglanilla Cebu', 'Single', 'Abellana', '1st Year', NULL),
(23, 'SCC-20-00010846', 'Abendan, Christian James A.', '4/27/2004', 'Male', 'Pob. Ward 2 Minglanilla, Cebu', 'Single', 'Abendan', '1st Year', NULL),
(24, 'SCC-14-0001275', 'Abendan, Nino Rashean T.', '2/12/2002', 'Male', 'Ward 2 pob., Minglanilla, Cebu ', 'Single', 'Abendan', '1st Year', NULL),
(25, 'SCC-21-00012754', 'Abellana, Ariel L', '10/1/2002', 'Male', 'Basak, Sibonga, Cebu', 'Single', 'Abellana', '2nd Year', NULL),
(26, 'SCC-21-00012377', 'Acidillo, Baby John V.', '7/21/2000', 'Male', 'Bairan City of Naga', 'Single', 'Acidillo', '2nd Year', NULL),
(27, 'SCC-21-00014490', 'Adona, Carl Macel C.', '3/29/2002', 'Male', 'Pob. Ward IV Minglanilla Cebu', 'Single', 'Adona', '2nd Year', NULL),
(28, 'SCC-19-0009149', 'Albiso, Creshell Mary M.', '6/18/2003', 'Female', 'Bairan, City of Naga, Cebu', 'Single', 'Albiso', '2nd Year', NULL),
(29, 'SCC-21-00014673', 'Alegado, John Raymon B.', '1/9/2002', 'Male', 'Tagjaguimit City of Naga Cebu', 'Single', 'Alegado', '2nd Year', NULL),
(30, 'SCC-18-0007848', 'Aguilar, Jaymar C', '2/22/2000', 'Male', 'North Poblacion, San Fernando, Cebu', 'Single', 'Aguilar', '3rd Year', NULL),
(31, 'SCC-18-0006048', 'Alicaya, Ralph Lorync D.', '1/17/2000', 'Male', 'Lower Pakigne, Minglanilla Cebu', 'Single', 'Alicaya', '3rd Year', NULL),
(32, 'SCC-20-00011552', 'Baraclan, Genesis S.', '11/12/1999', 'Male', 'Bacay Tulay Minglanilla Cebu', 'Single', 'Baraclan', '3rd Year', NULL),
(33, 'SCC-18-0007440', 'Base, Jev Adrian', '11/8/2001', 'Male', 'Sambag, Tuyan, City of Naga, Cebu', 'Single', 'Base', '3rd Year', NULL),
(34, 'SCC-19-00010521', 'Booc, Aloysius A.', '6/6/1996', 'Male', 'Babag Lapulapu City', 'Single', 'Booc', '3rd Year', NULL),
(35, 'SCC-18-0007793', 'Adlawan, Ealla Marie', '11/7/1999', 'Female', 'Spring Village Pakigne, Minglanilla', 'Single', 'Adlawan', '4th Year', NULL),
(36, 'SCC-19-00010625', 'Alferez Jr., Bernardino S.', '8/12/1999', 'Male', 'Cantao-an Naga Cebu', 'Single', 'Alferez Jr.', '4th Year', NULL),
(37, 'SCC-19-0009987', 'Almendras, Alistair A', '4/21/2000', 'Male', 'Purok Mahogany, Sambag Kolo, Tuyan City of Naga, Cebu', 'Single', 'Almendras', '4th Year', NULL),
(38, 'SCC-17-0005276', 'Alvarado, Dexter Q.', '7/12/1999', 'Male', 'Babayongan Dalaguete Cebu', 'Single', 'Alvarado', '4th Year', NULL),
(39, 'SCC-19-00010487', 'Amarille, Kim Ryan M', '10/31/1997', 'Male', 'Tungkop Minglanilla Cebu', 'Single', 'Amarille', '4th Year', NULL),
(40, 'SCC-18-0008724', 'Arcamo Jr., Emmanuel P.', '10/1/1997', 'Male', 'Sitio Tabay Tunghaan, Minglanilla Cebu', 'Single', 'Arcamo Jr.', '4th Year', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_email` varchar(255) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `user_email`, `action`, `timestamp`) VALUES
(1, 10, 'jaynu123@gmail.com', 'Logged in', '2025-05-17 07:43:37'),
(2, 9, 'jaynu@gmail.com', 'Logged in', '2025-05-17 07:43:52'),
(3, 10, 'jaynu123@gmail.com', 'Logged in', '2025-05-17 07:47:58'),
(4, 9, 'jaynu@gmail.com', 'Logged in', '2025-05-17 07:48:29'),
(5, 10, 'jaynu123@gmail.com', 'Logged in', '2025-05-17 07:53:03'),
(6, 11, 'jaynu@gmail.com', 'Logged in', '2025-05-17 09:24:49'),
(7, 12, 'jaynu123@gmail.com', 'Logged in', '2025-05-17 09:25:26'),
(8, 14, 'jaynu@gmail.com', 'Logged in', '2025-05-17 09:34:38'),
(9, 15, 'jaynu123@gmai.com', 'Logged in', '2025-05-17 09:35:01'),
(10, 1, 'jaynu@gmail.com', 'Logged in', '2025-05-17 09:44:01'),
(11, 5, 'jaynu', 'Logged in', '2025-05-17 14:21:46'),
(12, 6, 'jaynu123', 'Logged in', '2025-05-17 14:22:09'),
(13, 5, 'jaynu', 'Logged in', '2025-05-17 14:43:01'),
(14, 6, 'jaynu123', 'Logged in', '2025-05-17 14:43:11'),
(15, 5, 'jaynu', 'Logged in', '2025-05-17 23:21:14'),
(16, 5, 'jaynu', 'Logged in', '2025-05-18 00:53:56'),
(17, 5, 'jaynu', 'Logged in', '2025-05-18 00:56:05'),
(18, 5, 'jaynu', 'Logged in', '2025-05-18 00:58:29'),
(19, 5, 'jaynu', 'Logged in', '2025-05-18 01:04:40'),
(20, 6, 'jaynu123', 'Logged in', '2025-05-18 01:05:40'),
(21, 5, 'jaynu', 'Logged in', '2025-05-18 17:56:55'),
(22, 5, 'jaynu', 'Logged in', '2025-05-18 17:59:06'),
(23, 5, 'jaynu', 'Logged in', '2025-05-18 18:07:24'),
(24, 5, 'jaynu', 'Logged in', '2025-05-18 23:53:12'),
(25, 5, 'jaynu', 'Logged in', '2025-05-19 00:02:38'),
(26, 5, 'jaynu', 'Logged in', '2025-05-19 00:37:41'),
(27, 5, 'jaynu', 'Logged in', '2025-05-19 00:38:58'),
(28, 6, 'jaynu123', 'Logged in', '2025-05-19 01:08:05'),
(29, 5, 'jaynu', 'Logged in', '2025-05-19 01:48:50'),
(30, 6, 'jaynu123', 'Logged in', '2025-05-19 01:59:54'),
(31, NULL, 'Staff', 'Issued prescription for patient: Abellana, Ariel L', '2025-05-19 02:17:16'),
(32, NULL, 'Staff', 'Submitted prescription for patient: Abendan, Nino Rashean T.', '2025-05-19 02:17:34'),
(33, NULL, 'Unknown', 'Deleted medicine: Dolor eiusmod quidem', '2025-05-19 02:19:39'),
(34, NULL, 'Unknown', 'Edited medicine: Biogesics', '2025-05-19 02:19:51'),
(35, 5, 'jaynu', 'Logged in', '2025-05-19 03:16:02'),
(36, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-19 03:16:33'),
(37, 5, 'jaynu', 'Logged in', '2025-05-19 21:25:51'),
(38, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-19 21:30:30'),
(39, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-19 21:30:46'),
(40, NULL, 'Unknown', 'Added medicine: rhona tig nawnf', '2025-05-19 21:33:13'),
(41, NULL, 'Unknown', 'Deleted medicine: rhona tig nawnf', '2025-05-19 21:33:28'),
(42, NULL, 'Unknown', 'Edited medicine: Excepturi qui in vit', '2025-05-19 21:41:44'),
(43, NULL, 'Unknown', 'Edited medicine: Fugit voluptate bea', '2025-05-19 21:41:52'),
(44, NULL, 'Unknown', 'Edited medicine: Laurel Dejesus', '2025-05-19 21:41:58'),
(45, NULL, 'Unknown', 'Edited medicine: mefinamic', '2025-05-19 21:42:04'),
(46, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-19 22:11:09'),
(47, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-05-19 22:11:18'),
(48, 5, 'jaynu', 'Logged in', '2025-05-19 22:16:10'),
(49, 6, 'jaynu123', 'Logged in', '2025-05-19 22:16:30'),
(50, 38, 'SCC-17-0005276', 'Logged in', '2025-05-19 22:31:37'),
(51, 38, 'SCC-17-0005276', 'Logged in', '2025-05-19 22:32:03'),
(52, 37, 'SCC-19-0009987', 'Logged in', '2025-05-19 22:32:28'),
(53, 37, 'SCC-19-0009987', 'Logged in', '2025-05-19 22:49:42'),
(54, 39, 'SCC-19-00010487', 'Logged in', '2025-05-19 22:57:34'),
(55, 40, 'SCC-18-0008724', 'Logged in', '2025-05-19 23:01:48'),
(56, 40, 'SCC-18-0008724', 'Logged in', '2025-05-19 23:08:03'),
(57, 5, 'jaynu', 'Logged in', '2025-05-19 23:32:14'),
(58, 27, 'SCC-21-00014490', 'Logged in', '2025-05-20 01:54:42'),
(59, 40, 'SCC-18-0008724', 'Logged in', '2025-05-20 01:55:41'),
(60, 6, 'jaynu123', 'Logged in', '2025-05-20 02:10:46'),
(61, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 02:16:52'),
(62, 5, 'jaynu', 'Logged in', '2025-05-20 03:57:29'),
(63, 6, 'jaynu123', 'Logged in', '2025-05-20 03:57:59'),
(64, 5, 'jaynu', 'Logged in', '2025-05-20 06:21:01'),
(65, 6, 'jaynu123', 'Logged in', '2025-05-20 06:21:12'),
(66, 40, 'SCC-18-0008724', 'Logged in', '2025-05-20 06:22:19'),
(67, 5, 'jaynu', 'Logged in', '2025-05-20 06:24:28'),
(68, 6, 'jaynu123', 'Logged in', '2025-05-20 06:24:45'),
(69, 39, 'SCC-19-00010487', 'Logged in', '2025-05-20 07:06:19'),
(70, 5, 'jaynu', 'Logged in', '2025-05-20 09:23:01'),
(71, 6, 'jaynu123', 'Logged in', '2025-05-20 09:23:11'),
(72, 40, 'SCC-18-0008724', 'Logged in', '2025-05-20 09:23:34'),
(73, 5, 'jaynu', 'Logged in', '2025-05-20 21:53:29'),
(74, 6, 'jaynu123', 'Logged in', '2025-05-20 21:53:38'),
(75, 40, 'SCC-18-0008724', 'Logged in', '2025-05-20 21:53:53'),
(76, NULL, 'Staff', 'Submitted prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 22:06:09'),
(77, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 22:06:41'),
(78, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-20 22:19:42'),
(79, NULL, 'Staff', 'Submitted prescription for patient: Alegado, John Raymon B.', '2025-05-20 22:52:46'),
(80, NULL, 'Staff', 'Submitted prescription for patient: Aguilar, Jaymar C', '2025-05-20 23:03:15'),
(81, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-20 23:08:49'),
(82, NULL, 'Staff', 'Submitted prescription for patient: Alferez Jr., Bernardino S.', '2025-05-20 23:18:18'),
(83, NULL, 'Staff', 'Issued prescription for patient: Alferez Jr., Bernardino S.', '2025-05-20 23:27:28'),
(84, NULL, 'Staff', 'Issued prescription for patient: Alferez Jr., Bernardino S.', '2025-05-20 23:27:28'),
(85, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 23:27:56'),
(86, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 23:27:56'),
(87, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-20 23:28:07'),
(88, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-20 23:28:07'),
(89, NULL, 'Staff', 'Issued prescription for patient: Alegado, John Raymon B.', '2025-05-20 23:28:56'),
(90, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 23:29:05'),
(91, NULL, 'Staff', 'Submitted prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 23:29:44'),
(92, NULL, 'Staff', 'Submitted prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 23:29:45'),
(93, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 23:30:02'),
(94, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 23:30:02'),
(95, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-05-20 23:30:08'),
(96, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 23:30:17'),
(97, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 23:30:17'),
(98, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-20 23:30:21'),
(99, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 00:03:18'),
(100, NULL, 'Staff', 'Submitted prescription for patient: Aguilar, Jaymar C', '2025-05-21 00:09:42'),
(101, NULL, 'Staff', 'Submitted prescription for patient: Aguilar, Jaymar C', '2025-05-21 00:09:43'),
(102, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-21 00:12:51'),
(103, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-21 00:12:51'),
(104, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-21 00:13:19'),
(105, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-21 00:13:19'),
(106, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 00:13:27'),
(107, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:16:48'),
(108, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:16:48'),
(109, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:17:17'),
(110, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:17:17'),
(111, NULL, 'Staff', 'Issued prescription for patient: Adona, Carl Macel C.', '2025-05-21 00:19:06'),
(112, NULL, 'Staff', 'Issued prescription for patient: Adona, Carl Macel C.', '2025-05-21 00:19:06'),
(113, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 00:19:10'),
(114, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 00:19:33'),
(115, NULL, 'Staff', 'Submitted prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 00:19:48'),
(116, NULL, 'Staff', 'Submitted prescription for patient: Abendan, Christian James A.', '2025-05-21 00:19:56'),
(117, NULL, 'Staff', 'Submitted prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:20:04'),
(118, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:20:13'),
(119, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-05-21 00:20:13'),
(120, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-05-21 00:20:59'),
(121, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 00:21:25'),
(122, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 00:21:25'),
(123, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 00:23:24'),
(124, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 00:23:24'),
(125, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 00:32:05'),
(126, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 00:32:16'),
(127, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 00:37:08'),
(128, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 00:37:17'),
(129, 5, 'jaynu', 'Logged in', '2025-05-21 02:32:14'),
(130, 5, 'jaynu', 'Logged in', '2025-05-21 04:03:37'),
(131, 5, 'jaynu', 'Logged in', '2025-05-21 04:18:38'),
(132, 5, 'jaynu', 'Logged in', '2025-05-21 04:18:59'),
(133, 6, 'jaynu123', 'Logged in', '2025-05-21 04:19:09'),
(134, 40, 'SCC-18-0008724', 'Logged in', '2025-05-21 04:19:30'),
(135, 21, 'SCC-22-00015336', 'Logged in', '2025-05-21 05:06:30'),
(136, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 05:07:14'),
(137, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 05:35:31'),
(138, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 05:35:49'),
(139, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 05:35:53'),
(140, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 05:35:59'),
(141, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 05:36:04'),
(142, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 05:58:23'),
(143, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:05:05'),
(144, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:05:18'),
(145, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:05:38'),
(146, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:05:51'),
(147, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:06:11'),
(148, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:06:18'),
(149, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:08:23'),
(150, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:08:31'),
(151, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:08:57'),
(152, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:08:58'),
(153, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:09:11'),
(154, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:09:14'),
(155, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 06:12:01'),
(156, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 06:12:10'),
(157, NULL, 'Staff', 'Submitted prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 06:24:30'),
(158, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 06:24:53'),
(159, NULL, 'Staff', 'Submitted prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 06:25:07'),
(160, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 06:25:15'),
(161, NULL, 'Staff', 'Submitted prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-21 06:25:26'),
(162, 5, 'jaynu', 'Logged in', '2025-05-21 15:06:45'),
(163, 5, 'jaynu', 'Logged in', '2025-05-21 22:47:16'),
(164, 6, 'jaynu123', 'Logged in', '2025-05-21 22:47:24'),
(165, 22, 'SCC-22-00017358', 'Logged in', '2025-05-21 22:48:15'),
(166, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-21 22:53:04'),
(167, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-21 22:53:53'),
(168, NULL, 'Unknown', 'Added medicine: Atay', '2025-05-22 00:05:49'),
(169, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-22 00:47:48'),
(170, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-05-22 00:48:02'),
(171, NULL, 'Staff', 'Submitted prescription for patient: Acidillo, Baby John V.', '2025-05-22 00:51:52'),
(172, NULL, 'Staff', 'Submitted prescription for patient: Acidillo, Baby John V.', '2025-05-22 00:51:53'),
(173, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-22 00:55:32'),
(174, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-22 00:56:05'),
(175, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-22 00:57:02'),
(176, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-05-22 01:27:04'),
(177, NULL, 'Staff', 'Submitted prescription for patient: Acidillo, Baby John V.', '2025-05-22 01:28:16'),
(178, NULL, 'Staff', 'Submitted prescription for patient: Acidillo, Baby John V.', '2025-05-22 01:28:33'),
(179, NULL, 'Staff', 'Submitted prescription for patient: Acidillo, Baby John V.', '2025-05-22 01:28:43'),
(180, NULL, 'Staff', 'Issued prescription for patient: Acidillo, Baby John V.', '2025-05-22 01:28:55'),
(181, NULL, 'Staff', 'Issued prescription for patient: Acidillo, Baby John V.', '2025-05-22 01:28:55'),
(182, NULL, 'Staff', 'Issued prescription for patient: Acidillo, Baby John V.', '2025-05-22 01:28:55'),
(183, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-05-22 01:29:17'),
(184, NULL, 'Staff', 'Submitted prescription for patient: Aguilar, Jaymar C', '2025-05-22 01:30:38'),
(185, NULL, 'Staff', 'Submitted prescription for patient: Aguilar, Jaymar C', '2025-05-22 01:31:07'),
(186, NULL, 'Staff', 'Submitted prescription for patient: Aguilar, Jaymar C', '2025-05-22 01:31:27'),
(187, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-22 01:31:37'),
(188, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-22 01:31:40'),
(189, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-05-22 01:31:42'),
(190, 5, 'jaynu', 'Logged in', '2025-05-22 01:38:49'),
(191, 6, 'jaynu123', 'Logged in', '2025-05-22 01:38:55'),
(192, 5, 'jaynu', 'Logged in', '2025-05-22 01:39:06'),
(193, 6, 'jaynu123', 'Logged in', '2025-05-22 01:39:46'),
(194, 40, 'SCC-18-0008724', 'Logged in', '2025-05-22 01:59:38'),
(195, 5, 'jaynu', 'Logged in', '2025-05-22 02:34:42'),
(196, 6, 'jaynu123', 'Logged in', '2025-05-22 02:35:53'),
(197, 5, 'jaynu', 'Logged in', '2025-05-22 02:37:10'),
(198, 6, 'jaynu123', 'Logged in', '2025-05-22 03:13:56'),
(199, 5, 'jaynu', 'Logged in', '2025-05-22 07:29:22'),
(200, 23, 'SCC-20-00010846', 'Logged in', '2025-05-22 07:36:22'),
(201, 5, 'jaynu', 'Logged in', '2025-05-22 11:00:23'),
(202, 6, 'jaynu123', 'Logged in', '2025-05-22 11:00:33'),
(203, 40, 'SCC-18-0008724', 'Logged in', '2025-05-22 11:05:05'),
(204, 40, 'SCC-18-0008724', 'Logged in', '2025-05-22 11:29:50'),
(205, 40, 'SCC-18-0008724', 'Logged in', '2025-05-22 11:42:22'),
(206, 5, 'jaynu', 'Logged in', '2025-05-22 17:56:16'),
(207, 6, 'jaynu123', 'Logged in', '2025-05-28 17:51:19'),
(208, 5, 'jaynu', 'Logged in', '2025-05-28 17:53:12'),
(209, 6, 'jaynu123', 'Logged in', '2025-05-28 17:55:14'),
(210, NULL, 'Unknown', 'Added medicine: Biogesic', '2025-05-28 17:55:41'),
(211, 5, 'jaynu', 'Logged in', '2025-05-28 17:56:01'),
(212, 6, 'jaynu123', 'Logged in', '2025-05-28 17:58:46'),
(213, NULL, 'Unknown', 'Added medicine: Biogesic', '2025-05-28 17:59:12'),
(214, 5, 'jaynu', 'Logged in', '2025-05-28 17:59:24'),
(215, 6, 'jaynu123', 'Logged in', '2025-05-28 18:00:06'),
(216, NULL, 'Unknown', 'Added medicine: Biogesic', '2025-05-28 18:03:07'),
(217, 5, 'jaynu', 'Logged in', '2025-05-28 18:03:15'),
(218, 6, 'jaynu123', 'Logged in', '2025-05-28 18:07:00'),
(219, NULL, 'jaynu123', 'Added medicine: Biogesic', '2025-05-28 18:07:19'),
(220, 5, 'jaynu', 'Logged in', '2025-05-28 18:07:33'),
(221, 6, 'jaynu123', 'Logged in', '2025-05-28 18:12:14'),
(222, NULL, 'jaynu123', 'Added medicine: Arthur Bates', '2025-05-28 18:15:14'),
(223, NULL, 'jaynu123', 'Added medicine: Aidan Downs', '2025-05-28 18:16:45'),
(224, NULL, 'jaynu123', 'Added medicine: Madeline Huber', '2025-05-28 18:28:25'),
(225, NULL, 'Unknown', 'Edited medicine: Aidan Downs', '2025-05-28 18:29:49'),
(226, 5, 'jaynu', 'Logged in', '2025-05-28 18:30:55'),
(227, 11, 'jaynu13', 'Logged in', '2025-05-28 18:33:53'),
(228, NULL, 'jaynu13', 'Added medicine: Ria Shields', '2025-05-28 18:34:04'),
(229, 10, 'admin', 'Logged in', '2025-05-28 18:34:15'),
(230, 11, 'jaynu13', 'Logged in', '2025-05-28 18:39:28'),
(231, NULL, 'jaynu13', 'Added medicine: Zeus Salazar', '2025-05-28 18:39:36'),
(232, 10, 'admin', 'Logged in', '2025-05-28 18:39:44'),
(233, 10, 'admin', 'Logged in', '2025-05-28 18:41:36'),
(234, 10, 'admin', 'Logged in', '2025-05-28 18:43:03'),
(235, 11, 'jaynu13', 'Logged in', '2025-05-28 18:43:36'),
(236, NULL, 'jaynu13', 'Added medicine: Zephr Higgins', '2025-05-28 18:43:43'),
(237, 10, 'admin', 'Logged in', '2025-05-28 18:43:53'),
(238, 10, 'admin', 'Logged in', '2025-05-28 18:44:12'),
(239, 10, 'admin', 'Logged in', '2025-05-28 18:44:56'),
(240, 11, 'jaynu13', 'Logged in', '2025-05-28 18:45:59'),
(241, NULL, 'Unknown', 'Deleted medicine: Aidan Downs', '2025-05-28 18:46:07'),
(242, NULL, 'Unknown', 'Deleted medicine: Alaxan', '2025-05-28 18:46:11'),
(243, NULL, 'Unknown', 'Edited medicine: Diatabs', '2025-05-28 18:47:19'),
(244, NULL, 'Unknown', 'Edited medicine: Neozep', '2025-05-28 18:47:27'),
(245, 11, 'jaynu13', 'Logged in', '2025-06-01 10:26:43'),
(246, 11, 'jaynu13', 'Logged in', '2025-06-01 10:30:15'),
(247, 11, 'jaynu13', 'Logged in', '2025-06-01 10:33:03'),
(248, NULL, 'jaynu13', 'Added medicine: bioGesic', '2025-06-01 11:04:44'),
(249, NULL, 'jaynu13', 'Added medicine: Biogesic', '2025-06-01 11:11:48'),
(250, 10, 'admin', 'Logged in', '2025-06-01 11:12:10'),
(251, 11, 'jaynu13', 'Logged in', '2025-06-01 11:12:51'),
(252, NULL, 'jaynu13', 'Added medicine: diatabs', '2025-06-01 11:13:43'),
(253, 10, 'admin', 'Logged in', '2025-06-01 11:21:08'),
(254, 11, 'jaynu13', 'Logged in', '2025-06-01 11:37:14'),
(255, 10, 'admin', 'Logged in', '2025-06-01 11:38:43'),
(256, NULL, 'Staff', 'Submitted prescription for patient: Arcamo Jr., Emmanuel P.', '2025-06-01 11:57:18'),
(257, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-06-01 11:57:30'),
(258, NULL, 'Staff', 'Submitted prescription for patient: Arcamo Jr., Emmanuel P.', '2025-06-01 11:58:40'),
(259, NULL, 'Staff', 'Submitted prescription for patient: Arcamo Jr., Emmanuel P.', '2025-06-01 11:59:25'),
(260, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-06-01 11:59:32'),
(261, NULL, 'Staff', 'Issued prescription for patient: Arcamo Jr., Emmanuel P.', '2025-06-01 11:59:35'),
(262, NULL, 'admin', 'Added medicine: diATabs', '2025-06-01 12:06:41'),
(263, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-06-01 12:41:12'),
(264, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-06-01 12:45:34'),
(265, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-06-01 12:45:35'),
(266, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-06-01 12:45:56'),
(267, 10, 'admin', 'Logged in', '2025-06-01 13:02:41'),
(268, 12, 'jade', 'Logged in', '2025-06-01 13:03:13'),
(269, 11, 'jaynu13', 'Logged in', '2025-07-27 21:14:41'),
(270, 10, 'admin', 'Logged in', '2025-07-27 22:02:41'),
(271, 11, 'jaynu13', 'Logged in', '2025-07-27 22:03:43'),
(272, 11, 'jaynu13', 'Logged in', '2025-07-27 22:23:11'),
(273, 11, 'jaynu13', 'Logged in', '2025-07-31 17:29:41'),
(274, 10, 'admin', 'Logged in', '2025-07-31 17:33:58'),
(275, 11, 'jaynu13', 'Logged in', '2025-07-31 17:34:58'),
(276, 11, 'jaynu13', 'Logged in', '2025-07-31 17:35:39'),
(277, 10, 'admin', 'Logged in', '2025-07-31 18:11:43'),
(278, 11, 'jaynu13', 'Logged in', '2025-07-31 18:11:58'),
(279, 10, 'admin', 'Logged in', '2025-07-31 18:46:53'),
(280, 10, 'admin', 'Logged in', '2025-07-31 18:57:17'),
(281, 11, 'jaynu13', 'Logged in', '2025-07-31 18:57:53'),
(282, 11, 'jaynu13', 'Logged in', '2025-08-10 18:01:21'),
(283, 10, 'admin', 'Logged in', '2025-08-10 18:12:32'),
(284, 11, 'jaynu13', 'Logged in', '2025-08-10 18:13:29'),
(285, 10, 'admin', 'Logged in', '2025-08-10 18:15:24'),
(286, 11, 'jaynu13', 'Logged in', '2025-08-10 18:18:15'),
(287, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-08-10 18:32:20'),
(288, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-10 18:32:47'),
(289, NULL, 'Staff', 'Submitted prescription for patient: Abella, Joseph B.', '2025-08-10 18:33:43'),
(290, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-10 18:33:56'),
(291, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-10 18:38:43'),
(292, NULL, 'jaynu13', 'Added medicine: Rexidol', '2025-08-10 19:19:20'),
(293, 6, 'jaynu123', 'Logged in', '2025-08-16 23:55:41'),
(294, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-17 00:12:58'),
(295, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-17 00:16:37'),
(296, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-17 00:16:43'),
(297, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-17 00:16:45'),
(298, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-08-17 00:27:38'),
(299, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-08-17 00:29:27'),
(300, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-08-17 00:29:34'),
(301, NULL, 'Staff', 'Issued prescription for patient: Abendan, Nino Rashean T.', '2025-08-17 00:29:38'),
(302, NULL, 'Staff', 'Issued prescription for patient: Acidillo, Baby John V.', '2025-08-17 00:31:00'),
(303, NULL, 'Staff', 'Issued prescription for patient: Adona, Carl Macel C.', '2025-08-17 00:34:02'),
(304, NULL, 'Staff', 'Issued prescription for patient: Acidillo, Baby John V.', '2025-08-17 00:48:49'),
(305, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-17 00:51:40'),
(306, NULL, 'Staff', 'Issued prescription for patient: Abellana, Ariel L', '2025-08-17 00:52:39'),
(307, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-17 00:53:20'),
(308, NULL, 'Staff', 'Issued prescription for patient: Aguilar, Jaymar C', '2025-08-17 01:12:04'),
(309, NULL, 'jaynu123', 'Added medicine: Bioflu', '2025-08-17 01:26:21'),
(310, NULL, 'jaynu123', 'Added medicine: Bioflu', '2025-08-17 01:27:30'),
(311, NULL, 'jaynu123', 'Added medicine: Bioflu', '2025-08-17 01:28:07'),
(312, 10, 'admin', 'Logged in', '2025-08-17 06:39:12'),
(313, 6, 'jaynu123', 'Logged in', '2025-08-17 06:39:19'),
(314, 6, 'jaynu123', 'Logged in', '2025-08-17 06:49:31'),
(315, 6, 'jaynu123', 'Logged in', '2025-08-17 06:55:01'),
(316, 6, 'jaynu123', 'Logged in', '2025-08-17 07:04:33'),
(317, 6, 'jaynu123', 'Logged in', '2025-08-17 07:05:13'),
(318, 6, 'jaynu123', 'Logged in', '2025-08-17 07:05:37'),
(319, NULL, 'jaynu123', 'Added medicine: Mefinamic', '2025-08-17 09:30:00'),
(320, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-08-17 12:10:37'),
(321, 6, 'jaynu123', 'Logged in', '2025-08-17 14:00:01'),
(322, 6, 'jaynu123', 'Logged in', '2025-08-17 18:24:44'),
(323, 10, 'admin', 'Logged in', '2025-08-17 18:25:14'),
(324, 6, 'jaynu123', 'Logged in', '2025-08-17 18:32:25'),
(325, 6, 'jaynu123', 'Logged in', '2025-08-17 19:01:17'),
(326, 10, 'admin', 'Logged in', '2025-08-18 00:27:51'),
(327, 6, 'jaynu123', 'Logged in', '2025-08-18 00:27:59'),
(328, 6, 'jaynu123', 'Logged in', '2025-08-18 01:35:57'),
(329, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 01:45:27'),
(330, 6, 'jaynu123', 'Logged in', '2025-08-18 08:53:27');

-- --------------------------------------------------------

--
-- Table structure for table `medication_referrals`
--

CREATE TABLE `medication_referrals` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `subjective` text DEFAULT NULL,
  `objective` text DEFAULT NULL,
  `assessment` text DEFAULT NULL,
  `plan` text DEFAULT NULL,
  `intervention` text DEFAULT NULL,
  `evaluation` text DEFAULT NULL,
  `recorded_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `referral_to` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medication_referrals`
--

INSERT INTO `medication_referrals` (`id`, `patient_id`, `patient_name`, `subjective`, `objective`, `assessment`, `plan`, `intervention`, `evaluation`, `recorded_by`, `created_at`, `referral_to`) VALUES
(1, 21, 'Abella, Joseph B.', 'sfdasdadsa', 'sdadsadsadsa', 'sdadsasdas', 'dsadsadsa', 'dsadadsa', 'dsadsasda', 'jaynu13', '2025-08-10 19:37:33', 'jaynu'),
(2, 28, 'Albiso, Creshell Mary M.', 'sdfsdsdf', 'SDFJSDJFQJDHJSJDFSDFSJDFDJS', 'SKJFSDFJSJDH', 'KSDJKFSKDFJK', 'KSJDKFJSKJD', 'KSJDFKSKFJKSDJF', 'jaynu123', '2025-08-17 01:12:48', NULL),
(3, 26, 'Acidillo, Baby John V.', 'Deserunt iure porro ', 'Veniam excepturi in', 'Voluptas veniam exp', 'Quis sapiente natus ', 'Qui in deserunt quis', 'Ea id consectetur se', 'jaynu123', '2025-08-17 01:13:59', NULL),
(4, 26, 'Acidillo, Baby John V.', 'Ea possimus digniss', 'Animi commodo quis ', 'Ipsum et officiis un', 'Nihil aut corrupti ', 'Et enim obcaecati ea', 'Magnam repellendus ', 'jaynu123', '2025-08-17 01:24:46', 'jhoonard');

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dosage` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `expiry` date NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `dosage`, `quantity`, `expiry`, `created_at`) VALUES
(4, 'Diatabs', '350mg', 0, '2031-11-17', '2025-05-17 08:24:20'),
(7, 'Neozep', '10mg', 134, '2050-05-18', '2025-05-18 01:21:57'),
(12, 'Biogesic', '500mg', 195, '2029-06-05', '2025-05-28 17:55:41'),
(26, 'Rexidol', '500mg', 285, '2029-12-31', '2025-08-10 19:19:20'),
(27, 'Bioflu', '500mg', 500, '2027-10-17', '2025-08-17 01:26:21'),
(28, 'Bioflu', '500mg', 30, '2025-10-27', '2025-08-17 01:27:30'),
(29, 'Bioflu', '500mg', 1, '2025-08-17', '2025-08-17 01:28:07'),
(30, 'Mefinamic', '500mg', 600, '2024-06-04', '2025-08-17 09:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_role` varchar(50) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `sender_name`, `sender_role`, `recipient_id`, `recipient_name`, `subject`, `message`, `is_read`, `created_at`) VALUES
(1, 6, 'Staff', 'doctor/nurse', 21, 'Abella, Joseph B.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(2, 6, 'Staff', 'doctor/nurse', 25, 'Abellana, Ariel L', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(3, 6, 'Staff', 'doctor/nurse', 22, 'Abellana, Vincent Anthony Q.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(4, 6, 'Staff', 'doctor/nurse', 23, 'Abendan, Christian James A.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(5, 6, 'Staff', 'doctor/nurse', 24, 'Abendan, Nino Rashean T.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(6, 6, 'Staff', 'doctor/nurse', 26, 'Acidillo, Baby John V.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(7, 6, 'Staff', 'doctor/nurse', 35, 'Adlawan, Ealla Marie', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:21:50'),
(8, 6, 'Staff', 'doctor/nurse', 27, 'Adona, Carl Macel C.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(9, 6, 'Staff', 'doctor/nurse', 30, 'Aguilar, Jaymar C', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:21:50'),
(10, 6, 'Staff', 'doctor/nurse', 28, 'Albiso, Creshell Mary M.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(11, 6, 'Staff', 'doctor/nurse', 29, 'Alegado, John Raymon B.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:21:50'),
(12, 6, 'Staff', 'doctor/nurse', 36, 'Alferez Jr., Bernardino S.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(13, 6, 'Staff', 'doctor/nurse', 31, 'Alicaya, Ralph Lorync D.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(14, 6, 'Staff', 'doctor/nurse', 37, 'Almendras, Alistair A', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(15, 6, 'Staff', 'doctor/nurse', 38, 'Alvarado, Dexter Q.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(16, 6, 'Staff', 'doctor/nurse', 39, 'Amarille, Kim Ryan M', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(17, 6, 'Staff', 'doctor/nurse', 40, 'Arcamo Jr., Emmanuel P.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:21:50'),
(18, 6, 'Staff', 'doctor/nurse', 32, 'Baraclan, Genesis S.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:21:50'),
(19, 6, 'Staff', 'doctor/nurse', 33, 'Base, Jev Adrian', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:21:50'),
(20, 6, 'Staff', 'doctor/nurse', 34, 'Booc, Aloysius A.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:21:50'),
(21, 6, 'Staff', 'doctor/nurse', 21, 'Abella, Joseph B.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(22, 6, 'Staff', 'doctor/nurse', 25, 'Abellana, Ariel L', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(23, 6, 'Staff', 'doctor/nurse', 22, 'Abellana, Vincent Anthony Q.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(24, 6, 'Staff', 'doctor/nurse', 23, 'Abendan, Christian James A.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(25, 6, 'Staff', 'doctor/nurse', 24, 'Abendan, Nino Rashean T.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(26, 6, 'Staff', 'doctor/nurse', 26, 'Acidillo, Baby John V.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(27, 6, 'Staff', 'doctor/nurse', 35, 'Adlawan, Ealla Marie', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:22:27'),
(28, 6, 'Staff', 'doctor/nurse', 27, 'Adona, Carl Macel C.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(29, 6, 'Staff', 'doctor/nurse', 30, 'Aguilar, Jaymar C', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:22:27'),
(30, 6, 'Staff', 'doctor/nurse', 28, 'Albiso, Creshell Mary M.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:27'),
(31, 6, 'Staff', 'doctor/nurse', 29, 'Alegado, John Raymon B.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:22:27'),
(32, 6, 'Staff', 'doctor/nurse', 36, 'Alferez Jr., Bernardino S.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:28'),
(33, 6, 'Staff', 'doctor/nurse', 31, 'Alicaya, Ralph Lorync D.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:28'),
(34, 6, 'Staff', 'doctor/nurse', 37, 'Almendras, Alistair A', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:28'),
(35, 6, 'Staff', 'doctor/nurse', 38, 'Alvarado, Dexter Q.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:28'),
(36, 6, 'Staff', 'doctor/nurse', 39, 'Amarille, Kim Ryan M', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:28'),
(37, 6, 'Staff', 'doctor/nurse', 40, 'Arcamo Jr., Emmanuel P.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:22:28'),
(38, 6, 'Staff', 'doctor/nurse', 32, 'Baraclan, Genesis S.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:22:28'),
(39, 6, 'Staff', 'doctor/nurse', 33, 'Base, Jev Adrian', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 0, '2025-08-17 07:22:28'),
(40, 6, 'Staff', 'doctor/nurse', 34, 'Booc, Aloysius A.', 'agenda', 'ksjakdkasdakskdjakdkjaksjdkjaksdkaskdj', 1, '2025-08-17 07:22:28'),
(41, 6, 'Staff', 'doctor/nurse', 21, 'Abella, Joseph B.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(42, 6, 'Staff', 'doctor/nurse', 25, 'Abellana, Ariel L', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(43, 6, 'Staff', 'doctor/nurse', 22, 'Abellana, Vincent Anthony Q.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(44, 6, 'Staff', 'doctor/nurse', 23, 'Abendan, Christian James A.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(45, 6, 'Staff', 'doctor/nurse', 24, 'Abendan, Nino Rashean T.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(46, 6, 'Staff', 'doctor/nurse', 26, 'Acidillo, Baby John V.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(47, 6, 'Staff', 'doctor/nurse', 35, 'Adlawan, Ealla Marie', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19'),
(48, 6, 'Staff', 'doctor/nurse', 27, 'Adona, Carl Macel C.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(49, 6, 'Staff', 'doctor/nurse', 30, 'Aguilar, Jaymar C', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19'),
(50, 6, 'Staff', 'doctor/nurse', 28, 'Albiso, Creshell Mary M.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(51, 6, 'Staff', 'doctor/nurse', 29, 'Alegado, John Raymon B.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19'),
(52, 6, 'Staff', 'doctor/nurse', 36, 'Alferez Jr., Bernardino S.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(53, 6, 'Staff', 'doctor/nurse', 31, 'Alicaya, Ralph Lorync D.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(54, 6, 'Staff', 'doctor/nurse', 37, 'Almendras, Alistair A', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(55, 6, 'Staff', 'doctor/nurse', 38, 'Alvarado, Dexter Q.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(56, 6, 'Staff', 'doctor/nurse', 39, 'Amarille, Kim Ryan M', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(57, 6, 'Staff', 'doctor/nurse', 40, 'Arcamo Jr., Emmanuel P.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19'),
(58, 6, 'Staff', 'doctor/nurse', 32, 'Baraclan, Genesis S.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19'),
(59, 6, 'Staff', 'doctor/nurse', 33, 'Base, Jev Adrian', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 0, '2025-08-17 07:41:19'),
(60, 6, 'Staff', 'doctor/nurse', 34, 'Booc, Aloysius A.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `student_id`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 40, 'Your appointment for 2025-05-20 17:28:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-20 06:37:40'),
(2, 40, 'Your appointment for 2025-05-20 01:18:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-20 06:38:06'),
(3, 40, 'Your appointment for 2025-05-19 22:36:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-20 06:44:25'),
(4, 40, 'Your appointment for 2025-05-20 15:10:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-20 06:48:51'),
(5, 40, 'Your appointment for 2025-05-20 19:12:00 was moved by the clinic. Please adjust your schedule to <span class=\'font-semibold text-blue-600\'>2025-05-21 19:12:00</span>.', 'appointment', 0, '2025-05-20 06:57:57'),
(6, 39, 'Your appointment for 2025-05-20 23:54:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-20 07:06:38'),
(7, 39, 'Your appointment for 2025-05-20 23:54:00 was moved by the clinic. Please adjust your schedule to <span class=\'font-semibold text-blue-600\'>2025-05-20 23:54:00</span>.', 'appointment', 0, '2025-05-20 07:43:57'),
(8, 39, 'Your appointment for 2025-05-20 13:13:00 was moved by the clinic. Please adjust your schedule to <span class=\'font-semibold text-blue-600\'>2025-05-20 13:13:00</span>.', 'appointment', 0, '2025-05-20 09:03:58'),
(9, 39, 'Your appointment for 2025-05-20 09:59:00 was moved by the clinic. Please adjust your schedule to <span class=\'font-semibold text-blue-600\'>2025-05-20 09:59:00</span>.', 'appointment', 0, '2025-05-20 09:05:26'),
(10, 39, 'Your appointment for 2025-05-20 14:06:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-20 09:10:59'),
(11, 39, 'Your appointment for Non atque aute lauda has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-20</b> at <b>07:41:00</b>.', 'appointment', 0, '2025-05-20 09:14:58'),
(12, 40, 'Your appointment for Placeat nesciunt c has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-22</b> at <b>10:08:00</b>.', 'appointment', 0, '2025-05-20 09:24:25'),
(13, 40, 'Your appointment for Et amet doloremque has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-21</b> at <b>15:04:00</b>.', 'appointment', 0, '2025-05-20 21:56:33'),
(14, 27, 'Your appointment for 2025-05-19 13:05:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-20 21:57:21'),
(15, 40, 'Your appointment for 2025-05-20 01:33:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-20 21:58:00'),
(16, 40, 'Your appointment for 2025-05-20 01:33:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-20 22:00:15'),
(17, 40, 'Your appointment for 2025-05-20 02:58:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-20 22:01:14'),
(18, 40, 'Your appointment for 2025-05-20 06:34:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-20 22:01:55'),
(19, 40, 'Your appointment for 2025-05-20 07:05:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 00:33:35'),
(20, 40, 'Your appointment for 2025-05-21 19:07:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 00:34:29'),
(21, 40, 'Your appointment for 2025-05-20 06:34:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 00:42:14'),
(22, 40, 'Your appointment for 2025-05-21 00:55:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 00:55:45'),
(23, 40, 'Your appointment for 2025-05-21 02:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 01:53:15'),
(24, 40, 'Your appointment for 2025-05-21 23:53:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 02:26:14'),
(25, 40, 'Your appointment for 2025-05-21 19:04:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 02:26:16'),
(26, 40, 'Your appointment for 2025-05-21 16:26:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-21 02:28:14'),
(27, 40, 'Your appointment for Magnam molestiae des has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-21</b> at <b>04:02:00</b>.', 'appointment', 0, '2025-05-21 05:05:16'),
(28, 21, 'Your appointment for Rerum deleniti eum q has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-21</b> at <b>22:23:00</b>.', 'appointment', 0, '2025-05-21 06:33:41'),
(29, 22, 'Your appointment for 2025-05-23 14:36:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-05-21 22:58:42'),
(30, 22, 'Your appointment for Doloremque ratione q has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-25</b> at <b>12:08:00</b>.', 'appointment', 0, '2025-05-21 23:00:05'),
(31, 22, 'Your appointment for 2025-05-21 12:08:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-22 00:04:07'),
(32, 40, 'Your appointment for katakuban ng mga tao has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-22</b> at <b>13:00:00</b>.', 'appointment', 0, '2025-05-22 02:39:58'),
(33, 40, 'Your appointment for 2025-05-22 13:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-22 02:44:10'),
(35, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-05-28</b> at <b>13:00</b> (Consequuntur archite).', 'appointment', 0, '2025-05-22 02:59:18'),
(36, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-05-21</b> at <b>08:00</b> (Accusantium adipisic).', 'appointment', 0, '2025-05-22 02:59:44'),
(37, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-05-28</b> at <b>13:00</b> (Illo reiciendis in i).', 'appointment', 0, '2025-05-22 03:14:27'),
(38, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-05-28</b> at <b>13:00</b> (Esse enim placeat ).', 'appointment', 0, '2025-05-22 03:14:46'),
(39, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-05-28</b> at <b>13:00</b> (Esse enim placeat ).', 'appointment', 0, '2025-05-22 03:25:47'),
(40, 40, 'Your appointment for 2025-05-28 13:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-22 03:41:24'),
(41, 40, 'Your appointment for 2025-05-28 13:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-05-22 03:53:59'),
(42, NULL, 'A new appointment has been booked by <b>Abendan, Christian James A.</b> for <b>2025-05-21</b> at <b>08:00</b> (Excepteur sunt expli).', 'appointment', 0, '2025-05-22 07:36:49'),
(43, NULL, 'A new appointment has been booked by <b>Abendan, Christian James A.</b> for <b>2025-05-28</b> at <b>13:00</b> (Facere commodi do ma).', 'appointment', 0, '2025-05-22 07:37:15'),
(44, NULL, 'A new appointment has been booked by <b>Abendan, Christian James A.</b> for <b>2025-05-21</b> at <b>08:00</b> (Similique itaque eni).', 'appointment', 0, '2025-05-22 07:39:25'),
(45, 23, 'Your appointment for Similique itaque eni has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-05-21</b> at <b>08:00:00</b>.', 'appointment', 0, '2025-05-22 07:39:42'),
(46, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:32:45'),
(47, 40, 'Your appointment for 2025-06-04 08:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-06-01 10:33:15'),
(48, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:33:20'),
(49, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:34:10'),
(50, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:34:21'),
(51, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:34:26'),
(52, 40, 'Your appointment for 2025-06-04 08:00:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-06-01 10:34:38'),
(53, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:35:07'),
(54, 40, 'Your appointment for 2025-06-04 08:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-06-01 10:35:16'),
(55, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:35:37'),
(56, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:35:39'),
(57, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:35:41'),
(58, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:36:58'),
(59, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:37:15'),
(60, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:37:19'),
(61, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:37:28'),
(62, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:38:58'),
(63, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Quia magna quisquam ).', 'appointment', 0, '2025-06-01 10:39:04'),
(64, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Labore itaque debiti).', 'appointment', 0, '2025-06-01 10:43:20'),
(65, 40, 'Your appointment for 2025-06-04 08:00:00 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-06-01 10:46:38'),
(66, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Labore itaque debiti).', 'appointment', 0, '2025-06-01 10:46:45'),
(67, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Labore itaque debiti).', 'appointment', 0, '2025-06-01 10:46:48'),
(68, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-04</b> at <b>08:00</b> (Facere ratione moles).', 'appointment', 0, '2025-06-01 10:48:58'),
(69, 40, 'Your appointment for Facere ratione moles has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-06-11</b> at <b>13:00</b>.', 'appointment', 0, '2025-06-01 10:50:49'),
(70, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-18</b> at <b>08:00</b> (Iste eius provident).', 'appointment', 0, '2025-06-01 10:53:38'),
(71, 40, 'Your appointment for Iste eius provident has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-06-11</b> at <b>13:00</b>.', 'appointment', 0, '2025-06-01 10:54:08'),
(72, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-11</b> at <b>13:00</b> (Fuga Elit optio n).', 'appointment', 0, '2025-06-01 11:53:27'),
(73, 40, 'Your appointment for 2025-06-11 13:00:00 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-06-01 11:53:39'),
(74, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-06-11</b> at <b>13:00</b> (Fuga Elit optio n).', 'appointment', 0, '2025-06-01 11:53:45'),
(75, 40, 'Your appointment for Fuga Elit optio n has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-06-19</b> at <b>13:00:00</b>.', 'appointment', 0, '2025-07-31 18:23:49'),
(76, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-18</b> at <b>18:17-19:18</b> (Molestias maiores cu).', 'appointment', 0, '2025-08-17 18:18:45'),
(77, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-18</b> at <b>18:17-19:18</b> (Molestias maiores cu).', 'appointment', 0, '2025-08-17 18:18:54'),
(78, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-18</b> at <b>18:17-19:18</b> (Et nulla nostrud ut ).', 'appointment', 0, '2025-08-17 18:19:31'),
(79, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-18</b> at <b>18:17-19:18</b> (Aliquip dolor in par).', 'appointment', 0, '2025-08-17 18:20:02'),
(80, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-18</b> at <b>18:17-19:18</b> (Expedita pariatur N).', 'appointment', 0, '2025-08-17 18:21:34'),
(81, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-18</b> at <b>18:17-19:18</b> (Aut dolores eligendi).', 'appointment', 0, '2025-08-17 18:25:53'),
(82, 40, 'Your appointment for 2025-08-18 18:17-19:18 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-17 18:26:11'),
(83, NULL, 'A new appointment has been booked by <b>Booc, Aloysius A.</b> for <b>2025-08-18</b> at <b>00:32-03:36</b> (Eligendi iusto aliqu).', 'appointment', 0, '2025-08-18 00:48:19'),
(84, 34, 'Your appointment for 2025-08-18 00:32-03:36 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-18 00:48:40'),
(85, NULL, 'A new appointment has been booked by <b>Adlawan, Ealla Marie</b> for <b>2025-08-20</b> at <b>01:17-01:47</b> (Non et eu ipsa natu).', 'appointment', 0, '2025-08-18 01:18:04'),
(86, NULL, 'A new appointment has been booked by <b>Adlawan, Ealla Marie</b> for <b>2025-08-20</b> at <b>01:47-02:17</b> (Animi sit irure re).', 'appointment', 0, '2025-08-18 01:18:09'),
(87, NULL, 'A new appointment has been booked by <b>Adlawan, Ealla Marie</b> for <b>2025-08-19</b> at <b>01:31-02:01</b> (Qui eligendi dolorem).', 'appointment', 0, '2025-08-18 01:32:43'),
(88, NULL, 'A new appointment has been booked by <b>Alegado, John Raymon B.</b> for <b>2025-08-19</b> at <b>02:01-02:31</b> (Eum soluta recusanda).', 'appointment', 0, '2025-08-18 01:35:16');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `token` varchar(64) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `email`, `token`, `expires_at`, `used`) VALUES
(1, 8, 'jaynujangad03@gmail.com', 'b68dce0b2b01552f8d5187dd693efb3a145e93621c0ac763bf48d0863496251d', '2025-05-18 20:33:51', 0),
(2, 8, 'jaynujangad03@gmail.com', '45887e86db9de66e72a555fa387df767f0b5b7a90a8acc3e87276076f2bc4898', '2025-05-18 20:33:53', 0),
(3, 8, 'jaynujangad03@gmail.com', '4e88e49df878cc71abcebb6b7aa3f03228e3a3bfe969ad7131f9d362548417b7', '2025-05-18 20:33:54', 0),
(4, 8, 'jaynujangad03@gmail.com', '4af804095667fb6e1eb0827e082edefc10698484bdd5539dfc93294f6e3b7412', '2025-05-18 20:33:54', 0),
(5, 8, 'jaynujangad03@gmail.com', 'dc39528256939894c46893eb8e44f985eaa012781a4c4534a376df2d56187d7a', '2025-05-18 20:33:54', 0),
(6, 8, 'jaynujangad03@gmail.com', '05952cb144b4fdd1ed7a0a6bbd1f5b4e1af67daf18a8c1a27ccfeee8f7a26a86', '2025-05-18 20:33:54', 0),
(7, 8, 'jaynujangad03@gmail.com', '5eae727002fb6bb8429eaa9d6cd55c790b7b75bc6c17e379582a63e6c357b558', '2025-05-18 20:33:54', 0),
(8, 8, 'jaynujangad03@gmail.com', 'cf044cab8177407d6765388d40c22aa865d2de4613ba3b1cf5a64e22056c745e', '2025-05-18 20:33:55', 0),
(9, 8, 'jaynujangad03@gmail.com', 'a2eae7ec7604a517d8d5e2e103d6eae7a8ef17baf48d65a0cd4642481ec563b3', '2025-05-18 20:33:56', 0),
(10, 8, 'jaynujangad03@gmail.com', 'c375c7e1764e65b2cab3e1d6e5301a9c6883e7e80cc232f17e7b67b4824ba8e2', '2025-05-18 20:33:56', 0),
(11, 8, 'jaynujangad03@gmail.com', '5dee716808517b38b8fa2ffe88db23e050eff925f912b99e3a9f17a40b3b80fe', '2025-05-18 20:33:56', 0),
(12, 8, 'jaynujangad03@gmail.com', '08633010b17969b02618ea772554c96760958eb82c7c170659009fbdda6f2091', '2025-05-18 20:33:56', 0),
(13, 8, 'jaynujangad03@gmail.com', '490f5e1dd8616537c36b4465971bd0f60af469d8666634d9a200ed233a2bfb5b', '2025-05-18 20:33:56', 0),
(14, 8, 'jaynujangad03@gmail.com', '106255bd32c7b28fe1993d182b8a825d9b10fc3a3f45a8dca965e685fcdcf9e0', '2025-05-18 20:33:57', 0),
(15, 8, 'jaynujangad03@gmail.com', 'ed7b646ff5e601ebe0fe1728b6feb7450239a294074b726868bb09f879131183', '2025-05-18 20:33:58', 0),
(16, 8, 'jaynujangad03@gmail.com', '81cac3332e6f7a06c763e7026f2685eff79912d605ce3fbfb27ec573f1873d0d', '2025-05-18 20:33:58', 0),
(17, 8, 'jaynujangad03@gmail.com', '29d933f866f94df8d952f86900ea464dda3dec91be517507a01cf6693e59cd61', '2025-05-18 20:33:58', 0),
(18, 8, 'jaynujangad03@gmail.com', '26a203871c437098a9d6701c7a67d0bfe13eb37cd489d9a35ba1558e804ef56b', '2025-05-18 20:33:58', 0),
(19, 8, 'jaynujangad03@gmail.com', '0301f7b4ee987dde4a4a5de07eb386f5922af8ca1b00c0c50c192c2e00e08001', '2025-05-18 20:33:59', 0),
(20, 8, 'jaynujangad03@gmail.com', 'b504489bc664b34a7606daf939467e2cc4f7a68327b2b121ba58c0e7892489e8', '2025-05-18 20:34:46', 0),
(21, 8, 'jaynujangad03@gmail.com', '97178842f202ea4c96e6dc32b389fc739d52d7052f0f8e07ef3d896977f075c0', '2025-05-18 20:34:46', 0),
(22, 8, 'jaynujangad03@gmail.com', '84c0484b921a18be611bd55eb3bb971a92bed88ffd746c73e0473f8cbda83a02', '2025-05-18 20:36:36', 0),
(23, 8, 'jaynujangad03@gmail.com', 'f80ea4ab88f4463e03b219e912db188ad16c159fc05bb6ea985e6c2620da660a', '2025-05-18 20:36:37', 0),
(24, 8, 'jaynujangad03@gmail.com', '6f53b1025e81ee1a4ee10009dfb9290869e94ca42dce5911febb44d2d511a46e', '2025-05-18 20:36:37', 0),
(25, 8, 'jaynujangad03@gmail.com', '9298fc1b7b26fd6bf68377aaebe883d0dd445b79aa72b57c08dc093c79c2d33b', '2025-05-18 20:36:38', 0),
(26, 8, 'jaynujangad03@gmail.com', 'ea17a1ad03fbfb73722abd4dd5d25a01b966d4c588814e2f3b6b72f7145c69b9', '2025-05-18 20:36:38', 0),
(27, 8, 'jaynujangad03@gmail.com', '44fb0f34e3f5f7232bafcf641689cc4fdaf4ac27ba0627ace0c5d4e584cb578e', '2025-05-18 20:36:38', 0),
(28, 8, 'jaynujangad03@gmail.com', 'd1b1dd51e321192988476605c0443aa3d16c6be977bc164ce9b8435b9aa27ed3', '2025-05-18 20:36:38', 0),
(29, 8, 'jaynujangad03@gmail.com', '384245c5759c2611ce52bd1c19d9cf4054693f11771685f6d0eb846c44d95190', '2025-05-18 20:36:39', 0),
(30, 8, 'jaynujangad03@gmail.com', 'f69b9d4a60ee99479ef2af76f4d4ae67f72cdee52d4f1339034f9175467f2bde', '2025-05-18 20:36:39', 0),
(31, 8, 'jaynujangad03@gmail.com', 'ac4231712a3d0935a0eb6e4c81640e557e19fa1b22f5c0758b13d688ce7f0a7a', '2025-05-18 20:36:40', 0),
(32, 8, 'jaynujangad03@gmail.com', 'bb773c22bca0eaa2b25d7bbdf98d836d703d30e429df44a1d918733d3f7aba33', '2025-05-18 20:36:40', 0),
(33, 8, 'jaynujangad03@gmail.com', 'a03302d4dc6bf4756f48cb0952a26256852c1d330106c973363e6136e0f7cebe', '2025-05-18 20:36:40', 0),
(34, 8, 'jaynujangad03@gmail.com', 'b79c2f51eb93e01ecdf68004e890c649ae590e3dd726b658454a2fd945faf364', '2025-05-18 20:36:40', 0),
(35, 8, 'jaynujangad03@gmail.com', '68f3e61925e466399b618500942e36ec27300ae2fe8e4746424a1d791ce5f15d', '2025-05-18 20:36:41', 0),
(36, 8, 'jaynujangad03@gmail.com', '741f99d02c8223acfa9193e0e3320842ea37cc84a8a19aac29af6d421feef3ee', '2025-05-18 20:36:42', 0),
(37, 8, 'jaynujangad03@gmail.com', 'db179a54d81e13e314906690975958e80c5a0a3e53f2d90275e66763cc7fb3aa', '2025-05-18 20:36:42', 0),
(38, 8, 'jaynujangad03@gmail.com', 'c0ef3255a4dd2b56c75c395288b2029b34bb279be468a2bf7902b20c24d49da0', '2025-05-18 20:36:43', 0),
(39, 8, 'jaynujangad03@gmail.com', '2f47970775239a27ecca77b652363f8e601d0c0061a1222b89f6a8bbf162e2f4', '2025-05-18 20:36:44', 0),
(40, 8, 'jaynujangad03@gmail.com', '6775695e771563b513087429822eb6eca0548b80b3a2bfb194648baa3b16deaa', '2025-05-18 20:36:44', 0),
(41, 8, 'jaynujangad03@gmail.com', 'bac02050d152150941b680e8a862997870439aece8d0220a5153967153bb7c95', '2025-05-18 20:42:12', 0),
(42, 8, 'jaynujangad03@gmail.com', '5cda39fea5bf7ce29facb0a433bef38950e46833739a6fbe5c706716e23883e5', '2025-05-18 20:42:12', 0),
(43, 8, 'jaynujangad03@gmail.com', '583ed3393517af62de33d6eba073a4db62dbfb0bf974864f4d47352aa2417272', '2025-05-18 20:42:13', 0),
(44, 8, 'jaynujangad03@gmail.com', '72f2e93c41554653a6e1d4553220090982c79ffc3d44c65478dba3b7d4ea52b6', '2025-05-18 20:42:33', 0),
(45, 8, 'jaynujangad03@gmail.com', '9390fb8afdedf7cbb965cbbf336a137a13340f7e733f5c06289470cd090bf4eb', '2025-05-18 20:42:33', 0),
(46, 8, 'jaynujangad03@gmail.com', '0cda9b00e26780589b217c29117e981be1428324750e49cdd9f9495e3a271698', '2025-05-18 20:45:14', 0),
(47, 8, 'jaynujangad03@gmail.com', '4169bd450777973927c7701e62088b970c07c8881c365f36cd0332a5c3d92687', '2025-05-18 20:45:15', 0),
(48, 8, 'jaynujangad03@gmail.com', 'b0654cdb60a2637158f064c85504b3a1f9992d4d819450e01ee49a6537d9c17c', '2025-05-18 20:45:15', 0),
(49, 8, 'jaynujangad03@gmail.com', '3f74101b6a5fa5412d67d1f1b30998646b5680ffa13dbcfff5bbf3cf2672651a', '2025-05-18 20:45:16', 0),
(50, 8, 'jaynujangad03@gmail.com', 'b46f5e70b9f28c68f469f274cfbd4b7a167721775a8c8ffcdffd417d769db123', '2025-05-18 20:45:16', 0),
(51, 8, 'jaynujangad03@gmail.com', '9a32ba5a3e840a7a52e9d8861f473546fe04b52a680c934bcb07eb41573e2d40', '2025-05-18 20:45:16', 0),
(52, 8, 'jaynujangad03@gmail.com', '50977b8450f14725ad17feee19bfe96399ea59dc247ba92d98503fa41ac895cd', '2025-05-18 20:45:16', 0),
(53, 8, 'jaynujangad03@gmail.com', '49a6cd982c6b616f725259dd9e65fe19a0bdc7fd7137f975d4ed5a2a24134ea5', '2025-05-18 20:45:17', 0),
(54, 8, 'jaynujangad03@gmail.com', '30aa01428ba10a0a04a494c04c2a6c2232a5127a9492d02f1d968fa488b9e826', '2025-05-18 20:45:17', 0),
(55, 8, 'jaynujangad03@gmail.com', '36ef9a6f2bb4f4595dd65603e6b6760bf879a6ecd4c79134fbfb94d2403863bf', '2025-05-18 20:45:18', 0),
(56, 8, 'jaynujangad03@gmail.com', '1c2993880eb782882f801634d3f703070da08bb7c3c27460c417fd817d132eb1', '2025-05-18 20:45:18', 0),
(57, 8, 'jaynujangad03@gmail.com', 'b5c2415e629601e0f08396983aac39d13d49af66a5c71b0ce89f987132966592', '2025-05-18 20:45:18', 0),
(58, 8, 'jaynujangad03@gmail.com', '45c8e53e66250b408d546b227d564b953bd94a57c9c3ee894d5c71acb30160b6', '2025-05-18 20:45:18', 0),
(59, 8, 'jaynujangad03@gmail.com', '115f659e255b5a2512720ae41415da1b9abb377cd6e15c1aff941b5ac2dcd79b', '2025-05-18 20:45:19', 0),
(60, 8, 'jaynujangad03@gmail.com', 'd51fd70d6df396c2b2b8e731b8a359eda505dcac29eb697fd7c506c23ede8b8f', '2025-05-18 20:45:19', 0),
(61, 8, 'jaynujangad03@gmail.com', 'c9624fa6a43ef34877a45909f53e0e4a5864c34dd2361f77aea00ba7d5864226', '2025-05-18 20:45:47', 0),
(62, 8, 'jaynujangad03@gmail.com', 'd4e17119c64fb55a45c73d81d8cf54038943684a67d3f81d67ca6366ec6549c0', '2025-05-18 20:45:48', 0),
(63, 8, 'jaynujangad03@gmail.com', 'b211be78e41221a65b3f7fa385ff8739778aa1ea3017a2cedb6c0faba1689c04', '2025-05-18 20:45:49', 0),
(64, 8, 'jaynujangad03@gmail.com', '748817edce26be3502113f908246cbd20e94af8b67a036aa94ae99c2bc10ebb6', '2025-05-18 20:45:49', 0),
(65, 8, 'jaynujangad03@gmail.com', '24f07ff3df4392aa6f25c7723b9cd4063f50af3d9dfcfc88507ef4b85ecb12d9', '2025-05-18 20:45:49', 0),
(66, 8, 'jaynujangad03@gmail.com', '1d89580eb1321cb202b6e489ad1b3e737ec3d6736cf85b3a910f240172c6f0f9', '2025-05-18 20:45:54', 0),
(67, 8, 'jaynujangad03@gmail.com', 'c1bfc73479245237c81bb5b1e9ff122c7d67698ab1de24cb575bb128cb0c070c', '2025-05-18 20:45:55', 0),
(68, 8, 'jaynujangad03@gmail.com', 'c6d049ae24d086d499336a69fcb712b0894e7ffea272f0ff19054982d68b6d93', '2025-05-18 20:45:56', 0),
(69, 8, 'jaynujangad03@gmail.com', '595f32076bf5cf2d8dd33718e2f4704ccc8f3f78fe4391509e4d230fd1c6cc1f', '2025-05-18 20:45:57', 0),
(70, 8, 'jaynujangad03@gmail.com', '471a85c5cbb47b696833e3b1a410d4ca4d18a7a0c2a8296004c64722b5b45a29', '2025-05-18 20:45:57', 0),
(71, 8, 'jaynujangad03@gmail.com', 'dc0e2be3b066c44a6b83691807136fc23416d25ca75e4094c20e8ac5d925254d', '2025-05-18 20:48:40', 0),
(72, 8, 'jaynujangad03@gmail.com', '2ee561447f12f28c00cb2739a946798bf453c25ef9f968dc0e36bcb8e066d37f', '2025-05-18 20:48:41', 0),
(73, 8, 'jaynujangad03@gmail.com', '51c64e652c3a540946a51c7e703f482b978651040ec44cb0f9bc6529b46315cc', '2025-05-18 20:48:41', 0),
(74, 8, 'jaynujangad03@gmail.com', '5708a17c6c5010034b488cb376a286350c1fb66a8f9c68f1781e056601683e91', '2025-05-18 20:48:41', 0),
(75, 8, 'jaynujangad03@gmail.com', 'b68ac50b6d560d4cce2b8aafd932e54975ded50d5950c41a548abcc771418d37', '2025-05-18 20:48:41', 0),
(76, 8, 'jaynujangad03@gmail.com', '8a4f10c89d0b3993ec23a1e1c5c8287101900b4758b843461c401ed7eab0d60c', '2025-05-18 20:48:42', 0),
(77, 8, 'jaynujangad03@gmail.com', 'dbb59b6fa2ee080c66e0c30db28096ff264ba61858fd124a0f0660c904f24719', '2025-05-18 20:48:42', 0),
(78, 8, 'jaynujangad03@gmail.com', 'd2a07f735802d1aa06b7400ed13aeea6996a61389ae15f3da1a76eb15d713f50', '2025-05-18 20:48:43', 0),
(79, 8, 'jaynujangad03@gmail.com', '3d47f282e1a9fd1b2bbefd498b244d0beb190c9ed4a874c4aad7b621cf36a07b', '2025-05-18 20:48:43', 0),
(80, 8, 'jaynujangad03@gmail.com', '1cd4da83dc36b6c5f64aa45f9a5fd91bbcddf9259572da42a73eec54a4b44aa2', '2025-05-18 20:48:43', 0),
(81, 8, 'jaynujangad03@gmail.com', '1fd240b24e582c7d326b36726ea857be98aeb268ecec889a99f78b1ec56cda4c', '2025-05-18 20:48:43', 0),
(82, 8, 'jaynujangad03@gmail.com', 'ebfc5351a0416f67eea943fba7a65cce2eb5aaed65c2783b32b2f1b2ee582b49', '2025-05-18 20:48:44', 0),
(83, 8, 'jaynujangad03@gmail.com', '6e06895eeade7d9cb150794ef0fcdbbbb61fd6a70df995f46f233383ccd8cd68', '2025-05-18 20:48:44', 0),
(84, 8, 'jaynujangad03@gmail.com', 'dd7069dd33478e1f07fb20f9bba57b3dfa7d6099275e3b7513a42b362febad62', '2025-05-18 22:15:56', 0),
(85, 8, 'jaynujangad03@gmail.com', '1651f55a223dc98728dd3dc32558f697e12176c6347ba9502788e23b089cb0af', '2025-05-18 22:15:56', 0),
(86, 8, 'jaynujangad03@gmail.com', 'd303d9f5c4440d943de8acd2f65aa891f08b8614b0642e805cc40223744119d3', '2025-05-18 22:15:57', 0),
(87, 8, 'jaynujangad03@gmail.com', '080c4d936e8040aa2e4f44b356ec0ffdcb0bdfc8d53c140bc9988e6dbdedaf5a', '2025-05-18 22:15:57', 0),
(88, 8, 'jaynujangad03@gmail.com', '9007a009051c54de791162316cfe445dd31679f98b8ca3bb47a4fe387bc0a334', '2025-05-18 22:15:58', 0),
(89, 8, 'jaynujangad03@gmail.com', 'da01be76bfe467698ed4ecae31c670e023eb506aace5102e887a1d9e55dee049', '2025-05-18 22:15:58', 0),
(90, 8, 'jaynujangad03@gmail.com', '3fed7c4b24321ecf15bf9a27b1a1894251eb8a18a06920950933ae9544cea971', '2025-05-18 22:15:58', 0),
(91, 8, 'jaynujangad03@gmail.com', '00ef796f08454f3fab612a600edc7493b9af11e41e0fe145be1053166730291b', '2025-05-18 22:15:58', 0),
(92, 8, 'jaynujangad03@gmail.com', '449944897493388a97f83d8ee4247c7ef8c6ff621e5e68a3be139c6f5a58b010', '2025-05-18 22:15:59', 0),
(93, 8, 'jaynujangad03@gmail.com', '21254ace4c9f0fb737cb9d28e09d9354e01451d08f7ca6e312f15a770c4f74b1', '2025-05-20 01:20:32', 0),
(94, 8, 'jaynujangad03@gmail.com', '8bc55aa0340c42e15ccfffe8cd880267d06d7e8732af43b44bed9960e6e7d8e3', '2025-05-20 01:20:35', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pending_prescriptions`
--

CREATE TABLE `pending_prescriptions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `patient_email` varchar(255) DEFAULT NULL,
  `parent_email` varchar(255) DEFAULT NULL,
  `prescribed_by` varchar(255) DEFAULT NULL,
  `prescription_date` datetime DEFAULT current_timestamp(),
  `medicines` text DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_prescriptions`
--

INSERT INTO `pending_prescriptions` (`id`, `patient_id`, `patient_name`, `patient_email`, `parent_email`, `prescribed_by`, `prescription_date`, `medicines`, `reason`, `notes`) VALUES
(66, 21, 'Abella, Joseph B.', 'sawowadi@mailinator.com', 'bumimu@mailinator.com', 'Staff', '2025-06-01 12:41:11', '[{\"medicine\":\"diatabs\",\"dosage\":\"A illum aute pariat\",\"quantity\":\"78\",\"frequency\":\"Suscipit quia omnis \",\"instructions\":\"Aliquam ratione labo\"}]', NULL, 'Odio fugiat natus q'),
(67, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', 'Staff', '2025-06-01 12:45:34', '[]', NULL, 'just sleep'),
(68, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', 'Staff', '2025-06-01 12:45:35', '[]', NULL, 'just sleep'),
(69, 21, 'Abella, Joseph B.', 'xywuter@mailinator.com', 'kizoxyga@mailinator.com', 'Staff', '2025-06-01 12:45:56', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Dolorem quis volupta\",\"quantity\":\"1\",\"frequency\":\"Neque aliqua Id qua\",\"instructions\":\"Atque nostrud volupt\"}]', NULL, 'Velit do sit minima ');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `patient_email` varchar(255) DEFAULT NULL,
  `parent_email` varchar(255) DEFAULT NULL,
  `prescribed_by` varchar(255) DEFAULT NULL,
  `prescription_date` datetime DEFAULT current_timestamp(),
  `medicines` text DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_id`, `patient_name`, `patient_email`, `parent_email`, `prescribed_by`, `prescription_date`, `medicines`, `reason`, `notes`) VALUES
(3, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-17 06:43:11', '[{\"medicine\":\"rexidol\",\"dosage\":\"Voluptate consequatu\",\"quantity\":\"2\",\"frequency\":\"Facilis commodo ea m\",\"instructions\":\"Excepturi officiis e\"}]', NULL, 'Nostrum sunt reprehe'),
(4, 22, 'Abellana, Vincent Anthony Q.', NULL, NULL, 'Staff', '2025-05-17 07:48:15', '[{\"medicine\":\"rexidol\",\"dosage\":\"Eos quisquam ration\",\"quantity\":\"96\",\"frequency\":\"Et voluptate aute mo\",\"instructions\":\"Quae mollitia eum it\"}]', NULL, 'Cupidatat et ea amet'),
(5, 23, 'Abendan, Christian James A.', NULL, NULL, 'Staff', '2025-05-17 07:57:11', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Fugiat porro et qui\",\"quantity\":\"73\",\"frequency\":\"Officiis qui dolores\",\"instructions\":\"Voluptas et itaque e\"}]', NULL, 'Eaque quos officiis '),
(6, 26, 'Acidillo, Baby John V.', NULL, NULL, 'Staff', '2025-05-17 08:21:01', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Qui iste animi volu\",\"quantity\":\"20\",\"frequency\":\"Est esse cupiditat\",\"instructions\":\"Itaque sed vel incid\"}]', NULL, 'Et vel quia vitae pr'),
(7, 26, 'Acidillo, Baby John V.', NULL, NULL, 'Staff', '2025-05-18 01:11:11', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Eligendi quas cumque\",\"quantity\":\"6\",\"frequency\":\"Ut exercitation plac\",\"instructions\":\"Exercitation fugit \"}]', NULL, 'Assumenda nobis qui '),
(8, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-18 01:21:14', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Numquam non nesciunt\",\"quantity\":\"83\",\"frequency\":\"Aut qui nostrud aut \",\"instructions\":\"Architecto dolores n\"}]', NULL, 'Quia et omnis soluta'),
(9, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-18 20:13:42', '[{\"medicine\":\"Dolor eiusmod quidem\",\"dosage\":\"Consequatur culpa f\",\"quantity\":\"28\",\"frequency\":\"Illo fugiat accusam\",\"instructions\":\"Dicta nihil labore a\"}]', NULL, 'Necessitatibus aliqu'),
(10, 25, 'Abellana, Ariel L', NULL, NULL, 'Staff', '2025-05-19 02:17:16', '[{\"medicine\":\"mefinamic\",\"dosage\":\"Nihil sunt ut offic\",\"quantity\":\"84\",\"frequency\":\"Officiis quia asperi\",\"instructions\":\"Voluptatem aut enim\"}]', NULL, 'Quis adipisci eu bea'),
(11, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-19 03:16:33', '[{\"medicine\":\"Biogesics\",\"dosage\":\"Unde sit recusandae\",\"quantity\":\"2\",\"frequency\":\"Labore veniam iusto\",\"instructions\":\"Ex duis autem accusa\"}]', NULL, 'Ducimus aut sint pe'),
(12, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-19 21:30:46', '[{\"medicine\":\"rexidol\",\"dosage\":\"Non adipisci blandit\",\"quantity\":\"91\",\"frequency\":\"Nulla aliquip fuga \",\"instructions\":\"In eveniet accusant\"}]', NULL, 'Voluptatem ea sunt '),
(13, 23, 'Abendan, Christian James A.', NULL, NULL, 'Staff', '2025-05-19 22:11:18', '[{\"medicine\":\"Consectetur fugiat\",\"dosage\":\"Ea dolores qui autem\",\"quantity\":\"89\",\"frequency\":\"fgf\",\"instructions\":\"\"}]', NULL, 'Voluptatibus id labo'),
(14, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 02:16:52', '[{\"medicine\":\"mefinamic\",\"dosage\":\"Atque iusto Nam dese\",\"quantity\":\"14\",\"frequency\":\"Voluptas deserunt di\",\"instructions\":\"Voluptas ut expedita\"}]', NULL, 'Rerum enim sint aut '),
(15, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, 'Staff', '2025-05-20 22:06:41', '[{\"medicine\":\"Biogesics\",\"dosage\":\"500mg\",\"quantity\":\"19\",\"frequency\":\"3 times a day\",\"instructions\":\"After Meals\"}]', NULL, 'Ay sig ginahig ulo'),
(16, 36, 'Alferez Jr., Bernardino S.', NULL, NULL, 'Staff', '2025-05-20 23:27:28', '[{\"medicine\":\"Alaxan\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Diatabs\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(17, 36, 'Alferez Jr., Bernardino S.', NULL, NULL, 'Staff', '2025-05-20 23:27:28', '[{\"medicine\":\"Alaxan\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Diatabs\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(18, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 23:27:56', '[{\"medicine\":\"Alaxan\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(19, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 23:27:56', '[{\"medicine\":\"Alaxan\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(20, 30, 'Aguilar, Jaymar C', NULL, NULL, 'Staff', '2025-05-20 23:28:07', '[{\"medicine\":\"rexidol\",\"dosage\":\"Ut dolor aut non nul\",\"quantity\":\"1\",\"frequency\":\"Facere ipsum autem \",\"instructions\":\"Odio ullam nihil qua\"},{\"medicine\":\"Laurel Dejesus\",\"dosage\":\"Est mollit eos esse\",\"quantity\":\"1\",\"frequency\":\"Ut in maiores ad fug\",\"instructions\":\"Maxime fugit accusa\"}]', NULL, 'Recusandae Odio et '),
(21, 30, 'Aguilar, Jaymar C', NULL, NULL, 'Staff', '2025-05-20 23:28:07', '[{\"medicine\":\"rexidol\",\"dosage\":\"Ut dolor aut non nul\",\"quantity\":\"1\",\"frequency\":\"Facere ipsum autem \",\"instructions\":\"Odio ullam nihil qua\"},{\"medicine\":\"Laurel Dejesus\",\"dosage\":\"Est mollit eos esse\",\"quantity\":\"1\",\"frequency\":\"Ut in maiores ad fug\",\"instructions\":\"Maxime fugit accusa\"}]', NULL, 'Recusandae Odio et '),
(22, 29, 'Alegado, John Raymon B.', NULL, NULL, 'Staff', '2025-05-20 23:28:56', '[{\"medicine\":\"mefinamic\",\"dosage\":\"Id libero quia fuga\",\"quantity\":\"1\",\"frequency\":\"Hic est reiciendis v\",\"instructions\":\"Consectetur nulla m\"},{\"medicine\":\"Laurel Dejesus\",\"dosage\":\"Facere omnis velit \",\"quantity\":\"1\",\"frequency\":\"Qui perspiciatis pe\",\"instructions\":\"Qui aut quas dolor q\"}]', NULL, 'Animi non voluptate'),
(23, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 23:29:05', '[{\"medicine\":\"rexidol\",\"dosage\":\"Id sed qui itaque it\",\"quantity\":\"1\",\"frequency\":\"Eiusmod temporibus e\",\"instructions\":\"Omnis deleniti offic\"},{\"medicine\":\"Biogesics\",\"dosage\":\"Aliquam omnis enim a\",\"quantity\":\"1\",\"frequency\":\"Accusantium laboris \",\"instructions\":\"Hic sit cupidatat ve\"}]', NULL, 'Voluptatem consequat'),
(24, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, 'Staff', '2025-05-20 23:30:02', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Sequi illum rerum a\",\"instructions\":\"Voluptatem accusanti\"},{\"medicine\":\"Rexidol\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Consequat Omnis vol\",\"instructions\":\"Fugit consectetur \"},{\"medicine\":\"Diatabs\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Tempore voluptatibu\",\"instructions\":\"Illo et eos qui sunt\"}]', NULL, 'Adipisci esse do imp'),
(25, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, 'Staff', '2025-05-20 23:30:02', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Sequi illum rerum a\",\"instructions\":\"Voluptatem accusanti\"},{\"medicine\":\"Rexidol\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Consequat Omnis vol\",\"instructions\":\"Fugit consectetur \"},{\"medicine\":\"Diatabs\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Tempore voluptatibu\",\"instructions\":\"Illo et eos qui sunt\"}]', NULL, 'Adipisci esse do imp'),
(26, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, 'Staff', '2025-05-20 23:30:08', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Sequi illum rerum a\",\"instructions\":\"Voluptatem accusanti\"},{\"medicine\":\"Rexidol\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Consequat Omnis vol\",\"instructions\":\"Fugit consectetur \"},{\"medicine\":\"Diatabs\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Tempore voluptatibu\",\"instructions\":\"Illo et eos qui sunt\"}]', NULL, 'Adipisci esse do imp'),
(27, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 23:30:17', '[{\"medicine\":\"Fugit voluptate bea\",\"dosage\":\"Magni consectetur al\",\"quantity\":\"6\",\"frequency\":\"Adipisicing quidem i\",\"instructions\":\"Accusamus itaque dol\"}]', NULL, 'Nemo minus voluptate'),
(28, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 23:30:17', '[{\"medicine\":\"Fugit voluptate bea\",\"dosage\":\"Magni consectetur al\",\"quantity\":\"6\",\"frequency\":\"Adipisicing quidem i\",\"instructions\":\"Accusamus itaque dol\"}]', NULL, 'Nemo minus voluptate'),
(29, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-20 23:30:21', '[{\"medicine\":\"rexidol\",\"dosage\":\"Possimus rerum rati\",\"quantity\":\"11\",\"frequency\":\"Non in quis quia arc\",\"instructions\":\"Voluptatem vero cons\"}]', NULL, 'Laborum vero natus a'),
(30, 30, 'Aguilar, Jaymar C', NULL, NULL, 'Staff', '2025-05-21 00:12:51', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(31, 30, 'Aguilar, Jaymar C', NULL, NULL, 'Staff', '2025-05-21 00:12:51', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(32, 30, 'Aguilar, Jaymar C', NULL, NULL, 'Staff', '2025-05-21 00:13:19', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(33, 30, 'Aguilar, Jaymar C', NULL, NULL, 'Staff', '2025-05-21 00:13:19', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(34, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 00:13:27', '[{\"medicine\":\"Neozep\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Ullam earum omnis in\",\"instructions\":\"Laborum Aperiam eaq\"}]', NULL, 'Et sit ad aut alias'),
(35, 24, 'Abendan, Nino Rashean T.', NULL, NULL, 'Staff', '2025-05-21 00:16:48', '[{\"medicine\":\"rexidol\",\"dosage\":\"Neque vero aut sed n\",\"quantity\":\"41\",\"frequency\":\"Magni nostrum ex qui\",\"instructions\":\"Dignissimos illo obc\"}]', NULL, 'Aut ex facilis velit'),
(36, 24, 'Abendan, Nino Rashean T.', NULL, NULL, 'Staff', '2025-05-21 00:16:48', '[{\"medicine\":\"rexidol\",\"dosage\":\"Neque vero aut sed n\",\"quantity\":\"41\",\"frequency\":\"Magni nostrum ex qui\",\"instructions\":\"Dignissimos illo obc\"}]', NULL, 'Aut ex facilis velit'),
(37, 24, 'Abendan, Nino Rashean T.', NULL, NULL, 'Staff', '2025-05-21 00:17:17', '[{\"medicine\":\"mefinamic\",\"dosage\":\"In aliqua Tempore \",\"quantity\":\"63\",\"frequency\":\"Cillum culpa dolor \",\"instructions\":\"Consequuntur dolorum\"}]', NULL, 'Laboriosam Nam ut a'),
(38, 24, 'Abendan, Nino Rashean T.', NULL, NULL, 'Staff', '2025-05-21 00:17:17', '[{\"medicine\":\"mefinamic\",\"dosage\":\"In aliqua Tempore \",\"quantity\":\"63\",\"frequency\":\"Cillum culpa dolor \",\"instructions\":\"Consequuntur dolorum\"}]', NULL, 'Laboriosam Nam ut a'),
(39, 27, 'Adona, Carl Macel C.', NULL, NULL, 'Staff', '2025-05-21 00:19:05', '[{\"medicine\":\"rexidol\",\"dosage\":\"Sit esse sunt qui i\",\"quantity\":\"64\",\"frequency\":\"Excepteur autem illo\",\"instructions\":\"Assumenda mollit deb\"}]', NULL, 'Vel provident atque'),
(40, 27, 'Adona, Carl Macel C.', NULL, NULL, 'Staff', '2025-05-21 00:19:06', '[{\"medicine\":\"rexidol\",\"dosage\":\"Sit esse sunt qui i\",\"quantity\":\"64\",\"frequency\":\"Excepteur autem illo\",\"instructions\":\"Assumenda mollit deb\"}]', NULL, 'Vel provident atque'),
(41, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 00:19:10', '[{\"medicine\":\"Fugit voluptate bea\",\"dosage\":\"Vel delectus volupt\",\"quantity\":\"57\",\"frequency\":\"Modi et tempor sit \",\"instructions\":\"Consequatur consequa\"}]', NULL, 'Magni nemo autem com'),
(42, 24, 'Abendan, Nino Rashean T.', NULL, NULL, 'Staff', '2025-05-21 00:20:13', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Unde recusandae Ut \",\"quantity\":\"1\",\"frequency\":\"Eius dolorem anim re\",\"instructions\":\"Unde omnis ut sed od\"}]', NULL, 'Natus quo deserunt a'),
(43, 24, 'Abendan, Nino Rashean T.', NULL, NULL, 'Staff', '2025-05-21 00:20:13', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Unde recusandae Ut \",\"quantity\":\"1\",\"frequency\":\"Eius dolorem anim re\",\"instructions\":\"Unde omnis ut sed od\"}]', NULL, 'Natus quo deserunt a'),
(44, 23, 'Abendan, Christian James A.', NULL, NULL, 'Staff', '2025-05-21 00:20:59', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Dolor voluptatem Sa\",\"quantity\":\"1\",\"frequency\":\"Aut nisi reiciendis \",\"instructions\":\"Enim ipsum consectet\"}]', NULL, 'Et dolorum nisi aut '),
(45, 22, 'Abellana, Vincent Anthony Q.', NULL, NULL, 'Staff', '2025-05-21 00:21:25', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consectetur pariatur\",\"quantity\":\"1\",\"frequency\":\"Eos iure voluptatem\",\"instructions\":\"Consequat Sapiente \"}]', NULL, 'Accusamus irure ipsu'),
(46, 22, 'Abellana, Vincent Anthony Q.', NULL, NULL, 'Staff', '2025-05-21 00:21:25', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consectetur pariatur\",\"quantity\":\"1\",\"frequency\":\"Eos iure voluptatem\",\"instructions\":\"Consequat Sapiente \"}]', NULL, 'Accusamus irure ipsu'),
(47, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 00:23:24', '[{\"medicine\":\"Neozep\",\"dosage\":\"Placeat sint irure \",\"quantity\":\"1\",\"frequency\":\"Placeat obcaecati r\",\"instructions\":\"Pariatur Totam eum \"}]', NULL, 'Vel ducimus molliti'),
(48, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 00:23:24', '[{\"medicine\":\"Neozep\",\"dosage\":\"Placeat sint irure \",\"quantity\":\"1\",\"frequency\":\"Placeat obcaecati r\",\"instructions\":\"Pariatur Totam eum \"}]', NULL, 'Vel ducimus molliti'),
(49, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 00:32:16', '[{\"medicine\":\"Neozep\",\"dosage\":\"Numquam earum labore\",\"quantity\":\"1\",\"frequency\":\"Est quia dolor accus\",\"instructions\":\"Quia vel aut nostrum\"}]', NULL, 'Dolorum natus dolori'),
(50, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 00:37:17', '[{\"medicine\":\"Alaxan\",\"dosage\":\"Sunt illum sed duc\",\"quantity\":\"1\",\"frequency\":\"Velit officiis duis\",\"instructions\":\"Commodo nesciunt pe\"}]', NULL, 'Ut neque impedit et'),
(51, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 05:35:49', '[{\"medicine\":\"Neozep\",\"dosage\":\"Laborum Ipsam est \",\"quantity\":\"62\",\"frequency\":\"Totam vero et et nec\",\"instructions\":\"Obcaecati non doloru\"}]', NULL, 'Placeat distinctio'),
(52, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 05:35:53', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Aut eaque incidunt \",\"quantity\":\"45\",\"frequency\":\"Ullam voluptates tem\",\"instructions\":\"Ex ea laborum cumque\"}]', NULL, 'Dolor irure reprehen'),
(53, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 05:36:04', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Voluptates ea quas s\",\"quantity\":\"55\",\"frequency\":\"Quas eveniet obcaec\",\"instructions\":\"Consequatur Aliquip\"}]', NULL, 'Ex esse illo beatae'),
(54, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:05:18', '[{\"medicine\":\"Alaxan\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', NULL, 'asd'),
(55, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:05:51', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Consequuntur dolore \",\"quantity\":\"1\",\"frequency\":\"Consectetur pariatur\",\"instructions\":\"Laborum Est tempora\"}]', NULL, 'Do tempore ut minus'),
(56, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:06:18', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Voluptas pariatur Q\",\"quantity\":\"1\",\"frequency\":\"Est ut sed dolore a \",\"instructions\":\"Odit sunt voluptate\"}]', NULL, 'Quos non quis ad dol'),
(57, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:08:31', '[{\"medicine\":\"Neozep\",\"dosage\":\"Delectus quo cum si\",\"quantity\":\"1\",\"frequency\":\"Odit commodi provide\",\"instructions\":\"Omnis irure maiores \"}]', NULL, 'Maiores voluptatem e'),
(58, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:09:11', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Eos quas qui aut in\",\"quantity\":\"1\",\"frequency\":\"Quia eos elit pari\",\"instructions\":\"Anim iusto ducimus \"}]', NULL, 'Consectetur dolorum '),
(59, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:09:14', '[{\"medicine\":\"Neozep\",\"dosage\":\"Labore officia ut su\",\"quantity\":\"1\",\"frequency\":\"Tempor consequatur \",\"instructions\":\"Aut enim commodo qui\"}]', NULL, 'Illum ullam et fuga'),
(60, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', 'Staff', '2025-05-21 06:12:10', '[{\"medicine\":\"Neozep\",\"dosage\":\"Labore officia ut su\",\"quantity\":\"1\",\"frequency\":\"Tempor consequatur \",\"instructions\":\"Aut enim commodo qui\"}]', NULL, 'Illum ullam et fuga'),
(61, 22, 'Abellana, Vincent Anthony Q.', 'sellonmeow@gmail.com', 'kimxyzian@gmail.com', 'Staff', '2025-05-21 06:24:53', '[{\"medicine\":\"Neozep\",\"dosage\":\"Officiis quo reprehe\",\"quantity\":\"1\",\"frequency\":\"Aut sit nostrud cons\",\"instructions\":\"Possimus minim ab o\"}]', NULL, 'Fugiat eaque aspern'),
(62, 22, 'Abellana, Vincent Anthony Q.', 'sellonmeow@gmail.com', 'kimxyzian@gmail.com', 'Staff', '2025-05-21 06:25:15', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Officiis dolor repre\",\"quantity\":\"9\",\"frequency\":\"Adipisci sed qui qua\",\"instructions\":\"Ut voluptas cupidita\"}]', NULL, 'Facere odio qui poss'),
(63, 21, 'Abella, Joseph B.', NULL, NULL, 'Staff', '2025-05-21 22:53:53', '[{\"medicine\":\"Neozep\",\"dosage\":\"Itaque dignissimos d\",\"quantity\":\"15\",\"frequency\":\"Qui sit assumenda co\",\"instructions\":\"Adipisci perferendis\"}]', NULL, 'Sed ducimus maiores'),
(64, 21, 'Abella, Joseph B.', 'waryku@mailinator.com', 'wefyzineha@mailinator.com', 'Staff', '2025-05-22 00:47:47', '[{\"medicine\":\"Neozep\",\"dosage\":\"Sed accusantium qui \",\"quantity\":\"57\",\"frequency\":\"Mollit praesentium q\",\"instructions\":\"Et non voluptas volu\"}]', NULL, 'Quo magna explicabo'),
(65, 22, 'Abellana, Vincent Anthony Q.', 'sellonmeow@gmail.com', 'kimxyzian@gmail.com', 'Staff', '2025-05-22 00:48:02', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Dolor deserunt obcae\",\"quantity\":\"9\",\"frequency\":\"Ullamco ex voluptate\",\"instructions\":\"Hic consequuntur qui\"}]', NULL, 'Doloremque et mollit'),
(66, 26, 'Acidillo, Baby John V.', 'dybepedehy@mailinator.com', 'jijiplays991@mailinator.com', 'Staff', '2025-05-22 01:28:54', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Quas illo corrupti \",\"quantity\":\"65\",\"frequency\":\"Doloremque cupidatat\",\"instructions\":\"Sed enim sed laudant\"}]', NULL, 'Voluptatem magnam e'),
(67, 26, 'Acidillo, Baby John V.', 'dumuqexe@mailinator.com', 'jijiplays991@mailinator.com', 'Staff', '2025-05-22 01:28:55', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Vero totam nihil occ\",\"quantity\":\"60\",\"frequency\":\"Velit dolore dolorib\",\"instructions\":\"Ex quis exercitation\"}]', NULL, 'Quis adipisicing vol'),
(68, 26, 'Acidillo, Baby John V.', 'decyj@mailinator.com', 'jijiplays991@mailinator.com', 'Staff', '2025-05-22 01:28:55', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Earum voluptate vel \",\"quantity\":\"59\",\"frequency\":\"Molestias reprehende\",\"instructions\":\"Reprehenderit esse\"}]', NULL, 'Delectus qui est no'),
(69, 21, 'Abella, Joseph B.', 'kavob@mailinator.com', 'nygyqu@mailinator.com', 'Staff', '2025-05-22 01:29:17', '[{\"medicine\":\"Atay\",\"dosage\":\"Incidunt adipisci e\",\"quantity\":\"57\",\"frequency\":\"Officia voluptas aut\",\"instructions\":\"Deleniti occaecat re\"}]', NULL, 'In eiusmod explicabo'),
(70, 30, 'Aguilar, Jaymar C', 'hujinymeci@mailinator.com', 'jijiplays991@gmail.com', 'Staff', '2025-05-22 01:31:37', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Aperiam fugiat inven\",\"quantity\":\"59\",\"frequency\":\"Totam animi cupidat\",\"instructions\":\"Culpa sint sunt aut\"}]', NULL, 'Amet sapiente tempo'),
(71, 30, 'Aguilar, Jaymar C', 'vyqysykote@mailinator.com', 'jijiplays991@gmail.com', 'Staff', '2025-05-22 01:31:40', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Rerum molestiae qui \",\"quantity\":\"86\",\"frequency\":\"Proident est assume\",\"instructions\":\"Aspernatur irure sed\"}]', NULL, 'Qui qui sunt et sap'),
(72, 30, 'Aguilar, Jaymar C', 'jasury@mailinator.com', 'jijiplays991@gmail.com', 'Staff', '2025-05-22 01:31:42', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Commodo aut necessit\",\"quantity\":\"34\",\"frequency\":\"Earum sunt minus nih\",\"instructions\":\"Ea consequatur Veli\"}]', NULL, 'Tempore vitae modi '),
(73, 40, 'Arcamo Jr., Emmanuel P.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', 'Staff', '2025-06-01 11:57:30', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"After meal\"}]', NULL, 'Imna na ha'),
(74, 40, 'Arcamo Jr., Emmanuel P.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', 'Staff', '2025-06-01 11:59:32', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"After Meal\"}]', NULL, 'imna na waa ka'),
(75, 40, 'Arcamo Jr., Emmanuel P.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', 'Staff', '2025-06-01 11:59:35', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"After Meal\"}]', NULL, 'Imna na inatay na'),
(76, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', 'Staff', '2025-08-10 18:32:47', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', NULL, ''),
(77, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', 'Staff', '2025-08-10 18:33:56', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, ''),
(78, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', 'Staff', '2025-08-10 18:38:43', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, ''),
(79, 21, 'Abella, Joseph B.', 'qysupikame@mailinator.com', 'fusego@mailinator.com', 'Staff', '2025-08-17 00:12:58', '[{\"medicine\":\"Neozep\",\"dosage\":\"Accusantium est fuga\",\"quantity\":\"13\",\"frequency\":\"Ut quasi nisi sunt i\",\"instructions\":\"Quasi libero illo se\"}]', NULL, 'Sit deserunt in quam'),
(80, 22, 'Abellana, Vincent Anthony Q.', 'jykibonif@mailinator.com', 'nelucu@mailinator.com', 'Staff', '2025-08-17 00:16:37', '[{\"medicine\":\"Biogesic\",\"dosage\":\"In voluptas deserunt\",\"quantity\":\"46\",\"frequency\":\"Consequatur do offic\",\"instructions\":\"Soluta autem reprehe\"}]', NULL, 'Reprehenderit minus'),
(81, 22, 'Abellana, Vincent Anthony Q.', 'jykibonif@mailinator.com', 'nelucu@mailinator.com', 'Staff', '2025-08-17 00:16:43', '[{\"medicine\":\"Biogesic\",\"dosage\":\"In voluptas deserunt\",\"quantity\":\"46\",\"frequency\":\"Consequatur do offic\",\"instructions\":\"Soluta autem reprehe\"}]', NULL, 'Reprehenderit minus'),
(82, 22, 'Abellana, Vincent Anthony Q.', 'jykibonif@mailinator.com', 'nelucu@mailinator.com', 'Staff', '2025-08-17 00:16:44', '[{\"medicine\":\"Biogesic\",\"dosage\":\"In voluptas deserunt\",\"quantity\":\"46\",\"frequency\":\"Consequatur do offic\",\"instructions\":\"Soluta autem reprehe\"}]', NULL, 'Reprehenderit minus'),
(83, 23, 'Abendan, Christian James A.', 'xyzamu@mailinator.com', 'hizakop@mailinator.com', 'Staff', '2025-08-17 00:27:38', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Velit Nam odit dele\",\"quantity\":\"23\",\"frequency\":\"Id officia ad in qu\",\"instructions\":\"Odit voluptas nostru\"}]', NULL, 'Ut omnis magna irure'),
(84, 24, 'Abendan, Nino Rashean T.', 'bypo@mailinator.com', 'qahuv@mailinator.com', 'Staff', '2025-08-17 00:29:27', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Eiusmod asperiores m\",\"quantity\":\"48\",\"frequency\":\"Itaque mollit in dol\",\"instructions\":\"Aliquid ea quia iste\"}]', NULL, 'Recusandae Magnam h'),
(85, 24, 'Abendan, Nino Rashean T.', 'bypo@mailinator.com', 'qahuv@mailinator.com', 'Staff', '2025-08-17 00:29:34', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Eiusmod asperiores m\",\"quantity\":\"48\",\"frequency\":\"Itaque mollit in dol\",\"instructions\":\"Aliquid ea quia iste\"}]', NULL, 'Recusandae Magnam h'),
(86, 24, 'Abendan, Nino Rashean T.', 'bypo@mailinator.com', 'bypo@mailinator.com', 'Staff', '2025-08-17 00:29:38', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Eiusmod asperiores m\",\"quantity\":\"48\",\"frequency\":\"Itaque mollit in dol\",\"instructions\":\"Aliquid ea quia iste\"}]', NULL, 'Recusandae Magnam h'),
(87, 26, 'Acidillo, Baby John V.', 'fawyjyxesu@mailinator.com', 'saleg@mailinator.com', 'Staff', '2025-08-17 00:31:00', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Non ipsa magna culp\",\"quantity\":\"96\",\"frequency\":\"Ducimus quia commod\",\"instructions\":\"Voluptas et consequa\"}]', NULL, 'Rem ipsa non volupt'),
(88, 27, 'Adona, Carl Macel C.', 'haqupo@mailinator.com', 'vakuqaluz@mailinator.com', 'Staff', '2025-08-17 00:34:02', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Dolor est minim culp\",\"quantity\":\"4\",\"frequency\":\"Cumque eius eaque qu\",\"instructions\":\"Nihil reprehenderit\"}]', NULL, 'Nihil id rerum labor'),
(89, 26, 'Acidillo, Baby John V.', 'naja@mailinator.com', 'wunuhig@mailinator.com', 'Staff', '2025-08-17 00:48:49', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Ea quam voluptatibus\",\"quantity\":\"66\",\"frequency\":\"Quasi tempore molli\",\"instructions\":\"Sapiente doloremque \"}]', NULL, 'Doloribus odit quia '),
(90, 22, 'Abellana, Vincent Anthony Q.', 'hezy@mailinator.com', 'haputec@mailinator.com', 'Staff', '2025-08-17 00:51:40', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Do exercitationem mi\",\"quantity\":\"40\",\"frequency\":\"Consequatur vel et v\",\"instructions\":\"Dolore cillum nihil \"}]', NULL, 'Aut pariatur Facere'),
(91, 25, 'Abellana, Ariel L', 'bixotehel@mailinator.com', 'fejukatur@mailinator.com', 'Staff', '2025-08-17 00:52:39', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Quo nihil nisi in sa\",\"quantity\":\"69\",\"frequency\":\"Nihil ipsum aliqua \",\"instructions\":\"Qui iste natus bland\"}]', NULL, 'Rem et vel quia et e'),
(92, 22, 'Abellana, Vincent Anthony Q.', 'jule@mailinator.com', 'dybawaxoga@mailinator.com', 'Staff', '2025-08-17 00:53:20', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Fugiat debitis aut e\",\"quantity\":\"48\",\"frequency\":\"Aliquid distinctio \",\"instructions\":\"Ipsam ipsum ut delec\"}]', NULL, 'Ipsum culpa sequi cu'),
(93, 30, 'Aguilar, Jaymar C', 'dynymaq@mailinator.com', 'roxivyzopy@mailinator.com', 'Staff', '2025-08-17 01:12:04', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Quis porro ratione e\",\"quantity\":\"85\",\"frequency\":\"In explicabo Optio\",\"instructions\":\"Nam voluptatum aut p\"}]', NULL, 'Rerum ea qui et expe'),
(94, 23, 'Abendan, Christian James A.', 'salaqy@mailinator.com', 'kukusohak@mailinator.com', 'Staff', '2025-08-17 12:10:37', '[{\"medicine\":\"Neozep\",\"dosage\":\"Similique nobis iste\",\"quantity\":\"82\",\"frequency\":\"Molestias vel pariat\",\"instructions\":\"Ea et aute voluptas \"}]', NULL, 'Quia laborum sunt qu'),
(95, 21, 'Abella, Joseph B.', 'sytujon@mailinator.com', 'zawuhiqe@mailinator.com', 'Staff', '2025-08-18 01:45:27', '[{\"medicine\":\"Neozep\",\"dosage\":\"Quo odit ipsa in ea\",\"quantity\":\"38\",\"frequency\":\"Cum numquam in ea ex\",\"instructions\":\"Nesciunt labore dic\"}]', 'Fever', 'Odio laudantium a q');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(225) NOT NULL,
  `role` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active',
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `role`, `status`, `password`) VALUES
(1, 'Lane Wong', 'myjuci@mailinator.com', '', 'admin', 'Disabled', '$2y$10$wxeMPGvTwbOOIafIL7Vgg.B4Qsw9NLvmnZXvzpEb0E5EwU2r1gQ4K'),
(2, 'Margaret Haynes', 'gogav@mailinator.com', '', 'doctor/nurse', 'Active', '$2y$10$iSob1zjLfAuS9vjqOLbkTuHX/MrxtsF.GeGu7iJt/HmnL7nSHQQg2'),
(3, 'Abraham Shepherd', 'qamosuko@mailinator.com', '', 'doctor/nurse', 'Active', '$2y$10$VbnIK3S23mO.MC7XaN0XvOWifVCWdpNlGjk0q2LTm.OjAJdAzAJSy'),
(4, 'Hadley Frye', 'welawevuz', '', 'doctor/nurse', 'Active', '$2y$10$olZBsTQkbreXLUGXI83NK.O537cYMdHSiBPBrzuzGk.VuH/GB85yW'),
(5, 'jaynu', 'jaynu', '', 'admin', 'Active', '$2y$10$R6clXSYHdQvQdBU8squVOOco0Ji5AZbFWwk/ajGlIzIye93W5Khoq'),
(6, 'jaynu123', 'jaynu123', '', 'doctor/nurse', 'Active', '$2y$10$d93FveIXK0mVMglKJp0H0.h5XgBDpwalUqnVgbMXPLX1PzNlJSP46'),
(7, 'Lillith Lloyd', 'zocovufopy', '', 'doctor/nurse', 'Active', '$2y$10$THOu/EIjBMauYwbSeMvm6uRY5iNnsqsUoopWz7ak.eKEDrgzTAZG.'),
(8, 'vince', 'vince', 'jaynujangad03@gmail.com', 'admin', 'Active', '$2y$10$URrhyCKGyBV72spp6aLN4uvR2LtnaEpyPXur7kvmZ8CwjS8HP6O.i'),
(9, 'Pamela Burke', 'xoqeq', 'konutoj@mailinator.com', 'doctor/nurse', 'Active', '$2y$10$ALzKtdVZYq8UYEsaOTbACOTRsou82LdM/1qJbZDYWLgtduuLBx4qG'),
(10, 'ADMINISTRATOR', 'admin', 'cedricjade13@gmail.com', 'admin', 'Active', '$2y$10$CSBjItBOHgi3O6QB7J7SA.My7X1Tj2pskNfU/spaxHcB9pHLHc8hW'),
(11, 'Dr. Jaynu', 'jaynu13', 'jaynujangad03@gmail.com', 'doctor/nurse', 'Active', '$2y$10$IKnlo3YuS7MGwHLG8bWHEu8HaJGTa0HoTOG7Qwu1KQ9WCdlBTGtly'),
(12, 'Dr. jade', 'jade', 'cedricjade13@gmail.com', 'doctor/nurse', 'Active', '$2y$10$xTEIegJg6IdiVHRsH2BhJ.Ag9NCZVkCUWlU/Fun.ydIqlPIfgI1oW');

-- --------------------------------------------------------

--
-- Table structure for table `vital_signs`
--

CREATE TABLE `vital_signs` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `patient_name` varchar(255) DEFAULT NULL,
  `vital_date` date DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `body_temp` decimal(4,2) DEFAULT NULL,
  `resp_rate` int(11) DEFAULT NULL,
  `pulse` int(11) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `oxygen_sat` decimal(5,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `recorded_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vital_signs`
--

INSERT INTO `vital_signs` (`id`, `patient_id`, `patient_name`, `vital_date`, `weight`, `height`, `body_temp`, `resp_rate`, `pulse`, `blood_pressure`, `oxygen_sat`, `remarks`, `recorded_by`, `created_at`) VALUES
(10, 21, 'Abella, Joseph B.', '2025-08-10', 12.00, 67.00, 12.00, 12, 14, '120/80', 12.00, 'okay', 'jaynu123', '2025-08-10 19:42:59'),
(11, 23, 'John Doe', '2025-01-06', NULL, NULL, 36.50, 16, 72, NULL, NULL, NULL, 'Nurse Smith', '2025-08-10 20:30:27'),
(12, 24, 'Jane Smith', '2025-01-06', NULL, NULL, 36.80, 18, 68, NULL, NULL, NULL, 'Nurse Johnson', '2025-08-10 20:30:27'),
(13, 22, 'Abellana, Vincent Anthony Q.', '2025-08-10', 20.00, 20.00, 20.00, 20, 20, '20/80', 20.00, 'asd', 'jaynu13', '2025-08-10 21:54:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student` (`student_id`),
  ADD KEY `idx_date` (`date`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_schedule` (`doctor_name`,`schedule_date`,`schedule_time`);

--
-- Indexes for table `imported_patients`
--
ALTER TABLE `imported_patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medication_referrals`
--
ALTER TABLE `medication_referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recipient` (`recipient_id`),
  ADD KEY `idx_sender` (`sender_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_prescriptions`
--
ALTER TABLE `pending_prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_patient_date` (`patient_id`,`vital_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `imported_patients`
--
ALTER TABLE `imported_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=331;

--
-- AUTO_INCREMENT for table `medication_referrals`
--
ALTER TABLE `medication_referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `pending_prescriptions`
--
ALTER TABLE `pending_prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `imported_patients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
