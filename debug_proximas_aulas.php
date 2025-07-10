<?php
// Debug próximas aulas issue
require_once 'config/config.php';
require_once 'app/models/Aula.php';

try {
    echo "=== DEBUGGING PRÓXIMAS AULAS ===\n\n";
    
    $aulaModel = new Aula();
    
    echo "1. Testing database connection...\n";
    $database = new Database();
    $conn = $database->getConnection();
    if ($conn) {
        echo "✓ Database connected successfully\n\n";
    } else {
        echo "✗ Database connection failed\n\n";
        exit;
    }
    
    echo "2. Checking all aulas in database...\n";
    $stmt = $conn->query("SELECT * FROM aula ORDER BY Dt_Hora");
    $allAulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total aulas found: " . count($allAulas) . "\n";
    foreach ($allAulas as $aula) {
        echo "- ID: {$aula['ID_Aula']}, Date: {$aula['Dt_Hora']}, Desc: {$aula['Descricao']}\n";
    }
    
    echo "\n3. Testing getUpcoming method...\n";
    $proximasAulas = $aulaModel->getUpcoming(5);
    echo "Upcoming aulas found: " . count($proximasAulas) . "\n";
    foreach ($proximasAulas as $aula) {
        echo "- ID: {$aula['ID_Aula']}, Date: {$aula['Dt_Hora']}, Desc: {$aula['Descricao']}\n";
        echo "  Instructor: {$aula['instrutor_nome']}, Students: {$aula['total_alunos']}\n";
    }
    
    echo "\n4. Checking current datetime...\n";
    $stmt = $conn->query("SELECT NOW() as current_time");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Current DB time: " . $result['current_time'] . "\n";
    
    echo "\n5. Testing future aulas condition...\n";
    $stmt = $conn->query("SELECT * FROM aula WHERE Dt_Hora >= NOW() ORDER BY Dt_Hora");
    $futureAulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Future aulas (Dt_Hora >= NOW()): " . count($futureAulas) . "\n";
    foreach ($futureAulas as $aula) {
        echo "- ID: {$aula['ID_Aula']}, Date: {$aula['Dt_Hora']}, Desc: {$aula['Descricao']}\n";
    }
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
?>
