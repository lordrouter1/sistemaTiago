<?php
/**
 * Painel de Produção — Visão por Setor (Tabs)
 * Mostra todos os produtos de todos os pedidos abertos, agrupados pelo setor
 * em que se encontram. Apenas setores que o usuário tem permissão são exibidos.
 * Itens ordenados do mais antigo para o mais novo.
 * Permite concluir e retroceder setores diretamente.
 */

$sectorList = array_values($boardData);
$activeSectorId = $_GET['sector'] ?? ($sectorList[0]['id'] ?? '');
?>

<div class="container-fluid py-4 px-lg-4">

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">
                <i class="fas fa-tasks me-2 text-primary"></i>Painel de Produção
            </h1>
            <small class="text-muted">Acompanhe todos os produtos em produção, organizados por setor</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="location.reload()">
                <i class="fas fa-sync-alt me-1"></i> Atualizar
            </button>
            <a href="/sistemaTiago/?page=pipeline" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-stream me-1"></i> Pipeline
            </a>
        </div>
    </div>

    <?php if (empty($boardData)): ?>
    <!-- Sem setores / sem permissão -->
    <div class="text-center py-5">
        <i class="fas fa-industry d-block mb-3 text-muted" style="font-size:3rem;"></i>
        <h4 class="text-muted">Nenhum setor de produção disponível</h4>
        <p class="text-muted">Não há setores configurados ou você não tem permissão para acessar nenhum setor.</p>
    </div>
    <?php else: ?>

    <!-- Resumo Geral (cards de estatísticas) -->
    <?php
        $totalPendente = 0;
        $totalConcluido = 0;
        $totalItens = 0;
        foreach ($boardData as $sec) {
            $totalPendente  += $sec['counts']['pendente'];
            $totalConcluido += $sec['counts']['concluido'];
            $totalItens += count($sec['items']);
        }
    ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold mb-1"><i class="fas fa-clipboard-list me-1"></i>Total de Itens</div>
                    <div class="fs-3 fw-bold text-primary"><?= $totalItens ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold mb-1"><i class="fas fa-hourglass-half me-1"></i>Pendentes</div>
                    <div class="fs-3 fw-bold text-secondary"><?= $totalPendente ?></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 text-center">
                    <div class="text-muted small fw-bold mb-1"><i class="fas fa-check-double me-1"></i>Concluídos</div>
                    <div class="fs-3 fw-bold text-success"><?= $totalConcluido ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs por Setor -->
    <ul class="nav nav-tabs flex-wrap pb-0 mb-0" id="sectorTabs" role="tablist">
        <?php foreach ($boardData as $sid => $sector): 
            $isActive = ($sid == $activeSectorId);
            $badgeTotal = $sector['counts']['pendente'];
        ?>
        <li class="nav-item flex-shrink-0" role="presentation">
            <button class="nav-link <?= $isActive ? 'active' : '' ?> d-flex align-items-center gap-2 py-2 px-3" 
                    id="tab-sector-<?= $sid ?>" data-bs-toggle="tab" data-bs-target="#panel-sector-<?= $sid ?>" 
                    type="button" role="tab" aria-selected="<?= $isActive ? 'true' : 'false' ?>">
                <i class="<?= htmlspecialchars($sector['icon'] ?: 'fas fa-cog') ?>" style="color:<?= htmlspecialchars($sector['color'] ?: '#666') ?>;"></i>
                <span class="fw-bold"><?= htmlspecialchars($sector['name']) ?></span>
                <?php if ($badgeTotal > 0): ?>
                <span class="badge rounded-pill bg-secondary" style="font-size:0.7rem;">
                    <?= $badgeTotal ?>
                </span>
                <?php endif; ?>
            </button>
        </li>
        <?php endforeach; ?>
    </ul>

    <!-- Painéis por Setor -->
    <div class="tab-content border border-top-0 rounded-bottom bg-white shadow-sm" id="sectorTabContent">
        <?php foreach ($boardData as $sid => $sector): 
            $isActive = ($sid == $activeSectorId);
            $items = $sector['items'];

            // Separar por status (sem em_andamento)
            $pendentes   = array_filter($items, fn($i) => $i['status'] === 'pendente');
            $concluidos  = array_filter($items, fn($i) => $i['status'] === 'concluido');
        ?>
        <div class="tab-pane fade <?= $isActive ? 'show active' : '' ?> p-4" 
             id="panel-sector-<?= $sid ?>" role="tabpanel" aria-labelledby="tab-sector-<?= $sid ?>">

            <!-- Cabeçalho do painel do setor -->
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                          style="width:40px;height:40px;background:<?= htmlspecialchars($sector['color'] ?: '#666') ?>;color:#fff;font-size:1rem;">
                        <i class="<?= htmlspecialchars($sector['icon'] ?: 'fas fa-cog') ?>"></i>
                    </span>
                    <div>
                        <h5 class="mb-0 fw-bold"><?= htmlspecialchars($sector['name']) ?></h5>
                        <small class="text-muted">
                            <?= count($pendentes) ?> pendentes · 
                            <?= count($concluidos) ?> concluídos
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <span class="badge bg-secondary"><i class="fas fa-hourglass-half me-1"></i><?= count($pendentes) ?></span>
                    <span class="badge bg-success"><i class="fas fa-check me-1"></i><?= count($concluidos) ?></span>
                </div>
            </div>

            <?php if (empty($items)): ?>
            <!-- Setor vazio -->
            <div class="text-center py-5">
                <i class="fas fa-inbox d-block mb-2 text-muted" style="font-size:2.5rem;opacity:0.4;"></i>
                <p class="text-muted mb-0">Nenhum produto neste setor no momento</p>
            </div>
            <?php else: ?>

            <!-- ════ ITENS PENDENTES (Setor Atual) ════ -->
            <?php if (!empty($pendentes)): ?>
            <h6 class="text-secondary fw-bold mb-2">
                <i class="fas fa-hourglass-half me-1"></i> Pendentes (<?= count($pendentes) ?>)
            </h6>
            <div class="row g-3 mb-4">
                <?php foreach ($pendentes as $item): 
                    // O model já calculou se há setor anterior concluído
                    $hasCompletedPrevious = !empty($item['has_previous_concluded']);
                ?>
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-start border-4 h-100 board-item-card" style="border-color:<?= htmlspecialchars($sector['color'] ?: '#e67e22') ?> !important;">
                        <div class="card-body p-3">
                            <!-- Linha 1: Pedido + Prioridade -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $item['order_id'] ?>" 
                                       class="text-decoration-none fw-bold" title="Abrir pedido">
                                        <i class="fas fa-file-alt me-1 text-primary"></i>#<?= str_pad($item['order_id'], 4, '0', STR_PAD_LEFT) ?>
                                    </a>
                                    <?php if (!empty($item['priority']) && $item['priority'] !== 'normal'): ?>
                                    <span class="badge ms-1 <?= $item['priority'] === 'urgente' ? 'bg-danger' : ($item['priority'] === 'alta' ? 'bg-warning text-dark' : 'bg-info') ?>" style="font-size:0.65rem;">
                                        <?= ucfirst($item['priority']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-secondary" style="font-size:0.65rem;">
                                    <i class="fas fa-hourglass-half me-1"></i>Pendente
                                </span>
                            </div>

                            <!-- Produto -->
                            <h6 class="mb-1 fw-bold text-truncate" title="<?= htmlspecialchars($item['product_name']) ?>">
                                <?= htmlspecialchars($item['product_name']) ?>
                            </h6>

                            <!-- Detalhes -->
                            <div class="small text-muted mb-2">
                                <?php if (!empty($item['customer_name'])): ?>
                                <span class="me-2"><i class="fas fa-user me-1"></i><?= htmlspecialchars($item['customer_name']) ?></span>
                                <?php endif; ?>
                                <span class="me-2"><i class="fas fa-cubes me-1"></i>Qtd: <?= $item['quantity'] ?></span>
                                <span><i class="fas fa-calendar-plus me-1"></i><?= date('d/m H:i', strtotime($item['order_created_at'])) ?></span>
                            </div>

                            <?php if (!empty($item['deadline'])): ?>
                            <?php
                                $deadlineDate = strtotime($item['deadline']);
                                $isOverdue = ($deadlineDate < time());
                            ?>
                            <div class="small mb-2 <?= $isOverdue ? 'text-danger fw-bold' : 'text-muted' ?>">
                                <i class="fas fa-calendar-alt me-1"></i>Prazo: <?= date('d/m/Y', $deadlineDate) ?>
                                <?php if ($isOverdue): ?>
                                <span class="badge bg-danger ms-1" style="font-size:0.6rem;">ATRASADO</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>

                            <!-- Botões de Ação: Concluir + Retroceder (se não for o primeiro setor) -->
                            <div class="d-flex justify-content-between mt-auto pt-2 border-top">
                                <div class="d-flex gap-1">
                                    <?php if ($hasCompletedPrevious): ?>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-board-action"
                                            data-order-id="<?= $item['order_id'] ?>"
                                            data-item-id="<?= $item['order_item_id'] ?>"
                                            data-sector-id="<?= $item['sector_id'] ?>"
                                            data-action="revert"
                                            data-sector-name="<?= htmlspecialchars($sector['name']) ?>">
                                        <i class="fas fa-undo me-1"></i> Retroceder
                                    </button>
                                    <?php endif; ?>
                                    <?php $logCount = $itemLogCounts[$item['order_item_id']] ?? 0; ?>
                                    <button type="button" class="btn btn-sm btn-outline-info btn-open-log position-relative"
                                            data-order-id="<?= $item['order_id'] ?>"
                                            data-item-id="<?= $item['order_item_id'] ?>"
                                            data-product-name="<?= htmlspecialchars($item['product_name']) ?>"
                                            data-customer-name="<?= htmlspecialchars($item['customer_name'] ?? '') ?>"
                                            data-quantity="<?= $item['quantity'] ?>"
                                            title="Histórico do produto">
                                        <i class="fas fa-history"></i>
                                        <?php if ($logCount > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info" style="font-size:0.55rem;">
                                            <?= $logCount ?>
                                        </span>
                                        <?php endif; ?>
                                    </button>
                                </div>
                                <button type="button" class="btn btn-sm btn-success btn-board-action"
                                        data-order-id="<?= $item['order_id'] ?>"
                                        data-item-id="<?= $item['order_item_id'] ?>"
                                        data-sector-id="<?= $item['sector_id'] ?>"
                                        data-action="advance"
                                        data-sector-name="<?= htmlspecialchars($sector['name']) ?>">
                                    <i class="fas fa-check me-1"></i> Concluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- ════ ITENS CONCLUÍDOS ════ -->
            <?php if (!empty($concluidos)): ?>
            <details class="mb-3">
                <summary class="text-success fw-bold mb-2 cursor-pointer" style="cursor:pointer;">
                    <i class="fas fa-check-double me-1"></i> Concluídos neste setor (<?= count($concluidos) ?>)
                </summary>
                <div class="row g-3 mt-2">
                    <?php foreach ($concluidos as $item): ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card border-start border-4 h-100 board-item-card" style="border-color:#27ae60 !important; opacity:0.7;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $item['order_id'] ?>" 
                                           class="text-decoration-none fw-bold" title="Abrir pedido">
                                            <i class="fas fa-file-alt me-1 text-primary"></i>#<?= str_pad($item['order_id'], 4, '0', STR_PAD_LEFT) ?>
                                        </a>
                                    </div>
                                    <span class="badge bg-success" style="font-size:0.65rem;">
                                        <i class="fas fa-check me-1"></i>Concluído
                                    </span>
                                </div>
                                <h6 class="mb-1 fw-bold text-truncate text-success" title="<?= htmlspecialchars($item['product_name']) ?>">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </h6>
                                <div class="small text-muted mb-1">
                                    <?php if (!empty($item['customer_name'])): ?>
                                    <span class="me-2"><i class="fas fa-user me-1"></i><?= htmlspecialchars($item['customer_name']) ?></span>
                                    <?php endif; ?>
                                    <span class="me-2"><i class="fas fa-cubes me-1"></i>Qtd: <?= $item['quantity'] ?></span>
                                </div>
                                <?php if (!empty($item['completed_at'])): ?>
                                <div class="small text-muted">
                                    <i class="fas fa-check-circle text-success me-1"></i>
                                    Concluído em <?= date('d/m/Y H:i', strtotime($item['completed_at'])) ?>
                                    <?php if (!empty($item['completed_by_name'])): ?>
                                    por <strong><?= htmlspecialchars($item['completed_by_name']) ?></strong>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <!-- Botão de retroceder -->
                                <div class="d-flex justify-content-between mt-2 pt-2 border-top">
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-board-action"
                                            data-order-id="<?= $item['order_id'] ?>"
                                            data-item-id="<?= $item['order_item_id'] ?>"
                                            data-sector-id="<?= $item['sector_id'] ?>"
                                            data-action="revert"
                                            data-sector-name="<?= htmlspecialchars($sector['name']) ?>">
                                        <i class="fas fa-undo me-1"></i> Retroceder
                                    </button>
                                    <?php $logCount = $itemLogCounts[$item['order_item_id']] ?? 0; ?>
                                    <button type="button" class="btn btn-sm btn-outline-info btn-open-log position-relative"
                                            data-order-id="<?= $item['order_id'] ?>"
                                            data-item-id="<?= $item['order_item_id'] ?>"
                                            data-product-name="<?= htmlspecialchars($item['product_name']) ?>"
                                            data-customer-name="<?= htmlspecialchars($item['customer_name'] ?? '') ?>"
                                            data-quantity="<?= $item['quantity'] ?>"
                                            title="Histórico do produto">
                                        <i class="fas fa-history"></i>
                                        <?php if ($logCount > 0): ?>
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-info" style="font-size:0.55rem;">
                                            <?= $logCount ?>
                                        </span>
                                        <?php endif; ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </details>
            <?php endif; ?>

            <?php endif; /* end if empty items */ ?>
        </div>
        <?php endforeach; ?>
    </div>

    <?php endif; /* end if empty boardData */ ?>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- ═══ MODAL: Histórico do Produto (Logs, Imagens, PDFs)         ═══ -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal fade" id="itemLogModal" tabindex="-1" aria-labelledby="itemLogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title" id="itemLogModalLabel">
                    <i class="fas fa-history me-2"></i>Histórico do Produto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Info do produto -->
                <div class="bg-light p-3 border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <a href="#" id="logModalOrderLink" class="badge bg-primary rounded-pill px-3 py-2 text-white text-decoration-none" title="Abrir pedido no Pipeline">
                                <span id="logModalOrderBadge"></span> <i class="fas fa-external-link-alt ms-1" style="font-size:0.6rem;"></i>
                            </a>
                            <div>
                                <h6 class="mb-0 fw-bold" id="logModalProductName"></h6>
                                <small class="text-muted" id="logModalProductInfo"></small>
                            </div>
                        </div>
                        <a href="#" id="logModalDetailLink" class="btn btn-sm btn-outline-primary" title="Ver detalhes do pedido">
                            <i class="fas fa-file-alt me-1"></i> Ver Pedido
                        </a>
                    </div>
                </div>

                <!-- Formulário de novo log -->
                <div class="p-3 border-bottom bg-white">
                    <form id="formAddItemLog" enctype="multipart/form-data">
                        <input type="hidden" id="logOrderId" name="order_id">
                        <input type="hidden" id="logOrderItemId" name="order_item_id">
                        <div class="mb-2">
                            <textarea class="form-control form-control-sm" id="logMessage" name="message" rows="2" 
                                      placeholder="Adicione uma observação, registro de erro, instrução..."></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <label class="btn btn-sm btn-outline-secondary mb-0" for="logFile" title="Anexar imagem ou PDF">
                                    <i class="fas fa-paperclip me-1"></i> Anexar arquivo
                                </label>
                                <input type="file" class="d-none" id="logFile" name="file" accept="image/*,.pdf">
                                <small class="text-muted d-none" id="logFileLabel"></small>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-plus me-1"></i> Adicionar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Lista de logs -->
                <div class="p-3" id="logListContainer">
                    <div class="text-center py-4 text-muted" id="logListLoading">
                        <i class="fas fa-spinner fa-spin me-2"></i>Carregando histórico...
                    </div>
                    <div id="logListContent"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos específicos do painel -->
<style>
.board-item-card {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.board-item-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
#sectorTabs {
    border-bottom: 2px solid #dee2e6;
}
#sectorTabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-size: 0.9rem;
    transition: all 0.2s ease;
    white-space: nowrap;
}
#sectorTabs .nav-link:hover {
    color: #333;
    border-bottom-color: #ccc;
}
#sectorTabs .nav-link.active {
    color: #333;
    font-weight: 700;
    border-bottom-color: var(--accent-color, #3498db);
    background: transparent;
}
/* Hover effect for board items */
</style>

<script>
// ═══════════════════════════════════════════════════════
// ═══ PAINEL DE PRODUÇÃO — Ações AJAX por Setor     ═══
// ═══════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(el);
        }
    });

    // Salvar tab ativa na URL para persistir ao recarregar
    document.querySelectorAll('#sectorTabs button[data-bs-toggle="tab"]').forEach(function(tabBtn) {
        tabBtn.addEventListener('shown.bs.tab', function(e) {
            var sectorId = e.target.id.replace('tab-sector-', '');
            var url = new URL(window.location.href);
            url.searchParams.set('sector', sectorId);
            window.history.replaceState({}, '', url.toString());
        });
    });

    // Botões de ação (Concluir / Retroceder)
    document.querySelectorAll('.btn-board-action').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var orderId    = this.dataset.orderId;
            var itemId     = this.dataset.itemId;
            var sectorId   = this.dataset.sectorId;
            var action     = this.dataset.action;
            var sectorName = this.dataset.sectorName;
            var btnEl      = this;

            var isRevert = (action === 'revert');
            var confirmTitle, confirmText, confirmIcon, confirmBtn, confirmColor;

            if (isRevert) {
                confirmTitle = 'Retroceder setor?';
                confirmText  = 'Deseja retroceder ao setor anterior do pedido #' + orderId + '?<br><small class="text-muted">O último setor concluído será revertido.</small>';
                confirmIcon  = 'warning';
                confirmBtn   = '<i class="fas fa-undo me-1"></i> Retroceder';
                confirmColor = '#e67e22';
            } else {
                confirmTitle = 'Concluir setor?';
                confirmText  = 'Marcar <strong>' + sectorName + '</strong> como concluído no pedido #' + orderId + '?';
                confirmIcon  = 'success';
                confirmBtn   = '<i class="fas fa-check me-1"></i> Concluir';
                confirmColor = '#27ae60';
            }

            Swal.fire({
                title: confirmTitle,
                html: confirmText,
                icon: confirmIcon,
                showCancelButton: true,
                confirmButtonText: confirmBtn,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: confirmColor
            }).then(function(result) {
                if (result.isConfirmed) {
                    btnEl.disabled = true;
                    var originalHTML = btnEl.innerHTML;
                    btnEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';

                    fetch('/sistemaTiago/?page=production_board&action=moveSector', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'order_id=' + orderId + '&order_item_id=' + itemId + '&sector_id=' + sectorId + '&move_action=' + action
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            var toastMixin = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                didOpen: function(toast) {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });
                            var msg = isRevert ? 'Setor retrocedido!' : 'Setor concluído!';
                            toastMixin.fire({ icon: 'success', title: msg });
                            setTimeout(function() { location.reload(); }, 800);
                        } else {
                            btnEl.disabled = false;
                            btnEl.innerHTML = originalHTML;
                            Swal.fire({
                                icon: 'error',
                                title: 'Erro',
                                text: data.message || 'Não foi possível processar.',
                                timer: 3000
                            });
                        }
                    })
                    .catch(function(err) {
                        btnEl.disabled = false;
                        btnEl.innerHTML = originalHTML;
                        console.error('Erro:', err);
                        Swal.fire({ icon: 'error', title: 'Erro de conexão', timer: 2000, showConfirmButton: false });
                    });
                }
            });
        });
    });

    // Auto-refresh a cada 30 segundos para manter painel atualizado
    setInterval(function() {
        // Só recarregar se não houver modal/swal aberto
        if (!document.querySelector('.swal2-container') && !document.querySelector('.modal.show')) {
            location.reload();
        }
    }, 30000);

    // ═══════════════════════════════════════════
    // ═══ MODAL DE HISTÓRICO DO PRODUTO      ═══
    // ═══════════════════════════════════════════

    var logModal = new bootstrap.Modal(document.getElementById('itemLogModal'));

    // Abrir modal ao clicar no botão de histórico
    document.querySelectorAll('.btn-open-log').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var orderId = this.dataset.orderId;
            var itemId = this.dataset.itemId;
            var productName = this.dataset.productName;
            var customerName = this.dataset.customerName;
            var quantity = this.dataset.quantity;

            document.getElementById('logOrderId').value = orderId;
            document.getElementById('logOrderItemId').value = itemId;
            document.getElementById('logModalOrderBadge').textContent = '#' + orderId.padStart(4, '0');
            document.getElementById('logModalProductName').textContent = productName;
            document.getElementById('logModalProductInfo').textContent = 
                (customerName ? customerName + ' · ' : '') + 'Qtd: ' + quantity;

            // Atualizar links de acesso rápido ao pedido
            var detailUrl = '/sistemaTiago/?page=pipeline&action=detail&id=' + orderId;
            document.getElementById('logModalOrderLink').href = detailUrl;
            document.getElementById('logModalDetailLink').href = detailUrl;

            // Limpar form
            document.getElementById('logMessage').value = '';
            document.getElementById('logFile').value = '';
            document.getElementById('logFileLabel').classList.add('d-none');

            loadItemLogs(itemId);
            logModal.show();
        });
    });

    // Mostrar nome do arquivo selecionado
    document.getElementById('logFile').addEventListener('change', function() {
        var label = document.getElementById('logFileLabel');
        if (this.files.length > 0) {
            label.textContent = this.files[0].name;
            label.classList.remove('d-none');
        } else {
            label.classList.add('d-none');
        }
    });

    // Enviar novo log (AJAX com upload)
    document.getElementById('formAddItemLog').addEventListener('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';

        fetch('/sistemaTiago/?page=production_board&action=addItemLog', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i> Adicionar';
            if (data.success) {
                document.getElementById('logMessage').value = '';
                document.getElementById('logFile').value = '';
                document.getElementById('logFileLabel').classList.add('d-none');
                loadItemLogs(document.getElementById('logOrderItemId').value);
                Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true })
                    .fire({ icon: 'success', title: 'Registro adicionado!' });
            } else {
                Swal.fire({ icon: 'error', title: 'Erro', text: data.message || 'Não foi possível adicionar.', timer: 3000 });
            }
        })
        .catch(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i> Adicionar';
            Swal.fire({ icon: 'error', title: 'Erro de conexão', timer: 2000, showConfirmButton: false });
        });
    });

    // Carregar logs do item
    function loadItemLogs(itemId) {
        var loading = document.getElementById('logListLoading');
        var content = document.getElementById('logListContent');
        loading.classList.remove('d-none');
        content.innerHTML = '';

        fetch('/sistemaTiago/?page=production_board&action=getItemLogs&order_item_id=' + itemId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            loading.classList.add('d-none');
            if (data.success && data.logs.length > 0) {
                var html = '';
                data.logs.forEach(function(log) {
                    html += renderLogEntry(log);
                });
                content.innerHTML = html;
                // Bind delete buttons
                content.querySelectorAll('.btn-delete-log').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        deleteItemLog(this.dataset.logId, itemId);
                    });
                });
            } else {
                content.innerHTML = '<div class="text-center text-muted py-4">' +
                    '<i class="fas fa-clipboard d-block mb-2" style="font-size:2rem;opacity:0.4;"></i>' +
                    '<p class="mb-0">Nenhum registro ainda.<br><small>Adicione observações, imagens ou PDFs acima.</small></p></div>';
            }
        })
        .catch(function() {
            loading.classList.add('d-none');
            content.innerHTML = '<div class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle me-1"></i>Erro ao carregar.</div>';
        });
    }

    // Renderizar uma entrada de log
    function renderLogEntry(log) {
        var date = new Date(log.created_at);
        var dateStr = date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', {hour:'2-digit', minute:'2-digit'});
        var userName = log.user_name || 'Sistema';
        var isImage = log.file_type && log.file_type.startsWith('image/');
        var isPdf = log.file_type === 'application/pdf';

        var html = '<div class="d-flex gap-2 mb-3 pb-3 border-bottom log-entry">';
        html += '<div class="flex-shrink-0">';
        html += '<div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">';
        if (isImage) {
            html += '<i class="fas fa-image text-primary"></i>';
        } else if (isPdf) {
            html += '<i class="fas fa-file-pdf text-danger"></i>';
        } else {
            html += '<i class="fas fa-comment text-primary"></i>';
        }
        html += '</div></div>';
        html += '<div class="flex-grow-1">';
        html += '<div class="d-flex justify-content-between align-items-start">';
        html += '<div class="small fw-bold">' + userName + '</div>';
        html += '<div class="d-flex align-items-center gap-1">';
        html += '<span class="text-muted" style="font-size:0.65rem;">' + dateStr + '</span>';
        html += '<button type="button" class="btn btn-sm p-0 text-danger btn-delete-log" data-log-id="' + log.id + '" title="Excluir" style="font-size:0.7rem;line-height:1;"><i class="fas fa-times"></i></button>';
        html += '</div></div>';

        // Mensagem
        if (log.message) {
            html += '<div class="small mt-1" style="white-space:pre-wrap;">' + escapeHtml(log.message) + '</div>';
        }

        // Arquivo
        if (log.file_path) {
            if (isImage) {
                html += '<div class="mt-2">';
                html += '<a href="' + log.file_path + '" target="_blank" title="' + escapeHtml(log.file_name) + '">';
                html += '<img src="' + log.file_path + '" class="rounded border" style="max-width:100%;max-height:200px;cursor:pointer;" alt="' + escapeHtml(log.file_name) + '">';
                html += '</a>';
                html += '<div class="small text-muted mt-1"><i class="fas fa-image me-1"></i>' + escapeHtml(log.file_name) + '</div>';
                html += '</div>';
            } else if (isPdf) {
                html += '<div class="mt-2">';
                html += '<a href="' + log.file_path + '" target="_blank" class="btn btn-sm btn-outline-danger">';
                html += '<i class="fas fa-file-pdf me-1"></i>' + escapeHtml(log.file_name) + '</a>';
                html += '</div>';
            }
        }

        html += '</div></div>';
        return html;
    }

    // Excluir log
    function deleteItemLog(logId, itemId) {
        Swal.fire({
            title: 'Excluir registro?',
            text: 'Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#c0392b',
            confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
            cancelButtonText: 'Cancelar'
        }).then(function(result) {
            if (result.isConfirmed) {
                fetch('/sistemaTiago/?page=production_board&action=deleteItemLog', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'log_id=' + logId
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        loadItemLogs(itemId);
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true })
                            .fire({ icon: 'success', title: 'Registro excluído!' });
                    }
                });
            }
        });
    }

    // Escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
