<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../services/TaskService.php';
require_once 'db.php';

try {
    $data = json_decode(file_get_contents("php://input"));
    $id = $data->id;

    $database = Database::getInstance();
    $db = $database->getConnection();
    
    $taskService = new TaskService($db);
    $taskService->deleteTask($id);
    
    echo json_encode(["message" => "Task completed"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

?> 