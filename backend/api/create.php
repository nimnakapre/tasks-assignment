<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../services/TaskService.php';
require_once 'db.php';

try {
    
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (empty($data['title'])) {
        http_response_code(400);
        echo json_encode(["error" => "Title is required"]);
        exit();
    }

    $database = Database::getInstance();
    $db = $database->getConnection();
    
    $taskService = new TaskService($db);
    $result = $taskService->createTask($data['title'], $data['description'] ?? '');
    if ($result) {
        http_response_code(201);
        echo json_encode(["data" => ["data" => $result['data']], "message" => "Task created successfully"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?> 