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
