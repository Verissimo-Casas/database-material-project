<?php
// Test with session authentication
session_start();
require_once '../config/config.php';

echo "=== Authentication Test ===\n";

// Check current session
echo "Session ID: " . session_id() . "\n";
echo "Session data: " . print_r($_SESSION, true) . "\n";

// Test with a mock session for admin user
$_SESSION['user_id'] = 1;
$_SESSION['user_type'] = 'admin';
$_SESSION['user_name'] = 'Admin Test';

echo "Set session data for admin user\n";
echo "User ID: " . $_SESSION['user_id'] . "\n";
echo "User Type: " . $_SESSION['user_type'] . "\n";

// Now test the controller
$controller = 'plano_treino';
$action = 'create';
$controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
$controllerFile = BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php';

echo "\nTesting controller with authentication...\n";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = $controllerName . 'Controller';
    $controllerInstance = new $controllerClass();
    
    if (method_exists($controllerInstance, $action)) {
        echo "Calling $controllerClass::$action()\n";
        ob_start();
        try {
            call_user_func([$controllerInstance, $action]);
            $output = ob_get_contents();
            echo "Output length: " . strlen($output) . " bytes\n";
            if (strlen($output) > 500) {
                echo "Output preview (first 500 chars):\n";
                echo substr($output, 0, 500) . "...\n";
            } else {
                echo "Full output:\n";
                echo $output . "\n";
            }
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
        ob_end_clean();
    } else {
        echo "ERROR: Action $action not found in $controllerClass\n";
    }
} else {
    echo "ERROR: Controller file not found\n";
}

echo "\n=== Test Complete ===\n";
?>
