<?php
/**
 * Teste rápido para validar que o sistema não trava
 * Simula o carregamento do DashboardController
 */
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Admin';
$_SESSION['user_role'] = 'admin';
$_GET['page'] = 'dashboard';

require_once __DIR__ . '/../app/config/database.php';

// Testar conexão
$db = (new Database())->getConnection();
echo "[OK] Conexão com banco" . PHP_EOL;

// Testar Pipeline Model
require_once __DIR__ . '/../app/models/Pipeline.php';
$pipeline = new Pipeline($db);

$stats = $pipeline->getStats();
echo "[OK] Pipeline stats: " . json_encode([
    'total_active' => $stats['total_active'],
    'total_delayed' => $stats['total_delayed'],
    'completed_month' => $stats['completed_month'],
    'total_value' => $stats['total_value'],
]) . PHP_EOL;

$goals = $pipeline->getStageGoals();
echo "[OK] Stage goals: " . count($goals) . " etapas" . PHP_EOL;

$ordersByStage = $pipeline->getOrdersByStage();
echo "[OK] Orders by stage: " . array_sum(array_map('count', $ordersByStage)) . " pedidos" . PHP_EOL;

// Testar Order Model
require_once __DIR__ . '/../app/models/Order.php';
$orderModel = new Order($db);
echo "[OK] Total orders: " . $orderModel->countAll() . PHP_EOL;
echo "[OK] Active value: R$ " . number_format($orderModel->totalActiveValue(), 2, ',', '.') . PHP_EOL;

// Testar Customer Model
require_once __DIR__ . '/../app/models/Customer.php';
$customerModel = new Customer($db);
echo "[OK] Total customers: " . $customerModel->countAll() . PHP_EOL;

echo PHP_EOL . "=== TODOS OS TESTES PASSARAM ===" . PHP_EOL;
