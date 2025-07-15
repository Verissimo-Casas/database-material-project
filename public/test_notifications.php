<?php
// Test notification creation
session_start();
require_once '../config/config.php';
require_once BASE_PATH . '/app/models/Notification.php';

echo "<h1>Notification System Test</h1>";

// Test creating a notification
$notification = new Notification();

// Test data
$testData = [
    'tipo_notificacao' => 'nova_avaliacao',
    'titulo' => 'Teste de Notificação',
    'mensagem' => 'Esta é uma notificação de teste para verificar se o sistema está funcionando corretamente.',
    'destinatario_cpf' => '11122233344', // Student CPF from init.sql
    'destinatario_tipo' => 'aluno',
    'remetente_id' => '12345678', // Instructor CREF from init.sql
    'remetente_tipo' => 'instrutor',
    'id_referencia' => 1,
    'tipo_referencia' => 'avaliacao_fisica'
];

if ($notification->create($testData)) {
    echo "<p>✅ Notification created successfully!</p>";
} else {
    echo "<p>❌ Failed to create notification</p>";
}

// Test getting notifications
echo "<h2>Notifications for student:</h2>";
$notifications = $notification->getByUser('11122233344');
if (!empty($notifications)) {
    foreach ($notifications as $notif) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
        echo "<h3>" . htmlspecialchars($notif['Titulo']) . "</h3>";
        echo "<p>" . htmlspecialchars($notif['Mensagem']) . "</p>";
        echo "<small>From: " . htmlspecialchars($notif['remetente_nome']) . " | Status: " . $notif['Status'] . "</small>";
        echo "</div>";
    }
} else {
    echo "<p>No notifications found</p>";
}

// Test unread count
$unreadCount = $notification->getUnreadCount('11122233344');
echo "<h2>Unread notifications count: {$unreadCount}</h2>";

echo "<hr>";
echo "<p><a href='/notification'>View Notifications (login as student first)</a></p>";
echo "<p><a href='/quick_admin_login.php'>Quick Admin Login</a></p>";
?>
