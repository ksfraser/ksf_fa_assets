# UAT Plan - Fixed Assets Management (ksf_FA_Assets)

**Module:** ksf_FA_Assets  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. UAT Objectives

### 1.1 Primary Objectives

1. **Verify Module Activation**: Clean installation without errors
2. **Validate Asset CRUD**: Create, read, update assets correctly
3. **Confirm Depreciation Calculations**: Match expected values
4. **Test Maintenance Tracking**: Record and view maintenance
5. **Verify Transfer Functionality**: Asset moves recorded correctly
6. **Validate Reports**: All report types render properly

### 1.2 Success Criteria

| Metric | Target |
|--------|--------|
| Module activates cleanly | 100% |
| All CRUD operations work | 100% |
| Depreciation calculations correct | 100% match to expected |
| Maintenance tracking functional | 100% |
| Security permissions enforced | 100% |
| Reports render correctly | 100% |

---

## 2. UAT Scenarios

### 2.1 Module Activation Scenarios

#### UAT-ACT001: Install Module

**Scenario:** Fresh installation of FA_Assets module  
**Steps:**
1. Access FrontAccounting Extensions page
2. Select ksf_FA_Assets module
3. Click Install/Activate
4. Verify module appears in Modules list
5. Verify menu items appear under Assets app
6. Verify database tables created

**Expected Result:**
- Module activated without errors
- 5 tables created: fa_asset_categories, fa_assets, fa_asset_depreciation, fa_asset_maintenance, fa_asset_transfers
- Menu items visible

**Pass Criteria:**
- [ ] Activation completes without error
- [ ] Tables exist in database
- [ ] Menu items appear in correct locations
- [ ] Security areas registered

---

#### UAT-ACT002: Module Permissions

**Scenario:** Verify security permissions work  
**Steps:**
1. Create user without asset permissions
2. Attempt to access Assets pages
3. Verify access denied
4. Grant SA_ASSETSVIEW permission
5. Verify view-only access
6. Grant SA_ASSETSCREATE
7. Verify create access

**Expected Result:**
- No permission = access denied
- View permission = can see assets
- Create permission = can add assets

**Pass Criteria:**
- [ ] Unauthorized access blocked
- [ ] View permission grants read access
- [ ] Create permission allows new assets

---

### 2.2 Asset Category Scenarios

#### UAT-CAT001: Create Asset Category

**Scenario:** Create new asset category  
**Steps:**
1. Navigate to Asset Categories
2. Click "Add Category"
3. Enter name: "IT Equipment"
4. Enter description: "All information technology assets"
5. Select depreciation: Straight-Line
6. Enter useful life: 4 years
7. Enter salvage value: 0
8. Save

**Expected Result:** Category created and visible in list

**Pass Criteria:**
- [ ] Category saved to database
- [ ] Category appears in dropdown
- [ ] Can be selected when creating assets

---

#### UAT-CAT002: Create Category with All Methods

**Scenario:** Test all depreciation method options  
**Steps:**
1. Create category "Vehicles" with Declining Balance
2. Create category "Furniture" with Straight-Line
3. Create category "Art" with No Depreciation
4. Verify each method selectable

**Expected Result:** All three methods work correctly

**Pass Criteria:**
- [ ] Declining Balance category selectable
- [ ] Straight-Line category selectable
- [ ] No Depreciation category selectable

---

### 2.3 Asset Management Scenarios

#### UAT-ASSET001: Register New Asset

**Scenario:** Create complete asset record  
**Steps:**
1. Navigate to Assets > Add New
2. Enter asset_tag: "AST-2026-001"
3. Enter name: "Dell Laptop XPS 15"
4. Select category: "IT Equipment"
5. Enter serial: "DL2026A001"
6. Enter purchase_date: 2026-05-01
7. Enter purchase_cost: 1500.00
8. Enter location: "Main Office"
9. Select status: "Active"
10. Save

**Expected Result:** Asset created with all fields

**Pass Criteria:**
- [ ] Asset saved with correct tag
- [ ] Current value defaults to purchase_cost
- [ ] Asset appears in list
- [ ] Can be retrieved by serial number

---

#### UAT-ASSET002: Duplicate Asset Tag Prevention

**Scenario:** Verify unique tag constraint  
**Steps:**
1. Create asset with tag "AST-001"
2. Attempt to create another asset with same tag
3. Verify error message

**Expected Result:** Error displayed, duplicate not created

**Pass Criteria:**
- [ ] Error message shown
- [ ] Second asset not created
- [ ] Original asset unchanged

---

#### UAT-ASSET003: List Assets with Filters

**Scenario:** View and filter asset list  
**Steps:**
1. Create 3 assets in different categories
2. Navigate to Assets page (default list)
3. Filter by category "IT Equipment"
4. Verify only IT assets shown
5. Filter by status "Active"
6. Verify correct filtering

**Expected Result:** Assets filtered correctly

**Pass Criteria:**
- [ ] All assets shown by default
- [ ] Category filter works
- [ ] Status filter works

---

#### UAT-ASSET004: Search by Serial Number

**Scenario:** Find asset using serial search  
**Steps:**
1. Create asset with serial "SN-12345"
2. Navigate to Assets > Lookup by Serial
3. Enter "SN-12345"
4. Click Search
5. Verify asset found with details
6. View maintenance history section

**Expected Result:** Asset details and history displayed

**Pass Criteria:**
- [ ] Exact serial match found
- [ ] Asset details correct
- [ ] Maintenance history shown (if any)

---

#### UAT-ASSET005: Partial Serial Search

**Scenario:** Search with partial serial number  
**Steps:**
1. Assets with serials: SN-12345, SN-12346, SN-12347
2. Search for "SN-123"
3. Verify all matching serials found

**Expected Result:** All partial matches displayed

**Pass Criteria:**
- [ ] Partial search returns matches
- [ ] All relevant assets found

---

### 2.4 Depreciation Scenarios

#### UAT-DEPR001: Straight-Line Calculation

**Scenario:** Verify straight-line depreciation  
**Steps:**
1. Create category "Computers" with:
   - Method: Straight-Line
   - Useful life: 5 years
   - Salvage value: $200
2. Create asset:
   - Purchase cost: $5,200
   - Category: "Computers"
3. Calculate annual depreciation
4. Verify: ($5,200 - $200) / 5 = $1,000/year

**Expected Result:** $1,000 annual depreciation

**Pass Criteria:**
- [ ] Calculation matches formula
- [ ] Correct value displayed
- [ ] Book value = $4,200 after year 1

---

#### UAT-DEPR002: Declining Balance Calculation

**Scenario:** Verify declining balance depreciation  
**Steps:**
1. Create category "Vehicles" with:
   - Method: Declining Balance
   - Useful life: 5 years
   - Salvage value: $500
2. Create asset with cost $10,000
3. Calculate Year 1 depreciation: $10,000 × 40% = $4,000
4. Update asset current value to $6,000
5. Calculate Year 2 depreciation: $6,000 × 40% = $2,400

**Expected Results:**
- Year 1: $4,000
- Year 2: $2,400

**Pass Criteria:**
- [ ] Year 1 = $4,000
- [ ] Year 2 = $2,400
- [ ] Applied to current value (not original)

---

#### UAT-DEPR003: No Depreciation Method

**Scenario:** Verify assets with no depreciation  
**Steps:**
1. Create asset in category with "None" depreciation
2. Calculate depreciation
3. Verify result is $0.00

**Expected Result:** $0.00 depreciation

**Pass Criteria:**
- [ ] Zero returned for no depreciation
- [ ] Asset value unchanged

---

#### UAT-DEPR004: Salvage Value Floor

**Scenario:** Verify depreciation stops at salvage value  
**Steps:**
1. Asset with current value $1,200
2. Salvage value: $500
3. Useful life remaining: 3 years
4. Straight-line calculation: ($1,200 - $500) / 3 = $233.33
5. If calculation would go below salvage, cap it

**Expected Result:** Depreciation capped at $700 to reach salvage

**Pass Criteria:**
- [ ] Depreciation limited to preserve salvage value
- [ ] Final book value = salvage value

---

### 2.5 Maintenance Scenarios

#### UAT-MAINT001: Record Maintenance

**Scenario:** Add maintenance record to asset  
**Steps:**
1. Navigate to existing asset
2. Click "Add Maintenance" (or via list)
3. Select maintenance type: "Routine"
4. Enter description: "Annual inspection"
5. Enter cost: $50.00
6. Enter performed by: "John Technician"
7. Enter next maintenance date: 30 days from today
8. Save

**Expected Result:** Maintenance recorded

**Pass Criteria:**
- [ ] Maintenance saved with all fields
- [ ] Appears in maintenance history
- [ ] Next maintenance date set

---

#### UAT-MAINT002: View Maintenance History

**Scenario:** View asset's maintenance records  
**Steps:**
1. Asset has 3 maintenance records
2. Search asset by serial
3. View maintenance history section
4. Verify all 3 records displayed
5. Verify ordered by date (newest first)

**Expected Result:** Complete maintenance history shown

**Pass Criteria:**
- [ ] All records displayed
- [ ] Dates correct
- [ ] Type and description shown
- [ ] Cost recorded (if applicable)

---

#### UAT-MAINT003: View Upcoming Maintenance

**Scenario:** Review assets due for maintenance  
**Steps:**
1. Several assets have next_maintenance_date within 30 days
2. Navigate to Assets > Upcoming Maintenance
3. View list
4. Verify assets sorted by due date
5. Verify correct asset names and types

**Expected Result:** List of upcoming maintenance

**Pass Criteria:**
- [ ] All assets due within 30 days shown
- [ ] Sorted by due date (soonest first)
- [ ] Correct type and description displayed

---

### 2.6 Transfer Scenarios

#### UAT-TRANS001: Record Asset Transfer

**Scenario:** Move asset to new location/employee  
**Steps:**
1. Asset currently at "Engineering" assigned to "Employee A"
2. Navigate to asset
3. Click "Transfer Asset"
4. Enter:
   - To Location: "Sales Department"
   - To Employee: "Employee B"
   - Reason: "Equipment rotation"
5. Save transfer

**Expected Result:** Asset transferred, history recorded

**Pass Criteria:**
- [ ] Transfer record created
- [ ] Asset location updated to "Sales Department"
- [ ] Asset assigned to "Employee B"
- [ ] Transfer history shows from/to

---

### 2.7 Report Scenarios

#### UAT-REPT001: Asset Listing Report

**Scenario:** Generate asset inventory report  
**Steps:**
1. Navigate to Asset Reports
2. Select "Asset Listing" report
3. Optionally filter by category
4. Generate report
5. Verify all assets listed
6. Verify totals calculated

**Expected Result:** Complete asset list with values

**Pass Criteria:**
- [ ] All assets included
- [ ] Category grouping correct
- [ ] Totals match actual values

---

## 3. UAT Execution Matrix

| Scenario | Tester | Date | Result | Sign-off |
|----------|--------|------|--------|----------|
| UAT-ACT001 | | | | |
| UAT-ACT002 | | | | |
| UAT-CAT001 | | | | |
| UAT-CAT002 | | | | |
| UAT-ASSET001 | | | | |
| UAT-ASSET002 | | | | |
| UAT-ASSET003 | | | | |
| UAT-ASSET004 | | | | |
| UAT-ASSET005 | | | | |
| UAT-DEPR001 | | | | |
| UAT-DEPR002 | | | | |
| UAT-DEPR003 | | | | |
| UAT-DEPR004 | | | | |
| UAT-MAINT001 | | | | |
| UAT-MAINT002 | | | | |
| UAT-MAINT003 | | | | |
| UAT-TRANS001 | | | | |
| UAT-REPT001 | | | | |

---

## 4. Sign-off Criteria

### 4.1 Prerequisites for Sign-off

- [ ] All 18 scenarios executed
- [ ] All scenarios pass (100% pass rate)
- [ ] No critical or high severity issues open
- [ ] Module functions correctly
- [ ] Reports render correctly
- [ ] Security working as expected

### 4.2 Sign-off Declaration

| Role | Name | Date | Signature |
|------|------|------|-----------|
| UAT Lead | | | |
| Technical Lead | | | |
| Product Owner | | | |

---

## 5. Known Limitations

| Limitation | Impact | Workaround |
|------------|--------|------------|
| No journal entries | No auto-posting to GL | Manual journal entries |
| No asset disposal | Can't retire assets properly | Manual tracking |
| No barcodes | Manual identification | Asset tag numbers |
| No bulk import | Must enter one-by-one | Future enhancement |

---

## 6. Defect Severity Definitions

| Severity | Definition | Example |
|----------|------------|---------|
| Critical | Module won't activate | Table creation fails |
| High | Major feature broken | Asset creation doesn't work |
| Medium | Minor feature affected | Report totals incorrect |
| Low | Cosmetic issue | Text alignment |

---

*Document Version: 1.0*  
*Last Updated: May 2026*