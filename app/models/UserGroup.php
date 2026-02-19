<?php
class UserGroup {
    private $conn;
    private $table_name = "user_groups";
    
    public $id;
    public $name;
    public $description;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function readAll() {
        $query = "SELECT DISTINCT * FROM " . $this->table_name . " ORDER BY id ASC"; 
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET name = :name, description = :description";
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->name = $row['name'];
            $this->description = $row['description'];
            return $row;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function addPermission($groupId, $pageName) {
        $query = "INSERT INTO group_permissions (group_id, page_name) VALUES (:group_id, :page_name)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->bindParam(':page_name', $pageName);
        return $stmt->execute();
    }

    public function getPermissions($groupId) {
        $query = "SELECT page_name FROM group_permissions WHERE group_id = :group_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':group_id', $groupId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // Returns simple array of strings
    }

    public function deletePermissions($groupId) {
        $query = "DELETE FROM group_permissions WHERE group_id = :group_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':group_id', $groupId);
        return $stmt->execute();
    }
}
?>
