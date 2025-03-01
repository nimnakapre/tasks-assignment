<?php
use PHPUnit\Framework\TestCase;
require_once __DIR__ . '/../config/test_database.php';
require_once __DIR__ . '/TestDatabaseSetup.php';

class TaskServiceTest extends TestCase
{
    private $db;
    private $taskService;
    private $dbSetup;

    protected function setUp(): void
    {
        $this->dbSetup = new TestDatabaseSetup();
        $this->dbSetup->setupTestDatabase();
        
        $this->db = TestDatabase::getInstance()->getConnection();
        $this->taskService = new TaskService($this->db);
    }

    protected function tearDown(): void
    {
        $this->dbSetup->cleanupTestData();
    }

    public function testGetAllTasksWithoutLimit()
    {
        // Insert test data
        $this->db->exec("
            INSERT INTO task (title, description) VALUES 
            ('Task 1', 'Description 1'),
            ('Task 2', 'Description 2')
        ");

        $result = $this->taskService->getAllTasks();
        
        $this->assertCount(2, $result);
        $this->assertEquals('Task 1', $result[0]['title']);
        $this->assertEquals('Task 2', $result[1]['title']);
    }

    public function testGetAllTasksWithLimit()
    {
        // Insert test data
        $this->db->exec("
            INSERT INTO task (title, description) VALUES 
            ('Task 1', 'Description 1'),
            ('Task 2', 'Description 2')
        ");

        $result = $this->taskService->getAllTasks(['limit' => 1]);
        
        $this->assertCount(1, $result);
        $this->assertEquals('Task 1', $result[0]['title']);
    }

    public function testCreateTask()
    {
        $title = "New Task";
        $description = "New Description";

        $result = $this->taskService->createTask($title, $description);
        $this->assertTrue($result);

        // Verify the task was created
        $stmt = $this->db->query("SELECT * FROM task WHERE title = 'New Task'");
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals($title, $task['title']);
        $this->assertEquals($description, $task['description']);
    }

    public function testDeleteTask()
    {
        // Insert a test task
        $this->db->exec("INSERT INTO task (title, description) VALUES ('Test Task', 'Test Description')");
        $taskId = $this->db->lastInsertId();

        $result = $this->taskService->deleteTask($taskId);
        $this->assertTrue($result);

        // Verify the task was marked as completed
        $stmt = $this->db->prepare("SELECT completed FROM task WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals(1, $task['completed']);
    }
} 