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
        $stmt = $this->orderModel->readAll();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require 'app/views/layout/header.php';
        require 'app/views/orders/index.php';
        require 'app/views/layout/footer.php';
    }

    public function create() {
        require_once 'app/models/Product.php';
        require_once 'app/models/Customer.php';
        
        $database = new Database();
        $db = $database->getConnection();
        
        $productModel = new Product($db);
        $stmt_products = $productModel->readAll();
        $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

        $customerModel = new Customer($db);
        $stmt_customers = $customerModel->readAll();
        $customers = $stmt_customers->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/layout/header.php';
        require 'app/views/orders/create.php';
        require 'app/views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->orderModel->customer_id = $_POST['customer_id'];
            $this->orderModel->total_amount = $_POST['total_amount'] ?? 0;
            $this->orderModel->status = 'orcamento';
            $this->orderModel->pipeline_stage = 'contato';
            $this->orderModel->priority = $_POST['priority'] ?? 'normal';
            
            if ($this->orderModel->create()) {
                // Registrar entrada no pipeline
                $database = new Database();
                $db = $database->getConnection();
                require_once 'app/models/Pipeline.php';
                $pipeline = new Pipeline($db);
                $pipeline->addHistory($this->orderModel->id, null, 'contato', $_SESSION['user_id'] ?? null, 'Pedido criado');

                // Log
                require_once 'app/models/Logger.php';
                $logger = new Logger($db);
                $logger->log('ORDER_CREATE', "Pedido #{$this->orderModel->id} criado e inserido no pipeline");

                header('Location: /sistemaTiago/?page=orders&status=success');
                exit;
            } else {
                echo "Erro ao criar pedido.";
            }
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: /sistemaTiago/?page=orders');
            exit;
        }
        $order = $this->orderModel->readOne($_GET['id']);
        if (!$order) {
            header('Location: /sistemaTiago/?page=orders');
            exit;
        }

        require_once 'app/models/Customer.php';
        $database = new Database();
        $db = $database->getConnection();
        $customerModel = new Customer($db);
        $stmt_customers = $customerModel->readAll();
        $customers = $stmt_customers->fetchAll(PDO::FETCH_ASSOC);

        require 'app/views/layout/header.php';
        require 'app/views/orders/edit.php';
        require 'app/views/layout/footer.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->orderModel->id = $_POST['id'];
            $this->orderModel->customer_id = $_POST['customer_id'];
            $this->orderModel->total_amount = $_POST['total_amount'] ?? 0;
            $this->orderModel->status = $_POST['status'];
            
            if ($this->orderModel->update()) {
                header('Location: /sistemaTiago/?page=orders&status=success');
                exit;
            } else {
                echo "Erro ao atualizar pedido.";
            }
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->orderModel->delete($_GET['id']);
            header('Location: /sistemaTiago/?page=orders&status=success');
            exit;
        }
    }
}
