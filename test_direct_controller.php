<?php
// FILE: test_direct_controller.php
require_once 'config/config.php';

// Simulate direct controller loading test
echo "<h2>Direct Controller Test</h2>";

echo "<p><strong>BASE_PATH:</strong> " . BASE_PATH . "</p>";

$controllerFile = BASE_PATH . '/app/controllers/PlanoTreinoController.php';
echo "<p><strong>Controller File:</strong> $controllerFile</p>";
echo "<p><strong>File Exists:</strong> " . (file_exists($controllerFile) ? 'YES' : 'NO') . "</p>";

if (file_exists($controllerFile)) {
    echo "<p>Including controller file...</p>";
    try {
        require_once $controllerFile;
        echo "<p><strong>Controller included successfully</strong></p>";
        
        if (class_exists('PlanoTreinoController')) {
            echo "<p><strong>PlanoTreinoController class exists</strong></p>";
            
            $controller = new PlanoTreinoController();
            echo "<p><strong>Controller instantiated successfully</strong></p>";
            
            $methods = get_class_methods($controller);
            echo "<p><strong>Available methods:</strong> " . implode(', ', $methods) . "</p>";
            
            if (method_exists($controller, 'create')) {
                echo "<p><strong>create method exists!</strong></p>";
            } else {
                echo "<p><strong>ERROR: create method does NOT exist!</strong></p>";
            }
        } else {
            echo "<p><strong>ERROR: PlanoTreinoController class does NOT exist</strong></p>";
        }
    } catch (Exception $e) {
        echo "<p><strong>ERROR including controller:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p><strong>ERROR: Controller file does not exist!</strong></p>";
}

// Test the exact routing logic from index.php
echo "<h3>Routing Test</h3>";
$path = 'plano_treino/create';
$segments = explode('/', $path);
$controller = $segments[0] ?? 'auth';
$action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');

echo "<p>Controller: $controller</p>";
echo "<p>Action: $action</p>";

$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<p>Controller Name: $controllerName</p>";

$expectedFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<p>Expected File: $expectedFile</p>";
echo "<p>Expected File Exists: " . (file_exists($expectedFile) ? 'YES' : 'NO') . "</p>";
?>
