<?php
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
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>URL Test</title>
</head>
<body>
    <h2>URL Generation Test</h2>
    <p><strong>BASE_URL:</strong> <?= BASE_URL ?></p>
    <p><strong>Session User:</strong> <?= $_SESSION['user_name'] ?? 'Not logged in' ?> (<?= $_SESSION['user_type'] ?? 'No type' ?>)</p>
    
    <h3>Generated URLs:</h3>
    <ul>
        <li><strong>plano_treino index:</strong> <a href="<?= BASE_URL ?>plano_treino"><?= BASE_URL ?>plano_treino</a></li>
        <li><strong>plano_treino create:</strong> <a href="<?= BASE_URL ?>plano_treino/create"><?= BASE_URL ?>plano_treino/create</a></li>
    </ul>
    
    <h3>Button Test (same as in the actual page):</h3>
    <?php if ($_SESSION['user_type'] !== 'aluno'): ?>
        <a href="<?= BASE_URL ?>plano_treino/create" class="btn btn-primary" style="background: blue; color: white; padding: 10px; text-decoration: none;">
            <i class="fas fa-plus"></i> Novo Plano (TEST)
        </a>
    <?php else: ?>
        <p>User is 'aluno' - button would not be shown</p>
    <?php endif; ?>
    
    <h3>Direct Navigation Test:</h3>
    <script>
    function testNavigation() {
        const url = '<?= BASE_URL ?>plano_treino/create';
        console.log('Navigating to:', url);
        window.location.href = url;
    }
    </script>
    <button onclick="testNavigation()" style="background: green; color: white; padding: 10px;">Test Navigation to Create Page</button>
</body>
</html>
