<?php
// Simple test to debug routing
echo "<h1>Debug Routing Test</h1>";

// Get the request info
$request_uri = $_SERVER['REQUEST_URI'] ?? 'not set';
echo "<p><strong>REQUEST_URI:</strong> " . htmlspecialchars($request_uri) . "</p>";

// Test the parsing logic from index.php
$path = parse_url($request_uri, PHP_URL_PATH);
echo "<p><strong>Parsed path:</strong> " . htmlspecialchars($path) . "</p>";

$path = str_replace('/index.php', '', $path);
echo "<p><strong>After removing index.php:</strong> " . htmlspecialchars($path) . "</p>";

$path = ltrim($path, '/');
echo "<p><strong>After ltrim:</strong> " . htmlspecialchars($path) . "</p>";

$path = preg_replace('/\/+/', '/', $path);
echo "<p><strong>After regex:</strong> " . htmlspecialchars($path) . "</p>";

$path = trim($path, '/');
echo "<p><strong>After trim:</strong> " . htmlspecialchars($path) . "</p>";

// Test segments
$segments = explode('/', $path);
echo "<p><strong>Segments:</strong> ";
print_r($segments);
echo "</p>";

$controller = $segments[0] ?? 'auth';
$action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');

echo "<p><strong>Controller:</strong> " . htmlspecialchars($controller) . "</p>";
echo "<p><strong>Action:</strong> " . htmlspecialchars($action) . "</p>";

// Test controller name conversion
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<p><strong>Controller Name:</strong> " . htmlspecialchars($controllerName) . "</p>";

// Test file path
require_once '../config/config.php';
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<p><strong>Controller File Path:</strong> " . htmlspecialchars($controllerFile) . "</p>";
echo "<p><strong>File exists:</strong> " . (file_exists($controllerFile) ? 'YES' : 'NO') . "</p>";

if (file_exists($controllerFile)) {
    echo "<p><strong>File readable:</strong> " . (is_readable($controllerFile) ? 'YES' : 'NO') . "</p>";
    
    // Test class loading
    try {
        require_once $controllerFile;
        $controllerClass = $controllerName . 'Controller';
        echo "<p><strong>Class name:</strong> " . htmlspecialchars($controllerClass) . "</p>";
        echo "<p><strong>Class exists:</strong> " . (class_exists($controllerClass) ? 'YES' : 'NO') . "</p>";
        
        if (class_exists($controllerClass)) {
            $methods = get_class_methods($controllerClass);
            echo "<p><strong>Available methods:</strong> " . implode(', ', $methods) . "</p>";
            echo "<p><strong>Method '$action' exists:</strong> " . (in_array($action, $methods) ? 'YES' : 'NO') . "</p>";
        }
    } catch (Exception $e) {
        echo "<p><strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
