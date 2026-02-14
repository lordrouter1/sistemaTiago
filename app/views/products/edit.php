<div class="container py-4">
    <h2 class="mb-4 text-primary"><i class="fas fa-edit me-2"></i>Editar Produto</h2>
    
    <form id="productForm" method="post" action="/sistemaTiago/?page=products&action=update" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id'] ?>">
        <div class="row">
            <div class="col-md-8">
                <!-- Informações Básicas -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-info-circle me-2"></i>Informações Básicas</legend>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Cartão de Visita" value="<?= $product['name'] ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Categoria</label>
                            <div class="input-group">
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Selecione...</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $product['category_id'] == $category['id'] ? 'selected' : '' ?>><?= $category['name'] ?></option>
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
                                <select class="form-select" id="subcategory_id" name="subcategory_id">
                                    <option value="">Selecione...</option>
                                    <?php foreach($subcategories as $sub): ?>
                                        <option value="<?= $sub['id'] ?>" <?= $product['subcategory_id'] == $sub['id'] ? 'selected' : '' ?>><?= $sub['name'] ?></option>
                                    <?php endforeach; ?>
                                    <option value="new">+ Nova Subcategoria</option>
                                </select>
                             </div>
                             <input type="text" class="form-control mt-2" id="new_subcategory_name" name="new_subcategory_name" placeholder="Nome da nova subcategoria" style="display: none;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição Detalhada</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Detalhes técnicos, acabamentos, etc."><?= $product['description'] ?></textarea>
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
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="0.00" value="<?= $product['price'] ?>">
                            </div>
                            <small class="text-muted">Usado quando não há preço específico na tabela.</small>
                        </div>
                        <div class="col-md-4">
                            <label for="cost_price" class="form-label">Preço de Custo (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control" id="cost_price" name="cost_price" placeholder="0.00" value="<?= $product['cost_price'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label for="stock_quantity" class="form-label">Estoque</label>
                            <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" value="<?= $product['stock_quantity'] ?>">
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
                                       placeholder="<?= number_format($product['price'], 2, '.', '') ?>"
                                       value="<?= isset($productPrices[$pt['id']]) ? number_format($productPrices[$pt['id']], 2, '.', '') : '' ?>"
                                       data-table-id="<?= $pt['id'] ?>">
                            </div>
                            <?php if (isset($productPrices[$pt['id']])): ?>
                                <?php 
                                    $diff = $productPrices[$pt['id']] - $product['price'];
                                    $diffPercent = $product['price'] > 0 ? round(($diff / $product['price']) * 100, 1) : 0;
                                ?>
                                <small class="<?= $diff < 0 ? 'text-success' : ($diff > 0 ? 'text-danger' : 'text-muted') ?>">
                                    <?= $diff > 0 ? '+' : '' ?><?= $diffPercent ?>% em relação ao padrão
                                </small>
                            <?php else: ?>
                                <small class="text-muted">Usando preço padrão</small>
                            <?php endif; ?>
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
                            <input type="text" class="form-control" id="format" name="format" placeholder="Ex: A4, 9x5cm" value="<?= $product['format'] ?? '' ?>">
                         </div>
                         <div class="col-md-6">
                            <label for="material" class="form-label">Material/Papel</label>
                            <input type="text" class="form-control" id="material" name="material" placeholder="Ex: Couché 300g" value="<?= $product['material'] ?? '' ?>">
                         </div>
                    </div>
                </fieldset>
            </div>
            
            <div class="col-md-4">
                 <!-- Imagens do Produto -->
                 <fieldset class="p-4 mb-4 h-100">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-camera me-2"></i>Galeria de Imagens</legend>
                    
                    <!-- Imagens Existentes -->
                    <?php if(!empty($images)): ?>
                    <label class="form-label small fw-bold text-muted mb-2">Imagens Atuais</label>
                    <div class="d-flex flex-wrap gap-2 mb-3" id="existing-images">
                        <?php foreach($images as $img): ?>
                        <div class="position-relative border rounded p-1" id="img-cont-<?= $img['id'] ?>" style="width: 80px; height: 80px;">
                            <img src="<?= $img['image_path'] ?>" class="w-100 h-100 object-fit-cover rounded">
                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-0 d-flex align-items-center justify-content-center" 
                                    style="width: 20px; height: 20px; transform: translate(30%, -30%); border-radius: 50%;"
                                    onclick="deleteImage(<?= $img['id'] ?>)">
                                <i class="fas fa-times" style="font-size: 10px;"></i>
                            </button>
                            <div class="form-check position-absolute bottom-0 start-0 m-1 bg-white rounded-circle shadow-sm" style="padding: 2px;">
                                <input class="form-check-input m-0" type="radio" name="main_image_id" value="<?= $img['id'] ?>" <?= $img['is_main'] ? 'checked' : '' ?> title="Definir como principal">
                            </div>
                            <?php if($img['is_main']): ?>
                            <span class="badge bg-success position-absolute top-0 start-0 m-1" style="font-size: 8px;">Principal</span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                    <?php endif; ?>

                    <!-- Upload de Novas Imagens -->
                    <label class="form-label small fw-bold text-muted mb-2">Adicionar Novas Fotos</label>
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
                        <!-- Novas imagens serão listadas aqui via JS -->
                    </div>
                 </fieldset>
            </div>

            <div class="col-12 mt-3 text-end">
                 <div class="d-flex justify-content-end gap-2">
                    <a href="/sistemaTiago/?page=products" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
            subcategorySelect.innerHTML = '<option value="new">+ Nova Subcategoria</option>';
            subcategorySelect.value = 'new';
            subcategorySelect.dispatchEvent(new Event('change'));
        } else {
            newCategoryInput.style.display = 'none';
            newCategoryInput.required = false;
            newCategoryInput.value = '';
            
            if (categoryId) {
                fetchSubcategories(categoryId);
            } else {
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

    // Drag and Drop Logic for new images
    const dropbox = document.getElementById('product-img-dropbox');
    const input = document.getElementById('product_photos');
    const gallery = document.getElementById('image-gallery-preview');
    const dt = new DataTransfer();

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

    input.addEventListener('change', function(e) {
        handleFiles(this.files);
    });

    function handleFiles(newFiles) {
        let hasNewValidFiles = false;
        
        Array.from(newFiles).forEach(file => {
            if (file.type.startsWith('image/')) {
                const exists = Array.from(dt.files).some(f => f.name === file.name && f.size === file.size);
                if (!exists) {
                    dt.items.add(file);
                    hasNewValidFiles = true;
                }
            }
        });

        if (hasNewValidFiles) {
             input.files = dt.files;
             renderGallery();
        }
    }

    function renderGallery() {
        gallery.innerHTML = '';
        const files = input.files;
        
        let currentChecked = document.querySelector('input[name="main_image_index"]:checked');
        let checkedValue = currentChecked ? parseInt(currentChecked.value) : 0;
        
        if (files.length === 0) return;
        
        if (checkedValue >= files.length) checkedValue = 0;

        Array.from(files).forEach((file, index) => {
            const col = document.createElement('div');
            col.className = 'col-6 position-relative fade-in';
            
            const imgContainer = document.createElement('div');
            imgContainer.className = 'border rounded overflow-hidden position-relative';
            imgContainer.style.height = "100px";

            const img = document.createElement('img');
            img.className = 'w-100 h-100 object-fit-cover';
            
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
            }
            reader.readAsDataURL(file);

            const controlsDiv = document.createElement('div');
            controlsDiv.className = 'position-absolute top-0 w-100 d-flex justify-content-between p-1';
            controlsDiv.style.background = 'linear-gradient(to bottom, rgba(255,255,255,0.9), rgba(255,255,255,0))';

            const radioWrapper = document.createElement('div');
            radioWrapper.className = 'form-check form-check-inline m-0 bg-white rounded-circle p-1 d-flex align-items-center justify-content-center shadow-sm';
            radioWrapper.style.width = '24px';
            radioWrapper.style.height = '24px';

            const isChecked = index === checkedValue ? 'checked' : '';
            
            radioWrapper.innerHTML = `
                <input class="form-check-input m-0" type="radio" name="main_image_index" value="${index}" ${isChecked} style="cursor: pointer;" title="Definir como principal">
            `;

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

// Delete existing image with SweetAlert2
function deleteImage(imageId) {
    Swal.fire({
        title: 'Excluir imagem?',
        text: 'Deseja realmente excluir esta imagem? Esta ação não pode ser desfeita.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#c0392b',
        cancelButtonColor: '#95a5a6',
        confirmButtonText: '<i class="fas fa-trash me-1"></i> Sim, excluir',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('image_id', imageId);
            
            fetch('/sistemaTiago/?page=products&action=deleteImage', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    document.getElementById('img-cont-' + imageId).remove();
                    Swal.fire({ icon: 'success', title: 'Imagem excluída!', timer: 1500, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro!', text: 'Não foi possível excluir a imagem.' });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Erro!', text: 'Erro de comunicação com o servidor.' });
            });
        }
    });
}
</script>
