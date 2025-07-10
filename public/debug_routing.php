<?php
require_once '../config/config.php';

// Debug routing for plano_treino
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);

// Remove base path if exists and clean multiple slashes
$path = ltrim($path, '/');
$path = preg_replace('/\/+/', '/', $path); // Replace multiple slashes with single slash
$path = trim($path, '/'); // Remove leading/trailing slashes

echo "<h2>Debug Info for URL: " . htmlspecialchars($request) . "</h2>";
echo "<strong>Parsed path:</strong> '" . htmlspecialchars($path) . "'<br>";

// Split path into controller and action
$segments = explode('/', $path);
echo "<strong>Segments:</strong> " . print_r($segments, true) . "<br>";

$controller = $segments[0] ?? 'auth';
$action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');
$params = array_slice($segments, 2);

echo "<strong>Controller:</strong> '" . htmlspecialchars($controller) . "'<br>";
echo "<strong>Action:</strong> '" . htmlspecialchars($action) . "'<br>";

// Convert controller name to PascalCase for compound names
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
echo "<strong>Controller Name (converted):</strong> '" . htmlspecialchars($controllerName) . "'<br>";

// Controller file path
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';
echo "<strong>Controller File Path:</strong> '" . htmlspecialchars($controllerFile) . "'<br>";
echo "<strong>File exists:</strong> " . (file_exists($controllerFile) ? 'YES' : 'NO') . "<br>";

if (file_exists($controllerFile)) {
    echo "<strong>File is readable:</strong> " . (is_readable($controllerFile) ? 'YES' : 'NO') . "<br>";
    
    try {
        require_once $controllerFile;
        $controllerClass = $controllerName . 'Controller';
        echo "<strong>Controller Class:</strong> '" . htmlspecialchars($controllerClass) . "'<br>";
        echo "<strong>Class exists:</strong> " . (class_exists($controllerClass) ? 'YES' : 'NO') . "<br>";
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            echo "<strong>Method '" . htmlspecialchars($action) . "' exists:</strong> " . (method_exists($controllerInstance, $action) ? 'YES' : 'NO') . "<br>";
            
            $methods = get_class_methods($controllerClass);
            echo "<strong>Available methods:</strong> " . implode(', ', $methods) . "<br>";
        }
    } catch (Exception $e) {
        echo "<strong>Error loading controller:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    }
} else {
    // Check if directory exists
    $controllerDir = dirname($controllerFile);
    echo "<strong>Controller directory exists:</strong> " . (is_dir($controllerDir) ? 'YES' : 'NO') . "<br>";
    
    // List files in controller directory
    if (is_dir($controllerDir)) {
        $files = scandir($controllerDir);
        echo "<strong>Files in controller directory:</strong><br>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "- " . htmlspecialchars($file) . "<br>";
            }
        }
    }
}
?>
