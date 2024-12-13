/*
SQLyog Community v13.2.1 (64 bit)
MySQL - 8.0.37 : Database - online_bank_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`online_bank_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `online_bank_db`;

/*Table structure for table `accounts` */

DROP TABLE IF EXISTS `accounts`;

CREATE TABLE `accounts` (
  `account_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','locked','closed') DEFAULT 'active',
  PRIMARY KEY (`account_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `accounts` */

insert  into `accounts`(`account_id`,`user_id`,`account_type`,`balance`,`status`) values 
(3,23,'savings',100000.00,'active'),
(4,25,'savings',1231321.00,'active'),
(5,26,'savings',21313121.00,'active'),
(6,27,'savings',100000.00,'active'),
(7,28,'savings',213123.00,'active'),
(8,29,'savings',100000.00,'active'),
(9,30,'loan',1000000.00,'active'),
(11,30,'savings',222222.00,'active'),
(12,23,'loan',10000.00,'active');

/*Table structure for table `announcements` */

DROP TABLE IF EXISTS `announcements`;

CREATE TABLE `announcements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(5000) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `announcements` */

insert  into `announcements`(`id`,`title`,`description`,`is_active`,`created_at`,`updated_at`) values 
(5,'Announcement Test','Ako ay isang model',1,'2024-12-10 13:51:07','2024-12-12 07:39:37'),
(8,'Ang Announcement','Ang announcementsadas sad dsa d asdsadsadsad',1,'2024-12-10 22:18:48','2024-12-10 22:18:48'),
(10,'Ako ay si Stephen','Madami akong chix',1,'2024-12-12 02:53:50','2024-12-12 02:53:50');

/*Table structure for table `loans` */

DROP TABLE IF EXISTS `loans`;

CREATE TABLE `loans` (
  `loan_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `loan_type` enum('personal','home','vehicle','education') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `application_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`loan_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `loans_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `loans` */

/*Table structure for table `offers` */

DROP TABLE IF EXISTS `offers`;

CREATE TABLE `offers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `offers` */

insert  into `offers`(`id`,`title`,`image_url`,`is_active`,`created_at`) values 
(13,'Ako po','uploads/offers/6759a2162071e.jpg',1,'2024-12-11 22:30:46');

/*Table structure for table `points` */

DROP TABLE IF EXISTS `points`;

CREATE TABLE `points` (
  `point_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `points` int DEFAULT '0',
  PRIMARY KEY (`point_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `points` */

/*Table structure for table `reports` */

DROP TABLE IF EXISTS `reports`;

CREATE TABLE `reports` (
  `report_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `report_type` enum('invalid_transaction','account_issue','loan_issue') NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','resolved','dismissed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`report_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `reports` */

/*Table structure for table `transactions` */

DROP TABLE IF EXISTS `transactions`;

CREATE TABLE `transactions` (
  `transaction_id` int NOT NULL AUTO_INCREMENT,
  `account_id` int NOT NULL,
  `transaction_type` enum('deposit','withdrawal','transfer') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_status` enum('pending','completed','failed') DEFAULT 'completed',
  PRIMARY KEY (`transaction_id`),
  KEY `account_id` (`account_id`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `transactions` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `first_name` varchar(45) NOT NULL,
  `last_name` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `pass` varchar(45) NOT NULL,
  `birth_date` varchar(50) DEFAULT NULL,
  `middle_name` varchar(20) DEFAULT NULL,
  `phone_number` char(11) DEFAULT NULL,
  `address` varchar(100) NOT NULL,
  `isAdmin` tinyint(1) DEFAULT '0',
  `img_url` varchar(100) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `gender` varchar(10) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT '0',
  `is_blocked` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`first_name`,`last_name`,`email`,`pass`,`birth_date`,`middle_name`,`phone_number`,`address`,`isAdmin`,`img_url`,`is_verified`,`gender`,`is_archived`,`is_blocked`) values 
(1,'admin','admin','admin@gmail.com','admin123','2024-12-01','admin','123213131','admin',1,'/uploads/profile-pictures/675855f2c5d26-Screenshot 2024-11-23 202409.png',1,'male',0,0),
(23,'Stephen','Bucsit','stephen@gmail.com','manyakis','1999-09-16','Madayag','12312321313','Pangobilian',0,NULL,0,'male',0,0),
(24,'Louie','Cadaguit','louie@gmail.com','akosilouie','2024-12-01','Patagnan','12321321','Balacan',0,NULL,0,'male',1,0),
(25,'Louie','Cadaguit','louie@gmail.com','akosilouie','2024-12-01','Patagnan','12321321','Balacan',0,NULL,0,'male',0,0),
(26,'FAFA','Francisco','james@gmail.com','akosijames','2024-12-02','Salon','2311232131','sadsadsad',0,'/uploads/profile-pictures/67584ed9d3210-Screenshot 2024-07-18 142435.png',0,'female',1,1),
(27,'Junjun','Garcia','jhonnel@gmail.com','akosijhonnel','2024-12-02','Testigo','12931023131','asdadsadsad',0,'/uploads/profile-pictures/67581142be9e4-JUN_7457 (1) (1).JPG',1,'male',0,0),
(28,'James','Mediodia','james@gmail.com','akosiedwin','2024-12-03','Edwin','21321312','asdsadsadsa',0,NULL,0,'male',0,0),
(29,'James','Francisco','james@gmail.com','akosijames','2024-12-01','Salon','0909090909','sadsadsad',0,NULL,0,'male',1,0),
(30,'Kim','Pascual','kim@gmail.com','akosikim','2024-11-05','Elaiza','092323232','Bulacan',0,'/uploads/profile-pictures/6758566cbfe04-Screenshot 2024-06-26 155525.png',0,'male',0,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
