<?php
// Quick test for aula creation functionality
session_start();

// Set up a mock session as an instructor
$_SESSION['user_id'] = 'TEST123';
$_SESSION['user_type'] = 'instrutor';
$_SESSION['user_name'] = 'Test Instructor';

define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/app/controllers/AulaController.php';

// Simulate form submission data
$_POST = [
    'csrf_token' => 'mock_token', // We'll bypass CSRF for this test
    'titulo' => 'Aula de Teste',
    'tipo' => 'musculacao',
    'data_aula' => '2025-07-15',
    'hora_inicio' => '10:00',
    'hora_fim' => '11:00',
    'instrutor' => 'TEST123',
    'capacidade' => '20',
    'local' => 'sala_1',
    'descricao' => 'Esta é uma aula de teste para verificar se a funcionalidade está funcionando.'
];

$_SERVER['REQUEST_METHOD'] = 'POST';

echo "Testing aula creation...\n";

try {
    // Mock the CSRF validation function
    function validateCSRFToken($token) {
        return true; // Mock validation for testing
    }
    
    // Mock redirect function
    function redirect($url) {
        echo "Redirect to: $url\n";
        if (isset($_SESSION['error'])) {
            echo "Error: " . $_SESSION['error'] . "\n";
        }
        if (isset($_SESSION['success'])) {
            echo "Success: " . $_SESSION['success'] . "\n";
        }
    }
    
    $controller = new AulaController();
    $controller->store();
    
    echo "Test completed!\n";
    
} catch (Exception $e) {
    echo "Error during test: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
