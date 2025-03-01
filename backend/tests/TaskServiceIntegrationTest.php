<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/test_database.php';
require_once __DIR__ . '/TestDatabaseSetup.php';
require_once __DIR__ . '/../services/TaskService.php';

class TaskServiceIntegrationTest extends TestCase
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

        // Insert test data
        $this->db->exec("INSERT INTO task (title, description) VALUES 
            ('Test Task 1', 'Description 1'),
            ('Test Task 2', 'Description 2'),
            ('Test Task 3', 'Description 3')");
    }

    protected function tearDown(): void
    {
        $this->dbSetup->cleanupTestData();
    }

    public function testGetAllTasksWithOrderByDesc()
    {
        $tasks = $this->taskService->getAllTasks([
            'orderby' => 'id',
            'order' => 'DESC'
        ]);

        $this->assertCount(3, $tasks);
        $this->assertEquals('Test Task 3', $tasks[0]['title']);
        $this->assertEquals('Test Task 1', $tasks[2]['title']);
    }

    public function testGetAllTasksWithLimit()
    {
        $tasks = $this->taskService->getAllTasks([
            'limit' => 2,
            'orderby' => 'id',
            'order' => 'DESC'
        ]);

        $this->assertCount(2, $tasks);
        $this->assertEquals('Test Task 3', $tasks[0]['title']);
        $this->assertEquals('Test Task 2', $tasks[1]['title']);
    }

    public function testCreateAndRetrieveTask()
    {
        $title = 'Integration Test Task';
        $description = 'Created during integration test';

        $result = $this->taskService->createTask($title, $description);
        $this->assertTrue($result);

        $tasks = $this->taskService->getAllTasks([
            'orderby' => 'id',
            'order' => 'DESC'
        ]);

        $latestTask = $tasks[0];
        $this->assertEquals($title, $latestTask['title']);
        $this->assertEquals($description, $latestTask['description']);
        $this->assertEquals(0, $latestTask['completed']);
    }

    public function testDeleteTask()
    {
        // Create a task first
        $this->taskService->createTask('Task to Delete', 'Will be deleted');
        
        $tasks = $this->taskService->getAllTasks(['orderby' => 'id', 'order' => 'DESC']);
        $taskId = $tasks[0]['id'];

        // Delete the task
        $result = $this->taskService->deleteTask($taskId);
        $this->assertTrue($result);

        // Verify task is marked as completed
        $stmt = $this->db->prepare("SELECT completed FROM task WHERE id = ?");
        $stmt->execute([$taskId]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $this->assertEquals(1, $task['completed']);
    }

    public function testGetAllTasksExcludesCompletedTasks()
    {
        // Create a completed task
        $this->db->exec("INSERT INTO task (title, description, completed) VALUES 
            ('Completed Task', 'This task is done', 1)");

        $tasks = $this->taskService->getAllTasks();
        
        foreach ($tasks as $task) {
            $this->assertEquals(0, $task['completed']);
        }
        $this->assertCount(3, $tasks); // Only the original 3 uncompleted tasks
    }
} 