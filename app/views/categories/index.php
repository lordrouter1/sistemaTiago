<?php
    $editCatSectorIds = isset($editCategorySectors) ? array_column($editCategorySectors, 'sector_id') : [];
    $editSubSectorIds = isset($editSubcategorySectors) ? array_column($editSubcategorySectors, 'sector_id') : [];
?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary mb-0"><i class="fas fa-folder-open me-2"></i>Categorias e Subcategorias</h2>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link <?= (!isset($_GET['tab']) || $_GET['tab'] === 'categories') ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-categories" type="button">
                <i class="fas fa-folder me-1"></i>Categorias
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link <?= (isset($_GET['tab']) && $_GET['tab'] === 'subcategories') ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#tab-subcategories" type="button">
                <i class="fas fa-sitemap me-1"></i>Subcategorias
            </button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- ═══════ ABA CATEGORIAS ═══════ -->
        <div class="tab-pane fade <?= (!isset($_GET['tab']) || $_GET['tab'] === 'categories') ? 'show active' : '' ?>" id="tab-categories">
            <div class="row">
                <!-- Form -->
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary p-3">
                            <h6 class="mb-0 text-white ">
                                <?php if(isset($editCategory)): ?>
                                    <i class="fas fa-edit me-2"></i>Editar Categoria
                                <?php else: ?>
                                    <i class="fas fa-plus me-2"></i>Nova Categoria
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <form method="POST" action="?page=categories&action=<?= isset($editCategory) ? 'update' : 'store' ?>">
                                <?php if(isset($editCategory)): ?>
                                    <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Nome da Categoria</label>
                                    <input type="text" class="form-control" name="name" required placeholder="Ex: Impressão Digital" value="<?= isset($editCategory) ? htmlspecialchars($editCategory['name']) : '' ?>">
                                </div>

                                <!-- Setores de Produção -->
                                <?php if (!empty($allSectors)): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small"><i class="fas fa-industry me-1 text-success"></i>Setores de Produção</label>
                                    <p class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.5rem;">Selecione e arraste para ordenar os setores padrão desta categoria.</p>
                                    
                                    <!-- Setores selecionados (ordenáveis) -->
                                    <div id="cat-sectors-selected" class="sectors-sortable-list mb-2" style="min-height: 36px; border: 1px dashed #dee2e6; border-radius: 0.375rem; padding: 4px;">
                                        <?php foreach ($editCatSectorIds as $sid): 
                                            $sector = null;
                                            foreach ($allSectors as $s) { if ($s['id'] == $sid) { $sector = $s; break; } }
                                            if (!$sector) continue;
                                        ?>
                                        <div class="sector-item badge d-inline-flex align-items-center me-1 mb-1 px-2 py-1" data-id="<?= $sector['id'] ?>" style="background-color: <?= $sector['color'] ?>; cursor: grab; font-size: 0.8rem;">
                                            <i class="<?= $sector['icon'] ?> me-1"></i>
                                            <?= htmlspecialchars($sector['name']) ?>
                                            <button type="button" class="btn-close btn-close-white ms-1 sector-remove" style="font-size: 0.5rem;" data-id="<?= $sector['id'] ?>"></button>
                                            <input type="hidden" name="sector_ids[]" value="<?= $sector['id'] ?>">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Setores disponíveis para adicionar -->
                                    <div class="d-flex flex-wrap gap-1" id="cat-sectors-available">
                                        <?php foreach ($allSectors as $sector): 
                                            $isSelected = in_array($sector['id'], $editCatSectorIds);
                                        ?>
                                        <button type="button" class="btn btn-sm sector-add-btn <?= $isSelected ? 'd-none' : '' ?>" 
                                                data-id="<?= $sector['id'] ?>" data-name="<?= htmlspecialchars($sector['name']) ?>"
                                                data-icon="<?= $sector['icon'] ?>" data-color="<?= $sector['color'] ?>"
                                                style="border: 1px solid <?= $sector['color'] ?>; color: <?= $sector['color'] ?>; font-size: 0.75rem; padding: 2px 8px;">
                                            <i class="fas fa-plus me-1" style="font-size: 0.6rem;"></i>
                                            <i class="<?= $sector['icon'] ?> me-1"></i><?= htmlspecialchars($sector['name']) ?>
                                        </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Grades / Variações Padrão da Categoria -->
                                <?php
                                    $entityType = 'category';
                                    $entityGrades = $editCategoryGrades ?? [];
                                    $entityCombinations = $editCategoryCombinations ?? [];
                                    // $gradeTypes is already available from controller
                                    require 'app/views/categories/_grades_partial.php';
                                ?>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i><?= isset($editCategory) ? 'Salvar' : 'Criar Categoria' ?>
                                    </button>
                                    <?php if(isset($editCategory)): ?>
                                        <a href="?page=categories" class="btn btn-outline-secondary btn-sm">Cancelar</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Lista -->
                <div class="col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3 ps-4">Categoria</th>
                                            <th class="py-3">Setores</th>
                                            <th class="py-3 text-center" style="width:80px;">Subs</th>
                                            <th class="py-3 text-center" style="width:80px;">Prod.</th>
                                            <th class="py-3 text-end pe-4" style="width:100px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($categories)): ?>
                                            <tr><td colspan="5" class="text-center py-5 text-muted">Nenhuma categoria cadastrada.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($categories as $cat): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <i class="fas fa-folder text-warning me-2"></i>
                                                    <strong><?= htmlspecialchars($cat['name']) ?></strong>
                                                    <?php if(!empty($categoryGradesMap[$cat['id']])): ?>
                                                        <span class="badge bg-info ms-1" style="font-size:0.6rem;" title="Possui grades padrão">
                                                            <i class="fas fa-th-large me-1"></i>Grades
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $catSectorsData = isset($categorySectorsMap[$cat['id']]) ? $categorySectorsMap[$cat['id']] : [];
                                                    ?>
                                                    <?php if(!empty($catSectorsData)): ?>
                                                        <?php foreach($catSectorsData as $cs): ?>
                                                            <span class="badge me-1" style="background-color: <?= $cs['color'] ?>; font-size: 0.65rem;">
                                                                <i class="<?= $cs['icon'] ?> me-1"></i><?= htmlspecialchars($cs['sector_name']) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted" style="font-size: 0.75rem;">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info"><?= $cat['sub_count'] ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary"><?= $cat['product_count'] ?></span>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="?page=categories&action=edit&id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger btn-delete-cat" data-id="<?= $cat['id'] ?>" data-name="<?= htmlspecialchars($cat['name']) ?>" data-products="<?= $cat['product_count'] ?>" title="Excluir">
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

        <!-- ═══════ ABA SUBCATEGORIAS ═══════ -->
        <div class="tab-pane fade <?= (isset($_GET['tab']) && $_GET['tab'] === 'subcategories') ? 'show active' : '' ?>" id="tab-subcategories">
            <div class="row">
                <!-- Form -->
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-success p-3">
                            <h6 class="mb-0 text-white ">
                                <?php if(isset($editSubcategory)): ?>
                                    <i class="fas fa-edit me-2"></i>Editar Subcategoria
                                <?php else: ?>
                                    <i class="fas fa-plus me-2"></i>Nova Subcategoria
                                <?php endif; ?>
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <form method="POST" action="?page=categories&action=<?= isset($editSubcategory) ? 'updateSub' : 'storeSub' ?>">
                                <?php if(isset($editSubcategory)): ?>
                                    <input type="hidden" name="id" value="<?= $editSubcategory['id'] ?>">
                                <?php endif; ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Categoria</label>
                                    <select class="form-select" name="category_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (isset($editSubcategory) && $editSubcategory['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Nome da Subcategoria</label>
                                    <input type="text" class="form-control" name="name" required placeholder="Ex: Banner Lona" value="<?= isset($editSubcategory) ? htmlspecialchars($editSubcategory['name']) : '' ?>">
                                </div>

                                <!-- Setores de Produção -->
                                <?php if (!empty($allSectors)): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small"><i class="fas fa-industry me-1 text-success"></i>Setores de Produção</label>
                                    <p class="text-muted" style="font-size: 0.75rem; margin-bottom: 0.5rem;">Selecione e arraste para ordenar os setores padrão desta subcategoria.</p>
                                    
                                    <!-- Setores selecionados (ordenáveis) -->
                                    <div id="sub-sectors-selected" class="sectors-sortable-list mb-2" style="min-height: 36px; border: 1px dashed #dee2e6; border-radius: 0.375rem; padding: 4px;">
                                        <?php foreach ($editSubSectorIds as $sid): 
                                            $sector = null;
                                            foreach ($allSectors as $s) { if ($s['id'] == $sid) { $sector = $s; break; } }
                                            if (!$sector) continue;
                                        ?>
                                        <div class="sector-item badge d-inline-flex align-items-center me-1 mb-1 px-2 py-1" data-id="<?= $sector['id'] ?>" style="background-color: <?= $sector['color'] ?>; cursor: grab; font-size: 0.8rem;">
                                            <i class="<?= $sector['icon'] ?> me-1"></i>
                                            <?= htmlspecialchars($sector['name']) ?>
                                            <button type="button" class="btn-close btn-close-white ms-1 sector-remove" style="font-size: 0.5rem;" data-id="<?= $sector['id'] ?>"></button>
                                            <input type="hidden" name="sector_ids[]" value="<?= $sector['id'] ?>">
                                        </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Setores disponíveis para adicionar -->
                                    <div class="d-flex flex-wrap gap-1" id="sub-sectors-available">
                                        <?php foreach ($allSectors as $sector): 
                                            $isSelected = in_array($sector['id'], $editSubSectorIds);
                                        ?>
                                        <button type="button" class="btn btn-sm sector-add-btn <?= $isSelected ? 'd-none' : '' ?>"
                                                data-id="<?= $sector['id'] ?>" data-name="<?= htmlspecialchars($sector['name']) ?>"
                                                data-icon="<?= $sector['icon'] ?>" data-color="<?= $sector['color'] ?>"
                                                style="border: 1px solid <?= $sector['color'] ?>; color: <?= $sector['color'] ?>; font-size: 0.75rem; padding: 2px 8px;">
                                            <i class="fas fa-plus me-1" style="font-size: 0.6rem;"></i>
                                            <i class="<?= $sector['icon'] ?> me-1"></i><?= htmlspecialchars($sector['name']) ?>
                                        </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Grades / Variações Padrão da Subcategoria -->
                                <?php
                                    $entityType = 'subcategory';
                                    $entityGrades = $editSubcategoryGrades ?? [];
                                    $entityCombinations = $editSubcategoryCombinations ?? [];
                                    // $gradeTypes is already available from controller
                                    require 'app/views/categories/_grades_partial.php';
                                ?>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i><?= isset($editSubcategory) ? 'Salvar' : 'Criar Subcategoria' ?>
                                    </button>
                                    <?php if(isset($editSubcategory)): ?>
                                        <a href="?page=categories&tab=subcategories" class="btn btn-outline-secondary btn-sm">Cancelar</a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Lista -->
                <div class="col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-3 ps-4">Subcategoria</th>
                                            <th class="py-3">Categoria</th>
                                            <th class="py-3">Setores</th>
                                            <th class="py-3 text-end pe-4" style="width:100px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($subcategories)): ?>
                                            <tr><td colspan="4" class="text-center py-5 text-muted">Nenhuma subcategoria cadastrada.</td></tr>
                                        <?php else: ?>
                                            <?php foreach($subcategories as $sub): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <i class="fas fa-sitemap text-success me-2"></i>
                                                    <strong><?= htmlspecialchars($sub['name']) ?></strong>
                                                    <?php if(!empty($subcategoryGradesMap[$sub['id']])): ?>
                                                        <span class="badge bg-info ms-1" style="font-size:0.6rem;" title="Possui grades padrão">
                                                            <i class="fas fa-th-large me-1"></i>Grades
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-warning text-dark"><i class="fas fa-folder me-1"></i><?= htmlspecialchars($sub['category_name']) ?></span>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $subSectorsData = isset($subcategorySectorsMap[$sub['id']]) ? $subcategorySectorsMap[$sub['id']] : [];
                                                    ?>
                                                    <?php if(!empty($subSectorsData)): ?>
                                                        <?php foreach($subSectorsData as $ss): ?>
                                                            <span class="badge me-1" style="background-color: <?= $ss['color'] ?>; font-size: 0.65rem;">
                                                                <i class="<?= $ss['icon'] ?> me-1"></i><?= htmlspecialchars($ss['sector_name']) ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted" style="font-size: 0.75rem;">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-end pe-4">
                                                    <a href="?page=categories&action=editSub&id=<?= $sub['id'] ?>&tab=subcategories" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-outline-danger btn-delete-sub" data-id="<?= $sub['id'] ?>" data-name="<?= htmlspecialchars($sub['name']) ?>" title="Excluir">
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
    </div>
</div>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status'])): ?>
    if (window.history.replaceState) { const url = new URL(window.location); url.searchParams.delete('status'); window.history.replaceState({}, '', url); }
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Operação realizada!', timer: 2000, showConfirmButton: false });
    <?php endif; ?>

    // ── Inicializar drag-and-drop para setores ──
    function initSectorSortable(containerId, availableId) {
        const selectedContainer = document.getElementById(containerId);
        const availableContainer = document.getElementById(availableId);
        
        if (!selectedContainer || !availableContainer) return;

        // Inicializar SortableJS
        new Sortable(selectedContainer, {
            animation: 150,
            ghostClass: 'bg-opacity-50',
            handle: '.sector-item',
            draggable: '.sector-item',
            onEnd: function() {
                // Reordenar inputs hidden
                updateHiddenInputs(selectedContainer);
            }
        });

        // Botões de adicionar setor
        availableContainer.querySelectorAll('.sector-add-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const icon = this.dataset.icon;
                const color = this.dataset.color;

                // Criar item no container selecionado
                const item = document.createElement('div');
                item.className = 'sector-item badge d-inline-flex align-items-center me-1 mb-1 px-2 py-1';
                item.dataset.id = id;
                item.style.backgroundColor = color;
                item.style.cursor = 'grab';
                item.style.fontSize = '0.8rem';
                item.innerHTML = `
                    <i class="${icon} me-1"></i>
                    ${name}
                    <button type="button" class="btn-close btn-close-white ms-1 sector-remove" style="font-size: 0.5rem;" data-id="${id}"></button>
                    <input type="hidden" name="sector_ids[]" value="${id}">
                `;
                selectedContainer.appendChild(item);

                // Esconder o botão de adicionar
                this.classList.add('d-none');

                // Registrar evento de remover no novo item
                item.querySelector('.sector-remove').addEventListener('click', function() {
                    removeSector(this.dataset.id, selectedContainer, availableContainer);
                });
            });
        });

        // Registrar eventos de remover nos itens existentes
        selectedContainer.querySelectorAll('.sector-remove').forEach(btn => {
            btn.addEventListener('click', function() {
                removeSector(this.dataset.id, selectedContainer, availableContainer);
            });
        });
    }

    function removeSector(sectorId, selectedContainer, availableContainer) {
        // Remover do container selecionado
        const item = selectedContainer.querySelector(`.sector-item[data-id="${sectorId}"]`);
        if (item) item.remove();

        // Mostrar no container disponível
        const addBtn = availableContainer.querySelector(`.sector-add-btn[data-id="${sectorId}"]`);
        if (addBtn) addBtn.classList.remove('d-none');
    }

    function updateHiddenInputs(container) {
        // Os hidden inputs já estão dentro de cada sector-item, a ordem do DOM já reflete a ordem
    }

    // Inicializar para categorias e subcategorias
    initSectorSortable('cat-sectors-selected', 'cat-sectors-available');
    initSectorSortable('sub-sectors-selected', 'sub-sectors-available');

    // ── Confirmações de exclusão ──
    document.querySelectorAll('.btn-delete-cat').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id, name = this.dataset.name, prods = this.dataset.products;
            let msg = `Deseja excluir a categoria <strong>${name}</strong>?`;
            if (parseInt(prods) > 0) msg += `<br><span class="text-danger">Atenção: ${prods} produto(s) vinculado(s) perderão a categoria.</span>`;
            Swal.fire({
                title: 'Excluir categoria?', html: msg, icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#c0392b', confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir', cancelButtonText: 'Cancelar'
            }).then(r => { if (r.isConfirmed) window.location = '?page=categories&action=delete&id=' + id; });
        });
    });

    document.querySelectorAll('.btn-delete-sub').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id, name = this.dataset.name;
            Swal.fire({
                title: 'Excluir subcategoria?', html: `Deseja excluir <strong>${name}</strong>?`, icon: 'warning', showCancelButton: true,
                confirmButtonColor: '#c0392b', confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir', cancelButtonText: 'Cancelar'
            }).then(r => { if (r.isConfirmed) window.location = '?page=categories&action=deleteSub&id=' + id; });
        });
    });
});
</script>
