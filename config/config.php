<?php
// FILE: config/config.php

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Define constants
define('BASE_PATH', realpath(dirname(dirname(__FILE__))));
define('BASE_URL', 'http://localhost:8080/');

// Include database configuration
require_once BASE_PATH . '/config/database.php';

// Security functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Check user type
function getUserType() {
    return $_SESSION['user_type'] ?? null;
}

// Redirect function
function redirect($path) {
    header("Location: " . BASE_URL . $path);
    exit();
}

// Check if matricula is active
function isMatriculaActive($matricula_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT M_Status FROM matricula WHERE ID_Matricula = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $matricula_id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result && $result['M_Status'] == 1;
}

// Check for overdue payments
function hasOverduePayments($matricula_id) {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT COUNT(*) as overdue FROM boleto 
              WHERE ID_Matricula = :id 
              AND Dt_Vencimento < CURDATE() 
              AND Dt_Pagamento IS NULL";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $matricula_id);
    $stmt->execute();
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['overdue'] > 0;
}

// Render view with layout
function render($view, $data = []) {
    // Extract variables for the view
    extract($data);
    
    // Start output buffering to capture view content
    ob_start();
    include BASE_PATH . '/app/views/' . $view . '.php';
    $content = ob_get_clean();
    
    // Include layout with content
    include BASE_PATH . '/app/views/layout.php';
}

// Validate CSRF token
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
