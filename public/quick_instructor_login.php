<?php
// Quick instructor login for testing
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
    
    if ($email === 'joao@academia.com' && $password === 'password') {
        $_SESSION['user_id'] = '12345678'; // Instructor CREF
        $_SESSION['user_name'] = 'João Silva';
        $_SESSION['user_type'] = 'instrutor';
        $_SESSION['matricula_id'] = null;
        
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
    <title>Quick Instructor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Instructor Login</h4>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" value="joao@academia.com" required>
                            </div>
                            <div class="mb-3">
                                <label>Password:</label>
                                <input type="password" name="password" class="form-control" value="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Login as Instructor</button>
                        </form>
                        
                        <hr>
                        <p>After login, go to:</p>
                        <ul>
                            <li><a href="/avaliacao/create">Create New Evaluation</a></li>
                            <li><a href="/avaliacao">View All Evaluations</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
