<?php
// FILE: login_and_test.php
session_start();
require_once 'config/config.php';

$action = $_GET['action'] ?? 'show_status';

if ($action === 'login') {
    // Force login
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
            echo "<p>✅ Logged in successfully as: " . $instrutor['L_Nome'] . "</p>";
        } else {
            echo "<p>❌ No instructors found in database</p>";
        }
    } catch (Exception $e) {
        echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
    }
}

if ($action === 'logout') {
    session_destroy();
    echo "<p>✅ Logged out</p>";
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Test for Plano Treino</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .button { background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 5px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #f8f9fa; padding: 15px; border-radius: 4px; margin: 10px 0; }
    </style>
</head>
<body>
    <h2>Login Test for Plano Treino</h2>
    
    <div class="info">
        <h3>Current Status:</h3>
        <p><strong>Session Active:</strong> <?= session_status() === PHP_SESSION_ACTIVE ? '✅ YES' : '❌ NO' ?></p>
        <p><strong>User ID:</strong> <?= $_SESSION['user_id'] ?? '❌ NOT SET' ?></p>
        <p><strong>User Type:</strong> <?= $_SESSION['user_type'] ?? '❌ NOT SET' ?></p>
        <p><strong>User Name:</strong> <?= $_SESSION['user_name'] ?? '❌ NOT SET' ?></p>
        <p><strong>Can Access Plano Treino:</strong> 
        <?php 
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] !== 'aluno') {
            echo '✅ YES';
        } else {
            echo '❌ NO';
        }
        ?>
        </p>
    </div>
    
    <h3>Actions:</h3>
    <a href="?action=login" class="button">Force Login as Instructor</a>
    <a href="?action=logout" class="button">Logout</a>
    
    <h3>Test Navigation:</h3>
    <a href="<?= BASE_URL ?>plano_treino" class="button">Go to Plano Treino Index</a>
    <a href="<?= BASE_URL ?>plano_treino/create" class="button">Go to Plano Treino Create</a>
    
    <h3>Debug Info:</h3>
    <p><strong>BASE_URL:</strong> <?= BASE_URL ?></p>
    <p><strong>Session ID:</strong> <?= session_id() ?></p>
    <p><strong>Cookie Domain:</strong> <?= ini_get('session.cookie_domain') ?: 'default' ?></p>
    <p><strong>Cookie Path:</strong> <?= ini_get('session.cookie_path') ?: 'default' ?></p>
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="info">
        <h4>Testing PlanoTreinoController Access:</h4>
        <?php
        try {
            require_once BASE_PATH . '/app/controllers/PlanoTreinoController.php';
            $controller = new PlanoTreinoController();
            echo "<p>✅ Controller instantiated successfully</p>";
            
            // Test if we can access the create method (simulate what would happen)
            if (method_exists($controller, 'create')) {
                echo "<p>✅ create method exists</p>";
                
                // We can't actually call it here because it would try to render a view
                // But we can check if the authentication would pass
                if (isset($_SESSION['user_id']) && $_SESSION['user_type'] !== 'aluno') {
                    echo "<p>✅ Authentication would pass for create method</p>";
                } else {
                    echo "<p>❌ Authentication would fail for create method</p>";
                }
            } else {
                echo "<p>❌ create method not found</p>";
            }
        } catch (Exception $e) {
            echo "<p>❌ Error testing controller: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
    <?php endif; ?>
</body>
</html>
