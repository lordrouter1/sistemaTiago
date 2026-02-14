<?php
require_once 'app/models/Pipeline.php';
require_once 'app/models/Order.php';
require_once 'app/models/Customer.php';
require_once 'app/models/User.php';

class PipelineController {

    private $pipelineModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->pipelineModel = new Pipeline($this->db);
    }

    /**
     * View principal: Kanban Board
     */
    public function index() {
        $ordersByStage = $this->pipelineModel->getOrdersByStage();
        $stages = Pipeline::$stages;
        $goals = $this->pipelineModel->getStageGoals();
        $stats = $this->pipelineModel->getStats();
        $delayedOrders = $stats['delayed_orders'];

        require 'app/views/layout/header.php';
        require 'app/views/pipeline/index.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * Mover pedido para outra etapa (AJAX ou GET)
     */
    public function move() {
        if (!isset($_GET['id']) || !isset($_GET['stage'])) {
            header('Location: /sistemaTiago/?page=pipeline');
            exit;
        }

        $orderId = $_GET['id'];
        $newStage = $_GET['stage'];
        $notes = $_POST['notes'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;

        $this->pipelineModel->moveToStage($orderId, $newStage, $userId, $notes);

        // Log
        require_once 'app/models/Logger.php';
        $logger = new Logger($this->db);
        $logger->log('PIPELINE_MOVE', "Order #$orderId moved to stage: $newStage");

        header('Location: /sistemaTiago/?page=pipeline&status=moved');
        exit;
    }

    /**
     * Detalhes de um pedido no pipeline
     */
    public function detail() {
        if (!isset($_GET['id'])) {
            header('Location: /sistemaTiago/?page=pipeline');
            exit;
        }

        $order = $this->pipelineModel->getOrderDetail($_GET['id']);
        if (!$order) {
            header('Location: /sistemaTiago/?page=pipeline');
            exit;
        }

        $history = $this->pipelineModel->getHistory($_GET['id']);
        $stages = Pipeline::$stages;
        $goals = $this->pipelineModel->getStageGoals();

        // Buscar usuários para atribuição
        $userModel = new User($this->db);
        $usersStmt = $userModel->readAll();
        $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

        // Buscar produtos e itens do pedido (para seção de orçamento)
        require_once 'app/models/Product.php';
        require_once 'app/models/PriceTable.php';
        $productModel = new Product($this->db);
        $stmt_products = $productModel->readAll();
        $products = $stmt_products->fetchAll(PDO::FETCH_ASSOC);

        $orderModel = new Order($this->db);
        $orderItems = $orderModel->getItems($_GET['id']);
        $extraCosts = $orderModel->getExtraCosts($_GET['id']);

        // Carregar preços específicos do cliente (tabela de preço)
        $priceTableModel = new PriceTable($this->db);
        $customerPrices = [];
        if (!empty($order['customer_id'])) {
            $customerPrices = $priceTableModel->getAllPricesForCustomer($order['customer_id']);
        }

        // Carregar todas as tabelas de preço para o seletor
        $priceTables = $priceTableModel->readAll();

        // Identificar tabela de preço atual do pedido ou do cliente
        $currentPriceTableId = $order['price_table_id'] ?? null;
        if (!$currentPriceTableId && !empty($order['customer_id'])) {
            require_once 'app/models/Customer.php';
            $customerModel = new Customer($this->db);
            $customerData = $customerModel->readOne($order['customer_id']);
            $currentPriceTableId = $customerData['price_table_id'] ?? null;
        }

        require 'app/views/layout/header.php';
        require 'app/views/pipeline/detail.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * Atualizar detalhes do pedido (POST)
     */
    public function updateDetails() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id' => $_POST['id'],
                'priority' => $_POST['priority'] ?? 'normal',
                'assigned_to' => !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null,
                'internal_notes' => $_POST['internal_notes'] ?? '',
                'quote_notes' => $_POST['quote_notes'] ?? '',
                'deadline' => !empty($_POST['deadline']) ? $_POST['deadline'] : null,
                'payment_status' => $_POST['payment_status'] ?? 'pendente',
                'payment_method' => $_POST['payment_method'] ?? null,
                'discount' => $_POST['discount'] ?? 0,
                'shipping_type' => $_POST['shipping_type'] ?? 'retirada',
                'shipping_address' => $_POST['shipping_address'] ?? '',
                'tracking_code' => $_POST['tracking_code'] ?? '',
                'price_table_id' => !empty($_POST['price_table_id']) ? $_POST['price_table_id'] : null,
            ];

            $this->pipelineModel->updateOrderDetails($data);

            require_once 'app/models/Logger.php';
            $logger = new Logger($this->db);
            $logger->log('PIPELINE_UPDATE', "Updated order details #" . $data['id']);

            header('Location: /sistemaTiago/?page=pipeline&action=detail&id=' . $data['id'] . '&status=success');
            exit;
        }
    }

    /**
     * Configurações de metas por etapa
     */
    public function settings() {
        $goals = $this->pipelineModel->getStageGoals();
        $stages = Pipeline::$stages;

        require 'app/views/layout/header.php';
        require 'app/views/pipeline/settings.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * Salvar configurações de metas (POST)
     */
    public function saveSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            foreach ($_POST['max_hours'] as $stage => $hours) {
                $this->pipelineModel->updateStageGoal($stage, (int)$hours);
            }

            require_once 'app/models/Logger.php';
            $logger = new Logger($this->db);
            $logger->log('PIPELINE_SETTINGS', 'Updated pipeline stage goals');

            header('Location: /sistemaTiago/?page=pipeline&action=settings&status=success');
            exit;
        }
    }

    /**
     * API JSON: pedidos atrasados (para notificações)
     */
    public function alerts() {
        $delayed = $this->pipelineModel->getDelayedOrders();
        header('Content-Type: application/json');
        echo json_encode(['delayed' => $delayed, 'count' => count($delayed)]);
        exit;
    }

    /**
     * API JSON: Retorna preços de uma tabela de preço específica (AJAX)
     */
    public function getPricesByTable() {
        require_once 'app/models/PriceTable.php';
        $priceTableModel = new PriceTable($this->db);
        $tableId = $_GET['table_id'] ?? null;
        $customerId = $_GET['customer_id'] ?? null;

        $prices = [];
        if ($tableId) {
            // Buscar preços da tabela específica com fallback ao preço base
            $products = $this->db->query("SELECT id, price FROM products")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($products as $p) {
                $prices[$p['id']] = (float)$p['price'];
            }
            // Sobrepor com preços da tabela selecionada
            $items = $priceTableModel->getItems($tableId);
            foreach ($items as $item) {
                $prices[$item['product_id']] = (float)$item['price'];
            }
        } elseif ($customerId) {
            $prices = $priceTableModel->getAllPricesForCustomer($customerId);
        }

        header('Content-Type: application/json');
        echo json_encode($prices);
        exit;
    }

    /**
     * Adicionar custo extra ao pedido (POST)
     */
    public function addExtraCost() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = $_POST['order_id'] ?? null;
            $description = $_POST['extra_description'] ?? '';
            $amount = (float)($_POST['extra_amount'] ?? 0);

            if ($orderId && $description && $amount > 0) {
                $orderModel = new Order($this->db);
                $orderModel->addExtraCost($orderId, $description, $amount);
            }

            header('Location: /sistemaTiago/?page=pipeline&action=detail&id=' . $orderId . '&status=extra_added');
            exit;
        }
    }

    /**
     * Remover custo extra do pedido
     */
    public function deleteExtraCost() {
        $costId = $_GET['cost_id'] ?? null;
        $orderId = $_GET['order_id'] ?? null;

        if ($costId) {
            $orderModel = new Order($this->db);
            $orderModel->deleteExtraCost($costId);
        }

        header('Location: /sistemaTiago/?page=pipeline&action=detail&id=' . $orderId . '&status=extra_deleted');
        exit;
    }
}
