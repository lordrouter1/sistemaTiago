<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestão - Gráfica</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/sistemaTiago/assets/css/theme.css">
    <link rel="stylesheet" href="/sistemaTiago/assets/css/style.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        /* ── Estilos para os dropdowns do menu ── */
        .navbar .dropdown-menu {
            background: var(--primary-color, #2c3e50);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.5rem;
            padding: 0.35rem 0;
            margin-top: 0.25rem;
            min-width: 200px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.25);
        }
        .navbar .dropdown-item {
            color: rgba(255,255,255,0.85);
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            transition: background 0.15s, padding-left 0.15s;
        }
        .navbar .dropdown-item:hover,
        .navbar .dropdown-item:focus {
            background: rgba(255,255,255,0.12);
            color: #fff;
            padding-left: 1.25rem;
        }
        .navbar .dropdown-item.active,
        .navbar .dropdown-item:active {
            background: rgba(255,255,255,0.18);
            color: #fff;
        }
        .navbar .dropdown-item i {
            width: 20px;
            text-align: center;
            opacity: 0.7;
        }
        .navbar .dropdown-item:hover i {
            opacity: 1;
        }
        .navbar .dropdown-divider {
            border-color: rgba(255,255,255,0.1);
        }
        .navbar .dropdown-toggle::after {
            font-size: 0.65rem;
            vertical-align: 0.15em;
            margin-left: 0.35rem;
        }
        /* ── Bell dropdown override (fundo branco) ── */
        #bellDropdownMenu {
            background: #fff !important;
        }
        #bellDropdownMenu .dropdown-item {
            color: #333 !important;
            padding: 0.4rem 0.75rem;
            font-size: 0.85rem;
        }
        #bellDropdownMenu .dropdown-item:hover,
        #bellDropdownMenu .dropdown-item:focus {
            background: #f1f5f9 !important;
            color: #333 !important;
            padding-left: 0.75rem;
        }
        #bellDropdownToggle::after {
            display: none;
        }
    </style>
</head>
<body>

<?php
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';
    $menuPages = require 'app/config/menu.php';

    // Achata o menu para ter uma lista simples (para permissões)
    $flatMenuPages = [];
    foreach ($menuPages as $key => $info) {
        if (isset($info['children'])) {
            foreach ($info['children'] as $childKey => $childInfo) {
                $flatMenuPages[$childKey] = $childInfo;
            }
        } else {
            $flatMenuPages[$key] = $info;
        }
    }

    // Carrega as permissões do usuário logado para filtrar o menu
    $userPermissions = [];
    $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    
    if (!$isAdmin && isset($_SESSION['user_id'])) {
        $dbMenu = (new Database())->getConnection();
        if (!empty($_SESSION['group_id'])) {
            $stmtMenu = $dbMenu->prepare("SELECT page_name FROM group_permissions WHERE group_id = :gid");
            $stmtMenu->bindParam(':gid', $_SESSION['group_id']);
            $stmtMenu->execute();
            $userPermissions = $stmtMenu->fetchAll(PDO::FETCH_COLUMN);
        }
    }

    // Contar pedidos atrasados para badge no menu
    $headerDelayedCount = 0;
    $headerDelayedOrders = [];
    if (isset($_SESSION['user_id'])) {
        try {
            $dbAlert = isset($dbMenu) ? $dbMenu : (new Database())->getConnection();
            $stmtGoalsH = $dbAlert->query("SELECT stage, max_hours FROM pipeline_stage_goals");
            $goalsH = [];
            while ($gRow = $stmtGoalsH->fetch(PDO::FETCH_ASSOC)) {
                $goalsH[$gRow['stage']] = (int)$gRow['max_hours'];
            }
            // Buscar pedidos ativos com info do cliente e produtos
            $stmtActiveH = $dbAlert->query("
                SELECT o.id, o.pipeline_stage, o.pipeline_entered_at, o.priority, o.deadline,
                       c.name as customer_name
                FROM orders o
                LEFT JOIN customers c ON o.customer_id = c.id
                WHERE o.pipeline_stage NOT IN ('concluido','cancelado') AND o.status != 'cancelado'
                ORDER BY o.pipeline_entered_at ASC
            ");
            while ($oRow = $stmtActiveH->fetch(PDO::FETCH_ASSOC)) {
                $hrsH = round((time() - strtotime($oRow['pipeline_entered_at'])) / 3600);
                $goalH = $goalsH[$oRow['pipeline_stage']] ?? 24;
                if ($goalH > 0 && $hrsH > $goalH) {
                    $oRow['hours_in_stage'] = $hrsH;
                    $oRow['max_hours'] = $goalH;
                    $oRow['delay_hours'] = $hrsH - $goalH;
                    $headerDelayedOrders[] = $oRow;
                    $headerDelayedCount++;
                }
            }
            // Buscar produtos atrasados nos setores de produção (pedidos em producao/preparacao)
            $headerDelayedProducts = [];
            try {
                $stmtDelayedProd = $dbAlert->query("
                    SELECT ops.order_id, ops.order_item_id, ops.sector_id, ops.status, ops.started_at,
                           s.name as sector_name, s.color as sector_color,
                           p.name as product_name,
                           o.pipeline_stage,
                           oi.quantity,
                           c.name as customer_name
                    FROM order_production_sectors ops
                    JOIN production_sectors s ON ops.sector_id = s.id
                    JOIN order_items oi ON ops.order_item_id = oi.id
                    JOIN products p ON oi.product_id = p.id
                    JOIN orders o ON ops.order_id = o.id
                    LEFT JOIN customers c ON o.customer_id = c.id
                    WHERE ops.status = 'pendente'
                      AND o.pipeline_stage IN ('producao','preparacao')
                      AND o.status != 'cancelado'
                    ORDER BY ops.order_id ASC, ops.sort_order ASC
                ");
                $allPendingSectors = $stmtDelayedProd->fetchAll(PDO::FETCH_ASSOC);
                // Agrupar: primeiro setor pendente por item (setor atual)
                $currentSectorByItem = [];
                foreach ($allPendingSectors as $row) {
                    $itemKey = $row['order_id'] . '_' . $row['order_item_id'];
                    if (!isset($currentSectorByItem[$itemKey])) {
                        $currentSectorByItem[$itemKey] = $row;
                    }
                }
                $headerDelayedProducts = array_values($currentSectorByItem);
            } catch (Exception $e) { $headerDelayedProducts = []; }
        } catch (Exception $e) { $headerDelayedCount = 0; $headerDelayedOrders = []; $headerDelayedProducts = []; }
    }
    
    /**
     * Verifica se o usuário pode ver determinada página no menu.
     */
    function canShowInMenu($pageKey, $pageInfo, $isAdmin, $userPermissions) {
        if (empty($pageInfo['permission'])) return true;
        if ($isAdmin) return true;
        return in_array($pageKey, $userPermissions);
    }

    /**
     * Verifica se pelo menos um filho de um submenu é visível para o usuário.
     */
    function hasVisibleChild($children, $isAdmin, $userPermissions) {
        foreach ($children as $childKey => $childInfo) {
            if (!empty($childInfo['menu']) && canShowInMenu($childKey, $childInfo, $isAdmin, $userPermissions)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica se a página atual está dentro de um submenu (para destacar o dropdown).
     */
    function isChildActive($children, $currentPage) {
        return isset($children[$currentPage]);
    }
?>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--primary-color);">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/sistemaTiago/">
        <i class="fas fa-print me-2"></i>Gestão Gráfica
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">

      <!-- ── Menu Principal (com suporte a submenus) ── -->
      <ul class="navbar-nav">
        <?php foreach ($menuPages as $pageKey => $pageInfo): ?>
          <?php if (empty($pageInfo['menu'])) continue; ?>

          <?php if (isset($pageInfo['children'])): ?>
            <?php // ── DROPDOWN (submenu) ── ?>
            <?php if (!hasVisibleChild($pageInfo['children'], $isAdmin, $userPermissions)) continue; ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle <?= isChildActive($pageInfo['children'], $currentPage) ? 'active' : '' ?>"
                 href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="<?= $pageInfo['icon'] ?> me-1"></i><?= $pageInfo['label'] ?>
              </a>
              <ul class="dropdown-menu">
                <?php foreach ($pageInfo['children'] as $childKey => $childInfo): ?>
                  <?php if (empty($childInfo['menu'])) continue; ?>
                  <?php if (!canShowInMenu($childKey, $childInfo, $isAdmin, $userPermissions)) continue; ?>
                  <li>
                    <a class="dropdown-item <?= ($currentPage == $childKey) ? 'active' : '' ?>"
                       href="/sistemaTiago/?page=<?= $childKey ?>">
                      <i class="<?= $childInfo['icon'] ?> me-2"></i><?= $childInfo['label'] ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </li>
          <?php else: ?>
            <?php // ── LINK DIRETO (sem submenu) ── ?>
            <?php if (!canShowInMenu($pageKey, $pageInfo, $isAdmin, $userPermissions)) continue; ?>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage == $pageKey) ? 'active' : '' ?>"
                 href="/sistemaTiago/<?= $pageKey === 'home' ? '' : '?page=' . $pageKey ?>">
                <i class="<?= $pageInfo['icon'] ?> me-1"></i><?= $pageInfo['label'] ?>
              </a>
            </li>
          <?php endif; ?>

        <?php endforeach; ?>
      </ul>

      <!-- ── Menu Direito (Perfil / Config / Sair) ── -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        <?php if ($headerDelayedCount > 0): ?>
        <li class="nav-item dropdown">
          <a href="#" class="nav-link text-white position-relative me-2 dropdown-toggle" 
             role="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside"
             title="<?= $headerDelayedCount ?> pedido(s) atrasado(s)" id="bellDropdownToggle" style="cursor:pointer;">
            <i class="fas fa-bell"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.6rem;">
                <?= $headerDelayedCount ?>
            </span>
          </a>
          <div class="dropdown-menu dropdown-menu-end p-0" id="bellDropdownMenu" 
               style="width:420px;max-height:500px;overflow-y:auto;background:#fff !important;border:1px solid #dee2e6 !important;box-shadow:0 8px 32px rgba(0,0,0,0.18) !important;">
            <!-- Header do dropdown -->
            <div class="px-3 py-2 border-bottom" style="background:#fff3cd;">
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="text-dark"><i class="fas fa-exclamation-triangle text-warning me-1"></i> Pedidos Atrasados</strong>
                    <span class="badge bg-danger rounded-pill"><?= $headerDelayedCount ?></span>
                </div>
            </div>

            <?php if (!empty($headerDelayedOrders)): ?>
            <!-- Lista de pedidos atrasados -->
            <div class="px-2 py-1">
                <small class="text-muted fw-bold px-2"><i class="fas fa-clock me-1"></i>PEDIDOS NA ETAPA ALÉM DO PRAZO</small>
            </div>
            <?php 
            $stageLabelsH = [
                'contato' => ['label' => 'Contato', 'color' => '#9b59b6', 'icon' => 'fas fa-phone'],
                'orcamento' => ['label' => 'Orçamento', 'color' => '#3498db', 'icon' => 'fas fa-file-invoice-dollar'],
                'venda' => ['label' => 'Venda', 'color' => '#2ecc71', 'icon' => 'fas fa-handshake'],
                'producao' => ['label' => 'Produção', 'color' => '#e67e22', 'icon' => 'fas fa-industry'],
                'preparacao' => ['label' => 'Preparação', 'color' => '#1abc9c', 'icon' => 'fas fa-boxes-packing'],
                'envio' => ['label' => 'Envio/Entrega', 'color' => '#e74c3c', 'icon' => 'fas fa-truck'],
                'financeiro' => ['label' => 'Financeiro', 'color' => '#f39c12', 'icon' => 'fas fa-coins'],
            ];
            foreach (array_slice($headerDelayedOrders, 0, 10) as $dOrder): 
                $dStage = $stageLabelsH[$dOrder['pipeline_stage']] ?? ['label' => $dOrder['pipeline_stage'], 'color' => '#999', 'icon' => 'fas fa-circle'];
                $priorityEmoji = ['urgente' => '🔴', 'alta' => '🟡', 'normal' => '🔵', 'baixa' => '🟢'];
                $pEmoji = $priorityEmoji[$dOrder['priority'] ?? 'normal'] ?? '🔵';
            ?>
            <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $dOrder['id'] ?>" 
               class="dropdown-item px-3 py-2 border-bottom" style="white-space:normal;color:#333 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-start gap-2">
                        <div class="flex-shrink-0 mt-1">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                                  style="width:28px;height:28px;background:<?= $dStage['color'] ?>20;">
                                <i class="<?= $dStage['icon'] ?>" style="color:<?= $dStage['color'] ?>;font-size:0.7rem;"></i>
                            </span>
                        </div>
                        <div>
                            <div class="fw-bold" style="font-size:0.82rem;color:#333;">
                                <?= $pEmoji ?> #<?= str_pad($dOrder['id'], 4, '0', STR_PAD_LEFT) ?>
                                <?php if (!empty($dOrder['customer_name'])): ?>
                                — <?= htmlspecialchars(mb_substr($dOrder['customer_name'], 0, 20)) ?>
                                <?php endif; ?>
                            </div>
                            <div style="font-size:0.72rem;">
                                <span class="badge px-1 py-0" style="background:<?= $dStage['color'] ?>;color:#fff;font-size:0.65rem;">
                                    <?= $dStage['label'] ?>
                                </span>
                                <span class="text-danger fw-bold ms-1">
                                    <i class="fas fa-clock me-1"></i><?= $dOrder['delay_hours'] ?>h atrasado
                                </span>
                                <span class="text-muted">(<?= $dOrder['hours_in_stage'] ?>h / máx <?= $dOrder['max_hours'] ?>h)</span>
                            </div>
                            <?php if (!empty($dOrder['deadline'])): ?>
                            <div style="font-size:0.65rem;" class="text-muted">
                                <i class="fas fa-calendar me-1"></i>Prazo: <?= date('d/m/Y', strtotime($dOrder['deadline'])) ?>
                                <?php if (strtotime($dOrder['deadline']) < time()): ?>
                                <span class="text-danger fw-bold">— VENCIDO</span>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-muted mt-2" style="font-size:0.6rem;"></i>
                </div>
            </a>
            <?php endforeach; ?>
            
            <?php if (count($headerDelayedOrders) > 10): ?>
            <div class="text-center py-2 small text-muted">
                <i class="fas fa-ellipsis-h me-1"></i>e mais <?= count($headerDelayedOrders) - 10 ?> pedido(s) atrasado(s)
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($headerDelayedProducts)): ?>
            <!-- Produtos em produção (onde estão parados) -->
            <div class="px-2 py-1 border-top" style="background:#f8f9fa;">
                <small class="text-muted fw-bold px-2"><i class="fas fa-industry me-1"></i>PRODUTOS EM PRODUÇÃO (SETOR ATUAL)</small>
            </div>
            <?php foreach (array_slice($headerDelayedProducts, 0, 8) as $dProd): ?>
            <a href="/sistemaTiago/?page=pipeline&action=detail&id=<?= $dProd['order_id'] ?>" 
               class="dropdown-item px-3 py-2 border-bottom" style="white-space:normal;color:#333 !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle" 
                          style="width:24px;height:24px;min-width:24px;background:<?= $dProd['sector_color'] ?? '#e67e22' ?>20;">
                        <i class="fas fa-box" style="color:<?= $dProd['sector_color'] ?? '#e67e22' ?>;font-size:0.6rem;"></i>
                    </span>
                    <div style="font-size:0.78rem;">
                        <div class="fw-bold" style="color:#333;">
                            <?= htmlspecialchars(mb_substr($dProd['product_name'], 0, 25)) ?> 
                            <span class="text-muted fw-normal">(×<?= $dProd['quantity'] ?>)</span>
                        </div>
                        <div style="font-size:0.68rem;">
                            <span class="text-muted">Pedido #<?= str_pad($dProd['order_id'], 4, '0', STR_PAD_LEFT) ?></span>
                            <?php if (!empty($dProd['customer_name'])): ?>
                            — <span class="text-muted"><?= htmlspecialchars(mb_substr($dProd['customer_name'], 0, 15)) ?></span>
                            <?php endif; ?>
                            <span class="badge px-1 py-0 ms-1" style="background:<?= $dProd['sector_color'] ?? '#e67e22' ?>;color:#fff;font-size:0.6rem;">
                                <i class="fas fa-map-pin me-1"></i><?= htmlspecialchars($dProd['sector_name']) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
            <?php if (count($headerDelayedProducts) > 8): ?>
            <div class="text-center py-1 small text-muted border-top">
                <i class="fas fa-ellipsis-h me-1"></i>e mais <?= count($headerDelayedProducts) - 8 ?> produto(s) em produção
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Footer -->
            <div class="px-3 py-2 border-top text-center" style="background:#f8f9fa;">
                <a href="/sistemaTiago/?page=pipeline" class="text-decoration-none small fw-bold" style="color:#3498db;">
                    <i class="fas fa-columns me-1"></i>Ver Pipeline Completo
                </a>
            </div>
          </div>
        </li>
        <?php elseif (isset($_SESSION['user_id'])): ?>
        <li class="nav-item">
          <a href="#" class="nav-link text-white position-relative me-2" title="Sem avisos" style="opacity:0.5;">
            <i class="fas fa-bell"></i>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a href="/sistemaTiago/?page=profile"
             class="nav-link small me-2 text-decoration-none <?= ($currentPage == 'profile') ? 'active' : 'text-white' ?>"
             title="Meu Perfil">
            <i class="fas fa-user-circle me-1"></i>
            <?= $_SESSION['user_name'] ?? 'Visitante' ?>
            <span class="badge bg-light text-dark ms-1" style="font-size: 0.65rem;">
              <?= isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? 'Admin' : 'Usuário' ?>
            </span>
          </a>
        </li>
        <?php if($isAdmin || in_array('settings', $userPermissions)): ?>
        <li class="nav-item">
          <a href="/sistemaTiago/?page=settings"
             class="nav-link btn btn-sm px-3 me-1 border-0 <?= ($currentPage == 'settings') ? 'active' : 'text-white' ?>"
             title="Configurações">
            <i class="fas fa-building"></i>
          </a>
        </li>
        <?php endif; ?>
        <?php if($isAdmin || in_array('users', $userPermissions)): ?>
        <li class="nav-item">
          <a href="/sistemaTiago/?page=users"
             class="nav-link btn btn-sm px-3 me-1 border-0 <?= ($currentPage == 'users') ? 'active' : 'text-white' ?>"
             title="Gestão de Usuários">
            <i class="fas fa-users-cog"></i>
          </a>
        </li>
        <?php endif; ?>
        <li class="nav-item">
          <a class="nav-link text-white btn btn-sm px-3" href="/sistemaTiago/?page=login&action=logout" title="Sair do sistema">
            <i class="fas fa-sign-out-alt"></i> Sair
          </a>
        </li>
      </ul>

    </div>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-md-12 ms-sm-auto px-md-4 py-4 main-bg">
