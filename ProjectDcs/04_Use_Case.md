# Use Case Specification - Fixed Assets Management (ksf_FA_Assets)

**Module:** ksf_FA_Assets  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Use Case Overview

| ID | Use Case | Actor | Priority |
|----|----------|-------|----------|
| UC-001 | Create Asset Category | Asset Manager | HIGH |
| UC-002 | Register New Asset | Asset Manager | HIGH |
| UC-003 | View Asset List | Auditor, Manager | HIGH |
| UC-004 | Search Asset by Serial | Technician, Manager | MEDIUM |
| UC-005 | Record Maintenance | Technician | HIGH |
| UC-006 | Calculate Depreciation | Accountant | HIGH |
| UC-007 | Transfer Asset | Asset Manager | MEDIUM |
| UC-008 | View Upcoming Maintenance | Technician, Manager | MEDIUM |
| UC-009 | View Asset History | Auditor | MEDIUM |
| UC-010 | Generate Asset Report | Manager, Accountant | MEDIUM |

---

## 2. Use Case Definitions

### UC-001: Create Asset Category

**Actor:** Asset Manager  
**Precondition:** Module activated, user has SA_ASSETSCREATE permission  
**Trigger:** Need to add new asset type

**Steps:**
1. Navigate to Asset Categories page
2. Click "Add Category"
3. Enter category name (unique)
4. Enter description (optional)
5. Select depreciation method
6. Enter useful life in years
7. Enter salvage value (optional, defaults to 0)
8. Save category

**Postcondition:** New category available for asset creation

**Success Scenario:**
```
Input:
  - name: "Computer Equipment"
  - depreciation_method: "Straight-Line"
  - useful_life_years: 3
  - salvage_value: 0

Result: Category "Computer Equipment" created with 3-year straight-line depreciation
```

**Validation Rules:**
- Name must be unique
- Useful life must be positive integer
- Salvage value must be >= 0

---

### UC-002: Register New Asset

**Actor:** Asset Manager  
**Precondition:** At least one category exists  
**Trigger:** New equipment received

**Steps:**
1. Navigate to Assets page
2. Click "Add Asset"
3. Enter asset tag (unique identifier)
4. Enter asset name/description
5. Select category from dropdown
6. Enter serial number (optional)
7. Enter purchase date (optional)
8. Enter purchase cost (optional)
9. Enter current location (optional)
10. Enter assigned employee/customer (optional)
11. Set initial status (default: Active)
12. Save asset

**Postcondition:** Asset record created with initial current value

**Success Scenario:**
```
Input:
  - asset_tag: "AST-2026-001"
  - name: "Dell Laptop XPS 15"
  - category_id: 3 (Computer Equipment)
  - serial_number: "DL2026A001"
  - purchase_date: "2026-05-01"
  - purchase_cost: 1500.00

Result: Asset created with:
  - current_value = 1500.00 (defaults to purchase_cost)
  - status = "Active"
  - location = null
```

**Validation Rules:**
- asset_tag must be unique
- category_id must reference existing category
- purchase_cost must be >= 0 if provided

---

### UC-003: View Asset List

**Actor:** Auditor, Manager, Accountant  
**Precondition:** User has SA_ASSETSVIEW permission  
**Trigger:** Need to review asset inventory

**Steps:**
1. Navigate to Assets page
2. View default list (all active assets)
3. Optionally apply filters:
   a. Select category from filter
   b. Select status from filter
4. Browse list with pagination
5. Click asset row to view details (future)

**Postcondition:** User has viewed asset inventory

**Display Columns:**
- Name
- Serial Number
- Category
- Status
- Current Value
- Action (Edit link)

---

### UC-004: Search Asset by Serial Number

**Actor:** Technician, Manager  
**Precondition:** User has SA_ASSETSVIEW permission  
**Trigger:** Need to locate specific asset

**Steps:**
1. Navigate to Assets page
2. Click "Lookup by Serial" tab
3. Enter serial number (partial search supported)
4. Click Search
5. If found:
   a. Display asset details
   b. Display maintenance history
6. If not found:
   a. Display "Asset not found" message

**Postcondition:** Asset located or not found message displayed

**Success Scenario:**
```
Input: Serial "DL2026A"

Result (if exists):
  - Asset Name: "Dell Laptop XPS 15"
  - Serial: "DL2026A001"
  - Category: "Computer Equipment"
  - Status: "Active"
  - Current Value: $1,500.00
  - Maintenance History: (list)
```

---

### UC-005: Record Maintenance Activity

**Actor:** Technician, Maintenance Staff  
**Precondition:** Asset exists  
**Trigger:** Maintenance completed on asset

**Steps:**
1. Navigate to asset (via list or serial search)
2. Click "Record Maintenance" (future)
3. Enter maintenance details:
   a. Select maintenance type (Routine/Repair/Inspection/Replacement)
   b. Enter description
   c. Enter cost (optional)
   d. Enter performed by (optional)
   e. Enter next maintenance date (optional for scheduling)
4. Save maintenance record

**Postcondition:** Maintenance recorded with history preserved

**Maintenance Types:**
- Routine: Scheduled preventive maintenance
- Repair: Fix for broken/non-working asset
- Inspection: Assessment/audit of asset condition
- Replacement: Component replacement

---

### UC-006: Calculate Depreciation

**Actor:** Accountant, Asset Manager  
**Precondition:** Asset exists with category  
**Trigger:** Need annual depreciation amounts

**Steps:**
1. Navigate to Depreciation page
2. Select asset or view all assets
3. System calculates depreciation based on:
   a. Category depreciation method
   b. Asset purchase cost
   c. Asset current value
   d. Useful life remaining
4. Display annual depreciation amount
5. Optionally record depreciation entry

**Postcondition:** Depreciation calculated and displayed

**Calculation Examples:**

*Straight-Line:*
```
Asset: Purchase Cost $10,000, Salvage $500, Useful Life 5 years
Annual Depreciation = ($10,000 - $500) / 5 = $1,900/year
```

*Declining Balance (Double):*
```
Asset: Current Value $10,000, Useful Life 5 years
Rate = 2/5 = 40%
Year 1 Depreciation = $10,000 × 40% = $4,000
Remaining Value = $10,000 - $4,000 = $6,000

Year 2 Depreciation = $6,000 × 40% = $2,400
Remaining Value = $6,000 - $2,400 = $3,600
```

---

### UC-007: Transfer Asset

**Actor:** Asset Manager  
**Precondition:** Asset exists with current location  
**Trigger:** Asset moved to new location/employee

**Steps:**
1. Navigate to asset
2. Click "Transfer Asset" (future)
3. Enter transfer details:
   a. To Location (new location)
   b. To Employee (optional)
   c. Reason for transfer
   d. Transfer date (default: today)
4. Save transfer record

**Postcondition:** 
- Transfer history recorded
- Asset location updated
- Asset assigned_to updated (if specified)

**Success Scenario:**
```
Asset: "Dell Laptop XPS 15"
Current Location: "Engineering Department"
Current Assigned: "John Smith (Employee 5)"

Transfer To:
  - Location: "Sales Department"
  - Assigned To: "Jane Doe (Employee 12)"
  - Reason: "Equipment rotation"
  - Date: "2026-05-15"

Result:
  - Transfer record created
  - Asset location updated to "Sales Department"
  - Asset assigned_to updated to employee 12
```

---

### UC-008: View Upcoming Maintenance

**Actor:** Technician, Manager  
**Precondition:** User has SA_ASSETSMAINTENANCE permission  
**Trigger:** Need to view scheduled maintenance

**Steps:**
1. Navigate to Assets page
2. Click "Upcoming Maintenance" tab
3. View list of assets with next_maintenance_date within 30 days
4. Optionally adjust days filter
5. Review maintenance due dates

**Postcondition:** List of upcoming maintenance displayed

**Display Columns:**
- Asset Name
- Maintenance Type
- Due Date
- Description

**Ordering:** By due date (soonest first)

---

### UC-009: View Asset History

**Actor:** Auditor, Manager  
**Precondition:** Asset exists  
**Trigger:** Need audit trail for asset

**Steps:**
1. Search for asset by serial number or list
2. View asset details
3. View maintenance history section
4. View transfer history section (future)
5. Review timeline of all activities

**Postcondition:** Complete asset history displayed

**History Components:**
- Creation date
- All maintenance records with dates
- All transfers with dates and reasons
- Current status and location

---

### UC-010: Generate Asset Report

**Actor:** Manager, Accountant  
**Precondition:** User has SA_ASSETSVIEW permission  
**Trigger:** Need asset inventory report

**Steps:**
1. Navigate to Asset Reports
2. Select report type:
   - Asset Listing
   - Depreciation Schedule
   - Maintenance History
   - Assets by Category
   - Assets by Location
3. Apply filters (category, status, date range)
4. Generate report
5. Optionally export to PDF/Excel (future)

**Postcondition:** Report generated and displayed

**Report Types:**

*Asset Listing:*
- All assets with current values
- Grouped by category
- Totals by category and grand total

*Depreciation Schedule:*
- Asset with annual depreciation
- Accumulated depreciation
- Book value by year

---

## 3. Use Case Matrix

| Use Case | Actor | Trigger | Precondition | Postcondition |
|----------|-------|---------|--------------|---------------|
| UC-001 | Asset Manager | Add category | Module active | Category created |
| UC-002 | Asset Manager | New equipment | Category exists | Asset created |
| UC-003 | Auditor | Review inventory | View permission | List displayed |
| UC-004 | Technician | Locate asset | View permission | Asset found or not |
| UC-005 | Technician | Maintenance done | Asset exists | Maintenance logged |
| UC-006 | Accountant | Financial report | Asset exists | Depreciation calculated |
| UC-007 | Asset Manager | Asset moved | Asset exists | Location updated |
| UC-008 | Manager | Schedule review | Maintain permission | Maintenance list shown |
| UC-009 | Auditor | Audit trail | Asset exists | History displayed |
| UC-010 | Accountant | Report request | View permission | Report generated |

---

## 4. Error Handling

### EH-001: Duplicate Asset Tag

**Trigger:** Create asset with existing tag  
**Response:** Error message "Asset tag already exists"  
**User Action:** Enter different asset tag

### EH-002: Invalid Category

**Trigger:** Create asset with non-existent category  
**Response:** Error or validation failure  
**User Action:** Select valid category

### EH-003: Serial Not Found

**Trigger:** Search for non-existent serial  
**Response:** "Asset not found" message  
**User Action:** Verify serial number

### EH-004: Missing Required Fields

**Trigger:** Submit form with missing required fields  
**Response:** Highlight missing fields with error messages  
**User Action:** Fill in required fields

---

## 5. Alternative Flows

### AF-001: Create Asset Without Purchase Data

**Trigger:** Asset registered after purchase already recorded  
**Flow:**
1. Enter asset_tag and name
2. Select category
3. Leave purchase_date and cost empty
4. Set current_value manually
5. Save asset

### AF-002: Bulk Import (Future)

**Trigger:** Large number of assets to register  
**Flow:**
1. Prepare CSV with asset data
2. Upload via bulk import page
3. System validates and imports
4. Report errors for invalid rows

---

*Document Version: 1.0*  
*Last Updated: May 2026*