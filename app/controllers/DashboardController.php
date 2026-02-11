<?php
class DashboardController {
    public function index() {
        require 'app/views/layout/header.php';
        require 'app/views/dashboard/index.php';
        require 'app/views/layout/footer.php';
    }
}
