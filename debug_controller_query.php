<?php
// FILE: debug_controller_query.php
session_start();
require_once 'config/config.php';

// Quick login for testing
if (!isset($_SESSION['user_id'])) {
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
        }
    } catch (Exception $e) {
        echo "Database error: " . $e->getMessage();
        exit;
    }
}

echo "<h2>Debug Controller Query</h2>";
echo "<p><strong>User Type:</strong> " . $_SESSION['user_type'] . "</p>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // This is the EXACT query from the controller
    $query = "SELECT p.*, i.L_Nome as instrutor_nome 
             FROM plano_treino p 
             LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano 
             LEFT JOIN instrutor i ON m.CREF_j = i.CREF
             ORDER BY p.ID_Plano DESC";
    
    echo "<h3>Controller Query:</h3>";
    echo "<pre>" . htmlspecialchars($query) . "</pre>";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Results:</h3>";
    echo "<p><strong>Number of results:</strong> " . count($planos) . "</p>";
    
    if (empty($planos)) {
        echo "<p>❌ <strong>No results returned</strong></p>";
        echo "<p>This explains why the list page shows 'Nenhum plano de treino cadastrado'</p>";
        
        // Let's debug step by step
        echo "<h4>Step-by-step debugging:</h4>";
        
        // 1. Check plano_treino table
        $simple_query = "SELECT * FROM plano_treino ORDER BY ID_Plano DESC";
        $stmt = $db->prepare($simple_query);
        $stmt->execute();
        $simple_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<p>1. Simple plano_treino query: " . count($simple_result) . " results</p>";
        
        if (!empty($simple_result)) {
            echo "<p>✅ Data exists in plano_treino table</p>";
            foreach ($simple_result as $plano) {
                echo "<p>- ID: {$plano['ID_Plano']}, Descrição: " . substr($plano['Descricao'], 0, 50) . "...</p>";
            }
            
            // 2. Check JOIN with monta
            $monta_query = "SELECT p.*, m.CREF_j FROM plano_treino p LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano ORDER BY p.ID_Plano DESC";
            $stmt = $db->prepare($monta_query);
            $stmt->execute();
            $monta_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>2. With monta JOIN: " . count($monta_result) . " results</p>";
            
            // 3. Check final JOIN with instrutor
            $final_query = "SELECT p.*, m.CREF_j, i.L_Nome FROM plano_treino p LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano LEFT JOIN instrutor i ON m.CREF_j = i.CREF ORDER BY p.ID_Plano DESC";
            $stmt = $db->prepare($final_query);
            $stmt->execute();
            $final_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>3. With instrutor JOIN: " . count($final_result) . " results</p>";
            
            if (count($final_result) === 0) {
                echo "<p>⚠️ The instructor JOIN is eliminating all results!</p>";
                
                // Check if there are records in monta table
                $monta_check = "SELECT COUNT(*) as count FROM monta";
                $stmt = $db->prepare($monta_check);
                $stmt->execute();
                $monta_count = $stmt->fetch();
                echo "<p>Records in monta table: " . $monta_count['count'] . "</p>";
                
                if ($monta_count['count'] == 0) {
                    echo "<p>🔍 <strong>Found the problem!</strong> No records in monta table means the LEFT JOIN returns NULL for instructor info, but the query might be filtering those out.</p>";
                }
            }
        } else {
            echo "<p>❌ No data in plano_treino table - plans are not being saved!</p>";
        }
        
    } else {
        echo "<p>✅ <strong>Results found!</strong></p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID_Plano</th><th>Descricao</th><th>Instrutor</th></tr>";
        foreach ($planos as $plano) {
            echo "<tr>";
            echo "<td>" . $plano['ID_Plano'] . "</td>";
            echo "<td>" . htmlspecialchars(substr($plano['Descricao'], 0, 50)) . "...</td>";
            echo "<td>" . ($plano['instrutor_nome'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>Query Error:</strong> " . $e->getMessage() . "</p>";
}
?>
