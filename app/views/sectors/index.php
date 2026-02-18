<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary mb-0"><i class="fas fa-industry me-2"></i>Setores de Produção</h2>
    </div>

    <div class="row">
        <!-- Form -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0 sticky-top" style="top: 90px;">
                <div class="card-header text-white p-3" style="background: #e67e22;">
                    <h6 class="mb-0">
                        <?php if(isset($editSector)): ?>
                            <i class="fas fa-edit me-2"></i>Editar Setor
                        <?php else: ?>
                            <i class="fas fa-plus me-2"></i>Novo Setor
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body p-3">
                    <form method="POST" action="/sistemaTiago/?page=sectors&action=<?= isset($editSector) ? 'update' : 'store' ?>">
                        <?php if(isset($editSector)): ?>
                            <input type="hidden" name="id" value="<?= $editSector['id'] ?>">
                        <?php endif; ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nome do Setor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required placeholder="Ex: Impressão, Corte, Acabamento" value="<?= isset($editSector) ? htmlspecialchars($editSector['name']) : '' ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Descrição</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Breve descrição do setor..."><?= isset($editSector) ? htmlspecialchars($editSector['description']) : '' ?></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Ícone</label>
                                <select class="form-select" name="icon">
                                    <?php 
                                    $icons = [
                                        'fas fa-cogs' => '⚙ Engrenagens',
                                        'fas fa-print' => '🖨 Impressão',
                                        'fas fa-cut' => '✂ Corte',
                                        'fas fa-paint-brush' => '🎨 Acabamento',
                                        'fas fa-layer-group' => '📑 Camadas',
                                        'fas fa-drafting-compass' => '📐 Design',
                                        'fas fa-palette' => '🎨 Cores',
                                        'fas fa-ruler' => '📏 Medição',
                                        'fas fa-box' => '📦 Embalagem',
                                        'fas fa-tools' => '🔧 Ferramentas',
                                        'fas fa-magic' => '✨ Arte-final',
                                        'fas fa-fire' => '🔥 Solda/Calor',
                                        'fas fa-tshirt' => '👕 Sublimação',
                                        'fas fa-vector-square' => '🔲 Recorte',
                                        'fas fa-industry' => '🏭 Produção',
                                    ];
                                    $currentIcon = isset($editSector) ? $editSector['icon'] : 'fas fa-cogs';
                                    foreach($icons as $val => $lbl): ?>
                                    <option value="<?= $val ?>" <?= ($currentIcon == $val) ? 'selected' : '' ?>><?= $lbl ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Cor</label>
                                <input type="color" class="form-control form-control-color w-100" name="color" value="<?= isset($editSector) ? $editSector['color'] : '#6c757d' ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Ordem</label>
                                <input type="number" class="form-control" name="sort_order" min="0" value="<?= isset($editSector) ? $editSector['sort_order'] : 0 ?>">
                            </div>
                            <?php if(isset($editSector)): ?>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Status</label>
                                <select class="form-select" name="is_active">
                                    <option value="1" <?= $editSector['is_active'] ? 'selected' : '' ?>>Ativo</option>
                                    <option value="0" <?= !$editSector['is_active'] ? 'selected' : '' ?>>Inativo</option>
                                </select>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn text-white" style="background: #e67e22;">
                                <i class="fas fa-save me-1"></i><?= isset($editSector) ? 'Salvar Alterações' : 'Criar Setor' ?>
                            </button>
                            <?php if(isset($editSector)): ?>
                                <a href="/sistemaTiago/?page=sectors" class="btn btn-outline-secondary btn-sm">Cancelar</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Lista -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4" style="width:50px;">Ordem</th>
                                    <th class="py-3">Setor</th>
                                    <th class="py-3 text-center" style="width:80px;">Status</th>
                                    <th class="py-3 text-end pe-4" style="width:120px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($sectors)): ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Nenhum setor cadastrado.</td></tr>
                                <?php else: ?>
                                    <?php foreach($sectors as $sector): ?>
                                    <tr class="<?= !$sector['is_active'] ? 'opacity-50' : '' ?>">
                                        <td class="ps-4 text-muted"><?= $sector['sort_order'] ?></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                     style="width:36px;height:36px;background:<?= $sector['color'] ?>20;">
                                                    <i class="<?= $sector['icon'] ?>" style="color:<?= $sector['color'] ?>;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold"><?= htmlspecialchars($sector['name']) ?></div>
                                                    <?php if($sector['description']): ?>
                                                    <div class="small text-muted"><?= htmlspecialchars($sector['description']) ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?php if($sector['is_active']): ?>
                                                <span class="badge bg-success">Ativo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inativo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="/sistemaTiago/?page=sectors&action=edit&id=<?= $sector['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger btn-delete-sector" data-id="<?= $sector['id'] ?>" data-name="<?= htmlspecialchars($sector['name']) ?>" title="Excluir">
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
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status'])): ?>
    if (window.history.replaceState) { const url = new URL(window.location); url.searchParams.delete('status'); window.history.replaceState({}, '', url); }
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Operação realizada!', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    document.querySelectorAll('.btn-delete-sector').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id, name = this.dataset.name;
            Swal.fire({
                title: 'Excluir setor?', html: `Deseja excluir o setor <strong>${name}</strong>?`, icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#c0392b', confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir', cancelButtonText: 'Cancelar'
            }).then(r => { if (r.isConfirmed) window.location = '/sistemaTiago/?page=sectors&action=delete&id=' + id; });
        });
    });
});
</script>
