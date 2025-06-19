-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 06:07 PM
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
-- Database: `dueday`
--

-- --------------------------------------------------------

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
CREATE TABLE IF NOT EXISTS `achievements` (
  `Achievement_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Achievement_Description` text NOT NULL,
  `Achievement_Points` int(11) NOT NULL,
  PRIMARY KEY (`Achievement_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `achievements`
--

INSERT INTO `achievements` (`Achievement_ID`, `Achievement_Description`, `Achievement_Points`) VALUES
(1, 'Welcome Aboard! - Created an account.', 10),
(2, 'First Steps - Submitted your first assignment.', 25),
(3, 'Civic Duty - Voted in your first poll.', 15),
(4, 'Event-Goer - RSVP\'d for an event.', 20);

-- --------------------------------------------------------

--
-- Table structure for table `admin_data`
--

DROP TABLE IF EXISTS `admin_data`;
CREATE TABLE IF NOT EXISTS `admin_data` (
  `Data_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Data_Description` varchar(255) NOT NULL,
  `Data_Value` varchar(255) NOT NULL,
  PRIMARY KEY (`Data_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE IF NOT EXISTS `announcements` (
  `Announcement_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Announcement_Title` varchar(100) NOT NULL,
  `Announcement_Description` text DEFAULT NULL,
  `Announcement_Priority` int(11) NOT NULL,
  `Creator_User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Announcement_ID`),
  KEY `Announcement_Priority` (`Announcement_Priority`),
  KEY `Creator_User_ID` (`Creator_User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_user`
--

DROP TABLE IF EXISTS `announcement_user`;
CREATE TABLE IF NOT EXISTS `announcement_user` (
  `Announcement_User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Announcement_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Announcement_User_ID`),
  KEY `Announcement_ID` (`Announcement_ID`),
  KEY `User_ID` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `Assignment_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Assignment_Creator_ID` int(11) NOT NULL,
  `Class_ID` int(11) DEFAULT NULL,
  `Assignment_Title` varchar(100) NOT NULL,
  `Assignment_Description` text DEFAULT NULL,
  `Assignment_DueDate` datetime NOT NULL,
  `Assignment_Marks` int(11) DEFAULT NULL,
  `Assignment_Instructions` text DEFAULT NULL,
  PRIMARY KEY (`Assignment_ID`),
  KEY `Assignment_Creator_ID` (`Assignment_Creator_ID`),
  KEY `fk_assignment_class` (`Class_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`Assignment_ID`, `Assignment_Creator_ID`, `Class_ID`, `Assignment_Title`, `Assignment_Description`, `Assignment_DueDate`, `Assignment_Marks`, `Assignment_Instructions`) VALUES
(1, 5, NULL, 'OOP project', 'finish implementing all the API and libaries ', '2025-07-11 12:30:00', 20, 'Use the provided resources');

-- --------------------------------------------------------

--
-- Table structure for table `assignment_submission_data`
--

DROP TABLE IF EXISTS `assignment_submission_data`;
CREATE TABLE IF NOT EXISTS `assignment_submission_data` (
  `Submission_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Assignment_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Submission_Date` datetime NOT NULL,
  `File_Path` varchar(255) NOT NULL,
  `Notes` text DEFAULT NULL,
  `Grade` varchar(10) DEFAULT NULL,
  `Feedback` text DEFAULT NULL,
  PRIMARY KEY (`Submission_ID`),
  KEY `Assignment_ID` (`Assignment_ID`),
  KEY `User_ID` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `Class_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Class_Name` varchar(100) NOT NULL,
  PRIMARY KEY (`Class_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedule`
--

DROP TABLE IF EXISTS `class_schedule`;
CREATE TABLE IF NOT EXISTS `class_schedule` (
  `Entry_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Class_ID` int(11) NOT NULL,
  `Class_Time` datetime NOT NULL,
  `Venue_ID` int(11) NOT NULL,
  PRIMARY KEY (`Entry_ID`),
  KEY `Class_ID` (`Class_ID`),
  KEY `Venue_ID` (`Venue_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `Comment_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Assignment_ID` int(11) NOT NULL,
  `Comment_Content` text NOT NULL,
  `Comment_Date` datetime NOT NULL,
  PRIMARY KEY (`Comment_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `idx_assignment_id` (`Assignment_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `Event_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Event_Name` varchar(100) NOT NULL,
  `Event_Description` text DEFAULT NULL,
  `Event_Date` datetime NOT NULL,
  `Venue_ID` int(11) NOT NULL,
  PRIMARY KEY (`Event_ID`),
  KEY `Venue_ID` (`Venue_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`Event_ID`, `Event_Name`, `Event_Description`, `Event_Date`, `Venue_ID`) VALUES
(1, 'Chorale singalong', 'Come and experience Karaoke like never before', '2025-06-19 17:00:00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_attendee_data`
--

DROP TABLE IF EXISTS `event_attendee_data`;
CREATE TABLE IF NOT EXISTS `event_attendee_data` (
  `Attendee_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Event_ID` int(11) NOT NULL,
  PRIMARY KEY (`Attendee_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Event_ID` (`Event_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_attendee_data`
--

INSERT INTO `event_attendee_data` (`Attendee_ID`, `User_ID`, `Event_ID`) VALUES
(1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `Notification_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Notification_Content` text NOT NULL,
  `Notification_Date` datetime NOT NULL,
  PRIMARY KEY (`Notification_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_user`
--

DROP TABLE IF EXISTS `notification_user`;
CREATE TABLE IF NOT EXISTS `notification_user` (
  `Notification_User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Notification_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  PRIMARY KEY (`Notification_User_ID`),
  KEY `Notification_ID` (`Notification_ID`),
  KEY `User_ID` (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

DROP TABLE IF EXISTS `polls`;
CREATE TABLE IF NOT EXISTS `polls` (
  `Poll_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Poll_Title` varchar(100) NOT NULL,
  `Poll_Description` text DEFAULT NULL,
  `Class_ID` int(11) DEFAULT NULL,
  `Expires_At` datetime NOT NULL,
  `Status` varchar(20) NOT NULL,
  `Is_Anonymous` tinyint(1) NOT NULL DEFAULT 0,
  `Allow_Multiple_Choices` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Poll_ID`),
  KEY `fk_poll_class` (`Class_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `polls`
--

INSERT INTO `polls` (`Poll_ID`, `Poll_Title`, `Poll_Description`, `Class_ID`, `Expires_At`, `Status`, `Is_Anonymous`, `Allow_Multiple_Choices`) VALUES
(1, 'Spanish Make up Cat', 'For students who missed the previous CAT', NULL, '2025-06-21 07:00:00', 'Active', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `poll_data`
--

DROP TABLE IF EXISTS `poll_data`;
CREATE TABLE IF NOT EXISTS `poll_data` (
  `User_ID` int(11) NOT NULL,
  `Poll_ID` int(11) NOT NULL,
  `Option_ID` int(11) NOT NULL,
  PRIMARY KEY (`User_ID`,`Poll_ID`),
  KEY `Poll_ID` (`Poll_ID`),
  KEY `Option_ID` (`Option_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_data`
--

INSERT INTO `poll_data` (`User_ID`, `Poll_ID`, `Option_ID`) VALUES
(4, 1, 1),
(5, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `poll_options`
--

DROP TABLE IF EXISTS `poll_options`;
CREATE TABLE IF NOT EXISTS `poll_options` (
  `Option_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Poll_ID` int(11) NOT NULL,
  `Option_Text` varchar(255) NOT NULL,
  PRIMARY KEY (`Option_ID`),
  KEY `Poll_ID` (`Poll_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_options`
--

INSERT INTO `poll_options` (`Option_ID`, `Poll_ID`, `Option_Text`) VALUES
(1, 1, 'June 21'),
(2, 1, 'June 28');

-- --------------------------------------------------------

--
-- Table structure for table `priority`
--

DROP TABLE IF EXISTS `priority`;
CREATE TABLE IF NOT EXISTS `priority` (
  `Priority_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Priority_Type` varchar(50) NOT NULL,
  PRIMARY KEY (`Priority_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `priority`
--

INSERT INTO `priority` (`Priority_ID`, `Priority_Type`) VALUES
(1, 'Low'),
(2, 'Normal'),
(3, 'High'),
(4, 'Urgent');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `Role_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Role_Name` varchar(50) NOT NULL,
  PRIMARY KEY (`Role_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`Role_ID`, `Role_Name`) VALUES
(1, 'Module Leader'),
(2, 'Student'),
(3, 'Event Coordinator'),
(4, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

DROP TABLE IF EXISTS `timetable`;
CREATE TABLE IF NOT EXISTS `timetable` (
  `Timetable_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Timetable_Url` varchar(255) DEFAULT NULL,
  `Timetable_BLOB` longblob DEFAULT NULL,
  PRIMARY KEY (`Timetable_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `F_Name` varchar(50) NOT NULL,
  `L_Name` varchar(50) NOT NULL,
  `Role_ID` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `Email` (`Email`),
  KEY `Role_ID` (`Role_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `Email`, `Password`, `F_Name`, `L_Name`, `Role_ID`, `status`) VALUES
(1, 'leader@dueday.com', 'hashed_password', 'Natalie', 'Leader', 1, 'active'),
(2, 'student@dueday.com', 'hashed_password', 'Alvin', 'Student', 2, 'active'),
(3, 'ialvinmurithi@gmail.com', '$2y$10$.oxCuyOQbY0kiiwb1fFxN.AgnmmtQ1ARV9Zh1jdX.mfC0tnj2ZxlG', 'Alvin', 'Murithi', 3, 'active'),
(4, 'travism@gmail.com', '$2y$10$51w.mq/2Ya7Hj5xeyAAU9eq//OLTm..isTuxZ3dRbq6/7ybzUbwuO', 'Travis', 'Mutungi', 2, 'active'),
(5, 'dmuriuki@gmail.com', '$2y$10$O9m8z0oyYlkNr4DF.Y2ote0eMeT5eq1x4VDRRBaNtZ.VgQUJi4KCe', 'Daniel', 'Muriuki', 1, 'active'),
(6, 'erodriguez@gmail.com', '$2y$10$Ql59.CzaaXS1bHDe7zg45OqwlrQPNAeWNclu5xBffdnMweH3SmZze', 'Eva', 'Rodriguez', 4, 'active'),
(7, 'nataliec@gmail.com', '$2y$10$7x1XD.NkUzGj.mkQnGkOGOgcUphjhNEz.1ip5DnVw6cvT7eSQNE86', 'Natalie', 'Chelangat', 4, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_achievements`
--

DROP TABLE IF EXISTS `user_achievements`;
CREATE TABLE IF NOT EXISTS `user_achievements` (
  `User_ID` int(11) NOT NULL,
  `Achievement_ID` int(11) NOT NULL,
  PRIMARY KEY (`User_ID`,`Achievement_ID`),
  KEY `Achievement_ID` (`Achievement_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_achievements`
--

INSERT INTO `user_achievements` (`User_ID`, `Achievement_ID`) VALUES
(6, 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_classes`
--

DROP TABLE IF EXISTS `user_classes`;
CREATE TABLE IF NOT EXISTS `user_classes` (
  `User_ID` int(11) NOT NULL,
  `Class_ID` int(11) NOT NULL,
  PRIMARY KEY (`User_ID`,`Class_ID`),
  KEY `Class_ID` (`Class_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_timetable`
--

DROP TABLE IF EXISTS `user_timetable`;
CREATE TABLE IF NOT EXISTS `user_timetable` (
  `User_Timetable_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Timetable_ID` int(11) NOT NULL,
  PRIMARY KEY (`User_Timetable_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Timetable_ID` (`Timetable_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

DROP TABLE IF EXISTS `venues`;
CREATE TABLE IF NOT EXISTS `venues` (
  `Venue_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Venue_Name` varchar(100) NOT NULL,
  PRIMARY KEY (`Venue_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`Venue_ID`, `Venue_Name`) VALUES
(1, 'Microsoft Auditorium');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`Announcement_Priority`) REFERENCES `priority` (`Priority_ID`),
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`Creator_User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `announcement_user`
--
ALTER TABLE `announcement_user`
  ADD CONSTRAINT `announcement_user_ibfk_1` FOREIGN KEY (`Announcement_ID`) REFERENCES `announcements` (`Announcement_ID`),
  ADD CONSTRAINT `announcement_user_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`Assignment_Creator_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `fk_assignment_class` FOREIGN KEY (`Class_ID`) REFERENCES `classes` (`Class_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `assignment_submission_data`
--
ALTER TABLE `assignment_submission_data`
  ADD CONSTRAINT `assignment_submission_data_ibfk_1` FOREIGN KEY (`Assignment_ID`) REFERENCES `assignments` (`Assignment_ID`),
  ADD CONSTRAINT `assignment_submission_data_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `class_schedule`
--
ALTER TABLE `class_schedule`
  ADD CONSTRAINT `class_schedule_ibfk_1` FOREIGN KEY (`Class_ID`) REFERENCES `classes` (`Class_ID`),
  ADD CONSTRAINT `class_schedule_ibfk_2` FOREIGN KEY (`Venue_ID`) REFERENCES `venues` (`Venue_ID`);

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`Venue_ID`) REFERENCES `venues` (`Venue_ID`);

--
-- Constraints for table `event_attendee_data`
--
ALTER TABLE `event_attendee_data`
  ADD CONSTRAINT `event_attendee_data_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `event_attendee_data_ibfk_2` FOREIGN KEY (`Event_ID`) REFERENCES `events` (`Event_ID`);

--
-- Constraints for table `notification_user`
--
ALTER TABLE `notification_user`
  ADD CONSTRAINT `notification_user_ibfk_1` FOREIGN KEY (`Notification_ID`) REFERENCES `notifications` (`Notification_ID`),
  ADD CONSTRAINT `notification_user_ibfk_2` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `polls`
--
ALTER TABLE `polls`
  ADD CONSTRAINT `fk_poll_class` FOREIGN KEY (`Class_ID`) REFERENCES `classes` (`Class_ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `poll_data`
--
ALTER TABLE `poll_data`
  ADD CONSTRAINT `poll_data_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `poll_data_ibfk_2` FOREIGN KEY (`Poll_ID`) REFERENCES `polls` (`Poll_ID`),
  ADD CONSTRAINT `poll_data_ibfk_3` FOREIGN KEY (`Option_ID`) REFERENCES `poll_options` (`Option_ID`);

--
-- Constraints for table `poll_options`
--
ALTER TABLE `poll_options`
  ADD CONSTRAINT `poll_options_ibfk_1` FOREIGN KEY (`Poll_ID`) REFERENCES `polls` (`Poll_ID`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`Role_ID`) REFERENCES `role` (`Role_ID`);

--
-- Constraints for table `user_achievements`
--
ALTER TABLE `user_achievements`
  ADD CONSTRAINT `user_achievements_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `user_achievements_ibfk_2` FOREIGN KEY (`Achievement_ID`) REFERENCES `achievements` (`Achievement_ID`);

--
-- Constraints for table `user_classes`
--
ALTER TABLE `user_classes`
  ADD CONSTRAINT `user_classes_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `user_classes_ibfk_2` FOREIGN KEY (`Class_ID`) REFERENCES `classes` (`Class_ID`);

--
-- Constraints for table `user_timetable`
--
ALTER TABLE `user_timetable`
  ADD CONSTRAINT `user_timetable_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `user_timetable_ibfk_2` FOREIGN KEY (`Timetable_ID`) REFERENCES `timetable` (`Timetable_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
