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
        <li class="nav-item">
          <a class="nav-link <?= ($currentPage == 'home') ? 'active' : '' ?>" aria-current="page" href="/sistemaTiago/">Dashboard</a>
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
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar placeholder if needed -->
        
        <main class="col-md-12 ms-sm-auto px-md-4">
