# Functional Requirements - Fixed Assets Management (ksf_FA_Assets)

**Module:** ksf_FA_Assets  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Introduction

This document specifies the functional requirements for the Fixed Assets Management module. Requirements are categorized by feature area and traced to test cases.

---

## 2. Asset Category Requirements

### 2.1 Category Management

| ID | Requirement | Priority |
|----|-------------|----------|
| **CAT-001** | System shall allow creating asset categories | MUST |
| **CAT-002** | Category must have unique name | MUST |
| **CAT-003** | Category must have depreciation method selection | MUST |
| **CAT-004** | Category must support useful life in years | MUST |
| **CAT-005** | Category must support salvage value | MUST |
| **CAT-006** | Categories can be marked inactive | SHOULD |
| **CAT-007** | Inactive categories hidden from normal lists | SHOULD |

#### CAT-001: Create Asset Category

**Description:** Create new asset category

**Acceptance Criteria:**
- Category name required (max 100 chars)
- Depreciation method: Straight-Line, Declining Balance, or None
- Useful life defaults to 5 years
- Salvage value defaults to 0

**Test Scenario:**
1. Navigate to Asset Categories
2. Enter category name
3. Select depreciation method
4. Enter useful life (default 5)
5. Enter salvage value (default 0)
6. Save category
7. Verify category appears in list

---

## 3. Asset Record Requirements

### 3.1 Asset CRUD

| ID | Requirement | Priority |
|----|-------------|----------|
| **ASSET-001** | System shall allow creating asset records | MUST |
| **ASSET-002** | Asset tag must be unique | MUST |
| **ASSET-003** | Asset must be linked to category | MUST |
| **ASSET-004** | Asset must track serial number | SHOULD |
| **ASSET-005** | Asset must track purchase date and cost | MUST |
| **ASSET-006** | Asset must track current value | MUST |
| **ASSET-007** | Asset must track location | SHOULD |
| **ASSET-008** | Asset must track assigned employee/customer | SHOULD |
| **ASSET-009** | Asset must track status | MUST |
| **ASSET-010** | System shall list all assets with filtering | MUST |
| **ASSET-011** | System shall allow searching by serial number | SHOULD |

#### ASSET-001: Create Asset Record

**Description:** Create new fixed asset

**Acceptance Criteria:**
- Asset Tag: Required, unique, max 50 chars
- Name: Required, max 100 chars
- Category: Required, must exist
- Purchase Date: Optional, date format
- Purchase Cost: Optional, decimal
- Current Value: Defaults to purchase cost

**Required Fields:**
- asset_tag (unique)
- name
- category_id

**Optional Fields:**
- serial_number
- purchase_date
- purchase_cost
- location
- assigned_to
- status (default: 'Active')
- debtor_no

**Test Scenario:**
1. Navigate to Assets page
2. Click Add Asset
3. Enter asset_tag (unique)
4. Enter name
5. Select category
6. Optionally enter serial, cost, date
7. Save
8. Verify asset in list

---

#### ASSET-002: Asset Tag Uniqueness

**Description:** Prevent duplicate asset tags

**Acceptance Criteria:**
- Attempt to create asset with existing tag returns error
- System prevents save of duplicate
- Clear error message shown

**Test Scenario:**
1. Create asset with tag "AST-001"
2. Attempt to create asset with tag "AST-001"
3. Verify error message displayed
4. Verify second asset not created

---

#### ASSET-010: List Assets with Filters

**Description:** Display assets with filtering options

**Filters:**
- Category filter
- Status filter (Active, Under Maintenance, Retired)
- Search by serial number (partial match)

**Acceptance Criteria:**
- List shows all non-inactive assets by default
- Filter by category shows only that category
- Filter by status shows only matching status

---

#### ASSET-011: Search by Serial Number

**Description:** Find asset by serial number

**Acceptance Criteria:**
- Partial serial number search supported
- Returns asset details on match
- Shows "not found" if no match
- Displays maintenance history for found asset

---

## 4. Depreciation Requirements

### 4.1 Depreciation Calculation

| ID | Requirement | Priority |
|----|-------------|----------|
| **DEPR-001** | System shall calculate straight-line depreciation | MUST |
| **DEPR-002** | System shall calculate declining balance depreciation | MUST |
| **DEPR-003** | System shall record depreciation entries | SHOULD |
| **DEPR-004** | Depreciation shall respect salvage value floor | MUST |
| **DEPR-005** | Annual depreciation shall be determinable per asset | MUST |

#### DEPR-001: Straight-Line Depreciation

**Formula:** `(purchase_cost - salvage_value) / useful_life_years`

**Example:**
- Purchase Cost: $10,000
- Salvage Value: $500
- Useful Life: 5 years
- Annual Depreciation: ($10,000 - $500) / 5 = $1,900

**Acceptance Criteria:**
- Correct calculation for any valid inputs
- Result is decimal (financial precision)
- Minimum salvage value is 0

---

#### DEPR-002: Declining Balance Depreciation

**Formula:** `current_value × (2 / useful_life_years)` (Double Declining)

**Example:**
- Current Value: $10,000
- Useful Life: 5 years
- Rate: 2/5 = 40%
- Year 1 Depreciation: $10,000 × 40% = $4,000
- Year 2 Depreciation: $6,000 × 40% = $2,400

**Acceptance Criteria:**
- Applied to current book value (not original cost)
- Stops when book value reaches salvage value

---

#### DEPR-004: Salvage Value Floor

**Description:** Depreciation stops when book value reaches salvage

**Acceptance Criteria:**
- If calculated depreciation would result in book value < salvage
- Depreciation limited to: current_value - salvage_value
- Result: book value = salvage value

---

## 5. Maintenance Tracking Requirements

### 5.1 Maintenance Records

| ID | Requirement | Priority |
|----|-------------|----------|
| **MAINT-001** | System shall record maintenance activities | MUST |
| **MAINT-002** | Maintenance type options: Routine, Repair, Inspection, Replacement | MUST |
| **MAINT-003** | Maintenance must track date, description, cost | MUST |
| **MAINT-004** | Maintenance must track performed by | SHOULD |
| **MAINT-005** | System shall schedule next maintenance date | SHOULD |
| **MAINT-006** | System shall list upcoming maintenance | MUST |

#### MAINT-001: Record Maintenance Activity

**Required Fields:**
- asset_id (foreign key)
- maintenance_date
- maintenance_type (Routine, Repair, Inspection, Replacement)
- description

**Optional Fields:**
- cost
- performed_by
- next_maintenance_date

**Acceptance Criteria:**
- Maintenance linked to specific asset
- Type selection from predefined list
- Description required
- Date defaults to current date

---

#### MAINT-006: Upcoming Maintenance List

**Description:** Display assets with maintenance due

**Acceptance Criteria:**
- Shows assets with next_maintenance_date within N days
- Default period: 30 days
- Sort by due date (soonest first)
- Display asset name, maintenance type, due date

**Display Columns:**
- Asset Name
- Maintenance Type
- Due Date
- Description

---

## 6. Asset Transfer Requirements

### 6.1 Transfer Records

| ID | Requirement | Priority |
|----|-------------|----------|
| **TRANS-001** | System shall record asset transfers | MUST |
| **TRANS-002** | Transfer records from and to locations | MUST |
| **TRANS-003** | Transfer records from and to employees | SHOULD |
| **TRANS-004** | Transfer must include date and reason | MUST |
| **TRANS-005** | Transfer updates asset's current location | MUST |

#### TRANS-001: Record Asset Transfer

**Required Fields:**
- asset_id
- from_location (can be null for new asset)
- to_location
- transfer_date
- reason

**Optional Fields:**
- from_employee
- to_employee

**Acceptance Criteria:**
- Transfer creates historical record
- Asset's location updated to to_location
- Asset's assigned_to updated to to_employee
- Transfer date recorded

---

## 7. Reporting Requirements

### 7.1 Asset Reports

| ID | Requirement | Priority |
|----|-------------|----------|
| **REPT-001** | System shall provide asset listing report | MUST |
| **REPT-002** | Report shall include depreciation schedule | SHOULD |
| **REPT-003** | Report shall include maintenance history | SHOULD |
| **REPT-004** | Reports accessible from module menu | MUST |

#### REPT-001: Asset Listing Report

**Description:** Comprehensive list of all assets

**Columns:**
- Asset Tag
- Name
- Category
- Serial Number
- Status
- Location
- Current Value

**Filters:**
- Category
- Status
- Location

---

## 8. Database Schema Requirements

### 8.1 Tables Required

| Table | Purpose |
|-------|---------|
| fa_asset_categories | Asset type definitions |
| fa_assets | Individual asset records |
| fa_asset_depreciation | Depreciation history entries |
| fa_asset_maintenance | Maintenance activity records |
| fa_asset_transfers | Asset transfer history |

### 8.2 Index Requirements

| Table | Indexes |
|-------|---------|
| fa_assets | asset_tag (unique), category_id, status, debtor_no |
| fa_asset_categories | name (unique) |
| fa_asset_depreciation | asset_id, depreciation_date |
| fa_asset_maintenance | asset_id, maintenance_date |
| fa_asset_transfers | asset_id, transfer_date |

---

## 9. Security Requirements

### 9.1 Access Control

| Security Area | Permission Level |
|---------------|-------------------|
| SS_ASSETS | Module section identifier |
| SA_ASSETSVIEW | View asset lists and details |
| SA_ASSETSCREATE | Create new assets |
| SA_ASSETSEDIT | Modify existing assets |
| SA_ASSETSMAINTENANCE | Manage maintenance records |

### 9.2 Page Security

| Page | Required Permission |
|------|-------------------|
| assets.php | SA_ASSETS (basic) |
| asset_categories.php | SA_ASSETSVIEW |
| depreciation.php | SA_ASSETSMAINTENANCE |
| reports.php | SA_ASSETSVIEW |

---

## 10. Requirements Traceability Matrix

| Requirement ID | Use Case | Test Case |
|--------------|----------|-----------|
| CAT-001 | UC-001 | TC-CAT001 |
| ASSET-001 | UC-002 | TC-ASSET001 |
| ASSET-002 | UC-003 | TC-ASSET002 |
| ASSET-010 | UC-004 | TC-ASSET010 |
| ASSET-011 | UC-005 | TC-ASSET011 |
| DEPR-001 | UC-006 | TC-DEPR001 |
| DEPR-002 | UC-006 | TC-DEPR002 |
| MAINT-001 | UC-007 | TC-MAINT001 |
| MAINT-006 | UC-008 | TC-MAINT006 |
| TRANS-001 | UC-009 | TC-TRANS001 |
| REPT-001 | UC-010 | TC-REPT001 |

---

*Document Version: 1.0*  
*Last Updated: May 2026*