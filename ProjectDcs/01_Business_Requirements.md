# Business Requirements - Fixed Assets Management (ksf_FA_Assets)

**Module:** ksf_FA_Assets  
**Version:** 1.0.0  
**Date:** May 2026  
**Author:** Ksfraser Development Team  

---

## 1. Executive Summary

The Fixed Assets Management module (ksf_FA_Assets) integrates comprehensive fixed asset tracking capabilities into FrontAccounting. This module enables organizations to maintain complete asset records including categories, depreciation schedules, maintenance tracking, and transfer history—all within the familiar FA interface.

The module follows FrontAccounting's extension pattern, integrating seamlessly with the core FA system while providing dedicated asset management functionality previously absent from the base installation.

---

## 2. Problem Statement

### 2.1 Asset Management Challenges

Organizations using FrontAccounting face significant gaps in asset management:

1. **No Built-in Asset Tracking**: FA lacks comprehensive fixed asset management, forcing manual spreadsheets or external systems.

2. **Depreciation Calculation Complexity**: Manual depreciation calculations are error-prone and time-consuming.

3. **Maintenance Tracking Gaps**: No systematic way to track scheduled maintenance, leading to equipment failures.

4. **Transfer Documentation**: Asset location changes are not systematically recorded.

5. **Depreciation Method Variety**: Different asset types require different depreciation methods (straight-line, declining balance).

### 2.2 Business Impact

- Inefficient asset tracking requiring manual processes
- Risk of missed maintenance schedules
- Inaccurate financial reporting for depreciation
- Lack of audit trail for asset transfers
- Difficulty locating assets during inventory audits

---

## 3. Project Scope

### 3.1 In Scope

| Component | Description |
|-----------|-------------|
| Asset Categories | Define asset types with depreciation settings |
| Asset Records | Full asset information including location, assignment |
| Depreciation Calculation | Multiple depreciation methods |
| Maintenance Tracking | Scheduled and completed maintenance records |
| Asset Transfers | Location and assignment change history |
| Reports | Asset listing, depreciation schedules, maintenance due |

### 3.2 Out of Scope

- Asset acquisition workflow (purchase orders)
- Asset disposal workflow (sales, write-offs)
- Insurance tracking
- Asset barcode/QR generation
- Depreciation journal entry generation (future)

---

## 4. Features and Capabilities

### 4.1 Asset Category Management

**Purpose:** Define asset types with associated depreciation parameters

**Fields:**
- Category Name
- Description
- Depreciation Method (Straight-Line, Declining Balance, None)
- Useful Life (years)
- Salvage Value percentage

**Business Rules:**
- Useful life defaults to 5 years
- Salvage value defaults to 0
- Categories can be marked inactive

### 4.2 Asset Record Management

**Purpose:** Track individual fixed assets throughout their lifecycle

**Fields:**
- Asset Tag (unique identifier)
- Name/Description
- Category (foreign key)
- Serial Number
- Purchase Date
- Purchase Cost
- Current Value
- Location
- Assigned To (employee/customer)
- Status (Active, Under Maintenance, Retired, Disposed)
- Debtor Number (link to customer if applicable)

**Business Rules:**
- Asset Tag must be unique
- Current Value updated by depreciation entries
- Status changes logged

### 4.3 Depreciation Calculation

**Purpose:** Calculate and record asset depreciation

**Depreciation Methods:**

| Method | Formula | Use Case |
|--------|---------|----------|
| Straight-Line | (Cost - Salvage) / Useful Life | General purpose |
| Declining Balance | Book Value * Rate (e.g., 20%) | Accelerated |
| None | No depreciation | Land, assets fully expensed |

**Calculation Rules:**
- Annual depreciation calculated per asset
- Accumulated depreciation tracked
- Book value = Cost - Accumulated Depreciation
- When book value reaches salvage value, depreciation stops

### 4.4 Maintenance Tracking

**Purpose:** Schedule and record asset maintenance

**Maintenance Record Fields:**
- Maintenance Date
- Maintenance Type (Routine, Repair, Inspection, Replacement)
- Description
- Cost
- Performed By
- Next Maintenance Date

**Business Rules:**
- Next maintenance date can be set for scheduling
- Maintenance history preserved
- Cost tracking for budget analysis

### 4.5 Asset Transfers

**Purpose:** Document location and assignment changes

**Transfer Record Fields:**
- Asset ID
- From Location
- To Location
- From Employee
- To Employee
- Transfer Date
- Reason

**Business Rules:**
- Every transfer creates audit record
- Current location/assignment updated on asset

---

## 5. Use Cases

### 5.1 Asset Registration

**Scenario:** New equipment purchased and needs tracking

```
Steps:
1. Admin creates Asset Category (if not exists)
2. Admin creates Asset record with tag, cost, category
3. Asset appears in asset list
4. Depreciation begins calculation
```

### 5.2 Depreciation Schedule Review

**Scenario:** Accountant needs depreciation amounts for financial statements

```
Steps:
1. Navigate to Depreciation report
2. Select date range
3. View calculated depreciation per asset
4. Export for journal entry
```

### 5.3 Maintenance Scheduling

**Scenario:** Fleet manager schedules vehicle maintenance

```
Steps:
1. View upcoming maintenance report
2. Identify assets due for service
3. Record completed maintenance
4. Set next maintenance date
```

### 5.4 Asset Transfer

**Scenario:** Equipment transferred between departments

```
Steps:
1. Locate asset in system
2. Initiate transfer
3. Record from/to locations and employees
4. Asset location updated
5. Transfer history preserved
```

---

## 6. Integration Dependencies

### 6.1 FrontAccounting Core

| Component | Integration Point |
|-----------|-------------------|
| Database | FA database with module tables |
| Authentication | FA session and security |
| UI Framework | FA page and form functions |
| Menu System | Hooks for module menu |
| Security Areas | SA_ASSETSVIEW, SA_ASSETSCREATE, etc. |

### 6.2 Database Schema

**Tables Created:**
- fa_asset_categories
- fa_assets
- fa_asset_depreciation
- fa_asset_maintenance
- fa_asset_transfers

### 6.3 Security Integration

| Security Area | Permission |
|---------------|------------|
| SS_ASSETS | Asset Management section |
| SA_ASSETSVIEW | View assets |
| SA_ASSETSCREATE | Create assets |
| SA_ASSETSEDIT | Edit assets |
| SA_ASSETSMAINTENANCE | Manage maintenance |

---

## 7. Technical Constraints

### 7.1 PHP Version Requirements

- **Minimum:** PHP 7.3
- **Recommended:** PHP 8.0+
- **Target:** PHP 8.2

### 7.2 FA Version Compatibility

- **Minimum:** FA 2.4.x
- **Target:** FA 2.4.x and above

### 7.3 Database Requirements

- MySQL 5.7+ or MariaDB 10.3+
- InnoDB engine for foreign key support
- utf8mb4 character set

---

## 8. Success Criteria

| Criterion | Measurement |
|-----------|-------------|
| Module activates without errors | Clean activation |
| CRUD operations on assets work | All operations functional |
| Depreciation calculates correctly | Matches manual calculation |
| Maintenance tracking works | Records saved and displayed |
| Transfer history preserved | All transfers logged |
| Reports render correctly | All reports display data |
| Security permissions enforced | Only authorized access |

---

## 9. Future Roadmap

| Version | Feature | Description |
|---------|---------|-------------|
| 1.1.0 | Journal Integration | Auto-create depreciation entries |
| 1.2.0 | Barcode Support | Asset tagging with barcodes |
| 1.3.0 | Disposal Workflow | Asset sale/write-off process |
| 2.0.0 | Asset Acquisition | PO integration for new purchases |
| 2.0.0 | Insurance Module | Track insurance policies |

---

## 10. Glossary

| Term | Definition |
|------|------------|
| Fixed Asset | Tangible property with useful life > 1 year |
| Depreciation | Systematic allocation of asset cost over time |
| Book Value | Original cost minus accumulated depreciation |
| Salvage Value | Estimated value at end of useful life |
| Asset Tag | Unique identifier for physical asset |
| Straight-Line | Depreciation method with equal annual amounts |
| Declining Balance | Accelerated depreciation method |

---

*Document Version: 1.0*  
*Last Updated: May 2026*