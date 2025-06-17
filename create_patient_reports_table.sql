-- Table structure for table `patient_reports`
CREATE TABLE IF NOT EXISTS `patient_reports` (
    `report_id` int(11) NOT NULL AUTO_INCREMENT,
    `patient_id` int(11) NOT NULL,
    `doctor_id` int(11) NOT NULL,
    `appointment_id` int(11) DEFAULT NULL,
    `disease` varchar(255) NOT NULL,
    `symptoms` text NOT NULL,
    `diagnosis` text NOT NULL,
    `treatment` text NOT NULL,
    `prescription` text NOT NULL,
    `report_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`report_id`),
    KEY `patient_id` (`patient_id`),
    KEY `doctor_id` (`doctor_id`),
    KEY `appointment_id` (`appointment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4; 