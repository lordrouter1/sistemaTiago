<?php
require_once 'app/config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->prepare("UPDATE order_production_sectors SET status = 'pendente', started_at = NULL WHERE status = 'em_andamento'");
$stmt->execute();
echo "Updated " . $stmt->rowCount() . " em_andamento records to pendente\n";
