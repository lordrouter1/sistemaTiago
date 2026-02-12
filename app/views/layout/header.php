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
</head>
<body>

<?php
    $currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';
    $menuPages = require 'app/config/menu.php';

    // Carrega as permissões do usuário logado para filtrar o menu
    $userPermissions = [];
    $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    
    if (!$isAdmin && isset($_SESSION['user_id'])) {
        // Busca as permissões do grupo do usuário
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
     * - Admin vê tudo.
     * - Páginas sem controle de permissão (permission=false) são visíveis para todos.
     * - Páginas com controle de permissão só aparecem se o usuário tiver acesso.
     */
    function canShowInMenu($pageKey, $pageInfo, $isAdmin, $userPermissions) {
        // Páginas sem controle de permissão são visíveis para todos
        if (empty($pageInfo['permission'])) {
            return true;
        }
        // Admin vê tudo
        if ($isAdmin) {
            return true;
        }
        // Verifica se o usuário tem permissão
        return in_array($pageKey, $userPermissions);
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

      <!-- ── Menu Principal (gerado automaticamente de config/menu.php) ── -->
      <ul class="navbar-nav">
        <?php foreach ($menuPages as $pageKey => $pageInfo): ?>
          <?php if (!$pageInfo['menu']) continue; ?>
          <?php if (!canShowInMenu($pageKey, $pageInfo, $isAdmin, $userPermissions)) continue; ?>
          <li class="nav-item">
            <a class="nav-link <?= ($currentPage == $pageKey) ? 'active' : '' ?>"
               href="/sistemaTiago/<?= $pageKey === 'home' ? '' : '?page=' . $pageKey ?>">
              <i class="<?= $pageInfo['icon'] ?> me-1"></i><?= $pageInfo['label'] ?>
            </a>
          </li>
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
        <?php if($isAdmin || in_array('users', $userPermissions)): ?>
        <li class="nav-item">
          <a href="/sistemaTiago/?page=users"
             class="nav-link btn btn-sm px-3 me-1 border-0 <?= ($currentPage == 'users') ? 'active' : 'text-white' ?>"
             title="Gestão de Usuários">
            <i class="fas fa-cog"></i>
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
