<?php
session_start();
echo "<h1>Session Debug</h1>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session data:</strong></p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    echo "<p>✅ User is logged in</p>";
    echo "<p><strong>User ID:</strong> " . htmlspecialchars($_SESSION['user_id']) . "</p>";
    echo "<p><strong>User Type:</strong> " . htmlspecialchars($_SESSION['user_type'] ?? 'not set') . "</p>";
    echo "<p><strong>User Name:</strong> " . htmlspecialchars($_SESSION['user_name'] ?? 'not set') . "</p>";
} else {
    echo "<p>❌ User is NOT logged in</p>";
}
?>
