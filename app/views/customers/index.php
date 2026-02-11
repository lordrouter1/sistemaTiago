<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Clientes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/sistemaTiago/?page=customers&action=create" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Novo Cliente
        </a>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($customers) > 0): ?>
            <?php foreach($customers as $customer): ?>
            <tr>
                <td><?= $customer['id'] ?></td>
                <td><?= $customer['name'] ?></td>
                <td><?= $customer['email'] ?></td>
                <td><?= $customer['phone'] ?></td>
                <td>
                    <button class="btn btn-sm btn-info text-white"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btn-delete"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted py-4">Nenhum cliente cadastrado.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Modal Cadastro de Cliente -->
<!-- Modal removido -->
