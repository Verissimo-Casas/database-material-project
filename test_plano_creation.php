<?php
// FILE: test_plano_creation.php
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

echo "<h2>Test Plano Creation</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'];
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Test the exact same query as in the controller
        $query = "INSERT INTO plano_treino (Descricao) VALUES (:descricao)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':descricao', $descricao);
        
        if ($stmt->execute()) {
            $plano_id = $db->lastInsertId();
            echo "<p>✅ <strong>Success!</strong> Plano created with ID: $plano_id</p>";
            
            // Test the monta association
            if ($_SESSION['user_type'] === 'instrutor') {
                $query_monta = "INSERT INTO monta (CREF_j, ID_Plano) VALUES (:cref, :plano_id)";
                $stmt_monta = $db->prepare($query_monta);
                $stmt_monta->bindParam(':cref', $_SESSION['user_id']);
                $stmt_monta->bindParam(':plano_id', $plano_id);
                
                if ($stmt_monta->execute()) {
                    echo "<p>✅ <strong>Success!</strong> Plano associated with instructor</p>";
                } else {
                    echo "<p>⚠️ <strong>Warning:</strong> Plano created but association failed</p>";
                }
            }
            
            echo "<p><a href='/plano_treino'>Go back to Plano Treino list</a></p>";
        } else {
            echo "<p>❌ <strong>Error:</strong> Failed to create plano</p>";
        }
        
    } catch (Exception $e) {
        echo "<p>❌ <strong>Database Error:</strong> " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Plano Creation</title>
</head>
<body>
    <h3>Quick Test Form</h3>
    <form method="POST">
        <p>
            <label>Descrição do Plano:</label><br>
            <textarea name="descricao" rows="5" cols="50" required>Plano de teste:
- Segunda: Peito e Tríceps
- Terça: Costas e Bíceps
- Quarta: Pernas</textarea>
        </p>
        <p>
            <input type="submit" value="Test Create Plano" style="background: green; color: white; padding: 10px;">
        </p>
    </form>
    
    <p><strong>Current User:</strong> <?= $_SESSION['user_name'] ?? 'Not logged in' ?> (<?= $_SESSION['user_type'] ?? 'No type' ?>)</p>
</body>
</html>
