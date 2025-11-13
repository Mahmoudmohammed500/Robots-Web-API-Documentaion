-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 13, 2025 at 01:33 AM
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
(24, 'finish', 23, '#292929', '/start', 12);

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
(10, 'robot/main/out', 'New Log message 10.', 'notification', '2025-11-12', '16:40:00');

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
(24, 'robot/car/out', 'New messag 12.', 'notification', '2025-11-12', '15:30:00');

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
(12, 'Saudia Project', 'Saudi Arabia', 'Saudi', 'uploads/Robot1.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `robots`
--

CREATE TABLE `robots` (
  `id` int(11) NOT NULL,
  `RobotName` varchar(100) NOT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `projectId` int(11) NOT NULL,
  `mqttUrl` varchar(255) NOT NULL,
  `isTrolley` tinyint(1) NOT NULL DEFAULT 0,
  `Sections` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`Sections`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `robots`
--

INSERT INTO `robots` (`id`, `RobotName`, `Image`, `projectId`, `mqttUrl`, `isTrolley`, `Sections`) VALUES
(12, 'vvvvvv', 'warehousebot.png', 11, 'mqtt://192.168.1.50:1883', 0, '{\"main\": {\"Voltage\": 24, \"Cycles\": 500, \"Status\": \"Running\", \"ActiveBtns\": [{\"Name\": \"Forward\", \"id\": \"1\"}, {\"Name\": \"stop\", \"id\": \"2\"}], \"Topic_subscribe\": \"robot\\/main\\/in\", \"Topic_main\": \"robot\\/main\\/out\"}, \"car\": {}}'),
(13, 'ttttt', '', 11, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(14, 'Robot Beta', 'Robot1.jpeg', 12, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(15, 'Robot Alpha', 'warehousebot.png', 12, 'mqtt://192.168.1.50:1883', 0, '{\"main\": {\"Voltage\": 24, \"Cycles\": 500, \"Status\": \"Running\", \"ActiveBtns\": [{\"Name\": \"stop\", \"id\": \"2\"}, {\"Name\": \"Forward\", \"id\": \"1\"}], \"Topic_subscribe\": \"robot\\/main\\/in\", \"Topic_main\": \"robot\\/main\\/out\"}, \"car\": {}}'),
(17, 'fffffffffffffffff', '', 11, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"start\",\"id\":\"23\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(18, 'ccccccccccccc', '', 11, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Stop\",\"id\":\"1\",\"section\":\"main\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Forward\",\"id\":\"1\",\"section\":\"car\"},{\"Name\":\"Backward\",\"id\":\"2\",\"section\":\"car\"}],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(19, 'R1', '', 12, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Stop\",\"id\":\"1\",\"section\":\"main\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":20,\"Status\":\"Stopped\",\"ActiveBtns\":[{\"Name\":\"Forward\",\"id\":\"1\",\"section\":\"car\"},{\"Name\":\"Backward\",\"id\":\"2\",\"section\":\"car\"}],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(20, 'bbbb', '', 12, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":20,\"Cycles\":30,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Forward\",\"id\":\"1\",\"section\":\"main\"},{\"Name\":\"Backward\",\"id\":\"2\",\"section\":\"main\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/ma\\t\"},\"car\":{\"Voltage\":20,\"Cycles\":40,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Stop\",\"id\":\"1\",\"section\":\"car\"}],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/ca\"}}'),
(21, 'ttttttttttttt update', '', 12, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":20,\"Cycles\":30,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"start\",\"id\":\"1\",\"section\":\"main\"},{\"Name\":\"Forward\",\"id\":\"1762976016155\",\"section\":\"main\"},{\"Name\":\"Backward\",\"id\":\"1762976016727\",\"section\":\"main\"},{\"Name\":\"status\",\"id\":\"1762976027197\",\"section\":\"main\"},{\"Name\":\"status\",\"id\":\"1762976557635\",\"section\":\"main\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":50,\"Cycles\":50,\"Status\":\"Stopped\",\"ActiveBtns\":[{\"Name\":\"status\",\"id\":\"1\",\"section\":\"car\"},{\"Name\":\"Forward\",\"id\":\"1762976035999\",\"section\":\"car\"},{\"Name\":\"Backward\",\"id\":\"1762976036365\",\"section\":\"car\"},{\"Name\":\"Stop\",\"id\":\"1762976391603\",\"section\":\"car\"}],\"Topic_subscribe\":\"robot\\/car\\/in\",\"Topic_main\":\"robot\\/car\\/out\"}}'),
(23, 'trolleytest update', '', 12, 'ggg/ggg 2', 1, '{\"main\":{\"Voltage\":12,\"Cycles\":12,\"Status\":\"Stopped\",\"Topic_subscribe\":\"gfgg\\/ggg\",\"Topic_main\":\"ggg\\/gg\",\"ActiveBtns\":[{\"Name\":\"finish\",\"id\":\"24\"}]},\"car\":{\"Voltage\":13,\"Cycles\":13,\"Status\":\"Running\",\"Topic_subscribe\":\"rrrr\\/\\/rrr\",\"Topic_main\":\"rrrf\\/f555\"}}');

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
  `TelephoneNumber` varchar(20) DEFAULT NULL,
  `ProjectName` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `Username`, `Password`, `TelephoneNumber`, `ProjectName`) VALUES
(27, 'mahmoud Mohammed', 'GRYPQiMvWWbh0FxVVq65NATZEF4VR2FXIaQvj28yCxU=', '111111111111111111', 'Cairo Project'),
(28, 'asssssss', 'FG/zPoDkdd8QMy1t1OA6t/lOKn2VmJhP+f72SbWPP6Q=', '144444444444444', 'Cairo Project');

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
  ADD KEY `fk_user_project_name` (`ProjectName`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buttons`
--
ALTER TABLE `buttons`
  MODIFY `BtnID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `projectId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `robots`
--
ALTER TABLE `robots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

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
