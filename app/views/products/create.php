<div class="container py-4">
    <h2 class="mb-4 text-primary"><i class="fas fa-box-open me-2"></i>Novo Produto</h2>
    
    <form id="productForm" method="post" action="/sistemaTiago/?page=products&action=store" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                <!-- Informações Básicas -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-info-circle me-2"></i>Informações Básicas</legend>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Cartão de Visita">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Categoria</label>
                            <div class="input-group">
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                    <?php endforeach; ?>
                                    <option value="new">+ Nova Categoria</option>
                                </select>
                                <button type="button" class="btn btn-outline-secondary" id="btnAddCategory" style="display: none;"><i class="fas fa-check"></i></button>
                            </div>
                            <input type="text" class="form-control mt-2" id="new_category_name" name="new_category_name" placeholder="Nome da nova categoria" style="display: none;">
                        </div>
                        <div class="col-md-6">
                            <label for="subcategory_id" class="form-label">Subcategoria</label>
                             <div class="input-group">
                                <select class="form-select" id="subcategory_id" name="subcategory_id" disabled>
                                    <option value="">Selecione uma categoria primeiro</option>
                                </select>
                             </div>
                             <input type="text" class="form-control mt-2" id="new_subcategory_name" name="new_subcategory_name" placeholder="Nome da nova subcategoria" style="display: none;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição Detalhada</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Detalhes técnicos, acabamentos, etc."></textarea>
                    </div>
                </fieldset>

                <!-- Preços e Estoque -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-dollar-sign me-2"></i>Valores e Estoque</legend>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Preço Padrão (R$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="0.00">
                            </div>
                            <small class="text-muted">Usado quando não há preço específico na tabela.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="cost_price" class="form-label">Preço de Custo (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="stock_quantity" class="form-label">Estoque Inicial</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="0">
                        </div>
                    </div>
                </fieldset>

                <!-- Preços por Tabela -->
                <?php if (!empty($priceTables)): ?>
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-tags me-2"></i>Preços por Tabela</legend>
                    <p class="text-muted small mb-3">Defina preços específicos para cada tabela de preço. Deixe em branco para usar o <strong>preço padrão</strong> do produto.</p>
                    <div class="row g-3">
                        <?php foreach ($priceTables as $pt): ?>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">
                                <?= htmlspecialchars($pt['name']) ?>
                                <?php if ($pt['is_default']): ?>
                                    <span class="badge bg-success" style="font-size:0.65rem;">Padrão</span>
                                <?php endif; ?>
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control table-price-input" 
                                       name="table_prices[<?= $pt['id'] ?>]" 
                                       placeholder="Preço padrão"
                                       data-table-id="<?= $pt['id'] ?>">
                            </div>
                            <small class="text-muted">Usando preço padrão</small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
                <?php endif; ?>

                <!-- Detalhes Técnicos (Opcional) -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-cogs me-2"></i>Especificações Técnicas</legend>
                    <div class="row mb-3">
                         <div class="col-md-6">
                            <label for="format" class="form-label">Formato/Dimensões</label>
                            <input type="text" class="form-control" id="format" name="format" placeholder="Ex: A4, 9x5cm">
                         </div>
                         <div class="col-md-6">
                            <label for="material" class="form-label">Material/Papel</label>
                            <input type="text" class="form-control" id="material" name="material" placeholder="Ex: Couché 300g">
                         </div>
                    </div>
                </fieldset>

                <!-- Setores de Produção -->
                <?php if (!empty($allSectors)): ?>
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-success"><i class="fas fa-industry me-2"></i>Setores de Produção</legend>
                    <p class="text-muted small mb-3">Selecione os setores pelos quais este produto passa na produção. Arraste para reordenar a sequência.</p>
                    
                    <!-- Setores selecionados (ordenáveis) -->
                    <div id="prod-sectors-selected" class="sectors-sortable-list mb-2" style="min-height: 40px; border: 2px dashed #dee2e6; border-radius: 0.375rem; padding: 6px;">
                        <span class="text-muted small sectors-placeholder" style="line-height: 28px; padding: 2px 6px;"><i class="fas fa-info-circle me-1"></i>Clique nos setores abaixo para adicionar e arraste para ordenar</span>
                    </div>

                    <!-- Setores disponíveis para adicionar -->
                    <div class="d-flex flex-wrap gap-1 mt-2" id="prod-sectors-available">
                        <?php foreach ($allSectors as $sector): ?>
                        <button type="button" class="btn btn-sm sector-add-btn"
                                data-id="<?= $sector['id'] ?>" data-name="<?= htmlspecialchars($sector['name']) ?>"
                                data-icon="<?= $sector['icon'] ?>" data-color="<?= $sector['color'] ?>"
                                style="border: 1px solid <?= $sector['color'] ?>; color: <?= $sector['color'] ?>; font-size: 0.8rem; padding: 3px 10px;">
                            <i class="fas fa-plus me-1" style="font-size: 0.65rem;"></i>
                            <i class="<?= $sector['icon'] ?> me-1"></i><?= htmlspecialchars($sector['name']) ?>
                        </button>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-text mt-2"><i class="fas fa-info-circle me-1"></i>Os setores marcados definem o fluxo de produção deste produto. Sem setores, usa o da subcategoria ou categoria.</div>
                </fieldset>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                 <!-- Imagens do Produto -->
                 <fieldset class="p-4 mb-4 h-100">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-camera me-2"></i>Galeria de Imagens</legend>
                    
                    <div class="text-center mb-3">
                        <div id="product-img-dropbox" class="border rounded p-3 d-flex flex-column align-items-center justify-content-center bg-white" style="height: 150px; border-style: dashed !important; cursor: pointer; position: relative; overflow: hidden;">
                             <div id="dropbox-placeholder" class="text-secondary small">
                                <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                <p class="mb-0">Arraste ou clique</p>
                            </div>
                            <input type="file" name="product_photos[]" id="product_photos" accept="image/*" multiple class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
                        </div>
                    </div>
                    
                    <div id="image-gallery-preview" class="row g-2 overflow-auto" style="max-height: 400px;">
                        <!-- Imagens serão listadas aqui via JS -->
                    </div>
                 </fieldset>
            </div>

            <div class="col-12 mt-3 text-end">
                 <div class="d-flex justify-content-end gap-2">
                    <a href="/sistemaTiago/?page=products" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Salvar Produto</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- SortableJS CDN -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Inicializar drag-and-drop para setores de produção ──
    (function initProductSectors() {
        const selectedContainer = document.getElementById('prod-sectors-selected');
        const availableContainer = document.getElementById('prod-sectors-available');
        if (!selectedContainer || !availableContainer) return;

        new Sortable(selectedContainer, {
            animation: 150,
            ghostClass: 'bg-opacity-50',
            draggable: '.sector-item',
        });

        availableContainer.querySelectorAll('.sector-add-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const icon = this.dataset.icon;
                const color = this.dataset.color;

                // Remover placeholder se existir
                const placeholder = selectedContainer.querySelector('.sectors-placeholder');
                if (placeholder) placeholder.remove();

                const item = document.createElement('div');
                item.className = 'sector-item badge d-inline-flex align-items-center me-1 mb-1 px-2 py-2';
                item.dataset.id = id;
                item.style.backgroundColor = color;
                item.style.cursor = 'grab';
                item.style.fontSize = '0.85rem';
                item.innerHTML = `
                    <i class="fas fa-grip-vertical me-1" style="opacity:0.6; font-size:0.7rem;"></i>
                    <i class="${icon} me-1"></i>
                    ${name}
                    <button type="button" class="btn-close btn-close-white ms-2 sector-remove" style="font-size: 0.5rem;" data-id="${id}"></button>
                    <input type="hidden" name="sector_ids[]" value="${id}">
                `;
                selectedContainer.appendChild(item);
                this.classList.add('d-none');

                item.querySelector('.sector-remove').addEventListener('click', function() {
                    removeSector(this.dataset.id);
                });
            });
        });

        function removeSector(sectorId) {
            const item = selectedContainer.querySelector(`.sector-item[data-id="${sectorId}"]`);
            if (item) item.remove();
            const addBtn = availableContainer.querySelector(`.sector-add-btn[data-id="${sectorId}"]`);
            if (addBtn) addBtn.classList.remove('d-none');
            if (!selectedContainer.querySelector('.sector-item')) {
                selectedContainer.innerHTML = '<span class="text-muted small sectors-placeholder" style="line-height: 28px; padding: 2px 6px;"><i class="fas fa-info-circle me-1"></i>Clique nos setores abaixo para adicionar e arraste para ordenar</span>';
            }
        }
    })();

    const categorySelect = document.getElementById('category_id');
    const newCategoryInput = document.getElementById('new_category_name');
    const subcategorySelect = document.getElementById('subcategory_id');
    const newSubcategoryInput = document.getElementById('new_subcategory_name');

    // Handle Category Selection
    categorySelect.addEventListener('change', function() {
        const categoryId = this.value;
        
        if (categoryId === 'new') {
            newCategoryInput.style.display = 'block';
            newCategoryInput.required = true;
            subcategorySelect.disabled = false;
            subcategorySelect.innerHTML = '<option value="new">+ Nova Subcategoria</option>';
            subcategorySelect.value = 'new';
            subcategorySelect.dispatchEvent(new Event('change')); // Trigger subcategory change
        } else {
            newCategoryInput.style.display = 'none';
            newCategoryInput.required = false;
            newCategoryInput.value = '';
            
            if (categoryId) {
                subcategorySelect.disabled = false;
                fetchSubcategories(categoryId);
            } else {
                subcategorySelect.disabled = true;
                subcategorySelect.innerHTML = '<option value="">Selecione uma categoria primeiro</option>';
            }
        }
    });
    
    // Handle Subcategory Selection
     subcategorySelect.addEventListener('change', function() {
        if (this.value === 'new') {
            newSubcategoryInput.style.display = 'block';
             newSubcategoryInput.required = true;
        } else {
            newSubcategoryInput.style.display = 'none';
             newSubcategoryInput.required = false;
             newSubcategoryInput.value = '';
        }
    });

    function fetchSubcategories(categoryId) {
        fetch(`/sistemaTiago/?page=products&action=getSubcategories&category_id=${categoryId}`)
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Selecione...</option>';
                data.forEach(sub => {
                    options += `<option value="${sub.id}">${sub.name}</option>`;
                });
                options += '<option value="new">+ Nova Subcategoria</option>';
                subcategorySelect.innerHTML = options;
            })
            .catch(error => console.error('Error fetching subcategories:', error));
    }

    // Drag and Drop Logic (existing)
    const dropbox = document.getElementById('product-img-dropbox');
    const input = document.getElementById('product_photos');
    const gallery = document.getElementById('image-gallery-preview');
    // DataTransfer object to hold all selected files incrementally
    const dt = new DataTransfer();

    // Efeitos de Drag & Drop (mesmo anterior)
    dropbox.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropbox.classList.add('bg-light');
        dropbox.style.borderColor = 'var(--accent-color)';
    });

    dropbox.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropbox.classList.remove('bg-light');
        dropbox.style.borderColor = '';
    });

    dropbox.addEventListener('drop', (e) => {
        e.preventDefault();
        dropbox.classList.remove('bg-light');
        dropbox.style.borderColor = '';
        handleFiles(e.dataTransfer.files);
    });

    // Handle click uploads
    input.addEventListener('change', function(e) {
        // Standard input replaces files, so we capture the new ones and add to our set
        handleFiles(this.files);
    });

    function handleFiles(newFiles) {
        let hasNewValidFiles = false;
        
        Array.from(newFiles).forEach(file => {
            if (file.type.startsWith('image/')) {
                // Check if file already exists (simple check by name and size)
                const exists = Array.from(dt.files).some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    dt.items.add(file);
                    hasNewValidFiles = true;
                }
            }
        });

        if (hasNewValidFiles) {
             // Update the input with the accumulated files
             input.files = dt.files;
             renderGallery();
        }
    }

    function renderGallery() {
        gallery.innerHTML = '';
        const files = input.files;
        
        // Preserve selected main image index if possible, else default to 0
        // Since we re-render, we need to know what was checked. 
        // We'll rely on the logic that if we are adding, we keep the old check unless it was removed.
        // Simpler for MVP: Default to 0 (first image) if the previously checked index is no longer valid or unset.
        // Optimization: We could store the name/size of the "main" image to restore it. 
        // For now, let's default to the *current* checked radio before clean, or 0.
        
        let currentChecked = document.querySelector('input[name="main_image_index"]:checked');
        let checkedValue = currentChecked ? parseInt(currentChecked.value) : 0;
        
        // If checking logic gets complex with removals, defaulting to 0 is safest visual feedback 
        // (the first image becomes main if the main was deleted).
        // If we added images, file list grew. If we removed, it shrank.
        
        if (files.length === 0) return;
        
        // Ensure checkedValue is within bounds
        if (checkedValue >= files.length) checkedValue = 0;

        Array.from(files).forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-6 position-relative fade-in';
            
            const imgContainer = document.createElement('div');
            imgContainer.className = 'border rounded overflow-hidden position-relative';
            imgContainer.style.height = "100px";

            const img = document.createElement('img');
            img.className = 'w-100 h-100 object-fit-cover';
            
            // Read file for preview
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);

            // Controls overlay
            const controlsDiv = document.createElement('div');
            controlsDiv.className = 'position-absolute top-0 w-100 d-flex justify-content-between p-1';
            controlsDiv.style.background = 'linear-gradient(to bottom, rgba(255,255,255,0.9), rgba(255,255,255,0))';

            // Radio for Main Image
            const radioWrapper = document.createElement('div');
            radioWrapper.className = 'form-check form-check-inline m-0 bg-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow-sm';
            radioWrapper.style.width = '24px';
            radioWrapper.style.height = '24px';

            const isChecked = index === checkedValue ? 'checked' : '';
            
            radioWrapper.innerHTML = `
                <input class="form-check-input m-0" type="radio" name="main_image_index" value="${index}" ${isChecked} style="cursor: pointer;" title="Definir como principal">
            `;

            // Delete button
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'btn btn-danger btn-sm p-0 d-flex align-items-center justify-content-center rounded-circle shadow-sm';
            deleteBtn.style.width = '24px';
            deleteBtn.style.height = '24px';
            deleteBtn.innerHTML = '<i class="fas fa-times" style="font-size: 12px;"></i>';
            deleteBtn.onclick = function() {
                removeFile(index);
            };

            controlsDiv.appendChild(radioWrapper);
            controlsDiv.appendChild(deleteBtn);

            imgContainer.appendChild(img);
            imgContainer.appendChild(controlsDiv);
            col.appendChild(imgContainer);
            
            gallery.appendChild(col);
        });
    }

    function removeFile(indexToRemove) {
        dt.items.remove(indexToRemove);
        input.files = dt.files;
        renderGallery();
    }
});
</script>
