<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 sticky-top" style="top: 90px; z-index: 1;">
            <div class="card-header bg-primary text-white p-3">
                <h5 class="mb-0">
                    <?php if(isset($editGroup)): ?>
                        <i class="fas fa-edit me-2"></i>Editar Grupo
                    <?php else: ?>
                        <i class="fas fa-layer-group me-2"></i>Novo Grupo
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="/sistemaTiago/?page=users&action=<?= isset($editGroup) ? 'updateGroup' : 'createGroup' ?>" method="POST">
                    <?php if(isset($editGroup)): ?>
                        <input type="hidden" name="id" value="<?= $editGroup['id'] ?>">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Nome do Grupo</label>
                        <input type="text" class="form-control" name="name" required placeholder="Ex: Financeiro" value="<?= isset($editGroup) ? $editGroup['name'] : '' ?>">
                        <div class="form-text">Identificador único do grupo.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Descrição</label>
                        <textarea class="form-control" name="description" rows="2" placeholder="Breve descrição do acesso..."><?= isset($editGroup) ? $editGroup['description'] : '' ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">Permissões de Acesso</label>
                        <div class="border rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            <?php 
                            // Carrega páginas dinamicamente do registro centralizado
                            $menuPages = require 'app/config/menu.php';
                            
                            // Filtra apenas as páginas que possuem controle de permissão
                            $pages = [];
                            foreach ($menuPages as $key => $info) {
                                if (!empty($info['permission'])) {
                                    $pages[$key] = $info['label'];
                                }
                            }
                            
                            $currentPermissions = isset($editGroup) ? $editGroup['permissions'] : [];
                            
                            foreach($pages as $key => $label): 
                                $checked = in_array($key, $currentPermissions) ? 'checked' : '';
                                $icon = $menuPages[$key]['icon'] ?? 'fas fa-circle';
                            ?>
                            <div class="form-check mb-2 d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" name="permissions[]" value="<?= $key ?>" id="perm_<?= $key ?>" <?= $checked ?>>
                                <label class="form-check-label d-flex align-items-center" for="perm_<?= $key ?>">
                                    <i class="<?= $icon ?> me-2 text-primary" style="width:18px;text-align:center;font-size:0.85rem;"></i>
                                    <?= $label ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="form-text"><i class="fas fa-info-circle me-1"></i>Novas páginas adicionadas ao sistema aparecerão automaticamente aqui.</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary fw-bold">
                            <?php if(isset($editGroup)): ?>
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            <?php else: ?>
                                <i class="fas fa-plus me-2"></i>Criar Grupo
                            <?php endif; ?>
                        </button>
                        
                        <?php if(isset($editGroup)): ?>
                            <a href="/sistemaTiago/?page=users&action=groups" class="btn btn-outline-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-primary mb-0"><i class="fas fa-list-ul me-2"></i>Grupos Existentes</h4>
            <a href="/sistemaTiago/?page=users" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Voltar para Usuários</a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4" style="width: 30%;">Grupo</th>
                                <th class="py-3">Permissões</th>
                                <th class="py-3 text-end pe-4" style="width: 15%;">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($groups)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">Nenhum grupo cadastrado ainda.</td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                // Ensure we display unique groups if the fetchAll returns duplicates for some reason (e.g. JOIN issues in model)
                                // Although readAll in userGroup model is a simple SELECT *.
                                foreach($groups as $group2):
                                ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold fs-6"><?= $group2['name'] ?></div>
                                        <div class="small text-muted"><?= $group2['description'] ?></div>
                                    </td>
                                    <td>
                                        <?php if(empty($group2['permissions'])): ?>
                                            <span class="text-muted small fst-italic">Sem permissões específicas</span>
                                        <?php else: ?>
                                            <div class="d-flex flex-wrap gap-1">
                                                <?php foreach($group2['permissions'] as $perm): 
                                                    $permIcon = $menuPages[$perm]['icon'] ?? 'fas fa-circle';
                                                    $permLabel = $pages[$perm] ?? $perm;
                                                ?>
                                                    <span class="badge bg-light text-dark border">
                                                        <i class="<?= $permIcon ?> me-1" style="font-size:0.7rem;"></i><?= $permLabel ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="/sistemaTiago/?page=users&action=groups&manage_id=<?= $group2['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar Grupo">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-group" data-id="<?= $group2['id'] ?>" data-name="<?= $group2['name'] ?>" title="Excluir Grupo">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: 'Grupo salvo com sucesso!',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>

    // Exclusão de grupo com SweetAlert2
    document.querySelectorAll('.btn-delete-group').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            Swal.fire({
                title: 'Excluir grupo?',
                html: `Deseja realmente excluir o grupo <strong>${name}</strong>?<br>Isso pode afetar usuários vinculados.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Sim, excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Criar form dinâmico para POST
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/sistemaTiago/?page=users&action=deleteGroup';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    input.value = id;
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
</script>
