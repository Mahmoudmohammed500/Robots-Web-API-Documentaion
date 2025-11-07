-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2025 at 09:37 AM
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
(16, 'Start', 8, 'red', '/start', 10),
(17, 'ُEnd', 8, 'red', '/End', 10);

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `logId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `robotId` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('alert','notification') NOT NULL DEFAULT 'notification',
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notificationId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `robotId` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` enum('alert','notification') NOT NULL DEFAULT 'notification',
  `date` date NOT NULL,
  `time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(9, 'New project', 'ffffffff', 'Cairo', 'uploads/robot.jpg'),
(10, 'Main project', 'ffffffff', 'Cairo', 'uploads/robot.jpg');

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
(7, 'cccccccccccccccc  New', 'warehousebot.png', 10, 'mqtt://192.168.1.50:1883', 1, '{\"main\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[{\"Name\":\"Forward\",\"id\":\"1\"},{\"Name\":\"stop\",\"id\":\"2\"}],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"},\"car\":{\"Voltage\":24,\"Cycles\":500,\"Status\":\"Running\",\"ActiveBtns\":[\"Forward\",\"Stop\"],\"Topic_subscribe\":\"robot\\/main\\/in\",\"Topic_main\":\"robot\\/main\\/out\"}}'),
(8, 'WarehouseBot-03', 'warehousebot.png', 10, 'mqtt://192.168.1.50:1883', 0, '{\"main\": {\"Voltage\": 24, \"Cycles\": 500, \"Status\": \"Running\", \"ActiveBtns\": [{\"Name\": \"Forward\", \"id\": \"1\"}, {\"Name\": \"stop\", \"id\": \"2\"}], \"Topic_subscribe\": \"robot\\/main\\/in\", \"Topic_main\": \"robot\\/main\\/out\"}, \"car\": {}}');

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
(23, 'testadmin update', 's9jRCqXHjwYBZqzFLLoquPmPAau1e3v9doZ+n8RmmMU=', '01000000001', 'Main project');

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
  ADD PRIMARY KEY (`logId`),
  ADD KEY `projectId` (`projectId`),
  ADD KEY `robotId` (`robotId`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notificationId`),
  ADD KEY `projectId` (`projectId`),
  ADD KEY `robotId` (`robotId`);

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
  MODIFY `BtnID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `logId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notificationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `projectId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `robots`
--
ALTER TABLE `robots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `buttons`
--
ALTER TABLE `buttons`
  ADD CONSTRAINT `buttons_ibfk_1` FOREIGN KEY (`RobotId`) REFERENCES `robots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`projectId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `logs_ibfk_2` FOREIGN KEY (`robotId`) REFERENCES `robots` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`projectId`) REFERENCES `projects` (`projectId`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`robotId`) REFERENCES `robots` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
