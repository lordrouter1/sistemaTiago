<?php
class Pipeline {
    private $conn;

    // Definição das etapas do pipeline
    public static $stages = [
        'contato'    => ['label' => 'Contato',       'icon' => 'fas fa-phone',                'color' => '#9b59b6', 'order' => 1],
        'orcamento'  => ['label' => 'Orçamento',     'icon' => 'fas fa-file-invoice-dollar',   'color' => '#3498db', 'order' => 2],
        'venda'      => ['label' => 'Venda',         'icon' => 'fas fa-handshake',             'color' => '#2ecc71', 'order' => 3],
        'producao'   => ['label' => 'Produção',      'icon' => 'fas fa-industry',              'color' => '#e67e22', 'order' => 4],
        'preparacao' => ['label' => 'Preparação',    'icon' => 'fas fa-boxes-packing',         'color' => '#1abc9c', 'order' => 5],
        'envio'      => ['label' => 'Envio/Entrega', 'icon' => 'fas fa-truck',                 'color' => '#e74c3c', 'order' => 6],
        'financeiro' => ['label' => 'Financeiro',    'icon' => 'fas fa-coins',                 'color' => '#f39c12', 'order' => 7],
        'concluido'  => ['label' => 'Concluído',     'icon' => 'fas fa-check-double',          'color' => '#27ae60', 'order' => 8],
    ];

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Busca todos os pedidos ativos no pipeline (não concluídos/cancelados) 
     * agrupados por etapa, com dados do cliente e responsável.
     * Para contatos agendados, usa scheduled_date para calcular horas na etapa.
     */
    public function getOrdersByStage() {
        $query = "SELECT o.*, c.name as customer_name, c.phone as customer_phone,
                         u.name as assigned_name,
                         CASE 
                            WHEN o.pipeline_stage = 'contato' AND o.scheduled_date IS NOT NULL AND o.scheduled_date > CURDATE()
                                THEN 0
                            WHEN o.pipeline_stage = 'contato' AND o.scheduled_date IS NOT NULL 
                                THEN TIMESTAMPDIFF(HOUR, o.scheduled_date, NOW())
                            ELSE TIMESTAMPDIFF(HOUR, o.pipeline_entered_at, NOW())
                         END as hours_in_stage
                  FROM orders o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  LEFT JOIN users u ON o.assigned_to = u.id
                  WHERE o.pipeline_stage != 'concluido' AND o.status != 'cancelado'
                  ORDER BY o.priority DESC, o.pipeline_entered_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar por etapa
        $grouped = [];
        foreach (self::$stages as $key => $info) {
            $grouped[$key] = [];
        }
        foreach ($orders as $order) {
            $stage = $order['pipeline_stage'] ?? 'contato';
            if (isset($grouped[$stage])) {
                $grouped[$stage][] = $order;
            }
        }
        return $grouped;
    }

    /**
     * Busca metas de tempo por etapa
     */
    public function getStageGoals() {
        $query = "SELECT * FROM pipeline_stage_goals ORDER BY stage_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $goals = [];
        foreach ($rows as $row) {
            $goals[$row['stage']] = $row;
        }
        return $goals;
    }

    /**
     * Atualiza a meta de horas de uma etapa
     */
    public function updateStageGoal($stage, $maxHours) {
        $query = "UPDATE pipeline_stage_goals SET max_hours = :max_hours WHERE stage = :stage";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':max_hours', $maxHours, PDO::PARAM_INT);
        $stmt->bindParam(':stage', $stage);
        return $stmt->execute();
    }

    /**
     * Move um pedido para a próxima etapa (ou uma etapa específica)
     */
    public function moveToStage($orderId, $newStage, $userId = null, $notes = '') {
        // Buscar etapa atual
        $query = "SELECT pipeline_stage FROM orders WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        $current = $stmt->fetch(PDO::FETCH_ASSOC);
        $fromStage = $current ? $current['pipeline_stage'] : null;

        // Atualizar pedido
        $query = "UPDATE orders SET pipeline_stage = :stage, pipeline_entered_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stage', $newStage);
        $stmt->bindParam(':id', $orderId);
        $result = $stmt->execute();

        // Atualizar status compatível
        $statusMap = [
            'contato'    => 'orcamento',
            'orcamento'  => 'orcamento',
            'venda'      => 'aprovado',
            'producao'   => 'em_producao',
            'preparacao' => 'em_producao',
            'envio'      => 'em_producao',
            'financeiro' => 'em_producao',
            'concluido'  => 'concluido',
        ];
        if (isset($statusMap[$newStage])) {
            $newStatus = $statusMap[$newStage];
            $q = "UPDATE orders SET status = :status WHERE id = :id";
            $s = $this->conn->prepare($q);
            $s->bindParam(':status', $newStatus);
            $s->bindParam(':id', $orderId);
            $s->execute();
        }

        // Registrar histórico
        if ($result) {
            $this->addHistory($orderId, $fromStage, $newStage, $userId, $notes);
        }

        return $result;
    }

    /**
     * Registra histórico de movimentação
     */
    public function addHistory($orderId, $fromStage, $toStage, $userId = null, $notes = '') {
        $query = "INSERT INTO pipeline_history (order_id, from_stage, to_stage, changed_by, notes) 
                  VALUES (:order_id, :from_stage, :to_stage, :changed_by, :notes)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->bindParam(':from_stage', $fromStage);
        $stmt->bindParam(':to_stage', $toStage);
        $stmt->bindParam(':changed_by', $userId);
        $stmt->bindParam(':notes', $notes);
        return $stmt->execute();
    }

    /**
     * Busca histórico de um pedido
     */
    public function getHistory($orderId) {
        $query = "SELECT ph.*, u.name as user_name 
                  FROM pipeline_history ph 
                  LEFT JOIN users u ON ph.changed_by = u.id 
                  WHERE ph.order_id = :order_id 
                  ORDER BY ph.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':order_id', $orderId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca detalhes completos de um pedido para o pipeline
     */
    public function getOrderDetail($orderId) {
        $query = "SELECT o.*, c.name as customer_name, c.email as customer_email, 
                         c.phone as customer_phone, c.document as customer_document,
                         c.address as customer_address,
                         u.name as assigned_name,
                         CASE 
                            WHEN o.pipeline_stage = 'contato' AND o.scheduled_date IS NOT NULL AND o.scheduled_date > CURDATE()
                                THEN 0
                            WHEN o.pipeline_stage = 'contato' AND o.scheduled_date IS NOT NULL 
                                THEN TIMESTAMPDIFF(HOUR, o.scheduled_date, NOW())
                            ELSE TIMESTAMPDIFF(HOUR, o.pipeline_entered_at, NOW())
                         END as hours_in_stage
                  FROM orders o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  LEFT JOIN users u ON o.assigned_to = u.id
                  WHERE o.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $orderId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Atualiza dados extras do pedido (prioridade, responsável, notas, financeiro, envio)
     */
    public function updateOrderDetails($data) {
        $query = "UPDATE orders SET 
                    priority = :priority,
                    assigned_to = :assigned_to,
                    notes = :notes,
                    deadline = :deadline,
                    payment_status = :payment_status,
                    payment_method = :payment_method,
                    discount = :discount,
                    shipping_type = :shipping_type,
                    shipping_address = :shipping_address,
                    tracking_code = :tracking_code
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':priority', $data['priority']);
        $stmt->bindParam(':assigned_to', $data['assigned_to']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->bindParam(':deadline', $data['deadline']);
        $stmt->bindParam(':payment_status', $data['payment_status']);
        $stmt->bindParam(':payment_method', $data['payment_method']);
        $stmt->bindParam(':discount', $data['discount']);
        $stmt->bindParam(':shipping_type', $data['shipping_type']);
        $stmt->bindParam(':shipping_address', $data['shipping_address']);
        $stmt->bindParam(':tracking_code', $data['tracking_code']);
        $stmt->bindParam(':id', $data['id']);
        return $stmt->execute();
    }

    /**
     * Conta pedidos atrasados (acima da meta de horas por etapa)
     * Para contatos agendados, respeita a scheduled_date
     */
    public function getDelayedOrders() {
        $goals = $this->getStageGoals();
        $query = "SELECT o.*, c.name as customer_name,
                         CASE 
                            WHEN o.pipeline_stage = 'contato' AND o.scheduled_date IS NOT NULL AND o.scheduled_date > CURDATE()
                                THEN 0
                            WHEN o.pipeline_stage = 'contato' AND o.scheduled_date IS NOT NULL 
                                THEN TIMESTAMPDIFF(HOUR, o.scheduled_date, NOW())
                            ELSE TIMESTAMPDIFF(HOUR, o.pipeline_entered_at, NOW())
                         END as hours_in_stage
                  FROM orders o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  WHERE o.pipeline_stage != 'concluido' AND o.status != 'cancelado'
                  ORDER BY hours_in_stage DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $delayed = [];
        foreach ($orders as $order) {
            $stage = $order['pipeline_stage'] ?? 'contato';
            $maxHours = isset($goals[$stage]) ? (int)$goals[$stage]['max_hours'] : 24;
            if ($maxHours > 0 && (int)$order['hours_in_stage'] > $maxHours) {
                $order['max_hours'] = $maxHours;
                $order['delay_hours'] = (int)$order['hours_in_stage'] - $maxHours;
                $delayed[] = $order;
            }
        }
        return $delayed;
    }

    /**
     * Busca pedidos concluídos (para histórico/relatório)
     */
    public function getCompletedOrders($limit = 50) {
        $query = "SELECT o.*, c.name as customer_name 
                  FROM orders o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  WHERE o.pipeline_stage = 'concluido' OR o.status = 'concluido'
                  ORDER BY o.created_at DESC
                  LIMIT :limit";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Estatísticas do pipeline para o dashboard
     */
    public function getStats() {
        // Total por etapa
        $query = "SELECT pipeline_stage, COUNT(*) as total 
                  FROM orders 
                  WHERE status != 'cancelado'
                  GROUP BY pipeline_stage";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $byStage = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // Total geral ativo
        $query2 = "SELECT COUNT(*) FROM orders WHERE pipeline_stage != 'concluido' AND status != 'cancelado'";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->execute();
        $totalActive = $stmt2->fetchColumn();

        // Total atrasados
        $delayed = $this->getDelayedOrders();
        $totalDelayed = count($delayed);

        // Total concluídos no mês
        $query3 = "SELECT COUNT(*) FROM orders WHERE pipeline_stage = 'concluido' 
                   AND MONTH(pipeline_entered_at) = MONTH(NOW()) AND YEAR(pipeline_entered_at) = YEAR(NOW())";
        $stmt3 = $this->conn->prepare($query3);
        $stmt3->execute();
        $completedMonth = $stmt3->fetchColumn();

        // Valor total ativo
        $query4 = "SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE pipeline_stage != 'concluido' AND status != 'cancelado'";
        $stmt4 = $this->conn->prepare($query4);
        $stmt4->execute();
        $totalValue = $stmt4->fetchColumn();

        return [
            'by_stage' => $byStage,
            'total_active' => $totalActive,
            'total_delayed' => $totalDelayed,
            'completed_month' => $completedMonth,
            'total_value' => $totalValue,
            'delayed_orders' => $delayed,
        ];
    }
}
