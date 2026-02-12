<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Pedido #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></h1>
        <div class="d-flex gap-2">
            <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $order['id'] ?>" class="btn btn-outline-info btn-sm"><i class="fas fa-stream me-1"></i> Ver no Pipeline</a>
            <a href="/sistemaTiago/?page=orders" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Voltar</a>
        </div>
    </div>

    <?php
        // Info do pipeline para mostrar badge
        $pipelineStageMap = [
            'contato'    => ['label' => 'Contato',       'color' => '#9b59b6', 'icon' => 'fas fa-phone'],
            'orcamento'  => ['label' => 'Orçamento',     'color' => '#3498db', 'icon' => 'fas fa-file-invoice-dollar'],
            'venda'      => ['label' => 'Venda',         'color' => '#2ecc71', 'icon' => 'fas fa-handshake'],
            'producao'   => ['label' => 'Produção',      'color' => '#e67e22', 'icon' => 'fas fa-industry'],
            'preparacao' => ['label' => 'Preparação',    'color' => '#1abc9c', 'icon' => 'fas fa-boxes-packing'],
            'envio'      => ['label' => 'Envio/Entrega', 'color' => '#e74c3c', 'icon' => 'fas fa-truck'],
            'financeiro' => ['label' => 'Financeiro',    'color' => '#f39c12', 'icon' => 'fas fa-coins'],
            'concluido'  => ['label' => 'Concluído',     'color' => '#27ae60', 'icon' => 'fas fa-check-double'],
        ];
        $currentStage = $order['pipeline_stage'] ?? 'contato';
        $stageData = $pipelineStageMap[$currentStage] ?? ['label' => 'Contato', 'color' => '#999', 'icon' => 'fas fa-circle'];
    ?>

    <!-- Indicador da etapa atual no pipeline -->
    <div class="alert alert-light border shadow-sm mb-4 d-flex align-items-center justify-content-between">
        <div>
            <i class="fas fa-stream me-2 text-primary"></i>
            <strong>Etapa atual no pipeline:</strong>
            <span class="badge ms-2 px-3 py-2" style="background:<?= $stageData['color'] ?>;font-size:0.85rem;">
                <i class="<?= $stageData['icon'] ?> me-1"></i><?= $stageData['label'] ?>
            </span>
        </div>
        <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-eye me-1"></i> Gerenciar no Pipeline
        </a>
    </div>
    
    <form method="POST" action="/sistemaTiago/?page=orders&action=update">
        <input type="hidden" name="id" value="<?= $order['id'] ?>">
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold"><i class="fas fa-user-tag me-2"></i>Dados do Pedido</legend>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Cliente</label>
                            <select class="form-select" name="customer_id" required>
                                <option value="">Selecione um cliente...</option>
                                <?php foreach($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= $order['customer_id'] == $customer['id'] ? 'selected' : '' ?>><?= $customer['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="orcamento" <?= $order['status'] == 'orcamento' ? 'selected' : '' ?>>Orçamento</option>
                                <option value="Pendente" <?= $order['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="aprovado" <?= $order['status'] == 'aprovado' ? 'selected' : '' ?>>Aprovado</option>
                                <option value="em_producao" <?= $order['status'] == 'em_producao' ? 'selected' : '' ?>>Em Produção</option>
                                <option value="concluido" <?= $order['status'] == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                <option value="cancelado" <?= $order['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Prioridade</label>
                            <select class="form-select" name="priority">
                                <option value="baixa" <?= ($order['priority'] ?? '') == 'baixa' ? 'selected' : '' ?>>🟢 Baixa</option>
                                <option value="normal" <?= ($order['priority'] ?? 'normal') == 'normal' ? 'selected' : '' ?>>🔵 Normal</option>
                                <option value="alta" <?= ($order['priority'] ?? '') == 'alta' ? 'selected' : '' ?>>🟡 Alta</option>
                                <option value="urgente" <?= ($order['priority'] ?? '') == 'urgente' ? 'selected' : '' ?>>🔴 Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Valor Total (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control" name="total_amount" required value="<?= $order['total_amount'] ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Prazo de Entrega</label>
                            <input type="date" class="form-control" name="deadline" value="<?= $order['deadline'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Data de Criação</label>
                            <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($order['created_at'])) ?>" disabled>
                        </div>
                    </div>
                </fieldset>

                <div class="text-end">
                    <a href="/sistemaTiago/?page=orders" class="btn btn-secondary px-4 me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                </div>
            </div>
        </div>
    </form>
</div>
