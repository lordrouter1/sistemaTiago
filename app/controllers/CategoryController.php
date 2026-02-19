<?php
require_once 'app/models/Category.php';
require_once 'app/models/Subcategory.php';
require_once 'app/models/ProductionSector.php';

class CategoryController {
    
    private $categoryModel;
    private $subcategoryModel;
    private $sectorModel;
    private $logger;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->categoryModel = new Category($db);
        $this->subcategoryModel = new Subcategory($db);
        $this->sectorModel = new ProductionSector($db);
        require_once 'app/models/Logger.php';
        $this->logger = new Logger($db);
    }

    public function index() {
        $categories = $this->categoryModel->readAllWithCount();
        $subcategories = $this->subcategoryModel->readAll();
        $allSectors = $this->sectorModel->readAll(true);
        
        // Precarregar setores de cada categoria para exibição na lista
        $categorySectorsMap = [];
        foreach ($categories as $cat) {
            $categorySectorsMap[$cat['id']] = $this->sectorModel->getCategorySectors($cat['id']);
        }
        
        // Precarregar setores de cada subcategoria para exibição na lista
        $subcategorySectorsMap = [];
        foreach ($subcategories as $sub) {
            $subcategorySectorsMap[$sub['id']] = $this->sectorModel->getSubcategorySectors($sub['id']);
        }

        $editCategory = null;
        $editSubcategory = null;
        $editCategorySectors = [];
        $editSubcategorySectors = [];

        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $editCategory = $this->categoryModel->getCategory($_GET['id']);
            $editCategorySectors = $this->sectorModel->getCategorySectors($_GET['id']);
        }
        if (isset($_GET['action']) && $_GET['action'] === 'editSub' && isset($_GET['id'])) {
            $editSubcategory = $this->subcategoryModel->readOne($_GET['id']);
            $editSubcategorySectors = $this->sectorModel->getSubcategorySectors($_GET['id']);
        }

        require 'app/views/layout/header.php';
        require 'app/views/categories/index.php';
        require 'app/views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $this->categoryModel->name = $_POST['name'];
            if ($this->categoryModel->create()) {
                $this->logger->log('CREATE_CATEGORY', 'Created category: ' . $_POST['name']);
                // Salvar setores vinculados
                if (isset($_POST['sector_ids']) && is_array($_POST['sector_ids'])) {
                    $this->sectorModel->saveCategorySectors($this->categoryModel->id, $_POST['sector_ids']);
                }
            }
        }
        header('Location: /sistemaTiago/?page=categories&status=success');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->categoryModel->update($_POST['id'], $_POST['name']);
            $this->logger->log('UPDATE_CATEGORY', 'Updated category ID: ' . $_POST['id']);
            // Salvar setores vinculados
            $sectorIds = isset($_POST['sector_ids']) && is_array($_POST['sector_ids']) ? $_POST['sector_ids'] : [];
            $this->sectorModel->saveCategorySectors($_POST['id'], $sectorIds);
        }
        header('Location: /sistemaTiago/?page=categories&status=success');
        exit;
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->categoryModel->delete($_GET['id']);
            $this->logger->log('DELETE_CATEGORY', 'Deleted category ID: ' . $_GET['id']);
        }
        header('Location: /sistemaTiago/?page=categories&status=success');
        exit;
    }

    public function storeSub() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name']) && !empty($_POST['category_id'])) {
            $this->subcategoryModel->name = $_POST['name'];
            $this->subcategoryModel->category_id = $_POST['category_id'];
            if ($this->subcategoryModel->create()) {
                $this->logger->log('CREATE_SUBCATEGORY', 'Created subcategory: ' . $_POST['name']);
                // Salvar setores vinculados
                if (isset($_POST['sector_ids']) && is_array($_POST['sector_ids'])) {
                    $this->sectorModel->saveSubcategorySectors($this->subcategoryModel->id, $_POST['sector_ids']);
                }
            }
        }
        header('Location: /sistemaTiago/?page=categories&tab=subcategories&status=success');
        exit;
    }

    public function updateSub() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->subcategoryModel->update($_POST['id'], $_POST['name'], $_POST['category_id']);
            $this->logger->log('UPDATE_SUBCATEGORY', 'Updated subcategory ID: ' . $_POST['id']);
            // Salvar setores vinculados
            $sectorIds = isset($_POST['sector_ids']) && is_array($_POST['sector_ids']) ? $_POST['sector_ids'] : [];
            $this->sectorModel->saveSubcategorySectors($_POST['id'], $sectorIds);
        }
        header('Location: /sistemaTiago/?page=categories&tab=subcategories&status=success');
        exit;
    }

    public function deleteSub() {
        if (isset($_GET['id'])) {
            $this->subcategoryModel->delete($_GET['id']);
            $this->logger->log('DELETE_SUBCATEGORY', 'Deleted subcategory ID: ' . $_GET['id']);
        }
        header('Location: /sistemaTiago/?page=categories&tab=subcategories&status=success');
        exit;
    }
}
