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
        $this->host = (file_exists('/.dockerenv') || gethostbyname('db') !== 'db') ? 'db' : 'localhost';
    }

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            error_log("Database connection error: " . $exception->getMessage());
            // Return null instead of echoing error to avoid breaking API responses
            return null;
        }

        return $this->conn;
    }
}
?>
