<?php
// Quick test to check the aula table structure and data
echo "Starting test...\n";

try {
    echo "Loading database config...\n";
    require_once __DIR__ . '/config/database.php';
    
    echo "Creating database connection...\n";
    $database = new Database();
    $db = $database->getConnection();
    echo "Database connected successfully!\n";
    
    // Check table structure
    echo "\n=== AULA TABLE STRUCTURE ===\n";
    $query = "DESCRIBE aula";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($structure as $column) {
        echo $column['Field'] . " | " . $column['Type'] . " | " . $column['Key'] . " | " . $column['Extra'] . "\n";
    }
    
    echo "\n=== EXISTING AULAS ===\n";
    $query = "SELECT * FROM aula ORDER BY ID_Aula DESC LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($aulas)) {
        echo "No aulas found in database.\n";
    } else {
        foreach ($aulas as $aula) {
            echo "ID: " . $aula['ID_Aula'] . " | DateTime: " . $aula['Dt_Hora'] . " | Desc: " . substr($aula['Descricao'], 0, 50) . "...\n";
        }
    }
    
    echo "\n=== MAX ID_AULA ===\n";
    $query = "SELECT MAX(ID_Aula) as max_id FROM aula";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Max ID_Aula: " . ($result['max_id'] ?? 'NULL') . "\n";
    
    echo "\nTest completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
?>
