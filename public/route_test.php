<?php
// Direct test of the routing system
require_once '../config/config.php';

echo "<h1>🎯 Direct Route Test</h1>";

// Mock the exact request that's failing
$_SERVER['REQUEST_URI'] = '/plano_treino/create';

// Simulate the routing logic from index.php exactly
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);

// Remove base path if exists and clean multiple slashes
$path = ltrim($path, '/');
$path = preg_replace('/\/+/', '/', $path); // Replace multiple slashes with single slash
$path = trim($path, '/'); // Remove leading/trailing slashes

echo "<p><strong>Processed Path:</strong> '$path'</p>";

// Default route
if (empty($path)) {
    $path = 'auth/login';
}

// Split path into controller and action
$segments = explode('/', $path);

// Handle special routes
if ($segments[0] === 'login' || $segments[0] === 'register') {
    $controller = 'auth';
    $action = $segments[0];
    $params = array_slice($segments, 1);
} else {
    $controller = $segments[0] ?? 'auth';
    $action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');
    $params = array_slice($segments, 2);
}

// Convert controller name to PascalCase for compound names
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));

echo "<p><strong>Controller:</strong> '$controller'</p>";
echo "<p><strong>Action:</strong> '$action'</p>";
echo "<p><strong>Controller Name:</strong> '$controllerName'</p>";

// Controller file path
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';

echo "<p><strong>Controller File:</strong> '$controllerFile'</p>";
echo "<p><strong>File Exists:</strong> " . (file_exists($controllerFile) ? 'YES' : 'NO') . "</p>";

if (file_exists($controllerFile)) {
    echo "<p>✅ <strong>SUCCESS:</strong> Controller file found!</p>";
    
    require_once $controllerFile;
    
    $controllerClass = $controllerName . 'Controller';
    echo "<p><strong>Controller Class:</strong> '$controllerClass'</p>";
    echo "<p><strong>Class Exists:</strong> " . (class_exists($controllerClass) ? 'YES' : 'NO') . "</p>";
    
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        echo "<p><strong>Instance Created:</strong> YES</p>";
        
        if (method_exists($controllerInstance, $action)) {
            echo "<p>✅ <strong>SUCCESS:</strong> Method '$action' exists!</p>";
            echo "<p><strong>The routing should work perfectly!</strong></p>";
            
            echo "<h2>🔍 The Real Issue</h2>";
            echo "<p>The 'Controller not found' error is likely because:</p>";
            echo "<ul>";
            echo "<li>You're not logged in (session is empty)</li>";
            echo "<li>The controller redirects to login when not authenticated</li>";
            echo "<li>The redirect might be causing the 'Controller not found' message</li>";
            echo "</ul>";
            
        } else {
            echo "<p>❌ <strong>ERROR:</strong> Method '$action' not found</p>";
            $methods = get_class_methods($controllerClass);
            echo "<p><strong>Available methods:</strong> " . implode(', ', $methods) . "</p>";
        }
    } else {
        echo "<p>❌ <strong>ERROR:</strong> Class '$controllerClass' not found</p>";
    }
} else {
    echo "<p>❌ <strong>ERROR:</strong> Controller file not found</p>";
    echo "<h2>📁 Available Controllers</h2>";
    $dir = BASE_PATH . '/app/controllers/';
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && substr($file, -4) == '.php') {
                echo "<p>- $file</p>";
            }
        }
    }
}

echo "<hr>";
echo "<h2>💡 Solution</h2>";
echo "<p>To fix the issue:</p>";
echo "<ol>";
echo "<li><a href='/auth/login'>Login first</a> with admin@academia.com / password</li>";
echo "<li>Then try <a href='/plano_treino/create'>accessing the route</a></li>";
echo "</ol>";
?>
