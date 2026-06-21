-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql105.infinityfree.com
-- Generation Time: Feb 26, 2026 at 11:21 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_39948816_paris`
--

-- --------------------------------------------------------

--
-- Table structure for table `backgrounds`
--

CREATE TABLE `backgrounds` (
  `id` int(6) UNSIGNED NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `backgrounds`
--

INSERT INTO `backgrounds` (`id`, `image_url`, `created_at`) VALUES
(6, 'uploads/695bbc2a0497f.jpg', '2026-01-05 13:27:06'),
(5, 'uploads/69249706bd652.jpg', '2025-11-24 17:33:58');

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT 'گفتگوی جدید',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `sender` enum('user','admin') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `user_id`, `sender`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'user', 'سلام آقا غذای ما چی شد', 1, '2025-11-20 02:46:29'),
(2, 1, 'admin', 'در حال پخت هستیم', 1, '2025-11-20 04:01:22'),
(3, 1, 'user', 'سلام', 1, '2025-11-20 04:03:03'),
(4, 1, 'user', 'باشه مشکلی نیست', 1, '2025-11-20 04:09:53'),
(5, 1, 'admin', '**', 1, '2025-11-20 04:30:59'),
(6, 1, 'admin', 'خوبه', 1, '2025-11-20 04:53:08'),
(7, 1, 'user', 'Ok', 1, '2025-11-20 04:57:51'),
(8, 1, 'user', 'بازم دمت گرم', 1, '2025-11-20 04:58:09'),
(9, 1, 'admin', 'خواخش', 1, '2025-11-20 04:58:29'),
(10, 1, 'admin', 'خواهش', 1, '2025-11-20 04:59:33'),
(11, 1, 'user', '😴', 1, '2025-11-20 05:06:32'),
(12, 1, 'admin', 'عهههه خنده خنده', 1, '2025-11-20 05:09:35'),
(13, 1, 'user', 'زهر مار می خند', 1, '2025-11-20 05:12:19'),
(14, 1, 'admin', 'خری واقعا', 1, '2025-11-20 05:16:35'),
(15, 1, 'user', 'خودت خری', 1, '2025-11-20 05:24:03'),
(16, 1, 'admin', 'خری واقعا', 1, '2025-11-20 05:24:32'),
(17, 1, 'user', 'ببین ازت می‌خوام این کدهای پایینی که برات فرستادمو اون قسمتی که اربر چت می‌کنه با پشتیبانی به همین شماره کاربر داره پیام میده هر وقت که پیام میده اینور تو قسمت خود مشتری که داره پیام میده سرورش رفرش بشه چون که دیگه نیازی نباشه کاربر موقعی که داره چت می‌کنه با پشتیبانی برای اینکه بخواد پیام پشتیبانی رو ببینه صفحه خودشو رفرش نکنه و سرور رفرش بشه دقیقاً در زمانی که پشتیبانی یا سرآشپز دقیقاً پیامشو به همین شماره می‌فرسته', 1, '2025-11-20 05:34:35'),
(18, 1, 'user', 'سلام من نیما', 1, '2025-11-20 07:27:45'),
(19, 1, 'admin', 'آره میدونم', 1, '2025-11-20 07:28:20'),
(20, 1, 'user', 'Ievddb', 1, '2025-11-20 07:57:48'),
(21, 1, 'user', 'من نیما هستم و برادرم سینا است', 1, '2025-11-20 09:31:49'),
(22, 1, 'admin', 'آره میدونستم.', 1, '2025-11-20 09:32:22'),
(23, 2, 'user', 'سلام من گشنمه', 1, '2025-11-20 09:37:30'),
(24, 2, 'user', 'شما چه غذایی پیشنهاد میدین', 1, '2025-11-20 09:37:40'),
(25, 2, 'admin', 'باشه مشکلی نیست', 1, '2025-11-20 09:38:10'),
(26, 2, 'user', 'پیتزا خوبه یا برگر', 1, '2025-11-20 09:38:14'),
(27, 2, 'user', 'زود باش جواب بده دیگه', 1, '2025-11-20 09:38:29'),
(28, 2, 'admin', 'به نظر من پیتزا چون گرون تره', 1, '2025-11-20 09:38:30'),
(29, 2, 'user', 'من یک نفرم', 1, '2025-11-20 09:38:53'),
(30, 3, 'user', 'سلام', 1, '2025-11-20 10:46:05'),
(31, 2, 'admin', 'خری واقعا', 1, '2025-11-20 11:21:03'),
(32, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:21:13'),
(33, 3, 'admin', 'در حال پخت هستیم', 1, '2025-11-20 11:27:14'),
(34, 3, 'user', 'آها باشه', 1, '2025-11-20 11:31:08'),
(35, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:38:31'),
(36, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:51:57'),
(37, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:52:00'),
(38, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:52:02'),
(39, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:12'),
(40, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:12'),
(41, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:18'),
(42, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:18'),
(43, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:23'),
(44, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:23'),
(45, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:27'),
(46, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 11:59:27'),
(47, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 12:00:57'),
(48, 3, 'admin', 'سلام! پیک رسید 🛵. آقا غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-20 12:00:57'),
(49, 1, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-21 04:06:41'),
(50, 3, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-21 04:06:43'),
(51, 3, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-21 04:06:46'),
(52, 5, 'user', 'Salam', 1, '2025-11-22 04:24:14'),
(53, 5, 'admin', 'Test', 1, '2025-11-22 09:42:15'),
(54, 5, 'admin', 'آفرین', 1, '2025-11-23 06:15:13'),
(55, 6, 'user', 'dyjyfhvjhku.y,tmfngdfbxvc', 1, '2025-11-23 08:21:10'),
(56, 5, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-23 10:04:27'),
(57, 1, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-23 10:04:35'),
(58, 1, 'user', 'سلام', 1, '2025-11-23 11:50:01'),
(59, 1, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-23 11:51:30'),
(60, 1, 'admin', 'سلام! پیک رسید 🛵. مشتری عزیز غذاتون جلوی دربتون هست، لطفاً تحویل بگیرید. نوش جان!', 0, '2025-11-23 11:51:33'),
(61, 1, 'admin', 'خوبی', 1, '2025-11-23 11:52:11'),
(62, 1, 'user', 'سلامی مجدد', 1, '2025-11-23 23:24:24'),
(63, 1, 'admin', 'vhnh', 0, '2025-11-23 23:38:52'),
(64, 1, 'user', 'باشه', 1, '2025-11-24 04:28:34'),
(65, 1, 'admin', 'خوبه', 0, '2025-11-24 04:33:09'),
(66, 1, 'user', 'سلام عشقم', 1, '2025-11-24 10:49:06'),
(67, 1, 'admin', 'ممنونم', 0, '2025-11-24 10:49:34'),
(68, 1, 'admin', 'سلام', 0, '2025-11-24 12:49:52'),
(69, 1, 'user', '😍❤️💕👌🤡😈🤖💩🙊🙈🦄🐔🐲🐒🦍🦧🦮', 0, '2025-11-24 13:06:35'),
(70, 9, 'user', '⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.', 1, '2025-11-25 01:34:49'),
(71, 9, 'user', '⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.', 1, '2025-11-25 01:35:24'),
(72, 9, 'user', '⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.', 1, '2025-11-25 01:37:25'),
(73, 9, 'user', '⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.', 1, '2025-11-25 01:37:52'),
(74, 9, 'user', '⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.', 1, '2025-11-25 01:38:04'),
(75, 9, 'user', '⚠️ هشدار: تمام کلیدهای هوش مصنوعی (D-ID) منقضی شده‌اند یا اعتبار ندارند. لطفاً فایل api_handler.php را باز کرده و کلیدهای جدید اضافه کنید.', 1, '2025-11-25 01:44:12'),
(76, 8, 'user', 'سلام', 1, '2025-11-25 06:59:45'),
(77, 8, 'admin', 'سلام', 0, '2025-11-25 07:00:03'),
(78, 8, 'user', 'چطوری', 1, '2025-11-26 08:29:50'),
(79, 8, 'user', 'hjvsdjcd', 1, '2025-11-26 22:12:01'),
(80, 8, 'admin', 'kjbjjjkbj\\', 0, '2025-11-26 22:12:54'),
(81, 8, 'user', 'Hdbgx', 1, '2025-11-29 21:17:22'),
(82, 8, 'user', 'سلام منم نیما', 1, '2025-11-30 08:53:59'),
(83, 8, 'user', 'b v vb', 1, '2025-12-10 11:47:53'),
(84, 8, 'user', 'Boom', 1, '2025-12-20 01:23:43'),
(85, 8, 'user', 'b v vb', 0, '2026-01-31 03:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `amount` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `payment_status` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'pending',
  `authority` varchar(255) DEFAULT NULL,
  `ref_id` varchar(255) DEFAULT NULL,
  `selected_color` varchar(50) DEFAULT NULL,
  `order_details_json` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `customer_name`, `phone`, `email`, `address`, `amount`, `quantity`, `payment_status`, `created_at`, `status`, `authority`, `ref_id`, `selected_color`, `order_details_json`) VALUES
(23, 3, 1, 'Nima', '093680548712', 'hzn806512@gmail.com', 'منننت', 2000, 1, 'پرداخت موفق', '2025-11-20 10:47:15', 'sent', NULL, NULL, NULL, NULL),
(24, 3, 1, 'Nima', '093680548712', 'hzn806512@gmail.com', 'منه', 4000, 2, 'پرداخت موفق', '2025-11-20 10:48:19', 'sent', NULL, NULL, NULL, NULL),
(25, 3, 1, 'Nima', '093680548712', 'hzn806512@gmail.com', 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkhjkhjkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk', 2000, 1, 'پرداخت موفق', '2025-11-20 11:19:45', 'sent', NULL, NULL, NULL, NULL),
(26, 1, 1, 'Nima', '09368054871', 'hzn80651@gmail.com', 'نتتارال', 4000, 2, 'در انتظار پرداخت', '2025-11-21 00:09:26', 'sent', NULL, NULL, NULL, NULL),
(27, 1, 1, 'Nima', '09368054871', 'hzn80651@gmail.com', 'Hvvguugvgucgugu guvugvgjv', 2000, 1, 'در انتظار پرداخت', '2025-11-21 10:24:59', 'sent', NULL, NULL, NULL, NULL),
(28, 5, 1, 'Yasin Sarooje', '09155895827', 'yasinsaroje@gmail.com', 'P@ris_Cyber_7X9#Food!', 2000, 1, 'در انتظار پرداخت', '2025-11-22 09:41:16', 'sent', NULL, NULL, NULL, NULL),
(29, 1, 1, 'Nima', '09368054871', 'hzn80651@gmail.com', 'ooooooooooooooooooooooooo', 2000, 1, 'در انتظار پرداخت', '2025-11-23 11:28:36', 'sent', NULL, NULL, NULL, NULL),
(30, 1, 1, 'Nima', '09368054871', 'hzn80651@gmail.com', 'اااااااااااااااااااااااااااااااااااااااا', 2000, 1, 'در انتظار پرداخت', '2025-11-23 11:50:34', 'sent', NULL, NULL, NULL, NULL),
(31, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'ئدتذتاذتاتاذ تطاذتارطزازار ر ز ات ات', 10000, 1, 'در انتظار پرداخت', '2025-11-24 01:54:48', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"type\":\"main\",\"color\":\"فیروزه ای،مشکی،توسی\",\"qty\":1,\"price\":10000}]'),
(32, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'fhyfgjhhghjg', 0, 1, 'در انتظار پرداخت', '2025-11-24 04:23:55', 'pending', NULL, NULL, NULL, ''),
(33, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'نااناذتلالابلیبیبیببیبیسبی', 0, 1, 'در انتظار پرداخت', '2025-11-24 04:34:51', 'pending', NULL, NULL, NULL, ''),
(34, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'شهرک شاهد شاهد یک سه راه اول سمت چپ پلاک نوزده', 10000, 1, 'در انتظار پرداخت', '2025-11-24 10:13:38', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":10000,\"type\":\"main\"}]'),
(35, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent', 10000, 1, 'در انتظار پرداخت', '2025-11-24 10:15:35', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":10000,\"type\":\"main\"}]'),
(36, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'شهرک شاهد شاهد یک سه راه اول سمت چپ پلاک نوزده', 10000, 1, 'در انتظار پرداخت', '2025-11-24 10:36:44', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":10000}]'),
(37, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'بجنورد شهرک شاهد شاهد یک', 10000, 1, 'در انتظار پرداخت', '2025-11-24 10:47:06', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"آبی\",\"qty\":1,\"price\":10000}]'),
(38, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', '&lt;?php\r\n// شروع سشن\r\nif (session_status() === PHP_SESSION_NONE) {\r\n    session_start();\r\n}\r\n\r\n// اطلاعات اتصال\r\n$servername = &quot;sql105.infinityfree.com&quot;;\r\n$username = &quot;if0_39948816&quot;;\r\n$password = &quot;147280021HZK&quot;;\r\n$dbname = &quot;if0_39948816_paris&quot;;\r\n\r\n$conn = new mysqli($servername, $username, $password, $dbname);\r\n\r\nif ($conn-&gt;connect_error) {\r\n    die(&quot;Connection failed: &quot; . $conn-&gt;connect_error);\r\n}\r\n\r\n$conn-&gt;set_charset(&quot;utf8mb4&quot;);\r\n\r\n// --- آپدیت خودکار و هوشمند دیتابیس (بدون خطا) ---\r\n\r\n// 1. ساخت جدول تصاویر پس‌زمینه\r\n$conn-&gt;query(&quot;CREATE TABLE IF NOT EXISTS backgrounds (\r\n    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    image_url VARCHAR(500) NOT NULL,\r\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n)&quot;);\r\n\r\n// 2. اضافه کردن ستون‌های جدید (با بررسی اینکه آیا قبلاً وجود دارند یا نه)\r\n$columns_to_add = [\r\n    &#039;products&#039; =&gt; [\r\n        &#039;gallery_images&#039; =&gt; &#039;LONGTEXT DEFAULT NULL&#039;,\r\n        &#039;available_colors&#039; =&gt; &#039;TEXT DEFAULT NULL&#039;\r\n    ],\r\n    &#039;orders&#039; =&gt; [\r\n        &#039;status&#039; =&gt; &quot;VARCHAR(20) DEFAULT &#039;pending&#039;&quot;,\r\n        &#039;authority&#039; =&gt; &quot;VARCHAR(255) NULL&quot;,\r\n        &#039;ref_id&#039; =&gt; &quot;VARCHAR(255) NULL&quot;,\r\n        &#039;order_details_json&#039; =&gt; &quot;LONGTEXT DEFAULT NULL&quot;\r\n    ]\r\n];\r\n\r\nforeach ($columns_to_add as $table =&gt; $cols) {\r\n    foreach ($cols as $col_name =&gt; $col_def) {\r\n        $check = $conn-&gt;query(&quot;SHOW COLUMNS FROM $table LIKE &#039;$col_name&#039;&quot;);\r\n        if ($check-&gt;num_rows == 0) {\r\n            $conn-&gt;query(&quot;ALTER TABLE $table ADD COLUMN $col_name $col_def&quot;);\r\n        }\r\n    }\r\n}\r\n// -------------------------------------------\r\n\r\nfunction cleanInput($data) {\r\n    global $conn;\r\n    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));\r\n}\r\n\r\n// تابع جدید: ترجمه رنگ فارسی به کد رنگ CSS\r\nfunction translateColor($persianColor) {\r\n    $colors = [\r\n        &#039;قرمز&#039; =&gt; &#039;#ef4444&#039;, &#039;سرخ&#039; =&gt; &#039;#ef4444&#039;,\r\n        &#039;آبی&#039; =&gt; &#039;#3b82f6&#039;, &#039;سرمه ای&#039; =&gt; &#039;#1e3a8a&#039;, &#039;آبی آسمانی&#039; =&gt; &#039;#0ea5e9&#039;,\r\n        &#039;سبز&#039; =&gt; &#039;#22c55e&#039;, &#039;یشمی&#039; =&gt; &#039;#064e3b&#039;, &#039;لجنی&#039; =&gt; &#039;#3f6212&#039;,\r\n        &#039;زرد&#039; =&gt; &#039;#eab308&#039;, &#039;طلایی&#039; =&gt; &#039;#d4af37&#039;,\r\n        &#039;مشکی&#039; =&gt; &#039;#000000&#039;, &#039;سیاه&#039; =&gt; &#039;#000000&#039;,\r\n        &#039;سفید&#039; =&gt; &#039;#ffffff&#039;,\r\n        &#039;طوسی&#039; =&gt; &#039;#6b7280&#039;, &#039;خاکستری&#039; =&gt; &#039;#6b7280&#039;, &#039;نوک مدادی&#039; =&gt; &#039;#374151&#039;,\r\n        &#039;قهوه ای&#039; =&gt; &#039;#78350f&#039;, &#039;کرم&#039; =&gt; &#039;#fef3c7&#039;, &#039;شتری&#039; =&gt; &#039;#d97706&#039;,\r\n        &#039;بنفش&#039; =&gt; &#039;#a855f7&#039;, &#039;یاسی&#039; =&gt; &#039;#d8b4fe&#039;,\r\n        &#039;صورتی&#039; =&gt; &#039;#ec4899&#039;, &#039;گلبهی&#039; =&gt; &#039;#fb7185&#039;,\r\n        &#039;نارنجی&#039; =&gt; &#039;#f97316&#039;,\r\n        &#039;زرشکی&#039; =&gt; &#039;#7f1d1d&#039;\r\n    ];\r\n    \r\n    $clean = trim($persianColor);\r\n    \r\n    // اگر خودش کد رنگ انگلیسی یا هگز بود، همان را برگردان\r\n    if (preg_match(&#039;/^#[a-f0-9]{6}$/i&#039;, $clean) || preg_match(&#039;/^[a-z]+$/i&#039;, $clean)) {\r\n        return $clean;\r\n    }\r\n    \r\n    // اگر در لیست بود برگردان، وگرنه پیش‌فرض مشکی\r\n    return isset($colors[$clean]) ? $colors[$clean] : &#039;#000000&#039;;\r\n}\r\n?&gt;', 10000, 1, 'در انتظار پرداخت', '2025-11-24 11:54:10', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":10000}]'),
(39, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', '&lt;?php\r\n// شروع سشن\r\nif (session_status() === PHP_SESSION_NONE) {\r\n    session_start();\r\n}\r\n\r\n// اطلاعات اتصال\r\n$servername = &quot;sql105.infinityfree.com&quot;;\r\n$username = &quot;if0_39948816&quot;;\r\n$password = &quot;147280021HZK&quot;;\r\n$dbname = &quot;if0_39948816_paris&quot;;\r\n\r\n$conn = new mysqli($servername, $username, $password, $dbname);\r\n\r\nif ($conn-&gt;connect_error) {\r\n    die(&quot;Connection failed: &quot; . $conn-&gt;connect_error);\r\n}\r\n\r\n$conn-&gt;set_charset(&quot;utf8mb4&quot;);\r\n\r\n// --- آپدیت خودکار و هوشمند دیتابیس (بدون خطا) ---\r\n\r\n// 1. ساخت جدول تصاویر پس‌زمینه\r\n$conn-&gt;query(&quot;CREATE TABLE IF NOT EXISTS backgrounds (\r\n    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,\r\n    image_url VARCHAR(500) NOT NULL,\r\n    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\r\n)&quot;);\r\n\r\n// 2. اضافه کردن ستون‌های جدید (با بررسی اینکه آیا قبلاً وجود دارند یا نه)\r\n$columns_to_add = [\r\n    &#039;products&#039; =&gt; [\r\n        &#039;gallery_images&#039; =&gt; &#039;LONGTEXT DEFAULT NULL&#039;,\r\n        &#039;available_colors&#039; =&gt; &#039;TEXT DEFAULT NULL&#039;\r\n    ],\r\n    &#039;orders&#039; =&gt; [\r\n        &#039;status&#039; =&gt; &quot;VARCHAR(20) DEFAULT &#039;pending&#039;&quot;,\r\n        &#039;authority&#039; =&gt; &quot;VARCHAR(255) NULL&quot;,\r\n        &#039;ref_id&#039; =&gt; &quot;VARCHAR(255) NULL&quot;,\r\n        &#039;order_details_json&#039; =&gt; &quot;LONGTEXT DEFAULT NULL&quot;\r\n    ]\r\n];\r\n\r\nforeach ($columns_to_add as $table =&gt; $cols) {\r\n    foreach ($cols as $col_name =&gt; $col_def) {\r\n        $check = $conn-&gt;query(&quot;SHOW COLUMNS FROM $table LIKE &#039;$col_name&#039;&quot;);\r\n        if ($check-&gt;num_rows == 0) {\r\n            $conn-&gt;query(&quot;ALTER TABLE $table ADD COLUMN $col_name $col_def&quot;);\r\n        }\r\n    }\r\n}\r\n// -------------------------------------------\r\n\r\nfunction cleanInput($data) {\r\n    global $conn;\r\n    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));\r\n}\r\n\r\n// تابع جدید: ترجمه رنگ فارسی به کد رنگ CSS\r\nfunction translateColor($persianColor) {\r\n    $colors = [\r\n        &#039;قرمز&#039; =&gt; &#039;#ef4444&#039;, &#039;سرخ&#039; =&gt; &#039;#ef4444&#039;,\r\n        &#039;آبی&#039; =&gt; &#039;#3b82f6&#039;, &#039;سرمه ای&#039; =&gt; &#039;#1e3a8a&#039;, &#039;آبی آسمانی&#039; =&gt; &#039;#0ea5e9&#039;,\r\n        &#039;سبز&#039; =&gt; &#039;#22c55e&#039;, &#039;یشمی&#039; =&gt; &#039;#064e3b&#039;, &#039;لجنی&#039; =&gt; &#039;#3f6212&#039;,\r\n        &#039;زرد&#039; =&gt; &#039;#eab308&#039;, &#039;طلایی&#039; =&gt; &#039;#d4af37&#039;,\r\n        &#039;مشکی&#039; =&gt; &#039;#000000&#039;, &#039;سیاه&#039; =&gt; &#039;#000000&#039;,\r\n        &#039;سفید&#039; =&gt; &#039;#ffffff&#039;,\r\n        &#039;طوسی&#039; =&gt; &#039;#6b7280&#039;, &#039;خاکستری&#039; =&gt; &#039;#6b7280&#039;, &#039;نوک مدادی&#039; =&gt; &#039;#374151&#039;,\r\n        &#039;قهوه ای&#039; =&gt; &#039;#78350f&#039;, &#039;کرم&#039; =&gt; &#039;#fef3c7&#039;, &#039;شتری&#039; =&gt; &#039;#d97706&#039;,\r\n        &#039;بنفش&#039; =&gt; &#039;#a855f7&#039;, &#039;یاسی&#039; =&gt; &#039;#d8b4fe&#039;,\r\n        &#039;صورتی&#039; =&gt; &#039;#ec4899&#039;, &#039;گلبهی&#039; =&gt; &#039;#fb7185&#039;,\r\n        &#039;نارنجی&#039; =&gt; &#039;#f97316&#039;,\r\n        &#039;زرشکی&#039; =&gt; &#039;#7f1d1d&#039;\r\n    ];\r\n    \r\n    $clean = trim($persianColor);\r\n    \r\n    // اگر خودش کد رنگ انگلیسی یا هگز بود، همان را برگردان\r\n    if (preg_match(&#039;/^#[a-f0-9]{6}$/i&#039;, $clean) || preg_match(&#039;/^[a-z]+$/i&#039;, $clean)) {\r\n        return $clean;\r\n    }\r\n    \r\n    // اگر در لیست بود برگردان، وگرنه پیش‌فرض مشکی\r\n    return isset($colors[$clean]) ? $colors[$clean] : &#039;#000000&#039;;\r\n}\r\n?&gt;', 10000, 1, 'در انتظار پرداخت', '2025-11-24 11:54:23', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":10000}]'),
(40, 1, 5, 'Nima', '09368054871', 'hzn80651@gmail.com', 'نتذتتترزلزبببببببببببببببببببببببببببب', 10000, 1, 'در انتظار پرداخت', '2025-11-24 12:44:52', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":10000}]'),
(41, 8, 6, 'نیما', '09368054871', 'hzn80651@gmail.com', 'ljnjkbhjkjhhghvhvjvggfgf', 120000, 1, 'در انتظار پرداخت', '2025-11-24 21:47:59', 'pending', NULL, NULL, NULL, '[{\"id\":\"6\",\"name\":\"کاپشن\",\"color\":\"لجنی\",\"qty\":1,\"price\":100000}]'),
(42, 8, 5, 'نیما', '09368054871', 'hzn80651@gmail.com', 'ممممممممممممممممممممممممممممممممممممممممممممممممممممممم', 10000, 1, 'در انتظار پرداخت', '2025-11-24 22:11:34', 'pending', NULL, NULL, NULL, '[{\"id\":\"5\",\"name\":\"کاپشن\",\"color\":\"سبز\",\"qty\":1,\"price\":10000}]'),
(43, 8, 10, 'نیما', '09368054871', 'hzn80651@gmail.com', 'Icigcgjcgj gicgj ihvih bj hi hi hi hi', 4630000, 5, 'در انتظار پرداخت', '2025-11-26 23:39:14', 'pending', NULL, NULL, NULL, '[{\"id\":\"10\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":3,\"price\":1500000},{\"id\":\"5\",\"name\":\"کاپشن (ست)\",\"color\":\"-\",\"qty\":1,\"price\":10000},{\"id\":\"6\",\"name\":\"کاپشن (ست)\",\"color\":\"-\",\"qty\":1,\"price\":100000}]'),
(44, 5, 10, 'Yasin Sarooje', '09155895827', 'yasinsaroje@gmail.com', 'شهرک شاهد شاهد ۱۸ انتهای کوچه', 1520000, 1, 'در انتظار پرداخت', '2025-11-27 00:55:48', 'pending', NULL, NULL, NULL, '[{\"id\":\"10\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":1500000}]'),
(45, 8, 9, 'نیما', '09368054871', 'hzn80651@gmail.com', ',kf7654546547654754762764726171', 1050000, 1, 'در انتظار پرداخت', '2025-12-01 11:01:52', 'pending', NULL, NULL, NULL, '[{\"id\":\"9\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":1,\"price\":850000}]'),
(46, 8, 6, 'نیما', '09368054871', 'hzn80651@gmail.com', 'No cmxnxnxnxn', 520000, 5, 'در انتظار پرداخت', '2025-12-10 02:43:07', 'pending', NULL, NULL, NULL, '[{\"id\":\"6\",\"name\":\"کاپشن\",\"color\":\"مشکی\",\"qty\":5,\"price\":100000}]'),
(47, 8, 9, '????', '09368054871', 'hzn80651@gmail.com', 'gnfgghnghnhnn', 1050000, 1, '?? ?????? ??????', '2025-12-10 11:49:07', 'pending', NULL, NULL, NULL, '[{\"id\":\"9\",\"name\":\"?????\",\"color\":\"???\",\"qty\":1,\"price\":850000}]'),
(48, 8, 9, '????', '09368054871', 'hzn80651@gmail.com', 'dkvjkbvjxhcv hgc', 1050000, 1, '?? ?????? ??????', '2025-12-10 12:11:34', 'pending', NULL, NULL, NULL, '[{\"id\":\"9\",\"name\":\"?????\",\"color\":\"???\",\"qty\":1,\"price\":850000}]'),
(49, 8, 11, 'نیما', '09368054871', 'hzn80651@gmail.com', 'Fucdyfh🎟️🎫🎟️🤗🎫😄🤗🎟️🎫🤗', 195195564, 3, 'در انتظار پرداخت', '2025-12-20 01:27:09', 'pending', NULL, NULL, NULL, '[{\"id\":\"11\",\"name\":\"نيما حسین‌زاده\",\"color\":\"بنفش\",\"qty\":3,\"price\":65065065}]');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `image` text NOT NULL,
  `available_colors` text DEFAULT NULL,
  `gallery_images` longtext DEFAULT NULL,
  `shipping_cost` int(10) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `available_colors`, `gallery_images`, `shipping_cost`) VALUES
(5, 'کاپشن', 'کیفیت آن واقعا بسیار خوب است', 10000, 'uploads/69242aa9222d6.png', 'مشکی:#000000,سبز:#22c55e', '[\"uploads\\/69242aaa38ddd.png\",\"uploads\\/69242aab66bc3.png\"]', 20000),
(6, 'کاپشن', 'بسار گرم و با کیفیت از آلمان', 100000, 'uploads/6924b41fae986.jpg', 'سفید:#ffffff,مشکی:#000000,سورمه ای:#0a003d,لجنی:#003d29', '[]', 20000),
(7, 'کاپشن', 'مهم نیست', 500000, 'uploads/6925c0be081ee.jpg', 'سورمه ای پر رنگ:#000242,مشکی:#000000', '[]', 200000),
(8, 'کاپشن', 'مهمه', 550000, 'uploads/6925c1746b947.jpg', 'طوسی:#6b7280,مشکی:#000000,سرمه‌ای:#1e3a8a', '[]', 200000),
(9, 'کاپشن', 'اینم مهمه', 850000, 'uploads/6925c239280d7.jpg', 'سرمه‌ای:#1e3a8a,طوسی:#6b7280,مشکی:#000000,لجنی:#043800,کرم:#fef3c7', '[]', 200000),
(10, 'کاپشن', 'مهمه اما مهمه', 1500000, 'uploads/6925c37fdc9b2.png', 'لجنی:#003d0c,کرم:#fef3c7,مشکی:#000000', '[]', 20000),
(11, 'نيما حسین‌زاده', 'Jgj😚', 65065065, 'uploads/69466bb69f6ec.jpg', 'قرمز:#ef4444,آبی:#3b82f6,زرد:#eab308,بنفش:#a855f7,صورتی:#ec4899,نارنجی:#ea580c,طوسی:#6b7280,قهوه‌ای:#78350f,سرمه‌ای:#1e3a8a', '[\"uploads\\/69466bb70513b.jpg\",\"uploads\\/69466bb821e44.png\",\"uploads\\/69466bb8a49ed.jpg\"]', 369),
(12, 'nghgh', 'mmhjhjmj', 43342, 'uploads/6946e8b5a20f9.jpg', 'قرمز:#ef4444,سرمه‌ای:#1e3a8a,کرم:#fef3c7', '[]', 2312);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(6) UNSIGNED NOT NULL,
  `product_id` int(6) UNSIGNED NOT NULL,
  `image_url` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trending_items`
--

CREATE TABLE `trending_items` (
  `id` int(6) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `trending_items`
--

INSERT INTO `trending_items` (`id`, `title`, `subtitle`, `image_url`, `created_at`) VALUES
(1, 'Summer Vibe', 'کالکشن تابستانه با طراحی مینیمال', 'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?q=80&w=2000', '2025-12-10 19:37:46'),
(2, 'Classic Men', 'کت و شلوارهای ایتالیایی دست‌دوز', 'https://images.unsplash.com/photo-1539109136881-3be0616acf4b?q=80&w=2000', '2025-12-10 19:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_pic` varchar(500) DEFAULT 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png',
  `verification_code` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_persian_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone`, `email`, `password`, `profile_pic`, `verification_code`, `is_verified`, `created_at`) VALUES
(2, 'Sina', '09019429535', 'sina_hz2000@yahoo.com', '$2y$10$tdA0JwRr29nR5QLzGf1jcevqeitQPCTTsK06TLpBiZi.T8aJMeNii', 'uploads/Profile_09019429535.jpg', NULL, 1, '2025-11-20 09:36:31'),
(3, 'Nima', '093680548712', 'hzn806512@gmail.com', '$2y$10$XhyKcjpQ7keF/K5ikd8uHuWhSCc8KOW33TT080CNfCOoj0AlK.HZa', 'uploads/Profile_093680548712.png', NULL, 1, '2025-11-20 10:44:20'),
(4, 'ال', '3521', 'ذبل', '$2y$10$V9eL1utY3O0B/stghb/xPesnOUR9EqaDjMVfXWC1mPOA93gz5C9ey', 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png', '6619', 0, '2025-11-20 12:02:16'),
(5, 'Yasin Sarooje', '09155895827', 'yasinsaroje@gmail.com', '$2y$10$OGIe3lsVswQibwoW7I0Dn.Bb.R29yUacyYKggW3cLPvtHi3W9wnz2', 'uploads/Profile_09155895827.jpg', NULL, 1, '2025-11-22 04:23:58'),
(6, 'aref', '09217302209', 'gcdchycjjgthyf@gmail.com', '$2y$10$aL4NOjfUpTBKPLgR74JM7.5kklT5j5b2TMFLqkLBDs4DrGmHyFLjK', 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png', NULL, 1, '2025-11-23 08:20:35'),
(7, 'nima Nima2136', '09418876976', 'hzn806511@gmail.com', '$2y$10$F2hDBw2DRU/0WjIwV1FtzuKC8uMlMi6dvpXWZWKlGlGSVcCjXQrFK', 'uploads/Profile_09418876976.png', NULL, 1, '2025-11-23 23:26:26'),
(8, 'نیما', '09368054871', 'hzn80651@gmail.com', '$2y$10$L1bK4mt.W30P0X8TmOpUe.AHBAqZBaB/ITMxj4Yy/PcLNwVyoy2jW', 'uploads/Profile_8_1767619770.png', NULL, 1, '2025-11-24 21:26:11'),
(9, 'System Alert', '0000000000', 'sys@admin.com', '123', 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png', NULL, 1, '2025-11-25 01:34:49'),
(10, 'کوروش جاوید', '09026336049', 'kourosh618@gmail.com', '$2y$10$2UVb0NucHtZe7TnLyi2jXefjPiOuNddUg2.eTpjjl0y./76OXAWmq', 'https://cdn.jsdelivr.net/gh/microsoft/fluentui-emoji@latest/assets/Person/3D/person_3d.png', '6553', 0, '2025-12-10 08:51:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `backgrounds`
--
ALTER TABLE `backgrounds`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trending_items`
--
ALTER TABLE `trending_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `backgrounds`
--
ALTER TABLE `backgrounds`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trending_items`
--
ALTER TABLE `trending_items`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
