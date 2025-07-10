<?php
// FILE: test_plano_treino_flow.php
session_start();
require_once 'config/config.php';

echo "<h2>Complete Plano Treino Flow Test</h2>";

// Step 1: Check current session
echo "<h3>1. Session Status</h3>";
if (isset($_SESSION['user_id'])) {
    echo "<p>✅ User logged in: ID=" . $_SESSION['user_id'] . ", Type=" . $_SESSION['user_type'] . "</p>";
} else {
    echo "<p>❌ User NOT logged in</p>";
    // Quick login for testing
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT * FROM instrutor LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($instrutor) {
            $_SESSION['user_id'] = $instrutor['CREF'];
            $_SESSION['user_type'] = 'instrutor';
            $_SESSION['user_name'] = $instrutor['L_Nome'];
            echo "<p>✅ Auto-logged in as: " . $instrutor['L_Nome'] . "</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

// Step 2: Test controller file access
echo "<h3>2. Controller File Check</h3>";
$controllerFile = BASE_PATH . '/app/controllers/PlanoTreinoController.php';
echo "<p>Controller path: " . $controllerFile . "</p>";
echo "<p>File exists: " . (file_exists($controllerFile) ? '✅ YES' : '❌ NO') . "</p>";

if (file_exists($controllerFile)) {
    try {
        require_once $controllerFile;
        echo "<p>✅ Controller file included successfully</p>";
        
        if (class_exists('PlanoTreinoController')) {
            echo "<p>✅ PlanoTreinoController class exists</p>";
            
            $controller = new PlanoTreinoController();
            echo "<p>✅ Controller instantiated</p>";
            
            if (method_exists($controller, 'create')) {
                echo "<p>✅ create method exists</p>";
            } else {
                echo "<p>❌ create method missing</p>";
            }
            
            if (method_exists($controller, 'index')) {
                echo "<p>✅ index method exists</p>";
            } else {
                echo "<p>❌ index method missing</p>";
            }
        } else {
            echo "<p>❌ PlanoTreinoController class not found</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    }
}

// Step 3: Test actual HTTP requests
echo "<h3>3. HTTP Request Tests</h3>";
echo "<p><a href='/plano_treino' target='_blank'>Test: /plano_treino (should work)</a></p>";
echo "<p><a href='/plano_treino/create' target='_blank'>Test: /plano_treino/create (this is the problem)</a></p>";
echo "<p><a href='/index.php/plano_treino/create' target='_blank'>Test: /index.php/plano_treino/create (explicit)</a></p>";

// Step 4: Simulate the routing logic
echo "<h3>4. Routing Simulation</h3>";
$test_paths = [
    '/plano_treino',
    '/plano_treino/',
    '/plano_treino/create',
    '/plano_treino/create/',
    'plano_treino/create'
];

foreach ($test_paths as $test_path) {
    echo "<h4>Testing path: '$test_path'</h4>";
    
    $path = parse_url($test_path, PHP_URL_PATH);
    $path = str_replace('/index.php', '', $path);
    $path = ltrim($path, '/');
    $path = preg_replace('/\/+/', '/', $path);
    $path = trim($path, '/');
    
    if (empty($path)) {
        $path = 'auth/login';
    }
    
    $segments = explode('/', $path);
    
    if ($segments[0] === 'login' || $segments[0] === 'register') {
        $controller = 'auth';
        $action = $segments[0];
    } else {
        $controller = $segments[0] ?? 'auth';
        $action = $segments[1] ?? ($controller === 'auth' ? 'login' : 'index');
    }
    
    $controllerName = str_replace(' ', '', ucwords(str_replace('_', ' ', $controller)));
    
    echo "<p>→ Controller: '$controller' → '$controllerName'</p>";
    echo "<p>→ Action: '$action'</p>";
    echo "<p>→ Expected file: " . BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php</p>';
    echo "<p>→ File exists: " . (file_exists(BASE_PATH . '/app/controllers/' . $controllerName . 'Controller.php') ? '✅' : '❌') . "</p>";
    echo "<hr>";
}

// Step 5: Check nginx rewrite behavior
echo "<h3>5. Current Request Info</h3>";
echo "<p>REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";
echo "<p>SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</p>";
echo "<p>PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'NOT SET') . "</p>";
echo "<p>QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'NOT SET') . "</p>";
?>
