-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 25, 2021 at 03:13 AM
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
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`asset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`asset_id`, `asset_name`, `asset_type`, `asset_desc`, `asset_owned`, `asset_mthly_finance`, `asset_price`, `is_active`, `url_link`, `id_user`) VALUES
(1, 'Horseshoe Bend Rd LOT 14, Easley, SC 29642', 'Land', '0.82 Acres', 0, '218.00', '50000.00', 1, NULL, 1),
(2, '2017 Starcraft Satellite 17RB', 'Camper', 'Used', 0, '100.00', '18900.00', 1, NULL, 1),
(3, 'Ford Transit Connect Camper', 'Van', 'Ready to go', 0, '100.00', '19000.00', 1, 'https://thevancamper.com/post/2314/ford-transit-connect-camper-for-sale', 1);

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

DROP TABLE IF EXISTS `bills`;
CREATE TABLE IF NOT EXISTS `bills` (
  `b_id` int(11) NOT NULL AUTO_INCREMENT,
  `b_name` varchar(255) NOT NULL,
  `b_desc` varchar(255) NOT NULL DEFAULT '''''',
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`b_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`b_id`, `b_name`, `b_desc`, `is_active`) VALUES
(1, 'Gym Membership', '\'\'', b'1'),
(2, 'Vehicle Insurance', '\'\'', b'1'),
(3, 'Phone Service', '\'\'', b'1'),
(4, 'Amazon Prime', '\'\'', b'1');

-- --------------------------------------------------------

--
-- Table structure for table `bill_logs`
--

DROP TABLE IF EXISTS `bill_logs`;
CREATE TABLE IF NOT EXISTS `bill_logs` (
  `bl_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `bl_id_bill` int(11) UNSIGNED NOT NULL,
  `bl_amount` decimal(18,2) UNSIGNED NOT NULL,
  `bl_valid_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `id_user` int(11) NOT NULL,
  UNIQUE KEY `bl_id` (`bl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bill_logs`
--

INSERT INTO `bill_logs` (`bl_id`, `bl_id_bill`, `bl_amount`, `bl_valid_date`, `is_active`, `id_user`) VALUES
(1, 9, '150.00', '2015-01-01 05:00:00', b'1', 1),
(2, 9, '86.32', '2021-11-10 02:06:26', b'1', 1),
(3, 8, '30.00', '2015-01-01 05:00:00', b'1', 1),
(4, 8, '22.97', '2021-11-10 02:11:22', b'1', 1),
(5, 7, '10.00', '2019-04-01 04:00:00', b'1', 1),
(6, 7, '23.04', '2020-01-01 05:00:00', b'1', 1),
(7, 10, '1.99', '2019-01-01 05:00:00', b'1', 1);

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
  `bud_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` bit(1) DEFAULT b'1',
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`bud_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`bud_id`, `bud_name`, `bud_amount`, `bud_freq`, `bud_desc`, `bud_created`, `is_active`, `id_user`) VALUES
(1, 'Food', '250.00', 'M', '', '2021-10-27 04:00:00', b'1', 1),
(9, 'Rent', '0.00', 'M', '', '2021-11-09 05:00:00', b'1', 1),
(3, 'Donation', '50.00', 'M', '', '2021-11-03 04:00:00', b'1', 1),
(8, 'Vehicle', '30.00', 'M', '', '2021-11-09 05:00:00', b'1', 1),
(5, 'Style', '50.00', 'M', '', '2021-11-03 04:00:00', b'1', 1),
(6, 'Hygiene', '20.00', 'M', '', '2021-11-05 04:00:00', b'1', 1),
(7, 'Gas', '100.00', 'M', '', '2021-11-05 04:00:00', b'1', 1),
(10, 'Events', '50.00', 'M', '', '2021-11-10 01:58:49', b'1', 1),
(11, 'Physical Health', '50.00', 'M', '', '2021-11-11 03:31:57', b'1', 1),
(12, 'Mental Health', '20.00', 'M', '', '2021-11-11 03:32:11', b'1', 1),
(13, 'Investment', '50.00', 'M', '', '2021-11-11 03:32:22', b'1', 1),
(14, 'Fitness', '20.00', 'M', '', '2021-11-11 03:32:51', b'0', 1),
(15, 'Side Hustle', '30.00', 'M', '', '2021-11-11 03:33:01', b'1', 1),
(16, 'Decoration', '20.00', 'M', '', '2021-11-11 03:33:13', b'1', 1),
(17, 'Relationship', '0.00', 'M', '', '2021-11-11 03:33:48', b'1', 1),
(18, 'Protection', '15.00', 'M', 'This is for self defense', '2021-11-24 00:14:45', b'1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) DEFAULT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `cat_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`, `is_active`, `cat_created`) VALUES
(1, 'Donation', b'1', '2021-11-10 01:36:30'),
(2, 'Food', b'1', '2021-11-10 01:36:30'),
(3, 'Gym', b'1', '2021-11-10 01:36:30'),
(4, 'Insurance', b'1', '2021-11-10 01:36:30'),
(5, 'Style', b'1', '2021-11-10 01:36:30'),
(6, 'Hygiene', b'1', '2021-11-10 01:36:30'),
(7, 'Gas', b'1', '2021-11-10 01:36:30'),
(8, 'Vehicle', b'1', '2021-11-10 01:36:30'),
(9, 'Events', b'1', '2021-11-10 01:57:07'),
(10, 'Physical Health', b'1', '2021-11-11 03:28:51'),
(11, 'Mental Health', b'1', '2021-11-11 03:29:00'),
(12, 'Investment', b'1', '2021-11-11 03:29:14'),
(13, 'Fitness', b'0', '2021-11-11 03:29:24'),
(14, 'Side Hustle', b'1', '2021-11-11 03:29:33'),
(15, 'Decoration', b'1', '2021-11-11 03:29:39'),
(16, 'Relationship', b'1', '2021-11-11 03:30:11'),
(17, 'Protection', b'1', '2021-11-24 00:12:06'),
(18, 'Entertainment', b'1', '2021-11-24 01:24:35');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
CREATE TABLE IF NOT EXISTS `companies` (
  `comp_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comp_name` varchar(255) DEFAULT NULL,
  `comp_desc` varchar(255) DEFAULT NULL,
  `is_active` bit(1) DEFAULT b'1',
  PRIMARY KEY (`comp_id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`comp_id`, `comp_name`, `comp_desc`, `is_active`) VALUES
(1, 'Red Bowl', '', b'1'),
(2, 'Ingles', '', b'1'),
(3, 'QT', '', b'1'),
(4, 'Wal-Mart', '', b'1'),
(5, 'Sushi Murasaki', '', b'1'),
(6, 'Tropical Grille', '', b'1'),
(7, 'Coffee Underground', '', b'1'),
(8, 'ALDI', '', b'1'),
(9, 'Lidl', '', b'1'),
(10, 'Amazon', '', b'1'),
(11, 'Chick-fil-A', '', b'1'),
(12, 'Cantina 76', '', b'1'),
(13, 'Publix', '', b'1'),
(14, 'Starbucks', '', b'1'),
(15, 'Dollar General', '', b'1'),
(16, 'Moe Joe Coffee', '', b'1'),
(17, 'Swamp Rabbit Cafe', '', b'1'),
(18, 'Other', 'This is a placeholder for a company if doesnt exist in the dropdown. I will go through and add these once the user puts the company name into the notes for each expense. ', b'1'),
(19, 'Methodical Coffee', NULL, b'1');

-- --------------------------------------------------------

--
-- Table structure for table `current_bills`
--

DROP TABLE IF EXISTS `current_bills`;
CREATE TABLE IF NOT EXISTS `current_bills` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_name` varchar(255) NOT NULL,
  `bill_amount` decimal(18,2) DEFAULT NULL,
  `bill_freq` char(1) DEFAULT NULL,
  `bill_desc` varchar(255) DEFAULT NULL,
  `bill_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_active` bit(1) DEFAULT b'1',
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`bill_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `current_bills`
--

INSERT INTO `current_bills` (`bill_id`, `bill_name`, `bill_amount`, `bill_freq`, `bill_desc`, `bill_created`, `is_active`, `id_user`) VALUES
(7, 'Gym Membership (Monthly)', '23.04', 'M', '', '2021-11-02 00:00:00', b'1', 1),
(8, 'Phone Service', '22.97', 'M', '', '2021-11-03 00:00:00', b'1', 1),
(9, 'Vehicle Insurance', '86.32', 'M', '', '2021-11-03 00:00:00', b'1', 1),
(10, 'Microsoft OneDrive Extra Storage', '1.99', 'M', '', '2021-11-03 00:00:00', b'1', 1),
(11, 'Oil & Filter Change', '10.00', 'M', 'Every three months or so it\'s around $30.00 or so', '2021-11-05 00:00:00', b'0', 1),
(12, 'Amazon Prime', '120.00', 'Y', '', '2021-11-09 20:52:58', b'1', 1),
(13, 'Gym Membership (Yearly)', '39.99', 'Y', '', '2021-11-09 21:13:32', b'1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `daily_checklists`
--

DROP TABLE IF EXISTS `daily_checklists`;
CREATE TABLE IF NOT EXISTS `daily_checklists` (
  `dc_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_task` int(11) DEFAULT NULL,
  `task_value` int(11) NOT NULL,
  `task_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `task_times` varchar(255) NOT NULL,
  PRIMARY KEY (`dc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `daily_checklists`
--

INSERT INTO `daily_checklists` (`dc_id`, `id_user`, `id_task`, `task_value`, `task_date`, `is_active`, `task_times`) VALUES
(1, 2, 1, 1, '2021-11-19 16:19:45', b'1', '');

-- --------------------------------------------------------

--
-- Table structure for table `daily_tasks`
--

DROP TABLE IF EXISTS `daily_tasks`;
CREATE TABLE IF NOT EXISTS `daily_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `task_name` varchar(255) NOT NULL,
  `task_notes` varchar(255) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `min_task_completed` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`task_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `daily_tasks`
--

INSERT INTO `daily_tasks` (`task_id`, `task_name`, `task_notes`, `created_date`, `is_active`, `min_task_completed`) VALUES
(1, 'Feed Cats', '', '2021-11-19 16:19:17', b'1', 1);

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
  `is_user` int(11) NOT NULL,
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
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`fe_id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_expenses`
--

INSERT INTO `finance_expenses` (`fe_id`, `fe_company`, `fe_name`, `id_category`, `fe_amount`, `fe_date`, `fe_notes`, `is_active`, `id_user`) VALUES
(1, 'Ingles', 'Got drinks for weekend.', '2', '7.79', '2021-10-23 00:00:00', 'Central, SC', b'0', 1),
(2, 'QT', 'Gas', '2', '30.71', '2021-10-30 00:00:00', 'Easley, SC', b'0', 1),
(3, 'Cantina 76', 'Veggie Taco', '2', '3.51', '2021-11-01 00:00:00', 'downtown Greenville, SC', b'1', 1),
(4, 'Buffalo Wild Wings', '12 bogo free traditional wings', '2', '13.71', '2021-11-02 00:00:00', 'Greenville, SC', b'1', 1),
(5, 'Ingles', 'got coffee & red bull', '2', '4.80', '2021-11-02 00:00:00', 'Easley, SC', b'1', 1),
(6, 'Publix', 'got four drinks for each day of weekday', '2', '6.59', '2021-11-02 00:00:00', 'Clemson, SC', b'1', 1),
(8, 'Tropical Grille', 'Grilled Chicken Veggie + Rice', '2', '8.63', '2021-11-03 00:00:00', 'Greenville, SC', b'1', 1),
(9, 'Dollar General', 'Red Bull + spicy nuts + snickers ice cream bar', '2', '4.49', '2021-11-03 00:00:00', 'Six Mile, SC', b'1', 1),
(10, 'Ingles', 'Gas', '7', '22.43', '2021-11-04 00:00:00', 'Easley, SC', b'1', 1),
(11, 'Starbucks', 'Peppermint Latte', '2', '5.50', '2021-11-04 00:00:00', 'Easley, SC', b'1', 1),
(12, 'Murasaki', 'Chicken Hibachi', '2', '16.47', '2021-11-04 00:00:00', 'Greenville, SC', b'1', 1),
(13, 'Publix', 'got drinks', '2', '10.41', '2021-11-04 00:00:00', 'Greenville, SC', b'1', 1),
(14, 'Wal-Mart', 'two deodorants', '6', '11.21', '2021-11-04 00:00:00', 'Easley, SC', b'1', 1),
(15, 'Wal-Mart', 'snacks', '2', '10.20', '2021-11-04 00:00:00', 'Easley, SC', b'1', 1),
(16, 'Jersey Mikes', 'GF Turkey Provolone Sub Sandwhich', '2', '13.17', '2021-11-05 00:00:00', 'Greenville, SC', b'1', 1),
(17, 'Coffee Underground', 'Hot Hazelnut Latte', '2', '4.20', '2021-11-05 00:00:00', 'Greenville, SC', b'1', 1),
(18, 'QT', 'Snacks', '2', '6.98', '2021-11-05 00:00:00', 'Piedmont, SC', b'1', 1),
(27, 'Ingles', 'Gas', '7', '30.38', '2021-11-06 00:00:00', 'rate 2.99 per gal', b'1', 1),
(26, 'Moe Joe Coffee', 'Iced Peppermint Latte w/Oat milk', '2', '4.91', '2021-11-06 00:00:00', '', b'1', 1),
(25, 'Chick-fil-A', 'Grilled nuggets, medium fries, kale crunch', '2', '10.08', '2021-11-06 00:00:00', 'cash', b'1', 1),
(28, 'First Watch', 'Got breakfast with friends', '2', '18.72', '2021-11-07 00:00:00', '', b'1', 1),
(29, 'ALDI', 'Snacks', '2', '6.33', '2021-11-07 00:00:00', 'For hiking', b'1', 1),
(30, 'Liquid Highway', 'Iced Hazelnut Lattee', '2', '11.72', '2021-11-07 00:00:00', 'With friends', b'1', 1),
(31, 'Food Lion', 'Frozen pizza', '2', '5.99', '2021-11-07 00:00:00', 'With friends', b'1', 1),
(32, 'QT', 'Snacks', '2', '6.98', '2021-11-07 00:00:00', '', b'1', 1),
(33, 'QT', 'Snacks', '2', '5.43', '2021-11-08 00:00:00', 'Breakfast', b'1', 1),
(35, 'Swamp Rabbit Cafe', 'GF Turkey Pesto Sandwich w/Sea Salt Vinegar Chips', '2', '12.69', '2021-11-09 00:00:00', '', b'1', 1),
(36, 'Wal-Mart', 'Oil and Filter', '8', '29.63', '2021-11-09 00:00:00', '2011 Toyota Corolla', b'1', 1),
(37, 'ALDI', 'Two packs of water bottles', '2', '6.30', '2021-11-09 00:00:00', '', b'0', 1),
(38, 'Ingles', 'Got drinks for week', '2', '6.80', '2021-11-09 00:00:00', '', b'1', 1),
(39, 'Starbucks', 'Hot Grande Chestnut Praline Latte', '2', '5.50', '2021-11-08 00:00:00', '', b'1', 1),
(40, 'Chick-fil-A', '8 count grilled nuggets, kale crunch, OJ, and med fries', '2', '12.03', '2021-11-08 00:00:00', '', b'1', 1),
(41, 'Trio - A Brick Oven Cafe', 'GF Spinach Artichoke Pizza w/side of marinara', '2', '20.52', '2021-11-10 00:00:00', '', b'1', 1),
(42, 'CVS', '4 pack of Red Bulls', '2', '8.49', '2021-11-10 00:00:00', '', b'1', 1),
(43, 'Wal-Mart', 'Boots for costume', '9', '14.98', '2021-11-10 00:00:00', 'Ren fair costume (This will be added back to incomes when it is returned)', b'1', 1),
(44, 'Amazon', 'Satchel and Hat for costume', '9', '37.43', '2021-11-10 00:00:00', 'Ren fair (Will return and put in incomes so technically zero)', b'1', 1),
(45, 'Elite Singles', 'Premium Membership for 3 months', '16', '64.04', '2021-11-10 00:00:00', '', b'1', 1),
(46, 'Moe Joe Coffee', 'Iced Hazelnut Latte w/Oat milk', '2', '4.91', '2021-11-11 00:00:00', '', b'1', 1),
(47, 'Starbucks', 'Venti Iced Peppermint Latte', '2', '6.27', '2021-11-12 00:00:00', 'cash', b'1', 1),
(48, 'Swamp Rabbit Cafe', 'GF Pesto Turkey Sandwich w/Creme cheese and chives chips', '2', '7.60', '2021-11-12 00:00:00', '', b'1', 1),
(49, 'Famous Hair - Woodruff road', 'Haircut', '5', '19.75', '2021-11-12 00:00:00', 'gave her a 5 dollar tip', b'1', 1),
(50, 'Publix', 'Got drinks for reward', '2', '7.77', '2021-11-12 00:00:00', 'helps me program good hehe', b'1', 1),
(51, 'Chick-fil-A', '8 count grilled nuggets with kale crunch side and med fries', '2', '10.08', '2021-11-12 00:00:00', 'Dinner', b'1', 1),
(52, 'Nana', 'Paid back for borrowing', '1', '200.00', '2021-11-13 00:00:00', '', b'0', 1),
(69, 'Waves Car Wash', 'Regular Wash', '8', '6.00', '2021-11-16 00:00:00', 'In Powdersville. I also vacuumed out my car really well.', b'1', 1),
(54, 'Chick-fil-A', '8 count grilled nuggets with med fries', '2', '7.74', '2021-11-13 00:00:00', 'went with mason', b'1', 1),
(55, 'Ingles', 'protein bars and a starbeez coffee', '2', '6.37', '2021-11-13 00:00:00', 'with mason', b'1', 1),
(56, 'Ingles', 'Got snacks and zevias for friends', '2', '6.37', '2021-11-13 00:00:00', '', b'0', 1),
(57, 'Red Bowl', 'Sesame Tofu with broccoli and fried rice', '2', '15.53', '2021-11-13 00:00:00', 'I couldnt eat the tofu because it was wrapped in gluten, but I took it to my friends house and let them have it', b'1', 1),
(58, 'Ingles', 'Got snacks and zevia for friends', '2', '16.28', '2021-11-13 00:00:00', '', b'1', 1),
(59, 'Carolina Ren Fair', 'Ticket', '9', '28.50', '2021-11-13 00:00:00', '', b'1', 1),
(60, 'QT', 'Got a snack for the drive home from the ren fair', '2', '3.99', '2021-11-14 00:00:00', '', b'1', 1),
(61, 'Swamp Rabbit Cafe', 'GF Turkey Pesto Sandwich with two bags of chips', '2', '15.44', '2021-11-15 00:00:00', 'Lunch', b'1', 1),
(62, 'QT', 'Full tank of gas', '7', '34.09', '2021-11-15 00:00:00', '', b'1', 1),
(63, 'QT', 'Two steaz teas, a bag of jalapeno cheetos and a kind bar', '2', '7.28', '2021-11-15 00:00:00', '', b'1', 1),
(64, 'Carolina Ren Fair', 'Everything bought at the ren fair is in the notes', '9', '43.25', '2021-11-14 00:00:00', 'tomatoes for mason to throw at person $5, fries $3, turkey leg $10, hot almond coffee $5.25, circulation herb extract $16, chocolate amaretto truffle ball $3, bet for mason to jump and touch a flag $1', b'1', 1),
(68, 'Swamp Rabbit Cafe', 'GF turkey pesto sandwich with fries and a sucky salad', '2', '17.06', '2021-11-16 00:00:00', 'I got a side salad and it sucks, dont get again...', b'1', 1),
(70, 'QT', 'Snacks', '2', '7.15', '2021-11-17 00:00:00', 'Three teas, and kind bar', b'1', 1),
(71, 'Cantina 76', 'Two veggie tacos', '2', '11.34', '2021-11-17 00:00:00', '', b'1', 1),
(72, 'Green Fetish', 'Bibimbap Bowl w/Basil Tomato Soup and a Honey Lavender Latte', '2', '24.34', '2021-11-17 00:00:00', 'They forgot my soup and they didnt put my order on the online shelf so i wanted 30 minutes before going up to the counter and asking about it. Then they pulled it from the back.... not going here again', b'1', 1),
(73, 'Publix', 'Got drinks for week', '2', '9.58', '2021-11-17 00:00:00', '', b'1', 1),
(74, 'Coffee Underground', 'Hot Decaf Hazelnut Latte', '2', '4.20', '2021-11-17 00:00:00', 'I was hanging out with Anna and Lisa', b'1', 1),
(75, 'Swamp Rabbit Cafe', 'GF Egg and cheese sandwich w/avocado and an iced vanilla latte', '2', '13.45', '2021-11-18 00:00:00', 'Was on lunch break with friends for fun', b'1', 1),
(76, 'Panera Bread', 'Soup and Salad', '2', '9.99', '2021-11-19 00:26:42', NULL, b'0', 2),
(77, 'ALDI', 'Food for dinner', '2', '10.67', '2021-11-19 00:00:00', '', b'0', 2),
(78, 'Ingles', 'Drinks', '2', '9.84', '2021-11-19 00:00:00', '', b'1', 1),
(79, 'QT', 'Snacks', '2', '7.07', '2021-11-20 00:00:00', '', b'1', 1),
(80, 'McAllisters Deli', 'Veggie Spud', '2', '10.83', '2021-11-20 00:00:00', '', b'1', 1),
(81, 'Ingles', 'Snacks', '2', '8.60', '2021-11-20 00:00:00', '', b'1', 1),
(82, 'Ingles', 'Gas', '7', '28.41', '2021-11-20 00:00:00', '', b'1', 1),
(83, 'Tipsy Taco', 'Paid for Anna and queso with chips and my two tacos', '2', '29.70', '2021-11-21 00:00:00', '', b'1', 1),
(84, 'River Street Sweets', 'Got a bag of random candy with friends', '2', '10.49', '2021-11-21 00:00:00', '', b'1', 1),
(85, 'Publix', 'Got a tea', '2', '2.49', '2021-11-21 00:00:00', '', b'1', 1),
(86, 'QT', 'Snacks', '2', '9.46', '2021-11-21 00:00:00', '', b'1', 1),
(87, 'Amazon', 'Messenger Bag', '12', '52.00', '2021-11-22 00:00:00', 'I need a bag to carry my laptop, gym clothes, water bottle, notepads, etc. My laptop bag and backpack of 5-7 years both are falling apart. Buying this will be a valuable investment for the next 5-10 years. (This is also my birthday gift to myself)', b'1', 1),
(88, 'Amazon', 'Munchies hot nuts for breakfast', '2', '18.26', '2021-11-22 00:00:00', '', b'1', 1),
(89, 'Swamp Rabbit Cafe', 'GF Turkey Pesto Sandwich w/sea salt vinegar chips', '2', '7.60', '2021-11-22 00:00:00', '', b'1', 1),
(90, 'Tropical Grille', 'Chicken Veggie and Rice', '2', '8.63', '2021-11-23 00:00:00', '', b'1', 1),
(91, 'Methodical Coffee', 'Cold brew with almond milk and honey', '2', '4.24', '2021-11-23 00:00:00', 'Was really good. It gave me the idea to start making my own cold brew and have it everyday instead of buying so much coffee and different drinks.', b'1', 1),
(92, 'Amazon', 'Stun Gun', '17', '10.69', '2021-11-23 00:00:00', 'This is for self defense', b'1', 1),
(93, 'Amazon', 'Stun gun for Lisas Birthday', '9', '10.69', '2021-11-23 00:00:00', '', b'1', 1),
(98, 'Swamp Rabbit Cafe', 'GF Pesto Turkey Sandwich with creme cheese chives chips', '2', '7.60', '2021-11-24 00:00:00', '', b'1', 1),
(97, 'Other', 'TEST', '15', '12.00', '2021-11-23 00:00:00', 'TEST', b'1', 2),
(99, 'Methodical Coffee', 'Cold Brew Coffee with almond milk', '2', '4.24', '2021-11-24 00:00:00', '', b'1', 1);

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
  `is_active` bit(1) DEFAULT b'1',
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`fi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `finance_incomes`
--

INSERT INTO `finance_incomes` (`fi_id`, `fi_company`, `fi_name`, `fi_amount`, `fi_date`, `fi_notes`, `is_active`, `id_user`) VALUES
(3, 'OnGen', 'Current Job', '1211.83', '2021-11-11 00:00:00', '', b'1', 1),
(4, 'OnGen', 'Current Job', '1211.84', '2021-11-24 00:00:00', 'I think I got paid a lot earlier because of everyone being off for thanksgiving thursday and friday', b'1', 1),
(6, 'Nana', 'Borrowed money', '200.00', '2021-11-08 00:00:00', 'Didnt have enough money this week', b'0', 1),
(8, 'Amazon', 'Return for Ren fair Hat', '21.39', '2021-11-18 00:00:00', '', b'1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `food_categories`
--

DROP TABLE IF EXISTS `food_categories`;
CREATE TABLE IF NOT EXISTS `food_categories` (
  `fc_id` int(11) NOT NULL AUTO_INCREMENT,
  `fc_name` varchar(255) NOT NULL,
  `fc_desc` varchar(255) NOT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`fc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `food_categories`
--

INSERT INTO `food_categories` (`fc_id`, `fc_name`, `fc_desc`, `is_active`) VALUES
(1, 'Vegetable', 'Leafy Green(Kale, Spinach), Fungus (Mushrooms) Root (Carrots, Potatoes, Radishes, Onions) etc.', b'1'),
(2, 'Fruit', 'Apple, Orange, Strawberry, Blueberry, Pineapple, Kiwi, Banana, Raspberry, Coconut?', b'1'),
(3, 'Meat', 'Chicken, Cow, Fish, Lamb, Deer, Elk, Bison, Egg?', b'1'),
(4, 'Nut', 'Peanut, Almond, Brazilian, Cashew', b'1'),
(5, 'Legume', 'Bean, Pea', b'1'),
(6, 'Dairy', 'Milk, Cheese, Yogurt, Cream, Butter', b'1'),
(7, 'Seed', 'Sunflower, Pumpkin', b'1'),
(8, 'Oil', 'Sunflower, Safflower, Coconut, Olive, Avocado, Canola, Vegetable, Peanut', b'1'),
(9, 'Grain', 'Rice, Pasta, Bread, Bagels, Barley, Rye', b'1'),
(10, 'Sweetener', 'Honey, Stevia, Cane Sugar, White Sugar, Erythritol, Sucralose, Xylitol', b'1'),
(11, 'Mineral', 'Salt, etc.', b'1'),
(12, 'Vitamin', 'Vitamin C Powder, Vitamin D Supplement, etc.', b'1'),
(13, 'Herb', 'Turmeric, Basil, Oregano, Rosemary, Pepper, Bay Leaves, Cayenne Pepper, Garlic Powder, etc.', b'1'),
(14, 'Other', 'Undefined...', b'1'),
(15, 'Powder', 'Protein powder, Cocoa powder, etc. ', b'1');

-- --------------------------------------------------------

--
-- Table structure for table `food_logs`
--

DROP TABLE IF EXISTS `food_logs`;
CREATE TABLE IF NOT EXISTS `food_logs` (
  `fl_id` int(11) NOT NULL AUTO_INCREMENT,
  `fl_name` varchar(255) NOT NULL,
  `fl_amount` decimal(18,2) NOT NULL DEFAULT '0.00',
  `id_mea` int(11) NOT NULL,
  `fl_calories` decimal(18,2) NOT NULL DEFAULT '0.00',
  `fl_carbs` decimal(18,2) NOT NULL DEFAULT '0.00',
  `fl_protein` decimal(18,2) NOT NULL DEFAULT '0.00',
  `fl_fat` decimal(18,2) NOT NULL DEFAULT '0.00',
  `fl_log_date` timestamp NOT NULL,
  `fl_notes` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`fl_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `food_logs`
--

INSERT INTO `food_logs` (`fl_id`, `fl_name`, `fl_amount`, `id_mea`, `fl_calories`, `fl_carbs`, `fl_protein`, `fl_fat`, `fl_log_date`, `fl_notes`, `id_user`, `is_active`) VALUES
(1, 'TEST', '1.00', 1, '100.00', '1.00', '1.00', '1.00', '2021-11-23 02:32:56', '', 1, b'1'),
(2, 'TEST2', '2.00', 2, '2.00', '2.00', '2.00', '2.00', '2021-11-26 02:40:45', '', 1, b'1'),
(3, 'TEST3', '1.00', 1, '123.00', '123.00', '123.00', '123.00', '2021-11-26 03:09:07', '', 1, b'1');

-- --------------------------------------------------------

--
-- Table structure for table `measurements`
--

DROP TABLE IF EXISTS `measurements`;
CREATE TABLE IF NOT EXISTS `measurements` (
  `mea_id` int(11) NOT NULL AUTO_INCREMENT,
  `mea_abbr` varchar(255) NOT NULL,
  `mea_name` varchar(255) NOT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`mea_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `measurements`
--

INSERT INTO `measurements` (`mea_id`, `mea_abbr`, `mea_name`, `is_active`) VALUES
(1, 'lb', 'pound', b'1'),
(2, 'g', 'gram', b'1'),
(3, 'mg', 'miligram', b'1'),
(4, 'mcg', 'microgram', b'1'),
(5, 'oz', 'ounce', b'1'),
(6, 'fl oz', 'fluid ounce', b'1'),
(7, 'cup', 'cup', b'1'),
(8, 'kg', 'kilogram', b'1'),
(9, 'l', 'liter', b'1'),
(10, 'ml', 'mililiter', b'1'),
(11, 'pt', 'pint', b'1'),
(12, 'gal', 'gallon', b'1'),
(13, 'tsp', 'teaspoon', b'1'),
(14, 'tbsp', 'tablespoon', b'1'),
(15, 'qt', 'quart', b'1'),
(16, 'bar', 'bar', b'1'),
(17, 'bt', 'bottle', b'1'),
(18, 'scoop', 'scoop', b'1'),
(19, 'sm', 'small', b'1'),
(20, 'med', 'medium', b'1'),
(21, 'lg', 'large', b'1'),
(22, 'Other', 'Other', b'1');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `msg_id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_message` varchar(500) NOT NULL,
  `msg_send_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `msg_read_date` timestamp NOT NULL DEFAULT '1970-01-01 05:00:00',
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `msg_subject` varchar(50) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `msg_message`, `msg_send_date`, `is_active`, `msg_read_date`, `from_user`, `to_user`, `msg_subject`) VALUES
(1, 'Thank you for signing up as a member in the LMS (Life Management System). If you have any questions, comments, or find any bugs, please contact an administrator through your messages. ', '2021-11-19 17:02:15', b'1', '2021-11-20 19:20:30', 1, 2, 'Welcome!'),
(2, 'Hi, I was wondering if you could make the website look more colorful. Maybe you could add in some pink or change it up a bit! Great design work so far!', '2021-11-20 19:27:58', b'1', '2021-11-20 19:28:34', 2, 1, 'Happy colors');

-- --------------------------------------------------------

--
-- Table structure for table `needs`
--

DROP TABLE IF EXISTS `needs`;
CREATE TABLE IF NOT EXISTS `needs` (
  `need_id` int(11) NOT NULL AUTO_INCREMENT,
  `need_name` varchar(255) DEFAULT NULL,
  `is_active` int(11) DEFAULT '1',
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`need_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `needs`
--

INSERT INTO `needs` (`need_id`, `need_name`, `is_active`, `id_user`) VALUES
(1, 'Food', 1, 1),
(2, 'Shower', 1, 1),
(3, 'Bathroom', 1, 1),
(4, 'Water', 1, 1),
(5, 'Exercise', 1, 1),
(6, 'Laundry', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `n_id` int(11) NOT NULL AUTO_INCREMENT,
  `n_subject` varchar(100) NOT NULL,
  `n_message` varchar(500) NOT NULL,
  `n_type` varchar(255) NOT NULL DEFAULT 'Message',
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `n_send_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `n_read_date` timestamp NOT NULL DEFAULT '1970-01-01 05:00:00',
  `n_from_user` int(11) NOT NULL,
  `n_to_user` int(11) NOT NULL,
  PRIMARY KEY (`n_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`n_id`, `n_subject`, `n_message`, `n_type`, `is_active`, `n_send_date`, `n_read_date`, `n_from_user`, `n_to_user`) VALUES
(3, 'New Company', 'User ID: 2 is requesting a new company record be added. Check notes for new name.', 'Request', b'1', '2021-11-24 04:37:57', '2021-11-24 22:55:28', 2, 1);

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
  `id_user` int(11) NOT NULL,
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
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`plan_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plans`
--

INSERT INTO `plans` (`plan_id`, `plan_name`, `plan_desc`, `is_active`, `id_user`) VALUES
(1, 'VanLyfe', 'This plan is about living in a van in downtown Greenville', 1, 1),
(2, 'LandLord', 'This plan is about buying land and a house or two houses, then renting out one of them for passive income.', 1, 1),
(3, 'CamperLyfe', 'A camper placed on some owned land. ', 1, 1);

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
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`plan_asset_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `plan_assets`
--

INSERT INTO `plan_assets` (`plan_asset_id`, `id_plan`, `id_asset`, `is_active`, `id_user`) VALUES
(1, 3, 1, 1, 1),
(2, 3, 2, 1, 1),
(3, 1, 3, 1, 1),
(4, 1, 1, 1, 1);

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
  `id_user` int(11) NOT NULL,
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
  `id_user` int(11) NOT NULL,
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
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`pc_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pros_cons`
--

INSERT INTO `pros_cons` (`pc_id`, `pc_name`, `pc_type`, `pc_notes`, `is_active`, `id_user`) VALUES
(1, 'Closer to Job', 'Pro', '-Distance, -Gas, -Vehicle Miles, +Free Time', b'1', 1),
(2, 'Further from Job', 'Con', '+Distance, +Gas, +Vehicle Miles, -Free Time', b'1', 1),
(3, 'Peaceful Atmosphere', 'Pro', '-Stress, +Harmony', b'1', 1),
(4, 'Congested City/Town Area', 'Con', '+Stress, -Harmony', b'1', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `id_role` int(11) NOT NULL DEFAULT '3',
  `user_name` varchar(255) DEFAULT NULL,
  `user_fname` varchar(255) DEFAULT NULL,
  `user_lname` varchar(255) DEFAULT NULL,
  `user_dob` datetime DEFAULT NULL,
  `user_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_notes` varchar(255) DEFAULT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  `pass_word` varchar(255) NOT NULL,
  `user_icon` varchar(255) NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `id_role`, `user_name`, `user_fname`, `user_lname`, `user_dob`, `user_created`, `user_notes`, `is_active`, `pass_word`, `user_icon`) VALUES
(1, 1, 'redfox', 'Nathaniel', 'Merck', '1997-11-19 04:00:00', '2021-11-13 21:17:47', 'poop', b'1', '0421008445828ceb46f496700a5fa65e', ''),
(2, 3, 'abanana', 'Anna', 'Whelan', '1997-11-01 00:00:00', '2021-11-19 04:55:14', 'boop', b'1', '5f4dcc3b5aa765d61d8327deb882cf99', ''),
(4, 3, 'slothnigga', 'Mason', 'Merck', '1999-09-23 00:00:00', '2021-11-19 19:55:55', 'This is a protein filled chad with high levels of testosterone. (He is here to break the system for me)', b'1', '0bc0120463e5dcbb2ee289e4a8b26206', ''),
(5, 3, 'abell', 'Annabel', 'Lindsey', '2004-02-10 00:00:00', '2021-11-20 06:15:26', NULL, b'1', '5f4dcc3b5aa765d61d8327deb882cf99', ''),
(6, 3, 'gingersnap', 'Marie', 'Whelan', '1999-09-12 00:00:00', '2021-11-20 06:16:27', NULL, b'1', '5f4dcc3b5aa765d61d8327deb882cf99', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(255) DEFAULT NULL,
  `role_notes` varchar(255) DEFAULT NULL,
  `is_active` bit(1) DEFAULT b'1',
  `role_color` varchar(255) NOT NULL DEFAULT '#28a745',
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`role_id`, `role_name`, `role_notes`, `is_active`, `role_color`) VALUES
(1, 'Admin', 'Has access and full control to everything in the system.', b'1', '#ff3955'),
(2, 'Premium', 'A member with paid membership to the site.', b'1', '#3065fb'),
(3, 'Member', 'A member with access to the site.', b'1', '#28a745');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
