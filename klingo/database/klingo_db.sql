-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 03:33 PM
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
-- Database: `klingo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_word`
--

CREATE TABLE `daily_word` (
  `id` int(11) NOT NULL,
  `word_id` int(11) DEFAULT NULL,
  `suggested_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grammar_lessons`
--

CREATE TABLE `grammar_lessons` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grammar_quizzes`
--

CREATE TABLE `grammar_quizzes` (
  `id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `lesson_number`, `is_active`, `created_by`, `created_at`) VALUES
(1, 1, 1, 1, '2025-04-28 05:35:57'),
(2, 2, 1, NULL, '2025-04-28 06:37:56'),
(3, 3, 1, NULL, '2025-04-28 08:19:53'),
(4, 4, 1, NULL, '2025-04-29 03:48:38'),
(5, 5, 1, NULL, '2025-04-29 03:48:42'),
(6, 6, 1, NULL, '2025-04-29 03:48:44'),
(7, 7, 1, NULL, '2025-04-29 03:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_results`
--

CREATE TABLE `quiz_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lesson_id` int(11) NOT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `score` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_results`
--

INSERT INTO `quiz_results` (`id`, `user_id`, `lesson_id`, `correct_answers`, `total_questions`, `score`, `created_at`) VALUES
(8, 1, 1, 11, 30, 11, '2025-04-28 06:58:03'),
(9, 3, 1, 27, 30, 27, '2025-04-28 07:07:03'),
(10, 3, 1, 30, 30, 26, '2025-04-28 07:35:42'),
(11, 3, 1, 30, 30, 30, '2025-04-28 07:51:01'),
(12, 3, 2, 3, 100, 3, '2025-04-28 09:28:26'),
(13, 2, 1, 30, 30, 30, '2025-04-28 13:16:37'),
(14, 1, 1, 29, 30, 29, '2025-04-30 16:48:03'),
(15, 1, 1, 30, 30, 30, '2025-04-30 16:53:27'),
(16, 1, 3, 17, 17, 17, '2025-04-30 16:56:03'),
(17, 1, 4, 7, 8, 7, '2025-04-30 16:57:37'),
(18, 1, 4, 8, 8, 8, '2025-04-30 16:58:32'),
(19, 1, 5, 7, 7, 7, '2025-04-30 17:00:07'),
(20, 5, 1, 29, 30, 29, '2025-05-01 12:12:39'),
(21, 5, 1, 30, 30, 30, '2025-05-01 12:17:22'),
(22, 5, 2, 100, 100, 100, '2025-05-01 12:36:11'),
(23, 5, 3, 16, 17, 16, '2025-05-01 12:38:34'),
(24, 5, 3, 17, 17, 17, '2025-05-01 12:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','super_admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Super Admin', 'superadmin@gmail.com', '$2y$10$I7.1KEl448s0G5BBBPCPHOc3f34k30M37wangtEjfvZXIcP5720VC', 'super_admin', '2025-04-28 05:10:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `quiz_score` int(11) DEFAULT 0,
  `completed` tinyint(1) DEFAULT 0,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `lesson_id`, `quiz_score`, `completed`, `completed_at`) VALUES
(1, 3, 1, 30, 0, NULL),
(2, 3, 1, 30, 1, '2025-04-28 00:51:01'),
(3, 3, 2, 3, 0, NULL),
(4, 2, 1, 30, 1, '2025-04-28 06:16:37'),
(5, 1, 1, 29, 0, NULL),
(6, 1, 1, 30, 1, '2025-04-30 09:53:27'),
(7, 1, 3, 17, 1, '2025-04-30 09:56:03'),
(8, 1, 4, 7, 0, NULL),
(9, 1, 4, 8, 1, '2025-04-30 09:58:32'),
(10, 1, 5, 7, 1, '2025-04-30 10:00:07'),
(11, 5, 1, 29, 0, NULL),
(12, 5, 1, 30, 1, '2025-05-01 05:17:22'),
(13, 5, 2, 100, 1, '2025-05-01 05:36:11'),
(14, 5, 3, 16, 0, NULL),
(15, 5, 3, 17, 1, '2025-05-01 05:40:45');

-- --------------------------------------------------------

--
-- Table structure for table `words`
--

CREATE TABLE `words` (
  `id` int(11) NOT NULL,
  `korean` varchar(255) NOT NULL,
  `english` varchar(255) NOT NULL,
  `nepali` varchar(255) NOT NULL,
  `lesson_id` int(11) DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `words`
--

INSERT INTO `words` (`id`, `korean`, `english`, `nepali`, `lesson_id`, `image_url`, `created_at`) VALUES
(156, '표준어교육', 'Standard teaching', 'मानक भाषाशिक्षा', 1, '', '2025-04-28 05:36:12'),
(157, '교재', 'Learning material', 'पाठ्यपुस्तक', 1, '', '2025-04-28 05:36:12'),
(158, '한글', 'Korean alphabet', 'कोरियन लिपि', 1, '', '2025-04-28 05:36:12'),
(159, '익숙하다', 'Familiar with', 'परिचित हुनु', 1, '', '2025-04-28 05:36:12'),
(160, '자음', 'Consonant', 'व्यञ्जन', 1, '', '2025-04-28 05:36:12'),
(161, '모음', 'Vowel', 'स्वर', 1, '', '2025-04-28 05:36:12'),
(162, '한국어', 'Korean Language', 'कोरियन भाषा', 1, '', '2025-04-28 05:36:12'),
(163, '음절 구조', 'Syllable structure', 'स्वर र व्यञ्जन संरचना', 1, '', '2025-04-28 05:36:12'),
(164, '유형', 'Type', 'प्रकार', 1, '', '2025-04-28 05:36:12'),
(165, '이루어지다', 'Effected, attained', 'पूरा हुनु', 1, '', '2025-04-28 05:36:12'),
(166, '강', 'River', 'नदी', 1, '', '2025-04-28 05:36:12'),
(167, '곰', 'Bear', 'भालु', 1, '', '2025-04-28 05:36:12'),
(168, '운', 'Luck, fortune', 'भाग्य', 1, '', '2025-04-28 05:36:12'),
(169, '붙이다', 'Attach', 'टाँस्नु', 1, '', '2025-04-28 05:36:12'),
(170, '첫소리', 'Initial sound', 'पहिलो स्वर', 1, '', '2025-04-28 05:36:12'),
(171, '음가', 'Phonetic value', 'ध्वनि मूल्य', 1, '', '2025-04-28 05:36:12'),
(172, '게다가', 'More over', 'अझै पनि', 1, '', '2025-04-28 05:36:12'),
(173, '세로로', 'Vertically', 'ठाडो', 1, '', '2025-04-28 05:36:12'),
(174, '가로로', 'Horizontally', 'तेर्सो', 1, '', '2025-04-28 05:36:12'),
(175, '와', 'And, with', 'र, सँगै', 1, '', '2025-04-28 05:36:12'),
(176, '왼쪽', 'Leftside', 'देब्रेपट्टि', 1, '', '2025-04-28 05:36:12'),
(177, '위치', 'Locate', 'स्थान', 1, '', '2025-04-28 05:36:12'),
(178, '종이쪽', 'Paper Side', 'कागजपट्टि', 1, '', '2025-04-28 05:36:12'),
(179, '기본 모음', 'Basic vowel', 'आधारभूत स्वर वर्ण', 1, '', '2025-04-28 05:36:12'),
(180, '이중 모음', 'Double vowel', 'मिश्रित स्वर वर्ण', 1, '', '2025-04-28 05:36:12'),
(181, '나누다', 'To divide', 'बाँड्नु', 1, '', '2025-04-28 05:36:12'),
(182, '따르다', 'Following', 'पालना गर्नु', 1, '', '2025-04-28 05:36:12'),
(183, '순서', 'Sequence, order', 'क्रम', 1, '', '2025-04-28 05:36:12'),
(184, '쓰기', 'Writing', 'लेखाइ', 1, '', '2025-04-28 05:36:12'),
(185, '연습', 'Exercise, practice', 'अभ्यास', 1, '', '2025-04-28 05:36:12'),
(223, '반친구', 'Classmate', 'कक्षाको साथी', 3, '', '2025-04-28 08:46:24'),
(224, '칠판', 'Board', 'बोर्ड, पाटी, सिलोट', 3, '', '2025-04-28 08:46:24'),
(225, '책상', 'Table', 'टेबल', 3, '', '2025-04-28 08:46:24'),
(226, '의자', 'Chair', 'कुर्सी', 3, '', '2025-04-28 08:46:24'),
(227, '볼펜', 'Pen', 'कलम', 3, '', '2025-04-28 08:46:24'),
(228, '연필', 'Pencil', 'पेनसिल', 3, '', '2025-04-28 08:46:24'),
(229, '필통', 'Pencil box', 'पेनसिल बक्स', 3, '', '2025-04-28 08:46:24'),
(230, '달력', 'Calendar', 'पात्रो', 3, '', '2025-04-28 08:46:24'),
(231, '창문', 'Window', 'झ्याल', 3, '', '2025-04-28 08:46:24'),
(232, '책을 펴다', 'To open book', 'किताब खोल्नु', 3, '', '2025-04-28 08:46:24'),
(233, '책을 덮다', 'To close book', 'किताब बन्द गर्नु', 3, '', '2025-04-28 08:46:24'),
(234, '따라하다', 'To repeat after', 'सङ्गसँगै भन्नु', 3, '', '2025-04-28 08:46:24'),
(235, '이야기하다', 'Talking', 'कुराकानी गर्नु', 3, '', '2025-04-28 08:46:24'),
(236, '대답하다', 'Give answer', 'जवाफ दिनु', 3, '', '2025-04-28 08:46:24'),
(237, '알다', 'To know', 'थाहा पाउनु', 3, '', '2025-04-28 08:46:24'),
(238, '모르다', 'Do not know', 'थाहा नहुनु', 3, '', '2025-04-28 08:46:24'),
(239, '질문', 'Question', 'प्रश्न', 3, '', '2025-04-28 08:46:24'),
(262, '조합', 'Combination', 'संयोजन', 2, NULL, '2025-04-28 09:22:56'),
(263, '가수', 'Singer', 'गायक', 2, NULL, '2025-04-28 09:22:56'),
(264, '아기', 'Baby', 'बच्चा', 2, NULL, '2025-04-28 09:22:56'),
(265, '네모', 'Square', 'वर्ग', 2, NULL, '2025-04-28 09:22:56'),
(266, '다리', 'Leg/ bridge', 'खुट्टा/ पुल', 2, NULL, '2025-04-28 09:22:56'),
(267, '소리', 'Sound', 'आवाज', 2, NULL, '2025-04-28 09:22:56'),
(268, '모자', 'Hat', 'टोपी', 2, NULL, '2025-04-28 09:22:56'),
(269, '아버지', 'Father/dad', 'बुबा', 2, NULL, '2025-04-28 09:22:56'),
(270, '사자', 'Lion', 'सिंह', 2, NULL, '2025-04-28 09:22:56'),
(271, '새우', 'Shrimp/ Prawn', 'झिंगे माछा', 2, NULL, '2025-04-28 09:22:56'),
(272, '지우개', 'Eraser', 'इरेजर', 2, NULL, '2025-04-28 09:22:56'),
(273, '치마', 'Skirt', 'स्कर्ट', 2, NULL, '2025-04-28 09:22:56'),
(274, '타조', 'Ostrich', 'अष्ट्रिच चराहरु', 2, NULL, '2025-04-28 09:22:56'),
(275, '포도', 'Grape', 'अंगूर', 2, NULL, '2025-04-28 09:22:56'),
(276, '호수', 'Lake', 'ताल', 2, NULL, '2025-04-28 09:22:56'),
(277, '노래', 'Song', 'गीत', 2, NULL, '2025-04-28 09:22:56'),
(278, '모래', 'Sand', 'बालुवा', 2, NULL, '2025-04-28 09:22:56'),
(279, '사다', 'To buy', 'किन्नु', 2, NULL, '2025-04-28 09:22:56'),
(280, '자다', 'To sleep', 'सुत्नु', 2, NULL, '2025-04-28 09:22:56'),
(281, '매우', 'Very much', 'धेरै', 2, NULL, '2025-04-28 09:22:56'),
(282, '보도', 'Footpath', 'फुटपाथ', 2, NULL, '2025-04-28 09:22:56'),
(283, '기자', 'Journalist', 'पत्रकार', 2, NULL, '2025-04-28 09:22:56'),
(284, '기차', 'Train', 'रेल', 2, NULL, '2025-04-28 09:22:56'),
(285, '도리', 'Bearn', 'बियर', 2, NULL, '2025-04-28 09:22:56'),
(286, '고리', 'Ring', 'औंठी', 2, NULL, '2025-04-28 09:22:56'),
(287, '타다', 'To ride', 'चढ्नु', 2, NULL, '2025-04-28 09:22:56'),
(288, '머리', 'Head', 'टाउको', 2, NULL, '2025-04-28 09:22:56'),
(289, '허리', 'Waist', 'कम्मर', 2, NULL, '2025-04-28 09:22:56'),
(290, '까치', 'Korean magpie', 'कोरियन म्याग्पाइ', 2, NULL, '2025-04-28 09:22:56'),
(291, '어깨', 'Shoulder', 'काँध', 2, NULL, '2025-04-28 09:22:56'),
(292, '메뚜기', 'Grasshopper', 'फट्यांग्रा', 2, NULL, '2025-04-28 09:22:56'),
(293, '뿌리', 'Root', 'जरा', 2, NULL, '2025-04-28 09:22:56'),
(294, '아저씨', 'Maternal uncle/ uncle', 'मामा/अंकल', 2, NULL, '2025-04-28 09:22:56'),
(295, '찌개', 'Gravy soup', 'सुप', 2, NULL, '2025-04-28 09:22:56'),
(296, '만나다', 'Meet', 'भेट्नु', 2, NULL, '2025-04-28 09:22:56'),
(297, '끄다', 'To off', 'निभाउनु', 2, NULL, '2025-04-28 09:22:56'),
(298, '차다', 'To cold', 'चिसो हुनु', 2, NULL, '2025-04-28 09:22:56'),
(299, '짜다', 'Salted', 'नुनिलो हुनु', 2, NULL, '2025-04-28 09:22:56'),
(300, '크다', 'To big', 'ठूलो हुनु', 2, NULL, '2025-04-28 09:22:56'),
(301, '까다', 'To peel', 'छाला फुकाल्नु', 2, NULL, '2025-04-28 09:22:56'),
(302, '싸다', 'To cheap/ to pack', 'सस्तो हुनु/ प्याक गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(303, '부리', 'Beak', 'चरा को चोसो', 2, NULL, '2025-04-28 09:22:56'),
(304, '아프다', 'Be sick', 'बिरामी हुनु', 2, NULL, '2025-04-28 09:22:56'),
(305, '바쁘다', 'To busy', 'व्यस्त हुनु', 2, NULL, '2025-04-28 09:22:56'),
(306, '제외', 'Except', 'बाहेक', 2, NULL, '2025-04-28 09:22:56'),
(307, '모든', 'All', 'सबै', 2, NULL, '2025-04-28 09:22:56'),
(308, '끝', 'End/ close', 'अन्त', 2, NULL, '2025-04-28 09:22:56'),
(309, '받침', 'Final consonant', 'अन्तिम व्यञ्जन', 2, NULL, '2025-04-28 09:22:56'),
(310, '쓰다', 'To write', 'लेख्नु', 2, NULL, '2025-04-28 09:22:56'),
(311, '국수', 'Noodles', 'चाउचाउ', 2, NULL, '2025-04-28 09:22:56'),
(312, '가끔', 'Sometimes', 'कहिलेकाहीँ', 2, NULL, '2025-04-28 09:22:56'),
(313, '간단히', 'Simply', 'छोटोमा', 2, NULL, '2025-04-28 09:22:56'),
(314, '갑자기', 'Suddenly', 'अचानक', 2, NULL, '2025-04-28 09:22:56'),
(315, '같다', 'To be similar', 'जस्तै हुनु', 2, NULL, '2025-04-28 09:22:56'),
(316, '거의', 'Almost', 'झन्डै', 2, NULL, '2025-04-28 09:22:56'),
(317, '거절하다', 'To reject', 'अस्वीकार गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(318, '걱정하다', 'To worry', 'चिन्ता गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(319, '건너다', 'To cross', 'पार गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(320, '걸다', 'To hang', 'झुण्ड्याउनु', 2, NULL, '2025-04-28 09:22:56'),
(321, '걸리다', 'To take (time)', 'लाग्नु', 2, NULL, '2025-04-28 09:22:56'),
(322, '게임', 'Game', 'खेल', 2, NULL, '2025-04-28 09:22:56'),
(323, '겨울', 'Winter', 'जाडो मौसम', 2, NULL, '2025-04-28 09:22:56'),
(324, '결정하다', 'To decide', 'निर्णय गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(325, '경험', 'Experience', 'अनुभव', 2, NULL, '2025-04-28 09:22:56'),
(326, '계속', 'Continue', 'जारी राख्नु', 2, NULL, '2025-04-28 09:22:56'),
(327, '고르다', 'To choose', 'छान्नु', 2, NULL, '2025-04-28 09:22:56'),
(328, '공기', 'Air', 'हावा', 2, NULL, '2025-04-28 09:22:56'),
(329, '공부', 'Study', 'पढाइ', 2, NULL, '2025-04-28 09:22:56'),
(330, '공연', 'Performance', 'प्रदर्शन', 2, NULL, '2025-04-28 09:22:56'),
(331, '공장', 'Factory', 'कारखाना', 2, NULL, '2025-04-28 09:22:56'),
(332, '공책', 'Notebook', 'कापी', 2, NULL, '2025-04-28 09:22:56'),
(333, '과거', 'Past', 'अतीत', 2, NULL, '2025-04-28 09:22:56'),
(334, '관계', 'Relationship', 'सम्बन्ध', 2, NULL, '2025-04-28 09:22:56'),
(335, '광고', 'Advertisement', 'विज्ञापन', 2, NULL, '2025-04-28 09:22:56'),
(336, '괜찮다', 'It\'s okay', 'ठिक छ', 2, NULL, '2025-04-28 09:22:56'),
(337, '굉장히', 'Very very', 'धेरै धेरै', 2, NULL, '2025-04-28 09:22:56'),
(338, '교육', 'Education', 'शिक्षा', 2, NULL, '2025-04-28 09:22:56'),
(339, '구경하다', 'To go watch', 'हेर्न जानु', 2, NULL, '2025-04-28 09:22:56'),
(340, '구름', 'Cloud', 'बादल', 2, NULL, '2025-04-28 09:22:56'),
(341, '구십', 'Ninety', 'नब्बे', 2, NULL, '2025-04-28 09:22:56'),
(342, '국가', 'Nation', 'राष्ट्र', 2, NULL, '2025-04-28 09:22:56'),
(343, '국민', 'Citizen', 'जनता', 2, NULL, '2025-04-28 09:22:56'),
(344, '국제', 'International', 'अन्तर्राष्ट्रिय', 2, NULL, '2025-04-28 09:22:56'),
(345, '군인', 'Soldier', 'सैनिक', 2, NULL, '2025-04-28 09:22:56'),
(346, '굳다', 'To be hard', 'कठोर हुनु', 2, NULL, '2025-04-28 09:22:56'),
(347, '굴다', 'To behave', 'व्यवहार गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(348, '굶다', 'To starve', 'भोकै बस्नु', 2, NULL, '2025-04-28 09:22:56'),
(349, '굽다', 'To roast', 'पाकाउनु', 2, NULL, '2025-04-28 09:22:56'),
(350, '궁금하다', 'To be curious', 'जिज्ञासा हुनु', 2, NULL, '2025-04-28 09:22:56'),
(351, '권하다', 'To recommend', 'सुझाव दिनु', 2, NULL, '2025-04-28 09:22:56'),
(352, '귀', 'Ear', 'कान', 2, NULL, '2025-04-28 09:22:56'),
(353, '귀여워하다', 'To adore', 'माया गर्नु', 2, NULL, '2025-04-28 09:22:56'),
(354, '규칙', 'Rule', 'नियम', 2, NULL, '2025-04-28 09:22:56'),
(355, '균형', 'Balance', 'सन्तुलन', 2, NULL, '2025-04-28 09:22:56'),
(356, '그', 'That', 'त्यो', 2, NULL, '2025-04-28 09:22:56'),
(357, '그냥', 'Just', 'त्यसै', 2, NULL, '2025-04-28 09:22:56'),
(358, '그녀', 'She', 'उनी (महिला)', 2, NULL, '2025-04-28 09:22:56'),
(359, '그들', 'They', 'उनीहरू', 2, NULL, '2025-04-28 09:22:56'),
(360, '그림', 'Picture', 'चित्र', 2, NULL, '2025-04-28 09:22:56'),
(361, '그만두다', 'To quit', 'छोड्नु', 2, NULL, '2025-04-28 09:22:56'),
(384, '안녕하세요', 'Greeting', 'नमस्कार', 4, NULL, '2025-04-30 15:43:58'),
(385, '만나서 반갑습니다', 'Nice to meet you', 'भेटेर खुसी लाग्यो', 4, NULL, '2025-04-30 15:43:58'),
(386, '안녕히 계세요', 'Bye', 'राम्रोसँग बस्नुहोस्', 4, NULL, '2025-04-30 15:43:58'),
(387, '안녕히 가세요', 'Bye', 'राम्रोसँग जानुहोस्', 4, NULL, '2025-04-30 15:43:58'),
(388, '감사합니다', 'Thank you', 'धन्यवाद', 4, NULL, '2025-04-30 15:43:58'),
(389, '죄송합니다', 'I am sorry', 'माफ गर्नुहोस्', 4, NULL, '2025-04-30 15:43:58'),
(390, '아니예요', 'No', 'होइन', 4, NULL, '2025-04-30 15:43:58'),
(391, '괜찮아요', 'No problem', 'ठीकै छ', 4, NULL, '2025-04-30 15:43:58'),
(393, '잘 먹었습니다', 'Thanks for the meal', 'राम्ररी खाएँ', 5, NULL, '2025-04-30 15:44:42'),
(394, '축하합니다', 'Congratulation', 'बधाई छ', 5, NULL, '2025-04-30 15:44:42'),
(395, '감사합니다', 'Thank you', 'धन्यवाद', 5, NULL, '2025-04-30 15:44:42'),
(396, '안 녕 히 주 무 세 요 .', 'Good night', 'रामरी सुत्नुहोस्', 5, NULL, '2025-04-30 15:44:42'),
(397, '잘 자 요', 'Good night', 'रामरी सुत', 5, NULL, '2025-04-30 15:44:42'),
(398, '안 녕 히 주 무 셨 어 요 ?', 'How about your tonight ?', 'राम्री सुत्नुभयो ?', 5, NULL, '2025-04-30 15:44:42'),
(399, '많 이 드 세 요', 'Take much', 'धेरै खानुहोस्', 5, NULL, '2025-04-30 15:44:42'),
(401, '학습 목표', 'Learning objective', 'सिकाइ उद्देश्य', 6, NULL, '2025-04-30 16:12:51'),
(402, '자기소개', 'Self introduction', 'आफ्नो परिचय', 6, NULL, '2025-04-30 16:12:51'),
(403, '문법', 'Grammar', 'व्याकरण', 6, NULL, '2025-04-30 16:12:51'),
(404, '어휘', 'Vocabulary', 'शब्द', 6, NULL, '2025-04-30 16:12:51'),
(405, '나라', 'Country', 'देश', 6, NULL, '2025-04-30 16:12:51'),
(406, '직업', 'Job, occupation', 'पेशा', 6, NULL, '2025-04-30 16:12:51'),
(407, '정보', 'Information', 'जानकारी', 6, NULL, '2025-04-30 16:12:51'),
(408, '문화', 'Culture', 'संस्कृति', 6, NULL, '2025-04-30 16:12:51'),
(409, '인사', 'Greeting', 'अभिवादन', 6, NULL, '2025-04-30 16:12:51'),
(410, '예절', 'Manner', 'मर्यादा', 6, NULL, '2025-04-30 16:12:51'),
(411, '중국', 'China', 'चीन', 6, NULL, '2025-04-30 16:12:51'),
(412, '태국', 'Thailand', 'थाइल्यान्ड', 6, NULL, '2025-04-30 16:12:51'),
(413, '사용', 'Use', 'प्रयोग', 6, NULL, '2025-04-30 16:12:51'),
(414, '성명', 'Cast and name', 'थर र नाम', 6, NULL, '2025-04-30 16:12:51'),
(415, '국적', 'Nationality', 'राष्ट्रियता', 6, NULL, '2025-04-30 16:12:51'),
(416, '연습', 'Exercise', 'अभ्यास', 6, NULL, '2025-04-30 16:12:51'),
(417, '대화', 'Dialogue', 'कुराकानी', 6, NULL, '2025-04-30 16:12:51'),
(418, '선생님', 'Teacher', 'शिक्षक', 6, NULL, '2025-04-30 16:12:51'),
(419, '회사원', 'Company staff', 'कम्पनीको कर्मचारी', 6, NULL, '2025-04-30 16:12:51'),
(420, '주부', 'Housewife', 'गृहिणी', 6, NULL, '2025-04-30 16:12:51'),
(421, '경찰관', 'Police', 'प्रहरी', 6, NULL, '2025-04-30 16:12:51'),
(422, '소방관', 'Firefighter', 'अग्नि नियन्त्रक', 6, NULL, '2025-04-30 16:12:51'),
(423, '공무원', 'Public Officer', 'सरकारी कर्मचारी', 6, NULL, '2025-04-30 16:12:51'),
(424, '점원', 'Shop assistant', 'पसल सहायक', 6, NULL, '2025-04-30 16:12:51'),
(425, '의사', 'Doctor', 'चिकित्सक', 6, NULL, '2025-04-30 16:12:51'),
(426, '간호사', 'Nurse', 'नर्स', 6, NULL, '2025-04-30 16:12:51'),
(427, '요리사', 'Cook man', 'भान्से', 6, NULL, '2025-04-30 16:12:51'),
(428, '운전기사', 'Driver', 'चालक', 6, NULL, '2025-04-30 16:12:51'),
(429, '기술자', 'Technician', 'प्राविधिक', 6, NULL, '2025-04-30 16:12:51'),
(430, '목수', 'Carpenter', 'सिकर्मी', 6, NULL, '2025-04-30 16:12:51'),
(431, '농부', 'Farmer', 'किसान', 6, NULL, '2025-04-30 16:12:51'),
(432, '어부', 'Fisherman', 'माभी', 6, NULL, '2025-04-30 16:12:51'),
(433, '의문형', 'Question form', 'प्रश्नको रूपमा', 6, NULL, '2025-04-30 16:12:51'),
(434, '명사', 'Noun', 'सँज्ञा/नाम', 6, NULL, '2025-04-30 16:12:51'),
(435, '보기', 'Example', 'उदाहरण', 6, NULL, '2025-04-30 16:12:51'),
(436, '연결하다', 'To match', 'जोडा मिलाउनु', 6, NULL, '2025-04-30 16:12:51'),
(437, '완성하다', 'To complete', 'पुरा गर्नु', 6, NULL, '2025-04-30 16:12:51'),
(438, '활동', 'Activity', 'क्रियाकलाप', 6, NULL, '2025-04-30 16:12:51'),
(439, '자신보다', 'Self than', 'आफुभन्दा', 6, NULL, '2025-04-30 16:12:51'),
(440, '지위', 'Position', 'पद', 6, NULL, '2025-04-30 16:12:51'),
(441, '처음', 'First', 'पहिलो', 6, NULL, '2025-04-30 16:12:51'),
(442, '직장', 'Work place', 'कार्यस्थल', 6, NULL, '2025-04-30 16:12:51'),
(443, '동료', 'Colleague', 'सहकर्मी', 6, NULL, '2025-04-30 16:12:51'),
(444, '나이가 어리다', 'To be young', 'उमेर थोरै हुनु', 6, NULL, '2025-04-30 16:12:51'),
(445, '친하다', 'Intimate', 'आत्मीय हुनु', 6, NULL, '2025-04-30 16:12:51'),
(446, '고르다', 'Choose', 'छान्नु', 6, NULL, '2025-04-30 16:12:51'),
(447, '알맞은 것/맞는 것', 'Correct thing', 'मिल्ने कुरा', 6, NULL, '2025-04-30 16:12:51'),
(449, '슈퍼마켓', 'Supermarket', 'सुपरमार्केट', 7, NULL, '2025-04-30 16:42:44'),
(450, '나타내다', 'To show', 'देखाउनु', 7, NULL, '2025-04-30 16:42:44'),
(451, '바꾸다', 'To change', 'परिवर्तन गर्नु', 7, NULL, '2025-04-30 16:42:44'),
(452, '끝나다', 'To finish', 'सकिनु', 7, NULL, '2025-04-30 16:42:44'),
(453, '단어', 'Word', 'शब्द', 7, NULL, '2025-04-30 16:42:44'),
(454, '처럼', 'Like', 'जस्तै', 7, NULL, '2025-04-30 16:42:44'),
(455, '줄임말', 'Abbreviation', 'संक्षिप्त शब्द', 7, NULL, '2025-04-30 16:42:44'),
(456, '말하기', 'Speaking', 'बोलाई', 7, NULL, '2025-04-30 16:42:44'),
(457, '열쇠', 'Key', 'चाबी', 7, NULL, '2025-04-30 16:42:44'),
(458, '가족사진', 'Family\'s photo', 'परिवारको फोटो', 7, NULL, '2025-04-30 16:42:44'),
(459, '가방', 'Bag', 'झोला', 7, NULL, '2025-04-30 16:42:44'),
(460, '지갑', 'Wallet', 'पर्स', 7, NULL, '2025-04-30 16:42:44'),
(461, '여권', 'Passport', 'राहदानी', 7, NULL, '2025-04-30 16:42:44'),
(462, '우산', 'Umbrella', 'छाता', 7, NULL, '2025-04-30 16:42:44'),
(463, '거울', 'Mirror', 'ऐना', 7, NULL, '2025-04-30 16:42:44'),
(464, '화장품', 'Cosmetics item', 'श्रृंगार सामान', 7, NULL, '2025-04-30 16:42:44'),
(465, '빗', 'Comb', 'काइयो', 7, NULL, '2025-04-30 16:42:44'),
(466, '헤어드라이어', 'Hair dryer', 'कपाल सुकाउने मेशिन', 7, NULL, '2025-04-30 16:42:44'),
(467, '베개', 'Pillow', 'सिरानी', 7, NULL, '2025-04-30 16:42:44'),
(468, '이불', 'Blanket', 'सिरक', 7, NULL, '2025-04-30 16:42:44'),
(469, '서술', 'Description', 'वर्णनात्मक वाक्य', 7, NULL, '2025-04-30 16:42:44'),
(470, '물음', 'Question', 'प्रश्नवाचक वाक्य', 7, NULL, '2025-04-30 16:42:44'),
(471, '바닥', 'Floor', 'भुइँ', 7, NULL, '2025-04-30 16:42:44'),
(472, '생활하다', 'To exist the life', 'जीवनयापन गर्नु', 7, NULL, '2025-04-30 16:42:44'),
(473, '발달하다', 'To be developed', 'विकास हुनु', 7, NULL, '2025-04-30 16:42:44'),
(474, '대부분', 'Most of', 'अधिकांश धेरै', 7, NULL, '2025-04-30 16:42:44'),
(475, '신발', 'Shoes', 'जुत्ता', 7, NULL, '2025-04-30 16:42:44'),
(476, '들어가다', 'To enter', 'प्रवेश गर्नु', 7, NULL, '2025-04-30 16:42:44'),
(477, '도', 'Too', 'पनी', 7, NULL, '2025-04-30 16:42:44'),
(478, '린스', 'Conditioner', 'कपाल मल्ने लोसन', 7, NULL, '2025-04-30 16:42:44'),
(479, '샴푸', 'Shampoo', 'स्याम्पु', 7, NULL, '2025-04-30 16:42:44'),
(480, '비누 칫술', 'Soap and Toothbrush', 'साबुन & ब्रसं', 7, NULL, '2025-04-30 16:42:44'),
(481, '치약', 'Toothpaste', 'मञ्जन', 7, NULL, '2025-04-30 16:42:44'),
(482, '수건', 'Towel', 'रूमाल टावेल', 7, NULL, '2025-04-30 16:42:44'),
(483, '장소', 'Place', 'स्थान', 7, NULL, '2025-04-30 16:42:44'),
(484, '묻다', 'To ask', 'सोध्नु', 7, NULL, '2025-04-30 16:42:44'),
(485, '좌식 문화', 'Floor sitting culture', 'भूइँमा बस्ने संस्कार', 7, NULL, '2025-04-30 16:42:44'),
(486, '생활필수품', 'Daily necessaries', 'दैनिक जीवनका  आवश्यक सामान', 7, NULL, '2025-04-30 16:42:44'),
(487, '세면도구', 'Personal care', 'बाथरूपमको सामान', 7, NULL, '2025-04-30 16:42:44'),
(488, '화장실', 'Toilet', 'शौचालय', 7, NULL, '2025-04-30 16:42:44'),
(489, '회사', 'Company', 'कम्पनी', 7, NULL, '2025-04-30 16:42:44'),
(490, '식당', 'Restaurant', 'रेष्टुरेण्ट', 7, NULL, '2025-04-30 16:42:44'),
(491, '기숙사', 'Dormitory', 'होस्टेल', 7, NULL, '2025-04-30 16:42:44'),
(492, '세탁소', 'Laundromat', 'लुगा धुने ठाउँ', 7, NULL, '2025-04-30 16:42:44'),
(493, '미용실', 'Hair salon beauty parlor', 'सैलुन ब्युटी पार्लर', 7, NULL, '2025-04-30 16:42:44'),
(494, '편의점', 'Convenience store', 'चौबीस घण्टे पसल', 7, NULL, '2025-04-30 16:42:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_word`
--
ALTER TABLE `daily_word`
  ADD PRIMARY KEY (`id`),
  ADD KEY `word_id` (`word_id`);

--
-- Indexes for table `grammar_lessons`
--
ALTER TABLE `grammar_lessons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grammar_quizzes`
--
ALTER TABLE `grammar_quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `quiz_results`
--
ALTER TABLE `quiz_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- Indexes for table `words`
--
ALTER TABLE `words`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lesson_id` (`lesson_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_word`
--
ALTER TABLE `daily_word`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grammar_lessons`
--
ALTER TABLE `grammar_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grammar_quizzes`
--
ALTER TABLE `grammar_quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quiz_results`
--
ALTER TABLE `quiz_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `words`
--
ALTER TABLE `words`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=498;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_word`
--
ALTER TABLE `daily_word`
  ADD CONSTRAINT `daily_word_ibfk_1` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`);

--
-- Constraints for table `grammar_quizzes`
--
ALTER TABLE `grammar_quizzes`
  ADD CONSTRAINT `grammar_quizzes_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `grammar_lessons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_progress_ibfk_2` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);

--
-- Constraints for table `words`
--
ALTER TABLE `words`
  ADD CONSTRAINT `words_ibfk_1` FOREIGN KEY (`lesson_id`) REFERENCES `lessons` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
