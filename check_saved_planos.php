<?php
// FILE: check_saved_planos.php
require_once 'config/config.php';

echo "<h2>Check Saved Planos de Treino</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check plano_treino table
    echo "<h3>Direct plano_treino table query:</h3>";
    $query = "SELECT * FROM plano_treino ORDER BY ID_Plano DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($planos)) {
        echo "<p>❌ No records found in plano_treino table</p>";
    } else {
        echo "<p>✅ Found " . count($planos) . " records in plano_treino table:</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID_Plano</th><th>Descricao</th></tr>";
        foreach ($planos as $plano) {
            echo "<tr>";
            echo "<td>" . $plano['ID_Plano'] . "</td>";
            echo "<td>" . htmlspecialchars($plano['Descricao']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Check monta table (instructor associations)
    echo "<h3>Monta table (instructor associations):</h3>";
    $query = "SELECT * FROM monta ORDER BY ID_Plano DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $montas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($montas)) {
        echo "<p>❌ No records found in monta table</p>";
    } else {
        echo "<p>✅ Found " . count($montas) . " records in monta table:</p>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>CREF_j</th><th>ID_Plano</th></tr>";
        foreach ($montas as $monta) {
            echo "<tr>";
            echo "<td>" . $monta['CREF_j'] . "</td>";
            echo "<td>" . $monta['ID_Plano'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test the exact query from the controller
    echo "<h3>Controller query test (from PlanoTreinoController::index):</h3>";
    $query = "SELECT p.*, i.L_Nome as instrutor_nome, a.AL_Nome as aluno_nome 
             FROM plano_treino p 
             LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano 
             LEFT JOIN instrutor i ON m.CREF_j = i.CREF
             LEFT JOIN segue s ON p.ID_Plano = s.ID_Plano
             LEFT JOIN aluno a ON s.AL_CPF = a.CPF
             ORDER BY p.ID_Plano DESC";
    
    try {
        $stmt = $db->prepare($query);
        $stmt->execute();
        $controller_planos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($controller_planos)) {
            echo "<p>❌ Controller query returned no results</p>";
            
            // Let's break down the query to see which JOIN is failing
            echo "<h4>Breaking down the query:</h4>";
            
            // Test basic plano_treino query
            $query_basic = "SELECT * FROM plano_treino ORDER BY ID_Plano DESC";
            $stmt = $db->prepare($query_basic);
            $stmt->execute();
            $basic_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>Basic plano_treino query: " . count($basic_result) . " records</p>";
            
            // Test with monta join
            $query_monta = "SELECT p.*, m.CREF_j FROM plano_treino p LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano ORDER BY p.ID_Plano DESC";
            $stmt = $db->prepare($query_monta);
            $stmt->execute();
            $monta_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>With monta JOIN: " . count($monta_result) . " records</p>";
            
            // Test with instrutor join
            $query_instrutor = "SELECT p.*, i.L_Nome as instrutor_nome FROM plano_treino p LEFT JOIN monta m ON p.ID_Plano = m.ID_Plano LEFT JOIN instrutor i ON m.CREF_j = i.CREF ORDER BY p.ID_Plano DESC";
            $stmt = $db->prepare($query_instrutor);
            $stmt->execute();
            $instrutor_result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<p>With instrutor JOIN: " . count($instrutor_result) . " records</p>";
            
            // Check if segue table exists and might be causing issues
            $query_segue_check = "SHOW TABLES LIKE 'segue'";
            $stmt = $db->prepare($query_segue_check);
            $stmt->execute();
            $segue_exists = $stmt->fetch();
            
            if (!$segue_exists) {
                echo "<p>⚠️ The 'segue' table does not exist! This might be causing the query to fail.</p>";
            } else {
                echo "<p>✅ 'segue' table exists</p>";
                
                // Test segue table content
                $query_segue = "SELECT COUNT(*) as count FROM segue";
                $stmt = $db->prepare($query_segue);
                $stmt->execute();
                $segue_count = $stmt->fetch();
                echo "<p>Records in segue table: " . $segue_count['count'] . "</p>";
            }
            
        } else {
            echo "<p>✅ Controller query returned " . count($controller_planos) . " records:</p>";
            echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
            echo "<tr style='background: #f0f0f0;'><th>ID_Plano</th><th>Descricao</th><th>Instrutor</th><th>Aluno</th></tr>";
            foreach ($controller_planos as $plano) {
                echo "<tr>";
                echo "<td>" . $plano['ID_Plano'] . "</td>";
                echo "<td>" . htmlspecialchars(substr($plano['Descricao'], 0, 50)) . "...</td>";
                echo "<td>" . ($plano['instrutor_nome'] ?: 'N/A') . "</td>";
                echo "<td>" . ($plano['aluno_nome'] ?: 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ Controller query error: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Database connection error: " . $e->getMessage() . "</p>";
}
?>
