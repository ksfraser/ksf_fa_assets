# FA_Assets - Test Plan

**Document ID:** TP-FAASSETS-001  
**Module:** ksf_FA_Assets  
**Version:** 1.0.0  

---

## 1. Test Scope

- Asset list display
- Serial number lookup
- Maintenance history display
- Upcoming maintenance view

## 2. Test Cases

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-001 | testGetAllAssets | no filter | Returns asset array |
| TC-002 | testGetAssetBySerial_Exists | valid serial | Returns matching asset |
| TC-003 | testGetAssetBySerial_NotFound | invalid serial | Returns null |
| TC-004 | testGetUpcomingMaintenance | 30 days | Returns assets due soon |
| TC-005 | testGetMaintenanceHistory | asset ID | Returns history array |
| TC-006 | testAssetTableColumns | all assets | Contains required columns |
| TC-007 | testMaintenanceTableColumns | history | Contains required columns |