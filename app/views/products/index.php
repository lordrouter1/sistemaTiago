<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fas fa-box-open me-2"></i>Produtos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sistemaTiago/?page=products&action=create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i> Novo Produto
        </a>
    </div>
</div>

<div class="table-responsive bg-white rounded shadow-sm">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light">
            <tr>
                <th class="py-3 ps-4">Imagem</th>
                <th class="py-3">Nome</th>
                <th class="py-3">Categoria</th>
                <th class="py-3">Preço</th>
                <th class="py-3">Estoque</th>
                <th class="py-3 text-end pe-4">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($products) > 0): ?>
            <?php foreach($products as $product): ?>
            <tr>
                <td class="ps-4">
                    <div class="bg-light rounded d-flex align-items-center justify-content-center border" style="width: 50px; height: 50px; overflow: hidden;">
                        <?php if(!empty($product['main_image_path'])): ?>
                            <img src="<?= $product['main_image_path'] ?>" class="w-100 h-100 object-fit-cover">
                        <?php else: ?>
                            <i class="fas fa-image text-secondary"></i>
                        <?php endif; ?>
                    </div>
                </td>
                <td class="fw-bold"><?= $product['name'] ?></td>
                <td>
                    <span class="badge bg-light text-dark border">
                        <?= !empty($product['category_name']) ? $product['category_name'] : 'Geral' ?>
                    </span>
                    <?php if(!empty($product['subcategory_name'])): ?>
                    <small class="text-muted d-block mt-1"><?= $product['subcategory_name'] ?></small>
                    <?php endif; ?>
                </td>
                <td class="fw-bold">R$ <?= number_format($product['price'], 2, ',', '.') ?></td>
                <td>
                    <?php if($product['stock_quantity'] > 10): ?>
                        <span class="badge bg-success px-3"><?= $product['stock_quantity'] ?> uni.</span>
                    <?php elseif($product['stock_quantity'] > 0): ?>
                        <span class="badge bg-warning text-dark px-3"><?= $product['stock_quantity'] ?> uni.</span>
                    <?php else: ?>
                        <span class="badge bg-danger px-3">Esgotado</span>
                    <?php endif; ?>
                </td>
                <td class="text-end pe-4">
                    <div class="btn-group">
                        <a href="/sistemaTiago/?page=products&action=edit&id=<?= $product['id'] ?>" class="btn btn-sm btn-outline-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger ms-1 btn-delete-product" data-id="<?= $product['id'] ?>" data-name="<?= $product['name'] ?>" title="Excluir">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted py-5">
                    <i class="fas fa-box-open fa-3x mb-3 d-block text-secondary"></i>
                    Nenhum produto cadastrado ainda.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status'])): ?>
    if (window.history.replaceState) { const url = new URL(window.location); url.searchParams.delete('status'); window.history.replaceState({}, '', url); }
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Produto salvo com sucesso!', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    document.querySelectorAll('.btn-delete-product').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            Swal.fire({
                title: 'Excluir produto?',
                html: `Deseja realmente excluir <strong>${name}</strong>?<br>Esta ação não pode ser desfeita.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `/sistemaTiago/?page=products&action=delete&id=${id}`;
                }
            });
        });
    });
});
</script>
