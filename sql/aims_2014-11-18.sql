# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 127.0.0.1 (MySQL 5.5.38-0+wheezy1)
# Database: aims
# Generation Time: 2014-11-18 14:17:43 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

# Create the databse and user to access db
# ------------------------------------------------------------
CREATE DATABASE aims;
CREATE USER 'aims'@'localhost' IDENTIFIED BY 'password';
GRANT CREATE,DELETE,INSERT,SELECT,UPDATE ON aims.* TO 'aims'@'localhost';
FLUSH PRIVILEGES;
USE aims;

# Dump of table accountTypes
# ------------------------------------------------------------

CREATE TABLE `accountTypes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `shortName` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table bookingRatesExternal
# ------------------------------------------------------------

CREATE TABLE `bookingRatesExternal` (
  `accountTypeId` int(11) unsigned NOT NULL,
  `staffRate` int(3) NOT NULL,
  `highAccuracyRate` int(3) NOT NULL,
  `lowAccuracyRate` int(3) NOT NULL,
  PRIMARY KEY (`accountTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table bookingRatesInternal
# ------------------------------------------------------------

CREATE TABLE `bookingRatesInternal` (
  `accountTypeId` int(11) unsigned NOT NULL,
  `staffRate` int(3) NOT NULL,
  `oneHour` int(3) NOT NULL,
  `fourHours` int(3) NOT NULL,
  `eightHours` int(3) NOT NULL,
  `sixteenHours` int(3) NOT NULL,
  `twentyFourHours` int(3) NOT NULL,
  PRIMARY KEY (`accountTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table conferenceBookings
# ------------------------------------------------------------

CREATE TABLE `conferenceBookings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `conferenceId` int(11) NOT NULL,
  `dateFrom` date NOT NULL,
  `dateTo` date NOT NULL,
  `timeFrom` time NOT NULL,
  `timeTo` time NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table faq
# ------------------------------------------------------------

CREATE TABLE `faq` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(1000) NOT NULL DEFAULT '',
  `answer` varchar(5000) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table instrumentBookings
# ------------------------------------------------------------

CREATE TABLE `instrumentBookings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `instrumentId` int(11) NOT NULL,
  `dateFrom` date NOT NULL,
  `dateTo` date NOT NULL,
  `timeFrom` time NOT NULL,
  `timeTo` time NOT NULL,
  `archiveStatus` tinyint(1) NOT NULL DEFAULT '0',
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table invoices
# ------------------------------------------------------------

CREATE TABLE `invoices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `primaryInvestigator` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `addressOne` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `addressTwo` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `city` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `state` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `zip` int(15) NOT NULL,
  `pcbu` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `pid` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `did` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `po` varchar(20) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `jsonString` varchar(10000) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `total` varchar(11) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table mscAnalysisServices
# ------------------------------------------------------------

CREATE TABLE `mscAnalysisServices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `samplePrepId` int(11) DEFAULT '0',
  `memberRegular` int(5) NOT NULL,
  `memberDiscount` int(5) NOT NULL,
  `memberCutoff` int(5) NOT NULL,
  `collaboratorRegular` int(5) NOT NULL,
  `collaboratorDiscount` int(5) NOT NULL,
  `collaboratorCutoff` int(5) NOT NULL,
  `umbRegular` int(5) NOT NULL,
  `umbDiscount` int(5) NOT NULL,
  `umbCutoff` int(5) NOT NULL,
  `affiliateRegular` int(5) NOT NULL,
  `affiliateDiscount` int(5) NOT NULL,
  `affiliateCutoff` int(5) NOT NULL,
  `nonProfitRegular` int(5) NOT NULL,
  `nonProfitDiscount` int(5) NOT NULL,
  `nonProfitCutoff` int(5) NOT NULL,
  `forProfitRegular` int(5) NOT NULL,
  `forProfitDiscount` int(5) NOT NULL,
  `forProfitCutoff` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscConferenceAccess
# ------------------------------------------------------------

CREATE TABLE `mscConferenceAccess` (
  `conferenceId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`conferenceId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscConferenceRooms
# ------------------------------------------------------------

CREATE TABLE `mscConferenceRooms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `location` varchar(20) NOT NULL DEFAULT '',
  `description` varchar(500) NOT NULL DEFAULT '',
  `color` varchar(7) NOT NULL,
  `bookable` tinyint(1) NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscInstrumentAccess
# ------------------------------------------------------------

CREATE TABLE `mscInstrumentAccess` (
  `instrumentId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `access` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`instrumentId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscInstruments
# ------------------------------------------------------------

CREATE TABLE `mscInstruments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `model` varchar(100) NOT NULL DEFAULT '',
  `color` varchar(7) NOT NULL DEFAULT '',
  `accuracy` varchar(10) NOT NULL DEFAULT '',
  `minBookableUnit` int(3) NOT NULL DEFAULT '60',
  `assetNumber` int(11) NOT NULL,
  `location` varchar(20) NOT NULL DEFAULT '',
  `bookable` tinyint(1) NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscPrepServices
# ------------------------------------------------------------

CREATE TABLE `mscPrepServices` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `analysisId` int(11) DEFAULT NULL,
  `memberRegular` int(5) NOT NULL,
  `memberDiscount` int(5) NOT NULL,
  `memberCutoff` int(5) NOT NULL,
  `collaboratorRegular` int(5) NOT NULL,
  `collaboratorDiscount` int(5) NOT NULL,
  `collaboratorCutoff` int(5) NOT NULL,
  `umbRegular` int(5) NOT NULL,
  `umbDiscount` int(5) NOT NULL,
  `umbCutoff` int(5) NOT NULL,
  `affiliateRegular` int(5) NOT NULL,
  `affiliateDiscount` int(5) NOT NULL,
  `affiliateCutoff` int(5) NOT NULL,
  `nonProfitRegular` int(5) NOT NULL,
  `nonProfitDiscount` int(5) NOT NULL,
  `nonProfitCutoff` int(5) NOT NULL,
  `forProfitRegular` int(5) NOT NULL,
  `forProfitDiscount` int(5) NOT NULL,
  `forProfitCutoff` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscServiceRates
# ------------------------------------------------------------

CREATE TABLE `mscServiceRates` (
  `accountTypeId` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `regular` int(5) DEFAULT NULL,
  `discount` int(5) DEFAULT NULL,
  `cutoff` int(5) DEFAULT NULL,
  PRIMARY KEY (`accountTypeId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscServiceRequest
# ------------------------------------------------------------

CREATE TABLE `mscServiceRequest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `label` varchar(100) NOT NULL DEFAULT '',
  `concentration` varchar(20) NOT NULL DEFAULT '',
  `state` varchar(10) NOT NULL,
  `composition` varchar(50) NOT NULL DEFAULT '',
  `digestionEnzyme` varchar(50) NOT NULL DEFAULT '',
  `species` varchar(100) DEFAULT NULL,
  `purification` varchar(50) DEFAULT NULL,
  `redoxChemicals` varchar(100) DEFAULT NULL,
  `molecularWeight` varchar(10) DEFAULT NULL,
  `suspectedModifications` varchar(50) DEFAULT NULL,
  `aaModifications` varchar(50) DEFAULT NULL,
  `sequence` varchar(2000) DEFAULT '',
  `comments` varchar(2000) DEFAULT '',
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscServicesSelected
# ------------------------------------------------------------

CREATE TABLE `mscServicesSelected` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `requestId` int(11) NOT NULL,
  `serviceId` int(11) NOT NULL,
  `samples` int(11) NOT NULL,
  `replicates` int(11) DEFAULT '1',
  `prep` tinyint(1) NOT NULL,
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mscTrainingRequest
# ------------------------------------------------------------

CREATE TABLE `mscTrainingRequest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `projectId` int(11) NOT NULL,
  `moduleId` int(11) NOT NULL,
  `bookingId` int(11) DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table paymentInfo
# ------------------------------------------------------------

CREATE TABLE `paymentInfo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `purchaseOrder` varchar(20) DEFAULT '',
  `projectCostingBusinessUnit` varchar(20) DEFAULT '',
  `projectId` varchar(20) DEFAULT '',
  `departmentId` varchar(20) DEFAULT '',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table projects
# ------------------------------------------------------------

CREATE TABLE `projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `paymentId` int(11) NOT NULL,
  `title` varchar(50) NOT NULL DEFAULT '',
  `abstract` varchar(1000) NOT NULL DEFAULT '',
  `primaryInvestigator` varchar(50) NOT NULL,
  `addressOne` varchar(50) NOT NULL DEFAULT '',
  `addressTwo` varchar(50) DEFAULT '',
  `city` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(2) NOT NULL DEFAULT '',
  `zip` varchar(10) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `fax` varchar(20) DEFAULT '',
  `status` varchar(20) DEFAULT 'active',
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sideBarMessage
# ------------------------------------------------------------

CREATE TABLE `sideBarMessage` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `html` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table sopLogo
# ------------------------------------------------------------

CREATE TABLE `sopLogo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jpeg` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table trainingAnswers
# ------------------------------------------------------------

CREATE TABLE `trainingAnswers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL,
  `letter` char(1) DEFAULT NULL,
  `answer` varchar(1000) NOT NULL DEFAULT '',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table trainingBookings
# ------------------------------------------------------------

CREATE TABLE `trainingBookings` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `requestId` int(11) NOT NULL,
  `instrumentId` int(11) DEFAULT NULL,
  `dateFrom` date NOT NULL,
  `dateTo` date NOT NULL,
  `timeFrom` time NOT NULL,
  `timeTo` time NOT NULL,
  `invoiced` tinyint(1) NOT NULL DEFAULT '0',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table trainingDocuments
# ------------------------------------------------------------

CREATE TABLE `trainingDocuments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moduleId` int(11) NOT NULL,
  `documentPath` varchar(500) NOT NULL DEFAULT '',
  `documentName` varchar(200) NOT NULL DEFAULT '',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table trainingModules
# ------------------------------------------------------------

CREATE TABLE `trainingModules` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `contact` varchar(100) NOT NULL DEFAULT '',
  `contactEmail` varchar(150) NOT NULL DEFAULT '',
  `status` int(1) NOT NULL DEFAULT '1',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table trainingQuestions
# ------------------------------------------------------------

CREATE TABLE `trainingQuestions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `moduleId` int(11) NOT NULL,
  `question` varchar(1000) NOT NULL DEFAULT '',
  `correctAnswer` varchar(8) NOT NULL DEFAULT '',
  `createdAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table trainingRecords
# ------------------------------------------------------------

CREATE TABLE `trainingRecords` (
  `moduleId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `quizPassed` tinyint(1) NOT NULL DEFAULT '0',
  `trainingPassed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`moduleId`,`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '',
  `first` varchar(50) NOT NULL DEFAULT '',
  `last` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(64) NOT NULL DEFAULT '',
  `institution` varchar(100) NOT NULL DEFAULT '',
  `accountType` tinyint(1) NOT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `readEULA` tinyint(1) NOT NULL DEFAULT '1',
  `userActive` tinyint(1) NOT NULL DEFAULT '0',
  `passwordHash` char(60) NOT NULL,
  `activationHash` varchar(40) DEFAULT NULL,
  `registrationIp` varchar(15) NOT NULL DEFAULT '0.0.0.0',
  `passwordResetHash` char(40) DEFAULT NULL,
  `rememberMeToken` varchar(64) DEFAULT NULL,
  `passwordResetTimestamp` bigint(20) DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Create the admin account with default password of 'password'
# ------------------------------------------------------------

INSERT INTO users (id, username, first, email, isAdmin, readEULA, userActive, passwordHash) VALUES (1, 'admin', 'Administrator', 'root@localhost', 1, 1, 1, '$2y$10$R0cnFYG/pPE9OWymVVhrwOZ9BzK4JNoR5rW9E/5C93KOMbuzcrNd.');

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
