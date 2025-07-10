<?php
// Simple test to check aulas data
require_once 'config/config.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    echo "<h3>Database Connection Test</h3>";
    if ($conn) {
        echo "✅ Connected successfully<br><br>";
    } else {
        echo "❌ Connection failed<br>";
        exit;
    }
    
    echo "<h3>Current Database Time</h3>";
    $stmt = $conn->query("SELECT NOW() as now_time, CURDATE() as today");
    $time = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "NOW(): " . $time['now_time'] . "<br>";
    echo "CURDATE(): " . $time['today'] . "<br><br>";
    
    echo "<h3>All Aulas in Database</h3>";
    $stmt = $conn->query("SELECT ID_Aula, Dt_Hora, Descricao FROM aula ORDER BY Dt_Hora");
    $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Total aulas: " . count($aulas) . "<br>";
    echo "<table border='1'><tr><th>ID</th><th>Date/Time</th><th>Description</th></tr>";
    foreach ($aulas as $aula) {
        echo "<tr><td>{$aula['ID_Aula']}</td><td>{$aula['Dt_Hora']}</td><td>{$aula['Descricao']}</td></tr>";
    }
    echo "</table><br>";
    
    echo "<h3>Future Aulas (Dt_Hora >= NOW())</h3>";
    $stmt = $conn->query("SELECT ID_Aula, Dt_Hora, Descricao FROM aula WHERE Dt_Hora >= NOW() ORDER BY Dt_Hora");
    $futureAulas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Future aulas: " . count($futureAulas) . "<br>";
    echo "<table border='1'><tr><th>ID</th><th>Date/Time</th><th>Description</th></tr>";
    foreach ($futureAulas as $aula) {
        echo "<tr><td>{$aula['ID_Aula']}</td><td>{$aula['Dt_Hora']}</td><td>{$aula['Descricao']}</td></tr>";
    }
    echo "</table><br>";
    
    echo "<h3>Relationship Tables</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM cria");
    $criaCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "cria relationships: " . $criaCount['count'] . "<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM frequenta");
    $frequentaCount = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "frequenta relationships: " . $frequentaCount['count'] . "<br>";
    
    echo "<h3>Testing getUpcoming Query</h3>";
    $query = "
        SELECT a.ID_Aula, a.Dt_Hora, a.Descricao, 
               COALESCE(i.L_Nome, 'Sem instrutor') as instrutor_nome, 
               COALESCE(i.CREF, '') as CREF,
               COUNT(f.AL_CPF) as total_alunos
        FROM aula a 
        LEFT JOIN cria c ON a.ID_Aula = c.ID_Aula 
        LEFT JOIN instrutor i ON c.CREF_Instrutor = i.CREF
        LEFT JOIN frequenta f ON a.ID_Aula = f.ID_Aula
        WHERE a.Dt_Hora >= NOW()
        GROUP BY a.ID_Aula, a.Dt_Hora, a.Descricao, i.L_Nome, i.CREF
        ORDER BY a.Dt_Hora ASC
        LIMIT 5
    ";
    
    echo "Query: <pre>" . htmlspecialchars($query) . "</pre>";
    
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Result count: " . count($result) . "<br>";
    echo "<table border='1'><tr><th>ID</th><th>Date/Time</th><th>Description</th><th>Instructor</th><th>Students</th></tr>";
    foreach ($result as $aula) {
        echo "<tr><td>{$aula['ID_Aula']}</td><td>{$aula['Dt_Hora']}</td><td>{$aula['Descricao']}</td><td>{$aula['instrutor_nome']}</td><td>{$aula['total_alunos']}</td></tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}
?>
