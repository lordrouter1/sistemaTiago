<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Produtos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sistemaTiago/?page=products&action=create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Novo Produto
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Preço (R$)</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= $product['name'] ?></td>
                <td><?= $product['description'] ?></td>
                <td>R$ <?= number_format($product['price'], 2, ',', '.') ?></td>
                <td><?= $product['stock_quantity'] ?></td>
                <td>
                    <button class="btn btn-sm btn-info text-white" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btn-delete" title="Excluir"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-4">Nenhum produto cadastrado.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
