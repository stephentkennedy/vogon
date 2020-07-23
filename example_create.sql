-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 23, 2020 at 04:21 PM
-- Server version: 10.3.22-MariaDB-0+deb10u1
-- PHP Version: 7.3.19-1~deb10u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vogon`
--

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `data_id` int(11) NOT NULL,
  `data_name` tinytext NOT NULL,
  `data_slug` text NOT NULL,
  `data_content` longtext DEFAULT NULL,
  `data_type` varchar(25) NOT NULL,
  `data_parent` int(11) NOT NULL DEFAULT 0,
  `data_status` varchar(25) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_edit` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_key` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `data_meta`
--

CREATE TABLE `data_meta` (
  `data_meta_id` int(11) NOT NULL,
  `data_id` int(11) NOT NULL,
  `data_meta_name` tinytext NOT NULL,
  `data_meta_content` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `route`
--

CREATE TABLE `route` (
  `route_id` int(11) NOT NULL,
  `route_controller` text NOT NULL,
  `route_ext` text DEFAULT NULL,
  `ext_primary` tinyint(1) NOT NULL DEFAULT 0,
  `route_slug` text DEFAULT NULL,
  `in_h_nav` tinyint(1) NOT NULL DEFAULT 0,
  `in_f_nav` tinyint(1) NOT NULL DEFAULT 0,
  `nav_display` tinytext DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_edit` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_key` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `var`
--

CREATE TABLE `var` (
  `var_id` int(11) NOT NULL,
  `var_session` tinyint(1) NOT NULL DEFAULT 0,
  `var_name` text NOT NULL,
  `var_content` text NOT NULL,
  `var_type` int(11) DEFAULT NULL,
  `create_date` datetime NOT NULL DEFAULT current_timestamp(),
  `last_edit` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_key` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`data_id`),
  ADD KEY `data_type` (`data_type`);

--
-- Indexes for table `data_meta`
--
ALTER TABLE `data_meta`
  ADD PRIMARY KEY (`data_meta_id`);

--
-- Indexes for table `route`
--
ALTER TABLE `route`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `var`
--
ALTER TABLE `var`
  ADD PRIMARY KEY (`var_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `data_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11937;
--
-- AUTO_INCREMENT for table `data_meta`
--
ALTER TABLE `data_meta`
  MODIFY `data_meta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48659;
--
-- AUTO_INCREMENT for table `route`
--
ALTER TABLE `route`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- AUTO_INCREMENT for table `var`
--
ALTER TABLE `var`
  MODIFY `var_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
