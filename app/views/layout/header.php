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

<nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm" style="background-color: var(--primary-color);">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="/sistemaTiago/">
        <i class="fas fa-print me-2"></i>Gestão Gráfica
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <?php 
        $currentPage = isset($_GET['page']) ? $_GET['page'] : 'home'; 
      ?>
      <ul class="navbar-nav">
        <!-- Dashboard links based on role or general access -->
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'home') ? 'active' : '' ?>" aria-current="page" href="/sistemaTiago/">Início</a>
        </li>
        
        <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage == 'dashboard') ? 'active' : '' ?>" href="/sistemaTiago/?page=dashboard">Dashboard (Admin)</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage == 'customers') ? 'active' : '' ?>" href="/sistemaTiago/?page=customers">Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage == 'orders') ? 'active' : '' ?>" href="/sistemaTiago/?page=orders">Pedidos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage == 'products') ? 'active' : '' ?>" href="/sistemaTiago/?page=products">Produtos</a>
            </li>
        <?php else: ?>
            <!-- Menu for standard users based on permissions could go here -->
             <li class="nav-item">
              <a class="nav-link <?= ($currentPage == 'customers') ? 'active' : '' ?>" href="/sistemaTiago/?page=customers">Clientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link <?= ($currentPage == 'orders') ? 'active' : '' ?>" href="/sistemaTiago/?page=orders">Pedidos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($currentPage == 'products') ? 'active' : '' ?>" href="/sistemaTiago/?page=products">Produtos</a>
            </li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item">
                    <a href="/sistemaTiago/?page=profile" class="nav-link text-white small me-2 text-decoration-none" title="Meu Perfil">
                        <i class="fas fa-user-circle me-1"></i> 
                        <?= $_SESSION['user_name'] ?? 'Visitante' ?> 
                        <span class="badge bg-light text-dark ms-1" style="font-size: 0.65rem;"><?= isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin' ? 'Admin' : 'Usuário' ?></span>
                    </a>
                </li>
                <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-item">
                    <a href="/sistemaTiago/?page=users" class="nav-link text-white btn btn-sm px-3 me-1 border-0" title="Gestão de Usuários">
                        <i class="fas fa-cog"></i>
                    </a>
                </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-white btn btn-sm px-3" href="/sistemaTiago/?page=login&action=logout" title="Sair do sistema"><i class="fas fa-sign-out-alt"></i> Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar Navigation (Optional or just top menu) -->
       
        <!-- Main Content -->
        <main class="col-md-12 ms-sm-auto px-md-4 py-4 main-bg">
