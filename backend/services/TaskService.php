<?php
class TaskService {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getNextTask($params = []) {
        $sql = "SELECT * FROM task WHERE completed = 0 AND id < $params[id] ORDER BY id DESC LIMIT 1";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllTasks($params = []) {
        $sql = "SELECT * FROM task WHERE completed = 0";
        
        if(count($params) > 0) {
            if (isset($params['orderby'])) {
                $sql .= " ORDER BY " . $params['orderby'];
                if (isset($params['order']) && strtoupper($params['order']) === 'DESC') {
                    $sql .= " DESC";
                }
            }
            
            if (isset($params['limit'])) {
                $sql .= " LIMIT ?";
                $query = $this->db->prepare($sql);
                $query->bindParam(1, $params['limit'], PDO::PARAM_INT);
                $query->execute();
            } else {
                $query = $this->db->query($sql);
            }
        } else {
            $query = $this->db->query($sql);
        }
        
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }   

    public function createTask($title, $description) {
        $query = $this->db->prepare("INSERT INTO task (title, description) VALUES (?, ?)");
        $result = $query->execute([$title, $description]);
        $addedTask = $this->getTaskById($this->db->lastInsertId());
        if (!$result) {
            throw new Exception("Failed to create task");
        }
        return array(
            'success' => true,
            'message' => 'Task created successfully',
            'data' => $addedTask
        );
    }

    public function deleteTask($id) {
        $query = $this->db->prepare("UPDATE task SET completed = 1 WHERE id = ?");
        $result = $query->execute([$id]);
        
        if (!$result) {
            throw new Exception("Failed to delete task");
        }
        return true;
    }

    public function getTaskById($id) {
        $query = $this->db->prepare("SELECT * FROM task WHERE id = ?");
        $query->execute([$id]);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }
} 