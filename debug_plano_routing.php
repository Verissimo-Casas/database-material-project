<?php
// FILE: debug_plano_routing.php
session_start();

require_once 'config/config.php';

echo "<h2>Debug Plano Treino Routing</h2>";

// Test the routing logic
$request = '/plano_treino/create';
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);
$path = ltrim($path, '/');
$path = preg_replace('/\/+/', '/', $path);
$path = trim($path, '/');

echo "<p><strong>Original path:</strong> $request</p>";
echo "<p><strong>Processed path:</strong> $path</p>";

$segments = explode('/', $path);
echo "<p><strong>Segments:</strong> " . implode(', ', $segments) . "</p>";

$controller = $segments[0] ?? 'auth';
$action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');
$params = array_slice($segments, 2);

echo "<p><strong>Controller:</strong> $controller</p>";
echo "<p><strong>Action:</strong> $action</p>";
echo "<p><strong>Params:</strong> " . implode(', ', $params) . "</p>";

$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<p><strong>Controller Name (PascalCase):</strong> $controllerName</p>";

$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<p><strong>Controller File Path:</strong> $controllerFile</p>";
echo "<p><strong>Controller File Exists:</strong> " . (file_exists($controllerFile) ? 'YES' : 'NO') . "</p>";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = $controllerName . 'Controller';
    echo "<p><strong>Controller Class:</strong> $controllerClass</p>";
    echo "<p><strong>Class Exists:</strong> " . (class_exists($controllerClass) ? 'YES' : 'NO') . "</p>";
    
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        echo "<p><strong>Method '$action' Exists:</strong> " . (method_exists($controllerInstance, $action) ? 'YES' : 'NO') . "</p>";
        
        $methods = get_class_methods($controllerClass);
        echo "<p><strong>Available Methods:</strong> " . implode(', ', $methods) . "</p>";
    }
}

echo "<h3>Session Info</h3>";
echo "<p><strong>Session Active:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'YES' : 'NO') . "</p>";
echo "<p><strong>User ID:</strong> " . ($_SESSION['user_id'] ?? 'NOT SET') . "</p>";
echo "<p><strong>User Type:</strong> " . ($_SESSION['user_type'] ?? 'NOT SET') . "</p>";

echo "<h3>Config</h3>";
echo "<p><strong>BASE_PATH:</strong> " . (defined('BASE_PATH') ? BASE_PATH : 'NOT DEFINED') . "</p>";
echo "<p><strong>BASE_URL:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "</p>";
?>
