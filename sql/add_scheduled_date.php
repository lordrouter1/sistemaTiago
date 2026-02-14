<?php
require_once __DIR__ . '/../app/config/database.php';
$db = (new Database())->getConnection();

try {
    $db->exec("ALTER TABLE orders ADD COLUMN scheduled_date DATE NULL AFTER notes");
    echo "[OK] Coluna scheduled_date adicionada.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "[INFO] Coluna scheduled_date já existe.\n";
    } else {
        echo "[ERRO] " . $e->getMessage() . "\n";
    }
}
