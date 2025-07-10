<?php
// Test the actual controller behavior
session_start();
require_once '../config/config.php';

echo "<h1>🧪 Controller Behavior Test</h1>";

// Clear any existing session to simulate not being logged in
session_destroy();
session_start();

echo "<p><strong>Session Status:</strong> " . (isset($_SESSION['user_id']) ? 'Logged In' : 'Not Logged In') . "</p>";

// Test the PlanoTreinoController create method
echo "<h2>Testing PlanoTreinoController::create()</h2>";

try {
    // Load the controller
    require_once BASE_PATH . '/app/controllers/PlanoTreinoController.php';
    
    $controller = new PlanoTreinoController();
    
    echo "<p>✅ Controller instance created successfully</p>";
    
    // Capture any output and redirects
    ob_start();
    
    echo "<p>🔄 Calling create() method...</p>";
    
    // This should trigger a redirect to auth/login
    $controller->create();
    
    $output = ob_get_contents();
    ob_end_clean();
    
    echo "<p>📄 <strong>Output captured:</strong></p>";
    echo "<pre>" . htmlspecialchars($output) . "</pre>";
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
} catch (Error $e) {
    echo "<p>❌ <strong>Fatal Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " <strong>Line:</strong> " . $e->getLine() . "</p>";
}

echo "<hr>";
echo "<h2>💡 Expected Behavior</h2>";
echo "<p>When not logged in, the controller should:</p>";
echo "<ol>";
echo "<li>Check if user_id is set in session</li>";
echo "<li>Call redirect('auth/login')</li>";
echo "<li>Send Location header and exit</li>";
echo "</ol>";

echo "<h2>🔧 Troubleshooting</h2>";
echo "<p>If you're still seeing 'Controller not found':</p>";
echo "<ol>";
echo "<li>Check if there are PHP errors in the error log</li>";
echo "<li>Verify the .htaccess or nginx configuration</li>";
echo "<li>Make sure the redirect function is working correctly</li>";
echo "</ol>";
?>
