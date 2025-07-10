<?php
// Simple test for routing debugging
require_once '../config/config.php';

echo "=== Final Routing Test ===\n";

// Test the controller path construction
$controller = 'plano_treino';
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';

echo "Controller: $controller\n";
echo "Controller Name: $controllerName\n";
echo "Controller File: $controllerFile\n";
echo "File Exists: " . (file_exists($controllerFile) ? 'YES' : 'NO') . "\n";

if (file_exists($controllerFile)) {
    echo "File size: " . filesize($controllerFile) . " bytes\n";
    
    // Try to require the file
    try {
        require_once $controllerFile;
        echo "File required successfully\n";
        
        $controllerClass = $controllerName . 'Controller';
        echo "Controller Class: $controllerClass\n";
        echo "Class exists: " . (class_exists($controllerClass) ? 'YES' : 'NO') . "\n";
        
        if (class_exists($controllerClass)) {
            $instance = new $controllerClass();
            echo "Instance created successfully\n";
            echo "Has create method: " . (method_exists($instance, 'create') ? 'YES' : 'NO') . "\n";
        }
    } catch (Exception $e) {
        echo "ERROR requiring file: " . $e->getMessage() . "\n";
    }
} else {
    echo "ERROR: Controller file does not exist!\n";
    echo "Directory contents:\n";
    $dir = BASE_PATH . '/app/controllers/';
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "  - $file\n";
            }
        }
    }
}

echo "\n=== Test Complete ===\n";
?>
