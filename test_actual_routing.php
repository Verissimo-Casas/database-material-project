<?php
// FILE: test_actual_routing.php
// This file mimics exactly what index.php does

require_once 'config/config.php';

echo "<h2>Actual Routing Test</h2>";

// Get the actual request URI that nginx sends
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";
echo "<p><strong>PATH_INFO:</strong> " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "</p>";
echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'EMPTY') . "</p>";

// Exact copy of index.php routing logic
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);

echo "<p><strong>After parse_url and index.php removal:</strong> '$path'</p>";

// Remove base path if exists and clean multiple slashes
$path = ltrim($path, '/');
$path = preg_replace('/\/+/', '/', $path); // Replace multiple slashes with single slash
$path = trim($path, '/'); // Remove leading/trailing slashes

echo "<p><strong>After cleaning:</strong> '$path'</p>";

// Default route
if (empty($path)) {
    $path = 'auth/login';
    echo "<p><strong>Empty path, defaulting to:</strong> '$path'</p>";
}

// Split path into controller and action
$segments = explode('/', $path);
echo "<p><strong>Segments:</strong> [" . implode(', ', array_map(function($s) { return "'$s'"; }, $segments)) . "]</p>";

// Handle special routes
if ($segments[0] === 'login' || $segments[0] === 'register') {
    $controller = 'auth';
    $action = $segments[0];
    $params = array_slice($segments, 1);
    echo "<p><strong>Special route detected</strong></p>";
} else {
    $controller = $segments[0] ?? 'auth';
    $action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');
    $params = array_slice($segments, 2);
    echo "<p><strong>Normal route</strong></p>";
}

echo "<p><strong>Final Controller:</strong> '$controller'</p>";
echo "<p><strong>Final Action:</strong> '$action'</p>";
echo "<p><strong>Final Params:</strong> [" . implode(', ', $params) . "]</p>";

// Convert controller name to PascalCase for compound names
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<p><strong>Controller Name:</strong> '$controllerName'</p>";

// Controller file path
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<p><strong>Controller File:</strong> $controllerFile</p>";

if (file_exists($controllerFile)) {
    echo "<p><strong>✅ Controller file EXISTS</strong></p>";
    
    require_once $controllerFile;
    
    $controllerClass = $controllerName . 'Controller';
    echo "<p><strong>Controller Class:</strong> $controllerClass</p>";
    
    if (class_exists($controllerClass)) {
        echo "<p><strong>✅ Controller class EXISTS</strong></p>";
        
        $controllerInstance = new $controllerClass();
        
        if (method_exists($controllerInstance, $action)) {
            echo "<p><strong>✅ Method '$action' EXISTS</strong></p>";
            echo "<p><strong>🎯 ROUTING SHOULD WORK!</strong></p>";
        } else {
            echo "<p><strong>❌ Method '$action' NOT FOUND</strong></p>";
            $methods = get_class_methods($controllerClass);
            echo "<p><strong>Available methods:</strong> " . implode(', ', $methods) . "</p>";
        }
    } else {
        echo "<p><strong>❌ Controller class NOT FOUND</strong></p>";
    }
} else {
    echo "<p><strong>❌ Controller file NOT FOUND</strong></p>";
    echo "<p>Looking for: $controllerFile</p>";
}
?>
