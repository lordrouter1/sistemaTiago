<?php
$baseDir = dirname(__DIR__); // d:\xampp\htdocs\sistemaTiago
require_once $baseDir . '/app/config/database.php';
$db = (new Database())->getConnection();
$sql = file_get_contents($baseDir . '/sql/stock_module.sql');

if (!$sql) {
    echo "ERROR: Não foi possível ler stock_module.sql\n";
    exit(1);
}

// Remove comment lines and split by semicolon
$lines = explode("\n", $sql);
$clean = '';
foreach ($lines as $line) {
    $trimmed = trim($line);
    if ($trimmed === '' || strpos($trimmed, '--') === 0) continue;
    $clean .= $line . "\n";
}

$statements = array_filter(array_map('trim', explode(';', $clean)));
$ok = 0;
$err = 0;
foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (empty($stmt)) continue;
    try {
        $db->exec($stmt);
        $ok++;
        echo "OK: " . substr(preg_replace('/\s+/', ' ', $stmt), 0, 80) . "...\n";
    } catch (PDOException $e) {
        echo "WARN: " . $e->getMessage() . "\n";
        $err++;
    }
}
echo "\nDone: $ok OK, $err warnings\n";
