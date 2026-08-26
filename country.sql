-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2026 at 08:23 AM
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
  `co2_emission` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country_info`
--

INSERT INTO `country_info` (`year`, `country_code`, `country_name`, `capital`, `continent`, `region`, `population`, `gdp`, `life_expectancy`, `literacy_rate`, `co2_emission`) VALUES
(2025, 'BGD', 'Bangladesh', 'Dhaka', 'Asia', 'South Asia', 175686899, 459601600000.00, 75.20, 77.90, 124190733.00),
(2025, 'EST', 'Estonia', 'Tallinn', 'Europe', 'Northern Europe', 1366475, 0.00, 79.00, 100.00, 12300000.00),
(2025, 'FIN', 'Finland', 'Helsinki', 'Europe', 'Northern Europe', 5623000, 317.04, 82.00, 100.00, 5.54),
(2025, 'ISL', 'Iceland', 'Reykjavik', 'Europe', 'Northern Europe', 398000, 4956.00, 83.00, 100.00, 3.10),
(2025, 'IRL', 'Ireland', 'Dublin', 'Europe', 'Northern Europe', 5308039, 551400000000.00, 83.00, 99.00, 0.00),
(2025, 'LVA', 'Latvia', 'Riga', 'Europe', 'Northern Europe', 1847785, 48620000000.00, 76.00, 100.00, 6.43),
(2025, 'LTU', 'Lithuania', 'Vilnius', 'Europe', 'Northern Europe', 2888774, 95210000000.00, 77.00, 100.00, 12.88),
(2025, 'NOR', 'Norway', 'Oslo', 'Europe', 'Northern Europe', 5623000, 0.00, 83.00, 100.00, 38.50),
(2025, 'SWE', 'Sweden', 'Stockholm', 'Europe', 'Northern Europe', 10657000, 0.00, 83.00, 100.00, 51.20),
(2025, 'GBR', 'United Kingdom', 'London', 'Europe', 'Northern Europe', 69551000, 0.00, 81.00, 99.00, 367.00),
(2025, 'BGR', 'Bulgaria', 'Sofia', 'Europe', 'Eastern Europe', 6433302, 130780000000.00, 76.00, 98.40, 5.30),
(2025, 'CZE', 'Czechia', 'Prague', 'Europe', 'Eastern Europe', 10909500, 374620000000.00, 80.00, 99.00, 7.10),
(2025, 'HUN', 'Hungary', 'Budapest', 'Europe', 'Eastern Europe', 9514251, 246490000000.00, 77.00, 99.10, 4.50),
(2025, 'MDA', 'Moldova', 'Chisinau', 'Europe', 'Eastern Europe', 2995000, 19100000000.00, 72.00, 99.00, 1.90),
(2025, 'POL', 'Poland', 'Warsaw', 'Europe', 'Eastern Europe', 36435861, 1035000000000.00, 78.00, 99.80, 7.60),
(2025, 'ROU', 'Romania', 'Bucharest', 'Europe', 'Eastern Europe', 19020271, 428680000000.00, 76.00, 98.90, 3.70),
(2025, 'RUS', 'Russia', 'Moscow', 'Europe', 'Eastern Europe', 143000000, 2540000000000.00, 73.00, 99.70, 11.80),
(2025, 'SVK', 'Slovakia', 'Bratislava', 'Europe', 'Eastern Europe', 5416000, 150000000000.00, 78.00, 99.60, 5.50),
(2025, 'UKR', 'Ukraine', 'Kyiv', 'Europe', 'Eastern Europe', 36860000, 214200000000.00, 75.00, 99.80, 3.10),
(2025, 'CHN', 'China', 'Beijing', 'Asia', 'Eastern Asia', 1408000000, 19600000000000.00, 79.00, 97.00, 8.00),
(2025, 'JPN', 'Japan', 'Tokyo', 'Asia', 'Eastern Asia', 123100000, 4420000000000.00, 85.00, 99.00, 8.50),
(2025, 'MNG', 'Mongolia', 'Ulaanbaatar', 'Asia', 'Eastern Asia', 3500000, 23000000000.00, 72.00, 98.00, 9.00),
(2025, 'PRK', 'North Korea', 'Pyongyang', 'Asia', 'Eastern Asia', 26400000, 30000000000.00, 74.00, 100.00, 1.50),
(2025, 'KOR', 'South Korea', 'Seoul', 'Asia', 'Eastern Asia', 51600000, 1880000000000.00, 84.00, 98.00, 12.00),
(2025, 'AFG', 'Afghanistan', 'Kabul', 'Asia', 'Southern Asia', 43800000, 18000000000.00, 65.00, 37.00, 0.30),
(2025, 'BTN', 'Bhutan', 'Thimphu', 'Asia', 'Southern Asia', 790000, 3500000000.00, 72.00, 72.00, 1.60),
(2025, 'IND', 'India', 'New Delhi', 'Asia', 'Southern Asia', 1463000000, 4270000000000.00, 72.00, 78.00, 2.10),
(2025, 'MDV', 'Maldives', 'Malé', 'Asia', 'Southern Asia', 530000, 7700000000.00, 81.00, 98.00, 3.80),
(2025, 'NPL', 'Nepal', 'Kathmandu', 'Asia', 'Southern Asia', 29700000, 46000000000.00, 71.00, 68.00, 0.60),
(2025, 'PAK', 'Pakistan', 'Islamabad', 'Asia', 'Southern Asia', 255000000, 410000000000.00, 68.00, 60.00, 0.90),
(2025, 'LKA', 'Sri Lanka', 'Sri Jayawardenepura ', 'Asia', 'Southern Asia', 22000000, 99000000000.00, 78.00, 93.00, 1.00);

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
