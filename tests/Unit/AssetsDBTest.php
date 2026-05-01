<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../includes/assets_db.inc';

class AssetsDBTest extends TestCase {
    protected function setUp(): void {
        global $db;
        if (!isset($db)) {
            $db = new MockDB();
        }
    }
    
    public function testCreateAsset(): void {
        $result = create_asset('Test Asset', 1, 'SN123', 5000.00, '2024-01-01');
        $this->assertGreaterThan(0, $result);
    }
    
    public function testGetAssets(): void {
        $result = get_assets([]);
        $this->assertNotFalse($result);
    }
    
    public function testGetAssetBySerial(): void {
        $result = get_asset_by_serial('SN123');
        $this->assertNull($result);
    }
    
    public function testAssignAsset(): void {
        $result = assign_asset(1, 'employee', 5);
        $this->assertTrue($result);
    }
    
    public function testTransferAsset(): void {
        $result = transfer_asset(1, 'location', 10, 1);
        $this->assertNotFalse($result);
    }
    
    public function testAddMaintenance(): void {
        $result = add_maintenance(1, 'repair', 'Fixed issue', 150.00, 1, '2024-02-01');
        $this->assertGreaterThan(0, $result);
    }
    
    public function testCalculateDepreciation(): void {
        $result = calculate_depreciation(1, 2024);
        $this->assertIsFloat($result);
    }
    
    public function testGetUpcomingMaintenance(): void {
        $result = get_upcoming_maintenance(30);
        $this->assertNotFalse($result);
    }
}
