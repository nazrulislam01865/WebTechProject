-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 12, 2025 at 04:21 PM
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
-- Database: `gobus`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bus_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `boarding_point` varchar(100) NOT NULL,
  `dropping_point` varchar(100) NOT NULL,
  `booking_id` varchar(10) NOT NULL,
  `route` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Completed','Upcoming','Cancelled') NOT NULL,
  `returnDate` date DEFAULT NULL,
  `transaction_id` varchar(50) DEFAULT NULL,
  `payment_method` varchar(20) DEFAULT NULL,
  `fare` decimal(10,2) NOT NULL DEFAULT 0.00,
  `operator_name` varchar(255) NOT NULL,
  `promo_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `bus_id`, `seat_number`, `phone_number`, `boarding_point`, `dropping_point`, `booking_id`, `route`, `date`, `status`, `returnDate`, `transaction_id`, `payment_method`, `fare`, `operator_name`, `promo_code`) VALUES
(1, 2, 1, 'A1', '01738251690', 'Dhaka To Rajshahi', 'Dhaka To Rajshahi', 'BK12345', 'Dhaka To Rajshahi', '2025-08-15', 'Completed', NULL, NULL, NULL, 0.00, '', NULL),
(2, 2, 1, 'A1', '01738251690', 'Dhaka To Barisal', 'Dhaka To Barisal', 'BK12346', 'Dhaka To Barisal', '2025-08-20', 'Cancelled', NULL, NULL, NULL, 0.00, '', NULL),
(3, 2, 1, 'A1', '01738251690', 'Dhaka To Cox\'s Bazar', 'Dhaka To Cox\'s Bazar', 'BK12347', 'Dhaka To Cox\'s Bazar', '2025-07-10', 'Completed', NULL, NULL, NULL, 0.00, '', NULL),
(4, 2, 1, 'A2', '01738251690', 'Saydabad Terminal -1', 'Barisal Terminal', 'BK54321', 'Dhaka To Barisal', '2025-08-29', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(5, 2, 1, 'A3', '01738251690', 'Saydabad Terminal -1', 'Barisal Terminal', 'BK54322', 'Dhaka To Barisal', '2025-08-29', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(6, 2, 3, 'B1', '01738251690', 'Dhaka', 'Rajshahi', 'BK54323', 'Dhaka To Rajshahi', '2025-09-30', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(7, 2, 4, 'C1', '01738251690', 'Dhaka', 'Cox\'s Bazar', 'BK54324', 'Dhaka To Cox\'s Bazar', '2025-09-30', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(8, 2, 3, 'A3', '01738251690', 'Dhaka', 'Rajshahi', 'BK61375', 'Dhaka To Rajshahi', '2025-09-30', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(9, 2, 6, 'A3', '01738251690', 'Dhaka', 'Khulna', 'BK96553', 'Dhaka To Khulna', '2025-09-30', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(10, 2, 6, 'B3', '01738251690', 'Dhaka', 'Khulna', 'BK81099', 'Dhaka To Khulna', '2025-09-30', 'Upcoming', NULL, NULL, NULL, 0.00, '', NULL),
(11, 2, 6, '', '01738251690', 'Dhaka', 'Khulna', 'BK37991', 'Dhaka To Khulna', '2025-09-30', 'Upcoming', NULL, 'XYZTKSYJFDS', NULL, 0.00, '', NULL),
(12, 2, 3, 'B3', '01738251690', 'Dhaka', 'Rajshahi', 'BK61556', 'Dhaka To Rajshahi', '2025-09-30', 'Upcoming', NULL, 'XYZTKSYJFDSS', 'mobile_banking', 1200.00, '', NULL),
(13, 2, 6, 'C1', '01738251690', 'Dhaka', 'Khulna', 'BK52394', 'Dhaka To Khulna', '2025-09-30', 'Cancelled', NULL, 'XYZTKSYJFDSS', 'mobile_banking', 1000.00, '', NULL),
(14, 2, 3, 'B4', '01738251690', 'Dhaka', 'Rajshahi', 'BK92745', 'Dhaka To Rajshahi', '2025-09-30', 'Upcoming', NULL, 'XYZTKSYJFDS', 'mobile_banking', 1200.00, 'Green Line Paribahan', NULL),
(15, 2, 17, 'A3', '01738251690', 'Dhaka', 'Khulna', 'BK92180', 'Dhaka To Khulna', '2025-09-25', 'Upcoming', NULL, 'XYZTKSYJFDS', 'mobile_banking', 1250.00, 'Desh Travels', NULL),
(16, 2, 17, 'A4', '01738251690', 'Dhaka', 'Khulna', 'BK71117', 'Dhaka To Khulna', '2025-09-25', 'Upcoming', NULL, 'XYZTKSYJFDSS', 'mobile_banking', 1250.00, 'Desh Travels', NULL),
(17, 3, 5, 'A1', '01956351202', 'Dhaka', 'Sylhet', 'BK71008', 'Dhaka To Sylhet', '2025-09-30', 'Upcoming', NULL, 'XYZTKSYJFDS', 'mobile_banking', 1100.00, 'Ena Transport', NULL),
(18, 2, 5, '', '01738251690', 'Dhaka', 'Sylhet', 'BK79620', 'Dhaka To Sylhet', '2025-09-30', 'Upcoming', NULL, 'XYZTKSYJDG', 'mobile_banking', 1090.00, 'Ena Transport', 'ss'),
(19, 2, 6, 'A4', '01738251690', 'Dhaka', 'Khulna', 'BK27040', 'Dhaka To Khulna', '2025-09-30', 'Upcoming', NULL, 'XYZTKSYJFDS', 'mobile_banking', 1000.00, 'Desh Travels', NULL),
(20, 2, 6, 'B1', '01738251690', 'Dhaka', 'Khulna', 'BK51426', 'Dhaka To Khulna', '2025-09-30', 'Cancelled', NULL, 'XYZTKSYJFDS', 'mobile_banking', 1000.00, 'Desh Travels', NULL),
(24, 2, 29, 'A3', '01738251690', 'Dhaka', 'Rajshahi', 'BK99429', 'Dhaka To Rajshahi', '2025-10-12', 'Cancelled', NULL, 'XYZTKSYJFDS', 'mobile_banking', 600.00, 'Desh Travels', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `operator_name` varchar(100) NOT NULL,
  `bus_number` varchar(50) NOT NULL,
  `bus_type` varchar(50) NOT NULL,
  `starting_point` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `starting_time` time NOT NULL,
  `arrival_time` time NOT NULL,
  `fare` decimal(10,2) NOT NULL,
  `seats_available` int(11) NOT NULL,
  `journey_date` date NOT NULL,
  `status` enum('Upcoming','Completed','Cancelled') NOT NULL DEFAULT 'Upcoming'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `operator_name`, `bus_number`, `bus_type`, `starting_point`, `destination`, `starting_time`, `arrival_time`, `fare`, `seats_available`, `journey_date`, `status`) VALUES
(1, 'Sakura Paribahan', '102. SBD-BSL (PADMA)', 'Non AC', 'Saydabad Terminal -1', 'Barisal Terminal', '05:00:00', '07:30:00', 550.00, 28, '2025-08-29', 'Upcoming'),
(2, 'Shyamoli NR Travels', '6301-Barisal', 'Non AC', 'Rainkhola Counter', 'Barisal Terminal', '06:00:00', '11:00:00', 450.00, 25, '2025-08-29', 'Upcoming'),
(3, 'Green Line Paribahan', 'GL-201-DHK-RAJ', 'AC', 'Dhaka', 'Rajshahi', '08:00:00', '13:30:00', 1200.00, 36, '2025-09-30', 'Upcoming'),
(4, 'Hanif Enterprise', 'HF-301-DHK-COX', 'Non AC', 'Dhaka', 'Cox\'s Bazar', '22:00:00', '07:00:00', 900.00, 34, '2025-09-30', 'Upcoming'),
(5, 'Ena Transport', 'ENA-105-DHK-SYL', 'AC', 'Dhaka', 'Sylhet', '07:30:00', '14:00:00', 1100.00, 26, '2025-09-30', 'Upcoming'),
(6, 'Desh Travels', 'DT-502-DHK-KHU', 'AC', 'Dhaka', 'Khulna', '06:30:00', '13:00:00', 1000.00, 16, '2025-09-30', 'Upcoming'),
(7, 'National Travels', 'NT-601-DHK-MYM', 'Non AC', 'Dhaka', 'Mymensingh', '10:00:00', '12:30:00', 300.00, 40, '2025-09-30', 'Upcoming'),
(8, 'Sakura Paribahan', '102-BSL-DHK', 'Non AC', 'Barisal', 'Dhaka', '08:00:00', '10:30:00', 550.00, 30, '2025-10-01', 'Upcoming'),
(9, 'Shyamoli NR Travels', '6302-BAR-DHK', 'Non AC', 'Barisal', 'Dhaka', '09:00:00', '14:00:00', 450.00, 25, '2025-10-01', 'Upcoming'),
(10, 'Green Line Paribahan', 'GL-202-RAJ-DHK', 'AC', 'Rajshahi', 'Dhaka', '07:00:00', '12:30:00', 1200.00, 18, '2025-10-01', 'Upcoming'),
(11, 'Hanif Enterprise', 'HF-302-COX-DHK', 'Non AC', 'Cox\'s Bazar', 'Dhaka', '21:00:00', '06:00:00', 900.00, 32, '2025-10-01', 'Upcoming'),
(12, 'Ena Transport', 'ENA-106-SYL-DHK', 'AC', 'Sylhet', 'Dhaka', '06:00:00', '12:30:00', 1100.00, 25, '2025-10-01', 'Upcoming'),
(13, 'Saint Martin Paribahan', 'SM-401-CHT-BAN', 'Non AC', 'Chittagong', 'Bandarban', '09:00:00', '11:30:00', 400.00, 15, '2025-09-30', 'Upcoming'),
(14, 'Desh Travels', 'DT-503-KHU-BAR', 'Non AC', 'Khulna', 'Barisal', '07:00:00', '11:00:00', 600.00, 28, '2025-09-30', 'Upcoming'),
(15, 'National Travels', 'NT-602-MYM-SYL', 'Non AC', 'Mymensingh', 'Sylhet', '08:30:00', '13:30:00', 700.00, 30, '2025-09-30', 'Upcoming'),
(17, 'Desh Travels', 'BUS-16960F', 'Non AC', 'Dhaka', 'Khulna', '05:00:00', '12:00:00', 1250.00, 38, '2025-09-26', 'Upcoming'),
(19, 'Ena Transport', 'BUS-60C2B7', 'Non AC', 'Dhaka', 'Khulna', '22:00:00', '06:00:00', 1200.00, 40, '2025-09-26', 'Upcoming'),
(21, 'Ena Transport', 'BUS-1A59B6', 'Non AC', 'Dhaka', 'Sylhet', '22:00:00', '06:00:00', 1000.00, 40, '2025-09-29', 'Upcoming'),
(23, 'Ena Transport', 'BUS-D1A02B', 'Non AC', 'Dhaka', 'Sylhet', '22:00:00', '06:00:00', 1000.00, 40, '2025-09-29', 'Upcoming'),
(24, 'Ena Transport', 'BUS-F860AE', 'Non AC', 'Dhaka', 'Sylhet', '10:00:00', '17:00:00', 1250.00, 40, '2025-10-03', 'Upcoming'),
(29, 'Desh Travels', 'BUS-D9AEB1', 'Non AC', 'Dhaka', 'Rajshahi', '00:00:00', '06:00:00', 600.00, 39, '2025-10-12', 'Cancelled'),
(30, 'Desh Travels', 'BUS-2FDAD4', 'Non AC', 'Dhaka', 'Rajshahi', '00:00:00', '08:00:00', 700.00, 40, '2025-10-12', 'Cancelled'),
(31, 'Desh Travels', 'BUS-011165', 'Non AC', 'Dhaka', 'Rajshahi', '00:00:00', '02:00:00', 600.00, 40, '2025-10-12', 'Upcoming'),
(32, 'Desh Travels', 'BUS-10C62D', 'Non AC', 'Dhaka', 'Rajshahi', '00:00:00', '02:00:00', 600.00, 40, '2025-10-12', 'Upcoming'),
(33, 'Desh Travels', 'BUS-CC6954', 'Non AC', 'Dhaka', 'Rajshahi', '00:00:00', '02:00:00', 600.00, 40, '2025-10-12', 'Upcoming'),
(34, 'Desh Travels', 'BUS-4FCBB8', 'Non AC', 'Dhaka', 'Rajshahi', '00:00:00', '02:00:00', 600.00, 40, '2025-10-12', 'Upcoming');

-- --------------------------------------------------------

--
-- Table structure for table `bus_companies`
--

CREATE TABLE `bus_companies` (
  `id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_companies`
--

INSERT INTO `bus_companies` (`id`, `company_name`, `phone`, `password`, `remember_token`) VALUES
(1, 'Sakura Paribahan', '98765432101', '12345678', NULL),
(2, 'Desh Travels', '01738251690', '$2y$10$deA8zWmrLRaqnKyCtF7A9OONwtZXIfDiygszP95Bk0ns6VduURtim', NULL),
(3, 'xyz', '12345678901', '$2y$10$75rIXyu/t.23LJ28.tO3Rui46YFPyUrpIs5G6OfJ/mfeJ7EtsnJRq', NULL),
(4, 'Ena Transport', '01738251691', '$2y$10$..U.zgIBOkkPWsdsxEG1p./8QeTWedPHuIUCwlisy6yV5WxGCCx66', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` varchar(50) NOT NULL,
  `complaint_type` enum('Service','Driver','Vehicle','Other') NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Resolved','Dismissed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `booking_id`, `complaint_type`, `description`, `created_at`, `status`) VALUES
(1, 2, 'BK52394', 'Service', 'hgjgjhjh', '2025-09-24 15:41:30', 'Resolved'),
(2, 2, 'BK54323', 'Service', 'baje services', '2025-09-24 16:01:34', 'Pending'),
(4, 2, 'BK81099', 'Driver', 'zvczcx', '2025-09-24 16:48:13', 'Resolved'),
(5, 2, 'BK71117', 'Vehicle', 'Baje bus', '2025-09-26 05:30:24', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `contact_submissions`
--

CREATE TABLE `contact_submissions` (
  `id` int(11) NOT NULL,
  `purpose` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(14) NOT NULL,
  `email` varchar(255) NOT NULL,
  `city` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_submissions`
--

INSERT INTO `contact_submissions` (`id`, `purpose`, `name`, `phone`, `email`, `city`, `message`, `created_at`) VALUES
(1, 'bus', 'Nazrul Islam', '+8801738251690', 'nazrul.islam.01865@gmail.com', 'chittagong', 'sdgsdfsdfsdfsd', '2025-09-18 15:43:50'),
(2, 'ticket', 'hjkjhjhkhj', '+8801738251690', 'nazrul.islam.01865@gmail.com', 'chittagong', 'hjgfghfghjh', '2025-09-18 15:50:26'),
(3, 'ticket', 'hjkjhjhkhj', '+8801738251690', 'nazrul.islam.01865@gmail.com', 'chittagong', 'hjgfghfghjh', '2025-09-18 15:50:26'),
(4, 'ticket', 'hjkjhjhkhj', '+8801738251690', 'nazrul.islam.01865@gmail.com', 'chittagong', 'hjgfghfghjh', '2025-09-18 15:51:28');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `phone` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `company_id`, `name`, `license_number`, `phone`) VALUES
(1, 2, 'Naz', '123456789', '12345678901'),
(2, 2, 'Nazrul', '1234567890', '01234567891');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` varchar(10) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `booking_id`, `rating`, `comments`, `created_at`) VALUES
(1, 2, 'BK12347', 2, 'hgvgh', '2025-09-13 11:45:16'),
(2, 2, 'BK12345', 2, 'hghgfghfg', '2025-09-18 16:58:16'),
(3, 2, 'BK12346', 1, 'jgj', '2025-09-19 18:07:24'),
(4, 2, 'BK92180', 5, '5* dilam for check', '2025-09-26 05:30:03'),
(5, 2, 'BK12345', 1, 'hh', '2025-09-29 15:50:20');

-- --------------------------------------------------------

--
-- Table structure for table `feedbackadmin`
--

CREATE TABLE `feedbackadmin` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `feedback_text` text NOT NULL,
  `feedback_type` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedbackadmin`
--

INSERT INTO `feedbackadmin` (`id`, `user_name`, `feedback_text`, `feedback_type`, `created_at`) VALUES
(1, 'John Doe', 'Delayed bus on Dhaka-Rajshahi route.', 'Complaint', '2025-09-10 04:00:00'),
(2, 'Jane Smith', 'Excellent service!', 'Feedback', '2025-09-12 08:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `otp_records`
--

CREATE TABLE `otp_records` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `otp_expiry` datetime NOT NULL,
  `is_used` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `promo_code` varchar(20) NOT NULL,
  `discount_type` enum('Percentage','Fixed Amount') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `route` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `promo_code`, `discount_type`, `discount_value`, `route`) VALUES
(1, 'SUMMER10', 'Percentage', 10.00, 'Dhaka routes'),
(2, 'FALL20', 'Fixed Amount', 20.00, 'Chittagong routes'),
(3, 'TC', 'Percentage', 10.00, 'Dhaka-Sylhet'),
(4, 'ss', 'Fixed Amount', 10.00, 'Dhaka-Sylhet');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `item_type` enum('Feedback','Complaint') NOT NULL DEFAULT 'Feedback'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `item_id`, `response_text`, `created_at`, `item_type`) VALUES
(1, 2, 'kjhsdf', '2025-09-19 17:19:47', 'Feedback'),
(2, 2, 'kjhsdf', '2025-09-19 17:19:47', 'Feedback'),
(3, 2, 'kjhsdf', '2025-09-19 17:19:47', 'Feedback'),
(4, 1, 'hi ahs', '2025-09-24 15:57:23', 'Complaint'),
(5, 4, 'dfg', '2025-09-26 05:24:58', 'Complaint');

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `route_name` varchar(100) NOT NULL,
  `revenue` decimal(10,2) NOT NULL,
  `tickets_sold` int(11) NOT NULL,
  `transaction_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `route_name`, `revenue`, `tickets_sold`, `transaction_date`) VALUES
(1, 'Dhaka - Rajshahi', 15000.00, 300, '2025-09-10'),
(2, 'Dhaka - Barisal', 22000.00, 440, '2025-09-12'),
(3, 'Dhaka - Cox\'s Bazar', 10000.00, 200, '2025-09-15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `nid` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verify_token` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `nid`, `password`, `verify_token`, `remember_token`) VALUES
(1, 'nazrul3', 'nazrul01865@gmail.com', '1738251690', '9174589284', '$2y$10$8FbRTYee/0/3mrKkJqvs7emWY4zN5Z92KF8Pr2Wh0/PEz0PkktX5i', NULL, NULL),
(2, 'nazrul', 'nazrul.islam.01865@gmail.com', '01738251690', '9174589283', '$2y$10$jkC2CGJlrvCssLXUTwwWEulAsFgJ61A3YX7CxeFXrRZ2m3Jy84sNK', '', NULL),
(3, 'Nayeem', 'saruarmunna17@gmail.com', '01956351202', '9174589286', '$2y$10$0EbBq.kdmiW2FbFArdowfO3DmHjQLSgmO.S12yEm1QQdDQqaaI1jW', '54e743459e3af84ca3f48b4d44d50f01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD UNIQUE KEY `booking_id_2` (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_bus_id` (`bus_id`),
  ADD KEY `idx_seat_number` (`seat_number`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_companies`
--
ALTER TABLE `bus_companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `feedbackadmin`
--
ALTER TABLE `feedbackadmin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_records`
--
ALTER TABLE `otp_records`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promo_code` (`promo_code`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_item_id` (`item_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD UNIQUE KEY `nid` (`nid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `bus_companies`
--
ALTER TABLE `bus_companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_submissions`
--
ALTER TABLE `contact_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedbackadmin`
--
ALTER TABLE `feedbackadmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otp_records`
--
ALTER TABLE `otp_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `bus_companies` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
