<?php
// FILE: public/index.php

require_once '../config/config.php';

// Simple routing
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/index.php', '', $path);

// Remove base path if exists and clean multiple slashes
$path = ltrim($path, '/');
$path = preg_replace('/\/+/', '/', $path); // Replace multiple slashes with single slash
$path = trim($path, '/'); // Remove leading/trailing slashes

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
$controllerName = str_replace('_', '', ucwords($controller, '_'));

// Controller file path
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    $controllerClass = $controllerName . 'Controller';
    $controllerInstance = new $controllerClass();
    
    if (method_exists($controllerInstance, $action)) {
        call_user_func_array([$controllerInstance, $action], $params);
    } else {
        http_response_code(404);
        echo "Action not found";
    }
} else {
    http_response_code(404);
    echo "Controller not found";
}
?>
