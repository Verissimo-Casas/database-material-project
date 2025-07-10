<?php
// Simple test to check database connectivity
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== Database Connection Test ===\n";

// Test 1: Include config
try {
    require_once __DIR__ . '/config/config.php';
    echo "✓ Config loaded successfully\n";
} catch (Exception $e) {
    echo "✗ Error loading config: " . $e->getMessage() . "\n";
    exit;
}

// Test 2: Test database connection
try {
    $database = new Database();
    $db = $database->getConnection();
    
    if ($db) {
        echo "✓ Database connection successful\n";
        
        // Test 3: Try a simple query
        $stmt = $db->query("SELECT COUNT(*) as count FROM aluno");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "✓ Query executed successfully. Aluno count: " . $result['count'] . "\n";
        
    } else {
        echo "✗ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "=== End Test ===\n";
?>
