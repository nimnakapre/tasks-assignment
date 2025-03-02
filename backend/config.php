<?php

use Symfony\Component\Yaml\Yaml;

require 'vendor/autoload.php';

function getDatabaseConfig() {
    $configFile = __DIR__ . '/databases.yml';
    if (!file_exists($configFile)) {
        die("Error: Configuration file not found.");
    }

    $config = Yaml::parseFile($configFile);

    if (!isset($config['database'])) {
        die("Error: Invalid configuration format.");
    }

    return $config['database'];
}

?>
