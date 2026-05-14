# FA_Assets - Architecture

**Document ID:** ARCH-FAASSETS-001  
**Module:** ksf_FA_Assets  
**Version:** 1.0.0  

---

## 1. Module Overview

FA_Assets follows FrontAccounting page-based architecture with database access through FA's db.inc functions and includes for shared logic.

## 2. Component Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Pages (UI Layer)                         │
├─────────────────────────────────────────────────────────────┤
│ - assets.php                                                 │
│   ├─ All Assets View                                         │
│   ├─ Upcoming Maintenance View                               │
│   └─ Serial Number Lookup View                               │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ includes
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   Includes (Logic Layer)                    │
├─────────────────────────────────────────────────────────────┤
│ - assets_db.inc                                              │
│   ├─ get_assets()                                           │
│   ├─ get_asset_by_serial(sn)                                 │
│   ├─ get_upcoming_maintenance(days)                          │
│   └─ get_maintenance_history(assetId)                        │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ uses
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                FrontAccounting Core                          │
├─────────────────────────────────────────────────────────────┤
│ - db.inc (Database functions)                               │
│ - ui.inc (UI helpers)                                        │
│ - session.inc (Authentication)                              │
└─────────────────────────────────────────────────────────────┘
```

## 3. Directory Structure

```
ksf_FA_Assets/
├── pages/
│   └── assets.php
├── includes/
│   └── assets_db.inc
├── hooks.php
├── tests/
│   └── Unit/
│       └── AssetsDBTest.php
└── doc/ProjectDcs/
```

## 4. Database Schema

```sql
CREATE TABLE fa_assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    serial_number VARCHAR(100),
    category_id INT,
    status VARCHAR(50) DEFAULT 'active',
    current_value DECIMAL(15,2),
    purchase_date DATE,
    purchase_cost DECIMAL(15,2),
    location VARCHAR(255)
);

CREATE TABLE fa_asset_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    depreciation_method VARCHAR(50)
);

CREATE TABLE fa_asset_maintenance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id INT,
    maintenance_type VARCHAR(100),
    description TEXT,
    cost DECIMAL(10,2),
    performed_at DATETIME,
    performed_by VARCHAR(100)
);
```

## 5. Technology Stack

| Component | Technology |
|-----------|------------|
| Language | PHP |
| Database | MySQL/MariaDB (via FA db.inc) |
| UI | FrontAccounting UI helpers |
| Testing | PHPUnit |