<?php
// Test notification functionality with proper session
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/app/models/Notification.php';

echo "<h1>Notification Functionality Test</h1>";

// Set up student session
$_SESSION['user_id'] = '11122233344';
$_SESSION['user_type'] = 'aluno';
$_SESSION['user_name'] = 'Maria Santos';

try {
    $notificationModel = new Notification();
    
    echo "<h2>Test 1: Get notifications for user</h2>";
    $notifications = $notificationModel->getByUser('11122233344', null, 10);
    
    if (!empty($notifications)) {
        echo "<p>✅ Found " . count($notifications) . " notifications</p>";
        foreach ($notifications as $notif) {
            echo "<div style='border: 1px solid #ddd; padding: 10px; margin: 5px;'>";
            echo "<h4>" . htmlspecialchars($notif['Titulo']) . "</h4>";
            echo "<p>" . htmlspecialchars($notif['Mensagem']) . "</p>";
            echo "<small>Status: " . $notif['Status'] . " | From: " . htmlspecialchars($notif['remetente_nome'] ?? 'Unknown') . "</small>";
            echo "</div>";
        }
    } else {
        echo "<p>❌ No notifications found</p>";
    }
    
    echo "<h2>Test 2: Get unread count</h2>";
    $unreadCount = $notificationModel->getUnreadCount('11122233344');
    echo "<p>Unread count: {$unreadCount}</p>";
    
    echo "<h2>Test 3: Session information</h2>";
    echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";
    echo "<p>User Type: " . $_SESSION['user_type'] . "</p>";
    echo "<p>User Name: " . $_SESSION['user_name'] . "</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Stack trace: " . $e->getTraceAsString() . "</p>";
}

echo "<hr>";
echo "<p><a href='/notification'>Go to Notification Page</a></p>";
echo "<p><a href='/dashboard'>Go to Dashboard</a></p>";
?>
