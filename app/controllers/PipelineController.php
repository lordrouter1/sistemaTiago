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
                'notes' => $_POST['notes'] ?? '',
                'deadline' => !empty($_POST['deadline']) ? $_POST['deadline'] : null,
                'payment_status' => $_POST['payment_status'] ?? 'pendente',
                'payment_method' => $_POST['payment_method'] ?? null,
                'discount' => $_POST['discount'] ?? 0,
                'shipping_type' => $_POST['shipping_type'] ?? 'retirada',
                'shipping_address' => $_POST['shipping_address'] ?? '',
                'tracking_code' => $_POST['tracking_code'] ?? '',
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
}
