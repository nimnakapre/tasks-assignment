<?php
class TestDatabaseSetup {
    private $pdo;

    public function __construct() {
        $this->pdo = TestDatabase::getInstance()->getConnection();
    }

    public function setupTestDatabase() {
        $this->pdo->exec("
            CREATE TABLE IF NOT EXISTS task (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                completed BOOLEAN DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        
        $this->cleanupTestData();
    }

    public function cleanupTestData() {
        $this->pdo->exec("TRUNCATE TABLE task");
    }
} 