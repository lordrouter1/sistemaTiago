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

        // Carregar setores de produção do pedido e permissões do usuário
        // Carrega sempre (inclusive para concluido/cancelado, para visualização)
        $orderProductionSectors = [];
        $userAllowedSectorIds = [];
        $isProduction = ($order['pipeline_stage'] === 'producao');
        if ($isProduction) {
            // Garantir que setores existam (para pedidos que já estavam em produção antes do recurso)
            $this->pipelineModel->initOrderProductionSectors($_GET['id']);
        }
        // Carregar setores mesmo em concluido/cancelado para exibição read-only
        $orderProductionSectors = $this->pipelineModel->getOrderProductionSectors($_GET['id']);
        $userAllowedSectorIds = $userModel->getAllowedSectorIds($_SESSION['user_id'] ?? 0);

        // Carregar logs dos itens do pedido
        require_once 'app/models/OrderItemLog.php';
        $logModel = new OrderItemLog($this->db);
        $logModel->createTableIfNotExists();
        $orderItemLogs = $logModel->getLogsByOrder($_GET['id']);
        $orderItemLogCounts = $logModel->countLogsByOrderGrouped($_GET['id']);

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
                'installments' => !empty($_POST['installments']) ? (int)$_POST['installments'] : null,
                'installment_value' => !empty($_POST['installment_value']) ? (float)$_POST['installment_value'] : null,
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

            if ($orderId && $description && $amount != 0) {
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

    /**
     * Mover setor de produção de um item do pedido (AJAX)
     */
    public function moveSector() {
        header('Content-Type: application/json');
        
        $orderId = $_POST['order_id'] ?? $_GET['order_id'] ?? null;
        $orderItemId = $_POST['order_item_id'] ?? $_GET['order_item_id'] ?? null;
        $sectorId = $_POST['sector_id'] ?? $_GET['sector_id'] ?? null;
        $action = $_POST['move_action'] ?? $_GET['move_action'] ?? 'advance'; // advance, revert
        $userId = $_SESSION['user_id'] ?? null;

        if (!$orderId || !$orderItemId || !$sectorId) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
            exit;
        }

        // Verificar permissão do usuário para este setor
        $userModel = new User($this->db);
        $allowedSectors = $userModel->getAllowedSectorIds($userId);
        if (!empty($allowedSectors) && !in_array((int)$sectorId, $allowedSectors)) {
            echo json_encode(['success' => false, 'message' => 'Sem permissão para este setor']);
            exit;
        }

        $result = false;
        if ($action === 'advance') {
            $result = $this->pipelineModel->advanceItemSector($orderId, $orderItemId, $sectorId, $userId);
        } elseif ($action === 'revert') {
            $result = $this->pipelineModel->revertItemSector($orderId, $orderItemId, $sectorId, $userId);
        }

        if ($result) {
            require_once 'app/models/Logger.php';
            $logger = new Logger($this->db);
            $logger->log('PRODUCTION_SECTOR_MOVE', "Order #$orderId item #$orderItemId sector #$sectorId action:$action");
        }

        echo json_encode(['success' => $result]);
        exit;
    }

    /**
     * Painel de Produção: visão por setor com tabs
     */
    public function productionBoard() {
        $userModel = new User($this->db);
        $userAllowedSectorIds = $userModel->getAllowedSectorIds($_SESSION['user_id'] ?? 0);

        // Garantir que todos os pedidos em produção tenham setores inicializados
        $stmtOrders = $this->db->prepare("SELECT id FROM orders WHERE pipeline_stage = 'producao' AND status != 'cancelado'");
        $stmtOrders->execute();
        $prodOrders = $stmtOrders->fetchAll(PDO::FETCH_COLUMN);
        foreach ($prodOrders as $oid) {
            $this->pipelineModel->initOrderProductionSectors($oid);
        }
        
        $boardData = $this->pipelineModel->getProductionBoardData($userAllowedSectorIds);
        
        // Carregar contagem de logs por item para badges
        require_once 'app/models/OrderItemLog.php';
        $logModel = new OrderItemLog($this->db);
        $logModel->createTableIfNotExists();
        $allItemIds = [];
        foreach ($boardData as &$sec) {
            foreach ($sec['items'] as &$it) {
                $allItemIds[] = $it['order_item_id'];
            }
            unset($it);
        }
        unset($sec);
        // Buscar contagens em batch
        $itemLogCounts = [];
        if (!empty($allItemIds)) {
            $placeholders = implode(',', array_fill(0, count($allItemIds), '?'));
            $stmtCounts = $this->db->prepare("SELECT order_item_id, COUNT(*) as total FROM order_item_logs WHERE order_item_id IN ($placeholders) GROUP BY order_item_id");
            $stmtCounts->execute($allItemIds);
            foreach ($stmtCounts->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $itemLogCounts[$row['order_item_id']] = (int)$row['total'];
            }
        }

        $stages = Pipeline::$stages;

        require 'app/views/layout/header.php';
        require 'app/views/pipeline/production_board.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * API JSON: Buscar logs de um item do pedido (AJAX — usado pelo modal do painel de produção e detalhe)
     */
    public function getItemLogs() {
        header('Content-Type: application/json');
        require_once 'app/models/OrderItemLog.php';
        $logModel = new OrderItemLog($this->db);
        $logModel->createTableIfNotExists();

        $orderItemId = $_GET['order_item_id'] ?? null;
        if (!$orderItemId) {
            echo json_encode(['success' => false, 'message' => 'Item não informado']);
            exit;
        }

        $logs = $logModel->getLogsByItem($orderItemId);
        echo json_encode(['success' => true, 'logs' => $logs]);
        exit;
    }

    /**
     * Adicionar log a um item do pedido (AJAX POST, com suporte a upload)
     */
    public function addItemLog() {
        header('Content-Type: application/json');
        require_once 'app/models/OrderItemLog.php';
        $logModel = new OrderItemLog($this->db);
        $logModel->createTableIfNotExists();

        $orderId = $_POST['order_id'] ?? null;
        $orderItemId = $_POST['order_item_id'] ?? null;
        $message = trim($_POST['message'] ?? '');
        $userId = $_SESSION['user_id'] ?? null;

        if (!$orderId || !$orderItemId) {
            echo json_encode(['success' => false, 'message' => 'Parâmetros inválidos']);
            exit;
        }

        // Processar upload se houver
        $filePath = null;
        $fileName = null;
        $fileType = null;

        if (!empty($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = $logModel->handleFileUpload($_FILES['file'], $orderId, $orderItemId);
            if (isset($uploadResult['error'])) {
                echo json_encode(['success' => false, 'message' => $uploadResult['error']]);
                exit;
            }
            $filePath = $uploadResult['file_path'];
            $fileName = $uploadResult['file_name'];
            $fileType = $uploadResult['file_type'];
        }

        // Precisa ter pelo menos mensagem ou arquivo
        if (empty($message) && empty($filePath)) {
            echo json_encode(['success' => false, 'message' => 'Informe uma mensagem ou envie um arquivo.']);
            exit;
        }

        $logId = $logModel->addLog($orderId, $orderItemId, $userId, $message ?: null, $filePath, $fileName, $fileType);

        // Log do sistema
        require_once 'app/models/Logger.php';
        $logger = new Logger($this->db);
        $logger->log('ITEM_LOG_ADDED', "Log #$logId added to order #$orderId item #$orderItemId");

        echo json_encode(['success' => true, 'log_id' => $logId]);
        exit;
    }

    /**
     * Excluir um log de item (AJAX POST)
     */
    public function deleteItemLog() {
        header('Content-Type: application/json');
        require_once 'app/models/OrderItemLog.php';
        $logModel = new OrderItemLog($this->db);

        $logId = $_POST['log_id'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        if (!$logId) {
            echo json_encode(['success' => false, 'message' => 'ID do log não informado']);
            exit;
        }

        $result = $logModel->deleteLog($logId, $userId);
        echo json_encode(['success' => $result]);
        exit;
    }

    /**
     * Imprimir Ordem de Produção (para pedidos em produção)
     */
    public function printProductionOrder() {
        if (!isset($_GET['id'])) {
            header('Location: /sistemaTiago/?page=pipeline');
            exit;
        }

        $order = $this->pipelineModel->getOrderDetail($_GET['id']);
        if (!$order) {
            header('Location: /sistemaTiago/?page=pipeline');
            exit;
        }

        // Buscar itens do pedido
        $orderModel = new Order($this->db);
        $orderItems = $orderModel->getItems($_GET['id']);

        // Buscar setores de produção
        $orderProductionSectors = $this->pipelineModel->getOrderProductionSectors($_GET['id']);

        // Dados da empresa
        require_once 'app/models/CompanySettings.php';
        $companyModel = new CompanySettings($this->db);
        $company = $companyModel->getAll();
        $companyAddress = $companyModel->getFormattedAddress();

        require 'app/views/pipeline/print_production_order.php';
    }
}
