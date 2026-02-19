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
        $isReadOnly = in_array($currentStage, ['concluido', 'cancelado']);
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
            <?php if ($currentStage === 'producao'): ?>
            <a href="/sistemaTiago/?page=production_board" class="btn btn-outline-success btn-sm"><i class="fas fa-tasks me-1"></i> Painel de Produção</a>
            <a href="/sistemaTiago/?page=pipeline&action=printProductionOrder&id=<?= $order['id'] ?>" target="_blank" class="btn btn-outline-warning btn-sm text-dark"><i class="fas fa-print me-1"></i> Ordem de Produção</a>
            <?php endif; ?>
            <?php if (!$isReadOnly): ?>
            <a href="/sistemaTiago/?page=orders&action=edit&id=<?= $order['id'] ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-edit me-1"></i> Editar Pedido</a>
            <?php endif; ?>
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
                    <?php if (!$isReadOnly): ?>
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
                    <?php endif; ?> <!-- /!$isReadOnly -->

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

    <?php if ($isReadOnly): ?>
    <div class="alert <?= $currentStage === 'cancelado' ? 'alert-danger' : 'alert-success' ?> d-flex align-items-center mb-4" role="alert">
        <i class="fas <?= $currentStage === 'cancelado' ? 'fa-ban' : 'fa-check-double' ?> me-2 fs-5"></i>
        <div>
            <strong>Pedido <?= $currentStage === 'cancelado' ? 'Cancelado' : 'Concluído' ?>.</strong>
            Todos os campos estão em modo de visualização. Use o botão "Mover para..." para reabrir o pedido, se necessário.
        </div>
    </div>
    <?php endif; ?>

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
                // Mas NÃO mostrar na etapa "producao" (onde exibimos o controle de setores)
                // Em modo read-only (concluido/cancelado), mostrar sempre
                $showProducts = $isReadOnly || ($currentStage !== 'contato' && $currentStage !== 'producao');
                ?>

                <?php if ($showProducts): ?>
                <!-- Produtos do Orçamento -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Produtos do Orçamento
                        <?php if (!$isReadOnly): ?>
                        <a href="/sistemaTiago/?page=orders&action=printQuote&id=<?= $order['id'] ?>" target="_blank" class="btn btn-sm btn-outline-success ms-3">
                            <i class="fas fa-print me-1"></i> Imprimir Orçamento
                        </a>
                        <?php endif; ?>
                    </legend>

                    <!-- ═══ Link de Catálogo para o Cliente ═══ -->
                    <?php if ($currentStage === 'orcamento' && !$isReadOnly): ?>
                    <div class="card border-info border-opacity-25 mb-3" id="catalogLinkSection">
                        <div class="card-header bg-info bg-opacity-10 py-2 d-flex align-items-center justify-content-between">
                            <h6 class="mb-0 text-info"><i class="fas fa-share-alt me-2"></i>Catálogo do Cliente</h6>
                            <span class="badge bg-info bg-opacity-75">
                                <i class="fas fa-magic me-1"></i> O cliente monta a lista!
                            </span>
                        </div>
                        <div class="card-body p-3">
                            <p class="small text-muted mb-3">
                                <i class="fas fa-info-circle me-1"></i>
                                Gere um link de catálogo exclusivo para este pedido (#<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>).
                                O cliente poderá navegar pelos produtos e adicionar ao carrinho.
                                Os itens adicionados aparecerão automaticamente no orçamento deste pedido em tempo real.
                            </p>
                            
                            <!-- Formulário para gerar novo link -->
                            <div id="catalogLinkForm">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Mostrar preços?</label>
                                        <select class="form-select form-select-sm" id="catalogShowPrices">
                                            <option value="0" selected>🚫 Não mostrar preços</option>
                                            <option value="1">✅ Sim, mostrar preços</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-muted">Validade (dias)</label>
                                        <select class="form-select form-select-sm" id="catalogExpires">
                                            <option value="">Sem expiração</option>
                                            <option value="1">1 dia</option>
                                            <option value="3">3 dias</option>
                                            <option value="7" selected>7 dias</option>
                                            <option value="15">15 dias</option>
                                            <option value="30">30 dias</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <button class="btn btn-info btn-sm w-100 fw-bold" id="btnGenerateCatalog" onclick="generateCatalogLink()">
                                            <i class="fas fa-magic me-1"></i> Gerar Link do Catálogo
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Link gerado (aparece abaixo do formulário) -->
                            <div id="catalogLinkActive" style="display:none;" class="mt-3">
                                <hr class="my-2">
                                <label class="form-label small fw-bold text-muted mb-1"><i class="fas fa-link me-1"></i>Link gerado para Pedido #<?= str_pad($order['id'], 4, '0', STR_PAD_LEFT) ?>: <span class="text-success" id="catalogLinkPriceInfo"></span></label>
                                <div class="input-group input-group-sm mb-2">
                                    <input type="text" class="form-control text-primary fw-bold" id="catalogLinkUrl" readonly onclick="this.select()" style="font-size:0.82rem;">
                                    <button class="btn btn-outline-success" onclick="copyCatalogLink()" title="Copiar link">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    <a id="catalogLinkOpen" href="#" target="_blank" class="btn btn-outline-primary" title="Abrir catálogo">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <button class="btn btn-outline-info" onclick="shareViaWhatsApp()" title="Enviar via WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="deactivateCatalogLink()" title="Desativar link">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </div>
                                <small class="text-muted" id="catalogLinkMeta"></small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!$isReadOnly): ?>
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
                    <?php endif; ?> <!-- /!$isReadOnly price table -->

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
                                    <?php if (!$isReadOnly): ?>
                                    <th class="text-center" style="width:80px;">Ações</th>
                                    <?php endif; ?>
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
                                    <?php if (!$isReadOnly): ?>
                                    <td class="text-center">
                                        <a href="/sistemaTiago/?page=orders&action=deleteItem&item_id=<?= $item['id'] ?>&order_id=<?= $order['id'] ?>&redirect=pipeline" 
                                           class="btn btn-sm btn-outline-danger btn-delete-item" title="Remover item">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                    <?php endif; ?>
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

                    <?php if (!$isReadOnly): ?>
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
                    <?php endif; ?> <!-- /!$isReadOnly add item form -->

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
                                            <?php if (!$isReadOnly): ?>
                                            <th class="text-center" style="width:80px;">Ações</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalExtras = 0; ?>
                                        <?php foreach ($extraCosts as $ec): ?>
                                        <?php $totalExtras += $ec['amount']; ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ec['description']) ?></td>
                                            <td class="text-end fw-bold <?= $ec['amount'] < 0 ? 'text-danger' : '' ?>">
                                                <?= $ec['amount'] < 0 ? '- R$ ' . number_format(abs($ec['amount']), 2, ',', '.') : 'R$ ' . number_format($ec['amount'], 2, ',', '.') ?>
                                            </td>
                                            <?php if (!$isReadOnly): ?>
                                            <td class="text-center">
                                                <a href="/sistemaTiago/?page=pipeline&action=deleteExtraCost&cost_id=<?= $ec['id'] ?>&order_id=<?= $order['id'] ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-delete-extra" title="Remover custo">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-warning">
                                            <td class="text-end fw-bold">Total Custos Extras:</td>
                                            <td class="text-end fw-bold <?= $totalExtras < 0 ? 'text-danger' : '' ?>">
                                                <?= $totalExtras < 0 ? '- R$ ' . number_format(abs($totalExtras), 2, ',', '.') : 'R$ ' . number_format($totalExtras, 2, ',', '.') ?>
                                            </td>
                                            <?php if (!$isReadOnly): ?><td></td><?php endif; ?>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <?php endif; ?>
                            <?php if (!$isReadOnly): ?>
                            <!-- Form para adicionar custo extra -->
                            <div class="row g-2 align-items-end" id="addExtraCostRow">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Descrição do custo</label>
                                    <input type="text" class="form-control form-control-sm" id="extraDescription" placeholder="Ex: Frete, Arte, Desconto especial...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted">Valor (R$)</label>
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text">R$</span>
                                        <input type="number" step="0.01" class="form-control" id="extraAmount" placeholder="Use negativo p/ desconto">
                                    </div>
                                    <div class="form-text small" style="font-size:0.7rem;"><i class="fas fa-info-circle me-1"></i>Valor negativo = desconto</div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-warning btn-sm w-100" id="btnAddExtraCost">
                                        <i class="fas fa-plus me-1"></i> Adicionar
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Observações do Orçamento (aparece no orçamento impresso) -->
                    <div class="mt-3">
                        <label class="form-label small fw-bold text-muted"><i class="fas fa-file-alt me-1"></i>Observações do Orçamento <small class="text-success">(aparece no orçamento impresso)</small></label>
                        <textarea class="form-control" name="quote_notes" rows="3" placeholder="Notas visíveis ao cliente no orçamento impresso..." <?= $isReadOnly ? 'disabled' : '' ?>><?= $order['quote_notes'] ?? '' ?></textarea>
                    </div>
                </fieldset>
                <?php else: ?>
                <!-- Manter valores atuais nos campos ocultos quando a seção de produtos não aparece -->
                <input type="hidden" name="quote_notes" value="<?= htmlspecialchars($order['quote_notes'] ?? '') ?>">
                <input type="hidden" name="price_table_id" value="<?= $order['price_table_id'] ?? '' ?>">
                <?php endif; ?>

                <?php if ($currentStage === 'producao' || ($isReadOnly && !empty($orderProductionSectors))): ?>
                <?php if (!empty($orderProductionSectors)): ?>
                <!-- ═══════════════════════════════════════════════════════════ -->
                <!-- ═══ CONTROLE DE SETORES DE PRODUÇÃO (POR PRODUTO) ═══ -->
                <!-- ═══════════════════════════════════════════════════════════ -->
                <?php
                    // Agrupar setores por order_item_id
                    $itemSectors = [];
                    foreach ($orderProductionSectors as $sec) {
                        $iid = $sec['order_item_id'];
                        if (!isset($itemSectors[$iid])) {
                            $itemSectors[$iid] = [
                                'product_name' => $sec['product_name'],
                                'product_id'   => $sec['product_id'],
                                'quantity'      => $sec['quantity'],
                                'sectors'       => [],
                            ];
                        }
                        $itemSectors[$iid]['sectors'][] = $sec;
                    }

                    // Filtrar itens: mostrar apenas se o usuário tem permissão para pelo menos 1 setor do item
                    $visibleItems = [];
                    foreach ($itemSectors as $iid => $itemData) {
                        $hasPermission = false;
                        foreach ($itemData['sectors'] as $sec) {
                            if (empty($userAllowedSectorIds) || in_array((int)$sec['sector_id'], $userAllowedSectorIds)) {
                                $hasPermission = true;
                                break;
                            }
                        }
                        if ($hasPermission) {
                            $visibleItems[$iid] = $itemData;
                        }
                    }

                    // Calcular progresso geral
                    $totalSteps = 0;
                    $completedSteps = 0;
                    foreach ($visibleItems as $itemData) {
                        foreach ($itemData['sectors'] as $sec) {
                            $totalSteps++;
                            if ($sec['status'] === 'concluido') $completedSteps++;
                        }
                    }
                    $progressPct = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;
                ?>
                <fieldset class="p-4 mb-4" style="border: 2px solid #27ae60; border-radius: 8px;">
                    <legend class="float-none w-auto px-3 fs-5 text-success">
                        <i class="fas fa-industry me-2"></i>Controle de Produção
                        <span class="badge bg-success bg-opacity-75 ms-2" style="font-size:0.7rem;"><?= $completedSteps ?>/<?= $totalSteps ?> setores</span>
                    </legend>

                    <!-- Barra de Progresso Geral -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted fw-bold">Progresso Geral da Produção</small>
                            <small class="fw-bold <?= $progressPct == 100 ? 'text-success' : 'text-primary' ?>"><?= $progressPct ?>%</small>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 5px;">
                            <div class="progress-bar <?= $progressPct == 100 ? 'bg-success' : 'bg-primary' ?> progress-bar-striped <?= ($progressPct > 0 && $progressPct < 100) ? 'progress-bar-animated' : '' ?>" 
                                 role="progressbar" style="width: <?= $progressPct ?>%;"></div>
                        </div>
                        <?php if ($progressPct == 100): ?>
                        <div class="alert alert-success py-1 px-3 mt-2 mb-0 small">
                            <i class="fas fa-check-double me-1"></i> Todos os produtos passaram por todos os setores! O pedido pode avançar.
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Tabela de Produtos com Stepper de Setores -->
                    <?php foreach ($visibleItems as $itemId => $itemData): 
                        $sectors = $itemData['sectors'];
                        $totalItemSectors = count($sectors);
                        $itemCompleted = 0;
                        $currentSector = null;
                        $currentSectorIdx = -1;
                        foreach ($sectors as $idx => $sec) {
                            if ($sec['status'] === 'concluido') {
                                $itemCompleted++;
                            }
                        }
                        // O setor atual é o primeiro pendente
                        foreach ($sectors as $idx => $sec) {
                            if ($sec['status'] === 'pendente') {
                                $currentSector = $sec;
                                $currentSectorIdx = $idx;
                                break;
                            }
                        }
                        $allDone = ($itemCompleted === $totalItemSectors);
                        $itemPct = $totalItemSectors > 0 ? round(($itemCompleted / $totalItemSectors) * 100) : 0;

                        // Permissão do usuário para o setor atual
                        $canActOnCurrent = false;
                        if ($currentSector) {
                            $canActOnCurrent = empty($userAllowedSectorIds) || in_array((int)$currentSector['sector_id'], $userAllowedSectorIds);
                        }
                    ?>
                    <div class="card border-0 shadow-sm mb-3 production-item-card <?= $allDone ? 'border-success' : '' ?>" data-item-id="<?= $itemId ?>">
                        <div class="card-body p-3">
                            <!-- Cabeçalho do Produto -->
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center">
                                    <?php if ($allDone): ?>
                                        <span class="badge bg-success rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary bg-opacity-75 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:28px;height:28px;font-size:0.7rem;">
                                            <?= $itemCompleted ?>/<?= $totalItemSectors ?>
                                        </span>
                                    <?php endif; ?>
                                    <div>
                                        <h6 class="mb-0 fw-bold"><?= htmlspecialchars($itemData['product_name']) ?></h6>
                                        <small class="text-muted">Qtd: <?= $itemData['quantity'] ?></small>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($allDone): ?>
                                        <span class="badge bg-success px-3 py-1"><i class="fas fa-check-double me-1"></i>Concluído</span>
                                    <?php elseif ($currentSector): ?>
                                        <span class="badge py-1 px-2" style="background:<?= $currentSector['color'] ?>;">
                                            <i class="<?= $currentSector['icon'] ?> me-1"></i><?= htmlspecialchars($currentSector['sector_name']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-50 px-2 py-1"><i class="fas fa-pause me-1"></i>Aguardando</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Stepper Visual dos Setores -->
                            <div class="production-stepper d-flex align-items-center position-relative py-2 px-1">
                                <?php foreach ($sectors as $idx => $sec): 
                                    $isDone = ($sec['status'] === 'concluido');
                                    $isActive = ($sec['status'] === 'pendente' && $currentSector && $sec['sector_id'] === $currentSector['sector_id']);
                                    $isPending = ($sec['status'] === 'pendente' && !$isActive);
                                    $isFirst = ($idx === 0);
                                    $isLast = ($idx === $totalItemSectors - 1);

                                    // Cor do step
                                    if ($isDone) {
                                        $stepBg = '#27ae60';
                                        $stepColor = '#fff';
                                        $stepBorder = '#27ae60';
                                    } elseif ($isActive) {
                                        $stepBg = '#fff';
                                        $stepColor = $sec['color'];
                                        $stepBorder = $sec['color'];
                                    } else {
                                        $stepBg = '#f0f0f0';
                                        $stepColor = '#bbb';
                                        $stepBorder = '#ddd';
                                    }

                                    $userCanSector = empty($userAllowedSectorIds) || in_array((int)$sec['sector_id'], $userAllowedSectorIds);
                                ?>
                                <?php if (!$isFirst): ?>
                                <!-- Linha conectora -->
                                <div class="flex-grow-1" style="height:3px;background:<?= $isDone ? '#27ae60' : '#e0e0e0' ?>;min-width:12px;"></div>
                                <?php endif; ?>
                                <!-- Step -->
                                <div class="production-step text-center position-relative flex-shrink-0" 
                                     data-bs-toggle="tooltip" data-bs-placement="top"
                                     title="<?= htmlspecialchars($sec['sector_name']) ?><?= $isDone && !empty($sec['completed_at']) ? ' — Concluído em '.date('d/m H:i', strtotime($sec['completed_at'])) : '' ?>">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto
                                        <?= $isActive ? 'sector-pulse' : '' ?>"
                                        style="width:36px;height:36px;font-size:0.8rem;
                                        background:<?= $stepBg ?>;color:<?= $stepColor ?>;
                                        border:2px solid <?= $stepBorder ?>;
                                        transition: all 0.3s;">
                                        <?php if ($isDone): ?>
                                            <i class="fas fa-check"></i>
                                        <?php else: ?>
                                            <i class="<?= $sec['icon'] ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small mt-1 <?= $isActive ? 'fw-bold' : ($isPending ? 'text-muted' : '') ?>" 
                                         style="font-size:0.65rem;max-width:70px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;
                                         color:<?= $isDone ? '#27ae60' : ($isActive ? $sec['color'] : '#999') ?>;">
                                        <?= htmlspecialchars($sec['sector_name']) ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Botão de Ação -->
                            <?php if (!$isReadOnly && !$allDone && $currentSector && $canActOnCurrent): ?>
                            <div class="mt-2 d-flex justify-content-between">
                                <div>
                                    <?php
                                    // Botão de retroceder: se há setor anterior concluído
                                    $revertSector = null;
                                    if ($currentSectorIdx > 0) {
                                        $prevSec = $sectors[$currentSectorIdx - 1];
                                        if ($prevSec['status'] === 'concluido') {
                                            $canRevertPrev = empty($userAllowedSectorIds) || in_array((int)$prevSec['sector_id'], $userAllowedSectorIds);
                                            if ($canRevertPrev) $revertSector = $prevSec;
                                        }
                                    }
                                    ?>
                                    <?php if ($revertSector): ?>
                                    <button type="button" class="btn btn-sm btn-outline-warning btn-sector-action"
                                            data-order-id="<?= $order['id'] ?>"
                                            data-item-id="<?= $itemId ?>"
                                            data-sector-id="<?= $revertSector['sector_id'] ?>"
                                            data-action="revert"
                                            data-sector-name="<?= htmlspecialchars($revertSector['sector_name']) ?>">
                                        <i class="fas fa-undo me-1"></i> Retroceder
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-success btn-sector-action"
                                            data-order-id="<?= $order['id'] ?>"
                                            data-item-id="<?= $itemId ?>"
                                            data-sector-id="<?= $currentSector['sector_id'] ?>"
                                            data-action="advance"
                                            data-sector-name="<?= htmlspecialchars($currentSector['sector_name']) ?>">
                                        <i class="fas fa-check me-1"></i> Concluir <strong><?= htmlspecialchars($currentSector['sector_name']) ?></strong>
                                        <?php 
                                        $nextIdx = $currentSectorIdx + 1;
                                        if ($nextIdx < $totalItemSectors):
                                        ?>
                                        <span class="ms-1 opacity-75">→ <?= htmlspecialchars($sectors[$nextIdx]['sector_name']) ?></span>
                                        <?php endif; ?>
                                    </button>
                                </div>
                            </div>
                            <?php elseif (!$isReadOnly && $allDone): ?>
                            <!-- Produto concluído: permitir retroceder o último setor -->
                            <?php
                            $lastSec = end($sectors);
                            $canRevertLast = empty($userAllowedSectorIds) || in_array((int)$lastSec['sector_id'], $userAllowedSectorIds);
                            ?>
                            <?php if ($canRevertLast): ?>
                            <div class="mt-2 d-flex justify-content-start">
                                <button type="button" class="btn btn-sm btn-outline-warning btn-sector-action"
                                        data-order-id="<?= $order['id'] ?>"
                                        data-item-id="<?= $itemId ?>"
                                        data-sector-id="<?= $lastSec['sector_id'] ?>"
                                        data-action="revert"
                                        data-sector-name="<?= htmlspecialchars($lastSec['sector_name']) ?>">
                                    <i class="fas fa-undo me-1"></i> Retroceder <strong><?= htmlspecialchars($lastSec['sector_name']) ?></strong>
                                </button>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </fieldset>
                <?php else: ?>
                <!-- Sem setores configurados para os produtos deste pedido -->
                <fieldset class="p-4 mb-4" style="border: 2px solid #e67e22; border-radius: 8px;">
                    <legend class="float-none w-auto px-3 fs-5 text-warning">
                        <i class="fas fa-industry me-2"></i>Setores de Produção
                    </legend>
                    <?php if (empty($orderItems)): ?>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Este pedido não possui produtos.</strong>
                        <br><small class="text-muted">Adicione produtos ao pedido na etapa de Orçamento para que os setores de produção sejam configurados automaticamente.</small>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Nenhum setor de produção configurado</strong> para os produtos deste pedido.
                        <br><small class="text-muted">Configure os setores nos cadastros de Produtos, Subcategorias ou Categorias para que o controle de produção funcione.</small>
                    </div>
                    <?php endif; ?>
                </fieldset>
                <?php endif; ?>
                <?php endif; ?>                <!-- Gerenciamento do Pedido -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-sliders-h me-2"></i>Gerenciamento</legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Prioridade</label>
                            <select class="form-select" name="priority" <?= $isReadOnly ? 'disabled' : '' ?>>
                                <option value="baixa" <?= ($order['priority'] ?? '') == 'baixa' ? 'selected' : '' ?>>🟢 Baixa</option>
                                <option value="normal" <?= ($order['priority'] ?? 'normal') == 'normal' ? 'selected' : '' ?>>🔵 Normal</option>
                                <option value="alta" <?= ($order['priority'] ?? '') == 'alta' ? 'selected' : '' ?>>🟡 Alta</option>
                                <option value="urgente" <?= ($order['priority'] ?? '') == 'urgente' ? 'selected' : '' ?>>🔴 Urgente</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Prazo (Deadline)</label>
                            <input type="date" class="form-control" name="deadline" value="<?= $order['deadline'] ?? '' ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">Responsável</label>
                            <select class="form-select" name="assigned_to" <?= $isReadOnly ? 'disabled' : '' ?>>
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
                            <textarea class="form-control" name="internal_notes" rows="3" placeholder="Notas internas sobre este pedido..." <?= $isReadOnly ? 'disabled' : '' ?>><?= $order['internal_notes'] ?? '' ?></textarea>
                        </div>
                    </div>
                </fieldset>

                <?php
                // Campos de Envio/Entrega: só aparecem nas etapas de preparação, envio ou concluído
                // Em modo read-only (concluido/cancelado), mostrar sempre
                $showShipping = $isReadOnly || in_array($currentStage, ['preparacao', 'envio', 'concluido']);
                // Campos Financeiro: só aparecem nas etapas venda, financeiro ou concluído
                $showFinancial = $isReadOnly || in_array($currentStage, ['venda', 'financeiro', 'concluido']);
                ?>

                <?php if ($showFinancial): ?>
                <!-- Financeiro -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-coins me-2"></i>Financeiro</legend>
                    <input type="hidden" name="discount" value="<?= $order['discount'] ?? 0 ?>">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Valor Total</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control fw-bold" value="<?= number_format($order['total_amount'], 2, ',', '.') ?>" disabled>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Status Pagamento</label>
                            <select class="form-select" name="payment_status" <?= $isReadOnly ? 'disabled' : '' ?>>
                                <option value="pendente" <?= ($order['payment_status'] ?? '') == 'pendente' ? 'selected' : '' ?>>⏳ Pendente</option>
                                <option value="parcial" <?= ($order['payment_status'] ?? '') == 'parcial' ? 'selected' : '' ?>>💳 Parcial</option>
                                <option value="pago" <?= ($order['payment_status'] ?? '') == 'pago' ? 'selected' : '' ?>>✅ Pago</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Forma de Pagamento</label>
                            <select class="form-select" name="payment_method" id="finPaymentMethod" <?= $isReadOnly ? 'disabled' : '' ?>>
                                <option value="">Selecione...</option>
                                <option value="dinheiro" <?= ($order['payment_method'] ?? '') == 'dinheiro' ? 'selected' : '' ?>>💵 Dinheiro</option>
                                <option value="pix" <?= ($order['payment_method'] ?? '') == 'pix' ? 'selected' : '' ?>>📱 PIX</option>
                                <option value="cartao_credito" <?= ($order['payment_method'] ?? '') == 'cartao_credito' ? 'selected' : '' ?>>💳 Cartão Crédito</option>
                                <option value="cartao_debito" <?= ($order['payment_method'] ?? '') == 'cartao_debito' ? 'selected' : '' ?>>💳 Cartão Débito</option>
                                <option value="boleto" <?= ($order['payment_method'] ?? '') == 'boleto' ? 'selected' : '' ?>>📄 Boleto</option>
                                <option value="transferencia" <?= ($order['payment_method'] ?? '') == 'transferencia' ? 'selected' : '' ?>>🏦 Transferência</option>
                            </select>
                        </div>
                    </div>

                    <!-- Parcelamento (aparece quando forma de pagamento aceita parcelas) -->
                    <div class="row g-3 mt-1" id="installmentRow" style="display:none;">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Nº de Parcelas</label>
                            <select class="form-select" name="installments" id="finInstallments" <?= $isReadOnly ? 'disabled' : '' ?>>
                                <option value="">À vista</option>
                                <?php for ($i = 2; $i <= 12; $i++): ?>
                                <option value="<?= $i ?>" <?= ($order['installments'] ?? '') == $i ? 'selected' : '' ?>><?= $i ?>x</option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Valor por Parcela</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="text" class="form-control fw-bold" id="finInstallmentValue" name="installment_value_display" disabled
                                       value="<?= ($order['installment_value'] ?? 0) > 0 ? number_format($order['installment_value'], 2, ',', '.') : '' ?>">
                                <input type="hidden" name="installment_value" id="finInstallmentValueHidden" value="<?= $order['installment_value'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="alert alert-info py-1 px-3 mb-0 small w-100" id="installmentInfo" style="display:none;">
                                <i class="fas fa-calculator me-1"></i>
                                <span id="installmentInfoText"></span>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php else: ?>
                <!-- Manter valores atuais nos campos ocultos para não perder ao salvar -->
                <input type="hidden" name="discount" value="<?= $order['discount'] ?? 0 ?>">
                <input type="hidden" name="payment_status" value="<?= $order['payment_status'] ?? 'pendente' ?>">
                <input type="hidden" name="payment_method" value="<?= $order['payment_method'] ?? '' ?>">
                <input type="hidden" name="installments" value="<?= $order['installments'] ?? '' ?>">
                <input type="hidden" name="installment_value" value="<?= $order['installment_value'] ?? '' ?>">
                <?php endif; ?>

                <?php if ($showShipping): ?>
                <!-- Envio / Entrega -->
                <fieldset class="p-4 mb-4">
                    <legend class="float-none w-auto px-2 fs-5 text-primary"><i class="fas fa-truck me-2"></i>Envio / Entrega</legend>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Tipo de Envio</label>
                            <select class="form-select" name="shipping_type" id="shippingType" <?= $isReadOnly ? 'disabled' : '' ?>>
                                <option value="retirada" <?= ($order['shipping_type'] ?? '') == 'retirada' ? 'selected' : '' ?>>🏪 Retirada na loja</option>
                                <option value="entrega" <?= ($order['shipping_type'] ?? '') == 'entrega' ? 'selected' : '' ?>>🏍️ Entrega própria</option>
                                <option value="correios" <?= ($order['shipping_type'] ?? '') == 'correios' ? 'selected' : '' ?>>📦 Correios</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Código de Rastreio</label>
                            <input type="text" class="form-control" name="tracking_code" placeholder="Ex: BR123456789" value="<?= $order['tracking_code'] ?? '' ?>" <?= $isReadOnly ? 'disabled' : '' ?>>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">Endereço de Entrega</label>
                            <textarea class="form-control" name="shipping_address" rows="1" placeholder="Endereço completo..." <?= $isReadOnly ? 'disabled' : '' ?>><?= !empty($order['shipping_address']) ? htmlspecialchars($order['shipping_address']) : htmlspecialchars($customerFormattedAddress) ?></textarea>
                        </div>
                    </div>
                </fieldset>
                <?php else: ?>
                <!-- Manter valores atuais nos campos ocultos para não perder ao salvar -->
                <input type="hidden" name="shipping_type" value="<?= $order['shipping_type'] ?? 'retirada' ?>">
                <input type="hidden" name="shipping_address" value="<?= htmlspecialchars($order['shipping_address'] ?? '') ?>">
                <input type="hidden" name="tracking_code" value="<?= $order['tracking_code'] ?? '' ?>">
                <?php endif; ?>

                <?php if (!$isReadOnly): ?>
                <div class="text-end mb-4">
                    <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-2"></i>Salvar Alterações</button>
                </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Coluna Direita: Timeline / Histórico -->
        <div class="col-lg-4">
            <!-- Histórico de Movimentação do Pipeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom p-3">
                    <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-history me-2"></i>Histórico de Movimentação</h6>
                </div>
                <div class="card-body p-3" style="max-height: 400px; overflow-y: auto;">
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

            <!-- ═══ Histórico de Registros dos Produtos ═══ -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-success fw-bold"><i class="fas fa-clipboard-list me-2"></i>Registros dos Produtos</h6>
                        <?php if (!empty($orderItems) && !$isReadOnly): ?>
                        <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="collapse" data-bs-target="#collapseAddLog">
                            <i class="fas fa-plus me-1"></i> Novo
                        </button>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($orderItems) && !$isReadOnly): ?>
                <div class="collapse" id="collapseAddLog">
                    <div class="p-3 border-bottom bg-light">
                        <form id="formAddItemLogDetail" enctype="multipart/form-data">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <div class="mb-2">
                                <label class="form-label small fw-bold text-muted mb-1">Produto</label>
                                <select class="form-select form-select-sm" name="order_item_id" id="detailLogItemSelect" required>
                                    <option value="">Selecione o produto...</option>
                                    <?php foreach ($orderItems as $oi): ?>
                                    <option value="<?= $oi['id'] ?>"><?= htmlspecialchars($oi['product_name'] ?? 'Produto #'.$oi['product_id']) ?> (Qtd: <?= $oi['quantity'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-2">
                                <textarea class="form-control form-control-sm" name="message" rows="2" 
                                          placeholder="Observação, registro de erro, instrução..."></textarea>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="btn btn-sm btn-outline-secondary mb-0" for="detailLogFile">
                                        <i class="fas fa-paperclip me-1"></i> Anexar
                                    </label>
                                    <input type="file" class="d-none" id="detailLogFile" name="file" accept="image/*,.pdf">
                                    <small class="text-muted d-none" id="detailLogFileLabel"></small>
                                </div>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-1"></i> Adicionar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                    <?php if (empty($orderItemLogs)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clipboard d-block mb-2" style="font-size:2rem;opacity:0.4;"></i>
                            <p class="mb-0">Nenhum registro de produto.<br><small>Clique em "Novo" para adicionar.</small></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($orderItemLogs as $log): 
                            $isImage = !empty($log['file_type']) && str_starts_with($log['file_type'], 'image/');
                            $isPdf = ($log['file_type'] ?? '') === 'application/pdf';
                        ?>
                        <div class="d-flex gap-2 mb-3 pb-3 border-bottom detail-log-entry">
                            <div class="flex-shrink-0">
                                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width:32px;height:32px;background:<?= $isImage ? '#e8f5e9' : ($isPdf ? '#fce4ec' : '#e3f2fd') ?>;">
                                    <?php if ($isImage): ?>
                                        <i class="fas fa-image text-success" style="font-size:0.8rem;"></i>
                                    <?php elseif ($isPdf): ?>
                                        <i class="fas fa-file-pdf text-danger" style="font-size:0.8rem;"></i>
                                    <?php else: ?>
                                        <i class="fas fa-comment text-primary" style="font-size:0.8rem;"></i>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 me-1" style="font-size:0.6rem;">
                                            <i class="fas fa-box me-1"></i><?= htmlspecialchars($log['product_name'] ?? 'Produto') ?>
                                        </span>
                                        <span class="small fw-bold"><?= htmlspecialchars($log['user_name'] ?? 'Sistema') ?></span>
                                    </div>
                                    <div class="d-flex align-items-center gap-1">
                                        <span class="text-muted" style="font-size:0.6rem;"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></span>
                                        <?php if (!$isReadOnly): ?>
                                        <button type="button" class="btn btn-sm p-0 text-danger btn-delete-detail-log" 
                                                data-log-id="<?= $log['id'] ?>" title="Excluir" style="font-size:0.65rem;line-height:1;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if (!empty($log['message'])): ?>
                                <div class="small mt-1" style="white-space:pre-wrap;"><?= htmlspecialchars($log['message']) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($log['file_path'])): ?>
                                    <?php if ($isImage): ?>
                                    <div class="mt-2">
                                        <a href="<?= $log['file_path'] ?>" target="_blank">
                                            <img src="<?= $log['file_path'] ?>" class="rounded border" 
                                                 style="max-width:100%;max-height:150px;" alt="<?= htmlspecialchars($log['file_name']) ?>">
                                        </a>
                                        <div class="small text-muted mt-1"><i class="fas fa-image me-1"></i><?= htmlspecialchars($log['file_name']) ?></div>
                                    </div>
                                    <?php elseif ($isPdf): ?>
                                    <div class="mt-2">
                                        <a href="<?= $log['file_path'] ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-file-pdf me-1"></i><?= htmlspecialchars($log['file_name']) ?>
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ═══ Estilos Controle de Produção por Produto ═══ */
.production-item-card {
    transition: box-shadow 0.2s;
    border-left: 3px solid #e0e0e0 !important;
}
.production-item-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
}
.production-item-card.border-success {
    border-left: 3px solid #27ae60 !important;
}
.production-stepper {
    overflow-x: auto;
    scrollbar-width: thin;
}
.production-step {
    min-width: 50px;
}
@keyframes sectorPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(52, 152, 219, 0.4); }
    50% { box-shadow: 0 0 0 6px rgba(52, 152, 219, 0); }
}
.sector-pulse {
    animation: sectorPulse 2s ease-in-out infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status'])): ?>
    // Limpar o parâmetro status da URL para não disparar novamente
    if (window.history.replaceState) {
        const url = new URL(window.location);
        url.searchParams.delete('status');
        window.history.replaceState({}, '', url);
    }
    <?php endif; ?>
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

            if (!description || !amount || parseFloat(amount) === 0) {
                Swal.fire({ icon: 'warning', title: 'Preencha a descrição e o valor', text: 'O valor não pode ser zero.', timer: 2000, showConfirmButton: false });
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

    // ════════════════════════════════════════════════════════
    // ── CATÁLOGO DO CLIENTE — Geração e gestão de links ──
    // ════════════════════════════════════════════════════════
    
    let catalogLinkData = null;
    
    // Verificar se já existe link ativo para este pedido
    function checkExistingCatalogLink() {
        fetch('/sistemaTiago/?page=pipeline&action=getCatalogLink&order_id=<?= $order['id'] ?>')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    catalogLinkData = data;
                    showActiveCatalogLink(data);
                } else {
                    showCatalogLinkForm();
                }
            })
            .catch(() => showCatalogLinkForm());
    }
    
    // Inicializar verificação do link
    if (document.getElementById('catalogLinkSection')) {
        checkExistingCatalogLink();
    }
});

// ── Funções globais do catálogo (fora do DOMContentLoaded) ──

function generateCatalogLink() {
    const showPrices = document.getElementById('catalogShowPrices').value;
    const expiresIn = document.getElementById('catalogExpires').value;
    const btn = document.getElementById('btnGenerateCatalog');
    
    // Desabilitar botão enquanto gera
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Gerando...';
    
    fetch('/sistemaTiago/?page=pipeline&action=generateCatalogLink', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=<?= $order['id'] ?>&show_prices=${showPrices}&expires_in=${expiresIn}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            catalogLinkData = data;
            showActiveCatalogLink(data);
        } else {
            alert(data.message || 'Erro ao gerar link');
        }
    })
    .catch(() => {
        alert('Erro de conexão ao gerar o link.');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-magic me-1"></i> Gerar Link do Catálogo';
    });
}

function copyCatalogLink() {
    const url = document.getElementById('catalogLinkUrl').value;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({ icon: 'success', title: 'Link copiado!', timer: 1500, showConfirmButton: false, toast: true, position: 'top-end' });
    });
}

function shareViaWhatsApp() {
    const url = document.getElementById('catalogLinkUrl').value;
    const phone = '<?= preg_replace('/\D/', '', $order['customer_phone'] ?? '') ?>';
    const customerName = '<?= htmlspecialchars($order['customer_name'] ?? 'cliente') ?>';
    const companyName = 'nossa equipe';
    
    const message = encodeURIComponent(
        `Olá, ${customerName}! 😊\n\n` +
        `Preparamos um catálogo personalizado para você montar sua lista de produtos:\n\n` +
        `📋 *Acesse o catálogo:*\n${url}\n\n` +
        `Você pode adicionar os produtos que desejar ao carrinho. Depois, ${companyName} irá preparar o orçamento completo!\n\n` +
        `Qualquer dúvida, estamos à disposição! 🙌`
    );
    
    const waUrl = phone 
        ? `https://wa.me/55${phone}?text=${message}` 
        : `https://wa.me/?text=${message}`;
    
    window.open(waUrl, '_blank');
}

function deactivateCatalogLink() {
    if (!confirm('Desativar link? O cliente não poderá mais acessar o catálogo.')) return;
    
    fetch('/sistemaTiago/?page=pipeline&action=deactivateCatalogLink', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'order_id=<?= $order['id'] ?>'
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showCatalogLinkForm();
        }
    });
}

function showActiveCatalogLink(data) {
    const activeEl = document.getElementById('catalogLinkActive');
    const formEl = document.getElementById('catalogLinkForm');
    if (!activeEl || !formEl) return;
    
    // Mostrar o link abaixo do formulário
    activeEl.style.display = '';
    
    // Desabilitar campos do formulário (já tem link ativo)
    document.getElementById('catalogShowPrices').disabled = true;
    document.getElementById('catalogExpires').disabled = true;
    const btn = document.getElementById('btnGenerateCatalog');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Link ativo';
    btn.classList.replace('btn-info', 'btn-success');
    
    document.getElementById('catalogLinkUrl').value = data.url;
    document.getElementById('catalogLinkOpen').href = data.url;
    
    const priceInfo = document.getElementById('catalogLinkPriceInfo');
    priceInfo.textContent = data.show_prices ? '(com preços)' : '(sem preços)';
    
    const meta = document.getElementById('catalogLinkMeta');
    let metaText = '';
    if (data.created_at) {
        metaText = 'Criado em ' + formatDateBR(data.created_at);
    }
    if (data.expires_at) {
        metaText += ' · Expira em ' + formatDateBR(data.expires_at);
    } else {
        metaText += ' · Sem expiração';
    }
    meta.textContent = metaText;
}

function showCatalogLinkForm() {
    const activeEl = document.getElementById('catalogLinkActive');
    const formEl = document.getElementById('catalogLinkForm');
    if (!activeEl || !formEl) return;
    
    // Esconder o link e reabilitar o formulário
    activeEl.style.display = 'none';
    
    document.getElementById('catalogShowPrices').disabled = false;
    document.getElementById('catalogExpires').disabled = false;
    const btn = document.getElementById('btnGenerateCatalog');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-magic me-1"></i> Gerar Link do Catálogo';
    btn.classList.replace('btn-success', 'btn-info');
}

function formatDateBR(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    return d.toLocaleDateString('pt-BR') + ' ' + d.toLocaleTimeString('pt-BR', {hour: '2-digit', minute: '2-digit'});
}

// ════════════════════════════════════════════════════════════
// ═══ REGISTROS DOS PRODUTOS (Logs) — AJAX Form + Delete ═══
// ════════════════════════════════════════════════════════════
(function() {
    // Mostrar nome do arquivo selecionado
    var detailLogFile = document.getElementById('detailLogFile');
    if (detailLogFile) {
        detailLogFile.addEventListener('change', function() {
            var label = document.getElementById('detailLogFileLabel');
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
                label.classList.remove('d-none');
            } else {
                label.classList.add('d-none');
            }
        });
    }

    // Enviar novo log (AJAX com upload)
    var formDetail = document.getElementById('formAddItemLogDetail');
    if (formDetail) {
        formDetail.addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            var submitBtn = this.querySelector('button[type="submit"]');
            var itemSelect = document.getElementById('detailLogItemSelect');

            if (!itemSelect.value) {
                Swal.fire({ icon: 'warning', title: 'Selecione um produto', timer: 2000, showConfirmButton: false });
                return;
            }

            var msg = formData.get('message') || '';
            var file = formData.get('file');
            if (!msg.trim() && (!file || !file.size)) {
                Swal.fire({ icon: 'warning', title: 'Informe uma mensagem ou arquivo', timer: 2000, showConfirmButton: false });
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Enviando...';

            fetch('/sistemaTiago/?page=pipeline&action=addItemLog', {
                method: 'POST',
                body: formData
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i> Adicionar';
                if (data.success) {
                    Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true })
                        .fire({ icon: 'success', title: 'Registro adicionado!' });
                    setTimeout(function() { location.reload(); }, 800);
                } else {
                    Swal.fire({ icon: 'error', title: 'Erro', text: data.message || 'Não foi possível adicionar.', timer: 3000 });
                }
            })
            .catch(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-plus me-1"></i> Adicionar';
                Swal.fire({ icon: 'error', title: 'Erro de conexão', timer: 2000, showConfirmButton: false });
            });
        });
    }

    // Excluir log de produto (detail view)
    document.querySelectorAll('.btn-delete-detail-log').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var logId = this.dataset.logId;
            Swal.fire({
                title: 'Excluir registro?',
                text: 'Esta ação não pode ser desfeita.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#c0392b',
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
                cancelButtonText: 'Cancelar'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('/sistemaTiago/?page=pipeline&action=deleteItemLog', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'log_id=' + logId
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) {
                            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 1500, timerProgressBar: true })
                                .fire({ icon: 'success', title: 'Registro excluído!' });
                            setTimeout(function() { location.reload(); }, 800);
                        } else {
                            Swal.fire({ icon: 'error', title: 'Erro', text: 'Não foi possível excluir.', timer: 2000 });
                        }
                    })
                    .catch(function() {
                        Swal.fire({ icon: 'error', title: 'Erro de conexão', timer: 2000, showConfirmButton: false });
                    });
                }
            });
        });
    });
})();

<?php if ($currentStage === 'producao' && !empty($orderProductionSectors)): ?>
// ════════════════════════════════════════════════════════════
// ═══ CONTROLE DE PRODUÇÃO POR PRODUTO — Stepper + AJAX ═══
// ════════════════════════════════════════════════════════════
window.addEventListener('load', function() {
    // Inicializar tooltips do Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            new bootstrap.Tooltip(el);
        }
    });

    // Botões de ação (Concluir / Retroceder setor)
    document.querySelectorAll('.btn-sector-action').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const orderId = this.dataset.orderId;
            const itemId = this.dataset.itemId;
            const sectorId = this.dataset.sectorId;
            const action = this.dataset.action;
            const sectorName = this.dataset.sectorName;
            const btnEl = this;

            const isRevert = (action === 'revert');
            var confirmTitle, confirmText, confirmIcon, confirmBtn, confirmColor;

            if (isRevert) {
                confirmTitle = 'Retroceder setor?';
                confirmText = `Deseja retroceder o setor <strong>${sectorName}</strong>?<br><small class="text-muted">O progresso deste setor será revertido.</small>`;
                confirmIcon = 'warning';
                confirmBtn = '<i class="fas fa-undo me-1"></i> Retroceder';
                confirmColor = '#e67e22';
            } else {
                confirmTitle = 'Concluir setor?';
                confirmText = `Marcar <strong>${sectorName}</strong> como concluído?`;
                confirmIcon = 'success';
                confirmBtn = '<i class="fas fa-check me-1"></i> Concluir';
                confirmColor = '#27ae60';
            }

            Swal.fire({
                title: confirmTitle,
                html: confirmText,
                icon: confirmIcon,
                showCancelButton: true,
                confirmButtonText: confirmBtn,
                cancelButtonText: 'Cancelar',
                confirmButtonColor: confirmColor
            }).then((result) => {
                if (result.isConfirmed) {
                    btnEl.disabled = true;
                    btnEl.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Processando...';

                    fetch('/sistemaTiago/?page=pipeline&action=moveSector', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `order_id=${orderId}&order_item_id=${itemId}&sector_id=${sectorId}&move_action=${action}`
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            // Usar toast separado para não fechar/conflitar com outros Swals
                            var toastMixin = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 1500,
                                timerProgressBar: true,
                                didOpen: function(toast) {
                                    toast.addEventListener('mouseenter', Swal.stopTimer);
                                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                                }
                            });
                            toastMixin.fire({
                                icon: 'success',
                                title: isRevert ? 'Setor retrocedido!' : 'Setor concluído!'
                            });
                            setTimeout(function() { location.reload(); }, 800);
                        } else {
                            btnEl.disabled = false;
                            btnEl.innerHTML = isRevert 
                                ? '<i class="fas fa-undo me-1"></i> Retroceder' 
                                : '<i class="fas fa-check me-1"></i> Concluir';
                            Swal.fire({ 
                                icon: 'error', 
                                title: 'Erro', 
                                text: data.message || 'Não foi possível processar.',
                                timer: 3000 
                            });
                        }
                    })
                    .catch(function(err) {
                        btnEl.disabled = false;
                        console.error('Erro:', err);
                        Swal.fire({ icon: 'error', title: 'Erro de conexão', timer: 2000, showConfirmButton: false });
                    });
                }
            });
        });
    });
});
<?php endif; ?>

<?php if ($currentStage === 'orcamento'): ?>
// ── Auto-refresh: recarregar a página se itens mudarem (polling a cada 15s) ──
let lastItemCount = <?= count($orderItems ?? []) ?>;
let catalogPollingToken = null;

// Primeiro buscar o token do link ativo (se houver)
fetch('/sistemaTiago/?page=pipeline&action=getCatalogLink&order_id=<?= $order['id'] ?>')
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            catalogPollingToken = data.token;
        }
    })
    .catch(() => {});

setInterval(() => {
    if (!catalogPollingToken) return;
    fetch('/sistemaTiago/?page=catalog&action=getCart&token=' + catalogPollingToken)
        .then(r => r.json())
        .then(data => {
            if (data.success && data.cart_count !== lastItemCount) {
                lastItemCount = data.cart_count;
                location.reload();
            }
        })
        .catch(() => {});
}, 15000);
<?php endif; ?>

<?php if ($showFinancial): ?>
// ── Parcelamento: mostrar/ocultar e calcular valor por parcela ──
(function() {
    const paymentMethod = document.getElementById('finPaymentMethod');
    const installmentRow = document.getElementById('installmentRow');
    const installments = document.getElementById('finInstallments');
    const installmentValue = document.getElementById('finInstallmentValue');
    const installmentValueHidden = document.getElementById('finInstallmentValueHidden');
    const installmentInfo = document.getElementById('installmentInfo');
    const installmentInfoText = document.getElementById('installmentInfoText');
    const discountField = document.getElementById('finDiscount');
    
    if (!paymentMethod || !installmentRow) return;
    
    const totalAmount = <?= (float)($order['total_amount'] ?? 0) ?>;
    
    // Formas de pagamento que aceitam parcelamento
    const parcelableMethods = ['cartao_credito', 'boleto'];
    
    function toggleInstallmentRow() {
        const show = parcelableMethods.includes(paymentMethod.value);
        installmentRow.style.display = show ? '' : 'none';
        if (!show) {
            installments.value = '';
            installmentValue.value = '';
            installmentValueHidden.value = '';
            installmentInfo.style.display = 'none';
        } else {
            calcInstallment();
        }
    }
    
    function calcInstallment() {
        const n = parseInt(installments.value) || 0;
        const discount = parseFloat(discountField.value) || 0;
        const finalTotal = Math.max(0, totalAmount - discount);
        
        if (n >= 2 && finalTotal > 0) {
            const perInstallment = (finalTotal / n).toFixed(2);
            installmentValue.value = parseFloat(perInstallment).toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            installmentValueHidden.value = perInstallment;
            installmentInfo.style.display = '';
            installmentInfoText.textContent = `${n}x de R$ ${parseFloat(perInstallment).toLocaleString('pt-BR', {minimumFractionDigits: 2})} = R$ ${finalTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
        } else {
            installmentValue.value = finalTotal > 0 ? finalTotal.toLocaleString('pt-BR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
            installmentValueHidden.value = finalTotal > 0 ? finalTotal.toFixed(2) : '';
            installmentInfo.style.display = 'none';
        }
    }
    
    paymentMethod.addEventListener('change', toggleInstallmentRow);
    installments.addEventListener('change', calcInstallment);
    discountField.addEventListener('input', calcInstallment);
    
    // Inicializar
    toggleInstallmentRow();
})();
<?php endif; ?>
</script>
