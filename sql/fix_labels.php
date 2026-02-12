<?php
/**
 * Script para corrigir labels com acento na tabela pipeline_stage_goals
 * Execute via navegador: http://localhost/sistemaTiago/sql/fix_labels.php
 * Apague este arquivo após a execução.
 */
require_once __DIR__ . '/../app/config/database.php';

$db = (new Database())->getConnection();

$labels = [
    'orcamento'  => 'Orçamento',
    'producao'   => 'Produção',
    'preparacao' => 'Preparação',
    'concluido'  => 'Concluído',
];

foreach ($labels as $stage => $label) {
    $stmt = $db->prepare("UPDATE pipeline_stage_goals SET stage_label = :label WHERE stage = :stage");
    $stmt->execute([':label' => $label, ':stage' => $stage]);
    echo "Atualizado: $stage => $label<br>";
}

// Atualizar pedidos existentes que podem ter status antigo 'Pendente' (com P maiúsculo)
$db->exec("UPDATE orders SET status = 'pendente' WHERE status = 'Pendente'");
echo "<br>Status 'Pendente' normalizado para 'pendente'.<br>";

echo "<br><strong>Concluído!</strong> Apague este arquivo.";
