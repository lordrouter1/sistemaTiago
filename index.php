<?php
// Carregar configurações e banco de dados
require_once 'app/config/database.php';

// Sistema de Roteamento Simples
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Definir o controller base baseado na página
$controllerName = 'HomeController';
$actionName = 'index';

// Switch simples para rotas (pode ser evoluído para um Router mais robusto)
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

switch($page) {
    case 'customers':
        require_once 'app/controllers/CustomerController.php';
        $controller = new CustomerController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            $controller->index();
        }
        break;
        
    case 'products':
        require_once 'app/controllers/ProductController.php';
        $controller = new ProductController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            $controller->index();
        }
        break;

    case 'orders':
        require_once 'app/controllers/OrderController.php';
        $controller = new OrderController();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            $controller->index();
        }
        break;

    case 'home':
    default:
        require_once 'app/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;
}
