<?php $activeTab = $_GET['tab'] ?? 'company'; ?>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-building me-2"></i>Configurações do Sistema</h1>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'company' ? 'active' : '' ?>" href="/sistemaTiago/?page=settings&tab=company">
                <i class="fas fa-building me-1"></i> Dados da Empresa
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'prices' ? 'active' : '' ?>" href="/sistemaTiago/?page=settings&tab=prices">
                <i class="fas fa-tags me-1"></i> Tabelas de Preço
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $activeTab === 'preparation' ? 'active' : '' ?>" href="/sistemaTiago/?page=settings&tab=preparation">
                <i class="fas fa-boxes-packing me-1"></i> Etapas de Preparo
            </a>
        </li>
    </ul>

    <?php if ($activeTab === 'company'): ?>
    <!-- ══════════ ABA: DADOS DA EMPRESA ══════════ -->
    <form method="POST" action="/sistemaTiago/?page=settings&action=saveCompany" enctype="multipart/form-data">
        <div class="row">
            <div class="col-lg-8">
                <!-- Dados Básicos -->
                <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold">
                        <i class="fas fa-id-card me-2"></i>Dados Básicos
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nome da Empresa</label>
                            <input type="text" class="form-control" name="company_name" value="<?= htmlspecialchars($settings['company_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">CNPJ / CPF</label>
                            <input type="text" class="form-control" name="company_document" value="<?= htmlspecialchars($settings['company_document'] ?? '') ?>" placeholder="00.000.000/0000-00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Telefone</label>
                            <input type="text" class="form-control" name="company_phone" value="<?= htmlspecialchars($settings['company_phone'] ?? '') ?>" placeholder="(00) 00000-0000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">E-mail</label>
                            <input type="email" class="form-control" name="company_email" value="<?= htmlspecialchars($settings['company_email'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Website</label>
                            <input type="text" class="form-control" name="company_website" value="<?= htmlspecialchars($settings['company_website'] ?? '') ?>" placeholder="https://">
                        </div>
                    </div>
                </fieldset>

                <!-- Endereço -->
                <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold">
                        <i class="fas fa-map-marker-alt me-2"></i>Endereço
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">CEP</label>
                            <input type="text" class="form-control" name="company_zipcode" value="<?= htmlspecialchars($settings['company_zipcode'] ?? '') ?>" placeholder="00000-000">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Tipo Logradouro</label>
                            <select class="form-select" name="company_address_type">
                                <?php 
                                $tipos = ['Rua', 'Avenida', 'Travessa', 'Praça', 'Alameda', 'Rodovia', 'Outro'];
                                foreach ($tipos as $t): ?>
                                <option value="<?= $t ?>" <?= ($settings['company_address_type'] ?? '') === $t ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Nome do Logradouro</label>
                            <input type="text" class="form-control" name="company_address_name" value="<?= htmlspecialchars($settings['company_address_name'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Número</label>
                            <input type="text" class="form-control" name="company_address_number" value="<?= htmlspecialchars($settings['company_address_number'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Bairro</label>
                            <input type="text" class="form-control" name="company_neighborhood" value="<?= htmlspecialchars($settings['company_neighborhood'] ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Complemento</label>
                            <input type="text" class="form-control" name="company_complement" value="<?= htmlspecialchars($settings['company_complement'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">Cidade</label>
                            <input type="text" class="form-control" name="company_city" value="<?= htmlspecialchars($settings['company_city'] ?? '') ?>">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold small text-muted">Estado (UF)</label>
                            <input type="text" class="form-control" name="company_state" value="<?= htmlspecialchars($settings['company_state'] ?? '') ?>" maxlength="2" placeholder="SP">
                        </div>
                    </div>
                </fieldset>

                <!-- Configurações de Orçamento -->
                <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold">
                        <i class="fas fa-file-invoice me-2"></i>Configurações de Orçamento
                    </legend>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-muted">Validade (dias)</label>
                            <input type="number" class="form-control" name="quote_validity_days" value="<?= htmlspecialchars($settings['quote_validity_days'] ?? '15') ?>" min="1">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label fw-bold small text-muted">Nota de Rodapé do Orçamento</label>
                            <textarea class="form-control" name="quote_footer_note" rows="2"><?= htmlspecialchars($settings['quote_footer_note'] ?? '') ?></textarea>
                        </div>
                    </div>
                </fieldset>

                <div class="text-end mb-4">
                    <button type="submit" class="btn btn-primary px-4 fw-bold"><i class="fas fa-save me-2"></i>Salvar Configurações</button>
                </div>
            </div>

            <!-- Coluna Direita: Logo -->
            <div class="col-lg-4">
                <fieldset class="border p-4 mb-4 rounded bg-white shadow-sm">
                    <legend class="float-none w-auto px-2 fs-5 text-primary fw-bold">
                        <i class="fas fa-image me-2"></i>Logo da Empresa
                    </legend>
                    <div class="text-center mb-3">
                        <?php if (!empty($settings['company_logo']) && file_exists($settings['company_logo'])): ?>
                            <img src="/sistemaTiago/<?= $settings['company_logo'] ?>" alt="Logo" class="img-fluid rounded shadow-sm" style="max-height: 200px;">
                            <div class="mt-2">
                                <label class="form-check-label small text-danger">
                                    <input type="checkbox" name="remove_logo" value="1" class="form-check-input"> Remover logo atual
                                </label>
                            </div>
                        <?php else: ?>
                            <div class="border rounded p-4 text-muted bg-light">
                                <i class="fas fa-image d-block mb-2" style="font-size:3rem; opacity:0.3;"></i>
                                <small>Nenhuma logo cadastrada</small>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label class="form-label fw-bold small text-muted"><?= !empty($settings['company_logo']) ? 'Substituir' : 'Enviar' ?> Logo</label>
                        <input type="file" class="form-control form-control-sm" name="company_logo" accept="image/*">
                        <small class="text-muted">Formatos: JPG, PNG, SVG. Recomendado: 300x100px</small>
                    </div>
                </fieldset>

                <!-- Preview do Endereço -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light py-2">
                        <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-eye me-2"></i>Prévia do Cabeçalho</h6>
                    </div>
                    <div class="card-body text-center">
                        <?php if (!empty($settings['company_logo']) && file_exists($settings['company_logo'])): ?>
                            <img src="/sistemaTiago/<?= $settings['company_logo'] ?>" alt="Logo" style="max-height: 60px;" class="mb-2">
                        <?php else: ?>
                            <i class="fas fa-print me-2 text-primary" style="font-size:1.5rem;"></i>
                        <?php endif; ?>
                        <div class="fw-bold"><?= htmlspecialchars($settings['company_name'] ?? 'Minha Gráfica') ?></div>
                        <?php if (!empty($settings['company_document'])): ?>
                        <small class="text-muted d-block"><?= $settings['company_document'] ?></small>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_phone'])): ?>
                        <small class="text-muted d-block"><i class="fas fa-phone me-1"></i><?= $settings['company_phone'] ?></small>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_email'])): ?>
                        <small class="text-muted d-block"><i class="fas fa-envelope me-1"></i><?= $settings['company_email'] ?></small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <?php elseif ($activeTab === 'prices'): ?>
    <!-- ══════════ ABA: TABELAS DE PREÇO ══════════ -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-tags me-2"></i>Tabelas de Preço</h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNewTable">
                        <i class="fas fa-plus me-1"></i> Nova Tabela
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Produtos</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center" style="width:120px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($priceTables)): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">Nenhuma tabela de preço cadastrada.</td></tr>
                                <?php else: ?>
                                <?php foreach ($priceTables as $pt): ?>
                                <tr>
                                    <td class="fw-bold"><?= htmlspecialchars($pt['name']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($pt['description'] ?? '') ?></td>
                                    <td class="text-center">
                                        <span class="badge bg-primary rounded-pill"><?= $pt['item_count'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($pt['is_default']): ?>
                                            <span class="badge bg-success"><i class="fas fa-star me-1"></i>Padrão</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Personalizada</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="/sistemaTiago/?page=settings&action=editPriceTable&id=<?= $pt['id'] ?>" class="btn btn-outline-primary" title="Editar Preços">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if (!$pt['is_default']): ?>
                                            <a href="/sistemaTiago/?page=settings&action=deletePriceTable&id=<?= $pt['id'] ?>" class="btn btn-outline-danger btn-delete-table" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
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

        <!-- Info lateral -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-info-circle me-2"></i>Como Funciona</h6>
                </div>
                <div class="card-body small">
                    <p><strong>Tabelas de preço</strong> permitem definir preços diferenciados para cada produto.</p>
                    <ul class="mb-2">
                        <li>A <strong>Tabela Padrão</strong> é usada quando o cliente não tem tabela atribuída.</li>
                        <li>Associe uma tabela a um cliente no <strong>cadastro do cliente</strong>.</li>
                        <li>Ao criar um orçamento, o preço será carregado automaticamente da tabela do cliente.</li>
                        <li>O preço pode ser <strong>alterado manualmente</strong> durante o orçamento.</li>
                    </ul>
                    <p class="text-muted mb-0"><strong>Prioridade:</strong> Tabela do Cliente → Tabela Padrão → Preço do Produto.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Tabela -->
    <div class="modal fade" id="modalNewTable" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="/sistemaTiago/?page=settings&action=createPriceTable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Nova Tabela de Preço</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome da Tabela</label>
                            <input type="text" class="form-control" name="name" required placeholder="Ex: Tabela Atacado">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Descrição opcional..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Criar Tabela</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($activeTab === 'preparation'): ?>
    <!-- ══════════ ABA: ETAPAS DE PREPARO ══════════ -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0" style="color: #1abc9c;"><i class="fas fa-boxes-packing me-2"></i>Etapas de Preparo dos Pedidos</h5>
                    <button class="btn btn-sm text-white" style="background:#1abc9c;" data-bs-toggle="modal" data-bs-target="#modalNewStep">
                        <i class="fas fa-plus me-1"></i> Nova Etapa
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px;" class="text-center">Ordem</th>
                                    <th style="width:50px;" class="text-center">Ícone</th>
                                    <th>Nome da Etapa</th>
                                    <th>Descrição</th>
                                    <th class="text-center" style="width:90px;">Status</th>
                                    <th class="text-center" style="width:130px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="prepStepsTableBody">
                                <?php if (empty($preparationSteps)): ?>
                                <tr><td colspan="6" class="text-center text-muted py-4">Nenhuma etapa de preparo cadastrada.</td></tr>
                                <?php else: ?>
                                <?php foreach ($preparationSteps as $step): ?>
                                <tr class="<?= !$step['is_active'] ? 'table-secondary opacity-75' : '' ?>">
                                    <td class="text-center fw-bold text-muted"><?= $step['sort_order'] ?></td>
                                    <td class="text-center">
                                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                              style="width:32px;height:32px;background:<?= $step['is_active'] ? '#e0f7f1' : '#f0f0f0' ?>;">
                                            <i class="<?= htmlspecialchars($step['icon']) ?>" style="color:<?= $step['is_active'] ? '#1abc9c' : '#999' ?>;font-size:0.85rem;"></i>
                                        </span>
                                    </td>
                                    <td class="fw-bold <?= !$step['is_active'] ? 'text-muted text-decoration-line-through' : '' ?>">
                                        <?= htmlspecialchars($step['label']) ?>
                                        <div class="small text-muted" style="font-size:0.7rem;">Chave: <code><?= htmlspecialchars($step['step_key']) ?></code></div>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($step['description']) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-toggle-step <?= $step['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?>" 
                                                data-step-id="<?= $step['id'] ?>" title="<?= $step['is_active'] ? 'Ativa' : 'Inativa' ?>">
                                            <i class="fas fa-<?= $step['is_active'] ? 'check-circle' : 'times-circle' ?> me-1"></i>
                                            <?= $step['is_active'] ? 'Ativa' : 'Inativa' ?>
                                        </button>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary btn-edit-step" 
                                                    data-id="<?= $step['id'] ?>"
                                                    data-label="<?= htmlspecialchars($step['label']) ?>"
                                                    data-description="<?= htmlspecialchars($step['description']) ?>"
                                                    data-icon="<?= htmlspecialchars($step['icon']) ?>"
                                                    data-sort="<?= $step['sort_order'] ?>"
                                                    data-active="<?= $step['is_active'] ?>"
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <a href="/sistemaTiago/?page=settings&action=deletePreparationStep&id=<?= $step['id'] ?>" 
                                               class="btn btn-outline-danger btn-delete-step" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
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

        <!-- Info lateral -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light py-2">
                    <h6 class="mb-0 fw-bold" style="color:#1abc9c;"><i class="fas fa-info-circle me-2"></i>Como Funciona</h6>
                </div>
                <div class="card-body small">
                    <p><strong>Etapas de preparo</strong> são o checklist que aparece quando um pedido entra na fase de <span class="badge" style="background:#1abc9c;">Preparação</span>.</p>
                    <ul class="mb-2">
                        <li>As etapas <strong>ativas</strong> serão exibidas para todos os pedidos em preparação.</li>
                        <li>Desative etapas que não são necessárias no momento sem excluí-las.</li>
                        <li>A <strong>ordem</strong> define a sequência de exibição no checklist.</li>
                        <li>Cada etapa terá registro de quem confirmou e quando.</li>
                    </ul>
                    <p class="text-muted mb-0"><strong>Dica:</strong> Use ícones do <a href="https://fontawesome.com/search?o=r&m=free&s=solid" target="_blank">Font Awesome</a> (ex: <code>fas fa-box</code>).</p>
                </div>
            </div>

            <!-- Preview das etapas ativas -->
            <div class="card border-0 shadow-sm">
                <div class="card-header py-2" style="background: #e0f7f1;">
                    <h6 class="mb-0 fw-bold" style="color:#1abc9c;"><i class="fas fa-eye me-2"></i>Prévia do Checklist</h6>
                </div>
                <div class="card-body p-2">
                    <?php 
                    $activeSteps = array_filter($preparationSteps ?? [], function($s) { return $s['is_active']; });
                    if (empty($activeSteps)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-clipboard d-block mb-2" style="font-size:1.5rem;opacity:0.3;"></i>
                        <small>Nenhuma etapa ativa</small>
                    </div>
                    <?php else: ?>
                    <?php foreach ($activeSteps as $step): ?>
                    <div class="d-flex align-items-center gap-2 p-2 border-bottom">
                        <span class="d-flex align-items-center justify-content-center rounded-circle border border-2" 
                              style="width:24px;height:24px;min-width:24px;border-color:#ccc !important;">
                            <i class="<?= htmlspecialchars($step['icon']) ?> text-muted" style="font-size:0.6rem;"></i>
                        </span>
                        <div>
                            <div class="fw-bold" style="font-size:0.8rem;"><?= htmlspecialchars($step['label']) ?></div>
                            <?php if (!empty($step['description'])): ?>
                            <div class="text-muted" style="font-size:0.65rem;"><?= htmlspecialchars($step['description']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <div class="text-center text-muted mt-2" style="font-size:0.7rem;">
                        <i class="fas fa-info-circle me-1"></i><?= count($activeSteps) ?> etapa(s) ativa(s)
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Nova Etapa -->
    <div class="modal fade" id="modalNewStep" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="/sistemaTiago/?page=settings&action=addPreparationStep">
                <div class="modal-content">
                    <div class="modal-header" style="background:#e0f7f1;">
                        <h5 class="modal-title" style="color:#1abc9c;"><i class="fas fa-plus-circle me-2"></i>Nova Etapa de Preparo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome da Etapa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" required placeholder="Ex: Conferência de Cores">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <textarea class="form-control" name="description" rows="2" placeholder="Descrição breve da etapa..."></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Ícone <small class="text-muted">(classe Font Awesome)</small></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="newStepIconPreview"><i class="fas fa-check"></i></span>
                                    <input type="text" class="form-control" name="icon" value="fas fa-check" placeholder="fas fa-check" id="newStepIconInput">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ordem</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0">
                            </div>
                        </div>
                        <div class="mt-3">
                            <small class="text-muted"><i class="fas fa-lightbulb me-1"></i>Ícones populares: 
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-check">fa-check</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-box">fa-box</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-cut">fa-cut</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-search">fa-search</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-file-check">fa-file-check</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-truck-loading">fa-truck-loading</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-list-check">fa-list-check</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-paint-roller">fa-paint-roller</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-print">fa-print</code>,
                                <code class="cursor-pointer icon-pick" data-icon="fas fa-scissors">fa-scissors</code>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white" style="background:#1abc9c;"><i class="fas fa-save me-1"></i> Criar Etapa</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Etapa -->
    <div class="modal fade" id="modalEditStep" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="/sistemaTiago/?page=settings&action=updatePreparationStep">
                <div class="modal-content">
                    <div class="modal-header" style="background:#e0f7f1;">
                        <h5 class="modal-title" style="color:#1abc9c;"><i class="fas fa-edit me-2"></i>Editar Etapa de Preparo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editStepId">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome da Etapa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="label" id="editStepLabel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Descrição</label>
                            <textarea class="form-control" name="description" id="editStepDesc" rows="2"></textarea>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold">Ícone</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="editStepIconPreview"><i class="fas fa-check"></i></span>
                                    <input type="text" class="form-control" name="icon" id="editStepIcon" value="fas fa-check">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Ordem</label>
                                <input type="number" class="form-control" name="sort_order" id="editStepSort" value="0" min="0">
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="editStepActive" name="is_active" value="1" checked>
                                <label class="form-check-label fw-bold" for="editStepActive">Etapa Ativa</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white" style="background:#1abc9c;"><i class="fas fa-save me-1"></i> Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($_GET['status']) && $_GET['status'] === 'saved'): ?>
    Swal.fire({ icon: 'success', title: 'Salvo!', text: 'Configurações atualizadas.', timer: 2000, showConfirmButton: false });
    <?php endif; ?>
    <?php if(isset($_GET['status']) && in_array($_GET['status'], ['table_created','table_updated','table_deleted'])): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', timer: 1500, showConfirmButton: false });
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] === 'table_default_error'): ?>
    Swal.fire({ icon: 'error', title: 'Erro', text: 'Não é possível excluir a tabela padrão.', confirmButtonColor: '#3498db' });
    <?php endif; ?>
    <?php if(isset($_GET['status']) && in_array($_GET['status'], ['step_added','step_updated','step_deleted'])): ?>
    Swal.fire({ icon: 'success', title: 'Sucesso!', text: 'Etapa de preparo atualizada.', timer: 1500, showConfirmButton: false });
    <?php endif; ?>

    // Confirmar exclusão de tabela
    document.querySelectorAll('.btn-delete-table').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            Swal.fire({
                title: 'Excluir tabela de preço?',
                text: 'Clientes associados a esta tabela ficarão sem tabela de preço.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c'
            }).then(r => { if (r.isConfirmed) window.location.href = href; });
        });
    });

    // ═══ Etapas de Preparo — Ações ═══

    // Toggle ativo/inativo via AJAX
    document.querySelectorAll('.btn-toggle-step').forEach(btn => {
        btn.addEventListener('click', function() {
            const stepId = this.dataset.stepId;
            fetch('/sistemaTiago/?page=settings&action=togglePreparationStep', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'id=' + stepId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        });
    });

    // Abrir modal de edição com dados da etapa
    document.querySelectorAll('.btn-edit-step').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('editStepId').value = this.dataset.id;
            document.getElementById('editStepLabel').value = this.dataset.label;
            document.getElementById('editStepDesc').value = this.dataset.description;
            document.getElementById('editStepIcon').value = this.dataset.icon;
            document.getElementById('editStepSort').value = this.dataset.sort;
            document.getElementById('editStepActive').checked = this.dataset.active === '1';
            // Atualizar preview do ícone
            document.getElementById('editStepIconPreview').innerHTML = '<i class="' + this.dataset.icon + '"></i>';
            new bootstrap.Modal(document.getElementById('modalEditStep')).show();
        });
    });

    // Confirmar exclusão de etapa
    document.querySelectorAll('.btn-delete-step').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.href;
            Swal.fire({
                title: 'Excluir etapa de preparo?',
                text: 'Esta ação não pode ser desfeita. Os registros já feitos com esta etapa serão mantidos no histórico.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-trash me-1"></i> Excluir',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#e74c3c'
            }).then(r => { if (r.isConfirmed) window.location.href = href; });
        });
    });

    // Preview de ícone ao digitar
    const newIconInput = document.getElementById('newStepIconInput');
    if (newIconInput) {
        newIconInput.addEventListener('input', function() {
            document.getElementById('newStepIconPreview').innerHTML = '<i class="' + this.value + '"></i>';
        });
    }
    const editIconInput = document.getElementById('editStepIcon');
    if (editIconInput) {
        editIconInput.addEventListener('input', function() {
            document.getElementById('editStepIconPreview').innerHTML = '<i class="' + this.value + '"></i>';
        });
    }

    // Quick pick de ícones
    document.querySelectorAll('.icon-pick').forEach(el => {
        el.style.cursor = 'pointer';
        el.addEventListener('click', function() {
            const icon = this.dataset.icon;
            const modal = this.closest('.modal');
            if (modal) {
                const input = modal.querySelector('input[name="icon"]');
                const preview = modal.querySelector('[id$="IconPreview"]');
                if (input) input.value = icon;
                if (preview) preview.innerHTML = '<i class="' + icon + '"></i>';
            }
        });
    });
});
</script>
