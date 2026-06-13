-- --------------------------------------------------------
-- Table structure for table `school_info`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `school_info` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `school_name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `logo` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure for table `academic_years`
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `academic_years` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `year_name` VARCHAR(100) NOT NULL,
  `start_date` DATE DEFAULT NULL,
  `end_date` DATE DEFAULT NULL,
  `status` TINYINT(1) DEFAULT 0, -- 0 for inactive, 1 for active
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
