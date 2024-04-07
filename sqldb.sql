/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 10.4.32-MariaDB : Database - skindisease
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`skindisease` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;

USE `skindisease`;

/*Table structure for table `admin_login` */

DROP TABLE IF EXISTS `admin_login`;

CREATE TABLE `admin_login` (
  `admin_id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `admin_login` */

insert  into `admin_login`(`admin_id`,`username`,`password`) values 
(1,'admin','d033e22ae348aeb5660fc2140aec35850c4da997');

/*Table structure for table `user_predicition` */

DROP TABLE IF EXISTS `user_predicition`;

CREATE TABLE `user_predicition` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `email` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `predicted_disease` varchar(255) NOT NULL,
  `probability` varchar(40) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

/*Data for the table `user_predicition` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
