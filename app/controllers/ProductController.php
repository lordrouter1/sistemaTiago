<?php
require_once 'app/models/Product.php';
require_once 'app/models/Category.php';
require_once 'app/models/Subcategory.php';

class ProductController {
    
    private $productModel;
    private $categoryModel;
    private $subcategoryModel;
    private $logger;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->productModel = new Product($db);
        $this->categoryModel = new Category($db);
        $this->subcategoryModel = new Subcategory($db);
        require_once 'app/models/Logger.php';
        $this->logger = new Logger($db);
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
        // Fetch categories for the dropdown
        $stmt = $this->categoryModel->readAll();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch price tables
        require_once 'app/models/PriceTable.php';
        $database = new Database();
        $db = $database->getConnection();
        $priceTableModel = new PriceTable($db);
        $priceTables = $priceTableModel->readAll();
        $productPrices = []; // Nenhum preço salvo ainda (produto novo)

        require 'app/views/layout/header.php';
        require 'app/views/products/create.php';
        require 'app/views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Handle dynamically added category
            $category_id = $_POST['category_id'] ?? null;
            if ($category_id === 'new' && !empty($_POST['new_category_name'])) {
                 $this->categoryModel->name = $_POST['new_category_name'];
                 if ($this->categoryModel->create()) {
                     $category_id = $this->categoryModel->id;
                 }
            }

            // Handle dynamically added subcategory
            $subcategory_id = $_POST['subcategory_id'] ?? null;
             if ($subcategory_id === 'new' && !empty($_POST['new_subcategory_name']) && $category_id) {
                 $this->subcategoryModel->name = $_POST['new_subcategory_name'];
                 $this->subcategoryModel->category_id = $category_id;
                 if ($this->subcategoryModel->create()) {
                     $subcategory_id = $this->subcategoryModel->id;
                 }
            }

            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'category_id' => $category_id ? $category_id : null,
                'subcategory_id' => $subcategory_id ? $subcategory_id : null,
                'price' => $_POST['price'],
                'stock_quantity' => $_POST['stock_quantity']
            ];

            // Criar Produto primeiro para ter o ID
            $productId = $this->productModel->create($data);

            if($productId) {
                $this->logger->log('CREATE_PRODUCT', 'Created product ID: ' . $productId . ' Name: ' . $data['name']);

                // Salvar preços das tabelas de preço
                if (!empty($_POST['table_prices']) && is_array($_POST['table_prices'])) {
                    require_once 'app/models/PriceTable.php';
                    $dbPT = (new Database())->getConnection();
                    $ptModel = new PriceTable($dbPT);
                    $ptModel->saveProductPrices($productId, $_POST['table_prices']);
                }

                // Upload das fotos
                if(isset($_FILES['product_photos'])) {
                    $files = $_FILES['product_photos'];
                    $mainImageIndex = $_POST['main_image_index'] ?? 0;
                    $uploadDir = 'assets/uploads/products/';
                    
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $maxSize = 5 * 1024 * 1024; // 5 MB
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];

                    for ($i = 0; $i < count($files['name']); $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            
                            // Validate Size
                            if ($files['size'][$i] > $maxSize) {
                                // Skip or log error could be an option, here we might skip silently or could echo errors
                                // For simplicity/robustness, let's skip invalid files but continue others 
                                continue; 
                            }
                            
                            // Validate Type
                            $fileType = mime_content_type($files['tmp_name'][$i]);
                             if (!in_array($fileType, $allowedTypes)) {
                                continue;
                            }

                            $fileExt = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                            $newFileName = uniqid('prod_' . $productId . '_') . '.' . $fileExt;
                            $targetPath = $uploadDir . $newFileName;

                            if(move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                                $isMain = ($i == $mainImageIndex) ? 1 : 0;
                                $this->productModel->addImage($productId, $targetPath, $isMain);
                            }
                        }
                    }
                }
                
                header('Location: /sistemaTiago/?page=products&status=success');
                exit;
            } else {
                echo "Erro ao cadastrar produto.";
            }
        }
    }
    
    // AJAX for subcategories
    public function getSubcategories() {
        if (isset($_GET['category_id'])) {
            $stmt = $this->categoryModel->getSubcategories($_GET['category_id']);
            echo json_encode($stmt);
            exit;
        }
    }
    
    // AJAX for create category on the fly
    public function createCategoryAjax() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
            $this->categoryModel->name = $_POST['name'];
            if ($this->categoryModel->create()) {
                echo json_encode(['success' => true, 'id' => $this->categoryModel->id, 'name' => $_POST['name']]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit;
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
             header('Location: /sistemaTiago/?page=products');
             exit;
        }

        $id = $_GET['id'];
        $product = $this->productModel->readOne($id);

        if (!$product) {
            header('Location: /sistemaTiago/?page=products');
            exit;
        }

        $stmt = $this->categoryModel->readAll();
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $images = $this->productModel->getImages($id);
        
        // Get Subcategories for current category
        $subcategories = [];
        if ($product['category_id']) {
            $subcategories = $this->categoryModel->getSubcategories($product['category_id']);
        }

        // Fetch price tables and existing prices for this product
        require_once 'app/models/PriceTable.php';
        $database = new Database();
        $db = $database->getConnection();
        $priceTableModel = new PriceTable($db);
        $priceTables = $priceTableModel->readAll();
        $productPrices = $priceTableModel->getPricesForProduct($id);

        require 'app/views/layout/header.php';
        require 'app/views/products/edit.php';
        require 'app/views/layout/footer.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle content update similar to store
             $category_id = $_POST['category_id'] ?? null;
             // ... Logic for new categories ...
             if ($category_id === 'new' && !empty($_POST['new_category_name'])) {
                 $this->categoryModel->name = $_POST['new_category_name'];
                 if ($this->categoryModel->create()) {
                     $category_id = $this->categoryModel->id;
                 }
            }

            // Handle dynamically added subcategory
            $subcategory_id = $_POST['subcategory_id'] ?? null;
             if ($subcategory_id === 'new' && !empty($_POST['new_subcategory_name']) && $category_id) {
                 $this->subcategoryModel->name = $_POST['new_subcategory_name'];
                 $this->subcategoryModel->category_id = $category_id;
                 if ($this->subcategoryModel->create()) {
                     $subcategory_id = $this->subcategoryModel->id;
                 }
            }
            
            $data = [
                 'id' => $_POST['id'],
                 'name' => $_POST['name'],
                 'description' => $_POST['description'],
                 'category_id' => $_POST['category_id'],
                 'subcategory_id' => $_POST['subcategory_id'] ?? null,
                 'price' => $_POST['price'],
                 'stock_quantity' => $_POST['stock_quantity']
            ];

            if ($this->productModel->update($data)) {
                $this->logger->log('UPDATE_PRODUCT', 'Updated product ID: ' . $data['id']);

                // Salvar preços das tabelas de preço
                if (isset($_POST['table_prices']) && is_array($_POST['table_prices'])) {
                    require_once 'app/models/PriceTable.php';
                    $dbPT = (new Database())->getConnection();
                    $ptModel = new PriceTable($dbPT);
                    $ptModel->saveProductPrices($data['id'], $_POST['table_prices']);
                }

                header('Location: /sistemaTiago/?page=products&status=success');
                exit;
            }
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            // remove images first
            $images = $this->productModel->getImages($id);
            foreach($images as $img) {
                if(file_exists($img['image_path'])) {
                    unlink($img['image_path']);
                }
            }
            
            if ($this->productModel->delete($id)) {
                $this->logger->log('DELETE_PRODUCT', 'Deleted product ID: ' . $id);
                header('Location: /sistemaTiago/?page=products&status=success');
                exit;
            }
        }
    }

    public function deleteImage() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image_id'])) {
            $image = $this->productModel->getImage($_POST['image_id']);
            if ($image) {
                if (file_exists($image['image_path'])) {
                    unlink($image['image_path']);
                }
                $this->productModel->deleteImage($_POST['image_id']);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit;
        }
    }
}
