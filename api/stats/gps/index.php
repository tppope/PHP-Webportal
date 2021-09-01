<?php
header('Content-Type: application/json');
require_once(__DIR__ . "/../../../controllers/StatsController.php");
$controller = new StatsController();
try {
    echo json_encode($controller->getGPS());
} catch (Exception $e) {
    echo json_encode(array("error"=>true));
}

