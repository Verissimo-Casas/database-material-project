<?php
// FILE: debug_routing_step_by_step.php
require_once 'config/config.php';

echo "<h2>Step-by-Step Routing Debug</h2>";

// Simulate the exact request
$request = '/plano_treino/create';
echo "<p><strong>1. Original Request:</strong> $request</p>";

// Step 1: Parse URL
$path = parse_url($request, PHP_URL_PATH);
echo "<p><strong>2. After parse_url:</strong> $path</p>";

// Step 2: Remove index.php
$path = str_replace('/index.php', '', $path);
echo "<p><strong>3. After removing index.php:</strong> $path</p>";

// Step 3: Remove leading slash and clean
$path = ltrim($path, '/');
echo "<p><strong>4. After ltrim:</strong> '$path'</p>";

$path = preg_replace('/\/+/', '/', $path);
echo "<p><strong>5. After regex cleanup:</strong> '$path'</p>";

$path = trim($path, '/');
echo "<p><strong>6. After final trim:</strong> '$path'</p>";

// Step 4: Split into segments
$segments = explode('/', $path);
echo "<p><strong>7. Segments:</strong> [" . implode(', ', array_map(function($s) { return "'$s'"; }, $segments)) . "]</p>";

// Step 5: Determine controller and action
$controller = $segments[0] ?? 'auth';
$action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');
$params = array_slice($segments, 2);

echo "<p><strong>8. Controller:</strong> '$controller'</p>";
echo "<p><strong>9. Action:</strong> '$action'</p>";
echo "<p><strong>10. Params:</strong> [" . implode(', ', array_map(function($s) { return "'$s'"; }, $params)) . "]</p>";

// Step 6: Convert to PascalCase
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<p><strong>11. Controller Name (PascalCase):</strong> '$controllerName'</p>";

// Step 7: Build file path
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<p><strong>12. Controller File Path:</strong> $controllerFile</p>";

// Step 8: Check if file exists
$fileExists = file_exists($controllerFile);
echo "<p><strong>13. File Exists:</strong> " . ($fileExists ? 'YES' : 'NO') . "</p>";

if ($fileExists) {
    echo "<p><strong>14. File contents preview:</strong></p>";
    $firstLines = array_slice(file($controllerFile), 0, 10);
    echo "<pre>" . htmlspecialchars(implode('', $firstLines)) . "</pre>";
    
    // Try to include and test
    try {
        require_once $controllerFile;
        $controllerClass = $controllerName . 'Controller';
        echo "<p><strong>15. Controller Class:</strong> '$controllerClass'</p>";
        echo "<p><strong>16. Class Exists:</strong> " . (class_exists($controllerClass) ? 'YES' : 'NO') . "</p>";
        
        if (class_exists($controllerClass)) {
            $instance = new $controllerClass();
            echo "<p><strong>17. Instance Created:</strong> YES</p>";
            echo "<p><strong>18. Method '$action' Exists:</strong> " . (method_exists($instance, $action) ? 'YES' : 'NO') . "</p>";
            
            $methods = get_class_methods($controllerClass);
            echo "<p><strong>19. Available Methods:</strong> " . implode(', ', $methods) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    // List all controller files to see what's available
    $controllersDir = BASE_PATH . '/app/controllers/';
    echo "<p><strong>14. Available controller files:</strong></p>";
    if (is_dir($controllersDir)) {
        $files = scandir($controllersDir);
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && substr($file, -4) === '.php') {
                echo "<p>- $file</p>";
            }
        }
    }
}

echo "<h3>Current Directory Structure Check</h3>";
echo "<p><strong>BASE_PATH:</strong> " . BASE_PATH . "</p>";
echo "<p><strong>Controllers Directory:</strong> " . BASE_PATH . '/app/controllers/' . "</p>";
echo "<p><strong>Controllers Dir Exists:</strong> " . (is_dir(BASE_PATH . '/app/controllers/') ? 'YES' : 'NO') . "</p>";
?>
