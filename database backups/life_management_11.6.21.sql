-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 06, 2021 at 07:59 PM
-- Server version: 5.7.31
-- PHP Version: 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `life_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
CREATE TABLE IF NOT EXISTS `assets` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(255) DEFAULT NULL,
  `asset_type` varchar(255) DEFAULT NULL,
  `asset_desc` varchar(255) DEFAULT NULL,
  `asset_owned` int(11) DEFAULT '0',
  `asset_mthly_finance` decimal(18,2) DEFAULT NULL,
  `asset_price` decimal(18,2) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  `url_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_name`, `asset_type`, `asset_desc`, `asset_owned`, `asset_mthly_finance`, `asset_price`, `is_active`, `url_link`) VALUES
(1, 'Horseshoe Bend Rd LOT 14, Easley, SC 29642', 'Land', '0.82 Acres', 0, '218.00', '50000.00', 1, NULL),
(2, '2017 Starcraft Satellite 17RB', 'Camper', 'Used', 0, '100.00', '18900.00', 1, NULL),
(3, 'Ford Transit Connect Camper', 'Van', 'Ready to go', 0, '100.00', '19000.00', 1, 'https://thevancamper.com/post/2314/ford-transit-connect-camper-for-sale');

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

DROP TABLE IF EXISTS `budgets`;
CREATE TABLE IF NOT EXISTS `budgets` (
  `bud_id` int(11) NOT NULL AUTO_INCREMENT,
  `bud_name` varchar(255) DEFAULT NULL,
  `bud_amount` decimal(18,2) DEFAULT NULL,
  `bud_freq` char(1) DEFAULT NULL,
  `bud_desc` varchar(255) DEFAULT NULL,
  `bud_created` datetime DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`bud_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`bud_id`, `bud_name`, `bud_amount`, `bud_freq`, `bud_desc`, `bud_created`, `is_active`) VALUES
(1, 'Food', '250.00', 'M', '', '2021-10-27 00:00:00', 1),
(3, 'Donation', '50.00', 'M', '', '2021-11-03 00:00:00', 1),
(5, 'Style', '50.00', 'M', '', '2021-11-03 00:00:00', 1),
(6, 'Hygiene', '20.00', 'M', '', '2021-11-05 00:00:00', 1),
(7, 'Gas', '100.00', 'M', '', '2021-11-05 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `is_active`) VALUES
(1, 'Donation', 1),
(2, 'Food', 1),
(3, 'Gym', 1),
(4, 'Insurance', 1),
(5, 'Style', 1),
(6, 'Hygiene', 1),
(7, 'Gas', 1);

-- --------------------------------------------------------

--
-- Table structure for table `current_bills`
--

DROP TABLE IF EXISTS `current_bills`;
CREATE TABLE IF NOT EXISTS `current_bills` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_name` varchar(255) DEFAULT NULL,
  `bill_amount` decimal(18,2) DEFAULT NULL,
  `bill_freq` char(1) DEFAULT NULL,
  `bill_desc` varchar(255) DEFAULT NULL,
  `bill_created` datetime DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`bill_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `current_bills`
--

INSERT INTO `current_bills` (`bill_id`, `bill_name`, `bill_amount`, `bill_freq`, `bill_desc`, `bill_created`, `is_active`) VALUES
(7, 'Gym', '30.00', 'M', '', '2021-11-02 00:00:00', 1),
(8, 'Insurance', '86.32', 'M', '', '2021-11-03 00:00:00', 1),
(9, 'Phone', '22.97', 'M', '', '2021-11-03 00:00:00', 1),
(10, 'Microsoft OneDrive 1GB Storage', '1.99', 'M', '', '2021-11-03 00:00:00', 1),
(11, 'Oil & Filter Change', '10.00', 'M', 'Every three months or so it\'s around $30.00 or so', '2021-11-05 00:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `diet_logs`
--

DROP TABLE IF EXISTS `diet_logs`;
CREATE TABLE IF NOT EXISTS `diet_logs` (
  `dl_id` int(11) NOT NULL AUTO_INCREMENT,
  `dl_name` varchar(255) DEFAULT NULL,
  `dl_category` varchar(255) DEFAULT NULL,
  `dl_amount` decimal(18,2) DEFAULT NULL,
  `dl_measurement` decimal(18,2) DEFAULT NULL,
  `dl_calories` decimal(18,2) DEFAULT NULL,
  `dl_protein` decimal(18,2) DEFAULT NULL,
  `dl_fat` decimal(18,2) DEFAULT NULL,
  `dl_carbs` decimal(18,2) DEFAULT NULL,
  `dl_created` datetime DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`dl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `finance_expenses`
--

DROP TABLE IF EXISTS `finance_expenses`;
CREATE TABLE IF NOT EXISTS `finance_expenses` (
  `fe_id` int(11) NOT NULL AUTO_INCREMENT,
  `fe_company` varchar(255) DEFAULT NULL,
  `fe_name` varchar(255) DEFAULT NULL,
  `id_category` varchar(255) DEFAULT NULL,
  `fe_amount` decimal(18,2) DEFAULT NULL,
  `fe_date` datetime DEFAULT NULL,
  `fe_notes` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`fe_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_expenses`
--

INSERT INTO `finance_expenses` (`fe_id`, `fe_company`, `fe_name`, `id_category`, `fe_amount`, `fe_date`, `fe_notes`, `is_active`) VALUES
(1, 'Ingles', 'Got drinks for weekend.', '2', '7.79', '2021-10-23 00:00:00', 'Central, SC', 1),
(2, 'QT', 'Gas', '2', '30.71', '2021-10-30 00:00:00', 'Easley, SC', 1),
(3, 'Cantina 76', 'Veggie Taco', '2', '3.51', '2021-11-01 00:00:00', 'downtown Greenville, SC', 1),
(4, 'Buffalo Wild Wings', '12 bogo free traditional wings', '2', '13.71', '2021-11-02 00:00:00', 'Greenville, SC', 1),
(5, 'Ingles', 'got coffee & red bull', '2', '4.80', '2021-11-02 00:00:00', 'Easley, SC', 1),
(6, 'Publix', 'got four drinks for each day of weekday', '2', '6.59', '2021-11-02 00:00:00', 'Clemson, SC', 1),
(8, 'Tropical Grille', 'Grilled Chicken Veggie + Rice', '2', '8.63', '2021-11-03 00:00:00', 'Greenville, SC', 1),
(9, 'Dollar General', 'Red Bull + spicy nuts + snickers ice cream bar', '2', '4.49', '2021-11-03 00:00:00', 'Six Mile, SC', 1),
(10, 'Ingles', 'Gas', '7', '22.43', '2021-11-04 00:00:00', 'Easley, SC', 1),
(11, 'Starbucks', 'Peppermint Latte', '2', '5.50', '2021-11-04 00:00:00', 'Easley, SC', 1),
(12, 'Murasaki', 'Chicken Hibachi', '2', '16.47', '2021-11-04 00:00:00', 'Greenville, SC', 1),
(13, 'Publix', 'got drinks', '2', '10.41', '2021-11-04 00:00:00', 'Greenville, SC', 1),
(14, 'Wal-Mart', 'two deodorants', '6', '11.21', '2021-11-04 00:00:00', 'Easley, SC', 1),
(15, 'Wal-Mart', 'snacks', '2', '10.20', '2021-11-04 00:00:00', 'Easley, SC', 1),
(16, 'Jersey Mikes', 'GF Turkey Provolone Sub Sandwhich', '2', '13.17', '2021-11-05 00:00:00', 'Greenville, SC', 1),
(17, 'Coffee Underground', 'Hot Hazelnut Latte', '2', '4.20', '2021-11-05 00:00:00', 'Greenville, SC', 1),
(18, 'QT', 'Snacks', '2', '6.98', '2021-11-05 00:00:00', 'Piedmont, SC', 1),
(27, 'Ingles', 'Gas', '7', '30.38', '2021-11-06 00:00:00', 'rate 2.99 per gal', 1),
(26, 'Moe Joe Coffee', 'Iced Peppermint Latte w/Oat milk', '2', '4.91', '2021-11-06 00:00:00', '', 1),
(25, 'Chick-fil-A', 'Grilled nuggets, medium fries, kale crunch', '2', '10.08', '2021-11-06 00:00:00', 'cash', 1);

-- --------------------------------------------------------

--
-- Table structure for table `finance_incomes`
--

DROP TABLE IF EXISTS `finance_incomes`;
CREATE TABLE IF NOT EXISTS `finance_incomes` (
  `fi_id` int(11) NOT NULL AUTO_INCREMENT,
  `fi_company` varchar(255) DEFAULT NULL,
  `fi_name` varchar(255) DEFAULT NULL,
  `fi_amount` decimal(18,2) DEFAULT NULL,
  `fi_date` datetime DEFAULT NULL,
  `fi_notes` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`fi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_incomes`
--

INSERT INTO `finance_incomes` (`fi_id`, `fi_company`, `fi_name`, `fi_amount`, `fi_date`, `fi_notes`, `is_active`) VALUES
(3, 'OnGen', 'Current Job', '1211.83', '2021-11-12 00:00:00', '', 1),
(4, 'OnGen', 'Current Job', '1211.83', '2021-11-26 00:00:00', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `needs`
--

DROP TABLE IF EXISTS `needs`;
CREATE TABLE IF NOT EXISTS `needs` (
  `need_id` int(11) NOT NULL AUTO_INCREMENT,
  `need_name` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`need_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `needs`
--

INSERT INTO `needs` (`need_id`, `need_name`, `is_active`) VALUES
(1, 'Food', 1),
(2, 'Shower', 1),
(3, 'Bathroom', 1),
(4, 'Water', 1),
(5, 'Exercise', 1),
(6, 'Laundry', 1);

-- --------------------------------------------------------

--
-- Table structure for table `passive_incomes`
--

DROP TABLE IF EXISTS `passive_incomes`;
CREATE TABLE IF NOT EXISTS `passive_incomes` (
  `pi_id` int(11) NOT NULL AUTO_INCREMENT,
  `pi_name` varchar(255) DEFAULT NULL,
  `pi_amount` decimal(18,2) DEFAULT NULL,
  `pi_freq` char(1) DEFAULT NULL,
  `pi_desc` varchar(255) DEFAULT NULL,
  `pi_created` datetime DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`pi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `plans`
--

DROP TABLE IF EXISTS `plans`;
CREATE TABLE IF NOT EXISTS `plans` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `plan_name` varchar(255) DEFAULT NULL,
  `plan_desc` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`plan_id`, `plan_name`, `plan_desc`, `is_active`) VALUES
(1, 'VanLyfe', 'This plan is about living in a van in downtown Greenville', 1),
(2, 'LandLord', 'This plan is about buying land and a house or two houses, then renting out one of them for passive income.', 1),
(3, 'CamperLyfe', 'A camper placed on some owned land. ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `plan_assets`
--

DROP TABLE IF EXISTS `plan_assets`;
CREATE TABLE IF NOT EXISTS `plan_assets` (
  `plan_asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_plan` int(11) DEFAULT NULL,
  `id_asset` int(11) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`plan_asset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plan_assets`
--

INSERT INTO `plan_assets` (`plan_asset_id`, `id_plan`, `id_asset`, `is_active`) VALUES
(1, 3, 1, 1),
(2, 3, 2, 1),
(3, 1, 3, 1),
(4, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

DROP TABLE IF EXISTS `projects`;
CREATE TABLE IF NOT EXISTS `projects` (
  `proj_id` int(11) NOT NULL AUTO_INCREMENT,
  `proj_name` varchar(255) DEFAULT NULL,
  `proj_notes` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`proj_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `project_steps`
--

DROP TABLE IF EXISTS `project_steps`;
CREATE TABLE IF NOT EXISTS `project_steps` (
  `ps_id` int(11) NOT NULL AUTO_INCREMENT,
  `ps_project_id` int(11) DEFAULT NULL,
  `ps_name` varchar(255) DEFAULT NULL,
  `ps_desc` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`ps_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `pros_cons`
--

DROP TABLE IF EXISTS `pros_cons`;
CREATE TABLE IF NOT EXISTS `pros_cons` (
  `pc_id` int(11) NOT NULL AUTO_INCREMENT,
  `pc_name` varchar(255) DEFAULT NULL,
  `pc_type` varchar(255) DEFAULT NULL,
  `pc_notes` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`pc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pros_cons`
--

INSERT INTO `pros_cons` (`pc_id`, `pc_name`, `pc_type`, `pc_notes`, `is_active`) VALUES
(1, 'Closer to Job', 'Pro', '-Distance, -Gas, -Vehicle Miles, +Free Time', 1),
(2, 'Further from Job', 'Con', '+Distance, +Gas, +Vehicle Miles, -Free Time', 1),
(3, 'Peaceful Atmosphere', 'Pro', '-Stress, +Harmony', 1),
(4, 'Congested City/Town Area', 'Con', '+Stress, -Harmony', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_role` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_fname` varchar(255) DEFAULT NULL,
  `user_lname` varchar(255) DEFAULT NULL,
  `user_dob` datetime DEFAULT NULL,
  `user_notes` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_role`, `user_name`, `user_fname`, `user_lname`, `user_dob`, `user_notes`, `is_active`) VALUES
(1, 1, 'nmerck', 'Nathaniel', 'Merck', '1997-11-19 04:00:00', '', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
