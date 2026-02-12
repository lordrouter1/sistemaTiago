<?php
require_once __DIR__ . '/../app/config/database.php';
$db = (new Database())->getConnection();
$s = $db->query('SELECT stage, stage_label FROM pipeline_stage_goals ORDER BY stage_order');
while ($r = $s->fetch(PDO::FETCH_ASSOC)) {
    echo $r['stage'] . ' => ' . $r['stage_label'] . PHP_EOL;
}
echo PHP_EOL;
$s2 = $db->query('SELECT id, status, pipeline_stage, priority FROM orders LIMIT 10');
$rows = $s2->fetchAll(PDO::FETCH_ASSOC);
if (empty($rows)) {
    echo "Nenhum pedido encontrado." . PHP_EOL;
} else {
    foreach ($rows as $r) {
        echo "Order #{$r['id']}: status={$r['status']}, stage={$r['pipeline_stage']}, priority={$r['priority']}" . PHP_EOL;
    }
}
