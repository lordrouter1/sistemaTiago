<?php
class Subcategory {
    private $conn;
    private $table_name = "subcategories";

    public $id;
    public $category_id;
    public $name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readByCategoryId($categoryId) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE category_id = :category_id ORDER BY name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":category_id", $categoryId);
        $stmt->execute();
        return $stmt;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name=:name, category_id=:category_id";
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":category_id", $this->category_id);

        if($stmt->execute()) {
             $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }
}
