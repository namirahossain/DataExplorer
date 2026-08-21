-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2026 at 06:47 PM
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
-- Database: `country`
--

-- --------------------------------------------------------

--
-- Table structure for table `country_info`
--

CREATE TABLE `country_info` (
  `year` int(4) NOT NULL,
  `country_code` varchar(20) NOT NULL,
  `country_name` varchar(20) NOT NULL,
  `capital` varchar(20) NOT NULL,
  `continent` varchar(20) NOT NULL,
  `region` varchar(20) NOT NULL,
  `population` int(20) NOT NULL,
  `gdp` decimal(20,2) NOT NULL,
  `life_expectancy` decimal(5,2) NOT NULL,
  `literacy_rate` decimal(5,2) NOT NULL,
  `co2_emission` decimal(15,2) NOT NULL,
  `country_comparison_id` int(20) NOT NULL,
  `country_ranking_id` int(20) NOT NULL,
  `similar_country_finder_id` int(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country_info`
--

INSERT INTO `country_info` (`year`, `country_code`, `country_name`, `capital`, `continent`, `region`, `population`, `gdp`, `life_expectancy`, `literacy_rate`, `co2_emission`, `country_comparison_id`, `country_ranking_id`, `similar_country_finder_id`) VALUES
(2025, 'BGD', 'Bangladesh', 'Dhaka', 'Asia', 'South Asia', 175686899, 459601600000.00, 75.20, 77.90, 124190733.00, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `user_id` varchar(100) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_country` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`user_id`, `user_name`, `password`, `user_country`) VALUES
('namira', '', 'namira123', ''),
('', 'namira', 'namira1234', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
