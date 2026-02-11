<div class="container py-4">
    <h2 class="mb-4 text-primary"><i class="fas fa-cart-plus me-2"></i>Novo Pedido</h2>
    
    <form id="orderForm" method="post" action="/sistemaTiago/?page=orders&action=store">
        <div class="row">
            <div class="col-lg-8">
                <!-- Seleção do Cliente -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-user me-2"></i>Dados do Cliente</legend>
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Cliente <span class="text-danger">*</span></label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Selecione um cliente...</option>
                            <?php if(isset($customers)): ?>
                                <?php foreach($customers as $customer): ?>
                                    <option value="<?= $customer['id'] ?>"><?= $customer['name'] ?> (<?= $customer['document'] ?? 'N/A' ?>)</option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="form-text"><a href="/sistemaTiago/?page=customers&action=create" target="_blank"><i class="fas fa-plus-circle"></i> Cadastrar novo cliente</a></div>
                    </div>
                </fieldset>

                <!-- Itens do Pedido -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-list-ol me-2"></i>Itens do Pedido</legend>
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered" id="orderItemsTable">
                            <thead>
                                <tr>
                                    <th width="40%">Produto</th>
                                    <th width="15%">Qtd</th>
                                    <th width="20%">Preço Unit.</th>
                                    <th width="20%">Subtotal</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td>
                                        <select class="form-select product-select" name="items[0][product_id]" required>
                                            <option value="">Escolha...</option>
                                            <!-- Produtos serão carregados aqui -->
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control item-qty" name="items[0][quantity]" value="1" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control item-price" name="items[0][price]" placeholder="0.00" required>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control item-subtotal" readonly value="0.00">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove-item"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-sm btn-info text-white" id="btnAddItem"><i class="fas fa-plus"></i> Adicionar Item</button>

                </fieldset>
            </div>

            <div class="col-lg-4">
                <!-- Resumo e Pagamento -->
                <fieldset class="p-4 mb-4 h-100">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-calculator me-2"></i>Total e Status</legend>
                    
                    <div class="d-flex justify-content-between mb-3 fs-5 fw-bold">
                        <span>Total:</span>
                        <span class="text-success" id="orderTotal">R$ 0,00</span>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status do Pedido</label>
                        <select class="form-select" id="status" name="status">
                            <option value="orcamento">Orçamento</option>
                            <option value="pendente">Pendente</option>
                            <option value="aprovado">Aprovado</option>
                            <option value="em_producao">Em Produção</option>
                            <option value="concluido">Concluído</option>
                            <option value="cancelado">Cancelado</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações Internas</label>
                        <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                    </div>

                    <hr>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-check-circle me-2"></i>Gerar Pedido</button>
                        <a href="/sistemaTiago/?page=orders" class="btn btn-outline-secondary">Cancelar</a>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
</div>

<script>
// Script simples para manipulação de itens do pedido (MVP)
// Idealmente mover para arquivo JS separado e carregar produtos via AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Mock de produtos (deve vir do backend)
    const products = <?= json_encode($products ?? []) ?>;
    
    function populateProductSelect(selectElement) {
        products.forEach(p => {
            let option = document.createElement('option');
            option.value = p.id;
            option.text = p.name;
            option.dataset.price = p.price;
            selectElement.appendChild(option);
        });
    }

    // Inicializar o primeiro select
    const firstSelect = document.querySelector('.product-select');
    if(firstSelect) populateProductSelect(firstSelect);

    // Adicionar novo item
    document.getElementById('btnAddItem').addEventListener('click', function() {
        const tbody = document.querySelector('#orderItemsTable tbody');
        const rowCount = tbody.rows.length;
        const newRow = tbody.rows[0].cloneNode(true);
        
        // Limpar valores e atualizar nomes dos inputs
        newRow.querySelectorAll('input').forEach(input => {
            input.value = (input.classList.contains('item-qty')) ? 1 : '';
            if(!input.classList.contains('item-subtotal')) {
                input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);
            }
        });
        
        const select = newRow.querySelector('select');
        select.name = select.name.replace(/\[\d+\]/, `[${rowCount}]`);
        select.value = "";
        
        // Reativar eventos (forma simplificada, ideal usar delegação)
        // ... Lógica de cálculo seria reiniciada aqui
        
        tbody.appendChild(newRow);
    });

    // Delegação de eventos para cálculo de subtotal e total
    document.querySelector('#orderItemsTable').addEventListener('change', function(e) {
        if(e.target.classList.contains('product-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price;
            const row = e.target.closest('tr');
            row.querySelector('.item-price').value = price;
            calculateRow(row);
        }
        if(e.target.classList.contains('item-qty') || e.target.classList.contains('item-price')) {
            calculateRow(e.target.closest('tr'));
        }
    });

    function calculateRow(row) {
        const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const subtotal = qty * price;
        row.querySelector('.item-subtotal').value = subtotal.toFixed(2);
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('orderTotal').innerText = 'R$ ' + total.toFixed(2);
    }
});
</script>
