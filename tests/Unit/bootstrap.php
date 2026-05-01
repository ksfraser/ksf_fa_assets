<?php
if (!defined('TB_PREF')) define('TB_PREF', 'fa_');
if (!defined('TB_DB')) define('TB_DB', 'fa_company_db');

require_once __DIR__ . '/../../vendor/ksfraser/famock/php/FAMock.php';

class MockDB {
    public $insert_id = 0;
    public $queries = [];
    private $nextInsertId = 1;
    
    public function escape($val) {
        return "'" . addslashes($val) . "'";
    }
    
    public function query($sql) {
        $this->queries[] = $sql;
        if (stripos($sql, 'INSERT') !== false) {
            $this->insert_id = $this->nextInsertId++;
        }
        $result = new stdClass();
        $result->_result = $sql;
        return $result;
    }
    
    public function num_rows($result) { return 0; }
    public function fetch($result) { return null; }
}

$db = new MockDB();

function user_id() { return 1; }