<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $customer_id;
    public $total_amount;
    public $status;
    public $pipeline_stage;
    public $priority;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (customer_id, total_amount, status, pipeline_stage, pipeline_entered_at, priority, created_at) 
                  VALUES (:customer_id, :total_amount, :status, :pipeline_stage, NOW(), :priority, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':status', $this->status);
        $pipelineStage = $this->pipeline_stage ?? 'contato';
        $stmt->bindParam(':pipeline_stage', $pipelineStage);
        $priority = $this->priority ?? 'normal';
        $stmt->bindParam(':priority', $priority);
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT o.id, o.total_amount, o.status, o.pipeline_stage, o.priority, 
                         o.deadline, o.payment_status, o.created_at, c.name as customer_name 
                  FROM " . $this->table_name . " o
                  LEFT JOIN customers c ON o.customer_id = c.id
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT o.*, c.name as customer_name 
                  FROM " . $this->table_name . " o 
                  LEFT JOIN customers c ON o.customer_id = c.id 
                  WHERE o.id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET customer_id = :customer_id, total_amount = :total_amount, status = :status 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    /**
     * Conta pedidos por status para o dashboard
     */
    public function countByStatus() {
        $query = "SELECT status, COUNT(*) as total FROM " . $this->table_name . " GROUP BY status";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    /**
     * Total de pedidos
     */
    public function countAll() {
        $query = "SELECT COUNT(*) FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Total de valor dos pedidos ativos
     */
    public function totalActiveValue() {
        $query = "SELECT COALESCE(SUM(total_amount), 0) FROM " . $this->table_name . " WHERE status != 'cancelado' AND status != 'concluido'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
