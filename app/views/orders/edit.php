<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-edit me-2"></i>Editar Pedido #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></h1>
        <a href="/sistemaTiago/?page=orders" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Voltar</a>
    </div>
    
    <form method="POST" action="/sistemaTiago/?page=orders&action=update">
        <input type="hidden" name="id" value="<?= $order['id'] ?>">
        
        <div class="row">
            <div class="col-md-8 mx-auto">
                <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold"><i class="fas fa-user-tag me-2"></i>Dados do Pedido</legend>
                    
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-muted">Cliente</label>
                            <select class="form-select" name="customer_id" required>
                                <option value="">Selecione um cliente...</option>
                                <?php foreach($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>" <?= $order['customer_id'] == $customer['id'] ? 'selected' : '' ?>><?= $customer['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="Pendente" <?= $order['status'] == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                                <option value="em_producao" <?= $order['status'] == 'em_producao' ? 'selected' : '' ?>>Em Produção</option>
                                <option value="concluido" <?= $order['status'] == 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                <option value="cancelado" <?= $order['status'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
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
