<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Pedidos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sistemaTiago/?page=orders&action=create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Novo Pedido
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID do Pedido</th>
                <th>Cliente</th>
                <th>Data</th>
                <th>Valor Total</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($orders) > 0): ?>
            <?php foreach($orders as $order): ?>
            <tr>
                <td>#<?= $order['id'] ?></td>
                <td><?= $order['customer_name'] ? $order['customer_name'] : 'Cliente Não Encontrado' ?></td>
                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                <td>R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                <td>
                    <?php 
                    $badgeClass = 'bg-secondary';
                    if($order['status'] == 'concluido') $badgeClass = 'bg-success';
                    if($order['status'] == 'em_producao') $badgeClass = 'bg-warning text-dark';
                    if($order['status'] == 'cancelado') $badgeClass = 'bg-danger';
                    ?>
                    <span class="badge <?= $badgeClass ?>"><?= ucfirst(str_replace('_', ' ', $order['status'])) ?></span>
                </td>
                <td>
                    <button class="btn btn-sm btn-primary" title="Ver Detalhes"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-info text-white" title="Editar"><i class="fas fa-edit"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum pedido encontrado.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
