<?php
// FILE: test_login_and_plano.php
session_start();
require_once 'config/config.php';

echo "<h2>Test Login and Plano Treino Access</h2>";

// First check if there's an existing session
if (isset($_SESSION['user_id'])) {
    echo "<p><strong>Already logged in:</strong> User ID: " . $_SESSION['user_id'] . ", Type: " . $_SESSION['user_type'] . "</p>";
    
    // Try to access plano_treino/create
    echo "<a href='/plano_treino/create' target='_blank'>Test Plano Treino Create</a><br>";
    echo "<a href='/plano_treino' target='_blank'>Test Plano Treino Index</a><br>";
} else {
    echo "<p><strong>Not logged in.</strong> Let's check for test users...</p>";
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Check for test users
        $query = "SELECT * FROM instrutor LIMIT 2";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $instrutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Available Instructors:</h3>";
        foreach ($instrutores as $instrutor) {
            echo "<p>CREF: " . $instrutor['CREF'] . ", Email: " . $instrutor['E_mail'] . "</p>";
        }
        
        // Check for admin users
        $query = "SELECT * FROM admin LIMIT 2";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Available Admins:</h3>";
        foreach ($admins as $admin) {
            echo "<p>ID: " . $admin['ID_Admin'] . ", Email: " . $admin['E_mail'] . "</p>";
        }
        
        // Auto-login with first instructor if available
        if (!empty($instrutores)) {
            $instrutor = $instrutores[0];
            $_SESSION['user_id'] = $instrutor['CREF'];
            $_SESSION['user_type'] = 'instrutor';
            $_SESSION['user_name'] = $instrutor['L_Nome'];
            echo "<p><strong>Auto-logged in as instructor:</strong> " . $instrutor['L_Nome'] . "</p>";
            echo "<a href='/plano_treino/create' target='_blank'>Now Test Plano Treino Create</a><br>";
            echo "<a href='/plano_treino' target='_blank'>Now Test Plano Treino Index</a><br>";
        }
        
    } catch (Exception $e) {
        echo "<p><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
    }
}
?>
