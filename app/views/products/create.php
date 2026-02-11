<div class="container py-4">
    <h2 class="mb-4 text-primary"><i class="fas fa-box-open me-2"></i>Novo Produto</h2>
    
    <form id="productForm" method="post" action="/sistemaTiago/?page=products&action=store">
        <div class="row">
            <div class="col-md-12">
                <!-- Informações Básicas -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-info-circle me-2"></i>Informações Básicas</legend>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome do Produto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Ex: Cartão de Visita">
                        </div>
                        <div class="col-md-4">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Selecione...</option>
                                <option value="impressao">Impressão Digital</option>
                                <option value="offset">Offset</option>
                                <option value="comunicacao">Comunicação Visual</option>
                                <option value="brindes">Brindes</option>
                                <option value="servicos">Serviços de Design</option>
                            </select>
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
                            <label for="price" class="form-label">Preço de Venda (R$) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required placeholder="0.00">
                            </div>
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

                <div class="d-flex justify-content-end gap-2">
                    <a href="/sistemaTiago/?page=products" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Salvar Produto</button>
                </div>
            </div>
        </div>
    </form>
</div>
