<?php
// FILE: check_plano_treino_table.php
require_once 'config/config.php';

echo "<h2>Plano Treino Table Structure Check</h2>";

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Check table structure
    echo "<h3>Table Structure:</h3>";
    $query = "DESCRIBE plano_treino";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . ($column['Default'] ?: 'NULL') . "</td>";
        echo "<td>" . $column['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Check if table has any existing data
    echo "<h3>Existing Data:</h3>";
    $query = "SELECT * FROM plano_treino LIMIT 5";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($existing)) {
        echo "<p>No existing records in plano_treino table</p>";
    } else {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        $headers = array_keys($existing[0]);
        echo "<tr style='background: #f0f0f0;'>";
        foreach ($headers as $header) {
            echo "<th>$header</th>";
        }
        echo "</tr>";
        
        foreach ($existing as $row) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value ?: 'NULL') . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test what fields are required for insert
    echo "<h3>Test Insert Query:</h3>";
    echo "<p>Current insert query: <code>INSERT INTO plano_treino (Descricao) VALUES (:descricao)</code></p>";
    
    // Check if there are any NOT NULL fields without defaults
    $required_fields = [];
    foreach ($columns as $column) {
        if ($column['Null'] === 'NO' && is_null($column['Default']) && $column['Extra'] !== 'auto_increment') {
            $required_fields[] = $column['Field'];
        }
    }
    
    if (!empty($required_fields)) {
        echo "<p><strong>⚠️ Required fields (NOT NULL, no default):</strong> " . implode(', ', $required_fields) . "</p>";
    } else {
        echo "<p><strong>✅ No additional required fields found</strong></p>";
    }
    
    // Check monta table structure too (for the association)
    echo "<h3>Monta Table Structure:</h3>";
    try {
        $query = "DESCRIBE monta";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $monta_columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        foreach ($monta_columns as $column) {
            echo "<tr>";
            echo "<td>" . $column['Field'] . "</td>";
            echo "<td>" . $column['Type'] . "</td>";
            echo "<td>" . $column['Null'] . "</td>";
            echo "<td>" . $column['Key'] . "</td>";
            echo "<td>" . ($column['Default'] ?: 'NULL') . "</td>";
            echo "<td>" . $column['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (Exception $e) {
        echo "<p>⚠️ Monta table error: " . $e->getMessage() . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
