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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/* Table structure for table accounts */
DROP TABLE IF EXISTS accounts;

CREATE TABLE `accounts` (
  `account_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `account_type` varchar(50) DEFAULT NULL,
  `balance` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','locked','closed') DEFAULT 'active',
  PRIMARY KEY (`account_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS loans;

CREATE TABLE loans (
  loan_id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  loan_type enum('personal','home','vehicle','education') NOT NULL,
  amount decimal(10,2) NOT NULL,
  status enum('pending','approved','rejected') DEFAULT 'pending',
  application_date timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (loan_id),
  KEY user_id (user_id),
  CONSTRAINT loans_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table loans */

/*Table structure for table offers */

DROP TABLE IF EXISTS offers;


CREATE TABLE `offers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table offers */

/*Table structure for table points */

DROP TABLE IF EXISTS points;

CREATE TABLE points (
  point_id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  points int DEFAULT '0',
  PRIMARY KEY (point_id),
  KEY user_id (user_id),
  CONSTRAINT points_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table points */

/*Table structure for table reports */

DROP TABLE IF EXISTS reports;

CREATE TABLE reports (
  report_id int NOT NULL AUTO_INCREMENT,
  user_id int NOT NULL,
  report_type enum('invalid_transaction','account_issue','loan_issue') NOT NULL,
  description text NOT NULL,
  status enum('pending','resolved','dismissed') DEFAULT 'pending',
  created_at timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (report_id),
  KEY user_id (user_id),
  CONSTRAINT reports_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table reports */

/*Table structure for table transactions */

DROP TABLE IF EXISTS transactions;

CREATE TABLE transactions (
  transaction_id int NOT NULL AUTO_INCREMENT,
  account_id int NOT NULL,
  transaction_type enum('deposit','withdrawal','transfer') NOT NULL,
  amount decimal(10,2) NOT NULL,
  transaction_date timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  transaction_status enum('pending','completed','failed') DEFAULT 'completed',
  PRIMARY KEY (transaction_id),
  KEY account_id (account_id),
  CONSTRAINT transactions_ibfk_1 FOREIGN KEY (account_id) REFERENCES accounts (account_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;