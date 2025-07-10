<?php
// Comprehensive routing debug
session_start();
require_once '../config/config.php';

echo "<h1>🔍 Complete Routing Debug</h1>";

// 1. Check current request
echo "<h2>📝 Request Information</h2>";
echo "<p><strong>REQUEST_URI:</strong> " . htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'not set') . "</p>";
echo "<p><strong>PATH_INFO:</strong> " . htmlspecialchars($_SERVER['PATH_INFO'] ?? 'not set') . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . htmlspecialchars($_SERVER['SCRIPT_NAME'] ?? 'not set') . "</p>";

// 2. Simulate the routing logic from index.php
$request = $_SERVER['REQUEST_URI'] ?? '';
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);
$path = ltrim($path, '/');
$path = preg_replace('/\/+/', '/', $path);
$path = trim($path, '/');

echo "<h2>🛣️ Routing Logic</h2>";
echo "<p><strong>Original Request:</strong> " . htmlspecialchars($request) . "</p>";
echo "<p><strong>Parsed Path:</strong> " . htmlspecialchars($path) . "</p>";

if (empty($path)) {
    $path = 'auth/login';
    echo "<p><strong>Default Path Applied:</strong> " . htmlspecialchars($path) . "</p>";
}

$segments = explode('/', $path);
echo "<p><strong>Path Segments:</strong> " . implode(', ', $segments) . "</p>";

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

echo "<p><strong>Controller:</strong> " . htmlspecialchars($controller) . "</p>";
echo "<p><strong>Action:</strong> " . htmlspecialchars($action) . "</p>";
echo "<p><strong>Params:</strong> " . implode(', ', $params) . "</p>";

// Convert controller name
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<p><strong>Controller Name:</strong> " . htmlspecialchars($controllerName) . "</p>";

// Check controller file
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<p><strong>Controller File:</strong> " . htmlspecialchars($controllerFile) . "</p>";
echo "<p><strong>File Exists:</strong> " . (file_exists($controllerFile) ? '✅ YES' : '❌ NO') . "</p>";

if (file_exists($controllerFile)) {
    echo "<p><strong>File Size:</strong> " . filesize($controllerFile) . " bytes</p>";
    
    // Try to include and check class
    try {
        require_once $controllerFile;
        $controllerClass = $controllerName . 'Controller';
        echo "<p><strong>Controller Class:</strong> " . htmlspecialchars($controllerClass) . "</p>";
        echo "<p><strong>Class Exists:</strong> " . (class_exists($controllerClass) ? '✅ YES' : '❌ NO') . "</p>";
        
        if (class_exists($controllerClass)) {
            $instance = new $controllerClass();
            echo "<p><strong>Instance Created:</strong> ✅ YES</p>";
            echo "<p><strong>Method '$action' Exists:</strong> " . (method_exists($instance, $action) ? '✅ YES' : '❌ NO') . "</p>";
            
            // List all available methods
            $methods = get_class_methods($controllerClass);
            echo "<p><strong>Available Methods:</strong> " . implode(', ', $methods) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    // List available controllers
    $controllersDir = BASE_PATH . '/app/controllers/';
    if (is_dir($controllersDir)) {
        $files = array_filter(scandir($controllersDir), function($file) {
            return substr($file, -4) === '.php' && $file !== '.' && $file !== '..';
        });
        echo "<p><strong>Available Controllers:</strong> " . implode(', ', $files) . "</p>";
    }
}

// 3. Check session
echo "<h2>👤 Session Information</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Logged In:</strong> " . (isset($_SESSION['user_id']) ? '✅ YES' : '❌ NO') . "</p>";
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>User ID:</strong> " . htmlspecialchars($_SESSION['user_id']) . "</p>";
    echo "<p><strong>User Type:</strong> " . htmlspecialchars($_SESSION['user_type'] ?? 'not set') . "</p>";
    echo "<p><strong>User Name:</strong> " . htmlspecialchars($_SESSION['user_name'] ?? 'not set') . "</p>";
}

// 4. Simulate what would happen
echo "<h2>🎯 What Would Happen</h2>";
if (!file_exists($controllerFile)) {
    echo "<p>❌ <strong>Result:</strong> Controller not found</p>";
} elseif (!class_exists($controllerName . 'Controller')) {
    echo "<p>❌ <strong>Result:</strong> Controller class not found</p>";
} elseif (!method_exists(new ($controllerName . 'Controller'), $action)) {
    echo "<p>❌ <strong>Result:</strong> Action not found</p>";
} else {
    echo "<p>✅ <strong>Result:</strong> Controller and action found, would execute</p>";
    if (!isset($_SESSION['user_id']) && $controller !== 'auth') {
        echo "<p>⚠️ <strong>Note:</strong> Would likely redirect to login due to authentication check</p>";
    }
}

echo "<hr>";
echo "<h2>🔗 Quick Links</h2>";
echo "<a href='/auth/login'>Login</a> | ";
echo "<a href='/plano_treino/create'>Test Route</a> | ";
echo "<a href='/relatorio/alunos'>Test Report</a>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #333; }
h2 { color: #666; margin-top: 30px; }
p { margin: 5px 0; }
strong { color: #000; }
</style>
