-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Nov 29, 2025 at 12:20 AM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `robotswebdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `buttons`
--

CREATE TABLE `buttons` (
  `BtnID` int(11) NOT NULL,
  `BtnName` varchar(100) NOT NULL,
  `RobotId` int(11) NOT NULL,
  `Color` varchar(20) DEFAULT '#FFFFFF',
  `Operation` varchar(100) NOT NULL,
  `projectId` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `buttons`
--

INSERT INTO `buttons` (`BtnID`, `BtnName`, `RobotId`, `Color`, `Operation`, `projectId`) VALUES
(23, 'start', 17, '#1c1c1c', '/start', 11),
(31, 'Start', 31, '#f62717', '/start', 16),
(32, 'stop update', 31, '#e8be1a', '/stop update', 16),
(33, 'start', 30, '#34be0e', '/start', 16),
(34, 'Stop', 30, '#e01010', '/stop', 16),
(35, 'start', 30, '#4CAF50', '/start', 16),
(36, 'start', 30, '#4CAF50', '/start', 16),
(37, 'stop', 30, '#ff0000', '/stop', 16),
(45, 'start', 34, '#039508', '/start', 18),
(46, 'stop', 34, '#ff0000', '/stop', 18),
(47, 'farward', 34, '#3c4a9d', '/farward', 18),
(48, 'status', 34, '#00f7f4', '/status', 18),
(49, 'start', 34, '#00ff08', '/start', 18),
(50, 'stop', 34, '#ff0000', '/stop', 18),
(51, 'farward', 34, '#00d6e0', '/farward', 18),
(52, 'status', 34, '#ffe900', '/status', 18);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logId` int(11) NOT NULL,
  `topic_main` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('alert','notification') NOT NULL DEFAULT 'notification',
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`logId`, `topic_main`, `message`, `type`, `date`, `time`) VALUES
(5, 'robot/main/out', 'Robot Alpha is completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(6, 'robot/main/out', 'New Log message.', 'notification', '2025-11-12', '15:30:00'),
(7, 'robot/main/out', 'New Log message1.', 'notification', '2025-11-12', '15:31:00'),
(8, 'robot/main/out', 'New Log message2.', 'notification', '2025-11-12', '15:35:00'),
(9, 'robot/main/out', 'New Log message3.', 'notification', '2025-11-12', '15:40:00'),
(10, 'robot/main/out', 'New Log message 10.', 'notification', '2025-11-12', '16:40:00'),
(12, 'robot/car/out', 'New Allert Comes.', 'alert', '2025-11-12', '17:00:00'),
(13, 'ccc/cccu', 'set_time_21-11-2025_21-51-07', '', '2025-11-21', '19:51:07'),
(14, 'ccc/cccu', 'set_time_21-11-2025_21-51-07', '', '2025-11-21', '19:51:07'),
(15, 'ccc/cccu', 'set_time_21-11-2025_21-51-23', '', '2025-11-21', '19:51:24'),
(16, 'ccc/cccu', 'set_time_21-11-2025_21-51-23', '', '2025-11-21', '19:51:24'),
(17, 'ccc/cccu', 'set_time_21-11-2025_21-54-02', '', '2025-11-21', '19:54:02'),
(18, 'ccc/cccu', 'set_time_21-11-2025_21-54-02', '', '2025-11-21', '19:54:02'),
(19, 'ccc/cccu', 'set_time_21-11-2025_22-03-20', '', '2025-11-21', '20:03:20'),
(20, 'Airport/alpha/mainsub', 'Hello i am a meeasge to  Airport/alpha/mainsub', '', '2025-11-21', '20:04:18'),
(21, 'Airport/alpha/mainsub', '{\n  \"message_status\": {\n    \"voltage\": 40,\n    \"mode\": \"stop\"\n  }', '', '2025-11-21', '20:20:36'),
(22, 'Airport/alpha/mainsub', '{\n  \"message_status\": {\n    \"voltage\": 40,\n    \"mode\": \"stop\"\n  }', '', '2025-11-21', '20:21:17'),
(23, 'Airport/alpha/mainsub', '{\"message_status\":{\"voltage\":45,\"mode\":\"stop\"}}', '', '2025-11-21', '20:22:58'),
(24, 'Airport/alpha/mainsub', '{\n  \"message_status\": {\n    \"Cycles\": 50,\n  }\n}', '', '2025-11-21', '20:26:33'),
(25, 'Airport/alpha/mainsub', '{\"message_status\":{\"voltage\":10,\"mode\":\"run\"}}', '', '2025-11-21', '20:27:49'),
(26, 'robot/main/out', 'Battery low', 'alert', '2025-11-21', '21:26:33'),
(27, 'Airport/alpha/mainpub', 'Battery low', 'alert', '2025-11-21', '21:26:33'),
(28, 'Airport/alpha/mainsub', '{\"message_status\":{\"voltage\":10,\"mode\":\"stop\"}}', '', '2025-11-21', '20:41:07'),
(29, 'Airport/alpha/mainsub', '{\n  \"type\": \"alert\",\n  \"message\": \"Battery low new\",\n  \"topic_main\": \"Airport/alpha/mainpub\",\n \n}', '', '2025-11-21', '20:43:04'),
(30, 'Airport/alpha/mainsub', '{\n  \"type\": \"notification\",\n  \"message\": \"new note\",\n  \"topic_main\": \"Airport/alpha/mainpub\",\n \n}', '', '2025-11-21', '20:48:06'),
(31, 'new/alpha/pub', 'start', '', '2025-11-24', '08:31:30'),
(32, 'new/alpha/pub', 'stop', '', '2025-11-24', '08:31:36'),
(33, 'new/alpha/pub', 'start', '', '2025-11-24', '08:50:43'),
(34, 'new/alpha/pub', 'stop', '', '2025-11-24', '08:50:52'),
(35, 'new/alpha/pub', 'staus', '', '2025-11-24', '08:50:58'),
(36, 'new/alpha/pub', 'start', '', '2025-11-24', '09:15:36'),
(37, 'new/alpha/pub', 'stop', '', '2025-11-24', '09:15:43'),
(38, 'new/alpha/pub', 'start', '', '2025-11-24', '09:22:30'),
(39, 'new/alpha/pub', 'stop', '', '2025-11-24', '09:22:39'),
(40, 'new/alpha/pub', 'farward', '', '2025-11-24', '09:22:44'),
(41, 'new/alpha/pub', 'status', '', '2025-11-24', '09:22:54'),
(42, 'ddd/d/ddd', 'schedule_14_10_0_1_1_0_0_0_0', '', '2025-11-24', '09:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notificationId` int(11) NOT NULL,
  `topic_main` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('alert','notification') NOT NULL DEFAULT 'notification',
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notificationId`, `topic_main`, `message`, `type`, `date`, `time`) VALUES
(6, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(8, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(9, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(10, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(11, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(12, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(13, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(14, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(15, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(16, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(17, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(18, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(19, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(20, 'robot/car/out', 'Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(21, 'robot/car/out', 'New message Robot Alpha completed the assigned task successfully.', 'notification', '2025-11-12', '14:30:00'),
(22, 'robot/car/out', 'New messag.', 'notification', '2025-11-12', '14:30:00'),
(23, 'robot/car/out', 'New messag 11.', 'notification', '2025-11-12', '14:30:00'),
(24, 'robot/car/out', 'New messag 12.', 'notification', '2025-11-12', '15:30:00'),
(29, 'robot/car/out', 'New Allert Comes.', 'alert', '2025-11-12', '17:00:00'),
(30, 'robot/car/out', 'New Allert Comes on robot.', 'alert', '2025-11-12', '17:00:00'),
(31, 'ccc/cccu', 'set_time_21-11-2025_21-51-07', '', '2025-11-21', '19:51:07'),
(32, 'ccc/cccu', 'set_time_21-11-2025_21-51-07', '', '2025-11-21', '19:51:07'),
(33, 'ccc/cccu', 'set_time_21-11-2025_21-51-23', '', '2025-11-21', '19:51:24'),
(34, 'ccc/cccu', 'set_time_21-11-2025_21-51-23', '', '2025-11-21', '19:51:24'),
(35, 'ccc/cccu', 'set_time_21-11-2025_21-54-02', '', '2025-11-21', '19:54:02'),
(36, 'ccc/cccu', 'set_time_21-11-2025_21-54-02', '', '2025-11-21', '19:54:02'),
(37, 'ccc/cccu', 'set_time_21-11-2025_22-03-20', '', '2025-11-21', '20:03:20'),
(38, 'Airport/alpha/mainsub', 'Hello i am a meeasge to  Airport/alpha/mainsub', '', '2025-11-21', '20:04:18'),
(39, 'Airport/alpha/mainsub', '{\n  \"message_status\": {\n    \"voltage\": 40,\n    \"mode\": \"stop\"\n  }', '', '2025-11-21', '20:20:36'),
(40, 'Airport/alpha/mainsub', '{\n  \"message_status\": {\n    \"voltage\": 40,\n    \"mode\": \"stop\"\n  }', '', '2025-11-21', '20:21:17'),
(42, 'Airport/alpha/mainsub', '{\n  \"message_status\": {\n    \"Cycles\": 50,\n  }\n}', '', '2025-11-21', '20:26:33'),
(43, 'Airport/alpha/mainsub', '{\"message_status\":{\"voltage\":10,\"mode\":\"run\"}}', '', '2025-11-21', '20:27:49'),
(44, 'robot/main/out', 'Battery low', 'alert', '2025-11-21', '21:26:33'),
(45, 'Airport/alpha/mainpub', 'Battery low', 'alert', '2025-11-21', '21:26:33'),
(46, 'Airport/alpha/mainsub', '{\"message_status\":{\"voltage\":10,\"mode\":\"stop\"}}', '', '2025-11-21', '20:41:07'),
(47, 'Airport/alpha/mainsub', '{\n  \"type\": \"alert\",\n  \"message\": \"Battery low new\",\n  \"topic_main\": \"Airport/alpha/mainpub\",\n \n}', '', '2025-11-21', '20:43:04'),
(48, 'Airport/alpha/mainsub', '{\n  \"type\": \"notification\",\n  \"message\": \"new note\",\n  \"topic_main\": \"Airport/alpha/mainpub\",\n \n}', '', '2025-11-21', '20:48:06'),
(49, 'new/alpha/pub', 'start', '', '2025-11-24', '08:31:30'),
(50, 'new/alpha/pub', 'stop', '', '2025-11-24', '08:31:36'),
(51, 'new/alpha/pub', 'start', '', '2025-11-24', '08:50:43'),
(52, 'new/alpha/pub', 'stop', '', '2025-11-24', '08:50:52'),
(53, 'new/alpha/pub', 'staus', '', '2025-11-24', '08:50:58'),
(54, 'new/alpha/pub', 'start', '', '2025-11-24', '09:15:36'),
(55, 'new/alpha/pub', 'stop', '', '2025-11-24', '09:15:43'),
(56, 'new/alpha/pub', 'start', '', '2025-11-24', '09:22:30'),
(57, 'new/alpha/pub', 'stop', '', '2025-11-24', '09:22:39'),
(58, 'new/alpha/pub', 'farward', '', '2025-11-24', '09:22:44'),
(59, 'new/alpha/pub', 'status', '', '2025-11-24', '09:22:54'),
(60, 'ddd/d/ddd', 'schedule_14_10_0_1_1_0_0_0_0', '', '2025-11-24', '09:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `projectId` int(11) NOT NULL,
  `ProjectName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`projectId`, `ProjectName`, `Description`, `Location`, `Image`) VALUES
(11, 'Cairo Project', 'Cairo ......................!!!', 'cairo', 'uploads/Robot1.jpeg'),
(12, 'Saudia Project', 'Saudi Arabia', 'Saudi', 'uploads/Robot1.jpeg'),
(16, 'Airport', 'Airport Project', 'Jordan', '1763661066-Robot1.jpeg'),
(18, 'New project', 'New project in Brazil', 'Brazil', '1763974904-Robot1.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `robots`
--

CREATE TABLE `robots` (
  `id` int(11) NOT NULL,
  `RobotName` varchar(100) NOT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `projectId` int(11) NOT NULL,
  `isTrolley` tinyint(1) NOT NULL DEFAULT 0,
  `Sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`Sections`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `robots`
--

INSERT INTO `robots` (`id`, `RobotName`, `Image`, `projectId`, `isTrolley`, `Sections`) VALUES
(12, 'vvvvvv', 'warehousebot.png', 11, 0, '{\"main\": {\"Voltage\": 24, \"Cycles\": 500, \"Status\": \"Running\", \"ActiveBtns\": [{\"Name\": \"Forward\", \"id\": \"1\"}, {\"Name\": \"stop\", \"id\": \"2\"}], \"Topic_subscribe\": \"robot\\/main\\/in\", \"Topic_main\": \"robot\\/main\\/out\"}, \"car\": {}}'),
(13, 'ttttt', '', 11, 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(17, 'fffffffffffffffff', '', 11, 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"start\",\"id\":\"23\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(18, 'ccccccccccccc', '', 11, 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Stop\",\"id\":\"1\",\"section\":\"main\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Forward\",\"id\":\"1\",\"section\":\"car\"},{\"Name\":\"Backward\",\"id\":\"2\",\"section\":\"car\"}],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(30, 'alpha', 'robot_691f5562727a37.12560541.jpeg', 16, 0, '{\"main\": {\"Voltage\": 35, \"Cycles\": 10, \"Status\": \"Running\", \"Topic_subscribe\": \"Airport\\/alpha\\/mainpub\", \"Topic_main\": \"Airport\\/alpha\\/mainsub\", \"ActiveBtns\": [{\"id\": \"36\", \"Name\": \"start\", \"Color\": \"#4CAF50\", \"Operation\": \"\\/start\"}, {\"id\": \"37\", \"Name\": \"stop\", \"Color\": \"#ff0000\", \"Operation\": \"\\/stop\"}]}, \"car\": {}}'),
(31, 'Beat Trolley u', 'robot_691f55d981fee9.97094521.jpeg', 16, 1, '{\"main\":{\"Voltage\":20,\"Cycles\":20,\"Status\":\"Stopped\",\"Topic_subscribe\":\"fff\\/fffu\",\"Topic_main\":\"fff\\/ffu\"},\"car\":{\"Voltage\":20,\"Cycles\":20,\"Status\":\"Running\",\"Topic_subscribe\":\"fff\\/ffu\",\"Topic_main\":\"ccc\\/cccu\"}}'),
(34, 'Beta', 'robot_69241f6ee2bfd6.10150668.jpeg', 18, 0, '{\"main\": {\"Voltage\": 20, \"Cycles\": 20, \"Status\": \"Running\", \"Topic_subscribe\": \"new\\/alpha\\/sub\", \"Topic_main\": \"new\\/alpha\\/pub\", \"ActiveBtns\": [{\"id\": \"49\", \"Name\": \"start\", \"Color\": \"#00ff08\", \"Operation\": \"\\/start\"}, {\"id\": \"50\", \"Name\": \"stop\", \"Color\": \"#ff0000\", \"Operation\": \"\\/stop\"}, {\"id\": \"51\", \"Name\": \"farward\", \"Color\": \"#00d6e0\", \"Operation\": \"\\/farward\"}, {\"id\": \"52\", \"Name\": \"status\", \"Color\": \"#ffe900\", \"Operation\": \"\\/status\"}]}, \"car\": {}}'),
(35, 'Beta trolley', 'robot_69241fab05ba86.27314663.jpeg', 18, 1, '{\"main\":{\"Voltage\":\"25\",\"Cycles\":\"25\",\"Status\":\"gggggg\",\"ActiveBtns\":[],\"Topic_subscribe\":\"dff\\/fff\",\"Topic_main\":\"fff\\/ff\"},\"car\":{\"Voltage\":\"22\",\"Cycles\":\"22\",\"Status\":\"vvvvvvvvv\",\"ActiveBtns\":[],\"Topic_subscribe\":\"xdddd\\/ddd\",\"Topic_main\":\"ddd\\/d\\/ddd\"}}'),
(36, 'test beat U', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttpassword\": \"d554445 U\", \"mqttPassword\": \"\"}, \"car\": {}}'),
(37, 'test beat U2', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttpassword\": \"d554445 U\", \"mqttPassword\": \"\"}, \"car\": {}}'),
(38, 'new new', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttpassword\": \"d554445 U\", \"mqttPassword\": \"\"}, \"car\": {}}'),
(39, 'new new new', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttpassword\": \"d554445 U\"}, \"car\": {}}'),
(40, 'new new new', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttpassword\": \"d554445 U\"}, \"car\": {}}'),
(41, 'hhhhhh', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttPassword\": \"d554445 U\"}, \"car\": {}}'),
(42, 'hhhhhh', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttPassword\": \"d554445 U\"}, \"car\": {}}'),
(43, 'hhhhhh', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttPassword\": \"d554445 U\"}, \"car\": {}}'),
(44, 'bhhhhhhhh', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": {}, \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttPassword\": \"d554445 U\"}, \"car\": {}}'),
(45, 'aaaaaaaaaaaannnn', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttPassword\": \"d554445 U\"}, \"car\": {}}'),
(46, 'aaaaaaaaaaaannnn', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\", \"mqttUrl\": \"dfdsdddsd\\/ddd U\", \"mqttUsername\": \"fffffffff U\", \"mqttPassword\": \"d554445 U\"}, \"car\": {}}'),
(47, 'vcvcvcccaaaaaaaaaaaannnn', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 0, '{\"main\": {\"Voltage\": \"20\", \"Cycles\": \"35\", \"Status\": \"gggggg\", \"ActiveBtns\": [], \"Topic_subscribe\": \"dff\\/fff\", \"Topic_main\": \"fff\\/ff\"}, \"car\": {}}'),
(48, 'alphaaaaa', 'U_robot_69241fab05ba86.27314663.jpeg', 18, 1, '{\"main\":{\"Voltage\":\"20\",\"Cycles\":\"35\",\"Status\":\"gggggg\",\"ActiveBtns\":[],\"Topic_subscribe\":\"dff\\/fff\",\"Topic_main\":\"fff\\/ff\",\"mqttUrl\":\"dfdsdddsd\\/ddd U\",\"mqttUsername\":\"fffffffff U\",\"mqttPassword\":\"d554445 U\"},\"car\":{\"Voltage\":\"22\",\"Cycles\":\"22\",\"Status\":\"vvvvvvvvv\",\"ActiveBtns\":[],\"Topic_subscribe\":\"xdddd\\/ddd\",\"Topic_main\":\"ddd\\/d\\/ddd U\",\"mqttUrl\":\"dfdsdddsd\\/ddd U\",\"mqttUsername\":\"fffffffff U\",\"mqttPassword\":\"d554445 U\"}}');

--
-- Triggers `robots`
--
DELIMITER $$
CREATE TRIGGER `before_insert_robot` BEFORE INSERT ON `robots` FOR EACH ROW BEGIN
  IF NEW.isTrolley = FALSE THEN
    SET NEW.sections = JSON_OBJECT(
      'main', JSON_EXTRACT(NEW.sections, '$.main'),
      'car', JSON_OBJECT()
    );
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `before_update_robot` BEFORE UPDATE ON `robots` FOR EACH ROW BEGIN
  IF NEW.isTrolley = FALSE THEN
    SET NEW.sections = JSON_OBJECT(
      'main', JSON_EXTRACT(NEW.sections, '$.main'),
      'car', JSON_OBJECT()
    );
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `TelephoneNumber` varchar(20) DEFAULT NULL,
  `ProjectName` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Username`, `Password`, `Email`, `TelephoneNumber`, `ProjectName`) VALUES
(27, 'mahmoud Mohammed', 'Dp/xY4eUyh/FlbeZe6h9dTESaP1D1AZFCxeyxmAVsqQ=', 'mahmoudMohammed@gm.com', '111111111111111111', 'Cairo Project'),
(29, 'admin_mah@12345', 'Lw5c1p3nbQPcOSqTaJVHb/vVV4mhgAVCzwC0BQ4NOcQ=', 'admin@gdd.com', '144444444444444', 'Cairo Project'),
(34, 'test user', 'Bu//fO+r67vnsRMtiFzlzH8kbFF1bnNSfQBfXnrfjr8=', 'test@user', '54754544', 'New project');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buttons`
--
ALTER TABLE `buttons`
  ADD PRIMARY KEY (`BtnID`),
  ADD KEY `RobotId` (`RobotId`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`logId`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notificationId`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`projectId`),
  ADD UNIQUE KEY `unique_project_name` (`ProjectName`);

--
-- Indexes for table `robots`
--
ALTER TABLE `robots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_project` (`projectId`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `fk_user_project_name` (`ProjectName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buttons`
--
ALTER TABLE `buttons`
  MODIFY `BtnID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `projectId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `robots`
--
ALTER TABLE `robots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buttons`
--
ALTER TABLE `buttons`
  ADD CONSTRAINT `buttons_ibfk_1` FOREIGN KEY (`RobotId`) REFERENCES `robots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `robots`
--
ALTER TABLE `robots`
  ADD CONSTRAINT `fk_project` FOREIGN KEY (`projectId`) REFERENCES `projects` (`projectId`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_project_name` FOREIGN KEY (`ProjectName`) REFERENCES `projects` (`ProjectName`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
