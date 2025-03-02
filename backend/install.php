<?php

require 'vendor/autoload.php';
require_once 'config.php';

use Symfony\Component\Yaml\Yaml;


echo "Creating database $database_name...\n";

$servername = readline("Enter the host name: ");;
$username = readline("Enter the root username: ");;
$password = readline("Enter the root user password: ");;
$dbname = readline("Enter the database name: ");;

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

if ($conn->query($sql) === TRUE) {  
    echo "Database created successfully\n";

    $config = [
        'database' => [
            'host' => $servername,
            'username' => $username,
            'password' => $password,
            'dbname' => $dbname
        ]
    ];

    file_put_contents('databases.yml', Yaml::dump($config));

    echo "Database configuration updated in databases.yml\n";

    $sql = "USE $dbname";
    if ($conn->query($sql) === TRUE) {
        echo "Database selected successfully\n";
    } else {
        echo "Error selecting database: " . $conn->error;
    }
    echo "Installing tables...\n";
    $sql = file_get_contents('database.sql');
    if ($conn->multi_query($sql)) {
        do {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        } while ($conn->next_result());
    } else {
        echo "Error installing tables: " . $conn->error;
    }
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->close();
?>