<?php
$db = new PDO('mysql:host=localhost;dbname=sistema_grafica','root','');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== PRODUCTS ===\n";
$r = $db->query('SELECT id, name, category_id, subcategory_id FROM products');
foreach($r as $row) {
    echo "P{$row['id']}: {$row['name']} cat={$row['category_id']} sub=" . ($row['subcategory_id'] ?: 'NULL') . "\n";
}

echo "\n=== ORDER ITEMS (orders in producao) ===\n";
$r = $db->query("SELECT oi.id as item_id, oi.order_id, oi.product_id, p.name, p.category_id, p.subcategory_id 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    JOIN orders o ON oi.order_id = o.id 
    WHERE o.pipeline_stage = 'producao'
    ORDER BY oi.order_id, oi.id");
foreach($r as $row) {
    echo "Order#{$row['order_id']} Item#{$row['item_id']}: {$row['name']} (P{$row['product_id']}) cat={$row['category_id']} sub=" . ($row['subcategory_id'] ?: 'NULL') . "\n";
}

echo "\n=== ORDER_PRODUCTION_SECTORS ===\n";
$r = $db->query("SELECT ops.*, s.name as sector_name 
    FROM order_production_sectors ops 
    JOIN production_sectors s ON ops.sector_id = s.id 
    ORDER BY ops.order_id, ops.order_item_id, ops.sort_order");
foreach($r as $row) {
    echo "Order#{$row['order_id']} Item#{$row['order_item_id']} Sector:{$row['sector_name']}(#{$row['sector_id']}) sort={$row['sort_order']} status={$row['status']}\n";
}

echo "\n=== CATEGORY_SECTORS ===\n";
$r = $db->query("SELECT cs.*, s.name as sector_name FROM category_sectors cs JOIN production_sectors s ON cs.sector_id = s.id ORDER BY cs.category_id, cs.sort_order");
foreach($r as $row) {
    echo "Cat#{$row['category_id']} -> {$row['sector_name']}(#{$row['sector_id']}) sort={$row['sort_order']}\n";
}

echo "\n=== PRODUCT_SECTORS ===\n";
$r = $db->query("SELECT ps.*, s.name as sector_name FROM product_sectors ps JOIN production_sectors s ON ps.sector_id = s.id ORDER BY ps.product_id, ps.sort_order");
foreach($r as $row) {
    echo "Prod#{$row['product_id']} -> {$row['sector_name']}(#{$row['sector_id']}) sort={$row['sort_order']}\n";
}

echo "\n=== SUBCATEGORY_SECTORS ===\n";
$r = $db->query("SELECT ss.*, s.name as sector_name FROM subcategory_sectors ss JOIN production_sectors s ON ss.sector_id = s.id ORDER BY ss.subcategory_id, ss.sort_order");
foreach($r as $row) {
    echo "Sub#{$row['subcategory_id']} -> {$row['sector_name']}(#{$row['sector_id']}) sort={$row['sort_order']}\n";
}
