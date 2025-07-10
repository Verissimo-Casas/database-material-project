<?php
// FILE: database_check.php
require_once 'config/config.php';

echo "<h2>Database Status Check</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    echo "<p>✅ Database connection successful</p>";
    
    // Check critical tables
    $tables = ['instrutor', 'admin', 'aluno', 'plano_treino', 'monta', 'segue'];
    
    foreach ($tables as $table) {
        try {
            $query = "SELECT COUNT(*) as count FROM $table";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>✅ Table '$table': {$result['count']} rows</p>";
        } catch (Exception $e) {
            echo "<p>❌ Table '$table': ERROR - " . $e->getMessage() . "</p>";
        }
    }
    
    // Check if we have test users
    echo "<h3>Test Users Available:</h3>";
    
    $query = "SELECT CREF, L_Nome, E_mail FROM instrutor LIMIT 3";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $instrutores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($instrutores)) {
        echo "<p>❌ No instructors found</p>";
    } else {
        foreach ($instrutores as $instrutor) {
            echo "<p>Instructor: {$instrutor['L_Nome']} (CREF: {$instrutor['CREF']}, Email: {$instrutor['E_mail']})</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>
