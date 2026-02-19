<?php
require_once 'app/models/ProductionSector.php';

class SectorController {
    
    private $sectorModel;
    private $logger;

    public function __construct() {
        $db = (new Database())->getConnection();
        $this->sectorModel = new ProductionSector($db);
        require_once 'app/models/Logger.php';
        $this->logger = new Logger($db);
    }

    public function index() {
        $sectors = $this->sectorModel->readAll();

        // Filtrar setores por permissão (se não admin)
        $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
        $allowedSectorIds = [];
        if (!$isAdmin && isset($_SESSION['user_id'])) {
            $dbPerm = (new Database())->getConnection();
            $userModel = new User($dbPerm);
            $allowedSectorIds = $userModel->getAllowedSectorIds($_SESSION['user_id']);
            if (!empty($allowedSectorIds)) {
                $sectors = array_filter($sectors, function($s) use ($allowedSectorIds) {
                    return in_array($s['id'], $allowedSectorIds);
                });
                $sectors = array_values($sectors);
            }
        }

        $editSector = null;
        if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
            $editSector = $this->sectorModel->readOne($_GET['id']);
        }
        require 'app/views/layout/header.php';
        require 'app/views/sectors/index.php';
        require 'app/views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['name'])) {
            $this->sectorModel->create($_POST);
            $this->logger->log('CREATE_SECTOR', 'Created sector: ' . $_POST['name']);
        }
        header('Location: /sistemaTiago/?page=sectors&status=success');
        exit;
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
            $this->sectorModel->update($_POST);
            $this->logger->log('UPDATE_SECTOR', 'Updated sector ID: ' . $_POST['id']);
        }
        header('Location: /sistemaTiago/?page=sectors&status=success');
        exit;
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $this->sectorModel->delete($_GET['id']);
            $this->logger->log('DELETE_SECTOR', 'Deleted sector ID: ' . $_GET['id']);
        }
        header('Location: /sistemaTiago/?page=sectors&status=success');
        exit;
    }
}
