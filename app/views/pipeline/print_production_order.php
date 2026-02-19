<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ordem de Produção #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- JsBarcode para geração de códigos de barras -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; font-size: 11px; }
            .container { max-width: 100% !important; padding: 10px !important; }
            .card { border: 1px solid #ddd !important; box-shadow: none !important; break-inside: avoid; }
            .table th { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .bg-dark, .bg-primary, .bg-warning, .bg-success, .bg-info { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .production-item-card { break-inside: avoid; }
            .sector-badge { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            @page { margin: 10mm; }
        }
        body { background: #f5f5f5; font-family: 'Segoe UI', Arial, sans-serif; }
        .order-header { border-bottom: 3px solid #e67e22; padding-bottom: 15px; margin-bottom: 20px; }
        .company-logo img { max-height: 70px; }
        .company-name { font-size: 1.6rem; font-weight: 800; color: #2c3e50; }
        .order-title { font-size: 1.3rem; color: #e67e22; font-weight: 700; }
        .info-label { font-weight: 600; color: #7f8c8d; font-size: 0.8rem; text-transform: uppercase; }
        .info-value { font-weight: 500; color: #2c3e50; }
        .barcode-container { text-align: center; }
        .barcode-container svg { max-width: 100%; height: auto; }
        .production-item-card { border-left: 4px solid #e67e22 !important; }
        .production-item-card.all-done { border-left-color: #27ae60 !important; }
        .sector-flow { display: flex; align-items: center; flex-wrap: wrap; gap: 4px; }
        .sector-badge { 
            display: inline-flex; align-items: center; gap: 4px; 
            padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600;
            border: 1px solid #dee2e6; background: #f8f9fa; color: #495057;
        }
        .sector-badge.active { background: #fff3cd; border-color: #ffc107; color: #856404; }
        .sector-badge.done { background: #d1e7dd; border-color: #27ae60; color: #0f5132; }
        .sector-arrow { color: #adb5bd; font-size: 0.7rem; }
        .product-barcode svg { max-height: 45px; }
        .checkbox-line { 
            display: flex; align-items: center; gap: 8px; 
            padding: 6px 10px; border: 1px solid #e9ecef; border-radius: 6px; 
            margin-bottom: 4px; background: #fff;
        }
        .checkbox-line .check-box { 
            width: 18px; height: 18px; border: 2px solid #999; border-radius: 3px; flex-shrink: 0;
        }
        .footer-note { border-top: 2px solid #ecf0f1; padding-top: 10px; margin-top: 20px; }
    </style>
</head>
<body>
    <?php
    require_once 'app/models/CompanySettings.php';
    $customerFormattedAddress = '';
    if (!empty($order['customer_address'])) {
        $customerFormattedAddress = CompanySettings::formatCustomerAddress($order['customer_address']);
    }

    // Agrupar setores por order_item_id
    $itemSectors = [];
    foreach ($orderProductionSectors as $sec) {
        $iid = $sec['order_item_id'];
        if (!isset($itemSectors[$iid])) {
            $itemSectors[$iid] = [
                'product_name' => $sec['product_name'],
                'product_id'   => $sec['product_id'],
                'quantity'      => $sec['quantity'],
                'sectors'       => [],
            ];
        }
        $itemSectors[$iid]['sectors'][] = $sec;
    }
    ?>

    <!-- Barra de ações (não imprime) -->
    <div class="no-print bg-dark text-white py-2">
        <div class="container d-flex justify-content-between align-items-center">
            <span><i class="fas fa-industry me-2"></i>Ordem de Produção #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></span>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-warning btn-sm text-dark"><i class="fas fa-print me-1"></i> Imprimir</button>
                <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $order['id'] ?>" class="btn btn-outline-light btn-sm"><i class="fas fa-arrow-left me-1"></i> Voltar</a>
                <button onclick="window.close()" class="btn btn-outline-light btn-sm"><i class="fas fa-times me-1"></i> Fechar</button>
            </div>
        </div>
    </div>

    <div class="container py-4" style="max-width: 800px;">

        <!-- ═══ CABEÇALHO ═══ -->
        <div class="order-header d-flex justify-content-between align-items-start">
            <div>
                <?php if (!empty($company['company_logo']) && file_exists($company['company_logo'])): ?>
                <div class="company-logo mb-1">
                    <img src="/sistemaTiago/<?= $company['company_logo'] ?>" alt="Logo">
                </div>
                <?php endif; ?>
                <div class="company-name"><?= htmlspecialchars($company['company_name'] ?? 'Minha Gráfica') ?></div>
                <?php if (!empty($company['company_document'])): ?>
                <div class="text-muted small"><?= htmlspecialchars($company['company_document']) ?></div>
                <?php endif; ?>
                <?php if (!empty($companyAddress)): ?>
                <div class="text-muted small"><?= htmlspecialchars($companyAddress) ?></div>
                <?php endif; ?>
            </div>
            <div class="text-end">
                <div class="order-title"><i class="fas fa-industry me-1"></i> ORDEM DE PRODUÇÃO</div>
                <div class="fw-bold fs-5">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></div>
                <div class="text-muted small">Emitida em: <?= date('d/m/Y H:i') ?></div>
                <!-- Código de barras do pedido -->
                <div class="barcode-container mt-2">
                    <svg id="barcode-order"></svg>
                </div>
            </div>
        </div>

        <!-- ═══ DADOS DO PEDIDO E CLIENTE ═══ -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-info-circle me-2"></i>Dados do Pedido</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-1">
                            <div class="col-6">
                                <span class="info-label">Pedido</span>
                                <div class="info-value fw-bold">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></div>
                            </div>
                            <div class="col-6">
                                <span class="info-label">Data Criação</span>
                                <div class="info-value"><?= date('d/m/Y', strtotime($order['created_at'])) ?></div>
                            </div>
                            <div class="col-6">
                                <span class="info-label">Prioridade</span>
                                <div class="info-value">
                                    <?php
                                    $prioMap = ['baixa' => '🟢 Baixa', 'normal' => '🔵 Normal', 'alta' => '🟡 Alta', 'urgente' => '🔴 Urgente'];
                                    echo $prioMap[$order['priority'] ?? 'normal'] ?? 'Normal';
                                    ?>
                                </div>
                            </div>
                            <div class="col-6">
                                <span class="info-label">Prazo</span>
                                <div class="info-value <?= (!empty($order['deadline']) && strtotime($order['deadline']) < time()) ? 'text-danger fw-bold' : '' ?>">
                                    <?= !empty($order['deadline']) ? date('d/m/Y', strtotime($order['deadline'])) : '—' ?>
                                </div>
                            </div>
                            <?php if (!empty($order['assigned_name'])): ?>
                            <div class="col-12">
                                <span class="info-label">Responsável</span>
                                <div class="info-value"><?= htmlspecialchars($order['assigned_name']) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-user me-2"></i>Cliente</h6>
                    </div>
                    <div class="card-body py-2">
                        <div class="row g-1">
                            <div class="col-12">
                                <span class="info-label">Nome</span>
                                <div class="info-value fw-bold"><?= htmlspecialchars($order['customer_name'] ?? '—') ?></div>
                            </div>
                            <?php if (!empty($order['customer_phone'])): ?>
                            <div class="col-6">
                                <span class="info-label">Telefone</span>
                                <div class="info-value"><?= $order['customer_phone'] ?></div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($order['customer_email'])): ?>
                            <div class="col-6">
                                <span class="info-label">E-mail</span>
                                <div class="info-value small"><?= htmlspecialchars($order['customer_email']) ?></div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ PRODUTOS E SETORES DE PRODUÇÃO ═══ -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header py-2" style="background: #fef3e2;">
                <h6 class="mb-0 fw-bold" style="color: #e67e22;">
                    <i class="fas fa-boxes-packing me-2"></i>Produtos — Ordem de Produção
                    <span class="badge bg-secondary ms-2" style="font-size:0.7rem;"><?= count($orderItems) ?> itens</span>
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($orderItems)): ?>
                    <?php $idx = 0; foreach ($orderItems as $item): $idx++; 
                        $iid = $item['id'];
                        $sectors = $itemSectors[$iid]['sectors'] ?? [];
                        // Calcular progresso
                        $done = 0; 
                        foreach ($sectors as $s) { if ($s['status'] === 'concluido') $done++; }
                        $allDone = (!empty($sectors) && $done === count($sectors));
                        $barcodeId = 'P' . str_pad($order['id'], 4, '0', STR_PAD_LEFT) . '-I' . str_pad($iid, 4, '0', STR_PAD_LEFT);
                    ?>
                    <div class="production-item-card p-3 <?= $allDone ? 'all-done' : '' ?> <?= $idx > 1 ? 'border-top' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-circle d-flex align-items-center justify-content-center" 
                                      style="width:30px;height:30px;font-size:0.75rem;background:<?= $allDone ? '#27ae60' : '#e67e22' ?>;color:#fff;">
                                    <?= $allDone ? '✓' : $idx ?>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?= htmlspecialchars($item['product_name']) ?></h6>
                                    <small class="text-muted">
                                        <i class="fas fa-cubes me-1"></i>Quantidade: <strong><?= $item['quantity'] ?></strong>
                                        <?php if (!empty($sectors)): ?>
                                        &nbsp;·&nbsp; Setores: <?= $done ?>/<?= count($sectors) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                            <!-- Código de barras do item -->
                            <div class="product-barcode text-end">
                                <svg class="barcode-item" data-value="<?= $barcodeId ?>"></svg>
                                <div class="text-muted" style="font-size:0.6rem;">Item #<?= $iid ?></div>
                            </div>
                        </div>

                        <!-- Fluxo de setores -->
                        <?php if (!empty($sectors)): ?>
                        <div class="sector-flow mb-2">
                            <?php foreach ($sectors as $si => $sec): 
                                $isDone = ($sec['status'] === 'concluido');
                                $isPending = ($sec['status'] === 'pendente');
                                // O setor atual é o primeiro pendente
                                $isCurrentSector = false;
                                if ($isPending) {
                                    $isCurrentSector = true;
                                    foreach (array_slice($sectors, 0, $si) as $prev) {
                                        if ($prev['status'] === 'pendente') { $isCurrentSector = false; break; }
                                    }
                                }
                            ?>
                                <?php if ($si > 0): ?>
                                <span class="sector-arrow"><i class="fas fa-chevron-right"></i></span>
                                <?php endif; ?>
                                <span class="sector-badge <?= $isDone ? 'done' : ($isCurrentSector ? 'active' : '') ?>">
                                    <i class="<?= htmlspecialchars($sec['icon'] ?: 'fas fa-cog') ?>" style="font-size:0.7rem;"></i>
                                    <?= htmlspecialchars($sec['sector_name']) ?>
                                    <?php if ($isDone): ?>
                                        <i class="fas fa-check" style="font-size:0.6rem;"></i>
                                    <?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Checklist para marcação manual durante produção -->
                        <?php if (!empty($sectors)): ?>
                        <div class="mt-2">
                            <?php foreach ($sectors as $sec): 
                                $isDone = ($sec['status'] === 'concluido');
                            ?>
                            <div class="checkbox-line">
                                <div class="check-box <?= $isDone ? 'bg-success border-success' : '' ?>" style="<?= $isDone ? 'display:flex;align-items:center;justify-content:center;' : '' ?>">
                                    <?php if ($isDone): ?><i class="fas fa-check text-white" style="font-size:0.65rem;"></i><?php endif; ?>
                                </div>
                                <span class="small <?= $isDone ? 'text-decoration-line-through text-muted' : 'fw-bold' ?>">
                                    <i class="<?= htmlspecialchars($sec['icon'] ?: 'fas fa-cog') ?> me-1" style="color:<?= htmlspecialchars($sec['color'] ?: '#666') ?>;font-size:0.75rem;"></i>
                                    <?= htmlspecialchars($sec['sector_name']) ?>
                                </span>
                                <?php if ($isDone && !empty($sec['completed_by_name'])): ?>
                                <span class="text-muted ms-auto" style="font-size:0.65rem;">
                                    <i class="fas fa-user me-1"></i><?= htmlspecialchars($sec['completed_by_name']) ?>
                                    <?php if (!empty($sec['completed_at'])): ?>
                                    — <?= date('d/m H:i', strtotime($sec['completed_at'])) ?>
                                    <?php endif; ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-box-open d-block mb-2" style="font-size:2rem;"></i>
                    Nenhum produto no pedido.
                </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (!empty($order['internal_notes'])): ?>
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light py-2">
                <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-sticky-note me-2"></i>Observações Internas</h6>
            </div>
            <div class="card-body py-3">
                <p class="mb-0 small"><?= nl2br(htmlspecialchars($order['internal_notes'])) ?></p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Rodapé -->
        <div class="footer-note text-center">
            <p class="text-muted small mb-1">
                <i class="fas fa-industry me-1"></i>Ordem de Produção gerada em <?= date('d/m/Y \à\s H:i') ?>
            </p>
            <p class="text-muted small mb-0">
                <?= htmlspecialchars($company['company_name'] ?? '') ?> — Uso interno
            </p>
        </div>

        <!-- Assinatura -->
        <div class="row mt-4 pt-3">
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #333; width: 90%; margin: 0 auto; padding-top: 5px;">
                    <small class="text-muted">Responsável</small>
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #333; width: 90%; margin: 0 auto; padding-top: 5px;">
                    <small class="text-muted">Produção</small>
                </div>
            </div>
            <div class="col-4 text-center">
                <div style="border-top: 1px solid #333; width: 90%; margin: 0 auto; padding-top: 5px;">
                    <small class="text-muted">Conferência</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Gerar códigos de barras via JsBarcode -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Código de barras do pedido
        try {
            JsBarcode("#barcode-order", "OP<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>", {
                format: "CODE128",
                width: 1.5,
                height: 40,
                displayValue: true,
                fontSize: 11,
                margin: 5,
                textMargin: 2
            });
        } catch(e) { console.warn('Barcode order error:', e); }

        // Códigos de barras dos itens
        document.querySelectorAll('.barcode-item').forEach(function(svg) {
            var val = svg.getAttribute('data-value');
            if (val) {
                try {
                    JsBarcode(svg, val, {
                        format: "CODE128",
                        width: 1,
                        height: 30,
                        displayValue: true,
                        fontSize: 9,
                        margin: 2,
                        textMargin: 1
                    });
                } catch(e) { console.warn('Barcode item error:', e); }
            }
        });
    });
    </script>
</body>
</html>
