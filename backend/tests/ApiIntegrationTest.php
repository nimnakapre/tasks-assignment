<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/test_database.php';
require_once __DIR__ . '/TestDatabaseSetup.php';

class ApiIntegrationTest extends TestCase
{
    private $dbSetup;
    private $baseUrl = 'http://app/api';

    protected function setUp(): void
    {
        $this->dbSetup = new TestDatabaseSetup();
        $this->dbSetup->setupTestDatabase();
        
        // Insert test data
        $db = TestDatabase::getInstance()->getConnection();
        $db->exec("INSERT INTO task (title, description) VALUES 
            ('Test Task 1', 'Description 1'),
            ('Test Task 2', 'Description 2'),
            ('Test Task 3', 'Description 3')");
    }

    protected function tearDown(): void
    {
        $this->dbSetup->cleanupTestData();
    }

    public function testGetTasks()
    {
        $response = $this->makeRequest('GET', '/read.php');
        $this->assertEquals(200, $response['status']);
        
        $tasks = json_decode($response['body'], true);
        $this->assertIsArray($tasks);
        $this->assertCount(3, $tasks);
        $this->assertEquals('Test Task 3', $tasks[0]['title']);
    }

    public function testCreateTask()
    {
        $data = [
            'title' => 'API Test Task',
            'description' => 'Created via API test'
        ];

        $response = $this->makeRequest('POST', '/create.php', $data);
        $this->assertEquals(201, $response['status']);
        
        // Verify task was created
        $response = $this->makeRequest('GET', '/read.php');
        $tasks = json_decode($response['body'], true);
        $latestTask = $tasks[0];
        
        $this->assertEquals($data['title'], $latestTask['title']);
        $this->assertEquals($data['description'], $latestTask['description']);
    }

    public function testDeleteTask()
    {
        // Get first task ID
        $response = $this->makeRequest('GET', '/read.php');
        $tasks = json_decode($response['body'], true);
        $taskId = $tasks[0]['id'];

        // Delete task
        $response = $this->makeRequest('DELETE', '/delete.php', ['id' => $taskId]);
        $this->assertEquals(200, $response['status']);

        // Verify task is not in active tasks
        $response = $this->makeRequest('GET', '/read.php');
        $tasks = json_decode($response['body'], true);
        
        foreach ($tasks as $task) {
            $this->assertNotEquals($taskId, $task['id']);
        }
    }

    public function testCreateTaskWithInvalidData()
    {
        $data = [
            'title' => '', // Empty title should fail
            'description' => 'Test description'
        ];

        $response = $this->makeRequest('POST', '/create.php', $data);
        $this->assertEquals(400, $response['status']);
        
        $error = json_decode($response['body'], true);
        $this->assertArrayHasKey('error', $error);
    }

    private function makeRequest($method, $endpoint, $data = null)
    {
        $ch = curl_init($this->baseUrl . $endpoint);
        
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ];

        if ($data && in_array($method, ['POST', 'PUT', 'DELETE'])) {
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $httpCode,
            'body' => $response
        ];
    }
} 