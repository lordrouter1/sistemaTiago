<?php
require_once 'app/models/Order.php';

class OrderController {
    
    private $orderModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->orderModel = new Order($db);
    }

    public function index() {
        // Buscar pedidos do banco
        $stmt = $this->orderModel->readAll();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require 'app/views/layout/header.php';
        require 'app/views/orders/index.php';
        require 'app/views/layout/footer.php';
    }

    public function create() {
        // Obter lista de produtos para o select
        $productModel = new Product($this->conn); // Assumindo que temos acesso à conexão aqui ou re-instanciamos
        // Nota: Como OrderController instancia Order model, precisamos de um jeito de pegar produtos.
        // Solução rápida MVP:
        $database = new Database();
        $db = $database->getConnection();
        
        require_once 'app/models/Product.php';
        $productModel = new Product($db);
        $stmt_products = $productModel->readAll();
        $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

        require_once 'app/models/Customer.php';
        $customerModel = new Customer($db);
        $stmt_customers = $customerModel->readAll();
        $customers = $stmt_customers->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/layout/header.php';
        require 'app/views/orders/create.php';
        require 'app/views/layout/footer.php';
    }
}
