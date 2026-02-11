<?php
require_once 'app/models/Customer.php';

class CustomerController {
    
    private $customerModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->customerModel = new Customer($db);
    }

    public function index() {
        // Buscar clientes do banco
        $stmt = $this->customerModel->readAll();
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        require 'app/views/layout/header.php';
        require 'app/views/customers/index.php';
        require 'app/views/layout/footer.php';
    }

    public function create() {
        require 'app/views/layout/header.php';
        require 'app/views/customers/create.php';
        require 'app/views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Upload da Imagem
            $photoPath = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'assets/uploads/customers/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $fileName = uniqid() . '.' . $fileExtension;
                $targetFile = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                    $photoPath = $targetFile;
                }
            }

            $address = json_encode([
                'zipcode' => $_POST['zipcode'] ?? '',
                'address_type' => $_POST['address_type'] ?? '',
                'address_name' => $_POST['address_name'] ?? '',
                'address_number' => $_POST['address_number'] ?? '',
                'neighborhood' => $_POST['neighborhood'] ?? '',
                'complement' => $_POST['complement'] ?? ''
            ]);
            
            $this->customerModel->create([
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'],
                'document' => $_POST['document'],
                'address' => $address,
                'photo' => $photoPath
            ]);
            
            header('Location: /sistemaTiago/?page=customers');
            exit;
        }
    }
}
