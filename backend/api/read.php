<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../services/TaskService.php';
require_once 'db.php';

try {
    $database = Database::getInstance();
    $db = $database->getConnection();
    
    $taskService = new TaskService($db);
    $excludeIds = isset($_GET['exclude']) ? explode(',', $_GET['exclude']) : [];
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : -1;
    if ($id !== -1) {
        $tasks = $taskService->getNextTask([
            'id' => $id
        ]);
    } else {
        $tasks = $taskService->getAllTasks([
            'limit' => $limit,
            'orderby' => 'id',
            'order' => 'DESC',
            'exclude' => $excludeIds
        ]);
    }
    echo json_encode($tasks);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?> 