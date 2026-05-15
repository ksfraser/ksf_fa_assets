<?php
/**
 * FA_Assets Module Hooks for FrontAccounting
 */

define('SS_ASSETS', 115 << 8);

class hooks_fa_assets extends hooks {

    private function ensure_composer_dependencies(): void {
        $module_dir = dirname(__FILE__);
        $autoload_path = $module_dir . '/vendor/autoload.php';
        
        if (!file_exists($autoload_path)) {
            $composer_path = $module_dir . '/composer.json';
            if (file_exists($composer_path)) {
                chdir($module_dir);
                $output = [];
                $return_code = 0;
                exec('composer install --no-interaction --prefer-dist 2>&1', $output, $return_code);
                if ($return_code !== 0) {
                    error_log('KSF Module: composer install failed: ' . implode("\n", $output));
                }
            }
        }
    }
    var $module_name = 'fa_assets';

    private function ensure_composer_dependencies(): void {
        $module_dir = dirname(__FILE__);
        $autoload_path = $module_dir . '/vendor/autoload.php';
        
        if (!file_exists($autoload_path)) {
            $composer_path = $module_dir . '/composer.json';
            if (file_exists($composer_path)) {
                chdir($module_dir);
                $output = [];
                $return_code = 0;
                exec('composer install --no-interaction --prefer-dist 2>&1', $output, $return_code);
                if ($return_code !== 0) {
                    error_log('KSF Module: composer install failed: ' . implode("\n", $output));
                }
            }
        }
    }
    var $version = '1.0.0';

    private function ensure_composer_dependencies(): void {
        $module_dir = dirname(__FILE__);
        $autoload_path = $module_dir . '/vendor/autoload.php';
        
        if (!file_exists($autoload_path)) {
            $composer_path = $module_dir . '/composer.json';
            if (file_exists($composer_path)) {
                chdir($module_dir);
                $output = [];
                $return_code = 0;
                exec('composer install --no-interaction --prefer-dist 2>&1', $output, $return_code);
                if ($return_code !== 0) {
                    error_log('KSF Module: composer install failed: ' . implode("\n", $output));
                }
            }
        }
    }

    function install_options($app) {
        global $path_to_root;

        switch($app->id) {
            case 'Assets':
                $app->add_lapp_function(0, _("Asset Categories"),
                    $path_to_root."/modules/".$this->module_name."/asset_categories.php", 'SA_ASSETSVIEW', MENU_ENTRY);
                $app->add_lapp_function(1, _("Assets"),
                    $path_to_root."/modules/".$this->module_name."/assets.php", 'SA_ASSETSCREATE', MENU_ENTRY);
                $app->add_lapp_function(2, _("Depreciation"),
                    $path_to_root."/modules/".$this->module_name."/depreciation.php", 'SA_ASSETSMAINTENANCE', MENU_ENTRY);
                $app->add_rapp_function(3, _("Asset Reports"),
                    $path_to_root."/modules/".$this->module_name."/reports.php", 'SA_ASSETSVIEW', MENU_REPORT);
                break;
        }
    }

    function install_access() {
        $security_sections[SS_ASSETS] = _("Assets Management");
        $security_areas['SA_ASSETSVIEW'] = array(SS_ASSETS | 1, _("View Assets"));
        $security_areas['SA_ASSETSCREATE'] = array(SS_ASSETS | 2, _("Create Assets"));
        $security_areas['SA_ASSETSEDIT'] = array(SS_ASSETS | 3, _("Edit Assets"));
        $security_areas['SA_ASSETSMAINTENANCE'] = array(SS_ASSETS | 4, _("Manage Maintenance"));
        return array($security_areas, $security_sections);
    }

    function activate_extension($company, $check_only=true) {
        $updates = array('sql/update.sql' => array($this->module_name));
        $ok = $this->update_databases($company, $updates, $check_only);
        if ($check_only || !$ok) {
            return $ok;
        }
        $this->ensure_assets_schema();
        return $ok;
    }

    private function table_exists($table) {
        $sql = "SHOW TABLES LIKE " . db_escape($table);
        $res = db_query($sql, 'Failed checking table existence');
        return db_num_rows($res) > 0;
    }

    private function ensure_assets_schema() {
        $tables = array(
            TB_PREF . "fa_asset_categories" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asset_categories` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `name` VARCHAR(100) NOT NULL,
                    `description` TEXT,
                    `depreciation_method` VARCHAR(20) DEFAULT 'Straight-Line',
                    `useful_life_years` INT(11) DEFAULT 5,
                    `salvage_value` DECIMAL(15,2) DEFAULT 0,
                    `inactive` TINYINT(1) DEFAULT 0,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_name` (`name`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_assets" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_assets` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `asset_tag` VARCHAR(50) NOT NULL,
                    `name` VARCHAR(100) NOT NULL,
                    `category_id` INT(11) NOT NULL,
                    `serial_number` VARCHAR(100) DEFAULT NULL,
                    `purchase_date` DATE DEFAULT NULL,
                    `purchase_cost` DECIMAL(15,2) DEFAULT 0,
                    `current_value` DECIMAL(15,2) DEFAULT 0,
                    `location` VARCHAR(100) DEFAULT NULL,
                    `assigned_to` VARCHAR(100) DEFAULT NULL,
                    `status` VARCHAR(20) DEFAULT 'Active',
                    `debtor_no` VARCHAR(20) DEFAULT NULL,
                    `inactive` TINYINT(1) DEFAULT 0,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_asset_tag` (`asset_tag`),
                    KEY `idx_category` (`category_id`),
                    KEY `idx_status` (`status`),
                    KEY `idx_debtor` (`debtor_no`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_asset_depreciation" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asset_depreciation` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `asset_id` INT(11) NOT NULL,
                    `depreciation_date` DATE NOT NULL,
                    `amount` DECIMAL(15,2) NOT NULL,
                    `accumulated_depreciation` DECIMAL(15,2) DEFAULT 0,
                    `book_value` DECIMAL(15,2) DEFAULT 0,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_asset` (`asset_id`),
                    KEY `idx_date` (`depreciation_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_asset_maintenance" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asset_maintenance` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `asset_id` INT(11) NOT NULL,
                    `maintenance_date` DATE NOT NULL,
                    `maintenance_type` VARCHAR(20) DEFAULT 'Routine',
                    `description` TEXT,
                    `cost` DECIMAL(15,2) DEFAULT 0,
                    `performed_by` VARCHAR(100) DEFAULT NULL,
                    `next_maintenance_date` DATE DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_asset` (`asset_id`),
                    KEY `idx_date` (`maintenance_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_asset_transfers" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asset_transfers` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `asset_id` INT(11) NOT NULL,
                    `from_location` VARCHAR(100) DEFAULT NULL,
                    `to_location` VARCHAR(100) DEFAULT NULL,
                    `from_employee` VARCHAR(100) DEFAULT NULL,
                    `to_employee` VARCHAR(100) DEFAULT NULL,
                    `transfer_date` DATE NOT NULL,
                    `reason` TEXT,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_asset` (`asset_id`),
                    KEY `idx_date` (`transfer_date`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($tables as $table_name => $sql) {
            db_query($sql, "Could not create Assets table: $table_name");
        }
    }

    function db_prevoid($trans_type, $trans_no) {
        // Handle voiding if assets module tracks financial transactions
    }
}
?>
