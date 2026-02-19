<?php
    // Buscar dados rápidos para a home
    $dbHome = (new Database())->getConnection();
    $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');

    // Contadores rápidos
    $totalPedidosAtivos = $dbHome->query("SELECT COUNT(*) FROM orders WHERE pipeline_stage NOT IN ('concluido','cancelado') AND status != 'cancelado'")->fetchColumn();
    $pedidosHoje = $dbHome->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn();
    $atrasados = 0;
    $stmtGoals = $dbHome->query("SELECT stage, max_hours FROM pipeline_stage_goals");
    $goals = [];
    while ($g = $stmtGoals->fetch(PDO::FETCH_ASSOC)) {
        $goals[$g['stage']] = (int)$g['max_hours'];
    }
    $stmtActive = $dbHome->query("SELECT id, pipeline_stage, pipeline_entered_at FROM orders WHERE pipeline_stage NOT IN ('concluido','cancelado') AND status != 'cancelado'");
    while ($o = $stmtActive->fetch(PDO::FETCH_ASSOC)) {
        $hours = round((time() - strtotime($o['pipeline_entered_at'])) / 3600);
        $goal = $goals[$o['pipeline_stage']] ?? 24;
        if ($goal > 0 && $hours > $goal) $atrasados++;
    }

    // Próximos contatos agendados (hoje e futuros)
    $stmtAgenda = $dbHome->query("SELECT o.id, o.scheduled_date, o.priority, c.name as customer_name 
        FROM orders o LEFT JOIN customers c ON o.customer_id = c.id 
        WHERE o.pipeline_stage = 'contato' AND o.scheduled_date >= CURDATE() AND o.status != 'cancelado' 
        ORDER BY o.scheduled_date ASC LIMIT 5");
    $proximosContatos = $stmtAgenda->fetchAll(PDO::FETCH_ASSOC);

    // Últimos pedidos movidos
    $stmtRecentes = $dbHome->query("SELECT h.order_id, h.to_stage, h.created_at, c.name as customer_name 
        FROM pipeline_history h 
        LEFT JOIN orders o ON h.order_id = o.id 
        LEFT JOIN customers c ON o.customer_id = c.id 
        ORDER BY h.created_at DESC LIMIT 5");
    $recentesMov = $stmtRecentes->fetchAll(PDO::FETCH_ASSOC);

    $stagesMap = [
        'contato' => ['label'=>'Contato','color'=>'#9b59b6','icon'=>'fas fa-phone'],
        'orcamento' => ['label'=>'Orçamento','color'=>'#3498db','icon'=>'fas fa-file-invoice-dollar'],
        'venda' => ['label'=>'Venda','color'=>'#2ecc71','icon'=>'fas fa-handshake'],
        'producao' => ['label'=>'Produção','color'=>'#e67e22','icon'=>'fas fa-industry'],
        'preparacao' => ['label'=>'Preparação','color'=>'#1abc9c','icon'=>'fas fa-boxes-packing'],
        'envio' => ['label'=>'Envio/Entrega','color'=>'#e74c3c','icon'=>'fas fa-truck'],
        'financeiro' => ['label'=>'Financeiro','color'=>'#f39c12','icon'=>'fas fa-coins'],
        'concluido' => ['label'=>'Concluído','color'=>'#27ae60','icon'=>'fas fa-check-double'],
        'cancelado' => ['label'=>'Cancelado','color'=>'#95a5a6','icon'=>'fas fa-ban'],
    ];
?>

<div class="container-fluid py-3">

    <!-- Saudação -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 mb-0"><i class="fas fa-hand-sparkles me-2 text-warning"></i>Olá, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Usuário') ?>!</h1>
            <small class="text-muted"><?= date('l, d \d\e F \d\e Y') === date('l, d \d\e F \d\e Y') ? ucfirst(strftime('%A, %d de %B de %Y')) : date('d/m/Y') ?></small>
        </div>
    </div>

    <!-- Resumo Rápido -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <a href="/sistemaTiago/?page=pipeline" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-primary border-4">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold mb-1"><i class="fas fa-tasks me-1"></i>Pedidos Ativos</div>
                        <div class="fs-3 fw-bold text-primary"><?= $totalPedidosAtivos ?></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/sistemaTiago/?page=orders" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-info border-4">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold mb-1"><i class="fas fa-calendar-day me-1"></i>Criados Hoje</div>
                        <div class="fs-3 fw-bold text-info"><?= $pedidosHoje ?></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/sistemaTiago/?page=pipeline" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-4 <?= $atrasados > 0 ? 'border-danger' : 'border-success' ?>">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i>Atrasados</div>
                        <div class="fs-3 fw-bold <?= $atrasados > 0 ? 'text-danger' : 'text-success' ?>"><?= $atrasados ?></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="/sistemaTiago/?page=agenda" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 border-start border-4" style="border-color:#9b59b6 !important;">
                    <div class="card-body p-3 text-center">
                        <div class="text-muted small fw-bold mb-1"><i class="fas fa-calendar-alt me-1"></i>Agenda Hoje</div>
                        <div class="fs-3 fw-bold" style="color:#9b59b6;">
                            <?= count(array_filter($proximosContatos, fn($c) => ($c['scheduled_date'] ?? '') == date('Y-m-d'))) ?>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Ações Rápidas -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom py-2">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-bolt me-2"></i>Ações Rápidas</h5>
        </div>
        <div class="card-body p-3">
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="/sistemaTiago/?page=orders&action=create" class="btn btn-outline-primary w-100 py-3">
                        <i class="fas fa-plus d-block mb-1 fs-4"></i>
                        <span class="small fw-bold">Novo Pedido</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/sistemaTiago/?page=customers&action=create" class="btn btn-outline-success w-100 py-3">
                        <i class="fas fa-user-plus d-block mb-1 fs-4"></i>
                        <span class="small fw-bold">Novo Cliente</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/sistemaTiago/?page=pipeline" class="btn btn-outline-warning w-100 py-3 text-dark">
                        <i class="fas fa-stream d-block mb-1 fs-4"></i>
                        <span class="small fw-bold">Pipeline</span>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="/sistemaTiago/?page=production_board" class="btn btn-outline-info w-100 py-3">
                        <i class="fas fa-tasks d-block mb-1 fs-4"></i>
                        <span class="small fw-bold">Produção</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Próximos Contatos Agendados -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" style="color:#9b59b6;"><i class="fas fa-calendar-check me-2"></i>Próximos Contatos</h6>
                    <a href="/sistemaTiago/?page=agenda" class="btn btn-sm btn-outline-secondary py-0" style="font-size:0.75rem;">Ver Agenda</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($proximosContatos)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-check d-block mb-2" style="font-size:1.5rem;opacity:0.4;"></i>
                        <small>Nenhum contato agendado</small>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($proximosContatos as $contato): 
                            $isToday = (($contato['scheduled_date'] ?? '') == date('Y-m-d'));
                        ?>
                        <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $contato['id'] ?>" class="list-group-item list-group-item-action py-2 px-3 <?= $isToday ? 'list-group-item-warning' : '' ?>">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold small">#<?= str_pad($contato['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                    <span class="ms-1 small"><?= htmlspecialchars($contato['customer_name'] ?? 'Cliente') ?></span>
                                </div>
                                <div>
                                    <?php if ($isToday): ?>
                                    <span class="badge bg-warning text-dark" style="font-size:0.65rem;">HOJE</span>
                                    <?php else: ?>
                                    <span class="text-muted" style="font-size:0.7rem;"><?= date('d/m', strtotime($contato['scheduled_date'])) ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom py-2 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Atividade Recente</h6>
                    <a href="/sistemaTiago/?page=pipeline" class="btn btn-sm btn-outline-secondary py-0" style="font-size:0.75rem;">Ver Pipeline</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentesMov)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-history d-block mb-2" style="font-size:1.5rem;opacity:0.4;"></i>
                        <small>Nenhuma movimentação recente</small>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentesMov as $mov): 
                            $stInfo = $stagesMap[$mov['to_stage']] ?? ['label'=>$mov['to_stage'],'color'=>'#999','icon'=>'fas fa-circle'];
                        ?>
                        <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $mov['order_id'] ?>" class="list-group-item list-group-item-action py-2 px-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                          style="width:24px;height:24px;background:<?= $stInfo['color'] ?>;color:#fff;font-size:0.6rem;">
                                        <i class="<?= $stInfo['icon'] ?>"></i>
                                    </span>
                                    <div>
                                        <span class="fw-bold small">#<?= str_pad($mov['order_id'], 4, '0', STR_PAD_LEFT) ?></span>
                                        <span class="ms-1 small text-muted"><?= htmlspecialchars($mov['customer_name'] ?? '') ?></span>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <span class="badge rounded-pill" style="background:<?= $stInfo['color'] ?>;font-size:0.6rem;"><?= $stInfo['label'] ?></span>
                                    <div class="text-muted" style="font-size:0.6rem;"><?= date('d/m H:i', strtotime($mov['created_at'])) ?></div>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
