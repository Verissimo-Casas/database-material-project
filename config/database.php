<?php
// FILE: config/database.php

class Database {
    private $host;
    private $db_name = 'academiabd';
    private $username = 'academia_user';
    private $password = 'academia_pass';
    private $conn;

    public function __construct() {
        // Detect if running in Docker container or host
        if (file_exists('/.dockerenv')) {
            $this->host = 'db';
        } else {
            $this->host = 'localhost';
        }
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name;
            error_log("Attempting to connect to database with DSN: " . $dsn);
            
            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                array(PDO::ATTR_TIMEOUT => 30)
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
            
            error_log("Database connection successful");
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            error_log("Host: " . $this->host . ", Database: " . $this->db_name . ", User: " . $this->username);
            // Return null instead of echoing error to avoid breaking API responses
            return null;
        }

        return $this->conn;
    }
}

// Helper function to get database configuration for backup
function getDatabaseConfig() {
    return [
        'host' => (file_exists('/.dockerenv') || gethostbyname('db') !== 'db') ? 'db' : 'localhost',
        'database' => 'academiabd',
        'user' => 'academia_user',
        'password' => 'academia_pass'
    ];
}

// Helper function to get database connection
function getConnection() {
    $database = new Database();
    return $database->getConnection();
}
?>
