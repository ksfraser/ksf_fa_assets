# Test Plan - Fixed Assets Management (ksf_FA_Assets)

**Module:** ksf_FA_Assets  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Test Overview

### 1.1 Test Objectives

- Verify all database functions work correctly
- Validate depreciation calculations match expected values
- Ensure asset CRUD operations function properly
- Confirm maintenance tracking works
- Achieve comprehensive test coverage

### 1.2 Test Scope

| Component | Coverage Target |
|-----------|----------------|
| assets_db.inc functions | 100% |
| Depreciation calculations | All methods |
| Page rendering | Sections load correctly |
| Error handling | Edge cases covered |

---

## 2. Test Cases

### 2.1 Database Function Tests

#### TC-DB001: Create Asset

**Test ID:** TC-DB001  
**Requirement:** ASSET-001  
**Description:** Test create_asset function

**Setup:**
- Database connection available
- Category exists (id: 1)

**Execution:**
```php
$id = create_asset(
    name: "Test Laptop",
    category_id: 1,
    serial_number: "TST001",
    cost: 1500.00,
    purchase_date: "2026-05-01"
);
```

**Verification:**
- Returns integer (new asset ID)
- Asset exists in database with correct values

**Expected Result:** Valid ID returned, asset in database

---

#### TC-DB002: Get Assets List

**Test ID:** TC-DB002  
**Requirement:** ASSET-010  
**Description:** Test get_assets function with filters

**Setup:**
- Multiple assets exist
- Assets in different categories (1, 2)
- Assets with different statuses

**Test Data:**
| Asset | Category | Status |
|-------|----------|--------|
| Asset A | 1 | Active |
| Asset B | 1 | Active |
| Asset C | 2 | Retired |

**Execution:**
```php
// No filter
$all = get_assets();
$countAll = db_num_rows($all);

// Filter by category
$byCat = get_assets(['category_id' => 1]);
$countCat1 = db_num_rows($byCat);

// Filter by status
$active = get_assets(['status' => 'Active']);
$countActive = db_num_rows($active);
```

**Verification:**
- get_assets() returns all assets
- Filter by category returns correct count
- Filter by status returns correct count

**Expected Results:**
- All: 3 assets
- Category 1: 2 assets
- Active: 2 assets

---

#### TC-DB003: Get Asset by Serial

**Test ID:** TC-DB003  
**Requirement:** ASSET-011  
**Description:** Test serial number search

**Setup:**
- Asset with serial "SN-12345" exists

**Execution:**
```php
$asset = get_asset_by_serial("SN-12345");
```

**Verification:**
- Returns asset array
- Contains correct asset data

**Expected Result:** Asset with serial SN-12345 returned

---

#### TC-DB004: Get Single Asset

**Test ID:** TC-DB004  
**Description:** Test get_asset function

**Setup:**
- Asset exists with ID: 5

**Execution:**
```php
$asset = get_asset(5);
```

**Verification:**
- Returns array with all fields
- Includes category_name from join

**Expected Result:** Complete asset array

---

### 2.2 Depreciation Calculation Tests

#### TC-DEPR001: Straight-Line Calculation

**Test ID:** TC-DEPR001  
**Requirement:** DEPR-001  
**Description:** Verify straight-line depreciation formula

**Setup:**
- Asset with purchase_cost: 10000
- Category with depreciation_method: "straight_line"
- Useful life: 5 years
- Salvage value: 500

**Execution:**
```php
$annual = calculate_depreciation($asset_id, 2026);
```

**Verification:**
- Result equals (10000 - 500) / 5 = 1900
- For year 1 through year 5

**Expected Result:** $1,900.00 per year

---

#### TC-DEPR002: Declining Balance Calculation

**Test ID:** TC-DEPR002  
**Requirement:** DEPR-002  
**Description:** Verify declining balance depreciation

**Setup:**
- Asset with current_value: 10000
- Category with depreciation_method: "declining"
- Useful life: 5 years

**Execution:**
```php
// Year 1
$year1 = calculate_depreciation($asset_id, 2026);

// After depreciation, update current_value
update_asset_current_value($asset_id, 10000 - $year1);

// Year 2
$year2 = calculate_depreciation($asset_id, 2027);
```

**Verification:**
- Year 1: 10000 * (2/5) = 4000
- Year 2: 6000 * (2/5) = 2400

**Expected Results:**
- Year 1: $4,000.00
- Year 2: $2,400.00

---

#### TC-DEPR003: No Depreciation

**Test ID:** TC-DEPR003  
**Requirement:** DEPR-001  
**Description:** Verify "none" depreciation method returns 0

**Setup:**
- Asset with current_value: 5000
- Category with depreciation_method: "none"

**Execution:**
```php
$depr = calculate_depreciation($asset_id, 2026);
```

**Verification:**
- Result equals 0

**Expected Result:** $0.00

---

#### TC-DEPR004: Salvage Value Floor

**Test ID:** TC-DEPR004  
**Requirement:** DEPR-004  
**Description:** Verify depreciation stops at salvage value

**Setup:**
- Asset current_value: 1500
- Salvage value: 500
- Remaining useful life: 2 years
- Straight-line annual: (1500-500)/2 = 500

**Execution:**
```php
// Year 1: normal calculation
$year1 = calculate_depreciation($asset_id, 2026);
// current_value becomes 1500-500 = 1000

// Year 2: would be 500 but floor applies
$year2 = calculate_depreciation($asset_id, 2027);
```

**Verification:**
- Year 2 limited to current_value - salvage = 1000 - 500 = 500

**Expected Result:** 
- Year 1: $500.00
- Year 2: $500.00 (at salvage floor)

---

### 2.3 Maintenance Tests

#### TC-MAINT001: Add Maintenance Record

**Test ID:** TC-MAINT001  
**Requirement:** MAINT-001  
**Description:** Test adding maintenance record

**Setup:**
- Asset exists with ID: 3

**Execution:**
```php
$maint_id = add_maintenance(
    asset_id: 3,
    type: "Routine",
    desc: "Annual inspection",
    cost: 50.00,
    by: 5,
    next_due: "2027-05-01"
);
```

**Verification:**
- Returns integer (maintenance ID)
- Record exists in fa_asset_maintenance

**Expected Result:** Valid ID returned, record created

---

#### TC-MAINT002: Get Maintenance History

**Test ID:** TC-MAINT002  
**Description:** Test retrieving maintenance history

**Setup:**
- Asset has 3 maintenance records

**Execution:**
```php
$history = get_maintenance_history(3);
```

**Verification:**
- Returns result set with 3 rows
- Ordered by date descending (newest first)

**Expected Result:** 3 maintenance records

---

#### TC-MAINT003: Get Upcoming Maintenance

**Test ID:** TC-MAINT003  
**Requirement:** MAINT-006  
**Description:** Test upcoming maintenance listing

**Setup:**
- Multiple assets with next_maintenance_date values

**Test Data:**
| Asset | Next Maintenance |
|-------|------------------|
| Laptop A | 2026-05-20 (5 days) |
| Laptop B | 2026-05-25 (10 days) |
| Desktop C | 2026-07-01 (45 days) |

**Execution:**
```php
// Default 30 days
$upcoming30 = get_upcoming_maintenance(30);

// Custom 10 days
$upcoming10 = get_upcoming_maintenance(10);
```

**Verification:**
- 30-day filter returns 2 assets
- 10-day filter returns 1 asset

**Expected Results:**
- 30 days: 2 assets (Laptop A, Laptop B)
- 10 days: 1 asset (Laptop A)

---

### 2.4 Transfer Tests

#### TC-TRANS001: Record Asset Transfer

**Test ID:** TC-TRANS001  
**Requirement:** TRANS-001  
**Description:** Test asset transfer recording

**Setup:**
- Asset at location "Engineering", assigned to employee 5

**Execution:**
```php
transfer_asset(
    asset_id: 3,
    to_type: "employee",
    to_id: 12,
    by_user: 1
);
```

**Verification:**
- Transfer record created
- Asset location updated to new employee
- from_location recorded

**Expected Result:** 
- Transfer exists with from/to data
- Asset assigned_to updated to employee 12

---

### 2.5 Validation Tests

#### TC-VAL001: Duplicate Asset Tag

**Test ID:** TC-VAL001  
**Requirement:** ASSET-002  
**Description:** Verify duplicate tag prevention

**Setup:**
- Asset with tag "AST-001" exists

**Execution:**
```php
// Create first asset
create_asset(name: "First", category_id: 1, ...);
// Tag: "AST-001"

// Attempt duplicate
$result = create_asset(name: "Second", category_id: 1, ...);
// Use same tag: "AST-001"
```

**Verification:**
- Second create either throws error or returns failure
- Only one asset with tag AST-001 exists

**Expected Result:** Duplicate prevented

---

#### TC-VAL002: Empty Category ID

**Test ID:** TC-VAL002  
**Description:** Verify required category validation

**Execution:**
```php
try {
    create_asset(
        name: "Test",
        category_id: 0, // Invalid
        ...
    );
} catch (Exception $e) {
    // Handle
}
```

**Verification:**
- Error thrown or null returned

**Expected Result:** Error or validation failure

---

### 2.6 Page Tests

#### TC-PAGE001: Assets Page Load

**Test ID:** TC-PAGE001  
**Description:** Verify assets page renders correctly

**Execution:**
1. Navigate to /modules/FA_Assets/pages/assets.php?section=list

**Verification:**
- Page loads without fatal error
- Asset list displays (or empty message)
- Navigation tabs visible

**Expected Result:** Page renders with list section

---

#### TC-PAGE002: Serial Search Section

**Test ID:** TC-PAGE002  
**Description:** Verify serial search section loads

**Execution:**
1. Navigate to assets.php?section=serial

**Verification:**
- Search form displays
- Serial input field present
- Search button visible

**Expected Result:** Form renders correctly

---

#### TC-PAGE003: Maintenance Section

**Test ID:** TC-PAGE003  
**Description:** Verify upcoming maintenance section

**Execution:**
1. Navigate to assets.php?section=maintenance

**Verification:**
- Table displays with columns
- Asset names shown

**Expected Result:** Maintenance list renders

---

## 3. Test Data Matrix

| Test ID | Function | Input | Expected Output |
|---------|----------|-------|----------------|
| TC-DB001 | create_asset | valid data | ID returned |
| TC-DB002 | get_assets | no filter | all assets |
| TC-DB002 | get_assets | category_id=1 | category 1 only |
| TC-DB002 | get_assets | status=Active | active only |
| TC-DB003 | get_asset_by_serial | "SN-12345" | asset array |
| TC-DEPR001 | calculate_depreciation | SL, 10k, 5yr | $1,900 |
| TC-DEPR002 | calculate_depreciation | DB, 10k, 5yr | $4,000 yr1 |
| TC-DEPR003 | calculate_depreciation | none method | $0.00 |
| TC-MAINT001 | add_maintenance | valid data | ID returned |
| TC-MAINT003 | get_upcoming_maintenance | 30 days | 2 assets |

---

## 4. Mock Strategy

### 4.1 Database Mock (Unit Tests)

```php
class MockDB {
    public static $assets = [];
    
    public static function query($sql) {
        // Parse and execute mock queries
    }
    
    public static function fetch($result) {
        return array_shift(self::$assets);
    }
}
```

### 4.2 Global Mock (Integration Tests)

```php
// Mock FA globals
$GLOBALS['db'] = new MockFAConnection();
$GLOBALS['path_to_root'] = '/path/to/fa';
```

---

## 5. Pass Criteria

| Criterion | Target |
|-----------|--------|
| All test cases pass | 100% |
| DB function coverage | >= 90% |
| Depreciation coverage | 100% |
| No regressions | 0 failures |

---

## 6. Test Execution

### 6.1 Unit Tests

```bash
cd /home/kevin/Documents/ksf_FA_Assets
./vendor/bin/phpunit
```

### 6.2 Integration Tests

Manual testing via FA interface required for:
- Page rendering
- Form submission
- Session handling

---

*Document Version: 1.0*  
*Last Updated: May 2026*