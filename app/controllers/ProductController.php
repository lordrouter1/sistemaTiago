<?php
require_once 'app/models/Product.php';

class ProductController {
    
    private $productModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new Product($db);
    }

    public function index() {
        // Buscar produtos do banco
        $stmt = $this->productModel->readAll();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require 'app/views/layout/header.php';
        require 'app/views/products/index.php';
        require 'app/views/layout/footer.php';
    }

    public function create() {
        require 'app/views/layout/header.php';
        require 'app/views/products/create.php';
        require 'app/views/layout/footer.php';
    }
}
