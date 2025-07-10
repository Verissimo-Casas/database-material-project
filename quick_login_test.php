<?php
// FILE: quick_login_test.php
session_start();
require_once 'config/config.php';

// Quick login for testing
if (!isset($_SESSION['user_id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Try to get an instructor
        $query = "SELECT * FROM instrutor LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($instrutor) {
            $_SESSION['user_id'] = $instrutor['CREF'];
            $_SESSION['user_type'] = 'instrutor';
            $_SESSION['user_name'] = $instrutor['L_Nome'];
            echo "<p>Logged in as: " . $instrutor['L_Nome'] . " (Instructor)</p>";
        } else {
            // Try admin
            $query = "SELECT * FROM admin LIMIT 1";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin) {
                $_SESSION['user_id'] = $admin['ID_Admin'];
                $_SESSION['user_type'] = 'admin';
                $_SESSION['user_name'] = 'Administrator';
                echo "<p>Logged in as: Administrator</p>";
            } else {
                echo "<p>No users found in database</p>";
                exit();
            }
        }
    } catch (Exception $e) {
        echo "<p>Database error: " . $e->getMessage() . "</p>";
        exit();
    }
}

echo "<h2>Testing Plano Treino Access</h2>";
echo "<p>Current user: " . $_SESSION['user_name'] . " (" . $_SESSION['user_type'] . ")</p>";
echo "<p><a href='/plano_treino' target='_blank'>Go to Plano Treino Index</a></p>";
echo "<p><a href='/plano_treino/create' target='_blank'>Go to Plano Treino Create</a></p>";
?>
