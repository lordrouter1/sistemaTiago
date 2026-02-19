<div class="container py-4">
    <?php
        require_once 'app/models/CompanySettings.php';
        $customerFormattedAddress = '';
        if (!empty($order['customer_address'])) {
            $customerFormattedAddress = CompanySettings::formatCustomerAddress($order['customer_address']);
        }
        $currentStage = $order['pipeline_stage'] ?? 'contato';
        $stageInfo = $stages[$currentStage] ?? ['label' => $currentStage, 'color' => '#999', 'icon' => 'fas fa-circle'];
        $hoursInStage = (int)$order['hours_in_stage'];
        $stageGoal = isset($goals[$currentStage]) ? (int)$goals[$currentStage]['max_hours'] : 24;
        $isDelayed = ($stageGoal > 0 && $hoursInStage > $stageGoal);
    ?>

    <!-- Cabeçalho -->
    <div class="d-flex justify-content-between align-items-center pt-2 pb-2 mb-3 border-bottom">
        <div>
            <h1 class="h2 mb-0">
                <i class="<?= $stageInfo['icon'] ?> me-2" style="color:<?= $stageInfo['color'] ?>;"></i>
                Pedido #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>
            </h1>
            <small class="text-muted">Criado em <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
        </div>
        <div class="d-flex gap-2">
            <a href="/sistemaTiago/?page=pipeline" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Voltar</a>
<<<<<<< HEAD
            <?php if ($currentStage === 'producao'): ?>
            <a href="/sistemaTiago/?page=pipeline&action=printProductionOrder&id=<?= $order['id'] ?>" target="_blank" class="btn btn-outline-warning btn-sm text-dark">
                <i class="fas fa-print me-1"></i> Ordem de Produção
            </a>
            <a href="/sistemaTiago/?page=production_board" class="btn btn-outline-success btn-sm"><i class="fas fa-tasks me-1"></i> Painel de Produção</a>
            <?php endif; ?>
            <?php if (!$isReadOnly): ?>
=======
>>>>>>> parent of efe3602 (beta 0.6)
            <a href="/sistemaTiago/?page=orders&action=edit&id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit me-1"></i> Editar Pedido</a>
        </div>
    </div>

    <!-- Progress Bar do Pipeline -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="pipeline-progress d-flex align-items-center justify-content-between position-relative">
                <?php 
                $stageKeys = array_keys($stages);
                $currentIdx = array_search($currentStage, $stageKeys);
                $totalStages = count($stageKeys);
                foreach ($stages as $sKey => $sInfo):
                    $sIdx = array_search($sKey, $stageKeys);
                    $isCompleted = $sIdx < $currentIdx;
                    $isCurrent = $sKey === $currentStage;
                    $isFuture = $sIdx > $currentIdx;
                ?>
                <div class="pipeline-step text-center flex-fill position-relative" style="z-index:1;">
                    <div class="pipeline-step-icon mx-auto mb-1 rounded-circle d-flex align-items-center justify-content-center 
                        <?php if($isCurrent): ?>border border-3<?php endif; ?>"
                        style="width:40px; height:40px; font-size:0.85rem;
                        background: <?= $isCompleted ? $sInfo['color'] : ($isCurrent ? '#fff' : '#e9ecef') ?>;
                        color: <?= $isCompleted ? '#fff' : ($isCurrent ? $sInfo['color'] : '#adb5bd') ?>;
                        border-color: <?= $isCurrent ? $sInfo['color'] : 'transparent' ?> !important;">
                        <i class="<?= $isCompleted ? 'fas fa-check' : $sInfo['icon'] ?>"></i>
                    </div>
                    <div class="small <?= $isCurrent ? 'fw-bold' : ($isFuture ? 'text-muted' : '') ?>" style="font-size:0.7rem;color:<?= $isCurrent ? $sInfo['color'] : '' ?>;">
                        <?= $sInfo['label'] ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <!-- Linha de progresso -->
                <div class="position-absolute w-100" style="height:3px; top:20px; z-index:0;">
                    <div class="bg-light w-100 rounded" style="height:3px;"></div>
                    <div class="rounded position-absolute top-0 start-0" 
                         style="height:3px; width:<?= ($currentIdx / max($totalStages - 1, 1)) * 100 ?>%; background: var(--accent-color);"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ações rápidas de movimentação -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div>
                    <span class="badge fs-6 py-2 px-3" style="background:<?= $stageInfo['color'] ?>;">
                        <i class="<?= $stageInfo['icon'] ?> me-1"></i> <?= $stageInfo['label'] ?>
                    </span>
                    <span class="ms-2 small <?= $isDelayed ? 'text-danger fw-bold' : 'text-muted' ?>">
                        <i class="fas fa-clock me-1"></i>
                        <?= ($hoursInStage >= 24) ? floor($hoursInStage/24).'d '.($hoursInStage%24).'h' : $hoursInStage.'h' ?>
                        na etapa
                        <?php if($isDelayed): ?>
                            <span class="badge bg-danger ms-1">ATRASADO +<?= $hoursInStage - $stageGoal ?>h</span>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="d-flex gap-2">
                    <!-- Botão retroceder -->
                    <?php if ($currentIdx > 0): ?>
                    <a href="/sistemaTiago/?page=pipeline&action=move&id=<?= $order['id'] ?>&stage=<?= $stageKeys[$currentIdx - 1] ?>" 
                       class="btn btn-sm btn-outline-secondary btn-move-stage" data-dir="Retroceder" data-stage="<?= $stages[$stageKeys[$currentIdx - 1]]['label'] ?>">
                        <i class="fas fa-arrow-left me-1"></i> <?= $stages[$stageKeys[$currentIdx - 1]]['label'] ?>
                    </a>
                    <?php endif; ?>
                    
                    <!-- Botão avançar -->
                    <?php if ($currentIdx < $totalStages - 1): ?>
                    <a href="/sistemaTiago/?page=pipeline&action=move&id=<?= $order['id'] ?>&stage=<?= $stageKeys[$currentIdx + 1] ?>" 
                       class="btn btn-sm btn-success btn-move-stage" data-dir="Avançar" data-stage="<?= $stages[$stageKeys[$currentIdx + 1]]['label'] ?>">
                        <?= $stages[$stageKeys[$currentIdx + 1]]['label'] ?> <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                    <?php endif; ?>

                    <!-- Mover para qualquer etapa -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-random me-1"></i> Mover para...
                        </button>
                        <ul class="dropdown-menu">
                            <?php foreach ($stages as $sKey => $sInfo): ?>
                            <?php if ($sKey !== $currentStage): ?>
                            <li>
                                <a class="dropdown-item btn-move-stage" href="/sistemaTiago/?page=pipeline&action=move&id=<?= $order['id'] ?>&stage=<?= $sKey ?>" data-dir="Mover" data-stage="<?= $sInfo['label'] ?>">
                                    <i class="<?= $sInfo['icon'] ?> me-2" style="color:<?= $sInfo['color'] ?>;"></i> <?= $sInfo['label'] ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Coluna Esquerda: Informações e Formulário -->
        <div class="col-lg-8">
            <form method="POST" action="/sistemaTiago/?page=pipeline&action=updateDetails">
                <input type="hidden" name="id" value="<?= $order['id'] ?>">

                <!-- Dados do Cliente -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-user-tag me-2"></i>Cliente</legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Nome</label>
                            <input type="text" class="form-control" value="<?= $order['customer_name'] ?? '—' ?>" disabled>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Telefone</label>
                            <div class="input-group">
                                <input type="text" class="form-control" value="<?= $order['customer_phone'] ?? '—' ?>" disabled>
                                <?php if (!empty($order['customer_phone'])): ?>
                                <a href="https://wa.me/55<?= preg_replace('/\D/', '', $order['customer_phone']) ?>" target="_blank" class="btn btn-success btn-sm" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">CPF/CNPJ</label>
                            <input type="text" class="form-control" value="<?= $order['customer_document'] ?? '—' ?>" disabled>
                        </div>
                        <?php if (!empty($order['customer_email'])): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">E-mail</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($order['customer_email']) ?>" disabled>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($customerFormattedAddress)): ?>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Endereço</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($customerFormattedAddress) ?>" disabled>
                        </div>
                        <?php endif; ?>
                    </div>
                </fieldset>

                <?php
                // Mostrar seção de produtos quando o pedido está na etapa de orçamento ou posterior (exceto contato)
                $showProducts = ($currentStage !== 'contato');
                ?>

                <?php if ($showProducts): ?>
                <!-- Produtos do Orçamento -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Produtos do Orçamento
                        <a href="/sistemaTiago/?page=orders&action=printQuote&id=<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success ms-3">
                            <i class="fas fa-print me-1"></i> Imprimir Orçamento
                        </a>
                    </legend>

                    <!-- Seletor de Tabela de Preços -->
                    <div class="alert alert-light border mb-3 py-2">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-tags me-1"></i>Tabela de Preços</label>
                                <select class="form-select form-select-sm" name="price_table_id" id="priceTableSelect">
                                    <option value="">Padrão do cliente</option>
                                    <?php foreach ($priceTables as $pt): ?>
                                    <option value="<?= $pt['id'] ?>" <?= ($currentPriceTableId == $pt['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($pt['name']) ?> <?= $pt['is_default'] ? '(Padrão)' : '' ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block mt-md-4">
                                    <i class="fas fa-info-circle me-1"></i>Ao mudar a tabela, os preços dos produtos serão atualizados automaticamente.
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Itens Existentes -->
                    <?php if (!empty($orderItems)): ?>
                    <div class="table-responsive mb-3">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center" style="width:100px;">Qtd</th>
                                    <th class="text-end" style="width:130px;">Preço Unit.</th>
                                    <th class="text-end" style="width:130px;">Subtotal</th>
                                    <th class="text-center" style="width:80px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $totalItems = 0; ?>
                                <?php foreach ($orderItems as $item): ?>
                                <?php $subtotal = $item['quantity'] * $item['unit_price']; $totalItems += $subtotal; ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($item['product_name']) ?></strong></td>
                                    <td class="text-center"><?= $item['quantity'] ?></td>
                                    <td class="text-end">R$ <?= number_format($item['unit_price'], 2, ',', '.') ?></td>
                                    <td class="text-end fw-bold">R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                                    <td class="text-center">
                                        <a href="/sistemaTiago/?page=orders&action=deleteItem&item_id=<?= $item['id'] ?>&order_id=<?= $order['id'] ?>&redirect=pipeline" 
                                           class="btn btn-sm btn-outline-danger btn-delete-item" title="Remover item">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-success">
                                    <td colspan="3" class="text-end fw-bold">Subtotal Produtos:</td>
                                    <td class="text-end fw-bold fs-5">R$ <?= number_format($totalItems, 2, ',', '.') ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>Nenhum produto adicionado ao orçamento ainda.
                    </div>
                    <?php endif; ?>

                    <!-- Formulário Adicionar Item -->
                    <div class="card border-primary border-opacity-25">
                        <div class="card-header bg-primary bg-opacity-10 py-2">
                            <h6 class="mb-0 text-primary"><i class="fas fa-plus-circle me-2"></i>Adicionar Produto</h6>
                        </div>
                        <div class="card-body p-3">
                            <!-- O form real é colocado via JS para evitar nesting -->
                            <div class="row g-2 align-items-end" id="addItemRowPipeline">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">Produto</label>
                                    <select class="form-select form-select-sm" id="pipProductSelect">
                                        <option value="">Selecione um produto...</option>
                                        <?php foreach ($products as $prod): 
                                            $displayPrice = isset($customerPrices[$prod['id']]) ? $customerPrices[$prod['id']] : $prod['price'];
                                        ?>
                                        <option value="<?= $prod['id'] ?>" data-price="<?= $displayPrice ?>" data-original-price="<?= $prod['price'] ?>">
                                            <?= htmlspecialchars($prod['name']) ?> — R$ <?= number_format($displayPrice, 2, ',', '.') ?>
                                            <?php if (isset($customerPrices[$prod['id']]) && $customerPrices[$prod['id']] != $prod['price']): ?>
                                            (base: R$ <?= number_format($prod['price'], 2, ',', '.') ?>)
                                            <?php endif; ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-muted">Quantidade</label>
                                    <input type="number" min="1" class="form-control form-control-sm" id="pipQtyInput" value="1">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Preço Unitário</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" class="form-control" id="pipPriceInput">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-primary btn-sm w-100" id="btnAddItemPipeline">
                                        <i class="fas fa-plus me-1"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Custos Extras do Orçamento -->
                    <div class="card border-warning border-opacity-25 mt-3">
                        <div class="card-header bg-warning bg-opacity-10 py-2">
                            <h6 class="mb-0 text-warning"><i class="fas fa-receipt me-2"></i>Custos Extras</h6>
                        </div>
                        <div class="card-body p-3">
                            <?php if (!empty($extraCosts)): ?>
                            <div class="table-responsive mb-3">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Descrição</th>
                                            <th class="text-end" style="width:130px;">Valor</th>
                                            <th class="text-center" style="width:80px;">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalExtras = 0; ?>
                                        <?php foreach ($extraCosts as $ec): ?>
                                        <?php $totalExtras += $ec['amount']; ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ec['description']) ?></td>
                                            <td class="text-end fw-bold">R$ <?= number_format($ec['amount'], 2, ',', '.') ?></td>
                                            <td class="text-center">
                                                <a href="/sistemaTiago/?page=pipeline&action=deleteExtraCost&cost_id=<?= $ec['id'] ?>&order_id=<?= $order['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-delete-extra" title="Remover custo">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-warning">
                                            <td class="text-end fw-bold">Total Custos Extras:</td>
                                            <td class="text-end fw-bold">R$ <?= number_format($totalExtras, 2, ',', '.') ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <?php endif; ?>
                            <!-- Form para adicionar custo extra -->
                            <div class="row g-2 align-items-end" id="addExtraCostRow">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Descrição do custo</label>
                                    <input type="text" class="form-control form-control-sm" id="extraDescription" placeholder="Ex: Frete, Arte, Acabamento...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Valor (R$)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" min="0.01" class="form-control" id="extraAmount">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-warning btn-sm w-100" id="btnAddExtraCost">
                                        <i class="fas fa-plus me-1"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observações do Orçamento (aparece no orçamento impresso) -->
                    <div class="mt-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-file-alt me-1"></i>Observações do Orçamento <small class="text-success">(aparece no orçamento impresso)</small></label>
                        <textarea class="form-control" name="quote_notes" rows="3" placeholder="Notas visíveis ao cliente no orçamento impresso..."><?= $order['quote_notes'] ?? '' ?></textarea>
                    </div>
                </fieldset>
                <?php else: ?>
                <!-- Manter valores atuais nos campos ocultos quando a seção de produtos não aparece -->
                <input type="hidden" name="quote_notes" value="<?= htmlspecialchars($order['quote_notes'] ?? '') ?>">
                <input type="hidden" name="price_table_id" value="<?= $order['price_table_id'] ?? '' ?>">
                <?php endif; ?>

                <!-- Gerenciamento do Pedido -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-sliders-h me-2"></i>Gerenciamento</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Prioridade</label>
                            <select class="form-select" name="priority">
                                <option value="baixa" <?= ($order['priority'] ?? '') == 'baixa' ? 'selected' : '' ?>>🟢 Baixa</option>
                                <option value="normal" <?= ($order['priority'] ?? 'normal') == 'normal' ? 'selected' : '' ?>>🔵 Normal</option>
                                <option value="alta" <?= ($order['priority'] ?? '') == 'alta' ? 'selected' : '' ?>>🟡 Alta</option>
                                <option value="urgente" <?= ($order['priority'] ?? '') == 'urgente' ? 'selected' : '' ?>>🔴 Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Prazo (Deadline)</label>
                            <input type="date" class="form-control" name="deadline" value="<?= $order['deadline'] ?? '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Responsável</label>
                            <select class="form-select" name="assigned_to">
                                <option value="">Sem responsável</option>
                                <?php foreach ($users as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= ($order['assigned_to'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                    <?= $u['name'] ?> (<?= $u['role'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-muted">
                                <i class="fas fa-lock me-1"></i>Observações Internas 
                                <small class="text-danger">(NÃO aparece no orçamento impresso)</small>
                            </label>
                            <textarea class="form-control" name="internal_notes" rows="3" placeholder="Notas internas sobre este pedido..."><?= $order['internal_notes'] ?? '' ?></textarea>
                        </div>
                    </div>
                </fieldset>

                <?php
                // Campos de Envio/Entrega: só aparecem nas etapas de preparação, envio ou concluído
                $showShipping = in_array($currentStage, ['preparacao', 'envio', 'concluido']);
                // Campos Financeiro: só aparecem nas etapas financeiro ou concluído
                $showFinancial = in_array($currentStage, ['financeiro', 'concluido']);
                ?>

                <?php if ($showFinancial): ?>
                <!-- Financeiro -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-coins me-2"></i>Financeiro</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Valor Total</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control fw-bold" value="<?= number_format($order['total_amount'], 2, ',', '.') ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Desconto (R$)</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" step="0.01" class="form-control" name="discount" value="<?= $order['discount'] ?? 0 ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Status Pagamento</label>
                            <select class="form-select" name="payment_status">
                                <option value="pendente" <?= ($order['payment_status'] ?? '') == 'pendente' ? 'selected' : '' ?>>⏳ Pendente</option>
                                <option value="parcial" <?= ($order['payment_status'] ?? '') == 'parcial' ? 'selected' : '' ?>>💳 Parcial</option>
                                <option value="pago" <?= ($order['payment_status'] ?? '') == 'pago' ? 'selected' : '' ?>>✅ Pago</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Forma de Pagamento</label>
                            <select class="form-select" name="payment_method">
                                <option value="">Selecione...</option>
                                <option value="dinheiro" <?= ($order['payment_method'] ?? '') == 'dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                                <option value="pix" <?= ($order['payment_method'] ?? '') == 'pix' ? 'selected' : '' ?>>PIX</option>
                                <option value="cartao_credito" <?= ($order['payment_method'] ?? '') == 'cartao_credito' ? 'selected' : '' ?>>Cartão Crédito</option>
                                <option value="cartao_debito" <?= ($order['payment_method'] ?? '') == 'cartao_debito' ? 'selected' : '' ?>>Cartão Débito</option>
                                <option value="boleto" <?= ($order['payment_method'] ?? '') == 'boleto' ? 'selected' : '' ?>>Boleto</option>
                                <option value="transferencia" <?= ($order['payment_method'] ?? '') == 'transferencia' ? 'selected' : '' ?>>Transferência</option>
                            </select>
                        </div>
                    </div>
                </fieldset>
                <?php else: ?>
                <!-- Manter valores atuais nos campos ocultos para não perder ao salvar -->
                <input type="hidden" name="discount" value="<?= $order['discount'] ?? 0 ?>">
                <input type="hidden" name="payment_status" value="<?= $order['payment_status'] ?? 'pendente' ?>">
                <input type="hidden" name="payment_method" value="<?= $order['payment_method'] ?? '' ?>">
                <?php endif; ?>

                <?php if ($showShipping): ?>
                <!-- Envio / Entrega -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-truck me-2"></i>Envio / Entrega</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tipo de Envio</label>
                            <select class="form-select" name="shipping_type" id="shippingType">
                                <option value="retirada" <?= ($order['shipping_type'] ?? '') == 'retirada' ? 'selected' : '' ?>>🏪 Retirada na loja</option>
                                <option value="entrega" <?= ($order['shipping_type'] ?? '') == 'entrega' ? 'selected' : '' ?>>🏍️ Entrega própria</option>
                                <option value="correios" <?= ($order['shipping_type'] ?? '') == 'correios' ? 'selected' : '' ?>>📦 Correios</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Código de Rastreio</label>
                            <input type="text" class="form-control" name="tracking_code" placeholder="Ex: BR123456789" value="<?= $order['tracking_code'] ?? '' ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Endereço de Entrega</label>
                            <textarea class="form-control" name="shipping_address" rows="1" placeholder="Endereço completo..."><?= !empty($order['shipping_address']) ? htmlspecialchars($order['shipping_address']) : htmlspecialchars($customerFormattedAddress) ?></textarea>
                        </div>
                    </div>
                </fieldset>
                <?php else: ?>
                <!-- Manter valores atuais nos campos ocultos para não perder ao salvar -->
                <input type="hidden" name="shipping_type" value="<?= $order['shipping_type'] ?? 'retirada' ?>">
                <input type="hidden" name="shipping_address" value="<?= htmlspecialchars($order['shipping_address'] ?? '') ?>">
                <input type="hidden" name="tracking_code" value="<?= $order['tracking_code'] ?? '' ?>">
                <?php endif; ?>

                <div class="text-end mb-4">
                    <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                </div>
            </form>
        </div>

        <!-- Coluna Direita: Timeline / Histórico -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-history me-2"></i>Histórico de Movimentação</h6>
                </div>
                <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                    <?php if (empty($history)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-stream d-block mb-2" style="font-size:2rem;"></i>
                            Nenhuma movimentação registrada.
                        </div>
                    <?php else: ?>
                        <div class="timeline">
                            <?php foreach ($history as $h): ?>
                            <?php 
                                $toInfo = $stages[$h['to_stage']] ?? ['label' => $h['to_stage'], 'color' => '#999', 'icon' => 'fas fa-circle'];
                                $fromInfo = $stages[$h['from_stage'] ?? ''] ?? ['label' => '—', 'color' => '#ccc', 'icon' => ''];
                            ?>
                            <div class="timeline-item d-flex mb-3">
                                <div class="timeline-icon me-3 flex-shrink-0">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width:32px;height:32px;background:<?= $toInfo['color'] ?>;color:#fff;font-size:0.75rem;">
                                        <i class="<?= $toInfo['icon'] ?>"></i>
                                    </div>
                                </div>
                                <div class="timeline-content flex-grow-1">
                                    <div class="small fw-bold"><?= $toInfo['label'] ?></div>
                                    <div class="small text-muted">
                                        <?php if ($h['from_stage']): ?>
                                            De: <?= $fromInfo['label'] ?> → <?= $toInfo['label'] ?>
                                        <?php else: ?>
                                            Etapa inicial
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($h['notes'])): ?>
                                    <div class="small fst-italic mt-1">"<?= $h['notes'] ?>"</div>
                                    <?php endif; ?>
                                    <div class="text-muted" style="font-size:0.65rem;">
                                        <i class="fas fa-user me-1"></i><?= $h['user_name'] ?? 'Sistema' ?>
                                        · <?= date('d/m/Y H:i', strtotime($h['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    Swal.fire({ icon: 'success', title: 'Salvo!', text: 'Detalhes atualizados com sucesso.', timer: 2000, showConfirmButton: false });
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'item_added'): ?>
    Swal.fire({ icon: 'success', title: 'Produto adicionado!', timer: 1500, showConfirmButton: false });
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'item_deleted'): ?>
    Swal.fire({ icon: 'success', title: 'Produto removido!', timer: 1500, showConfirmButton: false });
    <?php endif; ?>

    // Confirmação ao mover etapa
    document.querySelectorAll('.btn-move-stage').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            const dir = this.dataset.dir;
            const stage = this.dataset.stage;
            Swal.fire({
                title: dir + ' pedido?',
                html: `${dir} para <strong>${stage}</strong>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check me-1"></i> Confirmar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#27ae60'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = href;
                }
            });
        });
    });

    // Auto-preencher preço ao selecionar produto (pipeline)
    const pipProductSelect = document.getElementById('pipProductSelect');
    const pipPriceInput = document.getElementById('pipPriceInput');
    if (pipProductSelect && pipPriceInput) {
        pipProductSelect.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.dataset.price) {
                pipPriceInput.value = parseFloat(opt.dataset.price).toFixed(2);
            }
        });
    }

    // Adicionar item via form dinâmico (evita nesting de forms)
    const btnAdd = document.getElementById('btnAddItemPipeline');
    if (btnAdd) {
        btnAdd.addEventListener('click', function() {
            const productId = document.getElementById('pipProductSelect').value;
            const quantity = document.getElementById('pipQtyInput').value;
            const price = document.getElementById('pipPriceInput').value;

            if (!productId || !quantity || !price) {
                Swal.fire({ icon: 'warning', title: 'Preencha todos os campos', timer: 2000, showConfirmButton: false });
                return;
            }

            // Criar form dinamicamente e submeter
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/sistemaTiago/?page=orders&action=addItem';
            form.innerHTML = `
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <input type="hidden" name="product_id" value="${productId}">
                <input type="hidden" name="quantity" value="${quantity}">
                <input type="hidden" name="unit_price" value="${price}">
                <input type="hidden" name="redirect" value="pipeline">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    }

    // Confirmar remoção de item
    document.querySelectorAll('.btn-delete-item').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            Swal.fire({
                title: 'Remover item?',
                text: 'O item será removido do orçamento.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Remover',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c'
            }).then(r => { if (r.isConfirmed) window.location.href = href; });
        });
    });

    // Adicionar custo extra via form dinâmico
    const btnAddExtra = document.getElementById('btnAddExtraCost');
    if (btnAddExtra) {
        btnAddExtra.addEventListener('click', function() {
            const description = document.getElementById('extraDescription').value.trim();
            const amount = document.getElementById('extraAmount').value;

            if (!description || !amount || parseFloat(amount) <= 0) {
                Swal.fire({ icon: 'warning', title: 'Preencha a descrição e o valor', timer: 2000, showConfirmButton: false });
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/sistemaTiago/?page=pipeline&action=addExtraCost';
            form.innerHTML = `
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <input type="hidden" name="extra_description" value="${description}">
                <input type="hidden" name="extra_amount" value="${amount}">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    }

    // Confirmar remoção de custo extra
    document.querySelectorAll('.btn-delete-extra').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            Swal.fire({
                title: 'Remover custo extra?',
                text: 'Este custo será removido do orçamento.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Remover',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c'
            }).then(r => { if (r.isConfirmed) window.location.href = href; });
        });
    });

    // ── Seletor de Tabela de Preços: atualizar preços ao mudar ──
    const priceTableSelect = document.getElementById('priceTableSelect');
    if (priceTableSelect) {
        priceTableSelect.addEventListener('change', function() {
            const tableId = this.value;
            const customerId = '<?= $order['customer_id'] ?? '' ?>';
            let url = '/sistemaTiago/?page=pipeline&action=getPricesByTable';
            
            if (tableId) {
                url += '&table_id=' + tableId;
            } else if (customerId) {
                url += '&customer_id=' + customerId;
            }

            fetch(url)
                .then(r => r.json())
                .then(prices => {
                    // Atualizar opções do select de produtos
                    const productSelect = document.getElementById('pipProductSelect');
                    if (productSelect) {
                        Array.from(productSelect.options).forEach(opt => {
                            if (opt.value) {
                                const pid = opt.value;
                                const origPrice = parseFloat(opt.dataset.originalPrice) || 0;
                                const newPrice = prices[pid] !== undefined ? parseFloat(prices[pid]) : origPrice;
                                opt.dataset.price = newPrice.toFixed(2);
                                
                                // Atualizar texto da opção
                                const prodName = opt.textContent.split(' — ')[0].trim();
                                let label = prodName + ' — R$ ' + newPrice.toFixed(2).replace('.', ',');
                                if (newPrice !== origPrice) {
                                    label += ' (base: R$ ' + origPrice.toFixed(2).replace('.', ',') + ')';
                                }
                                opt.textContent = label;
                            }
                        });
                        // Atualizar preço se já havia um produto selecionado
                        if (productSelect.value) {
                            const selOpt = productSelect.options[productSelect.selectedIndex];
                            if (selOpt && selOpt.dataset.price) {
                                document.getElementById('pipPriceInput').value = parseFloat(selOpt.dataset.price).toFixed(2);
                            }
                        }
                    }

                    Swal.fire({ 
                        icon: 'info', 
                        title: 'Tabela atualizada!', 
                        text: 'Os preços dos produtos foram atualizados.',
                        timer: 1500, 
                        showConfirmButton: false 
                    });
                })
                .catch(err => {
                    console.error('Erro ao buscar preços:', err);
                    Swal.fire({ icon: 'error', title: 'Erro ao atualizar preços', timer: 2000, showConfirmButton: false });
                });
        });
    }
});
</script>
