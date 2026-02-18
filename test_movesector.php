<?php
session_start();
$_SESSION['user_id'] = 1;
$_GET['page'] = 'pipeline';
$_GET['action'] = 'moveSector';
$_POST['order_id'] = '1';
$_POST['order_item_id'] = '1';
$_POST['sector_id'] = '1';
$_POST['move_action'] = 'start';
$_SERVER['REQUEST_METHOD'] = 'POST';

require_once 'app/config/database.php';
require_once 'app/controllers/PipelineController.php';

ob_start();
$c = new PipelineController();
$c->moveSector();
$output = ob_get_clean();
echo "OUTPUT: " . $output . "\n";
