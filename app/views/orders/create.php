<div class="container py-4">
    <h2 class="mb-4 text-primary"><i class="fas fa-cart-plus me-2"></i>Novo Pedido</h2>
    
    <form id="orderForm" method="post" action="/sistemaTiago/?page=orders&action=store">
        <div class="row">
            <div class="col-md-9 offset-md-2">
                 <!-- Seleção de Cliente -->
                 <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold"><i class="fas fa-user-tag me-2"></i>Dados do Cliente</legend>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="customer_id" class="form-label fw-bold">Cliente <span class="text-danger">*</span></label>
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
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Prioridade</label>
                            <select class="form-select" name="priority">
                                <option value="baixa">🟢 Baixa</option>
                                <option value="normal" selected>🔵 Normal</option>
                                <option value="alta">🟡 Alta</option>
                                <option value="urgente">🔴 Urgente</option>
                            </select>
                        </div>
                    </div>
                 </fieldset>

                 <!-- Info Pipeline -->
                 <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    Ao criar o pedido, ele será automaticamente inserido na <strong>Linha de Produção</strong> na etapa <span class="badge bg-purple" style="background:#9b59b6;"><i class="fas fa-phone me-1"></i>Contato</span>.
                    Você poderá gerenciar as etapas pelo <a href="/sistemaTiago/?page=pipeline" class="fw-bold text-info">Painel de Produção</a>.
                 </div>

                 <!-- Itens do Pedido -->
                 <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold"><i class="fas fa-list-alt me-2"></i>Itens do Pedido</legend>
                    
                    <div class="table-responsive mb-3">
                        <table class="table table-bordered table-hover" id="orderItemsTable">
                             <thead class="table-light">
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

            <div class="col-12 mt-4 text-end">
                <div class="d-flex justify-content-end gap-2 align-items-center">
                    <h4 class="mb-0 me-3">Total: <span class="text-primary fw-bold" id="grandTotalDisplay">R$ 0,00</span></h4>
                    <input type="hidden" name="total_amount" id="totalAmountInput" value="0">
                    <a href="/sistemaTiago/?page=orders" class="btn btn-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4 btn-lg"><i class="fas fa-check-circle me-2"></i>Finalizar Pedido</button>
                </div>
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
        document.getElementById('grandTotalDisplay').innerText = 'R$ ' + total.toFixed(2).replace('.', ',');
        document.getElementById('totalAmountInput').value = total.toFixed(2);
    }

    // Remover item
    document.querySelector('#orderItemsTable').addEventListener('click', function(e) {
        if(e.target.closest('.btn-remove-item')) {
            const tbody = document.querySelector('#orderItemsTable tbody');
            if(tbody.rows.length > 1) {
                e.target.closest('tr').remove();
                calculateTotal();
            } else {
                Swal.fire({ icon: 'warning', title: 'Atenção', text: 'O pedido deve ter pelo menos um item.' });
            }
        }
    });
});
</script>
