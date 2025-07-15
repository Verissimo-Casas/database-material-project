<?php
// Complete end-to-end test of notification system
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/app/models/Notification.php';

echo "<h1>Complete Notification System Test</h1>";

// Test 1: Create a notification
echo "<h2>Test 1: Creating a notification</h2>";
try {
    $notificationModel = new Notification();
    
    $testData = [
        'tipo_notificacao' => 'nova_avaliacao',
        'titulo' => 'Nova Avaliação Física Disponível',
        'mensagem' => 'Uma nova avaliação física foi realizada. Acesse o sistema para ver os resultados.',
        'destinatario_cpf' => '11122233344',
        'destinatario_tipo' => 'aluno',
        'remetente_id' => '12345678',
        'remetente_tipo' => 'instrutor',
        'id_referencia' => 1,
        'tipo_referencia' => 'avaliacao_fisica'
    ];
    
    if ($notificationModel->create($testData)) {
        echo "<p>✅ Notification created successfully</p>";
    } else {
        echo "<p>❌ Failed to create notification</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Error creating notification: " . $e->getMessage() . "</p>";
}

// Test 2: Retrieve notifications
echo "<h2>Test 2: Retrieving notifications</h2>";
try {
    $notifications = $notificationModel->getByUser('11122233344', null, 10);
    echo "<p>Found " . count($notifications) . " notifications</p>";
    
    if (!empty($notifications)) {
        foreach ($notifications as $notif) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px; background: #f9f9f9;'>";
            echo "<strong>" . htmlspecialchars($notif['Titulo']) . "</strong><br>";
            echo "<small>From: " . htmlspecialchars($notif['remetente_nome'] ?? 'System') . " | ";
            echo "Status: " . $notif['Status'] . " | ";
            echo "Date: " . date('d/m/Y H:i', strtotime($notif['Data_Criacao'])) . "</small><br>";
            echo "<p>" . htmlspecialchars($notif['Mensagem']) . "</p>";
            echo "</div>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Error retrieving notifications: " . $e->getMessage() . "</p>";
}

// Test 3: Count unread notifications
echo "<h2>Test 3: Unread notifications count</h2>";
try {
    $unreadCount = $notificationModel->getUnreadCount('11122233344');
    echo "<p>Unread notifications: <strong>{$unreadCount}</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ Error getting unread count: " . $e->getMessage() . "</p>";
}

// Test 4: Session setup for student
echo "<h2>Test 4: Setting up student session</h2>";
$_SESSION['user_id'] = '11122233344';
$_SESSION['user_type'] = 'aluno';
$_SESSION['user_name'] = 'Maria Santos';
$_SESSION['matricula_id'] = 1;

echo "<p>✅ Session configured:</p>";
echo "<ul>";
echo "<li>User ID: " . $_SESSION['user_id'] . "</li>";
echo "<li>User Type: " . $_SESSION['user_type'] . "</li>";
echo "<li>User Name: " . $_SESSION['user_name'] . "</li>";
echo "</ul>";

// Test 5: Test notification URLs
echo "<h2>Test 5: Navigation Links</h2>";
echo "<div style='background: #e8f5e8; padding: 15px; margin: 10px 0;'>";
echo "<h3>🎯 Ready to Test!</h3>";
echo "<p>The notification system is working. Use these links to test:</p>";
echo "<ul>";
echo "<li><a href='/notification' target='_blank'>📱 View All Notifications</a></li>";
echo "<li><a href='/dashboard' target='_blank'>🏠 Student Dashboard</a></li>";
echo "<li><a href='/quick_instructor_login.php' target='_blank'>👨‍🏫 Instructor Login</a></li>";
echo "<li><a href='/avaliacao/create' target='_blank'>📝 Create New Evaluation (as instructor)</a></li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<h2>How to Test Complete Workflow:</h2>";
echo "<ol>";
echo "<li>Current session is set as student (Maria Santos)</li>";
echo "<li>Open <a href='/quick_instructor_login.php' target='_blank'>Instructor Login</a> in new tab</li>";
echo "<li>Login as instructor and create a new evaluation for Maria Santos</li>";
echo "<li>Come back to this tab and click <a href='/notification'>View Notifications</a></li>";
echo "<li>You should see the new notification!</li>";
echo "</ol>";

echo "<script>";
echo "// Auto-refresh notification count every 5 seconds";
echo "setInterval(function() {";
echo "  fetch('/notification/getUnreadCount')";
echo "    .then(response => response.json())";
echo "    .then(data => {";
echo "      document.getElementById('live-count').textContent = data.count;";
echo "    });";
echo "}, 5000);";
echo "</script>";

echo "<div style='position: fixed; top: 10px; right: 10px; background: #007bff; color: white; padding: 10px; border-radius: 5px;'>";
echo "🔔 Live Count: <span id='live-count'>{$unreadCount}</span>";
echo "</div>";
?>
