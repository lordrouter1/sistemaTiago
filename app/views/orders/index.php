<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-shopping-cart me-2"></i>Pedidos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sistemaTiago/?page=orders&action=create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Pedido
        </a>
    </div>
</div>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="py-3 ps-4">Nº Pedido</th>
                <th class="py-3">Cliente</th>
                <th class="py-3">Data</th>
                <th class="py-3">Valor Total</th>
                <th class="py-3">Status</th>
                <th class="py-3 text-end pe-4">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($orders) > 0): ?>
            <?php foreach($orders as $order): ?>
            <tr>
                <td class="ps-4 fw-bold">#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?></td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 0.75rem;">
                            <?= $order['customer_name'] ? strtoupper(substr($order['customer_name'], 0, 1)) : '?' ?>
                        </div>
                        <?= $order['customer_name'] ?: '<span class="text-muted">Cliente Removido</span>' ?>
                    </div>
                </td>
                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                <td class="fw-bold">R$ <?= number_format($order['total_amount'], 2, ',', '.') ?></td>
                <td>
                    <?php 
                    $statusMap = [
                        'Pendente'     => ['bg-warning text-dark', 'fas fa-clock'],
                        'pendente'     => ['bg-warning text-dark', 'fas fa-clock'],
                        'em_producao'  => ['bg-info text-white', 'fas fa-cogs'],
                        'concluido'    => ['bg-success', 'fas fa-check-circle'],
                        'cancelado'    => ['bg-danger', 'fas fa-times-circle'],
                    ];
                    $statusKey = $order['status'];
                    $badgeClass = $statusMap[$statusKey][0] ?? 'bg-secondary';
                    $statusIcon = $statusMap[$statusKey][1] ?? 'fas fa-info-circle';
                    ?>
                    <span class="badge <?= $badgeClass ?> px-3 py-2">
                        <i class="<?= $statusIcon ?> me-1"></i><?= ucfirst(str_replace('_', ' ', $order['status'])) ?>
                    </span>
                </td>
                <td class="text-end pe-4">
                    <div class="btn-group">
                        <a href="/sistemaTiago/?page=orders&action=edit&id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-1 btn-delete-order" data-id="<?= $order['id'] ?>" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-shopping-cart fa-3x mb-3 d-block text-secondary"></i>
                    Nenhum pedido encontrado.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Pedido salvo com sucesso!', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    document.querySelectorAll('.btn-delete-order').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            Swal.fire({
                title: 'Excluir pedido?',
                html: `Deseja realmente excluir o pedido <strong>#${id}</strong>?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/sistemaTiago/?page=orders&action=delete&id=${id}`;
                }
            });
        });
    });
});
</script>
