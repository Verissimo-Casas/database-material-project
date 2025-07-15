<?php
// Test backup route access
require_once '../config/config.php';

echo "<h1>Backup Route Test</h1>";
echo "<p>Testing backup system...</p>";

// Test if we can access the backup controller
if (file_exists(BASE_PATH . '/app/controllers/BackupController.php')) {
    echo "<p>✅ BackupController.php exists</p>";
} else {
    echo "<p>❌ BackupController.php missing</p>";
}

// Test backup directory
if (is_dir(BASE_PATH . '/backups')) {
    echo "<p>✅ Backup directory exists</p>";
} else {
    echo "<p>❌ Backup directory missing</p>";
}

// Test if user is logged in
if (isLoggedIn()) {
    echo "<p>✅ User is logged in as: " . $_SESSION['user_name'] . " (" . getUserType() . ")</p>";
    
    if (getUserType() === 'administrador') {
        echo "<p>✅ User has admin privileges</p>";
        echo "<p><a href='/backup'>Go to Backup System</a></p>";
    } else {
        echo "<p>❌ User is not an administrator</p>";
    }
} else {
    echo "<p>❌ User not logged in</p>";
    echo "<p><a href='/auth/login'>Login</a></p>";
}

// Test database connection
try {
    $pdo = getConnection();
    if ($pdo) {
        echo "<p>✅ Database connection successful</p>";
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='/dashboard'>Back to Dashboard</a></p>";
?>
