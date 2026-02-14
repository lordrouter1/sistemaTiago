<?php
require_once 'app/models/CompanySettings.php';
require_once 'app/models/PriceTable.php';
require_once 'app/models/Product.php';

class SettingsController {

    private $db;
    private $companySettings;
    private $priceTable;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->companySettings = new CompanySettings($this->db);
        $this->priceTable = new PriceTable($this->db);
    }

    // ──────── CONFIGURAÇÕES DA EMPRESA ────────

    /**
     * Página de configurações da empresa
     */
    public function index() {
        $settings = $this->companySettings->getAll();
        $priceTables = $this->priceTable->readAll();

        require 'app/views/layout/header.php';
        require 'app/views/settings/index.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * Salvar configurações da empresa (POST)
     */
    public function saveCompany() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /sistemaTiago/?page=settings');
            exit;
        }

        $keys = [
            'company_name', 'company_document', 'company_phone', 'company_email',
            'company_website', 'company_zipcode', 'company_address_type',
            'company_address_name', 'company_address_number', 'company_neighborhood',
            'company_complement', 'company_city', 'company_state',
            'quote_validity_days', 'quote_footer_note'
        ];

        foreach ($keys as $key) {
            if (isset($_POST[$key])) {
                $this->companySettings->set($key, $_POST[$key]);
            }
        }

        // Upload de logo
        if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $ext = pathinfo($_FILES['company_logo']['name'], PATHINFO_EXTENSION);
            $filename = 'company_logo_' . time() . '.' . $ext;
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($_FILES['company_logo']['tmp_name'], $filepath)) {
                // Remover logo antiga
                $oldLogo = $this->companySettings->get('company_logo');
                if ($oldLogo && file_exists($oldLogo)) {
                    unlink($oldLogo);
                }
                $this->companySettings->set('company_logo', $filepath);
            }
        }

        // Remover logo se checkbox marcado
        if (isset($_POST['remove_logo']) && $_POST['remove_logo'] == '1') {
            $oldLogo = $this->companySettings->get('company_logo');
            if ($oldLogo && file_exists($oldLogo)) {
                unlink($oldLogo);
            }
            $this->companySettings->set('company_logo', '');
        }

        require_once 'app/models/Logger.php';
        $logger = new Logger($this->db);
        $logger->log('SETTINGS_UPDATE', 'Configurações da empresa atualizadas');

        header('Location: /sistemaTiago/?page=settings&status=saved');
        exit;
    }

    // ──────── TABELAS DE PREÇO ────────

    /**
     * Página dedicada de Tabelas de Preço (menu principal)
     */
    public function priceTablesIndex() {
        $priceTables = $this->priceTable->readAll();

        require 'app/views/layout/header.php';
        require 'app/views/settings/price_tables_index.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * Criar tabela de preço (POST)
     */
    public function createPriceTable() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $refPage = $_POST['ref_page'] ?? 'settings';
            if ($name) {
                $this->priceTable->create($name, $description);
            }
            if ($refPage === 'price_tables') {
                header('Location: /sistemaTiago/?page=price_tables&status=table_created');
            } else {
                header('Location: /sistemaTiago/?page=settings&tab=prices&status=table_created');
            }
            exit;
        }
    }

    /**
     * Atualizar tabela de preço (POST)
     */
    public function updatePriceTable() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            if ($id && $name) {
                $this->priceTable->update($id, $name, $description);
            }
            header('Location: /sistemaTiago/?page=settings&tab=prices&status=table_updated');
            exit;
        }
    }

    /**
     * Excluir tabela de preço
     */
    public function deletePriceTable() {
        $id = $_GET['id'] ?? null;
        $refPage = $_GET['ref'] ?? 'settings';
        if ($id) {
            $result = $this->priceTable->delete($id);
            $status = $result ? 'table_deleted' : 'table_default_error';
        }
        if ($refPage === 'price_tables') {
            header('Location: /sistemaTiago/?page=price_tables&status=' . ($status ?? 'error'));
        } else {
            header('Location: /sistemaTiago/?page=settings&tab=prices&status=' . ($status ?? 'error'));
        }
        exit;
    }

    /**
     * Editar itens de uma tabela de preço
     */
    public function editPriceTable() {
        $id = $_GET['id'] ?? null;
        $refPage = $_GET['ref'] ?? 'settings';
        if (!$id) {
            header('Location: /sistemaTiago/?page=' . ($refPage === 'price_tables' ? 'price_tables' : 'settings&tab=prices'));
            exit;
        }

        $table = $this->priceTable->readOne($id);
        if (!$table) {
            header('Location: /sistemaTiago/?page=' . ($refPage === 'price_tables' ? 'price_tables' : 'settings&tab=prices'));
            exit;
        }

        $items = $this->priceTable->getItems($id);
        
        $productModel = new Product($this->db);
        $products = $productModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

        // Criar mapa de produtos já na tabela
        $existingProducts = [];
        foreach ($items as $item) {
            $existingProducts[$item['product_id']] = true;
        }

        require 'app/views/layout/header.php';
        require 'app/views/settings/price_table_edit.php';
        require 'app/views/layout/footer.php';
    }

    /**
     * Adicionar/atualizar item na tabela de preço (POST)
     */
    public function savePriceItem() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tableId = $_POST['price_table_id'] ?? null;
            $productId = $_POST['product_id'] ?? null;
            $price = $_POST['price'] ?? 0;
            $refPage = $_POST['ref_page'] ?? 'settings';

            if ($tableId && $productId) {
                $this->priceTable->setItemPrice($tableId, $productId, $price);
            }
            $basePage = ($refPage === 'price_tables') ? 'price_tables' : 'settings';
            header('Location: /sistemaTiago/?page=' . $basePage . '&action=editPriceTable&id=' . $tableId . '&ref=' . $refPage . '&status=item_saved');
            exit;
        }
    }

    /**
     * Remover item da tabela de preço
     */
    public function deletePriceItem() {
        $itemId = $_GET['item_id'] ?? null;
        $tableId = $_GET['table_id'] ?? null;
        $refPage = $_GET['ref'] ?? 'settings';

        if ($itemId) {
            $this->priceTable->removeItem($itemId);
        }
        $basePage = ($refPage === 'price_tables') ? 'price_tables' : 'settings';
        header('Location: /sistemaTiago/?page=' . $basePage . '&action=editPriceTable&id=' . $tableId . '&ref=' . $refPage . '&status=item_deleted');
        exit;
    }

    /**
     * API: Retorna preços para um cliente (AJAX/JSON)
     */
    public function getPricesForCustomer() {
        $customerId = $_GET['customer_id'] ?? null;
        $prices = $this->priceTable->getAllPricesForCustomer($customerId);
        header('Content-Type: application/json');
        echo json_encode($prices);
        exit;
    }
}
