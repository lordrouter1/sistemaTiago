<?php
class Product {
    private $conn;
    private $table_name = "products";

    public $id;
    public $name;
    public $description;
    public $category_id;
    public $subcategory_id;
    public $price;
    public $stock_quantity;
    public $photo_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    function readAll() {
        $query = "SELECT p.*, 
                         (SELECT image_path FROM product_images pi WHERE pi.product_id = p.id AND pi.is_main = 1 LIMIT 1) as main_image_path
                  FROM " . $this->table_name . " p 
                  ORDER BY p.name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    function getImages($productId) {
        $query = "SELECT * FROM product_images WHERE product_id = :product_id ORDER BY is_main DESC, id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, category_id, subcategory_id, price, stock_quantity, created_at) 
                  VALUES (:name, :description, :category_id, :subcategory_id, :price, :stock_quantity, NOW())";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':subcategory_id', $data['subcategory_id']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    function addImage($productId, $imagePath, $isMain = 0) {
        $query = "INSERT INTO product_images (product_id, image_path, is_main, created_at) 
                  VALUES (:product_id, :image_path, :is_main, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->bindParam(':image_path', $imagePath);
        $stmt->bindParam(':is_main', $isMain);
        return $stmt->execute();
    }

    function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function update($data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, 
                      description = :description, 
                      category_id = :category_id, 
                      subcategory_id = :subcategory_id, 
                      price = :price, 
                      stock_quantity = :stock_quantity 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':subcategory_id', $data['subcategory_id']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':stock_quantity', $data['stock_quantity']);
        $stmt->bindParam(':id', $data['id']);

        return $stmt->execute();
    }
    
    function deleteImage($imageId) {
        $query = "DELETE FROM product_images WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $imageId);
        return $stmt->execute();
    }

    function getImage($imageId) {
        $query = "SELECT * FROM product_images WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $imageId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    function setMainImage($productId, $imageId) {
        // Reset all to 0
        $query = "UPDATE product_images SET is_main = 0 WHERE product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $productId);
        $stmt->execute();
        
        // Set new main
        $query2 = "UPDATE product_images SET is_main = 1 WHERE id = :id AND product_id = :product_id";
        $stmt2 = $this->conn->prepare($query2);
        $stmt2->bindParam(':id', $imageId);
        $stmt2->bindParam(':product_id', $productId);
        return $stmt2->execute();
    }
}
