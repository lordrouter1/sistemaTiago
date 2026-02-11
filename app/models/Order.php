<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public $id;
    public $customer_id;
    public $total_amount;
    public $status;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (customer_id, total_amount, status, created_at) 
                  VALUES (:customer_id, :total_amount, :status, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':customer_id', $this->customer_id);
        $stmt->bindParam(':total_amount', $this->total_amount);
        $stmt->bindParam(':status', $this->status);
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readAll() {
        $query = "SELECT o.id, o.total_amount, o.status, o.created_at, c.name as customer_name 
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
}
