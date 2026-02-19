<?php
$db = new PDO('mysql:host=localhost;dbname=sistema_grafica','root','');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== ALL ORDERS (status + stage) ===\n";
$r = $db->query("SELECT id, pipeline_stage, status, total_amount FROM orders ORDER BY id");
foreach($r as $row) {
    echo "Order#{$row['id']}: stage={$row['pipeline_stage']} status={$row['status']}\n";
}

echo "\n=== ORDER #2 items ===\n";
$r = $db->query("SELECT oi.id, oi.order_id, oi.product_id, p.name, p.category_id, p.subcategory_id 
    FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = 2");
foreach($r as $row) {
    echo "Item#{$row['id']}: {$row['name']} (P{$row['product_id']}) cat={$row['category_id']} sub=" . ($row['subcategory_id'] ?: 'NULL') . "\n";
}

echo "\n=== order_production_sectors for order 2 ===\n";
$r = $db->query("SELECT * FROM order_production_sectors WHERE order_id = 2");
$rows = $r->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) echo "NONE\n";
foreach($rows as $row) {
    echo "Item#{$row['order_item_id']} Sector#{$row['sector_id']} sort={$row['sort_order']} status={$row['status']}\n";
}
