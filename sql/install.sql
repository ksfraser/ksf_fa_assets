-- Assets module database schema for FrontAccounting

-- Asset categories
CREATE TABLE IF NOT EXISTS `fa_asset_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `depreciation_type` ENUM('none','straight_line','declining','sum_of_years') NOT NULL DEFAULT 'none',
    `useful_life_years` INT(3) DEFAULT NULL,
    `salvage_value` DECIMAL(12,2) DEFAULT 0,
    `parent_category_id` INT(11) DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Assets inventory
CREATE TABLE IF NOT EXISTS `fa_assets` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `serial_number` VARCHAR(100) DEFAULT NULL,
    `category_id` INT(11) NOT NULL,
    `purchase_date` DATE DEFAULT NULL,
    `purchase_cost` DECIMAL(12,2) DEFAULT NULL,
    `current_value` DECIMAL(12,2) DEFAULT 0,
    `location` VARCHAR(100) DEFAULT NULL,
    `assigned_to` INT(11) DEFAULT NULL,
    `assigned_type` ENUM('employee','location','customer') DEFAULT NULL,
    `status` ENUM('active','in_storage','in_repair','disposed','retired') NOT NULL DEFAULT 'active',
    `notes` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `serial_number` (`serial_number`),
    KEY `category_id` (`category_id`),
    KEY `assigned_to` (`assigned_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asset depreciation history
CREATE TABLE IF NOT EXISTS `fa_asset_depreciation` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `asset_id` INT(11) NOT NULL,
    `year` YEAR NOT NULL,
    `opening_value` DECIMAL(12,2) NOT NULL,
    `depreciation` DECIMAL(12,2) NOT NULL,
    `closing_value` DECIMAL(12,2) NOT NULL,
    `recorded_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `asset_year` (`asset_id`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asset maintenance history
CREATE TABLE IF NOT EXISTS `fa_asset_maintenance` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `asset_id` INT(11) NOT NULL,
    `maintenance_type` ENUM('preventive','repair','inspection','upgrade') NOT NULL DEFAULT 'preventive',
    `description` TEXT,
    `cost` DECIMAL(10,2) DEFAULT NULL,
    `performed_by` INT(11) DEFAULT NULL,
    `performed_at` DATETIME DEFAULT NULL,
    `next_due_date` DATE DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `asset_id` (`asset_id`),
    KEY `next_due_date` (`next_due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Asset transfers
CREATE TABLE IF NOT EXISTS `fa_asset_transfers` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `asset_id` INT(11) NOT NULL,
    `from_type` ENUM('employee','location','customer') DEFAULT NULL,
    `from_id` INT(11) DEFAULT NULL,
    `to_type` ENUM('employee','location','customer') NOT NULL,
    `to_id` INT(11) NOT NULL,
    `transferred_by` INT(11) NOT NULL,
    `transferred_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `asset_id` (`asset_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Module version
INSERT INTO `fa_modules` (`name`, `version`, `enabled`, `installed`) VALUES ('Assets', '1.0.0', 1, NOW()) ON DUPLICATE KEY UPDATE `version` = '1.0.0';