<?php

require_once '../../config.php';

class Database {
    private static $instance = null;
    private $conn;
    
    private $host;
    private $username; 
    private $password;      
    private $database; 

    private function __construct() {
        try {
            $config = getDatabaseConfig();

            $this->host = $config['host'];
            $this->username = $config['username'];
            $this->password = $config['password'];
            $this->database = $config['dbname'];

            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->database,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }


    public function getConnection() {
        return $this->conn;
    }

    public function __wakeup() { 
        
    }
}
?> 