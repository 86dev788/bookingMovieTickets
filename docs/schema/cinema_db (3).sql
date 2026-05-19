-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 08:39 AM
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
-- Database: `cinema_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `total_amount` decimal(8,2) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `booking_date`, `status`, `total_amount`, `customer_id`, `show_id`) VALUES
(1, '2026-05-14 21:00:02', 'Confirmed', 3000.00, 1, 1),
(2, '2026-05-16 14:41:20', 'Confirmed', 2400.00, 3, 2),
(3, '2026-05-16 15:32:24', 'Cancelled', 1500.00, 3, 3),
(4, '2026-05-16 15:34:16', 'Pending', 1500.00, 3, 3),
(5, '2026-05-16 15:35:18', 'Pending', 1500.00, 3, 3),
(6, '2026-05-16 15:38:17', 'Cancelled', 1500.00, 3, 3),
(9, '2026-05-16 15:44:10', 'Confirmed', 1500.00, 3, 3),
(10, '2026-05-16 15:48:37', 'Refunded', 1500.00, 4, 3),
(11, '2026-05-16 20:51:25', 'Refunded', 1500.00, 3, 3),
(12, '2026-05-16 21:07:53', 'Cancelled', 1500.00, 3, 3),
(13, '2026-05-16 21:17:34', 'Cancelled', 2899.00, 5, 4),
(14, '2026-05-16 21:37:21', 'Confirmed', 2899.00, 5, 4),
(15, '2026-05-16 22:24:07', 'Confirmed', 3000.00, 5, 3),
(16, '2026-05-17 20:36:48', 'Confirmed', 18000.00, 5, 8),
(17, '2026-05-19 00:00:46', 'Pending', 9000.00, 5, 8),
(18, '2026-05-19 00:02:16', 'Confirmed', 4500.00, 5, 8),
(19, '2026-05-19 01:13:41', 'Confirmed', 4500.00, 3, 8),
(21, '2026-05-19 11:20:05', 'Confirmed', 4500.00, 3, 8);

-- --------------------------------------------------------

--
-- Table structure for table `bookingtable`
--

CREATE TABLE `bookingtable` (
  `bookingID` int(11) NOT NULL,
  `movieID` int(11) DEFAULT NULL,
  `bookingTheatre` varchar(100) NOT NULL,
  `bookingType` varchar(100) DEFAULT NULL,
  `bookingDate` varchar(50) NOT NULL,
  `bookingTime` varchar(50) NOT NULL,
  `bookingFName` varchar(100) NOT NULL,
  `bookingLName` varchar(100) DEFAULT NULL,
  `bookingPNumber` varchar(12) NOT NULL,
  `bookingEmail` varchar(255) NOT NULL,
  `amount` varchar(255) NOT NULL,
  `ORDERID` varchar(255) NOT NULL,
  `DATE-TIME` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `bookingtable`
--

INSERT INTO `bookingtable` (`bookingID`, `movieID`, `bookingTheatre`, `bookingType`, `bookingDate`, `bookingTime`, `bookingFName`, `bookingLName`, `bookingPNumber`, `bookingEmail`, `amount`, `ORDERID`, `DATE-TIME`) VALUES
(71, 6, 'private-hall', 'imax', '14-3', '15-00', 'xyz', 'abc', '000000000', '000@gmail.com', '5000.00', 'cash', '2020-12-14 12:20:31'),
(0, 12345, 'main-hall', 'imax', '13-3', '09-00', 'test', 'test', '1234567890', 'asd@gmail.com', '200', 'cash', '2026-05-14 21:14:44'),
(71, 6, 'private-hall', 'imax', '14-3', '15-00', 'xyz', 'abc', '000000000', '000@gmail.com', '5000.00', 'cash', '2020-12-14 12:20:31'),
(0, 12345, 'main-hall', 'imax', '13-3', '09-00', 'test', 'test', '1234567890', 'asd@gmail.com', '200', 'cash', '2026-05-14 21:14:44');

-- --------------------------------------------------------

--
-- Table structure for table `cinema`
--

CREATE TABLE `cinema` (
  `cinema_id` int(11) NOT NULL,
  `cinema_name` varchar(150) NOT NULL,
  `city` varchar(100) NOT NULL,
  `address` varchar(300) NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cinema`
--

INSERT INTO `cinema` (`cinema_id`, `cinema_name`, `city`, `address`, `phone`) VALUES
(1, 'Cineplex Lahore', 'Lahore', '123 Mall Road, Lahore', '042-1234567'),
(2, 'Cineplex Islamabad', 'Islamabad', '456 F-10, Islamabad', '051-7654321'),
(3, 'LHR CineMax', 'Lahore', 'testing', '01311234567');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `email`, `phone`, `password_hash`, `created_at`) VALUES
(1, 'John Doe 2', 'john@example.com', '1234567890', '$2y$10$hO69iqeleADiK5sJ1/frWuBnMCrvCMJx9Dh.enEc8KcsAHthz7oqK', '2026-05-14 21:00:02'),
(2, 'Jane Smith', 'jane@example.com', '0987654321', '$2y$10$awSMpulO94wGiGHHK/ywNeQo0dsELqoMHUCg6M0KmzVFLaLGuwK6O', '2026-05-14 21:00:02'),
(3, 'Customer User', 'customer@gmail.com', '03001001001', '$2y$10$aFDB9B0CjROVm70TqbBG0OqJZ5zueD3HzGwQy.dYBr4UojoCO6AKG', '2026-05-14 21:00:02'),
(4, 'Faiqa Malik', 'faiqamalik@gmail.com', '01321234567', '$2y$10$B.OAuVfKuM3BEKpOKCAGiOrtIrNTIiIP3n3mPsptluixLiqHsXcnG', '2026-05-16 15:48:12'),
(5, 'Farhan Malik', 'farhanmalick1o1@gmail.com', '03044487392', '$2y$10$96/tx2ZTZ6q7PMM6kGDka.KUw8Hw4oxw7Q6YwFzpaen7WkvWNBfgC', '2026-05-16 21:13:26'),
(6, 'test', 'test@gmail.com', '123456789', '$2y$10$B.OAuVfKuM3BEKpOKCAGiOrtIrNTIiIP3n3mPsptluixLiqHsXcnG', '2026-05-19 00:17:04');

-- --------------------------------------------------------

--
-- Table structure for table `feedbacktable`
--

CREATE TABLE `feedbacktable` (
  `msgID` int(12) NOT NULL,
  `senderfName` varchar(100) DEFAULT NULL,
  `senderlName` varchar(100) DEFAULT NULL,
  `sendereMail` varchar(150) DEFAULT NULL,
  `senderfeedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedbacktable`
--

INSERT INTO `feedbacktable` (`msgID`, `senderfName`, `senderlName`, `sendereMail`, `senderfeedback`) VALUES
(1, 'test', 'test', 'test@gank,cin', 'test'),
(2, 'test', 'tst', 'test@gnail,com', 'admiandnin');

-- --------------------------------------------------------

--
-- Table structure for table `movie`
--

CREATE TABLE `movie` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `duration_min` int(11) NOT NULL,
  `language` varchar(50) NOT NULL,
  `rating` varchar(10) NOT NULL,
  `genres` varchar(300) NOT NULL,
  `poster_url` varchar(500) DEFAULT NULL,
  `release_date` date NOT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie`
--

INSERT INTO `movie` (`movie_id`, `title`, `duration_min`, `language`, `rating`, `genres`, `poster_url`, `release_date`, `description`) VALUES
(1, 'Captain Marvel', 220, 'English', 'PG-13', 'Action, Adventure, Sci-Fi', 'img/movie-poster-1.jpg', '2019-03-08', 'Carol Danvers becomes one of the universe\'s most powerful heroes.'),
(2, 'The Lego Movie', 110, 'English', 'U', 'Animation, Action, Adventure', 'img/movie-poster-3.jpg', '2014-02-07', 'An ordinary Lego construction worker discovers a hidden talent.'),
(3, 'Spiderman', 2, 'eng', 'PG-13', 'Action', 'img/movie-poster-1.jpg', '2026-05-14', 'Testing and testing again'),
(4, 'The Vanishing', 2, 'English', 'U', 'Bekaar', 'img/movie-poster-6.jpg', '2026-05-21', 'I have alot more than you hink'),
(5, 'Valhallah', 120, 'Eng', 'PF-20', 'Thriller', 'img/movie-poster-6.jpg', '2026-05-16', 'Best movie on netflix'),
(6, 'Testing', 120, 'English', 'PG-08', 'Drama', 'img/posters/1778952201_6a08a809b9ef8.jpeg', '2026-05-16', 'Drama is the best'),
(7, 'Testing Movie', 120, 'English', 'PG_10', 'Action', 'img/posters/1779032076_6a09e00cd26b4.jpeg', '2026-05-17', 'Best Movie ever');

-- --------------------------------------------------------

--
-- Table structure for table `movietable`
--

CREATE TABLE `movietable` (
  `movieID` int(11) NOT NULL,
  `movieTitle` varchar(255) DEFAULT NULL,
  `movieGenre` varchar(255) DEFAULT NULL,
  `DurationMin` int(11) DEFAULT NULL,
  `Language` varchar(50) DEFAULT NULL,
  `Rating` varchar(20) DEFAULT NULL,
  `movieRelDate` date DEFAULT NULL,
  `movieDirector` varchar(255) DEFAULT NULL,
  `PosterURL` varchar(500) DEFAULT NULL,
  `movieDescription` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `movietable`
--

INSERT INTO `movietable` (`movieID`, `movieTitle`, `movieGenre`, `DurationMin`, `Language`, `Rating`, `movieRelDate`, `movieDirector`, `PosterURL`, `movieDescription`) VALUES
(1, 'Avengers: Endgame', 'Action, Sci-Fi', 181, 'English', 'PG-13', '2019-04-26', 'Anthony Russo, Joe Russo', 'movie-poster-1.jpg', 'Superheroes unite to defeat Thanos.'),
(2, 'Spider-Man: No Way Home', 'Action, Adventure', 148, 'English', 'PG-13', '2021-12-17', 'Jon Watts', 'images/movie-poster-2.jpg', 'Peter Parker faces multiverse villains.'),
(3, 'Interstellar', 'Sci-Fi, Drama', 169, 'English', 'PG-13', '2014-11-07', 'Christopher Nolan', 'images/interstellar.jpg', 'A journey beyond the galaxy to save humanity.'),
(4, 'The Dark Knight', 'Action, Crime', 152, 'English', 'PG-13', '2008-07-18', 'Christopher Nolan', 'images/darkknight.jpg', 'Batman faces the Joker in Gotham City.'),
(5, 'Inception', 'Sci-Fi, Thriller', 148, 'English', 'PG-13', '2010-07-16', 'Christopher Nolan', 'images/inception.jpg', 'A thief who steals information through dreams.');

-- --------------------------------------------------------

--
-- Table structure for table `movie_show`
--

CREATE TABLE `movie_show` (
  `show_id` int(11) NOT NULL,
  `show_time` datetime NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `screen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_show`
--

INSERT INTO `movie_show` (`show_id`, `show_time`, `price`, `movie_id`, `screen_id`) VALUES
(1, '2025-05-15 14:00:00', 1500.00, 1, 1),
(2, '2025-05-15 18:00:00', 1200.00, 2, 1),
(3, '2026-05-17 14:39:00', 1500.00, 3, 1),
(4, '2026-05-16 15:58:00', 2899.00, 5, 5),
(5, '2026-05-16 22:43:00', 2999.99, 3, 2),
(6, '2026-05-18 20:02:00', 2999.00, 1, 3),
(7, '2026-05-19 20:05:00', 3499.99, 4, 1),
(8, '2026-05-17 20:36:00', 4500.00, 7, 3),
(9, '2026-05-22 03:14:00', 4500.00, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `amount` decimal(8,2) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `paid_at` datetime DEFAULT NULL,
  `booking_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `amount`, `method`, `status`, `paid_at`, `booking_id`) VALUES
(1, 3000.00, 'Card', 'Success', '2025-05-14 10:00:00', 1),
(2, 2400.00, 'Cash', 'Success', '2026-05-16 14:41:26', 2),
(3, 1500.00, 'EasyPaisa', 'Refunded', '2026-05-16 15:33:09', 3),
(4, 1500.00, 'Card', 'Failed', NULL, 4),
(5, 1500.00, 'JazzCash', 'Failed', NULL, 5),
(6, 1500.00, 'Cash', 'Failed', '2026-05-16 15:38:18', 6),
(9, 1500.00, 'Cash', 'Success', '2026-05-16 15:44:13', 9),
(10, 1500.00, 'Cash', 'Refunded', '2026-05-16 15:48:39', 10),
(11, 1500.00, 'EasyPaisa', 'Refunded', '2026-05-16 20:51:37', 11),
(12, 1500.00, 'Cash', 'Failed', NULL, 12),
(13, 2899.00, 'Cash', 'Failed', '2026-05-16 21:17:37', 13),
(14, 2899.00, 'Card', 'Success', '2026-05-16 21:37:32', 14),
(15, 3000.00, 'EasyPaisa', 'Success', '2026-05-16 22:24:14', 15),
(16, 18000.00, 'EasyPaisa', 'Success', '2026-05-17 20:36:56', 16),
(17, 9000.00, 'Cash', 'Pending', NULL, 17),
(18, 4500.00, 'EasyPaisa', 'Success', '2026-05-19 00:02:36', 18),
(19, 4500.00, 'EasyPaisa', 'Success', '2026-05-19 01:13:59', 19),
(21, 4500.00, 'Cash', 'Success', '2026-05-19 11:20:08', 21);

-- --------------------------------------------------------

--
-- Table structure for table `screen`
--

CREATE TABLE `screen` (
  `screen_id` int(11) NOT NULL,
  `screen_name` varchar(100) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `cinema_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `screen`
--

INSERT INTO `screen` (`screen_id`, `screen_name`, `total_seats`, `cinema_id`) VALUES
(1, 'Screen 1', 100, 1),
(2, 'Screen 2', 80, 1),
(3, 'Screen 1', 120, 2),
(4, 'World Screen', -1, 3),
(5, 'World Screen 2', 25, 3),
(6, '2D Screen', 240, 2);

-- --------------------------------------------------------

--
-- Table structure for table `seat`
--

CREATE TABLE `seat` (
  `seat_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `seat_type` varchar(30) NOT NULL DEFAULT 'Standard',
  `screen_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seat`
--

INSERT INTO `seat` (`seat_id`, `seat_number`, `seat_type`, `screen_id`) VALUES
(1, 'A1', 'Standard', 1),
(2, 'A2', 'Standard', 1),
(3, 'A3', 'VIP', 1),
(4, 'A4', 'VIP', 1),
(5, 'B1', 'Standard', 1),
(6, 'B2', 'Standard', 1),
(7, 'B3', 'VIP', 1),
(8, 'B4', 'VIP', 1),
(10, 'b4', 'VIP', 4),
(11, 'b3', 'VIP', 4),
(12, 'B4', 'Standard', 5),
(13, 'B3', 'Standard', 5),
(15, 'A1', 'Standard', 5),
(16, 'A5', 'Standard', 5),
(17, 'A2', 'VIP', 5),
(18, 'A3', 'VIP', 5),
(19, 'A4', 'VIP', 5),
(20, 'B1', 'VIP', 5),
(21, 'B2', 'VIP', 5),
(22, 'A1', 'VIP', 3),
(23, 'A2', 'VIP', 3),
(24, 'A3', 'VIP', 3),
(25, 'A4', 'VIP', 3),
(26, 'A5', 'VIP', 3),
(27, 'A6', 'VIP', 3),
(28, 'A7', 'VIP', 3),
(29, 'A8', 'VIP', 3),
(30, 'A9', 'VIP', 3),
(31, 'A10', 'VIP', 3),
(32, 'A11', 'Standard', 3),
(33, 'A12', 'Standard', 3),
(34, 'A13', 'Standard', 3),
(35, 'A14', 'Standard', 3),
(36, 'A15', 'Standard', 3),
(37, 'A16', 'Standard', 3),
(38, 'A17', 'Standard', 3),
(39, 'A18', 'Standard', 3),
(40, 'A19', 'Standard', 3),
(41, 'A20', 'Standard', 3),
(42, 'B1', 'Recliner', 3),
(43, 'B2', 'Recliner', 3),
(44, 'B3', 'Recliner', 3),
(45, 'B4', 'Recliner', 3),
(46, 'B5', 'Recliner', 3),
(47, 'B6', 'Recliner', 3),
(48, 'B7', 'Recliner', 3),
(49, 'B8', 'Recliner', 3),
(50, 'B9', 'Recliner', 3),
(51, 'B10', 'Recliner', 3),
(52, 'B11', 'Recliner', 3),
(53, 'B12', 'Recliner', 3),
(54, 'B13', 'Recliner', 3),
(55, 'B14', 'Recliner', 3),
(56, 'B15', 'Recliner', 3),
(57, 'B16', 'Recliner', 3),
(58, 'B17', 'Recliner', 3),
(59, 'B18', 'Recliner', 3),
(60, 'B19', 'Recliner', 3),
(61, 'B20', 'Recliner', 3),
(62, 'B21', 'Recliner', 3),
(63, 'B22', 'Recliner', 3),
(64, 'B23', 'Recliner', 3),
(65, 'B24', 'Recliner', 3),
(66, 'B25', 'Recliner', 3),
(67, 'A1', 'VIP', 6),
(68, 'A2', 'VIP', 6),
(69, 'A3', 'VIP', 6),
(70, 'A4', 'VIP', 6),
(71, 'A5', 'VIP', 6),
(72, 'A6', 'VIP', 6),
(73, 'A7', 'VIP', 6),
(74, 'A8', 'VIP', 6),
(75, 'A9', 'VIP', 6),
(76, 'A10', 'VIP', 6),
(77, 'A11', 'VIP', 6),
(78, 'A12', 'VIP', 6),
(79, 'A13', 'VIP', 6);

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `ticket_id` int(11) NOT NULL,
  `qr_code` varchar(255) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `booking_id` int(11) NOT NULL,
  `seat_id` int(11) NOT NULL,
  `is_confirmed` tinyint(1) DEFAULT 0,
  `confirmed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`ticket_id`, `qr_code`, `is_used`, `generated_at`, `booking_id`, `seat_id`, `is_confirmed`, `confirmed_at`) VALUES
(1, 'QR123456789', 0, '2026-05-14 21:00:02', 1, 1, 0, NULL),
(2, 'QR987654321', 0, '2026-05-14 21:00:02', 1, 2, 0, NULL),
(3, 'QR6a083bc05deef8.69413895', 0, '2026-05-16 14:41:20', 2, 3, 0, NULL),
(4, 'QR6a083bc05e9925.85922761', 0, '2026-05-16 14:41:20', 2, 4, 0, NULL),
(5, 'QR6a0847b8c6aa32.72576377', 0, '2026-05-16 15:32:24', 3, 2, 0, NULL),
(6, 'QR6a084828713d74.32164219', 0, '2026-05-16 15:34:16', 4, 6, 0, NULL),
(7, 'QR6a0848660fccc8.86377903', 0, '2026-05-16 15:35:18', 5, 7, 0, NULL),
(8, 'QR6a0849193b9940.49009677', 0, '2026-05-16 15:38:17', 6, 8, 0, NULL),
(11, 'RESV6a084a7b06fc0', 1, '2026-05-16 15:44:11', 9, 8, 0, NULL),
(12, 'RESV6a084b856e02f', 0, '2026-05-16 15:48:37', 10, 5, 0, NULL),
(13, 'TK11_3_6a08928953a79', 0, '2026-05-16 20:51:37', 11, 3, 0, '2026-05-16 20:51:37'),
(14, 'RESV6a0896597eb66', 0, '2026-05-16 21:07:53', 12, 2, 0, NULL),
(15, 'TK13_17_6a0898a166ec5', 0, '2026-05-16 21:17:37', 13, 17, 0, '2026-05-16 21:17:37'),
(16, 'TK14_17_6a089d4c813ea', 0, '2026-05-16 21:37:32', 14, 17, 1, '2026-05-16 21:37:32'),
(17, 'TK15_1_6a08a83e9a099', 1, '2026-05-16 22:24:14', 15, 1, 1, '2026-05-16 22:24:14'),
(18, 'TK15_2_6a08a83e9aac1', 0, '2026-05-16 22:24:14', 15, 2, 1, '2026-05-16 22:24:14'),
(19, 'TK16_24_6a09e0983b506', 0, '2026-05-17 20:36:56', 16, 24, 1, '2026-05-17 20:36:56'),
(20, 'TK16_25_6a09e0983f066', 0, '2026-05-17 20:36:56', 16, 25, 1, '2026-05-17 20:36:56'),
(21, 'TK16_44_6a09e0984395e', 0, '2026-05-17 20:36:56', 16, 44, 1, '2026-05-17 20:36:56'),
(22, 'TK16_45_6a09e09847a34', 1, '2026-05-17 20:36:56', 16, 45, 1, '2026-05-17 20:36:56'),
(23, 'RESV6a0b61de1b41d', 0, '2026-05-19 00:00:46', 17, 22, 0, NULL),
(24, 'RESV6a0b61de1b5d8', 0, '2026-05-19 00:00:46', 17, 31, 0, NULL),
(25, 'TK18_42_6a0b624cd42cb', 1, '2026-05-19 00:02:36', 18, 42, 1, '2026-05-19 00:02:36'),
(26, 'TK19_27_6a0b730726a2c', 0, '2026-05-19 01:13:59', 19, 27, 1, '2026-05-19 01:13:59'),
(27, 'TK21_35_6a0c01180bd0b', 1, '2026-05-19 11:20:08', 21, 35, 1, '2026-05-19 11:20:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `password`) VALUES
(1, 'admin@booking.com', 'Admin User', 'admin@booking.com', 'admin123'),
(2, 'customer@gmail.com', 'Customer User', 'customer@gmail.com', 'customer123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `show_id` (`show_id`);

--
-- Indexes for table `cinema`
--
ALTER TABLE `cinema`
  ADD PRIMARY KEY (`cinema_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `feedbacktable`
--
ALTER TABLE `feedbacktable`
  ADD PRIMARY KEY (`msgID`);

--
-- Indexes for table `movie`
--
ALTER TABLE `movie`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `movietable`
--
ALTER TABLE `movietable`
  ADD PRIMARY KEY (`movieID`);

--
-- Indexes for table `movie_show`
--
ALTER TABLE `movie_show`
  ADD PRIMARY KEY (`show_id`),
  ADD UNIQUE KEY `screen_id` (`screen_id`,`show_time`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`);

--
-- Indexes for table `screen`
--
ALTER TABLE `screen`
  ADD PRIMARY KEY (`screen_id`),
  ADD KEY `cinema_id` (`cinema_id`);

--
-- Indexes for table `seat`
--
ALTER TABLE `seat`
  ADD PRIMARY KEY (`seat_id`),
  ADD UNIQUE KEY `screen_id` (`screen_id`,`seat_number`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`),
  ADD UNIQUE KEY `booking_id` (`booking_id`,`seat_id`),
  ADD KEY `seat_id` (`seat_id`);

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
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `cinema`
--
ALTER TABLE `cinema`
  MODIFY `cinema_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `feedbacktable`
--
ALTER TABLE `feedbacktable`
  MODIFY `msgID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `movie`
--
ALTER TABLE `movie`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `movietable`
--
ALTER TABLE `movietable`
  MODIFY `movieID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `movie_show`
--
ALTER TABLE `movie_show`
  MODIFY `show_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `screen`
--
ALTER TABLE `screen`
  MODIFY `screen_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `seat`
--
ALTER TABLE `seat`
  MODIFY `seat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `ticket_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `booking_ibfk_2` FOREIGN KEY (`show_id`) REFERENCES `movie_show` (`show_id`) ON DELETE NO ACTION;

--
-- Constraints for table `movie_show`
--
ALTER TABLE `movie_show`
  ADD CONSTRAINT `movie_show_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movie` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_show_ibfk_2` FOREIGN KEY (`screen_id`) REFERENCES `screen` (`screen_id`) ON DELETE NO ACTION;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE;

--
-- Constraints for table `screen`
--
ALTER TABLE `screen`
  ADD CONSTRAINT `screen_ibfk_1` FOREIGN KEY (`cinema_id`) REFERENCES `cinema` (`cinema_id`) ON DELETE CASCADE;

--
-- Constraints for table `seat`
--
ALTER TABLE `seat`
  ADD CONSTRAINT `seat_ibfk_1` FOREIGN KEY (`screen_id`) REFERENCES `screen` (`screen_id`) ON DELETE CASCADE;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `ticket_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`booking_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_ibfk_2` FOREIGN KEY (`seat_id`) REFERENCES `seat` (`seat_id`) ON DELETE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
