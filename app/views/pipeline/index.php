<div class="container-fluid py-3">
    <!-- Header com Estatísticas -->
    <div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-3 border-bottom">
        <h1 class="h2 mb-0"><i class="fas fa-stream me-2"></i>Linha de Produção</h1>
        <div class="btn-toolbar gap-2">
            <?php if(!empty($delayedOrders)): ?>
            <button class="btn btn-sm btn-danger position-relative" data-bs-toggle="modal" data-bs-target="#delayedModal">
                <i class="fas fa-exclamation-triangle me-1"></i> Atrasados
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark">
                    <?= count($delayedOrders) ?>
                </span>
            </button>
            <?php endif; ?>
            <a href="/sistemaTiago/?page=pipeline&action=settings" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-sliders-h me-1"></i> Metas
            </a>
            <a href="/sistemaTiago/?page=orders&action=create" class="btn btn-sm btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Pedido
            </a>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(52,152,219,0.15);">
                        <i class="fas fa-tasks text-primary"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Pedidos Ativos</div>
                        <div class="fw-bold fs-5"><?= $stats['total_active'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(192,57,43,0.15);">
                        <i class="fas fa-exclamation-circle text-danger"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Atrasados</div>
                        <div class="fw-bold fs-5 <?= $stats['total_delayed'] > 0 ? 'text-danger' : '' ?>"><?= $stats['total_delayed'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(39,174,96,0.15);">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Concluídos (mês)</div>
                        <div class="fw-bold fs-5"><?= $stats['completed_month'] ?></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width:45px;height:45px;background:rgba(243,156,18,0.15);">
                        <i class="fas fa-dollar-sign text-warning"></i>
                    </div>
                    <div>
                        <div class="text-muted small">Valor em Aberto</div>
                        <div class="fw-bold fs-5">R$ <?= number_format($stats['total_value'], 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pipeline Kanban Board -->
    <div class="pipeline-board-wrapper">
        <div class="pipeline-board d-flex gap-3 pb-3" style="overflow-x: auto; min-height: 500px;">
            <?php foreach ($stages as $stageKey => $stageInfo): ?>
            <?php 
                if ($stageKey === 'concluido') continue; // Concluído não aparece no kanban
                $stageOrders = $ordersByStage[$stageKey] ?? [];
                $stageGoal = isset($goals[$stageKey]) ? (int)$goals[$stageKey]['max_hours'] : 24;
            ?>
            <div class="pipeline-column flex-shrink-0" style="min-width: 280px; width: 280px;">
                <!-- Cabeçalho da Coluna -->
                <div class="pipeline-column-header rounded-top p-2 px-3 d-flex align-items-center justify-content-between" 
                     style="background: <?= $stageInfo['color'] ?>; color: #fff;">
                    <div class="d-flex align-items-center">
                        <i class="<?= $stageInfo['icon'] ?> me-2"></i>
                        <span class="fw-bold small"><?= $stageInfo['label'] ?></span>
                    </div>
                    <span class="badge bg-white text-dark rounded-pill"><?= count($stageOrders) ?></span>
                </div>
                
                <!-- Meta de tempo -->
                <div class="bg-light text-center py-1 border-start border-end" style="font-size: 0.7rem;">
                    <i class="fas fa-clock text-muted me-1"></i>Meta: <?= $stageGoal ?>h
                </div>

                <!-- Cards dos Pedidos -->
                <div class="pipeline-column-body border border-top-0 rounded-bottom bg-white p-2" 
                     style="min-height: 400px; max-height: 70vh; overflow-y: auto;"
                     data-stage="<?= $stageKey ?>">
                    
                    <?php if (empty($stageOrders)): ?>
                        <div class="text-center text-muted py-4 small">
                            <i class="fas fa-inbox d-block mb-2" style="font-size: 1.5rem;"></i>
                            Nenhum pedido
                        </div>
                    <?php else: ?>
                        <?php foreach ($stageOrders as $order): ?>
                        <?php
                            $hoursInStage = (int)$order['hours_in_stage'];
                            $isDelayed = ($stageGoal > 0 && $hoursInStage > $stageGoal);
                            $delayHours = $isDelayed ? $hoursInStage - $stageGoal : 0;
                            $priorityColors = [
                                'baixa'   => 'secondary',
                                'normal'  => 'primary',
                                'alta'    => 'warning',
                                'urgente' => 'danger',
                            ];
                            $prioColor = $priorityColors[$order['priority'] ?? 'normal'] ?? 'primary';
                        ?>
                        <div class="pipeline-card card border-0 shadow-sm mb-2 <?= $isDelayed ? 'pipeline-card-delayed' : '' ?>" 
                             data-order-id="<?= $order['id'] ?>">
                            <div class="card-body p-2">
                                <!-- Topo: Nº + Prioridade -->
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $order['id'] ?>" 
                                       class="fw-bold text-decoration-none small text-dark">
                                        #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>
                                    </a>
                                    <span class="badge bg-<?= $prioColor ?> rounded-pill" style="font-size:0.65rem;">
                                        <?= ucfirst($order['priority'] ?? 'normal') ?>
                                    </span>
                                </div>
                                
                                <!-- Cliente -->
                                <div class="small mb-1">
                                    <i class="fas fa-user text-muted me-1" style="font-size:0.7rem;"></i>
                                    <span class="text-truncate d-inline-block" style="max-width: 180px;"><?= $order['customer_name'] ?? 'Cliente removido' ?></span>
                                </div>

                                <!-- Valor -->
                                <div class="small mb-1 fw-bold">
                                    R$ <?= number_format($order['total_amount'], 2, ',', '.') ?>
                                </div>

                                <!-- Tempo na etapa -->
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="small <?= $isDelayed ? 'text-danger fw-bold' : 'text-muted' ?>">
                                        <i class="fas fa-clock me-1"></i>
                                        <?php if ($hoursInStage < 24): ?>
                                            <?= $hoursInStage ?>h
                                        <?php else: ?>
                                            <?= floor($hoursInStage / 24) ?>d <?= $hoursInStage % 24 ?>h
                                        <?php endif; ?>
                                        <?php if ($isDelayed): ?>
                                            <i class="fas fa-exclamation-triangle ms-1" title="Atrasado em <?= $delayHours ?>h"></i>
                                        <?php endif; ?>
                                    </span>
                                    
                                    <?php if ($order['deadline']): ?>
                                    <span class="small text-muted" title="Prazo">
                                        <i class="fas fa-calendar-alt me-1"></i><?= date('d/m', strtotime($order['deadline'])) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Responsável -->
                                <?php if (!empty($order['assigned_name'])): ?>
                                <div class="small text-muted mt-1">
                                    <i class="fas fa-user-check me-1" style="font-size:0.65rem;"></i><?= $order['assigned_name'] ?>
                                </div>
                                <?php endif; ?>

                                <!-- Financeiro -->
                                <?php if ($stageKey === 'financeiro' || $stageKey === 'envio'): ?>
                                <div class="mt-1">
                                    <?php 
                                    $payColors = ['pendente' => 'warning', 'parcial' => 'info', 'pago' => 'success'];
                                    $payC = $payColors[$order['payment_status'] ?? 'pendente'] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $payC ?>" style="font-size:0.6rem;">
                                        Pgto: <?= ucfirst($order['payment_status'] ?? 'pendente') ?>
                                    </span>
                                </div>
                                <?php endif; ?>

                                <!-- Botões de ação rápida -->
                                <div class="d-flex gap-1 mt-2 pt-1 border-top">
                                    <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $order['id'] ?>" 
                                       class="btn btn-sm btn-outline-primary flex-fill py-0" style="font-size:0.7rem;" title="Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php
                                        // Determinar próxima etapa
                                        $stageKeys = array_keys($stages);
                                        $currentIdx = array_search($stageKey, $stageKeys);
                                        $nextStage = ($currentIdx !== false && isset($stageKeys[$currentIdx + 1])) ? $stageKeys[$currentIdx + 1] : null;
                                    ?>
                                    <?php if ($nextStage): ?>
                                    <a href="/sistemaTiago/?page=pipeline&action=move&id=<?= $order['id'] ?>&stage=<?= $nextStage ?>" 
                                       class="btn btn-sm btn-outline-success flex-fill py-0 btn-advance-stage" style="font-size:0.7rem;" 
                                       title="Avançar para <?= $stages[$nextStage]['label'] ?>"
                                       data-order="<?= $order['id'] ?>" data-next="<?= $stages[$nextStage]['label'] ?>">
                                        <i class="fas fa-arrow-right"></i> Avançar
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Modal de Pedidos Atrasados -->
<div class="modal fade" id="delayedModal" tabindex="-1" aria-labelledby="delayedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="delayedModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Pedidos Atrasados (<?= count($delayedOrders) ?>)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3">Pedido</th>
                            <th>Cliente</th>
                            <th>Etapa</th>
                            <th>Meta</th>
                            <th>Tempo Real</th>
                            <th>Atraso</th>
                            <th class="text-end pe-3">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($delayedOrders as $dOrder): ?>
                        <tr>
                            <td class="ps-3 fw-bold">#<?= str_pad($dOrder['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td><?= $dOrder['customer_name'] ?? '—' ?></td>
                            <td>
                                <?php $dStage = $dOrder['pipeline_stage'] ?? 'contato'; ?>
                                <span class="badge" style="background:<?= $stages[$dStage]['color'] ?? '#999' ?>;">
                                    <i class="<?= $stages[$dStage]['icon'] ?? 'fas fa-circle' ?> me-1"></i>
                                    <?= $stages[$dStage]['label'] ?? $dStage ?>
                                </span>
                            </td>
                            <td><?= $dOrder['max_hours'] ?>h</td>
                            <td class="text-danger fw-bold">
                                <?php $h = (int)$dOrder['hours_in_stage']; ?>
                                <?= ($h >= 24) ? floor($h/24).'d '.($h%24).'h' : $h.'h' ?>
                            </td>
                            <td>
                                <span class="badge bg-danger rounded-pill">+<?= $dOrder['delay_hours'] ?>h</span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $dOrder['id'] ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status']) && $_GET['status'] == 'moved'): ?>
    Swal.fire({ icon: 'success', title: 'Pedido movido!', text: 'O pedido foi movido para a próxima etapa.', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    // Alerta automático de atrasados ao entrar na página
    <?php if(count($delayedOrders) > 0): ?>
    Swal.fire({
        icon: 'warning',
        title: 'Atenção!',
        html: '<b><?= count($delayedOrders) ?></b> pedido(s) estão atrasados!<br>Clique em "Ver Detalhes" para analisar.',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-eye me-1"></i> Ver Detalhes',
        cancelButtonText: 'Fechar',
        confirmButtonColor: '#c0392b'
    }).then((result) => {
        if (result.isConfirmed) {
            var modal = new bootstrap.Modal(document.getElementById('delayedModal'));
            modal.show();
        }
    });
    <?php endif; ?>

    // Confirmação antes de avançar etapa
    document.querySelectorAll('.btn-advance-stage').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            const nextStage = this.dataset.next;
            const orderId = this.dataset.order;
            Swal.fire({
                title: 'Avançar pedido?',
                html: `Mover pedido <strong>#${orderId}</strong> para <strong>${nextStage}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-arrow-right me-1"></i> Avançar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#27ae60'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });
});
</script>
