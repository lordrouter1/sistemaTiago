<?php
session_start();

// Carregar configurações e banco de dados
require_once 'app/config/database.php';
require_once 'app/models/User.php';

// Sistema de Roteamento Simples
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

// Authentication Check
if (!isset($_SESSION['user_id'])) {
    if ($page !== 'login') {
        header('Location: /sistemaTiago/?page=login');
        exit;
    }
} else {
    if ($page === 'login' && $action !== 'logout') {
        header('Location: /sistemaTiago/');
        exit;
    }
}

// Permission Check — usa o registro centralizado de menu.php
// Páginas com 'permission' => false são acessíveis por todos os logados
$menuConfig = require 'app/config/menu.php';
$needsPermission = isset($menuConfig[$page]) && !empty($menuConfig[$page]['permission']);

if (isset($_SESSION['user_id']) && $page !== 'login' && $action !== 'logout' && $action !== 'getSubcategories' && $needsPermission) {
    $db = (new Database())->getConnection();
    $user = new User($db);
    if (!$user->checkPermission($_SESSION['user_id'], $page)) {
        require 'app/views/layout/header.php';
        echo "<div class='container mt-5'><div class='alert alert-danger'><i class='fas fa-ban me-2'></i>Acesso Negado.<br>Você não tem permissão para acessar o módulo: <strong>" . strtoupper($page) . "</strong>.</div></div>";
        require 'app/views/layout/footer.php';
        exit;
    }
}

switch ($page) {
    case 'home':
        require 'app/views/layout/header.php';
        require 'app/views/home/index.php';
        require 'app/views/layout/footer.php';
        break;

    case 'login':
        require_once 'app/controllers/UserController.php';
        $controller = new UserController();
        if ($action == 'logout') {
            $controller->logout();
        } else {
            $controller->login();
        }
        break;

    case 'dashboard':
        require_once 'app/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    // ── Perfil do Usuário (acessível por todos os logados) ──
    case 'profile':
        require_once 'app/controllers/UserController.php';
        $controller = new UserController();
        if ($action == 'update') {
            $controller->updateProfile();
        } else {
            $controller->profile();
        }
        break;

    // ── Produtos ──
    case 'products':
        require_once 'app/controllers/ProductController.php';
        $controller = new ProductController();
        if ($action == 'store') {
            $controller->store();
        } elseif ($action == 'create') {
            $controller->create();
        } elseif ($action == 'edit') {
            $controller->edit();
        } elseif ($action == 'update') {
            $controller->update();
        } elseif ($action == 'delete') {
            $controller->delete();
        } elseif ($action == 'deleteImage') {
            $controller->deleteImage();
        } elseif ($action == 'getSubcategories') {
            $controller->getSubcategories();
        } else {
            $controller->index();
        }
        break;

    // ── Clientes ──
    case 'customers':
        require_once 'app/controllers/CustomerController.php';
        $controller = new CustomerController();
        if ($action == 'store') {
            $controller->store();
        } elseif ($action == 'create') {
            $controller->create();
        } elseif ($action == 'edit') {
            $controller->edit();
        } elseif ($action == 'update') {
            $controller->update();
        } elseif ($action == 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    // ── Pedidos ──
    case 'orders':
        require_once 'app/controllers/OrderController.php';
        $controller = new OrderController();
        if ($action == 'store') {
            $controller->store();
        } elseif ($action == 'create') {
            $controller->create();
        } elseif ($action == 'edit') {
            $controller->edit();
        } elseif ($action == 'update') {
            $controller->update();
        } elseif ($action == 'delete') {
            $controller->delete();
        } else {
            $controller->index();
        }
        break;

    // ── Linha de Produção (Pipeline) ──
    case 'pipeline':
        require_once 'app/controllers/PipelineController.php';
        $controller = new PipelineController();
        if ($action == 'move') {
            $controller->move();
        } elseif ($action == 'detail') {
            $controller->detail();
        } elseif ($action == 'updateDetails') {
            $controller->updateDetails();
        } elseif ($action == 'settings') {
            $controller->settings();
        } elseif ($action == 'saveSettings') {
            $controller->saveSettings();
        } elseif ($action == 'alerts') {
            $controller->alerts();
        } else {
            $controller->index();
        }
        break;
        
    // ── Gestão de Usuários (Admin) ──
    case 'users':
        require_once 'app/controllers/UserController.php';
        $controller = new UserController();
        if ($action == 'create') {
            $controller->create();
        } elseif ($action == 'store') {
            $controller->store();
        } elseif ($action == 'edit') {
            $controller->edit();
        } elseif ($action == 'update') {
            $controller->update();
        } elseif ($action == 'delete') {
            $controller->delete();
        } elseif ($action == 'groups') {
            $controller->groups();
        } elseif ($action == 'createGroup') {
            $controller->createGroup();
        } elseif ($action == 'updateGroup') {
            $controller->updateGroup();
        } elseif ($action == 'deleteGroup') {
            $controller->deleteGroup();
        } else {
            $controller->index();
        }
        break;

    default:
        http_response_code(404);
        require 'app/views/layout/header.php';
        echo "<div class='container mt-5 text-center py-5'><h2 class='text-muted'><i class='fas fa-search me-2'></i>Página não encontrada</h2><p class='text-muted'>A página que você procura não existe.</p><a href='/sistemaTiago/' class='btn btn-primary mt-3'>Voltar ao Início</a></div>";
        require 'app/views/layout/footer.php';
        break;
}
