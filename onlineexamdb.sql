-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 07:24 PM
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
-- Database: `onlineexamdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `selected_answer` text NOT NULL,
  `is_correct` tinyint(4) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_text`, `selected_answer`, `is_correct`, `user_id`, `exam_id`, `created_at`) VALUES
(26, 'من هو نشوان', '1', 0, 13, 1, '2025-02-02 23:46:44'),
(27, 'من هو رعد', 'c', 0, 13, 1, '2025-02-02 23:46:49'),
(28, 'muhammed', '1', 0, 14, 1, '2025-02-04 15:27:41'),
(29, 'muhammed', '1', 0, 14, 1, '2025-02-04 15:28:41'),
(30, 'nasr', '8', 0, 14, 1, '2025-02-04 15:28:48'),
(31, 'محمد', 'بلا', 0, 14, 1, '2025-02-04 15:29:31'),
(32, 'NNN', 'w', 0, 14, 1, '2025-02-04 15:32:10'),
(33, 'من هو محمد', 'الشهراني', 0, 14, 1, '2025-02-04 16:18:10'),
(34, 'من هو محمد', 'الشهراني', 0, 14, 1, '2025-02-04 16:42:16'),
(35, 'من هو محمد', 'الشهراني', 0, 14, 1, '2025-02-04 16:45:57'),
(36, 'من منشئ هذا الموقع', 'الشهراني', 0, 14, 1, '2025-02-04 16:46:04'),
(37, 'من هو محمد', 'الشهراني', 0, 14, 1, '2025-02-04 16:46:20'),
(38, 'من هو محمد', 'a', 1, 14, 6, '2025-02-04 16:51:05'),
(39, 'من منشئ هذا الموقع', 'a', 1, 14, 6, '2025-02-04 16:51:11'),
(40, 'muhammed', 'a', 0, 14, 6, '2025-02-04 16:57:12'),
(41, 'nasr', 'b', 1, 14, 6, '2025-02-04 16:57:16'),
(42, 'محمد', 'c', 0, 14, 6, '2025-02-04 16:57:20'),
(43, 'muhammed', 'b', 1, 14, 6, '2025-02-04 17:02:52'),
(44, 'nasr', 'a', 0, 14, 6, '2025-02-04 17:02:57'),
(45, 'محمد', 'b', 1, 14, 6, '2025-02-04 17:03:01'),
(46, 'muhammed', 'a', 0, 14, 7, '2025-02-04 17:13:12'),
(47, 'nasr', 'b', 1, 14, 7, '2025-02-04 17:13:17'),
(48, 'محمد', 'b', 1, 14, 7, '2025-02-04 17:13:21'),
(49, 'Hello world', 'a', 1, 14, 10, '2025-02-04 17:19:24'),
(50, 'muhammed yahya', 'b', 1, 14, 10, '2025-02-04 17:19:33'),
(51, 'Hello world', 'a', 1, 6, 10, '2025-02-04 17:34:14'),
(52, 'muhammed yahya', 'b', 1, 6, 10, '2025-02-04 17:34:19'),
(53, 'muhammed', 'a', 0, 15, 7, '2025-02-04 20:37:47'),
(54, 'nasr', 'b', 1, 15, 7, '2025-02-04 20:37:53'),
(55, 'محمد', 'b', 1, 15, 7, '2025-02-04 20:38:00'),
(56, 'muhammed', 'b', 1, 16, 7, '2025-02-04 20:51:47'),
(57, 'nasr', 'b', 1, 16, 7, '2025-02-04 20:51:52'),
(58, 'محمد', 'b', 1, 16, 7, '2025-02-04 20:51:57'),
(59, 'muhammed', 'b', 1, 17, 7, '2025-02-04 20:57:18'),
(60, 'nasr', 'b', 1, 17, 7, '2025-02-04 20:57:22'),
(61, 'محمد', 'c', 0, 17, 7, '2025-02-04 20:57:27'),
(62, 'muhammed', 'b', 1, 18, 7, '2025-02-04 21:43:24'),
(63, 'nasr', 'c', 0, 18, 7, '2025-02-04 21:43:29'),
(64, 'محمد', 'd', 0, 18, 7, '2025-02-04 21:43:33'),
(65, 'من هو محمد', 'a', 1, 19, 6, '2025-02-04 21:55:36'),
(66, 'من منشئ هذا الموقع', 'a', 1, 19, 6, '2025-02-04 21:55:42'),
(67, 'muhammed', 'a', 0, 19, 9, '2025-02-04 22:12:59'),
(68, 'من هو محمد', 'd', 1, 19, 13, '2025-02-05 19:48:40'),
(69, 'zzz', 'a', 1, 19, 13, '2025-02-05 19:48:47'),
(70, 'من هو نشوان', 'b', 0, 19, 13, '2025-02-05 19:48:54'),
(71, 'muhammed', 'a', 0, 19, 13, '2025-02-05 19:48:59'),
(72, 'ككك', 'a', 0, 19, 13, '2025-02-05 19:49:04'),
(73, 'muhammed', 'a', 0, 22, 9, '2025-02-06 11:37:52'),
(74, 'من الذي يلعب بالنار', 'c', 1, 23, 16, '2025-02-06 21:32:54'),
(75, 'من الذي يلعب بالنار', 'c', 1, 24, 16, '2025-02-06 21:43:32'),
(76, 'من الذي يلعب بالنار', 'c', 1, 26, 16, '2025-02-06 22:43:51'),
(77, 'من الذي يلعب بالنار', 'c', 1, 26, 16, '2025-02-06 22:52:59'),
(78, 'من الذي يلعب بالنار', 'c', 1, 27, 16, '2025-02-06 23:12:23'),
(79, 'من الذي يلعب بالنار', 'c', 1, 27, 16, '2025-02-06 23:14:47'),
(80, 'من الذي يلعب بالنار', 'c', 1, 27, 16, '2025-02-06 23:15:11'),
(81, 'من الذي يلعب بالنار', 'c', 1, 27, 16, '2025-02-06 23:15:36'),
(82, 'من الذي يلعب بالنار', 'c', 1, 28, 16, '2025-02-06 23:22:19'),
(83, 'من الذي يلعب بالنار', 'b', 0, 28, 16, '2025-02-06 23:25:42'),
(84, 'ةةةة', 'b', 1, 29, 18, '2025-02-07 15:51:14'),
(85, 'من هو محمد', 'b', 0, 29, 17, '2025-02-07 16:23:10'),
(86, 'ةةةة', 'b', 1, 23, 18, '2025-02-07 16:33:43'),
(87, 'من الذي يلعب بالنار', 'c', 1, 28, 16, '2025-02-07 17:42:30'),
(88, 'ةةةة', 'b', 1, 28, 18, '2025-02-07 17:55:03'),
(89, 'من منشئ هذا الموقع', 'a', 1, 24, 20, '2025-02-07 18:03:52'),
(90, 'من هو نشوان', 'c', 1, 24, 20, '2025-02-07 18:05:14'),
(91, 'من منشئ هذا الموقع', 'a', 1, 28, 20, '2025-02-07 23:19:35'),
(92, 'من الذي يلعب بالنار', 'c', 1, 28, 20, '2025-02-07 23:19:44'),
(93, 'من هو نشوان', 'c', 1, 28, 20, '2025-02-07 23:19:50'),
(94, 'muhammed yahya', 'b', 1, 25, 21, '2025-02-07 23:43:22'),
(95, 'muhammed', 'c', 0, 25, 21, '2025-02-07 23:43:28'),
(96, 'what is your name', 'b', 1, 28, 19, '2025-02-08 06:53:33'),
(97, 'muhammed yahya', 'b', 1, 28, 21, '2025-02-08 06:53:51'),
(98, 'muhammed yahya', 'b', 1, 28, 21, '2025-02-08 09:26:39');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `created_at`) VALUES
(1, 'Programing (HTML)', '2025-02-01 20:43:35'),
(2, 'HTML', '2025-02-01 22:41:00'),
(3, 'CSS', '2025-02-01 22:41:00'),
(4, 'JavaScript', '2025-02-01 22:41:00'),
(5, 'SQL', '2025-02-02 14:22:15');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `complaint_type` enum('درجات','سؤال محدد','امتحان كامل') NOT NULL,
  `comments` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaint_replies`
--

CREATE TABLE `complaint_replies` (
  `reply_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `reply_comments` text NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `exam_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_available` tinyint(1) DEFAULT 0,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `is_taken` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `result_id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `wrong_answers` int(11) DEFAULT NULL,
  `ignored_answers` int(11) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`options`)),
  `correct_answer` varchar(50) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `exam_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `heart_count` int(11) DEFAULT 0,
  `like_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `answer_id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `selected_answer` varchar(50) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_exams`
--

CREATE TABLE `student_exams` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(15) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `password`, `email`, `role`, `created_at`, `phone`, `gender`, `status`) VALUES
(31, 'xxx', 'xxx', '$2y$10$Cssv8sb0xSqRORCJyM0NT.EatVqd93IUilsyBDyYRkNVnK9YwoG.a', 'xxx@gmail', 'student', '2025-02-08 17:26:36', '888', 'male', 'active'),
(32, 'muhammed alshahrany', 'muhammedalshahrany39##', '$2y$10$CVLbIeSv8GxyIrWJ8HOsq.CP31MCqBZXJfAMCH7n4z8f5uP8fuMbe', 'alshhranymuha39@gmail', 'admin', '2025-02-08 17:49:25', '771035532', 'male', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaint_id`),
  ADD UNIQUE KEY `unique_complaint` (`user_id`,`exam_id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `complaint_replies`
--
ALTER TABLE `complaint_replies`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`exam_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `complaint_id` (`complaint_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `fk_exam` (`exam_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `student_exams`
--
ALTER TABLE `student_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `complaint_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `complaint_replies`
--
ALTER TABLE `complaint_replies`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `exam_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_exams`
--
ALTER TABLE `student_exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`);

--
-- Constraints for table `complaint_replies`
--
ALTER TABLE `complaint_replies`
  ADD CONSTRAINT `complaint_replies_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`),
  ADD CONSTRAINT `complaint_replies_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`),
  ADD CONSTRAINT `exam_results_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`complaint_id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_exam` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`),
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `student_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `student_exams`
--
ALTER TABLE `student_exams`
  ADD CONSTRAINT `student_exams_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`exam_id`),
  ADD CONSTRAINT `student_exams_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
