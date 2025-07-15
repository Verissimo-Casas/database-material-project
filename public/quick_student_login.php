<?php
// Quick student login for testing notifications
require_once '../config/config.php';

// Check if already logged in
if (isLoggedIn()) {
    header("Location: /dashboard");
    exit;
}

// Handle login
if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($email === 'maria@email.com' && $password === 'password') {
        $_SESSION['user_id'] = '11122233344'; // Student CPF
        $_SESSION['user_name'] = 'Maria Santos';
        $_SESSION['user_type'] = 'aluno';
        $_SESSION['matricula_id'] = 1;
        
        header("Location: /dashboard");
        exit;
    } else {
        $error = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quick Student Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Student Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" value="maria@email.com" required>
                            </div>
                            <div class="mb-3">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" value="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login as Student</button>
                        </form>
                        
                        <hr>
                        <p>Test the notification system:</p>
                        <ol>
                            <li>Login as student (above)</li>
                            <li>Login as instructor (<a href="/quick_admin_login.php">here</a>)</li>
                            <li>Create a new evaluation at <a href="/avaliacao/create">/avaliacao/create</a></li>
                            <li>Student will receive notification</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
