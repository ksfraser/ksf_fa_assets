# Architecture - Fixed Assets Management (ksf_FA_Assets)

**Module:** ksf_FA_Assets  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Architecture Overview

The Fixed Assets module follows FrontAccounting's extension pattern using the hooks system. The module integrates into FA's menu structure, security system, and database while maintaining its own tables for asset-specific data.

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     FrontAccounting Core                                     │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   Session   │  │   Security  │  │    Menu     │  │  Database   │        │
│  │   Manager   │  │    System   │  │   System    │  │   Layer     │        │
│  └─────────────┘  └─────────────┘  └─────────────┘  └──────┬──────┘        │
└──────────────────────────────────────────────────────────────┼──────────────┘
                                                                     │
                                          ┌─────────────────────────┼───────────────┐
                                          │                   hooks_fa_assets     │
                                          │                          │             │
                                          │  ┌──────────────────────────────────────┐│
                                          │  │         Module Integration           ││
                                          │  │  - install_options()                 ││
                                          │  │  - install_access()                   ││
                                          │  │  - activate_extension()               ││
                                          │  └──────────────────────────────────────┘│
                                          │                          │             │
                                          │     ┌─────────────────────┴─────────────┐│
                                          │     │                                    ││
                                          │     ▼                                    ▼│
                                          │  ┌─────────────┐              ┌──────────────┐
                                          │  │   Pages     │              │   Includes   │
                                          │  │  assets.php │              │ assets_db.inc│
                                          │  └─────────────┘              └──────────────┘
                                          │         │                          │
                                          │         │                          │
                                          │         └──────────────────────────┘
                                          │                       │
                                          │                       ▼
                                          │  ┌────────────────────────────────────────┐
                                          │  │          FA Assets Tables             │
                                          │  │  fa_asset_categories                   │
                                          │  │  fa_assets                             │
                                          │  │  fa_asset_depreciation                 │
                                          │  │  fa_asset_maintenance                  │
                                          │  │  fa_asset_transfers                    │
                                          │  └────────────────────────────────────────┘
                                          └─────────────────────────────────────────────────┘
```

---

## 2. Module Structure

### 2.1 File Structure

```
ksf_FA_Assets/
├── hooks.php              # FA extension hooks
├── includes/
│   └── assets_db.inc       # Database functions
├── pages/
│   └── assets.php          # Asset management page
├── sql/
│   └── update.sql          # Database schema
├── src/
│   └── Ksfraser/           # (Future) Business logic layer
├── tests/
│   └── Unit/               # Unit tests
├── ProjectDcs/             # This documentation
├── composer.json
└── phpunit.xml
```

### 2.2 Hooks Integration

```php
class hooks_fa_assets extends hooks {
    var $module_name = 'fa_assets';
    
    function install_options($app) {
        // Add module to Assets app menu
    }
    
    function install_access() {
        // Define security areas
    }
    
    function activate_extension($company, $check_only=true) {
        // Create database tables
    }
}
```

---

## 3. Database Schema

### 3.1 Entity Relationship Diagram

```
┌─────────────────────────┐         ┌─────────────────────────┐
│   fa_asset_categories   │         │       fa_assets          │
├─────────────────────────┤         ├─────────────────────────┤
│ id (PK)                 │1       *│ id (PK)                 │
│ name                    │─────────│ category_id (FK)        │
│ description             │         │ asset_tag (UNIQUE)      │
│ depreciation_method    │         │ name                    │
│ useful_life_years       │         │ serial_number           │
│ salvage_value           │         │ purchase_date           │
│ inactive                │         │ purchase_cost           │
└─────────────────────────┘         │ current_value           │
                                    │ location                │
                                    │ assigned_to             │
                                    │ status                  │
                                    │ debtor_no               │
                                    └────────────┬────────────┘
                                                 │
                   ┌─────────────────────────────┼─────────────────────────────┐
                   │                             │                             │
                   ▼                             ▼                             ▼
┌─────────────────────────┐   ┌─────────────────────────┐   ┌─────────────────────────┐
│  fa_asset_depreciation  │   │   fa_asset_maintenance  │   │   fa_asset_transfers     │
├─────────────────────────┤   ├─────────────────────────┤   ├─────────────────────────┤
│ id (PK)                 │   │ id (PK)                 │   │ id (PK)                 │
│ asset_id (FK)          │   │ asset_id (FK)          │   │ asset_id (FK)          │
│ depreciation_date      │   │ maintenance_date       │   │ from_location           │
│ amount                  │   │ maintenance_type       │   │ to_location             │
│ accumulated_depreciation│   │ description            │   │ from_employee           │
│ book_value              │   │ cost                    │   │ to_employee             │
│ created_at              │   │ performed_by           │   │ transfer_date           │
└─────────────────────────┘   │ next_maintenance_date   │   │ reason                  │
                              │ created_at              │   │ created_at              │
                              └─────────────────────────┘   └─────────────────────────┘
```

### 3.2 Table Definitions

#### fa_asset_categories

```sql
CREATE TABLE IF NOT EXISTS `fa_asset_categories` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT,
    `depreciation_method` VARCHAR(20) DEFAULT 'Straight-Line',
    `useful_life_years` INT(11) DEFAULT 5,
    `salvage_value` DECIMAL(15,2) DEFAULT 0,
    `inactive` TINYINT(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_assets

```sql
CREATE TABLE IF NOT EXISTS `fa_assets` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 4. Page Architecture

### 4.1 Assets Page Flow

```
User Request (assets.php)
         │
         ▼
┌─────────────────────────────────────────────┐
│         Determine Section                   │
│  - $_GET['section']                         │
│  - 'list' | 'maintenance' | 'serial'        │
└─────────────────────────────────────────────┘
         │
    ┌────┴────┐
    │         │
    ▼         ▼
┌────────┐ ┌─────────────┐
│  List  │ │  Lookup by  │
│ Assets │ │   Serial    │
└───┬────┘ └──────┬──────┘
    │             │
    │             ▼
    │    ┌─────────────────┐
    │    │  Search Form    │
    │    │  (Serial Input) │
    │    └─────────────────┘
    │             │
    │             ▼
    │    ┌─────────────────┐
    │    │ get_asset_by_   │
    │    │   serial()      │
    │    └─────────────────┘
    │             │
    └─────────────┘
         │
         ▼
┌─────────────────────────────────────────────┐
│         Render Asset Data                   │
│  - Asset details table                       │
│  - Maintenance history (if serial search)    │
│  - Upcoming maintenance (if section=main)  │
└─────────────────────────────────────────────┘
```

### 4.2 Page Sections

| Section | Purpose | Functions Used |
|---------|---------|----------------|
| list | Main asset list | get_assets() |
| maintenance | Upcoming maintenance | get_upcoming_maintenance() |
| serial | Search by serial number | get_asset_by_serial() |

---

## 5. Data Access Layer

### 5.1 Database Functions (assets_db.inc)

```php
// Asset CRUD
function create_asset(string $name, int $category_id, string $serial_number = '', 
                      float $cost = null, string $purchase_date = ''): int

function get_assets(array $filters = []): ?object

function get_asset(int $id): ?array

function get_asset_by_serial(string $serial): ?array

// Assignment
function assign_asset(int $asset_id, string $assign_type, int $assign_id): bool

function transfer_asset(int $asset_id, string $to_type, int $to_id, int $by_user): int

// Maintenance
function add_maintenance(int $asset_id, string $type, string $desc, 
                         float $cost = null, int $by = null, string $next_due = ''): int

function get_maintenance_history(int $asset_id): ?object

function get_upcoming_maintenance(int $days = 30): ?object

// Depreciation
function calculate_depreciation(int $asset_id, int $year): float
```

### 5.2 Function Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                        Page Layer                                │
│                     (assets.php)                                 │
└────────────────────────────┬────────────────────────────────────┘
                             │ includes
                             ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Data Access Layer                             │
│                     (assets_db.inc)                              │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐      │
│  │   create_    │    │    get_      │    │    add_      │      │
│  │    asset()   │    │   assets()   │    │ maintenance()│      │
│  └──────┬───────┘    └──────┬───────┘    └──────┬───────┘      │
│         │                   │                   │              │
│         └───────────────────┴───────────────────┘              │
│                         │                                       │
│                         ▼                                       │
│              ┌─────────────────────┐                           │
│              │     Database        │                           │
│              │   (fa_assets, etc.)  │                           │
│              └─────────────────────┘                           │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. Depreciation Calculation Engine

### 6.1 Depreciation Flow

```
┌─────────────────────────────────────────────────────────────────┐
│              calculate_depreciation(asset_id, year)             │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  1. Get Asset + Category Data                                    │
│     ┌─────────────────────────────────────────────────────────┐ │
│     │ SELECT a.*, c.depreciation_type, c.useful_life,         │ │
│     │        c.salvage_value                                   │ │
│     │ FROM fa_assets a                                         │ │
│     │ JOIN fa_asset_categories c ON a.category_id = c.id        │ │
│     │ WHERE a.id = asset_id                                     │ │
│     └─────────────────────────────────────────────────────────┘ │
│                              │                                   │
│                              ▼                                   │
│  2. Check Depreciation Type                                     │
│     ┌────────────────────────────────────────────────────────┐  │
│     │ if type == 'none' → return 0                           │  │
│     │ if type == 'straight_line' → straight-line calc       │  │
│     │ if type == 'declining' → declining balance calc       │  │
│     └────────────────────────────────────────────────────────┘ │
│                              │                                   │
│                              ▼                                   │
│  3. Calculate Depreciation                                      │
│     ┌────────────────────────────────────────────────────────┐  │
│     │ Straight-Line:                                          │  │
│     │   (current_value - salvage) / useful_life              │  │
│     │                                                          │  │
│     │ Declining Balance (20%):                                │  │
│     │   current_value * 0.20                                  │  │
│     └────────────────────────────────────────────────────────┘ │
│                              │                                   │
│                              ▼                                   │
│  4. Return Annual Depreciation Amount                           │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 6.2 Depreciation Formulas

| Method | Formula | Example |
|--------|---------|---------|
| Straight-Line | (Cost - Salvage) / Useful Life | ($10000 - $500) / 5 = $1900/year |
| Declining Balance | Book Value × Rate | $10000 × 20% = $2000 (year 1) |
| Declining Balance (Double) | 2 × (1/Useful Life) × Book Value | 2 × 20% × $10000 = $4000 |

---

## 7. Security Architecture

### 7.1 Security Areas Definition

```php
define('SS_ASSETS', 115 << 8);  // Section 115

$security_areas['SA_ASSETSVIEW'] = array(SS_ASSETS | 1, _("View Assets"));
$security_areas['SA_ASSETSCREATE'] = array(SS_ASSETS | 2, _("Create Assets"));
$security_areas['SA_ASSETSEDIT'] = array(SS_ASSETS | 3, _("Edit Assets"));
$security_areas['SA_ASSETSMAINTENANCE'] = array(SS_ASSETS | 4, _("Manage Maintenance"));
```

### 7.2 Menu Integration

```php
$app->add_lapp_function(0, _("Asset Categories"), 
    ".../asset_categories.php", 'SA_ASSETSVIEW', MENU_ENTRY);
$app->add_lapp_function(1, _("Assets"), 
    ".../assets.php", 'SA_ASSETSCREATE', MENU_ENTRY);
$app->add_lapp_function(2, _("Depreciation"), 
    ".../depreciation.php", 'SA_ASSETSMAINTENANCE', MENU_ENTRY);
$app->add_rapp_function(3, _("Asset Reports"), 
    ".../reports.php", 'SA_ASSETSVIEW', MENU_REPORT);
```

---

## 8. Extension Activation Flow

```
Module Activation (hooks_fa_assets::activate_extension)
              │
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Check Only Mode                             │
│  if ($check_only) {                                              │
│      // Verify tables would be created without error            │
│      return true/false                                           │
│  }                                                               │
└─────────────────────────────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    Update Database                                │
│  $this->update_databases($company, $updates, $check_only);      │
│  // Runs sql/update.sql                                          │
└─────────────────────────────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                Ensure Schema (ensure_assets_schema)               │
│                                                                  │
│  foreach ($tables as $table_name => $sql) {                      │
│      db_query($sql, "Create table failed");                      │
│  }                                                               │
│                                                                  │
│  Tables: fa_asset_categories, fa_assets,                        │
│          fa_asset_depreciation, fa_asset_maintenance,           │
│          fa_asset_transfers                                       │
└─────────────────────────────────────────────────────────────────┘
              │
              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      Return Success                               │
└─────────────────────────────────────────────────────────────────┘
```

---

## 9. Future Architecture (KSFII)

### 9.1 Business Logic Extraction

```
Current (v1.0):                    Future (v2.0):
┌─────────────────────┐           ┌─────────────────────────────┐
│  assets_db.inc      │           │  Ksfraser\FA\Assets\         │
│  (Procedural)       │   ───►    │    Domain\Asset.php          │
│                     │           │    Service\AssetService.php  │
│                     │           │    Repository\AssetRepo.php  │
└─────────────────────┘           └─────────────────────────────┘
```

### 9.2 Trait Composition Pattern (Future)

```php
namespace Ksfraser\FA\Assets\Entity;

use Ksfraser\Traits\TimestampTrait;
use Ksfraser\Traits\EntityStateTrait;

class Asset {
    use TimestampTrait;
    use EntityStateTrait;
    
    private ?string $assetTag = null;
    
    public function setAssetTag(string $tag): self {
        $this->assetTag = $tag;
        $this->markModified();
        return $this;
    }
}
```

---

## 10. Package Structure

```
ksf_FA_Assets/
├── AGENTS.md
├── hooks.php                      # FA hooks integration
├── includes/
│   └── assets_db.inc              # Database functions
├── pages/
│   └── assets.php                 # Asset management page
├── sql/
│   └── update.sql                 # Database schema
├── src/
│   └── Ksfraser/                  # (Future) Business logic
├── tests/
│   └── Unit/
│       └── AssetFunctionsTest.php
├── composer.json
├── phpunit.xml
└── ProjectDcs/
    ├── 01_Business_Requirements.md
    ├── 02_Architecture.md
    ├── 03_Functional_Requirements.md
    ├── 04_Use_Case.md
    ├── 05_Test_Plan.md
    └── 06_UAT_Plan.md
```

---

*Document Version: 1.0*  
*Last Updated: May 2026*