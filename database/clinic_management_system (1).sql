-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2025 at 08:22 AM
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
(1, 40, '2025-08-18', '18:17-19:18', 'Aut dolores eligendi', 'pending', 'cuwanixid@mailinator.com', 'ziqefesu@mailinator.com', '2025-08-17 10:25:53'),
(2, 34, '2025-08-18', '00:32-03:36', 'Eligendi iusto aliqu', 'pending', 'wylufaj@mailinator.com', 'luted@mailinator.com', '2025-08-17 16:48:19'),
(3, 35, '2025-08-20', '01:17-01:47', 'Non et eu ipsa natu', 'pending', 'tejeqogiz@mailinator.com', 'bekox@mailinator.com', '2025-08-17 17:18:04'),
(4, 35, '2025-08-20', '01:47-02:17', 'Animi sit irure re', 'pending', 'kykasokaby@mailinator.com', 'mucuvy@mailinator.com', '2025-08-17 17:18:09'),
(5, 35, '2025-08-30', '15:50', 'Qui eligendi dolorem', 'rescheduled', 'mabemepo@mailinator.com', 'xukon@mailinator.com', '2025-08-17 17:32:43'),
(6, 29, '2025-08-20', '10:30', 'Eum soluta recusanda', 'declined', 'fyhot@mailinator.com', 'ryma@mailinator.com', '2025-08-17 17:35:16'),
(7, 40, '2025-08-20', '10:30', 'Aperiam Nam magni ma', 'pending', 'ragi@mailinator.com', 'vypisa@mailinator.com', '2025-08-18 03:29:43'),
(8, 40, '2025-08-19', '03:01-03:31', 'Est ea molestiae ips', 'pending', 'nafoc@mailinator.com', 'vewy@mailinator.com', '2025-08-18 04:39:33'),
(10, 40, '2025-08-22', '09:15-09:45', 'yawa', 'approved', 'jaynujangad03@gmail.com', 'cedricjade13@gmail.com', '2025-08-19 01:40:47'),
(19, 40, '2025-08-27', '08:28-08:58', 'asd', 'pending', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', '2025-08-26 00:27:46'),
(20, 40, '2025-08-27', '08:58-09:28', 'as', 'pending', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', '2025-08-26 00:27:53'),
(21, 40, '2025-08-27', '09:28-09:58', 'asd', 'pending', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', '2025-08-26 00:28:10'),
(22, 40, '2025-08-27', '09:58-10:28', 'asd', 'pending', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', '2025-08-26 00:28:18'),
(23, 40, '2025-08-27', '10:28-10:58', 'asd', 'declined', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', '2025-08-26 00:28:33'),
(24, 40, '2025-08-27', '10:58-11:28', 'asd', 'pending', 'cedricjade13@gmail.com', 'phennybert@gmail.com', '2025-08-26 04:18:41'),
(25, 40, '2025-08-27', '11:28-11:58', 'asd', 'approved', 'cedricjade13@gmail.com', 'phennybert@gmail.com', '2025-08-26 04:18:59'),
(26, 40, '2025-08-27', '11:58-12:28', 'as', 'approved', 'cedricjade13@gmail.com', 'phennybert@gmail.com', '2025-08-26 04:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `clinic_visits`
--

CREATE TABLE `clinic_visits` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `visit_date` date NOT NULL,
  `visit_time` time DEFAULT NULL,
  `visit_reason` varchar(500) DEFAULT NULL,
  `visit_type` enum('appointment','prescription','walk_in','emergency') DEFAULT 'appointment',
  `staff_member` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clinic_visits`
--

INSERT INTO `clinic_visits` (`id`, `patient_id`, `patient_name`, `visit_date`, `visit_time`, `visit_reason`, `visit_type`, `staff_member`, `notes`, `created_at`, `updated_at`) VALUES
(9, 21, 'Test Student', '2025-08-18', NULL, 'Fever and headache', 'prescription', 'Staff', NULL, '2025-08-18 20:13:07', '2025-08-18 20:13:07'),
(10, 21, 'Abella, Joseph B.', '2025-08-18', NULL, 'asd', 'prescription', 'Staff', NULL, '2025-08-18 20:13:41', '2025-08-18 20:13:41'),
(11, 21, 'Abella, Joseph B.', '2025-08-18', NULL, 'asd', 'prescription', 'Staff', NULL, '2025-08-18 20:14:00', '2025-08-18 20:14:00'),
(12, 21, 'Abella, Joseph B.', '2025-08-18', NULL, 'asd', 'prescription', 'Staff', NULL, '2025-08-18 20:14:16', '2025-08-18 20:14:16'),
(13, 21, 'Abella, Joseph B.', '2025-08-18', NULL, 'fevcer', 'prescription', 'Staff', NULL, '2025-08-18 20:33:09', '2025-08-18 20:33:09'),
(14, 21, 'Abella, Joseph B.', '2025-08-18', NULL, 'fever', 'prescription', 'Staff', NULL, '2025-08-18 20:49:06', '2025-08-18 20:49:06'),
(15, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Sakits ulo', 'prescription', 'Staff', NULL, '2025-08-19 07:35:33', '2025-08-19 07:35:33'),
(16, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Qui occaecat magna v', 'prescription', 'Staff', NULL, '2025-08-19 07:37:15', '2025-08-19 07:37:15'),
(17, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Dolore consequuntur ', 'prescription', 'Staff', NULL, '2025-08-19 07:37:31', '2025-08-19 07:37:31'),
(18, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Doloribus praesentiu', 'prescription', 'Staff', NULL, '2025-08-19 07:37:49', '2025-08-19 07:37:49'),
(19, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Recusandae Rerum te', 'prescription', 'Staff', NULL, '2025-08-19 07:38:02', '2025-08-19 07:38:02'),
(20, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Enim qui eum asperio', 'prescription', 'Staff', NULL, '2025-08-19 07:38:16', '2025-08-19 07:38:16'),
(21, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'fever', 'prescription', 'Staff', NULL, '2025-08-19 07:40:10', '2025-08-19 07:40:10'),
(22, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Assumenda a ipsa as', 'prescription', 'Staff', NULL, '2025-08-19 08:11:22', '2025-08-19 08:11:22'),
(23, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Repudiandae unde lau', 'prescription', 'Staff', NULL, '2025-08-19 08:13:01', '2025-08-19 08:13:01'),
(24, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Ab nihil alias accus', 'prescription', 'Staff', NULL, '2025-08-19 08:41:54', '2025-08-19 08:41:54'),
(25, 22, 'Abellana, Vincent Anthony Q.', '2025-08-19', NULL, 'Adipisci soluta est', 'prescription', 'Staff', NULL, '2025-08-19 08:42:17', '2025-08-19 08:42:17'),
(26, 21, 'Abella, Joseph B.', '2025-08-19', NULL, 'Neque provident sae', 'prescription', 'Staff', NULL, '2025-08-19 08:42:29', '2025-08-19 08:42:29'),
(27, 22, 'Abellana, Vincent Anthony Q.', '2025-08-19', NULL, 'Laboriosam culpa si', 'prescription', 'Staff', NULL, '2025-08-19 08:42:53', '2025-08-19 08:42:53'),
(28, 23, 'Abendan, Christian James A.', '2025-08-19', NULL, 'Corrupti debitis qu', 'prescription', 'Staff', NULL, '2025-08-19 08:58:05', '2025-08-19 08:58:05'),
(29, 23, 'Abendan, Christian James A.', '2025-08-19', NULL, 'Sit assumenda quod ', 'prescription', 'Staff', NULL, '2025-08-19 08:58:17', '2025-08-19 08:58:17'),
(30, 23, 'Abendan, Christian James A.', '2025-08-19', NULL, 'Maiores ex sint sed ', 'prescription', 'Staff', NULL, '2025-08-19 08:58:28', '2025-08-19 08:58:28'),
(31, 31, 'Alicaya, Ralph Lorync D.', '2025-08-26', NULL, 'asd', 'prescription', 'Staff', NULL, '2025-08-26 10:58:25', '2025-08-26 10:58:25'),
(32, 31, 'Alicaya, Ralph Lorync D.', '2025-08-26', NULL, 'asd', 'prescription', 'Staff', NULL, '2025-08-26 10:58:41', '2025-08-26 10:58:41'),
(33, 31, 'Alicaya, Ralph Lorync D.', '2025-08-26', NULL, 'asd', 'prescription', 'Staff', NULL, '2025-08-26 10:58:57', '2025-08-26 10:58:57');

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
(78, 'rhona', '2025-08-20', '15:49-16:50', '2025-08-19 15:49:05'),
(79, 'jade', '2025-08-27', '08:28-12:27', '2025-08-26 08:27:26');

-- --------------------------------------------------------

--
-- Table structure for table `health_reminders`
--

CREATE TABLE `health_reminders` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reminder_type` enum('appointment','vaccination','checkup','medication','general') NOT NULL,
  `due_date` date DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `health_tips`
--

CREATE TABLE `health_tips` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `category` enum('first_aid','wellness','nutrition','mental_health','hygiene','emergency') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `health_tips`
--

INSERT INTO `health_tips` (`id`, `title`, `content`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Basic First Aid for Cuts and Scrapes', 'Clean the wound with soap and water. Apply an antiseptic and cover with a sterile bandage. Seek medical attention if the wound is deep or shows signs of infection.', 'first_aid', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(2, 'Hand Hygiene Best Practices', 'Wash your hands frequently with soap and water for at least 20 seconds. Use hand sanitizer when soap is not available. Avoid touching your face with unwashed hands.', 'hygiene', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(3, 'Healthy Eating for Students', 'Eat a balanced diet with plenty of fruits, vegetables, and whole grains. Stay hydrated by drinking water throughout the day. Avoid excessive junk food and sugary drinks.', 'nutrition', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(4, 'Stress Management Techniques', 'Practice deep breathing exercises. Take regular breaks during study sessions. Exercise regularly and get adequate sleep. Talk to someone if you feel overwhelmed.', 'mental_health', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(5, 'Emergency Contact Information', 'In case of emergency, call 911 immediately. For school-related health concerns, contact the school clinic. Always have emergency contacts readily available.', 'emergency', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(6, 'Preventing Common Illnesses', 'Get adequate sleep (7-9 hours for students). Eat nutritious meals and stay hydrated. Practice good hygiene and avoid close contact with sick individuals.', 'wellness', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(7, 'First Aid for Burns', 'Cool the burn under cold running water for 10-20 minutes. Do not apply ice or butter. Cover with a sterile bandage. Seek medical attention for severe burns.', 'first_aid', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(8, 'Mental Health Awareness', 'It\'s okay to not be okay. Reach out to school counselors, teachers, or trusted adults if you\'re struggling. You\'re not alone in your feelings.', 'mental_health', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(9, 'Exercise and Physical Activity', 'Aim for at least 60 minutes of physical activity daily. Include both cardio and strength training. Find activities you enjoy to stay motivated.', 'wellness', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49'),
(10, 'Proper Mask Usage', 'Wear masks properly covering nose and mouth. Replace disposable masks regularly. Wash cloth masks after each use. Maintain social distance when possible.', 'hygiene', 1, '2025-08-18 04:20:49', '2025-08-18 04:20:49');

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
  `email` varchar(255) DEFAULT NULL,
  `parent_email` varchar(255) DEFAULT NULL,
  `parent_phone` varchar(20) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `citizenship` varchar(100) DEFAULT NULL,
  `course_program` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_contact` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_number` varchar(20) DEFAULT NULL,
  `upload_year` varchar(9) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `imported_patients`
--

INSERT INTO `imported_patients` (`id`, `student_id`, `name`, `dob`, `gender`, `address`, `email`, `parent_email`, `parent_phone`, `contact_number`, `religion`, `citizenship`, `course_program`, `civil_status`, `password`, `year_level`, `guardian_name`, `guardian_contact`, `emergency_contact_name`, `emergency_contact_number`, `upload_year`) VALUES
(21, 'SCC-22-00015336', 'Abella, Joseph B.', '3/19/2000', 'Male', 'Camarin Vito Minglanilla Cebu', 'joseph.abella@gmail.com', 'abella.maria@gmail.com', '09170000001', '09172345678', 'Roman Catholic', 'Filipino', 'BSHM', 'Single', 'Abella', '1st Year', 'Maria Abella', '09181234567', 'Juan Abella', '09987654321', NULL),
(22, 'SCC-22-00017358', 'Abellana, Vincent Anthony Q.', '7/8/2002', 'Male', 'Pakigne Minglanilla Cebu', 'vincentanthony.abellana@gmail.com', 'abellana.juan@gmail.com', '09170000002', '09354567890', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Abellana', '1st Year', 'Roberto Abellana', '09213456789', 'Grace Abellana', '09123456789', NULL),
(23, 'SCC-20-00010846', 'Abendan, Christian James A.', '4/27/2004', 'Male', 'Pob. Ward 2 Minglanilla, Cebu', 'christianjames.abendan@gmail.com', 'abendan.rosario@gmail.com', '09170000003', '09475678901', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Abendan', '1st Year', 'Lourdes Abendan', '09354567890', 'Michael Abendan', '09221234567', NULL),
(24, 'SCC-14-0001275', 'Abendan, Nino Rashean T.', '2/12/2002', 'Male', 'Ward 2 pob., Minglanilla, Cebu ', 'ninorashean.abendan@gmail.com', 'abendan.carlo@gmail.com', '09170000004', '09566789012', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Abendan', '1st Year', 'Antonio Abendan', '09475678901', 'Fatima Abendan', '09331234567', NULL),
(25, 'SCC-21-00012754', 'Abellana, Ariel L', '10/1/2002', 'Male', 'Basak, Sibonga, Cebu', 'ariel.abellana@gmail.com', 'abellana.luz@gmail.com', '09170000005', '09213456789', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Abellana', '2nd Year', 'Elena Abellana', '09566789012', 'Mario Abellana', '09441234567', NULL),
(26, 'SCC-21-00012377', 'Acidillo, Baby John V.', '7/21/2000', 'Male', 'Bairan City of Naga', 'babyjohn.acidillo@gmail.com', 'acidillo.jose@gmail.com', '09170000006', '09657890123', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Acidillo', '2nd Year', 'Victor Acidillo', '09657890123', 'Ana Acidillo', '09551234567', NULL),
(27, 'SCC-21-00014490', 'Adona, Carl Macel C.', '3/29/2002', 'Male', 'Pob. Ward IV Minglanilla Cebu', 'carlmacel.adona@gmail.com', 'adona.elena@gmail.com', '09170000007', '09919012345', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Adona', '2nd Year', 'Rosalinda Adona', '09778901234', 'Carlos Adona', '09661234567', NULL),
(28, 'SCC-19-0009149', 'Albiso, Creshell Mary M.', '6/18/2003', 'Female', 'Bairan, City of Naga, Cebu', 'creshellmary.albiso@gmail.com', 'albiso.raul@gmail.com', '09170000008', '09234567891', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Albiso', '2nd Year', 'Carmelita Albiso', '09919012345', 'Pedro Albiso', '09771234567', NULL),
(29, 'SCC-21-00014673', 'Alegado, John Raymon B.', '1/9/2002', 'Male', 'Tagjaguimit City of Naga Cebu', 'johnraymon.alegado@gmail.com', 'alegado.sofia@gmail.com', '09170000009', '09365678902', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Alegado', '2nd Year', 'Benjamin Alegado', '09123456780', 'Marissa Alegado', '09881234567', NULL),
(30, 'SCC-18-0007848', 'Aguilar, Jaymar C', '2/22/2000', 'Male', 'North Poblacion, San Fernando, Cebu', 'jaymar.aguilar@gmail.com', 'aguilar.pedro@gmail.com', '09170000010', '09123456780', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Aguilar', '3rd Year', 'Cynthia Aguilar', '09234567891', 'Joseph Aguilar', '09991234567', NULL),
(31, 'SCC-18-0006048', 'Alicaya, Ralph Lorync D.', '1/17/2000', 'Male', 'Lower Pakigne, Minglanilla Cebu', 'ralphlorync.alicaya@gmail.com', 'alicaya.lorena@gmail.com', '09170000011', '09577890124', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Alicaya', '3rd Year', 'Daniel Alicaya', '09365678902', 'Sophia Alicaya', '09102345678', NULL),
(32, 'SCC-20-00011552', 'Baraclan, Genesis S.', '11/12/1999', 'Male', 'Bacay Tulay Minglanilla Cebu', 'genesis.baraclan@gmail.com', 'baraclan.david@gmail.com', '09170000012', '09242345678', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Baraclan', '3rd Year', 'Gloria Baraclan', '09486789013', 'Mark Baraclan', '09213456780', NULL),
(33, 'SCC-18-0007440', 'Base, Jev Adrian', '11/8/2001', 'Male', 'Sambag, Tuyan, City of Naga, Cebu', 'jev.adrian.base@gmail.com', NULL, NULL, '09373456789', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Base', '3rd Year', 'Francisco Base', '09577890124', 'Jenny Base', '09334567890', NULL),
(34, 'SCC-19-00010521', 'Booc, Aloysius A.', '6/6/1996', 'Male', 'Babag Lapulapu City', 'aloysious.booc@gmail.com', 'booc.andres@gmail.com', '09170000014', '09494567890', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Booc', '3rd Year', 'Teresa Booc', '09668901235', 'Arthur Booc', '09445678901', NULL),
(35, 'SCC-18-0007793', 'Adlawan, Ealla Marie', '11/7/1999', 'Female', 'Spring Village Pakigne, Minglanilla', 'ealla.adlawan@gmail.com', 'adlawan.rina@gmail.com', '09170000015', '09778901234', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Adlawan', '4th Year', 'Rogelio Adlawan', '09789012346', 'Clara Adlawan', '09556789012', NULL),
(36, 'SCC-19-00010625', 'Alferez Jr., Bernardino S.', '8/12/1999', 'Male', 'Cantao-an Naga Cebu', 'bernardino.alferezjr@gmail.com', 'alferez.ernesto@gmail.com', '09170000016', '09486789013', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Alferez Jr.', '4th Year', 'Marites Alferez', '09920123457', 'Diego Alferez', '09667890123', NULL),
(37, 'SCC-19-0009987', 'Almendras, Alistair A', '4/21/2000', 'Male', 'Purok Mahogany, Sambag Kolo, Tuyan City of Naga, Cebu', 'alistair.almendras@gmail.com', 'almendras.gloria@gmail.com', '09170000017', '09668901235', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Almendras', '4th Year', 'Edgar Almendras', '09131234567', 'Liza Almendras', '09778901234', NULL),
(38, 'SCC-17-0005276', 'Alvarado, Dexter Q.', '7/12/1999', 'Male', 'Babayongan Dalaguete Cebu', 'dexter.alvarado@gmail.com', 'alvarado.manuel@gmail.com', '09170000018', '09789012346', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Alvarado', '4th Year', 'Angela Alvarado', '09242345678', 'Mario Alvarado', '09889012345', NULL),
(39, 'SCC-19-00010487', 'Amarille, Kim Ryan M', '10/31/1997', 'Male', 'Tungkop Minglanilla Cebu', 'kimryan.amarille@gmail.com', 'amarille.susan@gmail.com', '09170000019', '09920123457', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Amarille', '4th Year', 'Ricardo Amarille', '09373456789', 'Helen Amarille', '09990123456', NULL),
(40, 'SCC-18-0008724', 'Arcamo Jr., Emmanuel P.', '10/1/1997', 'Male', 'Sitio Tabay Tunghaan, Minglanilla Cebu', 'emmanuel.arcamojr@gmail.com', 'arcamo.roberto@gmail.com', '09170000020', '09131234567', 'Roman Catholic', 'Filipino', 'BSIT', 'Single', 'Arcamo Jr.', '4th Year', 'Josephine Arcamo', '09494567890', 'Paul Arcamo', '09111234567', NULL);

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
(330, 6, 'jaynu123', 'Logged in', '2025-08-18 08:53:27'),
(331, 10, 'admin', 'Logged in', '2025-08-18 10:40:01'),
(332, 6, 'jaynu123', 'Logged in', '2025-08-18 10:40:06'),
(333, 10, 'admin', 'Logged in', '2025-08-18 11:53:24'),
(334, 6, 'jaynu123', 'Logged in', '2025-08-18 11:53:30'),
(335, NULL, 'SCC-18-0008724', 'Added medicine: asd', '2025-08-18 12:40:13'),
(336, 11, 'jaynu13', 'Logged in', '2025-08-18 15:27:36'),
(337, 11, 'jaynu13', 'Logged in', '2025-08-18 15:29:11'),
(338, 11, 'jaynu13', 'Logged in', '2025-08-18 15:45:32'),
(339, 11, 'jaynu13', 'Logged in', '2025-08-18 15:48:27'),
(340, 10, 'admin', 'Logged in', '2025-08-18 15:48:46'),
(341, 11, 'jaynu13', 'Logged in', '2025-08-18 15:49:26'),
(342, 10, 'admin', 'Logged in', '2025-08-18 15:57:31'),
(343, 11, 'jaynu13', 'Logged in', '2025-08-18 16:30:41'),
(344, 11, 'jaynu13', 'Logged in', '2025-08-18 16:33:13'),
(345, 11, 'jaynu13', 'Logged in', '2025-08-18 16:34:07'),
(346, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-18 19:15:42'),
(347, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 19:17:39'),
(348, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 19:34:15'),
(349, NULL, 'Staff', 'Issued prescription for patient: Test Student', '2025-08-18 20:13:07'),
(350, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 20:13:41'),
(351, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 20:14:00'),
(352, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 20:14:16'),
(353, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 20:33:09'),
(354, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-18 20:49:06'),
(355, 10, 'admin', 'Logged in', '2025-08-19 07:33:23'),
(356, 6, 'jaynu123', 'Logged in', '2025-08-19 07:33:29'),
(357, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:35:33'),
(358, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:37:15'),
(359, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:37:31'),
(360, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:37:49'),
(361, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:38:02'),
(362, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:38:16'),
(363, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 07:40:10'),
(364, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 08:11:22'),
(365, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 08:13:01'),
(366, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 08:41:54'),
(367, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-19 08:42:17'),
(368, NULL, 'Staff', 'Issued prescription for patient: Abella, Joseph B.', '2025-08-19 08:42:29'),
(369, NULL, 'Staff', 'Issued prescription for patient: Abellana, Vincent Anthony Q.', '2025-08-19 08:42:53'),
(370, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-08-19 08:58:05'),
(371, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-08-19 08:58:17'),
(372, NULL, 'Staff', 'Issued prescription for patient: Abendan, Christian James A.', '2025-08-19 08:58:28'),
(373, 10, 'admin', 'Logged in', '2025-08-20 13:59:57'),
(374, 6, 'jaynu123', 'Logged in', '2025-08-20 14:04:23'),
(375, 11, 'jaynu13', 'Logged in', '2025-08-22 18:16:44'),
(376, 10, 'admin', 'Logged in', '2025-08-22 18:42:07'),
(377, 10, 'admin', 'Logged in', '2025-08-22 19:17:14'),
(378, 10, 'admin', 'Logged in', '2025-08-22 19:52:36'),
(379, 10, 'admin', 'Logged in', '2025-08-22 20:29:55'),
(380, 11, 'jaynu13', 'Logged in', '2025-08-22 20:38:41'),
(381, 10, 'admin', 'Logged in', '2025-08-23 06:52:22'),
(382, 6, 'jaynu123', 'Logged in', '2025-08-23 06:52:38'),
(383, NULL, 'Staff', 'Issued prescription for patient: Alicaya, Ralph Lorync D.', '2025-08-26 10:58:25'),
(384, NULL, 'Staff', 'Issued prescription for patient: Alicaya, Ralph Lorync D.', '2025-08-26 10:58:41'),
(385, NULL, 'Staff', 'Issued prescription for patient: Alicaya, Ralph Lorync D.', '2025-08-26 10:58:57');

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
(12, 'Biogesic', '500mg', 35, '2029-06-05', '2025-05-28 17:55:41'),
(26, 'Rexidol', '500mg', 158, '2029-12-31', '2025-08-10 19:19:20'),
(27, 'Bioflu', '500mg', 330, '2027-10-17', '2025-08-17 01:26:21'),
(28, 'Bioflu', '500mg', 0, '2025-10-27', '2025-08-17 01:27:30'),
(29, 'Bioflu', '500mg', 0, '2025-08-17', '2025-08-17 01:28:07'),
(30, 'Mefinamic', '500mg', 523, '2024-06-04', '2025-08-17 09:30:00'),
(31, 'asd', 'asd', 61, '2025-08-18', '2025-08-18 12:40:13');

-- --------------------------------------------------------

--
-- Table structure for table `medicine_requests`
--

CREATE TABLE `medicine_requests` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `medicine_name` varchar(255) NOT NULL,
  `quantity_requested` varchar(100) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','declined','completed') DEFAULT 'pending',
  `staff_notes` text DEFAULT NULL,
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(60, 6, 'Staff', 'doctor/nurse', 34, 'Booc, Aloysius A.', 'meeting', 'aksdadkajskdasdkaksdaksdkjaskdjakjsd', 1, '2025-08-17 07:41:19'),
(61, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(62, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(63, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(64, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(65, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(66, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(67, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(68, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(69, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(70, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(71, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(72, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(73, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(74, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(75, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(76, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(77, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Yawa', 'Mga yawa mo tanan', 1, '2025-08-18 11:29:20'),
(78, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(79, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(80, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Yawa', 'Mga yawa mo tanan', 0, '2025-08-18 11:29:20'),
(81, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(82, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(83, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(84, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(85, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(86, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(87, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(88, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(89, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(90, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(91, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(92, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(93, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(94, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(95, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(96, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(97, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'asdasd', 'asdasdasd', 1, '2025-08-18 12:39:14'),
(98, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(99, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(100, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'asdasd', 'asdasdasd', 0, '2025-08-18 12:39:14'),
(101, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(102, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'test', 'test', 0, '2025-08-18 12:43:25'),
(103, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(104, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(105, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(106, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(107, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'test', 'test', 0, '2025-08-18 12:43:25'),
(108, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(109, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'test', 'test', 0, '2025-08-18 12:43:25'),
(110, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(111, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(112, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(113, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(114, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'test', 'test', 0, '2025-08-18 12:43:25'),
(115, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(116, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'test', 'test', 0, '2025-08-18 12:43:25'),
(117, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'test', 'test', 1, '2025-08-18 12:43:25'),
(118, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(119, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'test', 'test', 0, '2025-08-18 12:43:25'),
(120, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'test', 'test', 0, '2025-08-18 12:43:25'),
(121, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(122, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'test', 'test', 0, '2025-08-18 12:44:30'),
(123, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(124, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(125, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(126, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(127, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'test', 'test', 0, '2025-08-18 12:44:30'),
(128, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(129, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'test', 'test', 0, '2025-08-18 12:44:30'),
(130, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(131, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(132, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(133, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(134, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'test', 'test', 0, '2025-08-18 12:44:30'),
(135, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(136, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'test', 'test', 0, '2025-08-18 12:44:30'),
(137, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'test', 'test', 1, '2025-08-18 12:44:30'),
(138, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(139, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'test', 'test', 0, '2025-08-18 12:44:30'),
(140, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'test', 'test', 0, '2025-08-18 12:44:30'),
(141, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(142, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(143, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(144, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(145, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(146, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(147, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(148, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(149, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(150, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(151, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(152, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(153, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(154, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(155, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(156, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(157, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'asd', 'asd', 1, '2025-08-18 12:44:42'),
(158, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(159, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(160, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'asd', 'asd', 0, '2025-08-18 12:44:42'),
(161, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(162, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(163, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(164, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(165, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(166, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(167, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(168, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(169, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(170, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(171, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(172, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(173, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(174, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(175, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(176, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(177, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'asd', 'asd', 1, '2025-08-18 12:44:46'),
(178, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(179, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(180, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'asd', 'asd', 0, '2025-08-18 12:44:46'),
(181, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(182, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(183, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(184, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(185, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(186, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(187, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(188, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(189, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(190, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(191, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(192, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(193, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(194, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(195, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(196, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(197, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'asd', 'asd', 1, '2025-08-18 13:10:05'),
(198, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(199, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(200, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'asd', 'asd', 0, '2025-08-18 13:10:05'),
(201, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(202, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(203, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(204, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(205, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(206, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(207, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(208, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(209, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(210, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(211, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(212, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(213, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(214, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(215, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(216, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(217, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'asd', 'asd', 1, '2025-08-18 13:10:07'),
(218, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(219, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(220, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'asd', 'asd', 0, '2025-08-18 13:10:07'),
(221, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(222, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(223, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(224, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(225, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(226, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(227, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(228, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(229, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(230, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(231, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(232, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(233, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(234, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(235, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(236, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(237, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 1, '2025-08-18 13:10:13'),
(238, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:13'),
(239, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:14'),
(240, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:14'),
(241, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(242, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(243, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(244, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(245, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(246, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(247, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(248, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(249, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(250, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(251, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(252, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(253, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(254, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(255, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(256, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(257, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 1, '2025-08-18 13:10:17'),
(258, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(259, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(260, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Ipsum mollitia moll', 'Temporibus ut ut vel', 0, '2025-08-18 13:10:17'),
(261, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(262, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(263, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(264, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(265, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(266, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(267, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(268, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(269, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(270, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(271, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(272, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(273, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(274, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(275, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(276, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(277, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 1, '2025-08-18 13:10:50'),
(278, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(279, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(280, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:50'),
(281, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(282, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(283, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(284, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(285, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(286, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(287, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(288, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(289, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(290, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(291, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(292, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(293, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(294, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(295, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(296, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(297, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 1, '2025-08-18 13:10:52'),
(298, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(299, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(300, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:10:52'),
(301, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(302, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(303, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(304, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(305, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(306, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(307, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(308, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(309, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(310, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(311, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(312, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(313, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(314, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(315, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(316, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(317, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 1, '2025-08-18 13:11:00'),
(318, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(319, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(320, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:00'),
(321, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(322, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(323, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(324, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(325, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(326, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(327, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(328, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(329, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(330, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(331, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(332, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(333, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(334, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(335, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(336, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(337, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 1, '2025-08-18 13:11:02'),
(338, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(339, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(340, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Dolor qui placeat e', 'Sed aut eum tenetur', 0, '2025-08-18 13:11:02'),
(341, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(342, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(343, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(344, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(345, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(346, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(347, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(348, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(349, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(350, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(351, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(352, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(353, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(354, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(355, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(356, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(357, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'yati', 'Quam enim exercitati', 1, '2025-08-18 13:11:42'),
(358, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(359, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(360, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:42'),
(361, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(362, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(363, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(364, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(365, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(366, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(367, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(368, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(369, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(370, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(371, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(372, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(373, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(374, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(375, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(376, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(377, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'yati', 'Quam enim exercitati', 1, '2025-08-18 13:11:57'),
(378, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(379, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(380, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:11:57'),
(381, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(382, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(383, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(384, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(385, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(386, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(387, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(388, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(389, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(390, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(391, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(392, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(393, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(394, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(395, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(396, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(397, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'yati', 'Quam enim exercitati', 1, '2025-08-18 13:14:43'),
(398, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(399, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(400, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:43'),
(401, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(402, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(403, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(404, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(405, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(406, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(407, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(408, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(409, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(410, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(411, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(412, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(413, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(414, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(415, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(416, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(417, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'yati', 'Quam enim exercitati', 1, '2025-08-18 13:14:45'),
(418, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(419, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(420, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:45'),
(421, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(422, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52');
INSERT INTO `messages` (`id`, `sender_id`, `sender_name`, `sender_role`, `recipient_id`, `recipient_name`, `subject`, `message`, `is_read`, `created_at`) VALUES
(423, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(424, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(425, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(426, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(427, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(428, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(429, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(430, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(431, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(432, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(433, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(434, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(435, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(436, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(437, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'yati', 'Quam enim exercitati', 1, '2025-08-18 13:14:52'),
(438, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(439, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(440, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'yati', 'Quam enim exercitati', 0, '2025-08-18 13:14:52'),
(441, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(442, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(443, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(444, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(445, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(446, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(447, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(448, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(449, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(450, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(451, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(452, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(453, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(454, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(455, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(456, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(457, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 1, '2025-08-18 13:15:00'),
(458, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(459, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(460, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:00'),
(461, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(462, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(463, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(464, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(465, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(466, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(467, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(468, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(469, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(470, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(471, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(472, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(473, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(474, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(475, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(476, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(477, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 1, '2025-08-18 13:15:07'),
(478, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(479, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(480, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'Dolor ratione id qui', 'Sunt aut nisi et ani', 0, '2025-08-18 13:15:07'),
(481, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(482, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(483, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(484, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(485, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(486, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(487, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(488, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(489, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(490, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(491, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(492, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(493, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(494, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(495, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(496, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(497, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'animal oi', 'Id exercitationem s', 1, '2025-08-18 13:15:17'),
(498, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(499, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(500, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'animal oi', 'Id exercitationem s', 0, '2025-08-18 13:15:17'),
(501, 40, 'Staff', 'student', 21, 'Abella, Joseph B.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(502, 40, 'Staff', 'student', 25, 'Abellana, Ariel L', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(503, 40, 'Staff', 'student', 22, 'Abellana, Vincent Anthony Q.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(504, 40, 'Staff', 'student', 23, 'Abendan, Christian James A.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(505, 40, 'Staff', 'student', 24, 'Abendan, Nino Rashean T.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(506, 40, 'Staff', 'student', 26, 'Acidillo, Baby John V.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(507, 40, 'Staff', 'student', 35, 'Adlawan, Ealla Marie', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(508, 40, 'Staff', 'student', 27, 'Adona, Carl Macel C.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(509, 40, 'Staff', 'student', 30, 'Aguilar, Jaymar C', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(510, 40, 'Staff', 'student', 28, 'Albiso, Creshell Mary M.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(511, 40, 'Staff', 'student', 29, 'Alegado, John Raymon B.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(512, 40, 'Staff', 'student', 36, 'Alferez Jr., Bernardino S.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(513, 40, 'Staff', 'student', 31, 'Alicaya, Ralph Lorync D.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(514, 40, 'Staff', 'student', 37, 'Almendras, Alistair A', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(515, 40, 'Staff', 'student', 38, 'Alvarado, Dexter Q.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(516, 40, 'Staff', 'student', 39, 'Amarille, Kim Ryan M', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(517, 40, 'Staff', 'student', 40, 'Arcamo Jr., Emmanuel P.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 1, '2025-08-19 15:51:28'),
(518, 40, 'Staff', 'student', 32, 'Baraclan, Genesis S.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(519, 40, 'Staff', 'student', 33, 'Base, Jev Adrian', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28'),
(520, 40, 'Staff', 'student', 34, 'Booc, Aloysius A.', 'agenda', 'sjhdkjahkdhaskahdkaskhad', 0, '2025-08-19 15:51:28');

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
(88, NULL, 'A new appointment has been booked by <b>Alegado, John Raymon B.</b> for <b>2025-08-19</b> at <b>02:01-02:31</b> (Eum soluta recusanda).', 'appointment', 0, '2025-08-18 01:35:16'),
(89, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-19</b> at <b>02:31-03:01</b> (Aperiam Nam magni ma).', 'appointment', 0, '2025-08-18 11:29:43'),
(90, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-19</b> at <b>03:01-03:31</b> (Est ea molestiae ips).', 'appointment', 0, '2025-08-18 12:39:33'),
(91, 35, 'Your appointment for Qui eligendi dolorem has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-08-30</b> at <b>15:50</b>.', 'appointment', 0, '2025-08-18 15:50:14'),
(92, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:29-14:59</b> (asd).', 'appointment', 0, '2025-08-18 16:33:52'),
(93, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-22</b> at <b>09:15-09:45</b> (yawa).', 'appointment', 0, '2025-08-19 09:40:47'),
(94, 40, 'Your appointment for 2025-08-22 09:15-09:45 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-19 09:48:53'),
(95, 40, 'Your appointment for asd has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-08-21</b> at <b>09:51</b>.', 'appointment', 0, '2025-08-19 09:49:08'),
(96, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:59-15:29</b> (yawa).', 'appointment', 0, '2025-08-19 09:50:26'),
(97, 40, 'Your appointment for yawa has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-08-21</b> at <b>09:51</b>.', 'appointment', 0, '2025-08-19 09:50:43'),
(98, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:29-14:59</b> (yati).', 'appointment', 0, '2025-08-19 09:56:41'),
(99, 40, 'Your appointment for yati has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>9:57 AM</b>.', 'appointment', 0, '2025-08-19 09:56:55'),
(100, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:29-14:59</b> (yati).', 'appointment', 0, '2025-08-19 10:00:26'),
(101, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:59-15:29</b> (minatay).', 'appointment', 0, '2025-08-19 10:00:54'),
(102, 40, 'Your appointment for minatay has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>10:02 AM</b>.', 'appointment', 0, '2025-08-19 10:01:16'),
(103, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>15:29-15:59</b> (huhu).', 'appointment', 0, '2025-08-19 10:06:31'),
(104, 40, 'Your appointment for huhu has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>10:07 AM</b>.', 'appointment', 0, '2025-08-19 10:06:56'),
(105, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:59-15:29</b> (bogo).', 'appointment', 0, '2025-08-19 10:07:19'),
(106, 40, 'Your appointment for bogo has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>10:08 AM</b>.', 'appointment', 0, '2025-08-19 10:07:34'),
(107, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>15:29-15:59</b> (hahays).', 'appointment', 0, '2025-08-19 10:13:46'),
(108, 40, 'Your appointment for hahays has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>10:15 AM</b>.', 'appointment', 0, '2025-08-19 10:14:37'),
(109, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-20</b> at <b>14:59-15:29</b> (huhays).', 'appointment', 0, '2025-08-19 10:18:40'),
(110, 40, 'Your appointment for huhays has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>10:19 AM</b>.', 'appointment', 0, '2025-08-19 10:19:02'),
(111, 40, 'Your appointment for yati has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>August 21, 2025</b> at <b>10:25 AM</b>.', 'appointment', 0, '2025-08-19 10:25:00'),
(112, 35, 'Your appointment for 2025-08-20 01:47-02:17 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-19 10:26:12'),
(113, 35, 'Your appointment for 2025-08-20 01:17-01:47 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-08-19 10:26:23'),
(114, 40, 'Your appointment for Aperiam Nam magni ma has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-08-20</b> at <b>10:30</b>.', 'appointment', 0, '2025-08-19 10:29:40'),
(115, 29, 'Your appointment for Eum soluta recusanda has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-08-20</b> at <b>10:30</b>.', 'appointment', 0, '2025-08-19 10:29:57'),
(116, 35, 'Your appointment for Qui eligendi dolorem has been <span class=\'text-blue-600 font-semibold\'>rescheduled</span> to <b>2025-08-30</b> at <b>15:50</b>.', 'appointment', 0, '2025-08-19 10:33:36'),
(117, 40, 'Your appointment for 2025-08-22 09:15-09:45 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-19 10:33:49'),
(118, 29, 'Your appointment for 2025-08-20 10:30 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-08-19 10:33:55'),
(119, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>08:28-08:58</b> (asd).', 'appointment', 0, '2025-08-26 08:27:46'),
(120, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>08:58-09:28</b> (as).', 'appointment', 0, '2025-08-26 08:27:53'),
(121, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>09:28-09:58</b> (asd).', 'appointment', 0, '2025-08-26 08:28:10'),
(122, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>09:58-10:28</b> (asd).', 'appointment', 0, '2025-08-26 08:28:18'),
(123, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>10:28-10:58</b> (asd).', 'appointment', 0, '2025-08-26 08:28:33'),
(124, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>10:58-11:28</b> (asd).', 'appointment', 0, '2025-08-26 12:18:41'),
(125, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>11:28-11:58</b> (asd).', 'appointment', 0, '2025-08-26 12:18:59'),
(126, NULL, 'A new appointment has been booked by <b>Arcamo Jr., Emmanuel P.</b> for <b>2025-08-27</b> at <b>11:58-12:28</b> (as).', 'appointment', 0, '2025-08-26 12:19:21'),
(127, 40, 'Your appointment for 2025-08-27 11:58-12:28 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-26 13:53:58'),
(128, 40, 'Your appointment for 2025-08-27 11:28-11:58 has been <span class=\'text-green-600 font-semibold\'>approved</span>.', 'appointment', 0, '2025-08-26 13:54:48'),
(129, 40, 'Your appointment for 2025-08-27 10:28-10:58 has been <span class=\'text-red-600 font-semibold\'>declined</span>.', 'appointment', 0, '2025-08-26 13:55:02');

-- --------------------------------------------------------

--
-- Table structure for table `parent_alerts`
--

CREATE TABLE `parent_alerts` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `parent_email` varchar(255) NOT NULL,
  `visit_count` int(11) NOT NULL,
  `week_start_date` date NOT NULL,
  `week_end_date` date NOT NULL,
  `alert_sent_at` datetime DEFAULT current_timestamp(),
  `alert_status` enum('pending','sent','failed') DEFAULT 'pending',
  `email_content` text DEFAULT NULL,
  `sent_by` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_alerts`
--

INSERT INTO `parent_alerts` (`id`, `patient_id`, `patient_name`, `parent_email`, `visit_count`, `week_start_date`, `week_end_date`, `alert_sent_at`, `alert_status`, `email_content`, `sent_by`, `created_at`) VALUES
(1, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 07:51:38', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 07:51:38'),
(2, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 07:51:45', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 07:51:45'),
(3, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 07:53:38', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 07:53:38'),
(4, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 07:58:55', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 07:58:55'),
(5, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 07:59:03', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 07:59:03'),
(6, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 08:02:44', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:02:44'),
(7, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 08:02:54', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:02:54');
INSERT INTO `parent_alerts` (`id`, `patient_id`, `patient_name`, `parent_email`, `visit_count`, `week_start_date`, `week_end_date`, `alert_sent_at`, `alert_status`, `email_content`, `sent_by`, `created_at`) VALUES
(8, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 08:10:48', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:10:48'),
(9, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 15, '2025-08-18', '2025-08-24', '2025-08-19 08:10:57', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>15 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:10:57'),
(11, 21, 'Abella, Joseph B.', 'sicecyre@mailinator.com', 16, '2025-08-18', '2025-08-24', '2025-08-19 08:11:31', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>16 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 16:</strong> Aug 19, 2025 at 8:11 AM<br>Reason: Assumenda a ipsa as<br>Medication: [{&quot;medicine&quot;:&quot;Mefinamic&quot;,&quot;dosage&quot;:&quot;Quo pariatur Labori&quot;,&quot;quantity&quot;:&quot;30&quot;,&quot;frequency&quot;:&quot;Delectus enim cumqu&quot;,&quot;instructions&quot;:&quot;Dolor illum quis au&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:11:31'),
(12, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 17, '2025-08-18', '2025-08-24', '2025-08-19 08:13:07', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>17 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 16:</strong> Aug 19, 2025 at 8:11 AM<br>Reason: Assumenda a ipsa as<br>Medication: [{&quot;medicine&quot;:&quot;Mefinamic&quot;,&quot;dosage&quot;:&quot;Quo pariatur Labori&quot;,&quot;quantity&quot;:&quot;30&quot;,&quot;frequency&quot;:&quot;Delectus enim cumqu&quot;,&quot;instructions&quot;:&quot;Dolor illum quis au&quot;}]<br><br><strong>Visit 17:</strong> Aug 19, 2025 at 8:13 AM<br>Reason: Repudiandae unde lau<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Voluptas error Nam m&quot;,&quot;quantity&quot;:&quot;84&quot;,&quot;frequency&quot;:&quot;Inventore modi aliqu&quot;,&quot;instructions&quot;:&quot;Neque et rerum dolor&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:13:07'),
(13, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 17, '2025-08-18', '2025-08-24', '2025-08-19 08:27:57', 'sent', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abella, Joseph B.</strong>, has received medication from the clinic <strong>17 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 1:45 AM<br>Reason: Fever<br>Medication: [{&quot;medicine&quot;:&quot;Neozep&quot;,&quot;dosage&quot;:&quot;Quo odit ipsa in ea&quot;,&quot;quantity&quot;:&quot;38&quot;,&quot;frequency&quot;:&quot;Cum numquam in ea ex&quot;,&quot;instructions&quot;:&quot;Nesciunt labore dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 18, 2025 at 7:17 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 3:</strong> Aug 18, 2025 at 7:34 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 4:</strong> Aug 18, 2025 at 8:13 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 5:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 6:</strong> Aug 18, 2025 at 8:14 PM<br>Reason: asd<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;asd&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 7:</strong> Aug 18, 2025 at 8:33 PM<br>Reason: fevcer<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 8:</strong> Aug 18, 2025 at 8:49 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;asd&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 9:</strong> Aug 19, 2025 at 7:35 AM<br>Reason: Sakits ulo<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Mollitia qui facilis&quot;,&quot;quantity&quot;:&quot;11&quot;,&quot;frequency&quot;:&quot;Iste explicabo Rem &quot;,&quot;instructions&quot;:&quot;Natus quos harum sae&quot;}]<br><br><strong>Visit 10:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Qui occaecat magna v<br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Omnis velit dolor m&quot;,&quot;quantity&quot;:&quot;71&quot;,&quot;frequency&quot;:&quot;Consequat Itaque in&quot;,&quot;instructions&quot;:&quot;Quia rerum eveniet &quot;}]<br><br><strong>Visit 11:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Dolore consequuntur <br>Medication: [{&quot;medicine&quot;:&quot;Diatabs&quot;,&quot;dosage&quot;:&quot;Ut rerum quis cupida&quot;,&quot;quantity&quot;:&quot;75&quot;,&quot;frequency&quot;:&quot;Autem obcaecati ut c&quot;,&quot;instructions&quot;:&quot;Fugiat enim a dolor &quot;}]<br><br><strong>Visit 12:</strong> Aug 19, 2025 at 7:37 AM<br>Reason: Doloribus praesentiu<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Non laborum Digniss&quot;,&quot;quantity&quot;:&quot;29&quot;,&quot;frequency&quot;:&quot;Commodo rerum id co&quot;,&quot;instructions&quot;:&quot;Voluptatem Velit of&quot;}]<br><br><strong>Visit 13:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Recusandae Rerum te<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Quis porro ipsum co&quot;,&quot;quantity&quot;:&quot;42&quot;,&quot;frequency&quot;:&quot;Dolor aliquip nisi q&quot;,&quot;instructions&quot;:&quot;Sint atque ab incid&quot;}]<br><br><strong>Visit 14:</strong> Aug 19, 2025 at 7:38 AM<br>Reason: Enim qui eum asperio<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Iusto optio anim do&quot;,&quot;quantity&quot;:&quot;16&quot;,&quot;frequency&quot;:&quot;Qui qui deserunt ips&quot;,&quot;instructions&quot;:&quot;Voluptas officiis po&quot;}]<br><br><strong>Visit 15:</strong> Aug 19, 2025 at 7:40 AM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;1&quot;,&quot;frequency&quot;:&quot;asd&quot;,&quot;instructions&quot;:&quot;asd&quot;}]<br><br><strong>Visit 16:</strong> Aug 19, 2025 at 8:11 AM<br>Reason: Assumenda a ipsa as<br>Medication: [{&quot;medicine&quot;:&quot;Mefinamic&quot;,&quot;dosage&quot;:&quot;Quo pariatur Labori&quot;,&quot;quantity&quot;:&quot;30&quot;,&quot;frequency&quot;:&quot;Delectus enim cumqu&quot;,&quot;instructions&quot;:&quot;Dolor illum quis au&quot;}]<br><br><strong>Visit 17:</strong> Aug 19, 2025 at 8:13 AM<br>Reason: Repudiandae unde lau<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Voluptas error Nam m&quot;,&quot;quantity&quot;:&quot;84&quot;,&quot;frequency&quot;:&quot;Inventore modi aliqu&quot;,&quot;instructions&quot;:&quot;Neque et rerum dolor&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:27:57'),
(16, 22, 'Abellana, Vincent Anthony Q.', 'jaynujangad03@gmail.com', 3, '2025-08-18', '2025-08-24', '2025-08-19 08:43:03', 'sent', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Abellana, Vincent Anthony Q.</strong>, has received medication from the clinic <strong>3 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'><strong>Visit 1:</strong> Aug 18, 2025 at 7:15 PM<br>Reason: fever<br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;500mg&quot;,&quot;quantity&quot;:&quot;2&quot;,&quot;frequency&quot;:&quot;3x a day&quot;,&quot;instructions&quot;:&quot;after meals&quot;}]<br><br><strong>Visit 2:</strong> Aug 19, 2025 at 8:42 AM<br>Reason: Adipisci soluta est<br>Medication: [{&quot;medicine&quot;:&quot;Biogesic&quot;,&quot;dosage&quot;:&quot;Dolorum dolore nisi &quot;,&quot;quantity&quot;:&quot;59&quot;,&quot;frequency&quot;:&quot;Et vel adipisci quia&quot;,&quot;instructions&quot;:&quot;Placeat et consequa&quot;}]<br><br><strong>Visit 3:</strong> Aug 19, 2025 at 8:42 AM<br>Reason: Laboriosam culpa si<br>Medication: [{&quot;medicine&quot;:&quot;Rexidol&quot;,&quot;dosage&quot;:&quot;Consequuntur eos cu&quot;,&quot;quantity&quot;:&quot;72&quot;,&quot;frequency&quot;:&quot;Laboriosam consecte&quot;,&quot;instructions&quot;:&quot;Rerum aliquid non se&quot;}]<br><br></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:43:03'),
(17, 23, 'Abendan, Christian James A.', 'jaynujangad03@gmail.com', 3, '2025-08-18', '2025-08-24', '2025-08-19 08:58:35', 'sent', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff;\'>\r\n                <div style=\'background-color: #2563eb; color: white; padding: 20px; text-align: center;\'>\r\n                    <h1 style=\'margin: 0; font-size: 24px;\'>School Health Clinic</h1>\r\n                    <p style=\'margin: 5px 0 0 0; font-size: 16px;\'>Student Health Notification</p>\r\n                </div>\r\n                \r\n                <div style=\'padding: 30px 20px;\'>\r\n                    <p style=\'font-size: 16px; color: #333333; margin-bottom: 20px;\'>Dear Parent/Guardian,</p>\r\n                    \r\n                    <p style=\'font-size: 16px; color: #333333; line-height: 1.6;\'>\r\n                        We are writing to inform you that your child, <strong style=\'color: #2563eb;\'>Abendan, Christian James A.</strong>, \r\n                        has visited the school clinic for medication <strong>3 times</strong> during this week \r\n                        (Monday through Sunday).\r\n                    </p>\r\n                    \r\n                    <div style=\'background-color: #f8f9fa; padding: 20px; border-left: 4px solid #2563eb; margin: 25px 0;\'>\r\n                        <h3 style=\'margin-top: 0; color: #374151; font-size: 18px;\'>This Week\'s Clinic Visits:</h3>\r\n                        <div style=\'font-size: 14px; color: #555555;\'>\r\n                            <strong>Visit 1:</strong> Aug 19, 2025 at 8:58 AM<br>Reason: Corrupti debitis qu<br>Medication: [{&quot;medicine&quot;:&quot;Rexidol&quot;,&quot;dosage&quot;:&quot;Voluptas est quis mo&quot;,&quot;quantity&quot;:&quot;55&quot;,&quot;frequency&quot;:&quot;Incididunt laudantiu&quot;,&quot;instructions&quot;:&quot;Qui sint eum in dic&quot;}]<br><br><strong>Visit 2:</strong> Aug 19, 2025 at 8:58 AM<br>Reason: Sit assumenda quod <br>Medication: [{&quot;medicine&quot;:&quot;Mefinamic&quot;,&quot;dosage&quot;:&quot;Ex debitis architect&quot;,&quot;quantity&quot;:&quot;47&quot;,&quot;frequency&quot;:&quot;Quasi veritatis cupi&quot;,&quot;instructions&quot;:&quot;Harum laborum Repel&quot;}]<br><br><strong>Visit 3:</strong> Aug 19, 2025 at 8:58 AM<br>Reason: Maiores ex sint sed <br>Medication: [{&quot;medicine&quot;:&quot;Bioflu&quot;,&quot;dosage&quot;:&quot;Perferendis autem de&quot;,&quot;quantity&quot;:&quot;80&quot;,&quot;frequency&quot;:&quot;Porro dolor voluptas&quot;,&quot;instructions&quot;:&quot;Voluptates natus duc&quot;}]<br><br>\r\n                        </div>\r\n                    </div>\r\n                    \r\n                    <div style=\'background-color: #fff3cd; padding: 15px; border-radius: 6px; margin: 20px 0;\'>\r\n                        <h4 style=\'margin-top: 0; color: #856404;\'>???? Recommended Actions:</h4>\r\n                        <ul style=\'margin-bottom: 0; color: #856404;\'>\r\n                            <li>Monitor your child\'s health and wellbeing at home</li>\r\n                            <li>Contact our clinic if you have concerns about frequent visits</li>\r\n                            <li>Consider scheduling a consultation with your family doctor</li>\r\n                            <li>Review any patterns that might be causing recurring symptoms</li>\r\n                        </ul>\r\n                    </div>\r\n                    \r\n                    <p style=\'font-size: 16px; color: #333333; line-height: 1.6;\'>\r\n                        Multiple clinic visits in one week may indicate a health concern that requires attention. \r\n                        We encourage you to follow up with your child\'s healthcare provider if you have any concerns.\r\n                    </p>\r\n                    \r\n                    <p style=\'font-size: 16px; color: #333333; line-height: 1.6;\'>\r\n                        If you have any questions about your child\'s clinic visits, please don\'t hesitate to contact us.\r\n                    </p>\r\n                </div>\r\n                \r\n                <div style=\'background-color: #f8f9fa; padding: 20px; text-align: center; border-top: 1px solid #dee2e6;\'>\r\n                    <p style=\'margin: 0; font-size: 14px; color: #666666;\'>\r\n                        <strong>School Health Clinic</strong><br>\r\n                        This is an automated notification for your child\'s health and safety.<br>\r\n                        Please do not reply to this email. Contact the clinic directly for inquiries.\r\n                    </p>\r\n                </div>\r\n            </div>\r\n        ', 'Staff', '2025-08-19 08:58:35'),
(19, 31, 'Alicaya, Ralph Lorync D.', 'phennybert@gmail.com', 3, '2025-08-25', '2025-08-31', '2025-08-26 12:36:35', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Alicaya, Ralph Lorync D.</strong>, has received medication from the clinic <strong>3 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-26 12:36:35'),
(20, 31, 'Alicaya, Ralph Lorync D.', 'phennybert@gmail.com', 3, '2025-08-25', '2025-08-31', '2025-08-26 12:38:12', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Alicaya, Ralph Lorync D.</strong>, has received medication from the clinic <strong>3 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-26 12:38:12'),
(21, 31, 'Alicaya, Ralph Lorync D.', 'phennybert@gmail.com', 3, '2025-08-25', '2025-08-31', '2025-08-26 12:38:21', 'failed', '\r\n            <div style=\'font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;\'>\r\n                <h2 style=\'color: #2563eb;\'>Clinic Medication Visit Alert</h2>\r\n                <p>Dear Parent/Guardian,</p>\r\n                <p>We are writing to inform you that your child, <strong>Alicaya, Ralph Lorync D.</strong>, has received medication from the clinic <strong>3 times</strong> this week (Monday to Sunday).</p>\r\n                <div style=\'background-color: #f3f4f6; padding: 15px; border-radius: 8px; margin: 20px 0;\'>\r\n                    <h3 style=\'margin-top: 0; color: #374151;\'>Medication Visit Details This Week:</h3>\r\n                    <p style=\'margin-bottom: 0;\'></p>\r\n                </div>\r\n                <p>We recommend that you:</p>\r\n                <ul>\r\n                    <li>Check up on your child\'s health and wellbeing</li>\r\n                    <li>Contact the clinic if you have any concerns about the frequent medication needs</li>\r\n                    <li>Consider scheduling a consultation to discuss any ongoing health issues</li>\r\n                    <li>Review if there are any patterns or triggers that might be causing frequent visits</li>\r\n                </ul>\r\n                <p>Multiple medication visits in a week may indicate an underlying health concern that should be addressed.</p>\r\n                <p>If you have any questions or concerns, please don\'t hesitate to contact us.</p>\r\n                <p style=\'margin-top: 30px;\'>\r\n                    Best regards,<br>\r\n                    <strong>Clinic Management Team</strong>\r\n                </p>\r\n            </div>\r\n        ', 'Staff', '2025-08-26 12:38:21');

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
-- Table structure for table `patient_medical_info`
--

CREATE TABLE `patient_medical_info` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `emergency_contact_name` varchar(255) DEFAULT NULL,
  `emergency_contact_phone` varchar(50) DEFAULT NULL,
  `emergency_contact_relationship` varchar(100) DEFAULT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `insurance_provider` varchar(255) DEFAULT NULL,
  `insurance_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `parent_phone` varchar(32) DEFAULT NULL,
  `prescribed_by` varchar(255) DEFAULT NULL,
  `prescription_date` datetime DEFAULT current_timestamp(),
  `medicines` text DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pending_prescriptions`
--

INSERT INTO `pending_prescriptions` (`id`, `patient_id`, `patient_name`, `patient_email`, `parent_email`, `parent_phone`, `prescribed_by`, `prescription_date`, `medicines`, `reason`, `notes`) VALUES
(66, 21, 'Abella, Joseph B.', 'sawowadi@mailinator.com', 'bumimu@mailinator.com', NULL, 'Staff', '2025-06-01 12:41:11', '[{\"medicine\":\"diatabs\",\"dosage\":\"A illum aute pariat\",\"quantity\":\"78\",\"frequency\":\"Suscipit quia omnis \",\"instructions\":\"Aliquam ratione labo\"}]', NULL, 'Odio fugiat natus q'),
(67, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', NULL, 'Staff', '2025-06-01 12:45:34', '[]', NULL, 'just sleep'),
(68, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', NULL, 'Staff', '2025-06-01 12:45:35', '[]', NULL, 'just sleep'),
(69, 21, 'Abella, Joseph B.', 'xywuter@mailinator.com', 'kizoxyga@mailinator.com', NULL, 'Staff', '2025-06-01 12:45:56', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Dolorem quis volupta\",\"quantity\":\"1\",\"frequency\":\"Neque aliqua Id qua\",\"instructions\":\"Atque nostrud volupt\"}]', NULL, 'Velit do sit minima ');

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
  `parent_phone` varchar(32) DEFAULT NULL,
  `prescribed_by` varchar(255) DEFAULT NULL,
  `prescription_date` datetime DEFAULT current_timestamp(),
  `medicines` text DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_id`, `patient_name`, `patient_email`, `parent_email`, `parent_phone`, `prescribed_by`, `prescription_date`, `medicines`, `reason`, `notes`) VALUES
(3, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-17 06:43:11', '[{\"medicine\":\"rexidol\",\"dosage\":\"Voluptate consequatu\",\"quantity\":\"2\",\"frequency\":\"Facilis commodo ea m\",\"instructions\":\"Excepturi officiis e\"}]', NULL, 'Nostrum sunt reprehe'),
(4, 22, 'Abellana, Vincent Anthony Q.', NULL, NULL, NULL, 'Staff', '2025-05-17 07:48:15', '[{\"medicine\":\"rexidol\",\"dosage\":\"Eos quisquam ration\",\"quantity\":\"96\",\"frequency\":\"Et voluptate aute mo\",\"instructions\":\"Quae mollitia eum it\"}]', NULL, 'Cupidatat et ea amet'),
(5, 23, 'Abendan, Christian James A.', NULL, NULL, NULL, 'Staff', '2025-05-17 07:57:11', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Fugiat porro et qui\",\"quantity\":\"73\",\"frequency\":\"Officiis qui dolores\",\"instructions\":\"Voluptas et itaque e\"}]', NULL, 'Eaque quos officiis '),
(6, 26, 'Acidillo, Baby John V.', NULL, NULL, NULL, 'Staff', '2025-05-17 08:21:01', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Qui iste animi volu\",\"quantity\":\"20\",\"frequency\":\"Est esse cupiditat\",\"instructions\":\"Itaque sed vel incid\"}]', NULL, 'Et vel quia vitae pr'),
(7, 26, 'Acidillo, Baby John V.', NULL, NULL, NULL, 'Staff', '2025-05-18 01:11:11', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Eligendi quas cumque\",\"quantity\":\"6\",\"frequency\":\"Ut exercitation plac\",\"instructions\":\"Exercitation fugit \"}]', NULL, 'Assumenda nobis qui '),
(8, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-18 01:21:14', '[{\"medicine\":\"Excepturi qui in vit\",\"dosage\":\"Numquam non nesciunt\",\"quantity\":\"83\",\"frequency\":\"Aut qui nostrud aut \",\"instructions\":\"Architecto dolores n\"}]', NULL, 'Quia et omnis soluta'),
(9, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-18 20:13:42', '[{\"medicine\":\"Dolor eiusmod quidem\",\"dosage\":\"Consequatur culpa f\",\"quantity\":\"28\",\"frequency\":\"Illo fugiat accusam\",\"instructions\":\"Dicta nihil labore a\"}]', NULL, 'Necessitatibus aliqu'),
(10, 25, 'Abellana, Ariel L', NULL, NULL, NULL, 'Staff', '2025-05-19 02:17:16', '[{\"medicine\":\"mefinamic\",\"dosage\":\"Nihil sunt ut offic\",\"quantity\":\"84\",\"frequency\":\"Officiis quia asperi\",\"instructions\":\"Voluptatem aut enim\"}]', NULL, 'Quis adipisci eu bea'),
(11, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-19 03:16:33', '[{\"medicine\":\"Biogesics\",\"dosage\":\"Unde sit recusandae\",\"quantity\":\"2\",\"frequency\":\"Labore veniam iusto\",\"instructions\":\"Ex duis autem accusa\"}]', NULL, 'Ducimus aut sint pe'),
(12, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-19 21:30:46', '[{\"medicine\":\"rexidol\",\"dosage\":\"Non adipisci blandit\",\"quantity\":\"91\",\"frequency\":\"Nulla aliquip fuga \",\"instructions\":\"In eveniet accusant\"}]', NULL, 'Voluptatem ea sunt '),
(13, 23, 'Abendan, Christian James A.', NULL, NULL, NULL, 'Staff', '2025-05-19 22:11:18', '[{\"medicine\":\"Consectetur fugiat\",\"dosage\":\"Ea dolores qui autem\",\"quantity\":\"89\",\"frequency\":\"fgf\",\"instructions\":\"\"}]', NULL, 'Voluptatibus id labo'),
(14, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 02:16:52', '[{\"medicine\":\"mefinamic\",\"dosage\":\"Atque iusto Nam dese\",\"quantity\":\"14\",\"frequency\":\"Voluptas deserunt di\",\"instructions\":\"Voluptas ut expedita\"}]', NULL, 'Rerum enim sint aut '),
(15, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, NULL, 'Staff', '2025-05-20 22:06:41', '[{\"medicine\":\"Biogesics\",\"dosage\":\"500mg\",\"quantity\":\"19\",\"frequency\":\"3 times a day\",\"instructions\":\"After Meals\"}]', NULL, 'Ay sig ginahig ulo'),
(16, 36, 'Alferez Jr., Bernardino S.', NULL, NULL, NULL, 'Staff', '2025-05-20 23:27:28', '[{\"medicine\":\"Alaxan\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Diatabs\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(17, 36, 'Alferez Jr., Bernardino S.', NULL, NULL, NULL, 'Staff', '2025-05-20 23:27:28', '[{\"medicine\":\"Alaxan\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Diatabs\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(18, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 23:27:56', '[{\"medicine\":\"Alaxan\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(19, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 23:27:56', '[{\"medicine\":\"Alaxan\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"},{\"medicine\":\"Biogesic\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, 'asd'),
(20, 30, 'Aguilar, Jaymar C', NULL, NULL, NULL, 'Staff', '2025-05-20 23:28:07', '[{\"medicine\":\"rexidol\",\"dosage\":\"Ut dolor aut non nul\",\"quantity\":\"1\",\"frequency\":\"Facere ipsum autem \",\"instructions\":\"Odio ullam nihil qua\"},{\"medicine\":\"Laurel Dejesus\",\"dosage\":\"Est mollit eos esse\",\"quantity\":\"1\",\"frequency\":\"Ut in maiores ad fug\",\"instructions\":\"Maxime fugit accusa\"}]', NULL, 'Recusandae Odio et '),
(21, 30, 'Aguilar, Jaymar C', NULL, NULL, NULL, 'Staff', '2025-05-20 23:28:07', '[{\"medicine\":\"rexidol\",\"dosage\":\"Ut dolor aut non nul\",\"quantity\":\"1\",\"frequency\":\"Facere ipsum autem \",\"instructions\":\"Odio ullam nihil qua\"},{\"medicine\":\"Laurel Dejesus\",\"dosage\":\"Est mollit eos esse\",\"quantity\":\"1\",\"frequency\":\"Ut in maiores ad fug\",\"instructions\":\"Maxime fugit accusa\"}]', NULL, 'Recusandae Odio et '),
(22, 29, 'Alegado, John Raymon B.', NULL, NULL, NULL, 'Staff', '2025-05-20 23:28:56', '[{\"medicine\":\"mefinamic\",\"dosage\":\"Id libero quia fuga\",\"quantity\":\"1\",\"frequency\":\"Hic est reiciendis v\",\"instructions\":\"Consectetur nulla m\"},{\"medicine\":\"Laurel Dejesus\",\"dosage\":\"Facere omnis velit \",\"quantity\":\"1\",\"frequency\":\"Qui perspiciatis pe\",\"instructions\":\"Qui aut quas dolor q\"}]', NULL, 'Animi non voluptate'),
(23, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 23:29:05', '[{\"medicine\":\"rexidol\",\"dosage\":\"Id sed qui itaque it\",\"quantity\":\"1\",\"frequency\":\"Eiusmod temporibus e\",\"instructions\":\"Omnis deleniti offic\"},{\"medicine\":\"Biogesics\",\"dosage\":\"Aliquam omnis enim a\",\"quantity\":\"1\",\"frequency\":\"Accusantium laboris \",\"instructions\":\"Hic sit cupidatat ve\"}]', NULL, 'Voluptatem consequat'),
(24, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, NULL, 'Staff', '2025-05-20 23:30:02', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Sequi illum rerum a\",\"instructions\":\"Voluptatem accusanti\"},{\"medicine\":\"Rexidol\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Consequat Omnis vol\",\"instructions\":\"Fugit consectetur \"},{\"medicine\":\"Diatabs\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Tempore voluptatibu\",\"instructions\":\"Illo et eos qui sunt\"}]', NULL, 'Adipisci esse do imp'),
(25, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, NULL, 'Staff', '2025-05-20 23:30:02', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Sequi illum rerum a\",\"instructions\":\"Voluptatem accusanti\"},{\"medicine\":\"Rexidol\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Consequat Omnis vol\",\"instructions\":\"Fugit consectetur \"},{\"medicine\":\"Diatabs\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Tempore voluptatibu\",\"instructions\":\"Illo et eos qui sunt\"}]', NULL, 'Adipisci esse do imp'),
(26, 40, 'Arcamo Jr., Emmanuel P.', NULL, NULL, NULL, 'Staff', '2025-05-20 23:30:08', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Sequi illum rerum a\",\"instructions\":\"Voluptatem accusanti\"},{\"medicine\":\"Rexidol\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Consequat Omnis vol\",\"instructions\":\"Fugit consectetur \"},{\"medicine\":\"Diatabs\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Tempore voluptatibu\",\"instructions\":\"Illo et eos qui sunt\"}]', NULL, 'Adipisci esse do imp'),
(27, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 23:30:17', '[{\"medicine\":\"Fugit voluptate bea\",\"dosage\":\"Magni consectetur al\",\"quantity\":\"6\",\"frequency\":\"Adipisicing quidem i\",\"instructions\":\"Accusamus itaque dol\"}]', NULL, 'Nemo minus voluptate'),
(28, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 23:30:17', '[{\"medicine\":\"Fugit voluptate bea\",\"dosage\":\"Magni consectetur al\",\"quantity\":\"6\",\"frequency\":\"Adipisicing quidem i\",\"instructions\":\"Accusamus itaque dol\"}]', NULL, 'Nemo minus voluptate'),
(29, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-20 23:30:21', '[{\"medicine\":\"rexidol\",\"dosage\":\"Possimus rerum rati\",\"quantity\":\"11\",\"frequency\":\"Non in quis quia arc\",\"instructions\":\"Voluptatem vero cons\"}]', NULL, 'Laborum vero natus a'),
(30, 30, 'Aguilar, Jaymar C', NULL, NULL, NULL, 'Staff', '2025-05-21 00:12:51', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(31, 30, 'Aguilar, Jaymar C', NULL, NULL, NULL, 'Staff', '2025-05-21 00:12:51', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(32, 30, 'Aguilar, Jaymar C', NULL, NULL, NULL, 'Staff', '2025-05-21 00:13:19', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(33, 30, 'Aguilar, Jaymar C', NULL, NULL, NULL, 'Staff', '2025-05-21 00:13:19', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consequatur maiores\",\"quantity\":\"1\",\"frequency\":\"Unde eius praesentiu\",\"instructions\":\"Aut consequuntur quo\"}]', NULL, 'Sint sint in reici'),
(34, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 00:13:27', '[{\"medicine\":\"Neozep\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"Ullam earum omnis in\",\"instructions\":\"Laborum Aperiam eaq\"}]', NULL, 'Et sit ad aut alias'),
(35, 24, 'Abendan, Nino Rashean T.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:16:48', '[{\"medicine\":\"rexidol\",\"dosage\":\"Neque vero aut sed n\",\"quantity\":\"41\",\"frequency\":\"Magni nostrum ex qui\",\"instructions\":\"Dignissimos illo obc\"}]', NULL, 'Aut ex facilis velit'),
(36, 24, 'Abendan, Nino Rashean T.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:16:48', '[{\"medicine\":\"rexidol\",\"dosage\":\"Neque vero aut sed n\",\"quantity\":\"41\",\"frequency\":\"Magni nostrum ex qui\",\"instructions\":\"Dignissimos illo obc\"}]', NULL, 'Aut ex facilis velit'),
(37, 24, 'Abendan, Nino Rashean T.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:17:17', '[{\"medicine\":\"mefinamic\",\"dosage\":\"In aliqua Tempore \",\"quantity\":\"63\",\"frequency\":\"Cillum culpa dolor \",\"instructions\":\"Consequuntur dolorum\"}]', NULL, 'Laboriosam Nam ut a'),
(38, 24, 'Abendan, Nino Rashean T.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:17:17', '[{\"medicine\":\"mefinamic\",\"dosage\":\"In aliqua Tempore \",\"quantity\":\"63\",\"frequency\":\"Cillum culpa dolor \",\"instructions\":\"Consequuntur dolorum\"}]', NULL, 'Laboriosam Nam ut a'),
(39, 27, 'Adona, Carl Macel C.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:19:05', '[{\"medicine\":\"rexidol\",\"dosage\":\"Sit esse sunt qui i\",\"quantity\":\"64\",\"frequency\":\"Excepteur autem illo\",\"instructions\":\"Assumenda mollit deb\"}]', NULL, 'Vel provident atque'),
(40, 27, 'Adona, Carl Macel C.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:19:06', '[{\"medicine\":\"rexidol\",\"dosage\":\"Sit esse sunt qui i\",\"quantity\":\"64\",\"frequency\":\"Excepteur autem illo\",\"instructions\":\"Assumenda mollit deb\"}]', NULL, 'Vel provident atque'),
(41, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 00:19:10', '[{\"medicine\":\"Fugit voluptate bea\",\"dosage\":\"Vel delectus volupt\",\"quantity\":\"57\",\"frequency\":\"Modi et tempor sit \",\"instructions\":\"Consequatur consequa\"}]', NULL, 'Magni nemo autem com'),
(42, 24, 'Abendan, Nino Rashean T.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:20:13', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Unde recusandae Ut \",\"quantity\":\"1\",\"frequency\":\"Eius dolorem anim re\",\"instructions\":\"Unde omnis ut sed od\"}]', NULL, 'Natus quo deserunt a'),
(43, 24, 'Abendan, Nino Rashean T.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:20:13', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Unde recusandae Ut \",\"quantity\":\"1\",\"frequency\":\"Eius dolorem anim re\",\"instructions\":\"Unde omnis ut sed od\"}]', NULL, 'Natus quo deserunt a'),
(44, 23, 'Abendan, Christian James A.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:20:59', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Dolor voluptatem Sa\",\"quantity\":\"1\",\"frequency\":\"Aut nisi reiciendis \",\"instructions\":\"Enim ipsum consectet\"}]', NULL, 'Et dolorum nisi aut '),
(45, 22, 'Abellana, Vincent Anthony Q.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:21:25', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consectetur pariatur\",\"quantity\":\"1\",\"frequency\":\"Eos iure voluptatem\",\"instructions\":\"Consequat Sapiente \"}]', NULL, 'Accusamus irure ipsu'),
(46, 22, 'Abellana, Vincent Anthony Q.', NULL, NULL, NULL, 'Staff', '2025-05-21 00:21:25', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Consectetur pariatur\",\"quantity\":\"1\",\"frequency\":\"Eos iure voluptatem\",\"instructions\":\"Consequat Sapiente \"}]', NULL, 'Accusamus irure ipsu'),
(47, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 00:23:24', '[{\"medicine\":\"Neozep\",\"dosage\":\"Placeat sint irure \",\"quantity\":\"1\",\"frequency\":\"Placeat obcaecati r\",\"instructions\":\"Pariatur Totam eum \"}]', NULL, 'Vel ducimus molliti'),
(48, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 00:23:24', '[{\"medicine\":\"Neozep\",\"dosage\":\"Placeat sint irure \",\"quantity\":\"1\",\"frequency\":\"Placeat obcaecati r\",\"instructions\":\"Pariatur Totam eum \"}]', NULL, 'Vel ducimus molliti'),
(49, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 00:32:16', '[{\"medicine\":\"Neozep\",\"dosage\":\"Numquam earum labore\",\"quantity\":\"1\",\"frequency\":\"Est quia dolor accus\",\"instructions\":\"Quia vel aut nostrum\"}]', NULL, 'Dolorum natus dolori'),
(50, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 00:37:17', '[{\"medicine\":\"Alaxan\",\"dosage\":\"Sunt illum sed duc\",\"quantity\":\"1\",\"frequency\":\"Velit officiis duis\",\"instructions\":\"Commodo nesciunt pe\"}]', NULL, 'Ut neque impedit et'),
(51, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 05:35:49', '[{\"medicine\":\"Neozep\",\"dosage\":\"Laborum Ipsam est \",\"quantity\":\"62\",\"frequency\":\"Totam vero et et nec\",\"instructions\":\"Obcaecati non doloru\"}]', NULL, 'Placeat distinctio'),
(52, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 05:35:53', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Aut eaque incidunt \",\"quantity\":\"45\",\"frequency\":\"Ullam voluptates tem\",\"instructions\":\"Ex ea laborum cumque\"}]', NULL, 'Dolor irure reprehen'),
(53, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 05:36:04', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Voluptates ea quas s\",\"quantity\":\"55\",\"frequency\":\"Quas eveniet obcaec\",\"instructions\":\"Consequatur Aliquip\"}]', NULL, 'Ex esse illo beatae'),
(54, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:05:18', '[{\"medicine\":\"Alaxan\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', NULL, 'asd'),
(55, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:05:51', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Consequuntur dolore \",\"quantity\":\"1\",\"frequency\":\"Consectetur pariatur\",\"instructions\":\"Laborum Est tempora\"}]', NULL, 'Do tempore ut minus'),
(56, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:06:18', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Voluptas pariatur Q\",\"quantity\":\"1\",\"frequency\":\"Est ut sed dolore a \",\"instructions\":\"Odit sunt voluptate\"}]', NULL, 'Quos non quis ad dol'),
(57, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:08:31', '[{\"medicine\":\"Neozep\",\"dosage\":\"Delectus quo cum si\",\"quantity\":\"1\",\"frequency\":\"Odit commodi provide\",\"instructions\":\"Omnis irure maiores \"}]', NULL, 'Maiores voluptatem e'),
(58, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:09:11', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Eos quas qui aut in\",\"quantity\":\"1\",\"frequency\":\"Quia eos elit pari\",\"instructions\":\"Anim iusto ducimus \"}]', NULL, 'Consectetur dolorum '),
(59, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:09:14', '[{\"medicine\":\"Neozep\",\"dosage\":\"Labore officia ut su\",\"quantity\":\"1\",\"frequency\":\"Tempor consequatur \",\"instructions\":\"Aut enim commodo qui\"}]', NULL, 'Illum ullam et fuga'),
(60, 21, 'Abella, Joseph B.', NULL, 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-21 06:12:10', '[{\"medicine\":\"Neozep\",\"dosage\":\"Labore officia ut su\",\"quantity\":\"1\",\"frequency\":\"Tempor consequatur \",\"instructions\":\"Aut enim commodo qui\"}]', NULL, 'Illum ullam et fuga'),
(61, 22, 'Abellana, Vincent Anthony Q.', 'sellonmeow@gmail.com', 'kimxyzian@gmail.com', NULL, 'Staff', '2025-05-21 06:24:53', '[{\"medicine\":\"Neozep\",\"dosage\":\"Officiis quo reprehe\",\"quantity\":\"1\",\"frequency\":\"Aut sit nostrud cons\",\"instructions\":\"Possimus minim ab o\"}]', NULL, 'Fugiat eaque aspern'),
(62, 22, 'Abellana, Vincent Anthony Q.', 'sellonmeow@gmail.com', 'kimxyzian@gmail.com', NULL, 'Staff', '2025-05-21 06:25:15', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Officiis dolor repre\",\"quantity\":\"9\",\"frequency\":\"Adipisci sed qui qua\",\"instructions\":\"Ut voluptas cupidita\"}]', NULL, 'Facere odio qui poss'),
(63, 21, 'Abella, Joseph B.', NULL, NULL, NULL, 'Staff', '2025-05-21 22:53:53', '[{\"medicine\":\"Neozep\",\"dosage\":\"Itaque dignissimos d\",\"quantity\":\"15\",\"frequency\":\"Qui sit assumenda co\",\"instructions\":\"Adipisci perferendis\"}]', NULL, 'Sed ducimus maiores'),
(64, 21, 'Abella, Joseph B.', 'waryku@mailinator.com', 'wefyzineha@mailinator.com', NULL, 'Staff', '2025-05-22 00:47:47', '[{\"medicine\":\"Neozep\",\"dosage\":\"Sed accusantium qui \",\"quantity\":\"57\",\"frequency\":\"Mollit praesentium q\",\"instructions\":\"Et non voluptas volu\"}]', NULL, 'Quo magna explicabo'),
(65, 22, 'Abellana, Vincent Anthony Q.', 'sellonmeow@gmail.com', 'kimxyzian@gmail.com', NULL, 'Staff', '2025-05-22 00:48:02', '[{\"medicine\":\"Kremil-S\",\"dosage\":\"Dolor deserunt obcae\",\"quantity\":\"9\",\"frequency\":\"Ullamco ex voluptate\",\"instructions\":\"Hic consequuntur qui\"}]', NULL, 'Doloremque et mollit'),
(66, 26, 'Acidillo, Baby John V.', 'dybepedehy@mailinator.com', 'jijiplays991@mailinator.com', NULL, 'Staff', '2025-05-22 01:28:54', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Quas illo corrupti \",\"quantity\":\"65\",\"frequency\":\"Doloremque cupidatat\",\"instructions\":\"Sed enim sed laudant\"}]', NULL, 'Voluptatem magnam e'),
(67, 26, 'Acidillo, Baby John V.', 'dumuqexe@mailinator.com', 'jijiplays991@mailinator.com', NULL, 'Staff', '2025-05-22 01:28:55', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Vero totam nihil occ\",\"quantity\":\"60\",\"frequency\":\"Velit dolore dolorib\",\"instructions\":\"Ex quis exercitation\"}]', NULL, 'Quis adipisicing vol'),
(68, 26, 'Acidillo, Baby John V.', 'decyj@mailinator.com', 'jijiplays991@mailinator.com', NULL, 'Staff', '2025-05-22 01:28:55', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Earum voluptate vel \",\"quantity\":\"59\",\"frequency\":\"Molestias reprehende\",\"instructions\":\"Reprehenderit esse\"}]', NULL, 'Delectus qui est no'),
(69, 21, 'Abella, Joseph B.', 'kavob@mailinator.com', 'nygyqu@mailinator.com', NULL, 'Staff', '2025-05-22 01:29:17', '[{\"medicine\":\"Atay\",\"dosage\":\"Incidunt adipisci e\",\"quantity\":\"57\",\"frequency\":\"Officia voluptas aut\",\"instructions\":\"Deleniti occaecat re\"}]', NULL, 'In eiusmod explicabo'),
(70, 30, 'Aguilar, Jaymar C', 'hujinymeci@mailinator.com', 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-22 01:31:37', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Aperiam fugiat inven\",\"quantity\":\"59\",\"frequency\":\"Totam animi cupidat\",\"instructions\":\"Culpa sint sunt aut\"}]', NULL, 'Amet sapiente tempo'),
(71, 30, 'Aguilar, Jaymar C', 'vyqysykote@mailinator.com', 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-22 01:31:40', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Rerum molestiae qui \",\"quantity\":\"86\",\"frequency\":\"Proident est assume\",\"instructions\":\"Aspernatur irure sed\"}]', NULL, 'Qui qui sunt et sap'),
(72, 30, 'Aguilar, Jaymar C', 'jasury@mailinator.com', 'jijiplays991@gmail.com', NULL, 'Staff', '2025-05-22 01:31:42', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Commodo aut necessit\",\"quantity\":\"34\",\"frequency\":\"Earum sunt minus nih\",\"instructions\":\"Ea consequatur Veli\"}]', NULL, 'Tempore vitae modi '),
(73, 40, 'Arcamo Jr., Emmanuel P.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', NULL, 'Staff', '2025-06-01 11:57:30', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"After meal\"}]', NULL, 'Imna na ha'),
(74, 40, 'Arcamo Jr., Emmanuel P.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', NULL, 'Staff', '2025-06-01 11:59:32', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"After Meal\"}]', NULL, 'imna na waa ka'),
(75, 40, 'Arcamo Jr., Emmanuel P.', 'cedricjade13@gmail.com', 'clydegetuaban@gmail.com', NULL, 'Staff', '2025-06-01 11:59:35', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"After Meal\"}]', NULL, 'Imna na inatay na'),
(76, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', NULL, 'Staff', '2025-08-10 18:32:47', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', NULL, ''),
(77, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', NULL, 'Staff', '2025-08-10 18:33:56', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, ''),
(78, 21, 'Abella, Joseph B.', 'cedricjade13@gmail.com', 'cedricjade13@gmail.com', NULL, 'Staff', '2025-08-10 18:38:43', '[{\"medicine\":\"Biogesic\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', NULL, ''),
(79, 21, 'Abella, Joseph B.', 'qysupikame@mailinator.com', 'fusego@mailinator.com', NULL, 'Staff', '2025-08-17 00:12:58', '[{\"medicine\":\"Neozep\",\"dosage\":\"Accusantium est fuga\",\"quantity\":\"13\",\"frequency\":\"Ut quasi nisi sunt i\",\"instructions\":\"Quasi libero illo se\"}]', NULL, 'Sit deserunt in quam'),
(80, 22, 'Abellana, Vincent Anthony Q.', 'jykibonif@mailinator.com', 'nelucu@mailinator.com', NULL, 'Staff', '2025-08-17 00:16:37', '[{\"medicine\":\"Biogesic\",\"dosage\":\"In voluptas deserunt\",\"quantity\":\"46\",\"frequency\":\"Consequatur do offic\",\"instructions\":\"Soluta autem reprehe\"}]', NULL, 'Reprehenderit minus'),
(81, 22, 'Abellana, Vincent Anthony Q.', 'jykibonif@mailinator.com', 'nelucu@mailinator.com', NULL, 'Staff', '2025-08-17 00:16:43', '[{\"medicine\":\"Biogesic\",\"dosage\":\"In voluptas deserunt\",\"quantity\":\"46\",\"frequency\":\"Consequatur do offic\",\"instructions\":\"Soluta autem reprehe\"}]', NULL, 'Reprehenderit minus'),
(82, 22, 'Abellana, Vincent Anthony Q.', 'jykibonif@mailinator.com', 'nelucu@mailinator.com', NULL, 'Staff', '2025-08-17 00:16:44', '[{\"medicine\":\"Biogesic\",\"dosage\":\"In voluptas deserunt\",\"quantity\":\"46\",\"frequency\":\"Consequatur do offic\",\"instructions\":\"Soluta autem reprehe\"}]', NULL, 'Reprehenderit minus'),
(83, 23, 'Abendan, Christian James A.', 'xyzamu@mailinator.com', 'hizakop@mailinator.com', NULL, 'Staff', '2025-08-17 00:27:38', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Velit Nam odit dele\",\"quantity\":\"23\",\"frequency\":\"Id officia ad in qu\",\"instructions\":\"Odit voluptas nostru\"}]', NULL, 'Ut omnis magna irure'),
(84, 24, 'Abendan, Nino Rashean T.', 'bypo@mailinator.com', 'qahuv@mailinator.com', NULL, 'Staff', '2025-08-17 00:29:27', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Eiusmod asperiores m\",\"quantity\":\"48\",\"frequency\":\"Itaque mollit in dol\",\"instructions\":\"Aliquid ea quia iste\"}]', NULL, 'Recusandae Magnam h'),
(85, 24, 'Abendan, Nino Rashean T.', 'bypo@mailinator.com', 'qahuv@mailinator.com', NULL, 'Staff', '2025-08-17 00:29:34', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Eiusmod asperiores m\",\"quantity\":\"48\",\"frequency\":\"Itaque mollit in dol\",\"instructions\":\"Aliquid ea quia iste\"}]', NULL, 'Recusandae Magnam h'),
(86, 24, 'Abendan, Nino Rashean T.', 'bypo@mailinator.com', 'bypo@mailinator.com', NULL, 'Staff', '2025-08-17 00:29:38', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Eiusmod asperiores m\",\"quantity\":\"48\",\"frequency\":\"Itaque mollit in dol\",\"instructions\":\"Aliquid ea quia iste\"}]', NULL, 'Recusandae Magnam h'),
(87, 26, 'Acidillo, Baby John V.', 'fawyjyxesu@mailinator.com', 'saleg@mailinator.com', NULL, 'Staff', '2025-08-17 00:31:00', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Non ipsa magna culp\",\"quantity\":\"96\",\"frequency\":\"Ducimus quia commod\",\"instructions\":\"Voluptas et consequa\"}]', NULL, 'Rem ipsa non volupt'),
(88, 27, 'Adona, Carl Macel C.', 'haqupo@mailinator.com', 'vakuqaluz@mailinator.com', NULL, 'Staff', '2025-08-17 00:34:02', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Dolor est minim culp\",\"quantity\":\"4\",\"frequency\":\"Cumque eius eaque qu\",\"instructions\":\"Nihil reprehenderit\"}]', NULL, 'Nihil id rerum labor'),
(89, 26, 'Acidillo, Baby John V.', 'naja@mailinator.com', 'wunuhig@mailinator.com', NULL, 'Staff', '2025-08-17 00:48:49', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Ea quam voluptatibus\",\"quantity\":\"66\",\"frequency\":\"Quasi tempore molli\",\"instructions\":\"Sapiente doloremque \"}]', NULL, 'Doloribus odit quia '),
(90, 22, 'Abellana, Vincent Anthony Q.', 'hezy@mailinator.com', 'haputec@mailinator.com', NULL, 'Staff', '2025-08-17 00:51:40', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Do exercitationem mi\",\"quantity\":\"40\",\"frequency\":\"Consequatur vel et v\",\"instructions\":\"Dolore cillum nihil \"}]', NULL, 'Aut pariatur Facere'),
(91, 25, 'Abellana, Ariel L', 'bixotehel@mailinator.com', 'fejukatur@mailinator.com', NULL, 'Staff', '2025-08-17 00:52:39', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Quo nihil nisi in sa\",\"quantity\":\"69\",\"frequency\":\"Nihil ipsum aliqua \",\"instructions\":\"Qui iste natus bland\"}]', NULL, 'Rem et vel quia et e'),
(92, 22, 'Abellana, Vincent Anthony Q.', 'jule@mailinator.com', 'dybawaxoga@mailinator.com', NULL, 'Staff', '2025-08-17 00:53:20', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Fugiat debitis aut e\",\"quantity\":\"48\",\"frequency\":\"Aliquid distinctio \",\"instructions\":\"Ipsam ipsum ut delec\"}]', NULL, 'Ipsum culpa sequi cu'),
(93, 30, 'Aguilar, Jaymar C', 'dynymaq@mailinator.com', 'roxivyzopy@mailinator.com', NULL, 'Staff', '2025-08-17 01:12:04', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Quis porro ratione e\",\"quantity\":\"85\",\"frequency\":\"In explicabo Optio\",\"instructions\":\"Nam voluptatum aut p\"}]', NULL, 'Rerum ea qui et expe'),
(94, 23, 'Abendan, Christian James A.', 'salaqy@mailinator.com', 'kukusohak@mailinator.com', NULL, 'Staff', '2025-08-17 12:10:37', '[{\"medicine\":\"Neozep\",\"dosage\":\"Similique nobis iste\",\"quantity\":\"82\",\"frequency\":\"Molestias vel pariat\",\"instructions\":\"Ea et aute voluptas \"}]', NULL, 'Quia laborum sunt qu'),
(95, 21, 'Abella, Joseph B.', 'sytujon@mailinator.com', 'zawuhiqe@mailinator.com', NULL, 'Staff', '2025-08-18 01:45:27', '[{\"medicine\":\"Neozep\",\"dosage\":\"Quo odit ipsa in ea\",\"quantity\":\"38\",\"frequency\":\"Cum numquam in ea ex\",\"instructions\":\"Nesciunt labore dic\"}]', 'Fever', 'Odio laudantium a q'),
(96, 22, 'Abellana, Vincent Anthony Q.', 'asd@gmail.com', 'asdsa@gmail.com', NULL, 'Staff', '2025-08-18 19:15:42', '[{\"medicine\":\"Bioflu\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', 'fever', ''),
(97, 21, 'Abella, Joseph B.', 'asd@gmail.com', 'asdsa@gmail.com', NULL, 'Staff', '2025-08-18 19:17:39', '[{\"medicine\":\"Bioflu\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', 'fever', ''),
(98, 21, 'Abella, Joseph B.', 'ceasda@gmail.com', 'asdsa@gmail.com', NULL, 'Staff', '2025-08-18 19:34:15', '[{\"medicine\":\"Bioflu\",\"dosage\":\"500mg\",\"quantity\":\"2\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', 'fever', ''),
(99, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:50:22', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(100, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:50:26', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(101, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:50:42', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(102, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:51:05', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(103, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:51:07', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(104, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:51:17', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(105, 1, 'Test Student', NULL, NULL, NULL, 'Nurse Jane', '2025-08-18 19:53:14', 'Paracetamol, Vitamin C', 'Fever and headache', NULL),
(106, 1, 'Test Student', NULL, 'parent@example.com', NULL, 'Staff', '2025-08-18 20:10:54', '[{\"medicine\":\"Paracetamol\",\"quantity\":\"2\"}]', 'Fever and headache', NULL),
(107, 21, 'Test Student', NULL, 'parent@example.com', NULL, 'Staff', '2025-08-18 20:13:07', '[{\"medicine\":\"Paracetamol\",\"quantity\":\"2\"}]', 'Fever and headache', NULL),
(108, 21, 'Abella, Joseph B.', 'asd@gmail.com', 'asdsa@gmail.com', NULL, 'Staff', '2025-08-18 20:13:41', '[{\"medicine\":\"asd\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'asd', ''),
(109, 21, 'Abella, Joseph B.', 'asd@gmail.com', 'asdsa@gmail.com', NULL, 'Staff', '2025-08-18 20:14:00', '[{\"medicine\":\"asd\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'asd', ''),
(110, 21, 'Abella, Joseph B.', 'xaxil@mailinator.com', 'ticaqaxi@mailinator.com', NULL, 'Staff', '2025-08-18 20:14:16', '[{\"medicine\":\"asd\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'asd', ''),
(111, 21, 'Abella, Joseph B.', 'sellonmeow@gmail.com', 'cedricjade13@gmail.com', NULL, 'Staff', '2025-08-18 20:33:09', '[{\"medicine\":\"Bioflu\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"3x a day\",\"instructions\":\"after meals\"}]', 'fevcer', ''),
(112, 21, 'Abella, Joseph B.', 'dumuqexe@mailinator.com', 'jaynu13@gmail.com', NULL, 'Staff', '2025-08-18 20:49:06', '[{\"medicine\":\"asd\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'fever', ''),
(113, 21, 'Abella, Joseph B.', 'synysin@mailinator.com', 'fyxaj@mailinator.com', NULL, 'Staff', '2025-08-19 07:35:33', '[{\"medicine\":\"Bioflu\",\"dosage\":\"Mollitia qui facilis\",\"quantity\":\"11\",\"frequency\":\"Iste explicabo Rem \",\"instructions\":\"Natus quos harum sae\"}]', 'Sakits ulo', 'Harum magni dignissi'),
(114, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'fawajuzih@mailinator.com', NULL, 'Staff', '2025-08-19 07:37:15', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Omnis velit dolor m\",\"quantity\":\"71\",\"frequency\":\"Consequat Itaque in\",\"instructions\":\"Quia rerum eveniet \"}]', 'Qui occaecat magna v', 'Et ex ipsum aut eve'),
(115, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'cyxi@mailinator.com', NULL, 'Staff', '2025-08-19 07:37:31', '[{\"medicine\":\"Diatabs\",\"dosage\":\"Ut rerum quis cupida\",\"quantity\":\"75\",\"frequency\":\"Autem obcaecati ut c\",\"instructions\":\"Fugiat enim a dolor \"}]', 'Dolore consequuntur ', 'Aut voluptatem ipsum'),
(116, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'marif@mailinator.com', NULL, 'Staff', '2025-08-19 07:37:49', '[{\"medicine\":\"Bioflu\",\"dosage\":\"Non laborum Digniss\",\"quantity\":\"29\",\"frequency\":\"Commodo rerum id co\",\"instructions\":\"Voluptatem Velit of\"}]', 'Doloribus praesentiu', 'Ipsa quos pariatur'),
(117, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'nuremoj@mailinator.com', NULL, 'Staff', '2025-08-19 07:38:02', '[{\"medicine\":\"Bioflu\",\"dosage\":\"Quis porro ipsum co\",\"quantity\":\"42\",\"frequency\":\"Dolor aliquip nisi q\",\"instructions\":\"Sint atque ab incid\"}]', 'Recusandae Rerum te', 'Deserunt iste at sun'),
(118, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'wenesuxi@mailinator.com', NULL, 'Staff', '2025-08-19 07:38:16', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Iusto optio anim do\",\"quantity\":\"16\",\"frequency\":\"Qui qui deserunt ips\",\"instructions\":\"Voluptas officiis po\"}]', 'Enim qui eum asperio', 'Error expedita quis '),
(119, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 07:40:10', '[{\"medicine\":\"Bioflu\",\"dosage\":\"500mg\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'fever', ''),
(120, 21, 'Abella, Joseph B.', 'kylozugopu@mailinator.com', 'sicecyre@mailinator.com', NULL, 'Staff', '2025-08-19 08:11:22', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Quo pariatur Labori\",\"quantity\":\"30\",\"frequency\":\"Delectus enim cumqu\",\"instructions\":\"Dolor illum quis au\"}]', 'Assumenda a ipsa as', 'Quo non lorem labori'),
(121, 21, 'Abella, Joseph B.', 'jaynujangad03@gmail.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:13:01', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Voluptas error Nam m\",\"quantity\":\"84\",\"frequency\":\"Inventore modi aliqu\",\"instructions\":\"Neque et rerum dolor\"}]', 'Repudiandae unde lau', 'Molestiae nobis ut n'),
(122, 21, 'Abella, Joseph B.', 'duwyho@mailinator.com', 'nukixygez@mailinator.com', NULL, 'Staff', '2025-08-19 08:41:54', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Voluptas ullam conse\",\"quantity\":\"1\",\"frequency\":\"Consequatur Sequi o\",\"instructions\":\"In do animi soluta \"}]', 'Ab nihil alias accus', 'Rerum quos rerum qui'),
(123, 22, 'Abellana, Vincent Anthony Q.', 'cupatijyja@mailinator.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:42:17', '[{\"medicine\":\"Biogesic\",\"dosage\":\"Dolorum dolore nisi \",\"quantity\":\"59\",\"frequency\":\"Et vel adipisci quia\",\"instructions\":\"Placeat et consequa\"}]', 'Adipisci soluta est', 'Aut saepe alias blan'),
(124, 21, 'Abella, Joseph B.', 'paxipywo@mailinator.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:42:29', '[{\"medicine\":\"asd\",\"dosage\":\"Praesentium eiusmod \",\"quantity\":\"4\",\"frequency\":\"Magna porro et molli\",\"instructions\":\"Iste dignissimos vol\"}]', 'Neque provident sae', 'Recusandae Nisi est'),
(125, 22, 'Abellana, Vincent Anthony Q.', 'toqufyjylu@mailinator.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:42:53', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Consequuntur eos cu\",\"quantity\":\"72\",\"frequency\":\"Laboriosam consecte\",\"instructions\":\"Rerum aliquid non se\"}]', 'Laboriosam culpa si', 'Aut rerum aute ex ut'),
(126, 23, 'Abendan, Christian James A.', 'duzydy@mailinator.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:58:05', '[{\"medicine\":\"Rexidol\",\"dosage\":\"Voluptas est quis mo\",\"quantity\":\"55\",\"frequency\":\"Incididunt laudantiu\",\"instructions\":\"Qui sint eum in dic\"}]', 'Corrupti debitis qu', 'Do ut nemo excepturi'),
(127, 23, 'Abendan, Christian James A.', 'zuhatule@mailinator.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:58:17', '[{\"medicine\":\"Mefinamic\",\"dosage\":\"Ex debitis architect\",\"quantity\":\"47\",\"frequency\":\"Quasi veritatis cupi\",\"instructions\":\"Harum laborum Repel\"}]', 'Sit assumenda quod ', 'Nihil minus atque la'),
(128, 23, 'Abendan, Christian James A.', 'mejugituh@mailinator.com', 'jaynujangad03@gmail.com', NULL, 'Staff', '2025-08-19 08:58:28', '[{\"medicine\":\"Bioflu\",\"dosage\":\"Perferendis autem de\",\"quantity\":\"80\",\"frequency\":\"Porro dolor voluptas\",\"instructions\":\"Voluptates natus duc\"}]', 'Maiores ex sint sed ', 'Mollitia veritatis o'),
(129, 31, 'Alicaya, Ralph Lorync D.', 'cedricjade13@gmail.com', 'phennybert@gmail.com', NULL, 'Staff', '2025-08-26 10:58:25', '[{\"medicine\":\"asd\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'asd', 'asd'),
(130, 31, 'Alicaya, Ralph Lorync D.', 'cedricjade13@gmail.com', 'phennybert@gmail.com', NULL, 'Staff', '2025-08-26 10:58:41', '[{\"medicine\":\"asd\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asdasd\",\"instructions\":\"asd\"}]', 'asd', 'asd'),
(131, 31, 'Alicaya, Ralph Lorync D.', 'cedricjade13@gmail.com', 'phennybert@gmail.com', NULL, 'Staff', '2025-08-26 10:58:57', '[{\"medicine\":\"asd\",\"dosage\":\"asd\",\"quantity\":\"1\",\"frequency\":\"asd\",\"instructions\":\"asd\"}]', 'asd', 'asd');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(50) NOT NULL,
  `summary` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `date`, `type`, `summary`, `created_at`) VALUES
(1, '2025-08-22', 'Visits', 'Total: 126 visits', '2025-08-22 12:44:40'),
(2, '2025-08-21', 'Medications', 'Diatabs low stock', '2025-08-22 12:44:40'),
(3, '2025-08-21', 'Medications', 'No soon-to-expire', '2025-08-22 12:44:40'),
(4, '2025-08-20', 'Appointments', '4 pending appointments', '2025-08-22 12:44:40'),
(5, '2025-08-19', 'Inventory', 'Monthly stock review completed', '2025-08-22 12:44:40'),
(6, '2025-08-18', 'Visits', 'Weekly patient summary', '2025-08-22 12:44:40'),
(7, '2025-08-17', 'Medications', 'Prescription analysis report', '2025-08-22 12:44:40'),
(8, '2025-08-16', 'Appointments', 'Daily appointment summary', '2025-08-22 12:44:40'),
(9, '2025-08-15', 'Inventory', 'Low stock alert report', '2025-08-22 12:44:40'),
(10, '2025-08-14', 'Visits', 'Patient demographics analysis', '2025-08-22 12:44:40'),
(11, '2025-08-13', 'Medications', 'Drug interaction review', '2025-08-22 12:44:40'),
(12, '2025-08-12', 'Appointments', 'Missed appointments report', '2025-08-22 12:44:40'),
(13, '2025-08-11', 'Inventory', 'Expiry date monitoring', '2025-08-22 12:44:40'),
(14, '2025-08-10', 'Visits', 'Treatment outcome analysis', '2025-08-22 12:44:40'),
(15, '2025-08-09', 'Medications', 'Prescription volume report', '2025-08-22 12:44:40');

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
(12, 'Dr. jade', 'jade', 'cedricjade13@gmail.com', 'doctor/nurse', 'Active', '$2y$10$xTEIegJg6IdiVHRsH2BhJ.Ag9NCZVkCUWlU/Fun.ydIqlPIfgI1oW'),
(14, 'jade', 'jade13', 'cedricjade13@gmail.com', 'doctor/nurse', 'Active', '$2y$10$xn0Lv3mnFSmcp2UCKVH5euGf3mLORpGY8RYQaCM/hjDLECwZMPusi');

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

-- --------------------------------------------------------

--
-- Table structure for table `weekly_visit_summary`
--

CREATE TABLE `weekly_visit_summary` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `patient_name` varchar(255) NOT NULL,
  `week_start_date` date NOT NULL,
  `week_end_date` date NOT NULL,
  `total_visits` int(11) DEFAULT 0,
  `visit_types` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`visit_types`)),
  `last_visit_date` date DEFAULT NULL,
  `needs_alert` tinyint(1) DEFAULT 0,
  `alert_sent` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `clinic_visits`
--
ALTER TABLE `clinic_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_date` (`patient_id`,`visit_date`),
  ADD KEY `idx_visit_date` (`visit_date`),
  ADD KEY `idx_patient_id` (`patient_id`);

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
-- Indexes for table `health_reminders`
--
ALTER TABLE `health_reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `health_tips`
--
ALTER TABLE `health_tips`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `medicine_requests`
--
ALTER TABLE `medicine_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

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
-- Indexes for table `parent_alerts`
--
ALTER TABLE `parent_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient_week` (`patient_id`,`week_start_date`),
  ADD KEY `idx_alert_date` (`alert_sent_at`),
  ADD KEY `idx_status` (`alert_status`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_medical_info`
--
ALTER TABLE `patient_medical_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_patient` (`patient_id`);

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
-- Indexes for table `reports`
--
ALTER TABLE `reports`
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
-- Indexes for table `weekly_visit_summary`
--
ALTER TABLE `weekly_visit_summary`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_patient_week` (`patient_id`,`week_start_date`),
  ADD KEY `idx_needs_alert` (`needs_alert`),
  ADD KEY `idx_week_dates` (`week_start_date`,`week_end_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `clinic_visits`
--
ALTER TABLE `clinic_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctor_schedules`
--
ALTER TABLE `doctor_schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `health_reminders`
--
ALTER TABLE `health_reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `health_tips`
--
ALTER TABLE `health_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `imported_patients`
--
ALTER TABLE `imported_patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=386;

--
-- AUTO_INCREMENT for table `medication_referrals`
--
ALTER TABLE `medication_referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `medicine_requests`
--
ALTER TABLE `medicine_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=521;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `parent_alerts`
--
ALTER TABLE `parent_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `patient_medical_info`
--
ALTER TABLE `patient_medical_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_prescriptions`
--
ALTER TABLE `pending_prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `weekly_visit_summary`
--
ALTER TABLE `weekly_visit_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `clinic_visits`
--
ALTER TABLE `clinic_visits`
  ADD CONSTRAINT `clinic_visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `imported_patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `health_reminders`
--
ALTER TABLE `health_reminders`
  ADD CONSTRAINT `health_reminders_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `imported_patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `medicine_requests`
--
ALTER TABLE `medicine_requests`
  ADD CONSTRAINT `medicine_requests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `imported_patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `imported_patients` (`id`);

--
-- Constraints for table `parent_alerts`
--
ALTER TABLE `parent_alerts`
  ADD CONSTRAINT `parent_alerts_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `imported_patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_medical_info`
--
ALTER TABLE `patient_medical_info`
  ADD CONSTRAINT `patient_medical_info_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `imported_patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `weekly_visit_summary`
--
ALTER TABLE `weekly_visit_summary`
  ADD CONSTRAINT `weekly_visit_summary_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `imported_patients` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
