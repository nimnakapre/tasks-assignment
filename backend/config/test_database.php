<?php
class TestDatabase {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            $this->connection = new PDO(
                "mysql:host=test-db;dbname=tasks_test_db",
                "root",
                "root",
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch(PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            throw $e;
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new TestDatabase();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
} 