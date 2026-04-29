<?php
// Assets Database Functions

function create_asset(string $name, int $category_id, string $serial_number = '', float $cost = null, string $purchase_date = ''): int
{
    global $db;
    $sql = "INSERT INTO " . TB_PREF . "assets (name, category_id, serial_number, purchase_cost, purchase_date, current_value)
        VALUES (" . $db->escape($name) . ", " . $category_id . ", " . $db->escape($serial_number) . ", " . ($cost ?: 'NULL') . ", " . ($purchase_date ? $db->escape($purchase_date) : 'NULL') . ", " . ($cost ?: 0) . ")";
    $db->query($sql);
    return $db->insert_id;
}

function get_assets(array $filters = []): ?object
{
    global $db;
    $sql = "SELECT a.*, c.name as category_name FROM " . TB_PREF . "assets a
        LEFT JOIN " . TB_PREF . "asset_categories c ON a.category_id = c.id WHERE 1=1";
    
    if (!empty($filters['category_id'])) $sql .= " AND a.category_id = " . $filters['category_id'];
    if (!empty($filters['status'])) $sql .= " AND a.status = " . $db->escape($filters['status']);
    if (!empty($filters['serial_number'])) $sql .= " AND a.serial_number LIKE " . $db->escape("%" . $filters['serial_number'] . "%");
    
    return $db->query($sql);
}

function get_asset_by_serial(string $serial): ?array
{
    global $db;
    $sql = "SELECT a.*, c.name as category_name FROM " . TB_PREF . "assets a
        LEFT JOIN " . TB_PREF . "asset_categories c ON a.category_id = c.id
        WHERE a.serial_number = " . $db->escape($serial);
    $result = $db->query($sql);
    return $result && $db->num_rows($result) > 0 ? $db->fetch($result) : null;
}

function get_asset(int $id): ?array
{
    global $db;
    $sql = "SELECT a.*, c.name as category_name FROM " . TB_PREF . "assets a
        LEFT JOIN " . TB_PREF . "asset_categories c ON a.category_id = c.id WHERE a.id = " . $id;
    $result = $db->query($sql);
    return $result && $db->num_rows($result) > 0 ? $db->fetch($result) : null;
}

function assign_asset(int $asset_id, string $assign_type, int $assign_id): bool
{
    global $db;
    return $db->query("UPDATE " . TB_PREF . "assets SET assigned_to = " . $assign_id . ", assigned_type = " . $db->escape($assign_type) . " WHERE id = " . $asset_id);
}

function transfer_asset(int $asset_id, string $to_type, int $to_id, int $by_user): int
{
    global $db;
    $asset = get_asset($asset_id);
    if (!$asset) return 0;
    
    $sql = "INSERT INTO " . TB_PREF . "asset_transfers (asset_id, from_type, from_id, to_type, to_id, transferred_by)
        VALUES (" . $asset_id . ", " . $db->escape($asset['assigned_type']) . ", " . ($asset['assigned_to'] ?: 'NULL') . ", " . $db->escape($to_type) . ", " . $to_id . ", " . $by_user . ")";
    $db->query($sql);
    
    return $db->query("UPDATE " . TB_PREF . "assets SET assigned_type = " . $db->escape($to_type) . ", assigned_to = " . $to_id . " WHERE id = " . $asset_id);
}

function add_maintenance(int $asset_id, string $type, string $desc, float $cost = null, int $by = null, string $next_due = ''): int
{
    global $db;
    $sql = "INSERT INTO " . TB_PREF . "asset_maintenance (asset_id, maintenance_type, description, cost, performed_by, performed_at, next_due_date)
        VALUES (" . $asset_id . ", " . $db->escape($type) . ", " . $db->escape($desc) . ", " . ($cost ?: 'NULL') . ", " . ($by ?: 'NULL') . ", NOW(), " . ($next_due ? $db->escape($next_due) : 'NULL') . ")";
    $db->query($sql);
    return $db->insert_id;
}

function get_maintenance_history(int $asset_id): ?object
{
    global $db;
    return $db->query("SELECT * FROM " . TB_PREF . "asset_maintenance WHERE asset_id = " . $asset_id . " ORDER BY created_at DESC");
}

function calculate_depreciation(int $asset_id, int $year): float
{
    global $db;
    
    $sql = "SELECT a.*, c.depreciation_type, c.useful_life_years, c.salvage_value FROM " . TB_PREF . "assets a
        JOIN " . TB_PREF . "asset_categories c ON a.category_id = c.id WHERE a.id = " . $asset_id;
    $result = $db->query($sql);
    $asset = $db->fetch($result);
    
    if (!$asset || $asset['depreciation_type'] === 'none') return 0;
    
    $opening = $asset['current_value'];
    $salvage = $asset['salvage_value'] ?: 0;
    $life = $asset['useful_life_years'] ?: 5;
    
    if ($asset['depreciation_type'] === 'straight_line') {
        return ($opening - $salvage) / $life;
    } elseif ($asset['depreciation_type'] === 'declining') {
        return $opening * 0.2; // 20% declining
    }
    
    return ($opening - $salvage) * (2 / ($life * ($life + 1)));
}

function get_upcoming_maintenance(int $days = 30): ?object
{
    global $db;
    $future = date('Y-m-d', strtotime("+{$days} days"));
    return $db->query("SELECT m.*, a.name as asset_name, a.serial_number FROM " . TB_PREF . "asset_maintenance m
        JOIN " . TB_PREF . "assets a ON m.asset_id = a.id WHERE m.next_due_date <= " . $db->escape($future) . " AND m.next_due_date IS NOT NULL ORDER BY m.next_due_date");
}