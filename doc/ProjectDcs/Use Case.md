# FA_Assets - Use Cases

**Document ID:** UC-FAASSETS-001  
**Module:** ksf_FA_Assets  
**Version:** 1.0.0  

---

## 1. Use Case Overview

### UC-001: View All Assets

**Description:** Asset Manager views complete list of company assets.

**Primary Flow:**
1. Asset Manager navigates to Assets page
2. System defaults to list view
3. System retrieves all assets
4. System displays asset table
5. Asset Manager views asset details

**Preconditions:** User has SA_ASSETS permission.

---

### UC-002: Lookup Asset by Serial

**Description:** Asset Manager locates specific asset using serial number.

**Primary Flow:**
1. Asset Manager clicks "Lookup by Serial"
2. Asset Manager enters serial number
3. Asset Manager clicks Search
4. System queries asset by serial
5. If found, display asset details
6. If found, display maintenance history
7. If not found, display error message

**Alternative Flow - Not Found:**
1. System cannot find asset
2. System displays "Asset not found" error
3. Use case ends

**Preconditions:** User has SA_ASSETS permission.

---

### UC-003: View Upcoming Maintenance

**Description:** Maintenance Staff views assets requiring maintenance.

**Primary Flow:**
1. Staff navigates to Assets page
2. Staff clicks "Upcoming Maintenance"
3. System queries maintenance due in 30 days
4. System displays maintenance list
5. Staff views upcoming maintenance items

**Preconditions:** User has SA_ASSETS permission.

---

## 2. Actors

| Actor | Role |
|-------|------|
| Asset Manager | Full asset management access |
| Maintenance Staff | Maintenance view and updates |
| Accountant | Asset value reporting |